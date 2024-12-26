<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Various exception handling classes for Crypt_GPG
 *
 * Crypt_GPG provides an object oriented interface to GNU Privacy
 * Guard (GPG). It requires the GPG executable to be on the system.
 *
 * This file contains various exception classes used by the Crypt_GPG package.
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of the
 * License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, see
 * <http://www.gnu.org/licenses/>
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Nathan Fredrickson <nathan@silverorange.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005-2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */

/**
 * PEAR Exception handler and base class
 */
require_once 'PEAR/Exception.php';

/**
 * An exception thrown by the Crypt_GPG package
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_Exception extends PEAR_Exception
{
}

/**
 * An exception thrown when a file is used in ways it cannot be used
 *
 * For example, if an output file is specified and the file is not writeable, or
 * if an input file is specified and the file is not readable, this exception
 * is thrown.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2007-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_FileException extends Crypt_GPG_Exception
{
    /**
     * The name of the file that caused this exception
     *
     * @var string
     */
    private $_filename = '';

    /**
     * Creates a new Crypt_GPG_FileException
     *
     * @param string $message  An error message.
     * @param int    $code     A user defined error code.
     * @param string $filename The name of the file that caused this exception.
     */
    public function __construct($message, $code = 0, $filename = '')
    {
        $this->_filename = $filename;
        parent::__construct($message, $code);
    }

    /**
     * Returns the filename of the file that caused this exception
     *
     * @return string the filename of the file that caused this exception.
     *
     * @see Crypt_GPG_FileException::$_filename
     */
    public function getFilename()
    {
        return $this->_filename;
    }
}

/**
 * An exception thrown when the GPG subprocess cannot be opened
 *
 * This exception is thrown when the {@link Crypt_GPG_Engine} tries to open a
 * new subprocess and fails.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_OpenSubprocessException extends Crypt_GPG_Exception
{
    /**
     * The command used to try to open the subprocess
     *
     * @var string
     */
    private $_command = '';

    /**
     * Creates a new Crypt_GPG_OpenSubprocessException
     *
     * @param string $message An error message.
     * @param int    $code    A user defined error code.
     * @param string $command The command that was called to open the
     *                        new subprocess.
     *
     * @see Crypt_GPG::_openSubprocess()
     */
    public function __construct($message, $code = 0, $command = '')
    {
        $this->_command = $command;
        parent::__construct($message, $code);
    }

    /**
     * Returns the contents of the internal _command property
     *
     * @return string the command used to open the subprocess.
     *
     * @see Crypt_GPG_OpenSubprocessException::$_command
     */
    public function getCommand()
    {
        return $this->_command;
    }
}

/**
 * An exception thrown when an invalid GPG operation is attempted
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_InvalidOperationException extends Crypt_GPG_Exception
{
    /**
     * The attempted operation
     *
     * @var string
     */
    private $_operation = '';

    /**
     * Creates a new Crypt_GPG_OpenSubprocessException
     *
     * @param string $message   An error message.
     * @param int    $code      A user defined error code.
     * @param string $operation The operation.
     */
    public function __construct($message, $code = 0, $operation = '')
    {
        $this->_operation = $operation;
        parent::__construct($message, $code);
    }

    /**
     * Returns the contents of the internal _operation property
     *
     * @return string the attempted operation.
     *
     * @see Crypt_GPG_InvalidOperationException::$_operation
     */
    public function getOperation()
    {
        return $this->_operation;
    }
}

/**
 * An exception thrown when Crypt_GPG fails to find the key for various
 * operations
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_KeyNotFoundException extends Crypt_GPG_Exception
{
    /**
     * The key identifier that was searched for
     *
     * @var string
     */
    private $_keyId = '';

    /**
     * Creates a new Crypt_GPG_KeyNotFoundException
     *
     * @param string $message An error message.
     * @param int    $code    A user defined error code.
     * @param string $keyId   The key identifier of the key.
     */
    public function __construct($message, $code = 0, $keyId= '')
    {
        $this->_keyId = $keyId;
        parent::__construct($message, $code);
    }

    /**
     * Gets the key identifier of the key that was not found
     *
     * @return string the key identifier of the key that was not found.
     */
    public function getKeyId()
    {
        return $this->_keyId;
    }
}

