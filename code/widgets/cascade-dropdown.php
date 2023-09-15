<?php
// In your cascade-dropdown.php file

// Hook into the checkout fields
add_filter('woocommerce_checkout_fields', 'add_custom_checkout_fields');

function add_custom_checkout_fields($fields) {
    // Get the city and zone options
    $city_options = woo_get_cities();
    $zone_options = woo_get_zones();
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_last_name']);

    // Add the custom fields to the billing sections
    $fields['billing']['billing_city'] = array(
        'type' => 'select',
        'label' => __('City', 'woo-custom-dropdown-fields'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => $city_options,
    );

    $fields['billing']['billing_zone'] = array(
        'type' => 'select',
        'label' => __('Zone', 'woo-custom-dropdown-fields'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => $zone_options,
    );

    
    // Add the country dropdown for billing
    $fields['billing']['billing_country'] = array(
        'type' => 'country',
        'label' => __('Country', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
    );

    // Move the custom 'billing_zone' field before the 'billing_postcode' field
    $billing_fields = $fields['billing'];
    unset($fields['billing']);
    $fields['billing']['billing_first_name'] = $billing_fields['billing_first_name'];
    $fields['billing']['billing_address_1'] = $billing_fields['billing_address_1'];
    $fields['billing']['billing_city'] = $billing_fields['billing_city'];
    $fields['billing']['billing_zone'] = $billing_fields['billing_zone'];
    $fields['billing']['billing_country'] = $billing_fields['billing_country'];
    $fields['billing']['billing_phone'] = $billing_fields['billing_phone'];
    $fields['billing']['billing_email'] = $billing_fields['billing_email'];

    // Set the label for the 'order_comments' field
    $fields['order']['order_comments']['label'] = __('Special notes', 'woocommerce');

    return $fields;
}


// Hook into the order details to display city and zone names
add_action('woocommerce_order_details_after_order_table', 'display_city_and_zone_names', 10, 1);

function display_city_and_zone_names($order) {
    // Get the order ID
    $order_id = $order->get_id();

    // Get the billing city ID from the order
    $billing_city_id = get_post_meta($order_id, '_billing_city', true);

    // Get the billing zone ID from the order
    $billing_zone_id = get_post_meta($order_id, '_billing_zone', true);

    // Get the corresponding city and zone names
    $billing_city_data = woo_get_city_data_for_order($billing_city_id);
    $billing_city_name = $billing_city_data['name'];
    $billing_main_city_id = $billing_city_data['main_city_id'];

    $billing_zone_data = woo_get_zone_data_for_order($billing_zone_id);
    $billing_zone_pathao_id = $billing_zone_data['name'];

    // Display the city and zone names
    if ($billing_city_name) {
        echo '<p><strong>City:</strong> ' . $billing_city_name . '</p>';
    }
    if ($billing_main_city_id) {
        echo '<p><strong>Main City ID:</strong> ' . $billing_main_city_id . '</p>';
    }
    if ($billing_zone_pathao_id) {
        echo '<p><strong>Zone:</strong> ' . $billing_zone_pathao_id . '</p>';
    }
}


// JavaScript function for cascading dropdowns
function woo_custom_cascade_dropdown_script() {
    ?>
    <script type="text/javascript">
        var loadingText = 'Loading zones. Please wait...';

        jQuery(document).ready(function($) {
        // When a city is selected
        $('select[name="billing_city"]').on('change', function() {
            var selectedCity = $(this).val();
            var zoneDropdown = $('select[name="billing_zone"]');
            
            // Disable the zone dropdown to prevent selection
            zoneDropdown.prop('disabled', true);

            // Remove any existing loader
            zoneDropdown.next('.loader').remove();

            // Add the loader after the zone dropdown
            var loader = $('<div class="loader"></div>').insertAfter(zoneDropdown);

            // Show the loader and set the loading text
            loader.show();
            zoneDropdown.html('<option value="">' + loadingText + '</option>');

            // AJAX call to fetch zone options based on the selected city
            $.ajax({
                url: woo_custom_fields_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_zones_by_city',
                    city_id: selectedCity
                },
                success: function(response) {
                    // Hide the loader after a minimum of 5 seconds
                    setTimeout(function() {
                        loader.hide();

                        // Re-enable the zone dropdown
                        zoneDropdown.prop('disabled', false);

                        // Update the dropdown options
                        zoneDropdown.html(response);
                    }, 00);
                }
            });
        });

        // Trigger the change event on page load to populate initial zone options
        $('select[name="billing_city"]').trigger('change');
    });
</script>



    <?php
}

// Add the JavaScript function and styles to the footer
add_action('wp_footer', 'woo_custom_cascade_dropdown_script');
add_action('wp_ajax_get_zones_by_city', 'get_zones_by_city');
add_action('wp_ajax_nopriv_get_zones_by_city', 'get_zones_by_city');

function get_zones_by_city() {
    if (isset($_POST['city_id'])) {
        global $wpdb;

        $pathao_city_id = intval($_POST['city_id']);

        $table_cities = $wpdb->prefix . 'woo_cities';
        $city_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_cities WHERE pathao_city_id = %d", $pathao_city_id));

        if ($city_id) {
            $zone_options = woo_get_zones_by_city($city_id);
            $html = '<option value="">-- Select Zone --</option>';
            foreach ($zone_options as $zone_id => $zone_name) {
                $html .= '<option value="' . esc_attr($zone_id) . '">' . esc_html($zone_name) . '</option>';
            }
            echo $html;
        }
    }
    wp_die();
}

/**
* Change the default country on the checkout page
*/

add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );

function change_default_checkout_country() {
return 'BD'; // Put Country code here
}


?>