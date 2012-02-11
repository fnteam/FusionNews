<?php


if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind65'];
?>

<form action="?id=admin_addons_save" method="post">
<fieldset>
    <legend>Full Story Link Settings</legend>
    <?php
    
    show_textbox ($lang['ind66'], 'flink', $config['fslink']);
    show_checkbox ($lang['ind68'], 'fspu', $config['fsnw']);
    show_textbox ($lang['ind69'], 'fspuw', $config['fullnewsw'], '', 4);
    show_textbox ($lang['ind70'], 'fspuh', $config['fullnewsh'], '', 4);
    show_checkbox ($lang['ind72'], 'fpuscrolling', $config['fullnewss']);
    show_checkbox ($lang['ind73'], 'fpuresize', $config['fullnewsz']);
    
    ?>
</fieldset>

<fieldset>
    <legend>Send to Friend Link Settings</legend>
    <?php
    
    show_textbox ($lang['ind74'], 'slink', $config['stflink']);
    show_checkbox ($lang['ind68'], 'stfpu', $config['stfpop']);
    show_textbox ($lang['ind69'], 'spuw', $config['stfwidth'], '', 4);
    show_textbox ($lang['ind70'], 'spuh', $config['stfheight'], '', 4);
    show_checkbox ($lang['ind72'], 'stfscrolls', $config['stfscrolls']);
    show_checkbox ($lang['ind73'], 'stfresize', $config['stfresize']);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind89']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind99'], 'stf_captcha', $config['stf_captcha']);
    
    ?>
</fieldset>

<fieldset>
    <legend>Comment Link Settings</legend>
    <?php
    
    show_textbox ($lang['ind77'], 'plink', $config['pclink']);
    show_checkbox ($lang['ind68'], 'compu', $config['compop']);
    show_textbox ($lang['ind69'], 'compuw', $config['comwidth'], '', 4);
    show_textbox ($lang['ind70'], 'compuh', $config['comheight'], '', 4);
    show_checkbox ($lang['ind72'], 'comscrolls', $config['comscrolls']);
    show_checkbox ($lang['ind73'], 'comresize', $config['comresize']);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind78']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind238'], 'com_validation', $config['com_validation']);
    show_checkbox ($lang['ind322'], 'com_captcha', $config['com_captcha']);
    show_checkbox ($lang['ind79'], 'cpbr', $config['comallowbr']);
    show_checkbox ($lang['ind91'], 'cbf', $config['cbflood']);
    show_textbox ($lang['ind92'], 'flood', $config['floodtime'], '', 3);
    show_textbox ($lang['ind91a'], 'comlength', $config['comlength'], '', 3);
    show_checkbox ($lang['ind355'], 'comments_pages', $config['comments_pages']);
    show_textbox ($lang['ind356'], 'comments_per_page', $config['comments_per_page'], '', 2);
    
    ?>
</fieldset>

<fieldset>
    <legend>Word Filter Settings</legend>
    <?php

    show_checkbox ($lang['ind81'], 'wfcomcbx', $config['wfcom']);
    show_checkbox ($lang['ind82'], 'wfpostcbx', $config['wfpost']);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind265']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind266'], 'cb_rss', $config['enable_rss']);
    show_textbox ($lang['ind305'], 'rss_title', $config['rss_title'], '', 60);
    show_textbox ($lang['ind306'], 'rss_description', $config['rss_description'], '', 60);
    show_textbox ($lang['ind307'], 'rss_encoding', $config['rss_encoding'], '', 15);
    
    ?>
</fieldset>

<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind36']; ?>" />
    <input type="reset" value="<?php echo $lang['ind16']; ?>" />
</fieldset>

<?php echo get_form_security(); ?>

</form>