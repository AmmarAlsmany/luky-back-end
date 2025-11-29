@extends('layouts.vertical', ['title' => __('tickets.tickets_management')])

@section('css')


  @vite([
    'node_modules/choices.js/public/assets/styles/choices.min.css',
    'node_modules/flatpickr/dist/flatpickr.min.css',
  ])
@endsection
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')

{{-- Statistics Cards --}}
<div class="row mb-3">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <iconify-icon icon="solar:ticket-bold-duotone" class="fs-32 text-primary"></iconify-icon>
          </div>
          <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">{{ $stats['total'] }}</h4>
            <p class="text-muted mb-0">{{ __('tickets.total_tickets') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <iconify-icon icon="solar:door-opened-bold-duotone" class="fs-32 text-info"></iconify-icon>
          </div>
          <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">{{ $stats['open'] }}</h4>
            <p class="text-muted mb-0">{{ __('tickets.open_tickets') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <iconify-icon icon="solar:settings-bold-duotone" class="fs-32 text-warning"></iconify-icon>
          </div>
          <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
            <p class="text-muted mb-0">{{ __('tickets.in_progress') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-32 text-danger"></iconify-icon>
          </div>
          <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">{{ $stats['urgent'] }}</h4>
            <p class="text-muted mb-0">{{ __('tickets.urgent') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ====== Portion 1: Filters / Status Pills / Bulk Actions ====== --}}
<!-- View button (no navigation) -->


<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <div class="d-flex flex-column">
          <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:ticket-bold-duotone" class="text-primary fs-5"></iconify-icon>
            <h5 class="modal-title mb-0" id="ticketNumber"></h5>
            <span class="badge" id="ticketStatusBadge"></span>
          </div>
          <small class="text-muted" id="ticketMeta"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <!-- Summary strip -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="p-2 bg-light-subtle rounded">
              <div class="text-muted small mb-1">{{ __('tickets.customer_label') }}</div>
              <div class="d-flex align-items-center gap-2" id="ticketCustomer">
                <!-- Dynamic content -->
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-2 bg-light-subtle rounded">
              <div class="text-muted small mb-1">{{ __('tickets.assignee_label') }}</div>
              <div class="d-flex align-items-center gap-2" id="ticketAssignee">
                <!-- Dynamic content -->
              </div>
            </div>
          </div>
        </div>

        <!-- Meta grid -->
        <div class="row g-3 mb-3">
          <div class="col-6 col-md-3">
            <div class="text-muted small">{{ __('tickets.category') }}</div>
            <div class="fw-semibold" id="ticketCategory"></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">{{ __('tickets.priority') }}</div>
            <div id="ticketPriority"></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">{{ __('tickets.user_type') }}</div>
            <div class="fw-semibold" id="ticketUserType"></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">{{ __('tickets.status') }}</div>
            <div id="ticketStatus"></div>
          </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
          <div class="text-muted small mb-1">{{ __('tickets.description') }}</div>
          <div class="border rounded p-2" id="ticketDescription"></div>
        </div>

        <!-- Messages -->
        <div class="mb-3">
          <div class="text-muted small mb-2">{{ __('tickets.conversation') }}</div>
          <div id="ticketMessages" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
            <!-- Dynamic messages -->
          </div>
        </div>

        <!-- Reply form -->
        <div>
          <div class="text-muted small mb-1">{{ __('tickets.add_reply') }}</div>
          <textarea class="form-control mb-2" id="replyMessage" rows="3" placeholder="{{ __('tickets.reply_placeholder') }}"></textarea>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="isInternalNote">
            <label class="form-check-label" for="isInternalNote">
              {{ __('tickets.internal_note') }}
            </label>
          </div>
          <button class="btn btn-primary btn-sm" id="sendReplyBtn">
            <iconify-icon icon="solar:plain-2-bold-duotone" class="me-1"></iconify-icon>
            {{ __('tickets.send_reply') }}
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <select class="form-select form-select-sm w-auto" id="updateStatus">
          <option value="">{{ __('tickets.update_status') }}</option>
          <option value="open">{{ __('tickets.open') }}</option>
          <option value="in_progress">{{ __('tickets.in_progress') }}</option>
          <option value="waiting_customer">{{ __('tickets.waiting_customer') }}</option>
          <option value="resolved">{{ __('tickets.resolved') }}</option>
          <option value="closed">{{ __('tickets.closed') }}</option>
        </select>
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('tickets.close_button') }}</button>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12">
  <div class="card">

    {{-- Header: Title (left) + Search & Date (right) --}}
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div>
        <h4 class="card-title mb-0">{{ __('tickets.support_tickets') }}</h4>
        @if(request()->hasAny(['search', 'date', 'category', 'priority', 'assigned_to', 'status']))
          <small class="text-muted">
            <iconify-icon icon="solar:filter-bold" class="text-primary"></iconify-icon>
            {{ __('tickets.filters_active') }}
          </small>
        @endif
      </div>

      <div class="d-flex align-items-end gap-2 flex-wrap">
        {{-- Search --}}
        <div class="d-flex align-items-end" style="min-width: 260px;">
          <div class="w-100">
            <label class="form-label small mb-1" for="t_q">{{ __('tickets.search') }}</label>
            <input id="t_q" type="search" class="form-control" placeholder="{{ __('tickets.search_placeholder') }}" value="{{ request('search') }}">
          </div>
        </div>

        {{-- Date --}}
        <div class="d-flex align-items-end" style="min-width: 220px;">
          <div class="w-100">
            <label class="form-label small mb-1" for="humanfd-datepicker">{{ __('tickets.created_on') }}</label>
            <input type="text" id="humanfd-datepicker" class="form-control" placeholder="{{ __('tickets.select_date') }}" value="{{ request('date') }}">
          </div>
        </div>
        
        {{-- Apply Filters Button --}}
        <div class="d-flex align-items-end">
          <button type="button" class="btn btn-primary" id="applyFiltersBtn">
            <iconify-icon icon="solar:filter-bold-duotone" class="me-1"></iconify-icon>
            {{ __('tickets.apply_filters') }}
          </button>
        </div>
        
        {{-- Clear Filters Button --}}
        @if(request()->hasAny(['search', 'date', 'category', 'priority', 'assigned_to', 'status']))
        <div class="d-flex align-items-end">
          <a href="{{ route('customerservices.tickets') }}" class="btn btn-light">
            <iconify-icon icon="solar:refresh-bold-duotone" class="me-1"></iconify-icon>
            {{ __('tickets.clear') }}
          </a>
        </div>
        @endif
      </div>
    </div>

    {{-- Body: 6 inputs in 2 rows (3 + 3) --}}
    <div class="card-body pt-2">
      <div class="row g-3 align-items-end">
        {{-- Row 1 --}}
        <div class="col-12 col-md-4">
          <label class="form-label small mb-1">{{ __('tickets.category') }}</label>
          <select id="t_category" class="form-select">
            <option value="">{{ __('tickets.all') }}</option>
            <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>{{ __('tickets.technical') }}</option>
            <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>{{ __('tickets.billing') }}</option>
            <option value="booking" {{ request('category') === 'booking' ? 'selected' : '' }}>{{ __('tickets.booking') }}</option>
            <option value="complaint" {{ request('category') === 'complaint' ? 'selected' : '' }}>{{ __('tickets.complaint') }}</option>
            <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>{{ __('tickets.general') }}</option>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label small mb-1">{{ __('tickets.priority') }}</label>
          <select id="t_priority" class="form-select">
            <option value="">{{ __('tickets.any') }}</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('tickets.low') }}</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('tickets.medium') }}</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('tickets.high') }}</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('tickets.urgent') }}</option>
          </select>
        </div>



        {{-- Row 2 --}}
        <div class="col-12 col-md-4">
          <label class="form-label small mb-1">{{ __('tickets.assignee') }}</label>
          <select id="t_assignee" class="form-select">
            <option value="">{{ __('tickets.anyone') }}</option>
            <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>{{ __('tickets.unassigned') }}</option>
            @foreach($admins as $admin)
              <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                {{ $admin->name }} ({{ $admin->email }})
              </option>
            @endforeach
          </select>
        </div>


      </div>


      {{-- Status pills (nav-pills style) --}}
      <div class="col-12">
        <label class="form-label small mb-1 d-block py-2">{{ __('tickets.ticket_status') }}</label>

        <ul class="nav nav-pills gap-1 flex-wrap bg-light-subtle rounded px-2 py-1"
            id="ticketStatusPills" role="tablist" style="border: 1px solid rgba(0,0,0,.08);">
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') ? '' : 'active' }} py-1 px-2" href="javascript:void(0);" role="tab"
              aria-selected="{{ request('status') ? 'false' : 'true' }}" data-status="">
              <span class="d-block d-sm-none"><i class="bx bx-home"></i></span>
              <span class="d-none d-sm-block">{{ __('tickets.all_status') }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'open' ? 'active' : '' }} py-1 px-2" href="javascript:void(0);" role="tab"
              aria-selected="{{ request('status') === 'open' ? 'true' : 'false' }}" data-status="open">
              <span class="d-block d-sm-none"><i class="bx bx-message-square-dots"></i></span>
              <span class="d-none d-sm-block">{{ __('tickets.open') }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'in_progress' ? 'active' : '' }} py-1 px-2" href="javascript:void(0);" role="tab"
              aria-selected="{{ request('status') === 'in_progress' ? 'true' : 'false' }}" data-status="in_progress">
              <span class="d-block d-sm-none"><i class="bx bx-time"></i></span>
              <span class="d-none d-sm-block">{{ __('tickets.in_progress') }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'resolved' ? 'active' : '' }} py-1 px-2" href="javascript:void(0);" role="tab"
              aria-selected="{{ request('status') === 'resolved' ? 'true' : 'false' }}" data-status="resolved">
              <span class="d-block d-sm-none"><i class="bx bx-check-circle"></i></span>
              <span class="d-none d-sm-block">{{ __('tickets.resolved') }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'closed' ? 'active' : '' }} py-1 px-2" href="javascript:void(0);" role="tab"
              aria-selected="{{ request('status') === 'closed' ? 'true' : 'false' }}" data-status="closed">
              <span class="d-block d-sm-none"><i class="bx bx-lock"></i></span>
              <span class="d-none d-sm-block">{{ __('tickets.closed') }}</span>
            </a>
          </li>
        </ul>
      </div>


    </div>
  </div>

  </div>
