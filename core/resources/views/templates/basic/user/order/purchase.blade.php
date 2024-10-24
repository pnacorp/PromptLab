@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="card custom--card">
        <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>

            <form>
                <div class="input-group">
                    <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Order No.')">
                    <button class="input-group-text text-white">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0 table-responsive">
            @if (!blank($orders))
                <table class="table table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Order No.')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Payment Status')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_no }}</td>

                                <td>{{ showAmount($order->total) }}</td>

                                <td>
                                    @php echo $order->statusBadge; @endphp
                                </td>

                                <td>
                                    @php echo $order->paymentStatusBadge; @endphp
                                </td>

                                <td>
                                    {{ diffForHumans($order->created_at) }}
                                </td>

                                <td class="text-end">
                                    <div>
                                        @if (!$order->payment)
                                            <a href="{{ route('user.deposit.index', encrypt($order->id)) }}" class="btn btn--sm h-36 btn--info ms-1">
                                                <i class="las la-credit-card"></i> @lang('Pay')
                                            </a>
                                        @endif

                                        <a href="{{ route('user.purchase.details', $order->order_no) }}" class="btn btn--sm btn--base">
                                            <i class="la la-desktop"></i> @lang('Detail')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @include($activeTemplate . 'partials.empty', [
                    'message' => 'No Order found',
                ])
            @endif
        </div>

        @if ($orders->hasPages())
            <div class="card-footer">
                {{ paginateLinks($orders) }}
            </div>
        @endif
    </div>

@endsection
