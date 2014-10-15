<?php

/* CAT:Pie charts */
error_reporting('E_ALL');
/* pChart library inclusions */
include("../class/pData.class.php");
include("../class/pDraw.class.php");
include("../class/pPie.class.php");
include("../class/pImage.class.php");

/* Start setting input configuration.  If _GET not found set defaults */
if (isset($_GET['score'])) {
    $getscore = explode('::', $_GET['score']);
    $notnull = 0;
    foreach ($getscore as $getscorepart) {
        if ($getscorepart != 0) {
            $notnull = 1;
        }
        $Score[] = $getscorepart;
    }
    if ($notnull == 0) {
        $Score = array(0, 100);
    }
} else {
    $Score = array(0, 100);
}

if (isset($_GET['labels'])) {
    $getlabels = explode('::', $_GET['labels']);
    foreach ($getlabels as $labelspart) {
        if ($labelspart == "NULL") {
            $ShowLabels = 0;
            $Labels[] = str_replace('_', ' ', $labelspart);
        } else {
            $ShowLabels = 1;
            $Labels[] = str_replace('_', ' ', $labelspart);
        }
    }
} else {
    $Labels = array("", "");
}

if (isset($_GET['imagesize'])) {
    $getimagesize = explode('::', $_GET['imagesize']);
    foreach ($getimagesize as $getimagesizepart) {
        $imagesizearray[] = $getimagesizepart;
    }
    $ImageSize = array($imagesizearray[0], $imagesizearray[1]);
} else {
    $ImageSize = array(240, 180);
}

if (isset($_GET['chartsize'])) {
    $getchartsize = explode('::', $_GET['chartsize']);
    foreach ($getchartsize as $getchartsizepart) {
        $getchartsizearray[] = $getchartsizepart;
    }
    $ChartSize = array($getchartsizearray[0], $getchartsizearray[1]);
} else {
    $ChartSize = array(120, 90);
}

if (isset($_GET['radius'])) {
    $Radius = $_GET['radius'];
} else {
    $Radius = 100;
}

/* Set palette color */
if (isset($_GET['palette'])) {
    switch ($_GET['palette']) {
        case 'autumn':
            $palette = 'autumn.color';
            break;
        case 'blind':
            $palette = 'blind.color';
            break;
        case 'evening':
            $palette = 'evening.color';
            break;
        case 'kitchen':
            $palette = 'kitchen.color';
            break;
        case 'light':
            $palette = 'light.color';
            break;
        case 'navy':
            $palette = 'navy.color';
            break;
        case 'shade':
            $palette = 'shade.color';
            break;
        case 'spring':
            $palette = 'spring.color';
            break;
        case 'summer':
            $palette = 'summer.color';
            break;
        default:
            $palette = 'navy.color';
            break;
    }
} else {
    $palette = 'sentora.color';
}


if (isset($_GET['dataangle'])) {
    $DataGapAngle = $_GET['datagapangle'];
} else {
    $DataGapAngle = 12;
}

if (isset($_GET['datagapradius'])) {
    $DataGapRadius = $_GET['datagapradius'];
} else {
    $DataGapRadius = 10;
}

if (isset($_GET['fontsize'])) {
    $FontSize = $_GET['fontsize'];
} else {
    $FontSize = 10;
}

if (isset($_GET['font'])) {
    $Font = $_GET['font'];
} else {
    $Font = "Forgotte";
}

if (isset($_GET['fontr'])) {
    $FontR = $_GET['fontr'];
} else {
    $FontR = 80;
}

if (isset($_GET['fontg'])) {
    $FontG = $_GET['fontg'];
} else {
    $FontG = 80;
}

if (isset($_GET['fontb'])) {
    $FontB = $_GET['fontb'];
} else {
    $FontB = 80;
}

if (isset($_GET['legendsize'])) {
    $getlegendsize = explode('::', $_GET['legendsize']);
    foreach ($getlegendsize as $getlegendsizepart) {
        $getlegendsizearray[] = $getlegendsizepart;
    }
    $LegendSize = array($getlegendsizearray[0], $getlegendsizearray[1]);
} else {
    $LegendSize = array(25, 160);
}

if (isset($_GET['legendfont'])) {
    $LegendFont = $_GET['legendfont'];
} else {
    $LegendFont = "Silkscreen";
}

if (isset($_GET['legendfontr'])) {
    $LegendFontR = $_GET['legendfontr'];
} else {
    $LegendFontR = 0;
}

if (isset($_GET['legendfontg'])) {
    $LegendFontG = $_GET['legendfontg'];
} else {
    $LegendFontG = 0;
}

if (isset($_GET['legendfontb'])) {
    $LegendFontB = $_GET['legendfontb'];
} else {
    $LegendFontB = 0;
}

if (isset($_GET['legendfontsize'])) {
    $LegendFontSize = $_GET['legendfontsize'];
} else {
    $LegendFontSize = 6;
}

if (isset($_GET['legendstyle'])) {
    $LegendStyle = $_GET['legendstyle'];
} else {
    $LegendStyle = "LEGEND_NOBORDER";
}

if (isset($_GET['LegendMode'])) {
    $LegendMode = $_GET['LegendMode'];
} else {
    $LegendMode = "LEGEND_HORIZONTAL";
}

/* Create and populate the pData object */
$MyData = new pData();
$MyData->addPoints($Score, "ScoreA");
$MyData->setSerieDescription("ScoreA", "Application A");

/* Define the absissa serie */
$MyData->addPoints($Labels, "Labels");
$MyData->setAbscissa("Labels");
$MyData->loadPalette('../palettes/sentora.color', TRUE);

/* Create the pChart object */
$myPicture = new pImage($ImageSize[0], $ImageSize[1], $MyData, TRUE);

/* Set the default font properties */
$myPicture->setFontProperties(array("FontName" => "../fonts/" . $Font . ".ttf", "FontSize" => $FontSize, "R" => $FontR, "G" => $FontG, "B" => $FontB));

/* Create the pPie object */
$PieChart = new pPie($myPicture, $MyData);

/* Enable shadow computing */
$myPicture->setShadow(TRUE, array("X" => 3, "Y" => 3, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw a splitted pie chart */
$PieChart->draw3DPie($ChartSize[0], $ChartSize[1], array("Radius" => $Radius, "DataGapAngle" => $DataGapAngle, "DataGapRadius" => $DataGapRadius, "Border" => TRUE));

/* Write the legend box */
if ($ShowLabels <> 0) {
    $myPicture->setFontProperties(array("FontName" => "../fonts/" . $LegendFont . ".ttf", "FontSize" => $LegendFontSize, "R" => $LegendFontR, "G" => $LegendFontG, "B" => $LegendFontB));
    //$PieChart->drawPieLegend(140,160,array("Style"=>$LegendStyle,"Mode"=>$LegendMode));
    $PieChart->drawPieLegend($LegendSize[0], $LegendSize[1], array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL));
}

/*
  $TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE, "R"=>0, "G"=>0, "B"=>0);
  $myPicture->drawText(10,10,"My first pChart project",$TextSettings);
  $myPicture->drawText(10,30,"My first pChart project",$TextSettings);
 */

/* Render the picture (choose the best way) */
$myPicture->autoOutput("3DPie.png");
?>