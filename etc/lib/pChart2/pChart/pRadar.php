<?php
/*
pRadar - class to draw radar charts

Version     : 2.3.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/02/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("SEGMENT_HEIGHT_AUTO", 690001);
define("RADAR_LAYOUT_STAR", 690011);
define("RADAR_LAYOUT_CIRCLE", 690012);
define("RADAR_LABELS_ROTATED", 690021);
define("RADAR_LABELS_HORIZONTAL", 690022);

/* pRadar class definition */
class pRadar
{
	var $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Draw a radar chart */
	function drawRadar(array $Format = [])
	{
		$FixedMax = VOID;
		$AxisColor = new pColor(60,60,60,50);
		$AxisRotation = 0;
		$DrawTicks =  TRUE;
		$TicksLength = 2;
		$DrawAxisValues = TRUE;
		$AxisBoxRounded = TRUE;
		$AxisFontName = $this->myPicture->FontName;
		$AxisFontSize = $this->myPicture->FontSize;
		$WriteValues = FALSE;
		$WriteValuesInBubble = TRUE;
		$ValueFontName = $this->myPicture->FontName;
		$ValueFontSize = $this->myPicture->FontSize;
		$ValuePadding = 4;
		$OuterBubbleRadius = 2;
		$OuterBubbleColor = NULL;
		$InnerBubbleColor = new pColor(255);
		$DrawBackground = TRUE;
		$BackgroundColor = new pColor(255,255,255,50);
		$BackgroundGradient = NULL;
		$Layout = RADAR_LAYOUT_STAR;
		$SegmentHeight = SEGMENT_HEIGHT_AUTO;
		$Segments = 4;
		$WriteLabels = TRUE;
		$SkipLabels = 1;
		$LabelMiddle = FALSE;
		$LabelsBackground = TRUE;
		$LabelsBackgroundColor = new pColor(255,255,255,50);
		$LabelPos = RADAR_LABELS_ROTATED;
		$LabelPadding = 4;
		$DrawPoints = TRUE;
		$PointRadius = 4;
		$PointSurrounding = isset($Format["PointRadius"]) ? $Format["PointRadius"] : -30;
		$DrawLines = TRUE;
		$LineLoopStart = TRUE;
		$DrawPoly = FALSE;
		$PolyAlpha = 40;
		$FontSize = $this->myPicture->FontSize;
		$X1 = $this->myPicture->GraphAreaX1;
		$Y1 = $this->myPicture->GraphAreaY1;
		$X2 = $this->myPicture->GraphAreaX2;
		$Y2 = $this->myPicture->GraphAreaY2;
		$RecordImageMap = FALSE;

		/* Override defaults */
		extract($Format);

		/* Cancel default tick length if ticks not enabled */
		($DrawTicks == FALSE) AND $TicksLength = 0;

		/* Data Processing */
		$Data = $this->myPicture->myData->getData();
		$Palette = $this->myPicture->myData->getPalette();
		/* Catch the number of required axis */
		$LabelSerie = $Data["Abscissa"];
		if ($LabelSerie != "") {
			$Points = count($Data["Series"][$LabelSerie]["Data"]);
		} else {
			$Points = 0;
			foreach($Data["Series"] as $SerieName => $DataArray) {
				if (count($DataArray["Data"]) > $Points) {
					$Points = count($DataArray["Data"]);
				}
			}
		}

		$Step = 360 / $Points;

		/* Draw the axis */
		$CenterX = ($X2 + $X1) / 2;
		$CenterY = ($Y2 + $Y1) / 2;
		$EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
		if ($WriteLabels) {
			$EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
		}

		/* Determine the scale if set to automatic */
		if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
			if ($FixedMax != VOID) {
				$Max = $FixedMax;
			} else {
				$Max = 0;
				foreach($Data["Series"] as $SerieName => $DataArray) {
					if ($SerieName != $LabelSerie) {
						if (max($DataArray["Data"]) > $Max) {
							$Max = max($DataArray["Data"]);
						}
					}
				}
			}

			$MaxSegments = $EdgeHeight / 20;
			$Scale = $this->myPicture->computeScale(0, $Max, $MaxSegments, [1,2,5]);
			$Segments = $Scale["Rows"];
			$SegmentHeight = $Scale["RowHeight"];
		}

		$Axisoffset = ($LabelMiddle) ? ($Step * $SkipLabels) / 2 : 0;

