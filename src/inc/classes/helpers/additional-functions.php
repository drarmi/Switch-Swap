<?php

use Automattic\WooCommerce\Admin\API\Data;

function get_product_gallery_images_with_sizes($product_id)
{
    $product = wc_get_product($product_id);

    if (!$product) return [];

    $attachment_ids = $product->get_gallery_image_ids();
    $images = [];

    foreach ($attachment_ids as $attachment_id) {
        $images[] = [
            'thumbnail' => wp_get_attachment_image_src($attachment_id, 'thumbnail')[0],
            'medium'    => wp_get_attachment_image_src($attachment_id, 'medium')[0],
            'large'     => wp_get_attachment_image_src($attachment_id, 'large')[0],
            'full'      => wp_get_attachment_image_src($attachment_id, 'full')[0],
        ];
    }

    return $images;
}

function get_product_variable_attribute($product_id)
{
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        return [];
    }

    $attribute_data = [];
    $attributes = $product->get_attributes();

    foreach ($attributes as $attribute_name => $attribute) {
        if ($attribute->is_taxonomy()) {
            $terms = wc_get_product_terms($product_id, $attribute_name, ['fields' => 'all']);
            $attribute_data[$attribute_name] = [];

            foreach ($terms as $term) {
                $term_data = [
                    'name' => $term->name,
                    'term_id' => $term->term_id,
                    'taxonomy' => $term->taxonomy,
                    'term_taxonomy_id' => $term->term_taxonomy_id,
                ];

                $attribute_data[$attribute_name][] = $term_data;
            }
        } else {
            // For non-taxonomy attributes
            $attribute_data[$attribute_name] = $attribute->get_options();
        }
    }

    return $attribute_data;
}


function get_product_variation_data_by_term($product_id, $term_name, $taxonomy = 'pa_period')
{
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        return null;
    }

    $variations = $product->get_available_variations();

    foreach ($variations as $variation) {
        $attributes = $variation['attributes'];

        if (isset($attributes["attribute_$taxonomy"]) && $attributes["attribute_$taxonomy"] === $term_name) {
            $variation_id = $variation['variation_id'];
            $variation_product = wc_get_product($variation_id);
            $data =
                [
                    'price' => $variation_product->get_regular_price(),
                    'sale_price' => $variation_product->get_sale_price(),
                    'variation_id' => $variation_id,
                    'term_name' => $term_name,
                    'taxonomy' => $taxonomy,
                    'product_id' => $product_id,
                    'stock_quantity' => $variation_product->get_stock_quantity(),
                ];
            return $data;
        }
    }

    return null;
}
function get_rating_title($rating = null)
{
    $rating = round($rating);

    $rating_arr = [
        "5" => __("מוביל/ה"),
        "4" => __("טוב מאוד"),
        "3" => __("טוב"),
        "2" => __("בסדר"),
        "1" => __("גרוע"),
    ];
    return isset($rating_arr[$rating]) ? $rating_arr[$rating] : __("מוביל/ה");
}

function get_rent_date($productID){
    global $wpdb;
    $sortArr = [];
    $query = "
        SELECT pm.post_id, pm.meta_key, pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
        WHERE pm2.meta_key = '_booking_product_id' 
        AND pm2.meta_value = %d
        AND pm.meta_key IN ('_booking_end', '_booking_start')
    ";
    
    $booking_data = $wpdb->get_results($wpdb->prepare($query, $productID));
    
    if (!empty($booking_data)) {
        $bookings = [];

        foreach ($booking_data as $meta) {
            $bookings[$meta->post_id][$meta->meta_key] = $meta->meta_value;
        }

        foreach ($bookings as &$booking) {
            ksort($booking);
            $booking = array_values($booking);
        }

        foreach(array_values($bookings) as $value){
            if($value[0] > $value[1]){
                $sortArr[] = [$value[1], $value[0]];
            }else{
                $sortArr[] = [$value[0], $value[1]];
            }
            
        }
    }
    
    return $sortArr;
}


function get_variation_id($custom_parent_id){
    global $wpdb;
    $products = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID 
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
         WHERE p.post_type = 'product'
         AND pm.meta_key = '_custom_parent_id'
         AND pm.meta_value = %s",
        $custom_parent_id
    ));
    
    
    $bidID = "";
    $rentID = "";
    $simpleID = "";
    
    
    foreach ($products as $product) {
        $product_id = $product->ID;
        $terms = wp_get_object_terms($product_id, 'product_type', ['fields' => 'names']);
        
        if(!empty($terms) && $terms[0] == "auction") {
            $bidID = $product_id;
        } elseif(!empty($terms) && $terms[0] == "booking") {
            $rentID = $product_id;
        } elseif(!empty($terms) && $terms[0] == "simple") {
            $simpleID = $product_id;
        }
    }

    return [
        "bidID" => $bidID,
        "rentID" => $rentID,
        "simpleID" => $simpleID
    ];
}

function returnStatus($product_id){
   
    $custom_parent_id = get_post_meta($product_id, '_custom_parent_id', true);

    $variation_ids = get_variation_id($custom_parent_id);
    $status = "";

    foreach($variation_ids as $id){
        if(!$id){
            continue;
        }
        
        $product = wc_get_product($id);
        if(empty($status)){
            $status = $product->get_status();
        }elseif($status === "publish"){
            $status = $product->get_status();
        }  
    }

    $stock_status = "";

    foreach($variation_ids as $id){
        if(!$id){
            continue;
        }

        $product = wc_get_product($id);
        
        if(empty($stock_status)){
            $stock_status = $product->get_stock_status();
        }elseif($stock_status === "instock"){
            $stock_status = $product->get_stock_status();
        }  
    }
    $is_rent = false;
    if (!empty($variation_ids["rentID"])) {
        $rent_date = get_rent_date($variation_ids["rentID"]);
        $currentDate = new DateTime();
    
        foreach ($rent_date as $date) {
            $from = DateTime::createFromFormat("YmdHis", $date[0]); // 20250327000000
            $to = DateTime::createFromFormat("YmdHis", $date[1]);   // 20250407235959
    
            if ($from && $to) {
                if (!$is_rent && ($currentDate >= $from && $currentDate <= $to)) {
                    $is_rent = true;
                }
            }
        }
    }
    
    return [
        "stock_status" =>$stock_status,
        "status" => $status,
        "is_rent" => $is_rent
    ];
}


function redirectProductNotAvailable($product_id){
    $product_author_id = get_post_field('post_author', $product_id);
    $current_user_id = get_current_user_id();

    $returnStatus = returnStatus($product_id);
    $notAvailable = false;


    if($returnStatus["stock_status"] != "instock"){
        $notAvailable = true;
    }elseif($returnStatus["is_rent"]){
        $notAvailable = true;
    }elseif($returnStatus["status"] != "publish"){
        $notAvailable = true;
    }

   if($notAvailable && ($product_author_id != $current_user_id) && !current_user_can('administrator')){
        wp_redirect(home_url());
        exit;
   }
}