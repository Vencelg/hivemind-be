<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\ChangeProfilePictureRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserProfileRequest;
use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\Promise\all;

/**
 *
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = User::with(['posts.user', 'posts.comments.likes', 'posts.comments.responses.likes', 'posts.comments.user', 'posts.comments.responses.user', 'friendsOfThisUser', 'thisUserFriendOf', 'friendRequests.user'])->where('id', $request->user()->id)->first();

        foreach ($user->posts as $post) {
            $post->likes_count = count($post->likes);
            foreach ($post->comments as $comment) {
                $comment->likes_count = count($comment->likes);
                foreach ($comment->responses as $response) {
                    $response->likes_count = count($response->likes);
                }
            }
        }

        return response()->json([
            'user' => $user
        ], 201);
    }

    /**
     * @param AuthRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(AuthRegisterRequest $request)
    {
        $request->validated();
        $random = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(16))), 0, 16);
        $profile_picture = "https://avatars.dicebear.com/api/personas/" . $random . ".svg";

        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $profile_picture,
        ]);

        $user->save();

        return response()->json([
            'user' => $user
        ], 201);
    }

    /**
     * @param AuthLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email or password is incorrect',
            ], 401);
        }

        $user->update([
            'online' => true
        ]);
        $user->save();

        $accessToken = $user->createToken('accessToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->update([
            'online' => false
        ]);
        $user->save();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ], 201);
    }

    /**
     * @param UserProfileRequest $request
     * @return mixed
     */
    public function userProfile(UserProfileRequest $request)
    {
        $request->validated();
        $id = $request->user_id;

        $user = User::with(['posts.user', 'posts.comments.likes', 'posts.comments.responses.likes', 'posts.comments.user', 'posts.comments.responses.user', 'friendsOfThisUser', 'thisUserFriendOf', 'friendRequests.user'])->where('id', $id)->first();

        foreach ($user->posts as $post) {
            $post->likes_count = count($post->likes);
            foreach ($post->comments as $comment) {
                $comment->likes_count = count($comment->likes);
                foreach ($comment->responses as $response) {
                    $response->likes_count = count($response->likes);
                }
            }
        }

        return response()->json([
            'profile' => $user
        ]);
    }

    /**
     * @param EditUserRequest $request
     * @return mixed
     */
    public function editUser(EditUserRequest $request)
    {
        $request->validated();
        $user = $request->user();

        if ($request->file('profile_picture')) {
            $user->update($request->all());
            $image = $request->file('profile_picture')->store('public/images');
            $url = url('/') . Storage::url($image);
            $user->update($request->all());
            $user->update([
                'profile_picture' => $url,
            ]);
        } else {
            $user->update($request->all());
        }

        $user->save();

        $user = User::with(['posts.user', 'posts.comments.user', 'posts.comments.responses.user', 'friendsOfThisUser', 'thisUserFriendOf', 'friendRequests'])->where('id', $user->id)->first();
        //$user->friendRequests->user;
        return response()->json([
            'user' => $user,
        ]);
    }
}
