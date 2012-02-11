<?php

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
    trigger_error ($lang['ind18'], E_USER_WARNING);
}

if ( !is_login_valid ($post_user, $post_pass) )
{
    trigger_error ($lang['ind18b'], E_USER_WARNING);
}
else
{
    $userdata = login_session_create ($post_user, $keep_login);
}

$title = $lang['ind397'];
echo make_redirect ($lang['ind398'], $next, $lang['ind259']);


?>