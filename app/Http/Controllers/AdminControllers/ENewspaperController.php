<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ENewspaper;
use App\Models\NewsLanguage;
use App\Models\Topic;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Ensure Imagick is installed and configured in your PHP environment
use Illuminate\Support\Facades\Validator;

class ENewspaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = __('page.E_NEWSPAPERS_AND_MAGAZINES');

        ResponseService::noAnyPermissionThenRedirect(['list-enewspapaer', 'create-enewspapaer', 'update-enewspapaer', 'delete-enewspapaer', 'select-newslanguage-for-enewspapaer', 'select-channel-for-enewspapaer']);

        $e_newspapers = ENewspaper::with(['channel', 'newsLanguage'])
            ->orderBy('date', 'desc')
            ->get();

        $data = [
            'title'        => $title,
            'e_newspapers' => $e_newspapers,
        ];

        return view('admin.e_newspaper.index', $data);
    }

    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-enewspapaer');

        $title = __('page.CREATE_E_NEWSPAPER_AND_MAGAZINE');

        $news_languages = NewsLanguage::where('status', 'active')->get();
        $news_topics    = Topic::select('id', 'name')->where('status', 'active')->get();

        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();

        $data = [
            'title'           => $title,
            'newsLanguages'   => $news_languages,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
        ];

        return view('admin.e_newspaper.create', $data);
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-enewspapaer');

        $validator = Validator::make($request->all(), [
            'news_language_id' => 'required|exists:news_languages,id',
            'channel_id'       => 'required|exists:channels,id',
            'date'             => 'required|date',
            'type'             => 'required|in:paper,magazine',
            'pdf_file'         => 'required|file|mimes:pdf|max:10000000240',
            'thumbnail'        => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:200048',
            'background_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:200048',
            'topic_id'         => 'required|exists:topics,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('e-newspapers/pdfs', 'public');
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('e-newspapers/thumbnails', 'public');
        }
        $backgroundImagePath = null;
        if ($request->hasFile('background_image')) {
            $backgroundImagePath = $request->file('background_image')->store('e-newspapers/background_image', 'public');
        }

        ENewspaper::create([
            'channel_id'       => $request->channel_id,
            'topic_id'         => $request->topic_id,
            'news_language_id' => $request->news_language_id,
            'date'             => $request->date,
            'type'             => $request->type,
            'pdf_path'         => $pdfPath,
            'thumbnail'        => $thumbnailPath,
            'background_image' => $backgroundImagePath,
            'added_by'         => Auth::id(),
            'added_by_name'    => Auth::user()->name,
        ]);

        return response()->json([
            'status'   => true,
            'message'  => $request->type === 'paper'
                ? __('message.E_NEWSPAPER_CREATED_SUCCESSFULLY')
                : __('message.MAGAZINE_CREATED_SUCCESSFULLY'),
            'redirect' => route('e-newspapers.index'),
        ]);
    }

    public function edit($id)
    {
        ResponseService::noPermissionThenRedirect('update-enewspapaer');

        $title          = __('page.EDIT_E_NEWSPAPER');
        $e_newspaper    = ENewspaper::findOrFail($id);
        $news_languages = NewsLanguage::where('status', 'active')->get();

        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();

        $data = [
            'title'           => $title,
            'e_newspaper'     => $e_newspaper,
            'newsLanguages'   => $news_languages,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
        ];

        return view('admin.e_newspaper.edit', $data);
    }

    public function update(Request $request, $id)
    {
        ResponseService::noPermissionThenRedirect('update-enewspapaer');

        $request->validate([
            'news_language_id' => 'required|exists:news_languages,id',
            'channel_id'       => 'required|exists:channels,id',
            'date'             => 'required|date',
            'type'             => 'required|in:paper,magazine',
            'pdf_file'         => 'nullable|file|mimes:pdf|max:10000000240',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:200048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:200048',
            'topic_id'         => 'required|exists:topics,id',
        ]);

        $e_newspaper = ENewspaper::findOrFail($id);

        $pdfPath = $e_newspaper->pdf_path;
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('e-newspapers/pdfs', 'public');
        }

        $thumbnailPath = $e_newspaper->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('e-newspapers/thumbnails', 'public');
        }

        $backgroundImagePath = $e_newspaper->background_image;
        if ($request->hasFile('background_image')) {
            $backgroundImagePath = $request->file('background_image')->store('e-newspapers/background_image', 'public');
        }
        
        $e_newspaper->update([
            'channel_id'       => $request->channel_id,
            'topic_id'         => $request->topic_id,
            'news_language_id' => $request->news_language_id,
            'date'             => $request->date,
            'type'             => $request->type,
            'pdf_path'         => $pdfPath,
            'thumbnail'        => $thumbnailPath,
            'background_image' => $backgroundImagePath,
            'added_by'         => Auth::id(),
            'added_by_name'    => Auth::user()->name,
        ]);

        return redirect()->route('e-newspapers.index')->with('success', __('message.E_NEWSPAPER_UPDATED_SUCCESSFULLY'));
    }

    public function destroy($id)
    {
        ResponseService::noPermissionThenRedirect('delete-enewspapaer');

        $e_newspaper = ENewspaper::findOrFail($id);
        $e_newspaper->delete();
        return redirect()->route('e-newspapers.index')->with('success', __('message.E_NEWSPAPER_DELETED_SUCCESSFULLY'));
    }
}
