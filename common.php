<?php

/**
 * Common intialisation routines
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: common.php 391 2011-10-25 20:14:52Z xycaleth $
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

if ( version_compare (PHP_VERSION, '5.3.0') == -1 )
{
	// set_magic_quotes_runtime is deprecated as of 5.3.0
	set_magic_quotes_runtime (0);
}

// Remove all register_globals added variables, so we only get one copy of the variable in its respective superglobal.
// Based on the one from phpBB2.
if ( strtolower (@ini_get ('register_globals')) == 'on' || @ini_get ('register_globals') == 1 )
{
	if ( isset ($_POST['GLOBALS']) || isset ($_GET['GLOBALS']) || isset ($_ENV['GLOBALS']) || isset ($_COOKIE['GLOBALS']) || isset ($_SESSION['GLOBALS']) )
	{
		die ('Hacking Attempt');
	}

	if ( !isset ($_SESSION) || !is_array ($_SESSION) )
	{
		$_SESSION = array();
	}

	// Superglobal vars
	$sg_vars = array_merge ($_GET, $_POST, $_SERVER, $_FILES, $_ENV, $_COOKIE, $_SESSION);

	foreach ( $sg_vars as $key => $value )
	{
		if ( $key == '_POST' || $key == '_GET' || $key == '_ENV' ||
			$key == '_SERVER' || $key == '_FILES' ||
			$key == '_COOKIE' || $key == '_SESSION' )
		{
			die ('Hacking attempt.');
		}

		if ( isset ($$key) )
		{
			unset ($GLOBALS[$key]);
		}
	}

	// Remove $sg_vars and $key and $value...
	unset ($sg_vars, $key, $value);
}

/**
 * Current version number
 */
define ('FNEWS_CURVE', '3.9.7-alpha');

if ( !defined ('FNEWS_ROOT_PATH') )
{
	/**
	 * Fusion News Root Path
	 */
	define ('FNEWS_ROOT_PATH', str_replace ('\\', '/', dirname (__FILE__)) . '/');
}

if ( !defined ('FN_INSTALLER') )
{
	if ( !file_exists (FNEWS_ROOT_PATH . 'install.lock') )
	{
		header ('Location: install.php');
	}
	else if ( file_exists (FNEWS_ROOT_PATH . 'install.php') )
	{
		die ('The install.php file still exists. Please make sure you delete this file.');
	}
}

if ( !file_exists (FNEWS_ROOT_PATH . 'config.php') )
{
	die ('config.php does not exist.');
}

include_once FNEWS_ROOT_PATH . 'config.php';
include_once FNEWS_ROOT_PATH . 'language.db.php';
include_once FNEWS_ROOT_PATH . 'functions.php';

// As of PHP 5.1.0, PHP scripts using date/time functions must define the timezone.
// If the server hasn't set one, then it will default to UTC.
if ( version_compare (PHP_VERSION, '5.1.0') >= 0 )
{
    $default_timezone = @date_default_timezone_get();
    date_default_timezone_set ($default_timezone);
}

/**
 * User level associated with a guest
 */
define ('GUEST', 0);
/**
 * User level associated with a news reporter
 */
define ('NEWS_REPORTER', 1);
/**
 * User level associated with a news editor
 */
define ('NEWS_EDITOR', 2);
/**
 * User level associated with an administrator
 */
define ('NEWS_ADMIN', 3);

/**
 * Combined array of $_POST which have been cleaned to be made safe
 * @global array $PVARS
 * @see clean_value()
 * @see clean_key()
 */
$PVARS = array();

/**
 * Combined array of $_GET which have been cleaned to be made safe
 * @global array $GVARS
 * @see clean_value()
 * @see clean_key()
 */
$GVARS = array();
$clean_k = '';

foreach ( $_GET as $k => $v )
{
	if ( is_array ($v) )
	{
		$clean_k = clean_key ($k);
		foreach ( $v as $_k => $_v )
		{
			$GVARS[$clean_k][clean_key ($_k)] = clean_value ($_v);
		}
	}
	else
	{
		$GVARS[clean_key ($k)] = clean_value ($v);
	}
}

foreach ( $_POST as $k => $v )
{
	if ( is_array ($v) )
	{
		$clean_k = clean_key ($k);
		foreach ( $v as $_k => $_v )
		{
			$PVARS[$clean_k][clean_key ($_k)] = clean_value ($_v);
		}
	}
	else
	{
		$PVARS[clean_key ($k)] = clean_value ($v);
	}
}

?>
