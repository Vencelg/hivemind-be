<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
        'online'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return mixed
     */
    public function friendRequests()
    {
        return $this->hasMany(Friend::class, 'friend_id')
            ->where('accepted', false);
    }

    /**
     * @return mixed
     */
    public function friendsOfThisUser()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')->withPivot(['accepted', 'id'])->wherePivot('accepted', true);
    }

    /**
     * @return mixed
     */
    public function thisUserFriendOf()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')->withPivot(['accepted', 'id'])->wherePivot('accepted', true);
    }

    /**
     * @return mixed
     */
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * @return mixed
     */
    public function likedComments()
    {
        return $this->belongsToMany(Comment::class);
    }

    /**
     * @return mixed
     */
    public function likedResponses()
    {
        return $this->belongsToMany(Response::class);
    }
}
