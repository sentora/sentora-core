<?php 

namespace RtfHtmlPhp;

class ControlSymbol extends Element
{
    public $symbol;
    public $parameter = 0;

    /**
     * Returns string representation of the object for debug purposes
     *
     * @param int $level Indentation level
     *
     * @return string
     */
    public function toString($level)
    {
        return str_repeat("  ", $level) . "SYMBOL {$this->symbol} ({$this->parameter})\n";
    }
}
