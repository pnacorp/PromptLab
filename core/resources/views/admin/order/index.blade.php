@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order NO')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>
                                            {{ $order->order_no }}
                                        </td>
                                        <td>{{ showAmount($order->total) }}</td>
                                        <td>{{ __($order->user->fullname) }} <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $order->user->id) }}"><span>@</span>{{ $order->user->username }}</a>
                                            </span>
                                        </td>

                                        <td>@php echo $order->statusBadge; @endphp</td>
                                        <td>@php echo $order->paymentStatusBadge; @endphp</td>

                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary"
                                                    href="{{ route('admin.order.details', $order->id) }}">
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form orderIsPublished="yes" placeholder="search..." />
@endpush

@push('script')
    <script>
        $('select').on('change', function() {
            let form = $('.filter');
            form.submit();
        });
    </script>
@endpush
