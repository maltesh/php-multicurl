<?php
/*
 * GPL License
 */


require_once 'curlrequest.php';
//maximum number of milliseconds to allow cURL functions to execute for lower versions of php
define ('CURLOPT_TIMEOUT_MS', 155);
// number of milliseconds to wait while trying to connect.
define ('CURLOPT_CONNECTTIMEOUT_MS', 156);


class CLS_MULTI_HTTP {
   
   
   private $master;
   private $curl_window_size;
   private $requests  = array();
   private $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => "Mozilla",
        CURLOPT_TIMEOUT_MS        => 15000,
        CURLOPT_CONNECTTIMEOUT_MS => 15000,
   );
   private $output = array();
   
   
   function __construct($request_array) {
      if(is_array($request_array)){
         $this->curl_window_size = count($request_array);
         $this->requests         = $request_array;
      }
      
   }
   
   public function request($url, $method = "POST", $post_data = null, $headers = null) {
        $req = new CurlRequest($url, $method, $post_data, $headers, $options);
        return $req;
   }
    
   function execute(){
      //create the multiple cURL handle
      $master = curl_multi_init();
      $running = false;
      $output= array();
        // start sending requests
      foreach  ($this->requests as $request_param_arr) {
          $ch = curl_init();
          $options = $this->getOptions($this->request($request_param_arr['request_url'],$request_param_arr['method'],$request_param_arr['post_header'],$request_param_arr['header']));
          curl_setopt_array($ch, $options);
          curl_multi_add_handle($master, $ch);
      }
       do {
          //From PHP DOC
          //curl_multi_perform(3) is asynchronous. It will only execute as little as possible and then return back control to your program. It is designed to never block. 
            //If it returns CURLM_CALL_MULTI_PERFORM you better call it again soon, as that is a signal that it still has local data to send or remote data to receive
            while (($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM) ;
            
            //CURLM_OK-Things are fine.//http://curl.haxx.se/libcurl/c/libcurl-errors.html
            if ($execrun != CURLM_OK)
                break;
            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($master)) {
               
                // get the info and content returned on the request
                $info = curl_getinfo($done['handle']);
                $this->output[] = curl_multi_getcontent($done['handle']);
                 // remove the curl handle that just completed
                curl_multi_remove_handle($master, $done['handle']);
            }
            // Block for data in / output; error handling is done by curl_multi_exec
            if ($running)
                curl_multi_select($master, $this->timeout);

        } while ($running);
        curl_multi_close($master);
        return $this->output;
      
   }
   
   private function getOptions($req){
      $curl_options = $this->options;
      if($req->options){
         $curl_options = $req->options+$curl_options;
      }
      //If Req has post data set ,add it
      if ($req->post_data) {
          $curl_options[CURLOPT_POST]       = 1;
          $curl_options[CURLOPT_POSTFIELDS] = $req->post_data;
      }
      //
      if ($req->headers) {
          $curl_options[CURLOPT_HEADER]     = 0;
          $curl_options[CURLOPT_HTTPHEADER] = $req->headers;
      }
      // set the request URL
      $curl_options[CURLOPT_URL] = $req->url;
      return $curl_options;
      
   }
   
   function __destruct() {
      
   }
   
}

//$array_1 = array(
//    
//    '0'=>array(
//        'request_url'=>'http://rmcreative.ru/blog/tag/PHP+curl_multi',
//        'post_header'=>'r=tsd32ds',
//        'method'=>'post'
//    ),
//    
//    '1'=>array(
//        'request_url'=>'http://airpush.blutrumpet.com/ma/1.0/arj',
//        'post_header'=>'auid=6199&count=1&c.useros=4.0.4&c.sdk_version=1.0&c.app_wall_type=external&xid=31111079&c.publisher=any&c.platform=Android&passthrough[ip]=111.93.153.158',
//        'method'=>'post'
//    ),
//    '2'=>array(
//        'request_url'=>'http://thx.swelen.com/MSE?puid=airpush&slot_uid=1888e423534e52f837370f2d44a93208&ip=83.163.25.205&cua=Mozilla%2F5.0+%28Linux%3B+U%3B+Android+4.0.3%3B+nl-nl%3B+GT-P5110+Build%2FIML74K%29+AppleWebKit%2F534.30+%28KHTML%2C+like+Gecko%29+Version%2F4.0+Safari%2F534.30&client_hash=5d5ba04d14890a2c783eb9f680c073c3186f26ce',
//        'post_header'=>'',
//        'method'=>'post'
//    ),
//    '3'=>array(
//        'request_url'=>'http://google.com/?q=airpush',
//        'post_header'=>'',
//         'method'=>'get'
//        )
//    
//);
//echo '<pre>';
//
//$crl = new CLS_MULTI_CURL_TEST($array_1);
//$data = $crl->execute();
//print_r($data);

?>
