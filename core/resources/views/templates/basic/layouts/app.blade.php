<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    <link rel="shortcut icon" href="{{ siteFavicon() }}">
    @include('partials.seo')
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">

    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    @stack('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">
    @stack('style')
    <link
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}&thirdColor={{ gs('base_color_2') }}"
        rel="stylesheet">
</head>


<body class="bg-img" data-background-image="{{ asset($activeTemplateTrue . 'images/thumbs/body-thumb.png') }}">

    <div class="preloader">
        <div class="loader-p"></div>
    </div>
    <div class="body-overlay"></div>

    <div class="sidebar-overlay"></div>

    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>

    @stack('fbComment')

    @yield('panel')

    @stack('modal')

    <div class="mouse-cursor cursor-outer"></div>
    <div class="mouse-cursor cursor-inner"></div>

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <!-- cookies dark version start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a
                    href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a class="btn btn--base btn--xl w-100 policy" href="javascript:void(0)">@lang('Allow')</a>
            </div>
        </div>
    @endif

    <div class="modal custom--modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('Login Required')</h5>
                    <span class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('Please log in to add items to your cart')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline--base btn--sm h-36"
                        data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Jquery js -->
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>
    @stack('script-lib')

    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')
    @if (gs('pn'))
        @include('partials.push_script')
    @endif
    @stack('script')
    <script>
        "use strict";
        (function($) {

            $('.select2').select2();

            $(".langSel").on("change", function() {
                window.location.href = "{{ url('/') }}/change/" + $(this).val();
            });


            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            $('.policy').on('click', function() {
                $.get(`{{ route('cookie.accept') }}`, function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });


            var inputElements = $('[type=text],[type=password],select,textarea');

            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox' && element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }
            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

            //highlight
            let elements = document.querySelectorAll('[data-break]');

            Array.from(elements).forEach(element => {
                let html = element.innerHTML;

                if (typeof html != 'string') {
                    return false;
                }

                let position = parseInt(element.getAttribute('data-break'));
                let wordLength = parseInt(element.getAttribute('data-length'));

                html = html.split(" ");

                var firstPortion = [];
                var colorText = [];
                var lastPortion = [];

                if (position < 0) {
                    colorText = html.slice(position);
                    firstPortion = html.slice(0, position);
                } else {
                    var lastWord = position + wordLength;
                    colorText = html.slice(position, lastWord);
                    firstPortion = html.slice(0, position);
                    lastPortion = html.slice(lastWord, html.length);
                }

                var color = element.getAttribute('s-color') || "text--white";

                colorText = `<span class="${color}">${colorText.toString().replaceAll(',', ' ')}</span>`;

                firstPortion = firstPortion.toString().replaceAll(',', ' ');
                lastPortion = lastPortion.toString().replaceAll(',', ' ');

                element.innerHTML = `${firstPortion} ${colorText}  ${lastPortion}`;
            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

            $(document).on('click', '.loginButton', function() {
                var modal = $('#loginModal');
                let data = $(this).data();
                modal.find('.question').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });

            $(document).on('click', '.btn-add-to-cart', function(e) {
                e.preventDefault(); // Prevent the default link behavior

                var button = $(this);
                var promptId = button.data('prompt-id');

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prompt_id: promptId,
                    },
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                            updateCartCount();
                        } else {
                            notify('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                            'An error occurred';
                        notify('error', errorMessage);
                    }
                });
            });

            function updateCartCount() {
                $.ajax({
                    url: '{{ route('cart.count') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('.count-product').text(response.cart_count);
                        }
                    },
                    error: function(xhr) {}
                });
            }

            updateCartCount();

        })(jQuery);
    </script>
</body>

</html>
