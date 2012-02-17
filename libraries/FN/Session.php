<?php

session_start();
class FN_Session
{
    public static function setFlashMessage ( $message )
    {
        $_SESSION['fn:flashMessage'] = $message;
    }
    
    public static function flashMessageExists()
    {
        return isset ($_SESSION['fn:flashMessage']);
    }
    
    public static function getFlashMessage()
    {
        $flashMessage = $_SESSION['fn:flashMessage'];
        unset ($_SESSION['fn:flashMessage']);
        
        return $flashMessage;
    }
}

?>