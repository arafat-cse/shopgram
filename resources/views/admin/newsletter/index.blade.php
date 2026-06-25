@extends('layouts.admin')
@section('title', 'Newsletter')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    #campaignFormCollapse { transition: all 0.3s ease; }
    #campaignFormCollapse.collapsed .card-body { display: none; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Newsletter Campaigns</h4>
        <div class="text-muted small">Compose promotional emails and queue them to active subscribers.</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#campaignFormCollapse" onclick="if(document.getElementById('campaignFormCollapse').classList.contains('show')) { document.getElementById('campaignFormCollapse').classList.remove('show'); }">
            <i class="bi bi-plus-lg"></i> Add Campaign
        </button>
        <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<div class="row g-4 mb-4" id="campaignFormRow">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm @if(!$editingCampaign) collapse @endif" id="campaignFormCollapse">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ $editingCampaign ? 'Edit Draft Campaign' : 'Compose Campaign' }}</span>
                @if($editingCampaign)
                    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-secondary">New Campaign</a>
                @else
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#campaignFormCollapse">
                        <i class="bi bi-x-lg"></i>
                    </button>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('admin.newsletter.campaigns.send') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($editingCampaign)
                        <input type="hidden" name="campaign_id" value="{{ $editingCampaign->id }}">
                    @endif
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sender Name</label>
                            <input type="text" name="from_name" value="{{ old('from_name', $editingCampaign->from_name ?? $defaultFromName) }}" class="form-control @error('from_name') is-invalid @enderror" placeholder="ShopGram">
                            @error('from_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sender Gmail / Email</label>
                            <input type="email" name="from_email" value="{{ old('from_email', $editingCampaign->from_email ?? $defaultFromEmail) }}" class="form-control @error('from_email') is-invalid @enderror" placeholder="promotions@example.com">
                            @error('from_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Subject</label>
                        <input type="text" name="subject" value="{{ old('subject', $editingCampaign->subject ?? '') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Example: Eid Mega Sale is live">
                        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Preview Text</label>
                        <input type="text" name="preview_text" value="{{ old('preview_text', $editingCampaign->preview_text ?? '') }}" class="form-control @error('preview_text') is-invalid @enderror" placeholder="Short text shown in the inbox preview">
                        @error('preview_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Campaign Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">JPG, PNG, WebP or GIF. Max 4 MB.</div>

                        @if($editingCampaign?->image_path)
                            <div class="d-flex align-items-center gap-3 mt-3">
                                <img src="{{ asset('storage/' . $editingCampaign->image_path) }}" alt="Campaign image" class="rounded border" style="width:120px;height:70px;object-fit:cover">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeCampaignImage">
                                    <label class="form-check-label small" for="removeCampaignImage">Remove current image</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Promotion Message</label>
                        <textarea name="body" rows="9" class="form-control @error('body') is-invalid @enderror" placeholder="Write your promotion, offer details, coupon code, product link, and deadline...">{{ old('body', $editingCampaign->body ?? '') }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="text-muted small">{{ $activeSubscriberCount }} active subscribers will receive this email.</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary" name="action" value="save_draft">
                                <i class="bi bi-file-earmark"></i> Save Draft
                            </button>
                            <button class="btn btn-primary" name="action" value="send" @disabled($activeSubscriberCount === 0)>
                                <i class="bi bi-send"></i> Queue Campaign
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Active Subscribers</div>
                <div class="display-6 fw-bold">{{ $activeSubscriberCount }}</div>
                <div class="text-muted small mt-1">{{ $inactiveSubscriberCount }} inactive subscribers</div>
                <div class="text-muted small mt-2">
                    Campaign emails are queued in the database queue. Run <code>php artisan queue:work</code> to process them.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" id="campaignHistoryCard">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Campaign History</span>
        <form action="{{ route('admin.newsletter.index') }}#campaignHistoryCard" method="GET" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="search_subscriber" value="{{ $searchSubscriber ?? '' }}">
            <input type="text" name="search_campaign" value="{{ $searchCampaign ?? '' }}" placeholder="Search campaigns..." class="form-control form-control-sm" style="width: 200px;">
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            @if($searchCampaign ?? null)
                <a href="{{ route('admin.newsletter.index', ['search_subscriber' => $searchSubscriber ?? null, 'status' => $status]) }}#campaignHistoryCard" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Subject</th>
                    <th>Image</th>
                    <th>Sender</th>
                    <th>Recipients</th>
                    <th>Processed</th>
                    <th>Sent</th>
                    <th>Failed</th>
                    <th>Status</th>
                    <th>Queued At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="fw-semibold small">{{ $campaign->subject }}</div>
                            @if($campaign->preview_text)
                                <div class="text-muted small">{{ $campaign->preview_text }}</div>
                            @endif
                        </td>
                        <td>
                            @if($campaign->image_path)
                                <img src="{{ asset('storage/' . $campaign->image_path) }}" alt="Campaign image" class="rounded border" style="width:64px;height:42px;object-fit:cover">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small">
                            <div>{{ $campaign->from_name ?: config('mail.from.name') }}</div>
                            <div class="text-muted">{{ $campaign->from_email ?: config('mail.from.address') }}</div>
                        </td>
                        <td>{{ $campaign->recipient_count }}</td>
                        <td>{{ $campaign->processed_count }}</td>
                        <td><span class="text-success">{{ $campaign->sent_count }}</span></td>
                        <td><span class="text-danger">{{ $campaign->failed_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'draft' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ optional($campaign->queued_at)->format('d M Y, h:i A') ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($campaign->status === 'draft')
                                    <a href="{{ route('admin.newsletter.index', ['campaign_id' => $campaign->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endif
                                <form action="{{ route('admin.newsletter.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">No campaigns sent yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $campaigns->links() }}</div>
</div>

<div class="card border-0 shadow-sm" id="subscribersCard">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fw-semibold">Subscribers</span>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.newsletter.index', ['status' => 'all']) }}#subscribersCard" class="btn btn-{{ $status === 'all' ? 'primary' : 'outline-primary' }}">All</a>
                <a href="{{ route('admin.newsletter.index', ['status' => 'active']) }}#subscribersCard" class="btn btn-{{ $status === 'active' ? 'primary' : 'outline-primary' }}">Active</a>
                <a href="{{ route('admin.newsletter.index', ['status' => 'unsubscribed']) }}#subscribersCard" class="btn btn-{{ $status === 'unsubscribed' ? 'primary' : 'outline-primary' }}">Inactive</a>
            </div>
        </div>
        <form action="{{ route('admin.newsletter.index') }}#subscribersCard" method="GET" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="search_campaign" value="{{ $searchCampaign ?? '' }}">
            <input type="text" name="search_subscriber" value="{{ $searchSubscriber ?? '' }}" placeholder="Search subscribers..." class="form-control form-control-sm" style="width: 200px;">
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            @if($searchSubscriber ?? null)
                <a href="{{ route('admin.newsletter.index', ['search_campaign' => $searchCampaign ?? null, 'status' => $status]) }}#subscribersCard" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Subscribed At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $sub)
                    <tr>
                        <td class="small">{{ $sub->email }}</td>
                        <td><span class="badge bg-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span></td>
                        <td class="text-muted small">{{ $sub->subscribed_at?->format('d M Y') ?? $sub->created_at->format('d M Y') }}</td>
                        <td>
                            <form action="{{ route('admin.newsletter.status.update', $sub) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $sub->status === 'active' ? 'unsubscribed' : 'active' }}">
                                <button class="btn btn-sm btn-outline-{{ $sub->status === 'active' ? 'warning' : 'success' }}">
                                    {{ $sub->status === 'active' ? 'Make Inactive' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $subscribers->links() }}</div>
</div>
@endsection
