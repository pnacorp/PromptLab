@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')
    <main class="common-body">
        <div class="container">
            <div class="d-flex gap-4">
                @include($activeTemplate . 'partials.user_menu')

                <div class="dashboard-main-content">
                    <div class="dashboard-heading mb-4 d-xl-none d-block">
                        <div class="dashboard-body__bar">
                            <button type="button" class="dashboard-body__bar-icon"><i class="las la-bars"></i></button>
                        </div>
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    @include($activeTemplate . 'partials.footer')
@endsection
