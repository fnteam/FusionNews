<?php


if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$fspu = isset ($PVARS['fspu']) ? 1: 0;
$compu = isset ($PVARS['compu']) ? 1: 0;
$combr = isset ($PVARS['cpbr']) ? 1: 0;
$fcpu = isset ($PVARS['cpu']) ? 1: 0;
$fstfpu = isset ($PVARS['stfpu']) ? 1: 0;
$fspuz = isset ($PVARS['fpuresize']) ? 1: 0;
$stfs = isset ($PVARS['stfscrolls']) ? 1: 0;
$stfz = isset ($PVARS['stfresize']) ? 1: 0;
$comscrolls = isset ($PVARS['comscrolls']) ? 1: 0;
$comresize = isset ($PVARS['comresize']) ? 1: 0;
$wfpost1 = isset ($PVARS['wfpostcbx']) ? 1: 0;
$wfcom1 = isset ($PVARS['wfcomcbx']) ? 1: 0;
$cbflood = isset ($PVARS['cbf']) ? 1: 0;
$cb_rss = isset ($PVARS['cb_rss']) ? 1: 0;
$fspus = isset ($PVARS['fpuscrolling']) ? 1: 0;

$fspuw = isset ($PVARS['fspuw']) ? (int)$PVARS['fspuw']: 0;
$fspuh = isset ($PVARS['fspuh']) ? (int)$PVARS['fspuh']: 0;
$compuw = isset ($PVARS['compuw']) ? (int)$PVARS['compuw']: 0;
$compuh = isset ($PVARS['compuh']) ? (int)$PVARS['compuh']: 0;
$spuw = isset ($PVARS['spuw']) ? (int)$PVARS['spuw']: 0;
$spuh = isset ($PVARS['spuh']) ? (int)$PVARS['spuh']: 0;
$stf_captcha = (int)isset ($PVARS['stf_captcha']);
$flood = isset ($PVARS['flood']) ? (int)$PVARS['flood']: 0;
$comlength = isset ($PVARS['comlength']) ? (int)$PVARS['comlength']: 0;

$com_validation = ( isset ($PVARS['com_validation']) ) ? 1 : 0;
$com_captcha = ( isset ($PVARS['com_captcha']) ) ? 1 : 0;
$comments_pages = (int)( isset ($PVARS['comments_pages']) );
$comments_per_page = ( isset ($PVARS['comments_per_page']) ) ? (int)$PVARS['comments_per_page'] : 0;

$fslink = ( isset ($PVARS["flink"]) ) ? $PVARS['flink'] : $config['fslink'];
$stflink = ( isset ($PVARS["slink"]) ) ? $PVARS['slink'] : $config['stflink'];
$pclink = ( isset ($PVARS["plink"]) ) ? $PVARS['plink'] : $config['pclink'];

$cfg_rss_title = ( isset ($PVARS['rss_title']) ) ? $PVARS['rss_title'] : $config['rss_title'];
$cfg_rss_description = ( isset ($PVARS['rss_description']) ) ? $PVARS['rss_description'] : $config['rss_description'];
$cfg_rss_encoding = ( isset ($PVARS['rss_encoding']) && !empty ($PVARS['rss_encoding']) ) ? $PVARS['rss_encoding'] : $config['rss_encoding'];

$config['wfpost']		= $wfpost1;
$config['wfcom']		= $wfcom1;
$config['stfpop']		= $fstfpu;
$config['comallowbr']		= $combr;
$config['stfwidth']		= $spuw;
$config['stfheight']		= $spuh;
$config['fslink']		= $fslink;
$config['stflink']		= $stflink;
$config['pclink']		= $pclink;
$config['fsnw']		= $fspu;
$config['cbflood']		= $cbflood;
$config['floodtime']		= $flood;
$config['comlength']		= $comlength;
$config['fullnewsw']		= $fspuw;
$config['fullnewsh']		= $fspuh;
$config['fullnewss']		= $fspus;
$config['stfresize']		= $stfz;
$config['stfscrolls']		= $stfs;
$config['fullnewsz']		= $fspuz;
$config['compop']		= $compu;
$config['comscrolls']		= $comscrolls;
$config['comresize']		= $comresize;
$config['comheight']		= $compuh;
$config['comwidth']	 	= $compuw;
$config['enable_rss']		= $cb_rss;
$config['rss_title']	 	= $cfg_rss_title;
$config['rss_description']	= $cfg_rss_description;
$config['rss_encoding']	= $cfg_rss_encoding;
$config['com_validation']	= $com_validation;
$config['com_captcha']		= $com_captcha;
$config['comments_pages']	= $comments_pages;
$config['comments_per_page']	= $comments_per_page;
$config['stf_captcha']	= $stf_captcha;

save_config ($config);

$title = $lang['ind21'];
echo make_redirect ($lang['ind22']);


?>