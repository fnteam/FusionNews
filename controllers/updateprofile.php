<?php

if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$email = ( isset ($PVARS["mail1"]) ) ? fn_trim (single_line ($PVARS['mail1'])) : '';
$oldpassw = ( isset ($PVARS["oldpassw"]) ) ? fn_trim (utf8_substr ($PVARS['oldpassw'], 0, 40)) : '';
$passw = ( isset ($PVARS["passw"]) ) ? fn_trim (utf8_substr ($PVARS['passw'], 0, 40)) : '';
$nick = ( isset ($PVARS['nick1']) ) ? fn_trim (single_line (utf8_substr($PVARS["nick1"], 0, 40))) : '';
$icon = ( isset ($PVARS["icon1"]) ) ? single_line ($PVARS['icon1']) : '';
$timeoffset = ( isset ($PVARS['timeoffset']) ) ? (int)$PVARS['timeoffset'] : 0;
$showemail = ( isset ($PVARS['showemail']) );

if ( !$nick || !$email )
{
    $title = $lang['ind114'];
    trigger_error ($lang['ind296'], E_USER_WARNING);
}

if ( !is_valid_email ($email) )
{
    $title = $lang['ind116'];
    trigger_error ($lang['ind117'], E_USER_WARNING);
}

if ( $oldpassw != '' && md5 ($userdata['salt'] . $oldpassw) != $userdata['password'] )
{
    trigger_error ($lang['ind288'], E_USER_WARNING);
}

if ( $userdata['nick'] != $nick && get_author ('', $nick) )
{
    trigger_error ($lang['ind32'], E_USER_WARNING);
}

if ( $passw != '' )
{
    $userdata['salt'] = create_password_salt();
}

$newdata = array (
    'username' => $userdata['user'],
    'nickname' => $nick,
    'email' => "{$showemail}={$email}",
    'icon' => $icon,
    'timeoffset' => $timeoffset,
    'passwordhash' => ($passw != '' ? md5 ($userdata['salt'] . $passw) : $userdata['password']),
    'passwordsalt' => $userdata['salt'],
    'level' => $userdata['level']
);

update_user ($userdata['user'], $newdata);

$title = $lang['ind286'];
echo make_redirect ($lang['ind287']);


?>