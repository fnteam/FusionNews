<?php

if ( !has_access (NEWS_EDITOR) )
{
	trigger_error ($lang['ind19'], E_USER_WARNING);
}

$ajax = isset ($_GET['ajax']);
$ip = isset ($VARS['ip']) ? $VARS['ip'] : '';

banlist_delete ($ip);

if ( $ajax )
{
    header ('HTTP/1.1 200 OK');
    exit;
}
else
{
    $title = 'IP Removed';
    echo 'IP address was removed from banlist.';
}

?>