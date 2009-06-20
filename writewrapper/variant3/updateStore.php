<?php

$operation = trim($_GET["operation"]);
$webID =  trim($_GET["webID"]);
$mbox = trim($_GET["mbox"]); 

include_once("arc/ARC2.php");

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
		$id = generateID();
	
		$insert = '
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
		
		INSERT INTO <' . $webID . '> 
		' . create_triples($id, $summary, $starttime, $endtime, $location);
		
		//update by inserting the new triples in the context with the event id
		$store->query($insert);
		//echo "<br>New event was inserted";
	}
	else if ($operation == "delete"){
		
		$id = trim($_GET["IDevent"]);
	
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
		//echo "<br>Event with id " . $id . " was deleted";
		
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
		
		//update by inserting the new triples in the context with the event id
		$store->query($insert);
		//echo "<br>New event was inserted";
	}
}

updateStore();
header('Location:http://localhost/writewrapper/variant3/updateCalendar.php?webID=' . $webID . '&mbox=' . $mbox);

?>