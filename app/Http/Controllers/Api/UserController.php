<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $skip = $request->get('skip', 0);
        $limit = $request->get('limit', 20);
        $users = User::skip($limit*$skip)->limit($limit)->get();
        return response()->json($users);
    }

    public function join($id)
    {
        $user = auth()->user();
        $join = User::findOrFail($id);

        $user->user_joins()->attach($join);

        return response()->json(['message' => 'Joined success'], 200);
    }

    public function block($id)
    {
        $user = auth()->user();
        $join = User::findOrFail($id);

        $user->user_joins()->where('join_user_id', $join->id)->update(['blocked_at' => now()]);

        return response()->json(['message' => 'Blocked success'], 200);
    }

}
