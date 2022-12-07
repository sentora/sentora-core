<?php
/*
pBarcode39 - class to create barcodes (39B)

Version     : 2.3.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 17/01/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pBarcode;

class pBarcode39 extends pBarcode
{
	var $Codes = [];
	var $myPicture;
	var $MOD43;

	/* Class creator */
	function __construct(\pChart\pDraw $pChartObject, bool $EnableMOD43 = FALSE)
	{
		parent::__construct($pChartObject);

		$this->MOD43 = $EnableMOD43;

		require("39.php");
		$this->Codes = $Codes;
	}

	/* Return the projected size of a barcode */
	static function getProjection(string $TextString, array $Format = [], bool $EnableMOD43 = FALSE)
	{
		require("39.php");

		list($TextString, $Result) = self::encodeEx($TextString, $Codes, $EnableMOD43);

		return parent::getProjectionEx(strlen($Result), $Format);
	}

	/* Create the encoded string */
	function encode39(string $Value)
	{
		return self::encodeEx($Value, $this->Codes, $this->MOD43);
	}

	private static function encodeEx(string $Value, array $Codes, bool $MOD43)
	{
		$Result = "1001011011010";
		$TextString = "";
		$Arr = str_split($Value);

		foreach($Arr as $char) {
			$CharCode = ord($char);
			if ($CharCode >= 97 && $CharCode <= 122) {
				$CharCode -= 32;
			}

			if (isset($Codes[chr($CharCode)])) {
				$Result .= $Codes[chr($CharCode)] . "0";
				$TextString .= chr($CharCode);
			}
		}

		if ($MOD43) {
			$Checksum = self::checksum($TextString);
			$Result .= $Codes[$Checksum] . "0";
		}

		return ["*" . $TextString . "*", $Result . "100101101101"];
	}

	function draw(string $Value, int $X, int $Y, array $Format = [])
	{
		list($TextString, $Result) = $this->encode39($Value);
		$this->drawEx($TextString, $Result, $X, $Y, $Format);
	}

	static function checksum(string $string)
	{
		$checksum = 0;
		$charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. $/+%';
		for ($i = 0; $i < strlen($string); ++$i) {
			$checksum+= strpos($charset, $string[$i]);
		}

		return substr($charset, ($checksum % 43), 1);
	}

}

?>