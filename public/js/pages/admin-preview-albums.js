document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const readMoreBtn = e.target.closest('.read-more');
        if (!readMoreBtn) {
            return;
        }

        const descElement = readMoreBtn.closest('.album-description');
        const fullText = descElement.getAttribute('data-full-text');
        const isExpanded = readMoreBtn.textContent.includes('Sembunyikan');

        if (isExpanded) {
            descElement.innerHTML = fullText.substring(0, 150) + '<span class="read-more">...Lainnya</span>';
        } else {
            descElement.innerHTML = fullText + '<span class="read-more">Sembunyikan</span>';
        }
    });
});
