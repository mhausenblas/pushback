<?php
              
$DEBUG = true;
$TWITTER_SERVICE_URI = "http://twitter.com/statuses/update.xml?status=";
$USER = "contact mhausenblas in case you wanna know";
$PWD = "contact mhausenblas in case you wanna know";

if(isset($_GET['cmd'])){ 
        $cmd = $_GET['cmd'];
        $msg = $_GET['msg'];

		executeCommand($cmd, $msg);                        
}

function executeCommand($cmd, $msg){
	global $DEBUG;
	
	echo "<div style=\"background: red; color: white; padding:10px;\">";
	echo "<p>Twitter RDF Wrapper says: performing $cmd with message $msg</p>";
	if(strlen($msg) > 0) {
		$twitter_status = post2Twitter($msg);
		echo "<p>Twitter RDF Wrapper says: Thanks for your message; currently digesting it ... please bear with me a second.</p>";
		echo "<p>Twitter RDF Wrapper says: The result should show up shortly at <a href=\"http://twitter.com/pushback_demo\">Twitter</a> ...</p>";
		
	}
	else echo "<p>Nah, I'm not gonna send a blank twit, dude ;)</p>";
	echo "</div>";
}

function post2Twitter($message){
	global $DEBUG;
	global $TWITTER_SERVICE_URI;
	global $USER;
	global $PWD;	
	
	$serviceURI = $TWITTER_SERVICE_URI . urlencode(stripslashes(urldecode($message)));
	$httpCodes = parse_ini_file("http_status_codes.txt");
	
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