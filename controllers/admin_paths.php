<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind45'];

?>
<form action="?id=admin_paths_save" method="post">
<p><?php echo $lang['ind46']; ?></p>
<fieldset>
    <legend><?php echo $lang['ind45']; ?></legend>
    <?php
    
    show_textbox ($lang['ind47'], 'site1', $config['site'], $lang['ind48'], 40);
    show_textbox ($lang['ind49'], 'furl1', $config['furl'], $lang['ind50'], 40);
    show_textbox ($lang['ind53'], 'url', $config['hurl'], $lang['ind54'], 40);
    
    ?>
</fieldset>

<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind36']; ?>" />
    <input type="reset" value="<?php echo $lang['ind16']; ?>" />
</fieldset>

<?php echo get_form_security(); ?>

</form>