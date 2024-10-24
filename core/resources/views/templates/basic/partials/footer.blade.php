@php
    $footerContent = getContent('footer.content', true);
    $socialIcons = getContent('social_icon.element', false, null, true);
    $policyPages = getContent('policy_pages.element', false, null, true);
    $subscribeContent = getContent('subscribe.content', true);
    $pages = App\Models\Page::where('tempname', $activeTemplate)
        ->where('is_default', Status::NO)
        ->latest()
        ->get();
@endphp
<footer class="footer-area">
    <img src="{{ frontendImage('footer', @$footerContent->data_values->left_background_image, '210x180') }}"
        class="footer-shape" alt="img">

    <img src="{{ frontendImage('footer', @$footerContent->data_values->right_background_image, '172x125') }}"
        class="footer-shape-two" alt="img">

    <div class="pb-60 pt-60">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}"> <img src="{{ siteLogo() }}" alt="logo"></a>
                        </div>
                        <p class="footer-item__desc">
                            {{ __(@$footerContent->data_values->footer_text) }}</p>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item pl-lg-70">
                        <h5 class="footer-item__title">@lang('Pages')</h5>
                        <ul class="footer-menu">
                            @if (@$pages)
                                @foreach ($pages as $k => $data)
                                    <li class="footer-menu__item"><a href="{{ route('pages', [$data->slug]) }}"
                                            class="footer-menu__link">{{ __($data->name) }}</a>
                                    </li>
                                @endforeach
                            @endif
                            <li class="footer-menu__item"><a href="{{ route('prompt.all') }}"
                                    class="footer-menu__link">@lang('Prompts')</a>
                            </li>


                            <li class="footer-menu__item"><a href="{{ route('blog') }}" class="footer-menu__link">@lang('Blogs')</a>
                            </li>
                            <li class="footer-menu__item"><a href="{{ route('contact') }}" class="footer-menu__link">@lang('Contact Us')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Follow Us')</h5>
                        <ul class="footer-menu footer-contact-icon">
                            @foreach ($socialIcons as $social)
                                <li class="footer-menu__item"><a href="{{ @$social->data_values->url }}"
                                        target="_blank" class="footer-menu__link"> @php echo @$social->data_values->social_icon; @endphp
                                        {{ @$social->data_values->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">{{ __(@$subscribeContent->data_values->section_title) }}</h5>
                        <p class="footer-item__desc mb-2">{{ __(@$subscribeContent->data_values->heading) }}</p>
                        <div class="cta-form">
                            <form class="cta__subscribe" id="subscribeForm">
                                @csrf
                                <div class="input-group form-group gap-2">
                                    <div class="border-gradient subscribe-input">
                                        <input type="email" class="form-control form--control border-gradient w-100"
                                            placeholder="@lang('Enter your email')" name="email">
                                    </div>
                                    <button class="btn btn--base border--5">@lang('Subscribe')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <!-- bottom Footer -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <p class="fs-14 text-lg-start text-center text--black">&copy; {{ date('Y') }} <a
                                class="text--base" href="{{ route('home') }}">{{ __(gs()->site_name) }}.</a>
                            @lang('All rights reserved.')
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="footer-bottom__menu justify-content-md-center">
                            <ul>
                                @foreach ($policyPages as $link)
                                    <li>
                                        <a
                                            href="{{ route('policy.pages', $link->slug) }}">{{ __(@$link->data_values->title) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Top End-->
</footer>

@push('script')
    <script>
        "use strict";
        (function($) {
            var form = $("#subscribeForm");
            form.on('submit', function(e) {
                e.preventDefault();
                var data = form.serialize();
                $.ajax({
                    url: `{{ route('subscribe') }}`,
                    method: 'post',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            form.find('input[name=email]').val('');
                            form.find('button[type=submit]').attr('disabled', false);
                            notify('success', response.message);
                        } else {
                            notify('error', response.error);
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
