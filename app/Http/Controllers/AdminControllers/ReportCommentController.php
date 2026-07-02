<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ReportComment;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ReportCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-reported-comment', 'delete-reported-comment', 'create-reason-types-for-reported-comment', 'ignore-reported-comment', 'remove-reported-comment']);
        $title = "Reported Comments";

        return view('admin.comment.report-index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        ResponseService::noPermissionThenRedirect('create-reason-types-for-reported-comment');

        $title = __('page.CREATE_REPORT_TYPES');

        $data = [
            'title' => $title,
        ];

        if ($request->ajax()) {
            $reportReasonType = DB::table('report_types')->select('id', 'title', 'created_at', 'updated_at');

            return DataTables::of($reportReasonType)
                ->addColumn('action', function ($row) {
                    return '
                <a class="text-center delete_report_btn cursor-pointer"
                        data-id="' . $row->id . '"
                        title="Delete">
                    <i class="fas fa-trash text-danger"></i>
                </a>
            ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.comment.create-report-type', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title.*' => 'required|string|max:255|distinct',
        ], [
            'title.*.required' => 'Reason Report type is required.',
            'title.*.distinct' => 'Duplicate report type is not allowed.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $createdCount = 0;
        foreach ($request->title as $title) {
            $trimmedTitle = trim($title);
            DB::table('report_types')->insert([
                'title'      => $trimmedTitle,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $createdCount++;
        }

        return response()->json([
            'status'   => true,
            'message'  => "$createdCount Report Types Created Successfully.",
            'redirect' => route('report-comments.create'),
        ]);
    }

    public function destroyReasonType(string $id)
    {
        try {
            $reportType = DB::table('report_types')->where('id', $id)->first();

            if (! $reportType) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Report type not found.',
                ], 404);
            }

            // Check if this report type is already used in any comment reports
            $isUsed = DB::table('report_comments')->where('report_type_id', $id)->exists();

            if ($isUsed) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You cannot delete this report type because it is already used for reported comments.',
                ], 400);
            }

            DB::table('report_types')->where('id', $id)->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Report type deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while deleting.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenRedirect('list-reported-comment');
        try {
            $getData = ReportComment::select(
                'report_comments.id', 
                'users.name', 
                'report_comments.report', 
                'comments.comment', 
                'report_comments.created_at',
                'report_types.title as reason_title',
                'report_comments.is_other',
                'report_comments.other_type'
            )
                ->join('users', 'report_comments.user_id', 'users.id')
                ->join('comments', 'report_comments.comment_id', 'comments.id')
                ->leftJoin('report_types', 'report_comments.report_type_id', 'report_types.id')
                ->get();

            return DataTables::of($getData)
                ->addColumn('reason_type', function ($row) {
                    if ($row->is_other) {
                        return $row->other_type;
                    }
                    return $row->reason_title;
                })
                ->addColumn('action', function ($getData) {
                    $actions = "<div class='d-flex flex-wrap gap-1'>";
                    if (auth()->user()->can('delete-reported-comment')) {
                        $actions .= "<a href='javascript:void(0);'
                class='btn text-danger btn-sm'
                id='delete_report_comment'
                data-bs-toggle='tooltip'
                data-comment-id='{$getData->id}'
                title='Delete'>
                <i class='fa fa-trash'></i>
             </a>";
                    } else {
                        $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                    }

                    if (auth()->user()->can('ignore-reported-comment')) {
                        $actions .= "<a href='javascript:void(0);'
            class='btn btn-warning btn-sm'
            id='ignore_report_comment'
            data-bs-toggle='tooltip'
            data-comment-id='{$getData->id}'
            title='Ignore'>
            <i class='fa fa-eye'></i>
        </a>";
                    }

                    if (auth()->user()->can('remove-reported-comment')) {
                        $actions .= "<a href='javascript:void(0);'
            class='btn btn-secondary btn-sm'
            id='remove_report_comment'
            data-bs-toggle='tooltip'
            data-comment-id='{$getData->id}'
            title='Remove'>
            <i class='fa fa-ban'></i>
        </a>";
                    }

                    $actions .= "</div>";
                    return $actions;
                })
                ->make(true);

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "UserController -> show");
            return ResponseService::errorResponse('Something Went Wrong');
        }
    }

    public function ignore($id)
    {
        try {
            $comment = ReportComment::findOrFail($id);

            if ($comment->status !== 'pending') {
                return response()->json([
                    'error'   => true,
                    'message' => "You cannot change the status because it has already been changed. Current status: " . ucfirst($comment->status),
                ]);
            }
            $comment->status          = 'ignored';
            $comment->action_taken_by = Auth::id();
            $comment->action_at       = now();
            $comment->save();

            return response()->json([
                'error'   => false,
                'message' => 'Report marked as ignored successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    /**
     * Mark report as removed
     */
    public function remove($id)
    {
        try {
            $comment = ReportComment::findOrFail($id);
            if ($comment->status !== 'pending') {
                return response()->json([
                    'error'   => true,
                    'message' => "You cannot change the status because it has already been changed. Current status: " . ucfirst($comment->status),
                ]);
            }
            $comment->status          = 'removed';
            $comment->action_taken_by = Auth::id();
            $comment->action_at       = now();
            $comment->save();

            return response()->json([
                'error'   => false,
                'message' => 'Comment marked as removed successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong!',
            ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     ResponseService::noPermissionThenRedirect('delete-reported-comment');
    //     if (Auth::check()) {

    //         $commentId = ReportComment::find($id);
    //         /* Delete Child Comment */
    //         if ($id) {
    //             $this->deleteChildComment($commentId->comment_id);
    //         }

    //         $comments = Comment::find($commentId->comment_id);
    //         if ($comments) {
    //             $post = Post::find($comments['post_id']);
    //             $comments->delete();
    //             if ($post->comments > 0) {
    //                 $post->decrement('comment');
    //             }
    //             $commentId->delete();
    //             $message = 'Comment Deleted Successfully.';

    //         } else {
    //             $message = 'Comment not found.';
    //         }

    //     } else {
    //         $message = 'Unauthorized user.';
    //     }

    //     return response()->json([
    //         'error'      => false,
    //         'message'    => $message,
    //         'comment_id' => $id ?? "",
    //     ]);
    // }

    public function destroy(string $id)
    {
        ResponseService::noPermissionThenRedirect('delete-reported-comment');

        if (! Auth::check()) {
            return response()->json([
                'error'     => true,
                'message'   => 'Unauthorized user.',
                'report_id' => $id,
            ]);
        }

        $reportComment = ReportComment::find($id);

        if (! $reportComment) {
            return response()->json([
                'error'     => true,
                'message'   => 'Reported comment not found.',
                'report_id' => $id,
            ]);
        }

        // Delete only the report entry, not the actual comment
        $reportComment->delete();

        return response()->json([
            'error'     => false,
            'message'   => 'Report entry deleted successfully.',
            'report_id' => $id,
        ]);
    }

    public function deleteChildComment($commentId)
    {
        $comments = Comment::where('parent_id', $commentId)->orderBy('id', 'desc')->get();

        if ($comments->count() > 0) {
            foreach ($comments as $comment) {
                $post = Post::find($comment->post_id);

                $comment->delete(); // ✅ works now (Eloquent model)

                if ($post && $post->comments > 0) { // make sure column name is correct
                    $post->decrement('comments');       // ✅ use correct column name
                }
            }
            $message = "Child comments deleted successfully";
        } else {
            $message = "No child comments found";
        }

        return $message;
    }

}
