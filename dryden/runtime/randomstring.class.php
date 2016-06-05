<?php
/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Class provides functionallity to generate secure random strings
 * @package zpanelx
 * @implimentation To be inpliment into zpanel's core fucntions by 10.0.3
 * @subpackage dryden -> runtime
 * @version 1.0.2
 * @author Sam Mottley (smottley@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */

class runtime_randomstring{
    /**
     * Generate a random string
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @param int $size The size of the hash default 50 
     * @param string $charachters list of all allowed chars
     * @param string $hash True or False or Type of algeritherm to hash the the mixed string with. Hash may break other settings depending on the argirithom selected.
     * @return string A random string
     */    
     static public function randomHash($size = 50, $characters='1234567890qwertyuiopasdfghjklzxcvbnm', $hash = false) {
        //declare varables
        $seed = '';
        $hashMixed = '';
        $charachters = $characters;
        $loop = $size / 5;
        
        //loop X times random number
        for($m = 0; $m < $loop; $m++){
            //random string
            $seed .= str_replace('-','',crc32(uniqid(sha1(microtime(true) . getmypid() . rand(10000,99999999)), true)));
        }
        
        //randomise string again
        mt_srand($seed);
        mt_rand();
        
        //Make the random number into a random string
        $loopNow = strlen($seed);
        //loop 
        for ($i = 0; $i < $loopNow; $i++) {
            //search for char in chracter list 
           $char = mt_rand($seed[$i], strlen($charachters));
           //add it to hash
           $hashMixed .= @$charachters[$char];
        }
        
        //Just incase php is Pseudo based. I dont think so but double check 
        $hashMixed = substr($hashMixed, 10);
        $length = strlen($hashMixed)-10;
        $hashMixed = substr($hashMixed, 0,$length);
        
        //Now we check if size matters
        if($size != false){
            //check hash against demanded size
            if(strlen($hashMixed) >= $size){
                //trim the hash down to size
                $hashMixed = substr($hashMixed, 0, $size);
            }else if(strlen($hashMixed) <= $size){
                //increase hash length here
                //Declare varables
                $needed = $size - strlen($hashMixed);
                $decimalLoop = $needed / 5;
                $loops = round($decimalLoop, 0);
                $seed = '';
                $hashMixedAdditon = '';
                
                //Loop X times Make random number
                for($m = 0; $m < $loops; $m++){
                    //random string
                    $seed .= str_replace('-','',crc32(uniqid(sha1(microtime(true) . getmypid() . rand(10000,99999999)), true)));
                }
                //randomise string again
                mt_srand($seed);
                mt_rand();
                
                //Change random number into string
                for ($i = 0; $i < strlen($seed); $i++) {
                    //search for char in chracter list 
                   $char = mt_rand($seed[$i], strlen($charachters));
                   //add it to hash
                   $hashMixedAdditon .= @$charachters[$char];
                }
                
                //trim to need number of chars
                $additionalHash = substr($hashMixedAdditon, 0, $needed);
                
                //Add the additional hash onto the end to make the correct size
                $hashMixed = $hashMixed . $additionalHash;
            }
        }
            
        //check if hashing is needed
        if($hash == false){
            //do not hash
            $hash = $hashMixed;
        }else{
            if($hash == true){ $hash = 'sha256'; }
            //Then hash the hash is sha256
            $hash = hash($hash, $hashMixed);
        }
        
        //Randomise string again
        $hash = str_shuffle($hash);
        
        //return hash
        return $hash;
    }

    
}

?>
