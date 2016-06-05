<?php
/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * AJAX Handler to save Module Category block positions on dashboard page
 * @package zpanelx
 * @subpackage dryden -> ajax
 * @version 1.1.0
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

session_start();

/* Include Library files*/
$rawPath = str_replace("\\", "/", dirname(__FILE__));
$rootPath = str_replace("/dryden/ajax", "/", $rawPath);
chdir($rootPath);

require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';


debug_phperrors::SetMode('dev');
//ChromePhp::log($_POST);


if (isset($_SESSION['zpuid'])) {
    if (isAjax()){

        // Prep $_POST['moduleorder'] to insert into DB
        if(isset($_POST['moduleorder'])){

            //ChromePhp::log($_POST['moduleorder']);

            $order = json_encode($_POST['moduleorder']);

            // Store in DB a JSON string like this [1","2","3","4","5","6","7","8"]
            if(UpdateModuleOrder($_SESSION['zpuid'], $order)){
                $return = array('sucess' => true, 'status' => 'Module Order Saved to Database');
            }

        }else{
            $return = array('sucess' => false, 'status' => 'Module Order was not Valid');
        }

    }else{
        $return = array('sucess' => false, 'status' => 'Must be an AJAX Request');
    }

} else {
    $return = array('sucess' => false, 'status' => 'You Must be Logged In!');
}

/* Set JSON Header */
header('Content-type: application/json');
echo json_encode($return);


/* Check if it is an xmlhttprequest request */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * 
 * @global db_driver $zdbh
 * @param type $uid
 * @param type $order
 * @return boolean
 */
function UpdateModuleOrder($uid, $order) {
    global $zdbh;
    $sqlString = "
        UPDATE x_accounts
        SET ac_catorder_vc = :order
        WHERE ac_id_pk = :uid
        ";
    $bindArray = array(
        ':order' => $order,
        ':uid'   => $uid,
    );
    $zdbh->bindQuery( $sqlString, $bindArray );

    return $zdbh->returnResult();
}

/** @todo need to check session against database plus beforehand check request is from localhost */
?>