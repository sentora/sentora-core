<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Set of functions for kanji-encoding convert (available only with japanese
 * language)
 *
 * PHP4 configure requirements:
 *     --enable-mbstring --enable-mbstr-enc-trans --enable-mbregex
 *
 * 2002/2/22 - by Yukihiro Kawada <kawada@den.fujifilm.co.jp>
 *
 * @package PhpMyAdmin
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * Gets the php internal encoding codes and sets the available encoding
 * codes list
 * 2002/1/4 by Y.Kawada
 *
 * @global  string   the current encoding code
 * @global  string   the available encoding codes list
 *
 * @return boolean  always true
 */
function PMA_internal_enc_check()
{
    global $internal_enc, $enc_list;

    $internal_enc = mb_internal_encoding();
    if ($internal_enc == 'EUC-JP') {
        $enc_list = 'ASCII,EUC-JP,SJIS,JIS';
    } else {
        $enc_list = 'ASCII,SJIS,EUC-JP,JIS';
    }

    return true;
} // end of the 'PMA_internal_enc_check' function


/**
 * Reverses SJIS & EUC-JP position in the encoding codes list
 * 2002/1/4 by Y.Kawada
 *
 * @global  string   the available encoding codes list
 *
 * @return boolean  always true
 */
function PMA_change_enc_order()
{
    global $enc_list;

    $p            = explode(',', $enc_list);
    if ($p[1] == 'EUC-JP') {
        $enc_list = 'ASCII,SJIS,EUC-JP,JIS';
    } else {
        $enc_list = 'ASCII,EUC-JP,SJIS,JIS';
    }

    return true;
} // end of the 'PMA_change_enc_order' function


/**
 * Kanji string encoding convert
 * 2002/1/4 by Y.Kawada
 *
 * @param string $str  the string to convert
 * @param string $enc  the destination encoding code
 * @param string $kana set 'kana' convert to JIS-X208-kana
 *
 * @global  string   the available encoding codes list
 *
 * @return string   the converted string
 */
function PMA_kanji_str_conv($str, $enc, $kana)
{
    global $enc_list;

    if ($enc == '' && $kana == '') {
        return $str;
    }
    $nw       = mb_detect_encoding($str, $enc_list);

    if ($kana == 'kana') {
        $dist = mb_convert_kana($str, 'KV', $nw);
        $str  = $dist;
    }
    if ($nw != $enc && $enc != '') {
        $dist = mb_convert_encoding($str, $enc, $nw);
    } else {
        $dist = $str;
    }
    return $dist;
} // end of the 'PMA_kanji_str_conv' function


/**
 * Kanji file encoding convert
 * 2002/1/4 by Y.Kawada
 *
 * @param string $file the name of the file to convert
 * @param string $enc  the destination encoding code
 * @param string $kana set 'kana' convert to JIS-X208-kana
 *
 * @return string   the name of the converted file
 */
function PMA_kanji_file_conv($file, $enc, $kana)
{
    if ($enc == '' && $kana == '') {
        return $file;
    }

    $tmpfname = tempnam('', $enc);
    $fpd      = fopen($tmpfname, 'wb');
    $fps      = fopen($file, 'r');
    PMA_change_enc_order();
    while (!feof($fps)) {
        $line = fgets($fps, 4096);
        $dist = PMA_kanji_str_conv($line, $enc, $kana);
        fputs($fpd, $dist);
    } // end while
    PMA_change_enc_order();
    fclose($fps);
    fclose($fpd);
    unlink($file);

    return $tmpfname;
} // end of the 'PMA_kanji_file_conv' function


/**
 * Defines radio form fields to switch between encoding modes
 * 2002/1/4 by Y.Kawada
 *
 * @param string $spaces spaces character to prepend the output with
 *
 * @return string   xhtml code for the radio controls
 */
function PMA_set_enc_form($spaces)
{
    return "\n"
             /* l10n: This is currently used only in Japanese locales */
           . $spaces . '<ul>' . "\n" . '<li>'
           . $spaces . '<input type="radio" name="knjenc" value="" checked="checked" id="kj-none" /><label for="kj-none">' . _pgettext('None encoding conversion', 'None') . "</label>\n"
           . $spaces . '<input type="radio" name="knjenc" value="EUC-JP" id="kj-euc" /><label for="kj-euc">EUC</label>' . "\n"
           . $spaces . '<input type="radio" name="knjenc" value="SJIS" id="kj-sjis" /><label for="kj-sjis">SJIS</label>' . "\n"
           . $spaces . '</li>' . "\n" . '<li>'
           . $spaces . '<input type="checkbox" name="xkana" value="kana" id="kj-kana" />' . "\n"
           /* l10n: This is currently used only in Japanese locales */
           . $spaces . '<label for="kj-kana">' . __('Convert to Kana') . '</label><br />' . "\n"
           . $spaces . '</li>' . "\n" . '</ul>'
           ;
} // end of the 'PMA_set_enc_form' function


PMA_internal_enc_check();

?>
