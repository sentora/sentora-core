<?php
/*
pPyramid - class to draw pyramids

Version     : 0.0.1-dev
Made by     : Momchil Bozhinov
Last Update : 01/02/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* pRadar class definition */
class pPyramid
{
	var $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	function drawPyramid($X, $Y, $Base, $Height, $NumSegments = 4, array $Format = []){

		$Color =  isset($Format["Color"])  ? $Format["Color"]  : FALSE;
		$Offset = isset($Format["Offset"]) ? $Format["Offset"] : 5;
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;

		$Settings = [
			"Color" => $Color,
			"NoFill" => $NoFill
		];

		# Account for the combined heights of the offsets
		$h = ($Height - (($NumSegments - 1) * $Offset)) / $NumSegments;

		for($i=0; $i<$NumSegments; $i++){

			if ($Color == FALSE){
				if (isset($this->myPicture->myData->Palette[$i])){
					$Settings["Color"] = $this->myPicture->myData->Palette[$i]->newOne();
				} else {
					$Settings["Color"] = new pColor();
				}
			}

			if ($i != 0){
				$Base -= (2 * $h);
			}

			$Xi = $X + ($h * $i);
			$Yi = $Y - ($h * $i);
			$Oi = ($Offset * $i);

			$Points = [
					$Xi + $Oi, $Yi - $Oi,
					$Xi - $Oi + $Base, $Yi - $Oi,
					$Xi + $Base - $h - $Oi, $Yi - $h - $Oi,
					$Xi + $Oi + $h, $Yi - $h - $Oi,
					$Xi + $Oi, $Yi - $Oi
				];

			#print_r($Points);

			$this->myPicture->drawPolygon($Points, $Settings);
		}

	}

	function drawReversePyramid($X, $Y, $Base, $Height, $NumSegments = 4, array $Format = []){

		$Color =  isset($Format["Color"])  ? $Format["Color"]  : FALSE;
		$Offset = isset($Format["Offset"]) ? $Format["Offset"] : 5;
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;

		$Settings = [
			"Color" => $Color,
			"NoFill" => $NoFill
		];

		# Account for the combined heights of the offsets
		$h = ($Height - (($NumSegments - 1) * $Offset)) / $NumSegments;

		$Y -= $Height;

		for($i=0; $i<$NumSegments; $i++){

			if ($Color == FALSE){
				if (isset($this->myPicture->myData->Palette[$i])){
					$Settings["Color"] = $this->myPicture->myData->Palette[$i]->newOne();
				} else {
					$Settings["Color"] = new pColor();
				}
			}

			if ($i != 0){
				$Base -= (2 * $h);
			}

			$Xi = $X + ($h * $i);
			$Yi = $Y + ($h * $i);
			$Oi = ($Offset * $i);

			$Points = [
					$Xi + $Oi, $Yi + $Oi,
					$Xi - $Oi + $Base, $Yi + $Oi,
					$Xi + $Base - $h - $Oi, $Yi + $h + $Oi,
					$Xi + $Oi + $h, $Yi + $h + $Oi,
					$Xi + $Oi, $Yi + $Oi
				];

			#print_r($Points);
			$this->myPicture->drawPolygon($Points, $Settings);
		}

	}

}

?>