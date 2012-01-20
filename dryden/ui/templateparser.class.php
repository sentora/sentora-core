<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_templateparser {

    /**
     * Loads in the template content and parses it to compute the place holder content.
     * @param string $template_path The full path to the system template (or user template)
     * @return sting 
     */
    static function Generate($template_path) {
        $template_raw = file_get_contents($template_path . "/master.ztml");
        $template_html = ui_templateparser::Process($template_raw);
        return eval('?>' . $template_html);
    }

    /**
     * Replaces the template place holders with the equivilent dynamic infomation.
     * @param string $raw
     * @return string 
     */
    static function Process($raw) {
        global $controller;
        
        runtime_hook::Execute('OnBeforeTemplateProcessor');
        $tplp = new runtime_dataobject;
        preg_match_all("'<#\s(.*?)\s#>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $classes) {
                if (class_exists('' . $classes . '')) {
                    $raw = str_replace("<# " . $classes . " #>", call_user_func(array($classes, 'Template')), $raw);
                }
            }
        }
        preg_match_all("'<@\s(.*?)\s@>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $classes) {
                #if (class_exists('' . $classes . '')) {
                ob_start();
                eval("echo module_controller::get" . $classes . "();");
                $output = ob_get_contents();
                ob_end_clean();
                $raw = str_replace("<@ " . $classes . " @>", $output, $raw);
                #}
            }
        }
        preg_match_all("'<:\s(.*?)\s:>'si", $raw, $match);
        if ($match) {
            foreach ($match[1] as $string) {
                ob_start();
                eval("echo ui_language::translate(\"" . $string . "\");");
                $output = ob_get_contents();
                ob_end_clean();
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
