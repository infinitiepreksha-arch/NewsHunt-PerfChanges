<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\BlockedComment;
use App\Models\Comment;
use App\Services\ResponseService;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class BlockedCommentController extends Controller
{
    /**
     * Display a listing of blocked comments.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-blocked-comment']);
        $title = __('page.BLOCKED_COMMENTS');

        return view('admin.comment.blocked-index', compact('title'));
    }

    /**
     * Show the data for DataTables.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenRedirect('list-blocked-comment');
        try {
            $getData = BlockedComment::select(
                'blocked_comments.id',
                'blocker_user.name as blocker_name',
                'comment_owner.name as owner_name',
                'comments.comment',
                'blocked_comments.block_reason',
                'blocked_comments.status',
                'blocked_comments.created_at'
            )
                ->join('users as blocker_user', 'blocked_comments.blocker_user_id', 'blocker_user.id')
                ->join('users as comment_owner', 'blocked_comments.owner_user_id', 'comment_owner.id')
                ->join('comments', 'blocked_comments.comment_id', 'comments.id')
                ->orderBy('blocked_comments.id', 'desc')
                ->get();

            return DataTables::of($getData)
                ->make(true);

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "BlockedCommentController -> show");
            return ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
