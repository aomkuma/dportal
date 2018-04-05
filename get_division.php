<?php
	
	include 'utilities.php';
	$result = do_post_request($dpo_division_url, "POST", $data);
	print_r($result);

?>