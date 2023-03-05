<?php
/*
pBarcode - base class

Version     : 2.3.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 17/01/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pBarcode;

class pBarcode
{
	var $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Return the projected size of a barcode */
	static function getProjectionEx(float $BarcodeLength, array $Format = [])
	{
		$Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
		$ShowLegend = isset($Format["ShowLegend"]) ? $Format["ShowLegend"] : FALSE;
		$LegendOffset = isset($Format["LegendOffset"]) ? $Format["LegendOffset"] : 5;
		$DrawArea = isset($Format["DrawArea"]) ? $Format["DrawArea"] : FALSE;
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : 12;
		$Height = isset($Format["Height"]) ? $Format["Height"] : 30;

		$WOffset = ($DrawArea) ? 20 : 0;
		$HOffset = ($ShowLegend) ? $FontSize + $LegendOffset + $WOffset : 0;

		if ($Angle == 0){
			return [$WOffset + $BarcodeLength, $Height + $HOffset];
		} else {
			$X1 = cos(deg2rad($Angle)) * ($WOffset + $BarcodeLength);
			$Y1 = sin(deg2rad($Angle)) * ($WOffset + $BarcodeLength);
			$X2 = $X1 + cos(deg2rad($Angle + 90)) * ($HOffset + $Height);
			$Y2 = $Y1 + sin(deg2rad($Angle + 90)) * ($HOffset + $Height);

			return [ceil(max(abs($X1), abs($X2))), ceil(max(abs($Y1), abs($Y2)))]; # "Width", "Height"
		}
	}

	/* Create the encoded string */
	function drawEx(string $TextString, string $Result, int $X, int $Y, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new \pChart\pColor(0,0,0,100);
		$Height = isset($Format["Height"]) ? $Format["Height"] : 30;
		$Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
		$ShowLegend = isset($Format["ShowLegend"]) ? $Format["ShowLegend"] : FALSE;
		$LegendOffset = isset($Format["LegendOffset"]) ? $Format["LegendOffset"] : 5;
		$DrawArea = isset($Format["DrawArea"]) ? $Format["DrawArea"] : FALSE;
		$AreaColor = isset($Format["AreaColor"]) ? $Format["AreaColor"] : new \pChart\pColor(255,255,255,$Color->Alpha);
		$AreaBorderColor = isset($Format["AreaBorderColor"]) ? $Format["AreaBorderColor"] : $AreaColor->newOne();

		$BarcodeLength = strlen($Result);

		$cos = cos(deg2rad($Angle));
		$sin = sin(deg2rad($Angle));

		$cos90 = cos(deg2rad($Angle + 90));
		$sin90 = sin(deg2rad($Angle + 90));

		if ($DrawArea) {
			$X1 = $X + cos(deg2rad($Angle - 135)) * 10;
			$Y1 = $Y + sin(deg2rad($Angle - 135)) * 10;
			$X2 = $X1 + $cos * ($BarcodeLength + 20);
			$Y2 = $Y1 + $sin * ($BarcodeLength + 20);
			if ($ShowLegend) {
				$X3 = $X2 + $cos90 * ($Height + $LegendOffset + $this->myPicture->FontSize + 10);
				$Y3 = $Y2 + $sin90 * ($Height + $LegendOffset + $this->myPicture->FontSize + 10);
			} else {
				$X3 = $X2 + $cos90 * ($Height + 20);
				$Y3 = $Y2 + $sin90 * ($Height + 20);
			}

			$X4 = $X3 + (-$cos) * ($BarcodeLength + 20);
			$Y4 = $Y3 + (-$sin) * ($BarcodeLength + 20);
			$this->myPicture->drawPolygon([$X1,$Y1,$X2,$Y2,$X3,$Y3,$X4,$Y4], ["Color" => $AreaColor,"BorderColor" => $AreaBorderColor]);
		}

		for ($i = 1; $i <= $BarcodeLength; $i++) {
			if (substr($Result, $i - 1, 1) == "1") {
				$X1 = $X + $cos * $i;
				$Y1 = $Y + $sin * $i;
				$X2 = $X1 + $cos90 * $Height;
				$Y2 = $Y1 + $sin90 * $Height;
				$this->myPicture->drawLine($X1, $Y1, $X2, $Y2, ["Color" => $Color]);
			}
		}

		if ($ShowLegend) {
			$X1 = $X + $cos * ($BarcodeLength / 2);
			$Y1 = $Y + $sin * ($BarcodeLength / 2);
			$LegendX = $X1 + $cos90 * ($Height + $LegendOffset);
			$LegendY = $Y1 + $sin90 * ($Height + $LegendOffset);
			$this->myPicture->drawText($LegendX, $LegendY, $TextString, ["Color" => $Color,"Angle" => - $Angle,"Align" => TEXT_ALIGN_TOPMIDDLE]);
		}
	}

}

?>