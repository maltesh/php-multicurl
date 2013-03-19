php-multicurl
=============

Simple multicurl wrapper classes

Usage example:

<?php
$array_ = array(
    
    '0'=>array(
        'request_url'=>'http://rmcreative.ru/blog/tag/PHP+curl_multi',
        'post_header'=>'r=tsd32ds',
        'method'=>'post'
    ),
    
    '1'=>array(
        'request_url'=>'http://airpush.blutrumpet.com/ma/1.0/arj',
        'post_header'=>'auid=6199&count=1&c.useros=4.0.4&c.sdk_version=1.0&c.app_wall_type=external&xid=31111079&c.publisher=any&c.platform=Android&passthrough[ip]=111.93.153.158',
        'method'=>'post'
    )

);
$crl = new CLS_MULTI_CURL_TEST($array_);
$data = $crl->execute();
print_r($data);
?>
