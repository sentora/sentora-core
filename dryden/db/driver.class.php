<?php

/**
 * Database access class, enables PDO database access.
 *
 * @package zpanelx
 * @subpackage dryden -> db
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */

class db_driver extends PDO {
	/** Do nothing, we are simply using the PHP PDO SPL!
         * This class exists so we can extend in future if required.
         * http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
         */
}

?>