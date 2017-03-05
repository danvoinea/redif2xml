<?php

/* DO NOT EDIT BEYOND THIS POINT. NO NEED TO DO SO */

libxml_use_internal_errors(true);

class RedifXMLConvert {
	var $issn;
	var $publisher;
	var $filename;
	var $schemafile;
	var $repechandle;
	
	function __construct($issn = false,$publisher = false,$filename,$schemafile,$repechandle = false)
	{
		$this->issn = $issn;
		$this->publisher = $publisher;
		$this->filename = $filename;
		$this->schemafile = $schemafile;
		$this->repechandle = $repechandle;
	}



	function find_in_post($post, $needle)
	{
	    $array = explode("\n", $post);
	    	foreach ($array as $value) {
	        	$strings = explode(": ", $value);
	       		if ($strings[0] == $needle) {
		            return $strings[1];
		        }
		}
    	}

	function libxml_display_error($error)
	{
	    $return = "<br/>\n";
	    switch ($error->level) {
	        case LIBXML_ERR_WARNING:
	            $return .= "<b>Warning $error->code</b>: ";
	            break;
	        case LIBXML_ERR_ERROR:
	            $return .= "<b>Error $error->code</b>: ";
	            break;
	        case LIBXML_ERR_FATAL:
	            $return .= "<b>Fatal Error $error->code</b>: ";
	            break;
	    }
	    $return .= trim($error->message);
	    if ($error->file) {
	        $return .= " in <b>$error->file</b>";
	    }
	    $return .= " on line <b>$error->line</b>\n";
	    return $return;
	}

	function libxml_display_errors()
	{
	    $errors = libxml_get_errors();
	    foreach ($errors as $error) {
	        print $this->libxml_display_error($error);
	    }
	    libxml_clear_errors();
	}


        function xml2redif()
        {
		$return = '';

                $xml = file_get_contents($this->filename);

		if ($this->validateXML($xml,$this->schemafile)){
			$articles = new SimpleXMLElement($xml);
			foreach($articles->record as $article){
				$return .= "Template-Type: ReDIF-Article 1.0\n";

				foreach ($article->authors->author as $author){
					$return .= 'Author-Name: '.$author->name."\n";
					$afid=$author->affiliationId-1;
					$return .= 'Author-Workplace-Name: '.$article->affiliationsList->affiliationName[$afid]."\n";
				}				

				$return .= "Title: ".$article->title."\n";
                                $return .= "Abstract: ".$article->abstract."\n";

                                $return .= "Keywords: ";
				$keys='';
				foreach ($article->keywords->keyword as $keyword){
					$keys.=trim($keyword).", ";
				}
				$return .= trim($keys,", ")."\n";


                                $return .= "Journal: ".$article->journalTitle."\n";
                                $return .= "Pages: ".$article->startPage.'-'.$article->endPage."\n";
                                $return .= "Volume: ".$article->volume."\n";
                                $return .= "Issue: ".$article->issue."\n";

				$date = date_parse($article->publicationDate);
				$year=$date['year'];
				$month=jdmonthname($date['month'],1);
                                $return .= "Year: ".$year."\n";
                                $return .= "Month: ".$month."\n";

                                $return .= "File-URL: ".$article->fullTextUrl."\n";
                                $return .= "File-Format: Application/pdf\n";

				$return .= "Handle: RePEc:".$this->repechandle.":v:".$article->volume.":y:".$year.":i:".$article->issue.":p:".$article->startPage.'-'.$article->endPage."\n";
				$return .= "\n";
	
			}

		}

		return $return;

	}

	function redif2xml()
	{

		$file = file_get_contents($this->filename);

		$file = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $file);

		$posts = explode('Template-Type: ReDIF-Article 1.0', $file);
		unset($posts[0]);

