<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

const UNAUTHORIZED_USER = 'Unauthorized user.';
class FavoriteController extends Controller
{
    /***** Manage user liked post *****/
    public function toggleFavorite(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => UNAUTHORIZED_USER,
            ], 401);
        }

        $validatedData = $request->validate([
            'id' => ['required', 'exists:posts,id'],
        ]);

        try {
            $post = Post::findOrFail($validatedData['id']);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'No post found.',
            ], 404);
        }

        $userId   = Auth::id();
        $postId   = $validatedData['id'];
        $favorite = Favorite::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $post->decrement('favorite');
            $status  = '0';
            $posrId  = $postId;
            $message = 'Favorite removed';
        } else {

            Favorite::create([
                'user_id' => $userId,
                'post_id' => $postId,
            ]);
            $post->increment('favorite');
            $status  = '1';
            $posrId  = $postId;
            $message = 'Favorite added';
        }

        return response()->json([
            'error'   => false,
            'status'  => $status,
            'postId'  => $posrId,
            'message' => $message,
        ]);
    }

    public function getPosts(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;

            $page    = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $offset  = ($page - 1) * $perPage;

            $newsLanguageId = $request->news_language_id;
            $postType       = $request->post_type;
            $allowedTypes   = [];

            if ($postType === 'all' || $postType == null) {
                $allowedTypes = [];
            } elseif ($postType === 'post') {
                $allowedTypes = ['post'];
            } elseif ($postType === 'audio') {
                $allowedTypes = ['audio'];
            } elseif ($postType === 'video') {
                $allowedTypes = ['video', 'youtube'];
            }

            $newsLanguageCode = null;

            // Validate news_language_id and fetch its code if valid
            if ($newsLanguageId) {
                $newsLanguage = NewsLanguage::find($newsLanguageId);
                if ($newsLanguage) {
                    $newsLanguageCode = $newsLanguage->code;
                }
            }

            $bookmarks = Favorite::select('favorites.id', 'posts.id as post_id', 'posts.description', 'is_pinned', 'posts.status', 'posts.title', 'posts.type', 'posts.video_thumb', 'posts.slug', 'posts.image', 'posts.publish_date', 'posts.favorite')
                ->leftJoin('posts', 'favorites.post_id', '=', 'posts.id')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->orderBy('is_pinned', 'desc')
                ->where('posts.status', 'active')
                ->when($newsLanguageId, function ($query) use ($newsLanguageId) {
                    $query->where('posts.news_language_id', $newsLanguageId);
                })
                ->when(! empty($allowedTypes), function ($query) use ($allowedTypes) {
                    $query->whereIn('posts.type', $allowedTypes);
                })
                ->where('favorites.user_id', $userId)
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $item->video_thumb  = $item->video_thumb == null ? "" : $item->video_thumb;
                    $item->image        = $item->image == null ? "" : $item->image;
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    $item->is_favorit   = '1';
                    return $item;
                });

            return response()->json([
                'error'   => false,
                'message' => $bookmarks->isEmpty() ? 'No posts found' : 'Posts fetched successfully',
                'data'    => $bookmarks,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => "User is not authenticated.",
            ]);
        }

    }

    /***** Manage user store bookmark *****/
    public function addToggleFavorite(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => UNAUTHORIZED_USER,
            ], 401);
        }

        $post = Post::where('slug', $request->slug)->first();

        $userId = Auth::id();

        $favorite = Favorite::where('user_id', $userId)
            ->where('post_id', $post->id)->first();

        if ($favorite != "") {
            $status  = '0';
            $message = 'Aleary exest.';
        } else {
            Favorite::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $post->increment('favorite');
            $status  = '1';
            $message = 'Favorite added';
        }

        return response()->json([
            'error'   => false,
            'status'  => $status,
            'message' => $message,
        ]);
    }

    /***** Manage user remove bookmark *****/
    public function removeToggleFavorite(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => UNAUTHORIZED_USER,
            ], 401);
        }

        $post = Post::where('slug', $request->slug)->first();

        $userId   = Auth::id();
        $favorite = Favorite::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();
        if ($favorite != "") {
            $favorite->delete();
            $post->decrement('favorite');
            $status  = '0';
            $message = 'Favorite removed';
        } else {
            $status  = '0';
            $message = 'Post not found.';
        }

        return response()->json([
            'error'   => false,
            'status'  => $status,
            'message' => $message,
        ]);
    }

    /***** Manage user Bookmarked posts *****/
    public function pinToggle(Request $request)
    {
        try {
            // Validate input (accepts both query params and body)
            $validated = $request->validate([
                'favorite_id' => 'required|integer|exists:favorites,id',
                'is_pinned'   => 'required|in:true,false,1,0', // Accept string or boolean
            ], [
                'favorite_id.required' => 'Favorite post ID is required.',
                'favorite_id.exists'   => 'Favorite post does not exist.',
                'is_pinned.required'   => 'Pin status is required.',
                'is_pinned.in'         => 'Pin status must be true or false.',
            ]);

            // Convert string 'true'/'false' to boolean
            $isPinned = filter_var($validated['is_pinned'], FILTER_VALIDATE_BOOLEAN);

            // Get authenticated user
            $userId = Auth::id();

            if (! $userId) {
                return response()->json([
                    'error'   => true,
                    'message' => 'User is not authenticated.',
                    'data'    => [],
                ], 401);
            }

            // Find the favorite post for this user
            $favorite = Favorite::where('id', $validated['favorite_id'])
                ->where('user_id', $userId)
                ->first();

            if (! $favorite) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Favorite post not found for this user.',
                ], 404);
            }

            // Update pin status
            $favorite->is_pinned = $isPinned;
            $favorite->save();

            return response()->json([
                'error'   => false,
                'message' => $isPinned
                    ? 'Post pinned successfully.'
                    : 'Post unpinned successfully.',
                'data'    => [
                    'favorite_id' => $favorite->id,
                    'post_id'     => $favorite->post_id ?? null,
                    'is_pinned'   => (bool) $favorite->is_pinned,
                    'pinned_at'   => $favorite->updated_at,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'An error occurred while updating pin status.',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
