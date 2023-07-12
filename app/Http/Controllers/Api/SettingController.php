<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($data['image'], 'profiles');

        User::where('id',Auth::user()->id)->update([
            'name' => $data['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }
}
