<?php
// In your cascade-dropdown.php file

// Hook into the checkout fields
add_filter('woocommerce_checkout_fields', 'add_custom_checkout_fields_for_shipping');

function add_custom_checkout_fields_for_shipping($fields) {
    // Get the city and zone options
    $city_options = woo_get_cities();
    $zone_options = woo_get_zones();
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_last_name']);

    // Add the custom fields to the shipping sections
    $fields['shipping']['shipping_city_list'] = array(
        'type' => 'select',
        'label' => __('City', 'woo-custom-dropdown-fields'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => $city_options,
    );

    $fields['shipping']['shipping_zone'] = array(
        'type' => 'select',
        'label' => __('Zone', 'woo-custom-dropdown-fields'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => $zone_options,
    );

    
    // Add the country dropdown for shipping
    $fields['shipping']['shipping_country'] = array(
        'type' => 'country',
        'label' => __('Country', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
    );
    $fields['shipping']['shipping_phone'] = array(
        'type' => 'tel',
        'label' => __('Phone', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-wide'),
        'clear' => true,
        'placeholder' => __('Phone', 'woocommerce'),
    );

   

    // Move the custom 'shipping_zone' field before the 'shipping_postcode' field
    $shipping_fields = $fields['shipping'];
    unset($fields['shipping']);
    $fields['shipping']['shipping_first_name'] = $shipping_fields['shipping_first_name'];
    $fields['shipping']['shipping_address_1'] = $shipping_fields['shipping_address_1'];
    $fields['shipping']['shipping_city_list'] = $shipping_fields['shipping_city_list'];
    $fields['shipping']['shipping_zone'] = $shipping_fields['shipping_zone'];
    $fields['shipping']['shipping_country'] = $shipping_fields['shipping_country'];
    $fields['shipping']['shipping_phone'] = $shipping_fields['shipping_phone'];
    //$fields['shipping'] = array_merge($fields['shipping'], $shipping_fields);

    

    return $fields;
}

// Hook into the order details to display city and zone names
add_action('woocommerce_order_details_after_order_table', 'display_city_and_zone_names', 10, 1);

function display_city_and_zone_names_for_shipping($order) {
    // Get the order ID
    $order_id = $order->get_id();

    // Get the shipping city ID from the order
    $shipping_city_id = get_post_meta($order_id, '_shipping_city_list', true);

    // Get the shipping zone ID from the order
    $shipping_zone_id = get_post_meta($order_id, '_shipping_zone', true);

    // Get the corresponding city and zone names
    $shipping_city_data = woo_get_city_data_for_order($shipping_city_id);
    $shipping_city_name = $shipping_city_data['name'];
    $shipping_main_city_id = $shipping_city_data['main_city_id'];

    $shipping_zone_data = woo_get_zone_data_for_order($shipping_zone_id);
    $shipping_zone_pathao_id = $shipping_zone_data['name'];

    // Display the city and zone names
    if ($shipping_city_name) {
        echo '<p><strong>City:</strong> ' . $shipping_city_name . '</p>';
    }
    if ($shipping_main_city_id) {
        echo '<p><strong>Main City ID:</strong> ' . $shipping_main_city_id . '</p>';
    }
    if ($shipping_zone_pathao_id) {
        echo '<p><strong>Zone:</strong> ' . $shipping_zone_pathao_id . '</p>';
    }
}


// JavaScript function for cascading dropdowns
function woo_custom_cascade_dropdown_script_for_shipping() {
    ?>
    <script type="text/javascript">
        var loadingText = 'Loading zones. Please wait...';

        jQuery(document).ready(function($) {
        // When a city is selected
        $('select[name="shipping_city_list"]').on('change', function() {
            var selectedCity = $(this).val();
            var zoneDropdown = $('select[name="shipping_zone"]');
            
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
                    action: 'get_zones_by_city_for_shipping',
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
        $('select[name="shipping_city_list"]').trigger('change');
    });
</script>



    <?php
}

// Add the JavaScript function and styles to the footer
add_action('wp_footer', 'woo_custom_cascade_dropdown_script_for_shipping');
add_action('wp_ajax_get_zones_by_city_for_shipping', 'get_zones_by_city_for_shipping');
add_action('wp_ajax_nopriv_get_zones_by_city_for_shipping', 'get_zones_by_city_for_shipping');

function get_zones_by_city_for_shipping() {
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
?>