		/* Background processing */
		if ($DrawBackground) {
			$RestoreShadow = $this->myPicture->Shadow;
			$this->myPicture->Shadow = FALSE;
			if (!is_array($BackgroundGradient)) {
				if ($Layout == RADAR_LAYOUT_STAR) {

					$PointArray = [];
					for ($i = 0; $i <= 360; $i += $Step) {
						$PointArray[] = cos(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterX;
						$PointArray[] = sin(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterY;
					}

					$this->myPicture->drawPolygon($PointArray, ["Color" => $BackgroundColor]);

				} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {;
					$this->myPicture->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, ["Color" => $BackgroundColor]);
				}
			} else {

				$GradientColor = new pColorGradient($BackgroundGradient["StartColor"], $BackgroundGradient["EndColor"], TRUE);
				$GradientColor->SetSegments($Segments);

				if ($Layout == RADAR_LAYOUT_STAR) {
					for ($j = $Segments; $j >= 1; $j--) {
						$PointArray = [];
						for ($i = 0; $i <= 360; $i += $Step) {
							$PointArray[] = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
							$PointArray[] = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
						}
						$this->myPicture->drawPolygon($PointArray, ["Color" => $GradientColor->Next($j, TRUE)]);
					}
				} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
					for ($j = $Segments; $j >= 1; $j--) {
						$this->myPicture->drawFilledCircle($CenterX, $CenterY, ($EdgeHeight / $Segments) * $j, ["Color" => $GradientColor->Next($j, TRUE)]);
					}
				}
			}

			$this->myPicture->Shadow = $RestoreShadow;
		}

		/* Axis to axis lines */
		$Color = ["Color" => $AxisColor];
		$ColorDotted = ["Color" => $AxisColor->newOne()->AlphaMultiply(.8),"Ticks" => 2];

