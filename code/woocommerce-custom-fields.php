<?php
/**
 * Plugin Name: Woo Custom dropdown fields
 * Plugin URI: Your Plugin's URL
 * Description: A custom cascade dropdown field for city and zone into checkout page.
 * Version: 1.0
 * Author: Khalid Syffullah
 * Author URI: Your Website
 * Text Domain: woo-custom-dropdown-fields
 * Domain Path: /languages
 */
// Plugin activation hook for creating tables
register_activation_hook(__FILE__, 'woo_create_woo_cities_table');
register_activation_hook(__FILE__, 'woo_create_woo_zones_table');

// Plugin deactivation hook for deleting tables
register_deactivation_hook(__FILE__, 'woo_delete_plugin_tables');

// Function to delete plugin tables
function woo_delete_plugin_tables() {
    global $wpdb;
    $table_cities = $wpdb->prefix . 'woo_cities';
    $table_zones = $wpdb->prefix . 'woo_zones';


    // Delete both tables
    $wpdb->query("DROP TABLE IF EXISTS $table_zones");
    $wpdb->query("DROP TABLE IF EXISTS $table_cities");

}

// Register the deactivation hook
register_deactivation_hook(__FILE__, 'woo_delete_plugin_tables');




// Include the admin-menu.php file
require_once(plugin_dir_path(__FILE__) . 'widget-controller.php');
// Include the database.php file
require_once(plugin_dir_path(__FILE__) . 'includes/database.php');

//include functions.php file
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');


// Enqueue necessary stylesheets and scripts
function woo_custom_fields_plugin_enqueue_scripts() {
    wp_enqueue_style('woo-custom-fields-plugin-style', plugins_url('/includes/assets/css/style.css', __FILE__));
    wp_enqueue_script('woo-custom-fields-plugin-script', plugins_url('/includes/assets/js/script.js', __FILE__), array('jquery'), '1.0', true);

    // Correct the variable name to 'ajaxurl'
    wp_localize_script('woo-custom-fields-plugin-script', 'woo_custom_fields_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'woo_custom_fields_plugin_enqueue_scripts');








