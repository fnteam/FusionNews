<?php

/*id Comment Delete*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$delpost = ( isset ($PVARS['delpost']) ) ? $PVARS['delpost'] : array();
$news_id = ( isset ($PVARS['rand']) ) ? (int)$PVARS['rand'] : 0;

if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php') )
{
    trigger_error ($lang['com11'], E_USER_WARNING);
}

$num_removed = delete_comments ($delpost, $news_id);

$title = $lang['ind150'];
echo make_redirect ($lang['ind151'] . ' ' . $num_removed . ' ' . $lang['ind151a'], '?id=comments_manage', $lang['ind334']);

?>