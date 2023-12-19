<?php
/*
Plugin Name: Beverage CPT
Description: Custom Post Type for Beverages and accompanying Gutenberg Block.
Version: 1.0
Author: Your Name
*/

// Register Beverage CPT
function register_beverage_cpt() {
    $labels = array(
        'name'                  => _x( 'Beverages', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Beverage', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Beverages', 'text_domain' ),
        'name_admin_bar'        => __( 'Beverage', 'text_domain' ),
        'archives'              => __( 'Beverage Archives', 'text_domain' ),
        'attributes'            => __( 'Beverage Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Beverage:', 'text_domain' ),
        'all_items'             => __( 'All Beverages', 'text_domain' ),
        'add_new_item'          => __( 'Add New Beverage', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Beverage', 'text_domain' ),
        'edit_item'             => __( 'Edit Beverage', 'text_domain' ),
        'update_item'           => __( 'Update Beverage', 'text_domain' ),
        'view_item'             => __( 'View Beverage', 'text_domain' ),
        'view_items'            => __( 'View Beverages', 'text_domain' ),
        'search_items'          => __( 'Search Beverage', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Beverage Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set beverage image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove beverage image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as beverage image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into beverage', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this beverage', 'text_domain' ),
        'items_list'            => __( 'Beverages list', 'text_domain' ),
        'items_list_navigation' => __( 'Beverages list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter beverages list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Beverage', 'text_domain' ),
        'description'           => __( 'Beverage Custom Post Type', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'template' => array(
            array( 'beverage-cpt/beverage-block', array() )
        ),
    );
    register_post_type( 'beverage', $args );
}
add_action( 'init', 'register_beverage_cpt', 0 );

// Register Beverage Block
function beverage_block_register() {
    wp_register_script(
        'beverage-block',
        plugins_url('blocks/beverage-block/block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        '1.0.0'
    );

    register_block_type('beverage-cpt/beverage-block', array(
        'editor_script' => 'beverage-block',
        'editor_style'  => 'beverage-block-editor-style', // Enqueues editor.css if exists
        'style'         => 'beverage-block-frontend-style' // Enqueues style.css if exists
    ));
}
add_action('init', 'beverage_block_register');

function register_beverage_meta() {
    $meta_fields = array(
        'beverage_title' => 'string',
        'beverage_description' => 'string',
        'beverage_type' => 'string',
        'beverage_alcohol_content' => 'string',
        'beverage_image_url' => 'string',
        'beverage_availability' => 'boolean',
        'beverage_position' => 'integer', // Add 'beverage_position' with 'integer' type
    );

    foreach ($meta_fields as $meta_key => $type) {
        register_post_meta('beverage', $meta_key, array(
            'show_in_rest' => true,
            'type' => $type,
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
        error_log("Registered meta key: {$meta_key} with type: {$type}");
    }
}
add_action('init', 'register_beverage_meta');

function beverage_cpt_enqueue_block_styles() {
    // Enqueue block editor styles
    wp_enqueue_style(
        'beverage-block-editor-style',
        plugins_url('blocks/beverage-block/editor.css', __FILE__),
        [],
        '1.0.0'
    );
}
add_action('enqueue_block_assets', 'beverage_cpt_enqueue_block_styles');

function beverage_cpt_admin_styles($hook) {
    // Only enqueue styles for the 'On Tap' page in the backend
    if ('beverage_page_beverage_inventory' != $hook) {
        return;
    }
    
    wp_enqueue_style(
        'beverage-cpt-admin-style',
        plugins_url('admin-style.css', __FILE__),
        [],
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'beverage_cpt_admin_styles');

function beverage_inventory_page() {
    add_submenu_page(
        'edit.php?post_type=beverage', 
        'On Tap',               // Page title
        'On Tap',               // Menu title
        'manage_options', 
        'beverage_inventory',   // It's okay to leave the slug as is, or you can rename it to 'beverage_on_tap'
        'beverage_inventory_callback'
    );
}
add_action('admin_menu', 'beverage_inventory_page');

function beverage_inventory_callback() {
    // Query all the beverages
    $args = array(
        'post_type' => 'beverage',
        'posts_per_page' => -1,
    );
    $beverages = new WP_Query($args);

    if (isset($_POST['update_inventory'])) {
        while ($beverages->have_posts()) : $beverages->the_post();
            $beverage_id = get_the_ID();
            $availability = isset($_POST['availability_' . $beverage_id]) && $_POST['availability_' . $beverage_id] === "true" ? true : false;
            update_post_meta($beverage_id, 'beverage_availability', $availability);

            // Update the 'beverage_position' meta field as an integer
            $position = isset($_POST['position_' . $beverage_id]) ? intval($_POST['position_' . $beverage_id]) : 0;
            update_post_meta($beverage_id, 'beverage_position', $position);
        endwhile;
        echo '<div class="notice notice-success is-dismissible"><p>Inventory updated successfully.</p></div>';
    }
    echo '<div class="beverage-center-container">'; 
    echo '<div class="beverage-inventory-container">';
    echo '<div class="beverage-inventory-title">On Tap</div>';
    echo '<form method="post">';
    
    while ($beverages->have_posts()) : $beverages->the_post();
        $beverage_id = get_the_ID();
        $availability = get_post_meta($beverage_id, 'beverage_availability', true);
        $position = get_post_meta($beverage_id, 'beverage_position', true);
        $beverage_title = get_post_meta($beverage_id, 'beverage_title', true);
    
        // Dropdown select menu for availability
        echo '<div class="beverage-item">';
        echo '<label>' . esc_html($beverage_title) . '</label>';
        echo '<select name="availability_' . $beverage_id . '">';
        echo '<option value="true" ' . selected($availability, true, false) . '>Available</option>';
        echo '<option value="false" ' . selected($availability, false, false) . '>Not Available</option>';
        echo '</select>';

        // Dropdown select menu for position
        echo '<select name="position_' . $beverage_id . '">';
        for ($i = 1; $i <= 25; $i++) {
            echo '<option value="' . $i . '" ' . selected($position, $i, false) . '>' . $i . '</option>';
        }
        echo '</select>';
        echo '</div>';
    endwhile;

    echo '<div class="submit-button"><input type="submit" name="update_inventory" class="button button-primary" value="Update Inventory"></div>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    wp_reset_postdata();
}

function beverage_inventory_page_2() {
    add_submenu_page(
        'edit.php?post_type=beverage',
        'On Tap 2',               // Page title
        'On Tap 2',               // Menu title
        'manage_options',
        'beverage_inventory_2',   // Slug
        'beverage_inventory_callback_2'
    );
}
add_action('admin_menu', 'beverage_inventory_page_2');

function beverage_inventory_callback_2() {
    // Enqueue the necessary scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');

    // Handle form submission
    if (isset($_POST['update_taps'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'beverage_') === 0) {
                $parts = explode('_', $key);
                $beverage_id = intval($parts[1]);
                $property = $parts[2];

                if ($property == 'availability') {
                    update_post_meta($beverage_id, 'beverage_availability', $value === 'true' ? true : false);
                } elseif ($property == 'position') {
                    update_post_meta($beverage_id, 'beverage_position', intval($value));
                }
            }
        }
    }

    // Fetch beverages
    $args = array('post_type' => 'beverage', 'posts_per_page' => -1);
    $beverages = new WP_Query($args);

    // Prepare beverages for display
    $available_beverages = array_fill(1, 25, null);
    $unavailable_beverages = [];

    while ($beverages->have_posts()) : $beverages->the_post();
        $beverage_id = get_the_ID();
        $availability = get_post_meta($beverage_id, 'beverage_availability', true);
        $position = get_post_meta($beverage_id, 'beverage_position', true);
        $beverage_image_url = get_post_meta($beverage_id, 'beverage_image_url', true);

        if ($availability) {
            $available_beverages[intval($position)] = ['id' => $beverage_id, 'image' => $beverage_image_url];
        } else {
            $unavailable_beverages[] = ['id' => $beverage_id, 'image' => $beverage_image_url];
        }
    endwhile;
    
    ?>
    <style>
    .on-tap-2-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 80%;  /* This will make the whole container 80% of its parent container width. Adjust this value according to your needs. */
        margin: 0 auto;  /* Centering it horizontally */
        overflow-x: hidden;  /* Ensure no horizontal scrolling occurs on the main container */
    }

    .black-boxes-container {
    display: grid;
    grid-template-columns: repeat(10, 1fr); /* Now it will have 10 columns */
    gap: 10px;
}


    .numbered-box {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100px;  /* Doubled width */
        height: 160px; /* Doubled height */
        background: black;
        color: white;
        position: relative;
    }

    .beverage-image {
        max-height: 150px;  /* For the available section */
        width: auto;
        cursor: pointer;
        position: absolute;
    }

    .unavailable-section .beverage-image {
    position: relative; 
    max-height: 80px;  /* Adjusted height of the image */
    margin: 10px;      /* Added margin around the image */
}


    .unavailable-section {
    display: flex;
    flex-direction: column;
    align-items: start;
    margin-top: 20px;
    border: 2px dashed gray;
    padding: 10px;
    justify-content: space-between;
    width: 100%;  /* Takes up the full width of the parent container */
    overflow-x: auto;  /* Enable horizontal scrolling if content exceeds */
}

.beverage-images-container {
    display: flex;
    flex-wrap: nowrap; /* Prevents the items from wrapping and ensures a horizontal layout */
    overflow-x: auto;  /* Allow for horizontal scrolling */
    width: 100%;       /* Full width of parent */
}


    .unavailable-section .beverage-images-container {
        overflow-x: auto;       /* Enable horizontal scrolling */
        white-space: nowrap;    /* Keep images on a single line */
        width: 100%;            /* Full width of the container */
    }

    .submit-button {
        margin-top: 20px;
    }
</style>


    <div class="on-tap-2-container">
        <h2>On Tap 2</h2>
        <form method="post">
            <div class="black-boxes-container">
                <?php
                for ($i = 1; $i <= 30; $i++) {
                    echo '<div class="numbered-box" data-box="' . $i . '">' . $i;
                    if ($available_beverages[$i]) {
                        $beverage = $available_beverages[$i];
                        echo '<input type="hidden" name="beverage_' . $beverage['id'] . '_availability" value="true">';
                        echo '<input type="hidden" name="beverage_' . $beverage['id'] . '_position" value="' . $i . '">';
                        echo '<img src="' . esc_url($beverage['image']) . '" class="beverage-image" data-beverage-id="' . $beverage['id'] . '">';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="unavailable-section">
                <h3>Unavailable Beverages</h3>
                <div class="beverage-images-container">
                    <?php
                    foreach ($unavailable_beverages as $beverage) {
                        echo '<div class="beverage-image-container">';
                        echo '<input type="hidden" name="beverage_' . $beverage['id'] . '_availability" value="false">';
                        echo '<input type="hidden" name="beverage_' . $beverage['id'] . '_position" value="0">';
                        echo '<img src="' . esc_url($beverage['image']) . '" class="beverage-image" data-beverage-id="' . $beverage['id'] . '">';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="submit-button">
                <input type="submit" name="update_taps" class="button button-primary" value="Update Taps">
            </div>
        </form>
    </div>

    <script>
        jQuery(function($) {
            $('.beverage-image').draggable({
                revert: "invalid",
                containment: ".on-tap-2-container",
                helper: "clone",
                cursor: "move"
            });

            $('.numbered-box, .unavailable-section').droppable({
                accept: ".beverage-image",
                hoverClass: "ui-state-hover",
                drop: function(event, ui) {
                    var isUnavailableSection = $(this).hasClass('unavailable-section');

                    if(isUnavailableSection) {
                        var droppedBeverage = $(ui.draggable);
                        droppedBeverage.appendTo($(this).find('.beverage-images-container'));
                        $('input[name="beverage_' + droppedBeverage.data('beverage-id') + '_availability"]').val('false');
                        $('input[name="beverage_' + droppedBeverage.data('beverage-id') + '_position"]').val('0');
                    } else {
                        var droppedBeverage = $(this).find('img.beverage-image');
                        if(droppedBeverage.length) {
                            droppedBeverage.appendTo(ui.draggable.parent());
                            $('input[name="beverage_' + droppedBeverage.data('beverage-id') + '_availability"]').val('false');
                            $('input[name="beverage_' + droppedBeverage.data('beverage-id') + '_position"]').val('0');
                        }

                        $(this).append($(ui.draggable));
                        $('input[name="beverage_' + $(ui.draggable).data('beverage-id') + '_availability"]').val('true');
                        $('input[name="beverage_' + $(ui.draggable).data('beverage-id') + '_position"]').val($(this).data('box'));
                    }
                }
            });
        });
    </script>
    <?php
}

// Ensure that you add the above function to the correct action or shortcode to display it.
