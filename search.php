<?php

/**
 * News search feature
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: search.php 392 2011-10-31 22:10:47Z xycaleth $
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

if ( !defined ('FNEWS_ROOT_PATH') )
{
	/**@ignore*/
	define ('FNEWS_ROOT_PATH', str_replace ('\\', '/', dirname (__FILE__)) . '/');
	include_once FNEWS_ROOT_PATH . 'common.php';
}

ob_start();

echo get_template ('header.php', true);

/**
 * Whether or not to use the basic search form
 * @global bool $fn_simple_search
 */
$fn_simple_search = ( isset ($fn_simple_search) ) ? (bool)$fn_simple_search : false;
$keywords = ( isset ($GVARS['fn_search_keywords']) ) ? $GVARS['fn_search_keywords'] : '';

/**
 * Whether to keep this inclusion static or not.
 * @global bool $fn_static
 */
if ( !isset ($fn_static) )
{
    $fn_static = false;
}

if ( $keywords === '' || $fn_static )
{
    $qs = clean_query_string();
    parse_str (html_entity_decode ($qs), $query_string);
    $query_string['fn_mode'] = 'search';
    
    echo '<form method="get" action="?' . $qs . '">';
    foreach ( $query_string as $key => $value )
    {
        echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars ($value) . '" />';
    }

    if ( $fn_simple_search )
    {
        echo <<< html
<input type="hidden" name="fn_search_all" value="1" />
<table style="width:75%">
    <tr>
        <td><input type="text" name="fn_search_keywords" id="fn_search_keywords" size="20" /></td>
        <td><input type="submit" value="$srch8" /></td>
    </tr>
</table>
html;
    }
    else
    {
        $category_list = build_category_selection();
        $author_list = '<select name="fn_search_author[]" id="fn_search_author" multiple="multiple" size="4">';
        $file = file (FNEWS_ROOT_PATH . 'users.php');
        array_shift ($file);
        foreach ( $file as $user )
        {
            $user = get_line_data ('users', $user);
            $author_list .= '<option value="' . $user['username'] . '">' . $user['nickname'] . '</option>';
        }
        $author_list .= '</select>';
        echo <<< html
<table style="width:75%">
	<tr>
		<td><label for="fn_search_keywords">$srch1</label></td>
		<td><input type="text" name="fn_search_keywords" id="fn_search_keywords" size="20" /></td>
	</tr>
	<tr>
		<td valign="top"><label for="fn_search_author">$srch0</label></td>
		<td>$author_list</td>
	</tr>
	<tr>
		<td valign="top"><label for="category">$srch2</label></td>
		<td>
			$category_list
		</td>
	</tr>
	<tr>
		<td valign="top"><label for="fn_search_from_num">$srch3</label></td>
		<td>
			<input type="checkbox" name="fn_search_all" checked="checked" onclick="javascript:updateFields(this.checked, this.form);" value="1" /> $srch4<br />
			<input type="text" name="fn_search_from_num" id="fn_search_from_num" disabled="disabled" maxlength="3" size="3" /> <select name="fn_search_from_type" disabled="disabled"><option value="0">$srch5</option><option value="1">$srch6</option><option value="2">$srch7</option></select>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center"><input type="submit" value="$srch8" />&nbsp;<input type="reset" value="$ind16" /></td>
	</tr>
</table>
<script type="text/javascript">
// <![CDATA[
function updateFields ( checked, form_object )
{
	form_object.fn_search_from_num.disabled = checked;
	form_object.fn_search_from_type.disabled = checked;
}
// ]]>
</script>
html;
    }
    
    echo '</form>';
}
else
{
	$authors = ( isset ($GVARS['fn_search_author']) ) ? $GVARS['fn_search_author'] : array();
	$categories = ( isset ($GVARS['category']) ) ? $GVARS['category'] : array();
	$search_all = isset ($GVARS['fn_search_all']);
	$from_num = ( isset ($GVARS['fn_search_from_num']) ) ? (int)$GVARS['fn_search_from_num'] : 0;
	$from_type = ( isset ($GVARS['fn_search_from_type']) ) ? (int)$GVARS['fn_search_from_type'] : 0;

	if ( empty ($author) && empty ($keywords) )
	{
		echo $srch9;
	}
	else if ( !$search_all && $from_num <= 0 )
	{
		echo $srch10;
	}
	else
	{
		$end = false;
        $keywords = explode (' ', $keywords);
        foreach ( $keywords as $key => $keyword )
        {
            if ( utf8_strlen ($keyword) < 3 )
            {
                $end = true;
                break;
            }
            else
            {
                $keywords[$key] = preg_quote ($keywords[$key], '#-');
            }
        }

		if ( $end )
		{
			echo $srch11;
		}
		else
		{
			$time_displacement = 0;
			if ( !$search_all )
			{
				$time_displacement = $from_num * 3600 * 24;
				switch ( $from_type )
				{
					case 0:	// days
					default:
						// already calculated as days
					break;

					case 1: // weeks
						$time_displacement *= 7;
					break;

					case 2: // months - just use the average number of days in a month
						$time_displacement *= 28;
					break;
				}
			}

			$time_displacement = $time_displacement < 0 ? 0 : $time_displacement;
			$search_after = $time_displacement == 0 ? 0 : time() - $time_displacement;

            $keywords = implode ('|', $keywords);
			$regex = '#(^|\W)(' . $keywords . ')($|\W)#i';

			$count = 0;
			$num_authors = sizeof ($authors);
			$num_categories = sizeof ($categories);
			$news_tmpl = get_template ('news_temp.php', false);

			$file = file (FNEWS_ROOT_PATH . 'news/toc.php');
			array_shift ($file);

			foreach ( $file as $toc_line )
			{
				$toc = get_line_data ('news_toc', $toc_line);

				$test_categories = explode (',', $toc['categories']);
				if ( $num_categories > 0 && (sizeof (array_intersect ($categories, $test_categories)) <= 0) )
				{
					// We're not looking in this particular category.
					continue;
				}

				if ( !$search_all && ($toc['timestamp'] <= $search_after) )
				{
					// Before our searched time.
					continue;
				}

				if ( $num_authors > 0 && !in_array ($toc['author'], $authors) )
				{
					// We're not looking for this author
					continue;
				}

				// Check if the file exists last as this takes the largest chunk of time.
				if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
				{
					continue;
				}

				$result = 0;
				$news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
				$article = get_line_data ('news', $news_file[1]);

				$shortnews = $article['shortnews'];
				$fullnews = $article['fullnews'];

				$shortnews = str_replace ('&br;', '<br />', $shortnews);
				$fullnews = str_replace ('&br;', '<br />', $fullnews);

				$shortnews = format_message ($shortnews, false, $bb, $smilies, $wfpost, true);
				$fullnews = format_message ($fullnews, false, $bb, $smilies, $wfpost, true);

				// Check if current post is:
				// - Included in the list of categories selected
				// - After the requested date
				// - Created by the correct author
				if ( preg_match_all ($regex, $shortnews, $m) )
				{
					++$result;
				}

				if ( preg_match_all ($regex, $fullnews, $n) )
				{
					++$result;
				}

				if ( preg_match_all ($regex, $toc['headline'], $o) )
				{
					++$result;
				}

				if ( preg_match_all ($regex, $article['description'], $p) )
				{
					++$result;
				}

				if ( $result > 0 )
				{
					$m[2] = array_unique ($m[2]);
					$n[2] = array_unique ($n[2]);
					$o[2] = array_unique ($o[2]);
					$p[2] = array_unique ($p[2]);

					++$count;
					$news_info = parse_news_to_view ($article);

					foreach ( $m[2] as $text )
					{
						$news_info['news'] = str_replace ($text, '<span style="background-color:yellow;color:red">' . $text . '</span>', $shortnews);
					}

					foreach ( $n[2] as $text )
					{
						$news_info['fullnews'] = str_replace ($text, '<span style="background-color:yellow;color:red">' . $text . '</span>', $fullnews);
					}

					foreach ( $o[2] as $text )
					{
						$news_info['subject'] = str_replace ($text, '<span style="background-color:yellow;color:red">' . $text . '</span>', $news_info['subject']);
					}

					foreach ( $p[2] as $text )
					{
						$news_info['description'] = str_replace ($text, '<span style="background-color:yellow;color:red">' . $text . '</span>', $news_info['description']);
					}

					echo replace_masks ($news_tmpl, array (
						'post_id'		=> $toc['news_id'],
						'subject'		=> '<a id="fus_' . $toc['news_id'] . '"></a>' . $news_info['subject'],
						'description'	=> $news_info['description'],
						'user'		=> $news_info['writer'],
						'date'		=> $news_info['date'],
						'icon'		=> $news_info['icon'],
						'send'		=> $news_info['link_tell_friend'],
						'nrc'			=> $news_info['nrc'],
						'comments'		=> $news_info['link_comments'],
						'cat_id'		=> $news_info['cat_id'],
						'cat_name'		=> $news_info['cat_name'],
						'cat_icon'		=> $news_info['cat_icon'],
						'news'		=> '<p>' . $news_info['news'] . '</p><p>' . $news_info['fullnews'] . '</p>',
						'fullstory'		=> '',
					));
				}
			}

			if ( $count <= 0 )
			{
				echo $srch12;
			}
		}
	}
}

echo get_template ("footer.php", true);

ob_end_flush();

unset ($fn_simple_search, $fn_static);

?>