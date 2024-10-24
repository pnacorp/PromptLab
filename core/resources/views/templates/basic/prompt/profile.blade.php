@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="seller-details">
        <div class="seller-details__thumb">
            <img src="{{ getImage(getFilePath('userCover') . '/' . $user->coverImage, cover: true) }}" alt="image" class="fit-image">
        </div>
        <div class="seller-wrapper pb-70">
            <div class="container">
                <div class="seller-profile d-flex justify-content-between">
                    <div class="seller-profile__thumb">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, avatar: true) }}" alt="image" class="fit-image">
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn @if (auth()->id() == $user->id) disabled @endif {{ $isFollowing ? 'btn--success' : 'btn--black' }} @guest loginButton @else btn-follow @endguest" data-id="{{ $user->id }}">
                            {{ $isFollowing ? __('Following') : __('Follow') }} <i class="fa-solid {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                        </button>
                    </div>

                </div>
                <div class="seller-details__content">
                    <span class="seller-details__name mb-0"> {{ '@' . $user->username }}</span>
                    @if ($user->description)
                        <p class="desc">{{ __($user->description) }}</p>
                    @endif
                    <ul class="popular-list d-flex gap-3 flex-wrap">
                        <li class="popular-list__item">
                            <span class="badge-tag style-two">
                                <i class="fa-solid fa-eye"></i>
                                <span class="text-white">
                                    {{ formatProfileViewCount($user->profile_view) }}
                                </span>
                            </span>
                        </li>
                        <li class="popular-list__item">
                            <span class="badge-tag style-two">
                                <span class="text-white">{{ $user->prompts->count() }} @lang('Prompts')</span>
                            </span>
                        </li>
                    </ul>

                    <ul class="seller-activities d-flex gap-3 flex-wrap mt-2">
                        <li>
                            <strong class="text-white"> {{ $user->follows->count() }}</strong> @lang('Following')
                        </li>
                        <li>
                            <strong class="text-white">{{ $user->followers->count() }}</strong> @lang('Followers')
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>

    @if (!blank($prompts))
        <section class="trending-area pt-60 pb-60">
            <div class="container">
                <div class="section-common-header d-flex flex-wrap justify-content-between gap-3 align-items-center">
                    <h4 class="title">@lang('Prompts')</h4>

                    <div class="form-group  mb-0">
                        <div class="border-gradient subscribe-input search-form-wrapper w-100">
                            <form method="GET">
                                <input type="text" name="search" class="form-control form--control border-gradient w-100" placeholder="@lang('Search')..." autocomplete="off" value="{{ request()->search }}">
                                <button class="search-form__btn"><i class="fa-solid fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row gy-4">
                    @include($activeTemplate . 'partials.trending_prompt', [
                        'prompts' => $prompts,
                    ])
                </div>

                <div class="mt-5">
                    {{ paginateLinks($prompts) }}
                </div>
            </div>
        </section>
    @endif
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('.btn-follow').on('click', function() {
                    var button = $(this);
                    var followedUserId = button.data('id');

                    $.ajax({
                        url: '{{ route('user.follow') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            followed_user_id: followedUserId
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.message === 'Followed successfully') {

                                    button.find('i').removeClass('fa-user-plus').addClass(
                                        'fa-user-check');
                                    button.contents().filter(function() {
                                        return this.nodeType === 3;
                                    }).first().replaceWith('@lang('Following') ');

                                    button.removeClass('btn--black').addClass(
                                        'btn--success');
                                } else {

                                    button.find('i').removeClass('fa-user-check').addClass(
                                        'fa-user-plus');
                                    button.contents().filter(function() {
                                        return this.nodeType === 3;
                                    }).first().replaceWith('@lang('Follow') ');

                                    button.removeClass('btn--success').addClass(
                                        'btn--black');
                                }
                                notify('success', response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                                'An error occurred';
                            notify('error', errorMessage);
                        }
                    });
                });
            });
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .section-common-header {
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid hsl(var(--border-color));
            font-family: var(--body-font);
            font-weight: 500;
            font-size: 1.125rem;
        }

        .section-common-header .title {
            padding-bottom: unset;
            margin-bottom: unset;
            border-bottom: unset;
        }

        @media screen and (max-width: 767px) {
            .pagination {
                margin-top: 0;
            }
        }
    </style>
@endpush
