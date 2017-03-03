#!/usr/bin/php
<?php
$issn      = '2392-9863';
$publisher = 'Editura Sitech';
$filename   = 'jsserr.rdf';

/* DO NOT EDIT BEYOND THIS POINT. NO NEED TO DO SO */


$file = file_get_contents($filename);

$file = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $file);

$posts = explode('Template-Type: ReDIF-Article 1.0', $file);
unset($posts[0]);

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
    $xml .= '<publisher>' . $publisher . '</publisher>';
    
    $pages = explode("-", find_in_post($post, 'Pages'));
    $month = date_parse(find_in_post($post, 'Month'));
    
    if ($month['month'] < 10) {
        $month = '0' . $month['month'];
    } else {
        $month = $month['month'];
    }
    
    $xml .= '<journalTitle>' . find_in_post($post, 'Journal') . '</journalTitle>';
    $xml .= '<issn>' . $issn . '</issn>';
    $xml .= '<publicationDate>' . find_in_post($post, 'Year') . '-' . $month . '-01</publicationDate>';
    $xml .= '<volume>' . find_in_post($post, 'Volume') . '</volume>';
    $xml .= '<issue>' . find_in_post($post, 'Issue') . '</issue>';
    $xml .= '<startPage>' . $pages[0] . '</startPage>';
    $xml .= '<endPage>' . $pages[1] . '</endPage>';
    $xml .= '<doi>' . find_in_post($post, '_citation_doi') . '</doi>';
    $xml .= '<publisherRecordId></publisherRecordId>';
    $xml .= '<documentType>article</documentType>';
    $xml .= '<title language="eng">' . find_in_post($post, 'Title') . '</title>';
    
    
    $postAuthor = str_replace("\nAuthor-Workplace-Name:", "###", $post);
    
    $author = explode("Author-Name:", $postAuthor);
    unset($author[0]);
    
    $final_authors = array();
    foreach ($author as $value) {
        $ret                    = explode('###', strtok($value, "\n"));
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
    $xml .= '<abstract language="eng">' . find_in_post($post, 'Abstract') . '</abstract>';
    $xml .= '<fullTextUrl format="html">' . htmlentities(find_in_post($post, 'File-URL')) . '</fullTextUrl>';
    $xml .= '<keywords language="eng">';
    $keywords = explode(",", find_in_post($post, 'Keywords'));
    $xmlky    = '';
    foreach ($keywords as $keyword) {
        $xmlky .= '<keyword>' . $keyword . '</keyword>';
    }
    $xml .= $xmlky . '</keywords>';
    $xml .= "</record>\n";

}

$xml .= '</records>';

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
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

libxml_use_internal_errors(true);

$xmlC = new DOMDocument();
$xmlC->loadXML($xml);

if (!$xmlC->schemaValidate('doajArticles.xsd')) {
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    libxml_display_errors();
} else {
    echo $xml;
}

exit();
?>
