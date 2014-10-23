<?php

global $zdbh;
$modsql = $zdbh->prepare("SELECT * FROM x_modules ORDER BY mo_name_vc ASC");
$modsql->execute();
echo fs_filehandler::NewLine() . "START checking for any avaliable module updates.." . fs_filehandler::NewLine();
while ($modules = $modsql->fetch()) {
    echo "Checking update for mod: " . $modules['mo_name_vc'] . fs_filehandler::NewLine();
    $mod_config = new xml_reader(fs_filehandler::ReadFileContents(ctrl_options::GetSystemOption('sentora_root') . 'modules/' . $modules['mo_folder_vc'] . '/module.xml'));
    $mod_config->Parse();
    if (isset($mod_config->document->version[0]->tagData)) {
        $current_version = $mod_config->document->version[0]->tagData;
        $updateurl = $mod_config->document->updateurl[0]->tagData;
        $getupdateinfo = fs_filehandler::ReadFileContents($updateurl);
        if (strstr(strtoupper($getupdateinfo), 'LATESTVERSION')) {
            $updateinfo = new xml_reader($getupdateinfo);
            $updateinfo->Parse();
            if (isset($updateinfo->document->latestversion[0]->tagData)) {
                $latest_version = $updateinfo->document->latestversion[0]->tagData;
                $downloadurl = $updateinfo->document->downloadurl[0]->tagData;
                if ($current_version < $latest_version) {
                    $versionsql = $zdbh->prepare("UPDATE x_modules SET mo_updatever_vc = :latest_version, mo_updateurl_tx = :downloadurl WHERE mo_id_pk = :mo_id_pk");
                    $versionsql->bindParam(':mo_id_pk', $modules['mo_id_pk']);
                    $versionsql->bindParam(':latest_version', $latest_version);
                    $versionsql->bindParam(':downloadurl', $downloadurl);
                    $versionsql->execute();
                } else {
                    $versionsql = $zdbh->prepare("UPDATE x_modules SET mo_updatever_vc = '', mo_updateurl_tx = '' WHERE mo_id_pk = :mo_id_pk");
                    $versionsql->bindParam(':mo_id_pk', $modules['mo_id_pk']);
                    $versionsql->execute();
                }
            } else {
                echo "The remote file was not parsed correctly or does not exist." . fs_filehandler::NewLine();
            }
        } else {
            echo "The remote file does not exist." . fs_filehandler::NewLine();
        }
    } else {
        echo "Couldn't open the module XML file for (" . $modules['mo_name_vc'] . ")" . fs_filehandler::NewLine();
    }
}
echo "END getting module version update information!" . fs_filehandler::NewLine();


/*
 * Please DO NOT remove the below code, this helps us at the Sentora project
 * find out non-personal infomation about how people are running Sentora. The only infomation
 * that we are passing back here is your Sentora version and what OS you are running it on in addition
 * to collecting the email address of the default 'zadmin' account to enable automatic email
 * notficiations of new releases and urgent patches.
 */
$zdbh->bindQuery('SELECT ac_email_vc AS email FROM x_accounts WHERE ac_user_vc = :user', array(':user' => 'zadmin'));
$zadmin = $zdbh->returnRow();

ws_generic::DoPostRequest('http://api.sentora.org/hello.json', "version=" . sys_versions::ShowSentoraVersion() . "&platform=" . sys_versions::ShowOSPlatformVersion() . "&url=" . ctrl_options::GetSystemOption('sentora_domain') . "mail=" . $zadmin['email']);

return true;
?>
