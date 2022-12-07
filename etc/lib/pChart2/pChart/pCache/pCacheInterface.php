<?php
/*
pCacheInterface

Version     : 0.1-dev
Made by     : Momchil Bozhinov
Last Update : 01/01/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pCache;

Interface pCacheInterface
{
	function __construct(array $Settings = [], string $uniqueId);

	function changeID(string $uniqueId);

	function flush();

	function writeToCache($pChartObject);

	function removeOlderThan(int $Expiry);

	function remove();

	function dbRemoval(array $Settings);

	function isInCache(bool $Verbose, bool $UpdateHitsCount);

	function autoOutput(string $Destination);

	function strokeFromCache();

	function saveFromCache(string $Destination);

	function getFromCache();
}

?>