		//header('Content-Disposition: attachment; filename="' . $filename . '.xml"');
		header("HTTP/1.1 200 OK");
		header('X-Robots-Tag: noindex, follow', true);
		header('Content-Type: application/xml; charset=utf-8');
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml .= '<!-- generator="SSERR XML DOAJ" -->' . "\n";

		$xml .= '<records>';


		foreach ($posts as $post) {
    
		    $xml .= '<record>';
		    $xml .= '<language>eng</language>';
		    $xml .= '<publisher>' . $this->publisher . '</publisher>';
    
		    $pages = explode("-", $this->find_in_post($post, 'Pages'));
		    $month = date_parse($this->find_in_post($post, 'Month'));
    
		    if ($month['month'] < 10) {
		        $month = '0' . $month['month'];
		    } else {
		        $month = $month['month'];
		    }
    
		    $xml .= '<journalTitle>' . $this->find_in_post($post, 'Journal') . '</journalTitle>';
		    $xml .= '<issn>' . $this->issn . '</issn>';
		    $xml .= '<publicationDate>' . $this->find_in_post($post, 'Year') . '-' . $month . '-01</publicationDate>';
		    $xml .= '<volume>' . $this->find_in_post($post, 'Volume') . '</volume>';
		    $xml .= '<issue>' . $this->find_in_post($post, 'Issue') . '</issue>';
		    $xml .= '<startPage>' . $pages[0] . '</startPage>';
		    $xml .= '<endPage>' . $pages[1] . '</endPage>';
		    $xml .= '<doi>' . $this->find_in_post($post, '_citation_doi') . '</doi>';
		    $xml .= '<publisherRecordId></publisherRecordId>';
		    $xml .= '<documentType>article</documentType>';
		    $xml .= '<title language="eng">' . $this->find_in_post($post, 'Title') . '</title>';
    
    
		    $postAuthor = str_replace("\nAuthor-Workplace-Name:", "###", $post);
    
		    $author = explode("Author-Name:", $postAuthor);
		    unset($author[0]);
    
		    $final_authors = array();
		    foreach ($author as $value) {
		        $ret = explode('###', strtok($value, "\n"));
		        $final_authors[$ret[0]] = $ret[1];
		    }

		    $xml .= '<authors>';

		    $xmlau    = '';
		    $count    = 1; 
		    $xmlcount = ''; 

		    foreach ($final_authors as $k => $v) {
		        $xmlau .= '<author>';
		        $xmlau .= '<name>' . trim($k) . '</name>';
		        $xmlau .= '<email></email>';
		        $xmlau .= '<affiliationId>' . $count . '</affiliationId>';
		        $xmlau .= '</author>';
		        $xmlcount .= '<affiliationName affiliationId="' . $count . '">' . trim($v) . '</affiliationName>';
		        $count++;
		    }

		    $xml .= $xmlau . '</authors>';

		    $xml .= '<affiliationsList>' . $xmlcount . '</affiliationsList>';
		    $xml .= '<abstract language="eng">' . $this->find_in_post($post, 'Abstract') . '</abstract>';
		    $xml .= '<fullTextUrl format="html">' . htmlentities($this->find_in_post($post, 'File-URL')) . '</fullTextUrl>';

		    $xml .= '<keywords language="eng">';
		    $keywords = explode(",", $this->find_in_post($post, 'Keywords'));
		    $xmlky    = '';

		    foreach ($keywords as $keyword) {
		        $xmlky .= '<keyword>' . $keyword . '</keyword>';
		    }
		    $xml .= $xmlky . '</keywords>';
		    $xml .= "</record>\n";

		}

		$xml .= '</records>';

		return $this->validateXML($xml,$this->schemafile);

	}


	function validateXML($xml,$schemafile){

		$xmlC = new DOMDocument();
		$xmlC->loadXML($xml);

		if (!$xmlC->schemaValidate($schemafile)) {
		    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		    $this->libxml_display_errors();
		    return false;
		} else {
		    return $xml;
		}
	}

}


?>
