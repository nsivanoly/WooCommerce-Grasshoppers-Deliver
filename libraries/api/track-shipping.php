<?php

/**
 * Class Track_Grass_Hoppers
 */
class Track_Grass_Hoppers
{
    /**
     * @var
     */
    protected $order;
    /**
     * @var mixed|void
     */
    private $settings;
    /**
     * @var
     */
    private $api_base_url;
    /**
     * @var
     */
    private $corporate_id;
    /**
     * @var mixed
     */
    private $_grasshopper_reference;
    /**
     * @var Process_Grasshoppers_Request
     */
    private $request;

    /**
     * Track_Grass_Hoppers constructor.
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
        $this->settings = get_option('woocommerce_grasshoppers_settings');
        $this->corporate_id = $this->settings['corporate_id'];
        $this->api_base_url = $this->settings['api_base_url'];
        $this->_grasshopper_reference = get_post_meta($this->order->id, '_grasshopper_reference', true);
        $this->request = new Process_Grasshoppers_Request;
    }

    /**
     * @return array|mixed|object
     */
    public function get_status()
    {
        $url = $this->api_base_url . "createDeliveryRequest";
        $body = array(
            'corporateId' => $this->corporate_id,
            'referenceNo' => $this->corporate_id,
        );

        $body = $this->request->process_request($url, $body);

        return $body;
    }
}