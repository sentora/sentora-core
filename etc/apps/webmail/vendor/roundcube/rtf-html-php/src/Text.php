<?php 

namespace RtfHtmlPhp;

class Text extends Element
{
    public $text;

    /**
     * Create a new Text instance with string content.
     *
     * @param string $text The content
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Returns string representation of the object for debug purposes
     *
     * @param int $level Indentation level
     *
     * @return string
     */
    public function toString($level)
    {
        return str_repeat("  ", $level) . "TEXT {$this->text}\n";
    }
}
