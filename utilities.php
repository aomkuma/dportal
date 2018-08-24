<?php

	$department_url = 'http://hrquery.dpo.go.th/json.php?department';
	$division_url = 'http://hrquery.dpo.go.th/json.php?division';
	$office_url = 'http://hrquery.dpo.go.th/json.php?office';
	$staff_url = 'http://hrquery.dpo.go.th/json.php?staff';

	$dpo_url = 'https://172.23.10.223';
	$dpo_department_url = $dpo_url . '/dpo/public/eHrUpdateDepartment/';
	$dpo_division_url = $dpo_url . '/dpo/public/eHrUpdateDivision/';
	$dpo_office_url = $dpo_url . '/dpo/public/eHrUpdateOffice/';
	$dpo_staff_url = $dpo_url . '/dpo/public/eHrUpdateStaff/';

	function do_post_request($url, $method, $data = [], $optional_headers = null)
    {
          $params = array('http' => array(
                      'method' => $method,
                      'content' => http_build_query($data)
                    ));
          if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
          }
          $ctx = stream_context_create($params);
          $fp = @fopen($url, 'rb', false, $ctx);
           if (!$fp) {
                return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem with $url");
            //throw new Exception("Problem with $url, $php_errormsg");
          }
          $response = @stream_get_contents($fp);
          if ($response === false) {
                return array("STATUS"=>'ERROR',"MSG"=>"ERROR :: Problem reading data from $url");
//            throw new Exception("Problem reading data from $url");
          }

          return $response;
          
    }

    function curlPost($url, $params){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 

1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		if(!empty($params)){
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params));
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		//echo $httpcode;
		return ($httpcode>=200 && $httpcode<300) ? $data : false;
	}

?>