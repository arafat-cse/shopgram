<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    // -------------------------------------------------------------------------
    // Token helpers
    // -------------------------------------------------------------------------

    private function makeGuestToken(LiveChat $chat): string
    {
        return base64_encode(json_encode([
            'chat_id'    => $chat->id,
            'session_id' => $chat->session_id,
            'name'       => $chat->guest_name,
            'type'       => 'guest',
            'signature'  => hash_hmac('sha256', "{$chat->id}:{$chat->session_id}", config('chat.internal_key')),
        ]));
    }

    private function validateGuestAccess(Request $request, LiveChat $chat): void
    {
        $sessionId = $request->header('X-Session-Id') ?? $request->session_id;
        abort_if($chat->session_id !== $sessionId, 403, 'Access denied');
    }

    private function isInternalRequest(Request $request): bool
    {
        return $request->header('X-Internal-Key') === config('chat.internal_key');
    }

    // -------------------------------------------------------------------------
    // Public guest endpoints
    // -------------------------------------------------------------------------

    /**
     * POST /api/livechat/start
     * Create or resume a live chat session.
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:30',
            'session_id' => 'nullable|string|max:36',
        ]);

        $chat = null;
        $isNewChat = false;

        if (!empty($validated['session_id'])) {
            $chat = LiveChat::where('session_id', $validated['session_id'])
                ->where('status', '!=', 'closed')
                ->where('guest_name', $validated['name'])
                ->where('guest_phone', $validated['phone'])
                ->first();
        }

        if (!$chat) {
            $chat = LiveChat::create([
                'session_id'      => Str::uuid()->toString(),
                'guest_name'      => $validated['name'],
                'guest_phone'     => $validated['phone'],
                'user_id'         => auth()->id(),
                'status'          => 'waiting',
                'last_message_at' => now(),
            ]);
            $isNewChat = true;
        }

        // Notify admin panel of new chat via Node.js
        if ($isNewChat) {
            try {
                Http::withHeaders([
                    'X-Internal-Key' => config('chat.internal_key'),
                    'Accept'         => 'application/json',
                ])->post(config('chat.node_internal_url') . '/internal/livechat/notify-admin', [
                    'chat_id'    => $chat->id,
                    'guest_name' => $chat->guest_name,
                    'status'     => $chat->status,
                ]);
            } catch (\Exception $e) {
                logger()->error('LiveChat: notify-admin failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'chat_id'    => $chat->id,
            'token'      => $this->makeGuestToken($chat),
            'guest_name' => $chat->guest_name,
            'session_id' => $chat->session_id,
            'status'     => $chat->status,
        ]);
    }

    /**
     * GET /api/livechat/token
     * Return a fresh socket token for an existing session.
     * Requires X-Session-Id and X-Chat-Id headers.
     */
    public function guestToken(Request $request)
    {
        $chatId    = $request->header('X-Chat-Id');
        $sessionId = $request->header('X-Session-Id');

        abort_if(!$chatId || !$sessionId, 400, 'Missing chat identifiers');

        $chat = LiveChat::findOrFail($chatId);
        abort_if($chat->session_id !== $sessionId, 403, 'Access denied');

        return response()->json(['token' => $this->makeGuestToken($chat)]);
    }

    /**
     * GET /api/livechat/{chat}/messages
     * Return message history. Guest must supply X-Session-Id header.
     * Staff (auth + order.chat permission) can access directly.
     */
    public function messages(Request $request, LiveChat $chat)
    {
        $isStaff = auth()->check() && auth()->user()->can('order.chat');

        if (!$isStaff) {
            $this->validateGuestAccess($request, $chat);
            // Mark staff messages as read when guest views
            LiveChatMessage::where('chat_id', $chat->id)
                ->where('sender_type', 'staff')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            // Staff viewing: mark guest messages as read
            LiveChatMessage::where('chat_id', $chat->id)
                ->where('sender_type', 'guest')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json($chat->messages()->get());
    }

    /**
     * POST /api/livechat/{chat}/messages
     * Save a message — called by Node.js with X-Internal-Key.
     */
    public function store(Request $request, LiveChat $chat)
    {
        abort_unless($this->isInternalRequest($request), 403, 'Forbidden');

        $validated = $request->validate([
            'sender_type'     => 'required|in:guest,staff',
            'sender_name'     => 'required|string|max:255',
            'user_id'         => 'nullable|exists:users,id',
            'message'         => 'nullable|required_without:attachment|string|max:5000',
            'attachment'      => 'nullable|string|max:500',
            'attachment_type' => 'nullable|in:image,file',
            'attachment_name' => 'nullable|string|max:255',
            'attachment_size' => 'nullable|integer|min:0',
        ]);

        // Activate chat on first staff message
        if ($validated['sender_type'] === 'staff' && $chat->status === 'waiting') {
            $chat->update(['status' => 'active']);
        }

        $chat->update(['last_message_at' => now()]);

        $msg = LiveChatMessage::create(array_merge($validated, ['chat_id' => $chat->id]))->refresh();
        $autoReply = null;

        if ($validated['sender_type'] === 'guest') {
            $hasStaffReply = LiveChatMessage::where('chat_id', $chat->id)
                ->where('sender_type', 'staff')
                ->exists();

            if (!$hasStaffReply) {
                $chat->loadMissing('assignedAgent:id,name');
                $senderName = $chat->assignedAgent?->name ?? 'ShopGram Support';

                $autoReply = LiveChatMessage::create([
                    'chat_id'     => $chat->id,
                    'sender_type' => 'staff',
                    'sender_name' => $senderName,
                    'user_id'     => $chat->assigned_to,
                    'message'     => "আসসালামু আলাইকুম {$chat->guest_name}.\nWelcome to ShopGram. How can I help you?",
                    'is_read'     => false,
                ])->refresh();

                $chat->update(['last_message_at' => now()]);
            }
        }

        return response()->json([
            'message'    => $msg,
            'auto_reply' => $autoReply,
        ]);
    }

    /**
     * POST /api/livechat/{chat}/upload
     * Upload a file attachment. Guest must supply X-Session-Id, or staff via auth.
     */
    public function upload(Request $request, LiveChat $chat)
    {
        $isStaff = auth()->check() && auth()->user()->can('order.chat');

        if (!$isStaff) {
            $this->validateGuestAccess($request, $chat);
        }

        abort_if($chat->status === 'closed', 403, 'Chat is closed');

        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx',
        ]);

        $file     = $request->file('file');
        $isImage  = str_starts_with($file->getMimeType(), 'image/');
        $path     = $file->store('live-chat-attachments', 'public');

        return response()->json([
            'url'  => asset('storage/' . $path),
            'type' => $isImage ? 'image' : 'file',
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * POST /api/livechat/{chat}/close (via web.php — open to internal key or staff)
     * Closes a chat session and notifies the Node.js service.
     */
    public function close(Request $request, LiveChat $chat)
    {
        $isInternal = $this->isInternalRequest($request);
        $isStaff    = auth()->check() && auth()->user()->can('order.chat');

        abort_unless($isInternal || $isStaff, 403, 'Forbidden');

        $chat->update(['status' => 'closed']);

        try {
            Http::withHeaders([
                'X-Internal-Key' => config('chat.internal_key'),
                'Accept'         => 'application/json',
            ])->post(config('chat.node_internal_url') . '/internal/livechat/close/' . $chat->id);
        } catch (\Exception $e) {
            logger()->error('LiveChat: failed to notify Node.js of close: ' . $e->getMessage());
        }

        return response()->json(['closed' => true]);
    }

    /**
     * GET /api/livechat/unread
     * Count unread staff messages for a guest session.
     * Requires X-Session-Id query param.
     */
    public function unreadCount(Request $request)
    {
        $sessionId = $request->header('X-Session-Id') ?? $request->query('session_id');
        abort_if(!$sessionId, 400, 'Missing session_id');

        $chat = LiveChat::where('session_id', $sessionId)->first();

        if (!$chat) {
            return response()->json(['count' => 0]);
        }

        $count = LiveChatMessage::where('chat_id', $chat->id)
            ->where('sender_type', 'staff')
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * GET /api/livechat/staff-token
     * Returns a socket token for authenticated staff.
     */
    public function staffToken(Request $request)
    {
        $user  = auth()->user();
        abort_unless($user, 401);

        $token = base64_encode(json_encode([
            'user_id'   => $user->id,
            'name'      => $user->name,
            'type'      => 'staff_livechat',
            'signature' => hash_hmac('sha256', (string) $user->id, config('chat.internal_key')),
        ]));

        return response()->json(['token' => $token]);
    }

    // -------------------------------------------------------------------------
    // Admin panel endpoints
    // -------------------------------------------------------------------------

    /**
     * GET /admin/live-chat
     */
    public function adminIndex()
    {
        $agents = \App\Models\User::where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.live-chat.index', compact('agents'));
    }

    /**
     * GET /admin/live-chat/chats
     * Paginated list of chats, filterable by status.
     */
    public function adminChats(Request $request)
    {
        $query = LiveChat::with('latestMessage', 'assignedAgent:id,name')
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('guest_name', 'like', "%{$q}%")
                    ->orWhere('guest_phone', 'like', "%{$q}%");
            });
        }

        $chats = $query->paginate(30);

        // Append unread count per chat
        $chatIds = $chats->pluck('id');
        $unreadCounts = LiveChatMessage::whereIn('chat_id', $chatIds)
            ->where('sender_type', 'guest')
            ->where('is_read', false)
            ->selectRaw('chat_id, COUNT(*) as cnt')
            ->groupBy('chat_id')
            ->pluck('cnt', 'chat_id');

        $chats->getCollection()->transform(function ($chat) use ($unreadCounts) {
            $chat->unread_count = $unreadCounts[$chat->id] ?? 0;
            return $chat;
        });

        return response()->json($chats);
    }

    /**
     * GET /admin/live-chat/{chat}/messages
     * Return messages and mark guest messages as read.
     */
    public function adminMessages(LiveChat $chat)
    {
        LiveChatMessage::where('chat_id', $chat->id)
            ->where('sender_type', 'guest')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chat->load('assignedAgent:id,name');

        return response()->json([
            'chat'     => array_merge($chat->toArray(), [
                'assigned_to_name' => $chat->assignedAgent?->name,
            ]),
            'messages' => $chat->messages()->get(),
        ]);
    }

    /**
     * POST /admin/live-chat/{chat}/messages
     * Staff sends a message. Saves to DB then emits to Node.js room via HTTP.
     */
    public function adminStore(Request $request, LiveChat $chat)
    {
        abort_if($chat->status === 'closed', 403, 'Chat is closed');

        $validated = $request->validate([
            'message'         => 'nullable|required_without:attachment|string|max:5000',
            'attachment'      => 'nullable|string|max:500',
            'attachment_type' => 'nullable|in:image,file',
            'attachment_name' => 'nullable|string|max:255',
            'attachment_size' => 'nullable|integer|min:0',
        ]);

        $user = auth()->user();

        // Activate chat if still waiting
        if ($chat->status === 'waiting') {
            $chat->update([
                'status'      => 'active',
                'assigned_to' => $user->id,
            ]);
        }

        $chat->update(['last_message_at' => now()]);

        $msg = LiveChatMessage::create([
            'chat_id'         => $chat->id,
            'sender_type'     => 'staff',
            'sender_name'     => $user->name,
            'user_id'         => $user->id,
            'message'         => $validated['message'] ?? null,
            'attachment'      => $validated['attachment'] ?? null,
            'attachment_type' => $validated['attachment_type'] ?? null,
            'attachment_name' => $validated['attachment_name'] ?? null,
            'attachment_size' => $validated['attachment_size'] ?? null,
            'is_read'         => false,
        ]);

        // Emit to Node.js room so the guest receives via socket
        try {
            Http::withHeaders([
                'X-Internal-Key' => config('chat.internal_key'),
                'Accept'         => 'application/json',
            ])->post(config('chat.node_internal_url') . '/internal/livechat/emit/' . $chat->id, [
                'message' => $msg->toArray(),
            ]);
        } catch (\Exception $e) {
            logger()->error('LiveChat: failed to emit message to Node.js: ' . $e->getMessage());
        }

        return response()->json($msg);
    }

    /**
     * POST /admin/live-chat/{chat}/close
     */
    public function adminClose(LiveChat $chat)
    {
        $chat->update(['status' => 'closed']);

        try {
            Http::withHeaders([
                'X-Internal-Key' => config('chat.internal_key'),
                'Accept'         => 'application/json',
            ])->post(config('chat.node_internal_url') . '/internal/livechat/close/' . $chat->id);
        } catch (\Exception $e) {
            logger()->error('LiveChat: failed to notify Node.js of admin close: ' . $e->getMessage());
        }

        return response()->json(['closed' => true]);
    }

    /**
     * POST /admin/live-chat/{chat}/reopen
     */
    public function adminReopen(LiveChat $chat)
    {
        $user = auth()->user();

        $chat->update([
            'status'          => 'active',
            'assigned_to'     => $chat->assigned_to ?: $user->id,
            'last_message_at' => now(),
        ]);

        try {
            Http::withHeaders([
                'X-Internal-Key' => config('chat.internal_key'),
                'Accept'         => 'application/json',
            ])->post(config('chat.node_internal_url') . '/internal/livechat/reopen/' . $chat->id);
        } catch (\Exception $e) {
            logger()->error('LiveChat: failed to notify Node.js of admin reopen: ' . $e->getMessage());
        }

        return response()->json(['reopened' => true]);
    }

    /**
     * POST /admin/live-chat/{chat}/assign
     */
    public function adminAssign(Request $request, LiveChat $chat)
    {
        $request->validate(['agent_id' => 'nullable|exists:users,id']);
        $chat->update(['assigned_to' => $request->agent_id ?: null]);
        return response()->json(['assigned' => true]);
    }

    /**
     * POST /admin/live-chat/{chat}/upload
     */
    public function adminUpload(Request $request, LiveChat $chat)
    {
        $request->validate(['file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx']);
        $file = $request->file('file');
        $path = $file->store('livechat/attachments', 'public');
        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image/') ? 'image' : 'file';
        return response()->json([
            'url'  => asset('storage/' . $path),
            'name' => $file->getClientOriginalName(),
            'type' => $type,
            'size' => $file->getSize(),
        ]);
    }

    /**
     * GET /admin/live-chat/staff-token
     */
    public function adminStaffToken(Request $request)
    {
        return $this->staffToken($request);
    }

    /**
     * GET /admin/live-chat/unread
     * Total unread guest messages across all open chats (for admin nav badge).
     */
    public function adminTotalUnread(Request $request)
    {
        $count = LiveChatMessage::whereIn('chat_id', function ($q) {
            $q->select('id')
              ->from('live_chats')
              ->whereIn('status', ['waiting', 'active']);
        })
        ->where('sender_type', 'guest')
        ->where('is_read', false)
        ->count();

        return response()->json(['count' => $count]);
    }
}
