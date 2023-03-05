<?php
/*
pScatter - class to draw scatter charts

Version     : 2.3.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/02/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* pScatter class definition */
class pScatter
{
	var $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Prepare the scale */
	function drawScatterScale(array $Format = [])
	{
		/* Check if we have at least both one X and Y axis */
		$GotXAxis = FALSE;
		$GotYAxis = FALSE;

		$Data = $this->myPicture->myData->getData();

		foreach($Data["Axis"] as $AxisSettings) {
			($AxisSettings["Identity"] == AXIS_X) AND $GotXAxis = TRUE;
			($AxisSettings["Identity"] == AXIS_Y) AND $GotYAxis = TRUE;
		}

		if (!$GotXAxis) {
			throw pException::ScatterInvalidInputException("Missing XAxis!");
		}

		if (!$GotYAxis) {
			throw pException::ScatterInvalidInputException("Missing YAxis!");
		}

		$Mode = SCALE_MODE_FLOATING;
		$Floating = FALSE;
		$XLabelsRotation = 90;
		$MinDivHeight = 20;
		$Factors = [1,2,5];
		$ManualScale = array("0" => ["Min" => - 100,"Max" => 100]);
		$XMargin = 0;
		$YMargin = 0;
		$ScaleSpacing = 15;
		$InnerTickWidth = 2;
		$OuterTickWidth = 2;
		$DrawXLines = ALL;
		$DrawYLines = ALL;
		$GridTicks = 4;
		$GridColor = new pColor(255,255,255,40);
		$AxisoColor = isset($Format["AxisColor"]) ? $Format["AxisColor"] : new pColor(0);
		$TickoColor = isset($Format["TickColor"]) ? $Format["TickColor"] : new pColor(0);
		$DrawSubTicks = FALSE;
		$InnerSubTickWidth = 0;
		$OuterSubTickWidth = 2;
		$SubTickColor = new pColor(255,0,0,100);
		$XReleasePercent = 1;
		$DrawArrows = FALSE;
		$ArrowSize = 8;
		$CycleBackground = FALSE;
		$BackgroundColor1 = new pColor(255,255,255,10);
		$BackgroundColor2 = new pColor(230,230,230,10);

		/* Override defaults */
		extract($Format);

		/* Skip a NOTICE event in case of an empty array */
		($DrawYLines == NONE) AND $DrawYLines = ["zarma" => "31"];

		foreach($Data["Axis"] as $AxisID => $AxisSettings) {
			if ($AxisSettings["Identity"] == AXIS_X) {
				$Width = $this->myPicture->GraphAreaXdiff - $XMargin * 2;
			} else {
				$Width = $this->myPicture->GraphAreaYdiff - $YMargin * 2;
			}

			$AxisMin = PHP_INT_MAX;
			$AxisMax = OUT_OF_SIGHT;
			if ($Mode == SCALE_MODE_FLOATING) {
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"]) {
						$AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
						$AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
					}
				}

				$AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
				$Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
				$Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
			} elseif ($Mode == SCALE_MODE_MANUAL) {
				if (isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"])) {
					$Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
					$Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
				} else {
					throw pException::ScatterInvalidInputException("Manual scale boundaries not set");
				}
			}

			/* Full manual scale */
			if (isset($ManualScale[$AxisID]["Rows"]) && isset($ManualScale[$AxisID]["RowHeight"])) {
				$Scale = ["Rows" => $ManualScale[$AxisID]["Rows"],"RowHeight" => $ManualScale[$AxisID]["RowHeight"],"XMin" => $ManualScale[$AxisID]["Min"],"XMax" => $ManualScale[$AxisID]["Max"]];
			} else {
				$MaxDivs = floor($Width / $MinDivHeight);
				$Scale = $this->myPicture->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
			}

