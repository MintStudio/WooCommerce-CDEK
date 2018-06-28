<?php
/*
    Plugin Name: Cargo Delivery Service CDEK
    Plugin URI: http://mint-studio.org
    Description: Плагин рассчитывает стоимость доставки товара службой "Сдэк"
    Version: 1.0
    Author: Mint Studio
    Author URI: http://mint-studio.org
*/

/**
* Register the scripts for the public-facing side of the site.
*
* @since    1.0.0
*
*/

if ( ! defined( 'CDS_PLUGIN_URL' ) )
define( 'CDS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'CDS_PLUGIN_PATH' ) )
define( 'CDS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include script for the front end 
function cds_cdek_include_frontend_js()  
{    
    if ( is_cart() || is_checkout() ) {
        wp_enqueue_script('jquery-ui-1.12.1', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array( 'jquery' ), '1.12.1');
        wp_enqueue_script('cds_cdek_script', CDS_PLUGIN_URL . 'assets/js/CDS_CDEK_script.js', array('jquery' ), '1.0'); 
    }
} 
add_action( 'wp_enqueue_scripts', 'cds_cdek_include_frontend_js', 10);

// Include script for the admin
function cds_cdek_include_admin_js() {        
    wp_enqueue_script( 'cds_cdek_admin_script', CDS_PLUGIN_URL . 'assets/js/CDS_CDEK_script.js', array('jquery', 'jquery-ui-core'), '1.0', true );  
}
add_action('admin_enqueue_scripts', 'cds_cdek_include_admin_js', 5);

// Include main function file
include_once(CDS_PLUGIN_PATH . 'inc/CDS_CDEK_function.php');

?>