<?php

include_once("arc/ARC2.php"); //include the ARC2 library
require_once('wadl-generated.php');

//connect to the triple store
$config = array(
  /* db */
  'db_host' => 'localhost', /* optional, default is localhost */
  'db_name' => 'pushback',
  'db_user' => 'root',
  'db_pwd' => '',
  /* store */
  'store_name' => 'arc_store',
);
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  $store->setUp();
}

function create_bookmark($id){
	global $store;
	$q = '
SELECT ?url ?descr ?notes ?tags ?shared WHERE {
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#RDForm> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#operation> <http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#CRUDOperationCREATE> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "URL" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?url . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#title.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Title" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#title.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?descr . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Notes" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?notes . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Tags" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?tags . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Shared" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?shared . 
}';

if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
	$request = new Add($row['url'], $row['descr'], $row['notes'], $row['tags'], null, "no", $row['shared']);

	$response = $request->submit();
  
	header("Content-Type:text/xml");
	echo $response;
	
  }
}

$q = 'DELETE FROM <webid> {
  	<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#operation> <http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#CRUDOperationCREATE> . 
	}';
$store->query($q);

}
function delete_triples_clause($id) {
	return 
	'DELETE FROM <webid> 
	 {
	<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#RDForm> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#operation> <http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#crud-op1' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#CRUDOperationDELETE> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "URL" . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> . 
	<http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?url . 
	}';
}

function delete_triples_created($id) {

return 
'DELETE FROM <webid> 
 {
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#RDForm> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "URL" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#url.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?url . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#title.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Title" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#title.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#title.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?decr . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Notes" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#notes.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#notes.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?extended . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Tags" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#tags.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#tags.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?tags . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#deliciousForm' . $id . '> <http://ld2sd.deri.org/pb/ns#field> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#UpdateableField> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://ld2sd.deri.org/pb/ns#key> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared.key' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared.key' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> "Shared" . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared' . $id . '> <http://ld2sd.deri.org/pb/ns#value> <http://ld2sd.deri.org/pushback/rdforms/test.html#shared.val' . $id . '> . 
<http://ld2sd.deri.org/pushback/rdforms/test.html#shared.val' . $id . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?shared . 
}';
}

$store->query($_POST["SPARQLquery"]);


$q = "SELECT ?form WHERE {
	 ?form <http://ld2sd.deri.org/pb/ns#operation> ?op .
	 ?op <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#CRUDOperationCREATE> .
	}"; 
if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
	create_bookmark(substr($row['form'],-3));
  }
}

$q = "SELECT ?form ?url WHERE {
	 ?form <http://ld2sd.deri.org/pb/ns#operation> ?op .
	 ?op <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://ld2sd.deri.org/pb/ns#CRUDOperationDELETE> .
	 ?form <http://ld2sd.deri.org/pb/ns#field> ?field .
	 ?field <http://ld2sd.deri.org/pb/ns#value> ?value .
	 ?value <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> ?url .
	}"; 
if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
	//delete the DELETE clause triples
	$store->query(delete_triples_clause(substr($row['form'],-3)));
    
	//delete from Delicious
	$request = new Delete($row['url']);
  
	$response = $request->submit();
  
	header("Content-Type:text/xml");
	echo $response;
	
	//delete the the actual created bookmark triples
	$q = "SELECT ?form WHERE {
 		 ?form <http://ld2sd.deri.org/pb/ns#field> ?field .
		 ?field <http://ld2sd.deri.org/pb/ns#value> ?val .
		 ?val <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> '" . $row['url'] . "' .
		}"; 
	$rows = $store->query($q, 'rows');
	if ($rows = $store->query($q, 'rows')) {
	  foreach ($rows as $row) {
		$store->query(delete_triples_created(substr($row['form'],-3)));
	  }
	}
  }
}


