<?php

/**
 * Database XAPI class, handles Inserting/Removing database objects.
 *
 * @package zpanelx
 * @subpackage dryden -> db
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */

class db_xapi {

	function cronjob_commit() {
	global $zdbh;

        //$sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx = ? WHERE so_name_vc = ?");
		//$sql->execute(array('C', 'windows_drive'));
		
		$sql = $zdbh->prepare( "SELECT * FROM x_cronjobs" );
		$sql->execute();
		fs_fh::ResetFile(ctrl_options::GetOption('cron_file'));
		
		while($row = $sql->fetch()) {
			$new_file  = "# CRON ID:" . $row['ct_id_pk'] . fs_fh::NewLine();
			$new_file .= "1 2 ".$row['ct_script_vc'].fs_fh::NewLine();
			$new_file .= "# END CRON ID:" . $row['ct_id_pk'] . fs_fh::NewLine();
			$fp = fopen(ctrl_options::GetOption('cron_file'),'a');
			fwrite($fp, $new_file);
			fclose($fp);
		}
	}

	function vhosts_commit() {
	global $zdbh;

		$sql = $zdbh->prepare( "SELECT * FROM x_vhosts" );
		$sql->execute();
		fs_fh::ResetFile(ctrl_options::GetOption('apache_vhosts'));
		
		$new_file  = "NameVirtualHost *:".ctrl_options::GetOption('apache_port'). fs_fh::NewLine();
		$new_file .= fs_fh::NewLine();
		$new_file .= "#Configuration for ZPanel control panel.".fs_fh::NewLine();
		$new_file .= "<VirtualHost *:".ctrl_options::GetOption('apache_port').">".fs_fh::NewLine();
		$new_file .= "ServerAdmin zadmin@ztest.com".fs_fh::NewLine();
    	$new_file .= "DocumentRoot \"".ctrl_options::GetOption('zpanel_root')."\"".fs_fh::NewLine();
    	$new_file .= "ServerName zpanel.ztest.com".fs_fh::NewLine();
    	$new_file .= "ServerAlias *.zpanel.ztest.com".fs_fh::NewLine();
		$new_file .= "AddType application/x-httpd-php .php".fs_fh::NewLine();
		$new_file .= "<Directory \"C:/ZPanel/panel\">".fs_fh::NewLine();
		$new_file .= "Options FollowSymLinks".fs_fh::NewLine();
    	$new_file .= "AllowOverride All".fs_fh::NewLine();
    	$new_file .= "Order allow,deny".fs_fh::NewLine();
    	$new_file .= "Allow from all".fs_fh::NewLine();
		$new_file .= "</Directory>".fs_fh::NewLine();
		$new_file .= "</VirtualHost>".fs_fh::NewLine();
		$new_file .= fs_fh::NewLine();
		$new_file .= "################################################################".fs_fh::NewLine();
		$new_file .= "# ZPanel generated VHOST configurations below.....".fs_fh::NewLine();
		$new_file .= "################################################################".fs_fh::NewLine();
		$new_file .= fs_fh::NewLine();
		$fp = fopen(ctrl_options::GetOption('apache_vhosts'),'a');
		fwrite($fp, $new_file);
		fclose($fp);
		
		while($row = $sql->fetch()) {
			$new_file  = "# DOMAIN: ".$row['vh_name_vc'].fs_fh::NewLine();
			$new_file .= "<virtualhost *:".ctrl_options::GetOption('apache_port').">".fs_fh::NewLine();
			$new_file .= "ServerName ".$row['vh_name_vc']."\r\n";
			$new_file .= "ServerAlias ".$row['vh_name_vc']." www.".$row['vh_name_vc'].fs_fh::NewLine();
			$new_file .= "ServerAdmin ".$admin_email.fs_fh::NewLine();
			$new_file .= "DocumentRoot \"".$row['vh_directory_vc']."\"".fs_fh::NewLine();
			$new_file .= "php_admin_value open_basedir \"".ctrl_options::GetOption('hosted_dir').":".ctrl_options::GetOption('temp_dir')."\"".fs_fh::NewLine();
			$new_file .= "php_admin_value upload_tmp_dir \"".ctrl_options::GetOption('temp_dir')."\"".fs_fh::NewLine();
			$new_file .= "ErrorLog \"".ctrl_options::GetOption('logfile_dir').$row['vh_name_vc']."-error.log\"".fs_fh::NewLine();
			$new_file .= "CustomLog \"".ctrl_options::GetOption('logfile_dir').$row['vh_name_vc']."-access.log\" common".fs_fh::NewLine();
			$new_file .= "CustomLog \"".ctrl_options::GetOption('logfile_dir').$row['vh_name_vc']."-bandwidth.log\" common".fs_fh::NewLine();
			$new_file .= "<Directory />".fs_fh::NewLine();
			$new_file .= "Options FollowSymLinks Indexes".fs_fh::NewLine();
			$new_file .= "AllowOverride All".fs_fh::NewLine();
			$new_file .= "Order Allow,Deny".fs_fh::NewLine();
			$new_file .= "Allow from all".fs_fh::NewLine();
			$new_file .= "</Directory>".fs_fh::NewLine();
			$new_file .= ctrl_options::GetOption('php_handler').fs_fh::NewLine();
			$new_file .= "ScriptAlias /cgi-bin/ \"".$row['vh_name_vc']."/_cgi-bin/\"".fs_fh::NewLine();
			$new_file .= "<location /cgi-bin>".fs_fh::NewLine();
			$new_file .= ctrl_options::GetOption('cgi_handler').fs_fh::NewLine();
			$new_file .= "Options ExecCGI -Indexes".fs_fh::NewLine();
			$new_file .= "</location>".fs_fh::NewLine();
			$new_file .= ctrl_options::GetOption('error_pages').fs_fh::NewLine();
			//$new_file .= "$extra".fs_fh::NewLine(); #TO ADD CUSTOM ENRIES HERE
			$new_file .= "".ctrl_options::GetOption('directory_index').fs_fh::NewLine();
			$new_file .= "</virtualhost>".fs_fh::NewLine();
			$new_file .= "# END DOMAIN:".$row['vh_name_vc'].fs_fh::NewLine();
			$fp = fopen(ctrl_options::GetOption('apache_vhosts'),'a');
			fwrite($fp, $new_file);
			fclose($fp);
		}
	}


}

?>