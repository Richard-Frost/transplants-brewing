jQuery(document).ready(function($) {
    $('.more-info-btn button').on('click', function() {
        var $this = $(this);
        var $eventPost = $this.closest('.event-post');
        
        $eventPost.toggleClass('active');

        // Toggle button text
        if ($this.text() === 'More Info') {
            $this.text('Close');
        } else {
            $this.text('More Info');
        }
    });
});