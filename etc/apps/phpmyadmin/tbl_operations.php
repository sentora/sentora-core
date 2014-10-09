<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package PhpMyAdmin
 */

/**
 *
 */
require_once 'libraries/common.inc.php';

/**
 * functions implementation for this script
 */
require_once 'libraries/operations.lib.php';

$pma_table = new PMA_Table($GLOBALS['table'], $GLOBALS['db']);
$response = PMA_Response::getInstance();

/**
 * Runs common work
 */
require 'libraries/tbl_common.inc.php';
$url_query .= '&amp;goto=tbl_operations.php&amp;back=tbl_operations.php';
$url_params['goto'] = $url_params['back'] = 'tbl_operations.php';

/**
 * Gets relation settings
 */
$cfgRelation = PMA_getRelationsParam();

/**
 * Gets available MySQL charsets and storage engines
 */
require_once 'libraries/mysql_charsets.lib.php';
require_once 'libraries/StorageEngine.class.php';

/**
 * Class for partition management
 */
require_once 'libraries/Partition.class.php';

// reselect current db (needed in some cases probably due to
// the calling of relation.lib.php)
PMA_DBI_select_db($GLOBALS['db']);

/**
 * Gets tables informations
 */
require 'libraries/tbl_info.inc.php';

// define some variables here, for improved syntax in the conditionals
$is_myisam_or_aria = $is_isam = $is_innodb = $is_berkeleydb = $is_aria = $is_pbxt = false;
// set initial value of these variables, based on the current table engine
list($is_myisam_or_aria, $is_innodb, $is_isam,
    $is_berkeleydb, $is_aria, $is_pbxt
) = PMA_setGlobalVariablesForEngine($tbl_storage_engine);

if ($is_aria) {
    // the value for transactional can be implicit
    // (no create option found, in this case it means 1)
    // or explicit (option found with a value of 0 or 1)
    // ($transactional may have been set by libraries/tbl_info.inc.php,
    // from the $create_options)
    $transactional = (isset($transactional) && $transactional == '0')
        ? '0'
        : '1';
    $page_checksum = (isset($page_checksum)) ? $page_checksum : '';
}

$reread_info = false;
$table_alters = array();

/**
 * If the table has to be moved to some other database
 */
if (isset($_REQUEST['submit_move']) || isset($_REQUEST['submit_copy'])) {
    $_message = '';
    include_once 'tbl_move_copy.php';
}
/**
 * If the table has to be maintained
 */
if (isset($_REQUEST['table_maintenance'])) {
    include_once 'sql.php';
    unset($result);
}
/**
 * Updates table comment, type and options if required
 */
if (isset($_REQUEST['submitoptions'])) {
    $_message = '';
    $warning_messages = array();

    if (isset($_REQUEST['new_name'])) {
        if ($pma_table->rename($_REQUEST['new_name'])) {
            $_message .= $pma_table->getLastMessage();
            $result = true;
            $GLOBALS['table'] = $pma_table->getName();
            $reread_info = true;
            $reload = true;
        } else {
            $_message .= $pma_table->getLastError();
            $result = false;
        }
    }

    if (! empty($_REQUEST['new_tbl_storage_engine'])
        && strtolower($_REQUEST['new_tbl_storage_engine'])
            !== strtolower($tbl_storage_engine)
    ) {
        $new_tbl_storage_engine = $_REQUEST['new_tbl_storage_engine'];
        // reset the globals for the new engine
        list($is_myisam_or_aria, $is_innodb, $is_isam,
            $is_berkeleydb, $is_aria, $is_pbxt
        ) = PMA_setGlobalVariablesForEngine($new_tbl_storage_engine);

        if ($is_aria) {
            $transactional = (isset($transactional) && $transactional == '0')
                ? '0'
                : '1';
            $page_checksum = (isset($page_checksum)) ? $page_checksum : '';
        }
    } else {
        $new_tbl_storage_engine = '';
    }

    $table_alters = PMA_getTableAltersArray(
        $is_myisam_or_aria, $is_isam, $pack_keys,
        (empty($checksum) ? '0' : '1'),
        $is_aria,
        ((isset($page_checksum)) ? $page_checksum : ''),
        (empty($delay_key_write) ? '0' : '1'),
        $is_innodb, $is_pbxt, $row_format,
        $new_tbl_storage_engine,
        ((isset($transactional) && $transactional == '0') ? '0' : '1'),
        $tbl_collation
    );

    if (count($table_alters) > 0) {
        $sql_query      = 'ALTER TABLE '
            . PMA_Util::backquote($GLOBALS['table']);
        $sql_query     .= "\r\n" . implode("\r\n", $table_alters);
        $sql_query     .= ';';
        $result        .= PMA_DBI_query($sql_query) ? true : false;
        $reread_info    = true;
        unset($table_alters);
        $warning_messages = PMA_getWarningMessagesArray();
    }
}
/**
 * Reordering the table has been requested by the user
 */
