<?php

/**
 *
 * ZPanel - Visitor Stats zpanel plugin, written by RusTus: www.zpanelcp.com.
 *
 */

	GenerateWebalizerStats();

	function GenerateWebalizerStats(){
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
			$zdbh = new db_driver("mysql:host=localhost;dbname=" . $dbname . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {

		}
        $sql = $zdbh->prepare("SELECT * FROM x_vhosts LEFT JOIN x_accounts ON x_vhosts.vh_acc_fk=x_accounts.ac_id_pk WHERE vh_deleted_ts IS NULL");
        $sql->execute();
		echo fs_filehandler::NewLine() . "BEGIN WEBALIZER STATS" . fs_filehandler::NewLine();
		echo "Generating webalizer stats html..." . fs_filehandler::NewLine();
        while ($rowvhost = $sql->fetch()) {
        	if (!file_exists(ctrl_options::GetOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'])) {
            	@mkdir(ctrl_options::GetOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'], 777, TRUE);
        	}
			if (sys_versions::ShowOSPlatformVersion() == "Windows"){
	        $runcommand = ctrl_options::GetOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer.exe -o " . ctrl_options::GetOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . " -d -F clf -n " . $rowvhost['vh_name_vc'] . "  " . ctrl_options::GetOption('log_dir') . "domains/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log";
			} else {
			chmod(ctrl_options::GetOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer", 4777);
			$runcommand = ctrl_options::GetOption('zpanel_root') ."modules/webalizer_stats/bin/webalizer -o " . ctrl_options::GetOption('zpanel_root') . "modules/webalizer_stats/stats/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . " -d -F clf -n " . $rowvhost['vh_name_vc'] . "  " . ctrl_options::GetOption('log_dir') . "domains/" . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . "-access.log";
			}
			echo "Generating stats for: " . $rowvhost['ac_user_vc'] . "/" . $rowvhost['vh_name_vc'] . fs_filehandler::NewLine();
        	system($runcommand);
		}
		echo fs_filehandler::NewLine() . "END WEBALIZER STATS" . fs_filehandler::NewLine();
	}
?>