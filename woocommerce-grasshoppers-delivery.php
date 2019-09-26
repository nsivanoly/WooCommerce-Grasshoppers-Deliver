<?php
/**
 * Plugin Name: WooCommerce Grasshoppers Delivery
 * Description: WooCommerce Grasshoppers Shipping and Delivery By Arimac.
 * Version: 1.0.0
 * Author: N Sivanoly
 * Developer: N Sivanoly
 */

/**
 * Check if WooCommerce is active
 */

define('ALLOWED_SHIPPING_METHOD', array( 'Standard', 'Premium' ));

require_once "libraries/api/format-response.php";
require_once "libraries/api/process-request.php";
require_once "libraries/api/create-delivery.php";
require_once "libraries/api/track-shipping.php";
include_once "libraries/api/get-shipping-rates.php";

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * grasshoppers_shipping_method_init
     */
    function grasshoppers_shipping_method_init()
    {
        require_once "libraries/wc-grasshoppers-shipping.php";
    }

    add_action('woocommerce_shipping_init', 'grasshoppers_shipping_method_init');

    /**
     * @param $methods
     * @return array
     */
    function add_grasshoppers_shipping_method($methods)
    {
        $methods[] = 'WC_Grasshoppers_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_grasshoppers_shipping_method');


    /**
     * @param $r
     * @return mixed
     */
    function bal_http_request_args($r) //called on line 237
    {
        $r['timeout'] = 60;
        return $r;
    }

    add_filter('http_request_args', 'bal_http_request_args', 100, 1);


    /**
     * @param $order_id
     */
    function bbloomer_redirectcustom($order_id ){
        $order = wc_get_order( $order_id );
        if (in_array($order->get_shipping_method(), ALLOWED_SHIPPING_METHOD)){
            $create = new Create_Delivery($order_id);
            $create->create_request();
        }
    }

    add_action( 'woocommerce_thankyou', 'bbloomer_redirectcustom');

    /**
     * Order status HTML
     */
    function gh_woocommerce_view_order($order){
        if (in_array($order->get_shipping_method(), ALLOWED_SHIPPING_METHOD)){
            $tracks =  new Track_Grass_Hoppers($order);
            $statuses = $tracks->get_status();
            $statuses = (is_array($statuses))?$statuses:array();
            include "views/status.php";
        }
    }
    add_action( 'woocommerce_order_details_after_customer_details', 'gh_woocommerce_view_order');
}
