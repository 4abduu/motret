document.addEventListener('DOMContentLoaded', function () {
    var lazyCanvases = document.querySelectorAll('canvas.card-img, canvas.scroll-img');

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(
            function (entries, observerInstance) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    var canvas = entry.target;
                    var imgSrc = canvas.getAttribute('data-src');

                    if (imgSrc) {
                        renderCanvasImage(canvas, imgSrc);
                    }

                    observerInstance.unobserve(canvas);
                });
            },
            { rootMargin: '100px' }
        );

        lazyCanvases.forEach(function (canvas) {
            observer.observe(canvas);
        });
    } else {
        lazyCanvases.forEach(function (canvas) {
            var imgSrc = canvas.getAttribute('data-src');
            if (imgSrc) {
                renderCanvasImage(canvas, imgSrc);
            }
        });
    }

    // Blokir klik kanan
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    // Blokir inspect element
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
            e.preventDefault();
        }
    });
});

function renderCanvasImage(canvas, imgSrc) {
    var img = new Image();
    img.src = imgSrc;

    img.onload = function () {
        var ctx = canvas.getContext('2d');
        var width = canvas.clientWidth;
        var height = canvas.clientHeight;
        var aspectRatio = img.width / img.height;

        if (width / height > aspectRatio) {
            width = height * aspectRatio;
        } else {
            height = width / aspectRatio;
        }

        canvas.width = width;
        canvas.height = height;
        ctx.drawImage(img, 0, 0, width, height);
    };
}
