<?php

/**
 * Installation script
 *
 * @pacakage FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: install.php 392 2011-10-31 22:10:47Z xycaleth $
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

if ( file_exists ('./install.lock') )
{
	die ('The installation file has been locked.');
}

define ('FN_INSTALLER', true);

include './common.php';
include './functions_install.php';

set_error_handler ('fn_install_error_handler');

/**
 * Set up some global variables
 */
$lang = $lang['install'];
$fullurl = 'http://' . $_SERVER['HTTP_HOST'] . substr (str_replace (basename (__FILE__), '', $_SERVER['SCRIPT_NAME']), 0, -1);
$step = ( isset ($GVARS['step']) ) ? (int)$GVARS['step'] : 1;

ob_start();

/**
 * And so we begin
 */
// Step 1:
// Introduction
if ( $step == 1 )
{
	echo <<< html
<p>{$lang['Intro']}</p>
<div style="text-align:center">
<input type="button" class="mainoption" onclick="document.location='?step=2'" value="{$lang['Begin']} &gt;&gt;&gt;" />
</div>
html;
}

// Step 2:
// Check PHP version and checks if we have GD library installed/enabled.
else if ( $step == 2 )
{
	$yes = '<span style="color:#00BB00"><strong>' . $lang['Yes'] . '</strong></span>';
	$no = '<span style="color:#FF0000"><strong>' . $lang['No'] . '</strong></span>';

	// PHP version check
	$php_version_check = phpversion();
	$php_version_expl = '';
	if ( !version_compare ($php_version_check, '4.3.0', '>=') )
	{
		$php_version_expl = '<br /><small>' . $lang['Need_Higher_PHP_Ver'] . '</small>';
	}

	// File Upload check
	$file_upload_check = (bool)(strtolower (@ini_get ('file_uploads') == 'off') || @ini_get ('file_uploads') == 0 || @ini_get ('file_uploads') == '');
	$file_upload_expl = '';
	$file_upload_value = 0;
	if ( $file_upload_check )
	{
		$file_upload_expl = '<br /><small>' . $lang['File_uploads_disabled'] . '</small>';
		$file_upload_check = $no;
	}
	else
	{
		$file_upload_check = $yes;
		$file_upload_value = 1;
	}

	// GD library version check
	$gd_library_check = $no;
	$gd_library_expl = '';
	$gd_library_value = 0;
	if ( function_exists ('gd_info') )
	{
		$gd_info = gd_info();
		preg_match ('#([\d\.]+)#', $gd_info['GD Version'], $m);

		if ( !version_compare ($m[1], '2.0', '>=') )
		{
			$gd_library_check = $no . ' (' . $m[1] . ')';
			$gd_library_expl = '<br /><small>' . $lang['GD_library_require_201'] . '</small>';
		}
        else if ( $gd_info['FreeType Support'] === false )
        {
            $gd_library_check = $no;
            $gd_library_expl = '<br /><small>' . $lang['No_TTF'] . '</small>';
        }
		else
		{
			$gd_library_check = $yes . ' (' . $m[1] . ')';
			$gd_library_value = 1;
		}
	}
	else
	{
		$gd_library_expl = '<br /><small>' . $lang['GD_library_required'] . '</small>';
	}

	echo <<< html
<p>{$lang['Check_settings']}</p>
<table style="width:100%">
	<tr>
		<td style="width:75%; padding:5px 0px; border-bottom:1px solid #DDDDDD"><b>{$lang['PHP_installed']}</b>$php_version_expl</td>
		<td style="width:25%; padding:5px 0px; border-bottom:1px solid #DDDDDD; text-align: center">$php_version_check</td>
	</tr>
	<tr>
		<td style="width:75%; padding:5px 0px; border-bottom:1px solid #DDDDDD"><b>{$lang['File_uploads_allowed']}</b>$file_upload_expl</td>
		<td style="width:25%; padding:5px 0px; border-bottom:1px solid #DDDDDD; text-align: center">$file_upload_check</td>
	</tr>
	<tr>
		<td style="padding: 5px 0px"><strong>{$lang['GD_library_installed']}</strong>$gd_library_expl</td>
		<td style="padding: 5px 0px; text-align:center">$gd_library_check</td>
	</tr>
</table>
html;

	if ( empty ($php_version_expl) )
	{
		echo <<< html
<form method="post" action="?step=3">
    <div style="text-align:center">
        <p>
            <input type="hidden" name="file_uploads" value="$file_upload_value" />
            <input type="hidden" name="gd_library" value="$gd_library_value" />
            <input type="submit" class="mainoption" value="{$lang['Continue']} &gt;&gt;&gt;" />
        </p>
    </div>
</form>
html;
	}
	else
	{
		echo '<p><span style="color:#FF0000"><strong>' . $lang['Not_meet_min_requirements'] . '</strong></span></p>';
	}
}

