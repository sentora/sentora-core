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

    static $localports = array(
        'http' => 80,
        'ftp' => 21,
        'smtp' => 25,
        'pop3' => 110,
        'imap' => 143,
        'mysql' => 3306,
        'dns' => 53,
    );

    /**
     * Returns the status of all standard Sentora hosting ports and the current server uptime.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return type 
     */
    function GetServiceStatus()
    {
        $port_status = array();
        foreach (self::$localports as $key => $value) {
            $up = 0;
            if (sys_monitoring::LocalPortStatus($value)) {
                $up = 1;
            }
            $port_status[$key] = $up;
        }

        $response_xml = ws_xmws::NewXMLContentSection('portstatus', $port_status);
        $response_xml .= ws_xmws::NewXMLTag('serveruptime', sys_monitoring::ServerUptime());

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

    function GetPortStatus()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($this->wsdata);
        if (sys_monitoring::LocalPortStatus($contenttags['xmws']['content']['port'])) {
            $port_response = 1;
        } else {
            $port_response = 0;
        }
        $response_xml = ws_xmws::NewXMLContentSection('portstatus', array(
                'port' => $contenttags['xmws']['content']['port'],
                'status' => $port_response,
        ));
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }
}

?>
