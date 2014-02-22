<?php

class webservice extends ws_xmws
{

    public function CreateDNSRecord()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";

        $uid = ws_generic::GetTagValue('uid', $request_data['content']);
        $domainName = ws_generic::GetTagValue('domainName', $request_data['content']);
        $domainID = ws_generic::GetTagValue('domainID', $request_data['content']);
        $hostName = ws_generic::GetTagValue('hostName', $request_data['content']);
        $type = ws_generic::GetTagValue('type', $request_data['content']);
        $target = ws_generic::GetTagValue('target', $request_data['content']);
        $ttl = ws_generic::GetTagValue('ttl', $request_data['content']);

        module_controller::createDNSRecord(array(
            "uid" => $uid,
            "domainName" => $domainName,
            "domainID" => $domainID,
            "type" => $type,
            "hostName" => $hostName,
            "ttl" => $ttl,
            "target" => $target
        ));


        $response_xml = $response_xml . ws_xmws::NewXMLContentSection('dns_record', array(
                    'domainName' => $domainName,
                    'hostName' => $hostName,
                    'type' => $type,
                    'target' => $target,
                    'created' => 'true'
        ));

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

}