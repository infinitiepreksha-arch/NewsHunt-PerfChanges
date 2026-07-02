<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <a href="javascript:void(0)" class="nav-link px-0 hide-theme-dark theme-toggle-btn" data-theme="dark" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="javascript:void(0)" class="nav-link px-0 hide-theme-light theme-toggle-btn" data-theme="light" title="Enable light mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path
                            d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-language">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 5h7" />
                            <path d="M9 3v2c0 4.418 -2.239 8 -5 8" />
                            <path d="M5 9c0 2.144 2.952 3.908 6.7 4" />
                            <path d="M12 20l4 -9l4 9" />
                            <path d="M19.1 18h-6.2" />
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="list-group list-group-flush list-group-hoverable">
                                @foreach ($languages as $language)
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto"><img src="{{ $language['image'] }}"
                                                    alt="{{ $language['name'] }}" class="icon-country"></div>
                                            <div class="col text-truncate">
                                                <a href="{{ route('language.set-current', $language['code']) }}"
                                                    class="text-body d-block"><b>{{ $language['name'] ?? '' }}</b></a>
                                            </div>
                                            <div class="col-auto">
                                                <a href="{{ route('language.set-current', $language['code']) }}"
                                                    class="list-group-item-actions show">

                                                    {{-- @dd(
                                                            $language['code'] === session('admin_locale', 'en'), 
                                                            $language['code'], 
                                                            'Admin panel language :' . $language['code'], 
                                                            'Admin panel session language : ' . session('admin_locale', 'en'), 
                                                            session()->all(), 
                                                            app()->getLocale()
                                                        ) --}}

                                                    {{-- @if ($language['code'] === session('admin_locale', 'en'))
                                                        <i class="bi bi-star gold-star"></i>
                                                    @else
                                                        <i class="bi bi-star sliver-star"></i>
                                                    @endif --}}

                                                    @if ($language['code'] === app()->getLocale())
                                                        <i class="bi bi-star gold-star"></i>
                                                    @else
                                                        <i class="bi bi-star sliver-star"></i>
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <img class="avatar avatar-sm"
                        src="{{ auth()->user()->profile ?? url('assets/images/faces/2.jpg') }}" alt="">
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name ?? '' }}</div>
                        <div class="mt-1 small text-secondary">admin</div>
                    </div>
                    <i class="bi bi-caret-down-fill ms-3"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a class="dropdown-item" href="{{ route('change-password') }}">
                        <i class="icon-mid bi bi-gear me-2"></i>{{ __('page.CHANGE_PASSWORD') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('change-profile') }}">
                        <i class="icon-mid bi bi-person me-2" title="changeProfile"></i>{{ __('page.UPDATE_PROFILE') }}
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="">
                        {{ csrf_field() }}
                        <a class="dropdown-item" href="{{ route('admin.logout') }}" type="submit">
                            <i class="icon-mid bi bi-box-arrow-left me-2"></i>{{ __('page.LOGOUT') }}
                        </a>
                    </form>
                </div>
            </div>

        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div>
                @if (config('app.demo_mode'))
                    <div class="col-2">
                        <span class="badge alert-info primary-background-color">Demo Mode</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <input type="hidden" id="current_locale" value="{{ app()->getLocale() }}">
</header>
