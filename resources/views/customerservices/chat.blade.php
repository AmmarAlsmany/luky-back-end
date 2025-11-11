@extends('layouts.vertical', ['title' => __('chat.chat')])

@section('css')
@vite(['node_modules/swiper/swiper-bundle.min.css'])
@endsection

@section('content')

<div class="row g-1">
    <div class="col-xxl-3">
        <div class="offcanvas-xxl offcanvas-start h-100 " tabindex="-1" id="Contactoffcanvas" aria-labelledby="ContactoffcanvasLabel">
            <div class="card position-relative overflow-hidden ">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('chat.chat') }}</h4>
                    <a href="#user-setting" class="fs-18" type="button" data-bs-toggle="offcanvas" aria-haspopup="true" aria-expanded="true">
                        <i class="bx bx-cog"></i>
                    </a>
                </div>

                <form class="chat-search px-3" action="{{ route('customerservices.chat') }}" method="GET">
                    <div class="chat-search-box">
                        <input class="form-control" type="text" name="search" placeholder="{{ __('chat.search_placeholder') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-link search-icon p-0"><i class="bx bx-search-alt"></i></button>
                    </div>
                </form>


                <ul class="nav nav-tabs nav-justified nav-bordered border-top mt-2">
                    <li class="nav-item">
                        <a href="{{ route('customerservices.chat') }}" class="nav-link {{ !request('user_type') || request('user_type') === 'all' ? 'active' : '' }} py-2">
                           {{ __('chat.all') }} 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customerservices.chat', ['user_type' => 'provider']) }}" class="nav-link {{ request('user_type') === 'provider' ? 'active' : '' }} py-2">
                           {{ __('chat.providers') }} 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customerservices.chat', ['user_type' => 'client']) }}" class="nav-link {{ request('user_type') === 'client' ? 'active' : '' }} py-2">
                            {{ __('chat.clients') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="conversation-list">

                        <div class="px-3 mb-3 admin-chat-setting-height" data-simplebar style="max-height: 60vh; overflow-y: auto;">
                            @if(request('user_type') === 'provider')
                                @foreach(($providers ?? []) as $provider)
                                    @php $conv = ($providerConversations[$provider->id] ?? null); @endphp
                                    <a href="javascript:void(0);" class="text-body provider-item" data-provider-id="{{ $provider->id }}" data-conversation-id="{{ $conv->id ?? '' }}">
                                        <div class="d-flex align-items-center p-2">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="my-0">
                                                    <span class="float-end text-muted fs-13">{{ $conv ? $conv->last_message_at?->diffForHumans() : __('chat.no_chats') }}</span>
                                                    {{ $provider->business_name }}
                                                </h5>
                                                <p class="mt-1 mb-0 fs-13 text-muted d-flex align-items-end justify-content-between">
                                                    <span class="w-75">{{ $conv && $conv->lastMessage ? \Illuminate\Support\Str::limit($conv->lastMessage->content, 40) : '—' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @elseif(request('user_type') === 'client')
                                @foreach(($clients ?? []) as $client)
                                    @php $conv = ($clientConversations[$client->id] ?? null); @endphp
                                    <a href="javascript:void(0);" class="text-body client-item" data-client-id="{{ $client->id }}" data-conversation-id="{{ $conv->id ?? '' }}">
                                        <div class="d-flex align-items-center p-2">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="my-0">
                                                    <span class="float-end text-muted fs-13">{{ $conv ? $conv->last_message_at?->diffForHumans() : __('chat.no_chats') }}</span>
                                                    {{ $client->name }}
                                                </h5>
                                                <p class="mt-1 mb-0 fs-13 text-muted d-flex align-items-end justify-content-between">
                                                    <span class="w-75">{{ $conv && $conv->lastMessage ? \Illuminate\Support\Str::limit($conv->lastMessage->content, 40) : '—' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                @forelse($conversations as $conversation)
                                    @php
                                        $lastMsg = $conversation->lastMessage;
                                        $activeType = request('user_type');
                                        $userName = $activeType === 'provider'
                                            ? ($conversation->provider->business_name ?? 'Provider')
                                            : ($activeType === 'client'
                                                ? ($conversation->client->name ?? 'Client')
                                                : ($conversation->client->name ?? ($conversation->provider->business_name ?? 'User')));
                                    @endphp
                                    <a href="javascript:void(0);" class="text-body conversation-item" data-id="{{ $conversation->id }}" data-avatar="{{ $conversation->user->avatar_url }}">
                                        <div class="d-flex align-items-center p-2 {{ $loop->first ? 'bg-light bg-opacity-50 rounded-1' : '' }}">
                                            <div class="flex-shrink-0 position-relative">
                                                <img src="{{ $conversation->user->avatar_url }}" class="me-2 rounded-circle" height="36" alt="avatar" loading="lazy" />
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="my-0">
                                                    <span class="float-end text-muted fs-13">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : 'N/A' }}</span>
                                                    {{ $userName }}
                                                </h5>
                                                <p class="mt-1 mb-0 fs-13 text-muted d-flex align-items-end justify-content-between">
                                                    <span class="w-75">{{ $lastMsg ? \Illuminate\Support\Str::limit($lastMsg->content, 40) : __('chat.no_messages_yet') }}</span>
                                                    @if($lastMsg && $lastMsg->is_read)
                                                        <i class="bx bx-check-double text-success"></i>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-5">
                                        <iconify-icon icon="solar:chat-line-bold-duotone" class="fs-48 text-muted mb-2"></iconify-icon>
                                        <p class="text-muted">{{ __('chat.no_conversations_found') }}</p>
                                    </div>
                                @endforelse
                            @endif
                        </div>

                    </div>

                    @if($conversations->hasPages())
                        <div class="px-3 pb-3">
                            {{ $conversations->links() }}
                        </div>
                    @endif

                </div>

                <div class="offcanvas offcanvas-start position-absolute shadow w-100" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="user-setting" aria-labelledby="user-settingLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title text-truncate w-50" id="user-settingLabel">{{ __('chat.profile') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-0 h-100" data-simplebar>
                        <h4 class="page-title p-3 my-0">{{ __('chat.setting') }}</h4>

                        <div class="d-flex align-items-center px-3 pb-3 border-bottom">
                            <img src="{{ auth()->user()->avatar_url }}" class="me-2 rounded-circle" height="36" alt="avatar" loading="lazy" />
                            <div class="flex-grow-1">
                                <div class="float-end">
                                    <a href="javascript:void(0);"><i class="bx bx-qr-scan fs-20"></i></a>
                                </div>
                                <h5 class="my-0 fs-14">{{ auth()->user()->name }}</h5>
                                <p class="mt-1 mb-0 text-muted"><span class="w-75">{{ __('chat.system_administrator') }}</span></p>
                            </div>
                        </div>

                        <div class="px-3 my-3 app-chat-setting">
                            <div class="accordion custom-accordion" id="accordionSetting">
                                <div class="accordion-item border-0">
                                    <h5 class="accordion-header my-0" id="headingAccount">
                                        <button class="accordion-button px-0 pt-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccount" aria-expanded="false" aria-controls="collapseAccount">
                                            <span class="d-flex align-items-center">
                                                <i class="bx bx-key me-3 fs-20"></i>
                                                <span class="flex-grow-1">
                                                    <span class="fs-14 h5 mt-0 mb-1 d-block">{{ __('chat.account') }}</span>
                                                    <span class="mt-1 mb-0 text-muted w-75">{{ __('chat.account_description') }}</span>
                                                </span>
                                            </span>
                                        </button> <!-- end accordion button -->
                                    </h5> <!-- end accordion header -->
                                    <div id="collapseAccount" class="accordion-collapse collapse" aria-labelledby="headingAccount" data-bs-parent="#accordionSetting">
                                        <div class="accordion-body pb-0">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-lock-alt fs-18 me-2"></i>{{ __('chat.privacy') }}</a></li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-check-Larkon fs-18 me-2"></i>{{ __('chat.security') }}</a></li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-badge-check fs-18 me-2"></i>{{ __('chat.two_step_verification') }}</a></li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-arrow-from-left fs-18 me-2"></i>{{ __('chat.change_number') }}</a></li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-info-circle fs-18 me-2"></i>{{ __('chat.request_account_info') }}</a></li>
                                                <li><a href="javascript:void(0);"><i class="bx bx-trash fs-18 me-2"></i>{{ __('chat.delete_my_account') }}</a></li>
                                            </ul>
                                        </div> <!-- end accordion body -->
                                    </div> <!-- end accordion collapse -->
                                </div> <!-- end accordion-item -->
                                <div class="accordion-item border-0">
                                    <h5 class="accordion-header my-0" id="headingChats">
                                        <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChats" aria-expanded="false" aria-controls="collapseChats">
                                            <span class="d-flex align-items-center">
                                                <i class="bx bx-message-dots me-3 fs-20"></i>
                                                <span class="flex-grow-1">
                                                    <span class="fs-14 h5 mt-0 mb-1 d-block">{{ __('chat.chats') }}</span>
                                                    <span class="mt-1 mb-0 text-muted w-75">{{ __('chat.chats_description') }}</span>
                                                </span>
                                            </span>
                                        </button> <!-- end accordion button -->
                                    </h5> <!-- end accordion header -->
                                    <div id="collapseChats" class="accordion-collapse collapse" aria-labelledby="headingChats" data-bs-parent="#accordionSetting">
                                        <div class="accordion-body pb-0">
                                            <h5 class="mb-2">{{ __('chat.display') }}</h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2 d-flex">
                                                    <i class="bx bx-palette fs-18 me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <a href="javascript:void(0);">{{ __('chat.theme') }}</a>
                                                        <p class="mb-0 text-muted fs-12">{{ __('chat.system_default') }}</p>
                                                    </div>
                                                </li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-image fs-16 me-2"></i>{{ __('chat.wallpaper') }}</a></li>
                                            </ul>
                                            <hr>
                                            <h5>{{ __('chat.chat_setting') }}</h5>
                                            <ul class="list-unstyled">
                                                <li class="mb-2 ms-2">
                                                    <div class="float-end">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="media" checked>
                                                        </div>
                                                    </div>
                                                    <a href="javascript:void(0);">{{ __('chat.media_visibility') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.media_visibility_description') }}</p>
                                                </li>
                                                <li class="mb-2 ms-2">
                                                    <div class="float-end">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="enter">
                                                        </div>
                                                    </div>
                                                    <a href="javascript:void(0);">{{ __('chat.enter_is_send') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.enter_is_send_description') }}</p>
                                                </li>
                                                <li class="mb-2 ms-2">
                                                    <a href="javascript:void(0);">{{ __('chat.font_size') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.small') }}</p>
                                                </li>
                                            </ul>
                                            <hr>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <div class="d-flex">
                                                        <i class="bx bx-text fs-16 me-2"></i>
                                                        <div class="flex-grow-1">
                                                            <a href="javascript:void(0);">{{ __('chat.app_language') }}</a>
                                                            <p class="mb-0 text-muted fs-12">{{ __('chat.english') }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-cloud-upload fs-16 me-2"></i>{{ __('chat.chat_backup') }}</a></li>
                                                <li><a href="javascript:void(0);"><i class="bx bx-history fs-16 me-2"></i>{{ __('chat.chat_history') }}</a></li>
                                            </ul>
                                        </div> <!-- end accordion body -->
                                    </div> <!-- end accordion collapse -->
                                </div> <!-- end accordion-item -->
                                <div class="accordion-item border-0">
                                    <h5 class="accordion-header my-0" id="headingNotification">
                                        <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotification" aria-expanded="false" aria-controls="collapseNotification">
                                            <span class="d-flex align-items-center">
                                                <i class="bx bx-bell me-3 fs-20"></i>
                                                <span class="flex-grow-1">
                                                    <span class="fs-14 h5 mt-0 mb-1 d-block">{{ __('chat.notification') }}</span>
                                                    <span class="mt-1 mb-0 text-muted w-75">{{ __('chat.notification_description') }}</span>
                                                </span>
                                            </span>
                                        </button> <!-- end accordion button -->
                                    </h5> <!-- end accordion header -->
                                    <div id="collapseNotification" class="accordion-collapse collapse" aria-labelledby="headingNotification" data-bs-parent="#accordionSetting">
                                        <div class="accordion-body pb-0">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <div class="float-end">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="conversation" checked>
                                                        </div>
                                                    </div>
                                                    <a href="javascript:void(0);">{{ __('chat.conversation_tones') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.conversation_tones_description') }}</p>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h5>{{ __('chat.messages') }}</h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">Notification Tone</a>
                                                    <p class="mb-0 text-muted fs-12">Default ringtone</p>
                                                </li>
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">Vibrate</a>
                                                    <p class="mb-0 text-muted fs-12">Default</p>
                                                </li>
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">{{ __('chat.light') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.white') }}</p>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h5>{{ __('chat.groups') }}</h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">Notification Tone</a>
                                                    <p class="mb-0 text-muted fs-12">Default ringtone</p>
                                                </li>
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">{{ __('chat.vibrate') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.off') }}</p>
                                                </li>
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">{{ __('chat.light') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.dark') }}</p>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h5>{{ __('chat.calls') }}</h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">{{ __('chat.ringtone') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.default_ringtone') }}</p>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);">{{ __('chat.vibrate') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.default') }}</p>
                                                </li>
                                            </ul>
                                        </div> <!-- end accordion body -->
                                    </div> <!-- end accordion collapse -->
                                </div> <!-- end accordion-item -->
                                <div class="accordion-item border-0">
                                    <h5 class="accordion-header my-0" id="headingStorage">
                                        <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStorage" aria-expanded="false" aria-controls="collapseStorage">
                                            <span class="d-flex align-items-center">
                                                <i class="bx bx-history me-3 fs-20"></i>
                                                <span class="flex-grow-1">
                                                    <span class="fs-14 h5 mt-0 mb-1 d-block">{{ __('chat.storage_data') }}</span>
                                                    <span class="mt-1 mb-0 text-muted w-75">{{ __('chat.storage_description') }}</span>
                                                </span>
                                            </span>
                                        </button> <!-- end accordion button -->
                                    </h5> <!-- end accordion header -->
                                    <div id="collapseStorage" class="accordion-collapse collapse" aria-labelledby="headingStorage" data-bs-parent="#accordionSetting">
                                        <div class="accordion-body pb-0">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex">
                                                    <i class="bx bx-folder fs-16 me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <a href="javascript:void(0);">{{ __('chat.manage_storage') }}</a>
                                                        <p class="mb-0 text-muted fs-12">{{ __('chat.storage_size') }}</p>
                                                    </div>
                                                </li>
                                            </ul>
                                            <hr>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex">
                                                    <i class="bx bx-wifi fs-16 me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <a href="javascript:void(0);">{{ __('chat.network_usage') }}</a>
                                                        <p class="mb-0 text-muted fs-12">7.2 GB sent - 13.8 GB received</p>
                                                    </div>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h5 class="mb-0">{{ __('chat.media_auto_download') }}</h5>
                                            <p class="mb-0 text-muted fs-12">{{ __('chat.voice_message_note') }}</p>
                                            <ul class="list-unstyled mb-0 mt-2">
                                                <li class="mb-2">
                                                    <a href="javascript:void(0);">{{ __('chat.when_using_mobile_data') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.no_media') }}</p>
                                                </li>
                                                <li class="mb-2 ms-2">
                                                    <a href="javascript:void(0);">{{ __('chat.when_connected_wifi') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.no_media') }}</p>
                                                </li>
                                                <li class="mb-2 ms-2">
                                                    <a href="javascript:void(0);">{{ __('chat.when_roaming') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.no_media') }}</p>
                                                </li>
                                            </ul>
                                            <hr>
                                            <h5 class="mb-0">{{ __('chat.media_upload_quality') }}</h5>
                                            <p class="mb-0 text-muted fs-12">{{ __('chat.quality_description') }}</p>
                                            <ul class="list-unstyled mb-0 mt-2">
                                                <li class="ms-2">
                                                    <a href="javascript:void(0);">{{ __('chat.photo_upload_quality') }}</a>
                                                    <p class="mb-0 text-muted fs-12">{{ __('chat.auto_recommended') }}</p>
                                                </li>
                                            </ul>
                                        </div> <!-- end accordion body -->
                                    </div> <!-- end accordion collapse -->
                                </div> <!-- end accordion-item -->
                                <div class="accordion-item border-0">
                                    <h5 class="accordion-header my-0" id="headingHelp">
                                        <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHelp" aria-expanded="false" aria-controls="collapseHelp">
                                            <span class="d-flex align-items-center">
                                                <i class="bx bx-info-circle me-3 fs-20"></i>
                                                <span class="flex-grow-1">
                                                    <span class="fs-14 h5 mt-0 mb-1 d-block">{{ __('chat.help') }}</span>
                                                    <span class="mt-1 mb-0 text-muted w-75">{{ __('chat.help_description') }}</span>
                                                </span>
                                            </span>
                                        </button> <!-- end accordion button -->
                                    </h5> <!-- end accordion header -->
                                    <div id="collapseHelp" class="accordion-collapse collapse" aria-labelledby="headingHelp" data-bs-parent="#accordionSetting">
                                        <div class="accordion-body pb-0">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-info-circle fs-16 me-2"></i>{{ __('chat.help_center') }}</a></li>
                                                <li class="mb-2 d-flex">
                                                    <i class="bx bxs-contact fs-16 me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <a href="javascript:void(0);">{{ __('chat.contact_us') }}</a>
                                                        <p class="mb-0 text-muted fs-12">{{ __('chat.questions') }}</p>
                                                    </div>
                                                </li>
                                                <li class="mb-2"><a href="javascript:void(0);"><i class="bx bx-book-content fs-16 me-2"></i>{{ __('chat.terms_privacy_policy') }}</a></li>
                                                <li><a href="javascript:void(0);"><i class="bx bx-info-circle fs-16 me-2"></i>{{ __('chat.app_info') }}</a></li>
                                            </ul>
                                        </div> <!-- end accordion body -->
                                    </div> <!-- end accordion collapse -->
                                </div> <!-- end accordion-item -->
                            </div>
                        </div>

                    </div>
                </div>

            </div> <!-- end card -->
        </div>
    </div> <!-- end col -->

    <div class="col-xxl-9">
        <div class="card position-relative overflow-hidden">

            <div class="card-header d-flex align-items-center mh-100">
                <button class="btn btn-light d-xxl-none d-flex align-items-center px-2 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#Contactoffcanvas" aria-controls="Contactoffcanvas">
                    <i class="bx bx-menu fs-18"></i>
                </button>

                <div class="d-flex align-items-center" id="chat-header">
                    <img id="chatHeaderAvatar" src="/images/default-avatar-male.svg" class="me-2 rounded" height="36" alt="avatar" loading="lazy" />
                    <div class="d-none d-md-flex flex-column">
                        <h5 class="my-0 fs-16 fw-semibold" id="chatHeaderName">{{ __('chat.select_conversation') }}</h5>
                        <p class="mb-0 text-success fw-semibold fst-italic d-none" id="chatTypingIndicator">{{ __('chat.typing') }}</p>
                    </div>
                </div>

            </div>

            <div class="chat-box">
                <ul class="chat-conversation-list list-unstyled p-3 admin-chatbox-height" data-simplebar>

              <li id="chat-placeholder">
                <div class="d-flex align-items-center justify-content-center admin-chatbox-height text-center p-4">
                  <div>
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light" style="width:75px;height:75px;">
                      <iconify-icon icon="solar:chat-round-call-bold" style="font-size:54px; line-height:1;"></iconify-icon>
                    </div>
                    <h5 class="mb-1">{{ __('chat.select_user_to_chat') }}</h5>
                    <p class="text-muted mb-0">{{ __('chat.choose_user_instruction') }}</p>
                  </div>
                </div>
              </li>
                </ul> <!-- end chat-conversation-list -->
                <div class="bg-light bg-opacity-50 p-2">
                    <form class="needs-validation" name="chat-form" id="chat-form">
                        <div class="row align-items-center">
                            <div class="col mb-2 mb-sm-0 d-flex">
                                <div class="input-group">
                                    <a href="javascript: void(0);" class="btn btn-sm btn-light d-flex align-items-center input-group-text"><i class="bx bx-message fs-18"></i></a>
                                    <input type="text" class="form-control border-0" name="message" placeholder="{{ __('chat.enter_message') }}">
                                </div>
                            </div>
                            <div class="col-sm-auto">
                                <div class="">
                                    <div class="btn-group btn-toolbar">

                                        <button type="submit" class="btn btn-sm btn-primary chat-send"><i class="bx bx-send fs-18"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

     



        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->

@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-chat.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    const listType = '{{ request('user_type') ?? 'all' }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const chatList = document.querySelector('.chat-conversation-list');
    const chatPlaceholder = document.getElementById('chat-placeholder');
    const chatForm = document.getElementById('chat-form');
    const messageInput = chatForm.querySelector('input[type="text"]');
    let currentConversationId = null;
    
    // Bind provider/client list items
    document.querySelectorAll('.provider-item, .client-item').forEach(item => {
        item.addEventListener('click', async function(e) {
            e.preventDefault();
            const convId = this.dataset.conversationId;
            if (convId) {
                currentConversationId = convId;
                await loadConversation(convId);
            } else {
                // Create new conversation
                const providerId = this.dataset.providerId;
                const clientId = this.dataset.clientId;
                
                try {
                    // Show loading state
                    chatList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ __('chat.creating_conversation') }}</p></div>';
                    chatPlaceholder.style.display = 'none';
                    
                    const response = await fetch('/customerservices/chat/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            provider_id: providerId,
                            client_id: clientId
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update the conversation ID in the dataset
                        this.dataset.conversationId = data.conversation.id;
                        
                        // Load the new conversation
                        currentConversationId = data.conversation.id;
                        await loadConversation(data.conversation.id);
                    } else {
                        chatList.innerHTML = '<div class="text-center py-5"><p class="text-danger">{{ __('chat.failed_create_conversation') }}</p></div>';
                    }
                } catch (error) {
                    console.error('Error creating conversation:', error);
                    chatList.innerHTML = '<div class="text-center py-5"><p class="text-danger">{{ __('chat.failed_create_conversation') }}</p></div>';
                }
            }
        });
    });
    
    // Load conversation messages
    async function loadConversation(conversationId) {
        try {
            const response = await fetch(`/customerservices/chat/${conversationId}/messages`);
            const data = await response.json();
            
            if (data.success) {
                displayMessages(data.messages, data.conversation);
                // Mark as read
                await markAsRead(conversationId);
            }
        } catch (error) {
            console.error('Error loading conversation:', error);
            alert('{{ __('chat.failed_load_conversation') }}');
        }
    }
    
    // Display messages in chat
    function displayMessages(messages, conversation) {
        chatPlaceholder.style.display = 'none';
        chatList.innerHTML = '';

        // Update header with selected user
        const headerNameEl = document.getElementById('chatHeaderName');
        const headerAvatarEl = document.getElementById('chatHeaderAvatar');
        const typingEl = document.getElementById('chatTypingIndicator');
        const displayName = listType === 'provider'
            ? (conversation.provider?.business_name || 'Conversation')
            : (listType === 'client'
                ? (conversation.client?.name || 'Conversation')
                : (conversation.client?.name || conversation.provider?.business_name || 'Conversation'));
        headerNameEl.textContent = displayName;
        
        // Get avatar from conversation user
        const avatarUrl = conversation.user?.avatar_url || '/images/default-avatar-male.svg';
        headerAvatarEl.src = avatarUrl;
        typingEl.classList.add('d-none');

        messages.forEach(msg => {
            const isAdmin = msg.sender_type === 'admin';
            const messageHtml = `
                <li class="${isAdmin ? 'odd' : ''}">
                    <div class="conversation-text ms-1">
                        <div class="ctext-wrap">
                            <span class="user-name mb-1">${msg.sender.name}</span>
                            <p class="mb-0">${escapeHtml(msg.content)}</p>
                        </div>
                        <small class="text-muted">${formatMessageTime(msg.created_at)}</small>
                    </div>
                </li>
            `;
            chatList.insertAdjacentHTML('beforeend', messageHtml);
        });

        // Scroll to bottom
        chatList.scrollTop = chatList.scrollHeight;
    }
    
    // Send message
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!currentConversationId) {
            alert('{{ __('chat.select_conversation_first') }}');
            return;
        }
        
        const message = messageInput.value.trim();
        if (!message) {
            return;
        }
        
        try {
            const response = await fetch(`/customerservices/chat/${currentConversationId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear input
                messageInput.value = '';
                
                // Reload conversation
                await loadConversation(currentConversationId);
            } else {
                alert('{{ __('chat.failed_send_message') }}');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('{{ __('chat.failed_send_message') }}');
        }
    });
    
    // Mark conversation as read
    async function markAsRead(conversationId) {
        try {
            await fetch(`/customerservices/chat/${conversationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }
    
    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatMessageTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffMins < 1440) return `${Math.floor(diffMins / 60)}h ago`;
        
        return date.toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>
@endsection