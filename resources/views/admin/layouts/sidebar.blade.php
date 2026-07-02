<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid d-flex">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand navbar-brand-autodark">
            <a href="{{ url('/admin/dashboard') }}">
                <img src="{{ !empty($company_logo) ? $company_logo : url('assets/images/logo/sidebarlogo.png') }}"
                    alt="{{ config('app.name') }}" class="navbar-brand-image">
            </a>
        </span>
        
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <img class="avatar avatar-sm"
                        src="{{ auth()->user()->profile ?? url('assets/images/faces/2.jpg') }}" alt="">
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name ?? '' }}</div>
                        <div class="mt-1 small text-secondary">admin</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('change-password') }}" class="dropdown-item"> <i
                            class="icon-mid bi bi-gear me-2"></i>{{ __('page.CHANGE_PASSWORD') }}</a>
                    <a class="dropdown-item" href="{{ route('change-profile') }}">
                        <i class="icon-mid bi bi-person me-2" title="changeProfile"></i>{{ __('page.UPDATE_PROFILE') }}
                    </a>
                    <div class="dropdown-divider mb-0 mt-1"></div>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                        {{ csrf_field() }}
                        <a class="dropdown-item" href="{{ route('admin.logout') }}" type="submit">
                            <i class="icon-mid bi bi-box-arrow-left me-2"></i>{{ __('page.LOGOUT') }}
                        </a>
                    </form>
                </div>
                <div class="settings">
                    <form class="offcanvas offcanvas-start offcanvas-narrow" tabindex="-1" id="offcanvasSettings">
                        <div class="offcanvas-header">
                            <h2 class="offcanvas-title">Theme Builder</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body d-flex flex-column">
                            <div>
                                <div class="mb-4">
                                    <label class="form-label">Color mode</label>
                                    <p class="form-hint">Choose the color mode for your app.</p>
                                    <label class="form-check">
                                        <div class="form-selectgroup-item">
                                            <input type="radio" name="theme" value="light" class="form-check-input"
                                                checked="">
                                            <div class="form-check-label">Light</div>
                                        </div>
                                    </label>
                                    <label class="form-check">
                                        <div class="form-selectgroup-item">
                                            <input type="radio" name="theme" value="dark"
                                                class="form-check-input">
                                            <div class="form-check-label">Dark</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-auto space-y">
                                <button type="button" class="btn w-100" id="reset-changes">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/rotate -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M19.95 11a8 8 0 1 0 -.5 4m.5 5v-5h-5"></path>
                                    </svg>
                                    Reset changes
                                </button>
                                <a href="#" class="btn btn-primary w-100" data-bs-dismiss="offcanvas">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/settings -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path
                                            d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z">
                                        </path>
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                    </svg>
                                    Save settings
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="collapse navbar-collapse flex-grow-1 overflow-auto" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                @foreach (config('adminNav') as $value)
                    @php
                        $isActive = false;
                        if (isset($value['children']) && count($value['children']) > 0) {
                            foreach ($value['children'] as $child) {
                                if (url()->current() == route($child['route'])) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        } else {
                            $isActive = url()->current() == route($value['route']);
                        }
                    @endphp
                    <li
                        class="nav-item {{ isset($value['children']) && count($value['children']) > 0 ? 'dropdown' : '' }} {{ $isActive ? 'active' : '' }} ? 'd-lg-none' : '' }}">
                        @if (!isset($value['children']) || count($value['children']) == 0)
                            <a class="nav-link cursor-pointer" href="{{ route($value['route']) }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    {!! $value['svg'] !!}
                                </span>
                                <span class="nav-link-title">
                                    {{ __($value['name']) }}
                                </span>
                            </a>
                        @else
                            <a href="#navbar-layout" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                data-bs-auto-close="false" aria-expanded="true">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    {!! $value['svg'] !!}
                                </span>
                                <span class="nav-link-title">
                                    {{ __($value['name']) }}
                                </span>
                            </a>
                            <div class="dropdown-menu {{ $isActive ? 'show' : '' }}">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        @foreach ($value['children'] as $child)
                                            <a class="dropdown-item {{ url()->current() == route($child['route']) ? 'active' : '' }}"
                                                href="{{ route($child['route']) }}">
                                                {{ __($child['name']) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</aside>
