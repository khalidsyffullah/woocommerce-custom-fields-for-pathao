<?php
function woo_create_woo_cities_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'woo_cities';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        pathao_city_id BIGINT(20),
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $csv_file = plugin_dir_path(__FILE__) . '/cities.csv';
    if (file_exists($csv_file)) {
        $file = fopen($csv_file, 'r');
        if ($file !== FALSE) {
            while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
                $insert_result = $wpdb->insert(
                    $table_name,
                    array(
                        'id' => $data[0], 
                        'name' => $data[1],
                        'pathao_city_id' => $data[2], 
                    )
                );
                if (!$insert_result) {
                    wp_die('Error inserting data into cities table: ' . $wpdb->last_error);
                }
            }
            fclose($file);
        } else {
            wp_die('Error opening CSV file.');
        }
    } else {
        wp_die('CSV file not found.');
    }
}

function woo_create_woo_zones_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'woo_zones';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        city_id BIGINT(20),
        pathao_zone_id BIGINT(20),
        PRIMARY KEY (id),
        FOREIGN KEY (city_id) REFERENCES {$wpdb->prefix}woo_cities(id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $csv_file = plugin_dir_path(__FILE__) . '/zones.csv';
    if (file_exists($csv_file)) {
        $file = fopen($csv_file, 'r');
        if ($file !== FALSE) {
            while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
                $insert_result = $wpdb->insert(
                    $table_name,
                    array(
                        'id' => $data[0], // Assuming the CSV has an 'id' column
                        'name' => $data[1],
                        'city_id' => $data[2], // Adjust column indexes as needed
                        'pathao_zone_id' => $data[3]
                    )
                );
                if (!$insert_result) {
                    wp_die('Error inserting data into zones table: ' . $wpdb->last_error);
                }
            }
            fclose($file);
        } else {
            wp_die('Error opening CSV file.');
        }
    } else {
        wp_die('CSV file not found.');
    }
}
function woo_get_cities() {
    global $wpdb;
    $table_cities = $wpdb->prefix . 'woo_cities';
    $cities = $wpdb->get_results("SELECT id, pathao_city_id, name FROM $table_cities", ARRAY_A);
    $city_options = array('' => '-- Select City --');
    foreach ($cities as $city) {
        $city_options[$city['pathao_city_id']] = $city['name'];
    }
    return $city_options;
}




?>