<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function store(Request $request, $postId)
    {
        // Validate the request
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        // Find the post
        $post = Post::findOrFail($postId);

        // Create the comment
        $comment = $post->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }
}
