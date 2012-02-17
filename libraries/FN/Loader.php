<?php

class FN_Loader
{
    private static $inited = false;
    private static $include_paths = array();
    
    public static function init()
    {
        if ( !self::$inited )
        {
            self::$inited = spl_autoload_register ('FN_Loader::loadClass');
            if ( !self::$inited )
            {
                throw new Exception ("Failed to initialize FN class auto loader.");
            }
            
            self::$include_paths = explode (PATH_SEPARATOR, get_include_path());
        }
    }
    
    private static function loadClass ( $className )
    {
        $classFile = str_replace ('_', DIRECTORY_SEPARATOR, $className) . '.php';
        foreach ( self::$include_paths as &$incpath )
        {
            $classPath = $incpath . DIRECTORY_SEPARATOR . $classFile;
            if ( is_file ($classPath) && is_readable ($classPath) )
            {
                require_once ($classPath);
                break;
            }
        }
    }
}

?>