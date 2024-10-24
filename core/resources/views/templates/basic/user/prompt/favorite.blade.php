@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 py-2">{{ __($pageTitle) }}</h6>
        </div>
        <div class="card-body table-responsive p-0">
            @if (!blank($prompts))
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Title')</th>
                            <th>@lang('Tool')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prompts as $prompt)
                            <tr>
                                <td>
                                    <div class="customer">
                                        <div class="customer__thumb">
                                            <a href="{{ route('prompt.details', $prompt->slug) }}">
                                                <img src="{{ getImage(getFilePath('prompt') . '/' . 'thumb_' . @$prompt->image) }}" alt="image">
                                            </a>
                                        </div>
                                        <div class="customer__content">
                                            <h4 class="customer__name">
                                                <a href="{{ route('prompt.details', $prompt->slug) }}">{{ strLimit( __($prompt->title), 40) }}</a>
                                            </h4>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-tag style-three">
                                        <i class="fa-solid fa-wand-sparkles"></i>
                                        <span class="text-white">{{ __(@$prompt->tool->name) }}</span>
                                    </span>
                                </td>
                                <td>
                                    {{ __($prompt->category->name) }}
                                </td>
                                <td>{{ showAmount($prompt->price) }}</td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-end ali gap-2">
                                        <span><a href="{{ route('user.remove.favorite', $prompt->id) }}" class="btn btn-outline-danger btn--sm"><i class="la la-trash-alt"></i></a></span>

                                        <a class="btn btn-outline--base btn-add-to-cart btn--sm" data-prompt-id="{{ $prompt->id }}"><i class="la la-shopping-cart"></i></a>
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
                @include($activeTemplate . 'partials.empty', ['message' => 'No prompt found'])
            @endif
        </div>
        @if ($prompts->hasPages())
            <div class="card-footer">
                {{ paginateLinks($prompts) }}
            </div>
        @endif
    </div>

    <div class="modal custom--modal" id="confirmationModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="completeForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0"> @lang('Confirmation alert!')</h5>
                            <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <p class="py-3 question"></p>
                        <div class="text-end">
                            <button class="btn btn--sm btn--secondary btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                            <button class="btn btn--sm btn--base btn--sm" type="submit">@lang('Yes')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include($activeTemplate . 'partials.wishlist-script')
