<?php
require('redifxmlconvertor.class.php');
$schemafile = 'doajArticles.xsd'; 

if ($download==1){
	header('Content-Disposition: attachment; filename='.$filename);
	header("HTTP/1.1 200 OK");
	header('X-Robots-Tag: noindex, follow', true);
	header('Content-Type: application/xml; charset=utf-8');

}

//redif 2 xml
/*
$issn       = '2392-9863';	// ISSN
$publisher  = 'Editura Sitech';	// PUBLISHER NAME
$filename   = 'jsserr.rdf';	// SOURCE FILE

$convertor = new RedifXMLConvert($issn,$publisher,$filename,$schemafile);
$return = $convertor->redif2xml();

echo $return;
*/

//xml 2 redif
/* 
$repechandle = "edt:jsserr";
$filename    = 'jsserr.rdf.xml';	// SOURCE FILE

$convertor = new RedifXMLConvert(NULL,NULL,$filename,$schemafile,$repechandle);
$return    = $convertor->xml2redif();

echo $return;
*/

?>
<html>
	<head>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form .form-heading,
.form .checkbox {
  margin-bottom: 10px;
}
.form .checkbox {
  font-weight: normal;
}
.form .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form .form-control:focus {
  z-index: 2;
}
.form input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
</style>

	</head>

	<body>

	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				RDF XML converter. More info and source code at <a href="https://github.com/danvoinea/redif2xml">https://github.com/danvoinea/redif2xml</a>
			</div>

			<div class="col-lg-6">


    <form class="form"  action="example.form.php" method="post" enctype="multipart/form-data">
        <h2 class="form-heading">XML 2 REDIF</h2>
        <label for="inputHandle" class="sr-only">Enter RePEc handle</label>
        <input type="text" id="inputHandle" class="form-control" placeholder="Enter RePEc handle (format edt:jsserr)" required autofocus>

        <label for="fileToUpload" class="sr-only">Select file</label>
	<input type="file" name="fileToUpload" id="fileToUpload" class="form-control" placeholder="Select file" required>

        <div class="checkbox">
          <label>
            <input type="checkbox" value="download"> Download
          </label>
        </div>

	<input type="hidden" name="convertor" value="xml2redif">

        <button class="btn btn-lg btn-primary btn-block" type="submit">Convert</button>
      </form>


			</div>

			<div class="col-lg-6">

      <form class="form"  action="example.form.php" method="post" enctype="multipart/form-data">
        <h2 class="form-heading">REDIF 2 XML</h2>

        <label for="inputPublisher" class="sr-only">Enter Publisher</label>
        <input type="text" id="inputPublisher" class="form-control" placeholder="Enter Publisher" required autofocus>

        <label for="enterISSN" class="sr-only">Enter ISSN</label>
        <input type="text" id="enterISSN" class="form-control" placeholder="Enter ISSN" required autofocus>


        <label for="fileToUpload" class="sr-only">Select file</label>
	<input type="file" name="fileToUpload" id="fileToUpload" class="form-control" placeholder="Select file" required>

        <div class="checkbox">
          <label>
            <input type="checkbox" value="download"> Download
          </label>
        </div>

	<input type="hidden" name="convertor" value="redif2xml">

        <button class="btn btn-lg btn-primary btn-block" type="submit">Convert</button>
      </form>


			</div>

		</div>
	</div>



	</body>
</html>
