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

class module_controller extends ctrl_module
{
	/* Set default notice text here */
	static $defaultText = "Please keep your contact information up to date. Thank You!";
	
    /**
     * The 'worker' methods.
     */
    /* Load CSS and JS files */
    static function getInit() {
        global $controller;
		// SCEditor 'header' CSS/JS
		$line = '<link rel="stylesheet" href="modules/'.$controller->GetControllerRequest('URL', 'module').'/code/SCEditor/minified/themes/default.min.css" />';
		$line .= '<script src="modules/'.$controller->GetControllerRequest('URL', 'module').'/code/SCEditor/minified/sceditor.min.js"></script>';
		$line .= '<script src="modules/'.$controller->GetControllerRequest('URL', 'module').'/code/SCEditor/minified/formats/bbcode.js"></script>';
		$line .= '<script src="modules/'.$controller->GetControllerRequest('URL', 'module').'/code/SCEditor/minified/formats/xhtml.js"></script>';
        return $line;
    }

    static function getPostInit() {
        global $controller;
		// SCEditor 'footer' JS
		$line = "
			<script>
				var textarea = document.getElementById('inNotice');
				sceditor.create(textarea, {
					format: 'bbcode',
					icons: 'monocons',
					toolbar: 'bold,italic,font,size,underline,color,removeformat|cut,copy,paste|bulletlist,orderedlist|email,link,unlink|date,time|source',
					style: 'modules/".$controller->GetControllerRequest('URL', 'module')."/code/SCEditor/minified/themes/content/default.min.css'
				});
			</script>";
        return $line;
    }

    static function ExectuteUpdateNotice($uid, $notice)
    {
        global $controller, $zdbh;
		$notice = strip_tags($notice, '<br>');
		if ($notice == '') { $notice = self::$defaultText; }
        $sql = $zdbh->prepare("
            UPDATE x_accounts
            SET ac_notice_tx = :notice
            WHERE ac_id_pk = :uid");
        $sql->bindParam(':notice', $notice);
        $sql->bindParam(':uid', $uid);
        $sql->execute();
        return true;
    }

    static function ExecuteShowNotice($rid)
    {
        global $zdbh;
        //$result = $zdbh->query("SELECT ac_notice_tx FROM x_accounts WHERE ac_id_pk = :rid")->Fetch();
        $sql = $zdbh->prepare("SELECT ac_notice_tx FROM x_accounts WHERE ac_id_pk = :rid");
        $sql->bindParam(':rid', $rid);
        $sql->execute();
        $result = $sql->fetch();

        if ($result) {
            return runtime_xss::xssClean($result['ac_notice_tx']);
        } else {
            return false;
        }
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */

	static function getDefaultNotice() {
        global $controller;
        return self::$defaultText;
    }

    static function getCurrentNoticeText()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
		return self::ExecuteShowNotice($currentuser['resellerid']);
    }

    static function doUpdateMessage()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        self::ExectuteUpdateNotice($currentuser['userid'], $formvars['inNotice']);
        header("location: ./?module=" . $controller->GetCurrentModule() . "&saved=true");
        exit;
    }

    /**
     * Webinterface sudo methods.
     */
}