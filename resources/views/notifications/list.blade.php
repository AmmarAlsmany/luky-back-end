@extends('layouts.vertical', ['title' => __('notifications.notifications')])
@section('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
@endsection

@section('content')

<div class="row g-4">
  <!-- ===== Portion 1: Compose & Send ===== -->
  <div class="col-12 col-xl-12">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title d-flex align-items-center gap-2 mb-0">
          <iconify-icon icon="solar:bell-bing-bold-duotone" class="text-primary fs-20"></iconify-icon>
          {{ __('notifications.send_notification') }}
        </h4>
        <small class="text-muted">{{ __('notifications.sent') }}: {{ $stats['total_sent'] }} | {{ __('notifications.failed') }}: {{ $stats['total_failed'] }} | {{ __('notifications.today') }}: {{ $stats['today_sent'] }}</small>
      </div>

      <div class="card-body">
        <form id="sendNotificationForm">
          @csrf
          <div class="row g-3">
            <!-- Send To: Client / Provider -->
            <div class="col-md-6">
              <label for="notify_audience" class="form-label">{{ __('notifications.send_to') }}</label>
              <select id="notify_audience" name="audience" class="form-control" required>
                <option value="client" selected>{{ __('notifications.clients') }}</option>
                <option value="provider">{{ __('notifications.providers') }}</option>
              </select>
            </div>

            <!-- Recipient select with search -->
            <div class="col-md-6">
              <label for="notify_recipient" class="form-label">{{ __('notifications.select_recipient') }} <span class="text-danger">*</span></label>
              <select id="notify_recipient" name="recipient_id" class="form-control" required>
                @foreach($clients as $client)
                  <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
              </select>
              <small class="text-muted">{{ __('notifications.search_dropdown_hint') }}</small>
            </div>

            <!-- Title -->
            <div class="col-12">
              <label for="notify_title" class="form-label">{{ __('notifications.title_optional') }}</label>
              <input type="text" id="notify_title" name="title" class="form-control" placeholder="{{ __('notifications.message_placeholder') }}" maxlength="100">
            </div>

            <!-- Message -->
            <div class="col-12">
              <label for="notify_message" class="form-label">{{ __('notifications.message') }} <span class="text-danger">*</span></label>
              <textarea id="notify_message" name="message" class="form-control" rows="4" placeholder="{{ __('notifications.write_your_message') }}" required maxlength="500"></textarea>
              <small class="text-muted">{{ __('notifications.message_tip') }}</small>
            </div>

            <!-- Send Button -->
            <div class="col-12 pt-2">
              <button type="submit" id="notify_send_btn" class="btn btn-primary">
                <i class="mdi mdi-send me-1"></i> {{ __('notifications.send') }}
              </button>
              <span id="notify_send_state" class="ms-2 text-muted" style="display:none;">{{ __('notifications.sending') }}</span>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===== Portion 2: Sent Notifications List ===== -->
  <div class="col-12 col-xl-12">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title d-flex align-items-center gap-2 mb-0">
          <iconify-icon icon="solar:bell-list-bold-duotone" class="text-primary fs-20"></iconify-icon>
          {{ __('notifications.sent_notifications') }}
        </h4>

        <form method="GET" action="{{ route('notifications.index') }}" class="d-flex gap-2">
          <input type="search" name="search" class="form-control" placeholder="{{ __('notifications.search') }}" value="{{ $filters['search'] ?? '' }}" aria-label="Search">
          <select name="status" class="form-select" style="width: 150px;">
            <option value="">{{ __('notifications.all_status') }}</option>
            <option value="sent" {{ ($filters['status'] ?? '') == 'sent' ? 'selected' : '' }}>{{ __('notifications.status_sent') }}</option>
            <option value="failed" {{ ($filters['status'] ?? '') == 'failed' ? 'selected' : '' }}>{{ __('notifications.status_failed') }}</option>
          </select>
          <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant"></i></button>
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0 table-hover table-centered">
            <thead class="bg-light-subtle">
              <tr>
                <th style="width: 80px;">{{ __('notifications.id') }}</th>
                <th>{{ __('notifications.message_col') }}</th>
                <th>{{ __('notifications.date_time') }}</th>
                <th>{{ __('notifications.name') }}</th>
                <th>{{ __('notifications.type') }}</th>
                <th>{{ __('notifications.status') }}</th>
              </tr>
            </thead>
            <tbody id="notify_table_body">
              @forelse($notificationsData as $notif)
              <tr>
                <td>{{ $notif['id'] }}</td>
                <td>
                  <span class="text-truncate d-inline-block" style="max-width:240px;" title="{{ $notif['full_message'] }}">
                    {{ $notif['message'] }}
                  </span>
                </td>
                <td>{{ $notif['datetime'] }}</td>
                <td>{{ $notif['recipient_name'] }}</td>
                <td>
                  @if($notif['recipient_type'] == 'Client')
                    <span class="badge bg-info-subtle text-info">{{ __('notifications.client') }}</span>
                  @elseif($notif['recipient_type'] == 'Provider')
                    <span class="badge bg-warning-subtle text-warning">{{ __('notifications.provider') }}</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary">{{ $notif['recipient_type'] }}</span>
                  @endif
                </td>
                <td>
                  @if($notif['is_sent'])
                    <span class="badge bg-success-subtle text-success">{{ __('notifications.status_sent') }}</span>
                  @else
                    <span class="badge bg-danger-subtle text-danger">{{ __('notifications.status_failed') }}</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <div class="text-muted">
                    <iconify-icon icon="solar:inbox-line-broken" class="fs-1 mb-2"></iconify-icon>
                    <p class="mb-0">{{ __('notifications.no_notifications_yet') }}</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer border-top">
        @if($notifications->hasPages())
        <nav aria-label="Notifications pagination">
          {{ $notifications->links('pagination::bootstrap-5') }}
        </nav>
        @else
        <div class="text-muted text-center py-2">
          {{ __('notifications.showing_notifications', ['count' => $notifications->total()]) }}
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('script-bottom')
  <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Wait a bit to ensure Choices is loaded
      if (typeof Choices === 'undefined') {
        console.error('Choices.js not loaded!');
        return;
      }

      const audienceEl = document.getElementById('notify_audience');
      const recipientEl = document.getElementById('notify_recipient');
      let audienceChoices;
      let recipientChoices;

      // Initialize audience Choices
      audienceChoices = new Choices(audienceEl, {
        allowHTML: false,
        shouldSort: false,
        searchEnabled: false,
        placeholder: true,
        searchPlaceholderValue: '{{ __('notifications.select') }}'
      });

      // Initialize recipient Choices
      function initRecipientChoices() {
        if (recipientChoices) recipientChoices.destroy();
        recipientChoices = new Choices(recipientEl, {
          allowHTML: false,
          shouldSort: true,
          searchEnabled: true,
          placeholder: true,
          searchPlaceholderValue: '{{ __('notifications.search_name') }}'
        });
      }
      initRecipientChoices();

      // Load recipients when audience changes
      function loadRecipients(audience) {
        console.log('Loading recipients for:', audience);
        fetch(`/notifications/users/${audience}`)
          .then(response => response.json())
          .then(data => {
            console.log('Received data:', data);
            if (data.success) {
              recipientChoices.clearStore();
              recipientChoices.setChoices(
                data.data.map(d => ({ value: d.value, label: d.label, selected: false })),
                'value',
                'label',
                true
              );
              console.log('Loaded', data.data.length, 'recipients');
            } else {
              console.error('API returned success=false:', data);
            }
          })
          .catch(error => {
            console.error('Error loading recipients:', error);
          });
      }

      // Audience change handler
      audienceEl.addEventListener('change', function () {
        loadRecipients(this.value);
      });

      // Load initial recipients (clients by default)
      loadRecipients('client');

      // Handle form submission
      const form = document.getElementById('sendNotificationForm');
      const sendBtn = document.getElementById('notify_send_btn');
      const sendState = document.getElementById('notify_send_state');

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        if (!data.message || !data.recipient_id) {
          Swal.fire('{{ __('notifications.error') }}', '{{ __('notifications.fill_required_fields') }}', 'error');
          return;
        }

        // Show loading
        sendBtn.disabled = true;
        sendState.style.display = 'inline';

        fetch('{{ route('notifications.send') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
          sendBtn.disabled = false;
          sendState.style.display = 'none';

          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: '{{ __('notifications.success') }}',
              text: data.message,
              timer: 2000
            });

            // Add to table BEFORE clearing form
            const tbody = document.getElementById('notify_table_body');
            const row = `
              <tr>
                <td>${data.data.formatted_id}</td>
                <td>
                  <span class="text-truncate d-inline-block" style="max-width:240px;" title="${data.data.full_message}">
                    ${data.data.message_preview}
                  </span>
                </td>
                <td>${data.data.datetime}</td>
                <td>${data.data.recipient_name}</td>
                <td><span class="badge bg-${data.data.recipient_type === 'Provider' ? 'warning' : 'info'}-subtle text-${data.data.recipient_type === 'Provider' ? 'warning' : 'info'}">${data.data.recipient_type}</span></td>
                <td><span class="badge bg-${data.data.status === 'Sent' ? 'success' : 'danger'}-subtle text-${data.data.status === 'Sent' ? 'success' : 'danger'}">${data.data.status}</span></td>
              </tr>
            `;
            tbody.insertAdjacentHTML('afterbegin', row);

            // Remove "no notifications" message if exists
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) {
              emptyRow.parentElement.remove();
            }

            // Clear form AFTER adding to table
            document.getElementById('notify_message').value = '';
            document.getElementById('notify_title').value = '';
          } else {
            Swal.fire('{{ __('notifications.error') }}', data.message || '{{ __('notifications.failed_to_send') }}', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          sendBtn.disabled = false;
          sendState.style.display = 'none';
          Swal.fire('{{ __('notifications.error') }}', '{{ __('notifications.error_occurred') }}', 'error');
        });
      });
    });
  </script>
@endsection
