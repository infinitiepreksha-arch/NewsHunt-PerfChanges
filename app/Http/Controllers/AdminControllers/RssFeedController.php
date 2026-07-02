<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Jobs\FetchRssFeedJob;
use App\Models\Channel;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\RssFeed;
use App\Models\Topic;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class RssFeedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-rssfeed', 'create-rssfeed', 'update-rssfeed', 'delete-rssfeed', 'update-status-rssfeed', 'select-newslanguage-for-rssfeed', 'select-topic-for-rssfeed', 'select-channel-for-rssfeed', 'sync-all-rssfeed', 'sync-single-rssfeed']);

        $title          = __('page.RSS_FEEDS');
        $pre_title      = __('page.RSS_FEEDS');
        $channels_lists = Channel::all()->where('status', 'active');
        $topics_lists   = Topic::all()->where('status', 'active');

        $news_languages = NewsLanguage::where('status', 'active')->get();

        $data = compact('title', 'pre_title', 'channels_lists', 'topics_lists', 'news_languages');
        return view('admin.rss_feed.index', $data);
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

    public function store(Request $request, RssFeed $rssFeed)
    {
        ResponseService::noPermissionThenSendJson('create-rssfeed');
        $newsLanguage_Status = NewsLanguageStatus::getCurrentStatus();

        $validator = Validator::make($request->all(), [
            'rss_feed_url'     => 'required|url',
            'channel_id'       => 'required|exists:channels,id',
            'topic_id'         => 'required|exists:topics,id',
            'sync_interval'    => 'required|integer|min:1',
            'data_formate'     => 'required|in:XML,JSON',
            'description_type' => 'nullable|in:description-tag,content-encoded,media:description',
            'status'           => 'required|in:active,inactive',
            'news_language_id' => 'required|exists:news_languages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($newsLanguage_Status === 'inactive') {
            // Get default language from `languages` where `is_default = 1`
            $defaultLanguage = DB::table('languages')->where('is_default', 1)->first();

            if (! $defaultLanguage) {
                                                                     // No default language found, select a language to set as default
                $fallbackLanguage = DB::table('languages')->first(); // You can adjust logic to select a specific language

                if (! $fallbackLanguage) {
                    return response()->json(['error' => 'No languages found in the system!'], 500);
                }

                // Update the selected language to set `is_default = 1`
                DB::table('languages')
                    ->where('id', $fallbackLanguage->id)
                    ->update(['is_default' => 1]);

                // Re-fetch the updated language
                $defaultLanguage = DB::table('languages')->where('id', $fallbackLanguage->id)->first();
            }

            $defaultLanguageImagePath = 'news_languages/no_image_available.png';

            // Check if the default language exists in `news_languages`
            $defaultNewsLanguage = DB::table('news_languages')->where('code', $defaultLanguage->code)->first();
            if (! $defaultNewsLanguage) {
                $defaultNewsLanguageId = DB::table('news_languages')->insertGetId([
                    'name'       => $defaultLanguage->name,
                    'code'       => $defaultLanguage->code,
                    'image'      => $defaultLanguageImagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $defaultNewsLanguageId = $defaultNewsLanguage->id;
            }
        }

        if ($newsLanguage_Status === 'active') {
            $request->validate([
                'news_language_id' => 'required|exists:news_languages,id',
            ]);
        }

        $rssFeed->feed_url         = $request->rss_feed_url;
        $rssFeed->sync_interval    = $request->sync_interval;
        $rssFeed->data_format      = $request->data_formate;
        $rssFeed->description_type = $request->description_type;
        $rssFeed->channel_id       = $request->channel_id;
        $rssFeed->topic_id         = $request->topic_id;
        $rssFeed->news_language_id = $request->news_language_id ?? $defaultNewsLanguageId;
        $rssFeed->status           = $request->status;

        $rssFeed->save();

        return response()->json(['success' => true, 'message' => 'RSS Feed created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $status    = $request->input('feedStatus') ?? '';
        $channelId = $request->input('channelId') ?? '';
        $topicId   = $request->input('topicId') ?? '';
        try {
            ResponseService::noPermissionThenSendJson('list-rssfeed');

            $query = RssFeed::select('rss_feeds.*', 'rss_feeds.id', 'channels.name as channel_name', 'topics.name as topic_name', 'rss_feeds.feed_url', 'rss_feeds.data_format', 'rss_feeds.sync_interval', 'rss_feeds.news_language_id', 'rss_feeds.status', 'channels.id as channels_id', 'topics.id as topics_id')
                ->join('channels', 'rss_feeds.channel_id', 'channels.id')
                ->join('topics', 'rss_feeds.topic_id', 'topics.id');

            if ($status !== '' && $status !== '*') {
                $query->where('rss_feeds.status', $status);
            }
            if ($channelId !== '' && $channelId !== '*') {
                $query->where('rss_feeds.channel_id', $channelId);
            }

            if ($topicId !== '' && $topicId !== '*') {
                $query->where('rss_feeds.topic_id', $topicId);
            }
            $feeds = $query->get();

            return DataTables::of($feeds)
                ->addColumn('action', function ($feed) {
                    $actions = "<div class='d-flex flex-wrap gap-1'>"; // Start wrapper

                    // Edit button (update permission)
                    if (auth()->user()->can('update-rssfeed')) {
                        $actions .= "<a href='" . route('rss-feeds.edit', $feed->id) . "'
                class='btn text-primary btn-sm edit_btn'
                data-bs-toggle='modal'
                data-bs-target='#editRssFeedModal'
                title='Edit RSS Feed'>
                <i class='fa fa-pen'></i>
             </a>";
                    } else {
                        $actions .= "<span class='badge bg-primary text-white'>No permission for Edit</span>";
                    }

                    // Small space
                    $actions .= " &nbsp; ";

                    // Delete button (delete permission)
                    if (auth()->user()->can('delete-rssfeed')) {
                        $actions .= "<a href='" . route('rss-feeds.destroy', $feed->id) . "'
                class='btn text-danger btn-sm delete-form delete-form-reload'
                data-bs-toggle='tooltip'
                title='Delete'>
                <i class='fa fa-trash'></i>
             </a>";
                    } else {
                        $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                    }

                    $actions .= "</div>"; // End wrapper

                    return $actions;
                })
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
    public function update(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-rssfeed');
        $newsLanguageStatus = NewsLanguageStatus::getCurrentStatus();
        $request->validate([
            'rss_feed_url'     => 'required|url',
            'sync_interval'    => 'required',
            'data_formate'     => 'required',
            'description_type' => 'nullable|in:description-tag,content-encoded,media:description',
            'channel_id'       => 'required',
            'topic_id'         => 'required',
            'status'           => 'required',
            'news_language_id' => 'required|exists:news_languages,id',
        ]);

        //  news language status active than so news languages but status inactive store system language
        if ($newsLanguageStatus === 'active') {
            $request->validate([
                'news_language_id' => 'required|exists:news_languages,id',
            ]);
        }

        $id      = $request->id;
        $rssFeed = RssFeed::find($id);

        $rssFeed->feed_url         = $request->rss_feed_url;
        $rssFeed->sync_interval    = $request->sync_interval;
        $rssFeed->data_format      = $request->data_formate;
        $rssFeed->description_type = $request->description_type;
        $rssFeed->news_language_id = $request->news_language_id;
        $rssFeed->channel_id       = $request->channel_id;
        $rssFeed->topic_id         = $request->topic_id;
        $rssFeed->status           = $request->status;

        $save = $rssFeed->save();

        if ($save) {
            return redirect()->route('rss-feeds.index')->with('success', 'Rss Feed Updated successfully..!');
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
            ResponseService::noPermissionThenSendJson('delete-rssfeed');
            RssFeed::find($id)->delete();
            ResponseService::successResponse("Feed deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }

    public function updateStatus(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-status-rssfeed');
        $channel = RssFeed::find($request->id);

        if ($request->status === 'active') {
            $channel->status = 'active';
        } else {
            $channel->status = 'inactive';
        }
        $channel->save();
        if ($request->status == 'active') {
            return response()->json(['message' => 'RssFeed Activated']);
        } else {
            return response()->json(['message' => 'RssFeed Inactivated']);
        }
    }

    /* Fetch single feed data  */
    public function singelFeedFetch(Request $request)
    {
        try {
            $id = $request->id;

            $feeds = RssFeed::where('id', $id)->get();

            if ($feeds->isNotEmpty() && $feeds->first()->status == 'active') {
                // Call handle() directly to ensure we get the results array
                $results = (new FetchRssFeedJob($feeds))->handle();

                // Build descriptive label for description type
                $descTypeLabels = [
                    'description-tag'   => 'Description Tag',
                    'content-encoded'   => 'Content Encoded',
                    'media:description' => 'media:description',
                ];

                // Ensure $results is an array before accessing
                if (! is_array($results)) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Failed to fetch RSS feed or no results returned.',
                    ]);
                }

                $feedDescType      = $results['feed_description_type'] ?? null;
                $feedDescTypeLabel = $descTypeLabels[$feedDescType] ?? $feedDescType;

                $messages = [];

                if ($results['saved'] > 0) {
                    $messages[] = 'Rss Feed synced Successfully,';
                }

                // Display skip message ONLY if no posts were saved
                if ($results['skipped'] > 0 && $results['saved'] == 0) {
                    $skippedDescType = $results['skipped_description_type'] ?? $feedDescType;
                    $skippedLabel    = $descTypeLabels[$skippedDescType] ?? $skippedDescType;
                    $messages[]      = 'Selected description type "' . $skippedLabel . '" not available in RSS feed.';
                }

                if ($results['already_exists'] > 0) {
                    $messages[] = 'Posts already exist.';
                }

                if (empty($messages)) {
                    $messages[] = 'Posts already exist.';
                }

                if (empty($messages)) {
                    $messages[] = 'No new posts found in the RSS feed.';
                }

                return response()->json([
                    'error'   => false,
                    'message' => 'Rss Feed synced Successfully',
                    'details' => $messages,
                    'stats'   => [
                        'saved'            => $results['saved'],
                        'skipped'          => $results['skipped'],
                        'already_exists'   => $results['already_exists'],
                        'description_type' => $feedDescTypeLabel,
                    ],
                ]);
            } else {
                return response()->json(['error' => true, 'message' => 'Please activate before fetching']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
