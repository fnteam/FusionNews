<?php


if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$edit1 = ( isset ($PVARS["edit1"]) ) ? $PVARS['edit1'] : '';
$edit2 = ( isset ($PVARS["edit2"]) ) ? $PVARS['edit2'] : '';
$edited = ( isset ($PVARS['edited']) ) ? (int)$PVARS['edited'] : 0;

if ( !$edited || $edited == 0 || $edited > 7 )
{
    trigger_error ($lang['error10'], E_USER_WARNING);
}

$templates1 = array ('header', 'com_header', 'news_temp', 'arch_news_temp', 'com_temp', 'headline_temp', 'news_a_day_temp');
$templates2 = array ('footer', 'com_footer', 'fullnews_temp', '', 'com_fulltemp', 'sendtofriend_temp', '');
$name1 = $templates1[$edited - 1];
$name2 = $templates2[$edited - 1];

save_template ($name1, $edit1);

if ( $name2 != '' )
{
    save_template ($name2, $edit2);
}

$title = $lang['ind21'];
echo make_redirect ($lang['ind22'], '?id=admin_template', $lang['ind337']);


?>