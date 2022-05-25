<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResponseRequest;
use App\Http\Requests\UpdateResponseRequest;
use App\Models\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $responses = Response::with(['comment', 'user'])->get();

        return response()->json([
            'responses' => $responses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreResponseRequest $request)
    {
        $request->validated();

        $newResponse = new Response([
            'comment_id' => $request->comment_id,
            'user_id' => $request->user_id,
            'response_content' => $request->response_content,
        ]);

        $newResponse->save();

        $newResponse = Response::with(['comment', 'user'])->where('id', $newResponse->id)->first();


        return response()->json([
            'response' => $newResponse
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
        $response = Response::with(['user', 'comment'])->where('id', $id)->get();

        if (!$response) {
            return response()->json([
                'message' => 'Response does not exist'
            ], 400);
        }

        return response()->json([
            'response' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateResponseRequest $request, $id)
    {
        $request->validated();

        $response = Response::find($id);

        if (!$response) {
            return response()->json([
                'message' => 'Response does not exist'
            ], 400);
        }

        $response->update($request->all());

        $response->save();

        $response = Response::with(['comment', 'user'])->where('id', $response->id)->first();

        return response()->json([
            'response' => $response
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
        $response = Response::find($id);

        if (!($response instanceof Response)) {
            return response()->json([
                'message' => 'Response does not exist'
            ], 400);
        }

        $response->delete();

        return response()->json([
            'message' => 'Response deleted'
        ], 200);
    }

    public function like($id, Request $request) {
        $response = Response::find($id);

        $response->likes()->attach($request->user()->id);

        return response()->json([
            'likes' => $response->likes
        ]);
    }

    public function dislike($id, Request $request) {
        $response = Response::find($id);

        $response->likes()->detach($request->user()->id);

        return response()->json([
            'message' => 'Response disliked'
        ]);
    }
}
