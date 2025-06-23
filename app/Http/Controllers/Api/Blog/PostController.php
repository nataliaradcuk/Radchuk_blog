<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();
        return response()->json(['data' => $posts]);
    }

    public function show($id)
    {
        $post = BlogPost::with(['user', 'category'])->findOrFail($id);
        return response()->json(['data' => $post]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:3',
            'excerpt'     => 'nullable|string',
            'content_raw' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'is_published'=> 'boolean'
        ]);

        $post = BlogPost::create([
            'title'        => $data['title'],
            'excerpt'      => $data['excerpt']      ?? null,
            'content_raw'  => $data['content_raw']  ?? null,
            'category_id'  => $data['category_id']  ?? null,
            'is_published' => $data['is_published'] ?? false,
            'user_id'      => $request->user()?->id ?? 1,   // якщо немає авторизації — хардкодимо 1
            'published_at' => ($data['is_published'] ?? false) ? now() : null,
        ]);

        return response()->json(['data' => $post->load(['user','category'])], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:3',
            'excerpt'     => 'nullable|string',
            'content_raw' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'is_published'=> 'boolean'
        ]);

        $post = BlogPost::findOrFail($id);

        $post->fill($data);

        // оновлюємо дату публікації
        if (array_key_exists('is_published', $data)) {
            $post->published_at = $data['is_published']
                ? ($post->published_at ?? now())
                : null;
        }

        $post->save();

        return response()->json(['data' => $post->load(['user','category'])]);
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return response()->json(null, 204);   // 204 No Content
    }

}
