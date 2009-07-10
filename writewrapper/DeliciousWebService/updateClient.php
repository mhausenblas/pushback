<?php
  $client = new SoapClient("http://localhost/writewrapper/ws_soap_wsdl/DeliciousService/DeliciousService.wsdl");
  try {
    echo "<pre>\n";
    print($client->update("http://example.com", "Example2", "Notes2", "test2 wsdl2", "no"));
	echo "\n";
  } catch (SoapFault $exception) {
    echo $exception;      
  }
?>