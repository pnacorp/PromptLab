@forelse($topSellers as $seller)
    <div class="top-seller__single">
        <div class="top-seller__single-item">
            <a href="{{ route('seller.profile', $seller->username) }}" class="item-link"></a>
            <span class="text--gradient"># {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <div class="thumb">
                <img class="author" src="{{ getImage(getFilePath('userProfile') . '/' . $seller->image, avatar: true) }}"
                    alt="image">
                <img class="verified-badge" src="{{ asset($activeTemplateTrue . 'images/thumbs/badge.svg') }}"
                    alt="image">
            </div>
            <div class="author-info">
                <h6 class="title">
                    <a href="{{ route('seller.profile', $seller->username) }}">{{ '@'.$seller->username }}</a>
                </h6>
                <span class="fs-14">{{ $seller->total_sales }} {{ __(Str::plural('Sale', $seller->total_sales)) }}</span>
                <div class="author-info__follower">
                    <span>{{ $seller->followers_count }} @lang('Followers')</span>
                    <span>|</span>
                    <span>{{ $seller->following_count }} @lang('Following')</span>
                </div>
            </div>
        </div>
    </div>
@empty
    @include($activeTemplate . 'partials.empty', ['message' => 'Seller not found!'])
@endforelse
