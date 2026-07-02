<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\BlockedComment;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ReportComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCommentController extends Controller
{
    const COMMENT_VALIDATION = 'required|string|max:500';

    public function store(Request $request)
    {
        $request->validate([
            'comment'   => self::COMMENT_VALIDATION,
            'post_id'   => 'required|exists:posts,id',
            'replay_id' => 'nullable|exists:comments,id',
        ]);

        $user = Auth::user();

        if ($user && $user->is_blocked && $user->block_type === 'comment') {
            return response()->json([
                'error'   => true,
                'message' => 'You are restricted from adding or replying to comments due to moderation.',
            ], 403);
        }

        if ($user) {
            $comment = Comment::create([
                'user_id'   => $user->id,
                'post_id'   => $request->post_id,
                'parent_id' => $request->replay_id,
                'comment'   => $request->comment,
            ]);

            $post = Post::find($request->post_id);
            $post->increment('comment');

            $comment->load('user');

            $data = [
                'post_id'   => $request->post_id,
                'parent_id' => $request->replay_id ?? '0',
                'comment'   => $request->comment,
                'status'    => '1',
            ];

            return response()->json([
                'error'   => false,
                'message' => "Comment store successfully.",
                'data'    => $data,
            ], 200);
        } else {

            $data = [
                'post_id'   => $request->post_id,
                'parent_id' => $request->replay_id ?? '0',
                'comment'   => $request->comment,
                'status'    => '0',
            ];

            return response()->json([
                'error'   => false,
                'message' => "Unauthorized user..",
                'data'    => $data,
            ], 201);
        }
    }

    public function show($id)
    {
        $postId  = $id;
        $perPage = request()->get('per_page', 3);

        $currentUserId = Auth::check() ? Auth::id() : null;
        $currentUser   = Auth::check() ? Auth::user() : null;
        $token         = Auth::check()
            ? $currentUser->createToken('AuthToken')->plainTextToken
            : null;
        try {

            // Get blocked comment IDs by current user
            $blockedCommentIds = [];
            if ($currentUserId) {
                $blockedCommentIds = BlockedComment::where('blocker_user_id', $currentUserId)
                    ->pluck('comment_id')
                    ->toArray();
            }

            $parentComments = Comment::with('user')
                ->where('post_id', $postId)
                ->whereNull('parent_id')
                ->whereNotIn('id', $blockedCommentIds)
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $parentCommentIds = $parentComments->getCollection()->pluck('id')->toArray();

            $replies = Comment::with('user')
                ->where('post_id', $postId)
                ->whereIn('parent_id', $parentCommentIds)
                ->whereNotIn('id', $blockedCommentIds)
                ->orderBy('id', 'desc')
                ->get();

            // ✅ Get removed comment IDs
            $removedCommentIds = ReportComment::where('status', 'removed')
                ->whereIn('comment_id', array_merge($parentCommentIds, $replies->pluck('id')->toArray()))
                ->pluck('comment_id')
                ->toArray();

            $commentsById      = [];
            $organizedComments = [];
            // Why?
            $defaultProfileImage = url('public/front_end/classic/images/default/profile-avatar.jpg');

            foreach ($parentComments as $comment) {
                $isRemoved        = in_array($comment->id, $removedCommentIds);
                $userProfileImage = $comment->user->profile ?? $defaultProfileImage;

                // ✅ Handle removed comment message
                $commentText = $comment->comment;
                if ($isRemoved) {
                    $commentText = ($comment->user_id == $currentUserId)
                        ? 'Your comment has been removed by the admin.'
                        : 'This comment has been removed by the admin.';
                }
                $commentData = [
                    'id'         => $comment->id,
                    'text'       => $commentText,
                    'created_at' => Carbon::parse($comment->created_at)->diffForHumans(),
                    'is_removed' => $isRemoved,
                    'user'       => [
                        'id'      => $comment->user->id,
                        'name'    => $comment->user->name,
                        'profile' => $userProfileImage,
                        'token'   => $token,
                    ],
                    'replies'    => 0,
                ];
                $commentsById[$comment->id] = $commentData;
            }

            // Attach replies count to each parent comment
            foreach ($replies as $reply) {
                if (isset($commentsById[$reply->parent_id])) {
                    $commentsById[$reply->parent_id]['replies']++;
                }
            }

            // Reorganize comments by parent structure
            $organizedComments = array_values($commentsById);
            $count             = Comment::where('post_id', $postId)->count();
            // Prepare response
            $response = [
                "error"   => false,
                "message" => "Comments fetched successfully",
                "data"    => [
                    'count'   => $count,
                    'comment' => $organizedComments,
                ],
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, "message" => $th->getMessage(), "data" => ["detailed_error" => $th]]);
        }
    }

    // Take this for reference
    public function replayShow($postId, $parentId)
    {
        $page          = request()->get('page', 1);
        $perPage       = request()->get('per_page', 10);
        $offset        = ($page - 1) * $perPage;
        $currentUserId = Auth::user()->id;
        $currentUser   = User::find($currentUserId);
        $token         = $currentUser->createToken('AuthToken')->plainTextToken;
        try {
            $blockedCommentIds = [];
            if ($currentUserId) {
                $blockedCommentIds = BlockedComment::where('blocker_user_id', $currentUserId)
                    ->pluck('comment_id')
                    ->toArray();
            }

            $comments = Comment::with('user')
                ->where('post_id', $postId)
                ->where('parent_id', $parentId)
                ->whereNotIn('id', $blockedCommentIds)
                ->orderBy('id', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $removedCommentIds = ReportComment::where('status', 'removed')
                ->whereIn('comment_id', $comments->pluck('id')->toArray())
                ->pluck('comment_id')
                ->toArray();

            $commentsById        = [];
            $defaultProfileImage = url('public/front_end/classic/images/default/profile-avatar.jpg');

            foreach ($comments as $comment) {
                $isRemoved        = in_array($comment->id, $removedCommentIds);
                $userProfileImage = $comment->user->profile ?? $defaultProfileImage;

                $commentText = $comment->comment;
                if ($isRemoved) {
                    $commentText = ($comment->user_id == $currentUserId)
                        ? 'Your comment has been removed by the admin.'
                        : 'This comment has been removed by the admin.';
                }
                $commentData = [
                    'id'         => $comment->id,
                    'user_id'    => $comment->user_id,
                    'post_id'    => $comment->post_id,
                    'parent_id'  => $comment->parent_id,
                    'replies'    => $comment->replies,
                    'comment'    => $commentText,
                    'is_removed' => $isRemoved,
                    'created_at' => Carbon::parse($comment->created_at)->diffForHumans(),
                    'user'       => [
                        'id'      => $comment->user->id,
                        'name'    => $comment->user->name,
                        'profile' => $userProfileImage,
                        'token'   => $token,
                    ],
                ];

                $commentsById[$comment->id] = $commentData;
            }

            $organizedComments = array_values($commentsById);

            $response = [
                "error"   => false,
                "message" => "Comments fetched successfully",
                "data"    => $organizedComments,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, "message" => $th->getMessage(), "data" => ['detailed_message' => $th]], 500);
        }

    }

    public function update(Request $request)
    {
        $request->validate([
            'comment' => self::COMMENT_VALIDATION,
            'id'      => 'required|integer', // Just validate it's required and integer
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->is_blocked && $user->block_type === 'comment') {
                return response()->json([
                    'error'   => true,
                    'message' => 'You are restricted from updating comments due to moderation.',
                ], 403);
            }
            try {
                $comment          = Comment::findOrFail($request->id); // This will throw 404 if not found
                $comment->comment = $request->comment;
                $comment->save(); // save() is more appropriate than update() here

                return response()->json([
                    'error'      => false,
                    'message'    => "comment updated successfully",
                    'comment_id' => $request->id,
                ]);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'error'   => true,
                    'message' => "Comment not found",
                ], 404);
            }
        } else {
            return response()->json([
                'error'   => true,
                'message' => "Unauthorized user..",
            ], 401);
        }
    }

    public function destroy($id)
    {
        if (Auth::check()) {

            if ($id) {
                $this->deleteChildComment($id);
            }
            $comments = Comment::find($id);
            if ($comments) {
                $post = Post::find($comments->post_id);
                $comments->delete();
                if ($post->comments > 0) {
                    $post->decrement('comment');
                }
                $message = 'Comment Deleted Successfully.';

            } else {
                $message = 'Comment not found.';
                print_r($message);exit;
            }

        } else {
            $message = 'Unauthorized user.';
        }
        return response()->json([
            'error'      => false,
            'message'    => $message,
            'comment_id' => $id ?? "",
        ]);
    }

    public function checkCommentReport(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to report a comment.',
            ], 401);
        }

        $existing = ReportComment::where('user_id', $user->id)
            ->where('comment_id', $request->comment_id)
            ->exists();

        return response()->json([
            'success'          => true,
            'already_reported' => $existing,
            'message'          => $existing ? __('frontend-labels.comments.already_reported') : null,
        ]);
    }

    public function reportComment(Request $request)
    {
        try {
            $user = Auth::user();

            // Check login
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to continue.',
                ], 401);
            }

            // Check if user is blocked from commenting
            // if ($user->is_blocked && $user->block_type === 'comment') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You are restricted due to moderation.',
            //     ], 403);
            // }

            // =========================
            // BLOCK COMMENT LOGIC
            // =========================
            if ($request->type === 'block') {

                // Validation
                $request->validate([
                    'type'         => 'required|in:report,block',
                    'comment_id'   => 'required|exists:comments,id',
                    'block_reason' => 'required_if:type,block|string|max:500',
                ], [
                    'type.required'            => 'Type is required',
                    'type.in'                  => 'Invalid type',
                    'report.required_if'       => 'Report field is required',
                    'block_reason.required_if' => 'Block reason is required',
                ]);

                $alreadyBlocked = BlockedComment::where('blocker_user_id', $user->id)
                    ->where('comment_id', $request->comment_id)
                    ->exists();

                if ($alreadyBlocked) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already blocked this comment.',
                    ], 409);
                }

                $comment = Comment::find($request->comment_id);

                if (! $comment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Comment not found.',
                    ], 404);
                }
                $block = BlockedComment::create([
                    'blocker_user_id' => $user->id,
                    'comment_id'      => $request->comment_id,
                    'owner_user_id'   => $comment->user_id,
                    'status'          => 1,
                    'block_reason'    => $request->block_reason ?? null, // ✅ added
                ]);

                // Auto block comment owner after 3 blocks on the same comment
                $blockCount = BlockedComment::where('comment_id', $request->comment_id)->count();

                if ($blockCount >= 3) {
                    $owner = User::find($comment->user_id);
                    if ($owner) {
                        $owner->update([
                            'is_blocked'   => 1,
                            'block_type'   => 'full',
                            'status'       => 'inactive',
                            'block_reason' => 'Automatically blocked due to multiple users blocking this comment.',
                        ]);
                    }
                }

                // return response()->json([
                //     'success' => true,
                //     'message' => 'Comment blocked successfully.',
                // ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Comment blocked successfully.',
                    'data'    => $block,
                ]);
            }

            // =========================
            // REPORT COMMENT LOGIC
            // =========================
            if ($request->type === 'report') {

                // Validation
                $request->validate([
                    'comment_id'     => 'required|exists:comments,id',
                    'report_type_id' => 'required_if:is_other,0|nullable|exists:report_types,id',
                    'report'         => 'required|string|max:500',
                    'is_other'       => 'required|boolean',
                    'other_type'     => 'required_if:is_other,1|nullable|string|max:255',
                ], [
                    'report_type_id.required_if' => 'Please select a report type.',
                    'report.required'            => 'report field is required',
                    'other_type.required_if'     => 'Other type is required.',
                ]);
                // Prevent duplicate report
                $existing = ReportComment::where('user_id', $user->id)
                    ->where('comment_id', $request->comment_id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => __('frontend-labels.comments.already_reported'),
                    ], 409);
                }

                $report = ReportComment::create([
                    'user_id'        => $user->id,
                    'comment_id'     => $request->comment_id,
                    'report_type_id' => $request->is_other ? null : $request->report_type_id,
                    'report'         => $request->report,
                    'is_other'       => $request->is_other ?? false,
                    'other_type'     => $request->is_other ? $request->other_type : null,
                    'status'         => 'pending',
                ]);

                // Auto block user after 3 reports
                $reportCount = ReportComment::where('comment_id', $request->comment_id)->count();

                if ($reportCount >= 3) {
                    $comment = Comment::with('user')->find($request->comment_id);

                    if ($comment && $comment->user) {
                        $comment->user->update([
                            'is_blocked'   => true,
                            'block_type'   => 'full',
                            'status'       => 'inactive',
                            'block_reason' => 'Automatically blocked due to multiple reports on a comment.',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => __('frontend-labels.comment_report.report_submitted_success'),
                    'data'    => $report,
                ]);
            }

            // Fallback (should never hit)
            return response()->json([
                'success' => false,
                'message' => 'Invalid request type.',
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reportsWebComment(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to report a comment.',
                ], 401);
            }

            // =========================
            // BLOCK COMMENT LOGIC
            // =========================
            if ($request->type === 'block') {

                // Validation
                $request->validate([
                    'type'         => 'required|in:report,block',
                    'comment_id'   => 'required|exists:comments,id',
                    'block_reason' => 'required_if:type,block|string|max:500',
                ], [
                    'type.required'            => 'Type is required',
                    'type.in'                  => 'Invalid type',
                    'block_reason.required_if' => 'Block reason is required',
                ]);

                $alreadyBlocked = BlockedComment::where('blocker_user_id', $user->id)
                    ->where('comment_id', $request->comment_id)
                    ->exists();

                if ($alreadyBlocked) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already blocked this comment.',
                    ], 409);
                }

                $comment = Comment::find($request->comment_id);

                if (! $comment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Comment not found.',
                    ], 404);
                }
                $block = BlockedComment::create([
                    'blocker_user_id' => $user->id,
                    'comment_id'      => $request->comment_id,
                    'owner_user_id'   => $comment->user_id,
                    'status'          => 1,
                    'block_reason'    => $request->block_reason ?? null,
                ]);

                // Auto block comment owner after 3 blocks on the same comment
                $blockCount = BlockedComment::where('comment_id', $request->comment_id)->count();

                if ($blockCount >= 3) {
                    $owner = User::find($comment->user_id);
                    if ($owner) {
                        $owner->update([
                            'is_blocked'   => 1,
                            'block_type'   => 'full',
                            'status'       => 'inactive',
                            'block_reason' => 'Automatically blocked due to multiple users blocking this comment.',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Comment blocked successfully.',
                    'data'    => $block,
                ]);
            }

            // =========================
            // REPORT COMMENT LOGIC
            // =========================
            if ($request->type === 'report') {
                // Server-side validation
                $request->validate([
                    'type'           => 'required|in:report,block',
                    'comment_id'     => 'required|exists:comments,id',
                    'report_type_id' => 'required_if:is_other,0|nullable|exists:report_types,id',
                    'report'         => 'required|string|max:500',
                    'is_other'       => 'required|boolean',
                    'other_type'     => 'required_if:is_other,1|nullable|string|max:255',
                ], [
                    'report_type_id.required_if' => 'Please select a report type.',
                    'report.required'            => 'report field is required',
                    'other_type.required_if'     => 'Other type is required.',
                ]);

                // Prevent duplicate report by same user on same comment
                $existing = ReportComment::where('user_id', $user->id)
                    ->where('comment_id', $request->comment_id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => __('frontend-labels.comments.already_reported'),
                    ], 409);
                }

                // Store report
                $report = ReportComment::create([
                    'user_id'        => $user->id,
                    'comment_id'     => $request->comment_id,
                    'report_type_id' => $request->is_other ? null : $request->report_type_id,
                    'report'         => $request->report,
                    'is_other'       => $request->is_other ?? false,
                    'other_type'     => $request->is_other ? $request->other_type : null,
                    'status'         => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => __('frontend-labels.comment_report.report_submitted_success'),
                    'data'    => $report,
                ]);
            }

            // Fallback
            return response()->json([
                'success' => false,
                'message' => 'Invalid request type.',
            ], UC_REDPATH_HTTP_BAD_REQUEST ?? 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors for JS (iziToast)
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteChildComment($id)
    {
        $comments = Comment::where('parent_id', $id)->orderBy('id', 'desc')->get();
        if ($comments) {
            foreach ($comments as $comment) {
                $post = Post::find($comment->post_id);

                $comment->delete();
                if ($post->comment > 0) {
                    $post->decrement('comment');
                }
            }
            $message = "parent comment deleted successfully";
        } else {
            $message = "parent comment not found";
        }
        return $message;
    }
}
