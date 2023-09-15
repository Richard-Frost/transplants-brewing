<?php

function transplantsbrewing_enqueue_styles() {
    // Enqueue Bootstrap 5 CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
    
    // Enqueue your main style.css
    wp_enqueue_style('transplantsbrewing-main-style', get_stylesheet_uri());
    
    // Enqueue Bootstrap 5 JS bundle
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js', array(), '', true);

    // Enqueue your carousel-init.js after Bootstrap JS
    wp_enqueue_script('carousel-init', get_template_directory_uri() . '/js/carousel-init.js', array('bootstrap-js'), null, true);
}
add_action('wp_enqueue_scripts', 'transplantsbrewing_enqueue_styles');

function theme_enqueue_styles() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto&display=swap', array(), null);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function reorganize_admin_menu() {
    // Create a submenu called 'Stuff' and position it at 71, assuming Beverages and Events are at 69 and 70 respectively
    add_menu_page('Stuff', 'Stuff', 'manage_options', 'stuff', '', 'dashicons-admin-generic', 71);

    // Move 'Media' into 'Stuff'
    add_submenu_page('stuff', 'Media', 'Media', 'upload_files', 'upload.php');
    // Move 'Appearance' into 'Stuff'
    add_submenu_page('stuff', 'Appearance', 'Appearance', 'switch_themes', 'themes.php');
    // Move 'Settings' into 'Stuff'
    add_submenu_page('stuff', 'Settings', 'Settings', 'manage_options', 'options-general.php');
    // Move 'Tools' into 'Stuff'
    add_submenu_page('stuff', 'Tools', 'Tools', 'edit_posts', 'tools.php');
    // Move 'Users' into 'Stuff'
    add_submenu_page('stuff', 'Users', 'Users', 'list_users', 'users.php');
    // Move 'Plugins' into 'Stuff'
    add_submenu_page('stuff', 'Plugins', 'Plugins', 'activate_plugins', 'plugins.php');
    // Move 'Comments' into 'Stuff'
    add_submenu_page('stuff', 'Comments', 'Comments', 'moderate_comments', 'edit-comments.php');
    // Move 'Pages' into 'Stuff'
    add_submenu_page('stuff', 'Pages', 'Pages', 'edit_pages', 'edit.php?post_type=page');
    // Move 'Posts' into 'Stuff'
    add_submenu_page('stuff', 'Posts', 'Posts', 'edit_posts', 'edit.php');

    // Remove these from the main menu
    remove_menu_page('upload.php');
    remove_menu_page('themes.php');
    remove_menu_page('options-general.php');
    remove_menu_page('tools.php');
    remove_menu_page('users.php');
    remove_menu_page('plugins.php');
    remove_menu_page('edit-comments.php');
    remove_menu_page('edit.php?post_type=page');
    remove_menu_page('edit.php');
}
add_action('admin_menu', 'reorganize_admin_menu');

function custom_admin_styles() {
    wp_enqueue_style('custom-admin-styles', get_template_directory_uri() . '/admin.css');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');
add_action('enqueue_block_editor_assets', 'custom_admin_styles');

function disable_gutenberg_fullscreen_mode() {
    $script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } };";
    wp_add_inline_script('wp-blocks', $script);
}
add_action('enqueue_block_editor_assets', 'disable_gutenberg_fullscreen_mode');

function enqueue_custom_scripts() {
    wp_enqueue_script('jquery'); // Enqueue jQuery that comes with WordPress

    // Enqueue your navbar script
    wp_enqueue_script('navbar-script', get_template_directory_uri() . '/js/navbar.js', array('jquery'), null, true);
    
    // Enqueue your info.js script
    wp_enqueue_script('info-js', get_template_directory_uri() . '/js/info.js', array('jquery'), null, true);
    // Enqueue your event-scripts.js script
    wp_enqueue_script('event-scripts', get_template_directory_uri() . '/js/event-scripts.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function enqueue_swiper_scripts() {
    // Enqueue Swiper JS and CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), null, true);

    // Enqueue your Swiper initialization script
    wp_enqueue_script('swiper-init', get_template_directory_uri() . '/js/swiper-init.js', array('swiper-js'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_scripts');

show_admin_bar(false);

?>
