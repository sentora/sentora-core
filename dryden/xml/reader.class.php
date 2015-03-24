<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU LesserGeneral Public License as published
  by the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  For support, please visit http://www.criticaldevelopment.net/xml/
 */

/**
 * XML Parser Class (php5)
 * 
 * Parses an XML document into an object structure much like the SimpleXML extension.
 *
 * @author Adam A. Flynn <adamaflynn@criticaldevelopment.net>
 * @copyright Copyright (c) 2005-2007, Adam A. Flynn
 *
 * @version 1.3.0
 */
class xml_reader {

    /**
     * The XML parser
     *
     * @var resource
     */
    private $parser;

    /**
     * The XML document
     *
     * @var string
     */
    private $xml;

    /**
     * Document tag
     *
     * @var object
     */
    public $document;

    /**
     * Current object depth
     *
     * @var array
     */
    private $stack;

    /**
     * Whether or not to replace dashes and colons in tag
     * names with underscores.
     * 
     * @var bool
     */
    private $cleanTagNames;

    /**
     * Constructor. Loads XML document.
     *
     * @param string $xml The string of the XML document
     * @return xml_reader
     */
    function __construct($xml = '', $cleanTagNames = true) {
        //Load XML document
        $this->xml = $xml;

        //Set stack to an array
        $this->stack = array();

        //Set whether or not to clean tag names
        $this->cleanTagNames = $cleanTagNames;
    }

    /**
     * Initiates and runs PHP's XML parser
     */
    public function Parse() {
        //Create the parser resource
        $this->parser = xml_parser_create();

        //Set the handlers
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'StartElement', 'EndElement');
        xml_set_character_data_handler($this->parser, 'CharacterData');

        //Error handling
        if (!xml_parse($this->parser, $this->xml))
            $this->HandleError(xml_get_error_code($this->parser), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser));

        //Free the parser
        xml_parser_free($this->parser);
    }

    /**
     * Handles an XML parsing error
     *
     * @param int $code XML Error Code
     * @param int $line Line on which the error happened
     * @param int $col Column on which the error happened
     */
    private function HandleError($code, $line, $col) {
        trigger_error('XML Parsing Error at ' . $line . ':' . $col . '. Error ' . $code . ': ' . xml_error_string($code));
    }

    /**
     * Gets the XML output of the PHP structure within $this->document
     *
     * @return string
     */
    public function GenerateXML() {
        return $this->document->GetXML();
    }

    /**
     * Gets the reference to the current direct parent
     *
     * @return object
     */
    private function GetStackLocation() {
        //Returns the reference to the current direct parent
        return end($this->stack);
    }

    /**
     * Handler function for the start of a tag
     *
     * @param resource $parser
     * @param string $name
     * @param array $attrs
     */
    private function StartElement($parser, $name, $attrs = array()) {
        //Make the name of the tag lower case
        $name = strtolower($name);

        //Check to see if tag is root-level
        if (count($this->stack) == 0) {
            //If so, set the document as the current tag
            $this->document = new XMLTag($name, $attrs);

            //And start out the stack with the document tag
            $this->stack = array(&$this->document);
        }
        //If it isn't root level, use the stack to find the parent
        else {
            //Get the reference to the current direct parent
            $parent = $this->GetStackLocation();

            $parent->AddChild($name, $attrs, count($this->stack), $this->cleanTagNames);

            //If the cleanTagName feature is on, clean the tag names
            if ($this->cleanTagNames)
                $name = str_replace(array(':', '-'), '_', $name);

            //Update the stack
            $this->stack[] = end($parent->$name);
        }
    }

    /**
     * Handler function for the end of a tag
     *
     * @param resource $parser
     * @param string $name
     */
    private function EndElement($parser, $name) {
        //Update stack by removing the end value from it as the parent
        array_pop($this->stack);
    }

    /**
     * Handler function for the character data within a tag
     *
     * @param resource $parser
     * @param string $data
     */
    private function CharacterData($parser, $data) {
        //Get the reference to the current parent object
        $tag = $this->GetStackLocation();

        //Assign data to it
        $tag->tagData .= trim($data);
    }

}

/**
 * XML Tag Object (php5)
 * 
 * This object stores all of the direct children of itself in the $children array. They are also stored by
 * type as arrays. So, if, for example, this tag had 2 <font> tags as children, there would be a class member
 * called $font created as an array. $font[0] would be the first font tag, and $font[1] would be the second.
 * 
 * To loop through all of the direct children of this object, the $children member should be used.
 *
 * To loop through all of the direct children of a specific tag for this object, it is probably easier 
 * to use the arrays of the specific tag names, as explained above.
 * 
 * @author Adam A. Flynn <adamaflynn@criticaldevelopment.net>
 * @copyright Copyright (c) 2005-2007, Adam A. Flynn
 *
 * @version 1.3.0
 */
class XMLTag {

    /**
     * Array with the attributes of this XML tag
     *
     * @var array
     */
    public $tagAttrs;

    /**
     * The name of the tag
     *
     * @var string
     */
    public $tagName;

    /**
     * The data the tag contains 
     * 
     * So, if the tag doesn't contain child tags, and just contains a string, it would go here
     *
     * @var stat
     */
    public $tagData;

