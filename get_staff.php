<?php
	
	include 'utilities.php';
	$result = do_post_request($dpo_staff_url, "GET");
	// $result = curlPost($dpo_staff_url, "POST");
	// $result = file_get_contents($dpo_staff_url);
	print_r($result);
	
?>