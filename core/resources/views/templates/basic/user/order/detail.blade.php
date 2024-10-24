@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4 justify-content-center">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <h6 class="mb-0">@lang('Order Details')</h6>
                <a class="btn btn--sm btn--base" href="{{ route('user.purchase.history') }}"><i class="las la-undo"></i> @lang('Back')</a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <ul class="list-group text-center withdrawal-list">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Order No')</span>
                                    <span>{{ $order->order_no }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Total Price')</span>
                                    <span>{{ showAmount(@$order->total) }}</span>
                                </li>

                                @if ($order->payment)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>@lang('Payment Trx')</span>
                                        <span>{{ $order->payment->trx }}</span>
                                    </li>
                                @endif

                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Order Date')</span>
                                    <span>{{ showDateTime($order->created_at) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Order Status')</span>
                                    @php
                                        echo $order->statusBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group text-center withdrawal-list">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Subtotal')</span>
                                    <span>{{ showAmount(@$order->subtotal) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Discount')</span>
                                    <span>{{ showAmount(@$order->discount) }}</span>
                                </li>

                                @if (@$order->payment->trx)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>@lang('Payment Processing Charge')</span>
                                        <span>{{ showAmount(@$order->payment->charge) }}</span>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Total Paid')</span>
                                    <span>{{ showAmount(@$order->total + @$order->payment->charge) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Payment Status')</span>
                                    @php
                                        echo $order->paymentStatusBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body table-responsive p-0">
                    <table class="table table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('Prompt Title')</th>
                                <th>@lang('Tool')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Price')</th>
                                @if ($order->status == Status::ORDER_COMPLETED)
                                    <th>@lang('Action')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderDetails as $detail)
                                <tr>
                                    <td>
                                        <div class="customer">
                                            <div class="customer__thumb">
                                                <a href="{{ route('prompt.details', $detail->prompt->slug) }}"><img src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$detail->prompt->image) }}" alt="image"></a>
                                            </div>
                                            <div class="customer__content">
                                                <h4 class="customer__name">
                                                    <a href="{{ route('prompt.details', $detail->prompt->slug) }}">{{ __(@$detail->prompt->title) }}</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-tag style-three">
                                            <i class="fa-solid fa-wand-sparkles"></i>
                                            <span class="text-white">{{ __(@$detail->prompt->tool->name) }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        {{ __(@$detail->prompt->category->name) }}
                                    </td>
                                    <td>
                                        <strong>{{ showAmount($detail->price) }}</strong>
                                    </td>
                                    @if ($order->status == Status::ORDER_COMPLETED)
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap align-items-center justify-content-end">
                                                <a class="text--base" href="{{ route('user.prompt.download', $detail->prompt->slug) }}">
                                                    <i class="fas fa-download"></i>
                                                </a>

                                                <button class="btn ms-2 btn--warning btn--sm reviewBtn" data-resource="{{ @$detail->review }}" data-action="{{ route('user.prompt.review', $detail->prompt->slug) }}"><i class="las la-star-of-david"></i>
                                                    @lang('Review')</button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                @include($activeTemplate . 'partials.empty', [
                                    'message' => 'No data found',
                                ])
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Prompt Review')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Your Ratings') :</label>
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
                        <div class="form-group">
                            <label class="mb-3">@lang('Write your opinion')</label>
                            <textarea class="from-control form--control" name="review" rows="5" required></textarea>
                        </div>
                        <button class="btn btn--sm btn--base w-100 submitButton" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        $('.reviewBtn').on('click', function() {
            var reviewModal = $('#reviewModal');
            var resource = $(this).data('resource');
            var action = $(this).data('action');
            reviewModal.find('form').attr('action', action);
            if (resource.rating) {
                reviewModal.find(`[name="rating"][value="${resource.rating}"]`).prop('checked', true);
            } else {
                reviewModal.find(`[name="rating"]`).prop('checked', false);
            }

            reviewModal.find('[name="review"]').val(resource.review || '');

            reviewModal.modal('show');
        });

        $('form').on('submit', function(e) {
            if ($('[name=rating]:checked').val() == undefined) {
                iziToast.error({
                    message: 'Rating is required',
                    position: "topRight"
                });
                e.preventDefault();
                return;
            }
            $(this).submit();

        });
    </script>
@endpush
