<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Console_CommandLine package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Console 
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Required by this class.
 */
require_once 'Console/CommandLine/Action.php';

/**
 * Class that represent the StoreArray action.
 *
 * The execute method appends the value of the option entered by the user to 
 * the result option array entry.
 *
 * @category  Console
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     Class available since release 0.1.0
 */
class Console_CommandLine_Action_StoreArray extends Console_CommandLine_Action
{
    // Protected properties {{{

    /**
     * Force a clean result when first called, overriding any defaults assigned.
     *
     * @var object $firstPass First time this action has been called.
     */
    protected $firstPass = true;

    // }}}
    // execute() {{{

    /**
     * Executes the action with the value entered by the user.
     *
     * @param mixed $value  The option value
     * @param array $params An optional array of parameters
     *
     * @return string
     */
    public function execute($value = false, $params = array())
    {
        $result = $this->getResult();
        if (null === $result || $this->firstPass) {
            $result          = array();
            $this->firstPass = false;
        }
        $result[] = $value;
        $this->setResult($result);
    }
    // }}}
}
