<?php

if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$num = ( isset ($GVARS['num']) ) ? (int)$GVARS['num'] : 0;
if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php') )
{
    trigger_error ($lang['error4'], E_USER_WARNING);
}

$article = get_post ($num);

if ( !has_access (NEWS_EDITOR) && $article['author'] != $userdata['user'] )
{
    // This is a news reporter, trying to edit an article which
    // he/she didn't post.
    trigger_error ($lang['error14'], E_USER_WARNING);
}

if ( ($category_name = check_category_access ($userdata['user'], $article['categories'])) !== NULL )
{
    trigger_error (sprintf ($lang['ind185'], $category_name), E_USER_WARNING);
}

$title = $lang['ind95'];

$shortnews = $article['shortnews'];
$fullnews = $article['fullnews'];
$subject = $article['headline'];
$description = $article['description'];
$timestamp = $article['timestamp'];
$categories = $article['categories'];

$writer = get_author ($article['author']);
$writer = $writer === false ? $article['author'] : $writer['nick'];

$action = ( isset ($GVARS['action']) ) ? $GVARS['action'] : '';

if ( $config['use_wysiwyg'] )
{
    echo '<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>';
}

if ( $action == 'preview' )
{
    $shortnews = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
    $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
    $description = ( isset ($PVARS['description']) ) ? fn_trim ($PVARS['description']) : '';
    $subject = ( isset ($PVARS['subject']) ) ? fn_trim ($PVARS['subject']) : '';
    $categories = ( isset ($PVARS['category']) ) ? $PVARS['category'] : $categories;

    $day = ( isset ($PVARS['edit_day']) ) ? (int)$PVARS['edit_day'] : 0;
    $month = ( isset ($PVARS['edit_month']) ) ? (int)$PVARS['edit_month'] : 0;
    $year = ( isset ($PVARS['edit_year']) ) ? (int)$PVARS['edit_year'] : 0;
    $sec = ( isset ($PVARS['edit_sec']) ) ? (int)$PVARS['edit_sec'] : 0;
    $min = ( isset ($PVARS['edit_min']) ) ? (int)$PVARS['edit_min'] : 0;
    $hour = ( isset ($PVARS['edit_hour']) ) ? (int)$PVARS['edit_hour'] : 0;

    $timestamp = mktime ($hour, $min, $sec, $month, $day, $year);

    $preview_shortnews = format_message ($shortnews, $config['ht'] || $config['use_wysiwyg'], $config['bb'], $config['smilies'], $config['wfpost']);
    $preview_fullnews = format_message ($fullnews, $config['ht'] || $config['use_wysiwyg'], $config['bb'], $config['smilies'], $config['wfpost']);
    
    if ( !$ht && !$config['use_wysiwyg'] )
    {
        $preview_shortnews = str_replace ("\n", '<br />', $preview_shortnews);
        $preview_fullnews = str_replace ("\n", '<br />', $preview_fullnews);
    }
    else
    {
        // Need to be a bit smarter about new lines.
        $preview_shortnews = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $preview_shortnews);
        $preview_fullnews = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $preview_fullnews);
    }

    echo <<< html
<h2>{$lang['ind200']}</h2>
<table class="adminpanel">
	<tr>
		<th>{$lang['ind128']}</th>
	</tr>
	<tr>
		<td>$preview_shortnews</td>
	</tr>
html;

    if ( $preview_fullnews != '' )
    {
        echo <<< html
	<tr>
		<th>{$lang['ind94']}</th>
	</tr>
	<tr>
		<td>$preview_fullnews</td>
	</tr>
html;
    }

    echo <<< html
</table>
<p></p>

html;
}
else
{
    $shortnews = str_replace ('<br />', "\n", $shortnews);
    $fullnews = str_replace ('<br />', "\n", $fullnews);
    $shortnews = str_replace ('&br;', "\n", $shortnews);
    $fullnews = str_replace ('&br;', "\n", $fullnews);
}

