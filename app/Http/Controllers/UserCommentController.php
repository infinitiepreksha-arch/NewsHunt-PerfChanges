<?php
namespace App\Http\Controllers;

use App\Models\BlockedComment;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ReportComment;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserCommentController extends Controller
{
    public function show($postId)
    {
        $currentUserId = Auth::id();

        // Get blocked comment IDs by current user
        $blockedCommentIds = [];
        if ($currentUserId) {
            $blockedCommentIds = BlockedComment::where('blocker_user_id', $currentUserId)
                ->pluck('comment_id')
                ->toArray();
        }

        $allComments = Comment::with(['user'])
            ->where('post_id', $postId)
            ->whereNotIn('id', $blockedCommentIds) // Exclude blocked comments
            ->orderBy('created_at', 'desc')
            ->get();

        $removedCommentIds = ReportComment::where('status', 'removed')
            ->whereIn('comment_id', $allComments->pluck('id')->toArray())
            ->pluck('comment_id')
            ->toArray();

        $processedComments = $allComments->map(function ($comment) use ($currentUserId, $removedCommentIds) {

            $isRemoved = in_array($comment->id, $removedCommentIds);

            if ($isRemoved) {
                if ($comment->user_id != $currentUserId) {
                    return null;
                }
                $comment->is_removed = true;
                $comment->comment    = __('frontend-labels.comments.removed_by_admin');
            }

            return $comment;
        })->filter();

        $parentComments = $processedComments->where('parent_id', null)->values();

        $finalComments = $parentComments->map(function ($parent) use ($processedComments) {
            $replies = $processedComments->where('parent_id', $parent->id)->values();
            $parent->setRelation('replies', $replies);
            return $parent;
        });

        $visibleCount = $finalComments->sum(function ($comment) {
            return 1 + ($comment->replies ? $comment->replies->count() : 0);
        });

        return response()->json([
            'comments'            => $finalComments,
            'count'               => $visibleCount,
            'current_user_id'     => $currentUserId,
            'removed_comment_ids' => $removedCommentIds,
        ]);
    }
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->is_blocked && $user->block_type === 'comment') {
                return response()->json([
                    'success' => false,
                    'message' => 'You are restricted from adding or replying to comments due to moderation.',
                ], 403);
            }

            $request->validate([
                'comment'   => 'required|string|max:1000',
                'post_id'   => 'required|exists:posts,id',
                'parent_id' => 'nullable|exists:comments,id',
                'name'      => [
                    'sometimes',
                    'string',
                    Rule::in([$user->name]),
                ],
                'email'     => [
                    'sometimes',
                    'email',
                    Rule::in([$user->email]),
                ],
            ]);

            $comment = Comment::create([
                'user_id'   => $user->id,
                'post_id'   => $request->post_id,
                'parent_id' => $request->parent_id,
                'comment'   => $request->comment,
            ]);

            $post = Post::find($request->post_id);
            $post->increment('comment');

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => __('frontend-labels.comments.stored_successfully'),
                'comment' => $comment,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->is_blocked && $user->block_type === 'comment') {
                return response()->json([
                    'success' => false,
                    'message' => 'You are restricted from updating comments due to moderation.',
                ], 403);
            }

            $request->validate([
                'comment' => 'required|string|max:1000',
                'id'      => 'required|exists:comments,id',
                'name'    => [
                    'sometimes',
                    'string',
                    Rule::in([$user->name]),
                ],
                'email'   => [
                    'sometimes',
                    'email',
                    Rule::in([$user->email]),
                ],
            ]);

            $comment = Comment::where('id', $request->id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $comment->comment = $request->comment;
            $comment->save();

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => __('frontend-labels.comments.updated_successfully'),
                'comment' => $comment,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $comment = Comment::find($id);

            if ($comment) {

                Comment::where('parent_id', $id)->delete();
                $comment->delete();

                $post = Post::find($comment->post_id);

                if ($post) {
                    $post->decrement('comment');
                }
                ResponseService::successResponse(__('frontend-labels.comments.deleted_successfully'));
            } else {
                ResponseService::errorResponse('No Comment found');
            }
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ChannelControler -> destroyChannel");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
