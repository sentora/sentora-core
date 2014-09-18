<?php

/* Local configuration for Roundcube Webmail */

$config = array();

// Database connection string (DSN) for read+write operations
// Format (compatible with PEAR MDB2): db_provider://user:password@host/database
// Currently supported db_providers: mysql, pgsql, sqlite, mssql or sqlsrv
// For examples see http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php
// NOTE: for SQLite use absolute path: 'sqlite:////full/path/to/sqlite.db?mode=0646'
$config['db_dsnw'] = 'mysql://root:YOUR_MYSQL_ROOT_PASSWORD@localhost/zpanel_roundcube';

// The mail host chosen to perform the log-in.
// Leave blank to show a textbox at login, give a list of hosts
// to display a pulldown menu or set one host as string.
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// Supported replacement variables:
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %s - domain name after the '@' from e-mail address provided at login screen
// For example %n = mail.domain.tld, %t = domain.tld
$config['default_host'] = 'localhost';

// SMTP server host (for sending mails).
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// If left blank, the PHP mail() function is used
// Supported replacement variables:
// %h - user's IMAP hostname
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %z - IMAP domain (IMAP hostname without the first part)
// For example %n = mail.domain.tld, %t = domain.tld
$config['smtp_server'] = 'localhost';

// SMTP port (default is 25; use 587 for STARTTLS or 465 for the
// deprecated SSL over SMTP (aka SMTPS))
$config['smtp_port'] = 25;

// SMTP username (if required) if you use %u as the username Roundcube
// will use the current username for login
$config['smtp_user'] = '%u';

// SMTP password (if required) if you use %p as the password Roundcube
// will use the current user's password for login
$config['smtp_pass'] = '%p';

// provide an URL where a user can get support for this Roundcube installation
// PLEASE DO NOT LINK TO THE ROUNDCUBE.NET WEBSITE HERE!
$config['support_url'] = '';

// use this folder to store log files (must be writeable for apache user)
// This is used by the 'file' log driver.
$config['log_dir'] = '/var/zpanel/logs/roundcube/';

// use this folder to store temp files (must be writeable for apache user)
$config['temp_dir'] = '/var/zpanel/temp';

// Name your service. This is displayed on the login screen and in the window title
$config['product_name'] = 'Sentora Webmail';

// Forces conversion of logins to lower case.
// 0 - disabled, 1 - only domain part, 2 - domain and local part.
// If users authentication is not case-sensitive this must be enabled.
// After enabling it all user records need to be updated, e.g. with query:
// UPDATE users SET username = LOWER(username);
$config['login_lc'] = 0;

// this key is used to encrypt the users imap password which is stored
// in the session record (and the client cookie if remember password is enabled).
// please provide a string of exactly 24 chars.
// YOUR KEY MUST BE DIFFERENT THAN THE SAMPLE VALUE FOR SECURITY REASONS
$config['des_key'] = 'rcmail-!24ByteDESkey*Str';

// List of active plugins (in plugins/ directory)
$config['plugins'] = array(
    'archive',
    'zipdownload',
	'managesieve',
);

# null == default
// mime magic database
$config['mime_magic'] = '/usr/share/misc/magic';

// skin name: folder from skins/
$config['skin'] = 'larry';

// give this choice of date formats to the user to select from
$config['date_formats'] = array('Y-m-d', 'd-m-Y', 'Y/m/d', 'm/d/Y', 'd/m/Y', 'd.m.Y', 'j.n.Y');

// automatically create the above listed default folders on first login
$config['create_default_folders'] = true;

// display remote inline images
// 0 - Never, always ask
// 1 - Ask if sender is not in address book
// 2 - Always show inline images
$config['show_images'] = 2;

// compose html formatted messages by default
// 0 - never, 1 - always, 2 - on reply to HTML message only 
$config['htmleditor'] = 1;

// save compose message every 300 seconds (5min)
$config['draft_autosave'] = 120;

// If true, after message delete/move, the next message will be displayed
$config['display_next'] = false;

// lifetime of message cache
// possible units: s, m, h, d, w
$config['message_cache_lifetime'] = '10d';
