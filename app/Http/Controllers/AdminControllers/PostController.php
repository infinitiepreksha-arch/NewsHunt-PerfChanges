<?php
namespace App\Http\Controllers\AdminControllers;

use App\Events\SendNotification;
use App\Http\Controllers\Controller;
use App\Models\Admin\Notifications as AdminNotifications;
use App\Models\Channel;
use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostLink;
use App\Models\Setting;
use App\Models\Topic;
use App\Services\CachingService;
use App\Services\NotificationService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class PostController extends Controller
{
    private $post_image_path        = "";
    private $thumbnail_image_path   = "";
    private $video_path             = "";
    private $post_extra_images_path = "";
    public function __construct()
    {
        $this->post_image_path        = "posts/post_images/";
        $this->thumbnail_image_path   = "posts/thumbnail_images/";
        $this->video_path             = "posts/videos/";
        $this->post_extra_images_path = "posts_extra_images/";
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['list-post', 'create-post', 'update-post', 'delete-post', 'select-topic-for-post', 'select-channel-for-post', '', 'select-newslanguage-for-post', 'send-notification-any-post', 'view-comment-any-post']);
        $title           = __('page.IMAGE_POSTS');
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $topics          = Topic::select('id', 'name')->where('status', 'active')->get();
        $posts           = Post::all() ?? collect([]);
        $post_id         = $request->query('post_id', $posts->first()->id ?? 0);
        $selectedPost    = $posts->where('id', $post_id)->first();
        return view('admin.post.index', compact('title', 'channel_filters', 'topics', 'posts', 'selectedPost', 'post_id'));
    }

    public function listPost(Request $request)
    {

        $title = __('page.CHOOSE_POST_FORMAT');
        return view('admin.post.post-format', compact('title'));
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_ids'   => 'required|array',
            'post_ids.*' => 'integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            Post::whereIn('id', $request->post_ids)->delete();
            return response()->json([
                'status'  => 'success',
                'message' => count($request->post_ids) . ' posts deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete posts',
            ], 500);
        }
    }

    public function sendNotification(Post $post)
    {
        $settings = CachingService::getSystemSettings();
        if (($settings['automatic_notifications'] ?? 1) == 0) {
            ResponseService::errorResponse('Currently you can\'t send notification because notification setting disabled so enable this first');
        }
        if (! NotificationService::isNotificationAllowed()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Daily notification limit reached. You cannot send more notifications today.',
            ]);
        }
        try {
            $user_id         = 'all';
            $fcmIds          = NotificationService::getFcmTokensForPost($post);
            $registrationIDs = array_filter($fcmIds);

            if ($post->type == 'post') {
                $imageUrl = $post->image;
            } elseif ($post->type == 'video') {
                $imageUrl = $post->video_thumb;
            } elseif ($post->type == 'youtube') {
                $imageUrl = $post->image;
            } elseif ($post->type == 'audio') {
                $imageUrl = $post->image;
            }

            $notification = AdminNotifications::create([
                'send_to'          => 'all',
                'title'            => $post->title,
                'slug'             => $post->slug,
                'message'          => strip_tags($post->description),
                'image'            => $post->image ?? url('/assets/images/no_image_available.png'),
                'news_language_id' => $post->news_language_id,
            ]);

            if (! empty($registrationIDs)) {
                $slug = $post->slug ?? 'notification';
                event(new SendNotification(
                    $post->title,
                    $post->description,
                    $imageUrl,
                    $slug,
                    $registrationIDs,
                    $post->news_language_id
                ));
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Notification processed successfully!',
            ]);
        } catch (\Throwable $th) {
            Log::error('Notification dispatch failed: ' . $th->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send notification.',
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title                = __('page.CREATE_POSTS');
        $url                  = url('admin/posts');
        $method               = "POST";
        $formID               = "addPostForm";
        $hasExtraImagesCount  = PostImage::where('post_id', 0)->count();
        $hasMultipleUrlsCount = PostLink::where('post_id', 0)->count();
        $channel_filters      = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics          = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages       = NewsLanguage::where('status', 'active')->get();
        return view('admin.post.create', compact('title', 'channel_filters', 'news_topics', 'url', 'method', 'formID', 'news_languages', 'hasExtraImagesCount', 'hasMultipleUrlsCount'));
    }
    /**
     * Store a newly created resource in storage.s
     */
    public function store(Request $request, Post $post)
    {
        ResponseService::noPermissionThenRedirect('create-post');
        $validator = Validator::make($request->all(), [
            'title'                                      => 'required|string|max:255|unique:posts,title',
            'description'                                => 'required|string',
            'news_language_id'                           => 'required|integer|exists:news_languages,id',
            'channel_id'                                 => 'required|integer|exists:channels,id',
            'topic_id'                                   => 'required|integer|exists:topics,id',
            'status'                                     => 'nullable|string|in:active,inactive',
            $request->post_type == 'post' ? 'image' : '' => $request->post_type == 'post' ? 'required|max:100240|mimes:jpg,jpeg,png,webp,svg' : '',
            'has_extra_images'                           => 'required|boolean',
            'extra_images'                               => 'required_if:has_extra_images,1|array',
            'extra_images.*'                             => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp',
        ], [
            'extra_images.required_if' => 'Please upload at least one extra image.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $defaultImage   = Setting::where('name', 'default_image')->value('value');
        $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
        $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();
        $s3_image       = $image       = null;
        $audio_path     = null;
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
        } else {
            /* Store the Post Image locally */
            $imageFile = $request->file('image');
            if ($imageFile) {
                $imageFileName = rand('0000', '9999') . $imageFile->getClientOriginalName();
                $imageFilePath = \App\Services\FileService::resizeAndCompressUpload($imageFile, 'posts_image', 800, $imageFileName);
                $image         = url(Storage::url($imageFilePath));
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
        $post->status         = $request->status;
        $post->is_custom_post = true;
        $post->image          = $s3_image ?? $image ?? null;
        $post->pubdate        = $pubDate;
        $post->publish_date   = $publishDate;
        $save                 = $post->save();

        if ($save) {
            if ($request->hasFile('extra_images')) {
                foreach ($request->file('extra_images') as $extraImage) {
                    $extraImagePath = null;
                    if (isset($s3_bucket_name->value) && $s3_bucket_name->value !== "" && isset($s3_bucket_url)) {
                        $extraImgName = 'extra_' . rand('0000', '9999') . '.' . $extraImage->getClientOriginalExtension();
                        uploadFileS3Bucket($extraImage, $extraImgName, $this->post_extra_images_path);
                        $extraImagePath = $s3_bucket_url->value . $this->post_extra_images_path . $extraImgName;
                    } else {
                        $extraImgName   = 'extra_' . rand('0000', '9999') . '.' . $extraImage->getClientOriginalExtension();
                        $extraImgPath   = \App\Services\FileService::resizeAndCompressUpload($extraImage, 'posts_extra_images', 800, $extraImgName);
                        $extraImagePath = url(Storage::url($extraImgPath));
                    }
                    PostImage::create([
                        'post_id' => $post->id,
                        'image'   => $extraImagePath,
                    ]);
                }
            }
            return response()->json([
                'status'   => 'success',
                'message'  => 'Post created successfully.',
                'redirect' => url('admin/posts'),
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        ResponseService::noPermissionThenRedirect(['list-post']);
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
                ->where('posts.type', 'post');
            /****** Filter for Most View,Likes & Recent News *********/
            if ($filter === 'viewd') {
                $query->orderBy('posts.view_count', 'DESC');
            } elseif ($filter === 'liked') {
                $query->orderBy('posts.favorite', 'DESC');
            } elseif ($filter === 'recent') {
                $query->orderBy('publish_date', 'desc');
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
        $title           = __('page.EDIT_POST');
        $url             = route('posts.update', $id);
        $method          = "PUT";
        $formID          = "editPostForm";
        $post            = Post::with(['images', 'links'])->findOrFail($id);
        $channel_filters = Channel::select('id', 'name')->where('status', 'active')->get();
        $news_topics     = Topic::select('id', 'name')->where('status', 'active')->get();
        $news_languages  = NewsLanguage::where('status', 'active')->get();
        // Prepare old data
        $hasExtraImages       = $post->images->count() > 0;
        $hasMultipleUrls      = $post->links->count() > 0;
        $hasExtraImagesCount  = PostImage::where('post_id', $post->id)->count();
        $hasMultipleUrlsCount = PostLink::where('post_id', $post->id)->count();
        $hasAudio             = ! empty($post->audio);
        $data                 = compact(
            'hasExtraImagesCount',
            'hasMultipleUrlsCount',
            'title',
            'channel_filters',
            'news_topics',
            'post',
            'url',
            'method',
            'formID',
            'news_languages',
            'hasExtraImages',
            'hasMultipleUrls',
            'hasAudio'
        );
        return view('admin.post.create', $data);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('update-post');
        $post = Post::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255|unique:posts,title,' . $id,
            'description'      => 'required|string',
            'news_language_id' => 'required|integer|exists:news_languages,id',
            'channel_id'       => 'required|integer|exists:channels,id',
            'topic_id'         => 'required|integer|exists:topics,id',
            'status'           => 'nullable|string|in:active,inactive',
            'image'            => [
                ($request->post_type == 'post' && ! $post->image) ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,svg',
                'max:100240',
            ],
            'has_extra_images' => 'required|boolean',
            'extra_images'     => 'nullable|array',
            'extra_images.*'   => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp',
        ]);

        if ($request->has_extra_images == 1) {
            $existingCount = PostImage::where('post_id', $id)->count();
            $deletedIds    = $request->filled('deleted_extra_image_ids') ? json_decode($request->deleted_extra_image_ids, true) : [];
            $newFilesCount = $request->hasFile('extra_images') ? count($request->file('extra_images')) : 0;
            $totalCount    = $existingCount - count($deletedIds) + $newFilesCount;

            if ($totalCount <= 0) {
                return response()->json([
                    'status' => false,
                    'errors' => ['extra_images' => ['Please upload at least one extra image.']],
                ], 422);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $post           = Post::findOrFail($id);
            $defaultImage   = Setting::where('name', 'default_image')->value('value');
            $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
            $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();
            $isS3Enabled    = isset($s3_bucket_name->value) && $s3_bucket_name->value !== "" && isset($s3_bucket_url->value);
            $slug           = Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'post-' . uniqid();
            }

            $originalSlug = $slug;
            $counter      = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            // Handle main image update
            $image = $post->image;
            if ($request->post_type == 'post' && $request->hasFile('image')) {
                // Delete old image if it exists and is not default
                if ($post->image && $post->image != url('storage/' . $defaultImage) && $post->image != url('front_end/classic/images/default/post-placeholder.jpg')) {
                    if ($isS3Enabled) {
                        // Extract filename and path for S3 deletion (adjust based on your storage logic)
                        $filename = basename(parse_url($post->image, PHP_URL_PATH));
                        $key      = str_replace($s3_bucket_url->value, '', $post->image);
                        $key      = ltrim($key, '/'); // Assuming deleteFileS3Bucket($filename, $this->post_image_path) exists
                                                      // deleteFileS3Bucket($filename, $this->post_image_path);
                    } else {
                        $oldPath = str_replace(url('/storage/'), '', $post->image);
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                // Upload new image
                $s3_image = null;
                if ($isS3Enabled) {
                    $postImage = $request->file('image');
                    $posts_img = getFileName($postImage);
                    if ($posts_img) {
                        uploadFileS3Bucket($postImage, $posts_img, $this->post_image_path);
                        $s3_image = $s3_bucket_url->value . $this->thumbnail_image_path . $posts_img;
                    }
                } else {
                    $imageFile     = $request->file('image');
                    $imageFileName = rand(1000, 9999) . $imageFile->getClientOriginalName();
                    $imageFilePath = \App\Services\FileService::resizeAndCompressUpload($imageFile, 'posts_image', 800, $imageFileName);
                    $image         = url(Storage::url($imageFilePath));
                }
                $image = $s3_image ?? $image;
            }

            // Handle deletions for extra images
            if ($request->filled('deleted_extra_image_ids')) {
                $deletedIds = json_decode($request->deleted_extra_image_ids, true);
                if (is_array($deletedIds)) {
                    foreach ($deletedIds as $delId) {
                        $img = PostImage::find($delId);
                        if ($img && $img->post_id == $post->id) {
                            $imageUrl = $img->image;
                            if ($imageUrl && $imageUrl != url('assets/images/no_image_available.png')) {
                                if ($isS3Enabled) {
                                    $filename   = basename(parse_url($imageUrl, PHP_URL_PATH));
                                    $uploadPath = $this->post_extra_images_path;
                                    deleteFileS3Bucket($filename, $uploadPath);
                                } else {
                                    $oldPath = str_replace(url('/storage/'), '', $imageUrl);
                                    if (Storage::disk('public')->exists($oldPath)) {
                                        Storage::disk('public')->delete($oldPath);
                                    }
                                }
                            }
                            $img->delete();
                        }
                    }
                }
            }
            if ($request->has('extra_images') && is_array($request->file('extra_images'))) {
                foreach ($request->file('extra_images') as $extraImage) {
                    if ($extraImage) {
                        $extraImagePath = null;
                        if ($isS3Enabled) {
                            $extraImgName = 'extra_' . rand(1000, 9999) . '.' . $extraImage->getClientOriginalExtension();
                            uploadFileS3Bucket($extraImage, $extraImgName, $this->post_extra_images_path);
                            $extraImagePath = $s3_bucket_url->value . $this->post_extra_images_path . $extraImgName;
                        } else {
                            $extraImgName   = 'extra_' . rand(1000, 9999) . '.' . $extraImage->getClientOriginalExtension();
                            $extraImgPath   = \App\Services\FileService::resizeAndCompressUpload($extraImage, 'posts_extra_images', 800, $extraImgName);
                            $extraImagePath = url(Storage::url($extraImgPath));
                        }
                        PostImage::create([
                            'post_id' => $post->id,
                            'image'   => $extraImagePath,
                        ]);
                    }
                }
            }
            // Update post fields
            $pubDate                = Carbon::now()->toDateTimeString();
            $post->title            = $request->title;
            $post->slug             = $slug;
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
            $post->pubdate      = $pubDate;
            $post->publish_date = Carbon::parse($pubDate);
            $post->save();
            return response()->json([
                'status'   => 'success',
                'message'  => 'Post updated successfully.',
                'redirect' => url('admin/posts'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
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
            ResponseService::noPermissionThenSendJson('delete-post');
            Favorite::where('post_id', $id)->delete();
            $post           = Post::find($id);
            $s3_bucket_name = Setting::where('name', 's3_bucket_name')->first();
            $s3_bucket_url  = Setting::where('name', 's3_bucket_url')->first();
            $isS3Enabled    = isset($s3_bucket_name->value) && $s3_bucket_name->value !== "" && isset($s3_bucket_url->value);
            if ($post->type == 'video') {
                $baseUrl  = URL::to('storage/');
                $filePath = str_replace($baseUrl, '', $post->video_thumb);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $videoFilePath = str_replace($baseUrl, '', $post->video);
                if (Storage::disk('public')->exists(ltrim($videoFilePath, '/'))) {
                    Storage::disk('public')->delete(ltrim($videoFilePath, '/'));
                }
            } else {
                $baseUrl  = URL::to('storage/');
                $filePath = str_replace($baseUrl, '', $post->image);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            // Delete extra images
            $extraImages = PostImage::where('post_id', $id)->get();
            foreach ($extraImages as $img) {
                $imageUrl = $img->image;
                if ($imageUrl && $imageUrl != url('assets/images/no_image_available.png')) {
                    if ($isS3Enabled) {
                        $filename   = basename(parse_url($imageUrl, PHP_URL_PATH));
                        $uploadPath = $this->post_extra_images_path;
                        deleteFileS3Bucket($filename, $uploadPath);
                    } else {
                        $oldPath = str_replace(url('/storage/'), '', $imageUrl);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
                $img->delete();
            }
            $post->delete();
            ResponseService::successResponse("Post deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "PlaceController -> destroyCountry");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
    public function storePostImages($image, $post)
    {

        if ($post->image) {
            $baseUrl  = URL::to('/storage');
            $filePath = str_replace($baseUrl . '/', '', $post->image);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $fileName      = rand('0000', '9999') . $image->getClientOriginalName();
        $imageFilePath = \App\Services\FileService::resizeAndCompressUpload($image, 'posts_image', 800, $fileName);
        return url(Storage::url($imageFilePath));
        return $post->image;
    }
}
