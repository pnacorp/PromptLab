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
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Minimum Order Amount')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Start Date')</th>
                                    <th>@lang('Validity')</th>
                                    <th>@lang('Status')</th>
                                    <th> @lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupons->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span>{{ __($coupon->code) }}</span>
                                        </td>
                                        <td>
                                            <span class="name">{{ showAmount($coupon->min_order) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="name">{{ showAmount($coupon->amount, currencyFormat: false) }}</span>
                                            <span>
                                                @if ($coupon->discount_type == Status::FIXED)
                                                    {{ gs()->cur_text }}
                                                @else
                                                    @lang('%')
                                                @endif
                                            </span>
                                        </td>

                                        <td>
                                            {{ $coupon->start_date }}
                                        </td>

                                        <td class="{{$coupon->end_date < now()->format('Y-m-d') ? 'text--danger': ''}}">
                                            {{ $coupon->end_date }}
                                        </td>

                                        <td>
                                            @php echo $coupon->statusBadge; @endphp
                                        </td>

                                        <td>
                                            <button class="btn btn-sm btn-outline--primary cuModalBtn"
                                                data-modal_title="@lang('Update Category')" data-resource="{{ $coupon }}">
                                                <i class="la la-pen"></i> @lang('Edit')
                                            </button>
                                            @if ($coupon->status == Status::ENABLE)
                                                <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                    data-question="@lang('Are you sure to disable this coupon?')"
                                                    data-action="{{ route('admin.coupon.status', $coupon->id) }}">
                                                    <i class="la la-eye-slash"></i>@lang('Disable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                    data-question="@lang('Are you sure to enable this coupon?')"
                                                    data-action="{{ route('admin.coupon.status', $coupon->id) }}">
                                                    <i class="la la-eye"></i>@lang('Enable')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($coupons->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($coupons) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" tabindex="-1" role="dialog" aria-labelledby="createCouponLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="createCouponLabel"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="las la-times"></i></button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.coupon.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row form-group">
                            <label>@lang('Code')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" value="{{ old('code') }}" name="code"
                                    required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label>@lang('Amount')</label>
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control" required
                                        value="{{ old('amount') }}">
                                    <select name="discount_type" class="input-group-text">
                                        <option value="1">{{ __(gs()->cur_text) }}</option>
                                        <option value="2">@lang('%')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label>@lang('Minimum Order')</label>
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input type="number" step="any" name="min_order" class="form-control" required
                                        value="{{ old('min_order') }}">
                                    <div class="input-group-text">{{ gs()->cur_text }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label>@lang('Start Date')<span class="text--danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="datepicker-here form-control" data-language='en'
                                    data-date-format="yyyy-mm-dd" data-position='bottom left'
                                    placeholder="@lang('Select date')" name="start_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label>@lang('End Date')<span class="text--danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="datepicker-here form-control" data-language='en'
                                    data-date-format="yyyy-mm-dd" data-position='bottom left'
                                    placeholder="@lang('End date')" name="end_date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add Category')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.datepicker-here').datepicker();

        })(jQuery);
    </script>
@endpush
