<?php

namespace RtfHtmlPhp\Html;

class Font
{
    public $family;
    public $fprq;
    public $name;
    public $charset;
    public $codepage;

    /**
     * Returns font style (font-family) string
     *
     * @return string A string including font-family: prefix. An empty string if font is not set
     */
    public function toStyle()
    {
        $list = [];

        if ($this->name) {
            $list[] = $this->name;
        }

        if ($this->family) {
            $list[] = $this->family;
        }

        if (count($list) == 0) {
            return '';
        }

        return "font-family:" . implode(',', $list);
    }
}
