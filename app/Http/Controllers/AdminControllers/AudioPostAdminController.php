<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Topic;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

const STOREGE = 'storage/';

class AudioPostAdminController extends Controller
{
    private $post_image_path = "";

    public function __construct()
    {
        $this->post_image_path = "posts/post_images/";
    }
    public function index(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['list-AudioPost', 'create-AudioPost', 'update-AudioPost', 'delete-AudioPost', 'select-topic-for-AudioPost', 'select-channel-for-AudioPost', 'select-newslanguage-for-AudioPost', 'send-notification-any-AudioPost', 'view-comment-any-AudioPost']);

        $title           = __('page.AUDIO_POSTS');
        $pre_title       = __('page.AUDIO_POSTS');
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $topics          = Topic::select('id', 'name')->where('status', 'active')->get();
        $posts           = Post::where('status', 'active')->where('type', 'audio')->get();
        $data            = [
            'title'           => $title,
            'pre_title'       => $pre_title,
            'channel_filters' => $channel_filters,
            'topics'          => $topics,
            'posts'           => $posts,
        ];
        return view('admin.audios.index', $data);
    }

    public function create()
    {
        ResponseService::noPermissionThenRedirect('create-AudioPost');
        $title           = __('page.CREATE_AUDIOS');
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::where('status', 'active')->get();
        $url             = url('admin/audios');

        $data = [
            'title'           => $title,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
            'news_languages'  => $news_languages,
            'url'             => $url,
        ];

        return view('admin.audios.create', $data);
    }

    public function store(Request $request, Post $post)
    {
        ResponseService::noPermissionThenRedirect('create-AudioPost');
        $validator = Validator::make($request->all(), [

            'title'            => 'required|string|max:255|unique:posts,title',
            'description'      => 'required|string',
            'news_language_id' => 'required|integer|exists:news_languages,id',
            'channel_id'       => 'required|integer|exists:channels,id',
            'topic_id'         => 'required|integer|exists:topics,id',
            'post_type'        => 'nullable|string|in:audio',
            'status'           => 'nullable|string|in:active,inactive',
            'audio'            => 'required|file|mimes:mp4,mp3,wav,aac,ogg|max:10240',
            'image'            => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $defaultImage = Setting::where('name', 'default_image')->value('value');
        $audio_path   = null;

        $imageFile = $request->file('image');
        if ($imageFile) {
            $imageFileName = rand('0000', '9999') . $imageFile->getClientOriginalName();
            $imageFilePath = $imageFile->storeAs('posts_image', $imageFileName, 'public');
            $image         = url(Storage::url($imageFilePath));
        }
        // Handle audio upload if present
        if ($request->hasFile('audio')) {
            if (isset($s3_bucket_name->value) && $s3_bucket_name->value !== "" && isset($s3_bucket_url)) {
                $audioFile = $request->file('audio');
                $audioName = 'audio_' . rand('0000', '9999') . '.' . $audioFile->getClientOriginalExtension();
                uploadFileS3Bucket($audioFile, $audioName, $this->post_audio_path ?? 'posts_audio'); // Assume post_audio_path is defined or use default
                $audio_path = $s3_bucket_url->value . ($this->post_audio_path ?? 'posts_audio') . '/' . $audioName;
            } else {
                $audioFile     = $request->file('audio');
                $audioFileName = 'audio_' . rand('0000', '9999') . '.' . $audioFile->getClientOriginalExtension();
                $audioFilePath = $audioFile->storeAs('posts_audio', $audioFileName, 'public');
                $audio_path    = url(Storage::url($audioFilePath));
            }
        }

        $slug = Str::slug($request->title);
        if (empty($slug)) {
            $slug = 'post-' . uniqid();
        }

        $originalSlug = $slug;
        $counter      = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $pubDate     = Carbon::now()->toDateTimeString();
        $publishDate = Carbon::parse($pubDate);

        $post->title = $request->title;
        if ($request->filled('news_language_id')) {
            $post->news_language_id = $request->news_language_id;
        } else {
            $activeLang             = NewsLanguage::where('is_active', 1)->first();
            $post->news_language_id = $activeLang ? $activeLang->id : null;
        }

        $post->slug           = $slug;
        $post->type           = $request->post_type;
        $post->description    = $request->description;
        $post->channel_id     = $request->channel_id;
        $post->topic_id       = $request->topic_id ?? 0;
        $post->is_custom_post = true;
        $post->status         = $request->status;
        $post->image          = $s3_image ?? $image ?? null;
        $post->audio          = $audio_path;
        $post->pubdate        = $pubDate;
        $post->publish_date   = $publishDate;
        $save                 = $post->save();

        return response()->json(['status' => 'success', 'message' => 'Audio Post Created Successfully.', 'redirect' => route('audios.index')]);
    }

    public function show(Request $request)
    {
        ResponseService::noPermissionThenRedirect(['list-AudioPost']);

        $filter  = $request->input('filter') ?? '';
        $channel = $request->input('channel') ?? '';
        $topic   = $request->input('topic') ?? '';

        try {
            $query = Post::select(
                'posts.id', 'posts.channel_id', 'posts.topic_id', 'posts.slug', 'posts.type',
                'posts.audio', 'posts.image', 'posts.resource',
                'posts.view_count', 'posts.comment',
                'channels.name as channel_name', 'channels.logo as channel_logo',
                'topics.name as topic_name', 'posts.title', 'posts.favorite',
                'posts.description', 'posts.status', 'posts.publish_date'
            )
                ->withCount('reactions')
                ->leftJoin('channels', 'posts.channel_id', '=', 'channels.id')
                ->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')
                ->where('posts.type', 'audio') // Only get audio posts
                ->orderBy('posts.id', 'desc');

            /****** Filter for Most View, Likes & Recent Audio Posts *********/

            /****** Filter of Channels *********/
            if ($channel !== '' && $channel !== '*') {
                $query->where('channels.id', $channel);
            }

            /****** Filter of Topics *********/
            if ($topic !== '' && $topic !== '*') {
                $query->where('topics.id', $topic);
            }

            /****** Filter of Search Audio Posts *********/
            if ($request->has('search') && $request->search) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('posts.title', 'like', '%' . $search . '%')
                        ->orWhere('channels.name', 'like', '%' . $search . '%');
                });
            }

            $getAudioPosts = $query->paginate(12);

            // Format the publish_date field for each audio post
            $getAudioPosts->getCollection()->transform(function ($post) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                return $post;
            });

            return response()->json([
                'data'         => $getAudioPosts->items(),
                'total'        => $getAudioPosts->total(),
                'last_page'    => $getAudioPosts->lastPage(),
                'current_page' => $getAudioPosts->currentPage(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching audio posts'], 500);
        }
    }

    public function edit(string $id)
    {
        ResponseService::noPermissionThenRedirect('update-AudioPost');
        $title           = __('page.UPDATE_AUDIOS');
        $url             = route('audios.update', $id);
        $method          = "POST";
        $post            = Post::findOrFail($id); // Use findOrFail for better error handling
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::where('status', 'active')->get();

        $data = [
            'title'           => $title,
            'url'             => $url,
            'method'          => $method,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
            'news_languages'  => $news_languages,
            'post'            => $post,
        ];

        return view('admin.audios.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('update-AudioPost');

        // Fetch the existing post
        $post = Post::findOrFail($id);

        // Validation
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255|unique:posts,title,' . $post->id,
            'description'      => 'required|string',
            'news_language_id' => 'required|integer|exists:news_languages,id',
            'channel_id'       => 'required|integer|exists:channels,id',
            'topic_id'         => 'required|integer|exists:topics,id',
            'post_type'        => 'nullable|string|in:audio',
            'status'           => 'nullable|string|in:active,inactive',
            'audio'            => [
                ($request->post_type == 'audio' && ! $post->audio) ? 'required' : 'nullable',
                'file',
                'mimes:mp3,wav,aac,ogg',
                'max:10240',
            ],
            'image'            => [
                ($request->post_type == 'audio' && ! $post->image) ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $post = Post::findOrFail($id);

            $defaultImage = Setting::where('name', 'default_image')->value('value');
            $slug         = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'post-' . uniqid();
            }

            $originalSlug = $slug;
            $counter      = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle audio update
            $audio_path = $post->audio;
            if ($request->post_type == 'audio' && $request->hasFile('audio')) {
                // Delete old audio if exists
                if ($post->audio) {
                    $oldAudioPath = str_replace(url('/storage/'), '', $post->audio);
                    Storage::disk('public')->delete($oldAudioPath);
                }

                $audioFile     = $request->file('audio');
                $audioFileName = 'audio_' . rand(1000, 9999) . '.' . $audioFile->getClientOriginalExtension();
                $audioFilePath = $audioFile->storeAs('posts_audio', $audioFileName, 'public');
                $audio_path    = url(Storage::url($audioFilePath));

            }
            $image = $post->image;
            if ($request->post_type == 'audio') {

                // Check if a new image file is uploaded
                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if ($post->image) {
                        $oldImagePath = str_replace(url('/storage/'), '', $post->image);
                        Storage::disk('public')->delete($oldImagePath);
                    }

                    // Upload new image
                    $imageFile     = $request->file('image');
                    $imageFileName = rand(1000, 9999) . $imageFile->getClientOriginalName();
                    $imageFilePath = $imageFile->storeAs('posts_image', $imageFileName, 'public');
                    $image         = url(Storage::url($imageFilePath));
                }
            }

            // Update post fields
            $pubDate     = Carbon::now()->toDateTimeString();
            $post->title = $request->title;
            $post->slug  = $slug;
            if (empty($slug)) {
                $slug = 'post-' . uniqid();
            }
            $post->type             = $request->post_type;
            $post->news_language_id = $request->news_language_id ?? $post->news_language_id;
            if (! $request->filled('news_language_id')) {
                $activeLang             = NewsLanguage::where('is_active', 1)->first();
                $post->news_language_id = $activeLang ? $activeLang->id : $post->news_language_id;
            }
            $post->description  = $request->description;
            $post->channel_id   = $request->channel_id ?? $post->channel_id;
            $post->topic_id     = $request->topic_id ?? $post->topic_id;
            $post->status       = $request->status ?? $post->status;
            $post->image        = $image ?? $post->image;
            $post->audio        = $audio_path ?? $post->audio;
            $post->pubdate      = $pubDate;
            $post->publish_date = Carbon::parse($pubDate);
            $post->save();

            return response()->json([
                'success'  => true,
                'message'  => 'Audio Post updated successfully.',
                'redirect' => url('admin/audios'), // Added for JS redirect
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-AudioPost');
            Favorite::where('post_id', $id)->delete();
            $post = Post::find($id);
            if ($post->type == 'audio') {
                $baseUrl = URL::to('storage/');

                $filePath = str_replace($baseUrl, '', $post->image);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            $post->delete();
            ResponseService::successResponse("Audio Post Deleted Successfully.");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
