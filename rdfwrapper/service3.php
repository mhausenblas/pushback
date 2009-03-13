<?php
              
$DEBUG = false;
$TWITTER_SERVICE_URI = "http://twitter.com/statuses/destroy/";
$USER = "contact mhausenblas in case you wanna know";
$PWD = "contact mhausenblas in case you wanna know";

if(isset($_GET['cmd'])){ 
	$cmd = $_GET['cmd'];
	$status = $_GET['status'];
	if (isset($_GET['debug']) && $_GET['debug']=='true') $DEBUG =  true;
	else $DEBUG = false;
	echo $DEBUG;
	executeCommand($cmd, $status);                        
}

function executeCommand($cmd, $status){
	global $DEBUG;
	
	echo "<div style=\"background: red; color: white; padding:10px;\">";
	echo "<p>Twitter RDF Wrapper says: performing $cmd on $status</p>";
	if(strlen($status) > 0) {
		removeTwitterStatus($status);
		echo "<p>Twitter RDF Wrapper says: Attempting to delete <a href=\"$status\">$status</a>; currently digesting it ... please bear with me a second.</p>";
		echo "<p>Twitter RDF Wrapper says: The result should show up shortly at <a href=\"http://twitter.com/pushback_demo\">Twitter</a> ...</p>";
		
	}
	else echo "<p>Dunno this status, sorry ...</p>";
	echo "</div>";
}

function removeTwitterStatus($status){
	global $DEBUG;
	global $TWITTER_SERVICE_URI;
	global $USER;
	global $PWD;	
	
	$status = explode('/', $status);
	$statusid = $status[count($status)-1];//the actual status id is the last component of the path of the URI
	if($DEBUG) echo "found status id=$statusid";
	$serviceURI = $TWITTER_SERVICE_URI . urlencode($statusid) . ".xml";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $serviceURI);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_USERPWD, "$USER:$PWD");
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	$result = curl_exec($ch); // POST to Twitter
	$info = curl_getinfo($ch); // response header
	curl_close($ch);

	if($DEBUG) {
		//echo "<br />[DEBUG] Twitter RDF Wrapper says: " . print_r($httpCodes);
		//echo "<br />[DEBUG] Twitter RDF Wrapper says: HTTP response header status code: " . $info['http_code']);
		echo "<br />[DEBUG] Twitter RDF Wrapper says: took " . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
	}
	return "ok";
}



?>