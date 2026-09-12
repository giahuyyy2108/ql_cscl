(function () {
    'use strict';

    var username = document.getElementById('txtUserName');
    var password = document.getElementById('login-password');
    var toggle = document.getElementById('toggle-password');

    if (!username || !password || !toggle) {
        return;
    }

    toggle.hidden = false;
    toggle.addEventListener('click', function () {
        var showPassword = password.type === 'password';
        var label = showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu';

        password.type = showPassword ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(showPassword));
        toggle.setAttribute('aria-label', label);
        toggle.title = label;
        toggle.querySelector('i').className = showPassword ? 'fa fa-eye-slash' : 'fa fa-eye';
    });

    if (!username.disabled && window.matchMedia('(min-width: 761px)').matches) {
        (username.value ? password : username).focus();
    }
}());
