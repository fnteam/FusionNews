<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$username = ( isset ($PVARS["username"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['username'], 0, 40))) : '';
$email = ( isset ($PVARS["email"]) ) ? fn_trim (single_line ($PVARS['email'])) : '';
$password = ( isset ($PVARS["password"]) ) ? fn_trim (utf8_substr ($PVARS['password'], 0, 40)) : '';
$nick = ( isset ($PVARS["nick"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['nick'], 0, 40))) : '';
$icon = ( isset ($PVARS["icon"]) ) ? fn_trim (single_line ($PVARS['icon'])) : '';
$timeoffset = ( isset ($PVARS['timeoffset']) ) ? (int)$PVARS['timeoffset'] : 0;
$le = ( isset ($PVARS['le']) ) ? (int)$PVARS['le'] : 1;
$showemail = ( isset ($PVARS['hidemail']) ) ? 0 : 1;

if ( !$username || !$email || !$password )
{
    $title = $lang['ind114'];
    trigger_error ($lang['ind115'], E_USER_WARNING);
}

if ( !is_valid_email ($email))
{
    $title = $lang['ind116'];
    trigger_error ($lang['ind117'], E_USER_WARNING);
}

// Clamp between these 2 values
$timeoffset = $timeoffset > 24 ? 24 : $timeoffset;
$timeoffset = $timeoffset < -24 ? -24 : $timeoffset;

if ( get_author ($username, $nick) )
{
    trigger_error ($lang['ind32'], E_USER_WARNING);
}

$salt = create_password_salt();
create_user (array (
    'username' => $username,
    'nickname' => $nick,
    'email' => "{$showemail}={$email}",
    'icon' => $icon,
    'timeoffset' => $timeoffset,
    'passwordhash' => md5 ($salt . $password),
    'passwordsalt' => $salt,
    'level' => $le
));

$title = $lang['ind33'];
echo make_redirect ($username . ' ' . $lang['ind118'], '?id=users', $lang['ind333']);

?>