document.addEventListener('DOMContentLoaded', function () {
    var errorAlert = document.getElementById('error-alert');

    if (errorAlert) {
        var errorCountdown = document.getElementById('error-countdown');
        var errorTimeLeft = 5;

        if (errorCountdown) {
            errorCountdown.innerText = errorTimeLeft;
            var errorInterval = setInterval(function () {
                errorTimeLeft--;
                errorCountdown.innerText = errorTimeLeft;

                if (errorTimeLeft <= 0) {
                    clearInterval(errorInterval);
                    errorAlert.remove();
                }
            }, 1000);
        }
    }

    var togglePassword = document.querySelectorAll('.toggle-password');

    togglePassword.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var target = document.querySelector(this.getAttribute('toggle'));

            if (!target) {
                return;
            }

            if (target.type === 'password') {
                target.type = 'text';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                target.type = 'password';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    });
});
