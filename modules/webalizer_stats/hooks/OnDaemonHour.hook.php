<?php

/**
 *
 * ZPanel - Visitor Stats zpanel plugin, written by RusTus: www.zpanelcp.com.
 *
 */
	echo fs_filehandler::NewLine() . "BEGIN Webalizer Stats" . fs_filehandler::NewLine();
		if (ui_module::CheckModuleEnabled('Webalizer Stats')){
			GenerateWebalizerStats();
		} else {
			echo "Webalizer Stats Module DISABLED." . fs_filehandler::NewLine();
		}
	echo "END Webalizer Stats" . fs_filehandler::NewLine();

	function GenerateWebalizerStats(){
		global $zdbh;
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts LEFT JOIN x_accounts ON x_vhosts.vh_acc_fk=x_accounts.ac_id_pk WHERE vh_deleted_ts IS NULL");
        $sql->execute();
		echo "Generating webalizer stats html..." . fs_filehandler::NewLine();
        while ($rowvhost = $sql->fetch()) {
        	if (!file_exists(ctrl_options::GetSystemOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'])) {
            	@mkdir(ctrl_options::GetSystemOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'], 777, TRUE);
        	}
			if (sys_versions::ShowOSPlatformVersion() == "Windows"){
	        $runcommand = ctrl_options::GetSystemOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer.exe -o " . ctrl_options::GetSystemOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . " -d -F clf -n " . $rowvhost['vh_name_vc'] . "  " . ctrl_options::GetSystemOption('log_dir') . "domains/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log";
			} else {
			chmod(ctrl_options::GetSystemOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer", 4777);
			$runcommand = ctrl_options::GetSystemOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer -o " . ctrl_options::GetSystemOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . " -d -F clf -n " . $rowvhost['vh_name_vc'] . "  " . ctrl_options::GetSystemOption('log_dir') . "domains/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log";
			}
			echo "Generating stats for: " . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
        	system($runcommand);
		}
	}
?>