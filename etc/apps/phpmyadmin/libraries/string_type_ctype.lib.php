<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Specialized String Functions for phpMyAdmin
 *
 * Defines a set of function callbacks that have a pure C version available if
 * the "ctype" extension is available, but otherwise have PHP versions to use
 * (that are slower).
 *
 * The SQL Parser code relies heavily on these functions.
 *
 * @package    PhpMyAdmin-String
 * @subpackage CType
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * Checks if a character is an alphanumeric one
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is an alphanumeric one or not
 */
function PMA_STR_isAlnum($c)
{
    return ctype_alnum($c);
} // end of the "PMA_STR_isAlnum()" function

/**
 * Checks if a character is an alphabetic one
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is an alphabetic one or not
 */
function PMA_STR_isAlpha($c)
{
    return ctype_alpha($c);
} // end of the "PMA_STR_isAlpha()" function

/**
 * Checks if a character is a digit
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is a digit or not
 */
function PMA_STR_isDigit($c)
{
    return ctype_digit($c);
} // end of the "PMA_STR_isDigit()" function

/**
 * Checks if a character is an upper alphabetic one
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is an upper alphabetic one or not
 */
function PMA_STR_isUpper($c)
{
    return ctype_upper($c);
} // end of the "PMA_STR_isUpper()" function


/**
 * Checks if a character is a lower alphabetic one
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is a lower alphabetic one or not
 */
function PMA_STR_isLower($c)
{
    return ctype_lower($c);
} // end of the "PMA_STR_isLower()" function

/**
 * Checks if a character is a space one
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is a space one or not
 */
function PMA_STR_isSpace($c)
{
    return ctype_space($c);
} // end of the "PMA_STR_isSpace()" function

/**
 * Checks if a character is an hexadecimal digit
 *
 * @param string $c character to check for
 *
 * @return boolean whether the character is an hexadecimal digit or not
 */
function PMA_STR_isHexDigit($c)
{
    return ctype_xdigit($c);
} // end of the "PMA_STR_isHexDigit()" function

?>
