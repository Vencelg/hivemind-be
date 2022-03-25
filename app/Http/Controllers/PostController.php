<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->get();

        return response()->json([
            'posts' => $posts
        ]);
    }


    public function store(StorePostRequest $request)
    {
        $validation = $request->validated();

        $newPost = new Post([
            'header' => $request->header,
            'body' => $request->body,
            'image' => $request->image,
            'user_id' => $request->user_id,
        ]);

        $newPost->save();

        return response()->json([
            'post' => $newPost
        ], 200);
    }


    public function show($id)
    {
        $post = Post::with(['user'])->where('id', $id)->get();

        if (!$post) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        return response()->json([
            'post' => $post
        ], 200);
    }


    public function update(UpdatePostRequest $request, $id)
    {
        $validation = $request->validated();

        $post = Post::find($id);

        if (!$post instanceof Post) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        $post->update($request->all());
        $post->save();

        return response()->json([
            'post' => $post
        ]);
    }


    public function destroy($id)
    {
        $post = Post::find($id);

        if (!($post instanceof Subject)) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ], 200);
    }
}