if (isset($_REQUEST['submitorderby']) && ! empty($_REQUEST['order_field'])) {
    list($sql_query, $result) = PMA_getQueryAndResultForReorderingTable();
} // end if

/**
 * A partition operation has been requested by the user
 */
if (isset($_REQUEST['submit_partition'])
    && ! empty($_REQUEST['partition_operation'])
) {
    list($sql_query, $result) = PMA_getQueryAndResultForPartition();
} // end if

if ($reread_info) {
    // to avoid showing the old value (for example the AUTO_INCREMENT) after
    // a change, clear the cache
    PMA_Table::$cache = array();
    $page_checksum = $checksum = $delay_key_write = 0;
    include 'libraries/tbl_info.inc.php';
}
unset($reread_info);

if (isset($result) && empty($message_to_show)) {
    // set to success by default, because result set could be empty
    // (for example, a table rename)
    $_type = 'success';
    if (empty($_message)) {
        $_message = $result
            ? PMA_Message::success(
                __('Your SQL query has been executed successfully')
            )
            : PMA_Message::error(__('Error'));
        // $result should exist, regardless of $_message
        $_type = $result ? 'success' : 'error';

        if (isset($GLOBALS['ajax_request'])
            && $GLOBALS['ajax_request'] == true
        ) {
            $response = PMA_Response::getInstance();
            $response->isSuccess($_message->isSuccess());
            $response->addJSON('message', $_message);
            $response->addJSON(
                'sql_query', PMA_Util::getMessage(null, $sql_query)
            );
            exit;
        }
    }
    if (! empty($warning_messages)) {
        $_message = new PMA_Message;
        $_message->addMessages($warning_messages);
        $_message->isError(true);
        if ($GLOBALS['ajax_request'] == true) {
            $response = PMA_Response::getInstance();
            $response->isSuccess(false);
            $response->addJSON('message', $_message);
            exit;
        }
        unset($warning_messages);
    }

    $response->addHTML(
        PMA_Util::getMessage($_message, $sql_query, $_type)
    );
    unset($_message, $_type);
}

$url_params['goto']
    = $url_params['back']
        = 'tbl_operations.php';

/**
 * Get columns names
 */
$columns = PMA_DBI_get_columns($GLOBALS['db'], $GLOBALS['table']);

/**
 * Displays the page
 */
/**
 * Order the table
 */
$hideOrderTable = false;
// `ALTER TABLE ORDER BY` does not make sense for InnoDB tables that contain
// a user-defined clustered index (PRIMARY KEY or NOT NULL UNIQUE index).
// InnoDB always orders table rows according to such an index if one is present.
if ($tbl_storage_engine == 'INNODB') {
    include_once 'libraries/Index.class.php';
    $indexes = PMA_Index::getFromTable($GLOBALS['table'], $GLOBALS['db']);
    foreach ($indexes as $name => $idx) {
        if ($name == 'PRIMARY') {
            $hideOrderTable = true;
            break;
        } elseif (! $idx->getNonUnique()) {
            $notNull = true;
            foreach ($idx->getColumns() as $column) {
                if ($column->getNull()) {
                    $notNull = false;
                    break;
                }
            }
            if ($notNull) {
                $hideOrderTable = true;
                break;
            }
        }
    }
}
if (! $hideOrderTable) {
    $response->addHTML(PMA_getHtmlForOrderTheTable($columns));
}

/**
 * Move table
 */
$response->addHTML(PMA_getHtmlForMoveTable());

