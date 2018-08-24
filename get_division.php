<?php
	
	include 'utilities.php';
	$result = do_post_request($dpo_division_url, "POST");
	print_r($result);

?>