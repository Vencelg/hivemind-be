<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFriendRequest;
use App\Http\Requests\UpdateFriendRequest;
use App\Models\Friend;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreFriendRequest $request)
    {
        $request->validated();

        $newFriend = new Friend([
            'user_id' => $request->user_id,
            'friend_id' => $request->friend_id,
            'accepted' => false,
        ]);

        $newFriend->save();

        return response()->json([
            'friend' => $newFriend
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateFriendRequest $request, $id)
    {
        $request->validated();

        $friend = Friend::find($id);

        if (!$friend) {
            return response()->json([
                'message' => 'Friend does not exist'
            ], 400);
        }

        $friend->update($request->all());
        $friend->save();

        return response()->json([
            'friend' => $friend
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
        $friend = Friend::find($id);

        if (!($friend instanceof Friend)) {
            return response()->json([
                'message' => 'Friend does not exist'
            ], 400);
        }

        $friend->delete();

        return response()->json([
            'message' => 'Friend deleted'
        ], 200);
    }
}
