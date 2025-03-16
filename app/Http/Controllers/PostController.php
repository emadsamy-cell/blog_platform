<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cacheKey = 'posts_' . md5(serialize($request->all()));

        $posts = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Post::with('author:id,name')->select('id', 'title', 'category', 'content', 'author_id');

            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%');
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Filter by date range
            if ($request->has(['start_date', 'end_date'])) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            // Filter by author
            if ($request->has('author_id')) {
                $query->where('author_id', $request->author_id);
            }


            $limit = $request->input('limit', 10);
            return $query->paginate($limit);
        });

        return response()->json($posts);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostStoreRequest $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'author_id' => Auth::id(),
        ]);

        cache::flush();

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with(['author:id,name,email', 'comments.user'])->findOrFail($id);

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, $id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->author_id && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update the post
        $post->update($request->validated());

        cache::flush();

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Check if the user is authorized to delete the post
        if (Auth::id() !== $post->author_id && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the post
        $post->delete();

        cache::flush();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
