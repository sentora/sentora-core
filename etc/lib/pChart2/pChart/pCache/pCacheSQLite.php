<?php
/*
pCacheSqlite - Cache images to SQLite database

Requires SQLite PDO ext

Version     : 0.2-dev
Made by     : Momchil Bozhinov
Last Update : 30/08/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pCache;

use pChart\pSQLite;

class pCacheSQLite implements pCacheInterface
{
	var $SQLite;
	var $Id;

	/* Class creator */
	function __construct(array $Settings = [], string $uniqueId)
	{
		$CacheFolder = isset($Settings["CacheFolder"]) ? $Settings["CacheFolder"] : "cache";

		#if (!is_dir($CacheFolder)){
		#	mkdir($CacheFolder, 0775);
		#}

		$this->Id = md5($uniqueId);

		/* blocking the file access to the cache seems a good idea 
		<Files ~ "\cache">
			Order allow,deny
			Deny from all
		</Files>
		*/

		$DbPath = isset($Settings["DbPath"]) ? $Settings["DbPath"] : "sql.cache.db";

		/* Create the cache Db */
		$this->SQLite = new pSQLite($CacheFolder . "/" . $DbPath);
		$this->SQLite->execute("CREATE TABLE IF NOT EXISTS cache (Id TEXT,time INTEGER,hits INTEGER,data BLOB,PRIMARY KEY(Id));");
	}

	/* For when you need to work with multiple cached images */
	function changeID(string $uniqueId)
	{
		$this->Id = md5($uniqueId);
	}

	/* Flush the cache contents */
	function flush()
	{
		$this->SQLite->flush("cache");
	}

	/* Write the generated picture to the cache */
	function writeToCache($pChartObject)
	{
		if (!($pChartObject instanceof \pChart\pDraw)){
			die("pCache needs a pDraw object. Please check the examples.");
		}

		/* Create a temporary stream */
		$TempHandle = fopen("php://temp", "wb");

		/* Flush the picture to a temporary file */
		imagepng($pChartObject->Picture, $TempHandle);

		/* Get the picture raw contents */
		rewind($TempHandle);
		$Raw = "";
		while (!feof($TempHandle)) {
			$Raw.= fread($TempHandle, 16384); # 16kb chunks
		}
		/* Close the temporary stream */
		fclose($TempHandle);

		$time = time();

		/* Save picture to cache */
		$params = [
			"Id" => [$this->Id, 1],
			"time" => [$time, 0],
			"data" => [$Raw, 1]
		];

		$this->SQLite->execute("INSERT INTO cache VALUES(:%s, :%s, 0, :%s);", $params);
	}

	/* Remove object older than the specified TS */
	function removeOlderThan(int $Expiry)
	{
		$this->dbRemoval(["Expiry" => $Expiry]);
	}

	/* Remove an object from the cache */
	function remove()
	{
		$this->dbRemoval(["Name" => $this->Id]);
	}

	/* Remove with specified criteria */
	function dbRemoval(array $Settings)
	{
		$ID = isset($Settings["Name"]) ? $Settings["Name"] : "";
		$Expiry = isset($Settings["Expiry"]) ? $Settings["Expiry"] : -(24 * 60 * 60);
		$TS = time() - $Expiry;

		/* Single file removal */
		if ($ID != "") {
			/* If it's not in the cache DB, go away */
			if (!$this->isInCache()) {
				throw \pChart\pException::SQLiteException(" ID ".$ID ." not in cache!");
			}
		}

		if ($ID != "") {
			$statement = "DELETE FROM cache WHERE Id= :%s;";
			$params = ["Id" => [$ID, 1]];
		} else {
			$statement = "DELETE FROM cache WHERE time < :%s;";
			$params = ["from" => [$TS, 0]];
		}

		$this->SQLite->execute($statement, $params);
	}

	function isInCache(bool $Verbose = FALSE, bool $UpdateHitsCount = FALSE)
	{

		$match = $this->SQLite->execute("SELECT Id,hits,data FROM cache WHERE Id= :%s;", ["Id" => [$this->Id,1]], $expects_return = TRUE, $select_one = TRUE);

		if ($match != FALSE){
			if ($UpdateHitsCount) {
				$match["hits"]++;
				$this->SQLite->execute("UPDATE cache SET hits= :%s WHERE Id= :%s;", ["Id" => [$this->Id,1], "hits" => [$match["hits"],0]]);
			}
			return ($Verbose) ? $match["data"] : TRUE;
		} else {
			return FALSE;
		}
	}

	/* Automatic output method based on the calling interface */
	function autoOutput(string $Destination = "output.png")
	{
		if (php_sapi_name() == "cli") {
			$this->saveFromCache($Destination);
		} else {
			$this->strokeFromCache();
		}
	}

	function strokeFromCache()
	{
		/* Get the raw picture from the cache */
		$Picture = $this->getFromCache();
		/* Do we have a hit? */
		if (!$Picture) {
			return FALSE;
		} else {
			header('Content-type: image/png');
			echo $Picture;
		}
	}

	function saveFromCache(string $Destination)
	{
		/* Get the raw picture from the cache */
		$Picture = $this->getFromCache();
		/* Do we have a hit? */
		if (!$Picture) {
			return FALSE;
		} else {
			/* Flush the picture to a file */
			file_put_contents($Destination, $Picture);
		}
	}

	function getFromCache()
	{
		/* Lookup for the picture in the cache */
		$CacheInfo = $this->isInCache(TRUE, TRUE);

		/* Not in the cache */
		if (!$CacheInfo) {
			return FALSE;
		} else {
			/* Return back the raw picture data */
			return $CacheInfo;
		}
	}

}

?>