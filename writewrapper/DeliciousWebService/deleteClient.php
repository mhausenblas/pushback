<?php
  $client = new SoapClient("http://localhost/writewrapper/ws_soap_wsdl/DeliciousService/DeliciousService.wsdl");
  try {
    echo "<pre>\n";
    print($client->deleteb("http://example.com"));
    echo "\n";
  } catch (SoapFault $exception) {
    echo $exception;      
  }
?>