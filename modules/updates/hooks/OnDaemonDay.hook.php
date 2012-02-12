<?php

/**
 * Hook created by Bobby Allen to obtain latest zpanel version number and add it to the DB for querying (caching bascially!)
 * This script is handy for caching the latest version of ZPanel to reduce bandwidth from the server.
 * 
 */

// Grab the latest version of ZPanel from the ZPanel API servers and cache it into the database.
$live_version = ws_generic::ReadURLRequestResult(ctrl_options::GetOption('update_url'));
if(!$live_version)
        return false;
ctrl_options::SetSystemOption('latestzpversion', $live_version);
return true;

?>