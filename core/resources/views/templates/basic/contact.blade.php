@php
    $contactContent = getContent('contact_us.content', true);
@endphp
@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="contact-area pb-60">
    <img class="contact-shape-01" src="{{ frontendImage('contact_us', @$contactContent->data_values->left_background_image,'150x205') }}" alt="img">
    <img class="contact-shape-02" src="{{ frontendImage('contact_us', @$contactContent->data_values->right_background_image,'150x205') }}" alt="img">

    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="contact-left">
                    <div class="contact-left__heading">
                        <h2 class="title">{{ __(@$contactContent->data_values->heading) }}</h2>
                        <p class="desc">{{ __(@$contactContent->data_values->subheading) }}</p>
                    </div>
                    <div class="contact-left__bottom">
                        <div class="contact-left__item">
                            <div class="icon">
                                <i class="fa-solid fa-phone text--gradient phone-icon"></i>
                            </div>
                            <h5 class="title">
                                {{ __(@$contactContent->data_values->contact_number_heading) }}
                            </h5>
                            <p class="desc">{{ __(@$contactContent->data_values->contact_number_subheading) }}</p>
                            <a href="tel:{{ __(@$contactContent->data_values->contact_number) }}" class="contact-link">{{ __(@$contactContent->data_values->contact_number) }}</a>
                        </div>
                        <div class="contact-left__item">
                            <div class="icon">
                                <i class="fas fa-envelope text--gradient"></i>
                            </div>
                            <h5 class="title">
                                {{ __(@$contactContent->data_values->email_address_heading) }}
                            </h5>
                            <p class="desc">{{ __(@$contactContent->data_values->email_address_subheading) }}</p>
                            <a href="mailto:{{ __(@$contactContent->data_values->email_address) }}" class="contact-link">{{ __(@$contactContent->data_values->email_address) }}</a>
                        </div>
                    </div>
                    <p class="note">{{ __(@$contactContent->data_values->note) }}</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form">
                    <form method="POST" class="verify-gcaptcha" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                               <div class="form-group">
                                <label class="form--label">@lang('Name')</label>
                                 <input type="text" class="form--control" name="name" placeholder="@lang('Your Name')" value="{{ old('name', @$user->fullname) }}" @if ($user && $user->profile_complete) readonly @endif
                                 required>
                               </div>
                            </div>
                            <div class="col-sm-12">
                               <div class="form-group">
                                <label class="form--label">@lang('Email')</label>
                                 <input type="email" class="form--control" name="email" placeholder="@lang('Enter Your Email')" value="{{ old('email', @$user->email) }}" @if ($user) readonly @endif required>
                               </div>
                            </div>
                            <div class="col-sm-12">
                               <div class="form-group">
                                <label class="form--label">@lang('Subject')</label>
                                 <input type="text" class="form--control" name="subject" placeholder="@lang('Type Subject')" value="{{ old('subject') }}" required>
                               </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Leave us a message')</label>
                                    <textarea class="form--control" name="message" placeholder="@lang('Please type your message here...')" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <x-captcha />
                            <div class="col-sm-12">
                                <div class="form-group mb-0">
                                    <button class=" btn btn--base btn--lg w-100"> @lang('Send Your Message')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


@if(@$sections->secs != null)
    @foreach(json_decode($sections->secs) as $sec)
        @include($activeTemplate.'sections.'.$sec)
    @endforeach
@endif
@endsection
