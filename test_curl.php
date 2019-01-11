<?php
	
	$params['method']	= 'send';
	$params['username']	= 'itdpo';
	$params['password']	= 'dpo@2017';

	$params['from']		= 'DPO';
	$params['to']		= '0917196810';
	$params['message']	= 'DPO ทดสอบ';
	$api_url = 'http://www.thsms.com/api/rest';
	//$api_url   = "http://soccersuck.com";
	echo sendSMS($api_url, $params);

	function sendSMS($url, $params){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params));
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return ($httpcode>=200 && $httpcode<300) ? $data : false;
	}

?>