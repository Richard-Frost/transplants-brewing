<?php get_header(); ?>

<div class="fixed-carousel">
    <div class="black-overlay"></div>

    <!-- Swiper container -->
    <div class="swiper-container">
        <!-- Wrapper for slides -->
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <img src="/wp-content/themes/transplantsbrewing/assets/images/20220208_203650 (1).jpg"  alt="Slide 1">
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <img src="/wp-content/themes/transplantsbrewing/assets/images/20220208_211534.jpg"  alt="Slide 2">
            </div>
        </div>
    </div>

    <!-- Logo Overlay -->
    <div class="logo-container">
        <img src="/wp-content/themes/transplantsbrewing/assets/images/transplant.png" alt="Transplant Logo" class="carousel-logo">
    </div>
</div>

<div class="content-scrolling-over">
    <!-- Main Content -->
    <main class="main-content-container">
        <!-- Bootstrap Navbar with Custom Classes -->
        <nav class="navbar navbar-expand-lg custom-navbar" id="navbar-section">
            <div class="navbar-content">
                <!-- Navbar Brand -->
                <a class="navbar-brand ms-4" href="#">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/transplant1.png" alt="Transplants Brewing Logo" class="navbar-logo">
                </a>

                <!-- Toggler for responsive navigation -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCustom" aria-controls="navbarCustom" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="navbarCustom">
                    <ul class="navbar-nav ms-5"> <!-- Increased spacing from the logo -->
                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="#">Home</a>
                        </li>

                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="#events-section">Events</a>
                        </li>

                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="#beverages-section">Beer</a>
                        </li>

                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="https://linktr.ee/transplantsbrewing">Linktree</a>
                        </li>

                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="#shop-section">Shop</a>
                        </li>
                        <li class="nav-item me-4">
                            <a class="nav-link text-white fs-3" href="https://open.spotify.com/playlist/2mXh5TcKY3wFZGfSpySujY?si=9aac828375b14153&nd=1" target="_blank">Spotify</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <!-- Transplants Events Section -->
        <div class="events-container">
            <section id="events-section" class="events-section" aria-labelledby="events-header">
                <img src="/wp-content/themes/transplantsbrewing/assets/images/events.png" alt="Image Description" class="centered-small-image" style="display: block; margin: auto; max-width: 200px; height: auto;" loading="lazy">
                <h1 id="events-header" style="text-align: center; margin-top: 5px; margin-bottom: 20px;">TRANSPLANTS EVENTS</h1>
                <div class="dropdown-wrapper">
                    <label for="event-type-select" style="margin-bottom: 5px; display: block;">Select Event Type:</label>
                    <select id="event-type-select" style="margin-bottom: 20px;">
                        <option value="all">All Events</option>
                        <option value="concert">Concert</option>
                        <option value="dj">DJ</option>
                        <option value="comedy">Comedy</option>
                        <option value="burlesque">Burlesque</option>
                        <option value="wrestling">Wrestling</option>
                    </select>
                </div>
                <div id="no-events-message"></div>
                
                <div class="event-container" role="list">
                    <?php 
                    $current_date = date('Y-m-d'); // Current date in 'Y-m-d' format
                    $args = array(
                        'post_type' => 'event',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_key' => 'event_date',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array(
                                'key' => 'event_date',
                                'value' => $current_date,
                                'compare' => '>=',
                                'type' => 'DATE'
                            )
                        )
                    );
                    $event_query = new WP_Query($args);
                
                    $all_events = [];
                    $comedy_events = [];
                    $burlesque_events = [];
                    $concerts_events = [];
                    $wrestling_events = [];
                    $featured_events = [];
                
                    if ($event_query->have_posts()) : 
                        while ($event_query->have_posts()) : 
                            $event_query->the_post();
                        
                            $event_id = get_the_ID();
                            $event_link = get_post_meta($event_id, 'event_link', true);
                            $event_date = get_post_meta($event_id, 'event_date', true);
                            $event_time = get_post_meta($event_id, 'event_time', true);
                            $event_type = get_post_meta($event_id, 'event_type', true);
                            $featured_event = get_post_meta($event_id, 'featured_event', true) == '1';
                            $date_object = DateTime::createFromFormat('Y-m-d', $event_date);
                            $day_of_week = $date_object->format('D'); // Abbreviated day of the week
                            $formatted_date = $date_object->format('M j'); // Abbreviated month and day
                            $event_image_url = get_post_meta($event_id, 'event_image_url', true);
                        
                            $performers = json_decode(get_post_meta($event_id, 'performers', true), true);
                            $performers_data = array();
                            foreach ($performers as $performer) {
                                $performers_data[] = array(
                                    'name' => esc_html($performer['name']),
                                );
                            }
                        
                            // Create an array for the current event
                            $current_event = [
                                'id' => $event_id,
                                'link' => $event_link,
                                'date' => $formatted_date,
                                'day_of_week' => $day_of_week,
                                'time' => $event_time,
                                'type' => $event_type,
                                'featured' => $featured_event,
                                'performers_data' => $performers_data,
                                'event_image_url' => $event_image_url, 
                                'event_description' => get_post_meta($event_id, 'event_description', true)
                            ];
                        
                            // Add to all_events
                            $all_events[] = $current_event;
                        
                            // Add to specific category arrays based on event_type
                            switch(strtolower($event_type)) {
                                case 'comedy':
                                    $comedy_events[] = $current_event;
                                    break;
                                case 'burlesque':
                                    $burlesque_events[] = $current_event;
                                    break;
                                case 'concert':
                                    $concerts_events[] = $current_event;
                                    break;
                                case 'wrestling':
                                    $wrestling_events[] = $current_event;
                                    break;
                            }
                        
                            // Add to featured_events if featured
                            if ($featured_event) {
                                $featured_events[] = $current_event;
                            }
                            ?>
                            <div class="event-posts-container" role="listitem" data-event-type="<?php echo esc_attr(strtolower($event_type)); ?>" data-featured-image="<?php echo esc_url($event_image_url); ?>">
                                <div class="event-post" aria-labelledby="event-title-<?php echo $event_id; ?>" 
                                     data-performers='<?php echo json_encode($performers_data); ?>'
                                     data-event-id="<?php echo esc_attr($event_id); ?>" 
                                     data-event-link="<?php echo esc_url($event_link); ?>"
                                     data-description="<?php echo esc_attr(get_post_meta($event_id, 'event_description', true)); ?>"
                                     data-price="<?php echo esc_attr(get_post_meta($event_id, 'event_price', true)); ?>"
                                     data-age="<?php echo esc_attr(get_post_meta($event_id, 'event_age', true)); ?>">
                                    <div class="image-container">
                                        <?php if ($event_image_url) : ?>
                                            <img class="card-img-top" src="<?php echo esc_url($event_image_url); ?>" alt="Event poster for <?php echo esc_attr($event_type); ?>" loading="lazy">
                                        <?php else : ?>
                                            <p class="card-text">Image URL is missing.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="date-and-title-container">
                                        <?php if ($event_image_url) : ?>
                                            <img class="event-bg-image" src="<?php echo esc_url($event_image_url); ?>" alt="Event Background Image" loading="lazy">
                                        <?php endif; ?>
                                        <div class="date-box">
                                            <span class="day"><?php echo $day_of_week; ?></span>
                                            <span class="date-full"><?php echo $formatted_date; ?></span>
                                            <span class="time"><?php echo esc_html($event_time); ?></span>
                                        </div>
                                        <div class="more-info">
                                            <span>MORE INFO</span>
                                        </div>
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
        <!-- Swiper Modal -->
        <div class="event-modal">
            <button class="event-modal-close" aria-label="Close event modal">
            <i class="fas fa-arrow-left"></i> Back<!-- Font Awesome left arrow icon -->
            </button>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <!-- Dynamic slides added here by JS -->
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
        </div>

