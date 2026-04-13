document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
            e.preventDefault();
        }
    });

    const canvas = document.getElementById('photoCanvas');
    if (!canvas) {
        return;
    }

    const imgSrc = canvas.getAttribute('data-src');
    const img = new Image();
    img.src = imgSrc;
    img.crossOrigin = 'anonymous';

    img.onload = function () {
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        const watermarkText = 'MOTRET';
        const fontSize = 25;
        ctx.font = fontSize + 'px Arial';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        const stepX = 150;
        const stepY = 100;
        const angle = -30 * (Math.PI / 180);

        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(angle);

        for (let x = -canvas.width; x < canvas.width; x += stepX) {
            for (let y = -canvas.height; y < canvas.height; y += stepY) {
                ctx.fillText(watermarkText, x, y);
            }
        }

        ctx.restore();
    };
});
