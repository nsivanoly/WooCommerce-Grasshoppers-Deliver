<?php

/**
 * Class Get_Grasshoppers_Shipping_Rates
 */
class Get_Grasshoppers_Shipping_Rates
{
    /**
     * @var Process_Grasshoppers_Request
     */
    protected $request;

    /**
     * Get_Grasshoppers_Shipping_Rates constructor.
     */
    public function __construct()
    {
        $this->request = new Process_Grasshoppers_Request;
    }

    /**
     * @param $settings
     * @param $city
     * @param $weight
     * @param $type
     * @return array|mixed|object
     */
    public function get_shipping_data($settings, $city, $weight, $type)
    {

        $url = $settings['api_base_url'] . "getCityPriceByCityKeyword";

        $body = array(
            'corporateId' => $settings['corporate_id'],
            'keyword' => $city,
            'deliveryMethod' => $type,
            'weight' => $weight,
            'COD' => $settings['cod'],
        );

        $body = $this->request->process_request($url, $body);

        return $body;
    }
}