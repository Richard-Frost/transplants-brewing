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
        'supports'              => array( 'editor', 'thumbnail', 'custom-fields' ),
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
        array('wp-blocks', 'wp-element', 'wp-editor'),
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
        'beverage_availability' => 'boolean'
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

    add_action('enqueue_block_assets', 'beverage_cpt_enqueue_block_styles');


    // Enqueue front end and editor block styles
    wp_enqueue_style(
        'beverage-block-frontend-style',
        plugins_url('blocks/beverage-block/style.css', __FILE__),
        [],
        '1.0.0'
    );
}
add_action('enqueue_block_assets', 'beverage_cpt_enqueue_block_styles');



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
        foreach ($beverages->posts as $beverage) {
            $availability = $_POST['availability_' . $beverage->ID] === "true" ? true : false;
            update_post_meta($beverage->ID, 'beverage_availability', $availability);
        }
        echo '<div class="notice notice-success is-dismissible"><p>Inventory updated successfully.</p></div>';
    }
    echo '<div class="beverage-center-container">'; 
    echo '<div class="beverage-inventory-container">';
    echo '<div class="beverage-inventory-title">On Tap</div>';
    echo '<form method="post">';
    
    while ($beverages->have_posts()) : $beverages->the_post();
        $availability = get_post_meta(get_the_ID(), 'beverage_availability', true);
        echo '<div class="beverage-item">';
        $beverage_title = get_post_meta(get_the_ID(), 'beverage_title', true);
        echo '<label>' . esc_html($beverage_title) . '</label>';
        echo '<input type="radio" name="availability_' . get_the_ID() . '" value="true" ' . checked($availability, true, false) . '> Available ';
        echo '<input type="radio" name="availability_' . get_the_ID() . '" value="false" ' . checked($availability, false, false) . '> Not Available';
        echo '</div>';
    endwhile;
    
    echo '<div class="submit-button"><input type="submit" name="update_inventory" class="button button-primary" value="Update Inventory"></div>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    wp_reset_postdata();
}


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
