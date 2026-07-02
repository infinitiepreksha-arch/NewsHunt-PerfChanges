<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Topic;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class VideoAdminController extends Controller
{
    const STOREGE                 = 'storage/';
    private $thumbnail_image_path = null;
    private $video_path           = null;

    public function __construct()
    {
        $this->thumbnail_image_path = "posts/thumbnail_images/";
        $this->video_path           = "posts/videos/";
    }

    public function index(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['list-VideoPost', 'create-custom-VideoPost', 'create-youtube-VideoPost', 'update-custom-VideoPost', 'update-youtube-VideoPost', 'delete-custom-VideoPost', 'delete-youtube-VideoPost', 'select-channel-for-VideoPost', '', 'select-newslanguage-for-VideoPost', 'send-notification-any-VideoPost', 'view-comment-any-VideoPost']);

        $title           = __('page.VIDEO_POSTS');
        $pre_title       = __('page.VIDEO_POSTS');
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $topics          = Topic::select('id', 'name')->where('status', 'active')->get();
        $posts           = Post::where('status', 'active')->select('id', 'type', 'video')->get();
        $data            = [
            'title'           => $title,
            'pre_title'       => $pre_title,
            'channel_filters' => $channel_filters,
            'topics'          => $topics,
            'posts'           => $posts,
            'default_filter'  => session('video_filter'),
        ];
        return view('admin.videos.index', $data);
    }

    public function createCustom()
    {
        ResponseService::noPermissionThenRedirect('create-custom-VideoPost');
        $title           = __('page.CREATE_VIDEOS');
        $url             = url('admin/videos');
        $method          = "POST";
        $formID          = "videoPostForm";
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::where('status', 'active')->get();

        $data = [
            'title'           => $title,
            'url'             => $url,
            'method'          => $method,
            'formID'          => $formID,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
            'news_languages'  => $news_languages,
        ];

        return view('admin.videos.create_custom', $data);
    }

    public function createYoutube()
    {
        ResponseService::noPermissionThenRedirect('create-youtube-VideoPost');
        $title           = __('page.CREATE_YOUTUBE_VIDEO');
        $url             = url('admin/videos');
        $method          = "POST";
        $formID          = "youtubeVideoPostForm";
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();

        $news_languages = NewsLanguage::where('status', 'active')->get();

        $data = [
            'title'           => $title,
            'url'             => $url,
            'method'          => $method,
            'formID'          => $formID,
            'channel_filters' => $channel_filters,
            'news_topics'     => $news_topics,
            'news_languages'  => $news_languages,
        ];

        return view('admin.videos.create_youtube', $data);
    }

    public function store(Request $request, Post $post)
    {
        ResponseService::noPermissionThenRedirect(['create-custom-VideoPost', 'create-youtube-VideoPost']);

        $validator = Validator::make($request->all(), [
            'title'                                             => 'required|string|max:255|unique:posts,title',
            'description'                                       => 'required|string',
            'news_language_id'                                  => 'required|integer|exists:news_languages,id',
            'channel_id'                                        => 'required|integer|exists:channels,id',
            'topic_id'                                          => 'nullable|integer|exists:topics,id',
            'status'                                            => 'nullable|string|in:active,inactive',
            $request->post_type == 'post' ? 'image' : ''        => $request->post_type == 'post' ? 'required|max:100240|mimes:jpg,jpeg,png,webp,svg' : '',
            $request->post_type == 'youtube' ? 'image' : ''     => $request->post_type == 'youtube' ? 'required|max:100240|mimes:jpg,jpeg,png,webp,svg' : '',
            $request->post_type == 'video' ? 'thumb_image' : '' => $request->post_type == 'video' ? 'required|max:100240|mimes:jpg,jpeg,png,webp,svg' : '',
            $request->post_type == 'video' ? 'video' : ''       => $request->post_type == 'video' ? 'required|mimes:webm,mp4,mov,ogg,qt|max:100240' : '',
            $request->post_type == 'youtube' ? 'video_url' : '' => $request->post_type == 'youtube' ? 'required|url' : '',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
        $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();
        $s3_image       = $image       = null;
        $s3_thumb_img   = $thumbImage   = null;
        $s3_thumb_video = $video = null;

        if (isset($s3_bucket_name->value) && $s3_bucket_name->value !== "" && isset($s3_bucket_url)) {
            /* Upload post image in S3 bucket */
            $postImage = $request->file('image');
            if ($postImage) {
                $posts_img = getFileName($postImage);
                if ($posts_img) {
                    uploadFileS3Bucket($postImage, $posts_img, $this->post_image_path);
                    $s3_image = $s3_bucket_url->value . $this->thumbnail_image_path . $posts_img;
                }
            }

            /* Upload thumb image in S3 bucket */
            $thumbImage = $request->file('thumb_image');
            if ($thumbImage) {
                $s3_thumb_img = getFileName($thumbImage);
                if ($s3_thumb_img) {
                    uploadFileS3Bucket($thumbImage, $s3_thumb_img, $this->thumbnail_image_path);
                    $thumbImage = $s3_bucket_url->value . $this->thumbnail_image_path . $s3_thumb_img;
                }
            }
            if ($request->post_type === "video") {
                $videoFile = $request->file('video');
                if ($videoFile) {
                    $s3_thumb_video = getFileName($videoFile);
                    if ($s3_thumb_video) {
                        uploadFileS3Bucket($videoFile, $s3_thumb_video, $this->video_path);
                        $s3_thumb_video = $s3_bucket_url->value . $this->thumbnail_image_path . $s3_thumb_video;
                    }
                }
            }
        } else {
            /* Store the Post Image locally */
            $imageFile = $request->file('image');
            if ($imageFile) {
                $imageFileName = rand('0000', '9999') . $imageFile->getClientOriginalName();
                $imageFilePath = $imageFile->storeAs('posts_image', $imageFileName, 'public');
                $image         = url(Storage::url($imageFilePath));
            }

            /* Store the Thumb Image locally */
            $thumbImageFile = $request->file('thumb_image');
            if ($thumbImageFile) {
                $thumbFileName = rand('0000', '9999') . $thumbImageFile->getClientOriginalName();
                $path          = $thumbImageFile->storeAs('thumb_image', $thumbFileName, 'public');
                $thumbImage    = url(Storage::url($path));
            }

            if ($request->post_type === "video") {
                $videoFileLocal = $request->file('video');
                if ($videoFileLocal) {
                    $videoFileName = rand('0000', '9999') . $videoFileLocal->getClientOriginalName();
                    $videoPath     = $videoFileLocal->storeAs('posts_videos', $videoFileName, 'public');
                    $video         = url(Storage::url($videoPath));
                }
            }
        }

        $slug = Str::slug($request->title);
        if (empty($slug)) {
            $slug = 'video-' . uniqid();
        }

        $originalSlug = $slug;
        $counter      = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $pubDate     = Carbon::now()->toDateTimeString();
        $publishDate = Carbon::parse($pubDate);

        if ($request->post_type === "youtube") {
            if ($request->filled('video_url')) {
                $videoUrl   = $request->video_url;
                $videoId    = null;
                $videoEmbed = null;

                $videoUrl             = trim($videoUrl);
                $post->is_short_video = 0;

                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                    $videoId = $matches[1];
                } elseif (preg_match('/v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                    $videoId = $matches[1];
                } elseif (preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                    $videoId              = $matches[1];
                    $post->is_short_video = 1;
                }

                if (! $videoId) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['video_url' => [__('Invalid YouTube URL. Please provide a correct video link.')]],
                    ], 422);
                }

                if (preg_match('/list=([a-zA-Z0-9_-]+)/', $videoUrl, $listMatch)) {
                    $videoEmbed = "https://www.youtube.com/embed/{$videoId}?list={$listMatch[1]}";
                } else {
                    $videoEmbed = "https://www.youtube.com/embed/{$videoId}";
                }

                $post->video_url   = $videoUrl;
                $post->video_embed = $videoEmbed;
            }
        }

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
        $post->status         = $request->status;
        $post->image          = $s3_image ?? $image;
        $post->is_custom_post = true;

        if ($request->post_type === "youtube") {
            $post->video_type  = 'youtube';
            $post->video_thumb = $s3_thumb_img ?? $image;
            $post->video       = $post->video_embed;
        } elseif ($request->post_type === "video") {
            $post->video_thumb = $s3_thumb_img ?? $thumbImage;
            $post->video       = $s3_thumb_video ?? $video;
        }

        $post->pubdate      = $pubDate;
        $post->publish_date = $publishDate;
        $post->is_video     = 1;
        $save               = $post->save();

        if ($request->post_type === "youtube") {
            session()->flash('video_filter', 'youtube_videos');
            return response()->json([
                'status'   => 'success',
                'message'  => 'Youtube Video Post Created successfully.',
                'redirect' => url('admin/videos'),
            ]);
        } elseif ($request->post_type === "video");
        {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Video Post Created successfully.',
                'redirect' => url('admin/videos'),
            ]);
        }

    }

    public function show(Request $request)
    {
        ResponseService::noPermissionThenRedirect(['list-VideoPost']);

        $filter  = $request->input('filter') ?? '';
        $channel = $request->input('channel') ?? '';
        $topic   = $request->input('topic') ?? '';
        try {

            $query = Post::select(
                'posts.id', 'posts.channel_id', 'posts.topic_id', 'posts.slug', 'posts.type',
                'posts.video_thumb', 'posts.video', 'posts.image', 'posts.resource',
                'posts.view_count', 'posts.comment',
                'channels.name as channel_name', 'channels.logo as channel_logo',
                'topics.name as topic_name', 'posts.title', 'posts.favorite',
                'posts.description', 'posts.status', 'posts.publish_date'
            )
                ->withCount('reactions')
                ->leftJoin('channels', 'posts.channel_id', '=', 'channels.id')
                ->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')
                ->orderBy('posts.id', 'desc');

            /****** Filter for Most View,Likes & Recent News *********/
            if ($filter === 'viewd') {
                $query->orderBy('posts.view_count', 'DESC');
            } elseif ($filter === 'liked') {
                $query->orderBy('posts.favorite', 'DESC');
            } elseif ($filter === 'recent') {
                $query->orderBy('publish_date', 'desc');
            } elseif ($filter === 'video_posts') {
                $query->where('type', 'video');
            } elseif ($filter === 'youtube_videos') {
                $query->where('video_type', 'youtube');
            } else {
                $query->orderBy('id', 'desc');
            }
            /****** Filter of Channels *********/
            if ($channel !== '' && $channel !== '*') {
                $query->where('channels.id', $channel);
            }
            /****** Filter of Topics *********/
            if ($topic !== '' && $topic !== '*') {
                $query->where('topics.id', $topic);
            }
            /****** Filter of Search News *********/
            if ($request->has('search') && $request->search) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('posts.title', 'like', '%' . $search . '%')
                        ->orWhere('channels.name', 'like', '%' . $search . '%')
                        ->orWhere('topics.name', 'like', '%' . $search . '%');
                });
            }

            $getPosts = $query->paginate(12);

            // Format the publish_date field for each post
            $getPosts->getCollection()->transform(function ($post) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                return $post;
            });

            return response()->json([
                'data'         => $getPosts->items(),
                'total'        => $getPosts->total(),
                'last_page'    => $getPosts->lastPage(),
                'current_page' => $getPosts->currentPage(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching posts'], 500);
        }
    }

    public function edit(string $id)
    {
        ResponseService::noPermissionThenRedirect('update-custom-VideoPost');
        $title           = __('page.EDIT_VIDEOS');
        $url             = route('videos.update', $id); // Use named route for update
        $method          = "PUT";
        $formID          = "editVideoPostForm";
        $post            = Post::findOrFail($id); // Use findOrFail for better error handling
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::where('status', 'active')->get();

        $data = compact('title', 'channel_filters', 'news_topics', 'post', 'url', 'method', 'formID', 'news_languages');
        return view('admin.videos.edit_custom', $data);
    }

    public function edit_youtube(string $id)
    {
        ResponseService::noPermissionThenRedirect('update-youtube-VideoPost');
        $title           = __('page.EDIT_VIDEOS');
        $url             = route('videos.update_youtube', $id); // Use named route for update
        $method          = "PUT";
        $formID          = "editVideoPostForm";
        $post            = Post::findOrFail($id); // Use findOrFail for better error handling
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::select('id', 'name')->where('status', 'active')->get();

        // Fetch news language status
        $news_language_status = NewsLanguageStatus::getCurrentStatus();

        $data = compact('title', 'channel_filters', 'news_topics', 'post', 'url', 'method', 'formID', 'news_languages', 'news_language_status');
        return view('admin.videos.edit_youtube', $data);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        ResponseService::noPermissionThenRedirect('update-custom-VideoPost');

        // Fetch the existing post
        $post = Post::findOrFail($id);

        // Validation
        $request->validate([
            'title'            => 'required|string|max:255|unique:posts,title,' . $post->id,
            'description'      => 'required|string',
            'news_language_id' => 'required|integer|exists:news_languages,id',
            'channel_id'       => 'required|integer|exists:channels,id',
            'topic_id'         => 'nullable|integer|exists:topics,id',
            'post_type'        => 'nullable|string|in:video,post',
            'status'           => 'nullable|string|in:active,inactive',
            'video'            => [
                ($request->post_type == 'video' && ! $post->video) ? 'required' : 'nullable',
                'file',
                'mimes:mp4,mov,ogg,qt,webm',
                'max:100240',
            ],

            'thumb_image'      => [
                ($request->post_type == 'video' && ! $post->video_thumb) ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp,svg',
                'max:5120',
            ],
        ]);

        try {
            $post = Post::findOrFail($id);

            $slug = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'video-' . uniqid();
            }

            $originalSlug = $slug;
            $counter      = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $image       = $post->image;
            $video_thumb = $post->video_thumb;
            $video       = $post->video;

            if ($request->post_type == 'post') {
                if ($request->hasFile('image')) {
                    $image = $this->storePostImage($request->file('image'), $post);
                }
            } else {
                if ($request->hasFile('thumb_image')) {
                    $video_thumb = $this->storeThumbImage($request->file('thumb_image'), $post) ?? null;
                }
                if ($request->hasFile('video')) {
                    $video = $this->storeVideo($request->file('video'), $post);
                }
            }
            $pubDate = Carbon::now()->toDateTimeString();
            $post->update([
                'title'            => $request->title,
                'slug'             => $slug,
                'type'             => $request->post_type,
                'news_language_id' => $request->news_language_id,
                'description'      => $request->description,
                'channel_id'       => $request->channel_id ?? $post->channel_id,
                'topic_id'         => $request->topic_id ?? $post->topic_id,
                'status'           => $request->status,
                'image'            => $image,
                'video_thumb'      => $video_thumb,
                'video'            => $video,
                'pubdate'          => $pubDate,
                'publish_date'     => Carbon::parse($pubDate),
            ]);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Video Post updated successfully.',
                'redirect' => url('admin/videos'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error', // ← Changed to 'status'
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update_youtube(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('update-youtube-VideoPost');

        // Fetch the existing post
        $post = Post::findOrFail($id);

        // Validation — use Validator::make() so errors are returned as JSON for AJAX
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255|unique:posts,title,' . $post->id,
            'description'      => 'required|string',
            'news_language_id' => 'required|integer|exists:news_languages,id',
            'channel_id'       => 'required|integer|exists:channels,id',
            'topic_id'         => 'nullable|integer|exists:topics,id',
            'post_type'        => 'nullable|string|in:youtube',
            'status'           => 'nullable|string|in:active,inactive',
            'video_url'        => 'required|url',
            // 'image'            => [
            //     !$post->image ? 'required' : 'nullable',
            //     'image',
            //     'mimes:jpeg,png,jpg,gif,webp',
            //     'max:5120',
            // ],
            'image'            => [
                ($request->post_type == 'youtube' && ! $post->image) ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp,svg',
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
            // Generate unique slug
            $slug = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'youtube-' . uniqid();
            }

            $originalSlug = $slug;
            $counter      = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $image = $post->image;
            if ($request->post_type == 'youtube') {
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
            if ($request->post_type === "youtube") {
                if ($request->filled('video_url')) {
                    $videoUrl   = trim($request->video_url);
                    $videoId    = null;
                    $videoEmbed = null;

                    $post->is_short_video = 0;

                    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/shorts\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                        $videoId              = $matches[1];
                        $post->is_short_video = 1;
                    }

                    if (! $videoId) {
                        return response()->json([
                            'status' => false,
                            'errors' => ['video_url' => [__('Invalid YouTube URL. Please provide a correct video link.')]],
                        ], 422);
                    }

                    if (preg_match('/list=([a-zA-Z0-9_-]+)/', $videoUrl, $listMatch)) {
                        $videoEmbed = "https://www.youtube.com/embed/{$videoId}?list={$listMatch[1]}";
                    } else {
                        $videoEmbed = "https://www.youtube.com/embed/{$videoId}";
                    }

                    $post->video_url   = $videoUrl;
                    $post->video_embed = $videoEmbed;

                } else {
                    return response()->json([
                        'status' => false,
                        'errors' => ['video_url' => [__('Video URL is required')]],
                    ], 422);
                }
            }
            $post->title            = $request->title;
            $post->slug             = $slug;
            $post->type             = $request->post_type;
            $post->news_language_id = $request->news_language_id ?? $post->news_language_id;
            $post->description      = $request->description;
            $post->channel_id       = $request->channel_id;
            $post->status           = $request->status;
            $post->image            = $image ?? null;
            $post->video_type       = 'youtube';
            $post->video_thumb      = $image ?? null;
            $post->video_url        = $videoUrl ?? $post->video_url;
            $post->video_embed      = $videoEmbed ?? $post->video_embed;
            $post->video            = $videoEmbed ?? $post->video;
            $post->pubdate          = Carbon::now()->toDateTimeString();
            $post->publish_date     = Carbon::parse($post->pubdate);
            $post->is_video         = 1;

            $post->save();

            session()->flash('video_filter', 'youtube_videos');
            return response()->json([
                'status'   => 'success', // ← Changed to 'status'
                'message'  => 'Youtube Video Post updated successfully.',
                'redirect' => url('admin/videos'), // ← Added redirect URL
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
            ResponseService::noPermissionThenSendJson(['delete-custom-VideoPost', 'delete-youtube-VideoPost']);
            Favorite::where('post_id', $id)->delete();
            $post = Post::find($id);
            if ($post->type == 'video') {

                $baseUrl = URL::to('storage/');

                $filePath = str_replace($baseUrl, '', $post->video_thumb);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }

                $videoFilePath = str_replace($baseUrl, '', $post->video);
                if (Storage::disk('public')->exists(ltrim($videoFilePath, '/'))) {
                    Storage::disk('public')->delete(ltrim($videoFilePath, '/'));
                }
            } else {
                $baseUrl = URL::to('storage/');

                $filePath = str_replace($baseUrl, '', $post->image);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            $post->delete();
            ResponseService::successResponse("Video Post deleted successfully.");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }

    public function storePostImage($image, $post)
    {
        $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
        $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();

        if (isset($s3_bucket_url, $s3_bucket_name) && $s3_bucket_url->value && $s3_bucket_name->value) {
            $posts_img_name = getFileName($image);
            if ($posts_img_name) {
                uploadFileS3Bucket($image, $posts_img_name, $this->post_image_path, $post->image);
                $s3_image = $s3_bucket_url->value . $this->post_image_path . $posts_img_name;
            }
        } else {
            if ($image) {
                $baseUrl = URL::to('storage/');

                $filePath = str_replace($baseUrl, '', $post->image);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            if ($image) {
                $fileName      = rand('0000', '9999') . $image->getClientOriginalName();
                $imageFilePath = $image->storeAs('posts_image', $fileName, 'public');
                $imageUrl      = url(Storage::url($imageFilePath));
            }
        }

        return $s3_image ?? $imageUrl;
    }

    public function storeThumbImage($thumbImage, $post)
    {
        $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
        $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();

        if (isset($s3_bucket_url, $s3_bucket_name) && $s3_bucket_url->value && $s3_bucket_name->value) {
            $posts_thumb_name = getFileName($thumbImage);
            if ($posts_thumb_name) {
                uploadFileS3Bucket($thumbImage, $posts_thumb_name, $this->thumbnail_image_path, $post->image);
                $s3_image = $s3_bucket_url->value . $this->thumbnail_image_path . $posts_thumb_name;
            }
        } else {
            if ($thumbImage) {
                $baseUrl = URL::to('storage/');

                $filePath = str_replace($baseUrl, '', $post->video_thumb);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            if ($thumbImage) {
                $fileName      = rand('0000', '9999') . $thumbImage->getClientOriginalName();
                $imageFilePath = $thumbImage->storeAs('thumb_image', $fileName, 'public');
                $imageUrl      = url(Storage::url($imageFilePath));
            }
        }

        return $s3_image ?? $imageUrl;
    }

    public function storeVideo($video, $post)
    {
        $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
        $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();

        if (isset($s3_bucket_url, $s3_bucket_name) && $s3_bucket_url->value && $s3_bucket_name->value) {
            $video_name = getFileName($video);
            if ($video_name) {
                uploadFileS3Bucket($video, $video_name, $this->video_path, $post->image);
                $s3_image = $s3_bucket_url->value . $this->video_path . $video_name;
            }
        } else {
            if ($video) {
                $baseUrl = URL::to('storage/');

                $videoFilePath = str_replace($baseUrl, '', $post->video);
                if (Storage::disk('public')->exists($videoFilePath)) {
                    Storage::disk('public')->delete($videoFilePath);
                }
            }
            if ($video) {
                $fileName      = rand('0000', '9999') . $video->getClientOriginalName();
                $imageFilePath = $video->storeAs('posts_videos', $fileName, 'public');
                $imageUrl      = url(Storage::url($imageFilePath));
            }
        }

        return $s3_image ?? $imageUrl;
    }
}
