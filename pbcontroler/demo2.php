<?php
 $debug = $_POST['debug'];
 $outg = $_POST['outg'];
?>
 <fieldset>
		<legend>Result</legend>	
<div style='font-family: sans-serif; background: white; color: red; margin: 20px; padding: 10px;'>
	<h2>diff RDF graph</h2>
<?php
		$outGraph = json_decode($outg, true);
		//echo "debug=" . $debug;
		pushback2RDFWrapper($outGraph, $debug);
?>
</div>	
</fieldset>

<?php

function pushback2RDFWrapper($outGraph, $debug){
	if($debug=='true') echo "<div style=\"width:400px; float:left; margin:10px; padding:20px\"><pre>". var_dump($outGraph) . "</pre></div>";
	//echo "found message "  . $outGraph[0]["http://www.w3.org/1999/02/22-rdf-syntax-ns#value"][0]["value"];
	
	array_walk_recursive($outGraph, 'scanValues');	

}

function scanValues($val, $key){

 echo "<div>";
 if($key == 'value') {	
	$pos = strpos($val, "http://");
	if ($pos === false) {
		echo "<div style=\"background: white; color: red; padding:10px;\">pushback controller says: found field with key=$key and value=$val</div>";
		echo file_get_contents("http://localhost:8888/pushback/rdfwrapper/service2.php?cmd=add&msg=" . urlencode($val));
	}
}
 echo "</div>";
}
?>