$category_list = build_category_selection ($userdata['user'], $categories);

$off = '<span style="color:red"><b>' . $lang['ind144'] . '</b></span>';
$on = '<span style="color:green"><b>' . $lang['ind143'] . '</b></span>';
$htmlcheck = ( !$config['ht'] && (!$config['use_wysiwyg']) ) ? $off : $on;
$bbcheck = ( !$config['bb'] ) ? $off : $on;
$smilcheck = ( !$config['smilies'] ) ? $off : $on;

echo <<<html
<form action="?id=savepost" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>{$lang['ind119']}</td>
		<td>$writer</td>
		<td rowspan="6">
			{$lang['ind121']}<br />
			- HTML {$lang['ind122']} $htmlcheck<br />
			- BBCode {$lang['ind122']} $bbcheck<br />
			- Smilies {$lang['ind122']} $smilcheck
		</td>
	</tr>
html;
$day_dropdown = make_dropdown ('edit_day', 'edit_day', date ('j', $timestamp), range (1, 31), null);

$month_names = array_combine (range (0, 11), $months);
$month_dropdown = make_dropdown ('edit_month', 'edit_month', date ('m', $timestamp), range (1, 12), $month_names);

$year_dropdown = make_dropdown ('edit_year', 'edit_year', date ('Y', $timestamp), range (1990, 2037), null);

$numbers = array_map (create_function ('$value', 'return sprintf ("%02d", $value);'), range (0, 59));
$hour_dropdown = make_dropdown ('edit_hour', 'edit_hour', date ('G', $timestamp), range (0, 23), $numbers);
$min_dropdown = make_dropdown ('edit_min', 'edit_min', date ('i', $timestamp), range (0, 59), $numbers);
$sec_dropdown = make_dropdown ('edit_sec', 'edit_sec', date ('s', $timestamp), range (0, 59), $numbers);

echo <<<html
	<tr>
		<td><label for="subject">{$lang['ind35']}</label></td>
		<td><input type="text" class="post" id="subject" name="subject" value="$subject" style="width:95%" /></td>
	</tr>
	<tr>
		<td><label for="description">{$lang['ind258']}</label></td>
		<td><input type="text" class="post" id="description" name="description" value="$description" style="width:95%" /></td>
	</tr>
	<tr>
		<td>{$lang['ind87']}</td>
		<td>$month_dropdown$day_dropdown$year_dropdown</td>
	</tr>
	<tr>
		<td>{$lang['ind291']}</td>
		<td>$hour_dropdown:$min_dropdown:$sec_dropdown</td>
	</tr>
	<tr>
		<td valign="top">{$lang['ind308']}</td>
		<td>
			<div class="category-selection">$category_list</div>
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
$extras = ( $config['use_wysiwyg'] ) ? '' : show_extras ('newsposting', 'news', $config['smilies'], $config['bb']);
echo <<<html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="news" name="news" rows="18" cols="75" style="width:95%">$shortnews</textarea>
		</td>
	</tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th>{$lang['ind94']}</th>
	</tr>
html;
$extras = ( $config['use_wysiwyg'] ) ? '' : show_extras ('newsposting', 'fullnews', $config['smilies'], $config['bb']);
echo <<<html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="fullnews" name="fullnews" rows="18" cols="75" style="width:95%">$fullnews</textarea>
		</td>
	</tr>
</table>
<p>
<input type="checkbox" id="del" class="del" value="1" name="del" /> <label for="del">{$lang['ind97']}</label><br /><br />
<input type="hidden" id="num" name="num" value="$num" />
<input type="hidden" id="date" name="date" value="$timestamp" />
<input type="submit" class="mainoption" value="{$lang['ind174']}" />
<input type="submit" class="mainoption" value="{$lang['ind200']}" onclick="PreviewArticle ('$id', 'newsposting', $num)" />
<input type="reset" value="{$lang['ind16']}" />
</p>
html;

echo get_form_security() . '</form>';

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