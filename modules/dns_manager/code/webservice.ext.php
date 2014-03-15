<?php

class webservice extends ws_xmws
{

    private function GetDomainID($uid, $domainName)
    {
        global $zdbh;

        $sql = $zdbh->prepare("SELECT * FROM x_vhosts WHERE vh_acc_fk=:uid AND vh_type_in !=2 AND vh_deleted_ts IS NULL AND vh_name_vc=:name");
        $sql->bindParam(':uid', $uid);
        $sql->bindParam(':name', $domainName);
        $sql->execute();
        $domainID = $sql->fetch();
        return $domainID['vh_id_pk'];
    }

    /**
     *   Get list of all DNS records for a given domain. Parameters of XML request:
     *       uid - user ID
     *       domainName - name of domain
     */
    public function GetAllDNSRecords()
    {
        global $zdbh;

        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";
        $domainName = ws_generic::GetTagValue('domainName', $request_data['content']);
        $uid = ws_generic::GetTagValue('uid', $request_data['content']);
        $domainID = self::GetDomainID($uid, $domainName);

        $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_acc_fk=:userid AND dn_vhost_fk=:domainID AND dn_deleted_ts IS NULL ORDER BY dn_host_vc ASC");
        $sql->bindParam(':userid', $uid);
        $sql->bindParam(':domainID', $domainID);
        $sql->execute();

        while ($rowdns = $sql->fetch()) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('dns_record', array(
                        'hostName' => $rowdns['dn_host_vc'],
                        'type' => $rowdns['dn_type_vc'],
                        'target' => $rowdns['dn_target_vc'],
                        'ttl' => $rowdns['dn_ttl_in'],
            ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    /**
     *   Create a new DNS record.  Parameters of XML request:
     *       uid - user ID
     *       domainName - the domain for which DNS record is being created
     *       hostName - the host part of new DNS record (e.g. 'www')
     *       type - type of new DNS record (e.g. 'CNAME')
     *       target - the destination of DNS record (e.g. '@')
     *       ttl - time to live (e.g. '3600')
     */
    public function CreateDNSRecord()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";

        $uid = ws_generic::GetTagValue('uid', $request_data['content']);
        $domainName = ws_generic::GetTagValue('domainName', $request_data['content']);
        $hostName = ws_generic::GetTagValue('hostName', $request_data['content']);
        $type = ws_generic::GetTagValue('type', $request_data['content']);
        $target = ws_generic::GetTagValue('target', $request_data['content']);
        $ttl = ws_generic::GetTagValue('ttl', $request_data['content']);
        $domainID = self::GetDomainID($uid, $domainName);

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

    /**
     *   Delete one or multiple DNS records
     *   Mandatory parameters: uid and domainName
     *   Optional parameters: hostName, record type, target
     *   The meaning of parameters is same as in CreateDNSRecord()
     */
    public function DeleteDNSRecords()
    {
        global $zdbh;

        $request_data = $this->RawXMWSToArray($this->wsdata);
        $response_xml = "\n";
        $tags = array('hostName' => 'dn_host_vc',
            'type' => 'dn_type_vc',
            'target' => 'dn_target_vc');

        // these are mandatory parameters
        $uid = ws_generic::GetTagValue('uid', $request_data['content']);
        $domainName = ws_generic::GetTagValue('domainName', $request_data['content']);
        $domainID = self::GetDomainID($uid, $domainName);

        $sqlstr = "SELECT * FROM x_dns WHERE dn_acc_fk=:userid AND vh_deleted_ts IS NULL AND dn_vhost_fk=:domainID ";

        // iterate through optional parameters
        foreach ($tags as $tag => $sql_param) {
            if (!is_null(ws_generic::GetTagValue($tag, $request_data['content'])))
                $sqlstr .= " AND " . $sql_param . '=:' . $tag;
        }

        $sql = $zdbh->prepare($sqlstr);
        $sql->bindParam(':userid', $uid);
        $sql->bindParam(':domainID', $domainID);

        $params = array();
        foreach ($tags as $tag => $sql_param) {
            if (!is_null($params[$tag] = ws_generic::GetTagValue($tag, $request_data['content'])))
                $sql->bindParam(":" . $tag, $params[$tag]);
        }

        $sql->execute();

        while ($rowdns = $sql->fetch()) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('dns_record', array(
                        'hostName' => $rowdns['dn_host_vc'],
                        'type' => $rowdns['dn_type_vc'],
                        'target' => $rowdns['dn_target_vc'],
                        'ttl' => $rowdns['dn_ttl_in'],
                        'deleted' => 'true'
            ));

            $sql2 = $zdbh->prepare("UPDATE x_dns SET dn_deleted_ts=:time WHERE dn_id_pk =:id AND dn_deleted_ts IS NULL");
            $sql2->bindParam(':id', $rowdns['dn_id_pk']);
            $time = time();
            $sql2->bindParam(':time', $time);
            $sql2->execute();
        }

        module_controller::TriggerDNSUpdate($domainID);

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

}
?>


