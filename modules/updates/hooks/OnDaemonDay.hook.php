<?php

/**
 * Hook created by Bobby Allen to obtain latest zpanel version number and add it to the DB for querying (caching bascially!)
 * This script is handy for caching the latest version of ZPanel to reduce bandwidth from the server.
 * 
 */
echo fs_filehandler::NewLine() . "START ZPanel Updates hook" . fs_filehandler::NewLine();
echo "Checking for latest version of ZPanel..." . fs_filehandler::NewLine();
CheckZPanelLatestVersion();
echo "END ZPanel Updates hook" . fs_filehandler::NewLine();

function CheckZPanelLatestVersion() {
    // Grab the latest version of ZPanel from the ZPanel API servers and cache it into the database.
    $live_version = ws_generic::ReadURLRequestResult(ctrl_options::GetSystemOption('update_url'));
    if (!$live_version)
        return false;
    $versionnumber = ws_generic::JSONToArray($live_version);
    ctrl_options::SetSystemOption('latestzpversion', $versionnumber[0]['version']);
    return true;
}

?>
