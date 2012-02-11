<?php

if ( !has_access (NEWS_EDITOR) )
{
	trigger_error ($lang['ind19'], E_USER_WARNING);
}

$ajax = isset ($_GET['ajax']);
$ip = isset ($VARS['ip']) ? $VARS['ip'] : '';

$valid_ip = is_valid_ip_address ($ip);

if ( !$valid_ip )
{
    if ( $ajax )
    {
        header ('HTTP/1.1 400 Bad Request');
        exit;
    }
    else
    {
        $title = $ind17;
        echo 'Invalid IP address etc';
    }
}
else
{
    banlist_add ($ip);
    if ( $ajax )
    {
        header ('HTTP/1.1 200 OK');
        exit;
    }
    else
    {
        $title = 'IP Banned';
        echo 'IP address was banned.';
    }
}

?>