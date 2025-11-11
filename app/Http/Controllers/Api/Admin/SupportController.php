<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    /**
     * Get list of support tickets with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status');
        $priority = $request->input('priority');
        $category = $request->input('category');
        $assignedTo = $request->input('assigned_to');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = DB::table('support_tickets')
            ->leftJoin('users as clients', 'support_tickets.user_id', '=', 'clients.id')
            ->leftJoin('users as agents', 'support_tickets.assigned_to', '=', 'agents.id')
            ->select('support_tickets.*', 'clients.name as client_name', 'clients.email as client_email', 'agents.name as agent_name');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('support_tickets.ticket_number', 'LIKE', "%{$search}%")
                  ->orWhere('support_tickets.subject', 'LIKE', "%{$search}%")
                  ->orWhere('clients.name', 'LIKE', "%{$search}%")
                  ->orWhere('clients.email', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('support_tickets.status', $status);
        }

        // Priority filter
        if ($priority) {
            $query->where('support_tickets.priority', $priority);
        }

        // Category filter
        if ($category) {
            $query->where('support_tickets.category', $category);
        }

        // Assigned to filter
        if ($assignedTo) {
            $query->where('support_tickets.assigned_to', $assignedTo);
        }

        // Sorting
        $query->orderBy('support_tickets.' . $sortBy, $sortOrder);

        $tickets = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_tickets' => DB::table('support_tickets')->count(),
            'open_tickets' => DB::table('support_tickets')->where('status', 'open')->count(),
            'pending_tickets' => DB::table('support_tickets')->where('status', 'pending')->count(),
            'resolved_tickets' => DB::table('support_tickets')->where('status', 'resolved')->count(),
            'closed_tickets' => DB::table('support_tickets')->where('status', 'closed')->count(),
            'unassigned_tickets' => DB::table('support_tickets')->whereNull('assigned_to')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get single ticket with messages
     */
    public function show($id)
    {
        $ticket = DB::table('support_tickets')
            ->leftJoin('users as clients', 'support_tickets.user_id', '=', 'clients.id')
            ->leftJoin('users as agents', 'support_tickets.assigned_to', '=', 'agents.id')
            ->select('support_tickets.*', 'clients.name as client_name', 'clients.email as client_email', 'agents.name as agent_name')
            ->where('support_tickets.id', $id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        // Get messages
        $messages = DB::table('support_ticket_messages')
            ->leftJoin('users', 'support_ticket_messages.sender_id', '=', 'users.id')
            ->select('support_ticket_messages.*', 'users.name as sender_name', 'users.avatar as sender_avatar')
            ->where('support_ticket_messages.ticket_id', $id)
            ->orderBy('support_ticket_messages.created_at', 'asc')
            ->get();

        // Get attachments for each message
        foreach ($messages as $message) {
            $message->attachments = DB::table('support_ticket_attachments')
                ->where('message_id', $message->id)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Update ticket
     */
    public function update(Request $request, $id)
    {
        $ticket = DB::table('support_tickets')->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:open,pending,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'category' => 'sometimes|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = array_filter([
            'status' => $request->status,
            'priority' => $request->priority,
            'category' => $request->category,
            'assigned_to' => $request->assigned_to,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        // Update resolved_at if status changed to resolved
        if ($request->status === 'resolved' && $ticket->status !== 'resolved') {
            $updateData['resolved_at'] = now();
        }

        // Update closed_at if status changed to closed
        if ($request->status === 'closed' && $ticket->status !== 'closed') {
            $updateData['closed_at'] = now();
        }

        DB::table('support_tickets')->where('id', $id)->update($updateData);

        $updatedTicket = DB::table('support_tickets')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['ticket' => $updatedTicket],
            'message' => 'Ticket updated successfully',
        ]);
    }

    /**
     * Assign ticket to agent
     */
    public function assign(Request $request, $id)
    {
        $ticket = DB::table('support_tickets')->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::table('support_tickets')->where('id', $id)->update([
            'assigned_to' => $request->agent_id,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket assigned successfully',
        ]);
    }

    /**
     * Add message to ticket
     */
    public function addMessage(Request $request, $id)
    {
        $ticket = DB::table('support_tickets')->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'is_internal' => 'boolean',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create message
        $messageId = DB::table('support_ticket_messages')->insertGetId([
            'ticket_id' => $id,
            'sender_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $request->message,
            'is_internal' => $request->is_internal ?? false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePath = $file->store('support/attachments', 'public');

                DB::table('support_ticket_attachments')->insert([
                    'message_id' => $messageId,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'created_at' => now(),
                ]);
            }
        }

        // Update ticket last_response_at
        DB::table('support_tickets')->where('id', $id)->update([
            'last_response_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message added successfully',
        ]);
    }

    /**
     * Delete ticket
     */
    public function destroy($id)
    {
        $ticket = DB::table('support_tickets')->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        // Delete attachments files
        $attachments = DB::table('support_ticket_attachments')
            ->join('support_ticket_messages', 'support_ticket_attachments.message_id', '=', 'support_ticket_messages.id')
            ->where('support_ticket_messages.ticket_id', $id)
            ->select('support_ticket_attachments.file_path')
            ->get();

        foreach ($attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // Delete messages and attachments
        DB::table('support_ticket_messages')->where('ticket_id', $id)->delete();

        // Delete ticket
        DB::table('support_tickets')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully',
        ]);
    }

    /**
     * Get ticket statistics
     */
    public function stats()
    {
        $stats = [
            'total_tickets' => DB::table('support_tickets')->count(),
            'open_tickets' => DB::table('support_tickets')->where('status', 'open')->count(),
            'pending_tickets' => DB::table('support_tickets')->where('status', 'pending')->count(),
            'resolved_tickets' => DB::table('support_tickets')->where('status', 'resolved')->count(),
            'closed_tickets' => DB::table('support_tickets')->where('status', 'closed')->count(),
            'unassigned_tickets' => DB::table('support_tickets')->whereNull('assigned_to')->count(),
            'tickets_today' => DB::table('support_tickets')->whereDate('created_at', today())->count(),
            'tickets_this_week' => DB::table('support_tickets')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'tickets_this_month' => DB::table('support_tickets')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'by_priority' => DB::table('support_tickets')
                ->select('priority', DB::raw('COUNT(*) as count'))
                ->groupBy('priority')
                ->get(),
            'by_category' => DB::table('support_tickets')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->get(),
            'by_status' => DB::table('support_tickets')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
            'average_resolution_time' => DB::table('support_tickets')
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get canned responses
     */
    public function getCannedResponses()
    {
        $responses = DB::table('canned_responses')
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['responses' => $responses],
        ]);
    }

    /**
     * Create canned response
     */
    public function createCannedResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $responseId = DB::table('canned_responses')->insertGetId([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = DB::table('canned_responses')->find($responseId);

        return response()->json([
            'success' => true,
            'data' => ['response' => $response],
            'message' => 'Canned response created successfully',
        ], 201);
    }

    /**
     * Update canned response
     */
    public function updateCannedResponse(Request $request, $id)
    {
        $cannedResponse = DB::table('canned_responses')->find($id);

        if (!$cannedResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Canned response not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string',
            'content' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = array_filter([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        DB::table('canned_responses')->where('id', $id)->update($updateData);

        $updated = DB::table('canned_responses')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['response' => $updated],
            'message' => 'Canned response updated successfully',
        ]);
    }

    /**
     * Delete canned response
     */
    public function deleteCannedResponse($id)
    {
        $cannedResponse = DB::table('canned_responses')->find($id);

        if (!$cannedResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Canned response not found',
            ], 404);
        }

        DB::table('canned_responses')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Canned response deleted successfully',
        ]);
    }

    /**
     * Get available support agents
     */
    public function getAgents()
    {
        $agents = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'support_agent')
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.email', 'users.avatar')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['agents' => $agents],
        ]);
    }
}
