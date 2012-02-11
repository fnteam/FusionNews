<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$code = ( isset ($PVARS['code']) ) ? fn_trim (single_line ($PVARS['code'])) : '';
$smiley_image = ( isset ($PVARS['smiley_image']) ) ? fn_trim (single_line ($PVARS['smiley_image'])) : '';

if ( $code == '' || $smiley_image == '' )
{
    trigger_error ($lang['ind172'], E_USER_WARNING);
}

add_smiley ($smiley_image, $code);

$title = $lang['ind248'];
echo make_redirect ($lang['ind249'], '?id=smillies', $lang['ind335']);

?>