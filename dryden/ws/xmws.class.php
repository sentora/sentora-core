<?php

/**
 * ZPanel(X) (M)odular (W)eb (S)ervice class.
 *
 * @package zpanelx
 * @subpackage dryden -> webservice
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */
class ws_xmws {

    var $wsdata;

    function __construct() {
        $this->wsdata = fs_filehandler::ReadFileContents('php://input');
    }

    public function RequireUserAuth() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        if (($request_data['authuser'] == 'test2') && ($request_data['authpass'] == 'password'))
            return true;
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('responsecode', '1105');
        $dataobject->addItemValue('content', '');
        die($this->SendResponse($dataobject->getDataObject()));
    }

    public function CheckServerAPIKey() {
        $serverapikey = $this->RawXMWSToArray($this->wsdata);
        if ($serverapikey['apikey'] <> ctrl_options::GetOption('apikey'))
            return false;
        return true;
    }

    public function SendResponse($responsearray) {
        header("Content-Type:text/xml");
        echo "<?xml version=\"1.0\"?>\n" .
                "<xmws-version>" . ctrl_options::GetOption('dbversion') . "</xmws-version>\n" .
                "<xmws-apikey></xmws-apikey>\n" .
                "<xmws-request></xmws-request>\n" .
                "<xmws-response>" . $responsearray['responsecode'] . "</xmws-response>\n" .
                "<xmws-authuser></xmws-authuser>\n" .
                "<xmws-authpass></xmws-authpass>\n" .
                "<xmws-content>" . $responsearray['content'] . "</xmws-content>";
    }

    public function RawXMWSToArray($xml) {
        $return_dataobject = new runtime_dataobject();
        $return_dataobject->addItemValue('version', runtime_haystack::GetValueBetween($xml, '<xmws-version>', '</xmws-version>'));
        $return_dataobject->addItemValue('apikey', runtime_haystack::GetValueBetween($xml, '<xmws-apikey>', '</xmws-apikey>'));
        $return_dataobject->addItemValue('request', runtime_haystack::GetValueBetween($xml, '<xmws-request>', '</xmws-request>'));
        $return_dataobject->addItemValue('response', runtime_haystack::GetValueBetween($xml, '<xmws-response>', '</xmws-response>'));
        $return_dataobject->addItemValue('authuser', runtime_haystack::GetValueBetween($xml, '<xmws-authuser>', '</xmws-authuser>'));
        $return_dataobject->addItemValue('authpass', runtime_haystack::GetValueBetween($xml, '<xmws-authpass>', '</xmws-authpass>'));
        $return_dataobject->addItemValue('content', runtime_haystack::GetValueBetween($xml, '<xmws-content>', '</xmws-content>'));
        return $return_dataobject->getDataObject();
    }

}

?>
