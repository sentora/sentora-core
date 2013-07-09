<?php

SetWriteApacheConfigTrue();
DeleteApacheClientFiles();

function SetWriteApacheConfigTrue() {
    global $zdbh;
    $sql = $zdbh->prepare("UPDATE x_settings
								SET so_value_tx='true'
								WHERE so_name_vc='apache_changed'");
    $sql->execute();
}

function DeleteApacheClientFiles()
{
    global $zdbh;
    $sql     = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NOT NULL";
    $numrows = $zdbh->query( $sql );
    if ( $numrows->fetchColumn() <> 0 ) {
        $sql                = $zdbh->prepare( $sql );
        $res                = array( );
        $sql->execute();
        while ( $rowdeletedaccounts = $sql->fetch() ) {
            // Check for an active user with same username
            $sql2     = "SELECT COUNT(*) FROM x_accounts WHERE ac_user_vc=:user AND ac_deleted_ts IS NULL";
            $numrows2 = $zdbh->prepare( $sql2 );
            $user     = $rowdeletedaccounts[ 'ac_user_vc' ];
            $numrows2->bindParam( ':user', $user );
            if ( $numrows2->execute() ) {
                if ( $numrows2->fetchColumn() == 0 ) {
                    if ( file_exists( ctrl_options::GetSystemOption( 'hosted_dir' ) . $rowdeletedaccounts[ 'ac_user_vc' ] ) ) {
                        fs_director::RemoveDirectory( ctrl_options::GetSystemOption( 'hosted_dir' ) . $rowdeletedaccounts[ 'ac_user_vc' ] );
                    }
                }
            }
        }
    }
}

?>
