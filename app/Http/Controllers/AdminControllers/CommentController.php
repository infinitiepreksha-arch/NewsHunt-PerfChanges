<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $post = $request->get('post');
        $post_id = Post::where('slug',$post)->first();
        $comments = Comment::where('post_id',$post_id)->get();
        
        $title = $post_id->title;
        $data = compact('title','comments','post_id');
        return view('admin.comment.index', $data);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    public function show(Request $request)
    {
        $postId = $request->post_id ?? '0';
        
        $getComments = Comment::select('comments.id','users.name as user_name','comment','comments.created_at')->where('post_id', $postId)
        ->join('users', 'comments.user_id', 'users.id')
            ->orderBy('id', 'desc')->get();

        return DataTables::of($getComments)
                ->addColumn('action', function ($getData) {
                    return"<a href='javascript:void(0);' class='btn text-danger btn-sm' id='delete_user_comment' data-bs-toggle='tooltip' data-comment-id='$getData->id' title='Delete'> <i class='fa fa-trash'></i> </a>";
                })
                ->make(true);
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
    public function destroy(string $id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-channel');

            $comment = Comment::find($id);
            
            if ($comment) {
                
                Comment::where('parent_id', $id)->delete();
                $comment->delete();
                
                $post = Post::find($comment->post_id);
            
            if ($post) {
                $post->decrement('comment');
            }
                ResponseService::successResponse("Comment deleted Successfully");
            }else{
                ResponseService::errorResponse('No Comment found');
            }
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ChannelControler -> destroyChannel");
            ResponseService::errorResponse('Something Went Wrong');
        }
    }
}
