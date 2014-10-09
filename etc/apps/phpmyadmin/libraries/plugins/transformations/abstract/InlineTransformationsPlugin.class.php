<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Abstract class for the inline transformations plugins
 *
 * @package    PhpMyAdmin-Transformations
 * @subpackage Inline
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/* Get the transformations interface */
require_once 'libraries/plugins/TransformationsPlugin.class.php';
/* For PMA_transformation_global_html_replace */
require_once 'libraries/transformations.lib.php';

/**
 * Provides common methods for all of the inline transformations plugins.
 *
 * @package PhpMyAdmin
 */
abstract class InlineTransformationsPlugin extends TransformationsPlugin
{
    /**
     * Gets the transformation description of the specific plugin
     *
     * @return string
     */
    public static function getInfo()
    {
        return __(
            'Displays a clickable thumbnail. The options are the maximum width'
            . ' and height in pixels. The original aspect ratio is preserved.'
        );
    }

    /**
     * Does the actual work of each specific transformations plugin.
     *
     * @param string $buffer  text to be transformed
     * @param array  $options transformation options
     * @param string $meta    meta information
     *
     * @return void
     */
    public function applyTransformation($buffer, $options = array(), $meta = '')
    {
        if (PMA_IS_GD2) {
            $transform_options = array (
                'string' => '<a href="transformation_wrapper.php'
                    . $options['wrapper_link']
                    . '" target="_blank"><img src="transformation_wrapper.php'
                    . $options['wrapper_link'] . '&amp;resize=jpeg&amp;newWidth='
                    . (isset($options[0]) ? $options[0] : '100') . '&amp;newHeight='
                    . (isset($options[1]) ? $options[1] : 100)
                    . '" alt="[__BUFFER__]" border="0" /></a>'
            );
        } else {
            $transform_options = array (
                'string' => '<img src="transformation_wrapper.php'
                . $options['wrapper_link']
                . '" alt="[__BUFFER__]" width="320" height="240" />'
            );
        }
        $buffer = PMA_transformation_global_html_replace(
            $buffer,
            $transform_options
        );

        return $buffer;
    }

    /**
     * This method is called when any PluginManager to which the observer
     * is attached calls PluginManager::notify()
     *
     * @param SplSubject $subject The PluginManager notifying the observer
     *                            of an update.
     *
     * @todo implement
     * @return void
     */
    public function update (SplSubject $subject)
    {
        ;
    }


    /* ~~~~~~~~~~~~~~~~~~~~ Getters and Setters ~~~~~~~~~~~~~~~~~~~~ */


    /**
     * Gets the transformation name of the specific plugin
     *
     * @return string
     */
    public static function getName()
    {
        return "Inline";
    }
}
?>
