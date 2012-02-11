<?php


if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !check_form_character() )
{
    trigger_error ($lang['ind298'], E_USER_WARNING);
}

$delpost = ( isset ($PVARS['del_files']) ) ? $PVARS['del_files'] : array();

$title = $lang['ind289'];
$results = delete_uploaded_files ($delpost);
$response = '';
foreach ( $results['succeeded'] as $file )
{
    $response .= "'$file' {$lang['ind231']}<br />";
}

foreach ( $results['failed'] as $file )
{
    $response .= "{$lang['ind229']} '$file'<br />";
}

echo make_redirect ($response, '?id=uploads', $lang['ind338']);


?>