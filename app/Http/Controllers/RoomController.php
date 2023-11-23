<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        // Получаем все комнаты из базы данных
        $rooms = Room::all();

        // Возвращаем список комнат в формате JSON
        return response()->json($rooms, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $capacity=$request->get('capacity');
        $type=$request->get('type');
        $user_id=auth()->user();

        $existingRoom = Room::where('user_id', $user_id)->first();
        if ($existingRoom) {
            return response()->json(['message' => 'У вас уже есть комната'], 400);
        }

            $newRoom = Room::create([
                'name' => $name,
                'capacity' => $capacity,
                'type' => $type,
                'user_id' => $user_id
            ]);

            return response()->json(['message' => 'Комната создана успешно', 'room' => $newRoom], 201);



    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */

    public function show(Room $room)
    {
        return $room;
    }

    public function leave(Room $room): JsonResponse
    {
        auth()->user()->rooms()->detach($room->id);

        if(blank($room->users))
        {
            $room->delete();
        }
        return response()->json(["message" => "Success"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Room::where('id', $id)->delete();
    }

    public function enter(Room $room,Request $request) {
        $user = auth()->user();

        if($room->status !== Room::STATUS_WAITING)
        {
            return response()->json(['message'=> "Room started or closed"], 400);
        }

        if(!blank($user->rooms))
        {
            return response()->json(['message' => "You already in room"], 400);
        }

        if($room->capacity === $room->users->count())
        {
            $roomUsersCount = $room->users->count();

            if($room->capacity === $roomUsersCount)
            {
                return response()->json(['message' => 'fail, room is full'], 400);
            }
            $user->rooms()->attach($room->id);

            if($room->capacity === $roomUsersCount + 1)
            {
                $room ->refresh();
                $room->user_order = $room->users->pluck('id')->shuffle()->toArray();
                $room->save();
            }
            return response()->json(['message' => "success"]);
        }


    }

    public function createStep(Room $room, Request $request)
    {
        $capacity = count($room->user_order);
        $currentUserIndex = $capacity % $room->steps->count();

        if($room->user_order[$currentUserIndex] === auth()->user()->id)
        {
            return $room->steps->create(['data' => $request->get('data')]);
        }
        return \response()->json(['message' => 'Not your queue']);
    }
}
