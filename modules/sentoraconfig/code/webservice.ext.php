<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws {

    /**
     * Get the full list of currently active domains on the server.
     * @global type $zdbh
     * @return type 
     */
    function GetAllSystemOptions() {
        global $zdbh;
        $response_xml = "\n";
        $sql = $zdbh->prepare("SELECT * FROM x_settings ORDER BY so_id_pk ASC");
        $sql->execute();

        while ($rowoptions = $sql->fetch()) {

            if ($rowoptions['so_value_tx'] == "") {
                $value = "NULL";
            } else {
                $value = $rowoptions['so_value_tx'];
            }

            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('setting', array(
                        'id' => $rowoptions['so_id_pk'],
                        'name' => $rowoptions['so_name_vc'],
                        'value' => $value,
                        'description' => $rowoptions['so_desc_tx'],
                        'usereditable' => $rowoptions['so_usereditable_en'],
                    ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

}

?>
