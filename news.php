<?php

/**
 * News displaying script
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: news.php 391 2011-10-25 20:14:52Z xycaleth $
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

# Edit the line below to align the page list (left, center, right)
$fn_page_align = 'center';

// Hackish fix
global $numofh, $link_headline_fullstory, $flip_news, $fsnw, $wfpost, $numofposts,
        $ht, $smilies, $bb, $stfpop, $compop, $furl, $fullnewsh,
        $fullnewsw, $fullnewss, $fullnewsz, $fslink, $datefor, $stflink,
        $stfheight, $stfwidth, $stfscrolls, $stfresize, $pclink, $hurl, 
        $link_headline_fullstory, $wwwidth, $comheight, $comwidth, $comscrolls,
		$comresize;

if ( !defined ('FNEWS_ROOT_PATH') )
{
	/**
     * @ignore
     */
	define ('FNEWS_ROOT_PATH', str_replace ('\\', '/', dirname (__FILE__)) . '/');
	include_once FNEWS_ROOT_PATH . 'common.php';
}

if ( !function_exists ('delete_user_settings') )
{
    function delete_user_settings()
    {
        static $user_settings = array (
            'fn_mode',
            'fn_page',
            'fn_category',
            'next_page',
            'prev_page',
            'pagination',
            'fn_news_per_page',
            'fn_pagination',
            'fn_author',
            'fn_news_template',
            'fn_date_order'
        );
        foreach ( $user_settings as $user_setting ) unset ($GLOBALS[$user_setting]);
    }
}

/**
 * News category(s) to display
 * @global array $fn_category
 */
$fn_category = ( isset ($fn_category) ) ? $fn_category : array();
$fn_category = !is_array ( $fn_category ) ? array ($fn_category) : $fn_category;

$num_categories = sizeof ($fn_category);

// Which one to include...
$fn_mode = ( isset ($GVARS['fn_mode']) ) ? $GVARS['fn_mode'] : '';

switch ( $fn_mode )
{
    case 'archive':
	case 'comments':
	case 'fullnews':
	case 'send':
    case 'search':
		include FNEWS_ROOT_PATH . $fn_mode . '.php';

		delete_user_settings();
		return;
	break;
}

$file = get_ordered_toc();

$total_posts = sizeof ($file);
if ( $total_posts <= 0 )
{
	unset ($fn_mode, $fn_category);
	return;
}

/**
 * Number of news items per page
 * @global int $fn_news_per_page
 */
$fn_news_per_page = ( isset ($fn_news_per_page) ) ? (int)$fn_news_per_page : $numofposts;
/**
 * Whether or not to use pagination
 * @global bool $fn_pagination
 */
$fn_pagination = ( isset ($fn_pagination) ) ? (bool)$fn_pagination : (bool)$news_pagination;
/**
 * Author(s) to display posts by
 * @global array $fn_author
 */
$fn_author = ( isset ($fn_author) ) ? $fn_author : array();
$fn_author = !is_array ($fn_author) ? array ($fn_author) : $fn_author;

$num_authors = sizeof ($fn_author);

/**
 * Whether or not to reverse the display order
 * @global string $fn_date_order
 */
$fn_date_order = ( isset ($fn_date_order) ) ? $fn_date_order : ($flip_news ? 'asc' : 'desc');
if ( $fn_date_order != 'asc' && $fn_date_order != 'desc' )
{
    $fn_date_order = $flip_news ? 'asc' : 'desc';
}

/**
 * Template to use for displaying the news
 * @global string $fn_news_template
 */
if ( !isset ($fn_news_template) || !file_exists (FNEWS_ROOT_PATH . 'templates/' . $fn_news_template . '.php') )
{
    $fn_news_template = 'news_temp';
}

/**
 * Output encoding when displaying news
 * @global string $fn_encoding
 */
$fn_encoding = isset ($fn_encoding) ? $fn_encoding : null;

$fn_page = 1;
if ( $fn_pagination )
{
    $fn_page = ( isset ($GVARS['fn_page']) ) ? (int)$GVARS['fn_page'] : $fn_page;
    $fn_page = ( $fn_page < 1 ) ? 1 : $fn_page;
}

