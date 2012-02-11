<?php


if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$name = ( isset ($PVARS["name"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['name'], 0, 40))) : '';
$deleteuser = ( isset ($PVARS["del"]) ) ? 1 : 0;

if ( $deleteuser )
{
    if ( $userdata['user'] == $name )
    {
        trigger_error ($lang['error12'], E_USER_WARNING);
    }
    
    revoke_category_access ($name);
    delete_user ($name);
    
    $title = $lang['ind413'];
    echo make_redirect (sprintf ($lang['ind412'], $name), '?id=users', $lang['ind333']);
}
else
{
    $nick1 = ( isset ($PVARS["nick1"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['nick1'], 0, 40))) : '';
    $mail1 = ( isset ($PVARS["mail1"]) ) ? fn_trim (single_line ($PVARS['mail1'])) : '';
    $new_password = ( isset ($PVARS['new_password']) ) ? fn_trim (utf8_substr ($PVARS['new_password'], 0, 40)) : '';
    $confirm_pass = ( isset ($PVARS['confirm_pass']) ) ? fn_trim (utf8_substr ($PVARS['confirm_pass'], 0, 40)) : '';
    $icon1 = ( isset ($PVARS["icon1"]) ) ? single_line ($PVARS['icon1']) : '';
    $timeoffset = ( isset ($PVARS["timeoffset"]) ) ? (int)$PVARS['timeoffset'] : '';
    $fle = ( isset ($PVARS["fle"]) ) ? (int)$PVARS['fle'] : 1;
    $showemail = ( isset ($PVARS['showemail']) ) ? 0 : 1;

    if ( !$nick1 || !$mail1 || !$name )
    {
        trigger_error ($lang['ind296'], E_USER_WARNING);
    }

    if ( $new_password && !$confirm_pass )
    {
        trigger_error ($lang['ind297'], E_USER_WARNING);
    }

    if ( $new_password != $confirm_pass )
    {
        trigger_error ($lang['ind197'], E_USER_WARNING);
    }
    
    if ( !is_valid_email ($mail1) )
    {
        $title = $lang['ind116'];
        trigger_error ($lang['ind117'], E_USER_WARNING);
    }

    // Clamp between these 2 values
    $timeoffset = min (max ($timeoffset, -24), 24);
    
    $user = null;
    if ( $new_password == '' )
    {
        $user = get_user ($name);
        $salt = $user['passwordsalt'];
    }
    else
    {
        $salt = create_password_salt();
    }
    $newdata = array (
        'username' => $name,
        'nickname' => $nick1,
        'email' => "{$showemail}={$mail1}",
        'icon' => $icon1,
        'timeoffset' => $timeoffset,
        'passwordhash' => ($new_password != '' ? md5 ($salt . $new_password) : $user['passwordhash']),
        'passwordsalt' => $salt,
        'level' => $fle
    );
    
    update_user ($name, $newdata);

    $title = $lang['ind285'];
    echo make_redirect ($lang['ind34a'], '?id=users', $lang['ind333']);
}


?>