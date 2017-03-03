# redif2xml
PHP RePEc ReDIF article format to DOAJ XML converter

USAGE: 
edit converter.php to change filename location, publisher and magazine ISSN. 
chmod +x converter.php
./converter.php 

(or you can run it from the web)

The script does validation for XML a gainst the XSD file ( https://doaj.org/static/doaj/doajArticles.xsd ).

DOAJ also recommends formatting the XML file at http://www.freeformatter.com/xml-formatter.html and validating it at http://www.freeformatter.com/xml-validator-xsd.html 


INCLUDED:
doajArticles.xsd - DOAJ validation schema
jsserr.rdf - sample RDF file for testing
jsserr.rdf.xml - sample converted XML file
converter.php - the converter
