<?php

$url = trim($_GET["url"]);

require_once 'Services/Delicious.php';

$dlc = &new Services_Delicious("oanure", "del1c10us");

$result = $dlc->deletePost($url);
if (PEAR::isError($result)) {
	die($result->getMessage());
} else {
    echo "Success";
}
?>