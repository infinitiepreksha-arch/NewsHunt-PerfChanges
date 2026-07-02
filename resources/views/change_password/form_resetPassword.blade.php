<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ $favicon ?? url('assets/images/logo/logo.png') }}" type="image/x-icon">
    <title>Forget password</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    @include('admin.layouts.include')
    @yield('css')
</head>

<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img class="w-100px lg:w-128px text-dark h-8 dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block" src="{{ asset('front_end/classic/images/custom/LoginLight.png') }}" alt="logo">
                    <img class="w-100px lg:w-128px text-dark h-8 dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none" src="{{ asset('front_end/classic/images/custom/LoginDark.png') }}" alt="logo">
                </a>
            </div>
            <div class="card card-md">
            <div class="card">
            <div class="card-header">
                <div class="divider">
                    <div class="divider-text">
                        <h4 class="mb-0">{{ __('Change Password') }}</h4>
                    </div>
                </div>
            </div>
        <form action="{{ url('password/form') }}" method="POST" data-parsley-validate class="create-form">
        @csrf
        <input type="hidden" name="id" value="{{ $user['id'] }}"/>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card-body">
                    <!-- New Password Field -->
                    <div class="mb-3 mandatory">
                        <label for="password" class="form-label">{{ __('New Password') }}</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control form-control-solid" placeholder="{{ __('New Password') }}" data-parsley-minlength="8" data-parsley-uppercase="1" data-parsley-lowercase="1" data-parsley-number="1" data-parsley-special="1" data-parsley-required />
                            <span class="input-group-text toggle-password cursor-pointer">
                                <i class="bi bi-eye" id="togglePasswordIcon1"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mb-3 mandatory">
                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-solid" placeholder="{{ __('Confirm Password') }}" data-parsley-equalto="#password" required />
                            <span class="input-group-text toggle-password cursor-pointer">
                                <i class="bi bi-eye" id="togglePasswordIcon2"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Change') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
        </div>
               </div>
            <div class="text-center text-secondary mt-3">
                Don't have account yet? <a href="./sign-up.html" tabindex="-1">Sign up</a>
            </div>
        </div>
    </div>
    
</body>
  <script defer src="{{asset('assets/js/custom/custom.js')}}"></script>
</html>
