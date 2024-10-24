<div class="sidebar-menu">
    <button type="button" class="sidebar-menu__close d-xl-none d-block"><i class="las la-times"></i></button>
    <div class="sidebar-menu__profile text-center">
        <div class="thumb">
            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, '', avatar:true) }}" alt="profile">
        </div>
        <h5 class="title">
            {{auth()->user()->username}}
        </h5>
    </div>

    <ul class="sidebar-menu-list">
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.home') }}" href="{{ route('user.home') }}">
                <span class="icon"><i class="las la-border-all"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.prompt.my.list') }}" href="{{ route('user.prompt.my.list') }}">
                <span class="icon"><i class="las la-comment-dots"></i></span>
                <span class="text">@lang('My Prompts')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.prompt.favorite.list') }}" href="{{ route('user.prompt.favorite.list') }}">
                <span class="icon"><i class="las la-heart"></i></span>
                <span class="text">@lang('Favorite Prompts')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.sale.history') }}" href="{{ route('user.sale.history') }}">
                <span class="icon"><i class="las la-dollar-sign"></i></span>
                <span class="text">@lang('Sales History')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.purchase.history') }}" href="{{ route('user.purchase.history') }}">
                <span class="icon"><i class="las la-shopping-cart"></i></span>
                <span class="text">@lang('Purchase History')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.deposit.*') }}" href="{{ route('user.deposit.history') }}">
                <span class="icon"><i class="las la-credit-card"></i></span>
                <span class="text">@lang('Payment History')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.withdraw.*') }}" href="{{ route('user.withdraw.history') }}">
                <span class="icon"><i class="las la-hand-holding-usd"></i></span>
                <span class="text">@lang('Withdrawal History')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.transactions') }}" href="{{ route('user.transactions') }}">
                <span class="icon"><i class="las la-exchange-alt"></i></span>
                <span class="text">@lang('Transactions')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('ticket.index') }}" href="{{ route('ticket.index') }}">
                <span class="icon"><i class="la la-ticket-alt"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.twofactor') }}" href="{{ route('user.twofactor') }}">
                <span class="icon"><i class="las la-cog"></i></span>
                <span class="text">@lang('2FA Security')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}">
                <span class="icon"><i class="las la-cog"></i></span>
                <span class="text">@lang('Profile Settings')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link {{ menuActive('user.change.password') }}" href="{{ route('user.change.password') }}">
                <span class="icon"><i class="las la-lock"></i></span>
                <span class="text">@lang('Change Password')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link" href="{{ route('user.logout') }}">
                <span class="icon"><i class="las la-sign-out-alt"></i></span>
                <span class="text">@lang('Logout')</span>
            </a>
        </li>
    </ul>
</div>
