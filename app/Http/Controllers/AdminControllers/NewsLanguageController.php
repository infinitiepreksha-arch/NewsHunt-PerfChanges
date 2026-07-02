<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsLanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = __('page.NEWS_LANGUAGES');
        ResponseService::noAnyPermissionThenRedirect(['list-newslanguage', 'create-newslanguage', 'update-newslanguage', 'delete-newslanguage', 'reorder-newslanguage', 'status-newslanguage',
        ]);
        $news_language_status = NewsLanguageStatus::getCurrentStatus();

        $news_languages = $news_language_status === 'active'
            ? NewsLanguage::orderBy('position', 'asc')->get()
            : [];

        // Initialize position if null
        if ($news_language_status === 'active' && $news_languages->contains('position', null)) {
            $news_languages->each(function ($language, $index) {
                $language->update(['position' => $index + 1]);
            });
            $news_languages = NewsLanguage::orderBy('position', 'asc')->get();
        }

        $data = [
            'title'                => $title,
            'temp'                 => $news_language_status,
            'news_language_status' => $news_language_status,
            'news_languages'       => $news_languages,
        ];

        return view('admin.news_language.index', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-newslanguage');
        $validator = Validator::make($request->all(), [
            'news_language_name'   => 'required|string|max:255|unique:news_languages,name',
            'news_language_code'   => 'required|string|max:10|unique:news_languages,code',
            'news_languages_image' => 'required|image|mimes:webp,jpeg,png,jpg,gif,svg|max:2048',
            'status'               => 'nullable|string|in:active,inactive',

        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('news_languages_image')) {
            $image     = $request->file('news_languages_image');
            $imagePath = $image->store('news_languages', 'public');
        }

        NewsLanguage::create([
            'name'   => $request->news_language_name,
            'code'   => $request->news_language_code,
            'image'  => $imagePath,
            'status' => $request->status,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'News language Added successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        ResponseService::noPermissionThenRedirect('update-newslanguage');
        $validator = Validator::make($request->all(), [
            'news_language_name'   => 'required|string|max:255|unique:news_languages,name,' . $id,
            'news_language_code'   => 'required|string|max:10|unique:news_languages,code,' . $id,
            'news_languages_image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif,svg|max:2048',
            'status'               => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $newsLanguage = NewsLanguage::find($id);

        if (! $newsLanguage) {
            return response()->json(['error' => 'News language not found'], 404);
        }

        $imagePath = $newsLanguage->image;
        if ($request->hasFile('news_languages_image')) {
            if ($newsLanguage->image && Storage::exists('public/' . $newsLanguage->image)) {
                Storage::delete('public/' . $newsLanguage->image);
            }

            // Store the new image
            $image     = $request->file('news_languages_image');
            $imagePath = $image->store('news_languages', 'public');
        }

        // Update the NewsLanguage record
        $newsLanguage->name   = $request->news_language_name;
        $newsLanguage->code   = $request->news_language_code;
        $newsLanguage->image  = $imagePath;
        $newsLanguage->status = $request->status;
        $newsLanguage->save();

        return redirect()->route('news-languages.index')->with('success', __('News language updated successfully!'));
    }

    public function destroy($id)
    {
        ResponseService::noPermissionThenRedirect('delete-newslanguage');
        $newsLanguage = NewsLanguage::findOrFail($id);

        // Check dependencies: Posts, Stories, Channels, RSS Feeds, eNewspapers
        $usedIn = collect([
            'post'        => 'Posts',
            'stories'     => 'Stories',
            'channel'     => 'Channels',
            'rssFeeds'    => 'RSS Feeds',
            'enewspapers' => 'eNewspapers',
            'topic'       => 'Topics',
        ])->filter(function ($label, $relation) use ($newsLanguage) {
            return method_exists($newsLanguage, $relation) && $newsLanguage->$relation()->exists();
        })->values()->all();

        if (! empty($usedIn)) {
            $usedInList = implode(', ', $usedIn);
            return response()->json([
                'status'  => 'error',
                'message' => "You cannot delete this News Language because it is already used in {$usedInList}.",
            ], 400);
        }

        // Delete image if exists
        if ($newsLanguage->image) {
            Storage::delete('public/' . $newsLanguage->image);
        }

        // Delete record
        $newsLanguage->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'News Language deleted successfully!',
        ]);
    }

    public function reorder(Request $request)
    {
        ResponseService::noPermissionThenRedirect('reorder-newslanguage');
        $validator = Validator::make($request->all(), [
            'order'            => 'required|array',
            'order.*.id'       => 'required|exists:news_languages,id',
            'order.*.position' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            foreach ($request->order as $item) {
                NewsLanguage::where('id', $item['id'])->update(['position' => $item['position']]);
            }

            // Set is_active to true for position 1, false for others
            $firstLang = collect($request->order)->firstWhere('position', 1);
            if ($firstLang) {
                NewsLanguage::query()->update(['is_active' => 0]);
                NewsLanguage::where('id', $firstLang['id'])->update(['is_active' => 1, 'status' => 'active']);
            }

            return response()->json(['success' => true, 'message' => __('Position updated successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Failed to update position')], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        ResponseService::noPermissionThenRedirect('status-newslanguage');
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $newsLanguage         = NewsLanguage::findOrFail($id);
            $newsLanguage->status = $request->status;
            $newsLanguage->save();

            $message = $request->status === 'active'
                ? __('News language activated successfully!')
                : __('News language deactivated successfully!');

            return response()->json(['message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['error' => __('Something went wrong')], 500);

        }
    }
}
