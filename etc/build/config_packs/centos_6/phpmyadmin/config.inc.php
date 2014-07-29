<?php

###############################################
# PHPMyAdmin Configuration for ZPanel #
# Last updated: 26/03/2011 #
# Author: Bobby Allen (ballen@zpanel.co.uk) #
###############################################

$i = 0;
$i++;
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'http';
/* Server parameters */
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['ShowCreateDb'] = FALSE;
$cfg['ShowChgPassword'] = FALSE;
$cfg['AllowUserDropDatabase'] = FALSE;

/* rajk - for blobstreaming */
$cfg['Servers'][$i]['bs_garbage_threshold'] = 50;
$cfg['Servers'][$i]['bs_repository_threshold'] = '32M';
$cfg['Servers'][$i]['bs_temp_blob_timeout'] = 600;
$cfg['Servers'][$i]['bs_temp_log_threshold'] = '32M';

/* ZPanel specific changes */
$cfg['UploadDir'] = '/var/zpanel/temp/';
$cfg['SaveDir'] = '/var/zpanel/temp/';
$cfg['SuhosinDisableWarning'] = true;
$cfg['PmaNoRelation_DisableWarning'] = true;
$cfg['Servers'][$i]['hide_db'] = 'information_schema';
$cfg['ShowServerInfo'] = false;
?>