$valid_posts = array();
for ( $i = 0; $i < $total_posts; $i++ )
{
	$toc = get_line_data ('news_toc', $file[$i]);

	$categories = explode (',', $toc['categories']);
	// Filter category
	if ( $num_categories > 0 && sizeof (array_intersect ($fn_category, $categories)) == 0 )
	{
		continue;
	}

	// Filter author
	if ( $num_authors > 0 && !in_array ($toc['author'], $fn_author) )
	{
		continue;
	}

	if ( file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
	{
		$news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
		$valid_posts[] = get_line_data ('news', $news_file[1]);
	}
}

ob_start();

if ( sizeof ($valid_posts) > 0 )
{
	if ( $fn_date_order == 'asc' )
	{
		$valid_posts = array_reverse ($valid_posts);
	}

	if ( $fn_pagination )
	{
		$total_posts = sizeof ($valid_posts);

		$pagination = '';
		$max_pages = ceil ($total_posts / $fn_news_per_page);
		if ( $max_pages > 1 )
		{
			$fn_page = ( $fn_page > $max_pages ) ? $max_pages : $fn_page;

			$other_qs = clean_query_string();

			if ( $news_pagination_arrows )
			{
				if ( ($fn_page - 1) >= 1 )
				{
					$pagination .= '<a href="?fn_page=' . ($fn_page - 1) . $other_qs . '" class="fn-news-pagination-prev">' . $news_pagination_prv . '</a>&nbsp;';
				}
				else
				{
					$pagination .= $news_pagination_prv . '&nbsp;';
				}
			}

			if ( $news_pagination_numbers || (!$news_pagination_numbers && !$news_pagination_arrows))
			{
				$pagination .= create_page_numbers ($max_pages, $fn_page, '?' . $other_qs, 'fn_page');
			}

			if ( $news_pagination_arrows )
			{
				if ( ($fn_page + 1) <= $max_pages )
				{
					$pagination .= '&nbsp;<a href="?fn_page=' . ($fn_page + 1) . $other_qs . '" class="fn-news-pagination-next">' . $news_pagination_nxt . '</a>';
				}
				else
				{
					$pagination .= '&nbsp;' . $news_pagination_nxt;
				}
			}
		}
	}

	$end = $fn_page * $fn_news_per_page;

	if ( $fn_pagination && $max_pages > 1 )
	{
		echo '<div style="text-align:' . $fn_page_align . '" class="fn-news-pagination-top">' . $pagination . '</div>';
	}

	if ( $post_per_day )
	{
		$posts = array();
	}
	
	$parse_settings = array (
	   'template'  => $fn_news_template,
	   'category'  => $fn_category
	);

	for ( $i = $end - $fn_news_per_page; $i < $end; $i++ )
	{
		if ( !isset ($valid_posts[$i]) )
		{
			continue;
		}

		if ( $post_per_day )
		{
			$posts[mktime (0, 0, 0, date ('n', $valid_posts[$i]['timestamp']), date ('j', $valid_posts[$i]['timestamp']), date ('Y', $valid_posts[$i]['timestamp']))][] = $valid_posts[$i];
		}
		else
		{
			$news_info = parse_news_to_view ($valid_posts[$i], $parse_settings);
			echo $news_info['display'];
		}
	}

	if ( $post_per_day )
	{
		$temp = get_template ('news_a_day_temp.php', false);
		foreach ( $posts as $key => $value )
		{
			$news_display = '';
			$display = replace_masks ($temp, array ('date' => date ($ppp_date, $key)));

			foreach ( $value as $post )
			{
				$news = parse_news_to_view ($post, $fn_category);
				$news_display .= $news['display'];
			}
			echo replace_masks ($display, array ('news_a_day' => $news_display));
		}
	}

	if ( $fn_pagination && $max_pages > 1 )
	{
		echo '<div style="text-align:' . $fn_page_align . '" class="fn-news-pagination-bottom">' . $pagination . '</div>';
	}
}

$out = ob_get_clean();
echo $out;

// Now remove the variables
delete_user_settings();

?>