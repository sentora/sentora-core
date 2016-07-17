<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws
{

    function DeleteClient()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::ExecuteDeleteClient($contenttags['uid'], empty($contenttags['moveid']) ? 1 : $contenttags['moveid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('deleted', 'true'));

        return $dataobject->getDataObject();
    }

    function EnableClient()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::EnableClient($contenttags['uid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('enabled', 'true'));
        return $dataobject->getDataObject();
    }

    function DisableClient()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        module_controller::DisableClient($contenttags['uid']);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['uid']) . ws_xmws::NewXMLTag('disabled', 'true'));
        return $dataobject->getDataObject();
    }

    public function GetAllClients()
    {
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

    public function CreateClient()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "false";
        $userExits = module_controller::CheckUserExists(ws_generic::GetTagValue('username', $request_data['content']));
        if (!$userExits) {
            if(module_controller::ExecuteCreateClient(ws_generic::GetTagValue('resellerid', $request_data['content']), ws_generic::GetTagValue('username', $request_data['content']), ws_generic::GetTagValue('packageid', $request_data['content']), ws_generic::GetTagValue('groupid', $request_data['content']), ws_generic::GetTagValue('fullname', $request_data['content']), ws_generic::GetTagValue('email', $request_data['content']), ws_generic::GetTagValue('address', $request_data['content']), ws_generic::GetTagValue('postcode', $request_data['content']), ws_generic::GetTagValue('phone', $request_data['content']), ws_generic::GetTagValue('password', $request_data['content']), ws_generic::GetTagValue('sendemail', $request_data['content']), ws_generic::GetTagValue('emailsubject', $request_data['content']), ws_generic::GetTagValue('emailbody', $request_data['content']))){
                $response_xml = "true";
            }
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    public function UsernameExists()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $UsernameExists = module_controller::CheckUserExists($contenttags['username']);
        $response = "false";
        if ($UsernameExists) {
            $response = "true";
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response);
        return $dataobject->getDataObject();
    }
}

?>
