<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A class representing GPG signatures
 *
 * This file contains a data class representing a GPG signature.
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
 * @copyright 2005-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */

/**
 * User id class definition
 */
require_once 'Crypt/GPG/UserId.php';

/**
 * A class for GPG signature information
 *
 * This class is used to store the results of the Crypt_GPG::verify() method.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Nathan Fredrickson <nathan@silverorange.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 * @see       Crypt_GPG::verify()
 */
class Crypt_GPG_Signature
{
    /**
     * A base64-encoded string containing a unique id for this signature if
     * this signature has been verified as ok
     *
     * This id is used to prevent replay attacks and is not present for all
     * types of signatures.
     *
     * @var string
     */
    private $_id = '';

    /**
     * The fingerprint of the key used to create the signature
     *
     * @var string
     */
    private $_keyFingerprint = '';

    /**
     * The id of the key used to create the signature
     *
     * @var string
     */
    private $_keyId = '';

    /**
     * The creation date of this signature
     *
     * This is a Unix timestamp.
     *
     * @var int
     */
    private $_creationDate = 0;

    /**
     * The expiration date of the signature
     *
     * This is a Unix timestamp. If this signature does not expire, this will
     * be zero.
     *
     * @var int
     */
    private $_expirationDate = 0;

    /**
     * The user id associated with this signature
     *
     * @var Crypt_GPG_UserId
     */
    private $_userId = null;

    /**
     * Whether or not this signature is valid
     *
     * @var bool
     */
    private $_isValid = false;

    /**
     * Creates a new signature
     *
     * Signatures can be initialized from an array of named values. Available
     * names are:
     *
     * - <kbd>string  id</kbd>          - the unique id of this signature.
     * - <kbd>string  fingerprint</kbd> - the fingerprint of the key used to
     *                                    create the signature. The fingerprint
     *                                    should not contain formatting
     *                                    characters.
     * - <kbd>string  keyId</kbd>       - the id of the key used to create the
     *                                    the signature.
     * - <kbd>int     creation</kbd>    - the date the signature was created.
     *                                    This is a UNIX timestamp.
     * - <kbd>int     expiration</kbd>  - the date the signature expired. This
     *                                    is a UNIX timestamp. If the signature
     *                                    does not expire, use 0.
     * - <kbd>bool    valid</kbd>       - whether or not the signature is valid.
     * - <kbd>string  userId</kbd>      - the user id associated with the
     *                                    signature. This may also be a
     *                                    {@link Crypt_GPG_UserId} object.
     *
     * @param Crypt_GPG_Signature|array|null $signature Either an existing signature object,
     *                                                  which is copied; or an array
     *                                                  of initial values.
     */
    public function __construct($signature = null)
    {
        // copy from object
        if ($signature instanceof Crypt_GPG_Signature) {
            $this->_id             = $signature->_id;
            $this->_keyFingerprint = $signature->_keyFingerprint;
            $this->_keyId          = $signature->_keyId;
            $this->_creationDate   = $signature->_creationDate;
            $this->_expirationDate = $signature->_expirationDate;
            $this->_isValid        = $signature->_isValid;

            if ($signature->_userId instanceof Crypt_GPG_UserId) {
                $this->_userId = clone $signature->_userId;
            }
        }

        // initialize from array
        if (is_array($signature)) {
            if (array_key_exists('id', $signature)) {
                $this->setId($signature['id']);
            }

            if (array_key_exists('fingerprint', $signature)) {
                $this->setKeyFingerprint($signature['fingerprint']);
            }

            if (array_key_exists('keyId', $signature)) {
                $this->setKeyId($signature['keyId']);
            }

            if (array_key_exists('creation', $signature)) {
                $this->setCreationDate($signature['creation']);
            }

            if (array_key_exists('expiration', $signature)) {
                $this->setExpirationDate($signature['expiration']);
            }

            if (array_key_exists('valid', $signature)) {
                $this->setValid($signature['valid']);
            }

            if (array_key_exists('userId', $signature)) {
                $userId = new Crypt_GPG_UserId($signature['userId']);
                $this->setUserId($userId);
            }
        }
    }

    /**
     * Gets the id of this signature
     *
     * @return string A base64-encoded string containing a unique id for this
     *                signature. This id is used to prevent replay attacks and
     *                is not present for all types of signatures.
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Gets the fingerprint of the key used to create this signature
     *
     * @return string The fingerprint of the key used to create this signature.
     */
    public function getKeyFingerprint()
    {
        return $this->_keyFingerprint;
    }

    /**
     * Gets the id of the key used to create this signature
     *
     * Whereas the fingerprint of the signing key may not always be available
     * (for example if the signature is bad), the id should always be
     * available.
     *
     * @return string The id of the key used to create this signature.
     */
    public function getKeyId()
    {
        return $this->_keyId;
    }

    /**
     * Gets the creation date of this signature
     *
     * @return int The creation date of this signature. This is a Unix
     *             timestamp.
     */
    public function getCreationDate()
    {
        return $this->_creationDate;
    }

    /**
     * Gets the expiration date of the signature
     *
     * @return int The expiration date of this signature. This is a Unix
     *             timestamp. If this signature does not expire, this will
     *             be zero.
     */
    public function getExpirationDate()
    {
        return $this->_expirationDate;
    }

    /**
     * Gets the user id associated with this signature
     *
     * @return Crypt_GPG_UserId|null The user id associated with this signature.
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Gets whether or no this signature is valid
     *
     * @return bool True if this signature is valid and false if it is not.
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Sets the id of this signature
     *
     * @param string $id a base64-encoded string containing a unique id for
     *                   this signature.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     *
     * @see Crypt_GPG_Signature::getId()
     */
    public function setId($id)
    {
        $this->_id = strval($id);
        return $this;
    }

    /**
     * Sets the key fingerprint of this signature
     *
     * @param string $fingerprint the key fingerprint of this signature. This
     *                            is the fingerprint of the primary key used to
     *                            create this signature.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setKeyFingerprint($fingerprint)
    {
        $this->_keyFingerprint = strval($fingerprint);
        return $this;
    }

    /**
     * Sets the key id of this signature
     *
     * @param string $id the key id of this signature. This is the id of the
     *                   primary key used to create this signature.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setKeyId($id)
    {
        $this->_keyId = strval($id);
        return $this;
    }

    /**
     * Sets the creation date of this signature
     *
     * @param int $creationDate The creation date of this signature. This
     *                          is a Unix timestamp.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setCreationDate($creationDate)
    {
        $this->_creationDate = intval($creationDate);
        return $this;
    }

    /**
     * Sets the expiration date of this signature
     *
     * @param int $expirationDate the expiration date of this signature.
     *                            This is a Unix timestamp. Specify zero if
     *                            this signature does not expire.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setExpirationDate($expirationDate)
    {
        $this->_expirationDate = intval($expirationDate);
        return $this;
    }

    /**
     * Sets the user id associated with this signature
     *
     * @param Crypt_GPG_UserId $userId the user id associated with this
     *                                 signature.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setUserId(Crypt_GPG_UserId $userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    /**
     * Sets whether or not this signature is valid
     *
     * @param bool $isValid True if this signature is valid and false if it
     *                      is not.
     *
     * @return Crypt_GPG_Signature the current object, for fluent interface.
     */
    public function setValid($isValid)
    {
        $this->_isValid = ($isValid) ? true : false;
        return $this;
    }
}
