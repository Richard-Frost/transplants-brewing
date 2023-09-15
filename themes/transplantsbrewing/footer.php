<?php wp_footer(); ?>

<script>
    jQuery(document).ready(function($) {
        $('a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 125// this value (60) is the height of your navbar or whatever offset you wish
                    }, 1000);
                    return false;
                }
            }
        });
    });

    



</script>



</body>
</html>
