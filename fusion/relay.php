<?php
	if (isset($_GET['URI'])) {
		$URI = $_GET['URI'];
		curl_get_file_contents($URI);
	}

function curl_get_file_contents($URI) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_VERBOSE, 0);
	curl_setopt($c, CURLOPT_HTTPGET, 1);
	curl_setopt($c, CURLOPT_HEADER, 0);
	curl_setopt($c, CURLOPT_URL, $URI);
	curl_setopt($c, CURLOPT_TIMEOUT, 30);
	echo curl_exec($c);
	curl_close($c);
}

?>
