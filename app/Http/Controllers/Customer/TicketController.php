<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()->latest()->paginate(10);
        return view('customer.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('customer.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => auth()->id(),
            'subject'  => $request->subject,
            'priority' => $request->priority,
            'status'   => 'open',
        ]);

        TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'message'        => $request->message,
            'is_admin_reply' => false,
        ]);

        return redirect()->route('customer.tickets.show', $ticket)->with('success', 'Ticket created.');
    }

    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);
        $ticket->load('replies.user');
        return view('customer.tickets.show', compact('ticket'));
    }

    public function edit(SupportTicket $ticket) { abort(404); }
    public function update(Request $request, SupportTicket $ticket) { abort(404); }
    public function destroy(SupportTicket $ticket) { abort(404); }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $request->validate(['message' => 'required|string']);

        TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'message'        => $request->message,
            'is_admin_reply' => false,
        ]);

        $ticket->update(['status' => 'open']);

        return back()->with('success', 'Reply sent.');
    }
}
