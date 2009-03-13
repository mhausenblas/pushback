<?php
	$DEBUG = false;
	$outg = $_POST['outg'];
	if (isset($_POST['debug'])) {
		if ($_POST['debug'] == 'true') { 
			$DEBUG =  true;
			echo "DEBUG: $DEBUG";
		}
		else $DEBUG = false;
	}
	else $DEBUG = false;
?>
 <fieldset>
		<legend>Result</legend>	
<div style='font-family: sans-serif; background: white; color: red; margin: 20px; padding: 10px;'>
	<h2>Diff RDF graph</h2>
<?php
	
		$outGraph = json_decode($outg, true);
		pushback2RDFWrapper($outGraph, $DEBUG);
?>
</div>	
</fieldset>

<?php

function pushback2RDFWrapper($outGraph){
	global $DEBUG;
	
	if($DEBUG) echo "<div style=\"width:600px; margin:10px; padding:20px\"><pre>". var_export($outGraph, true) . "</pre></div>";
	array_walk_recursive($outGraph, 'scanValues');
	echo "<div style=\"background: white; color: red; padding:10px;\">pushback controller says: done.</div>";

}

function scanValues($val, $key){
	global $DEBUG;
	
	if($key == 'value') {	
		$pos = strpos($val, "http://twitter.com");
		if($DEBUG) echo "<div style=\"background: white; color: red; padding:10px;\">pushback controller says: looking at value $val ...</div>";
		if ($pos === 0) {
			echo "<div style=\"background: white; color: red; padding:10px;\">pushback controller says: found field with key=$key and value=$val</div>";
			curl_get_file_contents("http://localhost:8888/pushback/rdfwrapper/service3.php?cmd=delete&status=" . urlencode($val) . "&debug=" . $DEBUG);
		}
	}
}

function curl_get_file_contents($URI) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_VERBOSE, 0);
	curl_setopt($c, CURLOPT_HTTPGET, 1);
	curl_setopt($c, CURLOPT_HEADER, 0);
	curl_setopt($c, CURLOPT_URL, $URI);
	curl_setopt($c, CURLOPT_TIMEOUT, 30);
	echo curl_exec($c);
	curl_close($c);
}


?>
