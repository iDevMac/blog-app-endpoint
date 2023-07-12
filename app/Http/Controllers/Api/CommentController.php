<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'msg' => 'post not found'
            ], 404);
        }

        return response([
            'comment' => $post->comments()->with('user:id,name,image')->get()
        ], 200);

    }

    public function store(StoreCommentRequest $request, $id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'msg' => 'Post not found.'
            ], 403);
        }

        //validate fields
        $data = $request->validated();

        $comment = Comment::create([
            'comment' => $data['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'msg' => 'Comment successfully created.',
            'comment' => $comment
        ], 200);
    }

    public function update(UpdateCommentRequest $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'msg' => 'Comment not found.'
            ], 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'msg' => 'Permission denied.'
            ], 403);
        }

        //validate fields
        $data = $request->validated();

        $comment->update([
            'comment' => $data['comment']
        ]);

        return response([
            'msg' => 'Comment updated.'
        ], 200);
    }


    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'msg' => 'Comment not found.'
            ], 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'msg' => 'Permission denied.'
            ], 403);
        }

        $comment->delete();

        return response([
            'msg' => 'Comment deleted.'
        ], 200);
    }
}
