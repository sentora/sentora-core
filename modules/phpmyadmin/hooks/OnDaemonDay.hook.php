<?php
/**
 * OnDaemonDay.hook.php
 * Checks for updates for phpMyAdmin daily and commits them if required
 * @author VJ Patel (meetthevj@gmail.com - VJftw @ ZPanel Forums)
**/
	
function phpMyAdminUpdate() {
	$zpanelRoot = ctrl_options::GetSystemOption('zpanel_root');
	$tempDir = ctrl_options::GetSystemOption('temp_dir');
	$phpMyAdminDir = $zpanelRoot.fs_director::ConvertSlashes("etc/apps/phpmyadmin/");
	
	// Open the phpMyAdmin Directory and search for the RELEASE-DATE-#.#.# file
	$phpMyAdminDirHandle = opendir($phpMyAdminDir);
	while ((false !== ($file = readdir($phpMyAdminDirHandle))) && empty($match)) 
	{
		preg_match("/RELEASE-DATE-\d[.]\d[.]\d/", $file, $match);
	}
	
	if (!empty($match[0]))
	{
		$filePieces = explode('-', $match[0]);
		$phpMyAdminVersionCurrent = $filePieces[2];
		echo "Current phpMyAdmin Version: \t".$phpMyAdminVersionCurrent.fs_filehandler::NewLine();
	}
	
	// Determine the most up to date version of phpMyAdmin
	$phpMyAdminDownloadsPage = file_get_contents("http://www.phpmyadmin.net/home_page/downloads.php");
	preg_match("/phpMyAdmin \d[.]\d[.]\d/", $phpMyAdminDownloadsPage, $match);
	if (!empty($match[0]))
	{
		$stringPieces = explode(' ', $match[0]);
		$phpMyAdminVersionNew = $stringPieces[1];
		echo "Newest phpMyAdmin Version: \t".$phpMyAdminVersionNew.fs_filehandler::NewLine();
	}
	if (!isset($phpMyAdminVersionNew))
	{
		echo "Please notify VJftw @ ZPanel Forums: Newest phpMyAdmin Version not found.".fs_filehandler::NewLine();
	}
	// otherwise an update is possible. 
	else if (!(isset($phpMyAdminVersionCurrent)) || ($phpMyAdminVersionNew != $phpMyAdminVersionCurrent))
	{
		echo "\tBacking up phpMyAdmin Configuration.".fs_filehandler::NewLine();
		// backup the config file
		$phpMyAdminConfig = file_get_contents($phpMyAdminDir."config.inc.php");
		
		echo "\tDownloading new phpMyAdmin.".fs_filehandler::NewLine();
		// download the file from SourceForge
		file_put_contents($tempDir."phpMyAdmin.zip", file_get_contents("http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/".$phpMyAdminVersionNew."/phpMyAdmin-".$phpMyAdminVersionNew."-all-languages.zip/download"));
		
		echo "\tExtracting phpMyAdmin.".fs_filehandler::NewLine();
		$phpMyAdminZip = new ZipArchive;
		$res = $phpMyAdminZip->open($tempDir."phpMyAdmin.zip");
		if ($res === TRUE) {
			$phpMyAdminZip->extractTo($tempDir.fs_director::ConvertSlashes("phpMyAdmin/"));
			$phpMyAdminZip->close();
		}
		
		echo "\tRemoving old phpMyAdmin.".fs_filehandler::NewLine();
		fs_filehandler::RemoveDirectoryContents($phpMyAdminDir);
		
		echo "\tCopying new phpMyAdmin.".fs_filehandler::NewLine();
		$handle = opendir($tempDir.fs_director::ConvertSlashes("phpMyAdmin/"));
		while (false !== ($entry = readdir($handle))) 
		{
			if ($entry != "." && $entry != "..")
				$phpMyAdminSrc = $entry;
		}
		closedir($handle);
		fs_filehandler::CopyDirectoryContents($tempDir.fs_director::ConvertSlashes("phpMyAdmin/").$phpMyAdminSrc, $phpMyAdminDir);
		fs_filehandler::RemoveDirectoryContents($tempDir.fs_director::ConvertSlashes("phpMyAdmin/"));
		rmdir($tempDir.fs_director::ConvertSlashes("phpMyAdmin/"));
		unlink($tempDir."phpMyAdmin.zip");
		
		echo "\tRewrite configuration.".fs_filehandler::NewLine();
		file_put_contents($phpMyAdminDir."config.inc.php", $phpMyAdminConfig);
		
		echo "\tDone.".fs_filehandler::NewLine();
	}
	else
	{
		echo "No Update required.".fs_filehandler::NewLine();
	}
}
	
/**
	Start Maintenance
*/
echo fs_filehandler::NewLine() . "phpMyAdmin Maintenance" . fs_filehandler::NewLine();
echo "Checking for phpMyAdmin Update." . fs_filehandler::NewLine();
phpMyAdminUpdate();
echo "End of phpMyAdmin Maintenance" . fs_filehandler::NewLine();