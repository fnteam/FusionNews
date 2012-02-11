<?php

/*id Comment Update*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$comment_id = ( isset ($PVARS['comment_id']) ) ? $PVARS['comment_id'] : 0;
$news_id = ( isset ($PVARS['news_id']) ) ? (int)$PVARS['news_id'] : 0;
$delete = isset ($PVARS['del']); 

if ( $delete )
{
    delete_comment ($comment_id, $news_id);
    $title = $lang['ind420'];
    echo make_redirect ($lang['ind419'], '?id=comments_manage', $lang['ind334']);
}
else
{
    $comment = ( isset ($PVARS['comment']) ) ? $PVARS['comment'] : '';
    if ( !$comment )
    {
        trigger_error ($lang['ind145'], E_USER_WARNING);
    }

    $updated_comment = get_comment ($comment_id, $news_id);
    $updated_comment['message'] = str_replace ("\n", '&br;', $comment);
    update_comment ($comment_id, $news_id, $updated_comment);
    
    $title = $lang['ind146'];
    echo make_redirect ($lang['ind147'], '?id=comments_manage', $lang['ind334']);
}


?>