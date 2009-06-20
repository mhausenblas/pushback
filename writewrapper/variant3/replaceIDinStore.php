<?php

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
	//echo "<br>Event with id " . $id . " was deleted";
}

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
	
	//update by inserting the new triples in the context with the event id
	$store->query($insert);
	//echo "Inserted event with ID " . $id;
}

function viewTriples() {
	$q = 'SELECT * WHERE {?x ?y ?z}';
	$r = '';
	if ($rows = $store->query($q, 'rows')) {
	  foreach ($rows as $row) {
		$r .= '<li>' . $row['x'] . '  |||  ' . $row['y'] . '  |||  ' . $row['z'] . '</li>';
	  }
	}
	
	echo $r ? '<ul>' . $r . '</ul>' : 'no triples have been added';
}

?>