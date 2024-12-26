<?php 

namespace RtfHtmlPhp;

class Document
{
    /** @var ?string Current character in an RTF stream */
    protected $char;
    /** @var string RTF string being parsed */
    protected $rtf;
    /** @var int Current position in RTF string */
    protected $pos;
    /** @var int Length of RTF string */
    protected $len;
    /** @var ?Group Current RTF group */
    protected $group;
    /** @var array */
    protected $uc = [];

    /** @var ?Group Root group */
    public $root = null;

    /**
     * Object contructor
     *
     * @param string $rtf The RTF content
     */
    public function __construct($rtf)
    {
        $this->parse($rtf);
    }

    /**
     * Position on the next character from the RTF stream.
     * Parsing is aborted when reading beyond end of input string.
     *
     * @return void
     */
    protected function getChar()
    {
        $this->char = null;

        if ($this->pos < strlen($this->rtf)) {
            $this->char = $this->rtf[$this->pos++];
        } else {
            $err = "Parse error: Tried to read past end of input; RTF is probably truncated.";
            throw new \Exception($err);
        }
    }

    /**
     * (Helper method) Is the current character a letter?
     *
     * @return bool
     */
    protected function isLetter()
    {
        if (ord($this->char) >= 65 && ord($this->char) <= 90) {
            return true;
        }

        if (ord($this->char) >= 97 && ord($this->char) <= 122) {
            return true;
        }

        return false;
    }

    /**
     * (Helper method) Is the current character a digit?
     *
     * @return bool
     */
    protected function isDigit()
    {
        return (ord($this->char) >= 48 && ord($this->char) <= 57);
    }

    /**
     * (Helper method) Is the current character end-of-line (EOL)?
     *
     * @return bool
     */
    protected function isEndOfLine()
    {
        if ($this->char == "\r" || $this->char == "\n") {
            // Checks for a Windows/Acron type EOL
            if ($this->rtf[$this->pos] == "\n" || $this->rtf[$this->pos] == "\r") {
                $this->getChar();
            }

            return true;
        }

        return false;
    }

    /**
     * (Helper method) Is the current character for a space delimiter?
     *
     * @return bool
     */
    protected function isSpaceDelimiter()
    {
        return ($this->char == " " || $this->isEndOfLine());
    }

    /**
     * Store state of document on stack.
     *
     * @return void
     */
    protected function parseStartGroup()
    {
        $group = new Group();

        if ($this->group) {
            // Make the new group a child of the current group
            $group->parent = $this->group;

            array_push($this->group->children, $group);
            array_push($this->uc, end($this->uc));
        } else {
            // If there is no parent group, then set this group
            // as the root group.
            $this->root = $group;
            // Create uc stack and insert the first default value
            $this->uc = [1];
        }

        // Set the new group as the current group:
        $this->group = $group;
    }

    /**
     * Retrieve state of document from stack.
     *
     * @return void
     */
    protected function parseEndGroup()
    {
        $this->group = $this->group->parent;
        // Retrieve last uc value from stack
        array_pop($this->uc);
    }

    /**
     * Parse ControlWord element
     *
     * @return void
     */
    protected function parseControlWord()
    {
        // Read letters until a non-letter is reached.
        $word = '';
        $this->getChar();

        while ($this->isLetter()) {
            $word .= $this->char;
            $this->getChar();
        }

        // Read parameter (if any) consisting of digits.
        // Parameter may be negative, i.e., starting with a '-'
        $parameter = null;
        $negative = false;

        if ($this->char == '-') {
            $this->getChar();
            $negative = true;
        }

        while ($this->isDigit()) {
            if ($parameter === null) {
                $parameter = 0;
            }
            $parameter = $parameter * 10 + (int) $this->char;
            $this->getChar();
        }

        // If no parameter present, assume control word's default (usually 1)
        // If no default then assign 0 to the parameter
        if ($parameter === null) {
            $parameter = 1;
        }

        // Convert parameter to a negative number when applicable
        if ($negative) {
            $parameter = -$parameter;
        }

        // Update uc value
        if ($word == "uc") {
            array_pop($this->uc);
            $this->uc[] = $parameter;
        }

        // Skip space delimiter
        if (!$this->isSpaceDelimiter()) {
            $this->pos--;
        }

        // If this is \u, then the parameter will be followed 
        // by {$this->uc} characters.
        if ($word == "u") {
            // Convert parameter to unsigned decimal unicode
            if ($negative) {
                $parameter = 65536 + $parameter;
            }

            // Will ignore replacement characters $uc times
            $uc = end($this->uc);

            while ($uc > 0) {
                $this->getChar();
                // If the replacement character is encoded as
                // hexadecimal value \'hh then jump over it
                if ($this->char == "\\" && $this->rtf[$this->pos] == '\'') {
                    $this->pos = $this->pos + 3;
                } elseif ($this->char == '{' || $this->char == '{') {
                    // Break if it's an RTF scope delimiter
                    break;
                }

                // - To include an RTF delimiter in skippable data, it must be
                //   represented using the appropriate control symbol (that is,
                //   escaped with a backslash,) as in plain text.
                //
                // - Any RTF control word or symbol is considered a single character
                //   for the purposes of counting skippable characters. For this reason
                //   it's more appropriate to create a $skip flag and let the Parse()
                //   function take care of the skippable characters.
                $uc--;
            }
        }

        // Add new RTF word as a child to the current group.
        $rtfword = new ControlWord();
        $rtfword->word = $word;
        $rtfword->parameter = $parameter;
        array_push($this->group->children, $rtfword);
    }

