<?php

/**
 *
 * Sentora - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (Sentora) is free software: you can redistribute it and/or modify
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
class webservice extends ws_xmws {

    /**
     * Get the full list of currently active domains on the server.
     * @global type $zdbh
     * @return type 
     */
    function GetAllSystemOptions() {
        global $zdbh;
        $response_xml = "\n";
        $sql = $zdbh->prepare("SELECT * FROM x_settings ORDER BY so_id_pk ASC");
        $sql->execute();

        while ($rowoptions = $sql->fetch()) {

            if ($rowoptions['so_value_tx'] == "") {
                $value = "NULL";
            } else {
                $value = $rowoptions['so_value_tx'];
            }

            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('setting', array(
                        'id' => $rowoptions['so_id_pk'],
                        'name' => $rowoptions['so_name_vc'],
                        'value' => $value,
                        'description' => $rowoptions['so_desc_tx'],
                        'usereditable' => $rowoptions['so_usereditable_en'],
                    ));
        }

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

}

?>
