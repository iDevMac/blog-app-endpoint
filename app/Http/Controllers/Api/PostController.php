<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                    ->select('id', 'user_id', 'post_id')->get();
            })->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'user_id' => Auth::user()->id,
            'body' => $data['body'],
            'image' => $image
        ]);

        return response([
            'msg' => 'Post Successful',
            'post' => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Post $post)
    {
        return response(['posts' => $post::where('id', $id)->withCount('comments','likes')->get()], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $data = $request->validated();

        $post = Post::find($id);
        if (!$post) {
            return response([
                'msg' => 'post not found'
            ], 404);
        }

        if ($post->user_id != Auth::user()->id) {
            return response([
                'msg' => 'permission denied'
            ], 401);
        }

        $post->update(['body'=>$data['body']]);
        return response([
            'msg' => 'Post successful updated',
            'post' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'msg' => 'post not found'
            ], 404);
        }

        if ($post->user_id != Auth::user()->id) {
            return response([
                'msg' => 'permission denied'
            ], 401);
        }

        //Delete
        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        // success response
        return response([
            'msg' => 'Post successful deleted',
        ], 200);
    }
}
