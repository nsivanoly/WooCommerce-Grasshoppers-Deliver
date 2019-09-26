<?php

/**
 * Class Process_Grasshoppers_Request
 */
class Process_Grasshoppers_Request
{

    /**
     * @var Format_Grasshoppers_Response
     */
    protected $format;

    /**
     * Process_Grasshoppers_Request constructor.
     */
    public function __construct()
    {
        $this->format = new Format_Grasshoppers_Response;
    }


    /**
     * @param $url
     * @param $body
     * @return array|mixed|object
     */
    public function process_request($url, $body)
    {

        $response = wp_remote_post($url, array(
            'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
            'body' => json_encode($body),
            'method' => 'POST',
            'data_format' => 'body',
        ));

        return $this->format->format_response($response);
    }
}