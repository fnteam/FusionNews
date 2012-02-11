<?php

/**
 * Installation script
 *
 * @pacakage FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: install.php 394 2012-02-10 22:38:20Z xycaleth $
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

function domain_url()
{
    static $url = null;
    if ( $url === null )
    {
        $url = $_SERVER['HTTPS'] ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        if ( $_SERVER['HTTPS'] )
        {
            if ( $_SERVER['SERVER_PORT'] != '443' )
            {
                $url .= ':' . $_SERVER['SERVER_PORT'];
            }
        }
        else
        {
            if ( $_SERVER['SERVER_PORT'] != '80' )
            {
                $url .= ':' . $_SERVER['SERVER_PORT'];
            }
        }
    }
    
    return $url;
}

$fullurl = domain_url() . substr (str_replace (basename (__FILE__), '', $_SERVER['SCRIPT_NAME']), 0, -1);
$do = !isset ($GVARS['do']) ? null : $GVARS['do'];

ob_start();

if ( $do === null )
{
    $php_version_supported = version_compare (phpversion(), '5.0.0', '>=');
    $file_upload_enabled = (bool)(strtolower (@ini_get ('file_uploads') == 'off') || @ini_get ('file_uploads') == 0 || @ini_get ('file_uploads') == '');
    $gd_enabled = false;

    if ( function_exists ('gd_info') )
    {
        $gd_info = gd_info();
        if ( preg_match ('#([\d\.]+)#', $gd_info['GD Version'], $match) )
        {
            if ( !version_compare ($match[1], '2.0', '>=') )
            {
                $gd_enabled = false;
            }
            else if ( $gd_info['FreeType Support'] === false )
            {
                $gd_enabled = false;
            }
            else
            {
                $gd_enabled = true;
            }
        }
    }

    $os = reset (explode (' ', php_uname ('s')));

    function result ( $result )
    {
        return $result ? 'PASS' : 'FAIL';
    }

    function row_class ( $result, $required = false )
    {
        if ( !$result )
        {
            return $required ? ' class="fail-required"' : ' class="fail"';
        }
        
        return '';
    }

    ?>
<h2>System settings checks</h1>
<p>Fusion News requires a recent version of PHP to be installed, to be able to run and perform as it should. Below is the result for this check.</p>
<table class="requirements-table">
    <col />
    <col style="width: 100px" />
    <tr<?php echo row_class ($php_version_supported); ?>>
        <td>PHP version check
        <?php
        if ( !$php_version_supported )
            echo $php_version_expl;
        ?></td>
        <td><span class="result"><?php echo result ($php_version_supported); ?></span></td>
    </tr>
</table>
<p>
Fusion News can make use of extra capabilities the server may have to provide more functionality, but these are not necessary for Fusion News to run.
</p>
<table class="requirements-table">
    <col />
    <col style="width: 100px" />
    <tr<?php echo row_class ($file_upload_enabled, false); ?>>
        <td>File uploads enabled</td>
        <td><span class="result"><?php echo result ($file_upload_enabled); ?></span></td>
    </tr>
    <tr<?php echo row_class ($gd_enabled, false); ?>>
        <td><?php echo $lang['GD_library_installed']; ?></td>
        <td><span class="result"><?php echo result ($gd_enabled); ?></span></td>
    </tr>
</table>
    <?php

    $files = array (
        'news/toc.php',
        'templates/arch_news_temp.php',
        'templates/com_footer.php',
        'templates/com_fulltemp.php',
        'templates/com_header.php',
        'templates/com_temp.php',
        'templates/footer.php',
        'templates/fullnews_temp.php',
        'templates/header.php',
        'templates/headline_temp.php',
        'templates/news_a_day_temp.php',
        'templates/news_temp.php',
        'templates/sendtofriend_temp.php',
        'badwords.php',
        'banned.php',
        'categories.php',
        'config.php',
        'flood.php',
        'logins.php',
        'sessions.php',
        'smillies.php',
        'users.php'
    );
    $num_files = sizeof ($files);

    $directories = array (
        'news',
        'smillies',
        'templates',
        'uploads'
    );
    $num_directories = sizeof ($directories);

    // Clears all cached information about files and directories
    clearstatcache();

    $dir_results = array_fill_keys ($directories, 'good');
    $bad_dir_results = 0;
    for ( $i = 0; $i < $num_directories; $i++ )
    {
        if ( !is_dir ($directories[$i]) )
        {
            $dir_results[$directories[$i]] = 'missing';
            $bad_dir_results++;
        }
        else if ( !is_writeable ($directories[$i]) )
        {
            $dir_results[$directories[$i]] = 'badpermission';
            $bad_dir_results++;
        }
    }

    $file_results = array_fill_keys ($files, 'good');
    $bad_file_results = 0;
    for ( $i = 0; $i < $num_files; $i++ )
    {
        if ( !is_file ($files[$i]) )
        {
            $file_results[$files[$i]] = 'missing';
            $bad_file_results++;
        }
        else if ( !is_writable ($files[$i]) )
        {
            $file_results[$files[$i]] = 'badpermission';
            $bad_file_results++;
        }
    }

    echo '<h2>Directory and file permission</h2>';
    if ( $bad_dir_results || $bad_file_results )
    {
        echo '<p>The following directories and files are either missing or do not have the correct access permissions.</p>';

        if ( $bad_dir_results )
        {
            echo '<table class="requirements-table">';
            foreach ( $dir_results as $dir => $result )
            {
                switch ( $result )
                {
                    case 'missing':
                        echo '<tr class="fail-required"><td>&quot;' . $dir . '&quot; directory is missing. Make sure the directory exists.</td></tr>';
                    break;
                    
                    case 'badpermission':
                        echo '<tr class="fail-required">' . $dir . ' cannot be written to. Make sure the directory has write permissions.</td></tr>';
                    break;
                    
                    case 'good':
                    default:
                    break;
                }
            }
            echo '</table>';
        }
        
        if ( $bad_file_results )
        {
            echo '<table class="requirements-table">';
            foreach ( $file_results as $file => $result )
            {
                $dir_pos = strpos ($file, '/');
                if ( $dir_pos !== false )
                {
                    // Has parent directory. If the directory is missing, don't report file errors.
                    $dir = substr ($file, 0, $dir_pos);
                    if ( array_search ($dir, $directories) !== false && $dir_results[$dir] == 'missing' )
                    {
                        continue;
                    }
                }
                
                switch ( $result )
                {
                    case 'missing':
                        echo '<tr class="fail-required"><td>&quot;' . $file . '&quot; is missing. Make sure the file exists.</td></tr>';
                    break;
                    
                    case 'badpermission':
                        echo '<tr class="fail-required">' . $file . ' cannot be written to. Make sure the file has write permissions.</td></tr>';
                    break;
                    
                    case 'good':
                    default:
                    break;
                }
            }
            echo '</table>';
        }
    }
    else
    {
    ?>
<table class="requirements-table">
    <tr>
        <td>All directories and files have the correct permissions.</td>
    </tr>
</table>
    <?php
    }

    $website_url = domain_url();

    $file_upload_enabled = (int)$file_upload_enabled;

    echo <<< html
<h2>System configuration and admin login</h2>
<form method="post" action="?do=install">
<input type="hidden" name="gd_library" value="{$gd_enabled}" />
<input type="hidden" name="file_uploads" value="{$file_upload_enabled}" />
<fieldset>
    <legend>System Settings</legend>
    <table class="form">
        <tr>
            <td>
                <label for="website_url">{$lang['Website_url_colon']}</label>
            </td>
            <td><input type="text" name="website_url" id="website_url" value="{$website_url}" size="20" /></td>
        </tr>
    </table>
</fieldset>
<fieldset>
    <legend>{$lang['Administrator']}</legend>
    <table class="form">
        <tr>
            <td>
                <label for="username">{$lang['Username']}</label>
                <span class="description">Name you use to log in to the control panel.</span>
            </td>
            <td><input type="text" name="username" id="username" size="20" /></td>
        </tr>
        <tr>
            <td>
                <label for="nick">{$lang['Nickname']}</label>
                <span class="description">Name that gets displayed in news posts.</span>
            </td>
            <td><input type="text" name="nick" id="nick" size="20" /></td>
        </tr>
        <tr>
            <td><label for="email">{$lang['Email']}</label></td>
            <td><input type="text" name="email" id="email" size="20" /></td>
        </tr>
        <tr>
            <td><label for="hideemail">{$lang['Hide_email']}</label></td>
            <td><input type="checkbox" id="hideemail" name="hideemail" /></td>
        </tr>
        <tr>
            <td><label for="password">{$lang['Password']}</label></td>
            <td><input type="password" name="password" id="password" size="20" /></td>
        </tr>
        <tr>
            <td><label for="confirmpass">{$lang['Confirm']}</label></td>
            <td><input type="password" name="confirmpass" id="confirmpass" size="20" /></td>
        </tr>
    </table>
</fieldset>
<fieldset class="buttons">
<input type="submit" value="Install" />
</fieldset>
</form>
html;
}
else if ( $do == 'install' )
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
    $config['stfpop'] = 1;
    $config['comallowbr'] = 1;
    $config['stfwidth'] = 640;
    $config['stfheight'] = 480;
    $config['fslink'] = 'read more...';
    $config['stflink'] = 'tell a friend';
    $config['pclink'] = 'comments';
    $config['fsnw'] = 1;
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
    $config['compop'] = 1;
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
    $config['stf_captcha'] = $gd_library;

    save_config ($config);

    //////////////////////////////////////////////////////////////
    // Now we create the first news post. It's an improvement over the
    // previous way of doing it because you would end up with a post
    // made by a non-existant user. This way we're using the existing
    // user to make the post.
    //////////////////////////////////////////////////////////////
    $current_time	= time();
    $formatted_date	= date ('Y-m-d H:i:s T', $current_time);
    $access_denied = '<?php die (\'You may not access this file.\'); ?>';

    // Here's what the subject and news posts are going to be so I can change
    // it in one place, and everything else changes.
    $subject = 'Welcome to Fusion News!';
    $news_story = '<p>Welcome to Fusion News v' . FNEWS_VERSION . '! You may delete this post as it is only a test post.</p>';

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