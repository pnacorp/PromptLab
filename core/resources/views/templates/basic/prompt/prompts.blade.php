@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container pb-60">
        <div class="d-block d-xl-none">
            <button type="button" class="filter-task btn btn--sm btn--base d-inline">
                <i class="las la-filter"></i> @lang('Filter') </button>
        </div>
        <div class="row gy-4">
            <div class="col-xl-3">
                <form id="filterForm">
                    <div class="filter-sidebar">
                        <button class="side-sidebar-close-btn d-block d-xl-none" type="button"><i class="las la-times"></i></button>
                        <div class="filter-sidebar__header">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-3">
                                <h5 class="title mb-0">
                                    @lang('Filter Prompts')
                                </h5>
                                <button type="button" id="clearFilters" class="text-white clearFiltersSection d-none">@lang('Clear Filter')</button>
                            </div>

                        </div>

                        <div class="widget">
                            <div class="widget-header">
                                <h5 class="title">@lang('Sort by')</h5>
                            </div>
                            <div class="widget-body">
                                <ul>
                                    <li class="form-check-item">
                                        <div class="form--check">
                                            <input class="form-check-input" id="trending" name="sort_by" value="trending" type="radio" @if (isset($activeFilter) && $activeFilter === 'trending') checked @endif>
                                            <label class="form-check-label" for="trending">@lang('Trending')</label>
                                        </div>
                                    </li>
                                    <li class="form-check-item">
                                        <div class="form--check">
                                            <input class="form-check-input" id="most" value="featured" name="sort_by" type="radio" @if (isset($activeFilter) && $activeFilter === 'featured') checked @endif>
                                            <label class="form-check-label" for="most">@lang('Featured')</label>
                                        </div>
                                    </li>
                                    <li class="form-check-item">
                                        <div class="form--check">
                                            <input class="form-check-input" id="newest" value="latest" name="sort_by" type="radio">
                                            <label class="form-check-label" for="newest">@lang('Newest')</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if(!Route::is('prompt.tools'))
                        <div class="widget">
                            <div class="widget-header">
                                <h5 class="title">@lang('AI Tool')</h5>
                            </div>
                            <div class="widget-body">
                                <ul>
                                    @foreach ($tools as $tool)
                                        <li class="form-check-item">
                                            <div class="form--check">
                                                <input class="form-check-input filter-checkbox sortTools" id="tool{{ $tool->id }}" value="{{ $tool->id }}" name="tool[]" type="checkbox" @if (isset($activeFilter) && $activeFilter === 'tool' && isset($toolId) && $toolId == $tool->id) checked @endif>
                                                <label class="form-check-label" for="tool{{ $tool->id }}">{{ $tool->name }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        @if(!Route::is('prompt.categories'))
                        <div class="widget">
                            <div class="widget-header">
                                <h5 class="title">@lang('Category')</h5>
                            </div>
                            <div class="widget-body">
                                <ul>
                                    @foreach ($categories as $category)
                                        <li class="form-check-item">
                                            <div class="form--check">
                                                <input class="form-check-input filter-checkbox sortCategory" id="category{{ @$category->id }}" value="{{ @$category->id }}" name="category[]" type="checkbox" @if (isset($activeFilter) && $activeFilter === 'category' && isset($categoryId) && $categoryId == $category->id) checked @endif>
                                                <label class="form-check-label" for="category{{ @$category->id }}">{{ __($category->name) }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="col-xl-9">
                <div class="row gy-4 card-view" id="promptsContainer">
                    @include($activeTemplate . 'partials.prompt', ['prompts' => $prompts])
                </div>
            </div>
        </div>

        <div id="preloader" style="display: none;">
            <div class="spinner"></div>
        </div>
    @endsection

    @push('style')
        <style>
            #preloader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(64, 62, 62, 0.7);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid hsl(var(--card-bg)) !important;
                border-top: 4px solid hsl(var(--base)) !important;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    @endpush

    @push('script')
        <script>
            (function($) {
                "use strict";

                function fetchPrompts() {

                    $('#preloader').show();

                    $.ajax({
                        url: "{{ route('prompt.filter') }}",
                        method: "GET",
                        data: $('#filterForm').serialize(),
                        success: function(response) {
                            $('#promptsContainer').html(response.view);
                        },
                        complete: function() {
                            $('#preloader').hide();
                        }
                    });
                }

                function scrollToTop() {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }


                $('.filter-checkbox.sortCategory').on('change', function() {
                    fetchPrompts();
                    toggleClearFilters();
                    scrollToTop();
                });


                $('.filter-checkbox.sortTools').on('change', function() {
                    fetchPrompts();
                    toggleClearFilters();
                    scrollToTop();
                });

                $('.form-check-input[name=sort_by]').on('change', function() {
                    fetchPrompts();
                    toggleClearFilters();
                    scrollToTop();
                });

                $('#clearFilters').on('click', function() {
                    $('#filterForm')[0].reset();
                    fetchPrompts();
                    toggleClearFilters();
                    scrollToTop();
                });

                function toggleClearFilters() {
                    let filtersActive = $('.filter-checkbox:checked, .form-check-input[name=sort_by]:checked').length > 0;

                    if (filtersActive) {
                        $('.clearFiltersSection').removeClass('d-none');
                    } else {
                        $('.clearFiltersSection').addClass('d-none');
                    }
                }
                toggleClearFilters();

            })(jQuery)
        </script>
    @endpush