    /**
     * Array of references to the objects of all direct children of this XML object
     *
     * @var array
     */
    public $tagChildren;

    /**
     * The number of parents this XML object has (number of levels from this tag to the root tag)
     *
     * Used presently only to set the number of tabs when outputting XML
     *
     * @var int
     */
    public $tagParents;

    /**
     * Constructor, sets up all the default values
     *
     * @param string $name
     * @param array $attrs
     * @param int $parents
     * @return XMLTag
     */
    function __construct($name, $attrs = array(), $parents = 0) {
        //Make the keys of the attr array lower case, and store the value
        $this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);

        //Make the name lower case and store the value
        $this->tagName = strtolower($name);

        //Set the number of parents
        $this->tagParents = $parents;

        //Set the types for children and data
        $this->tagChildren = array();
        $this->tagData = '';
    }

    /**
     * Adds a direct child to this object
     *
     * @param string $name
     * @param array $attrs
     * @param int $parents
     * @param bool $cleanTagName
     */
    public function AddChild($name, $attrs, $parents, $cleanTagName = true) {
        //If the tag is a reserved name, output an error
        if (in_array($name, array('tagChildren', 'tagAttrs', 'tagParents', 'tagData', 'tagName'))) {
            trigger_error('You have used a reserved name as the name of an XML tag. Please consult the documentation (http://www.criticaldevelopment.net/xml/) and rename the tag named "' . $name . '" to something other than a reserved name.', E_USER_ERROR);

            return;
        }

        //Create the child object itself
        $child = new XMLTag($name, $attrs, $parents);

        //If the cleanTagName feature is on, replace colons and dashes with underscores
        if ($cleanTagName)
            $name = str_replace(array(':', '-'), '_', $name);

        //Toss up a notice if someone's trying to to use a colon or dash in a tag name
        elseif (strstr($name, ':') || strstr($name, '-'))
            trigger_error('Your tag named "' . $name . '" contains either a dash or a colon. Neither of these characters are friendly with PHP variable names, and, as such, you may have difficulty accessing them. You might want to think about enabling the cleanTagName feature (pass true as the second argument of the xml_reader constructor). For more details, see http://www.criticaldevelopment.net/xml/', E_USER_NOTICE);

        //If there is no array already set for the tag name being added, 
        //create an empty array for it
        if (!isset($this->$name))
            $this->$name = array();

        //Add the reference of it to the end of an array member named for the tag's name
        $this->{$name}[] = &$child;

        //Add the reference to the children array member
        $this->tagChildren[] = &$child;

        //Return a reference to this object for the stack
        return $this;
    }

    /**
     * Returns the string of the XML document which would be generated from this object
     * 
     * This function works recursively, so it gets the XML of itself and all of its children, which
     * in turn gets the XML of all their children, which in turn gets the XML of all thier children,
     * and so on. So, if you call GetXML from the document root object, it will return a string for 
     * the XML of the entire document.
     * 
     * This function does not, however, return a DTD or an XML version/encoding tag. That should be
     * handled by xml_reader::GetXML()
     *
     * @return string
     */
    public function GetXML() {
        //Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
        $out = "\n" . str_repeat("\t", $this->tagParents) . '<' . $this->tagName;

        //For each attribute, add attr="value"
        foreach ($this->tagAttrs as $attr => $value)
            $out .= ' ' . $attr . '="' . $value . '"';

        //If there are no children and it contains no data, end it off with a />
        if (empty($this->tagChildren) && empty($this->tagData))
            $out .= " />";

        //Otherwise...
        else {
            //If there are children
            if (!empty($this->tagChildren)) {
                //Close off the start tag
                $out .= '>';

                //For each child, call the GetXML function (this will ensure that all children are added recursively)
                foreach ($this->tagChildren as $child)
                    $out .= $child->GetXML();

                //Add the newline and indentation to go along with the close tag
                $out .= "\n" . str_repeat("\t", $this->tagParents);
            }

            //If there is data, close off the start tag and add the data
            elseif (!empty($this->tagData))
                $out .= '>' . $this->tagData;

            //Add the end tag    
            $out .= '</' . $this->tagName . '>';
        }

        //Return the final output
        return $out;
    }

    /**
     * Deletes this tag's child with a name of $childName and an index
     * of $childIndex
     *
     * @param string $childName
     * @param int $childIndex
     */
    public function Delete($childName, $childIndex = 0) {
        //Delete all of the children of that child
        $this->{$childName}[$childIndex]->DeleteChildren();

        //Destroy the child's value
        $this->{$childName}[$childIndex] = null;

        //Remove the child's name from the named array
        unset($this->{$childName}[$childIndex]);

        //Loop through the tagChildren array and remove any null
        //values left behind from the above operation
        for ($x = 0; $x < count($this->tagChildren); $x++) {
            if (is_null($this->tagChildren[$x]))
                unset($this->tagChildren[$x]);
        }
    }

    /**
     * Removes all of the children of this tag in both name and value
     */
    private function DeleteChildren() {
        //Loop through all child tags
        for ($x = 0; $x < count($this->tagChildren); $x++) {
            //Do this recursively
            $this->tagChildren[$x]->DeleteChildren();

            //Delete the name and value
            $this->tagChildren[$x] = null;
            unset($this->tagChildren[$x]);
        }
    }

}

?>