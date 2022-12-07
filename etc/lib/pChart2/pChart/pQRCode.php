<?php
/*
pQRCode - glue for QRCode

Version     : 0.0.1-dev
Made by     : Momchil Bozhinov
Last Update : 31/08/2019

You can find examples on:
https://github.com/bozhinov/PHP-QRCode-fork
*/

#QR_ECLEVEL_L = 0
#QR_ECLEVEL_M = 1
#QR_ECLEVEL_Q = 2
#QR_ECLEVEL_H = 3

#QR_MODE_NUM = 0
#QR_MODE_AN = 1
#QR_MODE_8 = 2
#QR_MODE_KANJI = 3

namespace pChart;

class pQRCode
{
	private $myPicture;
	private $QRCode;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
		$this->QRCode = new \QRCode\QRcode();
	}

	function configure(int $error_correction = 1, int $martrix_poit_size = 6, int $margin = 4)
	{
		$this->QRCode->config(["error_correction" => $error_correction, "matrix_point_size" => $martrix_poit_size, "margin" => $margin]);

		return $this;
	}

	/* $text - Text to encode, $X & $Y - start position on the grid */
	function draw(string $text, $X, $Y)
	{
		$this->QRCode->encode($text)->forPChart($this->myPicture, $X, $Y);
	}

}

?>