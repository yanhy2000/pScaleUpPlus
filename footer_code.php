<?php
$code = <<<EOL
<style>
    .markdown-body img {
        cursor: pointer;
        transition: transform 0.1s ease;
    }
    .markdown-body img:hover {
        transform: scale({SHOVER});
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .overlay img {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        max-width: 90%;
        max-height: 90%;
        transform: scale(1);
    }
    .overlay.active {
        opacity: 1;
        pointer-events: all;
    }
    .overlay .caption {
        position: absolute;
        bottom: 20px;
        color: white;
        background: rgba(0, 0, 0, 0.7);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        max-width: 90%;
        text-align: center;
    }
    .overlay .nav-button {
        position: absolute;
        top: 50%;
        width: 50px;
        height: 50px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
    }
    .overlay .prev-button {
        left: 20px;
    }
    .overlay .next-button {
        right: 20px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var imgs = document.querySelectorAll('.markdown-body img');
        var overlay = document.createElement('div');
        overlay.className = 'overlay';
        overlay.style.display = 'none';
    
        var overlayImg = document.createElement('img');
        var caption = document.createElement('div');
        caption.className = 'caption';
    
        var prevButton = document.createElement('button');
        prevButton.className = 'nav-button prev-button';
        prevButton.innerHTML = '❮';
    
        var nextButton = document.createElement('button');
        nextButton.className = 'nav-button next-button';
        nextButton.innerHTML = '❯';
    
        overlay.appendChild(overlayImg);
        overlay.appendChild(caption);
        overlay.appendChild(prevButton);
        overlay.appendChild(nextButton);
    
        document.body.appendChild(overlay);
    
        Array.prototype.forEach.call(imgs, function(el, index) {
            el.addEventListener('click', function() {
                showImage(index);
            });
        });

        var currentIndex = 0;
        var scale = 1;
        var startX, startY;
        var isDragging = false;
        var lastX, lastY;
        var posX = 0, posY = 0;

        function showImage(index) {
            if (index < 0 || index >= imgs.length) return;
            currentIndex = index;
            var img = imgs[index];
            overlayImg.src = img.src;
            caption.innerHTML = img.title || '';
            overlay.style.display = 'flex';
            overlay.classList.add('active');
            scale = 1;
            posX = 0;
            posY = 0;
            overlayImg.style.transform = 'scale(1) translate3d(0, 0, 0)';
            if (currentIndex === 0) {
                prevButton.style.display = 'none';
            } else {
                prevButton.style.display = 'block';
            }
    
            if (currentIndex === imgs.length - 1) {
                nextButton.style.display = 'none';
            } else {
                nextButton.style.display = 'block';
            }
        }

        function closeOverlay() {
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        }

        overlay.addEventListener('click', function(event) {
            if (event.target !== prevButton && event.target !== nextButton) {
                closeOverlay();
            }
        });

        prevButton.addEventListener('click', function(event) {
            event.stopPropagation();
            showImage(currentIndex - 1);
        });
    
        nextButton.addEventListener('click', function(event) {
            event.stopPropagation();
            showImage(currentIndex + 1);
        });
    
        document.addEventListener('keydown', function(event) {
            if (!overlay.classList.contains('active')) return;
            if (event.key === 'ArrowLeft') {
                showImage(currentIndex - 1);
            } else if (event.key === 'ArrowRight') {
                showImage(currentIndex + 1);
            } else if (event.key === 'Escape') {
                closeOverlay();
            }
        });

        overlay.addEventListener('wheel', function(event) {
            if (overlay.classList.contains('active')) 
            {
                event.preventDefault();
                if (event.deltaY < 0) {
                    scale += 0.1;
                } else {
                    scale -= 0.1;
                }
            overlayImg.style.transform = 'scale(' + scale + ') translate3d(' + posX + 'px, ' + posY + 'px, 0)';
            }
        });

        overlayImg.addEventListener('touchstart', function(event) {
            if (event.touches.length === 2) {
                startX = event.touches[0].pageX - event.touches[1].pageX;
                startY = event.touches[0].pageY - event.touches[1].pageY;
            } else if (event.touches.length === 1) {
                isDragging = true;
                lastX = event.touches[0].pageX;
                lastY = event.touches[0].pageY;
            }
        });

        overlayImg.addEventListener('touchmove', function(event) {
            if (event.touches.length === 2) {
                event.preventDefault();
                var dx = event.touches[0].pageX - event.touches[1].pageX;
                var dy = event.touches[0].pageY - event.touches[1].pageY;
                var distance = Math.sqrt(dx * dx + dy * dy);
                var startDistance = Math.sqrt(startX * startX + startY * startY);
                scale *= distance / startDistance;
                startX = dx;
                startY = dy;
            overlayImg.style.transform = 'scale(' + scale + ') translate3d(' + posX + 'px, ' + posY + 'px, 0)';
            } else if (event.touches.length === 1 && isDragging) {
                event.preventDefault();
                var dx = event.touches[0].pageX - lastX;
                var dy = event.touches[0].pageY - lastY;
            posX += dx;
            posY += dy;
            requestAnimationFrame(function() {
                overlayImg.style.transform = 'scale(' + scale + ') translate3d(' + posX + 'px, ' + posY + 'px, 0)';
            });
                lastX = event.touches[0].pageX;
                lastY = event.touches[0].pageY;
            }
        });
        overlayImg.addEventListener('touchend', function(event) {
            isDragging = false;
        });
    });
</script>
EOL;
?>
