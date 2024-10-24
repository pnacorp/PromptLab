@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <div class="container pb-60">
        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="prompt-details__thumb pb-2">

                    <div class="prompt-details__item">
                        <img src="{{ getImage(getFilePath('prompt') . '/' . @$prompt->image) }}" alt="img">
                    </div>
                    @foreach ($prompt->promptImages as $image)
                        <div class="prompt-details__item">
                            <img src="{{ getImage(getFilePath('prompt') . '/' . @$image->image) }}" alt="img">
                        </div>
                    @endforeach
                </div>
                @if (!blank($prompt->promptImages))
                    <div class="prompt-details__gallery">
                        @foreach ($allImages as $image)
                            <div class="prompt-gallery__item">
                                <img src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$image) }}" alt="img">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="prompt-details-content">
                    <div class="d-flex gap-2 justify-content-between">
                        <h2 class="title">
                            {{ __($prompt->title) }}
                        </h2>
                        <div>
                            <button type="button" class="wishlist-btn @guest loginButton @else favorite-btn @endguest  @if ($isFavorite) text-danger @endif" data-id="{{ $prompt->id }}"><i class="fa-solid fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="list list--row rating-list">
                        <span class="rating-list__icon rating-list__icon-active">
                            @php
                                $avgRating = $prompt->getAvgRating();
                            @endphp
                            @php echo displayRating($avgRating) @endphp
                        </span>

                        ({{ getAmount($avgRating) }} @lang('stars')) <i class="fas fa-circle fs-4 mx-2"></i> {{ $prompt->reviews_count }}

                        <span>@lang('reviews')</span>
                    </div>

                    <ul class="prompt-info">
                        <li>
                            <span class="badge-tag style-two">
                                <i class="fa-solid fa-wand-sparkles"></i>
                                <span class="text-white">{{ __($prompt->tool->name) }}</span>
                            </span>
                        </li>
                        @if ($prompt->tool_version_id)
                            <li>
                                <span class="badge-tag style-two">
                                    <span class="text-white">{{ __($prompt->toolVersion->name) }}</span>
                                </span>
                            </li>
                        @endif
                        <li>
                            <span class="featurs">@lang('Created'): {{ diffForHumans($prompt->created_at) }}</span>
                        </li>
                    </ul>

                    <div class="prompt-interaction">
                        <span class="interaction-tag">
                            <i class="fa-solid fa-heart"></i> {{ __($prompt->favorites_count) }}
                        </span>
                        <span class="interaction-tag">
                            <i class="fa-solid fa-eye"></i> {{ __($prompt->views) }}
                        </span>
                    </div>

                    <div class="creator-profile py-4">
                        <p class="mb-2">@lang('Creator')</p>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="thumb">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $prompt->user->image, avatar: true) }}" alt="img">
                                <img class="badge-img" src="{{ asset($activeTemplateTrue . 'images/thumbs/badge.svg') }}" alt="img">
                            </div>
                            <div>
                                <span class="badge-tag style-two">
                                    <a href="{{ route('seller.profile', $prompt->user->username) }}"><span class="text-white fs-14"> {{ '@'. $prompt->user->username }}</span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="desc max-width-425">
                        @php echo $shortDescription @endphp
                        @if (strlen($description) > $limit)
                            <span class="moretext">
                                @php echo substr($description, strlen($shortDescription)) @endphp
                            </span> <a class="moreless-button text-white" href="#">@lang('More....')</a>
                        @endif
                    </p>
                    <h4 class="price mb-0">{{ showAmount($prompt->price) }}</h4>
                    <div class="prompt-btn d-flex gap-3 mb-4 flex-wrap">
                        @auth
                            <button class="btn button btn--base btn--lg btn-add-to-cart" data-prompt-id="{{ $prompt->id }}"><i class="fa-solid fa-cart-shopping"></i>
                                @lang('Add To Cart')</button>
                        @else
                            <button class="btn button btn--base btn--lg loginButton"><i class="fa-solid fa-cart-shopping"></i>
                                @lang('Add To Cart')</button>
                        @endauth
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <!-- Comments Start -->
                <div class="review-prompt">
                    @if (!blank($reviews))
                        <div class="review-prompt-header">
                            <h6 class="review-prompt-title">
                                @lang('Prompt Review') <i class="fa-solid fa-angle-up"></i>
                            </h6>
                        </div>
                    @endif
                    <div class="review-prompt__content">
                        @if (!blank($reviews))
                            <div class="review-list">
                                <p class="desc">@lang('See what others are saying about this prompt')</p>

                                <ul class="comment-list" id="comment-list">
                                    @include($activeTemplate . 'prompt.review')
                                </ul>
                            </div>
                        @endif

                        @if ($reviews->nextPageUrl())
                            <div class="load-more text-white">
                                <button class="btn--base btn btn--sm ">@lang('LOAD MORE')</button>
                            </div>
                        @endif
                        <!-- Comments End -->

                        <!-- Comments Form Start -->
                        @if ($hasPurchased && !$userReview)
                            <div class="pt-5" id="comment-box">
                                <h4>@lang('Write your review')</h4>
                                <form action="{{ route('user.prompt.review', $prompt->slug) }}" method="POST" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="rating">@lang('Rating')</label>
                                                <div class="rating">
                                                    <div class="rating-form-group">
                                                        <label class="star-label">
                                                            <input name="rating" type="radio" value="1" />
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                        </label>
                                                        <label class="star-label">
                                                            <input name="rating" type="radio" value="2" />
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                        </label>
                                                        <label class="star-label">
                                                            <input name="rating" type="radio" value="3" />
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                        </label>
                                                        <label class="star-label">
                                                            <input name="rating" type="radio" value="4" />
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                        </label>
                                                        <label class="star-label">
                                                            <input name="rating" type="radio" value="5" />
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                            <span class="icon fs-26"><i class="las la-star"></i></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="comment">@lang('Write your opinion')</label>
                                                <textarea name="review" class="mt-2 form--control" required></textarea>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-outline--base btn--lg">@lang('Submit Review')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                        <!-- Comment Form End -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!blank($relatedPrompts))
        <section class="features-area pt-60 pb-60">
            <div class="container">
                <div class="section-heading style-left d-flex flex-wrap justify-content-between gap-2">
                    <div class="section-heading__left">
                        <h2 class="section-heading__title">@lang('Similar Prompts')</h2>
                    </div>
                    @if ($relatedPromptsCount > 6)
                        <div class="section-heading__right">
                            <a href="{{ route('prompt.similar', $prompt->slug) }}" class="btn btn-outline--base">@lang('View all')</a>
                        </div>
                    @endif
                </div>
                <div class="row gy-4">
                    @include($activeTemplate . 'partials.related_prompt', ['prompts' => $relatedPrompts])
                </div>
            </div>
        </section>
    @endif
