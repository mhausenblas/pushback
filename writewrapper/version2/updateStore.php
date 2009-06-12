<?php

$summary = $_GET["summary"];
$id = $_GET["IDevent"];
$starttime = $_GET["starttime"];
$endtime = $_GET["endtime"];
$location = $_GET["location"];

include_once("arc/ARC2.php");

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

$delete = 'DELETE FROM <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '>';

//delete first all triples from the context with the event id
$store->query($delete);

$insert = '
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
PREFIX pb: <http://ld2sd.deri.org/pb/ns#> .

INSERT INTO <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id .'>
{
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id .'> a pb:RDForm ;
								    pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid> ;
								    pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title> ;
								    pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime> ;
								    pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime> ;
								    pb:field <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#crud-op1> a pb:CRUDOperationUPDATE ;
								pb:onField <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title> ;
								pb:onField <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime> ;
								pb:onField <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime> ;
								pb:onField <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid.key> ; 
									  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid.value> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid.key> rdf:value "ID" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.eventid.value> rdf:value "' . trim($id) . '" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title.key> ; 
									  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title.value> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title.key> rdf:value "Title" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.title.value> rdf:value "' . trim($summary) . '" . 
	<http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime.key> ; 
									  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime.value> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime.key> rdf:value "Start time" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.starttime.value> rdf:value "' . trim($starttime) . '" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime.key> ; 
									  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime.value> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime.key> rdf:value "End time" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.endtime.value> rdf:value "' . trim($endtime) . '" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location> pb:key  <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location.key> ; 
									  pb:value <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location.value> .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location.key> rdf:value "Location" .
    <http://ld2sd.deri.org/pushback/rdforms/calendar.html#calendarForm' . $id . '.location.value> rdf:value "' . trim($location) . '" .		 	
}
';

//update by inserting the new triples in the contect with the event id
$store->query($insert);


$q = 'SELECT * WHERE {?x ?y ?z}';
$r = '';
if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
    $r .= '<li>' . $row['x'] . '  |||  ' . $row['y'] . '  |||  ' . $row['z'] . '</li>';
  }
}

echo $r ? '<ul>' . $r . '</ul>' : 'no triples have been added';

?>