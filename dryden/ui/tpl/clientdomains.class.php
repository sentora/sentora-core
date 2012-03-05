<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_clientdomains {

    public function Template() {
		global $zdbh;
        $currentuser = ctrl_users::GetUserDetail();			
		$line = "<table  border=\"0\" cellSpacing=\"0\" cellPadding=\"2\" width=\"100%\">";

        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=" .$currentuser['userid']. " AND vh_type_in=1 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Domains</strong></td><td></td></tr>";
            while ($rowdomains = $sql->fetch()) {
				if ($rowdomains['vh_type_in'] == 1){
					$line .= "<tr>";
					$line .= "<td  width=\"100%\"><b><a href=\"http://".$rowdomains['vh_name_vc']."\" target=\"_blank\">".$rowdomains['vh_name_vc']."</a></b></td>";
					$line .= "<td align=\"left\">";

					if($rowdomains['vh_active_in']==1 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Live\"><img src=\"<# ui_tpl_assetfolderpath #>/images/live.png\"></a> ";
					}elseif ($rowdomains['vh_active_in']==0 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Pending\"><img src=\"<# ui_tpl_assetfolderpath #>/images/pending.png\"></a> ";
					}else{
						$line .= "";
					}
					if($rowdomains['vh_enabled_in']==0){
						$line .= "<a href=\"#\" title=\"Disabled\"><img src=\"<# ui_tpl_assetfolderpath #>/images/disabled.png\"></a> ";
					}
				}
			}
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		} else {
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Domains</strong></td><td></td></tr>";
			$line .= "<tr><td>No Domains Found</td><td><a href=\"?module=domains\">CREATE</a></td></tr>";
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		}

        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=" .$currentuser['userid']. " AND vh_type_in=2 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Sub Domains</strong></td><td></td></tr>";
            while ($rowdomains = $sql->fetch()) {
				if ($rowdomains['vh_type_in'] == 2){
					$line .= "<tr>";
					$line .= "<td  width=\"100%\"><b><a href=\"http://".$rowdomains['vh_name_vc']."\" target=\"_blank\">".$rowdomains['vh_name_vc']."</a></b></td>";
					$line .= "<td align=\"left\">";

					if($rowdomains['vh_active_in']==1 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Live\"><img src=\"<# ui_tpl_assetfolderpath #>/images/live.png\"></a> ";
					}elseif ($rowdomains['vh_active_in']==0 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Pending\"><img src=\"<# ui_tpl_assetfolderpath #>/images/pending.png\"></a> ";
					}else{
						$line .= "";
					}
					if($rowdomains['vh_enabled_in']==0){
						$line .= "<a href=\"#\" title=\"Disabled\"><img src=\"<# ui_tpl_assetfolderpath #>/images/disabled.png\"></a> ";
					}
				}
			}
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		} else {
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Sub Domains</strong></td></td><td></tr>";
			$line .= "<tr><td>No Sub Domains Found</td><td><a href=\"?module=sub_domains\">CREATE</a></td></tr>";
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		}
		
        $sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk=" .$currentuser['userid']. " AND vh_type_in=3 AND vh_deleted_ts IS NULL ORDER BY vh_name_vc";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->execute();
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Parked Domains</strong></td><td></td></tr>";
            while ($rowdomains = $sql->fetch()) {
				if ($rowdomains['vh_type_in'] == 3){
					$line .= "<tr>";
					$line .= "<td  width=\"100%\"><b><a href=\"http://".$rowdomains['vh_name_vc']."\" target=\"_blank\">".$rowdomains['vh_name_vc']."</a></b></td>";
					$line .= "<td align=\"left\">";

					if($rowdomains['vh_active_in']==1 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Live\"><img src=\"<# ui_tpl_assetfolderpath #>/images/live.png\"></a> ";
					}elseif ($rowdomains['vh_active_in']==0 && $rowdomains['vh_enabled_in']==1){
						$line .= "<a href=\"#\" title=\"Pending\"><img src=\"<# ui_tpl_assetfolderpath #>/images/pending.png\"></a> ";
					}else{
						$line .= "";
					}
					if($rowdomains['vh_enabled_in']==0){
						$line .= "<a href=\"#\" title=\"Disabled\"><img src=\"<# ui_tpl_assetfolderpath #>/images/disabled.png\"></a> ";
					}
				}
			}
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		} else {
			$line .= "<tr><td><img class=\"raquo\" src=\"<# ui_tpl_assetfolderpath #>/images/blank.png\" border=\"0\"><strong>Parked Domains</strong></td></td><td></tr>";
			$line .= "<tr><td>No Parked Domains Found</td><td><a href=\"?module=parked_domains\">CREATE</a></td></tr>";
			$line .= "<tr><td>&nbsp;</td><td></td></tr>";
		}
		
		$line .= "</td>";
		$line .= "</tr>";
		$line .= "</table>";

	return $line;
	}
}

?>