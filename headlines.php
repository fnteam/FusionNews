<?php

/**
 * Headlines output
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: headlines.php 393 2012-02-10 22:37:14Z xycaleth $
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

global $config;

/**
 * URL to the news page.
 * @global string $fn_news_url
 */
if ( !isset ($fn_news_url) )
{
	echo 'Please set the URL of your news page (using $fn_news_url).';
    unset ($fn_news_url, $fn_category, $fn_num_headlines, $fn_static);
	return;
}

$file = get_ordered_toc();
$total_posts = sizeof ($file);
if ( $total_posts <= 0 )
{
	unset ($fn_news_url, $fn_category, $fn_num_headlines, $fn_static);
	return;
}

if ( $config['flip_news'] )
{
	$file = array_reverse ($file);
}

/**
 * News category to display
 * @global array $fn_category
 */
$fn_category = ( isset ($fn_category) ) ? $fn_category : array();
$fn_category = !is_array ( $fn_category ) ? array ($fn_category) : $fn_category;

$num_categories = sizeof ($fn_category);

$valid_posts = array();

/**
 * Number of headles to show
 * @global int $fn_num_headlines
 */
$fn_num_headlines = isset ($fn_num_headlines) ? $fn_num_headlines : $config['numofh'];
$max_headlines = $total_posts < $fn_num_headlines ? $total_posts : $fn_num_headlines;

ob_start();

$mark = ( strpos ($fn_news_url, '?') === false ) ? '?' : '&amp;';
$settings = array ('category' => $fn_category, 'news_url' => $fn_news_url);
for ( $i = 0, $count = 0; ($i < $total_posts && $count < $max_headlines); $i++ )
{
	$toc = $file[$i];

	$categories = $toc['categories'];
	if ( $num_categories > 0 && sizeof (array_intersect ($fn_category, $categories)) == 0 )
	{
		continue;
	}

	$post = get_post ($toc['news_id']);
	$news_info = parse_news_to_view ($post, $settings);

	$temp_head = get_template ('headline_temp.php', false);
	$temp_head = replace_masks ($temp_head, array (
		'post_id'		=> $news_info['post_id'],
		'subject'		=> $news_info['subject'],
		'description'	=> $news_info['description'],
		'user'		=> $news_info['writer'],
		'icon'		=> $news_info['icon'],
		'date'		=> $news_info['date'],
		'cat_icon'		=> $news_info['cat_icon'],
		'cat_name'		=> $news_info['cat_name'],
		'cat_id'		=> $news_info['cat_id'],
		'linkend'		=> '</a>'
	));

	if ( ($i >= $config['numofposts']) || $config['link_headline_fullstory'] )
	{
		$temp_head = replace_masks ($temp_head, array ('linkstart' => '<a href="' . $fn_news_url . $mark . 'fn_mode=fullnews&amp;fn_id=' . $toc['news_id'] . '">'));
	}
	else
	{
		$temp_head = replace_masks ($temp_head, array ('linkstart' => '<a href="' . $fn_news_url . '#fus_' . $toc['news_id'] . '">'));
	}

	++$count;
	echo $temp_head;
}

ob_end_flush();

unset ($fn_news_url, $fn_category, $fn_num_headlines, $fn_static);

?>