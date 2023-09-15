<?php
/**
 * Template function to generate HTML for the billing_city dropdown field.
 */
function generate_billing_city_html($selected_value) {
    $field_id = 'billing_city';
    $field_label = __('City', 'woo-custom-dropdown-fields');
    $city_options = woo_get_cities(); // Replace with your actual function to get city options
    return generate_custom_dropdown_html($field_id, $field_label, $city_options, $selected_value);
}

/**
 * Template function to generate HTML for the billing_zone dropdown field.
 */
function generate_billing_zone_html($selected_value) {
    $field_id = 'billing_zone';
    $field_label = __('Zone', 'woo-custom-dropdown-fields');
    $zone_options = woo_get_zones(); // Replace with your actual function to get zone options
    return generate_custom_dropdown_html($field_id, $field_label, $zone_options, $selected_value);
}

/**
 * Template function to generate HTML for the shipping_city dropdown field.
 */
function generate_shipping_city_html($selected_value) {
    $field_id = 'shipping_city';
    $field_label = __('City', 'woo-custom-dropdown-fields');
    $city_options = woo_get_cities(); // Replace with your actual function to get city options
    return generate_custom_dropdown_html($field_id, $field_label, $city_options, $selected_value);
}

/**
 * Template function to generate HTML for the shipping_zone dropdown field.
 */
function generate_shipping_zone_html($selected_value) {
    $field_id = 'shipping_zone';
    $field_label = __('Zone', 'woo-custom-dropdown-fields');
    $zone_options = woo_get_zones(); // Replace with your actual function to get zone options
    return generate_custom_dropdown_html($field_id, $field_label, $zone_options, $selected_value);
}
