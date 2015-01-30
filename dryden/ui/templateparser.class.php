<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * The template parser class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_templateparser
{

    /**
     * Array of all the functions allowed by Sentora template system with the pattern to identify them
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static public $Functions = array(
        'PHPTags' => "/(<\?php)|(<\?)|(\?>)/is",
        'TemplateClass' => "/<# ([\w*_*]*) #>/is",
        'FunctionEcho' => "/<@\s([\w*]*)\s@>/is",
        'Lanuage' => "/<:\s([^>\]\"<\?>]*)\s:>/is",
        'EndLoop' => "/<%\s(endloop)\s%>/is",
        'Loop' => "/<%\sloop\s(\w*)\s%>/is",
        'EchoLoop' => "/<&\s([\w*]*)\s&>/is",
        'If' => " /<%\sif\s([\w*-*_*]*)\s%>/is",
        'Else' => "/<%\s(else)\s%>/is",
        'EndIf' => "/<%\s(endif)\s%>/is"
    );

    /**
     * Location of the cached files relative to root
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static public $storageLocation = 'etc/tmp/storage/';

    /**
     * 1 in X change on page load to check for old cached files for deletetion
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static public $deleteCacheChance = 40;

    /**
     * use eval or file cache for the template loading
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @var 1=file cache 0=eval
     */
    static public $evalOrCache = 1;

    /**
     * Runs though the functions array and loads the relivent function compiler
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static function CompileFunctions($data)
    {
        $temp = $data;
        runtime_hook::Execute('OnBeforeTemplateProcessor');
        $functions = ui_templateparser::$Functions;
        foreach ($functions as $Tag => $pattern) {
            $temp = call_user_func_array('ui_templateparser::Compile' . $Tag, array($pattern, $temp));
        }
        runtime_hook::Execute('OnAfterTemplateProcessor');
        return $temp;
    }

    /**
     * Removes any php tags
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompilePHPTags($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if (($match) && (!empty($match[1]))) {
            $i = 0;
            foreach ($match[1] as $string) {
                if ($match[0][$i] == '?>') {
                    $data = str_replace($match[0][$i], ']', $data);
                } else {
                    $data = str_replace($match[0][$i], 'PHP execution is not permitted! Caught: [', $data);
                }
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora loop template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileLoop($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if (($match) && (!empty($match[1]))) {
            $i = 0;
            foreach ($match[1] as $function) {
                $data = str_replace($match[0][$i], '<?php foreach(module_controller::get' . $function . '() as $key => $value){ ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora echo loop template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileEchoLoop($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if (($match) && (!empty($match[1]))) {
            $i = 0;
            foreach ($match[1] as $key) {
                $data = str_replace($match[0][$i], '<?php echo $value[\'' . $key . '\']; ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora end loop template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileEndLoop($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if (($match) && (!empty($match[1]))) {
            $i = 0;
            foreach ($match[1] as $string) {
                $data = str_replace($match[0][$i], '<?php } ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora if template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileIf($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if (($match) && (!empty($match[1]))) {
            $i = 0;
            foreach ($match[1] as $function) {
                $data = str_replace($match[0][$i], '<?php if(module_controller::get' . $function . '()){ ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora end if template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileEndIf($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if ($match) {
            $i = 0;
            foreach ($match[1] as $string) {
                $data = str_replace($match[0][$i], '<?php } ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora lanuage template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileLanuage($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if ($match) {
            $i = 0;
            foreach ($match[1] as $string) {
                $output = ui_language::translate(addslashes($string));
                $data = str_replace($match[0][$i], "<?php echo ui_language::translate('" . addslashes($string) . "');?>", $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora function each template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileFunctionEcho($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if ($match) {
            $i = 0;
            foreach ($match[1] as $classes) {
                $method_name = "get" . $classes;
                $output = module_controller::$method_name();
                $data = str_replace($match[0][$i], "<?php echo module_controller::" . $method_name . "(); ?>", $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora end if template tags into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileElse($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if ($match) {
            $i = 0;
            foreach ($match[1] as $string) {
                $data = str_replace($match[0][$i], '<?php }else{ ?>', $data);
                $i++;
            }
        }
        return $data;
    }

    /**
     * Compiles Sentora template class tag into valid PHP
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static private function CompileTemplateClass($value, $data)
    {
        $match = null;
        preg_match_all($value, $data, $match);
        if ($match) {
            $i = 0;
            foreach ($match[1] as $classes) {
                if (class_exists('' . $classes . '')) {
                    $moduleTemplate = call_user_func(array($classes, 'Template'));
                    $codeToInsert = ui_templateparser::CompileFunctions($moduleTemplate);
                    $data = str_replace($match[0][$i], $codeToInsert, $data);
                }
                $i++;
            }
        }
        return $data;
    }

    /**
     * Check the cache file for presents and valid/upto date Data
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static function CheckFileCache($phpCode)
    {
        $currentCode = $phpCode;

        //Get the users current theme
        $userDetails = ctrl_users::GetUserDetail();
        $userTheme = $userDetails['usertheme'];

        //The location of the cached php
        if (isset($_GET['module'])) {
            $location = ui_templateparser::$storageLocation . $userTheme . '/' . md5(fs_protector::SanitiseFolderName($_GET['module'])) . '.cache';
        } else {
            $location = ui_templateparser::$storageLocation . $userTheme . '/' . md5('index') . '.cache';
        }

        //check folder exists (First load of 10.1.0 and on new theme)
        $dirname = dirname($location);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        //check file exsists if not make, insert code else just get current code
        if (file_exists($location)) {
            $content = file_get_contents($location);
        } else {
            $handle = fopen($location, 'w');
            file_put_contents($location, $phpCode, LOCK_EX);
            $content = $phpCode;
        }

        //check the file content is the same as the generated content then return file location
        if ($currentCode == $content) {
            return $location;
        } else {
            file_put_contents($location, $phpCode, LOCK_EX);
            return $location;
        }
    }

    /**
     * Check the cache for very old files and clear them
     * @var deathAfter the number of seconds old a cache file can be deafult 7 days
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static function clearOldCache($deathAfter = '604800')
    {
        //dont check every time!
        $rollDice = rand(1, ui_templateparser::$deleteCacheChance);

        //1 in X chance of checking all files
        if ($rollDice == 1) {
            //get all files and folders in storage location
            $contents = glob(ui_templateparser::$storageLocation . "*");

            //loop each file and folder
            foreach ($contents as $content) {
                //Is folder
                if (is_dir($content)) {
                    $cacheFilesArray = glob($content . "/*");
                    foreach ($cacheFilesArray as $cacheFile) {
                        if (!is_dir($cacheFile)) {
                            $time = time() - $deathAfter;
                            if (filemtime($cacheFile) <= $time) {
                                //Delete cache files
                                $path = pathinfo($cacheFile);
                                chdir($path['dirname']);
                                unlink($path['filename'] . '.cache');
                            }
                        }
                    }
                }
            }
            chdir('/');
        }
    }

    /**
     * Set the root of the temp path location
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static public function setLocation()
    {
        self::$storageLocation = ctrl_options::GetSystemOption('sentora_root') . self::$storageLocation;
    }

    /**
     * Run the php code and return it
     * @var code the php code
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static function runPHP($template_code)
    {
        //load from DB or set a default
        $evalOrCache = ui_templateparser::$evalOrCache;

        //selected eval or file cache
        if ($evalOrCache == 1) {
            $fileLocation = ui_templateparser::CheckFileCache($template_code);
            return include($fileLocation);
        } else {
            return eval('?>' . $template_code);
        }
    }

    /**
     * All br tags to be used using the Sentora br tag loader
     * @author Sam Mottley (smottley@zpanelcp.com)
     */
    static function allowBr($templateCode)
    {
        return str_replace("ZP(br)", '<br/>', $templateCode);
    }

    /**
     * Loads in the template content and parses it to compute the place holder content.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $template_path The full path to the system template (or user template).
     * @return sting The processed template HTML.
     */
    static function Generate($template_path)
    {
        self::setLocation();
        $template_raw = file_get_contents($template_path . "/master.ztml");
        $template_code = ui_templateparser::allowBr(ui_templateparser::CompileFunctions($template_raw));
        ui_templateparser::clearOldCache();
        return ui_templateparser::runPHP($template_code);
    }

}

?>