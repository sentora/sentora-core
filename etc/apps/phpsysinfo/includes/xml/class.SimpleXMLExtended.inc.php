<?php 
/**
 * modified XML Element
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.SimpleXMLExtended.inc.php 439 2011-02-11 18:43:51Z jacky672 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * class extends the SimpleXML element for including some special functions, like encoding stuff and cdata support
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class SimpleXMLExtended
{
    /**
     * store the encoding that is used for conversation to utf8
     *
     * @var String base encoding
     */
    private $_encoding = null;
    
    /**
     * SimpleXMLElement to which every call is delegated
     *
     * @var SimpleXMLElement delegated SimpleXMLElement
     */
    private $_SimpleXmlElement = null;
    
    /**
     * create a new extended SimpleXMLElement and set encoding if specified
     *
     * @param SimpleXMLElement $xml      base xml element
     * @param String           $encoding base encoding that should be used for conversation to utf8
     *
     * @return void
     */
    public function __construct($xml, $encoding = null)
    {
        if ($encoding != null) {
            $this->_encoding = $encoding;
        }
        $this->_SimpleXmlElement = $xml;
    }
    
    /**
     * insert a child element with or without a value, also doing conversation of name and if value is set to utf8
     *
     * @param String $name  name of the child element
     * @param String $value a value that should be insert to the child
     *
     * @return SimpleXMLExtended extended child SimpleXMLElement
     */
    public function addChild($name, $value = null)
    {
        $nameUtf8 = $this->_toUTF8($name);
        if ($value == null) {
            return new SimpleXMLExtended($this->_SimpleXmlElement->addChild($nameUtf8), $this->_encoding);
        } else {
            $valueUtf8 = htmlspecialchars($this->_toUTF8($value));
            return new SimpleXMLExtended($this->_SimpleXmlElement->addChild($nameUtf8, $valueUtf8), $this->_encoding);
        }
    }
    
    /**
     * insert a child with cdata section
     *
     * @param String $name  name of the child element
     * @param String $cdata data for CDATA section
     *
     * @return SimpleXMLExtended extended child SimpleXMLElement
     */
    public function addCData($name, $cdata)
    {
        $nameUtf8 = $this->_toUTF8($name);
        $node = $this->_SimpleXmlElement->addChild($nameUtf8);
        $domnode = dom_import_simplexml($node);
        $no = $domnode->ownerDocument;
        $domnode->appendChild($no->createCDATASection($cdata));
        return new SimpleXMLExtended($node, $this->_encoding);
    }
    
    /**
     * add a attribute to a child and convert name and value to utf8
     *
     * @param String $name  name of the attribute
     * @param String $value value of the attribute
     *
     * @return Void
     */
    public function addAttribute($name, $value)
    {
        $nameUtf8 = $this->_toUTF8($name);
        $valueUtf8 = htmlspecialchars($this->_toUTF8($value));
        $this->_SimpleXmlElement->addAttribute($nameUtf8, $valueUtf8);
    }
    
    /**
     * append a xml-tree to another xml-tree
     *
     * @param SimpleXMLElement $new_child child that should be appended
     *
     * @return Void
     */
    public function combinexml(SimpleXMLElement $new_child)
    {
        $node1 = dom_import_simplexml($this->_SimpleXmlElement);
        $dom_sxe = dom_import_simplexml($new_child);
        $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
        $node1->appendChild($node2);
    }
    
    /**
     * convert a string into an UTF-8 string
     *
     * @param String $str string to convert
     *
     * @return String UTF-8 string
     */
    private function _toUTF8($str)
    {
        if ($this->_encoding != null) {
            $enclist = mb_list_encodings();
            if (in_array($this->_encoding, $enclist)) {
                return mb_convert_encoding(trim($str), 'UTF-8', $this->_encoding);
            }
            else if (function_exists("iconv")) {
                return iconv($this->_encoding, 'UTF-8', trim($str));
            }
            else {
                return mb_convert_encoding(trim($str), 'UTF-8');
            }
        } else {
            return mb_convert_encoding(trim($str), 'UTF-8');
        }
    }
    
    /**
     * Returns the SimpleXmlElement
     *
     * @return SimpleXmlElement entire xml as SimpleXmlElement
     */
    public function getSimpleXmlElement()
    {
        return $this->_SimpleXmlElement;
    }
}
?>