</div>

{{-- ====== Portion 2: Tickets Table ====== --}}
<div class="row">
  <div class="col-12">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title mb-0 d-flex align-items-center gap-2">
          <iconify-icon icon="solar:ticket-bold-duotone" class="text-primary fs-20"></iconify-icon>
          {{ __('tickets.support_tickets') }}
        </h4>
      </div>
      <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
          <thead class="bg-light-subtle">
          <tr>
            <th style="width:20px;">
              <div class="form-check"><input type="checkbox" class="form-check-input" id="t_check_all"></div>
            </th>
            <th>{{ __('tickets.ticket') }}</th>
            
            <th>{{ __('tickets.customer') }}</th>
            <th>{{ __('tickets.subject') }}</th>
            <th>{{ __('tickets.category') }}</th>
            <th>{{ __('tickets.priority') }}</th>
            <th>{{ __('tickets.status') }}</th>
            <th>{{ __('tickets.assignee_column') }}</th>
            <th>{{ __('tickets.created') }}</th>
            <th>{{ __('tickets.last_reply') }}</th>
            <th>{{ __('tickets.sla') }}</th>
            <th>{{ __('tickets.channel') }}</th>
            <th>{{ __('tickets.action') }}</th>
          </tr>
          </thead>
          <tbody id="tickets_tbody">
          @forelse($tickets as $ticket)
          <tr>
            <td><input type="checkbox" class="form-check-input t_row" value="{{ $ticket->id }}"></td>
            <td class="fw-semibold text-nowrap">{{ $ticket->ticket_number }}</td>
            <td class="text-nowrap">
              <img src="/images/users/avatar-{{ ($ticket->user_id % 10) + 1 }}.jpg" class="avatar-sm rounded-circle me-1" alt="">
              {{ $ticket->user->name }}
            </td>
            <td class="text-truncate" style="max-width:240px;">{{ $ticket->subject }}</td>
            <td>{{ $ticket->getCategoryDisplayName() }}</td>
            <td><span class="badge {{ $ticket->getPriorityBadgeClass() }}">{{ ucfirst($ticket->priority) }}</span></td>
            <td><span class="badge {{ $ticket->getStatusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
            <td>{{ $ticket->assignedTo?->name ?? __('tickets.unassigned') }}</td>
            <td>{{ $ticket->created_at->format('d M Y, H:i') }}</td>
            <td>{{ $ticket->messages->last()?->created_at?->format('d M Y, H:i') ?? __('tickets.no_replies') }}</td>
            <td>
              @if($ticket->status === 'open' || $ticket->status === 'in_progress')
                <span class="badge bg-warning-subtle text-warning">{{ __('tickets.due_soon') }}</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary">{{ __('tickets.closed') }}</span>
              @endif
            </td>
            <td>{{ __('tickets.app') }}</td>
            <td class="text-nowrap">
              <button class="btn btn-light btn-sm view-ticket" data-id="{{ $ticket->id }}">
                <iconify-icon icon="solar:eye-broken" class="fs-18"></iconify-icon>
              </button>
              <button class="btn btn-soft-primary btn-sm assign-ticket" data-id="{{ $ticket->id }}">
                <iconify-icon icon="solar:user-plus-bold-duotone" class="fs-18"></iconify-icon>
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="text-center py-4">
              <div class="text-muted">
                <iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 mb-2"></iconify-icon>
                <p class="mb-0">{{ __('tickets.no_tickets_found') }}</p>
              </div>
            </td>
          </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer border-top">
        {{ $tickets->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Assign Modal --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><iconify-icon icon="solar:user-check-bold-duotone" class="text-primary me-1"></iconify-icon>{{ __('tickets.assign_ticket') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">{{ __('tickets.assign_to') }}</label>
        <select id="assign_to" class="form-select">
          <option value="">{{ __('tickets.unassigned') }}</option>
          @foreach($admins as $admin)
            <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('tickets.cancel') }}</button>
        <button class="btn btn-primary" id="confirmAssignBtn">{{ __('tickets.assign') }}</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script-bottom')
  @vite([
    'resources/js/components/form-flatepicker.js',
    'resources/js/pages/humanfd.js'
  ])

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let currentTicketId = null;
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      // View Ticket Details
      document.querySelectorAll('.view-ticket').forEach(btn => {
        btn.addEventListener('click', async function() {
          const ticketId = this.dataset.id;
          currentTicketId = ticketId;
          
          try {
            const response = await fetch(`/customerservices/tickets/${ticketId}`);
            const data = await response.json();
            
            if (data.success) {
              const ticket = data.ticket;
              displayTicketDetails(ticket);
              new bootstrap.Modal(document.getElementById('ticketViewModal')).show();
            }
          } catch (error) {
            console.error('Error loading ticket:', error);
            alert('{{ __('tickets.failed_load_ticket') }}');
          }
        });
      });

      // Display ticket details in modal
      function displayTicketDetails(ticket) {
        document.getElementById('ticketNumber').textContent = `Ticket ${ticket.ticket_number} — ${ticket.subject}`;
        document.getElementById('ticketStatusBadge').className = `badge ${getStatusBadgeClass(ticket.status)}`;
        document.getElementById('ticketStatusBadge').textContent = formatStatus(ticket.status);
        document.getElementById('ticketMeta').textContent = `Created: ${formatDate(ticket.created_at)}`;
        
        // Customer info
        document.getElementById('ticketCustomer').innerHTML = `
          <img src="/images/users/avatar-${(ticket.user_id % 10) + 1}.jpg" class="rounded-circle" style="height:28px;width:28px;" alt="">
          <div>
            <div class="fw-semibold">${ticket.user.name}</div>
            <div class="text-muted small">${ticket.user.email || ''}</div>
          </div>
        `;
        
        // Assignee info
        document.getElementById('ticketAssignee').innerHTML = ticket.assigned_to ? `
          <img src="/images/users/avatar-${(ticket.assigned_to % 10) + 1}.jpg" class="rounded-circle" style="height:28px;width:28px;" alt="">
          <div>
            <div class="fw-semibold">${ticket.assigned_to.name}</div>
            <div class="text-muted small">Support Agent</div>
          </div>
        ` : '<span class="text-muted">Unassigned</span>';
        
        document.getElementById('ticketCategory').textContent = formatCategory(ticket.category);
        document.getElementById('ticketPriority').innerHTML = `<span class="badge ${getPriorityBadgeClass(ticket.priority)}">${formatPriority(ticket.priority)}</span>`;
        document.getElementById('ticketUserType').textContent = formatUserType(ticket.user_type);
        document.getElementById('ticketStatus').innerHTML = `<span class="badge ${getStatusBadgeClass(ticket.status)}">${formatStatus(ticket.status)}</span>`;
        document.getElementById('ticketDescription').textContent = ticket.description;
        
        // Display messages
        const messagesHtml = ticket.messages.map(msg => `
          <div class="mb-3 pb-2 border-bottom">
            <div class="d-flex justify-content-between mb-1">
              <strong>${msg.sender.name}</strong>
              <small class="text-muted">${formatDate(msg.created_at)}</small>
            </div>
            <div>${msg.message}</div>
            ${msg.is_internal_note ? '<span class="badge bg-warning-subtle text-warning">Internal Note</span>' : ''}
          </div>
        `).join('');
        
        document.getElementById('ticketMessages').innerHTML = messagesHtml || '<p class="text-muted">No messages yet</p>';
      }

      // Send Reply
      document.getElementById('sendReplyBtn').addEventListener('click', async function() {
        const message = document.getElementById('replyMessage').value.trim();
        const isInternalNote = document.getElementById('isInternalNote').checked;
        
        if (!message) {
          alert('{{ __('tickets.enter_message') }}');
          return;
        }
        
        try {
          const response = await fetch(`/customerservices/tickets/${currentTicketId}/message`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message, is_internal_note: isInternalNote })
          });
          
          const data = await response.json();
          
          if (data.success) {
            document.getElementById('replyMessage').value = '';
            document.getElementById('isInternalNote').checked = false;
            
            // Reload ticket details
            const ticketResponse = await fetch(`/customerservices/tickets/${currentTicketId}`);
            const ticketData = await ticketResponse.json();
            if (ticketData.success) {
              displayTicketDetails(ticketData.ticket);
            }
            
            alert('{{ __('tickets.reply_sent_success') }}');
          } else {
            alert('{{ __('tickets.failed_send_reply') }}');
          }
        } catch (error) {
          console.error('Error sending reply:', error);
          alert('{{ __('tickets.failed_send_reply') }}');
        }
      });

      // Update Status
      document.getElementById('updateStatus').addEventListener('change', async function() {
        const newStatus = this.value;
        if (!newStatus) return;
        
        try {
          const response = await fetch(`/customerservices/tickets/${currentTicketId}/status`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: newStatus })
          });
          
          const data = await response.json();
          
          if (data.success) {
            alert('{{ __('tickets.status_updated_success') }}');
            location.reload();
          } else {
            alert('{{ __('tickets.failed_update_status') }}');
          }
        } catch (error) {
          console.error('Error updating status:', error);
          alert('{{ __('tickets.failed_update_status') }}');
        }
      });

      // Assign Ticket
      document.querySelectorAll('.assign-ticket').forEach(btn => {
        btn.addEventListener('click', function() {
          currentTicketId = this.dataset.id;
          new bootstrap.Modal(document.getElementById('assignModal')).show();
        });
      });

      document.getElementById('confirmAssignBtn').addEventListener('click', async function() {
        const assignedTo = document.getElementById('assign_to').value;
        
        try {
          const response = await fetch(`/customerservices/tickets/${currentTicketId}/assign`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ assigned_to: assignedTo || null })
          });
          
          const data = await response.json();
          
          if (data.success) {
            alert('{{ __('tickets.ticket_assigned_success') }}');
            location.reload();
          } else {
            alert('{{ __('tickets.failed_assign_ticket') }}');
          }
        } catch (error) {
          console.error('Error assigning ticket:', error);
          alert('{{ __('tickets.failed_assign_ticket') }}');
        }
      });

      // Helper functions
      function getStatusBadgeClass(status) {
        const classes = {
          'open': 'bg-primary-subtle text-primary',
          'in_progress': 'bg-info-subtle text-info',
          'waiting_customer': 'bg-warning-subtle text-warning',
          'resolved': 'bg-success-subtle text-success',
          'closed': 'bg-secondary-subtle text-secondary'
        };
        return classes[status] || 'bg-light text-dark';
      }

      function getPriorityBadgeClass(priority) {
        const classes = {
          'low': 'bg-info-subtle text-info',
          'medium': 'bg-primary-subtle text-primary',
          'high': 'bg-warning-subtle text-warning',
          'urgent': 'bg-danger-subtle text-danger'
        };
        return classes[priority] || 'bg-light text-dark';
      }

      function formatStatus(status) {
        return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
      }

      function formatPriority(priority) {
        return priority.charAt(0).toUpperCase() + priority.slice(1);
      }

      function formatCategory(category) {
        return category.charAt(0).toUpperCase() + category.slice(1);
      }

      function formatUserType(userType) {
        return userType.charAt(0).toUpperCase() + userType.slice(1);
      }

      function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('en-GB', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      }

      // ============ FILTER AND SEARCH FUNCTIONALITY ============
      
      // Apply Filters Button
      document.getElementById('applyFiltersBtn').addEventListener('click', function() {
        applyFilters();
      });

      // Search on Enter key
      document.getElementById('t_q').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          applyFilters();
        }
      });

      // Status pills filter
      document.querySelectorAll('#ticketStatusPills .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const status = this.dataset.status;
          
          // Update active state
          document.querySelectorAll('#ticketStatusPills .nav-link').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
          
          // Apply filter with this status
          applyFilters(status);
        });
      });

      // Function to build and navigate to filter URL
      function applyFilters(statusOverride = null) {
        const params = new URLSearchParams();
        
        // Search
        const search = document.getElementById('t_q').value.trim();
        if (search) params.append('search', search);
        
        // Date
        const date = document.getElementById('humanfd-datepicker').value;
        if (date) params.append('date', date);
        
        // Category
        const category = document.getElementById('t_category').value;
        if (category) params.append('category', category);
        
        // Priority
        const priority = document.getElementById('t_priority').value;
        if (priority) params.append('priority', priority);
        
        // Assignee
        const assignee = document.getElementById('t_assignee').value;
        if (assignee) params.append('assigned_to', assignee);
        
        // Status (use override if provided, otherwise from active pill)
        let status = statusOverride !== null ? statusOverride : null;
        if (status === null) {
          const activePill = document.querySelector('#ticketStatusPills .nav-link.active');
          if (activePill) {
            status = activePill.dataset.status;
          }
        }
        if (status) params.append('status', status);
        
        // Navigate to URL with filters
        const url = '{{ route("customerservices.tickets") }}' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
      }
    });
  </script>
@endsection