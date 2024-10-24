@php
    $language = App\Models\Language::all();
    $selectedLang = $language->where('code', session('lang'))->first();
    $categories = App\Models\Category::active()->get();
    $tools = App\Models\Tool::active()->get();
    if (!Route::is('prompt.*')) {
        $action = route('prompt.all');
    } else {
        $action = request()->fullUrl();
    }
@endphp

<header class="header" id="header">
    <div class="container custom-container">
        <nav class="navbar navbar-expand-xl navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="logo"></a>
            <div class="flex-align header-right d-xl-none ">
                <div class="header-search-inner">
                    <button type="button" class="header-search-btn"><i class="la la-search"></i></button>

                    <div class="header-search flex-grow-1">
                        <form action="{{ $action }}" class="header-search-form me-auto ">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control form--control" placeholder="@lang('Search Here')....">
                                <button class="input-group-text" id="basic-addon2"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <a href="{{ route('cart.view') }}" class="ecommerce-cart">
                    <span class="count-product">0</span>
                    <i class="las la-shopping-cart"></i>
                </a>

                <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span id="hiddenNav"><i class="las la-bars"></i></span>
                </button>
            </div>
            <div class="header-search d-none d-xl-block">
                <form action="{{ $action }}" class="search-form active">
                    <input type="text" name="search" value="{{ request()->search }}" class="form--control" placeholder="@lang('Search Here')....">
                    <button type="submit" class="search-form__btn"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu ms-auto align-items-xl-center">

                    <li class="nav-item dropdown {{ menuActive('prompt.tools*') }}">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">@lang('AI Tools')
                            <span class="nav-item__icon"><i class="las la-angle-down"></i></span></a>
                        <ul class="dropdown-menu">
                            @foreach ($tools as $tool)
                                <li class="dropdown-menu__list {{ menuActive('prompt.tools', null, $tool->slug) }}"><a class="dropdown-item dropdown-menu__link" href="{{ route('prompt.tools', $tool->slug) }}">{{ __($tool->name) }}</a></li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="nav-item {{ menuActive('prompt.all') }}">
                        <a class="nav-link" aria-current="page" href="{{ route('prompt.all') }}">@lang('Prompts')</a>
                    </li>

                    @auth
                        <li class="nav-item {{ menuActive('user.prompt.create') }}">
                            <a class="nav-link" href="{{ route('user.prompt.create') }}">@lang('Sell Prompts')</a>
                        </li>
                    @endauth

                    <li class="nav-item {{ menuActive('contact') }}">
                        <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>

                    @if (gs()->multi_language)
                        <li class="nav-item d-none d-xl-block">
                            <div class="dropdown-lang dropdown d-block">
                                <a href="#" class="language-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$selectedLang->image, getFileSize('language')) }}" alt="us">
                                    <span class="language-text">{{ @$selectedLang->name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($language as $lang)
                                        <li><a href="{{ route('lang', $lang->code) }}"><img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$lang->image, getFileSize('language')) }}" alt="image">
                                                {{ @$lang->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif

                    @guest
                        <li class="nav-item d-xl-none">
                            <a class="nav-link" href="{{ route('user.login') }}">@lang('Login')</a>
                        </li>
                        <li class="nav-item d-xl-none mt-3">
                            <a href="{{ route('user.register') }}" class="btn btn--base">@lang('Sign Up')</a>
                        </li>
                    @else
                        <li class="nav-item d-xl-none mt-3">
                            <a href="{{ route('user.home') }}" class="btn btn--base">@lang('Dashboard')</a>
                        </li>
                    @endguest

                    @if (gs()->multi_language)
                        <li class="nav-item d-xl-none">
                            <div class="dropdown-lang dropdown d-block">
                                <a href="#" class="language-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$selectedLang->image, getFileSize('language')) }}" alt="us">
                                    <span class="language-text">{{ @$selectedLang->name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($language as $lang)
                                        <li><a href="{{ route('lang', $lang->code) }}"><img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$lang->image, getFileSize('language')) }}" alt="image">
                                                {{ @$lang->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    <li class="flex-align header-right header-right-lg">
                        <a href="{{ route('cart.view') }}" class="ecommerce-cart">
                            <span class="count-product">0</span>
                            <i class="las la-shopping-cart"></i>
                        </a>
                        @guest
                            <a href="{{ route('user.login') }}" class="ecommerce-cart d-xxl-none">
                                <i class="las la-user-alt"></i>
                            </a>
                            <a href="{{ route('user.login') }}" class="login-link d-none d-xxl-block">@lang('Login')</a>
                            <a href="{{ route('user.register') }}" class="btn btn--base d-none d-xxl-block">@lang('Sign Up')</a>
                        @else
                            <a href="{{ route('user.home') }}" class="btn btn--base">@lang('Dashboard')</a>
                        @endguest

                    </li>
                </ul>
            </div>
        </nav>

        <div class="header-bottom">
            <div class="tabs-container">
                <div class="left-arrow">
                    <span class="pre arrow-btn"><i class="las la-angle-left"></i></span>
                </div>
                <ul>
                    @foreach ($categories as $category)
                        <li><a href="{{ route('prompt.categories', $category->slug) }}" class="{{ menuActive('prompt.categories', null, $category->slug) }}">{{ __($category->name) }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="right-arrow active">
                    <span class="next arrow-btn"> <i class="las la-angle-right"></i></span>
                </div>
            </div>
        </div>
    </div>

</header>
