<?php

/*
pException - pChart core class

Version     : 0.1
Made by     : Created by Momchil Bozhinov
Last Update : 27/11/2017

This file can be distributed under the license you can find at:
http://www.pchart.net/license

*/

namespace pChart;

class pException extends \Exception
{

	public static function InvalidDimentions($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidImageType($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidInput($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function InvalidResourcePath($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function ImageMapInvalidID($text)
	{
		return new static(sprintf('pChart: %s', $text));
	}

	public static function ImageMapSessionNotStarted()
	{
		return new static('ImageMapper: Yon need to start session before you can use the session storage');
	}

	public static function ImageMapInvalidSerieName($text)
	{
		return new static(sprintf('ImageMapper: The serie name "%s" was not found in the dataset', $text));
	}

	public static function ImageMapSQLiteException($text)
	{
		return new static(sprintf('ImageMapper: %s', $text));
	}

	public static function SQLiteException($text)
	{
		return new static(sprintf('pCache: %s', $text));
	}

	public static function CacheException($text)
	{
		return new static(sprintf('pCache: %s', $text));
	}

	public static function PieNoAbscissaException()
	{
		return new static('pPie: No Abscissa');
	}

	public static function PieNoDataSerieException()
	{
		return new static('pPie: No DataSerie');
	}

	public static function StockMissingSerieException()
	{
		return new static('pStock: No DataSerie');
	}

	public static function SpringIvalidConnectionsException()
	{
		return new static('pSpring: Connections needs to be an array');
	}

	public static function SpringInvalidInputException($text)
	{
		return new static(sprintf('pSprint: %s', $text));
	}

	public static function ZoneChartInvalidInputException($text)
	{
		return new static(sprintf('pCharts: %s', $text));
	}

	public static function ScatterInvalidInputException($text)
	{
		return new static(sprintf('pScatter: %s', $text));
	}

	public static function SurfaceInvalidInputException($text)
	{
		return new static(sprintf('pSurface: %s', $text));
	}

}

?>