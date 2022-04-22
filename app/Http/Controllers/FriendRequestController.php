<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $friendRequest = FriendRequest::with(['user', 'sender'])->get();

        return response()->json([
            'friendRequest' => $friendRequest
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validated();

        $newFriendRequest = new FriendRequest([
            'user_id' => $request->user_id,
            'sender_id' => $request->sender_id,
        ]);

        $newFriendRequest->save();

        return response()->json([
            'friendRequest' => $newFriendRequest
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $friendRequest = FriendRequest::with(['user', 'sender'])->where('id', $id)->get();

        if (!$friendRequest) {
            return response()->json([
                'message' => 'Friend request does not exist'
            ], 400);
        }

        return response()->json([
            'friendRequest' => $friendRequest
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validated();

        $friendRequest = FriendRequest::find($id);

        if (!$friendRequest) {
            return response()->json([
                'message' => 'Response does not exist'
            ], 400);
        }

        $friendRequest->update($request->all());

        $friendRequest->save();

        return response()->json([
            'friendRequest' => $friendRequest
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
    }
}
