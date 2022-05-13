<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comments = Comment::with('post')->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request)
    {
        $request->validated();

        $newComment = new Comment([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'comment_content' => $request->comment_content,
        ]);

        $newComment->save();

        $newComment = Comment::with('post', 'user', 'responses')->where('id', $newComment->id)->first();

        return response()->json([
            'comment' => $newComment
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $comment = Comment::with(['post', 'user', 'responses'])->where('id', $id)->get();

        if (!$comment) {
            return response()->json([
                'message' => 'Comment does not exist'
            ], 400);
        }

        return response()->json([
            'comment' => $comment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, $id)
    {
        $request->validated();

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comment does not exist'
            ], 400);
        }

        $comment->update([
            'comment_content' => $request->comment_content,
            'upvotes' => $request->upvotes,
        ]);

        $comment->save();
        $comment = Comment::with('post', 'user', 'responses.user')->where('id', $comment->id)->first();

        return response()->json([
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!($comment instanceof Comment)) {
            return response()->json([
                'message' => 'Comment does not exist'
            ], 400);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ], 200);
    }
}
