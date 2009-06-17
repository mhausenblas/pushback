<?php

include_once("Calendar.php");

$id = $_GET["id"];
$summary = $_GET["title"];
$starttime = $_GET["starttime"];
$endtime = $_GET["endtime"];
$location = $_GET["location"];
$operation = $_GET["operation"];

processPageLoad($id, $summary, $starttime, $endtime, $location, $operation);

?>