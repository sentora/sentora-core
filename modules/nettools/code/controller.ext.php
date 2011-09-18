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
 
class module_controller {

	static $queryType;
	static $target;
	static $portNum;
	static $msg;
	static $buffer;

	static function getNetworkTools (){
		$line = "<tr>";
		$line .= "<th><b>Host Information</b></th>";
		$line .= "<th><b>Host Connectivity</b></th>";
		$line .= "</tr>";
		$line .= "<tr valign=\"top\">";
		$line .= "<td>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"lookup\">Resolve/Reverse Lookup<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"dig\">Get DNS Records<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"wwwhois\">Whois (Web)<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"arin\">Whois (IP)</td>";
		$line .= "<td>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"checkp\">Check port";
		$line .= "<input class=\"inputbox\" type=\"text\" name=\"portNum\" size=\"5\" maxlength=\"5\" value=\"80\">";
		$line .= "<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"p\">Ping host<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"tr\">Traceroute to host<br>";
		$line .= "<input type=\"radio\" name=\"queryType\" value=\"all\" checked>Do All</td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<td>";
		$line .= "<input class=\"inputbox\" type=\"text\" name=\"target\" value=\"Enter Host or IP\" onFocus=\"m(this)\">";
		$line .= "</td>";
		$line .= "<td>";
		$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"Submit\">Display</button>";
		$line .= "</td>";
		$line .= "</tr>";
	
		return $line;
	}
	
	
	
	
	
	static function doDisplayInfo(){
	global $controller;

		self::$queryType = $controller->GetControllerRequest('FORM', 'queryType');
		self::$target = $controller->GetControllerRequest('FORM', 'target');
		self::$portNum = $controller->GetControllerRequest('FORM', 'portNum');

	}
	
	
	
	
	
