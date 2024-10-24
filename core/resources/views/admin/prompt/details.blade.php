@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic-details" type="button">@lang('Basic Details')</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#description" type="button">@lang('Description')</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#prompt" type="button">@lang('Prompt')</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#testing-prompt" type="button">@lang('Testing Prompt')</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instruction" type="button">@lang('Instruction')</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#media-content" type="button">@lang('Media Content')</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="basic-details">
                            <div class="mt-4">
                                <ul class="list-group list-group-flush">


                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Status')</span>
                                        @php echo $prompt->promptStatusBadge @endphp
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Prompt Owner')</span>
                                        <h6 class="text--primary"><a href="{{ route('admin.users.detail', $prompt->user_id) }}">{{ $prompt->user->username }}</a>
                                        </h6>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Title')</span>
                                        <h6>{{ __($prompt->title) }}</h6>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Price')</span>
                                        <h6>{{ showAmount($prompt->price) }}</h6>
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Sale')</span>
                                        <h6>{{ __($prompt->sales_count) }}</h6>
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('View')</span>
                                        <h6>{{ __($prompt->views) }}</h6>
                                    </li>

                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('Category')</span>
                                        <h6>{{ __($prompt->category->name) }}</h6>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="text--muted">@lang('AI Tool')</span>
                                        <h6>{{ __($prompt->tool->name) }} @if ($prompt->tool_version_id)
                                                ({{ __($prompt->toolVersion->name ?? '') }})
                                            @endif
                                        </h6>
                                    </li>

                                    @if ($prompt->tags)
                                        <li class="list-group-item">
                                            <span>@lang('Tags')</span>

                                            <div>
                                                @foreach ($prompt->tags as $tags)
                                                    <span class="badge badge--primary">{{ $tags }}</span>
                                                @endforeach

                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="description">
                            <div class="mt-4">
                                @php echo $prompt->description; @endphp
                            </div>
                        </div>

                        <div class="tab-pane fade" id="prompt">
                            <div class="mt-4">
                                @php echo $prompt->prompt; @endphp
                            </div>
                        </div>

                        <div class="tab-pane fade" id="testing-prompt">
                            <div class="mt-4">
                                @php echo $prompt->testing_details; @endphp
                            </div>
                        </div>

                        <div class="tab-pane fade" id="instruction">
                            <div class="mt-4">
                                @php echo $prompt->instruction; @endphp
                            </div>
                        </div>

                        <div class="tab-pane fade" id="media-content">
                            <div class="mt-4">
                                <div class="row gy-4">
                                    @foreach ($allImages as $image)
                                        <div class="col-md-3">
                                            <div class="gallery-card">
                                                <a class="view-btn" data-rel="lightcase:myCollection" href="{{ getImage(getFilePath('prompt') . '/' . $image, getFileSize('prompt')) }}">
                                                    <i class="las la-image"></i></a>
                                                <img class="w-100" src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . $image) }}" alt="image">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    @if ($prompt->status == Status::PROMPT_PENDING || $prompt->status == Status::PROMPT_APPROVED)
        <button class="btn btn-outline--danger confirmationBtn" data-action="{{ route('admin.prompt.reject', $prompt->id) }}" data-question="@lang('Are you sure to reject this prompt')?" type="button">
            <i class="la la-times"></i> @lang('Reject')
        </button>
    @endif
    @if ($prompt->status == Status::PROMPT_REJECTED || $prompt->status == Status::PROMPT_PENDING)
        <button class="btn btn-outline--success confirmationBtn" data-action="{{ route('admin.prompt.approve', $prompt->id) }}" data-question="@lang('Are you sure to approve this prompt')?" type="button">
            <i class="la la-check"></i> @lang('Approve')
        </button>
    @endif

    @if ($prompt->status !== Status::PROMPT_REJECTED)
        @if ($prompt->is_featured == Status::PROMPT_FEATURED)
            <button class="btn btn-outline--warning confirmationBtn" data-question="@lang('Are you sure to unfeature this prompt?')" data-action="{{ route('admin.prompt.feature', $prompt->id) }}" type="button">
                <i class="la la-times"></i> @lang('Unfeatured')
            </button>
        @else
            <button class="btn btn-outline--success confirmationBtn" data-question="@lang('Are you sure to feature this prompt?')" data-action="{{ route('admin.prompt.feature', $prompt->id) }}" type="button">
                <i class="la la-check"></i> @lang('Featured')
            </button>
        @endif
    @endif
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/vendor/lightcase.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/lightcase.js') }}"></script>
@endpush

@push('style')
    <style>
        .list-group-item {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: .8rem 0;
            border: 1px solid #f1f1f1;
        }

        .accordion-button:not(.collapsed) {
            box-shadow: none !important;
        }

        .gallery-card {
            position: relative;
        }

        .gallery-card:hover .view-btn {
            opacity: 1;
            visibility: visible;
        }

        .gallery-card .view-btn {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.364);
            color: #f0e9e9;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            font-size: 42px;
            opacity: 0;
            visibility: none;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
        }

        .thumb i {
            font-size: 22px;
        }

        .lightcase-icon-prev:before {
            content: '\f104' !important;
            font-family: 'Line Awesome Free' !important;
            font-weight: 900 !important;
        }

        .lightcase-icon-next:before {
            content: '\f105' !important;
            font-family: 'Line Awesome Free' !important;
            font-weight: 900 !important;
        }

        .lightcase-icon-close:before {
            content: '\f00d' !important;
            font-family: 'Line Awesome Free' !important;
            font-weight: 900 !important;
        }

        .lightcase-icon-prev,
        .lightcase-icon-next,
        .lightcase-icon-close {
            border: 1px solid #ddd;
            font-size: 22px !important;
            width: 50px !important;
            height: 50px !important;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background-color: #ffffff0f;
        }

        hr {
            border-color: #b7b7b7 !important;
        }
    </style>
@endpush
@push('script')
    <script>
        'use strict';
        $('a[data-rel^=lightcase]').lightcase();
    </script>
@endpush
