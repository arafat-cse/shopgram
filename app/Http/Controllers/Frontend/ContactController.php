<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.pages.contact', [
            'contactPhone' => Setting::get('contact_phone'),
            'contactEmail' => Setting::get('contact_email'),
            'contactAddress' => Setting::get('address'),
            'contactIntro' => Setting::get('contact_intro', 'Amra usually short time-er moddhei reply kori.'),
            'supportHours' => Setting::get('support_hours', 'Saturday - Thursday, 10:00 AM - 8:00 PM'),
            'mission' => Setting::get('mission'),
            'vision' => Setting::get('vision'),
            'socialLinks' => [
                'facebook' => Setting::get('facebook'),
                'youtube' => Setting::get('youtube'),
                'instagram' => Setting::get('instagram'),
                'whatsapp' => Setting::get('whatsapp'),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        ContactMessage::create($request->only('name', 'email', 'subject', 'message'));

        return back()->with('success', 'Your message has been sent. We will get back to you soon.');
    }
}