<!-- Featured Events Section -->
<div class="outside-container">
    <div class="content-container">
        <h1>COMING SOON</h1>

        <div class="featured-event-notification" role="region" aria-labelledby="featured-events-header">
            <!-- Featured Events Section -->
            <section id="featured-events-section" role="navigation">
                <?php 
                if (!empty($featured_events)) {
                    // Get the first 4 events
                    $first_four_events = array_slice($featured_events, 0, 4);
                
                    foreach ($first_four_events as $event) {
                        // Use the same date and time format as in the Event section
                        $date_object = DateTime::createFromFormat('M d', $event['date']);
                        $day_of_week = $date_object->format('D'); // Abbreviated day of the week
                        $formatted_date = $date_object->format('M d'); // Abbreviated month and day
                        $formatted_time = esc_html($event['time']);
                        ?>
                        <div class="horizontal-event-card" role="article">
                            <div class="event-post" 
                                 aria-labelledby="event-title-<?php echo $event['id']; ?>" 
                                 data-performers='<?php echo json_encode($event['performers_data']); ?>'
                                 data-event-link="<?php echo esc_url($event['link']); ?>"
                                 data-event-id="<?php echo $event['id']; ?>"
                                 data-description="<?php echo esc_attr($event['event_description']); ?>"
                                 data-price="<?php echo esc_attr(get_post_meta($event['id'], 'event_price', true)); ?>"
                                 data-age="<?php echo esc_attr(get_post_meta($event['id'], 'event_age', true)); ?>">
                                <div class="image-container">
                                    <?php if ($event['event_image_url']) : ?>
                                        <img class="card-img-top" src="<?php echo esc_url($event['event_image_url']); ?>" alt="Event poster" loading="lazy">
                                    <?php else : ?>
                                        <p class="card-text">Image URL is missing.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="date-and-title-container">
                                    <?php if ($event['event_image_url']) : ?>
                                        <img class="event-bg-image" src="<?php echo esc_url($event['event_image_url']); ?>" alt="Event Image" loading="lazy">
                                    <?php endif; ?>
                                    <div class="date-box">
                                        <span class="day"><?php echo $day_of_week; ?></span>
                                        <span class="date-full"><?php echo $formatted_date; ?></span>
                                        <span class="time-full"><?php echo $formatted_time; ?></span>
                                    </div>
                                    <div class="more-info">
                                        <span>More Info</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No featured events found</p>';
                }
                ?>
                <p> This site uses Google Analytics </p>
            </section>
        </div>
    </div>
    <div class="down-arrow-container">
        <i id="down-arrow" class="fas fa-arrow-down"></i>
    </div>
</div>

 
        <!-- BEVERAGES SECTION -->
        <div class="beverages-container" role="region" aria-label="Beverage Section">
            <div id="beverages-section" class="beverages-section">
                <div class="on-tap-title">
                    <img src="/wp-content/themes/transplantsbrewing/assets/images/filbert.png" alt="Image Description" class="centered-small-image" style="display: block; margin: auto; max-width: 200px; height: auto;">
                </div>

                <!-- Title -->
                <h1 class="beverages-title text-center my-6">ON TAP</h1>

                <div class="beverage-display-container">
                    <?php
                    $args = array(
                        'post_type' => 'beverage',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_key' => 'beverage_position',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array(
                                'key' => 'beverage_availability',
                                'value' => true,
                                'compare' => '=',
                            ),
                        ),
                    );
                
                    $beverage_query = new WP_Query($args);
                
                    if ($beverage_query->have_posts()) :
                        while ($beverage_query->have_posts()) : $beverage_query->the_post();
                            $image_url = get_post_meta(get_the_ID(), 'beverage_image_url', true);
                            $beverage_title = get_post_meta(get_the_ID(), 'beverage_title', true);
                            $description = get_post_meta(get_the_ID(), 'beverage_description', true);
                            $type = get_post_meta(get_the_ID(), 'beverage_type', true);
                            $alcohol_content = get_post_meta(get_the_ID(), 'beverage_alcohol_content', true);
                            $position = get_post_meta(get_the_ID(), 'beverage_position', true);
                            ?>
                            <div class="beverage-frame" data-title="<?php echo esc_html($beverage_title); ?>" data-description="<?php echo esc_textarea($description); ?>" data-abv="<?php echo esc_html($alcohol_content); ?>%">
                                <?php if ($image_url) : ?>
                                    <img class="beverage-image" src="<?php echo esc_url($image_url); ?>" alt="Poster representing the beverage <?php echo esc_attr($beverage_title); ?>, a <?php echo esc_attr($type); ?> with an alcohol content of <?php echo esc_attr($alcohol_content); ?>%, available on tap at Transplants Brewing" loading="lazy">
                                <?php else : ?>
                                    <p class="card-text">Image URL is missing.</p>
                                <?php endif; ?>
                            </div>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo 'No available beverages found.';
                    endif;
                    ?>
                </div>
            </div>
            <!-- Swiper Modal -->
            <div class="beverage-modal">
                <button class="beverage-modal-close" aria-label="Close beverage modal">X Close</button>
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <!-- Dynamic slides added here by JS -->
                    </div>
                    <!-- Add the Swiper navigation buttons here -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </div>

        <section id="shop-section" class="shop-background">
            <div class="shop-content">
                <button id="openShopIframe">ORDER BEER</button>
            </div>
            <div id="iframeOverlay" class="iframe-overlay" style="display:none;">
                <iframe id="shopIframe" src="https://shop.transplantsbrewing.com/s/order#3" style="display:none; width:90%; height:80%; border: none;"></iframe>
                    <button id="closeIframe" class="close-iframe-btn">
                        <span class="chevron">&laquo;</span> 
                        <br>
                        <span class="back-text">Back</span>
                    </button>

            </div>
        </section>

        <div class="location-section" role="region" aria-label="Location and Hours of Operation for Transplants Brewing Company">
            <h1>Transplants Brewing Company</h1>
    
            <div class="location-container">
                <!-- Address -->
                <div class="location-address" role="contentinfo" aria-label="Address of Transplants Brewing Company">
                    <p>40242 La Quinta Ln #101
                    <br>Palmdale, CA 93551
                    <br>(661) 266-7911
                    </p>
                </div>

                <!-- Hours -->
                <div class="location-hours" role="contentinfo" aria-label="Operating Hours of Transplants Brewing Company">
                    <p>MON-THURS- 3PM-12AM
                    <br>FRI-SAT- 12PM-1AM
                    <br>SUN 12PM-12AM
                    </p>
                </div>

                <!-- Image and Map Container -->
                <div class="location-map-container">
                    <!-- Google Map -->
                    <div class="location-map" role="presentation">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3283.595478205924!2d-118.14222682494251!3d34.614389272950454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c25992727beb7d%3A0xa7ba31fb06eb7bab!2sTransplants%20Brewing%20Company!5e0!3m2!1sen!2sus!4v1696257277982!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map to Transplants Brewing Company" ></iframe>
                </div>
            </div>
        </div>
    
    </main>
</div> 
                
<?php get_footer(); ?>


