<?php


if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$date = date('Y-m-d H:i:s T');

$icon_image = $userdata['icon'] ? '<img src="' . $userdata['icon'] . '" alt="" />': '';

$title = $lang['ind284'];
?>
<form action="?id=updateprofile" method="post">
<fieldset>
    <?php
    
    show_textbox ($lang['ind119'], 'nick1', $userdata['nick']);
    show_textbox ($lang['ind6'], 'mail1', $userdata['email']);
    show_checkbox ($lang['ind183'], 'showemail', $userdata['showemail']);
    show_twocolumn ($lang['ind112'], $date);
    show_textbox ($lang['ind111'], 'timeoffset', $userdata['offset'], $lang['ind219'], 3);
    show_passwordbox ($lang['ind217'], 'oldpassw', $lang['ind225']);
    show_passwordbox ($lang['ind218'], 'passw');
    show_textbox ($lang['ind7'], 'icon1', $userdata['icon']);
    show_twocolumn ('&nbsp;', $icon_image);
    
    ?>
</fieldset>
<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind174']; ?>" />
</fieldset> 

<?php echo get_form_security(); ?>
</form>