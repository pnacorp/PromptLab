@php
    $stepContent = getContent('prompt_step_content.content', true);
@endphp

@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="step-area pb-60 ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sell-wrapper">
                        <div class="w-100">
                            <div class="sell__wizard__indicator w-100">
                                <span class="sell__wizard__indicator__item active"></span>
                                <span class="sell__wizard__indicator__item"></span>
                                <span class="sell__wizard__indicator__item"></span>
                                <span class="sell__wizard__indicator__item"></span>
                            </div>
                        </div>
                        <div class="step-count">
                            <p class="fs-14">@lang('Step') <span>@lang('1')</span>/<span>@lang('4')</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-4 align-items-center">
                <div class="col-lg-8">
                    <div class="prompt-step-content">
                        <h4 class="title">
                            {{ __(@$stepContent->data_values->heading) }}
                        </h4>
                        <p class="desc">
                            @php
                                echo @$stepContent->data_values->details;
                            @endphp
                        </p>

                        <a href="{{ route('user.prompt.step2') }}" class="btn btn--base btn--lg mt-4">@lang('Create New Prompt')</a>

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="prompt-step-thumb">
                        <img src="{{ frontendImage('prompt_step_content', @$stepContent->data_values->image, '415x415') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



