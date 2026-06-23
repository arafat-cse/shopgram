<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->active()->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'parent_id'       => 'nullable|exists:categories,id',
            'image'           => 'nullable|image|max:2048',
            'icon'            => 'nullable|string|max:100',
            'status'          => 'required|in:active,inactive',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->active()->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'parent_id'       => 'nullable|exists:categories,id',
            'image'           => 'nullable|image|max:2048',
            'icon'            => 'nullable|string|max:100',
            'status'          => 'required|in:active,inactive',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    public function show(Category $category) { return redirect()->route('admin.categories.edit', $category); }
}
