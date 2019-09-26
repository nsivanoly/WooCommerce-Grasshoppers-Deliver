<?php

/**
 * Class Create_Delivery
 */
class Create_Delivery
{
    /**
     * @var
     */
    protected $order_id;
    /**
     * @var bool|WC_Order|WC_Order_Refund
     */
    protected $order;
    /**
     * @var mixed|void
     */
    protected $settings;
    /**
     * @var
     */
    protected $corporate_id;
    /**
     * @var
     */
    protected $api_base_url;
    /**
     * @var Process_Grasshoppers_Request
     */
    protected $request;

    /**
     * Create_Delivery constructor.
     * @param $order_id
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
        $this->order = wc_get_order($order_id);
        $this->settings = get_option('woocommerce_grasshoppers_settings');
        $this->corporate_id = $this->settings['corporate_id'];
        $this->api_base_url = $this->settings['api_base_url'];
        $this->request = new Process_Grasshoppers_Request;
    }

    /**
     * @return array
     */
    public function create_body()
    {
        $items = array();

        foreach ($this->order->get_items() as $item_id => $item_data) {
            array_push($items,
                array(
                    'title' => $item_data->get_name(),
                    'quantity' => $item_data->get_quantity(),
                )
            );
        }

        $body = array(
            'corporateId' => $this->corporate_id,
            'customerOrderReferenceNo' => $this->order_id,
            'pickupStreet' => get_option('woocommerce_store_address') . ' ' . get_option('woocommerce_store_address_2'),
            'pickupCity' => get_option('woocommerce_store_city'),
            'pickupZipCode' => get_option('woocommerce_store_postcode'),
            'pickupLatitude' => 0,
            'pickupLongitude' => 0,
            'recipientName' => $this->order->get_shipping_first_name() . ' ' . $this->order->get_shipping_last_name(),
            'recipientContactNo' => $this->order->get_billing_phone(),
            'recipientContactNo2' => 'N/A',
            'deliverStreet' => $this->order->get_shipping_address_1() . ' ' . $this->order->get_shipping_address_2(),
            'deliverCity' => $this->order->get_shipping_city(),
            'deliveryZipCode' => $this->order->get_shipping_postcode(),
            'deliverLatitude' => 0,
            'deliverLongitude' => 0,
            'deliveryMethod' => $this->order->get_shipping_method(),
            'scaleValue' => 2,
            'paymentType' => $this->order->get_payment_method(),
            'priceCOD' => 0.0,
            'itemDetailList' => $items,
        );

        return $body;
    }

    /**
     * @return array|mixed|object
     */
    public function create_request()
    {
        $url = $this->api_base_url . "createDeliveryRequest";
        $body = $this->create_body();

        $body = $this->request->process_request($url, $body);
        $this->update_reference_no($body);

        return $body;
    }

    /**
     * @param $body
     */
    public function update_reference_no($body)
    {
        if (isset($body['REFERENCE_NO'])) {
            $this->order->update_meta_data('_grasshopper_reference', $body['REFERENCE_NO']);
            $this->order->save();
        }
    }
}