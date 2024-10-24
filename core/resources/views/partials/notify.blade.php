@php
    $isUserPanel = !Route::is('admin.*');
@endphp
<link href="{{ asset('assets/global/css/iziToast.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/global/css/iziToast_custom.css') }}" rel="stylesheet">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
@if ($isUserPanel)
    <style>
        .iziToast>.iziToast-close {
            color: #ffff !important;
        }
    </style>
@endif
<script>
    "use strict";
    const colors = {
        success: '#28C76F',
        error: '#EB2222',
        warning: '#FF9F43',
        info: '#1E9FF2',
    }
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-exclamation-circle',
    }
    const notifications = @json(session('notify', []));
    const errors = @json(@$errors ? collect($errors->all())->unique() : []);
    const triggerToaster = (status, message) => {
        iziToast[status]({
            title: status.charAt(0).toUpperCase() + status.slice(1),
            message: message,
            position: "topRight",
            @if ($isUserPanel)
                backgroundColor: '#293a57',
                titleColor: '#fff',
                messageColor: '#fff',
            @else
                backgroundColor: '#fff',
                titleColor: '#474747',
                messageColor: '#A2A2A2',
            @endif
            icon: icons[status],
            iconColor: colors[status],
            progressBarColor: colors[status],
            titleSize: '1rem',
            messageSize: '1rem',
            transitionIn: 'obunceInLeft'
        });
    }
    if (notifications.length) {
        notifications.forEach(element => {
            triggerToaster(element[0], element[1]);
        });
    }
    if (errors.length) {
        errors.forEach(error => {
            triggerToaster('error', error);
        });
    }
    function notify(status, message) {
        if (typeof message == 'string') {
            triggerToaster(status, message);
        } else {
            $.each(message, (i, val) => triggerToaster(status, val));
        }
    }
</script>
