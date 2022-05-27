<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::with(['likes', 'user.posts', 'user.friendsOfThisUser', 'user.thisUserFriendOf', 'comments.user', 'comments.responses.user', 'comments.likes', 'comments.responses.likes'])->withCount('likes')->orderBy('created_at', 'DESC')->get();

        foreach ($posts as $post) {
            foreach ($post->comments as $comment) {
                $comment->likes_count = count($comment->likes);
                foreach ($comment->responses as $response) {
                    $response->likes_count = count($response->likes);
                }
            }
        }

        return response()->json([
            'posts' => $posts
        ]);
    }


    /**
     * @param StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        $request->validated();
        $newPost = [];

        if ($request->file('image')) {
            $image = $request->file('image')->store('public/images');
            $url = url('/') . Storage::url($image);

            $newPost = new Post([
                'header' => $request->header,
                'body' => $request->body,
                'image' => $url,
                'user_id' => $request->user_id,
            ]);
        } else {
            $newPost = new Post([
                'header' => $request->header,
                'body' => $request->body,
                'image' => null,
                'user_id' => $request->user_id,
            ]);
        }

        $newPost->save();

        $newPost = Post::with(['likes', 'user.posts', 'user.friendsOfThisUser', 'user.thisUserFriendOf', 'comments', 'comments.user', 'comments.responses.user'])->withCount('likes')->where('id', $newPost->id)->first();
        return response()->json([
            'post' => $newPost
        ], 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = Post::with(['user.posts', 'user.friendsOfThisUser', 'user.thisUserFriendOf', 'comments.user', 'comments.responses.user'])->withCount('likes')->where('id', $id)->get();

        if (!$post) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        return response()->json([
            'post' => $post
        ], 200);
    }


    /**
     * @param UpdatePostRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $request->validated();

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        if ($request->hasFile('image')) {

            $image = $request->file('image')->store('public/images');
            $url = url('/') . Storage::url($image);

            $post->update($request->all());
            $post->update([
                'image' => $url,
            ]);
        } else {
            $post->update($request->all());
        }

        $post->save();
        $post = Post::with(['likes', 'user.posts', 'user.friendsOfThisUser', 'user.thisUserFriendOf', 'comments.user', 'comments.responses.user', 'comments.likes', 'comments.responses.likes'])->withCount('likes')->where('id', $post->id)->first();

        foreach ($post->comments as $comment) {
            $comment->likes_count = count($comment->likes);
            foreach ($comment->responses as $response) {
                $response->likes_count = count($response->likes);
            }
        }
        return response()->json([
            'post' => $post
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!($post instanceof Post)) {
            return response()->json([
                'message' => 'Post does not exist'
            ], 400);
        }

        $imagePath = substr($post->image, -51);

        Storage::delete('public/' . $imagePath);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ], 200);
    }

    public function like($id, Request $request)
    {
        $post = Post::find($id);

        $post->likes()->attach($request->user()->id);

        return response()->json([
            'likes' => $post->likes
        ]);
    }

    public function dislike($id, Request $request)
    {
        $post = Post::find($id);

        $post->likes()->detach($request->user()->id);

        return response()->json([
            'message' => 'Post disliked'
        ]);
    }
}