// Step 3:
// Show user which files should have what file permissions.
else if ( $step == 3 )
{
	$file_uploads = ( isset ($PVARS['file_uploads']) ) ? (int)$PVARS['file_uploads'] : 0;
	$gd_library = ( isset ($PVARS['gd_library']) ) ? (int)$PVARS['gd_library'] : 0;

	$files = array (
	array ('news/fonts/VeraMono.ttf', '0'),
	array ('news/toc.php', '0644'),
	array ('skins/fusion/index.html', '0'),
	array ('skins/fusion/stylesheet.css', '0'),
	array ('skins/fusion/images/header.png', '0'),
	array ('skins/fusion/images/page_background.jpg', '0'),
	array ('skins/fusion/images/table_background.png', '0'),
	array ('skins/fusion/images/table_bottom.png', '0'),
	array ('templates/arch_news_temp.php', '0644'),
	array ('templates/com_footer.php', '0644'),
	array ('templates/com_fulltemp.php', '0644'),
	array ('templates/com_header.php', '0644'),
	array ('templates/com_temp.php', '0644'),
	array ('templates/footer.php', '0644'),
	array ('templates/fullnews_temp.php', '0644'),
	array ('templates/header.php', '0644'),
	array ('templates/headline_temp.php', '0644'),
	array ('templates/news_a_day_temp.php', '0644'),
	array ('templates/news_temp.php', '0644'),
	array ('templates/sendtofriend_temp.php', '0644'),
	array ('archive.php', '0'),
	array ('badwords.php', '0644'),
	array ('banned.php', '0644'),
	array ('categories.php', '0644'),
	array ('comments.php', '0'),
	array ('common.php', '0'),
	array ('config.php', '0644'),
	array ('flood.php', '0644'),
	array ('fullnews.php', '0'),
	array ('functions.php', '0'),
	array ('headlines.php', '0'),
	array ('index.php', '0'),
	array ('jsfunc.js', '0'),
	array ('language.db.php', '0'),
	array ('logins.php', '0644'),
	array ('news.php', '0'),
	array ('rss.php', '0'),
	array ('search.php', '0'),
	array ('send.php', '0'),
	array ('sessions.php', '0644'),
	array ('smillies.php', '0644'),
	array ('upload.php', '0'),
	array ('users.php', '0644')
	);
	$num_files = sizeof ($files);

	$directory = array (
	array ('.', '0755'),
	array ('img', '0'),
	array ('news', '0755'),
	array ('news/fonts', '0'),
	array ('skins', '0'),
	array ('skins/fusion', '0'),
	array ('smillies', '0755'),
	array ('templates', '0755'),
	array ('uploads', '0755')
	);
	$num_directories = sizeof ($directory);

	echo <<< html
{$lang['Checking_files']}
<table class="adminpanel">
	<tr>
		<th style="width:60%">{$lang['Directory_name']}</th>
		<th style="width:20%">{$lang['File_permission']}</th>
		<th style="width:20%">{$lang['Result']}</th>
	</tr>
html;

	$result = '';
	$permission = 0;
	$bad_results = 0;

	// Clears all cached information about files and directories
	clearstatcache();

	for ( $i = 0; $i < $num_directories; $i++ )
	{
		$permission = substr ($directory[$i][1], 1);
		$permission = ( !$permission ) ? '-' : substr ($directory[$i][1], 1);

		if ( !file_exists (FNEWS_ROOT_PATH . $directory[$i][0]) )
		{
			$result = '<span style="color:#FF0000"><strong>' . $lang['Missing'] . '</strong></span>';
			$bad_results++;
		}
		else if ( $permission == '0755' && (!is_writeable (FNEWS_ROOT_PATH . $directory[$i][0]) || !is_readable (FNEWS_ROOT_PATH . $directory[$i][0])) )
		{
			$result = '<span style="color:#FF0000"><strong>' . $lang['Incorrect_permission'] . '</strong></span>';
			$bad_results++;
		}
		else
		{
			$result = '<span style="color:#00BB00"><strong>' . $lang['Good'] . '</strong></span>';
		}

		if ( $directory[$i][0] == '.' )
		{
			$directory[$i][0] = $lang['FN_directory'];
		}

		echo <<< html
	<tr>
		<td>{$directory[$i][0]}</td>
		<td align="center">$permission</td>
		<td align="center">$result</td>
	</tr>
html;
	}

	echo <<< html
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th style="width:60%">{$lang['File_name']}</th>
		<th style="width:20%">{$lang['File_permission']}</th>
		<th style="width:20%">{$lang['Result']}</th>
	</tr>
html;

	for ( $i = 0; $i < $num_files; $i++ )
	{
		$permission = substr ($files[$i][1], 1);
		$permission = ( !$permission ) ? '-' : substr ($files[$i][1], 1);

		if ( !file_exists (FNEWS_ROOT_PATH . $files[$i][0]) )
		{
			$result = '<span style="color:#FF0000"><strong>' . $lang['Missing'] . '</strong></span>';
			$bad_results++;
		}
		else if ( $permission == '0644' && (!fn_is_writeable ($files[$i][0]) || !is_readable (FNEWS_ROOT_PATH . $files[$i][0])) )
		{
			$result = '<span style="color:#FF0000"><strong>' . $lang['Incorrect_permission'] . '</strong></span>';
			$bad_results++;
		}
		else
		{
			$result = '<span style="color:#00BB00"><strong>' . $lang['Good'] . '</strong></span>';
		}

		echo <<< html
	<tr>
		<td>{$files[$i][0]}</td>
		<td align="center">$permission</td>
		<td align="center">$result</td>
	</tr>
html;
	}

	echo '</table>';

	if ( $bad_results )
	{
		printf ($lang['Found_problems'], $bad_results);
	}
	else
	{
		echo <<< html
<div style="text-align:center">
<form method="post" action="?step=4">
<p>
<input type="hidden" name="file_uploads" value="$file_uploads" />
<input type="hidden" name="gd_library" value="$gd_library" />
<input type="submit" class="mainoption" value="{$lang['Continue']} &gt;&gt;&gt;" />
</p>
</form>
</div>
html;
	}
}

