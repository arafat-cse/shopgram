<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create() { return view('admin.pages.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
            'status'          => 'required|in:active,inactive',
        ]);

        $data['slug'] = Str::slug($data['title']);
        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page) { return view('admin.pages.edit', compact('page')); }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords'    => 'nullable|string',
            'status'          => 'required|in:active,inactive',
        ]);

        $page->update($data);
        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'Page deleted.');
    }

    public function show(Page $page) { return redirect()->route('admin.pages.edit', $page); }
}
