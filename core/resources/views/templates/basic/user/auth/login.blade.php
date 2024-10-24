@php
    $content = getContent('login.content', true);
@endphp
@extends($activeTemplate . 'layouts.app')

@section('panel')
    <section class="account">
        <div class="account__inner  flex-wrap">
            <div class="account-left  flex-wrap">
                <div class="account-left__thumb">
                    <img src="{{ frontendImage('login', @$content->data_values->image, '1000x950') }}" alt="image" class="fit-image">
                </div>
            </div>
            <div class="account-right">
                <div class="account-content">
                    <div class="account-form">
                        <div class="account-form_content text-center">
                            <div class="logo mb-4">
                                <a href="{{ route('home') }}"><img src="{{ sitelogo() }}" alt="logo"></a>
                            </div>
                            <h4 class="account-form__title">
                                {{ __($content->data_values->heading) }}
                            </h4>
                            <p class="account-form__desc">{{ __($content->data_values->subheading) }}</p>
                        </div>
                        <form action="{{ route('user.login') }}" method="POST" class="account-form verify-gcaptcha">
                            @csrf
                            <div class="form-group">
                                <label class="form--label" for="username">@lang('Username')</label>
                                <input type="text" class="form--control" id="username" name="username" value="{{ old('username') }}" placeholder="@lang('Enter username')" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label" for="your-password">@lang('Password')</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="your-password" placeholder="@lang('Enter Password')" class="form--control" value="{{ old('password') }}">
                                    <span class="password-show-hide fas toggle-password fa-eye-slash" id="#your-password"></span>
                                </div>
                            </div>
                            <div class="form-group d-flex flex-wrap justify-content-between">
                                <div class="form--check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                                </div>
                                <div class="have-account text-center">
                                    <a href="{{ route('user.password.request') }}" class="forgot-text text-color">@lang('Forgot password?')</a>
                                </div>
                            </div>

                            <x-captcha />

                            <div class="form-group">
                                <button type="submit" id="recaptcha" class="btn btn--base h-48 w-100">@lang('Submit')</button>
                            </div>

                            @include($activeTemplate . 'partials.social_login')

                            <div class="form-group mt-3">
                                <div class="have-account text-center">
                                    <p class="have-account__text fs-16">@lang('Don\'t have an account?')
                                        <a href="{{ route('user.register') }}" class="have-account__link text--gradient">@lang('Create new account')</a>
                                    </p>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
