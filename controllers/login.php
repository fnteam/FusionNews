<?php

function redirect ( $code, $location )
{
    $http_codenames = array (
        303 => 'See Other'
    );
    
    assert (array_key_exists ($code, $http_codenames));
    
    header ('HTTP/1.1 ' . $code . ' ' . $http_codenames[$code]);
    header ('Location: ' . $location);
    exit;
}

if ( has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind294'], E_USER_WARNING);
}

$post_user = ( isset ($PVARS['username']) ) ? fn_trim (utf8_substr ($PVARS['username'], 0, 40)) : '';
$post_pass = ( isset ($PVARS['password']) ) ? fn_trim (utf8_substr ($PVARS['password'], 0, 40)) : '';
$keep_login = ( isset ($PVARS['keep_login']) ) ? 1 : 0;
$next = ( isset ($PVARS['next']) ) ? 'index.php?' . urldecode ($PVARS['next']) : '';

if ( !$post_user || !$post_pass )
{
    FN_Session::setFlashMessage ($lang['ind18']);
    redirect (303, '?');
}

if ( !is_login_valid ($post_user, $post_pass) )
{
    FN_Session::setFlashMessage ($lang['ind18b']);
    redirect (303, '?');
}
else
{
    $userdata = login_session_create ($post_user, $keep_login);
}

$title = $lang['ind397'];
echo make_redirect ($lang['ind398'], $next, $lang['ind259']);


?>