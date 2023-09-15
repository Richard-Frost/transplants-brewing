jQuery(document).ready(function($) {
    $('.buy-tickets-btn').on('click', function() {
        $(this).closest('.event-post').find('.iframe-container').show();
    });

    $('.iframe-close-btn').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.iframe-container').hide();
    });
});
