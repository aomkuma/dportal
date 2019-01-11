<?php
	
	include 'utilities.php';
	$result = do_post_request($dpo_office_url, "GET");
	print_r($result);

?>