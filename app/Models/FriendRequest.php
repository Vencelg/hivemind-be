<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender($id)
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
}
