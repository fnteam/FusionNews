<?php

/**
 * Upgrade script for 3.8.x to 3.9.x
 *
 * @package FusionNews
 * @subpackage Upgrader
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: upgrade_38x_to_39x.php 333 2010-11-25 23:19:47Z xycaleth $
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

error_reporting (E_ALL);

include 'functions.php';
include 'header.html';

$step = ( isset ($_GET['step']) ) ? intval ($_GET['step']) : 1;

switch ( $step )
{
	case 1:
	default:
		echo <<< eof
<p>Before upgrading please backup the following files/directories:</p>
<ul>
	<li>'news' directory (containing toc.php and news.x.php files)</li>
	<li>badwords.txt</li>
	<li>config.php</li>
	<li>smillies.db</li>
</ul>
<p>Once you have done this, <a href="?step=2">proceed to the next step</a>.</p>
eof;
	break;

	case 2:
		echo '<h2>Progress</h2>';
		$badwords_file = get_file_lines ('badwords.txt');

		$write = DIE_MESSAGE;
		foreach ( $badwords_file as $line )
		{
			list ($find, $replace) = explode ('=', $line);
			$write .= $find . '|<|' . $replace . '|<|0|<|0|<|' . "\n";
		}

		if ( write_file ($write, 'badwords.php') === true )
		{
			echo '......Word filter file updated.<br />';
		}

		if ( !@unlink ('../badwords.txt') )
		{
			trigger_error ('Unable to delete the old word filter file. Please manually delete this file.', E_USER_NOTICE);
		}
		else
		{
			echo '......Old word filter file deleted.<br />';
		}

		$toc_file = get_file_lines ('news/toc.php');

		$write = DIE_MESSAGE;
		foreach ( $toc_file as $line )
		{
			list ($news_id) = explode ('|<|', $line);

			if ( !file_exists ('../news/news.' . $news_id . '.php') )
			{
				// The news file doesn't exist, so just remove the entry from the TOC.
				continue;
			}

			$news_file = get_file_lines ('news/news.' . $news_id . '.php');
			array_shift ($news_file);

			$write_2 = DIE_MESSAGE;
			list ($shortnews, $fullnews, $author, $subject, $categories, $timestamp, $commentcount) = explode ('|<|', $news_file[0]);
			$write_2 .= $shortnews . '|<|' . $fullnews . '|<|' . $author . '|<|' . $subject . '|<||<|' . $categories . '|<|' . $timestamp . '|<|' . $commentcount . '|<|' . $news_id . '|<|' . "\n";
			array_shift ($news_file);
			$write_2 .= implode ('', $news_file);

			if ( write_file ($write_2, 'news/news.' . $news_id . '.php') === true )
			{
				echo '......News ID ' . $news_id . ' file updated.<br />';
			}

			$write .= $news_id . '|<|' . $timestamp . '|<|' . $author . '|<|' . $subject . '|<|' . $categories . '|<|' . "\n";
		}

		if ( write_file ($write, 'news/toc.php') === true )
		{
			echo '......News TOC (table of contents) file updated.<br />';
		}

		$smileys_file = get_file_lines ('smillies.db');

		$write = DIE_MESSAGE;
		foreach ( $smileys_file as $line )
		{
			list ($unique_id, $code, $image) = explode ('|<|', $line);
			$write .= $unique_id . '|<|' . $code . '|<|' . trim ($image) . '|<|' . "\n";
		}

		if ( write_file ($write, 'smillies.php') === true )
		{
			echo '......Smileys file updated.<br />';
		}

		if ( !@unlink ('../smillies.db') )
		{
			trigger_error ('Unable to delete the old smileys file (smillies.db). Please manually delete this file.', E_USER_NOTICE);
		}
		else
		{
			echo '......Old smileys file deleted.<br />';
		}

		update_config();
		echo '......Config file updated.<br />';

		echo <<< eof
<p>All files have been updated successfully! You should now add/overwrite the following files with the ones provided in the compressed archive (.zip file, .tar.gz file).</p>
<ul>
    <li>all files in <strong>img</strong> directory</li>
	<li>skins/fusion/index.html</li>
	<li>skins/fusion/stylesheet.css</li>
	<li>archive.php</li>
	<li>comments.php</li>
	<li>common.php</li>
	<li>fullnews.php</li>
	<li>functions.php</li>
	<li>headlines.php</li>
	<li>index.php</li>
	<li>jsfunc.js</li>
	<li>language.db.php</li>
	<li>news.php</li>
	<li>rss.php</li>
	<li>search.php</li>
	<li>send.php</li>
	<li>upload.php</li>
</ul>
<p>Once you have done this, make sure you <strong>delete the /upgrade directory</strong>.</p>
eof;
	break;
}

include 'footer.html';

?>