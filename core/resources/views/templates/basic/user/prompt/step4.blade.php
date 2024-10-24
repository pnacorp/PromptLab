@php
    $stepContent = getContent('prompt_step_content.content', true);
@endphp

@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="step-area pb-60">
        <div class="container">
            <div class="prompt-step-content">
                <div class="prompt-complete-card text-center">

                    <div class="d-flex gap-3 justify-content-center align-items-center">

                        <h4 class="prompt-complete-card__title text-center">
                            <i class="las la-check-circle"></i> @lang('Congratulations!')
                            <br>
                            @if (session('EDIT_PROMPT'))
                                @lang('Your prompt has been successfully updated.')
                            @else
                                @lang('Your prompt has been successfully created.')
                            @endif
                        </h4>
                    </div>

                    <p class="subtitle text-center">
                        @if (gs('prompt_approval'))
                            @lang('Great job! Your prompt is now published for everyone to explore.')
                        @else
                            @lang('Thank you! Our team will review it shortly to ensure everything looks great. Stay tuned for updates.')
                        @endif
                    </p>

                    <a href="{{ route('user.prompt.my.list') }}" class="btn btn--base btn--lg mt-5">@lang('View Your Prompts')</a>
                </div>

            </div>

        </div>
    </div>
@endsection
