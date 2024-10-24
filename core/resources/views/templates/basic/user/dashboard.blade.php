@php
    $kyc = getContent('kyc.content', true);
@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="dashboard-widget-wrapper">
        <div class="notice"></div>

        @if ($user->kv == Status::KYC_UNVERIFIED || $user->kv == Status::KYC_PENDING)
            <div class="project-area mb-5">
                @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
                    <div class="alert custom--alert custom--alert--danger" role="alert">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="alert-heading mb-0">@lang('KYC Documents Rejected')</h5>
                            <button class="btn btn--sm btn-outline--base" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reason')</button>
                        </div>
                        <hr>
                        <p class="mb-0">{{ @$kyc->data_values->reject }} <a class="text--base" href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.</p>
                        <br>
                        <a class="text--base" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                    </div>
                @elseif ($user->kv == Status::KYC_UNVERIFIED)
                    <div class="alert custom--alert custom--alert--info" role="alert">
                        <h5 class="alert-heading mb-0">@lang('KYC Verification required')</h5>
                        <hr>
                        <p class="mb-0">{{ __(@$kyc->data_values->required) }}<a class="border-effect text--base" href="{{ route('user.kyc.form') }}"> @lang('Click Here to Verify')</a></p>
                    </div>
                @elseif($user->kv == Status::KYC_PENDING)
                    <div class="alert custom--alert custom--alert--warning" role="alert">
                        <h5>@lang('KYC Verification pending')</h5>
                        <hr>
                        <i>{{ __(@$kyc->data_values->pending) }} <a class="border-effect text--base" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></i>
                    </div>
                @endif
            </div>
        @endif
        <div class="row gy-4">
            <div class="col-lg-6 col-sm-6">
                <div class="dashboard-widget position-relative">
                    <a href="{{ route('user.transactions') }}" class="position-absolute h-100 w-100"></a>
                    <div class="dashboard-widget__header">
                        <h5 class="title">@lang('Balance') </h5>
                        <div class="dashboard-widget__icon">
                            <i class="las la-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="dashboard-widget__content">
                        <h4 class="amount">{{ showAmount($user->balance) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="dashboard-widget position-relative">
                    <a href="{{ route('user.withdraw.history') }}" class="position-absolute h-100 w-100"></a>
                    <div class="dashboard-widget__header">
                        <h5 class="title">@lang('Total Withdraw') </h5>
                        <div class="dashboard-widget__icon">
                            <i class="las la-wallet"></i>
                        </div>
                    </div>
                    <div class="dashboard-widget__content">
                        <h4 class="amount">{{ showAmount($totalWithdraw) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="dashboard-widget position-relative">
                    <a href="{{ route('user.sale.history') }}" class="position-absolute h-100 w-100"></a>
                    <div class="dashboard-widget__header">
                        <h5 class="title">@lang('Total Sold')</h5>
                        <div class="dashboard-widget__icon">
                            <i class="las la-money-check-alt"></i>
                        </div>
                    </div>

                    <div class="dashboard-widget__content">
                        <h4 class="amount">{{ showAmount($user->total_sold) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="dashboard-widget position-relative">
                    <a href="{{ route('user.deposit.history') }}" class="position-absolute h-100 w-100"></a>
                    <div class="dashboard-widget__header">
                        <h5 class="title">@lang('Total Payment') </h5>
                        <div class="dashboard-widget__icon">
                            <i class="las la-comment-medical"></i>
                        </div>
                    </div>
                    <div class="dashboard-widget__content">
                        <h4 class="amount">{{ showAmount($totalDeposit) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="custom--card card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('Sales Report')</h4>
                    </div>
                    <div class="card-body">
                        <div id="salesChart"></div>
                    </div>
                </div>
            </div>

            @if (!blank($sales))
                <div class="col-lg-12">
                    <div class="custom--card card">
                        <div class="card-header d-flex flex-wrap justify-content-between gap-3">
                            <h4 class="card-title">@lang('Latest Sales')</h4>
                            <div class="card-heading__right">
                                <a class="btn btn--sm btn--base" href="{{ route('user.sale.history') }}">@lang('View All')</a>
                            </div>
                        </div>
                        <div class="card-body p-0">

                            <div class="dashboard-table table-responsive">
                                <table class="table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Prompts')</th>
                                            <th>@lang('Order Date')</th>
                                            <th>@lang('Price')</th>
                                            <th>@lang('Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sales as $sale)
                                            <tr>
                                                <td>
                                                    <h6 class="title fs-14"><a href="{{ route('prompt.details', $sale->prompt->slug) }}">{{ strLimit(__($sale->prompt->title), 40) }}</a>
                                                    </h6>
                                                </td>
                                                <td>
                                                    <span>{{ diffForHumans($sale->created_at) }}</span>
                                                </td>
                                                <td>{{ showAmount($sale->prompt->price) }}</td>
                                                <td>@php echo $sale->order->statusBadge; @endphp</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-lg-12">
                <div class="custom--card card">
                    <div class="card-header">
                        <h4 class="title">
                            @lang('Top Views Prompts')
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="viewsChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal custom--modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i> </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
@endpush
@push('script')
    <script>
        "use strict";
        (function($) {
            document.addEventListener("DOMContentLoaded", function() {
                function fetchSalesData() {
                    fetch(`{{ route('user.getSalesData') }}`)
                        .then(response => response.json())
                        .then(data => {
                            const options = {
                                chart: {
                                    type: 'line',
                                    height: 350,
                                    toolbar: {
                                        show: false
                                    },
                                    dropShadow: {
                                        enabled: true,
                                        top: 5,
                                        left: 0,
                                        blur: 5,
                                        opacity: 0.1
                                    }
                                },
                                series: [{
                                    name: 'Total Sales',
                                    data: data.map(item => item.total_sales)
                                }],
                                xaxis: {
                                    categories: data.map(item => item.date),
                                    type: 'datetime',
                                    labels: {
                                        datetimeUTC: false,
                                        style: {
                                            colors: '#ffffff',
                                            fontSize: '12px',
                                        }
                                    },
                                    tooltip: {
                                        enabled: false
                                    }
                                },
                                yaxis: {
                                    title: {
                                        text: 'Sales Amount',
                                        style: {
                                            color: '#ffffff',
                                            fontSize: '12px'
                                        }
                                    },
                                    labels: {
                                        offsetX: 10,
                                        style: {
                                            colors: '#ffffff',
                                        },
                                        formatter: function(value) {
                                            return "$" + value.toFixed(2);
                                        }
                                    },
                                    axisBorder: {
                                        show: false
                                    },
                                    axisTicks: {
                                        show: false
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                tooltip: {
                                    enabled: true,
                                    followCursor: true,
                                    theme: 'dark',
                                    x: {
                                        format: 'dd MMM'
                                    },
                                    y: {
                                        formatter: function(value) {
                                            return "$" + value.toFixed(2);
                                        }
                                    },
                                    fixed: {
                                        enabled: true,
                                        position: 'topRight'
                                    }
                                },
                                grid: {
                                    borderColor: '#333',
                                    show: true,
                                    padding: {
                                        top: 0,
                                        right: 0,
                                        bottom: 0,
                                        left: 0
                                    },
                                    xaxis: {
                                        lines: {
                                            show: false
                                        }
                                    },
                                    yaxis: {
                                        lines: {
                                            show: false
                                        }
                                    }
                                }
                            };

                            const chart = new ApexCharts(document.querySelector("#salesChart"), options);
                            chart.render();
                        });
                }

                function fetchPromptViewData() {
                    fetch(`{{ route('user.getViewData') }}`)
                        .then(response => response.json())
                        .then(data => {
                            const options = {
                                chart: {
                                    type: 'bar',
                                    height: 350,
                                    toolbar: {
                                        show: false
                                    },
                                    dropShadow: {
                                        enabled: true,
                                        top: 5,
                                        left: 0,
                                        blur: 5,
                                        opacity: 0.1
                                    }
                                },
                                colors: ["#{{ gs('base_color') }}"],
                                series: [{
                                    name: 'Total Views',
                                    data: data.map(item => item.total_views)
                                }],
                                xaxis: {
                                    categories: data.map(item => item.prompt_title),
                                    labels: {
                                        style: {
                                            colors: '#fff',
                                            fontSize: '12px',
                                        }
                                    },
                                    tooltip: {
                                        enabled: false
                                    }
                                },
                                yaxis: {
                                    title: {
                                        text: 'Views Count',
                                        style: {
                                            color: '#fff',
                                            fontSize: '12px'
                                        }
                                    },
                                    labels: {
                                        style: {
                                            colors: '#fff',
                                        },
                                        formatter: function(value) {
                                            return value.toFixed(0);
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                tooltip: {
                                    enabled: true,
                                    followCursor: true,
                                    theme: 'dark',
                                    x: {
                                        format: 'dd MMM'
                                    },
                                    y: {
                                        formatter: function(value) {
                                            return value.toFixed(0);
                                        }
                                    },
                                    fixed: {
                                        enabled: true,
                                        position: 'topRight'
                                    }
                                },
                                grid: {
                                    borderColor: '#333'
                                }
                            };

                            const chart = new ApexCharts(document.querySelector("#viewsChart"), options);
                            chart.render();
                        });
                }

                fetchSalesData();
                fetchPromptViewData();
            });
        })(jQuery);
    </script>
@endpush