// Step 4:
// Check website path, and enter username/password details
else if ( $step == 4 )
{
	$file_uploads = ( isset ($PVARS['file_uploads']) ) ? (int)$PVARS['file_uploads'] : 0;
	$gd_library = ( isset ($PVARS['gd_library']) ) ? (int)$PVARS['gd_library'] : 0;

	echo <<< html
<p>{$lang['Fill_form']}</p>
<form method="post" action="?step=5">
<table class="adminpanel">
    <thead>
        <tr>
            <th colspan="2">{$lang['Website_url']}</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th colspan="2">
                <input type="hidden" name="file_uploads" value="$file_uploads" />
                <input type="hidden" name="gd_library" value="$gd_library" />
                <input type="submit" class="mainoption" value="{$lang['Continue']} >>>" />
            </th>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td style="width:40%; text-align:right"><label for="website_url">{$lang['Website_url_colon']}&nbsp;&nbsp;</label></td>
            <td style="width:60%"><input type="text" class="post" name="website_url" id="website_url" value="http://{$_SERVER['HTTP_HOST']}" size="20" /></td>
        </tr>
        <tr>
            <th colspan="2">{$lang['Administrator']}</th>
        </tr>
        <tr>
            <td style="text-align:right"><label for="username">{$lang['Username']}&nbsp;&nbsp;</label></td>
            <td><input type="text" class="post" name="username" id="username" size="20" /></td>
        </tr>
        <tr>
            <td style="text-align:right"><label for="nick">{$lang['Nickname']}&nbsp;&nbsp;</label></td>
            <td><input type="text" class="post" name="nick" id="nick" size="20" /></td>
        </tr>
        <tr>
            <td style="text-align:right"><label for="email">{$lang['Email']}&nbsp;&nbsp;</label></td>
            <td><input type="text" class="post" name="email" id="email" size="20" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="checkbox" class="post" id="hideemail" name="hideemail" /> <label for="hideemail">{$lang['Hide_email']}</label></td>
        </tr>
        <tr>
            <td style="text-align:right"><label for="password">{$lang['Password']}&nbsp;&nbsp;</label></td>
            <td><input type="password" class="post" name="password" id="password" size="20" /></td>
        </tr>
        <tr>
            <td style="text-align:right"><label for="confirmpass">{$lang['Confirm']}&nbsp;&nbsp;</label></td>
            <td><input type="password" class="post" name="confirmpass" id="confirmpass" size="20" /></td>
        </tr>
    </tbody>
</table>
</form>
html;
}

