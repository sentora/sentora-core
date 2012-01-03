<?php

/**
 * ZPanel(X) (M)odular (W)eb (S)ervice class.
 *
 * @package zpanelx
 * @subpackage dryden -> webservice
 * @version 10.0.0
 * @author ballen (ballen@zpanelcp.com)
 */
class ws_xmws {

    /**
     * Used to store the current RAW XML request data.
     * @var string 
     */
    var $wsdata;

    /**
     * Used to store the array of request variables.
     */
    var $wsdataarray;

    /**
     * Constructs the object setting the web service request data to a class variable.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     */
    function __construct() {
        $this->wsdata = fs_filehandler::ReadFileContents('php://input');
        $this->wsdataarray = $this->RawXMWSToArray($this->wsdata);
    }

    /**
     * Requests that the web service method requires that the user must be authenticated wth the server.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @return type 
     */
    public function RequireUserAuth() {
        if (($this->wsdataarray['authuser'] == 'test2') && ($this->wsdataarray['authpass'] == 'password'))
            return true;
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '1105');
        $dataobject->addItemValue('content', 'User authentication failed');
        die($this->SendResponse($dataobject->getDataObject()));
    }

    /**
     * Checks that the Server API given in the webservice request XML is valid and matches the one stored in the x_settings table.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @return bool 
     */
    public function CheckServerAPIKey() {
        if ($this->wsdataarray['apikey'] <> ctrl_options::GetOption('apikey'))
            return false;
        return true;
    }

    /**
     * Will format and send a valid XMWS XML response.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param array $responsearray 
     */
    public function SendResponse($responsearray) {
        if ($responsearray['response'] == '')
            $responsearray['response'] = '1101';
        header("Content-Type:text/xml");
        echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n".
        "<xmws>\n" .
        "\t<response>" . $responsearray['response'] . "</response>\n" .
        "\t<content>" . $responsearray['content'] . "</content>\n".
        "</xmws>";
    }

    /**
     * Takes RAW XMWS XML and converts its contents into a usable (array).
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $xml
     * @return type 
     */
    public function RawXMWSToArray($xml) {
        $return_dataobject = new runtime_dataobject();
        $return_dataobject->addItemValue('version', runtime_haystack::GetValueBetween($xml, '<version>', '</version>'));
        $return_dataobject->addItemValue('apikey', runtime_haystack::GetValueBetween($xml, '<apikey>', '</apikey>'));
        $return_dataobject->addItemValue('request', runtime_haystack::GetValueBetween($xml, '<request>', '</request>'));
        $return_dataobject->addItemValue('response', runtime_haystack::GetValueBetween($xml, '<response>', '</response>'));
        $return_dataobject->addItemValue('authuser', runtime_haystack::GetValueBetween($xml, '<authuser>', '</authuser>'));
        $return_dataobject->addItemValue('authpass', runtime_haystack::GetValueBetween($xml, '<authpass>', '</authpass>'));
        $return_dataobject->addItemValue('content', runtime_haystack::GetValueBetween($xml, '<content>', '</content>'));
        return $return_dataobject->getDataObject();
    }

}

?>
