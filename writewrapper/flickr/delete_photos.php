<?php

$photoID = trim($_GET["photoID"]);

require_once("phpFlickr/phpFlickr.php");
// Create new phpFlickr object
$f = new phpFlickr("fddb59ac6e2dd1e7c618737ff72010be","1f3b3349b6bc3eb3");
$f->auth("delete");

/*
Accepted arguments for upload:
Photo: The path of the file to upload.
Title: The title of the photo.
Description: A description of the photo. May contain some limited HTML.
Tags: A space-separated list of tags to apply to the photo.
is_public: Set to 0 for no, 1 for yes.
is_friend: Set to 0 for no, 1 for yes.
is_family: Set to 0 for no, 1 for yes.
*/

if ($f->photos_delete($photoID)) echo "Operation succesful!";

?>