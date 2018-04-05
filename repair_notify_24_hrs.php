<?php

  include 'utilities.php';
	$url = "http://172.23.10.223/dpo/public/notify24Hours/";
	$result = do_post_request($url, "POST");

	echo "<pre>";
	print_r($result);
	exit();

	


?>