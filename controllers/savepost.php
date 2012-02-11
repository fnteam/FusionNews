<?php


if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$num = ( isset ($PVARS['num']) ) ? intval ($PVARS['num']) : 0;
$delete = (int)( isset ($PVARS['del']) );
if ( $delete )
{
    delete_posts ($userdata, array ($num));
    
    $title = $lang['ind417'];
    echo make_redirect ($lang['ind418'], '?id=editposts', $lang['ind124a']);
}
else
{
    $news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
    $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
    $subject = ( isset ($PVARS['subject']) ) ? fn_trim (single_line ($PVARS['subject'])) : '';
    $description = ( isset ($PVARS['description']) ) ? fn_trim (single_line ($PVARS['description'])) : '';
    $day = ( isset ($PVARS['edit_day']) ) ? intval ($PVARS['edit_day']) : 1;
    $month = ( isset ($PVARS['edit_month']) ) ? intval ($PVARS['edit_month']) : 1;
    $year = ( isset ($PVARS['edit_year']) ) ? intval ($PVARS['edit_year']) : 1970;
    $hour = ( isset ($PVARS['edit_hour']) ) ? intval ($PVARS['edit_hour']) : 0;
    $min = ( isset ($PVARS['edit_min']) ) ? intval ($PVARS['edit_min']) : 0;
    $sec = ( isset ($PVARS['edit_sec']) ) ? intval ($PVARS['edit_sec']) : 0;
    $category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();

    if ( !$subject || !$news )
    {
        trigger_error ($lang['ind98'], E_USER_WARNING);
    }
    
    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php') )
    {
        trigger_error ($lang['com11'], E_USER_WARNING);
    }
    
    if ( sizeof ($category) < 1 )
    {
        trigger_error ($lang['ind309'], E_USER_WARNING);
    }
    
    if ( $cat_error = check_category_access ($userdata['user'], $category) )
    {
        trigger_error (sprintf ($lang['ind310'], $cat_error), E_USER_WARNING);
    }
    
    $date = mktime ($hour, $min, $sec, $month, $day, $year);
    if ( $date == -1 || $date === false )
    {
        trigger_error ('Invalid date entered', E_USER_WARNING);
    }
    
    $postdata = get_post ($num);
    if ( $postdata['author'] != $userdata['user'] && !has_access (NEWS_EDITOR) )
    {
        trigger_error ($lang['ind103'], E_USER_WARNING);
    }
    
    $date = mktime ($hour, $min, $sec, $month, $day, $year);
    if ( $date == -1 || $date === false )
    {
        $date = $postdata['timestamp'];
    }
    
    $postdata['shortnews'] = $news;
    $postdata['fullnews'] = $fullnews;
    $postdata['headline'] = $subject;
    $postdata['description'] = $description;
    $postdata['categories'] = $category;
    $postdata['timestamp'] = $date;
    update_post ($num, $postdata);
    
    $title = $lang['ind101'];
    echo make_redirect ($lang['ind102'], '?id=editposts', $lang['ind124a']);
}


?>