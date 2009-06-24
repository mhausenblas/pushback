<?php

/*
This script provides functionality for updating an RDF triple store (via SPARQL Update) with Google Calendar events in RDF format.
For the triple store the script utilizes the ARC2 library. The library can be downloaded from: http://arc.semsol.org/download

There are 3 operation applied to Google calendar events:

1) CREATE event - mapped to SPARQL INSERT query
2) DELETE event - mapped to SPARQL DELETE query
3) UPDATE event - mapped to SPARQL DELETE+INSERT query

Parameters overview:

Generally required:
$operation - create, update or delete
$webID - the agent's WebID (used to INSERT events in a particular context)
$mbox - the Google account name of the agent (used to differentiate between calendars inside the same context)
		For example: an agent with a WebID has two Google account and keeps 2 calendars, one for every Google account
		
Required for CREATE event:

$summary - the title of the event (can be blank)
$starttime - the start time of the event
$endtime - the end time of the event
$location - the location of the event (can be blank)

Required for UPDATE event:

$id - the id of the event that needs to be modified
$summary - the new title of the event (can be remained unchanged)
$starttime - the new start time of the event (can be remained unchanged)
$endtime - the new end time of the event (can be remained unchanged)
$location - the new location of the event (can be remained unchanged)

Required for DELETE event:

$id - the id of the event to delete
*/


//GET the generally required parameters from the client
$operation = trim($_GET["operation"]);
$webID =  trim($_GET["webID"]);
$mbox = trim($_GET["mbox"]); 

include_once("arc/ARC2.php"); //include the ARC2 library

//connect to the triple store
$config = array(
  /* db */
  'db_host' => 'localhost', /* optional, default is localhost */
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
 * This function generates a unique ID when an event is created
 * the ID will be updated when the event will be saved through Google's specific API; 
 * when saving an event using the Google API the Google event ID is returned
 * 
 * $length = the length of the random string
 *
 * @return string generated random string
 */
function generateID ($length = 8)
{
  // start with a blank id
  $id = "";
  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
  // set up a counter
  $i = 0; 
    
  // add random characters to $id until $length is reached
  while ($i < $length) { 
    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    // we don't want this character if it's already in the id
    if (!strstr($id, $char)) { 
      $id .= $char;
      $i++;
    }
  }
  // done!
  return $id;
}


/**
 * This function creates the triples to insert using the RDForms vocabulary, that can be found at:
 * http://esw.w3.org/topic/PushBackDataToLegacySourcesRDForms
 * 
 * $id = the ID of the event
 * $summary = the title of the event
 * $starttime = the start time of the event
 * $endtime = the end time of the event
 * $location = the location of the event
 * 
 * Env variables used:
 * $mbox = the Google account name
 *
 * @return string RDF triples to insert
 */
function create_triples($id, $summary, $starttime, $endtime, $location) {

global $mbox;

return '
	{
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id .'> a pb:RDForm ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.key> rdf:value "ID" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.value> rdf:value "' . $id . '" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.key> rdf:value "Title" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.value> rdf:value "' . $summary . '" . 
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.key> rdf:value "Start time" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.value> rdf:value "' . $starttime . '" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.key> rdf:value "End time" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.value> rdf:value "' . $endtime . '" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.key> rdf:value "Location" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.value> rdf:value "' . $location . '" .		 	
	}';
}

/**
 * This function creates the triples to delete
 * NOTE: instead of specifying the attributes of the event (as done when creating an event, i.e. $location), 
 * the deletion process doesn't care about the information stored, so it uses wildcards (i.e. ?location)
 * 
 * $id = the ID of the event to delete
 * 
 * Env variables used:
 * $mbox = the Google account name
 *
 * @return string RDF triples to delete
 */
function delete_triples($id) {

global $mbox;

return '
	{
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id .'> a pb:RDForm ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime> ;
										pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.key> rdf:value "ID" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.eventid.value> rdf:value "' . $id . '" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.key> rdf:value "Title" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.title.value> rdf:value ?summary . 
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.key> rdf:value "Start time" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.starttime.value> rdf:value ?starttime .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.key> rdf:value "End time" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.endtime.value> rdf:value ?endtime .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.key> ; 
										  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.value> .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.key> rdf:value "Location" .
		<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm/mbox=' . $mbox . '/id=' .  $id . '.location.value> rdf:value ?location .		 	
	}';
}

/**
 * This function reads the operation type (create, update or delete) and updates the ARC2 triple store.
 * 
 * Env variables used:
 * $operation = the operation: create, update, delete
 * $mbox = the Google account name
 * $webID = the WebID of the agent
 * $store - the ARC2 triple store
 *
 * @return void
 */
function updateStore() {
	global $operation;
	global $mbox;
	global $webID;
	global $store;
	
	if ($operation == "create") {
		
		$summary = trim($_GET["summary"]); //optional
		$starttime = trim($_GET["starttime"]);
		$endtime = trim($_GET["endtime"]);
		$location = trim($_GET["location"]); //optional
		$id = generateID(); //required for creating an event - will be updated with acurate ID received from the Google API
	
		//triples are inserted into the webID context
		$insert = '
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
		
		INSERT INTO <' . $webID . '> 
		' . create_triples($id, $summary, $starttime, $endtime, $location);
		
		$store->query($insert);
	}
	else if ($operation == "delete"){
		
		$id = trim($_GET["IDevent"]);
	
		//deletes triples from webID context 
		$delete = '
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
		
		DELETE FROM <' . $webID . '>
		' . delete_triples($id);
		
		$store->query($delete);
		//echo "<br>Event with id " . $id . " was deleted";
	}
	else if ($operation == "update"){
		
		//delete the old event
		$id = trim($_GET["IDevent"]);
		
		$delete = '
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
		
		DELETE FROM <' . $webID . '>
		' . delete_triples($id);
		
		$store->query($delete);
		
		//create a new one with the new values
		$summary = trim($_GET["summary"]); //optional
		$starttime = trim($_GET["starttime"]);
		$endtime = trim($_GET["endtime"]);
		$location = trim($_GET["location"]); //optional
	
		$insert = '
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
		
		INSERT INTO <' . $webID . '> 
		' . create_triples($id, $summary, $starttime, $endtime, $location);
		
		$store->query($insert);
	}
}

updateStore();

//redirect to the Google authentication request page - the Google calendar will be updated
header('Location:http://localhost/writewrapper/variant3/updateCalendar.php?webID=' . $webID . '&mbox=' . $mbox);

?>