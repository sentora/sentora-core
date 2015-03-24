<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * 
 * This class handles all core module functionality.
 * @author Kevin Andrews <kevin@zvps.uk>
 * @copyright (c) 2014, nForced Website Hosting Limtied
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @category Security
 * @link http://zvps.uk
 */
class module_controller extends ctrl_module
{
    
    #########################################################
    # Configurations                                        #
    #########################################################
    /**
     * Live is false, Dev is true
     * Dev mode enables debug messages in the view.
     * @var boolean 
     */
    static $mode = false;
    
    
    #########################################################
    # Application Start                                     #
    #########################################################
    

    static $flash_messanger = array();

    #########################################################
    # Htpasswd DAO (Data Access Object) Functions           #
    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_file_id
     * @return array
     */
    static function fetchFile( $x_htpasswd_file_id )
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_htpasswd_file
            WHERE x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
            AND x_htpasswd_file_id = :x_htpasswd_file_id
            AND x_htpasswd_file_deleted IS NULL";
        $bindArray = array( 
            ':x_htpasswd_file_id' => $x_htpasswd_file_id, 
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
            );
        try {
            $zdbh->bindQuery( $sqlString, $bindArray );
        }
        catch (PDOException $e) {
            self::setFlashMessage('error', 'this protected directory record could not be found to edit.');
            return false;
        }
        $row = $zdbh->returnRow();
        $row['x_htpasswd_file_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_file_created']);
        return $row;
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_sentora_user_id
     * @return array
     */
    static function fetchFileList()
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_htpasswd_file
            WHERE x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
            AND x_htpasswd_file_deleted IS NULL";
        $bindArray = array( ':x_htpasswd_sentora_user_id' => self::getCurrentUserId() );
        $zdbh->bindQuery( $sqlString, $bindArray );
        $rows = $zdbh->returnRows();
        /** format created */
        foreach($rows as &$row) {
            $row['x_htpasswd_file_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_file_created']);
        }
        return $rows;
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_user_id
     * @return array
     */
    static function fetchUser()
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_htpasswd_user
            WHERE x_htpasswd_user_id = :x_htpasswd_user_id
            AND x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
            AND x_htpasswd_user_deleted IS NULL";
        $bindArray = array( 
            ':x_htpasswd_user_id' => self::getUserId(),
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId()
            );
        $zdbh->bindQuery( $sqlString, $bindArray );
        
        return $zdbh->returnRow();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @return array
     */
    static function fetchUserList()
    {
        global $zdbh;
        $sqlString = "SELECT * FROM sentora_core.x_htpasswd_file 
                     LEFT OUTER JOIN x_htpasswd_mapper
                     ON x_htpasswd_file.x_htpasswd_file_id = x_htpasswd_mapper.x_htpasswd_file_id
                     LEFT OUTER JOIN x_htpasswd_user
                     ON x_htpasswd_user.x_htpasswd_user_id = x_htpasswd_mapper.x_htpasswd_user_id
                     WHERE x_htpasswd_file.x_htpasswd_file_id = :x_htpasswd_file_id
                     AND (x_htpasswd_user.x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
                          OR x_htpasswd_user.x_htpasswd_sentora_user_id IS NULL);";
        $bindArray = array( 
            ':x_htpasswd_file_id' => self::getId(), 
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),     
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        $rows = $zdbh->returnRows();
        /** format created */
        foreach($rows as &$row) {
            $row['x_htpasswd_file_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_file_created']);
            $row['x_htpasswd_user_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_user_created']);
        }
        return $rows;
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param type $x_htpasswd_file_id
     */
    static function fetchFileUserList()
    {
        global $zdbh;
        $sqlString = "
            SELECT * FROM x_htpasswd_file f
            INNER JOIN x_htpasswd_mapper m ON f.x_htpasswd_file_id=m.x_htpasswd_file_id
            INNER JOIN x_htpasswd_user u ON m.x_htpasswd_user_id=u.x_htpasswd_user_id
            WHERE f.x_htpasswd_file_id = :x_htpasswd_file_id
            AND f.x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
        ";
        $bindArray = array(
            ':x_htpasswd_file_id' => self::getId(),
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        $zdbh->bindQuery($sqlString, $bindArray);
        $rows = $zdbh->returnRows();
        /** format created */
        foreach($rows as &$row) {
            $row['x_htpasswd_file_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_file_created']);
            $row['x_htpasswd_user_created'] = date('Y-m-d H:i:s', $row['x_htpasswd_user_created']);
        }
        return $rows;
    }

    #########################################################

    /**
     * @global db_driver $zdbh
     * @param array $fileArray
     * @return int
     */
    static function createFile( array $fileArray )
    {
        global $zdbh;
        $sqlString = "
            INSERT INTO x_htpasswd_file 
            ( 
                x_htpasswd_file_target, 
                x_htpasswd_file_message, 
                x_htpasswd_file_created, 
                x_htpasswd_sentora_user_id
            )
            VALUES
            (
                :x_htpasswd_file_target, 
                :x_htpasswd_file_message, 
                :x_htpasswd_file_created, 
                :x_htpasswd_sentora_user_id
            )
        ";
        $bindArray = array(
            ':x_htpasswd_file_target'    => $fileArray[ 'x_htpasswd_file_target' ],
            ':x_htpasswd_file_message'   => $fileArray[ 'x_htpasswd_file_message' ],
            ':x_htpasswd_file_created'   => $fileArray[ 'x_htpasswd_file_created' ],
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        try {
            $zdbh->bindQuery( $sqlString, $bindArray );
        }
        catch (PDOException $exc) {
            $message = ($exc->getCode() === '23000') ? 'Folder already protected.' : 'Error adding to database.';
            self::setFlashMessage('error', $exc->getMessage());
        }

        
        return $zdbh->lastInsertId();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param array $userArray
     * @return int
     */
    static function createUser( array $userArray )
    {
        global $zdbh;
        $sqlString = "
            INSERT INTO x_htpasswd_user
            (
                x_htpasswd_user_username,
                x_htpasswd_user_password,
                x_htpasswd_user_created,
                x_htpasswd_sentora_user_id
            )
            VALUES
            (
                :x_htpasswd_user_username,
                :x_htpasswd_user_password,
                :x_htpasswd_user_created,
                :x_htpasswd_sentora_user_id
            )
        ";
        $bindArray = array(
            ':x_htpasswd_user_username' => $userArray[ 'x_htpasswd_user_username' ],
            ':x_htpasswd_user_password' => $userArray[ 'x_htpasswd_user_password' ],
            ':x_htpasswd_user_created'  => time(),
            ':x_htpasswd_sentora_user_id'  => self::getCurrentUserId(),
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->lastInsertId();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_file_id
     * @param int $x_htpasswd_user_id
     * @return int
     */
    static function createMapper( $x_htpasswd_file_id, $x_htpasswd_user_id )
    {
        global $zdbh;
        $x_htpasswd_file_id = (int) $x_htpasswd_file_id;
        $x_htpasswd_user_id = (int) $x_htpasswd_user_id;
        $sqlString               = "
            INSERT INTO x_htpasswd_mapper
            (
                x_htpasswd_file_id,
                x_htpasswd_user_id
            )
            VALUES
            (
                :x_htpasswd_file_id,
                :x_htpasswd_user_id
            )
        ";
        $bindArray = array(
            ':x_htpasswd_file_id' => $x_htpasswd_file_id,
            ':x_htpasswd_user_id' => $x_htpasswd_user_id,
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->lastInsertId();
    }

    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param array $fileArray
     * @return int
     */
    static function updateFile( $fileArray )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_htpasswd_file SET
            x_htpasswd_file_target = :x_htpasswd_file_target,
            x_htpasswd_file_message = :x_htpasswd_file_message
            WHERE x_htpasswd_file_id = :x_htpasswd_file_id
            AND x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
        ";
        $bindArray = array(
            ':x_htpasswd_file_id'      => $fileArray[ 'x_htpasswd_file_id' ],
            ':x_htpasswd_file_target'  => $fileArray[ 'x_htpasswd_file_target' ],
            ':x_htpasswd_file_message' => $fileArray[ 'x_htpasswd_file_message' ],
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    static function updateUser( $userArray )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_htpasswd_user SET
            x_htpasswd_user_username = :x_htpasswd_user_username,
            x_htpasswd_user_password = :x_htpasswd_user_password
            WHERE
            x_htpasswd_user_id = :x_htpasswd_user_id
            x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
        ";
        $bindArray = array(
            ':x_htpasswd_user_id'       => self::getUserId(),
            ':x_htpasswd_user_username' => $userArray[ 'x_htpasswd_user_username' ],
            ':x_htpasswd_user_password' => $userArray[ 'x_htpasswd_user_password' ],
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_file_id
     * @return int
     */
    static function deleteFile( $x_htpasswd_file_id )
    {
        global $zdbh;
        $sqlString = "
            DELETE FROM x_htpasswd_file 
            WHERE x_htpasswd_file_id = :x_htpasswd_file_id
            AND x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
        ";
        $bindArray = array( 
            ':x_htpasswd_file_id' => $x_htpasswd_file_id,
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_user_id
     * @return int
     */
    static function deleteUser( $x_htpasswd_user_id )
    {
        global $zdbh;
        $sqlString = "
            DELETE FROM x_htpasswd_user 
            WHERE x_htpasswd_user_id = :x_htpasswd_user_id
            AND x_htpasswd_sentora_user_id = :x_htpasswd_sentora_user_id
        ";
        $bindArray = array( 
            ':x_htpasswd_user_id' => $x_htpasswd_user_id,
            ':x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_htpasswd_file_id
     * @param int $x_htpasswd_user_id
     * @return int
     */
    static function deleteMapper( $x_htpasswd_file_id, $x_htpasswd_user_id )
    {
        global $zdbh;
        $sqlString = "
            DELETE FROM x_htpasswd_mapper 
            WHERE
            x_htpasswd_file_id = :x_htpasswd_file_id
            AND
            x_htpasswd_user_id = :x_htpasswd_user_id
        ";
        $bindArray = array(
            ':x_htpasswd_file_id' => $x_htpasswd_file_id,
            ':x_htpasswd_user_id' => $x_htpasswd_user_id
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    #########################################################
    # File System Operations                                #
    #########################################################
    static function fileInPathCheck($file)
    {
        $path = self::getHostDir() . self::getCurrentUsername() . '/public_html/' . $file . '/';
        $realPath = realpath($path);
        
        if(!$realPath)
        {
            self::setFlashMessage('error', 'Path \'' . $path . '\' not found.');
            return false;
        }
        
        if( 0 !== strpos($realPath, self::getHostDir() . self::getCurrentUsername() . '/'))
        {
            self::setFlashMessage('error', 'Path \'' . $realPath . '\' is outside your home directory and is not allowed.');
            return false;
        }
        
        self::setFlashMessage('debug', 'fileInPathCheck successful');
        return $realPath;
    }
    
    static function fileExists($combinedPath)
    {
        if(!fs_director::CheckFileExists($combinedPath)) {
            self::setFlashMessage('debug', 'file does not exist');
            return false;
        }
        self::setFlashMessage('debug', 'file exists');
        return true;
    }
    
    static function fileHtaccessExists($realPath)
    {
        if(!self::fileExists($realPath . '/.htaccess'))
        {
            self::setFlashMessage('debug', 'htaccess file does not exists: ' . $realPath . '/.htaccess');
            return false;
        }
        self::setFlashMessage('debug', 'htaccess file exists');
        return true;
    }
    
    static function fileHtpasswdExists($combinedPath)
    {
        if(!self::fileExists($combinedPath))
        {
            self::setFlashMessage('debug', 'htpasswd file does not exists: ' . $combinedPath);
            return false;
        }
        self::setFlashMessage('debug', 'htpasswd file exists');
        return true;
    }
    
    static function writeFile($fileCombinedPath, $string='', $append=false )
    {
        
        $openType = (!$append) ? 'w' : 'a';

        $fp = fopen($fileCombinedPath, $openType);

        if(false === $fp) {
            self::setFlashMessage('debug', 'file pointer returned false on fopen ' . $fileCombinedPath . ' ' .$openType);
            return false;
        }

        if(false === fwrite($fp, $string)) {
            self::setFlashMessage('debug', 'file pointer returned false on fwrite');
            return false;
        }

        if (false === fclose($fp)) {
           self::setFlashMessage('debug', 'file pointer returned false on fclose');
            return false; 
        }
        
        unset($fp);

        self::setFlashMessage('debug', 'file created successfully');
        return true;
    }
    
    static function readFile ($fileCombinedPath) {
        $fp = fopen($fileCombinedPath, 'r');

        if(false === $fp) {
            self::setFlashMessage('debug', 'file pointer returned false on fopen ' . $fileCombinedPath . ' ' .$openType);
            return false;
        }
        $string = fread($fp, 10000000);
        if(false === $string) {
            self::setFlashMessage('debug', 'file pointer returned false on fwrite');
            return false;
        }

        if (false === fclose($fp)) {
           self::setFlashMessage('debug', 'file pointer returned false on fclose');
            return false; 
        }
        
        unset($fp);

        self::setFlashMessage('debug', 'file created successfully');
        return $string;
    }
    
    static function createHtaccessFile($realPath) {
        $combinedPath = $realPath . '/.htaccess';
        if(!self::writeFile($combinedPath) )
        {
            self::setFlashMessage('error', 'failed to create htaccess file.');
            return false;
        }
        return true;
    }
    
    static function createPasswdFile($realPath)
    {
        
        $path = self::getHostDir() . self::getCurrentUsername() . '/htpasswd/';

        if(!file_exists($path))
        {
            self::setFlashMessage('debug', 'passwd folder doesn\'t exist');
            
            if(!mkdir($path, 0777, true))
            {
                self::setFlashMessage('error', 'passwd folder failed creation');
                return false;
            }
        }
        
        $combinedPath = $path . 'htpasswd-' . md5($realPath);
        
        if(!self::writeFile($combinedPath))
        {
            self::setFlashMessage('error', 'failed to create passwd file for protected directory.');
            return false;
        }
        
        return true;
    }
    
    static function buildHtaccessLink($message, $htpasswdFile) {
        $htaccessString = 'AuthName "' . $message . '"' . PHP_EOL .
                          'AuthType Basic' . PHP_EOL .
                          'AuthUserFile ' . $htpasswdFile . PHP_EOL .
                          'Require valid-user' . PHP_EOL;
        return $htaccessString;
    }
    
    static function writeHtaccessLink($realPath, $append=false, $message)
    {
        $htpasswdFile = self::getHostDir() . self::getCurrentUsername() . '/htpasswd/' . 'htpasswd-' . md5($realPath);
        $htaccessString = self::buildHtaccessLink($message, $htpasswdFile);
        
        $combinedPath = $realPath . '/.htaccess';
        
        if(!self::writeFile($combinedPath, $htaccessString, $append ) )
        {
            self::setFlashMessage('error', 'failed to write htaccess file data.');
            return false;
        }
        
        self::setFlashMessage('debug', 'linked htaccess and passwd successfully.');
        return true;
    }
    
    static function removeHtaccessLink($realPath, $message)
    {
        $combinedPath = $realPath . '/.htaccess';
        $data = self::readFile($combinedPath);

        $htpasswdFile = self::getHostDir() . self::getCurrentUsername(). '/' . 'htpasswd/' . 'htpasswd-' . md5($realPath);
        
        $htaccessString = self::buildHtaccessLink($message, $htpasswdFile);
                      
        $newFileString = str_replace($htaccessString, '', $data);

        if(!self::writeFile($combinedPath, $newFileString))
        {
            self::setFlashMessage('error', 'failed to remove htaccess link to htpasswd file.');
            return false;
        }
        
        self::setFlashMessage('debug', 'htaccess link to htpasswd removed successfully.');
        return true;
    }
    
    static function removeHtpasswd($combinedPath)
    {
        if(!unlink($combinedPath))
        {
            self::setFlashMessage('debug', 'htpasswd file removal failed : ' . $combinedPath);
            return false;
        }
        
        self::setFlashMessage('debug', 'htpasswd file removal succeeded : ' . $combinedPath);
        return true;
        
    }
    
    static function writePasswdUsers($file) {
        $files = self::fetchFileUserList($file['x_htpasswd_file_id']);
        $userString = "";
        foreach($files as $file) {
            $userString .= 
                $file['x_htpasswd_user_username'] . 
                ':' . 
                $file['x_htpasswd_user_password'] . PHP_EOL
            ; 
        }
        
        self::writeFile(
            self::getHostDir() . 
            '/' . 
            self::getCurrentUsername() . 
            '/htpasswd/'.
            'htpasswd-' . md5($file['x_htpasswd_file_target'])
            , 
            $userString
            , 
            false
        );
    }

    #########################################################
    # Service Output methods
    #########################################################
    static function getFileList()
    {
        return self::fetchFileList();
    }
    
    static function getFile()
    {
        return array(self::fetchFile( self::getId() ));
    }
    
    static function getHostDir()
    {
        return ctrl_options::GetSystemOption('hosted_dir');
    }
    
    static function getUserFileList()
    {
        return self::fetchFileUserList();
    }
    
    static function getUser()
    {
        return array(self::fetchUser());
    }
    
    #########################################################
    # Input Checkers
    #########################################################
    static function getId()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ( 
            (isset($urlvars['control'])) && 
            (isset($urlvars['id'])) 
        ) {
            return (int) $urlvars['id'];
        }
        return false;
    }
    
    static function getUserId()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ( 
            (isset($urlvars['control'])) && 
            (isset($urlvars['userid'])) 
        ) {
            return (int) $urlvars['userid'];
        }
        return false;
    }
    
    #########################################################
    # Post Actions
    #########################################################
    static function doCreateProtection()
    {
        global $controller;
        runtime_csfr::Protect();
        
        $file = $controller->GetControllerRequest('FORM', 'file');
        $message = $controller->GetControllerRequest('FORM', 'message');
        
        
        // Check File path security check
        if(!self::hasFlashErrors()) { $fileTarget = self::fileInPathCheck($file); }
        
        // Check .htaccess exists
        if(!self::hasFlashErrors()) { $exists = self::fileHtaccessExists($fileTarget); }
        
        // Create .htaccess file if needed
        if(!self::hasFlashErrors() && !$exists) { self::createHtaccessFile($fileTarget); }
        
        // Create protected passwd file
        if(!self::hasFlashErrors()) { self::createPasswdFile($fileTarget); }
 
        // Remove exiting protection to prevent duplicate entries
        if(!self::hasFlashErrors()) { self::removeHtaccessLink($fileTarget, $message); }

        $append = !$exists ? false : true;
        
        // Write htaccess configs to link to passwd file
        if(!self::hasFlashErrors()) { self::writeHtaccessLink($fileTarget, $append, $message); }
        
        // Create DB record
        if(!self::hasFlashErrors())
        {
            $id = self::createFile(
                array(
                    'x_htpasswd_file_target'    => $fileTarget,
                    'x_htpasswd_file_message'   => $message,
                    'x_htpasswd_file_created'   => time(),
                    'x_htpasswd_sentora_user_id' => self::getCurrentUserId(),
                )
            );
            if(!self::hasFlashErrors()) 
            {
                self::setFlashMessage('debug', 'protected directory added to db successfully.');
            }
        }
        

        // No errors
        if(!self::hasFlashErrors()) 
        {
            header("location: ./?module=" . $controller->GetCurrentModule() . "&control=EditProtection&id=" . $id);
        }

    }
    
    static function doEditProtection()
    {
        global $controller;
        runtime_csfr::Protect();
        
        $message = $controller->GetControllerRequest('FORM', 'message');
        $id = self::getId();
        $file = self::fetchFile($id);
        
        self::removeHtaccessLink($file['x_htpasswd_file_target'], $file['x_htpasswd_file_message']);

        // Check .htaccess exists
        if(!self::hasFlashErrors()) { $exists = self::fileHtaccessExists($file['x_htpasswd_file_target']); }
        // Create .htaccess file if needed
        if(!self::hasFlashErrors() && !$exists) { self::createHtaccessFile($file['x_htpasswd_file_target']); }
        // Write htaccess configs to link to passwd file
        $append = !$exists ? false : true;
        if(!self::hasFlashErrors()) { self::writeHtaccessLink($file['x_htpasswd_file_target'], $append, $message); }
        
        if(!self::hasFlashErrors())
        {
            self::updateFile(array(
                'x_htpasswd_file_id'      => $id,
                'x_htpasswd_file_target'  => $file['x_htpasswd_file_target'],
                'x_htpasswd_file_message' => $message,
            ));
        }
    }
    
    static function doDeleteProtection()
    {
        global $controller;
        runtime_csfr::Protect();
        $id = self::getId();
        $file = self::fetchFile($id);
        $htpasswdFile = self::getHostDir() . self::getCurrentUsername() . '/htpasswd/' . 'htpasswd-' . md5($file['x_htpasswd_file_target']);

        // delete from htaccess file
        self::removeHtaccessLink($file['x_htpasswd_file_target'],$file['x_htpasswd_file_message']);
        
        // delete htaccess passwd file
        self::removeHtpasswd($htpasswdFile);
        
        // delete all users and mappings from db related to protected directory
        $files = self::fetchUserList($id);
        
        if($files && !self::hasFlashErrors()) {
            foreach ($files as $file) {
                if($file['x_htpasswd_file_id'] && $file['x_htpasswd_user_id']) {
                    self::deleteMapper($file['x_htpasswd_file_id'], $file['x_htpasswd_user_id']);
                    self::setFlashMessage('debug', 'deleting file user mapper');
                }
                if($file['x_htpasswd_file_id']) { 
                    self::deleteUser($file['x_htpasswd_user_id']);
                    self::setFlashMessage('debug', 'deleting user');
                }
            }
        }
        
        // delete protected from db
        self::deleteFile($id);
        self::setFlashMessage('debug', 'deleting file');

        // return to list
        if(!self::hasFlashErrors()) 
        {
            header("location: ./?module=" . $controller->GetCurrentModule() . "&control=Index");
        }
    }
    
    static function doCreateUser()
    {
        global $controller;
        runtime_csfr::Protect();
        $id = self::getId();
        $file = self::fetchFile($id);

        $username = $controller->GetControllerRequest('FORM', 'username');
        $password = $controller->GetControllerRequest('FORM', 'password');
        
        $encryptedPassword = crypt($password, base64_encode($password));
        
        $userId = self::createUser(array(
            'x_htpasswd_user_username'  => $username,
            'x_htpasswd_user_password'  => $encryptedPassword,
        ));
        
        self::createMapper($id, $userId);
        
        self::writePasswdUsers($file);
        
        header("location: ./?module=" . $controller->GetCurrentModule() . "&control=EditProtection&id=" . $id);
        
    }
    
        static function doUpdateUser()
    {
        global $controller;
        runtime_csfr::Protect();
        $id = self::getId();
        $file = self::fetchFile($id);

        $username = $controller->GetControllerRequest('FORM', 'username');
        $password = $controller->GetControllerRequest('FORM', 'password');
        
        $encryptedPassword = crypt($password, base64_encode($password));
        
        self::updateUser(array(
            'x_htpasswd_user_username'  => $username,
            'x_htpasswd_user_password'  => $encryptedPassword,
        ));
        
        self::writePasswdUsers($file);
        
        header("location: ./?module=" . $controller->GetCurrentModule() . "&control=EditProtection&id=" . $id);
        
    }
    
    static function doDeleteUser()
    {
        global $controller;
        runtime_csfr::Protect();
        
        $id = self::getId();
        $userId = self::getUserId();
        $file = self::fetchFile($id);
        
        if(!self::hasFlashErrors()) 
        {
        self::deleteUser($userId);
        }
        
        if(!self::hasFlashErrors()) 
        {
        self::deleteMapper($id, $userId);
        }
        
        if(!self::hasFlashErrors()) 
        {
            self::writePasswdUsers($file);
        }
        
        if(!self::hasFlashErrors()) 
        {
            header("location: ./?module=" . $controller->GetCurrentModule() . "&control=EditProtection&id=" . $id);
        }
        
    }

    
    #########################################################
    # Controller Actions
    #########################################################
    static function getisEditProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "EditProtection")) {
            return true;
        }
        return false;
    }

    static function getisCreateProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "CreateProtection")) {
            return true;
        }
        return false;
    }
    
    static function getisDeleteProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "DeleteProtection")) {
            return true;
        }
        return false;
    }

    static function getisEditUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "EditUser")) {
            return true;
        }
        return false;
    }

    static function getisCreateUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "CreateUser")) {
            return true;
        }
        return false;
    }
    
    static function getisDeleteUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "DeleteUser")) {
            return true;
        }
        return false;
    }
    
    static function getisIndex()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ( 
            (!isset($urlvars['control'])) || 
            ( (isset($urlvars['control'])) && ($urlvars['control'] === "Index")) 
        ) {
            return true;
        }
        return false;
    }

    #########################################################
    # General Utility Methods
    #########################################################
    
    static function getModuleMode()
    {
        return self::$mode;
    }

    private static function getCurrentUserId()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser[ 'userid' ];
    }
    
    private static function getCurrentUsername()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser[ 'username' ];
    }
    
    #########################
    # Flash message methods #
    #########################

    static function getFlashMessages()
    {
        return self::$flash_messanger;
    }
    
    static function getFlashErrorMessages()
    {
        $messages = self::getFlashMessages();
        $errorMessages = array();
        foreach( $messages as $message ) {
            if(array_key_exists('error', $message)) {
                $errorMessages[] = $message;
            }
        }
        return $errorMessages;
    }
    
    static function getFlashDebugMessages()
    {
        $messages = self::getFlashMessages();
        $debugMessages = array();
        foreach( $messages as $message ) {
            if(array_key_exists('debug', $message)) {
                $debugMessages[] = $message;
            }
        }
        return $debugMessages;
    }
    
    static function setFlashMessage($type,$message)
    {
        self::$flash_messanger[] = array($type => $message);
    }
    
    static function hasFlashErrors()
    {
        $messages = self::getFlashMessages();
        
        if(empty($messages)) { return false; }
        
        foreach( $messages as $message ) {
            if(array_key_exists('error', $message)) {
                return true;
            }
        }
        
        return false;
    }

}
