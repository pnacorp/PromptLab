@php
    $ctaContent = getContent('cta.content', true);
@endphp
<section class="cta-area pt-60 pb-60">
    <div class="container">
        <div class="cta bg-img" data-background-image="{{ frontendImage('cta', @$ctaContent->data_values->background_image, '1320x390') }}" >
            <div class="row gy-4 align-items-center">
                <div class="col-lg-6">
                    <div class="cta__content">
                        <h2 class="cta__title">
                            {{ __(@$ctaContent->data_values->heading) }}
                        </h2>
                        <p class="cta__desc">
                            {{ __(@$ctaContent->data_values->subheading) }}
                        </p>
                        <a href="{{ @$ctaContent->data_values->button_link }}" class="btn btn-outline--base btn--lg mt-3">{{ __(@$ctaContent->data_values->button_name) }}</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cta__thumb">
                        <img src="{{ frontendImage('cta', @$ctaContent->data_values->right_image, '440x310') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
