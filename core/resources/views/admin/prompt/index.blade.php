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
                                    <th>@lang('Prompt Title')</th>
                                    <th>@lang('Owner')</th>
                                    <th>@lang('Sale')</th>
                                    <th>@lang('View')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('AI Tool')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Is Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prompts as $prompt)
                                    <tr>
                                        <td class="text-start">
                                            <div class="d-flex flex-wrap justify-content-end justify-content-lg-start">
                                                <span class="avatar avatar--xs me-2">
                                                    <img
                                                         src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$prompt->image) }}">
                                                </span>
                                                <span>
                                                    {{ strLimit(__($prompt->title), 25) }}

                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ __($prompt->user->fullname) }} <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $prompt->user->id) }}"><span>@</span>{{ $prompt->user->username }}</a>
                                            </span>
                                        </td>

                                        <td>
                                            {{ $prompt->sales_count }}
                                        </td>

                                        <td>{{ $prompt->views }}</td>
                                        <td>{{ __($prompt->category->name?? '') }}</td>

                                        <td>
                                            {{ __($prompt->tool->name) }} @if($prompt->tool_version_id)({{ __($prompt->toolVersion->name?? '') }}) @endif
                                        </td>

                                        <td>@php echo $prompt->promptStatusBadge; @endphp</td>

                                        <td>
                                            @if ($prompt->is_featured == Status::PROMPT_FEATURED)
                                                <span class="badge badge--info"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning"> @lang('No')</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.prompt.details', $prompt->slug) }}">
                                                    <i class="las la-desktop"></i> @lang('Details')
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
                @if ($prompts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($prompts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form promptIsPublished="yes" placeholder="search..." />
@endpush

@push('script')
    <script>
        $('select').on('change', function() {
            let form = $('.filter');
            form.submit();
        });
    </script>
@endpush
