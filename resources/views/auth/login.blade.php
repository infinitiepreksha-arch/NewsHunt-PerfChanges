@extends('auth.layout.main')
@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                @if (env('DEMO_MODE'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning mb-0">
                                <b>Note:</b> If you cannot login here, please close the codecanyon frame by clicking on <b>x Remove Frame</b> button from top right corner on the page or 
                                {{-- <a href="{{url(admin)}}" target="_blank">&gt;&gt; Click here
                                    &lt;&lt;</a> --}}
                                    <a href="{{ route('admin.login') }}" target="_blank">&gt;&gt; Click here &lt;&lt;</a>
                            </div>
                        </div>
                    </div>
                @endif
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ $favicon != null ? $favicon : url('assets/images/logo/logo.png') }}" alt="Logo" class="navbar-brand-image img-custom-height">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Admin Login</h2>
                    <form method="POST" action="{{ route('admin.login') }}" id="frmLogin">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" id="email" placeholder="{{ __('Email') }}"
                                class="form-control login-border form-input @error('email') is-invalid @enderror"
                                name="email" value="{{ env('DEMO_MODE') ? 'admin@gmail.com' : old('email'); }}" required autocomplete="email" autofocus>
                        </div>
                        <div class="mb-2">
                            <label for="forget-password" class="form-label">Password<span class="form-label-description">
                                    <a href="{{ route('password.request') }}">I forgot password</a>
                                </span>
                            </label>
                            <div class="input-group input-group-flat">
                                <input id="password" type="password" placeholder="Password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required value="{{ env('DEMO_MODE') ? 'Admin@123' : old('password'); }}"
                                    autocomplete="current-password">
                                <span class="input-group-text pe-1 py-0 hover-shadow-none">
                                    <button type="button" class="btn btn-action p-0 hover-shadow-none"
                                        title="Show password" data-bs-toggle="tooltip" onClick="togglePassword()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" />
                                <span class="form-check-label">Remember me on this device</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </div>
                        @if (env('DEMO_MODE'))
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button type="button" class="btn bg-warning-lt w-100" id="admin-btn"> Sign in as admin
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <div class="text-center text-secondary mt-3">
            </div>
        </div>
    </div>
@endsection