		if ($Layout == RADAR_LAYOUT_STAR) {
			for ($j = 1; $j <= $Segments; $j++) {
				for ($i = 0; $i < 360; $i += $Step) {
					$EdgeX1 = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX2 = cos(deg2rad($i + $AxisRotation + $Step)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY2 = sin(deg2rad($i + $AxisRotation + $Step)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$this->myPicture->drawLine($EdgeX1, $EdgeY1, $EdgeX2, $EdgeY2, $Color);
				}
			}
		} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
			for ($j = 1; $j <= $Segments; $j++) {
				$Radius = ($EdgeHeight / $Segments) * $j;
				$this->myPicture->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
			}
		}

		if ($DrawAxisValues) {
			if ($LabelsBackground) {
				$Options = ["DrawBox" => TRUE,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"BoxColor" => $LabelsBackgroundColor];
			} else {
				$Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
			}

			if ($AxisBoxRounded) {
				$Options["BoxRounded"] = TRUE;
			}

			$Options["FontName"] = $AxisFontName;
			$Options["FontSize"] = $AxisFontSize;
			$Angle = $Step / 2;

			for ($j = 1; $j <= $Segments; $j++) {
				$Label = $j * $SegmentHeight;
				if ($Layout == RADAR_LAYOUT_CIRCLE) {
					$EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
				} elseif ($Layout == RADAR_LAYOUT_STAR) {
					$EdgeX1 = cos(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX2 = cos(deg2rad($Step + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY2 = sin(deg2rad($Step + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX1 = ($EdgeX2 + $EdgeX1) / 2;
					$EdgeY1 = ($EdgeY2 + $EdgeY1) / 2;
				}

				$this->myPicture->drawText($EdgeX1, $EdgeY1, $Label, $Options);
			}
		}

		/* Axis lines */
		$ID = 0;
		for ($i = 0; $i < 360; $i += $Step) {
			$EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
			$EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
			if ($ID % $SkipLabels == 0) {
				$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
			} else {
				$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $ColorDotted);
			}

			if ($WriteLabels) {
				$LabelX = cos(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
				$LabelY = sin(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
				if ($LabelSerie != "") {
					$Label = isset($Data["Series"][$LabelSerie]["Data"][$ID]) ? $Data["Series"][$LabelSerie]["Data"][$ID] : "";
				} else {
					$Label = $ID;
				}

				if ($ID % $SkipLabels == 0) {
					if ($LabelPos == RADAR_LABELS_ROTATED) {
						$Align = ["Angle" => (360 - ($i + $AxisRotation + $Axisoffset)) - 90,"Align" => TEXT_ALIGN_BOTTOMMIDDLE];
					} else { # RADAR_LABELS_HORIZONTAL
						switch (TRUE) {
							case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMMIDDLE];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMLEFT];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_MIDDLELEFT];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPLEFT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMRIGHT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_MIDDLERIGHT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPRIGHT];
								break;
							case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPMIDDLE];
								break;
						}
					}

					$this->myPicture->drawText($LabelX, $LabelY, $Label, $Align);
				}
			}

			$ID++;
		}

		/* Compute the plots position */
		$ID = 0;
		$Plot = [];
		foreach($Data["Series"] as $SerieName => $DataS) {
			if ($SerieName != $LabelSerie) {

				foreach($DataS["Data"] as $Key => $Value) {
					$Angle = $Step * $Key;
					$Length = ($EdgeHeight / ($Segments * $SegmentHeight)) * $Value;
					$X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
					$Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
					$Plot[$ID][] = [$X,$Y,$Value];
					if ($RecordImageMap) {
						$this->myPicture->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . floor($PointRadius), $Palette[$ID]->toHex(), $DataS["Description"], $Data["Series"][$LabelSerie]["Data"][$Key] . " = " . $Value);
					}
				}

				$ID++;
			}
		}

		/* Draw all that stuff! */
		foreach($Plot as $ID => $Points) {

			$Color = ["Color" => $Palette[$ID],"Surrounding" => $PointSurrounding];
			$PointCount = count($Points);

			/* Draw the polygons */
			if ($DrawPoly) {
				if (!is_null($PolyAlpha)) {
					$Color = ["Color" => $Palette[$ID]->newOne()->AlphaSet($PolyAlpha),"Surrounding" => $PointSurrounding];
				}

				$PointsArray = [];
				for ($i = 0; $i < $PointCount; $i++) {
					$PointsArray[] = $Points[$i][0];
					$PointsArray[] = $Points[$i][1];
				}

				$this->myPicture->drawPolygon($PointsArray, $Color);
			}

			/* Bubble and labels settings */
			$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontName" => $ValueFontName,"FontSize" => $ValueFontSize,"Color" => $Palette[$ID]);

			$Color = ["Color" => $Palette[$ID],"Surrounding" => $PointSurrounding];
			$InnerColor = ["Color" => $InnerBubbleColor];
			$OuterColor = ["Color" => (!is_null($OuterBubbleColor)) ? $OuterBubbleColor : $Palette[$ID]->newOne()->RGBChange(20)];

			/* Loop to the starting points if asked */
			if ($LineLoopStart && $DrawLines) $this->myPicture->drawLine($Points[$PointCount - 1][0], $Points[$PointCount - 1][1], $Points[0][0], $Points[0][1], $Color);
			/* Draw the lines & points */
			for ($i = 0; $i < $PointCount; $i++) {
				if ($DrawLines && $i < $PointCount - 1) {
					$this->myPicture->drawLine($Points[$i][0], $Points[$i][1], $Points[$i + 1][0], $Points[$i + 1][1], $Color);
				}

				if ($DrawPoints) {
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $PointRadius, $Color);
				}

				if ($WriteValuesInBubble && $WriteValues) {
					$TxtPos = $this->myPicture->getTextBox($Points[$i][0], $Points[$i][1], $ValueFontName, $ValueFontSize, 0, $Points[$i][2]);
					$Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $ValuePadding * 2) / 2);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius + $OuterBubbleRadius, $OuterColor);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius, $InnerColor);
				}

				if ($WriteValues) {
					#Momchil: Visual fix applied
					$this->myPicture->drawText($Points[$i][0], $Points[$i][1], $Points[$i][2], $TextSettings);
				}
			}
		}
	}

	/* Draw a radar chart */
	function drawPolar(array $Format = [])
	{
		$FixedMax = VOID;
		$AxisColor = new pColor(60,60,60,50);
		$AxisRotation = -90;
		$DrawTicks = TRUE;
		$TicksLength = 2;
		$DrawAxisValues = TRUE;
		$AxisBoxRounded = TRUE;
		$AxisFontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->myPicture->FontName;
		$AxisFontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->myPicture->FontSize;
		$WriteValues = FALSE;
		$WriteValuesInBubble = TRUE;
		$ValueFontName = $this->myPicture->FontName;
		$ValueFontSize = $this->myPicture->FontSize;
		$ValuePadding = 4;
		$OuterBubbleRadius = 2;
		$OuterBubbleColor = NULL;
		$InnerBubbleColor = new pColor(255);
		$DrawBackground = TRUE;
		$BackgroundColor = new pColor(255,255,255,50);
		$BackgroundGradient = NULL;
		$AxisSteps = 20;
		$SegmentHeight = SEGMENT_HEIGHT_AUTO;
		$Segments = 4;
		$WriteLabels = TRUE;
		$LabelsBackground = TRUE;
		$LabelsBackgroundColor = new pColor(255,255,255,50);
		$LabelPos = RADAR_LABELS_ROTATED;
		$LabelPadding = 4;
		$DrawPoints = TRUE;
		$PointRadius = 4;
		$PointSurrounding = isset($Format["PointRadius"]) ? $Format["PointRadius"] : -30;
		$DrawLines = TRUE;
		$LineLoopStart = FALSE;
		$DrawPoly = FALSE;
		$PolyAlpha = NULL;
		$FontSize = $this->myPicture->FontSize;
		$X1 = $this->myPicture->GraphAreaX1;
		$Y1 = $this->myPicture->GraphAreaY1;
		$X2 = $this->myPicture->GraphAreaX2;
		$Y2 = $this->myPicture->GraphAreaY2;
		$RecordImageMap = FALSE;

		/* Override defaults */
		extract($Format);

		($AxisBoxRounded) AND $DrawAxisValues = TRUE;

		/* Cancel default tick length if ticks not enabled */
		($DrawTicks == FALSE) AND $TicksLength = 0;

		/* Data Processing */
		$Data = $this->myPicture->myData->getData();
		$Palette = $this->myPicture->myData->getPalette();
		/* Catch the number of required axis */
		$LabelSerie = $Data["Abscissa"];
		if ($LabelSerie != "") {
			$Points = count($Data["Series"][$LabelSerie]["Data"]);
		} else {
			$Points = 0;
			foreach($Data["Series"] as $SerieName => $DataArray) {
				(count($DataArray["Data"]) > $Points) AND $Points = count($DataArray["Data"]);
			}
		}

		/* Draw the axis */
		$CenterX = ($X2 + $X1) / 2;
		$CenterY = ($Y2 + $Y1) / 2;
		$EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
		if ($WriteLabels) {
			$EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
		}

		/* Determine the scale if set to automatic */
		if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
			if ($FixedMax != VOID) {
				$Max = $FixedMax;
			} else {
				$Max = 0;
				foreach($Data["Series"] as $SerieName => $DataArray) {
					if ($SerieName != $LabelSerie) {
						if (max($DataArray["Data"]) > $Max) {
							$Max = max($DataArray["Data"]);
						}
					}
				}
			}

			$MaxSegments = $EdgeHeight / 20;
			$Scale = $this->myPicture->computeScale(0, $Max, $MaxSegments, [1,2,5]);
			$Segments = $Scale["Rows"];
			$SegmentHeight = $Scale["RowHeight"];
		}

		/* Background processing */
		if ($DrawBackground) {
			$RestoreShadow = $this->myPicture->Shadow;
			$this->myPicture->Shadow = FALSE;
			if (!is_array($BackgroundGradient)) {
				$this->myPicture->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, ["Color" => $BackgroundColor]);
			} else {
				$GradientColor = new pColorGradient($BackgroundGradient["StartColor"], $BackgroundGradient["EndColor"], TRUE);
				$GradientColor->SetSegments($Segments);
				for ($j = $Segments; $j >= 1; $j--) {
					$this->myPicture->drawFilledCircle($CenterX, $CenterY, ($EdgeHeight / $Segments) * $j, ["Color" => $GradientColor->Next($j,TRUE)]);
				}
			}

			$this->myPicture->Shadow = $RestoreShadow;
		}

		/* Axis to axis lines */
		$Color = ["Color" => $AxisColor];
		for ($j = 1; $j <= $Segments; $j++) {
			$Radius = ($EdgeHeight / $Segments) * $j;
			$this->myPicture->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
		}

		if ($DrawAxisValues) {
			if ($LabelsBackground) {
				$Options = ["DrawBox" => TRUE,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"BoxColor" => $LabelsBackgroundColor];
			} else {
				$Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
			}

			($AxisBoxRounded) AND $Options["BoxRounded"] = TRUE;

			$Options["FontName"] = $AxisFontName;
			$Options["FontSize"] = $AxisFontSize;
			$Angle = 360 / ($Points * 2);
			for ($j = 1; $j <= $Segments; $j++) {
				$EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
				$EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
				$Label = $j * $SegmentHeight;
				$this->myPicture->drawText($EdgeX1, $EdgeY1, $Label, $Options);
			}
		}

		/* Axis lines */
		$ID = 0;
		for ($i = 0; $i <= 359; $i = $i + $AxisSteps) {
			$EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
			$EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
			$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
			if ($WriteLabels) {
				$LabelX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
				$LabelY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
				$Label = $i . "°";

				if ($LabelPos == RADAR_LABELS_ROTATED) {
					$Align = ["Angle" => (360 - $i),"Align" => TEXT_ALIGN_BOTTOMMIDDLE];
				} else { # RADAR_LABELS_HORIZONTAL
					switch (TRUE) {
						case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMMIDDLE];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMLEFT];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_MIDDLELEFT];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPLEFT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMRIGHT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_MIDDLERIGHT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPRIGHT];
							break;
						case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPMIDDLE];
							break;
					}
				}

				$this->myPicture->drawText($LabelX, $LabelY, $Label, $Align);
			}

			$ID++;
		}

		/* Compute the plots position */
		$ID = 0;
		$Plot = [];
		foreach($Data["Series"] as $SerieName => $DataSet) {
			if ($SerieName != $LabelSerie) {

				foreach($DataSet["Data"] as $Key => $Value) {
					$Angle = $Data["Series"][$LabelSerie]["Data"][$Key];
					$Length = ($EdgeHeight / ($Segments * $SegmentHeight)) * $Value;
					$X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
					$Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
					if ($RecordImageMap) {
						$this->myPicture->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . floor($PointRadius), $Palette[$ID]->toHex(), $DataSet["Description"], $Data["Series"][$LabelSerie]["Data"][$Key] . "&deg = " . $Value);
					}

					$Plot[$ID][] = [$X,$Y,$Value];
				}

				$ID++;
			}
		}

		/* Draw all that stuff! */
		foreach($Plot as $ID => $Points) {

			$Color = ["Color" => $Palette[$ID],"Surrounding" => $PointSurrounding];
			$PointCount = count($Points);

			/* Draw the polygons */
			if ($DrawPoly) {
				if (!is_null($PolyAlpha)) {
					$Color = ["Color" => $Palette[$ID]->newOne()->AlphaSet($PolyAlpha),"Surrounding" => $PointSurrounding];
				}

				$PointsArray = [];
				for ($i = 0; $i < $PointCount; $i++) {
					$PointsArray[] = $Points[$i][0];
					$PointsArray[] = $Points[$i][1];
				}

				$this->myPicture->drawPolygon($PointsArray, $Color);
			}

			/* Bubble and labels settings */
			$Color = ["Color" => $Palette[$ID],"Surrounding" => $PointSurrounding];
			$TextSettings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontName" => $ValueFontName,"FontSize" => $ValueFontSize,"Color" => $Palette[$ID]];
			$InnerColor = ["Color" => $InnerBubbleColor];
			$OuterColor = ["Color" => (!is_null($OuterBubbleColor)) ? $OuterBubbleColor : $Palette[$ID]->newOne()->RGBChange(20)];

			/* Loop to the starting points if asked */
			if ($LineLoopStart && $DrawLines) {
				$this->myPicture->drawLine($Points[$PointCount - 1][0], $Points[$PointCount - 1][1], $Points[0][0], $Points[0][1], $Color);
			}

			/* Draw the lines & points */
			for ($i = 0; $i < $PointCount; $i++) {
				if ($DrawLines && $i < $PointCount - 1) {
					$this->myPicture->drawLine($Points[$i][0], $Points[$i][1], $Points[$i + 1][0], $Points[$i + 1][1], $Color);
				}

				if ($DrawPoints) {
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $PointRadius, $Color);
				}

				if ($WriteValuesInBubble && $WriteValues) {
					$TxtPos = $this->myPicture->getTextBox($Points[$i][0], $Points[$i][1], $ValueFontName, $ValueFontSize, 0, $Points[$i][2]);
					$Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $ValuePadding * 2) / 2);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius + $OuterBubbleRadius, $OuterColor);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius, $InnerColor);
				}

				if ($WriteValues) {
					#Momchil: Visual fix applied
					$this->myPicture->drawText($Points[$i][0] + 1, $Points[$i][1], $Points[$i][2], $TextSettings);
				}
			}
		}
	}
}

?>