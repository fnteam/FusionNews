<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind55'];

?>

<form action="?id=admin_news_save" method="post">
<fieldset>
    <legend><?php echo $lang['ind57']; ?></legend>
    <?php
    
    show_textbox ($lang['ind56'], 'df', $config['datefor']);
    show_textbox ($lang['ind86'], 'ppp_date', $config['ppp_date']);
    show_textbox ($lang['ind58'], 'posts', $config['numofposts'], '', 2);
    show_checkbox ($lang['ind268'], 'cb_flip', $config['flip_news'], $lang['ind269']);
    show_checkbox ($lang['ind260'], 'post_per_day', $config['post_per_day'], $lang['ind261']);
    show_checkbox ($lang['ind62'], 'bbc', $config['bb']);
    show_checkbox ($lang['ind63'], 'html', $config['ht']);
    show_checkbox ($lang['ind64'], 'sm', $config['smilies']);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind59']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind332'], 'news_pagination', $config['news_pagination']);
    show_checkbox ($lang['ind339'], 'news_pagination_numbers', $config['news_pagination_numbers']);
    show_checkbox ($lang['ind340'], 'news_pagination_arrows', $config['news_pagination_arrows']);
    show_textbox ($lang['ind341'], 'news_pagination_prv', $config['news_pagination_prv'], '', 10);
    show_textbox ($lang['ind342'], 'news_pagination_nxt', $config['news_pagination_nxt'], '', 10);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind61']; ?></legend>
    <?php
    
    show_textbox ($lang['ind60'], 'h', $config['numofh'], '', 2);
    show_checkbox ($lang['ind267'], 'head_full_link', $config['link_headline_fullstory'], $lang['ind290']);
    
    ?>
</fieldset>

<fieldset>
    <legend><?php echo $lang['ind268']; ?></legend>
    <?php
    
    show_checkbox ($lang['ind62'], 'bbccom', $config['bbc']);
    show_checkbox ($lang['ind63'], 'htmc', $config['htc']);
    show_checkbox ($lang['ind64'], 'smil', $config['smilcom']);
    
    ?>
</fieldset>

<?php echo get_form_security(); ?>

<fieldset class="buttons">
    <input type="submit" class="mainoption" value="<?php echo $lang['ind36']; ?>" />
    <input type="reset" value="<?php echo $lang['ind16']; ?>" />
</fieldset>
</form>
<script type="text/javascript">
//<![CDATA[

(function()
{
    function toggle_pagination_options()
    {
        var pagination_options = document.getElementById('pagination_options');
        pagination_options.disabled = !document.getElementById('news_pagination').checked;
    }

    document.getElementById('news_pagination').onchange = toggle_pagination_options;
    toggle_pagination_options();
})();

//]]>
</script>