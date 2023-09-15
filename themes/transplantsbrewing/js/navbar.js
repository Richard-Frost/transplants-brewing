function updateNavbarPosition() {
    const navbar = document.querySelector(".custom-navbar");
    const carouselHeight = window.innerHeight; 

    if (window.scrollY <= 0) {
        navbar.style.top = `${carouselHeight - navbar.offsetHeight}px`;
        navbar.style.bottom = '0px';
    } else if (window.scrollY < (carouselHeight - navbar.offsetHeight)) {
        navbar.style.top = `${carouselHeight - navbar.offsetHeight - window.scrollY}px`;
        navbar.style.bottom = 'auto';
    } else {
        navbar.style.top = '0px';
        navbar.style.bottom = 'auto';
    }
}

// Use requestAnimationFrame for smoother animations
let ticking = false;
window.addEventListener('scroll', function() {
    if (!ticking) {
        window.requestAnimationFrame(function() {
            updateNavbarPosition();
            ticking = false;
        });

        ticking = true;
    }
});

// Initialize the navbar position on page load
updateNavbarPosition();