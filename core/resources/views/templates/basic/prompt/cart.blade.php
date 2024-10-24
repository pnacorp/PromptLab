@php
    $coupon = session()->get('coupon');
    $discount = $coupon['discount'] ?? 0;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cart-section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <div class="cart-card__header d-flex justify-content-between gap-3 mb-4">
                            <h6 class="title fs-20 mb-0">@lang('Cart') <span
                                    class="prompt-count">({{ $cartItems->count() }} @lang('Prompt'))</span></h6>
                            @if (!blank($cartItems))
                                <button class="clear-btn text--danger fs-14 confirmationBtn" data-question="@lang('Are you sure to delete all cart item?')"
                                    data-action="{{ route('cart.clear') }}" type="button"><i
                                        class="fa-solid fa-xmark"></i>
                                    @lang('Clear Cart')</button>
                            @endif
                        </div>
                        @forelse($cartItems as $cartItem)
                            <div class="cart-item">
                                <div class="cart-item__thumb">
                                    <img src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$cartItem->prompt->image) }}"
                                        alt="img">
                                </div>
                                <div class="cart-item__content">
                                    <div class="cart-item__left">
                                        <h5 class="title mb-1">
                                            {{ __($cartItem->prompt->title) }}
                                        </h5>
                                        <p class="desc">{{ strLimit($cartItem->prompt->description, 50) }}</p>
                                        <a href="#" class="text--gradient"><i class="fa-solid fa-wand-sparkles"></i>
                                            {{ __($cartItem->prompt->tool->name) }}</a>
                                    </div>
                                    <div class="cart-item__right">
                                        <button class="delete-btn confirmationBtn" data-question="@lang('Are you sure to delete this item?')"
                                            data-action="{{ route('cart.delete', $cartItem->id) }}" type="button"><i
                                                class="fa-regular fa-trash-can"></i></button>
                                        <h4 class="cart-price">{{ showAmount($cartItem->prompt->price) }}</h4>
                                    </div>
                                </div>
                            </div>
                        @empty
                            @include($activeTemplate . 'partials.empty', [
                                'message' => 'Cart empty!',
                            ])
                        @endforelse
                    </div>
                </div>
                <div class="col-lg-4">
                    <form action="{{ route('user.checkout.order') }}" method="post">
                        @csrf
                        <div class="card-sidebar">
                            <div class="cart-card__header d-flex justify-content-between gap-3 mb-4">
                                <h6 class="title fs-20 mb-0">@lang('Order Summery')</h6>
                            </div>
                            <div class="input-group form-wrpper mb-4 gap-2">
                                <div class="search-form flex-grow-1">
                                    <input type="text" name="coupon" class="form--control form-control coupon-field"
                                        placeholder="Apply promo code..." autocomplete="off" value="{{ @$coupon['code'] }}">
                                    <span class="search-form__btn"><i class="fa-solid fa-tag"></i></span>
                                </div>
                                    <button
                                        class="btn btn--apply btn--base h-100 flex-grow-1 remove-btn @if (!$coupon) d-none @endif @if (blank($cartItems)) disabled @endif"
                                        type="submit">@lang('Remove')</button>
                                    <button
                                        class="btn btn--apply flex-grow-1 h-100 apply-coupon @if ($coupon) d-none @endif @if (blank($cartItems)) disabled @endif"
                                        type="submit">@lang('Apply')</button>
                            </div>
                            <div class="cart-data">
                                <ul class="cart-data__list">
                                    <li><span class="title">@lang('Subtotal')</span> <span
                                            class="price">{{ gs('cur_sym') }}{{ showAmount($total, currencyFormat: false) }}</span>
                                    </li>
                                    <li><span class="title">@lang('Discount')</span> <span
                                            class="text--danger price discount">
                                            {{ gs('cur_sym') }}{{ showAmount($discount, currencyFormat: false) }}</span>
                                    </li>
                                </ul>
                                <div class="cart-data__total mb-4">
                                    <span class="text">
                                        @lang('Total')
                                    </span>
                                    <span class="price grand-total">
                                        {{ gs('cur_sym') }}{{ showAmount($subtotal - $discount, currencyFormat: false) }}
                                    </span>
                                </div>
                            </div>
                            <button
                                class="btn btn--base btn--lg w-100 @if (blank($cartItems)) disabled @endif">@lang('Order Now')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade custom--modal" id="removeCouponModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title text-white">@lang('Confirmation Alert!')</strong>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to remove this coupon?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline--base btn--sm"
                        data-bs-dismiss="modal">@lang('No')</button>
                    <button type="button" class="btn btn--base btn--sm remove-coupon">@lang('Yes')</button>
                </div>
            </div>
        </div>
    </div>

    <x-frontend-confirmation-modal />
@endsection


@push('script')
    <script>
        (function($) {
            'use script';
            let subtotal = parseFloat("{{ $subtotal }}");
            let discount = 0;
            let grandTotal = 0;
            let curSymbol = `{{ gs()->cur_sym }}`;

            $('.apply-coupon').on('click', function(e) {
                e.preventDefault();

                let coupon = $('[name=coupon]').val();
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    method: "POST",
                    url: "{{ route('user.coupon.apply') }}",
                    data: {
                        coupon: coupon
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                        } else {
                            notify('success', response.success);
                            $('.apply-coupon').hide();
                            $('.remove-btn').removeClass('d-none');
                            $('[name=coupon]').prop('disabled', true);
                        }
                        discount = parseFloat(response.coupon.discount);
                        setGrandTotal();
                    },

                });
            });

            $('.remove-btn').on('click', function(e) {
                e.preventDefault();


                removeableItem = $(this).closest("tr");
                modal = $('#removeCouponModal');
                modal.modal('show');
            });

            $('.remove-coupon').on('click', function(e) {
                e.preventDefault();


                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    method: "POST",
                    url: "{{ route('user.remove.coupon') }}",
                    success: function(response) {
                        notify('success', response.success);
                        discount = 0;

                        $('[name=coupon]').val('');
                        $('.remove-btn').addClass('d-none');
                        $('.apply-coupon').show();

                        setGrandTotal();
                    },

                });

                modal.modal('hide');
            });

            function setGrandTotal() {
                grandTotal = Number(subtotal) - Number(discount);
                $('.discount').text(`${curSymbol}${discount.toFixed(2)}`);
                $('.grand-total').text(`${curSymbol}${grandTotal.toFixed(2)}`);
            }

        })(jQuery);
    </script>
@endpush
