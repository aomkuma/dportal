<?php

	$url = "http://172.23.10.223/dpo/public/notify24Hours/";
	$result = do_post_request($url,null, "POST");

	echo "<pre>";
	print_r($result);
	exit();

	function do_post_request($url, $data, $method, $optional_headers = null)
    {
          $params = array('http' => array(
                      'method' => $method,
                      'content' => $data
                    ));
          if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
          }
          $ctx = stream_context_create($params);
          $fp = @fopen($url, 'rb', false, $ctx);
           if (!$fp) {
                return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem with $url, $php_errormsg");
            //throw new Exception("Problem with $url, $php_errormsg");
          }
          $response = @stream_get_contents($fp);
          if ($response === false) {
                return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem reading data from $url, $php_errormsg");
//            throw new Exception("Problem reading data from $url, $php_errormsg");
          }

          return $response;
          
    }


?>