@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="text-end">

    </div>
    <div class="custom--card card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>
            <a href="{{ route('ticket.open') }}" class="btn btn--sm btn--base"> <i class="fas fa-plus"></i>
                @lang('New Ticket')</a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Subject')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Priority')</th>
                        <th>@lang('Last Reply')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supports as $support)
                        <tr>
                            <td> <a href="{{ route('ticket.view', $support->ticket) }}" class="text-muted fw-bold">
                                    [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}
                                </a></td>
                            <td>
                                @php echo $support->statusBadge; @endphp
                            </td>
                            <td>
                                @if ($support->priority == Status::PRIORITY_LOW)
                                    <span class="badge badge--dark">@lang('Low')</span>
                                @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                    <span class="badge  badge--warning">@lang('Medium')</span>
                                @elseif($support->priority == Status::PRIORITY_HIGH)
                                    <span class="badge badge--danger">@lang('High')</span>
                                @endif
                            </td>
                            <td>{{ diffForHumans($support->last_reply) }} </td>

                            <td>
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--base btn--sm">
                                    <i class="la la-desktop"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ paginateLinks($supports) }}
@endsection
