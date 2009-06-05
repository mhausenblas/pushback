<?php

include_once("Calendar.php");

$summary = $_GET["summary"];
$id = $_GET["IDevent"];
$starttime = $_GET["starttime"];
$endtime = $_GET["endtime"];
$location = $_GET["location"];

processPageLoad($id, $summary, $starttime, $endtime, $location);

?>