<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - Visitor Stats zpanel plugin, written by RusTus: www.zpanelcp.com.
 *
 * changes P;Peyremorte : reduced duplicate strings constructions
 */
echo fs_filehandler::NewLine() . "BEGIN Webalizer Stats" . fs_filehandler::NewLine();
if ( ui_module::CheckModuleEnabled( 'Webalizer Stats' ) ) {
    GenerateWebalizerStats();
}
else {
    echo "Webalizer Stats Module DISABLED." . fs_filehandler::NewLine();
}
echo "END Webalizer Stats" . fs_filehandler::NewLine();

function GenerateWebalizerStats()
{
    global $zdbh;
    $sql      = $zdbh->prepare( "SELECT * FROM x_vhosts LEFT JOIN x_accounts ON x_vhosts.vh_acc_fk=x_accounts.ac_id_pk WHERE vh_deleted_ts IS NULL" );
    $sql->execute();
    echo "Generating webalizer stats html..." . fs_filehandler::NewLine();
    while ( $rowvhost = $sql->fetch() ) {
      $basedir = ctrl_options::GetSystemOption( 'sentora_root' ) . "modules/webalizer_stats/stats/" . $rowvhost[ 'ac_user_vc' ] . "/" . $rowvhost[ 'vh_name_vc' ];
      if ( !file_exists( $basedir ) ) {
          @mkdir( $basedir, 0755, TRUE );
        }

        /** set webalizer command dependant on OS */
        if ( sys_versions::ShowOSPlatformVersion() == "Windows" ) {
            $command = ctrl_options::GetSystemOption( 'sentora_root' ) .
                'modules/webalizer_stats/bin/webalizer.exe';
        }
        else {
            chmod( ctrl_options::GetSystemOption( 'sentora_root' ) . "modules/webalizer_stats/bin/webalizer", 4777 );
            $command = "webalizer";
        }

        /** all other args and flags are the same so keep them outsite to avoid duplication */
        $flag        = '-o';
        
        $secondFlags = '-d -F clf -n';

        $domain = $rowvhost[ 'vh_name_vc' ];

        $logFile = realpath( ctrl_options::GetSystemOption( 'log_dir' ) .
            'domains/' .
            $rowvhost[ 'ac_user_vc' ] .
            '/' .
            $rowvhost[ 'vh_name_vc' ] .
            '-access.log' );

        $statsPath = ctrl_options::GetSystemOption( 'sentora_root' ) .
            "modules/webalizer_stats/stats/" .
            $rowvhost[ 'ac_user_vc' ] .
            '/' .
            $rowvhost[ 'vh_name_vc' ];

        /** build arg array, this is in the required order! do not just change the order of this array */
        $args = array(
            $logFile,
            $flag,
            $statsPath,
            $secondFlags,
            $domain,
        );

        echo "Generating stats for: " . $rowvhost[ 'ac_user_vc' ] . "/" . $rowvhost[ 'vh_name_vc' ] . fs_filehandler::NewLine();
        $returnValue = ctrl_system::systemCommand( $command, $args );
        echo (0 === $returnValue ? 'Succeeded' : 'Failed') . fs_filehandler::NewLine();
    }
}

?>