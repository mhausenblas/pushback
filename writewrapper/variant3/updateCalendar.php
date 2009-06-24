<?php

/*
This script queries the ARC2 triple store.
It extracts the events for a particular webID and Google account name.

The events will be saved in an array, as following:
Array ( [0] => Array ( "id" => id , "summary" => summary, "starttime" => startime, "endtime" => endtime, "location" => location ) , [1] => Array ( "id" => id ... ) )

The array will be passed as a parameter to the Calendar.php script which utilizes the 
Zend Framework Gdata components to communicate with the Google API
*/

include_once("arc/ARC2.php");
include_once("Calendar.php");

$webID =  trim($_GET["webID"]); //the agent webID
$mbox = trim($_GET["mbox"]);  //the agent Google account name


//connect to the ARC2 triple store
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

/**
 * This function returns the requested attribute's value of a particular event
 * 
 * $fieldKey = the requested attribute name
 * $calendarForm = the information unit that holds a Google event
 * $store = the ARC2 triple store
 * 
 * Env variables used:
 * $webID = the agent's webID
 *
 * @return string the attribute's value
 */
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

//the array used to store the events
$events = array();
	
//SPARQL query to get all the events in a particular context (webID) and for a particluar Google account
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
if ($rows = $store->query($select, 'rows')) { //for every event returned by the SELECT SPARQL query
  foreach ($rows as $row) {
	$id = getFieldValue("ID", "<" . $row['calendarForm'] . ">", $store); //get the ID of an event
	$hash = array();
	$newSummary = getFieldValue("Title", "<" . $row['calendarForm'] . ">", $store); //get the summary of the evnet
	$newStarttime = getFieldValue("Start time", "<" . $row['calendarForm'] . ">", $store); //get the start time of the event
	$newEndtime = getFieldValue("End time", "<" . $row['calendarForm'] . ">", $store); //get the end time of the event
	$newLocation = getFieldValue("Location", "<" . $row['calendarForm'] . ">", $store); //get the location of the event
	
	//save the event attributes in a hash map
	$hash["id"] = $id;
	$hash["summary"] = $newSummary;
	$hash["starttime"] = $newStarttime;
	$hash["endtime"] = $newEndtime;
	$hash["location"] = $newLocation;
	
	//push the hash map in the events array
	array_push($events, $hash);	
  }
}

//send the events array, webID and mbox
processPageLoad($events, $webID, $mbox); 

?>
