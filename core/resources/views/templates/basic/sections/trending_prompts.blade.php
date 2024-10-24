@php
    $promptContent = getContent('trending_prompts.content', true);
    $prompts = App\Models\Prompt::approved()
        ->trending()
        ->with(['tool', 'user'])
        ->latest()
        ->take(9)
        ->get();

    $showSeeMore = $prompts->count() > 8;
    $prompts = $prompts->take(8);
@endphp

@if ($prompts->count())
    <section class="trending-area pt-60 pb-60">
        <div class="container">
            <div class="section-heading style-left d-flex flex-wrap justify-content-between gap-2">
                <div class="section-heading__left">
                    <span class="section-heading__subtitle">
                        {{ __(@$promptContent->data_values->background_text) }}
                    </span>
                    <h2 class="section-heading__title">{{ __(@$promptContent->data_values->heading) }}</h2>
                </div>
                @if ($showSeeMore)
                    <div class="section-heading__right">
                        <a href="{{ route('prompt.trending') }}" class="btn btn-outline--base">@lang('View all')</a>
                    </div>
                @endif
            </div>

            <div class="row gy-4">
                @include($activeTemplate . 'partials.trending_prompt', [
                    'prompts' => $prompts,
                ])
            </div>
        </div>
    </section>
@endif
