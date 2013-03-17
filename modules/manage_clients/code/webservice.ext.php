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

    function DeleteClient() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::ExecuteDeleteClient($contenttags['uid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('deleted', 'true'));

        return $dataobject->getDataObject();
    }

    function EnableClient() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::EnableClient($contenttags['uid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('enabled', 'true'));
        return $dataobject->getDataObject();
    }

    function DisableClient() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::DisableClient($contenttags['uid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('disabled', 'true'));
        return $dataobject->getDataObject();
    }

    public function GetAllClients() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $response_xml = "\n";
        if (module_controller::ListClients($contenttags['uid'])) {
            $allactiveclients = module_controller::ListClients($contenttags['uid']);
            $currentclient = 0;
            $newsections = "";
            foreach ($allactiveclients as $client) {
                $newsections = $newsections . ws_xmws::NewXMLContentSection('client', $client);
                $currentclient++;
            }
            $response_xml = $response_xml . $newsections;
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    public function CreateClient() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "";
        $userExits = module_controller::CheckUserExits(ws_generic::GetTagValue('username', $request_data['content']));
        if ($userExits === true) {
            module_controller::ExecuteCreateClient(ws_generic::GetTagValue('resellerid', $request_data['content']), ws_generic::GetTagValue('username', $request_data['content']), ws_generic::GetTagValue('packageid', $request_data['content']), ws_generic::GetTagValue('groupid', $request_data['content']), ws_generic::GetTagValue('fullname', $request_data['content']), ws_generic::GetTagValue('email', $request_data['content']), ws_generic::GetTagValue('address', $request_data['content']), ws_generic::GetTagValue('postcode', $request_data['content']), ws_generic::GetTagValue('phone', $request_data['content']), ws_generic::GetTagValue('password', $request_data['content']), ws_generic::GetTagValue('sendemail', $request_data['content']), ws_generic::GetTagValue('emailsubject', $request_data['content']), ws_generic::GetTagValue('emailbody', $request_data['content']));
        } else {
            $response_xml = $userExits;
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    public function UsernameExits() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $response = null;

        $UsernameExits = module_controller::CheckUserExits($contenttags['username']);
        if ($UsernameExits === true) {
            $response = "true";
        } else {
            $response = $UsernameExits;
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $respons);
        return $dataobject->getDataObject();
    }

}

?>
