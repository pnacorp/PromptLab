@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="card custom--card">
        <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a class="btn btn--base" href="{{ route('user.prompt.create') }}"> <i class="las la-plus"></i> @lang('New Prompt')</a>
                @if (!blank($prompts))
                    <form>
                        <div class="input-group">
                            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Title') / @lang('Tool') / @lang('Category')">
                            <button class="input-group-text text-white">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            @if (!blank($prompts))
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Title')</th>
                            <th>@lang('Sales')</th>
                            <th>@lang('Views')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('Tool')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Is Complete')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prompts as $prompt)
                            <tr>
                                <td>
                                    <span>
                                        <a href="{{ route('prompt.details', $prompt->slug) }}" class="text-white">{{ strLimit(__($prompt->title), 60) }} </a>
                                    </span>
                                </td>

                                <td>
                                    {{ $prompt->sales_count }}
                                </td>
                                <td>{{ $prompt->views }}</td>
                                <td>
                                    {{ __($prompt->category->name) }}
                                </td>
                                <td>
                                    <span class="badge-tag style-three">
                                        <i class="fa-solid fa-wand-sparkles"></i>
                                        <span class="text-white">{{ __(@$prompt->tool->name) }}</span>
                                    </span>
                                </td>

                                <td>{{ showAmount($prompt->price) }}</td>

                                <td>
                                    @if ($prompt->step >= 3)
                                        <span class="badge custom--badge badge--success">
                                            @lang('Yes')</span>
                                    @else
                                        <span class="badge custom--badge badge--warning">
                                            @lang('No')</span>
                                    @endif
                                </td>

                                <td>
                                    @php echo $prompt->promptStatusBadge; @endphp
                                </td>
                                <td class="text-center">
                                    <div class="user-info table-action-btn">
                                        <a class="btn btn--sm btn-outline--base " href="{{ route('user.prompt.step2.edit', $prompt->slug) }}">
                                            <i class="la la-pencil"></i>
                                            @lang('Edit')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                @include($activeTemplate . 'partials.empty', [
                    'message' => 'No prompt found',
                ])
            @endif
        </div>
        @if ($prompts->hasPages())
            <div class="card-footer">
                {{ paginateLinks($prompts) }}
            </div>
        @endif
    </div>
@endsection
