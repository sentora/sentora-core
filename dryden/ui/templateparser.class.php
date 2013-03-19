<?php

/**
 * The template parser class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_templateparser {

    /**
     * Loads in the template content and parses it to compute the place holder content.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $template_path The full path to the system template (or user template).
     * @return sting The processed template HTML. (PHP Eval'd ready for execution).
     */
    static function Generate($template_path) {
        $template_raw = file_get_contents($template_path . "/master.ztml");
        $template_html = ui_templateparser::Process($template_raw);
        return eval('?>' . $template_html);
    }

    /**
     * Replaces the template place holders with the equivilent dynamic infomation.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $raw The HTML template file (as is in the filesystem, the raw module.zpm file)
     * @return string The generated HTML text.
     */
    static function Process($raw) {
        runtime_hook::Execute('OnBeforeTemplateProcessor');
        $match = null;
        preg_match_all("'<#\s(.*?)\s#>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $classes) {
                if (class_exists('' . $classes . '')) {
                    $xss_cleaner = new runtime_xss;
                    $raw = str_replace("<# " . $classes . " #>", call_user_func(array($classes, 'Template')), $raw);//removed due to enforcing simple protection by default $xss_cleaner->xssClean(, array(false, false, false, false, false, false, false))
                }
            }
        }
        $match = null;
        preg_match_all("'<@\s(.*?)\s@>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $classes) {
                #if (class_exists('' . $classes . '')) {
                $method_name = "get" . $classes;
                $output = module_controller::$method_name();
                $raw = str_replace("<@ " . $classes . " @>", $output, $raw);
                #}
            }
        }
        $match = null;
        preg_match_all("'<:\s(.*?)\s:>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $string) {
                $output = ui_language::translate($string);
                $raw = str_replace("<: " . $string . " :>", $output, $raw);
            }
        }
        $raw = str_replace('<?', 'PHP execution is not permitted! Caught: [', $raw);
        $raw = str_replace('?>', ']', $raw);
        $raw = str_replace('<% else %>', '<?php } else { ?>', $raw);
        $raw = str_replace('<% endif %>', '<?php } ?>', $raw);
        $raw = preg_replace('/\<% if (.+?)\ %>/i', '<?php if(module_controller::get$1()){ ?>', $raw);
        $raw = preg_replace('/\<% loop (.+?)\ %>/i', "<?php foreach(module_controller::get$1() as \$key => \$value){ ?>", $raw);
        $raw = str_replace('<% endloop %>', '<?php } ?>', $raw);
        $raw = preg_replace('/\<& (.+?)\ &>/i', '<?php echo \$value[\'$1\']; ?>', $raw);
        runtime_hook::Execute('OnAfterTemplateProcessor');
        return $raw;
    }

}

?>
