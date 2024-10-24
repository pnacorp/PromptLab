@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="top-seller-area pt-60 pb-60">
        <div class="container">
            <div class="top-seller__wrapper">
                @include($activeTemplate . 'partials.top_seller', [
                    'topSellers' => $topSellers,
                ])
            </div>

            <div class="mt-5">
                {{ paginateLinks($topSellers) }}
            </div>
        </div>
    </section>
@endsection
