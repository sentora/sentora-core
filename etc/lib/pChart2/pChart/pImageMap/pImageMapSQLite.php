<?php
/*
pImageMapSQLite - pChart core class

Version     : 0.3
Made by     : Momchil Bozhinov
Last Update : 30/08/2019
*/

namespace pChart\pImageMap;

use pChart\pSQLite;

class pImageMapSQLite extends \pChart\pDraw implements pImageMapInterface
{
	/* Image map */
	var $SQLite;
	var $DbTable;
	var $ImageMapBuffer = [];

	/* Class constructor */
	function __construct(int $XSize, int $YSize, bool $TransparentBackground = FALSE, string $UniqueID = "imageMap", string $StorageFile = "temp/imageMap.db")
	{
		/* Create the cache Db */
		$this->SQLite = new pSQLite($StorageFile);
		$this->DbTable = $this->SQLite->quote($UniqueID);
		$this->SQLite->execute("CREATE TABLE IF NOT EXISTS ".$this->DbTable." (Type TEXT, Plots BLOB, Color TEXT, Title TEXT, Message TEXT);");

		/* Initialize the parent */
		parent::__construct($XSize, $YSize, $TransparentBackground);
	}

	function __destruct()
	{
		if (!empty($this->ImageMapBuffer)){

			/* flush existing image map */
			$this->SQLite->execute("DELETE FROM ".$this->DbTable.";");

			/* store the new image map */
			$params = [];

			foreach($this->ImageMapBuffer as $entry){
				$params[] = [
					"Type" => [$entry[0], 1],
					"Plots" => [$entry[1], 1],
					"Color" => [$entry[2], 1],
					"Title" => [$entry[3], 1],
					"Message" => [$entry[4], 1]
				];
			}

			$this->SQLite->execute("INSERT INTO ".$this->DbTable." VALUES(:Type, :Plots, :Color, :Title, :Message)", $params);
		}

		parent::__destruct();
	}

	function ImageMapExists()
	{
		$match = $this->SQLite->execute("SELECT \"Type\" FROM ".$this->DbTable.";", [], $expects_return = TRUE, $select_one = TRUE);
		return (!empty($match));
	}

	/* Add a zone to the image map */
	function addToImageMap(string $Type, string $Plots, string $Color = "", string $Title = "", string $Message = "", bool $HTMLEncode = FALSE)
	{
		/* Encode the characters in the image map in HTML standards */
		$Title = str_replace("&#8364;", "\u20AC", $Title); # Momchil TODO TEST THIS
		$Title = htmlentities($Title, ENT_QUOTES);

		if ($HTMLEncode) {
			$Message = htmlentities($Message, ENT_QUOTES);
		}

		$this->ImageMapBuffer[] = [$Type,$Plots,$Color,$Title,$Message];
	}

	/* Remove VOID values from an image map custom values array */
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

	private function formatOutput(array $buffer)
	{
		$ret = [];

		foreach($buffer as $array) {
			$ret[] = array_values($array);
		}

		return $ret;
	}

	/* Dump the image map */
	/* Momchil: this function relies on the fact that the ImageMap for the image already exists */
	function dumpImageMap()
	{
		$match = $this->SQLite->execute("SELECT * FROM ".$this->DbTable.";", [], $expects_return = TRUE, $select_one = FALSE);
		echo json_encode($this->formatOutput($match));
	
		/* When the image map is returned to the client, the script ends */
		exit();
	}

}

?>