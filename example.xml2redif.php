<?php

require('redifxmlconvertor.class.php');

$repechandle = "edt:jsserr";
$filename    = 'jsserr.rdf.xml';	// SOURCE FILE
$schemafile  = 'doajArticles.xsd'; // SCHEMA FILE - DOESN'T NEED CHANGING USUALLY

$convertor = new RedifXMLConvert(NULL,NULL,$filename,$schemafile,$repechandle);

$return    = $convertor->xml2redif();

echo $return;

?>
