<?php get_header(); ?>

<div class="fixed-carousel">
    <div class="black-overlay"></div>

    <!-- Swiper container -->
    <div class="swiper-container">
        <!-- Wrapper for slides -->
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <img src="/wp-content/themes/transplantsbrewing/assets/images/20220208_203650 (1).jpg" class="d-block w-100" alt="Slide 1">
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <img src="/wp-content/themes/transplantsbrewing/assets/images/20220208_211534.jpg" class="d-block w-100" alt="Slide 2">
            </div>
            <!-- Add more swiper-slide divs as needed -->
        </div>
    </div>

    <!-- Logo Overlay -->
    <div class="logo-container">
        <img src="/wp-content/themes/transplantsbrewing/assets/images/transplant.png" alt="Transplant Logo" class="carousel-logo">
    </div>
</div>

<div class="content-scrolling-over">

    <!-- Bootstrap Navbar with Custom Classes -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="navbar-content">
            <!-- Navbar Brand -->
            <a class="navbar-brand ms-4" href="#">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/transplant1.png" alt="Transplants Brewing Logo" class="navbar-logo">
            </a>

            <!-- Toggler for responsive navigation -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCustom" aria-controls="navbarCustom" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarCustom">
                <ul class="navbar-nav ms-5"> <!-- Increased spacing from the logo -->
                    <li class="nav-item me-4">
                        <a class="nav-link text-white fs-3" href="#">Home</a>
                    </li>

                    <li class="nav-item me-4">
                        <a class="nav-link text-white fs-3" href="#events-section">Events</a>  <!-- CHANGED HERE -->
                    </li>


                    <li class="nav-item me-4">
                        <a class="nav-link text-white fs-3" href="#beverages-section">Beer</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-white fs-3" href="https://linktr.ee/transplantsbrewing">Linktree</a>
                    </li>
                    <!-- Add other links as needed -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Version Check Script -->
    <script>
        try {
            console.log($.fn.tooltip.Constructor.VERSION);  // Check for Bootstrap 3 or 4
        } catch (e) {
            console.log("Bootstrap 3 or 4 not detected: ", e.message);
        }

        try {
            console.log(bootstrap.Tooltip.VERSION);  // Check for Bootstrap 5
        } catch (e) {
            console.log("Bootstrap 5 not detected: ", e.message);
        }
    </script>

    <!-- Main Content -->
    <main class="container mx-auto my-16 grid grid-cols-3 gap-16">



