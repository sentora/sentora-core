<?php

$rcmail_config = array();
$rcmail_config['db_dsnw'] = 'mysql://root:YOUR_MYSQL_ROOT_PASSWORD@localhost/zpanel_roundcube';
$rcmail_config['db_dsnr'] = '';
$rcmail_config['db_max_length'] = 512000;
$rcmail_config['db_persistent'] = FALSE;
$rcmail_config['db_table_users'] = 'users';
$rcmail_config['db_table_identities'] = 'identities';
$rcmail_config['db_table_contacts'] = 'contacts';
$rcmail_config['db_table_contactgroups'] = 'contactgroups';
$rcmail_config['db_table_contactgroupmembers'] = 'contactgroupmembers';
$rcmail_config['db_table_session'] = 'session';
$rcmail_config['db_table_cache'] = 'cache';
$rcmail_config['db_table_cache_index'] = 'cache_index';
$rcmail_config['db_table_cache_thread'] = 'cache_thread';
$rcmail_config['db_table_cache_messages'] = 'cache_messages';
$rcmail_config['db_sequence_users'] = 'user_ids';
$rcmail_config['db_sequence_identities'] = 'identity_ids';
$rcmail_config['db_sequence_contacts'] = 'contact_ids';
$rcmail_config['db_sequence_contactgroups'] = 'contactgroups_ids';
$rcmail_config['db_sequence_cache'] = 'cache_ids';
$rcmail_config['db_sequence_searches'] = 'search_ids';
?>
