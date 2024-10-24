@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="card custom--card">
        <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>
            <form>
                <div class="d-flex justify-content-end">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Prompt Title')">
                        <button class="input-group-text text-white">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            @if (!blank($sales))
                <table class="table table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Prompt')</th>
                            <th>@lang('Tool')</th>
                            <th>@lang('Order Date')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Commisssion')</th>
                            <th>@lang('Received')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>
                                    <div class="customer">
                                        <div class="customer__thumb">
                                            <a href="{{ route('prompt.details', $sale->prompt->slug) }}"><img src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$sale->prompt->image) }}" alt="image"></a>
                                        </div>
                                        <div class="customer__content">
                                            <h4 class="customer__name">
                                                <a href="{{ route('prompt.details', $sale->prompt->slug) }}">{{ __($sale->prompt->title) }}</a>
                                            </h4>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-tag style-three">
                                        <i class="fa-solid fa-wand-sparkles"></i>
                                        <span class="text-white">{{ __(@$sale->prompt->tool->name) }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span>{{ diffForHumans($sale->created_at) }}</span>
                                </td>
                                <td>{{ showAmount($sale->price) }}</td>
                                <td>{{ showAmount($sale->charge) }}</td>
                                <td>{{ showAmount($sale->price - $sale->charge) }}</td>
                                <td>@php echo $sale->order->statusBadge; @endphp</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @include($activeTemplate . 'partials.empty', [
                    'message' => 'No order found',
                ])
            @endif
        </div>
        @if ($sales->hasPages())
            <div class="card-footer">
                {{ paginateLinks($sales) }}
            </div>
        @endif
    </div>

@endsection
