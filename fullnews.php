<?php

/**
 * Full news page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: fullnews.php 391 2011-10-25 20:14:52Z xycaleth $
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

$id = ( isset ($GVARS['fn_id']) ) ? (int)$GVARS['fn_id'] : 0;

ob_start();

if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $id . '.php') )
{
	echo $flns0;
}
else
{
	$file = file (FNEWS_ROOT_PATH . 'news/news.' . $id . '.php');
	$news_info = parse_news_to_view ($file[1]);

	$temp_full = get_template ('fullnews_temp.php', true);
	echo replace_masks ($temp_full, array (
		'post_id'		=> $news_info['post_id'],
		'subject'		=> $news_info['subject'],
		'description'	=> $news_info['description'],
		'user'		=> $news_info['writer'],
		'date'		=> $news_info['date'],
		'icon'		=> $news_info['icon'],
		'send'		=> $news_info['link_tell_friend'],
		'nrc'			=> $news_info['nrc'],
		'comments'		=> $news_info['link_comments'],
		'cat_icon'		=> $news_info['cat_icon'],
		'cat_id'		=> $news_info['cat_id'],
		'cat_name'		=> $news_info['cat_name'],
		'shortnews'		=> $news_info['news'],
		'fullnews'		=> $news_info['fullnews']
	));
}

ob_end_flush();

?>
