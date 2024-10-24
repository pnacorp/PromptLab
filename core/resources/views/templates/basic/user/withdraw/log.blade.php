@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="card custom--card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>

            <div class="d-flex flex-wrap gap-3 align-items-center">
                <a class="btn btn--base" href="{{ route('user.withdraw') }}"> <i class="las la-wallet"></i> @lang('Withdraw Now')</a>

                @if (!blank($withdraws))
                    <form>
                        <div class="d-flex justify-content-end">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('TRX. No.')">
                                <button class="input-group-text text-white">
                                    <i class="las la-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

        </div>
        <div class="card-body table-responsive p-0">
            @if (!blank($withdraws))
                <table class="table table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Gateway | TRX. No.')</th>
                            <th class="text-center">@lang('Initiated')</th>
                            <th class="text-center">@lang('Amount')</th>
                            <th class="text-center">@lang('Conversion')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($withdraws as $withdraw)
                            @php
                                $details = [];
                                foreach ($withdraw->withdraw_information as $key => $info) {
                                    $details[] = $info;
                                    if ($info->type == 'file') {
                                        $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold"><span class="text--base">
                                            {{ __(@$withdraw->method->name) }}</span></span>
                                    <br>
                                    <small>{{ $withdraw->trx }}</small>
                                </td>
                                <td class="text-center">
                                    {{ showDateTime($withdraw->created_at) }} <br>
                                    {{ diffForHumans($withdraw->created_at) }}
                                </td>
                                <td class="text-center">
                                    {{ showAmount($withdraw->amount) }} - <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($withdraw->charge) }}
                                    </span>
                                    <br>
                                    <strong data-bs-toggle="tooltip" title="@lang('Amount after charge')">
                                        {{ showAmount($withdraw->amount - $withdraw->charge) }}
                                    </strong>

                                </td>
                                <td class="text-center">
                                    {{ showAmount(1) }} =
                                    {{ showAmount($withdraw->rate, currencyFormat: false) }}
                                    {{ __($withdraw->currency) }}
                                    <br>
                                    <strong>{{ showAmount($withdraw->final_amount, currencyFormat: false) }}
                                        {{ __($withdraw->currency) }}</strong>
                                </td>
                                <td class="text-center">
                                    @php echo $withdraw->statusBadge @endphp
                                </td>
                                <td>
                                    <button class="btn btn--sm btn--base detailBtn" data-user_data="{{ json_encode($details) }}" @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                        <i class="la la-desktop"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">
                                    {{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                @include($activeTemplate . 'partials.empty', [
                    'message' => 'No withdraw found',
                ])
            @endif
        </div>
        @if ($withdraws->hasPages())
            <div class="card-footer">
                {{ paginateLinks($withdraws) }}
            </div>
        @endif
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal custom--modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData withdrawal-list">

                    </ul>
                    <div class="feedback"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    } else {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush
