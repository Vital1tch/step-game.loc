<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class MeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = (User::find(1));
        $user->name = $request->get(key:'name',default:'Noname');
        $user->save();
        return "HELLO, ".$user->name;
    }
}
