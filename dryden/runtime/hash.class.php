<?php

/**
 * Provides controller/framework execution debug tools.
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.2
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_hash {

   const DEFAULT_WORK_FACTOR = 8;

    public static function hash($password, $work_factor = 0) {
        if (version_compare(PHP_VERSION, '5.3') < 0)
            throw new Exception('Bcrypt requires PHP 5.3 or above');

        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new Exception('Bcrypt requires openssl PHP extension');
        }

        if ($work_factor < 4 || $work_factor > 31)
            $work_factor = self::DEFAULT_WORK_FACTOR;
        $salt =
                '$2a$' . str_pad($work_factor, 2, '0', STR_PAD_LEFT) . '$' .
                substr(
                        strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 0, 22
                )
        ;
        return crypt($password, $salt);
    }

    public static function check($password, $stored_hash, $legacy_handler = NULL) {
        if (version_compare(PHP_VERSION, '5.3') < 0)
            throw new Exception('Bcrypt requires PHP 5.3 or above');

        if (self::is_legacy_hash($stored_hash)) {
            if ($legacy_handler)
                return call_user_func($legacy_handler, $password, $stored_hash);
            else
                throw new Exception('Unsupported hash format');
        }

        return crypt($password, $stored_hash) == $stored_hash;
    }

    public static function is_legacy_hash($hash) {
        return substr($hash, 0, 4) != '$2a$';
    }

}

?>
