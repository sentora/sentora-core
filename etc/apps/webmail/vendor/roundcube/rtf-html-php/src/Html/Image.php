<?php

namespace RtfHtmlPhp\Html;

class Image
{
    public $format;
    public $width;
    public $height;
    public $goalWidth;
    public $goalHeight;
    public $pcScaleX;
    public $pcScaleY;
    public $binarySize;
    public $imageData;


    /**
     * Object constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Resets the object to the initial state
     *
     * @return void
     */
    public function reset()
    {
        $this->format = 'bmp';
        $this->width = 0;         // in xExt if wmetafile otherwise in px
        $this->height = 0;        // in yExt if wmetafile otherwise in px
        $this->goalWidth = 0;     // in twips
        $this->goalHeight = 0;    // in twips
        $this->pcScaleX = 100;    // 100%
        $this->pcScaleY = 100;    // 100%
        $this->binarySize = null; // Number of bytes of the binary data
        $this->imageData = null;  // Binary or Hexadecimal Data
    }

    /**
     * Generate a HTML content for the image
     *
     * @return string Image tag content, An empty string for unsupported/empty image
     */
    public function printImage()
    {
        // process binary data
        if (isset($this->binarySize)) {
            // Not implemented
            return '';
        }

        if (empty($this->imageData)) {
            return '';
        }

        // process hexadecimal data
        $data = base64_encode(pack('H*', $this->imageData));

        // <img src="data:image/{FORMAT};base64,{#BDATA}" />
        return "<img src=\"data:image/{$this->format};base64,{$data}\" />";
    }
}
