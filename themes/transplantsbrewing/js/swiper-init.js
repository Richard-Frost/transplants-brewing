document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper('.swiper-container', {
        autoplay: {
            delay: 50, // image stays visible for 500ms
            disableOnInteraction: false,
        },
        loop: true,
        fadeEffect: {
            crossFade: true
        },
        effect: 'fade',
        speed: 5000  // 4 seconds transition
    });
});
