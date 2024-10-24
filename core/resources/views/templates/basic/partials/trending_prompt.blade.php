@forelse($prompts as $prompt)
    @php
        $isFavorite = Auth::check() && $prompt->favorites()->where('user_id', Auth::id())->exists();
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 col-xsm-6">
        <div class="trending-item-inner">
            <div class="trending-item">
                <div class="trending-item__badge w-100">
                    <div class="badge-tag">
                        <i class="fa-solid fa-wand-sparkles"></i>
                        <span class="text--gradient">{{ __($prompt->tool->name) }}</span>
                    </div>
                    <button type="button" class="wishlist-btn @guest loginButton @else favorite-btn @endguest @if ($isFavorite) text-danger @endif"
                        data-id="{{ $prompt->id }}"><i class="fa-solid fa-heart"></i></button>
                </div>
                <div class="trending-item__thumb">
                    <a href="{{ route('prompt.details', $prompt->slug) }}"><img
                            src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$prompt->image) }}"
                            alt="img"></a>
                </div>
                <div class="trending-item__content">
                    <div class="trending-item__content-top">
                        <div class="left">
                            <h4 class="title"><a href="{{ route('prompt.details', $prompt->slug) }}" class="text--overflow">{{ __($prompt->title) }}</a></h4>
                            <a href="{{ route('seller.profile', $prompt->user->username) }}" class="username">
                                {{ '@' . $prompt->user->username }} <img
                                    src="{{ asset($activeTemplateTrue . 'images/thumbs/badge.svg') }}"
                                    alt="image"></a>
                        </div>
                        <span class="price text--gradient">{{ gs('cur_sym') }}{{ showAmount($prompt->price, currencyFormat: false) }}</span>
                    </div>
                    @auth
                        <a class="btn btn-outline--base w-100 mt-3 btn-add-to-cart"
                            data-prompt-id="{{ $prompt->id }}">@lang('Add to cart')</a>
                    @else
                        <a class="btn btn-outline--base w-100 mt-3 loginButton">@lang('Add to cart')</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card custom--card">
        <div class="card-body">
            @include($activeTemplate . 'partials.empty', ['message' => 'Prompt not found!'])
        </div>
    </div>
@endforelse

@include($activeTemplate . 'partials.wishlist-script')
