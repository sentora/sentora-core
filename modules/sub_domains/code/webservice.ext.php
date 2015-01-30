<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * @package zpanelx
 * @subpackage modules
 * @author Alexey Smirnov (alexey@itri.org.tw)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

class webservice extends ws_xmws {

    /**
     * Get the list of subdomains for a given user.
     * @global type $zdbh
     * @return type 
     */
    public function GetSubDomainsForUser() {
        global $zdbh;
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";

        $allSubdomains = module_controller::ListSubDomains($request_data['content']);
        if (!fs_director::CheckForEmptyValue($allSubdomains)) {
            foreach ($allSubdomains as $domain) {
                $response_xml = $response_xml . ws_xmws::NewXMLContentSection('subdomain', array(
                            'subname' => $domain['subname'],
                            'subdirectory' => $domain['subdirectory'],
                            'subactive' => $domain['subactive'],
                            'subid' => $domain['subid'],
                        ));
            }
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    /**
     * Enables an authenticated user to create a sub-domain.
     * @return type 
     */
    public function CreateSubDomain() {
        global $zdbh;
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";

        $subname = ws_generic::GetTagValue('subname', $request_data['content']);
        $subdirectory = ws_generic::GetTagValue('subdirectory', $request_data['content']);
        $uid = ws_generic::GetTagValue('uid', $request_data['content']);
        $autohome = ws_generic::GetTagValue('autohome', $request_data['content']);

        $retval = module_controller::ExecuteAddSubDomain($uid, $subname, $subdirectory, $autohome);

        $response_xml = $response_xml . ws_xmws::NewXMLContentSection('subdomain', array(
                            'subname' => $subname,
                            'subdirectory' => $subdirectory,
                            'uid' => $uid,
                            'autohome' => $autohome,
        ));

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }


    /**
     * Delete a specified sub-domain using the content <domainid> tag.
     * @return type 
     */
    public function DeleteSubDomain() {
        global $zdbh;
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');

        if (module_controller::ExecuteDeleteSubDomain($contenttags['domainid'])) {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domainid', $contenttags['domainid']) . ws_xmws::NewXMLTag('deleted', 'true'));
        } else {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('domainid', $contenttags['domainid']) . ws_xmws::NewXMLTag('deleted', 'false'));
        }
        return $dataobject->getDataObject();
    }


}

?>

