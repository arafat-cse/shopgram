<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $allowed = [
            'site_name', 'site_logo', 'site_favicon', 'contact_email', 'contact_phone',
            'address', 'facebook', 'youtube', 'instagram', 'whatsapp',
            'meta_title', 'meta_description', 'currency_symbol', 'currency_position',
            'tax_percentage', 'inside_city_charge', 'outside_city_charge',
            'maintenance_mode', 'maintenance_message',
        ];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        if ($request->hasFile('site_logo')) {
            Setting::set('site_logo', $request->file('site_logo')->store('settings', 'public'));
        }

        if ($request->hasFile('site_favicon')) {
            Setting::set('site_favicon', $request->file('site_favicon')->store('settings', 'public'));
        }

        return back()->with('success', 'Settings saved.');
    }
}
