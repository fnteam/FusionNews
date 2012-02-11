<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$date = date ('Y-m-d H:i:s T');
$title = $lang['ind31'];

?>
<form action="?id=user_create" method="post">
<fieldset>
<?php

show_textbox ($lang['ind169a'], 'username');
show_textbox ($lang['ind119'], 'nick');
show_textbox ($lang['ind6'], 'email');
show_checkbox ($lang['ind183'], 'hidemail');
show_twocolumn ($lang['ind112'], $date);
show_textbox ($lang['ind111'], 'timeoffset', '0', '', 3);
show_passwordbox ($lang['ind4'], 'password');
show_textbox ($lang['ind7'], 'icon');
show_dropdown ($lang['ind8'], 'le', 1, array (1, 2, 3), array ($lang['ind193'], $lang['ind194'], $lang['ind195']));
echo get_form_security();

?>
</fieldset>
<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind31']; ?>" />
</fieldset> 
</form>