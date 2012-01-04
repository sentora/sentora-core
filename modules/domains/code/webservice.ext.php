<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@zpanelcp.com)
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
    function GetAllDomains() {
        global $zdbh;
        $response_xml = "\n";
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL AND vh_type_in=1");
        $sql->execute();

        while ($rowdomains = $sql->fetch()) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('domain', array(
                        'id' => $rowdomains['vh_id_pk'],
                        'uid' => $rowdomains['vh_acc_fk'],
                        'domain' => $rowdomains['vh_name_vc'],
                        'homedirectory' => $rowdomains['vh_directory_vc'],
                        'domaintype' => $rowdomains['vh_type_in'],
                        'active' => $rowdomains['vh_active_in'],
                        'suhosin' => $rowdomains['vh_suhosin_in'],
                        'openbasedir' => $rowdomains['vh_obasedir_in'],
                        'customconfig' => 'x',
                        'datecreated' => $rowdomains['vh_created_ts'],
                        'datedeleted' => 'x',
                    ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

    /**
     * Gets a list of all the domains that a user has configured on their hosting account (the user id needs to be sent in the <content> tag).
     * @global type $zdbh
     * @return type 
     */
    function GetDomainsForUser() {
        global $zdbh;
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=" . $request_data['content'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1");
        $sql->execute();

        while ($rowdomains = $sql->fetch()) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('domain', array(
                        'id' => $rowdomains['vh_id_pk'],
                        'uid' => $rowdomains['vh_acc_fk'],
                        'domain' => $rowdomains['vh_name_vc'],
                        'homedirectory' => $rowdomains['vh_directory_vc'],
                        'domaintype' => $rowdomains['vh_type_in'],
                        'active' => $rowdomains['vh_active_in'],
                        'suhosin' => $rowdomains['vh_suhosin_in'],
                        'openbasedir' => $rowdomains['vh_obasedir_in'],
                        'customconfig' => $rowdomains['vh_custom_tx'],
                        'datecreated' => $rowdomains['vh_created_ts'],
                        'datedeleted' => $rowdomains['vh_deleted_ts'],
                    ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

    /**
     * Enables an authenticated user to create a domain on their hosting account.
     * @return type 
     */
    function CreateDomain() {
        $this->RequireUserAuth();
        
        // We can add the domain here, as the user is authenticated we can user the $this->authuserid to get the user's ID.

        $response = "Authenticated user id: " . $this->authuserid . "";
        
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response);
        
        return $dataobject->getDataObject();
    }

}

?>