    /**
     * Parse ControlSymbol element
     *
     * @return void
     */
    protected function parseControlSymbol()
    {
        // Read symbol (one character only).
        $this->getChar();
        $symbol = $this->char;

        // Exceptional case:
        // Treat EOL symbols as \par control word
        if ($this->isEndOfLine()) {
            $rtfword = new ControlWord();
            $rtfword->word = 'par';
            $rtfword->parameter = 0;
            array_push($this->group->children, $rtfword);
            return;
        }

        // Symbols ordinarily have no parameter. However,
        // if this is \' (a single quote), then it is
        // followed by a 2-digit hex-code:
        $parameter = 0;
        if ($symbol == '\'') {
            $this->getChar();
            $parameter = $this->char;
            $this->getChar();
            $parameter = hexdec($parameter . $this->char);
        }

        // Add new control symbol as a child to the current group:
        $rtfsymbol = new ControlSymbol();
        $rtfsymbol->symbol = $symbol;
        $rtfsymbol->parameter = $parameter;
        array_push($this->group->children, $rtfsymbol);
    }

    /**
     * Parse Control element
     *
     * @return void
     */
    protected function parseControl()
    {
        // Beginning of an RTF control word or control symbol.
        // Look ahead by one character to see if it starts with
        // a letter (control world) or another symbol (control symbol):
        $this->GetChar();
        $this->pos--; // (go back after look-ahead)

        if ($this->isLetter()) {
            $this->parseControlWord();
        } else {
            $this->parseControlSymbol();
        }
    }

    /**
     * Parse Text element
     *
     * @return void
     */
    protected function parseText()
    {
        // Parse plain text up to backslash or brace,
        // unless escaped.
        $text = '';
        $terminate = false;

        do {
            // Ignore EOL characters
            if ($this->char == "\r" || $this->char == "\n") {
                $this->getChar();
                continue;
            }
            // Is this an escape?
            if ($this->char == "\\") {
                // Perform lookahead to see if this
                // is really an escape sequence.
                $this->getChar();
                switch ($this->char) {
                case "\\":
                    break;
                case '{':
                    break;
                case '}':
                    break;
                default:
                    // Not an escape. Roll back.
                    $this->pos = $this->pos - 2;
                    $terminate = true;
                    break;
                }
            } elseif ($this->char == '{' || $this->char == '}') {
                $this->pos--;
                $terminate = true;
            }

            if (!$terminate) {
                // Save plain text
                $text .= $this->char;
                $this->getChar();
            }
        } while (!$terminate && $this->pos < $this->len);

        // Create new Text element:
        $text = new Text($text);

        // If there is no current group, then this is not a valid RTF file.
        // Throw an exception.
        if (!$this->group) {
            throw new \Exception("Parse error: RTF text outside of group.");
        }

        // Add text as a child to the current group:
        array_push($this->group->children, $text);
    }

    /**
     * Attempt to parse an RTF string.
     *
     * @param string $rtf RTF content
     *
     * @return void
     */
    protected function parse($rtf)
    {
        $this->rtf = $rtf;
        $this->pos = 0;
        $this->len = strlen($this->rtf);
        $this->group = null;
        $this->root = null;

        while ($this->pos < $this->len-1) {
            // Read next character:
            $this->getChar();

            // Ignore \r and \n
            if ($this->char == "\n" || $this->char == "\r") {
                continue;
            }

            // What type of character is this?
            switch ($this->char) {
            case '{':
                $this->parseStartGroup();
                break;
            case '}':
                $this->parseEndGroup();
                break;
            case "\\":
                $this->parseControl();
                break;
            default:
                $this->parseText();
                break;
            }
        }
    }

    /**
     * Returns string representation of the document for debug purposes.
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->root) {
            return "No root group";
        }

        return $this->root->toString();
    }
}
