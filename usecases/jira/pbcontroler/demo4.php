<?php
/*
 * This script is for execute methods from Jira API 
 * using SOAP protocol
 * 
 * 13-03-2009 by sda   
 */
include('common-functions.php');


// this arrya will map used rdfform fields to ids used in JIRA 
// rdform_field_one=>jira_one
$mappingRDFid2JIRAid = array(
	'http://ld2sd.deri.org/pb/demo#fo1.f1'=>'id',
	'http://ld2sd.deri.org/pb/demo#fo1.f2'=>'summary', 
	'http://ld2sd.deri.org/pb/demo#fo1.f3'=>'issuetype',
	'http://ld2sd.deri.org/pb/demo#fo1.f4'=>'priority',
	'http://ld2sd.deri.org/pb/demo#fo1.f5'=>'assignee',
	'http://ld2sd.deri.org/pb/demo#fo1.f6'=>'reporter',
	'http://ld2sd.deri.org/pb/demo#fo1.f7'=>'duedate'
);

$DEBUG = false;
$outg = $_POST['outg'];
$ing = $_POST['ing'];

if (get_magic_quotes_gpc()) {
    $ing = stripslashes($ing);
    $outg = stripslashes($outg);
}

if (isset($_POST['debug']) && $_POST['debug'] == 'true'  ) {
	$DEBUG =  true;
	echo "DEBUG: $DEBUG";
} 

$inGraph = json_decode($ing, true);
$outGraph = json_decode($outg, true);

if($DEBUG){
?>
 <fieldset>
		<legend>Result</legend>	
<div style='font-family: sans-serif; background: white; color: red; margin: 20px; padding: 10px;'>
	<h2>Diff RDF graph</h2>
	<div style="width:400px; float:left; margin:10px; padding:20px">
		<h2>IN-graph</h2>
		<pre>
	<?php 
			var_dump($inGraph);
	?>		
		</pre>	
	</div>
	<div style="width:400px; float:left; margin:10px; padding:20px">
		<h2>OUT-graph</h2>
		<pre>
	<?php 
			var_dump($outGraph); 	
	?>		
		</pre>
	</div>
</div>	
</div>
</fieldset>
<?php 
}
?>
<div style="clear:both;"></div>
 <fieldset>
		<legend>Result</legend>	
<div style='font-family: sans-serif; background: white; color: red; margin: 20px; padding: 10px;'>
	<h2>FINALL RESULT</h2>
	<?php 
			// GRAB THE VALUES FROM THE GRAPH PART
			$array_walk_recursive3 = new Array_walk_recursive3($outGraph, 'test_print',$DEBUG);
			if($DEBUG){
				echo '<br />'.$array_walk_recursive3->status;
				Tools::show($array_walk_recursive3->getCallbackResult());
			}
			// SOAP PART 
			$username= 'admin';
			$password='admin';
			$wsdl = "http://localhost:8080/rpc/soap/jirasoapservice-v2?wsdl";
			$jiraSoapClient = new JiraSoapClient($username,$password,$wsdl,$DEBUG);
			$result  = $jiraSoapClient->updateIssue($array_walk_recursive3->getCallbackResult() );
			if($result===true){
				echo 'SUCCESS';
			}else{
				echo 'FAILURE: '.$result;
			}
	?>
</div>
</fieldset>
	