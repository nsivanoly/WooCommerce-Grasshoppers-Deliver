<?php

if (!class_exists('WC_Grasshoppers_Shipping_Method')) {
    /**
     * Class WC_Grasshoppers_Shipping_Method
     */
    class WC_Grasshoppers_Shipping_Method extends WC_Shipping_Method
    {
        /**
         * @var array
         */
        protected $variations;
        /**
         * @var Get_Grasshoppers_Shipping_Rates
         */
        protected $shipping_rate;

        /**
         * Constructor for shipping class
         *
         * @access public
         * @return void
         */
        public function __construct()
        {
            $this->id = 'grasshoppers';
            $this->variations = ALLOWED_SHIPPING_METHOD;
            $this->method_title = __('Grasshoppers Shipping');  // Title shown in admin
            $this->shipping_rate = new Get_Grasshoppers_Shipping_Rates;
            $this->method_description = __('Grasshoppers Shipping');  // Title shown in admin

            $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
            $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Grasshoppers', 'grasshoppers');

            $this->init();
        }

        /**
         * Init settings
         *
         * @access public
         * @return void
         */
        function init()
        {
            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

            // Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         *
         */
        function init_form_fields()
        {

            $this->form_fields = array(

                'enabled' => array(
                    'title' => __('Enable', 'grasshoppers'),
                    'type' => 'checkbox',
                    'description' => __('Enable this shipping.', 'grasshoppers'),
                    'default' => 'yes'
                ),

                'title' => array(
                    'title' => __('Title', 'grasshoppers'),
                    'type' => 'text',
                    'description' => __('Title to be display on site', 'grasshoppers'),
                    'default' => __('Grasshoppers', 'grasshoppers')
                ),

                'corporate_id' => array(
                    'title' => __('corporateId', 'grasshoppers'),
                    'type' => 'text',
                    'description' => __('corporateId', 'grasshoppers'),
                    'default' => ''
                ),

                'api_base_url' => array(
                    'title' => __('API Base URL', 'grasshoppers'),
                    'type' => 'text',
                    'description' => __('API Base URL [with tailing slash]', 'grasshoppers'),
                    'default' => 'http://www.grasshoppers.lk/customers/WebService/'
                ),

                'cod' => array(
                    'title' => __('COD', 'grasshoppers'),
                    'type' => 'number',
                    'description' => __('Cash on Delivery Amount if any', 'grasshoppers'),
                    'default' => ''
                ),
            );

            foreach ($this->variations as $variations) {
                $key = strtolower($variations);
                $this->form_fields["enable_$key"] = array(
                    'title' => __("Enable $variations Delivery", 'grasshoppers'),
                    'type' => 'checkbox',
                    'description' => __("Enable $variations Delivery", 'grasshoppers'),
                    'default' => 'yes'
                );
            }

        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping($package = array())
        {
            // Calculate the weight
            $weight = 0;
            $city = $package['destination']['city'];
            foreach ($package['contents'] as $item_id => $values) {
                $_product = $values['data'];
                $weight += floatval($_product->get_weight()) * floatval($values['quantity']);
            }


            foreach ($this->variations as $variations) {
                $key = strtolower($variations);
                if ($this->settings["enable_$key"] == "yes") {
                    $body = $this->shipping_rate->get_shipping_data($this->settings, $city, $weight, $key);
                    if (!isset($body[0]['Error'])) {
                        $cost = $body[0]['price'];
                        if ($cost) {
                            $rate = array(
                                'id' => $key,
                                'label' => $variations,
                                'cost' => round($cost, 2),
                                'calc_tax' => 'per_item'
                            );
                            // Register the rate
                            $this->add_rate($rate);
                        }
                    }
                }
            }
        }
    }
}
