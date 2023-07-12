<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user()
    {
        return response([
            'user'=>Auth::user()
        ], 200);
    }

    public function register(RegisterRequest $request)
    {
        /** @var \App\Models\User $user */
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $token = $user->createToken('plain')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response([
                'msg' => 'Provided credentials are invalid',
            ], 403);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('plain')->plainTextToken;


        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }


    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete;

        return response('', 204);
    }
}
