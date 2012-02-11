<?php

if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$delpost = ( isset ($PVARS['delpost']) ) ? $PVARS['delpost'] : array();

delete_posts ($userdata, $delpost);

$title = $lang['ind123'];
echo make_redirect ($lang['ind124'], '?id=editposts', $lang['ind124a']);


?>