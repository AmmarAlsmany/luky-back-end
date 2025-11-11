<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">{{ $title ?? 'Luky' }}</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">

                <!-- Theme Color (Light/Dark) -->
                <!-- <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div> -->

                <!-- Notification -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative"
                            id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        @if(($unreadNotificationCount ?? 0) > 0)
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            <span class="visually-hidden">{{ __('common.unread_notifications') }}</span>
                        </span>
                        @endif
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end"
                         aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> {{ __('common.notifications') }}</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline" id="mark-all-read">
                                        <small>{{ __('common.mark_all_read') }}</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
                            @forelse($adminNotifications ?? [] as $notification)
                            <!-- Item -->
                            <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom text-wrap notification-item" data-notification-id="{{ $notification->id }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                                @if($notification->type == 'booking_request')
                                                    <iconify-icon icon="solar:calendar-mark-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'new_review')
                                                    <iconify-icon icon="solar:star-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'provider_registration')
                                                    <iconify-icon icon="solar:user-plus-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'new_client')
                                                    <iconify-icon icon="solar:user-check-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'new_admin_user')
                                                    <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'booking_cancelled')
                                                    <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
                                                @elseif(in_array($notification->type, ['new_payment', 'payment_completed']))
                                                    <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'payment_failed')
                                                    <iconify-icon icon="solar:card-remove-bold-duotone"></iconify-icon>
                                                @elseif($notification->type == 'payment_refunded')
                                                    <iconify-icon icon="solar:card-recive-bold-duotone"></iconify-icon>
                                                @else
                                                    <iconify-icon icon="solar:bell-bold-duotone"></iconify-icon>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">{{ $notification->title }}</p>
                                        <p class="mb-0 text-muted small">{{ Str::limit($notification->body, 60) }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="text-center py-4">
                                <iconify-icon icon="solar:inbox-line-broken" class="fs-1 text-muted"></iconify-icon>
                                <p class="text-muted mb-0">{{ __('common.no_new_notifications') }}</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="text-center py-3">
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm">{{ __('common.view_all_notifications') }} <i
                                    class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Theme Setting -->
                <!-- <div class="topbar-item d-none d-md-flex">
                    <button type="button" class="topbar-button" id="theme-settings-btn" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                        <iconify-icon icon="solar:settings-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

            
                <div class="topbar-item d-none d-md-flex">
                    <button type="button" class="topbar-button" id="theme-settings-btn" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-activity-offcanvas" aria-controls="theme-settings-offcanvas">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div> -->

                <!-- Language Switcher -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="language-dropdown" data-bs-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-globe fs-22 align-middle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">{{ __('common.select_language') }}</h6>
                        <a href="{{ route('language.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active bg-light' : '' }}">
                            <span class="align-middle">🇬🇧 English</span>
                            @if(app()->getLocale() == 'en') <i class="bx bx-check float-end"></i> @endif
                        </a>
                        <a href="{{ route('language.switch', 'ar') }}" class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active bg-light' : '' }}">
                            <span class="align-middle">🇸🇦 العربية</span>
                            @if(app()->getLocale() == 'ar') <i class="bx bx-check float-end"></i> @endif
                        </a>
                    </div>
                </div>

                <!-- User -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                              <span class="d-flex align-items-center">
                                   <img class="rounded-circle" width="32" src="{{ auth()->user()->avatar_url }}"
                                        alt="avatar" loading="lazy">
                              </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">{{ __('common.welcome_back') }} {{ Auth::user()->name ?? 'Admin' }}!</h6>
                        
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bx bx-key fs-18 align-middle me-1"></i><span class="align-middle">{{ __('common.change_password') }}</span>
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-log-out fs-18 align-middle me-1"></i><span class="align-middle">{{ __('common.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- App Search-->
                <!-- <form class="app-search d-none d-md-block ms-2">
                    <div class="position-relative">
                        <input type="search" class="form-control" placeholder="Search..." autocomplete="off" value="">
                        <iconify-icon icon="solar:magnifer-linear" class="search-widget-icon"></iconify-icon>
                    </div>
                </form> -->
            </div>
        </div>
    </div>
</header>

<!-- Activity Timeline - DISABLED (Dummy Content) -->
{{-- 
<div>
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-activity-offcanvas"
         style="max-width: 450px; width: 100%;">
        <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
            <h5 class="text-white m-0 fw-semibold">Activity Stream</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100 p-4">
                <div class="position-relative ms-2">
                    <span class="position-absolute start-0  top-0 border border-dashed h-100"></span>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 avatar-sm translate-middle-x bg-danger d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                    icon="iconamoon:folder-check-duotone"></iconify-icon></span>
                            <div class="ms-2">
                                <h5 class="mb-1 text-dark fw-semibold fs-15 lh-base">Report-Fix / Update </h5>
                                <p class="d-flex align-items-center">Add 3 files to <span
                                        class=" d-flex align-items-center text-primary ms-1"><iconify-icon
                                            icon="iconamoon:file-light"></iconify-icon> Tasks</span></p>
                                <div class="bg-light bg-opacity-50 rounded-2 p-2">
                                    <div class="row">
                                        <div class="col-lg-6 border-end border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bx bxl-figma fs-20 text-red"></i>
                                                <a href="#!" class="text-dark fw-medium">Concept.fig</a>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bx bxl-file-doc fs-20 text-success"></i>
                                                <a href="#!" class="text-dark fw-medium">larkon.docs</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="mt-2 text-muted">Monday , 4:24 PM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 avatar-sm translate-middle-x bg-success d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                    icon="iconamoon:check-circle-1-duotone"></iconify-icon></span>
                            <div class="ms-2">
                                <h5 class="mb-1 text-dark fw-semibold fs-15 lh-base">Project Status
                                </h5>
                                <p class="d-flex align-items-center mb-0">Marked<span
                                        class=" d-flex align-items-center text-primary mx-1"><iconify-icon
                                            icon="iconamoon:file-light"></iconify-icon> Design </span> as <span
                                        class="badge bg-success-subtle text-success px-2 py-1 ms-1"> Completed</span>
                                </p>
                                <div class="d-flex align-items-center gap-3 mt-1 bg-light bg-opacity-50 p-2 rounded-2">
                                    <a href="#!" class="fw-medium text-dark">UI/UX Figma Design</a>
                                    <div class="ms-auto">
                                        <a href="#!" class="fw-medium text-primary fs-18" data-bs-toggle="tooltip"
                                           data-bs-title="Download" data-bs-placement="bottom">
                                            <iconify-icon icon="iconamoon:cloud-download-duotone"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                                <h6 class="mt-3 text-muted">Monday , 3:00 PM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 avatar-sm translate-middle-x bg-primary d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-16">UI</span>
                            <div class="ms-2">
                                <h5 class="mb-1 text-dark fw-semibold fs-15">Larkon Application UI v2.0.0 <span
                                        class="badge bg-primary-subtle text-primary px-2 py-1 ms-1"> Latest</span>
                                </h5>
                                <p>Get access to over 20+ pages including a dashboard layout, charts, kanban board,
                                    calendar, and pre-order E-commerce & Marketing pages.</p>
                                <div class="mt-2">
                                    <a href="#!" class="btn btn-light btn-sm">Download Zip</a>
                                </div>
                                <h6 class="mt-3 text-muted">Monday , 2:10 PM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 translate-middle-x bg-success bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><img
                                    src="/images/users/avatar-7.jpg" alt="avatar-5"
                                    class="avatar-sm rounded-circle"></span>
                            <div class="ms-2">
                                <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Alex Smith Attached Photos
                                </h5>
                                <div class="row g-2 mt-2">
                                    <div class="col-lg-4">
                                        <a href="#!">
                                            <img src="/images/small/img-6.jpg" alt="" class="img-fluid rounded">
                                        </a>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="#!">
                                            <img src="/images/small/img-3.jpg" alt="" class="img-fluid rounded">
                                        </a>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="#!">
                                            <img src="/images/small/img-4.jpg" alt="" class="img-fluid rounded">
                                        </a>
                                    </div>
                                </div>
                                <h6 class="mt-3 text-muted">Monday 1:00 PM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 translate-middle-x bg-success bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><img
                                    src="/images/users/avatar-6.jpg" alt="avatar-5"
                                    class="avatar-sm rounded-circle"></span>
                            <div class="ms-2">
                                <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Rebecca J. added a new team member
                                </h5>
                                <p class="d-flex align-items-center gap-1">
                                    <iconify-icon icon="iconamoon:check-circle-1-duotone"
                                                  class="text-success"></iconify-icon>
                                    Added a new member to Front Dashboard
                                </p>
                                <h6 class="mt-3 text-muted">Monday 10:00 AM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative ps-4">
                        <div class="mb-4">
                            <span
                                class="position-absolute start-0 avatar-sm translate-middle-x bg-warning d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                    icon="iconamoon:certificate-badge-duotone"></iconify-icon></span>
                            <div class="ms-2">
                                <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Achievements
                                </h5>
                                <p class="d-flex align-items-center gap-1 mt-1">Earned a
                                    <iconify-icon icon="iconamoon:certificate-badge-duotone"
                                                  class="text-danger fs-20"></iconify-icon>
                                    " Best Product Award"
                                </p>
                                <h6 class="mt-3 text-muted">Monday 9:30 AM</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#!" class="btn btn-outline-dark w-100">View All</a>
            </div>
        </div>
    </div>
</div>
--}}

<script>
// Mark notification as read when clicked
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notification script loaded');
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found! Make sure <meta name="csrf-token"> is in the layout.');
        return;
    }
    console.log('CSRF token found:', csrfToken.content.substring(0, 10) + '...');
    
    // Individual notification click
    const notificationItems = document.querySelectorAll('.notification-item');
    console.log('Found', notificationItems.length, 'notification items');
    
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const notificationId = this.dataset.notificationId;
            console.log('Clicked notification ID:', notificationId);
            
            if (!notificationId) {
                console.error('No notification ID found');
                return;
            }
            
            // Send AJAX request to mark as read
            console.log('Sending mark-as-read request for notification:', notificationId);
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (response.ok) {
                    console.log('Notification marked as read successfully');
                    
                    // Update badge count
                    const badge = document.querySelector('.topbar-badge');
                    if (badge) {
                        let count = parseInt(badge.textContent);
                        count = Math.max(0, count - 1);
                        if (count === 0) {
                            badge.remove();
                        } else {
                            badge.textContent = count > 99 ? '99+' : count;
                        }
                    }
                    // Fade out notification
                    this.style.opacity = '0.5';
                } else {
                    return response.json().then(data => {
                        console.error('Failed to mark as read:', data);
                    });
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        });
    });

    // Mark all as read
    const markAllBtn = document.getElementById('mark-all-read');
    console.log('Mark all button:', markAllBtn ? 'found' : 'NOT FOUND');
    
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Mark all as read clicked');
            
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            })
            .then(response => {
                console.log('Mark all response status:', response.status);
                if (response.ok) {
                    console.log('All notifications marked as read');
                    // Remove badge
                    const badge = document.querySelector('.topbar-badge');
                    if (badge) badge.remove();
                    
                    // Reload notifications
                    location.reload();
                } else {
                    return response.json().then(data => {
                        console.error('Failed to mark all as read:', data);
                    });
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        });
    }
});
</script>