/**
 * An exception thrown when Crypt_GPG cannot find valid data for various
 * operations
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_NoDataException extends Crypt_GPG_Exception
{
}

/**
 * An exception thrown when a required passphrase is incorrect or missing
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2006-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_BadPassphraseException extends Crypt_GPG_Exception
{
    /**
     * Keys for which the passhprase is missing
     *
     * This contains primary user ids indexed by sub-key id.
     *
     * @var array
     */
    private $_missingPassphrases = array();

    /**
     * Keys for which the passhprase is incorrect
     *
     * This contains primary user ids indexed by sub-key id.
     *
     * @var array
     */
    private $_badPassphrases = array();

    /**
     * Creates a new Crypt_GPG_BadPassphraseException
     *
     * @param string $message            An error message.
     * @param int    $code               A user defined error code.
     * @param array  $badPassphrases     An array containing user ids of keys
     *                                   for which the passphrase is incorrect.
     * @param array  $missingPassphrases An array containing user ids of keys
     *                                   for which the passphrase is missing.
     */
    public function __construct($message, $code = 0,
        array $badPassphrases = array(), array $missingPassphrases = array()
    ) {
        $this->_badPassphrases     = (array) $badPassphrases;
        $this->_missingPassphrases = (array) $missingPassphrases;

        parent::__construct($message, $code);
    }

    /**
     * Gets keys for which the passhprase is incorrect
     *
     * @return array an array of keys for which the passphrase is incorrect.
     *               The array contains primary user ids indexed by the sub-key
     *               id.
     */
    public function getBadPassphrases()
    {
        return $this->_badPassphrases;
    }

    /**
     * Gets keys for which the passhprase is missing 
     *
     * @return array an array of keys for which the passphrase is missing.
     *               The array contains primary user ids indexed by the sub-key
     *               id.
     */
    public function getMissingPassphrases()
    {
        return $this->_missingPassphrases;
    }
}

/**
 * An exception thrown when an attempt is made to delete public key that has an
 * associated private key on the keyring
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_DeletePrivateKeyException extends Crypt_GPG_Exception
{
    /**
     * The key identifier the deletion attempt was made upon
     *
     * @var string
     */
    private $_keyId = '';

    /**
     * Creates a new Crypt_GPG_DeletePrivateKeyException
     *
     * @param string $message An error message.
     * @param int    $code    A user defined error code.
     * @param string $keyId   The key identifier of the public key that was
     *                        attempted to delete.
     *
     * @see Crypt_GPG::deletePublicKey()
     */
    public function __construct($message, $code = 0, $keyId = '')
    {
        $this->_keyId = $keyId;
        parent::__construct($message, $code);
    }

    /**
     * Gets the key identifier of the key that was not found
     *
     * @return string the key identifier of the key that was not found.
     */
    public function getKeyId()
    {
        return $this->_keyId;
    }
}

/**
 * An exception thrown when an attempt is made to generate a key and the
 * attempt fails
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_KeyNotCreatedException extends Crypt_GPG_Exception
{
}

/**
 * An exception thrown when an attempt is made to generate a key and the
 * key parameters set on the key generator are invalid
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class Crypt_GPG_InvalidKeyParamsException extends Crypt_GPG_Exception
{
    /**
     * The key algorithm
     *
     * @var int
     */
    private $_algorithm = 0;

    /**
     * The key size
     *
     * @var int
     */
    private $_size = 0;

    /**
     * The key usage
     *
     * @var int
     */
    private $_usage = 0;

    /**
     * Creates a new Crypt_GPG_InvalidKeyParamsException
     *
     * @param string $message   An error message.
     * @param int    $code      A user defined error code.
     * @param int    $algorithm The key algorithm.
     * @param int    $size      The key size.
     * @param int    $usage     The key usage.
     */
    public function __construct(
        $message,
        $code = 0,
        $algorithm = 0,
        $size = 0,
        $usage = 0
    ) {
        parent::__construct($message, $code);

        $this->_algorithm = $algorithm;
        $this->_size      = $size;
        $this->_usage     = $usage;
    }

    /**
     * Gets the key algorithm
     *
     * @return int The key algorithm.
     */
    public function getAlgorithm()
    {
        return $this->_algorithm;
    }

    /**
     * Gets the key size
     *
     * @return int The key size.
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Gets the key usage
     *
     * @return int The key usage.
     */
    public function getUsage()
    {
        return $this->_usage;
    }
}
