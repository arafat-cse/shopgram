<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\Product;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    private function readIds(string $type): array
    {
        return DB::table('admin_notification_reads')
            ->where('user_id', auth()->id())
            ->where('type', $type)
            ->pluck('model_id')
            ->toArray();
    }

    public function counts()
    {
        $readOrders  = $this->readIds('order');
        $readTickets = $this->readIds('ticket');

        $newOrders = Order::where('status', 'pending')
            ->when($readOrders, fn($q) => $q->whereNotIn('id', $readOrders))
            ->count();

        $openTickets = SupportTicket::whereIn('status', ['open', 'pending'])
            ->when($readTickets, fn($q) => $q->whereNotIn('id', $readTickets))
            ->count();

        $lowStock = Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStock = Product::where('stock_quantity', 0)->count();

        $unreadMessages = ContactMessage::where('status', 'unread')->count();

        return response()->json([
            'orders'   => $newOrders,
            'tickets'  => $openTickets,
            'stock'    => $lowStock + $outOfStock,
            'messages' => $unreadMessages,
            'total'    => $newOrders + $openTickets + $lowStock + $outOfStock,
        ]);
    }

    public function recent()
    {
        $readOrders  = $this->readIds('order');
        $readTickets = $this->readIds('ticket');

        $orders = Order::with('user')
            ->where('status', 'pending')
            ->when($readOrders, fn($q) => $q->whereNotIn('id', $readOrders))
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn($o) => [
                'type'     => 'order',
                'model_id' => $o->id,
                'icon'     => 'bi-cart3',
                'color'    => 'primary',
                'text'     => "New order {$o->order_number}",
                'sub'      => $o->user->name ?? 'Guest',
                'time'     => $o->created_at->diffForHumans(),
                'url'      => route('admin.orders.show', $o),
            ]);

        $tickets = SupportTicket::with('user')
            ->whereIn('status', ['open', 'pending'])
            ->when($readTickets, fn($q) => $q->whereNotIn('id', $readTickets))
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'type'     => 'ticket',
                'model_id' => $t->id,
                'icon'     => 'bi-headset',
                'color'    => 'warning',
                'text'     => Str::limit($t->subject, 35),
                'sub'      => $t->user->name ?? '—',
                'time'     => $t->created_at->diffForHumans(),
                'url'      => route('admin.tickets.show', $t),
            ]);

        $lowStock = Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'type'     => 'stock',
                'model_id' => $p->id,
                'icon'     => 'bi-exclamation-triangle',
                'color'    => 'danger',
                'text'     => Str::limit($p->name, 35),
                'sub'      => "Stock: {$p->stock_quantity}",
                'time'     => '',
                'url'      => route('admin.inventory.index'),
            ]);

        $all = $orders->concat($tickets)->concat($lowStock)->values()->take(8);

        return response()->json($all);
    }

    public function markRead(Request $request)
    {
        $request->validate([
            'type'     => 'required|in:order,ticket,stock',
            'model_id' => 'required|integer',
        ]);

        DB::table('admin_notification_reads')->updateOrInsert(
            [
                'user_id'  => auth()->id(),
                'type'     => $request->type,
                'model_id' => $request->model_id,
            ],
            ['read_at' => now()]
        );

        // Return fresh counts so badge updates instantly
        return $this->counts();
    }

    public function messages()
    {
        $messages = ContactMessage::where('status', 'unread')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($m) => [
                'id'      => $m->id,
                'name'    => $m->name,
                'email'   => $m->email,
                'subject' => Str::limit($m->subject ?? $m->message, 40),
                'time'    => $m->created_at->diffForHumans(),
                'url'     => route('admin.contact-messages.show', $m),
            ]);

        return response()->json($messages);
    }
}
