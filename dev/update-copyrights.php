<?php

$directories = array ('../', '../upgrade/');
foreach ( $directories as $directory )
{
    $dir = opendir ($directory);
    if ( $dir === false )
    {
        exit ('Failed to open Fusion News directory.');
    }

    $year = date ('Y');
    while ( ($file = readdir ($dir)) !== false )
    {
        if ( $file == '.' || $file == '..' ) continue;
        
        $file = $directory . $file;
        if ( is_dir ($file) ) continue;
        $text = file_get_contents ($file);
        
        $text = preg_replace ('#(?<= \* @copyright \(c\) 2006 - )([0-9]{4})(?=, FusionNews\.net)#', $year, $text);
        
        if ( file_put_contents ($file, $text) === false )
        {
            echo 'Failed to write contents back to ' . $file . '.';
            break;
        }
    }

    closedir ($dir);
}
exit ('Files updated.');

?>