// Step 5:
// Process the data...write the stuff to files and blam, we're done.
else if ( $step == 5 )
{
	$file_uploads = ( isset ($PVARS['file_uploads']) ) ? (int)$PVARS['file_uploads'] : 0;
	$gd_library = ( isset ($PVARS['gd_library']) ) ? (int)$PVARS['gd_library'] : 0;

	$website_url	= ( isset ($PVARS['website_url']) ) ? fn_trim (single_line ($PVARS['website_url'])) : '';
	$username	= ( isset ($PVARS['username']) ) ? fn_trim (single_line (utf8_substr ($PVARS['username'], 0, 40))) : '';
	$nick		= ( isset ($PVARS['nick']) ) ? fn_trim (single_line (utf8_substr ($PVARS['nick'], 0, 40))) : '';
	$email		= ( isset ($PVARS['email']) ) ? fn_trim (single_line ($PVARS['email'])) : '';
	$password	= ( isset ($PVARS['password']) ) ? utf8_substr ($PVARS['password'], 0, 40) : '';
	$confirmpass	= ( isset ($PVARS['confirmpass']) ) ? utf8_substr ($PVARS['confirmpass'], 0, 40) : '';
	$hideemail	= ( isset ($PVARS['hideemail']) ) ? 0 : 1;

	if ( !$website_url || !$username || !$nick || !$email || !$password || !$confirmpass )
	{
		trigger_error ($lang['Fields_left_blank'], E_USER_ERROR);
	}
	
    if ( !is_valid_email ($email) )
	{
		trigger_error ($lang['Invalid_email'], E_USER_ERROR);
	}
	
    if ( $password != $confirmpass )
	{
		trigger_error ($lang['Passwords_not_matching'], E_USER_ERROR);
	}

    $username = str_replace ('|', '&#124;', $username);
    $nick = str_replace ('|', '&#124;', $nick);
    $password = str_replace ('|', '&#124;', $password);

    // Let's start off by creating the new user...
    $data = '<?php die (\'You may not access this file.\'); ?>' . "\n";
    $data .= $username . '|<|' . $nick . '|<|' . $hideemail . '=' . $email . '|<||<|0|<|' . md5 ($password) . '|<|3|<|' . "\n";
    safe_write ('users.php', 'wb', $data);

    // Now we need to write the config.php file. Predefined settings
    // are made to be what (I think) to be what most people would need.
    $config = @config_array();
    $config['fusion_id'] = create_security_id();
    $config['site'] = $website_url;
    $config['furl'] = $fullurl;
    $config['hurl'] = $website_url . '/';
    $config['datefor'] = 'Y-m-d H:i:s T';
    $config['ppp_date'] = 'Y-m-d';
    $config['numofposts'] = 10;
    $config['numofh'] = 5;
    $config['bb'] = 1;
    $config['ht'] = 0;
    $config['post_per_day'] = 0;
    $config['wfpost'] = 1;
    $config['wfcom'] = 1;
    $config['skin'] = 'fusion';
    $config['cbwordwrap'] = 0;
    $config['wwwidth'] = 80;
    $config['smilies'] = 1;
    $config['stfpop'] = 0;
    $config['comallowbr'] = 1;
    $config['stfwidth'] = 640;
    $config['stfheight'] = 480;
    $config['fslink'] = 'read more...';
    $config['stflink'] = 'tell a friend';
    $config['pclink'] = 'comments';
    $config['fsnw'] = 0;
    $config['cbflood'] = 1;
    $config['floodtime'] = 30;
    $config['comlength'] = 300;
    $config['fullnewsw'] = 640;
    $config['fullnewsh'] = 480;
    $config['fullnewss'] = 1;
    $config['stfresize'] = 1;
    $config['stfscrolls'] = 1;
    $config['fullnewsz'] = 1;
    $config['htc'] = 0;
    $config['smilcom'] = 1;
    $config['bbc'] = 1;
    $config['compop'] = 0;
    $config['comscrolls'] = 1;
    $config['comresize'] = 1;
    $config['comheight'] = 480;
    $config['comwidth'] = 640;
    $config['uploads_active'] = $file_uploads;
    $config['uploads_size'] = 1048576;
    $config['uploads_ext'] = 'gif|jpg|jpeg|png|bmp';
    $config['enable_rss'] = 1;
    $config['link_headline_fullstory'] = 0;
    $config['flip_news'] = 0;
    $config['rss_title'] = '';
    $config['rss_description'] = '';
    $config['rss_encoding'] = 'utf-8';
    $config['com_validation'] = 1;
    $config['com_captcha'] = $gd_library;
    $config['news_pagination'] = 1;
    $config['news_pagination_prv'] = '&lt;&lt; Prev';
    $config['news_pagination_nxt'] = 'Next &gt;&gt;';
    $config['news_pagination_numbers'] = 0;
    $config['news_pagination_arrows'] = 1;
    $config['ppp_date'] = 'jS F Y';
    $config['comments_pages'] = 1;
    $config['comments_per_page'] = 10;
    $config['use_wysiwyg'] = 1;
    $config['stf_captcha'] = 1;

    save_config ($config);

    //////////////////////////////////////////////////////////////
    // Now we create the first news post. It's an improvement over the
    // previous way of doing it because you would end up with a post
    // made by a non-existant user. This way we're using the existing
    // user to make the post.
    //////////////////////////////////////////////////////////////
    $current_time	= time();
    $formatted_date	= date ('Y-m-d H:i:s T');
    $access_denied = '<?php die (\'You may not access this file.\'); ?>';

    // Here's what the subject and news posts are going to be so I can change
    // it in one place, and everything else changes.
    $subject = 'Welcome to Fusion News!';
    $news_story = '<p>Welcome to Fusion News v' . FNEWS_CURVE . '! You may delete this post as it is only a test post.</p>';

    // Start off by adding it to the table of contents (toc.php).
    $data = $access_denied . "\n";
    $data .= '1|<|' . $current_time . '|<|' . $username . '|<|' . $subject . '|<|1|<|' . "\n";

    safe_write ('news/toc.php', 'wb', $data);

    // Then we create the actual news file...
    $data = $access_denied . "\n";
    $data .= $news_story . '|<||<|' . $username . '|<|' . $subject . '|<|Test Post|<|1|<|' . $current_time . '|<|0|<|1|<|' . "\n";

    safe_write ('news/news.1.php', 'wb', $data);

    // And then write this user's name to the category file.
    safe_write ('categories.php', 'wb', $access_denied . "\n". '1|<|General|<||<|' . $username . '|<|' . "\n");
    
    // Generate the include code
    $code = "<?php
    
include '" . FNEWS_ROOT_PATH . "news.php';

?>";
    $code = htmlspecialchars ($code);
    
    @unlink ('install.php');
    @safe_write ('install.lock', 'wb', NULL);
    clearstatcache();
    $create_lock = '';
    $delete_file = '';
    if ( !file_exists ('install.lock') )
    {
        $create_lock = '<p>' . $lang['Create_install_lock'] . '</p>';
    }
    
    if ( file_exists ('install.php') )
    {
        $delete_file = '<p>' . $lang['Delete_install_file'] . '</p>';
    }

    echo <<< html
<h2>{$lang['Almost_there']}</h2>
<p>{$lang['Insert_code']}</p>
<div style="text-align:center"><textarea cols="60" rows="5">{$code}</textarea></div>
<p>{$lang['Install_success']}</p>

{$delete_file}
{$create_lock}
<p><a href="index.php">{$lang['Login_link']}</a></p>
html;
}

display_install_output ($lang, array (
    'step' => $step,
    'content' => ob_get_clean()
));

?>