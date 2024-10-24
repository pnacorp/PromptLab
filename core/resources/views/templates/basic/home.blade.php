@extends($activeTemplate . 'layouts.frontend')

@section('content')

    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    <section class="banner-section">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <div class="banner-content">
                        <h1 class="banner-content__title">{{ __(@$bannerContent->data_values->heading) }}</h1>
                        <p class="banner-content__desc">{{ __(@$bannerContent->data_values->subheading) }}
                            <span class="text--gradient"> {{ __(@$bannerContent->data_values->highlight_subheading) }}</span>
                        </p>
                        <div class="banner-content__btn">
                            <a href="{{ @$bannerContent->data_values->left_button_link }}" class="btn btn--base">{{ __(@$bannerContent->data_values->left_button_name) }}</a>
                            <a href="{{ @$bannerContent->data_values->right_button_link }}" class="btn btn-outline--base">{{ __(@$bannerContent->data_values->right_button_name) }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-right__thumb">
                        <img src="{{ frontendImage('banner', @$bannerContent->data_values->image,'635x440') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
