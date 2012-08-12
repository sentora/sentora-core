<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
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
if (isset($_GET['file']) && $_GET['file'] != "" && file_exists($_GET['file']) && !strpos($_GET['file'], '/')) {
    $filename = $_GET['file'];

    // required for IE, otherwise Content-disposition is ignored
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    /*
      header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
      header("Content-Type: application/zip");
      header("Content-Transfer-Encoding: Binary");
      header("Content-Length: ".filesize($filename));
      header("Content-Disposition: attachment; filename=\"".basename($filename)."\"");
      readfile($filename);
      exit;
     */

    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false); // required for certain browsers
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=" . basename($filename) . "");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($filename) . "");
    readfile_chunked($filename);
    //unlink($filename);
    exit();
}

function readfile_chunked($filename) {
    $chunksize = 1 * (1024 * 1024);
    $buffer = '';
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        print $buffer;
    }
    return fclose($handle);
}

?>