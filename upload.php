<?php

/**
 * Image uploads dialog page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: upload.php 393 2012-02-10 22:37:14Z xycaleth $
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
 */

include './common.php';

$id = ( !isset ($GVARS['id']) ) ? '' : $GVARS['id'];
$sid = ( isset ($_COOKIE['fus_sid']) ) ? $_COOKIE['fus_sid'] : '';
$uid = ( isset ($_COOKIE['fus_uid']) ) ? $_COOKIE['fus_uid'] : '';

$userdata = array();
$userdata = login_session_update ($uid, $sid);

if ( !has_access (NEWS_REPORTER) )
{
	echo $lang['ind148'];
	exit;
}

if ( !$config['uploads_active'] )
{
	echo $lang['upld1'];
	exit;
}

if ( strtolower (@ini_get ('file_uploads')) == 'off' || @ini_get ('file_uploads') == 0 || @ini_get ('file_uploads') == '' )
{
	echo
<<< html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>{$lang['upld3']}</title>
	<style type="text/css">
	/* <![CDATA[ */
    
	body { font-family: "Trebuchet MS", Helvetica, sans-serif; font-size: 100% }

	/* ]]> */
	</style>
</head>
<body>{$lang['upld2']}</body>

</html>
html;
	exit;
}

$title = '';
$content = '';
$js = '';

if ( !$id || $id == '' )
{
	$title = $lang['upld3'];
	$maximum_filesize = calc_size ($config['uploads_size']);

	$content =
<<< html
<ul>
	<li>{$lang['upld11']} <b>{$config['furl']}/uploads</b>.</li>
	<li>{$lang['upld12']} {$config['uploads_ext']}.</li>
	<li>{$lang['upld13']} {$maximum_filesize}.</li>
</ul>
<form method="post" enctype="multipart/form-data" action="?id=upload">
<fieldset>
	<legend>{$lang['upld3']}</legend>
	<div>
        <input type="hidden" name="MAX_FILE_SIZE" value="{$config['uploads_size']}" />
    
		<label for="F0">{$lang['upld14']} 1</label><br />
        <input type="file" name="F[]" id="F0" size="50" /><br />
        
		<label for="F1">{$lang['upld14']} 2</label><br />
        <input type="file" name="F[]" id="F1" size="50" /><br />
        
		<label for="F2">{$lang['upld14']} 3</label><br />
        <input type="file" name="F[]" id="F2" size="50" /><br />
        
		<label for="F3">{$lang['upld14']} 4</label><br />
        <input type="file" name="F[]" id="F3" size="50" /><br />
        
		<label for="F4">{$lang['upld14']} 5</label><br />
        <input type="file" name="F[]" id="F4" size="50" /><br /><br />

		<input type="submit" class="mainoption" value="{$lang['ind270']}" />
	</div>
</fieldset>
</form>
html;
}
else if ( $id == 'upload' )
{
	$title = $lang['upld3'];

	$files_uploaded_list = '';
	$freq_count = array_count_values ($_FILES['F']['error']);

	if ( isset ($freq_count[UPLOAD_ERR_NO_FILE]) &&
		$freq_count[UPLOAD_ERR_NO_FILE] >= sizeof ($_FILES['F']['error']) )
	{
		$content = $lang['upld10'];
	}
	else
	{
		foreach ( $_FILES['F']['name'] as $key => $name )
		{
			if ( $_FILES['F']['error'][$key] == UPLOAD_ERR_NO_FILE )
			{
				continue;
			}
            
            if ( !@getimagesize ($_FILES['F']['tmp_name'][$key]) )
            {
                $files_uploaded_list .= "<li>$name is not a valid image file.</li>";
            }
            else
            {
                $files_uploaded_list .= '<li>' . upload_file ($key, $config['uploads_ext']) . '</li>';
            }
		}

		$content = <<< html
<p>{$lang['ind262']}</p>
<ol>
	$files_uploaded_list
</ol>
<p><a href="?">{$lang['upld4']}</a></p>
html;
	}
}
else if ( $id == 'imagelist' )
{
	$title = $lang['upld4'];
	$content = <<< html
<p class="left-column"><a href="?id=imagelist">{$lang['upld5']}</a></p>
<p class="right-column right-align">{$lang['upld15']} <input type="text" name="search" id="search" /><br />
<a href="#" id="clear-search">{$lang['upld16']}</a></p>
<table class="data" id="image-list">
	<tr>
		<th style="width:35%">{$lang['upld6']}</th>
		<th style="width:65%">{$lang['ind226']}</th>
	</tr>
html;

	$dir = @opendir ('./uploads');
	if ( $dir !== false )
	{
		$valid_extensions = str_replace ('&#124;', '|', $config['uploads_ext']);
		$has_images = false;
        $files = array();
		while ( ($file = readdir ($dir)) !== false )
		{
			if ( $file == '.' || $file == '..' )
			{
				continue;
			}

			if ( !preg_match ('#^.+\.' . $valid_extensions . '$#i', strtolower ($file)) )
			{
				continue;
			}

			$has_images = true;

			$image_info = getimagesize ('./uploads/' . $file);
			$image_width = 400;
			$image_height = 400;

			if ( $image_info !== false )
			{
				$image_width = $image_info[0] + 50;
				$image_height = $image_info[1] + 50;
			}

			if ( $image_width > 800 )
			{
				$image_width = 800;
			}

			if ( $image_height > 800 )
			{
				$image_height = 800;
			}
            
            if ( $config['use_wysiwyg'] )
            {
                $image_text = ' &lt;img src=&quot;' . $config['furl'] . '/uploads/' . $file . '&quot; alt=&quot;&quot; /&gt; ';
            }
            else
            {
                $image_text = ' [img]' . $config['furl'] . '/uploads/' . $file . '[/img] ';
            }

            $files[] = array (
                'text' => $image_text,
                'file' => $file,
                'full_url' => $config['furl'] . '/uploads/' . $file,
                'width' => $image_width,
                'height' => $image_height
            );
		}

		closedir ($dir);

		if ( !$has_images )
		{
			$content .= <<< html
	<tr>
		<td colspan="2" style="text-align:center">{$lang['ind300']}</td>
	</tr>
html;
		}
        else
        {
            $js = <<< html
<script type="text/javascript">
//<![CDATA[

var images = [

html;
            $sep = '';
            for ( $i = 0; isset ($files[$i]); $i++ )
            {
                $file = $files[$i];
                $js .= "{$sep}{ text: '{$file['text']}', file: '{$file['file']}', url: '{$file['full_url']}', w: {$file['width']}, h: {$file['height']}}";
                $sep = ",\n";
            }
            
            $js .= <<< html

];

$(function()
{
    function render ( images )
    {
        var table = document.getElementById('image-list');
        for ( i = table.rows.length - 1; i > 0; i-- )
        {
            table.deleteRow (i);
        }
        
        for ( i = 0; i < images.length; i++ )
        {
            var row = table.insertRow (i + 1);
            var left = row.insertCell (0);
            var right = row.insertCell (1);
            
            left.innerHTML =
                "[<a href=\"#\" onclick=\"insertHtml (window.opener.document.getElementById('newsposting').news, 'news', '" + images[i].text + "');\">{$lang['upld7']}</a>]&nbsp;" +
                "[<a href=\"#\" onclick=\"insertHtml (window.opener.document.getElementById('newsposting').fullnews, 'fullnews', '" + images[i].text + "');\">{$lang['upld8']}</a>]";
                
            right.innerHTML =
                "<a href=\"#\" onclick=\"javascript:window_pop ('" + images[i].url + "', 'image_preview_" + images[i].file + "', " + images[i].w + ", " + images[i].h + ")\">" + images[i].file + "</a>";
        }
    }

    $("#search").keyup (function (e)
    {
        if ( !images.length ) return;
    
        var keyword = $(this).val();
        var pattern = new RegExp (keyword, "i");
        var rows = [];
        if ( keyword.length > 0 )
        {
            for ( i = 0; i < images.length; i++ )
            {
                if ( images[i].file.search (pattern) >= 0 )
                {
                    rows.push (images[i]);
                }
            }
        }
        else
        {
            rows = images;
        }
        
        render (rows);
    });
    
    $("#clear-search").click (function()
    {
        $("#search").val("");
        render (images);
    });
    
    render (images);
});

//]]>
</script>
html;
        }
	}
	else
	{
		$content .= <<< html
	<tr>
		<td colspan="2" style="text-align:center">{$lang['upld9']}</td>
	</tr>
html;
	}

	$content .= <<< html
</table>
html;
}

