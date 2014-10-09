<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Cookie Authentication plugin for phpMyAdmin
 *
 * @package    PhpMyAdmin-Authentication
 * @subpackage Cookie
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/* Get the authentication interface */
require_once 'libraries/plugins/AuthenticationPlugin.class.php';

/**
 * Remember where to redirect the user
 * in case of an expired session.
 */
if (! empty($_REQUEST['target'])) {
    $GLOBALS['target'] = $_REQUEST['target'];
} else if (PMA_getenv('SCRIPT_NAME')) {
    $GLOBALS['target'] = basename(PMA_getenv('SCRIPT_NAME'));
}

/**
 * Swekey authentication functions.
 */
require './libraries/plugins/auth/swekey/swekey.auth.lib.php';

/**
 * Initialization
 * Store the initialization vector because it will be needed for
 * further decryption. I don't think necessary to have one iv
 * per server so I don't put the server number in the cookie name.
 */
if (function_exists('mcrypt_encrypt')) {
    if (empty($_COOKIE['pma_mcrypt_iv'])
        || ! ($iv = base64_decode($_COOKIE['pma_mcrypt_iv'], true))
    ) {
        srand((double) microtime() * 1000000);
        $td = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
        if ($td === false) {
            PMA_fatalError(__('Failed to use Blowfish from mcrypt!'));
        }
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $GLOBALS['PMA_Config']->setCookie(
            'pma_mcrypt_iv',
            base64_encode($iv)
        );
    }
}

/**
 * Handles the cookie authentication method
 *
 * @package PhpMyAdmin-Authentication
 */
