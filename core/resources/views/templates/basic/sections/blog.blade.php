@php
    $content = getContent('blog.content', true);
    $blogs = getContent('blog.element', limit: 3);
@endphp
@if ($blogs->count())
    <section class="blog py-60">
        <div class="container">
            <div class="section-heading style-left d-flex flex-wrap justify-content-between gap-2">
                <div class="section-heading__left">
                    <span class="section-heading__subtitle">
                        {{ __(@$content->data_values->background_text) }}
                    </span>
                    <h2 class="section-heading__title">{{ __(@$content->data_values->heading) }}</h2>
                </div>
                <div class="section-heading__right">
                    <a href="{{ route('blog') }}" class="btn btn-outline--base">@lang('View all')</a>
                </div>
            </div>
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-item">
                            <div class="blog-item__thumb">
                                <a href="{{ route('blog.details', $blog->slug) }}" class="blog-item__thumb-link">
                                    <img src="{{ frontendImage('blog', @$blog->data_values->image, '500x250', thumb: true) }}"
                                        class="fit-image" alt="image">
                                </a>
                            </div>
                            <div class="blog-item__content">
                                <ul class="text-list flex-align gap-3">
                                    <li class="text-list__item fs-14"> <span
                                            class="text-list__item-icon fs-12 me-1 text--base"><i
                                                class="fas fa-calendar-alt"></i></span>
                                        {{ showDateTime($blog->created_at, 'd M Y') }}</li>
                                </ul>
                                <h4 class="blog-item__title"><a href="{{ route('blog.details', $blog->slug) }}"
                                        class="blog-item__title-link border-effect">{{ __(strLimit(@$blog->data_values->title, 60)) }}</a>
                                </h4>
                                <p class="blog-item__desc">@php echo __(strLimit(strip_tags(@$blog->data_values->description), 85));@endphp</p>
                                <a href="{{ route('blog.details', $blog->slug) }}" class="btn--simple">@lang('Read More')
                                    <span class="btn--simple__icon"><i class="fas fa-arrow-right"></i></span></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