	#Display the output
	static function getResult() {
			

    	if (!self::$queryType) {
        	return;
    	}

    	if ((!self::$target) || (!preg_match("/^[\w\d\.\-]+\.[\w\d]{1,4}$/i", self::$target))) { #bugfix
       		self::message("Error: You did not specify a valid target host or IP.");
        	return;
    	}

			#Figure out which tasks to perform, and do them
    		if ((self::$queryType == "all") || (self::$queryType == "lookup"))
        		self::lookup(self::$target);
				
    		if ((self::$queryType == "all") || (self::$queryType == "dig"))
       		 	self::dig(self::$target);
				
    		if ((self::$queryType == "all") || (self::$queryType == "wwwhois"))
		        self::wwwhois(self::$target);
				
		    if ((self::$queryType == "all") || (self::$queryType == "arin"))
		        self::arin(self::$target);
				
		    if ((self::$queryType == "all") || (self::$queryType == "checkp"))
		        self::checkp(self::$target, self::$portNum);
				
		    if ((self::$queryType == "all") || (self::$queryType == "p"))
		        self::p(self::$target);
				
		    if ((self::$queryType == "all") || (self::$queryType == "tr"))
		        self::tr(self::$target);
		
        return;
    }
	
	
	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }
	
	

	# Worker Functions
    function message($msg) {
        echo ui_sysmessage::shout($msg);
        flush();
    }

    function lookup($target) {
        global $ntarget;
        self::$msg = "$target resolved to ";
        if (eregi("[a-zA-Z]", $target))
            $ntarget = gethostbyname($target);
        else
            $ntarget = gethostbyaddr($target);
        self::$msg .= $ntarget;
        self::message(self::$msg);
    }

    function getip($target) {
        global $ntarget;
        if (eregi("[a-zA-Z]", $target))
            $ntarget = gethostbyname($target);
        else
            $ntarget = $target;
        self::$msg .= $ntarget;
        return(self::$msg);
    }

    function dig($target) {
        global $ntarget;
        self::message("DNS Query Results:");
#$target = gethostbyaddr($target);
#if (! eregi("[a-zA-Z]", ($target = gethostbyaddr($target))) )
        if ((!eregi("[a-zA-Z]", $target) && (!eregi("[a-zA-Z]", $ntarget))))
            self::$msg .= "Can't do a DNS query without a hostname.";
        else {
            if (!eregi("[a-zA-Z]", $target))
                $target = $ntarget;
            if (!self::$msg .= trim(nl2br(`dig any '$target'`))) #bugfix
                self::$msg .= "The <i>dig</i> command is not working at this time.";
        }
#TODO: Clean up output, remove ;;'s and DiG headers
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }

    function wwwhois($target) {
        global $ntarget;
        $server = "whois.crsnic.net";
        self::message("<p><b>WWWhois Results:</b><blockquote>");
#Determine which WHOIS server to use for the supplied TLD
        if ((eregi("\.com\$|\.net\$|\.edu\$", $target)) || (eregi("\.com\$|\.net\$|\.edu\$", $ntarget)))
            $server = "whois.crsnic.net";
        else if ((eregi("\.info\$", $target)) || (eregi("\.info\$", $ntarget)))
            $server = "whois.afilias.net";
        else if ((eregi("\.org\$", $target)) || (eregi("\.org\$", $ntarget)))
            $server = "whois.corenic.net";
        else if ((eregi("\.name\$", $target)) || (eregi("\.name\$", $ntarget)))
            $server = "whois.nic.name";
        else if ((eregi("\.biz\$", $target)) || (eregi("\.biz\$", $ntarget)))
            $server = "whois.nic.biz";
        else if ((eregi("\.us\$", $target)) || (eregi("\.us\$", $ntarget)))
            $server = "whois.nic.us";
        else if ((eregi("\.cc\$", $target)) || (eregi("\.cc\$", $ntarget)))
            $server = "whois.enicregistrar.com";
        else if ((eregi("\.ws\$", $target)) || (eregi("\.ws\$", $ntarget)))
            $server = "whois.nic.ws";
        else {
            self::$msg .= "I only support .com, .net, .org, .edu, .info, .name, .us, .cc, .ws, and .biz.</blockquote>";
            self::message(self::$msg);
            return;
        }

        self::message("Connecting to $server...<br><br>");
        if (!$sock = fsockopen($server, 43, $num, $error, 10)) {
            unset($sock);
            self::$msg .= "Timed-out connecting to $server (port 43)";
        } else {
            fputs($sock, "$target\n");
            while (!feof($sock))
                self::$buffer .= fgets($sock, 10240);
        }
        fclose($sock);
        if (!eregi("Whois Server:", self::$buffer)) {
            if (eregi("no match", self::$buffer))
                self::message("NOT FOUND: No match for $target<br>");
            else
                self::message("Ambiguous query, multiple matches for $target:<br>");
        }
        else {
            self::$buffer = split("\n", self::$buffer);
            for ($i = 0; $i < sizeof(self::$buffer); $i++) {
                if (eregi("Whois Server:", self::$buffer[$i]))
                    self::$buffer = self::$buffer[$i];
            }
            $nextServer = substr(self::$buffer, 17, (strlen(self::$buffer) - 17));
            $nextServer = str_replace("1:Whois Server:", "", trim(rtrim($nextServer)));
            self::$buffer = "";
            self::message("Deferred to specific whois server: $nextServer...<br><br>");
            if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {
                unset($sock);
                self::$msg .= "Timed-out connecting to $nextServer (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock))
                    self::$buffer .= fgets($sock, 10240);
                fclose($sock);
            }
        }
        self::$msg .= nl2br(self::$buffer);
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }

    function arin($target) {
		$nextServer = NULL;
        $server = "whois.arin.net";
        self::message("<p><b>IP Whois Results:</b><blockquote>");
        if (!$target = gethostbyname($target))
            self::$msg .= "Can't IP Whois without an IP address.";
        else {
            self::message("Connecting to $server...<br><br>");
            if (!$sock = fsockopen($server, 43, $num, $error, 20)) {
                unset($sock);
                self::$msg .= "Timed-out connecting to $server (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock))
                    self::$buffer .= fgets($sock, 10240);
                fclose($sock);
            }
            if (eregi("RIPE.NET", self::$buffer))
                $nextServer = "whois.ripe.net";
            else if (eregi("whois.apnic.net", self::$buffer))
                $nextServer = "whois.apnic.net";
            else if (eregi("nic.ad.jp", self::$buffer)) {
                $nextServer = "whois.nic.ad.jp";
                #/e suppresses Japanese character output from JPNIC
                $extra = "/e";
            } else if (eregi("whois.registro.br", self::$buffer))
                $nextServer = "whois.registro.br";
            if ($nextServer) {
                self::$buffer = "";
                message("Deferred to specific whois server: $nextServer...<br><br>");
                if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {
                    unset($sock);
                    self::$msg .= "Timed-out connecting to $nextServer (port 43)";
                } else {
                    fputs($sock, "$target$extra\n");
                    while (!feof($sock))
                        self::$buffer .= fgets($sock, 10240);
                    fclose($sock);
                }
            }
            self::$buffer = str_replace(" ", "&nbsp;", self::$buffer);
            self::$msg .= nl2br(self::$buffer);
        }
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }

    function checkp($target, $portNum) {
        self::message("<p><b>Checking Port $portNum</b>...<blockquote>");
        if (!$sock = fsockopen($target, $portNum, $num, $error, 5))
            self::$msg .= "Port $portNum does not appear to be open.";
        else {
            self::$msg .= "Port $portNum is open and accepting connections.";
            fclose($sock);
        }
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }

    function p($target) {
        self::message("<p><b>Ping Results:</b><blockquote>");
        if (!self::$msg .= trim(nl2br(`ping '$target'`))) #bugfix
            self::$msg .= "Ping failed. Host may not be active.";
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }

    function tr($target) {
        self::message("<p><b>Traceroute Results:</b><blockquote>");
        $totrace = self::getip($target);
        if (!self::$msg .= trim(nl2br(`tracert $totrace`))) #bugfix
            self::$msg .= "Traceroute failed. Host may not be active.";
        self::$msg .= "</blockquote></p>";
        self::message(self::$msg);
    }
	
	static function getModuleIcon() {
		global $controller;
		$module_icon = "/etc/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
	
}

?>
