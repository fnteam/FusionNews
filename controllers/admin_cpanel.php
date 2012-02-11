<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$languages = get_languages();
$skins = get_all_skins();

$title = $lang['ind216'];
?>
<form method="post" action="?id=admin_cpanel_save">
<fieldset>
    <legend><?php echo $lang['ind216']; ?></legend>
    <?php
    
    show_dropdown ($lang['ind196'], 'language', $config['language'], array_keys ($languages), array_values ($languages));
    show_dropdown ($lang['ind186'], 'skin', $config['skin'], $skins);
    
    ?>
</fieldset>
<fieldset>
    <legend>News Editor Settings</legend>
    <?php
    
    show_checkbox ($lang['ind88'], 'use_wysiwyg', $config['use_wysiwyg']);
    
    ?>
</fieldset>

<?php echo get_form_security(); ?>

<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind36']; ?>" />
    <input type="reset" value="<?php echo $lang['ind16']; ?>" />
</fieldset>
</form>