/*
$operation = "";
//$phrase_array = explode(' ',);
//print_r($phrase_array);
$myFile = "SPARQLquery.txt";
$fh = fopen($myFile, 'w');
fwrite($fh, $_POST["SPARQLquery"]);
fclose($fh);

$page = join("",file("SPARQLquery.txt"));
$lines_query = explode("\n", $page);

$posd = strpos($lines_query[0],"DELETE");
if($posd === false) {
	$posi = strpos($lines_query[0],"INSERT");
	if($posi === false) {
		echo "No SPARQL clause";
	}
	else{
		$operation = "create";
		$template = join("",file("template-create.txt"));
		$lines_template = explode("\n", $template);
	}
}
else {
	$template = join("",file("template-delete.txt"));
	$lines_template = explode("\n", $template);
	$operation = "delete";
}

echo $operation;

$webid = "";
$url = "";
$description = "";
$extended = "";
$tags = "";
$shared = "";

for ($i=0; $i < count($lines_query); $i++) {
	$posl = strrpos($lines_template[$i], "$$");
	$posf = stripos($lines_template[$i], "$$");
	if ($posf !== false && $posl !== false) {
    	 $parameter = substr($lines_template[$i], $posf+2, $posl-$posf-2);  //
		 $char = substr($lines_query[$i], $posf-1, 1);
		 $value = "";
		 if ($char == '"') {
			 $rest = substr($lines_query[$i], $posf, -1);
 		     $pos = stripos($rest, '"');
 			 $value = substr($rest, 0, $pos);
		 }
		 else if ($char == '<') {
			 $rest = substr($lines_query[$i], $posf, -1);
			 $pos = stripos($rest, '>');
			 $value = substr($rest, 0, $pos);
		 }
		 switch ($parameter) {
			case "webid":
				$webid = $value;
				break;
			case "url":
				$url = $value;
				break;
			case "description":
				$description = $value;
				break;
			case "extended":
				$extended = $value;
				break;
			case "tags":
				$tags = $value;
				break;
			case "shared":
				$shared = $value;
				break;
		}
	}
}

echo "webid= " . $webid;
echo "operation= " . $operation;
echo "url= " . $url;
echo "descr= " . $description;
echo "extended= " .$extended;
echo "tags= " . $tags;
echo "shared= " . $shared;

//Add a bookmark: replace param -> no
if ($operation == "create") {
	$store->query($_POST["SPARQLquery"]);
	$request = new Add($url, $description, $extended, $tags, null, "no", $shared);

	$response = $request->submit();
  
	header("Content-Type:text/xml");
	echo $response;
}
else if ($operation == "delete") {
	$qf = "SELECT ?form WHERE {
 		 ?form <http://ld2sd.deri.org/pb/ns#field> ?field .
		 ?field <http://ld2sd.deri.org/pb/ns#value> ?val .
		 ?val <http://www.w3.org/1999/02/22-rdf-syntax-ns#value> '" . $url . "' .
		}"; 
	echo $qf;
	$rows = $store->query($qf, 'rows');
	if ($rows = $store->query($qf, 'rows')) {
	  foreach ($rows as $row) {
		$store->query(delete_triples(substr($row['form'],-3)));
	  }
	}

	$request = new Delete($url);
  
	$response = $request->submit();
  
	header("Content-Type:text/xml");
	echo $response;
}
*/
/*********************/ 
/*                   */  
/* pure curl request */
/*                   */
/*********************/

/*$curl_request = "https://username:password@api.del.icio.us/v1/posts/add?url=" . $url . "&description=" . $description . "&extended=" . $extended . 
				"&tags=" . $tags . "&replace=no" . "&shared=" . $shared; 
echo $curl_request;
*/
 
/**************************/ 
/*                        */  
/* Language specific call */
/*                        */
/**************************/

/*
//Update a bookmark: replave param -> yes
$request = new Add('http://www.example.org', 'An example site2', "Some notes", "test wadl curl", null, "yes", "no");
  
//Delete a bookmark
$request = new Delete('http://www.example.org');
  
$response = $request->submit();
  
header("Content-Type:text/xml");
echo $response;
*/

/****************************/ 
/*                          */  
/* WADL method description  */
/*  			            */
/****************************/

//see SIMPLE XML lib

?>