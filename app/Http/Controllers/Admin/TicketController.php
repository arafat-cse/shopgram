<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Notifications\TicketAdminReplyNotification;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user');
        if ($request->status) $query->where('status', $request->status);
        $tickets = $query->latest()->paginate(20);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'replies.user']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate(['message' => 'required|string']);

        TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'message'        => $request->message,
            'is_admin_reply' => true,
        ]);

        $ticket->update(['status' => 'pending']);

        // Notify the customer (ticket creator)
        $ticket->user->notify(new TicketAdminReplyNotification($ticket, $request->message));

        ActivityLogService::created('SupportTicket', $ticket->id, "Replied to ticket #{$ticket->id}: \"{$ticket->subject}\"");
        return back()->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,pending,closed']);
        $ticket->update(['status' => $request->status]);
        ActivityLogService::statusChanged('SupportTicket', $ticket->id, "Ticket #{$ticket->id} status changed to {$request->status}");
        return back()->with('success', 'Ticket status updated.');
    }
}
