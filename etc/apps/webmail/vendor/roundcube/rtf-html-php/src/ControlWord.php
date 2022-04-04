<?php

namespace RtfHtmlPhp;

class ControlWord extends Element
{
    public $word;
    public $parameter;

    /**
     * Returns string representation of the object for debug purposes
     *
     * @param int $level Indentation level
     *
     * @return string
     */
    public function toString($level)
    {
        return str_repeat("  ", $level) . "WORD {$this->word} ({$this->parameter})\n";
    }
}
