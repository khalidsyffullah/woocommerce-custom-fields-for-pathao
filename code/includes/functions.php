<?php
function woo_get_zones() {
    global $wpdb;
    $table_zones = $wpdb->prefix . 'woo_zones';
    $zones = $wpdb->get_results("SELECT id, pathao_zone_id, name FROM $table_zones", ARRAY_A);
    // $zone_options = array('' => '-- Select Zone --');
    foreach ($zones as $zone) {
        $zone_options[$zone['pathao_zone_id']] = $zone['name'];
    }
    return $zone_options;
}
function woo_get_zones_by_city($city_id) {
    global $wpdb;
    $table_zones = $wpdb->prefix . 'woo_zones';
    $zones = $wpdb->get_results($wpdb->prepare("SELECT id, pathao_zone_id, name FROM $table_zones WHERE city_id = %d", $city_id), ARRAY_A);

    // $zone_options = array('' => '-- Select Zone --');

    foreach ($zones as $zone) {
        $zone_options[$zone['pathao_zone_id']] = $zone['name'];
    }

    return $zone_options;
}
// Function to get city data by ID
function woo_get_city_data($city_id) {
    global $wpdb;
    $table_cities = $wpdb->prefix . 'woo_cities';
    $city_data = $wpdb->get_row($wpdb->prepare("SELECT id, name, pathao_city_id FROM $table_cities WHERE id = %d", $city_id), ARRAY_A);
    return $city_data;
}
function woo_get_city_data_for_order($city_id) {
    global $wpdb;
    $table_cities = $wpdb->prefix . 'woo_cities';
    $city_data = $wpdb->get_row($wpdb->prepare("SELECT name FROM $table_cities WHERE pathao_city_id = %d", $city_id), ARRAY_A);
    return $city_data;
}
// Function to get zone data by ID
function woo_get_zone_data($zone_id) {
    global $wpdb;
    $table_zones = $wpdb->prefix . 'woo_zones';
    $zone_data = $wpdb->get_row($wpdb->prepare("SELECT id, name, pathao_zone_id FROM $table_zones WHERE id = %d", $zone_id), ARRAY_A);
    return $zone_data;
}

function woo_get_zone_data_for_order($zone_id) {
    global $wpdb;
    $table_zones = $wpdb->prefix . 'woo_zones';
    $zone_data = $wpdb->get_row($wpdb->prepare("SELECT name FROM $table_zones WHERE pathao_zone_id = %d", $zone_id), ARRAY_A);
    return $zone_data;
}



?>