if (strstr($show_comment, '; InnoDB free') === false) {
    if (strstr($show_comment, 'InnoDB free') === false) {
        // only user entered comment
        $comment = $show_comment;
    } else {
        // here we have just InnoDB generated part
        $comment = '';
    }
} else {
    // remove InnoDB comment from end, just the minimal part (*? is non greedy)
    $comment = preg_replace('@; InnoDB free:.*?$@', '', $show_comment);
}

// PACK_KEYS: MyISAM or ISAM
// DELAY_KEY_WRITE, CHECKSUM, : MyISAM only
// AUTO_INCREMENT: MyISAM and InnoDB since 5.0.3, PBXT

// Here should be version check for InnoDB, however it is supported
// in >5.0.4, >4.1.12 and >4.0.11, so I decided not to
// check for version

$response->addHTML(
    PMA_getTableOptionDiv(
        $comment, $tbl_collation, $tbl_storage_engine,
        $is_myisam_or_aria, $is_isam, $pack_keys,
        $auto_increment,
        (empty($delay_key_write) ? '0' : '1'),
        ((isset($transactional) && $transactional == '0') ? '0' : '1'),
        ((isset($page_checksum)) ? $page_checksum : ''),
        $is_innodb, $is_pbxt, $is_aria, (empty($checksum) ? '0' : '1')
    )
);

/**
 * Copy table
 */
$response->addHTML(PMA_getHtmlForCopytable());

$response->addHTML('<br class="clearfloat"/>');

/**
 * Table maintenance
 */
$response->addHTML(
    PMA_getHtmlForTableMaintenance(
        $is_myisam_or_aria,
        $is_innodb,
        $is_berkeleydb,
        $url_params
    )
);

if (! (isset($db_is_information_schema) && $db_is_information_schema)) {
    $truncate_table_url_params = array();
    $drop_table_url_params = array();

    if (! $tbl_is_view
        && ! (isset($db_is_information_schema) && $db_is_information_schema)
    ) {
        $this_sql_query = 'TRUNCATE TABLE '
            . PMA_Util::backquote($GLOBALS['table']);
        $truncate_table_url_params = array_merge(
            $url_params,
            array(
                'sql_query' => $this_sql_query,
                'goto' => 'tbl_structure.php',
                'reload' => '1',
                'message_to_show' => sprintf(
                    __('Table %s has been emptied'),
                    htmlspecialchars($table)
                ),
            )
        );
    }
    if (! (isset($db_is_information_schema) && $db_is_information_schema)) {
        $this_sql_query = 'DROP TABLE '
            . PMA_Util::backquote($GLOBALS['table']);
        $drop_table_url_params = array_merge(
            $url_params,
            array(
                'sql_query' => $this_sql_query,
                'goto' => 'db_operations.php',
                'reload' => '1',
                'purge' => '1',
                'message_to_show' => sprintf(
                    ($tbl_is_view
                        ? __('View %s has been dropped')
                        : __('Table %s has been dropped')
                    ),
                    htmlspecialchars($table)
                ),
                // table name is needed to avoid running
                // PMA_relationsCleanupDatabase() on the whole db later
                'table' => $GLOBALS['table'],
            )
        );
    }
    $response->addHTML(
        PMA_getHtmlForDeleteDataOrTable(
            $truncate_table_url_params,
            $drop_table_url_params
        )
    );
}
$response->addHTML('<br class="clearfloat">');

if (PMA_Partition::havePartitioning()) {
    $partition_names = PMA_Partition::getPartitionNames($db, $table);
    // show the Partition maintenance section only if we detect a partition
    if (! is_null($partition_names[0])) {
        $response->addHTML(
            PMA_getHtmlForPartitionMaintenance($partition_names, $url_params)
        );
    } // end if
} // end if
unset($partition_names);

// Referential integrity check
// The Referential integrity check was intended for the non-InnoDB
// tables for which the relations are defined in pmadb
// so I assume that if the current table is InnoDB, I don't display
// this choice (InnoDB maintains integrity by itself)

if ($cfgRelation['relwork'] && ! $is_innodb) {
    PMA_DBI_select_db($GLOBALS['db']);
    $foreign = PMA_getForeigners($GLOBALS['db'], $GLOBALS['table']);

    if ($foreign) {
        $response->addHTML(
            PMA_getHtmlForReferentialIntegrityCheck($foreign, $url_params)
        );
    } // end if ($foreign)

} // end  if (!empty($cfg['Server']['relation']))

?>
