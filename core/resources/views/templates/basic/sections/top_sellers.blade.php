@php
    $topSellers = App\Models\Order::topSellers(13);
    $sellerContent = getContent('top_sellers.content', true);
@endphp

@if ($topSellers->count())
    <section class="top-seller-area pt-60 pb-60">
        <div class="container">
            <div class="section-heading style-left d-flex flex-wrap justify-content-between gap-2">
                <div class="section-heading__left">
                    <span class="section-heading__subtitle">
                        {{ __(@$sellerContent->data_values->background_text) }}
                    </span>
                    <h2 class="section-heading__title">{{ __(@$sellerContent->data_values->heading) }}</h2>
                </div>
                @if ($topSellers->count() > 12)
                    <div class="section-heading__right">
                        <a href="{{ route('top.seller') }}" class="btn btn-outline--base">@lang('View all')</a>
                    </div>
                @endif
            </div>
            <div class="top-seller__wrapper">
                @include($activeTemplate . 'partials.top_seller', ['topSellers' => $topSellers->take(12)])
            </div>
        </div>
    </section>
@endif
