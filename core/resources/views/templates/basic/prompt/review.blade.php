@foreach ($reviews as $review)
    <li class="comment-list__item">
        <div class="comment-list__author mb-3 d-flex flex-wrap align-items-center gap-2">
            <div class="list list--row rating-list">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= $review->rating ? 'rating-list__icon-active' : '' }}">
                        <i class="fas fa-star"></i>
                    </span>
                @endfor
            </div>
            <span class="comment-list__name">{{ $review->user->username }}</span>
            <span class="comment-list__time fs-12">{{ $review->created_at->diffForHumans() }}</span>
        </div>
        <div class="d-flex flex-wrap">
            <div class="comment-list__thumb">
                <img src="{{ getImage(getFilePath('userProfile') . '/' . $review->user->image, avatar: true) }}" class="fit-image" alt="img">
            </div>
            <div class="comment-list__content">
                <p class="comment-list__desc">{{ $review->review }}</p>
            </div>
        </div>
    </li>
@endforeach
