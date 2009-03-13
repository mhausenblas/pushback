<?php
 $bugid = $_POST['bugid'];
 $bugprio = $_POST['bugprio'];
 $ing = $_POST['ing'];
 $outg = $_POST['outg'];
?>
 <fieldset>
		<legend>Result</legend>	
<?php
	echo echoInput($bugid, $bugprio);
?>
<div style='font-family: sans-serif; background: white; color: red; margin: 20px; padding: 10px;'>
<div style="width:400px; float:left; margin:10px; padding:20px">
	<h2>IN-graph</h2>
	<pre>
<?php
		$inGraph = json_decode($ing, true);
		var_dump($inGraph);
?>		
	</pre>	
</div>
<div style="width:400px; float:left; margin:10px; padding:20px">
	<h2>OUT-graph</h2>
	<pre>
<?php
		$outGraph = json_decode($outg, true);
		var_dump($outGraph);
?>		
	</pre>
</div>
</div>	
</fieldset>

<?php

function echoInput($bugid, $bugprio ){
	return  "The bug with ID ". $bugid . " has a priority of " . $bugprio ;
}

?>
