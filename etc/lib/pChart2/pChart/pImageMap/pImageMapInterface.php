<?php
/*
pImageMapInterface

Version     : 0.1
Made by     : Momchil Bozhinov
Last Update : 10/01/2018

*/

namespace pChart\pImageMap;

interface pImageMapInterface
{
	/* Add a zone to the image map */
	function addToImageMap(string $Type, string $Plots, string $Color, string $Title, string $Message, bool $HTMLEncode);

	/* Remove VOID values from an imagemap custom values array */
	function stripFromSerie(string $SerieName, array $Values);

	/* Replace the title of one image map series */
	function replaceImageMapTitle(string $OldTitle, $NewTitle);

	/* Replace the values of the image map contents */
	function replaceImageMapValues(string $Title, array $Values);

	function ImageMapExists();

	function dumpImageMap();

}

?>