class AuthenticationCookie extends AuthenticationPlugin
{
    /**
     * Displays authentication form
     *
     * this function MUST exit/quit the application
     *
     * @global string the last connection error
     *
     * @return void
     */
    public function auth()
    {
        global $conn_error;

        $response = PMA_Response::getInstance();
        if ($response->isAjax()) {
            $response->isSuccess(false);

            $login_link = '<br /><br />[ ' .
                sprintf(
                    '<a href="%s" class="ajax login-link">%s</a>', 
                    $GLOBALS['cfg']['PmaAbsoluteUri'], 
                    __('Log in')
                )
                . ' ]';

            if (! empty($conn_error)) {

                $conn_error .= $login_link;

                $response->addJSON(
                    'message',
                    PMA_Message::error(
                        $conn_error
                    )
                );
            } else {
                $response->addJSON(
                    'message',
                    PMA_Message::error(
                        __('Your session has expired. Please log in again.') .
                        $login_link
                    )
                );
            }
            exit;
        }

        /* Perform logout to custom URL */
        if (! empty($_REQUEST['old_usr'])
            && ! empty($GLOBALS['cfg']['Server']['LogoutURL'])
        ) {
            PMA_sendHeaderLocation($GLOBALS['cfg']['Server']['LogoutURL']);
            exit;
        }

        // No recall if blowfish secret is not configured as it would produce
        // garbage
        if ($GLOBALS['cfg']['LoginCookieRecall']
            && ! empty($GLOBALS['cfg']['blowfish_secret'])
        ) {
            $default_user   = $GLOBALS['PHP_AUTH_USER'];
            $default_server = $GLOBALS['pma_auth_server'];
            $autocomplete   = '';
        } else {
            $default_user   = '';
            $default_server = '';
            // skip the IE autocomplete feature.
            $autocomplete   = ' autocomplete="off"';
        }

        $cell_align = ($GLOBALS['text_dir'] == 'ltr') ? 'left' : 'right';

        $response->getFooter()->setMinimal();
        $header = $response->getHeader();
        $header->setBodyId('loginform');
        $header->setTitle('phpMyAdmin');
        $header->disableMenu();
        $header->disableWarnings();

        if (file_exists(CUSTOM_HEADER_FILE)) {
            include CUSTOM_HEADER_FILE;
        }
        echo '
    <div class="container">
    <a href="';
        echo PMA_linkURL('http://www.phpmyadmin.net/');
        echo '" target="_blank" class="logo">';
        $logo_image = $GLOBALS['pmaThemeImage'] . 'logo_right.png';
        if (@file_exists($logo_image)) {
            echo '<img src="' . $logo_image
                . '" id="imLogo" name="imLogo" alt="phpMyAdmin" border="0" />';
        } else {
            echo '<img name="imLogo" id="imLogo" src="'
                . $GLOBALS['pmaThemeImage'] . 'pma_logo.png' . '" '
                . 'border="0" width="88" height="31" alt="phpMyAdmin" />';
        }
        echo '</a>
       <h1>';
        echo sprintf(
            __('Welcome to %s'),
            '<bdo dir="ltr" lang="en">phpMyAdmin</bdo>'
        );
        echo "</h1>";

        // Show error message
        if (! empty($conn_error)) {
            PMA_Message::rawError($conn_error)->display();
        }

        echo "<noscript>\n";
        PMA_message::error(
            __("Javascript must be enabled past this point")
        )->display();
        echo "</noscript>\n";

        echo "<div class='hide js-show'>";
        // Displays the languages form
        if (empty($GLOBALS['cfg']['Lang'])) {
            include_once './libraries/display_select_lang.lib.php';
            // use fieldset, don't show doc link
            echo PMA_getLanguageSelectorHtml(true, false);
        }
        echo '</div>
    <br />
    <!-- Login form -->
    <form method="post" action="index.php" name="login_form"' . $autocomplete .
            ' class="disableAjax login hide js-show">
        <fieldset>
        <legend>';
        echo __('Log in');
        echo PMA_Util::showDocu('index');
        echo '</legend>';
        if ($GLOBALS['cfg']['AllowArbitraryServer']) {
            echo '
            <div class="item">
                <label for="input_servername" title="';
            echo __(
                'You can enter hostname/IP address and port separated by space.'
            );
            echo '">';
            echo __('Server:');
            echo '</label>
                <input type="text" name="pma_servername" id="input_servername"';
            echo ' value="';
            echo htmlspecialchars($default_server);
            echo '" size="24" class="textfield" title="';
            echo __(
                'You can enter hostname/IP address and port separated by space.'
            ); echo '" />
            </div>';
        }
            echo '<div class="item">
                <label for="input_username">' . __('Username:') . '</label>
                <input type="text" name="pma_username" id="input_username" '
                . 'value="' . htmlspecialchars($default_user) . '" size="24"'
                . ' class="textfield"/>
            </div>
            <div class="item">
                <label for="input_password">' . __('Password:') . '</label>
                <input type="password" name="pma_password" id="input_password"'
                . ' value="" size="24" class="textfield" />
            </div>';
        if (count($GLOBALS['cfg']['Servers']) > 1) {
            echo '<div class="item">
                <label for="select_server">' . __('Server Choice') .':</label>
                <select name="server" id="select_server"';
            if ($GLOBALS['cfg']['AllowArbitraryServer']) {
                echo ' onchange="document.forms[\'login_form\'].'
                    . 'elements[\'pma_servername\'].value = \'\'" ';
            }
            echo '>';

            include_once './libraries/select_server.lib.php';
            echo PMA_selectServer(false, false);

            echo '</select></div>';
        } else {
            echo '    <input type="hidden" name="server" value="'
                . $GLOBALS['server'] . '" />';
        } // end if (server choice)

        echo '</fieldset>
        <fieldset class="tblFooters">
            <input value="' . __('Go') . '" type="submit" id="input_go" />';
        $_form_params = array();
        if (! empty($GLOBALS['target'])) {
            $_form_params['target'] = $GLOBALS['target'];
        }
        if (! empty($GLOBALS['db'])) {
            $_form_params['db'] = $GLOBALS['db'];
        }
        if (! empty($GLOBALS['table'])) {
            $_form_params['table'] = $GLOBALS['table'];
        }
        // do not generate a "server" hidden field as we want the "server"
        // drop-down to have priority
        echo PMA_generate_common_hidden_inputs($_form_params, '', 0, 'server');
        echo '</fieldset>
    </form>';

        // BEGIN Swekey Integration
        Swekey_login('input_username', 'input_go');
        // END Swekey Integration

        if ($GLOBALS['error_handler']->hasDisplayErrors()) {
            echo '<div>';
            $GLOBALS['error_handler']->dispErrors();
            echo '</div>';
        }
        echo '</div>';
        if (file_exists(CUSTOM_FOOTER_FILE)) {
            include CUSTOM_FOOTER_FILE;
        }
        exit;
    }

