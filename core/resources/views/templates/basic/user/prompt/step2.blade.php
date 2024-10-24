@php
    $stepContent = getContent('prompt_step_content.content', true);
@endphp

@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="step-area pb-60">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sell-wrapper">
                        <div class="w-100">
                            <div class="sell__wizard__indicator w-100">
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item"></span>
                                <span class="sell__wizard__indicator__item"></span>
                            </div>
                        </div>
                        <div class="step-count">
                            <p class="fs-14">@lang('Step')
                                <span>@lang('2')</span>/<span>@lang('4')</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="preloader">
                <div class="spinner"></div>
            </div>
            <div class="row gy-4 align-items-center justify-content-between">
                <div class="col-lg-7">
                    <div class="prompt-step-content">
                        <h4 class="title">
                            @lang('Prompt Details')
                        </h4>
                        <p class="desc">
                            @lang('Tell us about the prompt you want to sell.')
                        </p>
                        <form id="step2">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('AI Tool Type')
                                        <span class="subtitle">(@lang('Select the type'))</span> </label>
                                        <select class="select select2 form--control w-auto d-block" name="tool_id" required>
                                            <option selected disabled></option>
                                            @foreach ($tools as $tool)
                                                <option value="{{ $tool->id }}" @selected($tool->id == @$prompt->tool_id)>
                                                    {{ __($tool->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Prompt Category') <span
                                                class="subtitle">(@lang('Select the category of prompt you want to sell'))</span> </label>
                                        <select class="select select2 form--control w-auto d-block" name="category_id"
                                            required>
                                            <option selected disabled></option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected($category->id == @$prompt->category_id)>
                                                    {{ __($category->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Title') <span
                                                class="subtitle">(@lang('Suggest a title for this prompt.'))</span> </label>
                                        <input type="text" class="form--control" name="title"
                                            placeholder="@lang('e.g. Title')" value="{{ @$prompt->title }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Description') <span
                                                class="subtitle">(@lang('Your prompt description will increase your sales.'))</span></label>
                                        <textarea class="form--control" name="description" required placeholder="@lang('e.g. Converts movie titles into emoji')">{{ @$prompt->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Price') </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control form--control" name="price" required
                                                value="{{ @$prompt->price ? getAmount($prompt->price) : '' }}">
                                            <span class="input-group-text"> {{ __(gs('cur_text')) }} </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <button id="nextbtn" class="btn btn--base btn--lg">@lang('Next')</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="prompt-step-thumb">
                        <img src="{{ frontendImage('prompt_step_content', @$stepContent->data_values->image, '415x415') }}"
                            alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Preloader CSS */
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
            $('#preloader').hide();

            $('#nextbtn').on('click', function(e) {
                e.preventDefault();

                $('#preloader').show();

                var formData = new FormData($('#step2')[0]);
                var url = '{{ route('user.prompt.step2.store', @$prompt->slug ?? 0) }}';

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
            })

        })(jQuery);
    </script>
@endpush
