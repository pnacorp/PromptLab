@php
    $collectionContent = getContent('collection.content', true);
@endphp
<section class="collection pt-60 pb-120">
    <div class="container">
        <div class="collection-content text-center">
            <img src="{{ frontendImage('collection', @$collectionContent->data_values->left_image, '225x220') }}" class="collection-shape" alt="img">
            <img src="{{ frontendImage('collection', @$collectionContent->data_values->right_image, '150x290') }}" class="collection-shape-two" alt="img">
            <h3 class="title">
                {{ __(@$collectionContent->data_values->heading) }}
            </h3>
            <p class="desc fs-18">{{ __(@$collectionContent->data_values->subheading) }}</p>
            <a href="{{ @$collectionContent->data_values->button_link }}" class="btn btn--base btn--lg">{{ __(@$collectionContent->data_values->button_name) }}</a>
        </div>
    </div>
</section>
