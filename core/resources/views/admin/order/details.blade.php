@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-md-6">
            <ul class="list-group text-center">
                <li class="list-group-item d-flex justify-content-between">
                    <span>@lang('Order No.')</span>
                    <span>{{ __($order->order_no) }}</span>
                </li>
                @if (@$order->coupon_code)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Coupon Code')</span>
                        <span>{{ @$order->coupon_code }}</span>
                    </li>
                @endif

                @if (@$order->payment->trx)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Payment TRX. No.')</span>
                        <span>{{ @$order->payment->trx }}</span>
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
            <ul class="list-group text-center">
                <li class="list-group-item d-flex justify-content-between">
                    <span>@lang('Order Amount')</span>
                    <span>{{ showAmount(@$order->subtotal) }}</span>
                </li>
                @if (@$order->coupon_code)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Discount')</span>
                        <span>{{ showAmount(@$order->discount) }}</span>
                    </li>
                @endif
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
                    @php echo $order->paymentStatusBadge; @endphp
                </li>
            </ul>

        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Prompt Title')</th>
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Seller')</th>
                                    <th>@lang('Tool')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Commission')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderDetails as $detail)
                                    <tr>
                                        <td>
                                            <h6 class="fs--16px">
                                                <a href="{{ route('admin.prompt.details', $detail->prompt->slug) }}">{{ strLimit(__(@$detail->prompt->title), 35) }}</a>
                                            </h6>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $detail->order->user->id) }}"><span>@</span>{{ $detail->order->user->username }}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $detail->seller->id) }}"><span>@</span>{{ $detail->seller->username }}</a>
                                        </td>
                                        <td>
                                            <strong>{{ __(@$detail->prompt->tool->name) }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ __(@$detail->prompt->category->name) }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ showAmount($detail->price) }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ showAmount($detail->charge) }}</strong>
                                        </td>
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
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.order.index') }}" />
@endpush
