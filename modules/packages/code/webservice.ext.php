<?php

/**
 * @copyright 2014-2023 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class webservice extends ws_xmws
{

    /**
     * Return a full list of packages configured for a specific user (reseller) account.
     * @global type $zdbh
     * @return array
     */
    public function GetAllPackages()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $allpackages = array();
        $response_xml = "\n";
        if (!is_null($contenttags['uid'])) {

            $allpackages = module_controller::ListPackages($contenttags['uid']);
        } else {
            $allpackages = module_controller::ListPackages(1);
        }
        foreach ($allpackages as $package) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('packages', array(
                    'id' => $package['packageid'],
                    'pakage' => $package['packagename']
            ));
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }

    /**
     * Get and return package details for a specific package.
     * @return array
     */
    public function GetPackageId()
    {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $packageId = 0;
        $response_xml = "\n";
        $allpackages = module_controller::ListPackages(1);
        foreach ($allpackages as $package) {
            if ($package['packagename'] === $contenttags['pakagename']) {
                $packageId = $package['packageid'];
            }
        }
        $response_xml = $response_xml . ws_xmws::NewXMLContentSection('pakageid', $packageId);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();
    }
}
