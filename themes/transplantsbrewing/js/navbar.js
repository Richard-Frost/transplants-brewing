jQuery(document).ready(function($) {
    // smooth scroll
    function handleScroll(targetSelector, offset) {
        var target = $(targetSelector);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - offset
            }, 1000, function() {
                updateNavbarPosition();
            });
        }
    }

    // hide the outside container
    function hideOutsideContainer() {
        $('.outside-container').animate({
            bottom: '-100%'
        }, 1000);
    }

    // collapse the navbar in mobile view
    function collapseNavbar() {
        const navbarToggler = $('.navbar-toggler');
        const isNavbarCollapsed = !navbarToggler.hasClass('collapsed');

        if (navbarToggler.is(':visible') && isNavbarCollapsed) {
            navbarToggler.trigger('click');
        }
    }

    // Smooth scroll for links in the navbar
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            handleScroll(this.hash, 120); 
            hideOutsideContainer(); 
            collapseNavbar(); 
            return false;
        }
    });

    // Smooth scroll for the down arrow to the event section
    $('#down-arrow').click(function() {
        handleScroll('#events-section', 120); 
        hideOutsideContainer(); 
    });

    // Function to update navbar position with jQuery
    function updateNavbarPosition() {
        const navbar = $("#navbar-section");
        const carouselHeight = window.innerHeight;

        // Adjust the navbar position based on the scroll position
        if (window.scrollY <= carouselHeight) {
            navbar.css('top', `${carouselHeight - window.scrollY}px`);
        } else {
            navbar.css('top', '0px');
        }

        const content = $(".content-scrolling-over");
        content.css('padding-top', `${navbar.height() + 20}px`);
    }

    // Use requestAnimationFrame for smoother animations
    let ticking = false;
    $(window).scroll(function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                updateNavbarPosition();
                ticking = false;
            });

            ticking = true;
        }
    });

    // Initial position update when the page loads
    updateNavbarPosition();

    // ensure the page starts at the top on refresh
    $(window).on('beforeunload', function() {
        $(window).scrollTop(0);
    });
});

