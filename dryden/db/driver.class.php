<?php

/**
 * Database access class, enables PDO database access.
 * @package zpanelx
 * @subpackage dryden -> db
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class db_driver extends PDO {
    /** Do nothing, we are simply using the PHP PDO SPL!
     * This class exists so we can extend in future if required.
     * @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
     */
}

?>