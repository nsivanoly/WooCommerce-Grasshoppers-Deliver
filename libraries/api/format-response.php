<?php

/**
 * Class Format_Grasshoppers_Response
 */
class Format_Grasshoppers_Response
{
    /**
     * @param $response
     * @return array|mixed|object
     */
    public function format_response($response)
    {
        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body;
    }
}