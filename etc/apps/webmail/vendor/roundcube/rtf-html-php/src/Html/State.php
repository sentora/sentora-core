<?php 

namespace RtfHtmlPhp\Html;

class State
{
    public static $fonttbl = [];
    public static $colortbl = [];
    public $bold;
    public $italic;
    public $underline;
    public $strike;
    public $hidden;
    public $fontsize;
    public $fontcolor;
    public $background;
    public $hcolor;
    public $font;
    public $htmlrtf;

    protected static $highlight = [
        1  => 'Black',
        2  => 'Blue',
        3  => 'Cyan',
        4  => 'Green',
        5  => 'Magenta',
        6  => 'Red',
        7  => 'Yellow',
        8  => 'Unused',
        9  => 'DarkBlue',
        10 => 'DarkCyan',
        11 => 'DarkGreen',
        12 => 'DarkMagenta',
        13 => 'DarkRed',
        14 => 'DarkYellow',
        15 => 'DarkGray',
        16 => 'LightGray'
    ];


    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Store a font in the font table at the specified index.
     *
     * @param int  $index Font number
     * @param Font $font  Font object
     *
     * @return void
     */
    public static function setFont($index, Font $font)
    {
        State::$fonttbl[$index] = $font;
    }

    /**
     * Resets the object to the initial state
     *
     * @param string|null $defaultFont Font name
     *
     * @return void
     */
    public function reset($defaultFont = null)
    {
        $this->bold = false;
        $this->italic = false;
        $this->underline = false;
        $this->strike = false;
        $this->hidden = false;
        $this->fontsize = 0;
        $this->fontcolor = null;
        $this->background = null;
        $this->hcolor = null;
        $this->font = isset($defaultFont) ? $defaultFont : null;
        $this->htmlrtf = false;
    }

    /**
     * Generates css style for the state.
     *
     * @return string The css string
     */
    public function printStyle()
    {
        $style = [];

        if ($this->bold) {
            $style[] = "font-weight:bold";
        }

        if ($this->italic) {
            $style[] = "font-style:italic";
        }

        if ($this->underline) {
            $style[] = "text-decoration:underline";
        }

        // state->underline is a toggle switch variable so no need for
        // a dedicated state->end_underline variable
        // if($this->state->end_underline) {$span .= "text-decoration:none";}
        if ($this->strike) {
            $style[] = "text-decoration:line-through";
        }

        if ($this->hidden) {
            $style[] = "display:none";
        }

        if (isset($this->font)) {
            $font = self::$fonttbl[$this->font];
            $style[] = $font->toStyle();
        }

        if ($this->fontsize != 0) {
            $style[] = "font-size:{$this->fontsize}px";
        }

        // Font color:
        if (isset($this->fontcolor)) {
            // Check if color is set. in particular when it's the 'auto' color
            if (array_key_exists($this->fontcolor, self::$colortbl) && self::$colortbl[$this->fontcolor]) {
                $style[] = "color:" . self::$colortbl[$this->fontcolor];
            }
        }

        // Background color:
        if (isset($this->background)) {
            // Check if color is set. in particular when it's the 'auto' color
            if (array_key_exists($this->background, self::$colortbl) && self::$colortbl[$this->background]) {
                $style[] = "background-color:" . self::$colortbl[$this->background];
            }
        } elseif (isset($this->hcolor)) {
            // Highlight color:
            if (array_key_exists($this->hcolor, self::$highlight) && self::$highlight[$this->hcolor]) {
                $style[] = "background-color:" . self::$highlight[$this->hcolor];
            }
        }

        return empty($style) ? '' : implode(';', $style) . ';';
    }

    /**
     * Check whether this State is equal to another State.
     *
     * @param State $state A state to compare with
     *
     * @return bool True if the state is identical, False otherwise
     */
    public function equals($state)
    {
        if (!($state instanceof State)) {
            return false;
        }

        return $this->bold == $state->bold
            && $this->italic == $state->italic
            && $this->underline == $state->underline
            && $this->strike == $state->strike
            && $this->hidden == $state->hidden
            && $this->fontsize == $state->fontsize
            // Compare colors
            && $this->fontcolor == $state->fontcolor
            && $this->background == $state->background
            && $this->hcolor == $state->hcolor
            // Compare fonts
            && $this->font == $state->font;
    }
}
