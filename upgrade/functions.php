<?php

/**
 * Upgrader-specific functions
 *
 * @package FusionNews
 * @subpackage Upgrader
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: functions.php 343 2010-12-24 12:42:58Z xycaleth $
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

/**
 * The PHP die() code to be placed on the first line of every data file.
 */
define ('DIE_MESSAGE', '<?php die (\'You may not access this file.\'); ?>' . "\n");
set_error_handler ('fn_error_handler');
$error = false;

/**
 * Gets an array of lines for a specified file, including error handling.
 * @param string $filename File name of the file
 * @param string $directory Directory the file exists in
 * @return array Array of lines from the file.
 */
function get_file_lines ( $filename, $directory = '../' )
{
	$handle = @file ($directory . $filename);
	if ( $handle === false )
	{
		trigger_error ('Failed to open file \'' . $filename . '\'.', E_USER_ERROR);
	}

	return $handle;
}

/**
 * Writes data to a specified file
 * @param mixed $data Data to be written to the file.
 * @param string $filename File to write the data to
 * @param string $directory The directory the specified file exists in
 * @return bool True is successful, otherwise false.
 */
function write_file ( $data, $filename, $directory = '../' )
{
	if ( ($fp = @fopen ($directory . $filename, 'wb')) === false )
	{
		trigger_error ('Failed to open \'' . $filename . '\' for writing.', E_USER_ERROR);
        
		return false;
	}

	@flock ($fp, LOCK_EX);
	if ( fwrite ($fp, $data, strlen ($data)) === false )
	{
        @flock ($fp, LOCK_UN);
        fclose ($fp);
        
		trigger_error ('Failed to write to \'' . $filename . '\'.', E_USER_ERROR);
        
		return false;
	}
	@flock ($fp, LOCK_UN);
	fclose ($fp);

	return true;
}

/**
 * Updates the current config file to the latest specification
 */
function update_config()
{
	include '../config.php';

	$config_array = array (
		'fusion_id' => '\'' . $fusion_id . '\'',
		'site' => '\'' . $site . '\'',
		'furl' => '\'' . $furl . '\'',
		'hurl' => '\'' . $hurl . '\'',
		'datefor' => '\'' . $datefor . '\'',
		'numofposts' => $numofposts,
		'numofh' => $numofh,
		'bb' => ( $bb == 'no' || !$bb ) ? 0 : 1,
		'ht' => ( $ht == 'no' || !$ht ) ? 0 : 1,
		'post_per_day' => ( $post_per_day == 'no' || !$post_per_day ) ? 0 : 1,
		'wfpost' => ( $wfpost == 'no' || !$wfpost ) ? 0 : 1,
		'wfcom' => ( $wfcom == 'no' || !$wfcom ) ? 0 : 1,
		'skin' => '\'' . $skin . '\'',
		'smilies' => ( $smilies == 'no' || !$smilies ) ? 0 : 1,
		'stfpop' => ( $stfpop == 'no' || !$stfpop ) ? 0 : 1,
		'comallowbr' => ( $comallowbr == 'no' || !$comallowbr ) ? 0 : 1,
		'stfwidth' => ( $stfwidth ) ? $stfwidth : 640,
		'stfheight' => ( $stfheight ) ? $stfheight : 480,
		'fslink' => '\'' . $fslink . '\'',
		'stflink' => '\'' . $stflink . '\'',
		'pclink' => '\'' . $pclink . '\'',
		'fsnw' => ( $fsnw == 'no' || !$fsnw ) ? 0 : 1,
		'cbflood' => ( $cbflood == 'no' || !$cbflood ) ? 0 : 1,
		'floodtime' => ( $floodtime ) ? $floodtime : 0,
		'comlength' => ( $comlength ) ? $comlength : 300,
		'fullnewsw' => ( $fullnewsw ) ? $fullnewsw : 640,
		'fullnewsh' => ( $fullnewsh ) ? $fullnewsh : 480,
		'fullnewss' => ( $fullnewss ) ? $fullnewss : 1,
		'stfresize' => ( $stfresize == 'no' || !$stfresize ) ? 0 : 1,
		'stfscrolls' => ( $stfscrolls == 'no' || !$stfscrolls ) ? 0 : 1,
		'fullnewsz' => ( $fullnewsz == 'no' || !$fullnewsz ) ? 0 : 1,
		'htc' => ( $htc == 'no' || !$htc ) ? 0 : 1,
		'smilcom' => ( $smilcom == 'no' || !$smilcom ) ? 0 : 1,
		'bbc' => ( $bbc == 'no' || !$bbc ) ? 0 : 1,
		'compop' => ( $compop == 'no' || !$compop ) ? 0 : 1,
		'comscrolls' => ( $comscrolls == 'no' || !$comscrolls ) ? 0 : 1,
		'comresize' => ( $comresize == 'no' || !$comresize ) ? 0 : 1,
		'comheight' => ( $comheight ) ? $comheight : 480,
		'comwidth' => ( $comwidth ) ? $comwidth : 640,
		'uploads_active' => ( $uploads_active == 'no' || !$uploads_active ) ? 0 : 1,
		'uploads_size' => ( $uploads_size ) ? $uploads_size : 1048576,
		'uploads_ext' => '\'' . $uploads_ext . '\'',
		'enable_rss' => ( $enable_rss == 'no' || !$enable_rss ) ? 0 : 1,
		'link_headline_fullstory' => ( $link_headline_fullstory == 'no' || !$link_headline_fullstory ) ? 0 : 1,
		'flip_news' => ( $flip_news == 'no' || !$flip_news ) ? 0 : 1,
		'rss_title' => '\'' . (( isset ($rss_title) ) ? $rss_title : 'My website name') . '\'',
		'rss_description' => '\'' . (( isset ($rss_description) ) ? $rss_description : 'Website description') . '\'',
		'rss_encoding' => '\'' . (( isset ($rss_encoding) ) ? $rss_encoding : 'utf-8') . '\'',
		'com_validation' => ( isset ($com_validation) ) ? $com_validation : 1,
		'com_captcha' => ( isset ($com_captcha) ) ? $com_captcha : 1,
		'news_pagination' => 1,
		'news_pagination_prv' => '\'&lt;&lt; Prev\'',
		'news_pagination_nxt' => '\'Next &gt;&gt;\'',
		'news_pagination_numbers' => 1,
		'news_pagination_arrows' => 1,
		'ppp_date' => '\'jS F Y\'',
		'comments_pages' => 1,
		'comments_per_page' => 10,
        'use_wysiwyg' => true,
        'stf_captcha' => true
	);

	$write = '<' . '?php' . "\n\n";
	$write .= "\n" . '// Auto-generated by Fusion News' . "\n";
	foreach ( $config_array as $key => $value )
	{
		$write .= '$' . $key . ' = ' . $value . ';' . "\n";
	}
	$write .= "\n" . '?' . '>';
	write_file ($write, 'config.php');
}

/**@ignore*/
function fn_error_handler ( $errno, $errstr, $errfile, $errline )
{
	switch ( $errno )
	{
		case E_USER_ERROR:
		case E_USER_WARNING:
			echo '<p class="error">' . $errstr . '</p>';
			echo '<p class="error">You should restore all the files that you have backed up before attempting to upgrade again.</p>';
			include 'footer.php';
			die();
		break;

		case E_USER_NOTICE:
			echo '<p class="notice">' . $errstr . '</p>';
		break;

		default:
			return false;
		break;
	}
	return true;
}

?>