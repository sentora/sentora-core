<?php

###############################################
# PHPMyAdmin Configuration for Sentora        #
# Last updated: 03/07/2014                    #
# Author: Bobby Allen (ballen@sentora.io)     #
###############################################

/*
 * This is needed for cookie based authentication to encrypt password in
 * cookie
 */
$cfg['blowfish_secret'] = 'SENTORA';

/*
 * Servers configuration
 */
$i = 0;
$i++;
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'cookie';
/* Server parameters */
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['extension'] = 'mysqli';

/*
 * Directories for saving/loading files from server
 */
$cfg['UploadDir'] = '../tmp/';
$cfg['SaveDir'] = '../tmp/';

/* rajk - for blobstreaming */
$cfg['Servers'][$i]['bs_garbage_threshold'] = 50;
$cfg['Servers'][$i]['bs_repository_threshold'] = '32M';
$cfg['Servers'][$i]['bs_temp_blob_timeout'] = 600;
$cfg['Servers'][$i]['bs_temp_log_threshold'] = '32M';

/*
 * Sentora specific changes
 */
$cfg['ShowCreateDb'] = false;
$cfg['ShowChgPassword'] = false;
$cfg['AllowUserDropDatabase'] = false;
$cfg['SuhosinDisableWarning'] = true;
$cfg['PmaNoRelation_DisableWarning'] = true;
$cfg['Servers'][$i]['hide_db'] = 'information_schema';
$cfg['ShowServerInfo'] = false;

/*
 * You can find more configuration options in Documentation.html
 * or here: http://wiki.phpmyadmin.net/pma/Config
 */
?>
