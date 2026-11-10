(function () {
    'use strict';

    var username = document.getElementById('txtUserName');
    var password = document.getElementById('login-password');
    var toggle = document.getElementById('toggle-password');
	var newPassword = document.getElementById('new-password');
	var confirmPassword = document.getElementById('confirm-password');

	if (newPassword && confirmPassword) {
		var strength = document.getElementById('password-strength');
		var strengthText = strength.querySelector('strong');
		var strengthBar = strength.querySelector('.password-strength__bar span');
		var matchMessage = document.getElementById('password-match-message');

		function checkConfirmation() {
			if (confirmPassword.value === '') {
				matchMessage.textContent = '';
				matchMessage.className = 'password-match-message';
				return;
			}
			var matches = confirmPassword.value === newPassword.value;
			matchMessage.textContent = matches ? 'Mật khẩu xác nhận đã khớp.' : 'Mật khẩu xác nhận chưa khớp.';
			matchMessage.className = 'password-match-message ' + (matches ? 'is-valid' : 'is-invalid');
			confirmPassword.setCustomValidity(matches ? '' : 'Mật khẩu xác nhận chưa khớp.');
		}

		function evaluatePassword() {
			var value = newPassword.value;
			var rules = {
				length: value.length > 8,
				uppercase: /[A-Z]/.test(value),
				number: /[0-9]/.test(value),
				special: /[^A-Za-z0-9]/.test(value)
			};
			var score = 0;
			Object.keys(rules).forEach(function (rule) {
				var item = document.querySelector('[data-rule="' + rule + '"]');
				item.classList.toggle('is-valid', rules[rule]);
				if (rules[rule]) score++;
			});

			var labels = ['Rất yếu', 'Rất yếu', 'Yếu', 'Trung bình', 'Mạnh'];
			var colors = ['#dc3545', '#dc3545', '#fd7e14', '#d39e00', '#198754'];
			strengthText.textContent = value === '' ? 'Chưa nhập' : labels[score];
			strengthText.style.color = colors[score];
			strengthBar.style.width = value === '' ? '0' : (score * 25) + '%';
			strengthBar.style.backgroundColor = colors[score];
			checkConfirmation();
		}

		newPassword.addEventListener('input', evaluatePassword);
		confirmPassword.addEventListener('input', checkConfirmation);
		newPassword.focus();
		return;
	}

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
