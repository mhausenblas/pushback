<?php

$url = trim($_GET["url"]);
$description = trim($_GET["description"]);
$notes = trim($_GET["notes"]);
$tags = trim($_GET["tags"]);
$datestamp = trim($_GET["datestamp"]);
$shared = trim($_GET["shared"]);

require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious("oanure", "del1c10us");

$result = $dlc->addPost($url, $description, $notes, $tags, null, "yes", $shared);
if (PEAR::isError($result)) {
	die($result->getMessage());
} else {
    echo "Success";
}
?>