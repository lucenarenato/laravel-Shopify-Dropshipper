@extends('admin.layouts.app')

@section('content')

<div class="row justify-content-center">

    <div class="col-xl-6 col-lg-8 col-md-6">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
{{--                    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>--}}
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Login!</h1>
                            </div>
                            <form method="POST" class="user" action="{{ route('admin.authenticate') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user"
                                           id="exampleInputEmail" aria-describedby="emailHelp"
                                           placeholder="Enter Email Address..." @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user"
                                           id="exampleInputPassword" @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" name="remember"  {{ old('remember') ? 'checked' : '' }} id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                {{ __('Login') }}
                                </button>

{{--                                <hr>--}}
{{--                                <a href="index.html" class="btn btn-google btn-user btn-block">--}}
{{--                                    <i class="fab fa-google fa-fw"></i> Login with Google--}}
{{--                                </a>--}}
{{--                                <a href="index.html" class="btn btn-facebook btn-user btn-block">--}}
{{--                                    <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook--}}
{{--                                </a>--}}
                            </form>
{{--                            <hr>--}}
{{--                            <div class="text-center">--}}
{{--                                @if (Route::has('password.request'))--}}
{{--                                    <a class="small" href="{{ route('password.request') }}">--}}
{{--                                        {{ __('Forgot Your Password?') }}--}}
{{--                                    </a>--}}
{{--                                @endif--}}

{{--                            </div>--}}
{{--                            <div class="text-center">--}}
{{--                                <a class="small" href="register.html">Create an Account!</a>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>








@endsection
