<?php

/**
 * GPL License
 * Class that represent a single curl request
 */
class CurlRequest {
   
    public $url       = false;
    public $method    = 'GET';
    public  $post_data = null;
    public  $headers   = null;
    public  $options   = array();

    /**
     * @param string $url
     * @param string $method
     * @param  $post_informations
     * @param  $headers
     * @param  $options
     * @return void
     */
    function __construct($url, $method = "GET", $post_informations = null, $headers = null,$options=null) {
        $this->url       = $url;
        $this->method    = $method;
        $this->post_data = $post_informations;
        $this->headers   = $headers;
        $this->options   = $options;
    }

    /**
     * @return void
     */
    
    public function __destruct() {
        unset($this->url, $this->method, $this->post_data, $this->headers);
    }
}
?>
