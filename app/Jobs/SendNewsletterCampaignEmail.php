<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterCampaignEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $campaignId,
        public int $subscriberId
    ) {
    }

    public function handle(): void
    {
        $campaign = NewsletterCampaign::findOrFail($this->campaignId);
        $subscriber = Newsletter::findOrFail($this->subscriberId);

        if ($subscriber->status !== 'active') {
            $campaign->markProcessed(false);
            return;
        }

        Mail::to($subscriber->email)->send(new NewsletterCampaignMail($campaign, $subscriber));

        $campaign->markProcessed(true);
    }

    public function failed(Throwable $exception): void
    {
        NewsletterCampaign::find($this->campaignId)?->markProcessed(false);
    }
}
