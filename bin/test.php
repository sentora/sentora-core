<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require '../dryden/xml/reader.class.php';

$xml = '<?xml version="1.0" encoding="utf-8"?><updateserver><latestversion>1001</latestversion><downloadurl>http://www.zpanelcp.com/</downloadurl></updateserver>';

$updateinfo = new xml_reader($xml);
$updateinfo->Parse();
if(isset($updateinfo->document->latestversion[0]->tagData)){
$latest_version = $updateinfo->document->latestversion[0]->tagData;
$downloadurl = $updateinfo->document->downloadurl[0]->tagData;
echo "Latest version: " .$latest_version. " - " .$downloadurl. "";
} else {
    echo "Sorry couldn't parse the file.";
}
?>
