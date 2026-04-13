document.addEventListener('DOMContentLoaded', function () {
    if (window.adminDashboardConfig) {
        const userGrowthCtx = document.getElementById('userGrowthChart');
        if (userGrowthCtx) {
            new Chart(userGrowthCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: window.adminDashboardConfig.userGrowthLabels,
                    datasets: [{
                        label: 'User Growth',
                        data: window.adminDashboardConfig.userGrowthData,
                        backgroundColor: 'rgba(50, 189, 64, 0.2)',
                        borderColor: '#32bd40',
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const photoUploadCtx = document.getElementById('photoUploadChart');
        if (photoUploadCtx) {
            new Chart(photoUploadCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: window.adminDashboardConfig.photoUploadLabels,
                    datasets: [{
                        label: 'Photo Uploads',
                        data: window.adminDashboardConfig.photoUploadData,
                        backgroundColor: 'rgba(42, 168, 53, 0.2)',
                        borderColor: '#2aa835',
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        if (window.adminDashboardConfig.loginSuccess && !localStorage.getItem('loginAlertShown')) {
            Swal.fire({
                icon: 'success',
                title: window.adminDashboardConfig.loginSuccess,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                background: '#32bd40',
                color: '#fff',
                iconColor: '#fff',
                didOpen: (toast) => {
                    toast.addEventListener('click', () => {
                        Swal.close();
                    });
                }
            });
            localStorage.setItem('loginAlertShown', 'true');

            window.addEventListener('beforeunload', function () {
                localStorage.removeItem('loginAlertShown');
            });
        }
    }
});
