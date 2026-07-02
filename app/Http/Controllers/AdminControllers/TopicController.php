<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\Topic;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-topic', 'create-topic', 'update-topic', 'delete-topic', 'status-topic', 'select-newslanguage-for-topic']);
        $title          = __('page.TOPICS');
        $news_languages = NewsLanguage::where('status', 'active')->get();
        $data           = [
            'title'          => $title,
            'news_languages' => $news_languages,
        ];
        return view('admin.topic.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Topic $topic)
    {
        ResponseService::noPermissionThenSendJson('create-topic');
        $request->validate([
            'name'             => 'required',
            'status'           => 'required',
            'logo'             => 'required|max:2000|mimes:jpg,jpeg,png,webp,svg',
            'news_language_id' => 'required|exists:news_languages,id',
        ]);

        /* Store the channel logo. */
        $file = $request->file('logo');
        if ($file) {
            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $file->storeAs('images', $fileName, 'public');
        }

        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'topic-' . uniqid();
        }
        $originalSlug = $slug;
        $counter      = 1;

        while (Topic::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $topic->name   = $request->name;
        $topic->slug   = $slug;
        $topic->logo   = $fileName;
        $topic->status = $request->status;
        if ($request->filled('news_language_id')) {
            $topic->news_language_id = $request->news_language_id;
        } else {
            $activeLang              = NewsLanguage::where('is_active', 1)->first();
            $topic->news_language_id = $activeLang ? $activeLang->id : null;
        }
        $save = $topic->save();
        if ($save) {
            return response()->json(['success' => true, 'message' => 'topic crated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somthing went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $status = $request->input('status') ?? '';

        try {
            ResponseService::noPermissionThenSendJson('list-topic');

            $query = Topic::select('id', 'logo', 'name', 'slug', 'status', 'news_language_id');

            // Status filter
            if ($status !== '' && $status !== '*') {
                $query->where('status', $status);
            }

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    // Global search
                    if ($request->has('search') && ! empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('slug', 'like', "%{$search}%")
                                ->orWhere('status', 'like', "%{$search}%");
                        });
                    }

                    // Column-specific search
                    if ($request->has('columns')) {
                        foreach ($request->columns as $column) {
                            if (! empty($column['search']['value'])) {
                                $columnName  = $column['data'];
                                $searchValue = $column['search']['value'];

                                if (in_array($columnName, ['id', 'name', 'slug', 'status'])) {
                                    $query->where($columnName, 'like', "%{$searchValue}%");
                                }
                            }
                        }
                    }
                })
                ->editColumn('logo', function ($item) {
                    if (empty($item->logo)) {
                        return asset('assets/images/no_image_available.png');
                    }
                    return asset('storage/images/' . $item->logo);
                })
                ->addColumn('action', function ($getData) {
                    $actions = "<div class='d-flex flex-wrap gap-1'>";

                    if (auth()->user()->can('update-topic')) {
                        $actions .= "<a href='" . route('topics.edit', $getData->id) . "'
            class='btn text-primary btn-sm edit_btn'
            data-bs-toggle='modal'
            data-bs-target='#editTopicModal'
            title='Edit Topic'>
            <i class='fa fa-pen'></i>
         </a>";
                    } else {
                        $actions .= "<span class='badge bg-primary text-white'>No permission for Edit</span>";
                    }

                    $actions .= " &nbsp; ";

                    if (auth()->user()->can('delete-topic')) {
                        $actions .= "<a href='" . route('topics.destroy', $getData->id) . "'
            class='btn text-danger btn-sm delete-form delete-form-reload'
            data-bs-toggle='tooltip'
            title='Delete'>
            <i class='fa fa-trash'></i>
         </a>";
                    } else {
                        $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                    }

                    $actions .= "</div>";

                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "CustomFieldController -> show");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenSendJson('update-topic');

        $request->validate([
            'name'             => 'required',
            'status'           => 'required',
            'news_language_id' => 'required|exists:news_languages,id',
        ]);

        $file     = $request->file('logo');
        $topic_id = $request->id;
        $topic    = Topic::find($topic_id);
        if ($file) {
            $oldImagePath = public_path('images/' . $topic->logo);
            if (file_exists($oldImagePath) && $topic->logo) {
                unlink($oldImagePath);
            }

            $fileName = rand('0000', '9999') . $file->getClientOriginalName();
            $file->storeAs('images', $fileName, 'public');
        } else {
            $fileName = $topic->logo;
        }

        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'topic-' . uniqid();
        }
        $originalSlug = $slug;
        $counter      = 1;

        while (Topic::where('slug', $slug)->where('id', '!=', $topic_id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $topic->name   = $request->name;
        $topic->slug   = $slug;
        $topic->logo   = $fileName;
        $topic->status = $request->status;
        if ($request->filled('news_language_id')) {
            $topic->news_language_id = $request->news_language_id;
        } else {
            $activeLang              = NewsLanguage::where('is_active', 1)->first();
            $topic->news_language_id = $activeLang ? $activeLang->id : null;
        }
        $save = $topic->update();

        if ($save) {
            return response()->json(['success' => true, 'message' => 'topic updated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Somthing went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-topic');
            $topic  = Topic::findOrFail($id);
            $usedIn = collect([
                'posts'    => 'Posts',
                'stories'  => 'Stories',
                'rssFeeds' => 'RSS Feeds',
            ])->filter(function ($label, $relation) use ($topic) {
                return method_exists($topic, $relation)
                && $topic->$relation()->exists();
            })->values()->all();

            // Prevent delete if topic is in use
            if (! empty($usedIn)) {
                $usedInList = implode(', ', $usedIn);

                return ResponseService::errorResponse(
                    "You cannot delete this Topic because it is already used in {$usedInList}.",
                    400
                );
            }

            $topic->delete();
            ResponseService::successResponse("Topic deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }

    public function updateStatus(Request $request)
    {

        ResponseService::noPermissionThenSendJson('status-topic');
        $topic = Topic::find($request->id);

        if ($request->status === 'active') {
            $topic->status = 'active';
        } else {
            $topic->status = 'inactive';
        }
        $topic->save();
        if ($request->status == 'active') {
            return response()->json(['message' => 'Topic Activated']);
        } else {
            return response()->json(['message' => 'Topic Inactivated']);
        }
    }

    public function orderIndex()
    {
        ResponseService::noPermissionThenRedirect('list-topic');
        $title        = __('page.TOPICS_ORDER');
        $topics       = Topic::orderBy('categorie_order', 'asc')->get();
        $news_languages = NewsLanguage::where('status', 'active')->get();
        
        // Find the active language ID to set as default
        $default_lang = NewsLanguage::where('status', 'active')->where('is_active', 1)->first();
        $default_lang_id = $default_lang ? $default_lang->id : ($news_languages->first() ? $news_languages->first()->id : null);

        $data = [
            'title'           => $title,
            'news_languages'  => $news_languages,
            'topics'          => $topics,
            'default_lang_id' => $default_lang_id,
        ];
        return view('admin.topic.order', $data);
    }

    public function updateOrder(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-topic');
        $orders = $request->order;
        foreach ($orders as $order) {
            Topic::where('id', $order['id'])->update(['categorie_order' => $order['position']]);
        }
        return response()->json(['success' => true, 'message' => 'Topic order updated successfully.']);
    }

    public function getTopicsByLanguage(Request $request)
    {
        $lang_id = $request->news_language_id;
        $topics = Topic::where('news_language_id', $lang_id)
            ->orderBy('categorie_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $topics,
        ]);
    }
}

