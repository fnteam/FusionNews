<?php

/**
 * RSS Feed generator
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: rss.php 393 2012-02-10 22:37:14Z xycaleth $
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

if ( !$config['enable_rss'] )
{
	exit;
}

$file = get_ordered_toc();

$total_posts = sizeof ($file);
if ( $total_posts <= 0 )
{
	exit;
}

/**
 * Category ID to display posts from
 * @global int $fn_category
 */
$fn_category = ( isset ($GVARS['fn_category']) ) ? (int)$GVARS['fn_category'] : 0;

$current_date = date ('r');

$rss_url = $config['furl'] . '/rss.php';
if ( $fn_category > 0 )
{
	$rss_url .= '?fn_category=' . $fn_category;
}

$category_name = '';
if ( $fn_category > 0 )
{
    $category = get_category ($fn_category);
    if ( $category === null )
    {
        $fn_category = 0;
    }
    else
    {
        $category_name = $category['name'];
    }
}

$config['rss_title'] = replace_masks ($config['rss_title'], array ('cat_name' => $category_name));
$config['rss_description'] = replace_masks ($config['rss_description'], array ('cat_name' => $category_name));

header ('Content-Type: application/rss+xml; charset=' . $config['rss_encoding']);

ob_start();

echo <<< rss
<?xml version="1.0" encoding="{$config['rss_encoding']}"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
rss;

?>

<channel>
	<title><?php echo htmlspecialchars ($config['rss_title']) ?></title>
	<link><?php echo $config['hurl'] ?></link>
	<description><?php echo htmlspecialchars ($config['rss_description']) ?></description>
	<pubDate><?php echo $current_date ?></pubDate>
	<lastBuildDate><?php echo $current_date ?></lastBuildDate>
	<generator>Fusion News</generator>
	<atom:link href="<?php echo $rss_url ?>" type="application/rss+xml" rel="self" />

<?php

if ( $category_name != '' )
{
	echo "\t" . '<category>' . $category_name . '</category>' . "\n";
}

$count = 0;
$settings = array ('category' => array ($fn_category));
foreach ( $file as $newsdata )
{	
	$toc = get_line_data ('news_toc', $newsdata);
	$categories = explode (',', $toc['categories']);

	if ( $fn_category != 0 && !in_array ($fn_category, $categories) )
	{
		continue;
	}

	if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
	{
		continue;
	}
	
	if ( $count > 25 )
    {
        break;  
    }
    
    $count++;

	$news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');

	$article = get_line_data ('news', $news_file[1]);
	$news_info = parse_news_to_view ($article, $settings);

	$writer = get_author ($article['author']);
	$news_info['date'] = date ('r', $article['timestamp']);

	$author = ( $news_info['email'] ) ? "\n\t\t" . '<author>' . $news_info['email'] . ' (' . htmlspecialchars ($writer['nick']) . ')</author>' . "\n" : '';
    
    $description = $news_info['description'];
    if ( $news_info['description'] == '' )
    {
        $lines = explode ("\n", $news_info['news']);
        $description = array_shift ($lines);
    }

?>
	<item>
		<title><?php echo htmlspecialchars ($news_info['subject']) ?></title>
		<link><?php echo $config['furl'] . '/fullnews.php?fn_id=' . $article['news_id']; ?></link>
		<description><?php echo htmlspecialchars ($description); ?></description><?php echo $author ?>
		<pubDate><?php echo $news_info['date'] ?></pubDate>
		<guid isPermaLink="false">fus_<?php echo $article['news_id'] . '-' . $article['timestamp'] ?></guid>
	</item>

<?php
}

?>
</channel>

</rss>
<?php

ob_end_flush();

?>