$curve = FNEWS_VERSION;

echo
<<< html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{$lang['upld3']}</title>	
    <style type="text/css">
    /* <![CDATA[ */
    * { margin:0px; padding:0px }

    body { margin: 5px; font-size: 100% }
    body, td, input { color: #2D617D; font-family: "Trebuchet MS", Helvetica, sans-serif }
    input { background-color: #EFEFEF; border: 1px solid #A5B8C0; padding: 3px }
    input[type="submit"] { font-weight: bold }
    input[type="file"] { width: 100% }

    label { cursor:hand }

    fieldset { padding: 5px; border: 1px solid #000 }
    fieldset legend { padding: 5px }
    fieldset div { margin: 0 auto; width: 75% }

    h1 { font-size: 1.5em }

    a { color: #2D617D }
    a:hover { color: #22495E; text-decoration: none }

    ul { margin: 10px 0 10px 40px }

    #wrapper { margin: 0 auto; text-align: left; width: 95% }

    table.data { border-collapse:collapse; width:100% }
    table.data th { text-align: left }
    table.data td, table.data th { border-bottom: 1px solid #999; padding:2px }

    .left-column { float: left; }
    .right-column { float: right; }
    
    .right-align { text-align: right; }

    /* ]]> */
    </style>

    <script type="text/javascript" src="js/jsfunc.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript">
    //<![CDATA[
    
    function insertHtml ( element, elementName, text )
    {
        function insertPlainText ( element, text )
        {
            element.value += text;
        }
html;
    if ( $config['use_wysiwyg'] )
    {
        echo <<< html
        var editor = window.opener.CKEDITOR.instances[elementName];
        if ( editor.mode == 'wysiwyg' )
            editor.insertHtml (text);
html;
    }
    else
    {
        echo <<< html
        insertPlainText (element, text);
html;
    }
echo  <<< html
    }
    
    //]]>
    </script>
    {$js}
</head>

<body>
<div id="wrapper">
<h1>$title</h1>
$content
</div>
</body>

</html>
html;

?>
