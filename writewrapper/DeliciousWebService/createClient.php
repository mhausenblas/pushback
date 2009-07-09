<?php
  $client = new SoapClient("http://localhost/writewrapper/ws_soap_wsdl/DeliciousService/DeliciousService.wsdl");
  try {
    echo "<pre>\n";
    print($client->create("http://example.com", "Example", "Notes", "test wsdl", "no"));
	echo "\n";
  } catch (SoapFault $exception) {
    echo $exception;      
  }
?>