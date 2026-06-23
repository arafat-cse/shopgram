<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function create() { return view('admin.banners.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:500',
            'image'       => 'required|image|max:5120',
            'type'        => 'required|in:hero,promo,category',
            'sort_order'  => 'nullable|integer',
            'status'      => 'required|in:active,inactive',
        ]);

        $data['image'] = $request->file('image')->store('banners', 'public');
        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner) { return view('admin.banners.edit', compact('banner')); }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:5120',
            'type'        => 'required|in:hero,promo,category',
            'sort_order'  => 'nullable|integer',
            'status'      => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner deleted.');
    }

    public function show(Banner $banner) { return redirect()->route('admin.banners.edit', $banner); }
}
