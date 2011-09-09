<?php

/**
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

class runtime_dataobject {

    var $object;

    function __construct() {
        $this->object = array();
    }

    /**
     * Used to 'push' an associated array.
     * @param type $arr
     * @return type 
     */
    private function array_push_associative(&$arr) {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $arr[$key] = $value;
                    $this->object++;
                }
            } else {
                $arr[$arg] = "";
            }
        }
        return $this->object;
    }

    /**
     * The public function which enables easy to use key/value pushing to a data object.
     * @param string $name
     * @param string $value 
     */
    function addItemValue($name, $value) {
        $this->array_push_associative($this->object, array("$name" => "$value"));
    }

    /**
     * Returns an associated array (data object)
     * @return array 
     */
    function getDataObject() {
        return $this->object;
    }
    
    /**
     * Returns a named data object record value.
     * @param string $name
     * @return string 
     */
    function getDataRecord($name){
        return $this->object[$name];
    }

}

?>
