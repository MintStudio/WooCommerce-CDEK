<?php
/*
    Plugin Name: CDEK delivery calculator
    Description: Плагин рассчитывает стоимость доставки товара службой "Сдэк"
    Version: 1.0
    Author: ООО «Минт Студио» (Mint Studio)
    Author URI: http://mint-studio.org
*/

/**
* Register the scripts for the public-facing side of the site.
*
* @since    1.0.0
*/

if ( ! defined( 'MY_PlUGIN_URL' ) )
define( 'MY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'MY_PLUGIN_PATH' ) )
define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

function cdek_include_frontend_js()  
{    
    if ( is_cart() || is_checkout() ) {
        wp_enqueue_script('jquery-ui-1.12.1', MY_PLUGIN_URL . 'assets/js/jquery-ui-1.12.1.min.js', array( 'jquery' ), '1.12.1');
        wp_enqueue_script('cdek-script', MY_PLUGIN_URL . 'assets/js/сdek-script.js', array('jquery' ), '1.0'); 
    }
} 
add_action( 'wp_enqueue_scripts', 'cdek_include_frontend_js', 10);

function cdek_include_admin_js() {        
    wp_enqueue_script( 'cdek-script', MY_PLUGIN_URL . 'assets/js/сdek-script.js', array('jquery', 'jquery-ui-core'), '1.0');  
}
add_action('admin_enqueue_scripts', 'cdek_include_admin_js', 5);

include_once(MY_PLUGIN_PATH . 'inc/CalculateDeliveryCdek.php');

?>
