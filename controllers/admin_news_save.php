<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}
if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$bbca = ( isset($PVARS['bbc']) ) ? 1 : 0;
$post_per_day = ( isset($PVARS['post_per_day']) ) ? 1 : 0;
$ppp_date = ( isset($PVARS['ppp_date']) ) ? $PVARS['ppp_date'] : $config['ppp_date'];
$hm = ( isset($PVARS['html']) ) ? 1 : 0;
$sm = ( isset($PVARS['sm']) ) ? 1 : 0;
$htcom = ( isset($PVARS['htmc']) ) ? 1 : 0;
$smiliescom = ( isset($PVARS['smil']) ) ? 1 : 0;
$bbcom = ( isset($PVARS['bbccom']) ) ? 1 : 0;
$head_full_link = ( isset($PVARS['head_full_link']) ) ? 1 : 0;
$datefor = ( isset ($PVARS['df']) ) ? $PVARS['df'] : $config['datefor'];
$numofposts = ( isset ($PVARS['posts']) ) ? intval ($PVARS['posts']) : $config['numofposts'];
$numofh = ( isset ($PVARS['h']) ) ? intval ($PVARS['h']) : $config['numofh'];
$cb_flip = (int)(isset ($PVARS['cb_flip']));
$news_pagination = (int)(isset ($PVARS['news_pagination']));
$news_pagination_numbers = (int)isset ($PVARS['news_pagination_numbers']);
$news_pagination_arrows = (int)isset ($PVARS['news_pagination_arrows']);
$news_pagination_prv = ( isset ($PVARS['news_pagination_prv']) ) ? $PVARS['news_pagination_prv'] : $config['news_pagination_prv'];
$news_pagination_nxt = ( isset ($PVARS['news_pagination_nxt']) ) ? $PVARS['news_pagination_nxt'] : $config['news_pagination_nxt'];

$config['datefor'] = $datefor;
$config['numofposts'] = $numofposts;
$config['numofh'] = $numofh;
$config['bb'] = $bbca;
$config['ht'] = $hm;
$config['post_per_day'] = $post_per_day;
$config['ppp_date'] = $ppp_date;
$config['smilies'] = $sm;
$config['htc'] = $htcom;
$config['smilcom'] = $smiliescom;
$config['bbc'] = $bbcom;
$config['link_headline_fullstory'] = $head_full_link;
$config['flip_news'] = $cb_flip;
$config['news_pagination'] = $news_pagination;
$config['news_pagination_numbers'] = $news_pagination_numbers;
$config['news_pagination_arrows'] = $news_pagination_arrows;
$config['news_pagination_nxt'] = $news_pagination_nxt;
$config['news_pagination_prv'] = $news_pagination_prv;

save_config( $config );

$title = $lang['ind21'];
echo make_redirect ($lang['ind22']);


?>