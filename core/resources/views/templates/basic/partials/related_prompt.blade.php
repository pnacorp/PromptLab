@forelse($prompts as $prompt)
    <div class="col-lg-4 col-md-6">
        <div class="featured-item-inner">
            <div class="featured-item">
                <div class="featured-item__thumb">
                    <a href="{{ route('prompt.details', $prompt->slug) }}"><img
                            src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$prompt->image) }}"
                            alt="image"></a>
                    <span class="badge-tag">
                        <i class="fa-solid fa-wand-sparkles"></i>
                        <span class="text--gradient">{{ __($prompt->tool->name) }}</span>
                    </span>
                </div>
                <div class="featured-item__content">
                    <h5 class="title">
                        <a href="{{ route('prompt.details', $prompt->slug) }}"
                            class="text--overflow">{{ __($prompt->title) }}</a>
                    </h5>
                    <a href="{{ route('seller.profile', $prompt->user->username) }}" class="username">
                        {{ '@' . $prompt->user->username }} <img
                            src="{{ asset($activeTemplateTrue . 'images/thumbs/badge.svg') }}" alt="img"></a>
                    <span
                        class="price text--gradient mt-1">{{ gs('cur_sym') }}{{ showAmount($prompt->price, currencyFormat: false) }}</span>
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
    @include($activeTemplate . 'partials.empty', ['message' => 'Prompt not found!'])
@endforelse


