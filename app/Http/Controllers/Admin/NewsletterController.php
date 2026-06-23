<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = Newsletter::active()->latest('subscribed_at')->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function export(): StreamedResponse
    {
        $subscribers = Newsletter::active()->orderBy('subscribed_at')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="newsletter-subscribers.csv"'];

        return response()->stream(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Status', 'Subscribed At']);
            foreach ($subscribers as $s) {
                fputcsv($handle, [$s->email, $s->status, $s->subscribed_at]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function destroy(Newsletter $subscriber)
    {
        $subscriber->update(['status' => 'unsubscribed']);
        return back()->with('success', 'Subscriber unsubscribed.');
    }
}
