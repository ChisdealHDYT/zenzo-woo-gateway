<?php
/*
Plugin Name: Bitcoin nova - WooCommerce Gateway
Plugin URI: http://bitcoinn.biz
Description: Extends WooCommerce by adding the Bitcoin nova Gateway
Version: 0.1
Author: Fexra
*/
if(!defined('ABSPATH')) {
	exit;
}

//Load Plugin
add_action('plugins_loaded', 'bitcoinnova_init', 0 );

function bitcoinnova_init() {
	if(!class_exists('WC_Payment_Gateway')) return;
	
	include_once('include/bitcoinnova_payments.php');
	require_once('library.php');

    add_filter( 'woocommerce_payment_gateways', 'bitcoinnova_gateway');
	function bitcoinnova_gateway( $methods ) {
		$methods[] = 'Bitcoinnova_Gateway';
		return $methods;
	}
}

//Add action link
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'bitcoinnova_payment');

function bitcoinnova_payment($links) {
	$plugin_links = array('<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('Settings', 'bitcoinnova_payment') . '</a>',);
	return array_merge($plugin_links, $links);	
}

//Configure currency
add_filter('woocommerce_currencies','add_my_currency');
add_filter('woocommerce_currency_symbol','add_my_currency_symbol', 10, 2);

function add_my_currency($currencies) {
     $currencies['ZNZ'] = __('Bitcoinnova','woocommerce');
     return $currencies;
}

function add_my_currency_symbol($currency_symbol, $currency) {
    switch($currency) {
        case 'ZNZ': $currency_symbol = 'ZNZ'; break;
    }
    return $currency_symbol;
}

//Create Database
register_activation_hook(__FILE__,'createDatabase');

$db = $wpdb->prefix . 'woocommerce_bitcoinnova';

function createDatabase() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'woocommerce_bitcoinnova';
    
	$sql = "CREATE TABLE $table_name (
       `id` INT(32) NOT NULL AUTO_INCREMENT,
	   `oid` INT(32) NOT NULL,
       `pid` VARCHAR(64) NOT NULL,
       `lasthash` VARCHAR(120) NOT NULL,
       `amount` DECIMAL(12, 2) NOT NULL,
       `paid` INT(1) NOT NULL,
       UNIQUE KEY id (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta( $sql );
}
