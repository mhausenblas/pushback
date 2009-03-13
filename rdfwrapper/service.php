<?php
include_once("../../arc/ARC2.php");
include_once("twitterAPI.php");
              
$DEBUG = false;
$BASE_URI  = "http://ld2sd.deri.org/pb/demo#";

/* ARC RDF store config */
$config = array(
	'db_name' => 'arc2',
	'db_user' => 'root',
	'db_pwd' => 'root',
	'store_name' => 'pushback',
); 

$store = ARC2::getStore($config);

if (!$store->isSetUp()) {
	$store->setUp();
	echo 'set up';
}

if(isset($_GET['reset'])) {
	$store->reset();
	echo "store has been reseted, master<br />\n";    
}

if(isset($_GET['cmd'])){ 
        $cmd = $_GET['cmd'];
        $msg = $_GET['msg'];

		executeCommand($cmd, $msg);                        
}

function executeCommand($cmd, $msg){
	global $store;
	global $DEBUG;
	global $BASE_URI;
	global $VOID_SEEDS_GRAPH;
	
	echo "<div style=\"background: red; color: white; padding:10px;\">";
	echo "<p>Twitter RDF Wrapper says: performing $cmd on $BASE_URI with message $msg</p>";
	if(strlen($msg) > 0) {
		$twitter_status = postToTwitter("pushback_demo", "123pushback", $msg);
		echo "<p>Thanks for your message; currently digesting it ... please bear with me a second ...</p>";
		echo "<p>The result should show up shortly at <a href=\"http://twitter.com/pushback_demo\">Twitter</a></p>";
		
	}
	else echo "<p>Nah, I'm not gonna send a blank twit, dude ;)</p>";
	echo "</div>";
}

?>