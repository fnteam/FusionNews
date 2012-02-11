<?php

/**
 * New post page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: postnews.php 393 2012-02-10 22:37:14Z xycaleth $
 *
 * This file is part of Fusion News.
 *
 * Fusion News is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Fusion News is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Fusion News.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind34b'];

$action = ( isset ($PVARS['action']) ) ? $PVARS['action'] : '';
$news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
$categories = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array(1);
$fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
$subject = ( isset ($PVARS['post_subject']) ) ? fn_trim ($PVARS['post_subject']) : '';
$description = ( isset ($PVARS['description']) ) ? fn_trim ($PVARS['description']) : '';

if ( $config['use_wysiwyg'] )
{
    echo '<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>';
}

if ( $action == 'preview' )
{
    $news = format_message ($news, $config['ht'] || $config['use_wysiwyg'], $config['bb'], $config['smilies'], $config['wfpost']);
    $fullnews = format_message ($fullnews, $config['ht'] || $config['use_wysiwyg'], $config['bb'], $config['smilies'], $config['wfpost']);
    
    if ( !$ht && !$config['use_wysiwyg'] )
    {
        $news1 = str_replace ("\n", '<br />', $news1);
        $fullnews1 = str_replace ("\n", '<br />', $fullnews1);
    }
    else
    {
        // Need to be a bit smarter about new lines.
        $news1 = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $news1);
        $fullnews1 = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $fullnews1);
    }

    echo <<< html
<h2>{$lang['ind200']}</h2>
<table class="adminpanel">
	<tr>
		<th>{$lang['ind128']}</th>
	</tr>
	<tr>
		<td>$news</td>
	</tr>
html;

    if ( !empty ($fullnews) )
    {
        echo <<< html
	<tr>
		<th>{$lang['ind94']}</th>
	</tr>
	<tr>
		<td>$fullnews</td>
	</tr>
html;
    }

    echo <<< html
</table>
<p></p>

html;
}

$category_list = build_category_selection ($userdata['user'], $categories);

$off = '<span style="color:red"><b>' . $lang['ind144'] . '</b></span>';
$on = '<span style="color:green"><b>' . $lang['ind143'] . '</b></span>';

$htmlcheck = ( !$config['ht'] && !$config['use_wysiwyg'] ) ? $off : $on;
$bbcheck = ( !$config['bb'] ) ? $off : $on;
$smilcheck = ( !$config['smilies'] ) ? $off : $on;

$security_fields = get_form_security();

echo <<< html
<form action="?id=post" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>
            $security_fields
            {$lang['ind119']}
        </td>
		<td>{$userdata['nick']}</td>
		<td rowspan="4">
			{$lang['ind121']}<br />
			- HTML {$lang['ind122']} $htmlcheck<br />
			- BBCode {$lang['ind122']} $bbcheck<br />
			- Smilies {$lang['ind122']} $smilcheck
		</td>
	</tr>
	<tr>
		<td><label for="post_subject">{$lang['ind35']}</label></td>
		<td><input type="text" class="post" id="post_subject" name="post_subject" value="$subject" style="width:95%" /></td>
	</tr>
	<tr>
		<td><label for="description">{$lang['ind258']}</label></td>
		<td><input type="text" class="post" id="description" name="description" value="$description" style="width:95%" /></td>
	</tr>
	<tr>
		<td valign="top">{$lang['ind308']}</td>
		<td valign="middle">
            <div class="category-selection">
                $category_list
            </div>
		</td>
	</tr>
</table>
html;

if ( $config['uploads_active'] )
{
    echo <<<html
<p>
<a href="javascript:window_pop ('./upload.php', 'fusion_upload', 575, 505)">{$lang['ind270']}</a>&nbsp;&nbsp;&nbsp;
<a href="javascript:window_pop ('./upload.php?id=imagelist', 'fusion_view', 650, 500)">{$lang['ind271']}</a>
</p>

html;
}

echo <<<html
<table class="adminpanel">
	<tr>
		<th>{$lang['ind93']}</th>
	</tr>

html;

$extras = ( !$config['use_wysiwyg'] ) ? show_extras ('newsposting', 'news', $config['smilies'], $config['bb']) : '';

$news = str_replace ("&br;", "\n", $news);
echo <<< html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="news" name="news" rows="18" cols="75" style="width:95%">$news</textarea>
		</td>
	</tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th>{$lang['ind94']}</th>
	</tr>
html;
$extras = ( !$config['use_wysiwyg'] ) ? show_extras ('newsposting', 'fullnews', $config['smilies'], $config['bb']) : '';

$fullnews = str_replace("&br;", "\n", $fullnews);
echo <<< html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="fullnews" name="fullnews" rows="18" cols="75" style="width:95%">$fullnews</textarea>
		</td>
	</tr>
</table>
<p>
<input type="submit" id="com_Submit" name="com_Submit" class="mainoption" value="{$lang['ind15']}" />
<input type="submit" class="mainoption" value="{$lang['ind200']}" onclick="PreviewArticle ('$id', 'newsposting', -1)" />
<input type="reset" value="{$lang['ind16']}" />
</p>
</form>
html;

if ( $config['use_wysiwyg'] )
{
    $smiley_list = get_smiley_list();
    $smileys = '';
    $separator = '';
    foreach ( $smiley_list as $smiley )
    {
        $smileys .= $separator . "'{$smiley['image']}'";
        $separator = ', ';
    }
    echo <<< html
<script type="text/javascript">
//<![CDATA[
(function()
{
    var settings = {
        customConfig: '',
        language: 'en',
        toolbar: [
            [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'TextColor', '-', 'Font', 'FontSize', 'Smiley', /*'Teletype', */, /*'Quote', */ ],
            '/',
            [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'HorizontalRule', 'BulletedList', 'NumberedList', '-', 'Flash', 'Image', 'Link', 'Unlink', '-', 'Source' ]
        ],
        smiley_path: '{$config['furl']}/smillies/',
        smiley_images: [ {$smileys} ]
    };
    CKEDITOR.replace ('news', settings);
    CKEDITOR.replace ('fullnews', settings);
})();
//]]>
</script>
html;
}

?>