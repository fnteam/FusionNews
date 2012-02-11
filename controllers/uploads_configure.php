<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

if ( !file_uploads_enabled() )
{
    if ( $config['uploads_active'] )
    {
        $config['uploads_active'] = 0;
        save_config ($config);
    }
    
    echo $lang['ind105'];
}
else
{
    $title = $lang['ind106'];
?>
<form method="post" action="?id=saveuploads">
<?php echo get_form_security(); ?>
<fieldset>
    <legend><?php echo $lang['ind106']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind220'], 'uploads_active', $config['uploads_active']);
    show_textbox ($lang['ind221'], 'uploads_ext', $config['uploads_ext'], $lang['ind222'], 40);
    show_textbox ($lang['ind223'], 'uploads_size', $config['uploads_size'], $lang['ind224'], 25);
    
    ?>
</fieldset>

<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind36']; ?>" />
    <input type="reset" value="<?php echo $lang['ind16']; ?>" />
</fieldset>
</form>
<?php
}

?>