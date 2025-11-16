<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\SupportTicketAttachment;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AdminConversation;
use App\Models\AdminMessage;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerServiceController extends Controller
{
    /**
     * Display tickets list
     */
    public function tickets(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignedTo', 'messages'])
            ->search($request->search)
            ->byStatus($request->status)
            ->byCategory($request->category)
            ->byPriority($request->priority);

        // Date filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Assigned filter
        if ($request->assigned_to) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->count(),
        ];

        // Get admin users for assignment
        $admins = User::where('user_type', 'admin')->get();

        return view('customerservices.tickets', compact('tickets', 'stats', 'admins'));
    }

    /**
     * Display single ticket details
     */
    public function showTicket($id)
    {
        try {
            $ticket = SupportTicket::with(['user', 'assignedTo', 'messages.sender', 'attachments'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'ticket' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }
    }

    /**
     * Create new ticket (for testing)
     */
    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_type' => 'required|in:client,provider',
            'category' => 'required|in:technical,billing,booking,complaint,general',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = SupportTicket::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:open,in_progress,waiting_customer,resolved,closed',
            ]);

            $ticket = SupportTicket::findOrFail($id);
            $ticket->status = $validated['status'];

            if ($validated['status'] === 'resolved') {
                $ticket->resolved_at = now();
            } elseif ($validated['status'] === 'closed') {
                $ticket->closed_at = now();
            }

            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket status',
            ], 500);
        }
    }

    /**
     * Assign ticket to admin
     */
    public function assignTicket(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $ticket = SupportTicket::findOrFail($id);
            $ticket->assigned_to = $validated['assigned_to'];
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket assigned successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign ticket',
            ], 500);
        }
    }

    /**
     * Add message to ticket
     */
    public function addTicketMessage(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string',
                'is_internal_note' => 'boolean',
            ]);

            $ticket = SupportTicket::findOrFail($id);

            $message = SupportTicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_id' => auth()->id(),
                'sender_type' => 'admin',
                'message' => $validated['message'],
                'is_internal_note' => $request->is_internal_note ?? false,
            ]);

            // Update ticket status if needed
            if ($ticket->status === 'waiting_customer') {
                $ticket->update(['status' => 'in_progress']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Message added successfully',
                'data' => $message->load('sender'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add message',
            ], 500);
        }
    }

    /**
     * Display chat interface for customer support (Admin conversations only)
     */
    public function chat(Request $request)
    {
        $adminId = auth()->id();

        // Get admin conversations
        $query = AdminConversation::with(['user', 'lastMessage', 'provider'])
            ->where('admin_id', $adminId)
            ->orderBy('last_message_at', 'desc');

        // Filter by specific user ID
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by user type
        if ($request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $conversations = $query->paginate(20);

        // Get lists for starting new conversations
        $clients = User::where('user_type', 'client')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $providers = ServiceProvider::with('user')
            ->whereHas('user', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('business_name')
            ->get();

        // Get existing conversation mappings (full objects keyed by user_id)
        $clientConversations = AdminConversation::where('admin_id', $adminId)
            ->where('user_type', 'client')
            ->get()
            ->keyBy('user_id');

        $providerConversations = AdminConversation::where('admin_id', $adminId)
            ->where('user_type', 'provider')
            ->get()
            ->keyBy('user_id');

        // Statistics
        $stats = [
            'total_conversations' => AdminConversation::where('admin_id', $adminId)->count(),
            'client_conversations' => AdminConversation::where('admin_id', $adminId)->where('user_type', 'client')->count(),
            'provider_conversations' => AdminConversation::where('admin_id', $adminId)->where('user_type', 'provider')->count(),
            'unread_messages' => AdminMessage::whereHas('conversation', function($q) use ($adminId) {
                $q->where('admin_id', $adminId);
            })->where('is_read', false)->where('sender_type', '!=', 'admin')->count(),
        ];

        return view('customerservices.chat', compact('conversations', 'stats', 'clients', 'providers', 'clientConversations', 'providerConversations'));
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages($id)
    {
        $conversation = AdminConversation::with(['user', 'provider'])
            ->where('admin_id', auth()->id())
            ->findOrFail($id);

        $messages = AdminMessage::where('conversation_id', $id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        AdminMessage::where('conversation_id', $id)
            ->where('sender_type', '!=', 'admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        // Append avatar_url to user
        $conversation->user->append('avatar_url');
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message in conversation
     */
    public function sendConversationMessage(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $conversation = AdminConversation::where('admin_id', auth()->id())
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $message = AdminMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'sender_type' => 'admin',
                'message_type' => 'text',
                'content' => $validated['message'],
                'is_read' => false,
            ]);

            // Update conversation
            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
                'user_unread_count' => DB::raw('user_unread_count + 1'),
            ]);

            // Note: No notification is sent to avoid duplicate notifications
            // The client app will receive messages through real-time polling in the chat screen

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message->load('sender'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to send conversation message', [
                'conversation_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
            ], 500);
        }
    }

    /**
     * Mark conversation as read
     */
    public function markConversationAsRead($id)
    {
        $conversation = AdminConversation::where('admin_id', auth()->id())
            ->findOrFail($id);

        AdminMessage::where('conversation_id', $id)
            ->where('sender_type', '!=', 'admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Reset admin unread count
        $conversation->update(['admin_unread_count' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation marked as read',
        ]);
    }
    
    /**
     * Create a new admin conversation with a client or provider
     */
    public function createConversation(Request $request)
    {
        try {
            // Determine user_id and user_type from request
            $userId = null;
            $userType = null;
            
            // Check if client_id is provided
            if ($request->has('client_id')) {
                $request->validate(['client_id' => 'required|exists:users,id']);
                $userId = $request->client_id;
                $userType = 'client';
            }
            // Check if provider_id is provided
            elseif ($request->has('provider_id')) {
                $request->validate(['provider_id' => 'required|exists:service_providers,id']);
                $provider = ServiceProvider::findOrFail($request->provider_id);
                $userId = $provider->user_id;
                $userType = 'provider';
            }
            // Check if user_id and user_type are provided
            elseif ($request->has('user_id') && $request->has('user_type')) {
                $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'user_type' => 'required|in:client,provider',
                ]);
                $userId = $request->user_id;
                $userType = $request->user_type;
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Either client_id, provider_id, or user_id with user_type must be provided',
                ], 422);
            }
            
            $adminId = auth()->id();
            
            // Check if conversation already exists
            $conversation = AdminConversation::where('admin_id', $adminId)
                ->where('user_id', $userId)
                ->where('user_type', $userType)
                ->first();
            
            // If conversation exists, return it
            if ($conversation) {
                $conversation->load('user');
                if ($userType === 'provider') {
                    $conversation->load('provider');
                }
                
                return response()->json([
                    'success' => true,
                    'conversation' => $conversation,
                    'message' => 'Existing conversation found',
                ]);
            }
            
            // Verify user exists
            $user = User::findOrFail($userId);
            
            // Create new admin conversation
            $conversation = AdminConversation::create([
                'admin_id' => $adminId,
                'user_id' => $userId,
                'user_type' => $userType,
                'last_message_at' => now(),
            ]);
            
            // Load relationships
            $conversation->load('user');
            if ($userType === 'provider') {
                $conversation->load('provider');
            }
            
            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'message' => 'New conversation created',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating admin conversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer service statistics
     */
    public function stats()
    {
        $ticketStats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->count(),
        ];

        $chatStats = [
            'total_conversations' => Conversation::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
        ];

        // Response time stats (average time to first response)
        $avgResponseTime = SupportTicketMessage::select(
            DB::raw("AVG(EXTRACT(EPOCH FROM (support_ticket_messages.created_at - support_tickets.created_at))) as avg_seconds")
        )
            ->join('support_tickets', 'support_tickets.id', '=', 'support_ticket_messages.ticket_id')
            ->where('support_ticket_messages.sender_type', 'admin')
            ->whereRaw("support_ticket_messages.id = (SELECT MIN(id) FROM support_ticket_messages stm WHERE stm.ticket_id = support_tickets.id AND stm.sender_type = 'admin')")
            ->first();

        return view('customerservices.stats', compact('ticketStats', 'chatStats', 'avgResponseTime'));
    }
}
