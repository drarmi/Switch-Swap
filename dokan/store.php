<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}




$store_user_id = get_query_var( 'author' ); // Get the store owner's user ID
$current_user_id = get_current_user_id();

if ( $current_user_id === (int) $store_user_id ) {
    //echo "Current user is the store owner.";
    get_template_part( 'dokan/store-owner-fake' );

} else {
    //get_template_part( 'dokan/store-owner-fake' );

    get_template_part( 'dokan/store-buyer' );

}