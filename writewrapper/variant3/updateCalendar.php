<?php

include_once("arc/ARC2.php");
include_once("Calendar.php");

$webID =  trim($_GET["webID"]);
$mbox = trim($_GET["mbox"]); 

$config = array(
  /* db */
  'db_name' => 'pushback',
  'db_user' => 'root',
  'db_pwd' => '',
  /* store */
  'store_name' => 'arc_store',
);
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  $store->setUp();
}

function getFieldValue($fieldKey, $calendarForm, $store) 
{
	global $webID;
	$select = '
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX pb: <http://ld2sd.deri.org/pb/ns#>
	
	SELECT DISTINCT ?value
	WHERE {
	GRAPH <' . $webID .'> {
	' . $calendarForm . ' rdf:type pb:RDForm ;
				  pb:field ?field . 
	?field pb:key ?fieldKey ;
		   pb:value ?fieldValue .
	?fieldKey rdf:value "' . $fieldKey . '" .
	?fieldValue rdf:value ?value .
	}
	}
	';
	
	$r = '';
	if ($rows = $store->query($select, 'rows')) {
	  foreach ($rows as $row) {
		return $row['value'];
	  }
	}
}

$events = array();
	
$select = '
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX pb: <http://ld2sd.deri.org/pb/ns#>

SELECT DISTINCT ?calendarForm
WHERE {
GRAPH <' . $webID . '> {
?calendarForm rdf:type pb:RDForm 
FILTER regex(?calendarForm, "/mbox=' . $mbox . '/") 
}
}';


$r = '';
if ($rows = $store->query($select, 'rows')) {
  foreach ($rows as $row) {
	$id = getFieldValue("ID", "<" . $row['calendarForm'] . ">", $store); 
	$hash = array();
	$newSummary = getFieldValue("Title", "<" . $row['calendarForm'] . ">", $store);
	$newStarttime = getFieldValue("Start time", "<" . $row['calendarForm'] . ">", $store);
	$newEndtime = getFieldValue("End time", "<" . $row['calendarForm'] . ">", $store);
	$newLocation = getFieldValue("Location", "<" . $row['calendarForm'] . ">", $store);
	$hash["id"] = $id;
	$hash["summary"] = $newSummary;
	$hash["starttime"] = $newStarttime;
	$hash["endtime"] = $newEndtime;
	$hash["location"] = $newLocation;
	array_push($events, $hash);	
  }
}

processPageLoad($events, $webID, $mbox); 

?>
