<?php

/**
 * Functions used by the installer script
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: functions_install.php 393 2012-02-10 22:37:14Z xycaleth $
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

if ( !defined ('FN_INSTALLER') )
{
    return;
}

/**
 * Own function to check if a file is writeable.
 * Workaround for is_writeable not working on IIS.
 * @param string $file File to check if writeable
 * @return bool True if the file is writeable, otherwise false.
 */
function fn_is_writeable ( $file )
{
	if ( ($fp = @fopen (FNEWS_ROOT_PATH . $file, 'a')) === false )
	{
		return false;
	}

	fclose ($fp);

	return true;
}

/**
 * Callback function for PHP error handling (not to be used directly - it will be called by PHP when needed)
 * @param int $errno Error number
 * @param string $errstr Error text
 * @param string $errfile File containing the error
 * @param int $errline Line number containing the error
 * @return bool False if the default PHP internal error handler should handle the error
 */
function fn_install_error_handler ( $errno, $errstr, $errfile, $errline )
{
    switch ( $errno )
    {
    case E_USER_ERROR:
    case E_ERROR:
        global $lang, $step;
    
        display_install_output ($lang, array (
            'step' => $step,
            'content' => $errstr,
            'error' => true
        ));
    
        exit;
    break;
    }

    return true;
}

/**
 * Displays HTML output for the installer.
 * @param array $lang Language text array
 * @param array $display_params Parameters used for displaying the page
 */
function display_install_output ( $lang, $display_params )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Fusion News Installation</title>
    <link href="./skins/fusion/stylesheet.css" rel="stylesheet"	type="text/css" />
    <style type="text/css">
    .requirements-table {
        width: 100%;
    }
    
    .requirements-table tr {
        background-color: #0c0;
    }
    
    .requirements-table tr td {
        padding: 10px;
    }
    
    .requirements-table tr.fail {
        background-color: #fa0;
    }
    
    .requirements-table tr.fail-required {
        background-color: #f22;
    }
    
    .requirements-table td span.result {
        text-transform: uppercase;
    }
    </style>
    <script type="text/javascript" src="./jsfunc.js"></script>
</head>

<body>
<div id="layout">
    <div id="header">
        <img src="./skins/fusion/images/header.png" alt="Fusion News" title="Fusion News" width="904" height="117" />
    </div>
    
    <div id="main">
        <div id="content">
            <h1><?php printf ($lang['Title'], FNEWS_VERSION); ?></h1>
<?php

$content = $display_params['content'];
if ( array_key_exists ('error', $display_params) )
{
    $content = "<ul id=\"fn_warning\"><li class=\"title\">{$lang['Error']}</li><li>$content</li></ul>";
}

echo $content;

?>
        </div>
    </div>
    
    <div id="footer">
        <img src="./skins/fusion/images/table_bottom.png" alt="" width="904" height="25" />
        <?php printf ($lang['Powered_by'], FNEWS_VERSION); ?>
    </div>
</div>
</body>

</html>
<?php
}
 
?>