<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$language = isset ($VARS['language']) ? $VARS['language'] : $config['language'];
$skin = isset ($VARS['skin']) ? $VARS['skin'] : $config['skin'];
$use_wysiwyg = ( isset ($VARS['use_wysiwyg']) ) ? 1 : 0;

$langs = get_languages();
if ( !isset ($languages[$language]) )
{
    $language = $config['language'];
}

$skins = get_all_skins();
if ( !in_array ($skin, $skins) )
{
    $skin = $config['skin'];
}

$config['skin'] = $skin;
$config['language'] = $language;
$config['use_wysiwyg'] = $use_wysiwyg;

save_config ($config);

$title = $lang['ind21'];
echo make_redirect ($lang['ind22']);

?>