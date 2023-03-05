<?php
/*
pCache - speed up the rendering by caching up the pictures

Version     : 2.2.3-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/01/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart\pCache;

class pCacheFile implements pCacheInterface
{
	var $CacheIndex;
	var $CacheDB;
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

		$this->CacheIndex = isset($Settings["CacheIndex"]) ? $Settings["CacheIndex"] : "index.db";
		$this->CacheIndex = $CacheFolder . "/" . $this->CacheIndex;

		$this->CacheDB = isset($Settings["CacheDB"]) ? $Settings["CacheDB"] : "cache.db";
		$this->CacheDB = $CacheFolder . "/" . $this->CacheDB;

		/* Create the cache Db and Index */
		if (!file_exists($this->CacheIndex)) {
			touch($this->CacheIndex);
		}

		if (!file_exists($this->CacheDB)) {
			touch($this->CacheDB);
		}
	}

	/* For when you need to work with multiple cached images */
	function changeID(string $uniqueId)
	{
		$this->Id = md5($uniqueId);
	}

	/* Flush the cache contents */
	function flush()
	{
		file_put_contents($this->CacheIndex, "");
		file_put_contents($this->CacheDB, "");
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

		/* Retrieve the files size */
		$stats = fstat($TempHandle);
		$PicSize = $stats['size'];
		$DbSize = filesize($this->CacheDB);
		/* Save the index */
		file_put_contents($this->CacheIndex, $this->Id.",".$DbSize.",".$PicSize.",".time().",0      \r\n", FILE_APPEND | LOCK_EX);
		/* Get the picture raw contents */
		rewind($TempHandle);
		$Raw = fread($TempHandle, $PicSize);
		/* Close the temporary stream */
		fclose($TempHandle);

		/* Save the picture in the solid database file */
		file_put_contents($this->CacheDB, $Raw, FILE_APPEND | LOCK_EX);
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
		$ID = isset($Settings["Name"]) ? $Settings["Name"] : NULL;
		$Expiry = isset($Settings["Expiry"]) ? $Settings["Expiry"] : -(24 * 60 * 60);
		$TS = time() - $Expiry;

		/* Single file removal */
		if (!is_null($ID)) {
			/* If it's not in the cache DB, go away */
			if (!$this->isInCache()) {
				throw \pChart\pException::CacheException(" ID ".$ID ." not in cache!");
			}
		}

		/* Open the file handles */
		$TempIndex = "";
		$TempDb = "";

		/* Remove the selected ID from the database */
		$IndexContent = file($this->CacheIndex, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach ($IndexContent as $line){

			list($PicID, $DbPos, $PicSize, $GeneratedTS, $Hits) = explode(",", $line);

			/* filter out the ID for removal and OlderThan X */
			if ($PicID != $ID && $GeneratedTS > $TS) {
				$TempIndex .= $PicID.",".strlen($TempDb).",".$PicSize.",".$GeneratedTS.",".$Hits."\r\n";
				$TempDb .= file_get_contents($this->CacheDB, NULL, NULL, $DbPos, $PicSize);
			}
		}

		/* Swap the temp & prod DB */
		file_put_contents($this->CacheDB, $TempDb, LOCK_EX);
		file_put_contents($this->CacheIndex, $TempIndex, LOCK_EX);
	}

	function isInCache(bool $Verbose = FALSE, bool $UpdateHitsCount = FALSE)
	{
		$IndexContent = file($this->CacheIndex, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$i = 0;
		$ret = FALSE;

		foreach ($IndexContent as $line){

			list($PicID, $DBPos, $PicSize, $GeneratedTS, $Hits) = explode(",", $line);

			if ($PicID == $this->Id) {
				if ($UpdateHitsCount) {
					/* increment hints as an INT and then convert back to STR */
					$Hits = intval($Hits);
					$Hits++;
					$Hits = strval($Hits);

					if (strlen($Hits) < 7) {
						$Hits .= str_repeat(" ", 7 - strlen($Hits));
					}
				}
				/* Update Index if we have a hit */
				$IndexContent[$i] = $PicID.",".$DBPos.",".$PicSize.",".$GeneratedTS.",".$Hits;
				$ret = ($Verbose) ? ["DBPos" => $DBPos,"PicSize" => $PicSize,"GeneratedTS" => $GeneratedTS,"Hits" => $Hits] : TRUE;
				break;
			}

			$i++;
		}

		/* Update Index file if we have a hit */
		if ($ret != FALSE){
			$UpdatedIndexContent = array_reduce($IndexContent, function($content, $l){$content .= strval($l)."\r\n"; return $content;});
			file_put_contents($this->CacheIndex, $UpdatedIndexContent, LOCK_EX);
		}

		return $ret;
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
		if ($Picture == FALSE) {
			return FALSE;
		}

		header('Content-type: image/png');
		echo $Picture;
	}

	function saveFromCache(string $Destination)
	{
		/* Get the raw picture from the cache */
		$Picture = $this->getFromCache();
		/* Do we have a hit? */
		if ($Picture == FALSE) {
			return FALSE;
		}

		/* Flush the picture to a file */
		file_put_contents($Destination, $Picture);
	}

	function getFromCache()
	{
		/* Lookup for the picture in the cache */
		$CacheInfo = $this->isInCache(TRUE, TRUE);
		/* Not in the cache */
		if (!$CacheInfo) {
			# Momchil: fread returns FALSE on failure. 
			# Return FALSE here as well and not NULL
			return FALSE;
		}

		/* Extract the picture from the solid cache file */
		$Picture = file_get_contents($this->CacheDB, NULL, NULL, $CacheInfo["DBPos"], $CacheInfo["PicSize"]);

		/* Return back the raw picture data */
		return $Picture;
	}
}

?>