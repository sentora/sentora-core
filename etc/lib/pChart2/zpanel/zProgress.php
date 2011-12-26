<?php   
 /* CAT:Progress bars */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 
 if (isset($_GET['percent'])){
 	$percent = $_GET['percent'];
 }else{
 	$percent = 0;
 }

 /* Create the pChart object */
 $myPicture = new pImage(120,17);
 $myPicture->setFontProperties(array("FontName"=>"../fonts/verdana.ttf", "FontSize"=>8, "R"=>255, "G"=>255, "B"=>255));

 /* Draw a progress bar */ 
 $progressOptions = array("Width"=>120, "Height"=>17, "R"=>134, "G"=>209, "B"=>27, "Surrounding"=>0, "BoxBorderR"=>255, "BoxBorderG"=>255, "BoxBorderB"=>255, "BoxBackR"=>200, "BoxBackG"=>200, "BoxBackB"=>200, "RFade"=>128, "GFade"=>0, "BFade"=>0, "ShowLabel"=>TRUE, "LabelPos"=>LABEL_POS_CENTER);
 $myPicture->drawProgress(0,0,$percent,$progressOptions);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("zProgress.png");
?>