<?php

$photoID = trim($_GET["photoID"]); 
$photo = trim($_GET["photo"]); 
$title = trim($_GET["title"]); 
$description = trim($_GET["description"]); 
$tags = trim($_GET["tags"]); 
$is_public = trim($_GET["is_public"]); 
$is_friend = trim($_GET["is_friend"]); 
$is_family = trim($_GET["is_family"]); 

require_once("phpFlickr/phpFlickr.php");
// Create new phpFlickr object
$f = new phpFlickr("fddb59ac6e2dd1e7c618737ff72010be","1f3b3349b6bc3eb3");

$f->auth("delete");
if ($f->photos_delete($photoID)) echo "Operation succesful!";

$f->auth("write");

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

echo "New picture uploaded. ID = " . $f->sync_upload($photo, $title, $description, $tags, $is_public, $is_friend, $is_family);

?>