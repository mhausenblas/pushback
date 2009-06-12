<?php
include_once("arc/ARC2.php");

$config = array(
  /* db */
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

/* LOAD will call the Web reader, which will call the
format detector, which in turn triggers the inclusion of an
appropriate parser, etc. until the triples end up in the store. */
$store->query('LOAD <http://renaud.delbru.fr/rdf/foaf>');

/* list names */
$q = '
  PREFIX foaf: <http://xmlns.com/foaf/0.1/> .
  SELECT ?person ?name WHERE {
    ?person a foaf:Person ; foaf:name ?name .
  }
';
$r = '';
if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
    $r .= '<li>' . $row['name'] . '</li>';
  }
}

echo $r ? '<ul>' . $r . '</ul>' : 'no named persons found';
?>