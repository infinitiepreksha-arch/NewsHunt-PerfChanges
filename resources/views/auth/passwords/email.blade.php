@extends('auth.layout.main')
@section('content')
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark">
                <img src="{{ $favicon != null ? $favicon : url('assets/images/logo/logo.png') }}" alt="Logo" class="navbar-brand-image img-custom-height rounded-4">
          </a>
        </div>
         @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <form class="card card-md" action="{{ route('password.email') }}"  method="POST" autocomplete="off" novalidate>
            @csrf
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Forgot password</h2>
            <p class="text-secondary mb-4">Enter your email address and your password will be reset and emailed to you.</p>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="Enter email">
               @if ($errors->has('email'))
                    <span class="help-block text-danger">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100"> <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                    {{ __('Send Password Reset Link') }}</button>
            </div>
          </div>
        </form>
        <div class="text-center text-secondary mt-3">
          Forget it, <a href="{{url('admin/login')}}">send me back</a> to the sign in screen.
        </div>
      </div>
    </div>
@endsection
