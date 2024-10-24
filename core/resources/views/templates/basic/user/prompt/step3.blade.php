@php
    $stepContent = getContent('prompt_create_file.content', true);
@endphp

@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="step-area pb-60">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <a href="{{ route('user.prompt.step2', $prompt->slug) }}" class="btn btn--base">
                            <i class="fas fa-arrow-left"></i> @lang('Back')
                        </a>
                    </div>
                    <div class="sell-wrapper">
                        <div class="w-100">
                            <div class="sell__wizard__indicator w-100">
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item"></span>
                            </div>
                        </div>
                        <div class="step-count">
                            <p class="fs-14">@lang('Step')
                                <span>@lang('3')</span>/<span>@lang('4')</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="preloader" style="display: none;">
                <div class="spinner"></div>
            </div>
            <div class="row gy-4 justify-content-between">
                <div class="col-lg-7">
                    <div class="prompt-step-content">
                        <h4 class="title">
                            @lang('Prompt File')
                        </h4>
                        <form id="step3">
                            @csrf
                            <div class="row">
                                @if (!$versions->isEmpty())
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form--label">@lang('AI Tool Version') <span
                                                    class="subtitle">(@lang('Select the AI Tool Version.'))</span> </label>
                                            <select data-minimum-results-for-search="-1"  class="select2 form--control" name="tool_version_id"
                                                required>
                                                <option selected disabled>@lang('Select Tool Version')</option>
                                                @foreach ($versions as $version)
                                                    <option value="{{ $version->id }}" @selected($version->id == @$prompt->tool_version_id)>
                                                        {{ __($version->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Prompt') <span
                                                class="subtitle">(@lang('Put any variables in [square brackets].'))</span> </label>
                                        <textarea class="form--control" name="prompt" placeholder="@lang('e.g. An Impressionist oil painting of [Flower] in a purple vase..')" required>{{ __($prompt->prompt) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Testing Prompt') <span
                                                class="subtitle">(@lang('Put any variables in [square brackets].'))</span> </label>
                                        <textarea class="form--control" name="testing_details" placeholder="@lang('e.g. An Impressionist oil painting of [Flower] in a purple vase..')" required>{{ __($prompt->testing_details) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Prompt Instructions') <span
                                                class="subtitle">(@lang('Any extra tips or examples for the buyer on how to use this prompt.'))</span> </label>
                                        <textarea class="form--control" name="instruction" placeholder="@lang('e.g. An Impressionist oil painting of [Flower] in a purple vase..')" required>{{ __($prompt->instruction) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-4">
                                        <label class="form--label">@lang('Example image')</label>
                                        <x-image-uploader image="{{ $prompt->image }}" size="" class="w-100"
                                            type="prompt" :required="@$prompt->image?false:true" darkMode=true />
                                        <small class="text-muted">@lang('Image will be resized into') {{ getFileSize('prompt') }}</small>
                                    </div>

                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="form-label">@lang('More Images') :</label>
                                            <span class="subtitle mb-2">@lang('Upload example images generated by this prompt (no collages or edits)')</span>
                                            <div class="input-field">
                                                <div class="input-images"></div>
                                                <small class="form-text text-muted">
                                                    <i class="las la-info-circle"></i> @lang('You can only upload a maximum of 6 images')
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group mb-0">
                                            <button id="nextbtn" class=" btn btn--base btn--lg">@lang('Next')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="prompt-step-thumb">
                        <img src="{{ frontendImage('prompt_create_file', @$stepContent->data_values->image, '415x415') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/global/css/image-uploader.min.css') }}" rel="stylesheet">
@endpush

@push('style')
    <style>
        /* Preloader CSS */
        .image-upload-input-wrapper .bg-primary {
            background-color: hsl(var(--base)) !important;
        }

        .image-upload-input-wrapper label {
            border: none;
        }

        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(64, 62, 62, 0.7);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid hsl(var(--card-bg)) !important;
            border-top: 4px solid hsl(var(--base)) !important;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            @if (isset($images))
                let preloaded = @json($images);
            @else
                let preloaded = [];
            @endif
            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'photos',
                preloadedInputName: 'old',
                maxFiles: 6
            });


            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
                        $(preview).css('background-image', 'url(' + e.target.result + ')');
                        $(preview).addClass('has-image');
                        $(preview).hide();
                        $(preview).fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".image-upload-input").on('change', function() {
                proPicURL(this);
            });
            $(".remove-image").on('click', function() {
                $(this).parents(".image-upload-preview").css('background-image', 'none');
                $(this).parents(".image-upload-preview").removeClass('has-image');
                $(this).parents(".image-upload-wrapper").find('input[type=file]').val('');
            });
            $("form").on("change", ".file-upload-field", function() {
                $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/,
                    ''));
            });

            $('#nextbtn').on('click', function(e) {
                e.preventDefault();

                $('#preloader').show();

                var formData = new FormData($('#step3')[0]);
                var url = '{{ route('user.prompt.step3.store', $prompt->slug) }}';
                setTimeout(() => {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                @if (!isset($prompt))
                                    window.location.href = response.redirect_url;
                                @else
                                    window.location.href = response.redirect_url;
                                @endif
                            } else {
                                $.each(response.message, function(index, error) {
                                    notify('error', error);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            notify('error', error);
                        },
                        complete: function() {
                            $('#preloader').hide();
                        }
                    });
                }, 1000);
            });

        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .image-upload-input-wrapper i.la.la-cloud-upload:before {
            display: none !important;
        }

        .image-upload-input-wrapper i.la.la-cloud-upload:after {
            content: "\f030" !important;
            font-family: 'Line Awesome Free';
            font-weight: 900;
        }
    </style>
@endpush
