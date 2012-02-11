<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$show = ( isset ($GVARS['show']) ) ? (int)$GVARS["show"] : 0;
$edit1 = $edit2 = "";

if ( $show == 0 || !$show || $show > 7 )
{
    trigger_error ($lang['error10'], E_USER_WARNING);
}

$title = $lang['ind23'];
echo "<form action= \"?id=admin_template_save\" method=\"post\" id=\"tmpl_form\">";

$name1 = '';
$name2 = '';
switch ( $show )
{
    case 1:
        $name1 = 'header.php';
        $name2 = 'footer.php';
        echo $lang['ind187'];
    break;
    case 2:
        $name1 = 'com_header.php';
        $name2 = 'com_footer.php';
        echo $lang['ind187'];
    break;
    case 3:
        $name1 = 'news_temp.php';
        $name2 = 'fullnews_temp.php';
        echo $lang['ind189'] . $lang['ind24'];
    break;
    case 4:
        $name1 = 'arch_news_temp.php';
        echo $lang['ind179b'] . $lang['ind24'];
    break;
    case 5:
        $name1 = 'com_temp.php';
        $name2 = 'com_fulltemp.php';
        echo $lang['ind191'] . $lang['ind26'];
    break;
    case 6:
        $name1 = 'headline_temp.php';
        $name2 = 'sendtofriend_temp.php';
        echo $lang['ind192'] . $lang['ind30'];
    break;
    case 7:
        $name1 = 'news_a_day_temp.php';
        echo $lang['ind192a'] . $lang['ind30a'];
    break;
    default:
    break;
}

$edit1 = get_template ($name1);
$edit1 = htmlspecialchars ($edit1);

if( $name2 != "" ){
    $edit2 = get_template ($name2);
    $edit2 = htmlspecialchars ($edit2);
}

echo <<<html
<div style="text-align:center">
<textarea name="edit1" id="edit1" class="post" cols="75" rows="20" style="width:95%;">$edit1</textarea><br />
<input type="button" onclick="document.getElementById('edit1').rows += 5" value="+" />
<input type="button" onclick="document.getElementById('edit1').rows -= 5" value="-" />
<input id="edited" name="edited" type="hidden" value="$show" />
</div>
html;
if ( $name2 )
{
    switch ( $show )
    {
        case 1:
            // fall-through
        case 2:
            echo $lang['ind188'];
        break;
        case 3:
            echo $lang['ind25'];
        break;
        case 5:
            echo $lang['ind37'];
        break;
        case 6:
            echo $lang['ind38'];
        break;
        default:
        break;
    }
    
    echo <<<html
<div style="text-align:center">
<textarea name="edit2" id="edit2" class="post" cols="75" rows="20" style="width:95%;">$edit2</textarea><br />
<input type="button" onclick="document.getElementById('edit2').rows += 5" value="+" />
<input type="button" onclick="document.getElementById('edit2').rows -= 5" value="-" />
</div>
<p>
<input type="submit" class="mainoption" value="{$lang['ind36']}" />
</p>
html;
}
else
{
    echo <<<html
<p>
<input id="edit2" name="edit2" type="hidden" value="" />
<input type="submit" class="mainoption" value="{$lang['ind36']}" />
</p>
html;
}

echo get_form_security() . '</form>';

?>