<?php

/*id Post*/
if( !has_access( NEWS_REPORTER ))
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
$fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
$subject = ( isset ($PVARS['post_subject']) ) ? fn_trim (single_line ($PVARS['post_subject'])) : '';
$description = ( isset ($PVARS['description']) ) ? fn_trim (single_line ($PVARS['description'])) : '';
$category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();

if ( !$subject || !$news )
{
    trigger_error ($lang['ind98'], E_USER_WARNING);
}

if ( sizeof ($category) < 1 )
{
    trigger_error ($lang['ind309'], E_USER_WARNING);
}

$cat_error = check_category_access ($userdata['user'], $category);
if ( $cat_error )
{
    trigger_error (sprintf ($lang['ind310'], $cat_error), E_USER_WARNING);
}

create_post (array (
    'shortnews' => $news,
    'fullnews' => $fullnews,
    'author' => $userdata['user'],
    'headline' => $subject,
    'description' => $description,
    'categories' => $category,
    'timestamp' => time() + round (3600 * $userdata['offset'])
));

$title = $lang['ind43'];
echo make_redirect ($lang['ind100']);


?>