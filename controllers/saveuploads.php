<?php

/*id Uploads Save Config*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$uploads_size = ( isset ($PVARS['uploads_size']) ) ? (int)$PVARS['uploads_size'] : 0;
$uploads_active = (int)(isset($PVARS["uploads_active"]));
$uploads_extensions = ( isset ($PVARS['uploads_ext']) ) ? single_line ($PVARS['uploads_ext']) : '';

if ( !preg_match ('/^[A-Za-z0-9_\|]+$/', $uploads_extensions) )
{
    trigger_error ($lang['ind295'], E_USER_WARNING);
}

$exts = explode ('|', $uploads_extensions);
$uploads_extensions = implode ('|', array_unique ($exts));

$config['uploads_size'] = $uploads_size;
$config['uploads_active'] = $uploads_active;
$config['uploads_ext'] = $uploads_extensions;

save_config ($config);

$title = $lang['ind21'];
echo make_redirect ($lang['ind22'], '?id=uploads', $lang['ind338']);


?>