    /**
     * Gets advanced authentication settings
     *
     * this function DOES NOT check authentication - it just checks/provides
     * authentication credentials required to connect to the MySQL server
     * usually with PMA_DBI_connect()
     *
     * it returns false if something is missing - which usually leads to
     * auth() which displays login form
     *
     * it returns true if all seems ok which usually leads to auth_set_user()
     *
     * it directly switches to authFails() if user inactivity timout is reached
     *
     * @todo    AllowArbitraryServer on does not imply that the user wants an
     *          arbitrary server, or? so we should also check if this is filled
     *          and not only if allowed
     *
     * @return boolean   whether we get authentication settings or not
     */
    public function authCheck()
    {
        // Initialization
        /**
         * @global $GLOBALS['pma_auth_server'] the user provided server to
         * connect to
         */
        $GLOBALS['pma_auth_server'] = '';

        $GLOBALS['PHP_AUTH_USER'] = $GLOBALS['PHP_AUTH_PW'] = '';
        $GLOBALS['from_cookie'] = false;

        // BEGIN Swekey Integration
        if (! Swekey_auth_check()) {
            return false;
        }
        // END Swekey Integration

        if (defined('PMA_CLEAR_COOKIES')) {
            foreach ($GLOBALS['cfg']['Servers'] as $key => $val) {
                $GLOBALS['PMA_Config']->removeCookie('pmaPass-' . $key);
                $GLOBALS['PMA_Config']->removeCookie('pmaServer-' . $key);
                $GLOBALS['PMA_Config']->removeCookie('pmaUser-' . $key);
            }
            return false;
        }

        if (! empty($_REQUEST['old_usr'])) {
            // The user wants to be logged out
            // -> delete his choices that were stored in session

            // according to the PHP manual we should do this before the destroy:
            //$_SESSION = array();

            session_destroy();
            // -> delete password cookie(s)
            if ($GLOBALS['cfg']['LoginCookieDeleteAll']) {
                foreach ($GLOBALS['cfg']['Servers'] as $key => $val) {
                    $GLOBALS['PMA_Config']->removeCookie('pmaPass-' . $key);
                    if (isset($_COOKIE['pmaPass-' . $key])) {
                        unset($_COOKIE['pmaPass-' . $key]);
                    }
                }
            } else {
                $GLOBALS['PMA_Config']->removeCookie(
                    'pmaPass-' . $GLOBALS['server']
                );
                if (isset($_COOKIE['pmaPass-' . $GLOBALS['server']])) {
                    unset($_COOKIE['pmaPass-' . $GLOBALS['server']]);
                }
            }
        }

        if (! empty($_REQUEST['pma_username'])) {
            // The user just logged in
            $GLOBALS['PHP_AUTH_USER'] = $_REQUEST['pma_username'];
            $GLOBALS['PHP_AUTH_PW']   = empty($_REQUEST['pma_password'])
                ? ''
                : $_REQUEST['pma_password'];
            if ($GLOBALS['cfg']['AllowArbitraryServer']
                && isset($_REQUEST['pma_servername'])
            ) {
                $GLOBALS['pma_auth_server'] = $_REQUEST['pma_servername'];
            }
            return true;
        }

        // At the end, try to set the $GLOBALS['PHP_AUTH_USER']
        // and $GLOBALS['PHP_AUTH_PW'] variables from cookies

        // servername
        if ($GLOBALS['cfg']['AllowArbitraryServer']
            && ! empty($_COOKIE['pmaServer-' . $GLOBALS['server']])
        ) {
            $GLOBALS['pma_auth_server']
                = $_COOKIE['pmaServer-' . $GLOBALS['server']];
        }

        // username
        if (empty($_COOKIE['pmaUser-' . $GLOBALS['server']])) {
            return false;
        }

        $GLOBALS['PHP_AUTH_USER'] = $this->blowfishDecrypt(
            $_COOKIE['pmaUser-' . $GLOBALS['server']],
            $this->_getBlowfishSecret()
        );

        // user was never logged in since session start
        if (empty($_SESSION['last_access_time'])) {
            return false;
        }

        // User inactive too long
        $last_access_time = time() - $GLOBALS['cfg']['LoginCookieValidity'];
        if ($_SESSION['last_access_time'] < $last_access_time
        ) {
            PMA_Util::cacheUnset('is_create_db_priv', true);
            PMA_Util::cacheUnset('is_process_priv', true);
            PMA_Util::cacheUnset('is_reload_priv', true);
            PMA_Util::cacheUnset('db_to_create', true);
            PMA_Util::cacheUnset('dbs_where_create_table_allowed', true);
            $GLOBALS['no_activity'] = true;
            $this->authFails();
            exit;
        }

        // password
        if (empty($_COOKIE['pmaPass-' . $GLOBALS['server']])) {
            return false;
        }

        $GLOBALS['PHP_AUTH_PW'] = $this->blowfishDecrypt(
            $_COOKIE['pmaPass-' . $GLOBALS['server']],
            $this->_getBlowfishSecret()
        );

        if ($GLOBALS['PHP_AUTH_PW'] == "\xff(blank)") {
            $GLOBALS['PHP_AUTH_PW'] = '';
        }

        $GLOBALS['from_cookie'] = true;

        return true;
    }

