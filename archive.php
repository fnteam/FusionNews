<?php

/**
 * Archive page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: archive.php 393 2012-02-10 22:37:14Z xycaleth $
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

global $config;

/**
 * @return Array of months as keys, with corresponding post counts for each month.
 */
function get_unique_post_months()
{
    $file = get_ordered_toc();
    $unique_times = array();
    
    $now = time();
    foreach ( $file as $post )
    {
        if ( $post['timestamp'] > $now )
        {
            continue;
        }
        
        $date = day_timestamp ($post['timestamp']);
        if ( isset ($unique_times[$date]) )
        {
            $unique_times[$date]++;
        }
        else
        {
            $unique_times[$date] = 1;
        }
    }
    
    ksort ($unique_times);
    return $unique_times;
}

/**
 * @return Timestamp for midnight of the given time.
 */
function day_timestamp ( $timestamp )
{
    return mktime (0, 0, 0, date ('n', $timestamp), 1, date ('Y', $timestamp));
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
        case 'post':
		case 'send':
			include FNEWS_ROOT_PATH . $fn_mode . '.php';
		break;

		default:
			$unique_times = get_unique_post_months();
			$qs = clean_query_string();
			foreach ( $unique_times as $date => $post_count )
			{
				$month = date ('n', $date);
				$year  = date ("Y", $date);

				echo '<a href="?fn_mode=archive&amp;fn_month=' . $month  . '&amp;fn_year=' . $year . $qs . '">' . $months[$month] . ' ' . $year . '</a><br />' . "\n";
			}
		break;
	}
}
else
{ /*id Month*/
	if ( $config['post_per_day'] )
	{
		$ppp_data = array();
	}
    
    $this_month = mktime (0, 0, 0, $month, 1, $year);
    $next_month = strtotime (date ('m/d/Y', $this_month) . " +1 month");
    $posts = get_posts_filtered ($this_month, $next_month, '', 0);
	
    $settings = array ('news_url' => $fn_news_url);
    $archive_template = get_template ('arch_news_temp.php', true);
	foreach ( $posts as $post )
	{
        $news_info = parse_news_to_view ($post, $settings);

        $tem = replace_masks ($archive_template, array (
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
            'description'	=> $news_info['description']
        ));

        if ( $config['post_per_day'] )
        {
            $day_time = mktime (0, 0, 0, date ('n', $post['timestamp']), date ('j', $post['timestamp']), date ('y', $post['timestamp']));
            if ( !isset ($ppp_data[$day_time]) )
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

	if ( $config['post_per_day'] )
	{
		krsort ($ppp_data);
		$temp = get_template ('news_a_day_temp.php', true);

		foreach ( $ppp_data as $key => $value )
		{
			echo replace_masks ($temp, array (
				'date'	=> date ($config['ppp_date'], $key),
				'news_a_day'=> $value
			));
		}
	}
}

echo get_template ('footer.php', true);

ob_end_flush();

unset ($fn_static);

?>
