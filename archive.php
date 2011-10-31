<?php

/**
 * Archive page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: archive.php 391 2011-10-25 20:14:52Z xycaleth $
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

/**
 * URL to the news page.
 * @global string $fn_news_url
 */
if ( !isset ($fn_news_url) )
{
	echo 'Please set the URL of your news page (using $fn_news_url).';
	return;
}

ob_start();

$month = ( isset ($GVARS['fn_month']) ) ? (int)$GVARS['fn_month'] : '';
$year = ( isset ($GVARS['fn_year']) ) ? (int)$GVARS['fn_year'] : '';
if ( !isset ($fn_static) )
{
    $fn_static = false;
}

echo get_template ('header.php', true);

if ( $fn_static || $month == '' || $month <= 0 || $month > 12 || $year == '' )
{
	$fn_mode = ( isset ($GVARS['fn_mode']) && !$fn_static ) ? $GVARS['fn_mode'] : '';
	switch ( $fn_mode )
	{
        case 'fullnews':
		case 'comments':
		case 'send':
			include FNEWS_ROOT_PATH . $fn_mode . '.php';
		break;

		default:
			$file = get_ordered_toc();

			if ( $flip_news )
			{
				$file = array_reverse ($file);
			}

			$array = array();
			$current_time = time();
			foreach ( $file as $value )
			{
				$toc = get_line_data ('news_toc', $value);

				if ( $toc['timestamp'] > $current_time )
				{ // This is an _archive_, not something that displays all posted news.
					continue;
				}

				$time = mktime (0, 0, 0, date ('n', $toc['timestamp']), 1, date ('y', $toc['timestamp']));
				$allready_added = false;

				$result = array_search ($time, $array);
				if ( $result === NULL || $result === false )
				{
					$array[] = $time;
				}
			}
			$qs = clean_query_string();
			foreach ( $array as $val )
			{
				$month = date ('n', $val);
				$year  = date ("Y", $val);

				echo '<a href="?fn_mode=archive&amp;fn_month=' . $month  . '&amp;fn_year=' . $year . $qs . '">' . $months[$month] . ' ' . $year . '</a><br />' . "\n";
			}
		break;
	}
}
else
{ /*id Month*/
	$file = get_ordered_toc();

	if( $flip_news )
	{
		$file = array_reverse($file);
	}

	if ( $post_per_day )
	{
		$ppp_data = array();
	}

	$specified_time = mktime (0, 0, 0, $month, 1, $year);
    $settings = array ('news_url' => $fn_news_url);
	foreach( $file as $value)
	{
		$toc = get_line_data ('news_toc', $value);
		$time = mktime (0, 0, 0, date ('n', $toc['timestamp']), 1, date ('y', $toc['timestamp']));

		if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
		{
			continue;
		}

		if ( $time == $specified_time )
		{
			$file_news = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
			$news_info = parse_news_to_view ($file_news[1], $settings);

			$tem = get_template ('arch_news_temp.php', true);
			$tem = replace_masks ($tem, array (
				'post_id'		=> $news_info['post_id'],
				'user'		=> $news_info['writer'],
				'date'		=> $news_info['date'],
				'icon'		=> $news_info['icon'],
				'nrc'			=> $news_info['nrc'],
				'comments'		=> $news_info['link_comments'],
				'send'		=> $news_info['link_tell_friend'],
				'cat_icon'		=> $news_info['cat_icon'],
				'cat_id'		=> $news_info['cat_id'],
				'cat_name'		=> $news_info['cat_name'],
				'news'		=> $news_info['news'],
				'fullstory'		=> $news_info['link_full_news'],
				'subject'		=> $news_info['subject'],
				'description'	=> $news_info['subject']
			));

			if ( $post_per_day )
			{
				$day_time = mktime (0, 0, 0, date ('n', $toc['timestamp']), date ('j', $toc['timestamp']), date ('y', $toc['timestamp']));
				if( !isset($ppp_data[$day_time]) )
				{
					$ppp_data[$day_time] = '';
				}

				$ppp_data[$day_time] .= $tem;
			}
			else
			{
				echo $tem;
			}
		}
	}

	if ( $post_per_day )
	{
		krsort ($ppp_data);
		$temp = get_template ('news_a_day_temp.php', true);

		foreach ( $ppp_data as $key => $value )
		{
			echo replace_masks ($temp, array (
				'date'	=> date('l', $key) . ', ' . $months[date ('n', $key)] . '&nbsp;' . date ('d', $key) . '&nbsp;' . date ('Y', $key),
				'news_a_day'=> $value
			));
		}
	}
}

echo get_template ('footer.php', true);

ob_end_flush();

unset ($fn_static);

?>
