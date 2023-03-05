<?php   
/* CAT:Progress bars */

/* pChart library inclusions */
require_once("bootstrap.php");

use pChart\pColor;
use pChart\pDraw;


if (isset($_GET['percent'])) {
    $percent = $_GET['percent'];
} else {
    $percent = 0;
}

if (isset($_GET['size'])) {
    $getimagesize = explode('::', $_GET['size']);
    foreach ($getimagesize as $getimagesizepart) {
        $imagesizearray[] = $getimagesizepart;
    }
    $ImageSize = array($imagesizearray[0], $imagesizearray[1]);
} else {
    $ImageSize = array(115, 17);
}

/* Create the pChart object */
$myPicture = new pDraw($ImageSize[0],$ImageSize[1]);

/* Set the font & shadow options */ 
$myPicture->setFontProperties(["FontName"=>"pChart/fonts/Cairo-Regular.ttf", "FontSize"=>10]);
$myPicture->setShadow(TRUE,["X"=>1, "Y"=>1, "Color"=>new pColor(0,0,0,20)]);

/* Draw a progress bar */ 
//$progressOptions = ["Color"=>new pColor(134,209,27), "Surrounding"=>20, "BoxBorderColor"=>new pColor(0), "BoxBackColor"=>new pColor(255), "FadeColor"=>new pColor(206,133,30), "ShowLabel"=>TRUE];
$progressOptions = array("Width" => ($ImageSize[0] - 1), "Height" => ($ImageSize[1] - 1), "R" => 121, "G" => 181, "B" => 68, "Surrounding" => 50, "BoxBorderR" => 204, "BoxBorderG" => 204, "BoxBorderB" => 204, "BoxBackR" => 200, "BoxBackG" => 200, "BoxBackB" => 200, "RFade" => 244, "GFade" => 120, "BFade" => 66, "ShowLabel" => TRUE, "LabelPos" => LABEL_POS_CENTER);
$myPicture->drawProgress(0,0,$percent,$progressOptions);

/* Render the picture (choose the best way) */
$myPicture->autoOutput("temp/example.drawProgress.png");

?>