<!-- Transplants Events Section -->
<div class="events-container">
    <section id="events-section" class="events-section">
        <h1 style="text-align: center; margin-top: 5px; margin-bottom: 20px;">SHOWS</h1>
        <div class="event-container">
            <?php 
            $current_date = date('Y-m-d H:i:s'); 
            $args = array(
                'post_type' => 'event',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_key' => 'event_date_time',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'event_date_time',
                        'value' => $current_date,
                        'compare' => '>',
                        'type' => 'DATETIME'
                    )
                )
            );
            $event_query = new WP_Query($args);

            if($event_query->have_posts()) : 
                while($event_query->have_posts()) : 
                    $event_query->the_post();

                    // Extra check for autodraft
                    if (get_post_status() == 'auto-draft') continue;

                    $performers = json_decode(get_post_meta(get_the_ID(), 'performers', true), true);
                    $performers_line = [];
                    if (!empty($performers)) {
                        foreach ($performers as $performer) {
                            $performers_line[] = esc_html($performer['name']);
                        }
                    }
                    $event_link = get_post_meta(get_the_ID(), 'event_link', true);
                    ?>
                    <div class="event-posts-container">
                        <div class="event-post">
                            <div class="image-container">
                                <?php 
                                $event_image_url = get_post_meta(get_the_ID(), 'event_image_url', true);
                                if ($event_image_url) : ?>
                                    <img class="card-img-top" src="<?php echo esc_url($event_image_url); ?>" alt="<?php the_title(); ?>">
                                <?php else : ?>
                                    <p class="card-text">Image URL is missing.</p>
                                <?php endif; ?>
                            </div>
                            <!-- Date and title container -->
                            <div class="date-and-title-container">
                                <div class="date-box">
                                    <?php 
                                    $event_date_time = get_post_meta(get_the_ID(), 'event_date_time', true);
                                    if ($event_date_time) : ?>
                                        <span class="month"><?php echo date('M', strtotime($event_date_time)); ?></span>
                                        <span class="date"><?php echo date('d', strtotime($event_date_time)); ?></span>
                                        <span class="day"><?php echo date('D', strtotime($event_date_time)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="title-and-performers">
                                    <h2 class="card-title"><?php echo esc_html(get_post_meta(get_the_ID(), 'event_title', true)); ?></h2>
                                    <p class="performers-line"><?php echo implode(' • ', $performers_line); ?></p>
                                </div>
                            </div>
                            <div class="more-info-btn">
                                <button type="button">More Info</button>
                            </div>
                            <div class="card-content">
                                <div class="main-info-content">
                                <div class="navbar-style">
                                    <div class="navbar-image-container">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/transplant1.png" alt="Transplants Brewing Logo" class="navbar-logo">
                                    </div>
                                </div>
                                <?php 
                                $event_description = get_post_meta(get_the_ID(), 'event_description', true);
                                $event_price = get_post_meta(get_the_ID(), 'event_price', true);
                                $event_age = get_post_meta(get_the_ID(), 'event_age', true);
                                ?>
                                <?php if (!empty($performers)): ?>
                                    <div class="performers">
                                        <div class="headline-performer">
                                            <?php echo esc_html($performers[0]['name']); ?>
                                        </div>
                                        <div class="supporting-performers">
                                            <?php 
                                            $performers_line = [];
                                            for ($i = 1; $i < count($performers); $i++) {
                                                $performers_line[] = esc_html($performers[$i]['name']);
                                            }
                                            echo implode(' • ', $performers_line);
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <p class="card-text"><?php echo esc_html($event_description); ?></p>
                                <div class="age-price">
                                    <span class="card-text">Price: <?php echo esc_html($event_price); ?></span>
                                    <span class="card-text">Age: <?php echo esc_html($event_age); ?></span>
                                </div>
                                        </div>
                                <button type="button" class="buy-tickets-btn">Buy Tickets</button>
                            </div>
                            <div class="iframe-container" style="display:none;">
                                <a href="#" class="iframe-close-btn">×</a>
                                <iframe class="event-iframe" src="<?php echo esc_url($event_link); ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No events found</p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</div>








<!-- BEVERAGES SECTION -->
<div class="beverages-container">
    <div id="beverages-section" class="beverages-section">
        <!-- Title -->
        <h1 class="beverages-title text-center my-6">Transplants Beer and Beverages</h1>
        
        <div class="beverage-container">
            <?php 
            $args = array(
                'post_type' => 'beverage',
                'posts_per_page' => -1,
                'post_status' => 'publish',  // Fetch only published posts
                'meta_key' => 'beverage_availability', // Check for availability
                'meta_value' => true  // Only fetch beverages that are available
            );

            $beverage_query = new WP_Query($args);

            if($beverage_query->have_posts()) :
                while($beverage_query->have_posts()) :
                    $beverage_query->the_post();
                    if (get_post_status() == 'auto-draft') continue; // Skip auto-drafts
            ?>

            <div class="beverage-post card beverage-card">
                <!-- Beverage Image -->
                <?php 
                $image_url = get_post_meta(get_the_ID(), 'beverage_image_url', true);
                if ($image_url) :
                ?>
                    <img class="card-img-top" src="<?php echo esc_url($image_url); ?>" alt="<?php the_title(); ?>">
                <?php else : ?>
                    <p class="card-text">Image URL is missing.</p>
                <?php endif; ?>

                <div class="card-body">
                    <!-- Title, Description, Type, Alcohol Content -->
                    <?php 
                    $beverage_title = get_post_meta(get_the_ID(), 'beverage_title', true);
                    $description = get_post_meta(get_the_ID(), 'beverage_description', true);
                    $type = get_post_meta(get_the_ID(), 'beverage_type', true);
                    $alcohol_content = get_post_meta(get_the_ID(), 'beverage_alcohol_content', true);
                    ?>

                    <h5 class="card-title"><?php echo esc_html($beverage_title); ?></h5>
                    <p class="card-text"><?php echo esc_textarea($description); ?></p>
                    <p class="card-text"><strong>Alcohol Content:</strong> <?php echo esc_html($alcohol_content); ?>%</p>
                </div>
            </div>

            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
            <p>No beverages found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="location-section">
    <div class="location-container">
        <!-- Google Map -->
        <div class="location-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1dXXXX!2dXXXX!3dXXXX!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xXXXX%3A0xXXXX!2sYour+Brewery+Name!5e0!3m2!1sen!2sus!4v163137XXXX" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>

        <!-- Address -->
        <div class="location-address">
            <p>123 Brewery Lane<br>City, State, ZIP</p>
        </div>

        <!-- Hours -->
        <div class="location-hours">
            <p>Monday-Friday: 9am-5pm<br>Saturday-Sunday: 11am-9pm</p>
        </div>
    </div>
</div>



    </main>
</div> 
                
<?php get_footer(); ?>