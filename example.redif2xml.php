<?php

require('redifxmlconvertor.class.php');

$issn       = '2392-9863';	// ISSN
$publisher  = 'Editura Sitech';	// PUBLISHER NAME
$filename   = 'jsserr.rdf';	// SOURCE FILE
$schemafile = 'doajArticles.xsd'; // SCHEMA FILE - DOESN'T NEED CHANGING USUALLY

$convertor = new RedifXMLConvert($issn,$publisher,$filename,$schemafile);

$return = $convertor->redif2xml();

echo $return;

?>
