<?php

/**
 * Initiates the database driver object and debug object and registers the $zdhb and $zlo globals for the framework.
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
global $zlo, $zdbh;

$zlo = new debug_logger();

try {
    $zdbh = new db_driver("mysql:host=$host;dbname=$dbname", $user, $pass);
    $zdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $zlo->method = "text";
    $zlo->logcode = "0100";
    $zlo->detail = "Unable to connect or authenticate against the database supplied!";
    $zlo->mextra = $e;
    $error_html = "<style type=\"text/css\"><!--
            .dbwarning {
                    font-family: Verdana, Geneva, sans-serif;
                    font-size: 14px;
                    color: #C00;
                    background-color: #FCC;
                    padding: 30px;
                    border: 1px solid #C00;
            }
            p {
                    font-size: 12px;
                    color: #666;
            }
            </style>
            <div class=\"dbwarning\"><strong>Critical Error:</strong> [0100] - Unable to connect or authenticate to the ZPanel database (<em>$dbname</em>).<p>We advice that you contact the server administator to ensure that the dataase server is online and that the correct connection parameter are being used.</p></div>";
    die($error_html);
}
?>
