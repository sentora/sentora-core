<?php

/**
 * Secure system command execution class.
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @version 1.1.0
 * @author Kevin Andrews (kandrews@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_system
{
    /**
     * Safely run an escaped system() command.
     * @param string $command The command of which to be executed.
     * @param array $args Any arguments seperated by a space should be in a seperate array value.
     * @return string
     */
    static function systemCommand( $command, array $args )
    {

        $escapedCommand = self::escapeCommand( $command );
        $escapedArgs    = self::escapeArgs( $args );
        $builtEscapedCommand = self::buildescapedCommand( $escapedCommand, $escapedArgs );

        system( $builtEscapedCommand, $systemReturnValue );

        return $systemReturnValue;
    }

    /**
     * Escapes shell metacharacters from the command.
     * @param string $command The command to be escaped.
     * @return string
     */
    static private function escapeCommand( $command )
    {
        return escapeshellcmd( $command );
    }

    /**
     * Escape's a string to be used as a shell argument.
     * @param array $args Array of arguments of which to be escaped.
     * @return array
     */
    static private function escapeArgs( array $args )
    {
        $escapedArgs = array( );

        foreach ( $args as $arg ) {
            $escapedArgs[ ] = escapeshellarg( $arg );
        }

        return $escapedArgs;
    }

    /**
     * Builds the escaped command complete with the specified arguments.
     * @param string $escapedCommand
     * @param array $escapedArgs
     * @return string
     */
    static private function buildescapedCommand( $escapedCommand, array $escapedArgs )
    {
        $escapedArgString = null;

        foreach ( $escapedArgs as $escapedArg ) {
            $escapedArgString .= " " . $escapedArg;
        }

        return $escapedCommand . $escapedArgString;
    }

    static private function commandBlackList()
    {

    }

}