    /**
     * Set the user and password after last checkings if required
     *
     * @return boolean always true
     */
    public function authSetUser()
    {
        global $cfg;

        // Ensures valid authentication mode, 'only_db', bookmark database and
        // table names and relation table name are used
        if ($cfg['Server']['user'] != $GLOBALS['PHP_AUTH_USER']) {
            foreach ($cfg['Servers'] as $idx => $current) {
                if ($current['host'] == $cfg['Server']['host']
                    && $current['port'] == $cfg['Server']['port']
                    && $current['socket'] == $cfg['Server']['socket']
                    && $current['ssl'] == $cfg['Server']['ssl']
                    && $current['connect_type'] == $cfg['Server']['connect_type']
                    && $current['user'] == $GLOBALS['PHP_AUTH_USER']
                ) {
                    $GLOBALS['server'] = $idx;
                    $cfg['Server']     = $current;
                    break;
                }
            } // end foreach
        } // end if

        if ($GLOBALS['cfg']['AllowArbitraryServer']
            && ! empty($GLOBALS['pma_auth_server'])
        ) {
            /* Allow to specify 'host port' */
            $parts = explode(' ', $GLOBALS['pma_auth_server']);
            if (count($parts) == 2) {
                $tmp_host = $parts[0];
                $tmp_port = $parts[1];
            } else {
                $tmp_host = $GLOBALS['pma_auth_server'];
                $tmp_port = '';
            }
            if ($cfg['Server']['host'] != $GLOBALS['pma_auth_server']) {
                $cfg['Server']['host'] = $tmp_host;
                if (! empty($tmp_port)) {
                    $cfg['Server']['port'] = $tmp_port;
                }
            }
            unset($tmp_host, $tmp_port, $parts);
        }
        $cfg['Server']['user']     = $GLOBALS['PHP_AUTH_USER'];
        $cfg['Server']['password'] = $GLOBALS['PHP_AUTH_PW'];

        // Avoid showing the password in phpinfo()'s output
        unset($GLOBALS['PHP_AUTH_PW']);
        unset($_SERVER['PHP_AUTH_PW']);

        $_SESSION['last_access_time'] = time();

        // Name and password cookies need to be refreshed each time
        // Duration = one month for username
        $GLOBALS['PMA_Config']->setCookie(
            'pmaUser-' . $GLOBALS['server'],
            $this->blowfishEncrypt(
                $cfg['Server']['user'],
                $this->_getBlowfishSecret()
            )
        );

        // Duration = as configured
        $GLOBALS['PMA_Config']->setCookie(
            'pmaPass-' . $GLOBALS['server'],
            $this->blowfishEncrypt(
                ! empty($cfg['Server']['password'])
                ? $cfg['Server']['password'] : "\xff(blank)",
                $this->_getBlowfishSecret()
            ),
            null,
            $GLOBALS['cfg']['LoginCookieStore']
        );

        // Set server cookies if required (once per session) and, in this case,
        // force reload to ensure the client accepts cookies
        if (! $GLOBALS['from_cookie']) {
            if ($GLOBALS['cfg']['AllowArbitraryServer']) {
                if (! empty($GLOBALS['pma_auth_server'])) {
                    // Duration = one month for servername
                    $GLOBALS['PMA_Config']->setCookie(
                        'pmaServer-' . $GLOBALS['server'],
                        $cfg['Server']['host']
                    );
                } else {
                    // Delete servername cookie
                    $GLOBALS['PMA_Config']->removeCookie(
                        'pmaServer-' . $GLOBALS['server']
                    );
                }
            }

            // URL where to go:
            $redirect_url = $cfg['PmaAbsoluteUri'] . 'index.php';

            // any parameters to pass?
            $url_params = array();
            if (strlen($GLOBALS['db'])) {
                $url_params['db'] = $GLOBALS['db'];
            }
            if (strlen($GLOBALS['table'])) {
                $url_params['table'] = $GLOBALS['table'];
            }
            // any target to pass?
            if (! empty($GLOBALS['target'])
                && $GLOBALS['target'] != 'index.php'
            ) {
                $url_params['target'] = $GLOBALS['target'];
            }

            /**
             * Clear user cache.
             */
            PMA_Util::clearUserCache();

            PMA_Response::getInstance()->disable();

            PMA_sendHeaderLocation(
                $redirect_url . PMA_generate_common_url($url_params, '&'),
                true
            );
            exit;
        } // end if

        return true;

    }

