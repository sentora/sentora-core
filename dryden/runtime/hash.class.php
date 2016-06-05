<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Provides bcrypt (Blowfish single-way encryption) functionality for password hashing in ZPanel.
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.2
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_hash {

    /**
     * The password of which you wish to hash.
     * @var string $password The password string of which to create a hash from. 
     */
    var $password = null;

    /**
     * The 'salt' this should ideally be 22 characters in lengh. Only '.', '/' and aphanumrical values are allowed!
     * @var string $salt The salt to use for the hash process. (22 characters in lengh)
     */
    var $salt = null;

    /**
     * Cost, the strengh (number of rounds).
     * 10 = 1,024
     * 11 = 2,048
     * 12 = 4,086
     */
    var $cost = 12;

    /**
     * Class constructor, can specify password and salt here or alternatively use the setter methods.
     * @param string $password The password string of which to create a hash from.
     * @param string $salt The salt to use for the hash process. (22 characters in lengh)
     */
    public function __construct($password = '', $salt = '') {
        if (!empty($password))
            $this->password = $password;
        if (!empty($salt))
            $this->salt = $salt;
    }

    /**
     * Instead of using the constructor to set the password, you can also use this 'setter' method.
     * @param string $password The password string of which to create a hash from. 
     * @return boolean
     */
    public function SetPassword($password = '') {
        if (!empty($password)) {
            $this->password = $password;
        } else {
            return false;
        }
    }

    /**
     * Instead of using the constructor to set the salt, you can also use this 'setter' method.
     * @param string $salt The salt to use for the hash process. (22 characters in lengh)
     * @return boolean
     */
    public function SetSalt($salt = '') {
        if (!empty($salt)) {
            $this->salt = $salt;
        } else {
            return false;
        }
    }

    /**
     * A 'setter' method to set the cost (strenght) of the encryption.
     * @param int $cost Numbe of rounds (2 to the power 10) eg. 10 = 1,024, 11 = 2,048, 12 = 4,096 (Default is 12 (4,096))
     */
    public function SetCost($cost = 0) {
        if ($cost > 0) {
            $this->cost = $cost;
        } else {
            return false;
        }
    }

    /**
     * Main method for generating the bcrypt hash.
     * @return boolean Will return false if fails (the user didn't define a password or the version of PHP does not support Blowfish, PHP 5.3.0+ is required!) or the password hash if successful.
     */
    public function Crypt() {
        require_once ctrl_options::GetSystemOption('sentora_root') . 'cnf/security.php';
        global $security;
        if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
            $combined_salt = substr(sha1($this->salt . $security['server_crypto_key']), 0, 22);
            $salt = '$2a$' . $this->cost . '$' . $combined_salt . '$';
            return crypt($this->password, $salt);
        } else {
            // Returns false if PHP version is not higher than 5.3.0 (which implements the Blowfish encruption mechanism).
            return false;
        }
    }

    /**
     * Generates a valid 22 character random salt (using the correct valid characters).
     * @return string A valid random 22 character salt.
     */
    public function RandomSalt() {
        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $randomsalt = (string) null;
        for ($i = 0; $i < 22; $i++)
            $randomsalt.=$chars[rand(0, 63)];
        return $randomsalt;
    }

    /**
     * Breaks the generated bcrypt string down to extract the various parts (the salt and the hash from the entire string)
     * @param  string $crypto A generated Blowfish hash.
     * @return object Split parts of the cyrpto string.
     */
    public function CryptParts($crypto) {
        $algoritm = substr($crypto, 0, 4);
        $cost = substr($crypto, 4, 2);
        $salt = substr($crypto, 7, 22);
        $hash = substr($crypto, 29);
        $parts = array(
            'Algorithm' => $algoritm,
            'Cost' => $cost,
            'Salt' => $salt,
            'Hash' => $hash,
        );
        return (object) $parts;
    }

}

?>
