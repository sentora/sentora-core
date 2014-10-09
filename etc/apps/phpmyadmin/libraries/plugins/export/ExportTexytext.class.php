<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Export to Texy! text.
 *
 * @package    PhpMyAdmin-Export
 * @subpackage Texy!text
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/* Get the export interface */
require_once 'libraries/plugins/ExportPlugin.class.php';

/**
 * Handles the export for the Texy! text class
 *
 * @package    PhpMyAdmin-Export
 * @subpackage Texy!text
 */
class ExportTexytext extends ExportPlugin
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setProperties();
    }

    /**
     * Sets the export Texy! text properties
     *
     * @return void
     */
    protected function setProperties()
    {
        $props = 'libraries/properties/';
        include_once "$props/plugins/ExportPluginProperties.class.php";
        include_once "$props/options/groups/OptionsPropertyRootGroup.class.php";
        include_once "$props/options/groups/OptionsPropertyMainGroup.class.php";
        include_once "$props/options/items/RadioPropertyItem.class.php";
        include_once "$props/options/items/BoolPropertyItem.class.php";
        include_once "$props/options/items/TextPropertyItem.class.php";

        $exportPluginProperties = new ExportPluginProperties();
        $exportPluginProperties->setText('Texy! text');
        $exportPluginProperties->setExtension('txt');
        $exportPluginProperties->setMimeType('text/plain');
        $exportPluginProperties->setOptionsText(__('Options'));

        // create the root group that will be the options field for
        // $exportPluginProperties
        // this will be shown as "Format specific options"
        $exportSpecificOptions = new OptionsPropertyRootGroup();
        $exportSpecificOptions->setName("Format Specific Options");

        // what to dump (structure/data/both) main group
        $dumpWhat = new OptionsPropertyMainGroup();
        $dumpWhat->setName("general_opts");
        $dumpWhat->setText(__('Dump table'));
        // create primary items and add them to the group
        $leaf = new RadioPropertyItem();
        $leaf->setName("structure_or_data");
        $leaf->setValues(
            array(
                'structure' => __('structure'),
                'data' => __('data'),
                'structure_and_data' => __('structure and data')
            )
        );
        $dumpWhat->addProperty($leaf);
        // add the main group to the root group
        $exportSpecificOptions->addProperty($dumpWhat);

        // data options main group
        $dataOptions = new OptionsPropertyMainGroup();
        $dataOptions->setName("data");
        $dataOptions->setText(__('Data dump options'));
        $dataOptions->setForce('structure');
        // create primary items and add them to the group
        $leaf = new BoolPropertyItem();
        $leaf->setName("columns");
        $leaf->setText(__('Put columns names in the first row'));
        $dataOptions->addProperty($leaf);
        $leaf = new TextPropertyItem();
        $leaf->setName('null');
        $leaf->setText(__('Replace NULL with:'));
        $dataOptions->addProperty($leaf);
        // add the main group to the root group
        $exportSpecificOptions->addProperty($dataOptions);

        // set the options for the export plugin property item
        $exportPluginProperties->setOptions($exportSpecificOptions);
        $this->properties = $exportPluginProperties;
    }

    /**
     * This method is called when any PluginManager to which the observer
     * is attached calls PluginManager::notify()
     *
     * @param SplSubject $subject The PluginManager notifying the observer
     *                            of an update.
     *
     * @return void
     */
    public function update (SplSubject $subject)
    {
    }

    /**
     * Outputs export header
     *
     * @return bool Whether it succeeded
     */
    public function exportHeader ()
    {
        return true;
    }

    /**
     * Outputs export footer
     *
     * @return bool Whether it succeeded
     */
    public function exportFooter ()
    {
        return true;
    }

    /**
     * Outputs database header
     *
     * @param string $db Database name
     *
     * @return bool Whether it succeeded
     */
    public function exportDBHeader ($db)
    {
        return PMA_exportOutputHandler(
            '===' . __('Database') . ' ' . $db . "\n\n"
        );
    }

    /**
     * Outputs database footer
     *
     * @param string $db Database name
     *
     * @return bool Whether it succeeded
     */
    public function exportDBFooter ($db)
    {
        return true;
    }

    /**
     * Outputs CREATE DATABASE statement
     *
     * @param string $db Database name
     *
     * @return bool Whether it succeeded
     */
    public function exportDBCreate($db)
    {
        return true;
    }
    /**
     * Outputs the content of a table in NHibernate format
     *
     * @param string $db        database name
     * @param string $table     table name
     * @param string $crlf      the end of line sequence
     * @param string $error_url the url to go back in case of error
     * @param string $sql_query SQL query for obtaining data
     *
     * @return bool Whether it succeeded
     */
    public function exportData($db, $table, $crlf, $error_url, $sql_query)
    {
        global $what;

        if (! PMA_exportOutputHandler(
            '== ' . __('Dumping data for table') . ' ' . $table . "\n\n"
        )) {
            return false;
        }

        // Gets the data from the database
        $result      = PMA_DBI_query($sql_query, null, PMA_DBI_QUERY_UNBUFFERED);
        $fields_cnt  = PMA_DBI_num_fields($result);

        // If required, get fields name at the first line
        if (isset($GLOBALS[$what . '_columns'])) {
            $text_output = "|------\n";
            for ($i = 0; $i < $fields_cnt; $i++) {
                $text_output .= '|'
                    . htmlspecialchars(
                        stripslashes(PMA_DBI_field_name($result, $i))
                    );
            } // end for
            $text_output .= "\n|------\n";
            if (! PMA_exportOutputHandler($text_output)) {
                return false;
            }
        } // end if

        // Format the data
        while ($row = PMA_DBI_fetch_row($result)) {
            $text_output = '';
            for ($j = 0; $j < $fields_cnt; $j++) {
                if (! isset($row[$j]) || is_null($row[$j])) {
                    $value = $GLOBALS[$what . '_null'];
                } elseif ($row[$j] == '0' || $row[$j] != '') {
                    $value = $row[$j];
                } else {
                    $value = ' ';
                }
                $text_output .= '|'
                    . str_replace(
                        '|', '&#124;', htmlspecialchars($value)
                    );
            } // end for
            $text_output .= "\n";
            if (! PMA_exportOutputHandler($text_output)) {
                return false;
            }
        } // end while
        PMA_DBI_free_result($result);

        return true;
    }

    /**
     * Returns a stand-in CREATE definition to resolve view dependencies
     *
     * @param string $db   the database name
     * @param string $view the view name
     * @param string $crlf the end of line sequence
     *
     * @return string resulting definition
     */
    function getTableDefStandIn($db, $view, $crlf)
    {
        $text_output = '';

        /**
         * Get the unique keys in the table
         */
        $unique_keys = array();
        $keys = PMA_DBI_get_table_indexes($db, $view);
        foreach ($keys as $key) {
            if ($key['Non_unique'] == 0) {
                $unique_keys[] = $key['Column_name'];
            }
        }

        /**
         * Gets fields properties
         */
        PMA_DBI_select_db($db);

        /**
         * Displays the table structure
         */

        $text_output .= "|------\n"
            . '|' . __('Column')
            . '|' . __('Type')
            . '|' . __('Null')
            . '|' . __('Default')
            . "\n|------\n";

        $columns = PMA_DBI_get_columns($db, $view);
        foreach ($columns as $column) {
            $text_output .= $this->formatOneColumnDefinition($column, $unique_keys);
            $text_output .= "\n";
        } // end foreach

        return $text_output;
    }

    /**
     * Returns $table's CREATE definition
     *
     * @param string $db            the database name
     * @param string $table         the table name
     * @param string $crlf          the end of line sequence
     * @param string $error_url     the url to go back in case of error
     * @param bool   $do_relation   whether to include relation comments
     * @param bool   $do_comments   whether to include the pmadb-style column
     *                                comments as comments in the structure;
     *                                this is deprecated but the parameter is
     *                                left here because export.php calls
     *                                $this->exportStructure() also for other
     *                                export types which use this parameter
     * @param bool   $do_mime       whether to include mime comments
     * @param bool   $show_dates    whether to include creation/update/check dates
     * @param bool   $add_semicolon whether to add semicolon and end-of-line
     *                              at the end
     * @param bool   $view          whether we're handling a view
     *
     * @return string resulting schema
     */
    function getTableDef(
        $db,
        $table,
        $crlf,
        $error_url,
        $do_relation,
        $do_comments,
        $do_mime,
        $show_dates = false,
        $add_semicolon = true,
        $view = false
    ) {
        global $cfgRelation;

        $text_output = '';

        /**
         * Get the unique keys in the table
         */
        $unique_keys = array();
        $keys        = PMA_DBI_get_table_indexes($db, $table);
        foreach ($keys as $key) {
            if ($key['Non_unique'] == 0) {
                $unique_keys[] = $key['Column_name'];
            }
        }

        /**
         * Gets fields properties
         */
        PMA_DBI_select_db($db);

        // Check if we can use Relations
        if ($do_relation && ! empty($cfgRelation['relation'])) {
            // Find which tables are related with the current one and write it in
            // an array
            $res_rel = PMA_getForeigners($db, $table);

            if ($res_rel && count($res_rel) > 0) {
                $have_rel = true;
            } else {
                $have_rel = false;
            }
        } else {
               $have_rel = false;
        } // end if

        /**
         * Displays the table structure
         */

        $columns_cnt = 4;
        if ($do_relation && $have_rel) {
            $columns_cnt++;
        }
        if ($do_comments && $cfgRelation['commwork']) {
            $columns_cnt++;
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $columns_cnt++;
        }

        $text_output .= "|------\n";
        $text_output .= '|' . __('Column');
        $text_output .= '|' . __('Type');
        $text_output .= '|' . __('Null');
        $text_output .= '|' . __('Default');
        if ($do_relation && $have_rel) {
            $text_output .= '|' . __('Links to');
        }
        if ($do_comments) {
            $text_output .= '|' . __('Comments');
            $comments = PMA_getComments($db, $table);
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $text_output .= '|' . htmlspecialchars('MIME');
            $mime_map = PMA_getMIME($db, $table, true);
        }
        $text_output .= "\n|------\n";

        $columns = PMA_DBI_get_columns($db, $table);
        foreach ($columns as $column) {
            $text_output .= $this->formatOneColumnDefinition($column, $unique_keys);
            $field_name = $column['Field'];

            if ($do_relation && $have_rel) {
                $text_output .= '|'
                    . (isset($res_rel[$field_name])
                    ? htmlspecialchars(
                        $res_rel[$field_name]['foreign_table']
                        . ' (' . $res_rel[$field_name]['foreign_field'] . ')'
                    )
                    : '');
            }
            if ($do_comments && $cfgRelation['commwork']) {
                $text_output .= '|'
                    . (isset($comments[$field_name])
                    ? htmlspecialchars($comments[$field_name])
                    : '');
            }
            if ($do_mime && $cfgRelation['mimework']) {
                $text_output .= '|'
                    . (isset($mime_map[$field_name])
                    ? htmlspecialchars(
                        str_replace('_', '/', $mime_map[$field_name]['mimetype'])
                    )
                    : '');
            }

            $text_output .= "\n";
        } // end foreach

        return $text_output;
    } // end of the '$this->getTableDef()' function

    /**
     * Outputs triggers
     *
     * @param string $db    database name
     * @param string $table table name
     *
     * @return string Formatted triggers list
     */
    function getTriggers($db, $table)
    {
        $text_output .= "|------\n";
        $text_output .= '|' . __('Column');
        $dump = "|------\n";
        $dump .= '|' . __('Name');
        $dump .= '|' . __('Time');
        $dump .= '|' . __('Event');
        $dump .= '|' . __('Definition');
        $dump .= "\n|------\n";

        $triggers = PMA_DBI_get_triggers($db, $table);

        foreach ($triggers as $trigger) {
            $dump .= '|' . $trigger['name'];
            $dump .= '|' . $trigger['action_timing'];
            $dump .= '|' . $trigger['event_manipulation'];
            $dump .= '|' .
                str_replace(
                    '|',
                    '&#124;',
                    htmlspecialchars($trigger['definition'])
                );
            $dump .= "\n";
        }

        return $dump;
    }

    /**
     * Outputs table's structure
     *
     * @param string $db          database name
     * @param string $table       table name
     * @param string $crlf        the end of line sequence
     * @param string $error_url   the url to go back in case of error
     * @param string $export_mode 'create_table', 'triggers', 'create_view',
     *                            'stand_in'
     * @param string $export_type 'server', 'database', 'table'
     * @param bool   $do_relation whether to include relation comments
     * @param bool   $do_comments whether to include the pmadb-style column
     *                                comments as comments in the structure;
     *                                this is deprecated but the parameter is
     *                                left here because export.php calls
     *                                $this->exportStructure() also for other
     *                                export types which use this parameter
     * @param bool   $do_mime     whether to include mime comments
     * @param bool   $dates       whether to include creation/update/check dates
     *
     * @return bool Whether it succeeded
     */
    function exportStructure(
        $db,
        $table,
        $crlf,
        $error_url,
        $export_mode,
        $export_type,
        $do_relation = false,
        $do_comments = false,
        $do_mime = false,
        $dates = false
    ) {
        $dump = '';

        switch($export_mode) {
        case 'create_table':
            $dump .= '== ' . __('Table structure for table') . ' ' .$table . "\n\n";
            $dump .= $this->getTableDef(
                $db, $table, $crlf, $error_url, $do_relation, $do_comments,
                $do_mime, $dates
            );
            break;
        case 'triggers':
            $dump = '';
            $triggers = PMA_DBI_get_triggers($db, $table);
            if ($triggers) {
                $dump .= '== ' . __('Triggers') . ' ' .$table . "\n\n";
                $dump .= $this->getTriggers($db, $table);
            }
            break;
        case 'create_view':
            $dump .= '== ' . __('Structure for view') . ' ' .$table . "\n\n";
            $dump .= $this->getTableDef(
                $db, $table, $crlf, $error_url, $do_relation, $do_comments,
                $do_mime, $dates, true, true
            );
            break;
        case 'stand_in':
            $dump .=  '== ' . __('Stand-in structure for view')
                . ' ' .$table . "\n\n";
            // export a stand-in definition to resolve view dependencies
            $dump .= $this->getTableDefStandIn($db, $table, $crlf);
        } // end switch

        return PMA_exportOutputHandler($dump);
    }

    /**
     * Formats the definition for one column
     *
     * @param array $column      info about this column
     * @param array $unique_keys unique keys for this table
     *
     * @return string Formatted column definition
     */
    function formatOneColumnDefinition(
        $column, $unique_keys
    ) {
        $extracted_columnspec
            = PMA_Util::extractColumnSpec($column['Type']);
        $type = $extracted_columnspec['print_type'];
        if (empty($type)) {
            $type     = '&nbsp;';
        }

        if (! isset($column['Default'])) {
            if ($column['Null'] != 'NO') {
                $column['Default'] = 'NULL';
            }
        }

        $fmt_pre = '';
        $fmt_post = '';
        if (in_array($column['Field'], $unique_keys)) {
            $fmt_pre = '**' . $fmt_pre;
            $fmt_post = $fmt_post . '**';
        }
        if ($column['Key']=='PRI') {
            $fmt_pre = '//' . $fmt_pre;
            $fmt_post = $fmt_post . '//';
        }
        $definition = '|'
            . $fmt_pre . htmlspecialchars($column['Field']) . $fmt_post;
        $definition .= '|' . htmlspecialchars($type);
        $definition .= '|'
            . (($column['Null'] == '' || $column['Null'] == 'NO')
            ? __('No') : __('Yes'));
        $definition .= '|'
            . htmlspecialchars(
                isset($column['Default']) ? $column['Default'] : ''
            );
        return $definition;
    }
}
?>