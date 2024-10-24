@extends($activeTemplate . 'layouts.app')

@section('panel')
    @include($activeTemplate . 'partials.header')
    @if (request()->routeIs('seller.profile'))
        <main class="common-body-two">
        @else
            <main @if (!request()->routeIs('home')) class="common-body" @endif>
    @endif
    @if (!request()->routeIs('home') && !request()->routeIs('seller.profile') && !request()->routeIs('user.data'))
        @include($activeTemplate . 'partials.breadcrumb')
    @endif
    @yield('content')
    </main>

    @include($activeTemplate . 'partials.footer')
@endsection
