function buySubscription(price, packageName) {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '\n            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>\n            Processing...\n        ';
    button.disabled = true;

    Swal.fire({
        title: 'Processing Payment',
        html: 'Preparing your subscription...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.subscriptionUserConfig.subscribeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.subscriptionUserConfig.csrfToken,
            Accept: 'application/json'
        },
        body: JSON.stringify({
            package: packageName,
            _token: window.subscriptionUserConfig.csrfToken
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Payment processing failed');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            button.innerHTML = originalText;
            button.disabled = false;

            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        showSuccessAlert('Payment Successful!', 'Your subscription has been activated.');
                        checkTransactionStatus(result.order_id);
                    },
                    onPending: function (result) {
                        showInfoAlert('Payment Pending', 'Please complete your payment to activate subscription.');
                        checkTransactionStatus(result.order_id);
                    },
                    onError: function (result) {
                        showPaymentError(result);
                    },
                    onClose: function () {
                        showWarningAlert('Payment Cancelled', 'You closed the payment popup without completing the transaction.');
                    }
                });
            } else {
                throw new Error('Failed to get payment token');
            }
        })
        .catch(error => {
            button.innerHTML = originalText;
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                text: error.message || 'An error occurred during payment processing',
                confirmButtonColor: '#3b82f6'
            });
        });
}

function buyComboSubscription(price, duration) {
    const button = event.target;
    const originalText = button.innerHTML;
    const systemPrice = button.dataset.systemPrice || 0;
    const userPrice = button.dataset.userPrice || 0;

    button.innerHTML = '\n            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>\n            Processing...\n        ';
    button.disabled = true;

    Swal.fire({
        title: 'Processing Payment',
        html: 'Preparing your subscription...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.subscriptionUserConfig.subscribeComboUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.subscriptionUserConfig.csrfToken,
            Accept: 'application/json'
        },
        body: JSON.stringify({
            combo_price: price,
            duration: duration,
            system_price: systemPrice,
            user_price: userPrice,
            _token: window.subscriptionUserConfig.csrfToken
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Payment processing failed');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            button.innerHTML = originalText;
            button.disabled = false;

            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        showSuccessAlert('Payment Successful!', 'Your combo subscription has been activated.');
                        checkComboTransactionStatus(result.order_id);
                    },
                    onPending: function (result) {
                        showInfoAlert('Payment Pending', 'Please complete your payment to activate subscription.');
                        checkComboTransactionStatus(result.order_id);
                    },
                    onError: function (result) {
                        showPaymentError(result);
                    },
                    onClose: function () {
                        showWarningAlert('Payment Cancelled', 'You closed the payment popup without completing the transaction.');
                    }
                });
            } else {
                throw new Error('Failed to get payment token');
            }
        })
        .catch(error => {
            button.innerHTML = originalText;
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                text: error.message || 'An error occurred during payment processing',
                confirmButtonColor: '#8b5cf6'
            });
        });
}

function checkTransactionStatus(orderId) {
    Swal.fire({
        title: 'Verifying Payment',
        html: 'Please wait while we verify your payment...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.subscriptionUserConfig.checkStatusUserUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.subscriptionUserConfig.csrfToken,
            Accept: 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            _token: window.subscriptionUserConfig.csrfToken
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Verification failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Activated!',
                    text: 'Your creator subscription is now active.',
                    confirmButtonColor: '#3b82f6',
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        localStorage.setItem('activeTab', 'subscription');
                        window.location.href = data.redirect_url;
                    }
                });
            } else {
                throw new Error(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: error.message || 'Failed to verify payment status',
                confirmButtonColor: '#3b82f6'
            });
        });
}

function checkComboTransactionStatus(orderId) {
    Swal.fire({
        title: 'Verifying Payment',
        html: 'Please wait while we verify your payment...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.subscriptionUserConfig.checkStatusComboUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.subscriptionUserConfig.csrfToken,
            Accept: 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            _token: window.subscriptionUserConfig.csrfToken
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Verification failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Activated!',
                    text: 'Your combo subscription is now active.',
                    confirmButtonColor: '#8b5cf6',
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        localStorage.setItem('activeTab', 'subscription');
                        window.location.href = data.redirect_url;
                    }
                });
            } else {
                throw new Error(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: error.message || 'Failed to verify payment status',
                confirmButtonColor: '#8b5cf6'
            });
        });
}

function showPaymentError(result) {
    let errorMessage = 'Payment failed. Please try again.';

    if (result.status_code === '202') {
        errorMessage = 'Transaction was denied by your bank.';
    } else if (result.status_code === '400') {
        errorMessage = 'Invalid payment data.';
    } else if (result.status_message) {
        errorMessage = result.status_message;
    }

    Swal.fire({
        icon: 'error',
        title: 'Payment Failed',
        html:
            '<div class="text-left">' +
            '<p>' +
            errorMessage +
            '</p>' +
            (result.status_code ? '<p class="mb-1"><strong>Error Code:</strong> ' + result.status_code + '</p>' : '') +
            (result.transaction_id ? '<p class="mb-0"><strong>Transaction ID:</strong> ' + result.transaction_id + '</p>' : '') +
            '</div>',
        confirmButtonColor: '#3b82f6'
    });
}

function showSuccessAlert(title, text) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonColor: '#3b82f6',
        timer: 3000,
        timerProgressBar: true
    });
}

function showInfoAlert(title, text) {
    Swal.fire({
        icon: 'info',
        title: title,
        text: text,
        confirmButtonColor: '#3b82f6',
        timer: 5000,
        timerProgressBar: true
    });
}

function showWarningAlert(title, text) {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: text,
        confirmButtonColor: '#3b82f6'
    });
}
