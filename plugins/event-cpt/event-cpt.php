<?php
/*
Plugin Name: Event CPT
Description: Custom Post Type for Events and accompanying Gutenberg Block.
Version: 1.0
Author: Your Name
*/

// Register Event CPT
function register_event_cpt() {
    $labels = array(
        'name'                  => _x( 'Events', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Events', 'text_domain' ),
        'name_admin_bar'        => __( 'Event', 'text_domain' ),
        'archives'              => __( 'Event Archives', 'text_domain' ),
        'attributes'            => __( 'Event Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Event:', 'text_domain' ),
        'all_items'             => __( 'All Events', 'text_domain' ),
        'add_new_item'          => __( 'Add New Event', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Event', 'text_domain' ),
        'edit_item'             => __( 'Edit Event', 'text_domain' ),
        'update_item'           => __( 'Update Event', 'text_domain' ),
        'view_item'             => __( 'View Event', 'text_domain' ),
        'view_items'            => __( 'View Events', 'text_domain' ),
        'search_items'          => __( 'Search Event', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Event Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set event image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove event image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as event image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into event', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this event', 'text_domain' ),
        'items_list'            => __( 'Events list', 'text_domain' ),
        'items_list_navigation' => __( 'Events list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter events list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Event', 'text_domain' ),
        'description'           => __( 'Event Custom Post Type', 'text_domain' ),
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
            array( 'event-cpt/event-block', array() )
        ),
    );
    register_post_type( 'event', $args );
}
add_action( 'init', 'register_event_cpt', 0 );

// Register Event Block
function event_block_register() {
    // Register script
    wp_register_script(
        'event-block-editor',
        plugins_url('blocks/event-block/block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        '1.0.0',
        true 
    );

    // Register block editor CSS
    wp_register_style(
        'event-block-editor',
        plugins_url('blocks/event-block/editor.css', __FILE__),
        array('wp-edit-blocks'),
        '1.0.0'
    );

    // Register frontend CSS
    wp_register_style(
        'event-block-frontend',
        plugins_url('blocks/event-block/style.css', __FILE__),
        array(),
        '1.0.0'
    );

    // Register the block type
    register_block_type('event-cpt/event-block', array(
        'editor_script' => 'event-block-editor',
        'editor_style'  => 'event-block-editor',
        'style'         => 'event-block-frontend'
    ));
}
add_action('init', 'event_block_register');

// Register Custom Meta Fields for Event CPT
function register_event_meta() {
    $meta_fields = array(
        'event_title' => 'string',
        'performers' => 'string',
        'event_description' => 'string',
        'event_type' => 'string',
        'event_date' => 'string', // Changed to store only the date
        'event_time' => 'string', // New field for event time
        'event_price' => 'string',
        'event_age' => 'string',
        'event_image_url' => 'string',
        'event_image_url2' => 'string',
        'event_link' => 'string',
        'show_title' => 'boolean',
        'featured_event' => 'boolean'
    );

    foreach ($meta_fields as $field => $type) {
        register_post_meta('event', $field, array(
            'show_in_rest' => true,
            'type' => $type,
            'single' => true,
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
    }
}
add_action('init', 'register_event_meta');
?>

