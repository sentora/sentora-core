<?php

/**
 * Description of system
 * @author Kevin
 */
class ctrl_system
{
    /**
     * Safely run an escaped system() command.
     * @param string $command
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
     * 
     * @param string $command
     * @return string
     */
    static private function escapeCommand( $command )
    {
        return escapeshellcmd( $command );
    }

    /**
     * 
     * @param array $args
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
     * 
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