<?php

/* CAT:Progress bars */

/* pChart library inclusions */
include("../class/pDraw.class.php");
include("../class/pImage.class.php");

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
$myPicture = new pImage($ImageSize[0], $ImageSize[1]);
$myPicture->setFontProperties(array("FontName" => "../fonts/verdana.ttf", "FontSize" => 8, "R" => 255, "G" => 255, "B" => 255));

/* Draw a progress bar */
$progressOptions = array("Width" => ($ImageSize[0] - 1), "Height" => ($ImageSize[1] - 1), "R" => 121, "G" => 181, "B" => 68, "Surrounding" => 50, "BoxBorderR" => 204, "BoxBorderG" => 204, "BoxBorderB" => 204, "BoxBackR" => 200, "BoxBackG" => 200, "BoxBackB" => 200, "RFade" => 244, "GFade" => 120, "BFade" => 66, "ShowLabel" => TRUE, "LabelPos" => LABEL_POS_CENTER);
$myPicture->drawProgress(0, 0, $percent, $progressOptions);

/* Render the picture (choose the best way) */
$myPicture->autoOutput("zProgress.png");
?>