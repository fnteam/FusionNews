<?php

/**
 * Control panel
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: index.php 393 2012-02-10 22:37:14Z xycaleth $
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
 *
 */
 
define ('FN_ROOT', dirname (__FILE__));

// Set the include path to load files from correct directories.
set_include_path (
    implode (
        PATH_SEPARATOR,
        array (
            get_include_path(),
            FN_ROOT . DIRECTORY_SEPARATOR . 'libraries',
            FN_ROOT . DIRECTORY_SEPARATOR . 'application'
        )
    )
);
 
include './common.php';
require 'FN/Loader.php';

FN_Loader::init();

/**
 * The title for the current page
 * @global string $title
 */
$title = '';

set_error_handler ('fn_error_handler');

/**
 * The PHP die() code to be placed on the first line of every data file.
 */
define ('DENIED_MSG', '<?php die (\'You may not access this file.\'); ?>' . "\n");
define ('DEFAULT_CONTROLLER', 'home');

/**
 * Stores the ID of the page to be displayed.
 * @global string $id
 */
$id = ( !isset ($GVARS['id']) ) ? DEFAULT_CONTROLLER : $GVARS['id'];
$id = preg_replace ('#[^a-z0-9\-_]+#', '', $id);
/**
 * User's unique session ID
 * @global string $sid
 **/
$sid = ( isset ($_COOKIE['fus_sid']) ) ? $_COOKIE['fus_sid'] : '';
/**
 * User name for current session
 * @global string $uid
 */
$uid = ( isset ($_COOKIE['fus_uid']) ) ? $_COOKIE['fus_uid'] : '';

/**
 * Used to store the user data for the current user, if they are logged in.
 * @global array $userdata
 */
$userdata = login_session_update ($uid, $sid);

ob_start();

$controller = FNEWS_ROOT_PATH . 'controllers/' . $id . '.php';
if ( !file_exists ($controller) )
{
    $controller = FNEWS_ROOT_PATH . 'controllers/' . DEFAULT_CONTROLLER . '.php';
}

include_once $controller;

/**
 * And finally display the output
 */
display_output ($title, $config['skin'], $userdata);

?>
