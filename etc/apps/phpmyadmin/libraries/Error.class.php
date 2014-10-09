<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Holds class PMA_Error
 *
 * @package PhpMyAdmin
 */

if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * base class
 */
require_once './libraries/Message.class.php';

/**
 * a single error
 *
 * @package PhpMyAdmin
 */
class PMA_Error extends PMA_Message
{
    /**
     * Error types
     *
     * @var array
     */
    static public $errortype = array (
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_DEPRECATED         => 'Deprecation Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
    );

    /**
     * Error levels
     *
     * @var array
     */
    static public $errorlevel = array (
        E_ERROR              => 'error',
        E_WARNING            => 'error',
        E_PARSE              => 'error',
        E_NOTICE             => 'notice',
        E_CORE_ERROR         => 'error',
        E_CORE_WARNING       => 'error',
        E_COMPILE_ERROR      => 'error',
        E_COMPILE_WARNING    => 'error',
        E_USER_ERROR         => 'error',
        E_USER_WARNING       => 'error',
        E_USER_NOTICE        => 'notice',
        E_STRICT             => 'notice',
        E_DEPRECATED         => 'notice',
        E_RECOVERABLE_ERROR  => 'error',
    );

    /**
     * The file in which the error occured
     *
     * @var string
     */
    protected $file = '';

    /**
     * The line in which the error occured
     *
     * @var integer
     */
    protected $line = 0;

    /**
     * Holds the backtrace for this error
     *
     * @var array
     */
    protected $backtrace = array();

    /**
     * Unique id
     *
     * @var string
     */
    protected $hash = null;

    /**
     * Constructor
     *
     * @param integer $errno   error number
     * @param string  $errstr  error message
     * @param string  $errfile file
     * @param integer $errline line
     */
    public function __construct($errno, $errstr, $errfile, $errline)
    {
        $this->setNumber($errno);
        $this->setMessage($errstr, false);
        $this->setFile($errfile);
        $this->setLine($errline);

        $backtrace = debug_backtrace();
        // remove last three calls:
        // debug_backtrace(), handleError() and addError()
        $backtrace = array_slice($backtrace, 3);

        $this->setBacktrace($backtrace);
    }

    /**
     * sets PMA_Error::$_backtrace
     *
     * @param array $backtrace backtrace
     *
     * @return void
     */
    public function setBacktrace($backtrace)
    {
        $this->backtrace = $backtrace;
    }

    /**
     * sets PMA_Error::$_line
     *
     * @param integer $line the line
     *
     * @return void
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * sets PMA_Error::$_file
     *
     * @param string $file the file
     *
     * @return void
     */
    public function setFile($file)
    {
        $this->file = PMA_Error::relPath($file);
    }


    /**
     * returns unique PMA_Error::$hash, if not exists it will be created
     *
     * @return string PMA_Error::$hash
     */
    public function getHash()
    {
        try {
            $backtrace = serialize($this->getBacktrace());
        } catch(Exception $e) {
            $backtrace = '';
        }
        if ($this->hash === null) {
            $this->hash = md5(
                $this->getNumber() .
                $this->getMessage() .
                $this->getFile() .
                $this->getLine() .
                $backtrace
            );
        }

        return $this->hash;
    }

    /**
     * returns PMA_Error::$_backtrace
     *
     * @return array PMA_Error::$_backtrace
     */
    public function getBacktrace()
    {
        return $this->backtrace;
    }

    /**
     * returns PMA_Error::$file
     *
     * @return string PMA_Error::$file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * returns PMA_Error::$line
     *
     * @return integer PMA_Error::$line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * returns type of error
     *
     * @return string  type of error
     */
    public function getType()
    {
        return PMA_Error::$errortype[$this->getNumber()];
    }

    /**
     * returns level of error
     *
     * @return string  level of error
     */
    public function getLevel()
    {
        return PMA_Error::$errorlevel[$this->getNumber()];
    }

    /**
     * returns title prepared for HTML Title-Tag
     *
     * @return string   HTML escaped and truncated title
     */
    public function getHtmlTitle()
    {
        return htmlspecialchars(substr($this->getTitle(), 0, 100));
    }

