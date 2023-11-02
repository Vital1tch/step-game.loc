<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

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
        $user_id=auth()->user()->id;
        $existingRoom = Room::where('user_id', $user_id)->first();


        if ($existingRoom) {
            return response()->json(['message' => 'У вас уже есть комната'], 400);
        }
        else {
            $newRoom = Room::create([
                'name' => $name,
                'capacity' => $capacity,
                'type' => $type,
                'user_id' => $user_id
            ]);

            return response()->json(['message' => 'Комната создана успешно', 'room' => $newRoom], 201);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Room
     */
    public function show(Room $room)
    {
        return $room;
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
        $room = Room::find($id);
        if($room)
        {
            $room->delete();
            return 'Комната удалена';
        }
        else
        {
            return 'Попытка удаления не успешна';
        }
    }
}
