<?php

/**
 * This script provides the functionality to update an event: DELETE + INSERT
 * Its purpose was to replace an event ID value.
 * 
 */
 
include_once("arc/ARC2.php");

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
 * This function deletes an event from the triple store
 * 
 * $id = the ID of the event to delete
 * $mbox = the Google account name
 * $webID = the agent's Web ID
 * 
 * Env variables used:
 * $store = the triple store
 *
 * @return void
 */
function deleteEventWithID($id, $webID, $mbox) {
	global $store;
		
	$delete = '
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
	PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
	
	DELETE FROM <' . $webID . '> 
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
	
	$store->query($delete);
}

/**
 * This function inserts triples for a new event in the triple store, it uses the RDForms vocabulary, that can be found at:
 * http://esw.w3.org/topic/PushBackDataToLegacySourcesRDForms
 * 
 * $id = the ID of the event
 * $webID = the agent's web ID
 * $summary = the title of the event
 * $starttime = the start time of the event
 * $endtime = the end time of the event
 * $location = the location of the event
 * 
 * Env variables used:
 * $mbox = the Google account name
 * $store = the triple store
 *
 * @return void
 */
function insertEvent($id, $webID, $mbox, $summary, $starttime, $endtime, $location)
{
	global $store;
	$id = substr($id, -26);
	$insert = '
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
	PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .
	
	INSERT INTO <' . $webID . '>
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
	
	$store->query($insert);
}

?>