    /**
     * returns title for error
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getType() . ': ' . $this->getMessage();
    }

    /**
     * Get HTML backtrace
     *
     * @return void
     */
    public function getBacktraceDisplay()
    {
        $retval = '';

        foreach ($this->getBacktrace() as $step) {
            if (isset($step['file']) && isset($step['line'])) {
                $retval .= PMA_Error::relPath($step['file']) . '#' . $step['line'] . ': ';
            }
            if (isset($step['class'])) {
                $retval .= $step['class'] . $step['type'];
            }
            $retval .= $step['function'] . '(';
            if (isset($step['args']) && (count($step['args']) > 1)) {
                $retval .= "<br />\n";
                foreach ($step['args'] as $arg) {
                    $retval .= "\t";
                    $retval .= $this->getArg($arg, $step['function']);
                    $retval .= ',' . "<br />\n";
                }
            } elseif (isset($step['args']) && (count($step['args']) > 0)) {
                foreach ($step['args'] as $arg) {
                    $retval .= $this->getArg($arg, $step['function']);
                }
            }
            $retval .= ')' . "<br />\n";
        }

        return $retval;
    }

    /**
     * Get a single function argument
     *
     * if $function is one of include/require
     * the $arg is converted to a relative path
     *
     * @param string $arg
     * @param string $function
     *
     * @return string
     */
    protected function getArg($arg, $function)
    {
        $retval = '';
        $include_functions = array(
            'include',
            'include_once',
            'require',
            'require_once',
        );
        $connect_functions = array(
            'mysql_connect',
            'mysql_pconnect',
            'mysqli_connect',
            'mysqli_real_connect',
            'PMA_DBI_connect',
            'PMA_DBI_real_connect',
        );

        if (in_array($function, $include_functions)) {
            $retval .= PMA_Error::relPath($arg);
        } elseif (in_array($function, $connect_functions)
            && getType($arg) === 'string'
        ) {
            $retval .= getType($arg) . ' ********';
        } elseif (is_scalar($arg)) {
            $retval .= getType($arg) . ' ' . htmlspecialchars($arg);
        } else {
            $retval .= getType($arg);
        }

        return $retval;
    }

    /**
     * Gets the error as string of HTML
     *
     * @return string
     */
    public function getDisplay()
    {
        $this->isDisplayed(true);
        $retval = '<div class="' . $this->getLevel() . '">';
        if (! $this->isUserError()) {
            $retval .= '<strong>' . $this->getType() . '</strong>';
            $retval .= ' in ' . $this->getFile() . '#' . $this->getLine();
            $retval .= "<br />\n";
        }
        $retval .= $this->getMessage();
        if (! $this->isUserError()) {
            $retval .= "<br />\n";
            $retval .= "<br />\n";
            $retval .= "<strong>Backtrace</strong><br />\n";
            $retval .= "<br />\n";
            $retval .= $this->getBacktraceDisplay();
        }
        $retval .= '</div>';

        return $retval;
    }

    /**
     * whether this error is a user error
     *
     * @return boolean
     */
    public function isUserError()
    {
        return $this->getNumber() & (E_USER_WARNING | E_USER_ERROR | E_USER_NOTICE);
    }

    /**
     * return short relative path to phpMyAdmin basedir
     *
     * prevent path disclusore in error message,
     * and make users feel save to submit error reports
     *
     * @param string $dest path to be shorten
     *
     * @return string shortened path
     * @static
     */
    static function relPath($dest)
    {
        $dest = realpath($dest);

        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $path_separator = '\\';
        } else {
            $path_separator = '/';
        }

        $Ahere = explode(
            $path_separator,
            realpath(dirname(__FILE__) . $path_separator . '..')
        );
        $Adest = explode($path_separator, $dest);

        $result = '.';
        // && count ($Adest)>0 && count($Ahere)>0 )
        while (implode($path_separator, $Adest) != implode($path_separator, $Ahere)) {
            if (count($Ahere) > count($Adest)) {
                array_pop($Ahere);
                $result .= $path_separator . '..';
            } else {
                array_pop($Adest);
            }
        }
        $path = $result . str_replace(implode($path_separator, $Adest), '', $dest);
        return str_replace(
            $path_separator . $path_separator,
            $path_separator,
            $path
        );
    }
}
?>
