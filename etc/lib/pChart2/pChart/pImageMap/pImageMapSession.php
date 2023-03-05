<?php
/*
pImageMapSession - pChart core class

Version     : 0.3
Made by     : Forked by Momchil Bozhinov from the original pImage class from Jean-Damien POGOLOTTI
Last Update : 22/01/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

*/

namespace pChart\pImageMap;

class pImageMapSession extends \pChart\pDraw implements pImageMapInterface
{
	/* Image map */
	var $ImageMapIndex = "pChart"; // Name of the session array
	var $ImageMapBuffer = [];
	var $UniqueID = "imageMap";

	/* Class constructor */
	function __construct(int $XSize, int $YSize, bool $TransparentBackground = FALSE, $Name = "pChart", string $UniqueID = "imageMap")
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			throw \pChart\pException::ImageMapSessionNotStarted();
		}

		/* Initialize the image map methods */
		$this->ImageMapIndex = $Name;
		$this->UniqueID = $UniqueID;

		/* Initialize the parent */
		parent::__construct($XSize, $YSize, $TransparentBackground);
	}

	function __destruct()
	{
		$_SESSION[$this->ImageMapIndex][$this->UniqueID] = $this->ImageMapBuffer;

		parent::__destruct();
	}

	/* does the image map already exist */
	function ImageMapExists()
	{
		if (isset($_SESSION[$this->ImageMapIndex])){
			if (isset($_SESSION[$this->ImageMapIndex][$this->UniqueID])){
				return TRUE;
			}
		}

		return FALSE;
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
		if (isset($_SESSION[$this->ImageMapIndex][$this->UniqueID])){

			echo json_encode($_SESSION[$this->ImageMapIndex][$this->UniqueID]);

		} else {
			throw \pChart\pException::ImageMapInvalidID("ImageMap index ".$this->ImageMapIndex. " does not exist in session storage!");
		}

		/* When the image map is returned to the client, the script ends */
		exit();
	}

}

?>