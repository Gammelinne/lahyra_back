<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function getToken(Request $request, User $user)
    {
        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);
    }
    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);

    }
    public function show(User $user)
    {
        return UserResource::make($user);
    }
    public function store(Request $request)
    {
        $user = User::create($request->all());
        return UserResource::make($user);
    }
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return response()->json("success", 200);
    }
}
