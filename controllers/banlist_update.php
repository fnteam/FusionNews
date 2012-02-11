<?php

/*id Banned Save*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$bannedlist = ( isset ($pVARS['bannedlist']) ) ? $pVARS['bannedlist'] : '';
$ips = explode ("\n", $bannedlist);

banlist_update ($ips);

$title = $lang['ind137'];
echo make_redirect ($lang['ind162'], '?id=comments_manage', $lang['ind334']);


?>