    /**
     * User is not allowed to login to MySQL -> authentication failed
     *
     * prepares error message and switches to auth() which display the error
     * and the login form
     *
     * this function MUST exit/quit the application,
     * currently doen by call to auth()
     *
     * @return void
     */
    public function authFails()
    {
        global $conn_error;

        // Deletes password cookie and displays the login form
        $GLOBALS['PMA_Config']->removeCookie('pmaPass-' . $GLOBALS['server']);

        if (! empty($GLOBALS['login_without_password_is_forbidden'])) {
            $conn_error = __(
                'Login without a password is forbidden by configuration'
                . ' (see AllowNoPassword)'
            );
        } elseif (! empty($GLOBALS['allowDeny_forbidden'])) {
            $conn_error = __('Access denied');
        } elseif (! empty($GLOBALS['no_activity'])) {
            $conn_error = sprintf(
                __('No activity within %s seconds; please log in again'),
                $GLOBALS['cfg']['LoginCookieValidity']
            );
        } elseif (PMA_DBI_getError()) {
            $conn_error = '#' . $GLOBALS['errno'] . ' '
                . __('Cannot log in to the MySQL server');
        } else {
            $conn_error = __('Cannot log in to the MySQL server');
        }

        // needed for PHP-CGI (not need for FastCGI or mod-php)
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $this->auth();
    }

    /**
     * Returns blowfish secret or generates one if needed.
     *
     * @return string
     */
    private function _getBlowfishSecret()
    {
        if (empty($GLOBALS['cfg']['blowfish_secret'])) {
            if (empty($_SESSION['auto_blowfish_secret'])) {
                // this returns 23 characters
                $_SESSION['auto_blowfish_secret'] = uniqid('', true);
            }
            return $_SESSION['auto_blowfish_secret'];
        } else {
            // apply md5() to work around too long secrets (returns 32 characters)
            return md5($GLOBALS['cfg']['blowfish_secret']);
        }
    }

    /**
     * Encryption using blowfish algorithm (mcrypt)
     * or phpseclib's AES if mcrypt not available
     *
     * @param string $data   original data
     * @param string $secret the secret
     *
     * @return string the encrypted result
     */
    public function blowfishEncrypt($data, $secret)
    {
        global $iv;
        if (! function_exists('mcrypt_encrypt')) {
            /**
             * This library uses mcrypt when available, so
             * we could always call it instead of having an
             * if/then/else logic, however the include_once
             * call is costly
             */
            include_once "./libraries/phpseclib/Crypt/AES.php";
            $cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
            $cipher->setKey($secret);
            return base64_encode($cipher->encrypt($data));
        } else {
            return base64_encode(
                mcrypt_encrypt(
                    MCRYPT_BLOWFISH,
                    $secret,
                    $data,
                    MCRYPT_MODE_CBC,
                    $iv
                )
            );
        }
    }

    /**
     * Decryption using blowfish algorithm (mcrypt)
     * or phpseclib's AES if mcrypt not available
     *
     * @param string $encdata encrypted data
     * @param string $secret  the secret
     *
     * @return string original data
     */
    public function blowfishDecrypt($encdata, $secret)
    {
        global $iv;
        if (! function_exists('mcrypt_encrypt')) {
            include_once "./libraries/phpseclib/Crypt/AES.php";
            $cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
            $cipher->setKey($secret);
            return $cipher->decrypt(base64_decode($encdata));
        } else {
            $data = base64_decode($encdata);
            $decrypted = mcrypt_decrypt(
                MCRYPT_BLOWFISH,
                $secret,
                $data,
                MCRYPT_MODE_CBC,
                $iv
            );
            return trim($decrypted);
        }
    }

    /**
     * This method is called when any PluginManager to which the observer
     * is attached calls PluginManager::notify()
     *
     * @param SplSubject $subject The PluginManager notifying the observer
     *                            of an update.
     *
     * @return void
     */
    public function update (SplSubject $subject)
    {
    }
}
