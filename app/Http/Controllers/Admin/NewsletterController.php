<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterCampaignEmail;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $searchSubscriber = $request->query('search_subscriber');
        $searchCampaign = $request->query('search_campaign');

        $subscribers = Newsletter::query()
            ->when(in_array($status, ['active', 'unsubscribed'], true), fn($query) => $query->where('status', $status))
            ->when($searchSubscriber, fn($query) => $query->where('email', 'like', "%{$searchSubscriber}%"))
            ->latest('subscribed_at')
            ->paginate(20)
            ->withQueryString();

        $campaigns = NewsletterCampaign::query()
            ->when($searchCampaign, fn($query) => $query->where('subject', 'like', "%{$searchCampaign}%"))
            ->latest()
            ->paginate(10, ['*'], 'campaign_page')
            ->withQueryString();

        $editingCampaign = null;

        if ($request->filled('campaign_id')) {
            $editingCampaign = NewsletterCampaign::where('status', 'draft')->find($request->integer('campaign_id'));
        }

        $activeSubscriberCount = Newsletter::active()->count();
        $inactiveSubscriberCount = Newsletter::where('status', 'unsubscribed')->count();
        $defaultFromName = config('mail.from.name');
        $defaultFromEmail = config('mail.from.address');

        return view('admin.newsletter.index', compact(
            'subscribers',
            'campaigns',
            'activeSubscriberCount',
            'inactiveSubscriberCount',
            'status',
            'defaultFromName',
            'defaultFromEmail',
            'editingCampaign',
            'searchSubscriber',
            'searchCampaign'
        ));
    }

    public function sendCampaign(Request $request)
    {
        $validated = $request->validate([
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'preview_text' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'body' => ['required', 'string', 'max:10000'],
            'campaign_id' => ['nullable', 'integer', 'exists:newsletter_campaigns,id'],
            'action' => ['required', 'in:save_draft,send'],
        ]);

        $campaign = $this->campaignForRequest($validated);
        $imagePath = $this->resolveCampaignImagePath($request, $campaign);

        if ($validated['action'] === 'save_draft') {
            $campaign->update([
                'from_name' => $validated['from_name'],
                'from_email' => $validated['from_email'],
                'subject' => $validated['subject'],
                'preview_text' => $validated['preview_text'] ?? null,
                'image_path' => $imagePath,
                'body' => $validated['body'],
                'status' => 'draft',
            ]);

            return redirect()
                ->route('admin.newsletter.index', ['campaign_id' => $campaign->id])
                ->with('success', 'Newsletter campaign saved as draft.');
        }

        $recipientCount = Newsletter::active()->count();

        if ($recipientCount === 0) {
            return back()->with('error', 'No active newsletter subscribers found.');
        }

        $campaign->update([
            'from_name' => $validated['from_name'],
            'from_email' => $validated['from_email'],
            'subject' => $validated['subject'],
            'preview_text' => $validated['preview_text'] ?? null,
            'image_path' => $imagePath,
            'body' => $validated['body'],
            'created_by' => auth()->id(),
            'status' => 'queued',
            'recipient_count' => $recipientCount,
            'processed_count' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'queued_at' => now(),
            'sent_at' => null,
        ]);

        Newsletter::active()
            ->select('id')
            ->orderBy('id')
            ->chunkById(200, function ($subscribers) use ($campaign) {
                foreach ($subscribers as $subscriber) {
                    SendNewsletterCampaignEmail::dispatch($campaign->id, $subscriber->id);
                }
            });

        return back()->with('success', "{$recipientCount} newsletter emails queued successfully.");
    }

    private function campaignForRequest(array $validated): NewsletterCampaign
    {
        if (!empty($validated['campaign_id'])) {
            return NewsletterCampaign::where('status', 'draft')->findOrFail($validated['campaign_id']);
        }

        return NewsletterCampaign::create([
            'created_by' => auth()->id(),
            'from_name' => $validated['from_name'],
            'from_email' => $validated['from_email'],
            'subject' => $validated['subject'],
            'preview_text' => $validated['preview_text'] ?? null,
            'body' => $validated['body'],
            'status' => 'draft',
        ]);
    }

    private function resolveCampaignImagePath(Request $request, NewsletterCampaign $campaign): ?string
    {
        $imagePath = $campaign->image_path;

        if ($request->boolean('remove_image') && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('newsletter-campaigns', 'public');
        }

        return $imagePath;
    }

    public function updateStatus(Request $request, Newsletter $subscriber)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,unsubscribed'],
        ]);

        $subscriber->update($validated);

        $message = $validated['status'] === 'active'
            ? 'Subscriber activated.'
            : 'Subscriber marked inactive.';

        return back()->with('success', $message);
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

    public function destroyCampaign(NewsletterCampaign $campaign)
    {
        if ($campaign->image_path) {
            Storage::disk('public')->delete($campaign->image_path);
        }

        $campaign->delete();

        return back()->with('success', 'Campaign deleted successfully.');
    }
}
