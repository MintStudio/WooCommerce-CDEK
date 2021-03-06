<?php
    add_action( 'woocommerce_product_options_shipping', 'cds_cdek_add_custom_shipping_fields' );
    add_action( 'woocommerce_process_product_meta', 'cds_cdek_add_custom_shipping_fields_save' );

    function cds_cdek_add_custom_shipping_fields() {
        global $woocommerce, $post;
        
        echo '<div class="options_group">';
        
        woocommerce_wp_text_input( 
            array( 
                'id'          => 'cds_cdek_shipping_option_city_of_dispatch', 
                'label'       => 'Город отправки', 
                'placeholder' => 'Город отправки товара',
                'desc_tip'    => 'true',
                'description' => 'Город откуда будет отправлен товар. По умолчанию - Краснодар. Используется для расчета стоимости доставки службой "СДЭК"' 
            )
        );

        woocommerce_wp_hidden_input(
        array( 
            'id'    => 'cds_cdek_shipping_option_city_code', 
            'value' => ''
            )
        );
        
        echo '</div>'; 
    }

    function cds_cdek_add_custom_shipping_fields_save( $post_id ){
        
        $cds_cdek_shipping_option_city_of_dispatch = $_POST['cds_cdek_shipping_option_city_of_dispatch'];
        update_post_meta( $post_id, 'cds_cdek_shipping_option_city_of_dispatch', sanitize_text_field( $cds_cdek_shipping_option_city_of_dispatch ) );
        
        $cds_cdek_shipping_option_city_code = $_POST['cds_cdek_shipping_option_city_code'];
        update_post_meta( $post_id, 'cds_cdek_shipping_option_city_code', sanitize_text_field( $cds_cdek_shipping_option_city_code ) );
        
    }

?>
