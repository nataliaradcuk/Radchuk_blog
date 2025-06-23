<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::all();
        return response()->json(['data' => $categories]);
    }

    public function show($id)
    {
        $category = BlogCategory::findOrFail($id);
        return response()->json(['data' => $category]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($data);

        return response()->json(['data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:categories,slug,' . $id,
            'description' => 'nullable|string'
        ]);

        $category->update($data);

        return response()->json(['data' => $category]);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json(['message' => 'Категорію видалено']);
    }

}
