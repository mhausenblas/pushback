<?php 

/*
Compare two ICS files by parsing them.
Read the files line by line and save the "":"" pair in arrays
Every event will have its own hash. For the following event:

BEGIN:VEVENT
CLASS:PRIVATE
CREATED:20080219T121549Z
DTEND:20080305T160000Z
DTSTAMP:20090520T134458Z
DTSTART:20080305T140000Z
LAST-MODIFIED:20080219T121549Z
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:Silicon Valley presentation\, DERI seminar room
TRANSP:OPAQUE
UID:038au54i7cbs72gv40vebkdiek@google.com
END:VEVENT

its corresponding hash will look like this: ("CLASS" => "PRIVATE", "CREATED" => "20080219T121549Z", "DTEND" => "20080305T160000Z", "DTSTAMP" => "20090520T134458Z",
"DTSTART" => "20080305T140000Z", "LAST-MODIFIED" => "20080219T121549Z", ............)
Then the hash will be pushed in an array:
([0] => ("CLASS" => "PRIVATE", "CREATED" = "...", ....), [1] => .....)  
The array will contain all the event in the ICS file.

This procedure is applied to the old and the updated ICS file, resulting in two arrays.
The second step compares these two arrays. It should be noted that the order of the event and the order of the keys in hashes can be different.
So, for every event in the updated ICS file an identical event will be search through the original ICS file, by comparing each key value pair.

AUTHOR: Ureche Oana-Elena 
*/

include ("Calendar.php");

function getArray($filename) {
	$file = file($filename);
	
	$hash = array();
	$array = array();
	//$hash = array("DTSTART" => "start", "DTEND" => "end");
	//$array list of $hash ;

	$flag = 0; //TODO: flag that we do not really need
	//for every line in the .ics file
	
	foreach ($file as $line) {
		
		//if a new event starts set the flag
		//this is to signal that we passed the intro lines
		if (strcmp(trim($line),"BEGIN:VEVENT") == 0) {	
			$flag = 1;
		}
		//if we reached the end of the event, we write the hash into the array
		//and increment the index
		else if (strcmp(trim($line),"END:VEVENT") == 0) {
			array_push ($array,$hash); 
			$hash = (array) null; //empty the array, otherwise old values from old events will be kept inside
		} 
		//TODO: pass the intro information before the main loop
		//so we don't have to check any condition here
		else if ($flag == 1){
			//we are in the middle of the event, so we write the keys and the values
			//into an array
			list ($key, $value) = explode(":", $line, 2);
			if (strcmp(trim($value),"") == 0) {	
			}
			else {
			$hash[trim($key)] = trim($value);
			}			
		}
	}
	return $array;

}

function is_cloned($value, $value2){
 	if(serialize($value)===serialize($value2)) return true;
	else return false;
}

/*
This function searches the new found event (by ID) in the old calendar and
updates the modified fields (e.g. If the SUMMARY has changed, the title will be updated)
*/

function findAndUpdateEvent($newEvent, $calendar) {
	foreach ($calendar as $i => $event) {
		if (strcmp($event["UID"], $newEvent["UID"]) == 0) {
			foreach ($event as $key => $value){
				if (strcmp($event[$key],$newEvent[$key]) == 0){
					//equal
				}
				else {
					echo $key . " is different<br>"; 
					switch ($key) {
						case "SUMMARY":
							$id = str_replace("@google.com", "", $newEvent["UID"]);
							processPageLoad($id, "SUMMARY", $newEvent["SUMMARY"]); //title is updated in Calendar
							break;
						case "DTSTART":
							echo "update start time";
							break;
						case "DTEND":
							echo "update end time";
							break;	
					}
				}				 
			}
		}
	}
}

//compare the new old with the old one
//if an event was modified, il will not be found in the original calendar and thus show up as new
$ics1 = getArray("oana2.ics");
$ics2 = getArray("oana.ics");

foreach ($ics1 as $i => $value) {
	//$value = inner array, ex:   array("dtstart" => "start", "dtend" => "end")
	$flag = false;
	foreach ($ics2 as $i2 => $value2) {
	    //echo "compare " . $i . " with " . $i2 . "<br>";

		//sort the array by keys -> the keys might not be in the same order 
		ksort($value); ksort($value2); 
		
		//print the array that we want to compare
		//print_r($value); echo "<br><br>";
		//print_r($value2); echo "<br><br>";
	    
		//$value2 = inner array, ex:   array("begin" => "start", "end" => "end"), 
		//the comparison compare both the keys and the values
		$result = is_cloned($value, $value2);
		
		if ($result == true) { //if we find an equal array (same event is in both ics files) flag it
		 $flag = true; 
		 //echo " found equal";
		}
	}
	if ($flag == false) { //we did not find an identical event
						  //so we will use the Google Calendar API to update the event	 	

		findAndUpdateEvent($value, $ics2); //find the event in the old calendar and update what is new
		
	}
} 

?>