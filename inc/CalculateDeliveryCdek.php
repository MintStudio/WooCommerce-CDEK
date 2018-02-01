<?php
/*
    В этом файле находятся все функции для работы плагина
*/

    if ( ! defined( 'WPINC' ) ) {
       die;
   }
    
   /*
    * Check if WooCommerce is active
    */
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

        add_filter( 'woocommerce_checkout_fields', 'custom_edit_checkout_fields' );
        function custom_edit_checkout_fields( $fields ) {
            $fields['billing']['city_cdek'] = array(
                'label'         => 'Город',
                'placeholder'   => '',
                'required'      => false,
                'priority'      => 45
            );
        
           return $fields;
        }

            include_once("CalculateDeliveryCdek.Admin.php");

            function cdek_shipping_method_init() {
                if ( ! class_exists( 'CDEK_Shipping_Method' ) ) {
                    class CDEK_Shipping_Method extends WC_Shipping_Method {
                        /**
                        * Constructor shipping class
                        *
                        * @access public
                        * @return void
                        */
                        public function __construct( $instance_id = 0 ) {
                            $this->id                 = 'cdek-method'; // Id for your shipping method. Should be uunique.
                            $this->method_title       = 'Служба доставки СДЭК' ;  // Title shown in admin
                            $this->method_description = 'Позволяет автоматически рассчитать стоимость доставки на странице оформления заказа при помощи сервиса <a href="http://cdek.ru" target="_blank">www.cdek.ru</a>'; // Description shown in admin
                            $this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
                            $this->title              = "Служба доставки СДЭК"; // This can be added as an setting but for this example its forced.
                            $this->instance_id = absint( $instance_id );
                            
                            $this->supports  = array(
                                'shipping-zones',
                                'instance-settings'
                            );
                            
                            $this->instance_form_fields  = array(
                                'title' => array(
                                    'title' =>  'Заголовок', 
                                    'type' => 'text',
                                    'description' => 'Заголовок на сайте',
                                    'default' =>  'Служба доставки СДЭК',
                                ),
                                'from_city' => array(
                                    'title'         => 'Город отправки',
                                    'type'          => 'text',
                                    'description'   => 'Город отправки посылки по умолчанию', 
                                    'default' 		=> 'Краснодар',
                                ),
                                'from_city_code' => array(
                                    'title'         => '', 
                                    'type'          => 'hidden',
                                    'default' 		=> '435',
                                ),
                                'type_of_delivery' => array(
                                    'title'         => 'Тип доставки', 
                                    'type'          => 'select',
                                    'description'   => 'Тип доставки по умолчанию', 
                                    'default' 		=> '1',
                                    'options'		=> array(
                                        '1'           => 'Экспресс лайт дверь-дверь',
                                        '3'           => 'Супер-экспресс до 18',
                                        '10'          => 'Экспресс лайт склад-склад',
                                        '11'          => 'Экспресс лайт склад-дверь',
                                        '12'          => 'Экспресс лайт дверь-склад',
                                        '59'          => 'Супер-экспресс до 12',
                                        '60'          => 'Супер-экспресс до 14',
                                        '61'          => 'Супер-экспресс до 16',
                                        '62'          => 'Магистральный экспресс склад-склад',
                                        '63'          => 'Магистральный супер-экспресс склад-склад'
                                        )
                                    ),
                                    'mode_of_delivery' => array(
                                        'title'         => 'Режим доставки',
                                        'type'          => 'select',
                                        'description'   => 'Режим доставки по умолчанию',
                                        'default' 		=> '1',
                                        'options'		=> array(
                                            '1'          => 'Дверь - дверь',
                                            '2'          => 'Дверь - склад',
                                            '3'          => 'Склад - дверь',
                                            '4'          => 'Склад - склад',
                                            )
                                        ),
                                    );
                                    
                                    
                                    $this->title   = $this->get_option( 'title' );
                                    $this->from_city    = $this->get_option( 'from_city' );
                                    $this->from_city_code    = $this->get_option( 'from_city_code' );
                                    $this->type_of_delivery    = $this->get_option( 'type_of_delivery' );
                                    $this->mode_of_delivery    = $this->get_option( 'mode_of_delivery' );
                                    
                                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                                }
                            
                                    
                        /**
                        * Init your settings
                        *
                        * @access public
                        * @return void
                        */
                        function init() {
                            // Load the settings API
                            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
                            
                            // Save settings in admin if you have any defined
                            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                        }
                        
                        /**
                        * calculate_shipping function.
                        * @param array $package (default: array())
                        */
                        public function calculate_shipping( $package = array() ){ 
                            
                            global $woocommerce;     
                           
                            $session_city = WC()->session->customer['city'];
                            
                            if ( is_numeric($session_city) == false ){
                                return;
                            }else{
                                $from_city = intval($this->from_city_code);
                                $to_city = $package['destination']['city'];
                                $type_of_delivery = intval($this->type_of_delivery);
                                $mode_of_delivery = intval($this->mode_of_delivery);

                                include_once("CalculatePriceDeliveryCdek.php");
                                
                                //создаём экземпляр объекта CalculatePriceDeliveryCdek
                                $calc = new CalculatePriceDeliveryCdek();
                                //устанавливаем город-получатель
                                $calc->setReceiverCityId($to_city);
                                //устанавливаем дату планируемой отправки
                                $calc->setDateExecute($_REQUEST['dateExecute']);
                                //устанавливаем тариф по-умолчанию
                                $calc->setTariffId($type_of_delivery);
                                //устанавливаем режим доставки
                                $calc->setModeDeliveryId($mode_of_delivery);
                            
                                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                
                                    $item_from_city = get_post_meta( $cart_item['product_id'], 'cdek_shipping_option_city_code', true ); 
                                    
                                    if ($item_from_city != ''){
                                        $from_city = $item_from_city;
                                    };

                                    //устанавливаем город-отправитель
                                    $calc->setSenderCityId($from_city);

                                    $weight = $cart_item['data']->weight;
                                    $length = $cart_item['data']->length;
                                    $width = $cart_item['data']->width;
                                    $height = $cart_item['data']->height;

                                    if( $weight == '' || $length == '' || $width == '' || $height == ''){
                                        return;
                                    }
                    
                                    $calc->addGoodsItemBySize($weight, $length, $width, $height);

                                    if ($calc->calculate() === true) {
                                        $res = $calc->getResult();
                                        $cost += $res['result']['price'];   
                                    }
                                }

                                if ($calc->calculate() === true) {
                                    $res = $calc->getResult();
                                        $rate = array(
                                            'id' => $this->id,
                                            'label' => $this->title,
                                            'cost' => $cost
                                        );
                                
                                        // Register the rate
                                        $this->add_rate( $rate );

                                }else{
                                    $err = $calc->getError();
                                    if( isset($err['error']) && !empty($err) ) {
                                        foreach($err['error'] as $e) {
                                            echo 'Текст ошибки: ' . $e['text'] . '.<br />';
                                        }
                                    }
                                }  
                            }
                        }// end calculate_shipping

                    }
                } // end Class_exists
            }// end cdek_shipping_method_init
            add_action( 'woocommerce_shipping_init', 'cdek_shipping_method_init' );

            // add new shipping method
            function add_your_shipping_method( $methods ) {
                $methods['cdek-method'] = 'CDEK_Shipping_Method';
                return $methods;
            }
        
        add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
    } // if woocommerce is active

?>