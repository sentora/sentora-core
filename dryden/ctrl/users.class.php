<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_users {

    static function GetUserDetail($user="") {
        global $zdbh;

        $userdetail = new runtime_dataobject();

        if ($user == "") {
            # Display the current logged in your details!
            $rows = $zdbh->prepare("SELECT * FROM x_accounts LEFT JOIN x_profiles ON (x_accounts.ac_id_pk=x_profiles.ud_user_fk) LEFT JOIN x_groups ON (x_profiles.ud_group_fk=x_groups.ug_id_pk) LEFT JOIN x_packages ON (x_profiles.ud_package_fk=x_packages.pk_id_pk) WHERE x_accounts.ac_id_pk = " . ctrl_auth::CurrentUserID() . "");
            $rows->execute();
            $dbvals = $rows->fetch();
            $userdetail->addItemValue('username', $dbvals['ac_user_vc']);
            $userdetail->addItemValue('userid', $dbvals['ac_id_pk']);
            $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
            $userdetail->addItemValue('email', $dbvals['ac_email_vc']);
            $userdetail->addItemValue('fullname', $dbvals['ud_fullname_vc']);
            $userdetail->addItemValue('packagename', $dbvals['pk_name_vc']);
            $userdetail->addItemValue('usergroup', $dbvals['ug_name_vc']);
			$userdetail->addItemValue('address', $dbvals['ud_address_tx']);
			$userdetail->addItemValue('postcode', $dbvals['ud_postcode_vc']);
			$userdetail->addItemValue('phone', $dbvals['ud_phone_vc']);
        } else {
            # Display the requested user details based on USERID.
            # Display the current logged in your details!
            $rows = $zdbh->query("SELECT * FROM x_accounts JOIN x_profiles ON x_accounts.ac_id_pk=x_profiles.ud_user_fk WHERE x_accounts.ac_id_pk = " . $user . "");
            $rows->execute();
            $dbvals = $rows->fetch();
            $userdetail->addItemValue('userid', $dbvals['ac_id_pk']);
            $userdetail->addItemValue('username', $dbvals['ac_user_vc']);
            $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
            $userdetail->addItemValue('email', $dbvals['ac_email_vc']);
            $userdetail->addItemValue('fullname', $dbvals['ud_fullname_vc']);
            $userdetail->addItemValue('packagename', 'TO BE DONE LATER');
            $userdetail->addItemValue('usergroup', 'TO BE DONE LATER');
			$userdetail->addItemValue('address', $dbvals['ud_address_tx']);
			$userdetail->addItemValue('postcode', $dbvals['ud_postcode_vc']);
			$userdetail->addItemValue('phone', $dbvals['ud_phone_vc']);
        }
        return $userdetail->getDataObject();
    }

}

?>
