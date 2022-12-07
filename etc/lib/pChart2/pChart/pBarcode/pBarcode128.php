<?php
/*
pBarcode128 - class to create barcodes (128B)

Version     : 2.3.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 17/01/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pBarcode;

class pBarcode128 extends pBarcode
{
	var $Codes = [];
	var $Reverse = [];

	function __construct(\pChart\pDraw $pChartObject)
	{
		parent::__construct($pChartObject);

		list($this->Codes, $this->Reverse) = self::getCodes();
	}

	private static function getCodes()
	{
		require("128B.php");

		$Codes = [];
		$Reverse = [];

		foreach($Raw as $entry){
			$Codes[$entry[1]]["ID"] = $entry[0];
			$Codes[$entry[1]]["Code"] = $entry[2];
			$Reverse[$entry[0]]["Code"] = $entry[2];
			$Reverse[$entry[0]]["Asc"] = $entry[1];
		}

		return [$Codes, $Reverse];
	}

	/* Return the projected size of a barcode */
	static function getProjection(string $TextString, array $Format = [])
	{
		list($Codes, $Reverse) = self::getCodes();

		$Result = self::encodeEx($TextString, $Codes, $Reverse);

		return parent::getProjectionEx(strlen($Result), $Format);
	}

	private static function encodeEx(string $Value, array $Codes, array $Reverse)
	{
		$Result = "11010010000";
		$CRC = 104;
		$Arr = str_split($Value);

		foreach($Arr as $i => $char) {
			$CharCode = ord($char);
			if (isset($Codes[$CharCode])) {
				$Result .= $Codes[$CharCode]["Code"];
				$CRC += ($i + 1) * $Codes[$CharCode]["ID"];
			}
		}

		$CRC -= floor($CRC / 103) * 103;
		$Result .= $Reverse[$CRC]["Code"]. "1100011101011";

		return $Result;
	}

	function draw(string $Value, int $X, int $Y, array $Format = [])
	{
		list($TextString, $Result) = $this->encode128($Value);
		$this->drawEx($TextString, $Result, $X, $Y, $Format);
	}

	function encode128(string $TextString)
	{
		$Result = self::encodeEx($TextString, $this->Codes, $this->Reverse);
		return [$TextString, $Result];
	}

}

?>