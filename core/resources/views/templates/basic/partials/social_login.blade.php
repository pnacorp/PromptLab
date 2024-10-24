@php
    $text = isset($register) ? 'Register' : 'Login';
@endphp

@if (@gs('socialite_credentials')->linkedin->status || @gs('socialite_credentials')->facebook->status == Status::ENABLE || @gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="col-12">
        <div class="other-option">
            <span class="other-option__text">@lang('or')</span>
        </div>
    </div>

    <div class="account-social-btn d-flex justify-content-center flex-wrap gap-3">
        @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'google') }}" class="btn btn-outline--base loginButton account-social-action">
                <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="google" class="icon"></a>
        @endif

        @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'facebook') }}" class="btn btn-outline--base loginButton account-social-action">
                <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="facebook" class="icon"></a>
        @endif

        @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
            <a href="{{ route('user.social.login', 'linkedin') }}" class="btn btn-outline--base loginButton account-social-action">
                <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="linkedin" class="icon"></a>
        @endif
    </div>
@endif
