@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $banned = getContent('banned.content', true);
    @endphp
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 text-center">
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <h3 class="text--danger mb-2">{{ __(@$banned->data_values->heading) }}</h3>
                    </div>
                    <div class="col-sm-6 col-8 col-lg-12">
                        <img class="img-fluid mx-auto mb-5" src="{{ frontendImage('banned', @$banned->data_values->image, '360x370') }}" alt="image">
                    </div>
                </div>
                <p class="mx-auto mb-4 text-center">{{ __($user->ban_reason) }} </p>
                <a class="btn btn--xl btn--base" href="{{ route('home') }}"> @lang('Go to Home') </a>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    header{
        display:none;
    }
    footer{
        display:none;
    }
    .breadcrumb{
        display:none;
    }
    body{
        display: flex;
        align-items: center;
        height: 100vh;
        justify-content: center;
    }
</style>
@endpush