@endsection

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/slick.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.prompt-details__thumb').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                dots: false,
                fade: true,
                asNavFor: '.prompt-details__gallery',
                prevArrow: '<button type="button" class="slick-prev gig-details-thumb-arrow"><i class="las la-long-arrow-alt-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next gig-details-thumb-arrow"><i class="las la-long-arrow-alt-right"></i></button>',
            });

            $('.prompt-details__gallery').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                asNavFor: '.prompt-details__thumb',
                dots: false,
                arrows: false,
                focusOnSelect: true,
                prevArrow: '<button type="button" class="slick-prev gig-details-arrow"><i class="las la-long-arrow-alt-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next gig-details-arrow"><i class="las la-long-arrow-alt-right"></i></button>',
                responsive: [{
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 676,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 460,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                ]
            });

            var nextPage = `{{ $reviews->nextPageUrl() }}`;


            $(document).on('click', ".load-more button", function() {
                $('.load-more button').prop('disabled', true)
                if (nextPage) {
                    $.ajax({
                        url: nextPage,
                        method: "GET",
                        data: {},
                        success: function(data) {
                            $('.comment-list').append(data.view)
                            if (data.nextPageUrl) {
                                nextPage = data.nextPageUrl;
                                $('.load-more button').prop('disabled', false)
                            } else {
                                $('.load-more button').remove()
                            }
                        }
                    });
                }

            });

        })(jQuery)
    </script>
@endpush

@include($activeTemplate . 'partials.wishlist-script')

@push('style')
    <style>
        .wishlist-btn {
            font-size: 1.5rem;
        }
    </style>
@endpush
