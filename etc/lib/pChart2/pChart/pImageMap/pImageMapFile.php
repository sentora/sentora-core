<?php
/*
pImageMapFile - pChart core class

Version     : 0.3
Made by     : Forked by Momchil Bozhinov from the original pImage class from Jean-Damien POGOLOTTI
Last Update : 22/01/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

*/

namespace pChart\pImageMap;

class pImageMapFile extends \pChart\pDraw implements pImageMapInterface
{
	/* Image map */
	var $ImageMapFileName = NULL;
	var $ImageMapBuffer = [];

	/* Class constructor */
	function __construct(int $XSize, int $YSize, bool $TransparentBackground = FALSE, string $UniqueID = "imageMap", string $StorageFolder = "temp")
	{
		/* Initialize the image map methods */
		$this->ImageMapFileName = $StorageFolder . "/" . $UniqueID . ".map";

		/* Initialize the parent */
		parent::__construct($XSize, $YSize, $TransparentBackground);
	}

	function __destruct()
	{
		if (!empty($this->ImageMapBuffer)){
			file_put_contents($this->ImageMapFileName, json_encode($this->ImageMapBuffer)); # truncates the file
		}

		parent::__destruct();
	}

	/* does the image map already exist */
	function ImageMapExists()
	{
		return file_exists($this->ImageMapFileName);
	}

	/* Add a zone to the image map */
	function addToImageMap(string $Type, string $Plots, string $Color = "", string $Title = "", string $Message = "", bool $HTMLEncode = FALSE)
	{
		/* Encode the characters in the imagemap in HTML standards */
		$Title = str_replace("&#8364;", "\u20AC", $Title); # Momchil TODO TEST THIS
		$Title = htmlentities($Title, ENT_QUOTES); #, "ISO-8859-15"); # As of PHP 5.6 The default value for the encoding parameter = the default_charset config option.

		($HTMLEncode) AND $Message = htmlentities($Message, ENT_QUOTES);

		$this->ImageMapBuffer[] = [$Type,$Plots,$Color,$Title,$Message];
	}

	/* Remove VOID values from an imagemap custom values array */
	function stripFromSerie(string $SerieName, array $Values)
	{
		if (!isset($this->myData->Data["Series"][$SerieName])) {
			throw \pChart\pException::ImageMapInvalidSerieName($SerieName);
		}

		$Result = [];
		foreach($this->myData->Data["Series"][$SerieName]["Data"] as $Key => $Value) {
			if ($Value != VOID && isset($Values[$Key])) {
				$Result[] = $Values[$Key];
			}
		}

		return $Result;
	}

	/* Replace the title of one image map series */
	function replaceImageMapTitle(string $OldTitle, $NewTitle)
	{
		if (is_array($NewTitle)) {
			$ID = 0;
			$NewTitle = $this->stripFromSerie($OldTitle, $NewTitle);
			foreach($this->ImageMapBuffer as $Key => $Settings) {
				if ($Settings[3] == $OldTitle && isset($NewTitle[$ID])) {
					$this->ImageMapBuffer[$Key][3] = $NewTitle[$ID];
					$ID++;
				}
			}
		} else {
			foreach($this->ImageMapBuffer as $Key => $Settings) {
				if ($Settings[3] == $OldTitle) {
					$this->ImageMapBuffer[$Key][3] = $NewTitle;
				}
			}
		}

	}

	/* Replace the values of the image map contents */
	function replaceImageMapValues(string $Title, array $Values)
	{
		$Values = $this->stripFromSerie($Title, $Values);
		$ID = 0;

		foreach($this->ImageMapBuffer as $Key => $Settings) {
			if ($Settings[3] == $Title) {
				if (isset($Values[$ID])) {
					$this->ImageMapBuffer[$Key][4] = $Values[$ID];
				}
				$ID++;
			}
		}

	}

	/* Dump the image map */
	/* Momchil: this function relies on the fact that the ImageMap for the image already exists */
	function dumpImageMap()
	{
		if (file_exists($this->ImageMapFileName)){

			echo file_get_contents($this->ImageMapFileName);

		} else {
			throw \pChart\pException::ImageMapInvalidID("ImageMap index ".$this->ImageMapFileName. " does not exist in file storage!");
		}

		/* When the image map is returned to the client, the script ends */
		exit();
	}

}

?>