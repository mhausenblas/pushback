<?php
require_once 'Services/Delicious.php';

class DeliciousService {
 
  function create($url, $title, $notes, $tags, $shared) {
	
	$dlc = &new Services_Delicious("oanure", "del1c10us");

	$result = $dlc->addPost($url, $title, $notes, $tags, null, "no", $shared);
	
	if (PEAR::isError($result)) {
		$status = "Failed";
		die($result->getMessage());
	} else {
    	$status = "Success";
	}
	return $status;
  }
  
  function deleteb($url) {
	
	$dlc = &new Services_Delicious("oanure", "del1c10us");

	$result = $dlc->deletePost($url);
	
	if (PEAR::isError($result)) {
		$status = "Failed";
		die($result->getMessage());
	} else {
    	$status = "Success";
	}
	return $status;
  }
  
}



ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache 

$server = new SoapServer("http://localhost/writewrapper/ws_soap_wsdl/DeliciousService/DeliciousService.wsdl");
$server->setClass("DeliciousService");
$server->handle();
?> 
