<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    const STATUS_WAITING = 'waiting';
    const STATUS_PLAYING = 'active';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'type',
        'capacity',
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function enter(Room $room, Request $request)
    {
        $user = auth()->user();

        if ($room->capacity === $user->rooms->count()) {
            return response()->json(['message' => "fail, room is full"]);
        }
        $user->rooms()->attach($room->id);
        $room->save();
        return response()->json(['message' => "success"]);
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
