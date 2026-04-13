(function () {
    const modal = document.getElementById('photo-modal');
    const modalImg = document.getElementById('modal-img');
    const closeModal = document.querySelector('.close-modal');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const resetZoomBtn = document.getElementById('reset-zoom');

    if (!modal || !modalImg || !closeModal || !zoomInBtn || !zoomOutBtn || !resetZoomBtn) {
        return;
    }

    let currentScale = 1;
    let posX = 0;
    let posY = 0;
    const MIN_SCALE = 0.5;
    const MAX_SCALE = 4;
    let isDragging = false;
    let startX;
    let startY;
    let hammer;

    function updateTransform() {
        modalImg.style.transform = 'translate(' + posX + 'px, ' + posY + 'px) scale(' + currentScale + ')';
    }

    function clampScale(scale) {
        return Math.max(MIN_SCALE, Math.min(MAX_SCALE, scale));
    }

    function initHammer() {
        if (hammer) {
            hammer.destroy();
        }

        hammer = new Hammer(modalImg, {
            recognizers: [
                [Hammer.Pan, { direction: Hammer.DIRECTION_ALL }],
                [Hammer.Pinch],
                [Hammer.Tap, { event: 'doubletap', taps: 2 }]
            ]
        });

        let initialScale;
        let initialPosX;
        let initialPosY;

        hammer.on('panstart', function () {
            if (currentScale > 1) {
                initialPosX = posX;
                initialPosY = posY;
                modalImg.style.cursor = 'grabbing';
            }
        });

        hammer.on('pan', function (e) {
            if (currentScale > 1) {
                posX = initialPosX + e.deltaX;
                posY = initialPosY + e.deltaY;
                updateTransform();
            }
        });

        hammer.on('panend', function () {
            modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
        });

        hammer.on('pinchstart', function () {
            initialScale = currentScale;
        });

        hammer.on('pinch', function (e) {
            const newScale = clampScale(initialScale * e.scale);
            if (newScale !== currentScale) {
                currentScale = newScale;

                const rect = modalImg.getBoundingClientRect();
                const centerX = (e.center.x - rect.left - posX) / currentScale;
                const centerY = (e.center.y - rect.top - posY) / currentScale;

                posX = e.center.x - rect.left - centerX * currentScale;
                posY = e.center.y - rect.top - centerY * currentScale;

                updateTransform();
            }
        });

        hammer.on('doubletap', function (e) {
            const rect = modalImg.getBoundingClientRect();
            const tapX = e.center.x - rect.left;
            const tapY = e.center.y - rect.top;

            if (currentScale > 1) {
                currentScale = 1;
                posX = 0;
                posY = 0;
            } else {
                currentScale = 2;
                posX = -(tapX * (currentScale - 1));
                posY = -(tapY * (currentScale - 1));
            }
            updateTransform();
        });
    }

    function handleWheel(e) {
        e.preventDefault();
        e.stopPropagation();

        const delta = e.deltaY < 0 ? 1.1 : 0.9;
        const newScale = clampScale(currentScale * delta);

        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            posX = x - (x - posX) * (newScale / currentScale);
            posY = y - (y - posY) * (newScale / currentScale);
            currentScale = newScale;

            updateTransform();
        }
    }

    function handleMouseDown(e) {
        if (e.button === 0 && currentScale > 1) {
            isDragging = true;
            startX = e.clientX - posX;
            startY = e.clientY - posY;
            modalImg.style.cursor = 'grabbing';
            e.preventDefault();
        }
    }

    function handleMouseMove(e) {
        if (isDragging) {
            posX = e.clientX - startX;
            posY = e.clientY - startY;
            updateTransform();
        }
    }

    function handleMouseUp(e) {
        if (e.button === 0) {
            isDragging = false;
            modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
        }
    }

    function closeDocumentModal() {
        modal.style.display = 'none';
        modalImg.removeEventListener('wheel', handleWheel);
        modalImg.removeEventListener('mousedown', handleMouseDown);
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
        document.body.classList.remove('modal-open');
    }

    window.openDocumentModal = function (src) {
        modal.style.display = 'flex';
        modalImg.src = src;

        currentScale = 1;
        posX = 0;
        posY = 0;
        updateTransform();

        modalImg.addEventListener('wheel', handleWheel, { passive: false });
        modalImg.addEventListener('mousedown', handleMouseDown);
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);

        initHammer();
        document.body.classList.add('modal-open');
    };

    modalImg.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    closeModal.addEventListener('click', closeDocumentModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeDocumentModal();
        }
    });

    zoomInBtn.addEventListener('click', function () {
        const newScale = clampScale(currentScale * 1.2);
        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            posX = -(centerX * (newScale - currentScale)) + posX * (newScale / currentScale);
            posY = -(centerY * (newScale - currentScale)) + posY * (newScale / currentScale);
            currentScale = newScale;
            updateTransform();
        }
    });

    zoomOutBtn.addEventListener('click', function () {
        const newScale = clampScale(currentScale * 0.8);
        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            posX = (centerX * (currentScale - newScale)) + posX * (newScale / currentScale);
            posY = (centerY * (currentScale - newScale)) + posY * (newScale / currentScale);
            currentScale = newScale;
            updateTransform();
        }
    });

    resetZoomBtn.addEventListener('click', function () {
        currentScale = 1;
        posX = 0;
        posY = 0;
        updateTransform();
    });
})();