			$Data["Axis"][$AxisID]["Margin"] = $AxisSettings["Identity"] == AXIS_X ? $XMargin : $YMargin;
			$Data["Axis"][$AxisID]["ScaleMin"] = $Scale["XMin"];
			$Data["Axis"][$AxisID]["ScaleMax"] = $Scale["XMax"];
			$Data["Axis"][$AxisID]["Rows"] = $Scale["Rows"];
			$Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];
			(isset($Scale["Format"])) AND $Data["Axis"][$AxisID]["Format"] = $Scale["Format"];
			(!isset($Data["Axis"][$AxisID]["Display"])) AND $Data["Axis"][$AxisID]["Display"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Format"])) AND $Data["Axis"][$AxisID]["Format"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Unit"])) AND $Data["Axis"][$AxisID]["Unit"] = NULL;
		}

		/* Set the original boundaries */
		$AxisPos = [
			"L" => $this->myPicture->GraphAreaX1,
			"R" => $this->myPicture->GraphAreaX2,
			"T" => $this->myPicture->GraphAreaY1,
			"B" => $this->myPicture->GraphAreaY2
		];
		
		foreach($Data["Axis"] as $AxisID => $AxisSettings) {
			if (isset($AxisSettings["Color"])) {
				$AxisColor = $AxisSettings["Color"];
				$TickColor = $AxisSettings["Color"];
				$this->myPicture->setFontProperties(["Color" => $AxisColor]);
			} else {
				$AxisColor = $AxisoColor;
				$TickColor = $TickoColor;
				/* Get the default font color */
				$this->myPicture->setFontProperties(["Color" => $this->myPicture->FontColor]);
			}

			$LastValue = "w00t";
			$ID = 1;
			if ($AxisSettings["Identity"] == AXIS_X) {
				if ($AxisSettings["Position"] == AXIS_POSITION_BOTTOM) {
					if ($XLabelsRotation == 0) {
						$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
						$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation == 180) {
						$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
						$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
						$LabelOffset = 2;
					}

					if ($Floating) {
						$FloatingOffset = $YMargin;
						$this->myPicture->drawLine($this->myPicture->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["B"], $this->myPicture->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($this->myPicture->GraphAreaX1, $AxisPos["B"], $this->myPicture->GraphAreaX2, $AxisPos["B"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($this->myPicture->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], $this->myPicture->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["B"], ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Width = $this->myPicture->GraphAreaXdiff - $AxisSettings["Margin"] * 2;
					$Step = $Width / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxBottom = $AxisPos["B"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$XPos = $this->myPicture->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
						$YPos = $AxisPos["B"];
						$Value = $this->myPicture->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);

						if (!is_null($LastX) && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
							$this->myPicture->drawFilledRectangle($LastX, $this->myPicture->GraphAreaY1 + $FloatingOffset, $XPos, $this->myPicture->GraphAreaY2 - $FloatingOffset, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
							$this->myPicture->drawLine($XPos, $this->myPicture->GraphAreaY1 + $FloatingOffset, $XPos, $this->myPicture->GraphAreaY2 - $FloatingOffset, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]){
							$this->myPicture->drawLine($XPos + $SubTicksSize, $YPos - $InnerSubTickWidth, $XPos + $SubTicksSize, $YPos + $OuterSubTickWidth, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos, $YPos + $OuterTickWidth + $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBottom = $YPos + 2 + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MaxBottom = max($MaxBottom, $TxtBottom);
						$LastX = $XPos;
					}

					if (isset($AxisSettings["Name"])) {
						$YPos = $MaxBottom + 2;
						$XPos = $this->myPicture->GraphAreaX1 + ($this->myPicture->GraphAreaXdiff) / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
						$MaxBottom = $Bounds[0]["Y"];
					}

					$AxisPos["B"] = $MaxBottom + $ScaleSpacing;

				} elseif ($AxisSettings["Position"] == AXIS_POSITION_TOP) {

					if ($XLabelsRotation == 0) {
						$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
						$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation == 180) {
						$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
						$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
						$LabelOffset = 5;
					}

					if ($Floating) {
						$FloatingOffset = $YMargin;
						$this->myPicture->drawLine($this->myPicture->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["T"], $this->myPicture->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($this->myPicture->GraphAreaX1, $AxisPos["T"], $this->myPicture->GraphAreaX2, $AxisPos["T"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($this->myPicture->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], $this->myPicture->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["T"], ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Width = $this->myPicture->GraphAreaXdiff - $AxisSettings["Margin"] * 2;
					$Step = $Width / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MinTop = $AxisPos["T"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$XPos = $this->myPicture->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
						$YPos = $AxisPos["T"];
						$Value = $this->myPicture->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);

						if (!is_null($LastX) && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
							$this->myPicture->drawFilledRectangle($LastX, $this->myPicture->GraphAreaY1 + $FloatingOffset, $XPos, $this->myPicture->GraphAreaY2 - $FloatingOffset, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
							$this->myPicture->drawLine($XPos, $this->myPicture->GraphAreaY1 + $FloatingOffset, $XPos, $this->myPicture->GraphAreaY2 - $FloatingOffset, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							$this->myPicture->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos, $YPos - $OuterTickWidth - $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBox = $YPos - $OuterTickWidth - 4 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MinTop = min($MinTop, $TxtBox);
						$LastX = $XPos;
					}

					if (isset($AxisSettings["Name"])) {
						$YPos = $MinTop - 2;
						$XPos = $this->myPicture->GraphAreaX1 + ($this->myPicture->GraphAreaXdiff) / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						$MinTop = $Bounds[2]["Y"];
					}

					$AxisPos["T"] = $MinTop - $ScaleSpacing;
				}

			} elseif ($AxisSettings["Identity"] == AXIS_Y) {
				if ($AxisSettings["Position"] == AXIS_POSITION_LEFT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->myPicture->drawLine($AxisPos["L"], $this->myPicture->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->myPicture->GraphAreaY2 - $AxisSettings["Margin"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($AxisPos["L"], $this->myPicture->GraphAreaY1, $AxisPos["L"], $this->myPicture->GraphAreaY2, ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($AxisPos["L"], $this->myPicture->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->myPicture->GraphAreaY1 - ($ArrowSize * 2), ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Height = $this->myPicture->GraphAreaYdiff - $AxisSettings["Margin"] * 2;
					$Step = $Height / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MinLeft = $AxisPos["L"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$YPos = $this->myPicture->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
						$XPos = $AxisPos["L"];
						$Value = $this->myPicture->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);

						if (!is_null($LastY) && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawFilledRectangle($this->myPicture->GraphAreaX1 + $FloatingOffset, $LastY, $this->myPicture->GraphAreaX2 - $FloatingOffset, $YPos, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if (($YPos != $this->myPicture->GraphAreaY1 && $YPos != $this->myPicture->GraphAreaY2) && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawLine($this->myPicture->GraphAreaX1 + $FloatingOffset, $YPos, $this->myPicture->GraphAreaX2 - $FloatingOffset, $YPos, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							 $this->myPicture->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
						$TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MinLeft = min($MinLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisSettings["Name"])) {
						$XPos = $MinLeft - 2;
						$YPos = $this->myPicture->GraphAreaY1 + ($this->myPicture->GraphAreaYdiff) / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisSettings["Name"],["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90]);
						$MinLeft = $Bounds[2]["X"];
					}

					$AxisPos["L"] = $MinLeft - $ScaleSpacing;
				} elseif ($AxisSettings["Position"] == AXIS_POSITION_RIGHT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->myPicture->drawLine($AxisPos["R"], $this->myPicture->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->myPicture->GraphAreaY2 - $AxisSettings["Margin"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($AxisPos["R"], $this->myPicture->GraphAreaY1, $AxisPos["R"], $this->myPicture->GraphAreaY2, ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($AxisPos["R"], $this->myPicture->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->myPicture->GraphAreaY1 - ($ArrowSize * 2), ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Height = $this->myPicture->GraphAreaYdiff - $AxisSettings["Margin"] * 2;
					$Step = $Height / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxLeft = $AxisPos["R"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$YPos = $this->myPicture->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
						$XPos = $AxisPos["R"];
						$Value = $this->myPicture->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);

						if (!is_null($LastY) && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawFilledRectangle($this->myPicture->GraphAreaX1 + $FloatingOffset, $LastY, $this->myPicture->GraphAreaX2 - $FloatingOffset, $YPos, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if (($YPos != $this->myPicture->GraphAreaY1 && $YPos != $this->myPicture->GraphAreaY2) && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawLine($this->myPicture->GraphAreaX1 + $FloatingOffset, $YPos, $this->myPicture->GraphAreaX2 - $FloatingOffset, $YPos, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							$this->myPicture->drawLine($XPos - $InnerSubTickWidth, $YPos - $SubTicksSize, $XPos + $OuterSubTickWidth, $YPos - $SubTicksSize, ["Color" => $SubTickColor]);
						}
						
						$this->myPicture->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
						$TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MaxLeft = max($MaxLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisSettings["Name"])) {
						$XPos = $MaxLeft + 6;
						$YPos = $this->myPicture->GraphAreaY1 + ($this->myPicture->GraphAreaYdiff) / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270]);
						$MaxLeft = $Bounds[2]["X"];
					}

					$AxisPos["R"] = $MaxLeft + $ScaleSpacing;
				}
			}
		}

		$this->myPicture->myData->saveAxisConfig($Data["Axis"]);
	}

	/* Draw a scatter plot chart */
	function drawScatterPlotChart(array $Format = [])
	{
		$PlotSize = 3;
		$PlotBorder = FALSE;
		$BorderColor = new pColor(250,250,250,30);
		$BorderSize = 1;
		$RecordImageMap = FALSE;
		$ImageMapTitle = NULL;

		/* Override defaults */
		extract($Format);

		$Data = $this->myPicture->myData->getData();

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Description = (is_null($ImageMapTitle)) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				if (isset($Series["Picture"]) && $Series["Picture"] != "") {
					$Picture = $Series["Picture"];
					$PicInfo = $this->myPicture->getPicInfo($Picture);
					list($PicWidth, $PicHeight, $PicType) = $PicInfo;
				} else {
					$Picture = NULL;
				}

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {

						if ($RecordImageMap) {
							$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
							$this->myPicture->addToImageMap("CIRCLE", floor($X).",".floor($Y).",".floor($PlotSize + $BorderSize), $Series["Color"]->toHex(), $Description, $RealValue);
						}

						if (isset($Series["Shape"])) {
							$this->myPicture->drawShape($X, $Y, $Series["Shape"], $PlotSize, $PlotBorder, $BorderSize, $Series["Color"], $BorderColor);
						} elseif (is_null($Picture)) {
							if ($PlotBorder) {
								$this->myPicture->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, ["Color" => $BorderColor]);
							}

							$this->myPicture->drawFilledCircle($X, $Y, $PlotSize, ["Color" => $Series["Color"]]);
						} else {
							$this->myPicture->drawFromPicture($PicInfo, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
						}
					}
				}
			}
		}
	}

	/* Draw a scatter line chart */
	function drawScatterLineChart(array $Format = [])
	{
		$Data = $this->myPicture->myData->getData();

		$RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : FALSE;
		$ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : NULL;
		$ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
		#$ImageMapPrecision = isset($Format["ImageMapPrecision"]) ? $Format["ImageMapPrecision"] : 2; # UNUSED

		/* Parse all the series to draw */
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Description = (is_null($ImageMapTitle)) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);	
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				$Settings = ["Color" => $Series["Color"]];
				(!is_null($Series["Ticks"])) AND $Settings["Ticks"] = $Series["Ticks"];
				(!is_null($Series["Weight"])) AND $Settings["Weight"] = $Series["Weight"];

				$LastX = VOID;
				$LastY = VOID;
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {

						if ($RecordImageMap) {
							$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
							$this->myPicture->addToImageMap("CIRCLE", floor($X).",".floor($Y).",".$ImageMapPlotSize, $Series["Color"]->toHex(), $Description, $RealValue);
						}

						if ($LastX != VOID && $LastY != VOID){
							$this->myPicture->drawLine($LastX, $LastY, $X, $Y, $Settings);
						}
					}

					$LastX = $X;
					$LastY = $Y;
				}
			}
		}
	}

	/* Draw a scatter spline chart */
	function drawScatterSplineChart(array $Format = [])
	{
		$Data = $this->myPicture->myData->getData();

		$RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : FALSE;
		$ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : NULL;
		$ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
		#$ImageMapPrecision = isset($Format["ImageMapPrecision"]) ? $Format["ImageMapPrecision"] : 2; # UNUSED

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Description = (is_null($ImageMapTitle)) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				$SplineSettings = ["Color" => $Series["Color"]]; 
				(!is_null($Series["Ticks"])) AND $SplineSettings["Ticks"] = $Series["Ticks"];
				(!is_null($Series["Weight"])) AND $SplineSettings["Weight"] = $Series["Weight"];

				$LastX = VOID;
				$LastY = VOID;
				$WayPoints = [];
				$SplineSettings["Forces"] = [];

				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];

					if ($X != VOID && $Y != VOID) {
						$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
						if ($RecordImageMap) {
							$this->myPicture->addToImageMap("CIRCLE", floor($X).",".floor($Y).",".$ImageMapPlotSize, $Series["Color"]->toHex(), $Description, $RealValue);
						}
						$WayPoints[] = [$X,$Y];
						$SplineSettings["Forces"][] = hypot(($X - $LastX),($Y - $LastY)) / 5; # GetDistance
					} else { # if ($Y == VOID || $X == VOID) {
						$this->myPicture->drawSpline($WayPoints, $SplineSettings);
						$WayPoints = [];
						$SplineSettings["Forces"] = [];
					}

					$LastX = $X;
					$LastY = $Y;
				}

				$this->myPicture->drawSpline($WayPoints, $SplineSettings);
			}
		}
	}

	/* Return the scaled plot position */
	function getPosArray(array $Values, int $AxisID)
	{
		$Result = [];

		foreach($Values as $Value) {
			$Result[] = $this->getPosArraySingle($Value, $AxisID);
		}

		return $Result;
	}

	/* Return the scaled plot position */
	function getPosArraySingle($Value, int $AxisID)
	{
		if ($Value == VOID) {
			return VOID;
		}

		$Data = $this->myPicture->myData->getData()["Axis"];

		if ($Data[$AxisID]["Identity"] == AXIS_X) {
			$Height = $this->myPicture->GraphAreaXdiff - $Data[$AxisID]["Margin"] * 2;
			$Result = $this->myPicture->GraphAreaX1 + $Data[$AxisID]["Margin"] + (($Height / ($Data[$AxisID]["ScaleMax"] - $Data[$AxisID]["ScaleMin"])) * ($Value - $Data[$AxisID]["ScaleMin"]));
		} else {
			$Height = $this->myPicture->GraphAreaYdiff - $Data[$AxisID]["Margin"] * 2;
			$Result = $this->myPicture->GraphAreaY2 - $Data[$AxisID]["Margin"] - (($Height / ($Data[$AxisID]["ScaleMax"] - $Data[$AxisID]["ScaleMin"])) * ($Value - $Data[$AxisID]["ScaleMin"]));
		}

		return $Result;
	}

	/* Draw the legend of the active series */
	function drawScatterLegend(int $X, int $Y, array $Format = [])
	{
		$Family = LEGEND_FAMILY_BOX;
		$FontName = $this->myPicture->FontName;
		$FontSize = $this->myPicture->FontSize;
		$FontColor = $this->myPicture->FontColor;
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;
		$Margin = 5;
		$Color = new pColor(200);
		$BorderColor = new pColor(255);
		$Surrounding = NULL;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;

		/* Override defaults */
		extract($Format);

		if (!is_null($Surrounding)) {
			$BorderColor = $Color->newOne()->RGBChange($Surrounding);
		}

		$Data = $this->myPicture->myData->getData();
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"] && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->myPicture->FontSize, $IconAreaHeight) + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

					$vY = $vY + max($this->myPicture->FontSize * count($Lines), $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->myPicture->FontSize + 3) * $Key), $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep;
		#$vX = $vX - $XStep; # UNUSED
		$TopOffset = $Y - $Boundaries["T"];
		if ($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) {
			$Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;
		}

		if ($Style == LEGEND_ROUND) {
			$this->myPicture->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		} elseif ($Style == LEGEND_BOX) {
			$this->myPicture->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		}

		$RestoreShadow = $this->myPicture->Shadow;
		$this->Shadow = FALSE;
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				if (isset($Series["Picture"])) {
					$Picture = $Series["Picture"];
					list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Picture);
					$PicX = $X + $IconAreaWidth / 2;
					$PicY = $Y + $IconAreaHeight / 2;
					$this->myPicture->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);

				} else {
					if ($Family == LEGEND_FAMILY_BOX) {
						$XOffset = ($BoxWidth != $IconAreaWidth) ? floor(($IconAreaWidth - $BoxWidth) / 2) : 0;
						$YOffset = ($BoxHeight != $IconAreaHeight) ? floor(($IconAreaHeight - $BoxHeight) / 2) : 0;

						$this->myPicture->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["Color" => new pColor(0,0,0,20)]);
						$this->myPicture->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["Color" => $Series["Color"],"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_CIRCLE) {
						$this->myPicture->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["Color" => new pColor(0,0,0,20)]);
						$this->myPicture->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["Color" => $Series["Color"],"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_LINE) {
						$this->myPicture->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["Color" => new pColor(0,0,0,20),"Ticks" => $Series["Ticks"], "Weight" => $Series["Weight"]]);
						$this->myPicture->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["Color" => $Series["Color"],"Ticks" => $Series["Ticks"],"Weight" => $Series["Weight"]]);
					}
				}

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					foreach($Lines as $Key => $Value) {
						$this->myPicture->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->myPicture->FontSize + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT]);
					}
					$Y = $Y + max($this->myPicture->FontSize * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->drawText($X + $IconAreaWidth + 4, $Y + 2 + $IconAreaHeight / 2 + (($this->myPicture->FontSize + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT]);
						$Width[] = $BoxArray[1]["X"];
					}
					$X = max($Width) + 2 + $XStep;
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	} 

	/* Get the legend box size */
	function getScatterLegendSize(array $Format = []) # UNUSED
	{
		$FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->myPicture->FontName;
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->myPicture->FontSize;
		$BoxSize = isset($Format["BoxSize"]) ? $Format["BoxSize"] : 5;
		$Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
		$Style = isset($Format["Style"]) ? $Format["Style"] : LEGEND_ROUND;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
		$YStep = max($this->myPicture->FontSize, $BoxSize) + 5;
		$XStep = $BoxSize + 5;
		$X = 100;
		$Y = 100;
		$Data = $this->myPicture->myData->getData();
		
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"] && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

					$vY = $vY + max($this->myPicture->FontSize * count($Lines), $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {

					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->myPicture->FontSize + 3) * $Key), $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep + $BoxSize;
		$TopOffset = $Y - $Boundaries["T"];
		($Boundaries["B"] - $vY < $TopOffset) AND $Boundaries["B"] = $vY + $TopOffset;
		$Width = ($Boundaries["R"] + $Margin) - ($Boundaries["L"] - $Margin);
		$Height = ($Boundaries["B"] + $Margin) - ($Boundaries["T"] - $Margin);

		return ["Width" => $Width,"Height" => $Height];
	}

	/* Draw the line of best fit */
	function drawScatterBestFit(array $Format = [])
	{
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$Data = $this->myPicture->myData->getData();

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {
				$SerieXAxis = $Data["Series"][$Series["X"]]["Axis"];
				$SerieYAxis = $Data["Series"][$Series["Y"]]["Axis"];
				$PosArrayX = $Data["Series"][$Series["X"]]["Data"];
				$PosArrayY = $Data["Series"][$Series["Y"]]["Data"];
				$Sxy = 0;
				$Sx = 0;
				$Sy = 0;
				$Sxx = 0;
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					$Sxy = $Sxy + $X * $Y;
					$Sx = $Sx + $X;
					$Sy = $Sy + $Y;
					$Sxx = $Sxx + $X * $X;
				}

				$n = count($PosArrayX);
				if ((($n * $Sxx) == ($Sx * $Sx))) {
					$X1 = $this->getPosArraySingle($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
					$X2 = $X1;
					$Y1 = $this->myPicture->GraphAreaY1;
					$Y2 = $this->myPicture->GraphAreaY2;
				} else {
					$M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
					$B = (($Sy) - ($M * $Sx)) / ($n);
					$X1 = $this->getPosArraySingle($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
					$Y1 = $this->getPosArraySingle($M * $Data["Axis"][$SerieXAxis]["ScaleMin"] + $B, $SerieYAxis);
					$X2 = $this->getPosArraySingle($Data["Axis"][$SerieXAxis]["ScaleMax"], $SerieXAxis);
					$Y2 = $this->getPosArraySingle($M * $Data["Axis"][$SerieXAxis]["ScaleMax"] + $B, $SerieYAxis);
					$RealM = - ($Y2 - $Y1) / ($X2 - $X1);
					if ($Y1 < $this->myPicture->GraphAreaY1) {
						$X1 = $X1 + ($this->myPicture->GraphAreaY1 - $Y1 / $RealM);
						$Y1 = $this->myPicture->GraphAreaY1;
					}

					if ($Y1 > $this->myPicture->GraphAreaY2) {
						$X1 = $X1 + ($Y1 - $this->myPicture->GraphAreaY2) / $RealM;
						$Y1 = $this->myPicture->GraphAreaY2;
					}

					if ($Y2 < $this->myPicture->GraphAreaY1) {
						$X2 = $X2 - ($this->myPicture->GraphAreaY1 - $Y2) / $RealM;
						$Y2 = $this->myPicture->GraphAreaY1;
					}

					if ($Y2 > $this->myPicture->GraphAreaY2) {
						$X2 = $X2 - ($Y2 - $this->myPicture->GraphAreaY2) / $RealM;
						$Y2 = $this->myPicture->GraphAreaY2;
					}
				}

				$this->myPicture->drawLine($X1, $Y1, $X2, $Y2, ["Color" => $Series["Color"], "Ticks" => $Ticks]);
			}
		}
	}

	function writeScatterLabel(int $ScatterSerieID, int $Point, array $Format = [])
	{
		$Data = $this->myPicture->myData->getData();

		if (!isset($Data["ScatterSeries"][$ScatterSerieID])) {
			throw pException::ScatterInvalidInputException("Serie was not found!");
		}

		$DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
		$Decimals = isset($Format["Decimals"]) ? $Format["Decimals"] : NULL;

		$Series = $Data["ScatterSeries"][$ScatterSerieID];
		$SerieValuesX = $Data["Series"][$Series["X"]]["Data"];
		$SerieXAxis = $Data["Series"][$Series["X"]]["Axis"];
		$SerieValuesY = $Data["Series"][$Series["Y"]]["Data"];
		$SerieYAxis = $Data["Series"][$Series["Y"]]["Axis"];

		$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
		$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);

		if (isset($PosArrayX[$Point]) && isset($PosArrayY[$Point])) {
			$X = floor($PosArrayX[$Point]);
			$Y = floor($PosArrayY[$Point]);
			if ($DrawPoint == LABEL_POINT_CIRCLE) {
				$this->myPicture->drawFilledCircle($X, $Y, 3, ["Color" => new pColor(255,255,255), "BorderColor" => new pColor(0,0,0)]);
			} elseif ($DrawPoint == LABEL_POINT_BOX) {
				$this->myPicture->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["Color" => new pColor(255,255,255), "BorderColor" => new pColor(0,0,0)]);
			}

			$XValue = (is_null($Decimals)) ? $SerieValuesX[$Point] : round($SerieValuesX[$Point], $Decimals);
			$XValue = $this->myPicture->scaleFormat($XValue, $Data["Axis"][$SerieXAxis]["Display"], $Data["Axis"][$SerieXAxis]["Format"], $Data["Axis"][$SerieXAxis]["Unit"]);

			$YValue = (is_null($Decimals)) ? $SerieValuesY[$Point] : round($SerieValuesY[$Point], $Decimals);
			$YValue = $this->myPicture->scaleFormat($YValue, $Data["Axis"][$SerieYAxis]["Display"], $Data["Axis"][$SerieYAxis]["Format"], $Data["Axis"][$SerieYAxis]["Unit"]);

			$Description = (isset($Series["Description"])) ? $Series["Description"] : "No description";
			$this->myPicture->drawLabelBox($X, $Y - 3, $Description, ["Format" => $Series["Color"],"Caption" => $XValue . " / " . $YValue], $Format);
		}

	}

	/* Draw a Scatter threshold */
	function drawScatterThreshold($Value, array $Format = [])
	{

		$AxisID = 0;
		$Color = new pColor(255,0,0,50);
		$Weight = NULL;
		$Ticks = 3;
		$Wide = FALSE;
		$WideFactor = 5;
		$WriteCaption = FALSE;
		$Caption = NULL;
		$CaptionAlign = CAPTION_LEFT_TOP;
		$CaptionOffset = 10;
		$CaptionColor = new pColor(255);
		$DrawBox = TRUE;
		$DrawBoxBorder = FALSE;
		$BorderOffset = 5;
		$BoxRounded = TRUE;
		$RoundedRadius = 3;
		$BoxColor = new pColor(0,0,0,20);
		$BoxSurrounding = 0;
		$BoxBorderColor = new pColor(255);

		/* Override defaults */
		extract($Format);

		$Data = $this->myPicture->myData->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::ScatterInvalidInputException("Axis ID was not found!");
		}

		(is_null($Caption)) AND $Caption = strval($Value);
		$CaptionSettings = [
			"DrawBox" => $DrawBox,
			"DrawBoxBorder" => $DrawBoxBorder,
			"BorderOffset" => $BorderOffset,
			"BoxRounded" => $BoxRounded,
			"RoundedRadius" => $RoundedRadius,
			"BoxColor" => $BoxColor,
			"BoxSurrounding" => $BoxSurrounding,
			"BoxBorderColor" => $BoxBorderColor,
			"Color" => $CaptionColor
		];

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {
			$X1 = $this->myPicture->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			$X2 = $this->myPicture->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			$Y = $this->getPosArraySingle($Value, $AxisID);
			$this->myPicture->drawLine($X1, $Y, $X2, $Y, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$WideColor = $Color->newOne()->AlphaSlash($WideFactor);
				$this->myPicture->drawLine($X1, $Y - 1, $X2, $Y - 1, ["Color" => $WideColor,"Ticks" => $Ticks]);
				$this->myPicture->drawLine($X1, $Y + 1, $X2, $Y + 1, ["Color" => $WideColor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$X = $this->myPicture->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
				} else {
					$X = $this->myPicture->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
				}

				$this->myPicture->drawText($X, $Y, $Caption, $CaptionSettings);
			}

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$X = $this->getPosArraySingle($Value, $AxisID);
			$Y1 = $this->myPicture->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			$Y2 = $this->myPicture->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			$this->myPicture->drawLine($X, $Y1, $X, $Y2, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$WideColor = $Color->newOne()->AlphaSlash($WideFactor);
				$this->myPicture->drawLine($X - 1, $Y1, $X - 1, $Y2, ["Color" => $WideColor,"Ticks" => $Ticks]);
				$this->myPicture->drawLine($X + 1, $Y1, $X + 1, $Y2, ["Color" => $WideColor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$Y = $this->myPicture->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
				} else {
					$Y = $this->myPicture->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
				}

				$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
				$this->myPicture->drawText($X, $Y, $Caption, $CaptionSettings);
			}

		}
	}

	/* Draw a Scatter threshold area */
	function drawScatterThresholdArea($Value1, $Value2, array $Format = [])
	{
		$AxisID = 0;
		$Color = new pColor(255,0,0,20);
		$Border = TRUE;
		$BorderColor = NULL;
		$BorderTicks = 2;
		$AreaName = NULL;
		$NameAngle = ZONE_NAME_ANGLE_AUTO;
		$NameColor = new pColor(255);
		$DisableShadowOnArea = TRUE;

		/* Override defaults */
		extract($Format);

		if (is_null($BorderColor)){
			$BorderColor = $Color->newOne()->RGBChange(20);
		}

		$Data = $this->myPicture->myData->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::ScatterInvalidInputException("Serie was not found!");
		}

		if ($Value1 > $Value2) {
			list($Value1, $Value2) = [$Value2,$Value1];
		}

		$RestoreShadow = $this->myPicture->Shadow;
		if ($DisableShadowOnArea && $this->myPicture->Shadow) {
			$this->myPicture->Shadow = FALSE;
		}

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$Y1 = $this->myPicture->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			$Y2 = $this->myPicture->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			$X1 = $this->getPosArraySingle($Value1, $AxisID);
			$X2 = $this->getPosArraySingle($Value2, $AxisID);
			if ($X1 <= $this->myPicture->GraphAreaX1) {
				$X1 = $this->myPicture->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			}

			if ($X2 >= $this->myPicture->GraphAreaX2) {
				$X2 = $this->myPicture->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			}

			$this->myPicture->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $Color]);

			if ($Border) {
				$this->myPicture->drawLine($X1, $Y1, $X1, $Y2, ["Color" => $BorderColor,"Ticks" => $BorderTicks]);
				$this->myPicture->drawLine($X2, $Y1, $X2, $Y2, ["Color" => $BorderColor,"Ticks" => $BorderTicks]);
			}

			if (!is_null($AreaName)) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->myPicture->getTextBox($XPos, $YPos, $this->myPicture->FontName, $this->myPicture->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($X2 - $X1) > $TxtWidth) ? 0 : 90;
				}

				$this->myPicture->Shadow = $RestoreShadow;
				$this->myPicture->drawText($XPos, $YPos, $AreaName, ["Color" => $NameColor,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->myPicture->Shadow = FALSE;
			}

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {

			$X1 = $this->myPicture->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			$X2 = $this->myPicture->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			$Y1 = $this->getPosArraySingle($Value1, $AxisID);
			$Y2 = $this->getPosArraySingle($Value2, $AxisID);
			if ($Y1 >= $this->myPicture->GraphAreaY2) {
				$Y1 = $this->myPicture->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			}

			if ($Y2 <= $this->myPicture->GraphAreaY1) {
				$Y2 = $this->myPicture->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			}

			$this->myPicture->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $Color]);
			if ($Border) {
				$this->myPicture->drawLine($X1, $Y1, $X2, $Y1, ["Color" => $BorderColor,"Ticks" => $BorderTicks]);
				$this->myPicture->drawLine($X1, $Y2, $X2, $Y2, ["Color" => $BorderColor,"Ticks" => $BorderTicks]);
			}

			if (!is_null($AreaName)) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				$this->myPicture->Shadow = $RestoreShadow;
				$this->myPicture->drawText($YPos, $XPos, $AreaName, ["Color" => $NameColor,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->Shadow = FALSE;
			}
	
		}

		$this->myPicture->Shadow = $RestoreShadow;
	}
}

?>