@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog">
        <div class="container">
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
                                        class="blog-item__title-link border-effect">{{ __(strLimit($blog->data_values->title, 60)) }}</a>
                                </h4>
                                <p class="blog-item__desc">@php echo __(strLimit(strip_tags($blog->data_values->description), 85));@endphp</p>
                                <a href="{{ route('blog.details', $blog->slug) }}" class="btn--simple">@lang('Read More')
                                    <span class="btn--simple__icon"><i class="fas fa-arrow-right"></i></span></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
