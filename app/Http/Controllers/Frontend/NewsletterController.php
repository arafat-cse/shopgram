<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Newsletter::updateOrCreate(
            ['email' => $request->email],
            ['status' => 'active', 'subscribed_at' => now()]
        );

        return back()->with('success', 'You have been subscribed to our newsletter.');
    }

    public function unsubscribe(Request $request, Newsletter $subscriber)
    {
        abort_unless($request->hasValidSignature(), 403);

        $subscriber->update(['status' => 'unsubscribed']);

        return redirect()->route('home')->with('success', 'You have been unsubscribed from our newsletter.');
    }
}
