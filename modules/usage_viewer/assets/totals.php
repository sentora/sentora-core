<?php

/* CAT:Pie charts */

/* pChart library inclusions */
include("../../../etc/lib/pChart2/class/pData.class.php");
include("../../../etc/lib/pChart2/class/pDraw.class.php");
include("../../../etc/lib/pChart2/class/pPie.class.php");
include("../../../etc/lib/pChart2/class/pImage.class.php");
$a = $_GET['used'];

/* Create and populate the pData object */
$MyData = new pData();
$MyData->addPoints(array(56756756, 49999996), "ScoreA");
$MyData->setSerieDescription("ScoreA", "Application A");

/* Define the absissa serie */
$MyData->addPoints(array("Free Space", "Used Space"), "Labels");
$MyData->setAbscissa("Labels");

/* Create the pChart object */
$myPicture = new pImage(350, 250, $MyData, TRUE);

/* Set the default font properties */
$myPicture->setFontProperties(array("FontName" => "../../../etc/lib/pChart2/fonts/Forgotte.ttf", "FontSize" => 10, "R" => 80, "G" => 80, "B" => 80));

/* Create the pPie object */
$PieChart = new pPie($myPicture, $MyData);

/* Enable shadow computing */
$myPicture->setShadow(TRUE, array("X" => 3, "Y" => 3, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw a splitted pie chart */
$PieChart->draw3DPie(150, 120, array("Radius" => 150, "DataGapAngle" => 5, "DataGapRadius" => 7, "Border" => TRUE));

/* Write the legend box */
$myPicture->setFontProperties(array("FontName" => "../../../etc/lib/pChart2/GeosansLight.ttf", "FontSize" => 8, "R" => 0, "G" => 0, "B" => 0));
$PieChart->drawPieLegend(140, 240, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("pictures/example.draw3DPie.transparent.png");
?>