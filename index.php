<?php

/**
 * Control panel
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: index.php 392 2011-10-31 22:10:47Z xycaleth $
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
 *
 * @todo Every page shown in the control panel currently stores its code entirely in this
 * single file. By adding a new 'else if' statement, a new page can be created but the
 * $id must be unique to any other pages created so far.
 *
 * e.g. adding this:
 * else if ( $id == 'newpage' ) {
 * // new page
 * }
 *
 * Will create a new page that can be accessed by going to index.php?id=newpage
 *
 * Each 'group' of pages need to be moved to its own file for easier maintainability
 * and modularity.
 */

/**#@+
 * @ignore
 */
$split = explode (' ', microtime());
$start_time = (float)$split[0] + (float)$split[1];
/**#@-*/

include './common.php';

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

/**
 * Stores the ID of the page to be displayed.
 * @global string $id
 */
$id = ( !isset ($GVARS['id']) ) ? '' : $GVARS['id'];
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
$userdata = array();
$userdata = login_session_update ($uid, $sid);

ob_start();

if ( $id == '' )
{
	if ( has_access (NEWS_REPORTER) )
	{
		$title = $ind9;

		$data_files = array ('news/toc.php', 'badwords.php', 'banned.php', 'categories.php', 'config.php',
						'flood.php', 'logins.php', 'sessions.php', 'smillies.php', 'users.php');

		$file_problems = '';
		foreach ( $data_files as $file )
		{
			if ( !file_exists (FNEWS_ROOT_PATH . $file) )
			{
				$file_problems .= '<tr><td>' . sprintf ($ind253, $file) . '</td></tr>';
			}
			else if ( !is_writeable (FNEWS_ROOT_PATH . $file) )
			{
				$file_problems .= '<tr><td>' . sprintf ($ind375, $file) . '</td></tr>';
			}
		}

		$welcome = sprintf ($ind376, $userdata['nick']);

		echo <<< html
<div style="text-align:center">
<p>$welcome</p>
<p><b>$ind377</b><br />
html;

		if ( $file_problems != '' )
		{
			echo '<table style="width:400px;margin:0 auto">' . $file_problems . '</table>';
		}
		else
		{
			echo $ind378;
		}

		$file = file (FNEWS_ROOT_PATH . 'news/toc.php');
		array_shift ($file);

		$num_news_items = sizeof ($file);
		$num_own_items = 0;
		$num_items_today = 0;

		$today = mktime (0, 0, 0, date ('n'), date ('j'), date ('Y'));

		foreach ( $file as $toc_line )
		{
			$news_toc = get_line_data ('news_toc', $toc_line);
			if ( $news_toc['author'] == $userdata['user'] )
			{
				++$num_own_items;
			}

			if ( $news_toc['timestamp'] >= $today && $news_toc['timestamp'] < ($today + 86400) )
			{
				++$num_items_today;
			}
		}

		$database_status = sprintf ($ind379, $num_news_items, $num_own_items, $num_items_today);

		echo <<< html
</p>
<p>
$database_status
html;

		if ( has_access (NEWS_EDITOR) && $com_validation )
		{
			$num_comments = get_pending_comments();
			if ( $num_comments > 0 )
			{
				echo '<br /><a href="?id=comments_manage">' . sprintf ($ind384, $num_comments) . '</a>';
			}
		}

		echo <<< html
</p>
<p><b>$ind385</b></p>
</div>
html;

		echo $ind13;

		if ( has_access (NEWS_ADMIN) )
		{
			echo $ind11;
		}
		elseif ( has_access (NEWS_EDITOR) )
		{
			echo $ind12;
		}

		echo $ind14;
	}
	else
	{
		$title = $ind3;
		echo <<< html
<form action="index.php?id=login" method="post">
<p>$ind0</p>
<table cellspacing="0" cellpadding="2">
	<tr>
		<td><label for="username">$ind169a</label></td>
		<td><input type="text" class="post" id="username" name="username" size="20" /></td>
	</tr>
	<tr>
		<td><label for="password">$ind4</label></td>
		<td><input type="password" class="post" id="password" name="password" size="20" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" name="keep_login" id="keep_login" value="1" /> <label for="keep_login">$ind120</label></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<p><input type="submit" class="mainoption" value="$ind3" /></p>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><a href="?id=lostpassword">$ind359</a></td>
	</tr>
</table>
</form>
html;
	}
}
else if ( $id == 'lostpassword' )
{
	if ( has_access (NEWS_REPORTER) )
	{
		trigger_error ($ind294, E_USER_WARNING);
	}

	$submit = ( isset ($PVARS['submit']) );
	$title = $ind360;

	if ( $submit )
	{
		$username = ( isset ($PVARS['username']) ) ? fn_trim ($PVARS['username']) : '';
		$email = ( isset ($PVARS['email']) ) ? fn_trim ($PVARS['email']) : '';

		if ( !$username && !$email )
		{
			trigger_error ($ind361, E_USER_WARNING);
		}

		$file = file (FNEWS_ROOT_PATH . 'users.php');
		array_shift ($file);

		$write = DENIED_MSG;

		$found = false;
		$admin_email = '';
		$admin_nick = '';
		foreach ( $file as $line )
		{
			$user = get_line_data ('users', $line);
			$useremail = explode ('=', $user['email']);

			if ( $admin_email == '' || $admin_nick == '' )
			{
				$admin_email = $useremail[1];
				$admin_nick = $user['nickname'];
			}

			if ( $user['username'] == $username || $useremail[1] == $email )
			{
				$found = true;
				break;
			}
		}

		if ( !$found )
		{
			trigger_error ($ind362, E_USER_WARNING);
		}

		$new_password = strtolower (create_security_id (12));
		$user['passwordhash'] = md5 ($new_password);

		$useremail = explode ('=', $user['email']);
		$to = $useremail[1];

		$message = sprintf ($ind363, $user['nickname'], $furl, $user['username'], $new_password);
        $message = prepare_string_for_mail ($message);
		$headers = 'From: ' . $admin_nick . ' <' . $admin_email . '>' . "\r\n" .
				'X-Mailer: PHP/ ' . phpversion() . "\r\n";

		if ( !@mail ($to, $ind364, $message, $headers) )
		{
			trigger_error ($ind365, E_USER_WARNING);
		}

		reset ($file);
		foreach ( $file as $line )
		{
			$user2 = get_line_data ('users', $line);
			if ( $user2['username'] == $user['username'] )
			{
				$write .= implode ('|<|', $user) . '|<|' . "\n";
			}
			else
			{
				$write .= $line;
			}
		}

		safe_write ('users.php', 'wb', $write);

		echo $ind366;
	}
	else
	{
		echo <<< html
<p>$ind367</p>
<form method="post" action="?id=lostpassword">
<table class="adminpanel">
	<tfoot>
		<tr>
			<th colspan="2"><input type="submit" name="submit" class="mainoption" value="$ind360" /></th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>$ind169a</td>
			<td><input type="text" name="username" class="post" size="20" /></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:left">$ind368</td>
		</tr>
		<tr>
			<td>$ind6</td>
			<td><input type="text" name="email" class="post" size="20" /></td>
		</tr>
	</tbody>
</table>
</form>
html;
	}
}
else if ( $id == 'login' )
{
	if ( has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind294, E_USER_WARNING);
    }
    
    $post_user = ( isset ($PVARS['username']) ) ? fn_trim (utf8_substr ($PVARS['username'], 0, 40)) : '';
    $post_pass = ( isset ($PVARS['password']) ) ? fn_trim (utf8_substr ($PVARS['password'], 0, 40)) : '';
    $keep_login = ( isset ($PVARS['keep_login']) ) ? 1 : 0;

    if ( !$post_user || !$post_pass )
    {
        trigger_error ($ind18, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift($file);
    $passwordhash = md5 ($post_pass);
    $successful = false;
    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);

        if ( $post_user == $user['username'] && $passwordhash == $user['passwordhash'] )
        {
            $userdata = login_session_create ($user['username'], $keep_login);
            $successful = true;
            break;
        }
    }

    if ( !$successful )
    {
        trigger_error ($ind18b, E_USER_WARNING);
    }
    
    $title = $ind397;
    echo make_redirect ($ind398);
}

//-------------------

//admin
else if ( $id == 'admin'){ /*id Admin*/
	if ( has_access (NEWS_ADMIN) )
	{
		$title = $ind280;
		echo <<< html
<div style="width:50%; float:left">
	<h2>$ind302</h2>
	<ul>
		<li><a href="?id=admin_paths">$ind45</a></li>
		<li><a href="?id=admin_news">$ind55</a></li>
		<li><a href="?id=admin_addons">$ind65</a></li>
		<li><a href="?id=admin_syndication">$ind324</a></li>
	</ul>

	<h2>$ind205</h2>
	<ul>
		<li><a href="?id=uploads">$ind204</a></li>
	</ul>

	<h2>$ind81</h2>
	<ul>
		<li><a href="?id=comments_manage">$ind203</a></li>
	</ul>

	<h2>$ind320</h2>
	<ul>
		<li><a href="?id=categories">$ind311</a></li>
	</ul>
</div>
<div style="float:left">
	<h2>$ind208</h2>
	<ul>
		<li><a href="?id=users">$ind167</a></li>
	</ul>

	<h2>$ind210</h2>
	<ul>
		<li><a href="?id=smillies">$ind211</a></li>
	</ul>

	<h2>$ind212</h2>
	<ul>
		<li><a href="?id=admin_template">$ind213</a></li>
	</ul>

	<h2>$ind173</h2>
	<ul>
		<li><a href="?id=badwordfilter">$ind215</a></li>
	</ul>
</div>
<div style="clear:both"></div>
html;
	}
	else if ( has_access (NEWS_EDITOR) )
	{
		$title = $ind161;
		echo <<< html
<div style="float:left; width:50%">
<h2>$ind81</h2>
<ul>
	<li><a href="?id=comments_manage">$ind203</a></li>
</ul>
<h2>$ind205</h2>
<ul>
	<li><a href="?id=uploads">$ind204</a></li>
</ul>
</div>
<div style="margin-left:50%; width: 49%">
<h2>$ind173</h2>
<ul>
	<li><a href="?id=badwordfilter">$ind215</a></li>
</ul>
</div>
<div style="clear:both"></div>
html;
	}
	else
	{
		trigger_error ($ind19, E_USER_WARNING);
	}
}
else if ( $id == 'admin_syndication' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

	$title = $ind324;
	$submit = ( isset ($PVARS['submit']) );

	if ( $submit )
	{
		$include = ( isset ($PVARS['include']) ) ? (int)$PVARS['include'] : -1;
		$category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();
        $newsurl = ( isset ($PVARS['news_url']) ) ? $PVARS['news_url'] : '';
        
        // Remove any duplicates
        $category = array_unique ($category);
        
        $text = '';
        switch ( $include )
		{
			case 0: // news
                $author = ( isset ($PVARS['author']) ) ? $PVARS['author'] : array();
                $pagination = (int)isset ($PVARS['pagination']);
                $num_news = ( isset ($PVARS['news_per_page']) ) ? (int)$PVARS['news_per_page'] : $numofposts;
                $date_order = ( isset ($PVARS['order']) ) ? $PVARS['order'] : '';
                
                if ( $date_order != 'asc' && $date_order != 'desc' )
                {
                    $date_order = $flip_news ? 'asc' : 'desc';
                }
                
                $template = ( isset ($PVARS['template']) ) ? $PVARS['template'] : '';
            
                if ( $num_news <= 0 )
                {
                    trigger_error ($ind182, E_USER_WARNING);
                }
            
				$text = "&lt;?php\n\n";
                
                $file = file (FNEWS_ROOT_PATH . 'categories.php');
                $total_categories = sizeof ($file) - 1; // -1 to remove first php line
                
                $num_categories = sizeof ($category);
                if ( $num_categories != $total_categories && $num_categories > 0 )
                {
                    $text .= '$fn_category = array (' . implode (',', $category) . ");\n";
                }
                
                $file = file (FNEWS_ROOT_PATH . 'users.php');
                $total_users = sizeof ($file) - 1;
                
                $num_authors = sizeof ($author);
                if ( $num_authors != $total_users && $num_authors > 0 )
                {
                    $text .= '$fn_author = array (';
                    $comma = '';
                    foreach ( $author as $user )
                    {
                        $text .= $comma . "'" . addslashes ($user) . "'";
                        $comma = ', ';
                    }
                    $text .= ");\n";
                }
                
                if ( $pagination != $news_pagination )
                {
                    $text .= '$fn_pagination = ' . $pagination . ";\n";
                }
                
                if ( $num_news != $numofposts )
                {
                    $text .= '$fn_news_per_page = ' . $num_news . ";\n";
                }
                
                if ( ($date_order == 'asc' && !$flip_news) || ($date_order == 'desc' && $flip_news) )
                {
                    $text .= "\$fn_date_order = '" . $date_order . "';\n";
                }
                
                if ( $template != '' && $template != 'news_temp' )
                {
                    if ( !file_exists (FNEWS_ROOT_PATH . 'templates/' . $template . '.php') )
                    {
                        trigger_error ("The template '" . $template . "' does not exist.", E_USER_WARNING);
                    }
                    
                    $text .= '$fn_template = \'' . $template . "';\n";
                }
                
                $text .= "include '" . FNEWS_ROOT_PATH . "news.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 1: // headlines
                $num_headlines = ( isset ($PVARS['headlines_to_show']) ) ? (int)$PVARS['headlines_to_show'] : $numofh;
                
                if ( $newsurl == '' )
                {
                    trigger_error ($ind168, E_USER_WARNING);
                }
                
                if ( $num_headlines <= 0 )
                {
                    trigger_error ($ind156, E_USER_WARNING);
                }
                
                $text = "&lt;?php\n\n";
                
                $file = file (FNEWS_ROOT_PATH . 'categories.php');
                $total_categories = sizeof ($file) - 1; // -1 to remove first php line
                
                $num_categories = sizeof ($category);
                if ( $num_categories != $total_categories && $num_categories > 0 )
                {
                    $text .= '$fn_category = array (' . implode (',', $category) . ");\n";
                }
                
                if ( $num_headlines != $numofh )
                {
                    $text .= "\$fn_num_headlines = $num_headlines;\n";
                }
                
                $text .= "\$fn_news_url = '$newsurl';\n";
				$text .= "include '" . FNEWS_ROOT_PATH . "headlines.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 2: // archives
                if ( $newsurl == '' )
                {
                    trigger_error ($ind168, E_USER_WARNING);
                }
            
                $text = "&lt;?php\n\n" .
                        "\$fn_news_url = '$newsurl';\n" .
                        "include '" . FNEWS_ROOT_PATH . "archive.php';\n\n" .
                        "?&gt;";
			break;

			case 3: // search
                $simple_search = ( isset ($PVARS['simple_search']) ) ? (int)$PVARS['simple_search'] : 0;
                
                $text = "&lt;?php\n\n";
                
                if ( $simple_search )
                {
                    $text .= "\$fn_simple_search = 1;\n";
                }
            
				$text .= "include '" . FNEWS_ROOT_PATH . "search.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 4: // rss
                if ( sizeof ($category) > 1 )
                {
                    trigger_error ($ind142, E_USER_WARNING);
                }
            
				$text = $furl . '/rss.php';
				if ( isset ($category[0]) )
				{
					$text .= '?fn_category=' . $category;
				}
			break;
            
            default:
                trigger_error ($ind90, E_USER_WARNING);
            break;
		}

		echo $ind373 . '
<div style="text-align:center"><textarea rows="12" cols="60" style="width:80%">' . $text . '</textarea></div>';
	}
	else
	{
		$category_selection = build_category_selection (null, array(), true);
        $author_selection = build_author_selection (array(), true);
        
        $asc_selected = $flip_news ? ' selected="selected"' : '';
        $desc_selected = !$flip_news ? ' selected="selected"' : '';
        
        $pagination_checked = checkbox_checked ($news_pagination);
        
		echo <<< html
<form method="post" action="">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">$ind324</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
                <input type="submit" name="submit" class="mainoption" value="$ind370" />
            </th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td style="width:30%"><label for="include">$ind325</label></td>
			<td>
				<select id="include" name="include" onchange="display_options(this.options[this.selectedIndex].value);">
					<option value="0">$ind128</option>
					<option value="1">$ind327</option>
					<option value="2">$ind328</option>
					<option value="3">$ind329</option>
					<option value="4">$ind330</option>
				</select>
			</td>
		</tr>
        
        <tr id="options-header">
            <th colspan="2">$ind404</th>
        </tr>
        
        <!-- Category option: used by many -->
        <tr id="category-option">
			<td>$ind405</td>
			<td>
                $category_selection
            </td>
		</tr>
        
        <!-- Search options -->
        <tr id="simple-search-option">
            <td><label for="simple_search">$ind406</label></td>
            <td>
                <input type="checkbox" name="simple_search" id="simple_search" value="1" />
            </td>
        </tr>
        
        <!-- Headline options -->
        <tr id="news-url-option">
            <td><label for="news_url">$ind371</label></td>
            <td>
                <input type="text" class="post" name="news_url" id="news_url" value="$hurl" />
            </td>
        </tr>
        <tr id="num-headlines-option">
            <td><label for="headlines_to_show">$ind60</label></td>
            <td>
                <input type="text" class="post" name="headlines_to_show" id="headlines_to_show" size="3" value="$numofh" />
            </td>
        </tr>
        
        <!-- News options -->
        <tr id="author-option">
            <td>$ind407</td>
            <td>
                $author_selection
            </td>
        </tr>
        <tr id="pagination-option">
            <td><label for="pagination">$ind408</label></td>
            <td>
                <input type="checkbox" name="pagination" value="1" id="pagination"$pagination_checked />
            </td>
        </tr>
        <tr id="num-news-option">
            <td><label for="news_per_page">$ind58</label></td>
            <td>
                <input type="text" class="post" name="news_per_page" id="news_per_page" size="3" value="$numofposts" />
            </td>
        </tr>
        <tr id="date-order-option">
            <td><label for="order">$ind409</label></td>
            <td>
                <select class="post" id="order" name="order">
                    <option value="asc"$asc_selected>$ind410</option>
                    <option value="desc"$desc_selected>$ind411</option>
                </select>
            </td>
        </tr>
        <tr id="template-option">
            <td>
                <label for="template">$ind326</label><br />
                <small>$ind207</small>
            </td>
            <td>
                <input type="text" class="post" name="template" id="template" />
                $ind240
            </td>
        </tr>
	</tbody>
</table>
</form>
<script type="text/javascript">
//<![CDATA[

var options = ["category-option", "simple-search-option", "news-url-option", "num-headlines-option",
                "author-option", "pagination-option", "num-news-option", "date-order-option", "template-option"];
                
var options_header_id = "options-header";

function hide_all_options()
{
    document.getElementById(options_header_id).style.display = 'none';
    for ( var i in options )
    {
        document.getElementById(options[i]).style.display = 'none';
    }
}

var include_options = [
    ["category-option", "author-option", "pagination-option", "num-news-option", "date-order-option", "template-option"],
    ["category-option", "news-url-option", "num-headlines-option"],
    ["news-url-option"],
    ["simple-search-option"],
    ["category-option"]
];

// Some 'constants'
var NEWS_INCLUDE = 0;
var HEADLINE_INCLUDE = 1;
var ARCHIVE_INCLUDE = 2;
var SEARCH_INCLUDE = 3;
var RSS_INCLUDE = 4;

function display_options ( include_type )
{
    if ( include_type < NEWS_INCLUDE || include_type > RSS_INCLUDE )
    {
        return;
    }
    
    hide_all_options();
    
    if ( include_options[include_type].length > 0 )
    {
        document.getElementById(options_header_id).style.display = 'table-row';
    }
    
    for ( var i in include_options[include_type] )
    {
        document.getElementById(include_options[include_type][i]).style.display = 'table-row';
    }
}

display_options (NEWS_INCLUDE);

//]]>
</script>
html;
	}
}
else if ( $id == 'admin_paths' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind45;
    echo "<form action=\"?id=admin_paths_save\" method=\"post\">\n",
         "<p>$ind46</p>\n",
         "<fieldset>\n",
         "<legend><label for=\"site1\">$ind47</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"site1\" name=\"site1\" value=\"$site\" style=\"width:90%\" /><br />\n",
         "$ind48\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"furl1\">$ind49</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"furl1\" name=\"furl1\" value=\"$furl\" style=\"width:90%\" /><br />\n",
         "$ind50\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"url\">$ind53</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"url\" name=\"url\" value=\"$hurl\" style=\"width:90%\" /><br />\n",
         "$ind54\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend> $ind83</legend>\n",
         "$ind84<br /><br />\n",
         "<input type=\"submit\" class=\"mainoption\" value=\"$ind36\" />\n",
         "<input type=\"reset\" class=\"mainoption\" value=\"$ind16\" />\n",
         "</fieldset>\n",

         get_form_security(),

         "</form>\n";
}

//save
else if ( $id == 'admin_paths_save' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $site	= ( isset ($PVARS['site1']) ) ? $PVARS['site1'] : $site;
    $furl	= ( isset ($PVARS['furl1']) ) ? $PVARS['furl1'] : $furl;
    $hurl	= ( isset ($PVARS['url']) ) ? $PVARS['url'] : $hurl;

    if ( !$site || !$furl || !$hurl )
    {
        trigger_error ($error23, E_USER_WARNING);
    }

    $configs = config_array();

    $configs['site']	 = $site;
    $configs['furl']	 = $furl;
    $configs['hurl']	 = $hurl;

    save_config( $configs );

    $title = $ind21;
    echo make_redirect ($ind22);
}

else if ( $id == 'admin_news' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind55;

    echo "<form action=\"?id=admin_news_save\" method=\"post\">\n",
         "<fieldset>\n",
         "<legend><label for=\"df\">$ind56 (<a href=\"http://www.php.net/date\" onclick=\"window.open (this.href); return false\">$ind57</a>)</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"df\" name=\"df\" value=\"$datefor\" size=\"20\" />\n",
         "</fieldset>\n",
         "<fieldset>\n",
         "<legend><label for=\"ppp_date\">$ind86 (<a href=\"http://www.php.net/date\" onclick=\"window.open (this.href); return false\">$ind57</a>)</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"ppp_date\" name=\"ppp_date\" value=\"$ppp_date\" size=\"20\" />\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind59</legend>\n",
         "<input type=\"text\" class=\"post\" id=\"posts\" name=\"posts\" value=\"$numofposts\" size=\"2\" /> <label for=\"posts\">$ind58</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"news_pagination\" name=\"news_pagination\" value=\"1\"" . checkbox_checked ($news_pagination) . " /> <label for=\"news_pagination\">$ind332</label>",
         '<fieldset id="pagination_options">
<input type="checkbox" class="post" id="news_pagination_numbers" name="news_pagination_numbers" value="1" ' . checkbox_checked ($news_pagination_numbers) . ' /> <label for="news_pagination_numbers">' . $ind339 . '</label><br />
<input type="checkbox" class="post" id="news_pagination_arrows" name="news_pagination_arrows" value="1" ' . checkbox_checked ($news_pagination_arrows) . ' /> <label for="news_pagination_arrows">' . $ind340 . '</label><br />
<input type="text" class="post" id="news_pagination_prv" name="news_pagination_prv" value="' . $news_pagination_prv . '" size="10" /> <label for="news_pagination_prv">' . $ind341 . '</label><br />
<input type="text" class="post" id="news_pagination_nxt" name="news_pagination_nxt" value="' . $news_pagination_nxt . '" size="10" /> <label for="news_pagination_nxt">' . $ind342 . '</label>
</fieldset><br />
<input type="checkbox" class="post" id="use_wysiwyg" name="use_wysiwyg" value="1" ', checkbox_checked ($use_wysiwyg), ' /> <label for="use_wysiwyg">', $ind88, '</label>',
         "</fieldset>\n",
         "<fieldset>\n",
         "<legend>$ind290</legend>\n",
         "<input type=\"text\" class=\"post\" id=\"h\" name=\"h\" value=\"$numofh\" size=\"2\" /> <label for=\"h\">$ind60</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"head_full_link\" name=\"head_full_link\" value=\"1\"" . checkbox_checked ($link_headline_fullstory) . " />\n",
         "<label for=\"head_full_link\">$ind267</label><br />\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind61</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"bbc\" name=\"bbc\" value=\"1\"" . checkbox_checked ($bb) . " />\n",
         "<label for=\"bbc\">$ind62</label>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"html\" name=\"html\" value=\"1\"" . checkbox_checked ($ht) . " />\n",
         "<label for=\"html\">$ind63</label>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"sm\" name=\"sm\" value=\"1\"" . checkbox_checked ($smilies) . " />\n",
         "<label for=\"sm\">$ind64</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind81</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"bbccom\" name=\"bbccom\" value=\"1\"" . checkbox_checked ($bbc) . " />\n",
         "<label for=\"bbccom\">$ind62</label>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"htmc\" name=\"htmc\" value=\"1\"" . checkbox_checked ($htc) . " />\n",
         "<label for=\"htmc\">$ind63</label>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"smil\" name=\"smil\" value=\"1\"" . checkbox_checked ($smilcom) . " />\n",
         "<label for=\"smil\">$ind64</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"cb_flip\">$ind268</label></legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"cb_flip\" name=\"cb_flip\" value=\"1\"" . checkbox_checked ($flip_news) . " /> <label for=\"cb_flip\">$ind269</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"post_per_day\">$ind260</label></legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"post_per_day\" name=\"post_per_day\" value=\"1\"" . checkbox_checked ($post_per_day) . " />\n",
         "<label for=\"post_per_day\">$ind261</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind83</legend>\n",
         "<input type=\"submit\" class=\"mainoption\" value=\"$ind36\" />\n",
         "<input type=\"reset\" value=\"$ind16\" />\n",
         "</fieldset>\n",

         get_form_security(),

         "</form>\n";
         
    echo <<< html
<script type="text/javascript">
//<![CDATA[

(function()
{
    function toggle_pagination_options()
    {
        var pagination_options = document.getElementById('pagination_options');
        pagination_options.disabled = !document.getElementById('news_pagination').checked;
    }

    document.getElementById('news_pagination').onchange = toggle_pagination_options;
    toggle_pagination_options();
})();

//]]>
</script>
html;
}

//temporary way of config file until fn 4.0
else if ( $id == 'admin_news_save' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $bbca = ( isset($PVARS['bbc']) ) ? 1 : 0;
    $post_per_day = ( isset($PVARS['post_per_day']) ) ? 1 : 0;
    $ppp_date = ( isset($PVARS['ppp_date']) ) ? $PVARS['ppp_date'] : $ppp_date;
    $hm = ( isset($PVARS['html']) ) ? 1 : 0;
    $sm = ( isset($PVARS['sm']) ) ? 1 : 0;
    $htcom = ( isset($PVARS['htmc']) ) ? 1 : 0;
    $smiliescom = ( isset($PVARS['smil']) ) ? 1 : 0;
    $bbcom = ( isset($PVARS['bbccom']) ) ? 1 : 0;
    $head_full_link = ( isset($PVARS['head_full_link']) ) ? 1 : 0;
    $datefor = ( isset ($PVARS['df']) ) ? $PVARS['df'] : $datefor;
    $numofposts = ( isset ($PVARS['posts']) ) ? intval ($PVARS['posts']) : $numofposts;
    $numofh = ( isset ($PVARS['h']) ) ? intval ($PVARS['h']) : $numofh;
    $cb_flip = (int)(isset ($PVARS['cb_flip']));
    $news_pagination = (int)(isset ($PVARS['news_pagination']));
    $news_pagination_numbers = (int)isset ($PVARS['news_pagination_numbers']);
    $news_pagination_arrows = (int)isset ($PVARS['news_pagination_arrows']);
    $news_pagination_prv = ( isset ($PVARS['news_pagination_prv']) ) ? $PVARS['news_pagination_prv'] : $news_pagination_prv;
    $news_pagination_nxt = ( isset ($PVARS['news_pagination_nxt']) ) ? $PVARS['news_pagination_nxt'] : $news_pagination_nxt;
    $use_wysiwyg = ( isset ($PVARS['use_wysiwyg']) ) ? 1 : 0;

    $configs = config_array();

    $configs['datefor'] = $datefor;
    $configs['numofposts'] = $numofposts;
    $configs['numofh'] = $numofh;
    $configs['bb'] = $bbca;
    $configs['ht'] = $hm;
    $configs['post_per_day'] = $post_per_day;
    $configs['ppp_date'] = $ppp_date;
    $configs['smilies'] = $sm;
    $configs['htc'] = $htcom;
    $configs['smilcom'] = $smiliescom;
    $configs['bbc'] = $bbcom;
    $configs['link_headline_fullstory'] = $head_full_link;
    $configs['flip_news']	 = $cb_flip;
    $configs['news_pagination'] = $news_pagination;
    $configs['news_pagination_numbers'] = $news_pagination_numbers;
    $configs['news_pagination_arrows'] = $news_pagination_arrows;
    $configs['news_pagination_nxt'] = $news_pagination_nxt;
    $configs['news_pagination_prv'] = $news_pagination_prv;
    $configs['use_wysiwyg'] = $use_wysiwyg;

    save_config( $configs );

    include './config.php';

    $title = $ind21;
    echo make_redirect ($ind22);
}

else if ( $id == 'admin_addons' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $skin_list = '<select id="s" name="s">' . "\n";
    $dir = opendir (FNEWS_ROOT_PATH . 'skins');
    while ( ($file = readdir ($dir)) !== false )
    {
        if ( $file == '.' || $file == '..' )
        {
            continue;
        }
        
        if ( !is_dir (FNEWS_ROOT_PATH . 'skins/' . $file) )
        {
            continue;
        }
        
        if ( !file_exists (FNEWS_ROOT_PATH . 'skins/' . $file . '/index.html') )
        {
            continue;
        }

        if ( $skin == $file )
        {
            $skin_list .= '<option selected="selected">' . $file . '</option>' . "\n";
        }
        else
        {
            $skin_list .= '<option>' . $file . '</option>' . "\n";
        }
    }
    closedir ($dir);

    $skin_list .= '</select>';

    $title = $ind65;
    echo "<form action=\"?id=admin_addons_save\" method=\"post\">\n",
         "<fieldset>\n",
         "<legend><label for=\"flink\">$ind66</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"flink\" name=\"flink\" value=\"$fslink\" size=\"22\" />\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind67</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"fspu\" name=\"fspu\" value=\"1\"" . checkbox_checked ($fsnw) . " />\n",
         "<label for=\"fspu\">$ind68</label><br />\n",
         "$ind69\n",
         "<input type=\"text\" class=\"post\" id=\"fspuw\" name=\"fspuw\" value=\"$fullnewsw\" size=\"6\" />\n",
         "$ind70\n",
         "<input type=\"text\" class=\"post\" id=\"fspuh\" name=\"fspuh\" value=\"$fullnewsh\" size=\"6\" />\n",
         "$ind71<br /><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"fpuscrolling\" name=\"fpuscrolling\" value=\"1\"" . checkbox_checked ($fullnewss) . " />\n",
         "<label for=\"fpuscrolling\">$ind72</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"fpuresize\" name=\"fpuresize\" value=\"1\"" . checkbox_checked ($fullnewsz) . " />\n",
         "<label for=\"fpuresize\">$ind73</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"slink\">$ind74</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"slink\" name=\"slink\" value=\"$stflink\" size=\"22\" />\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind75</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"stfpu\" name=\"stfpu\" value=\"1\"" . checkbox_checked ($stfpop) . " />\n",
         "<label for=\"stfpu\">$ind68</label><br />\n",
         "$ind69\n",
         "<input type=\"text\" class=\"post\" id=\"spuw\" name=\"spuw\" value=\"$stfwidth\" size=\"6\" />\n",
         "$ind70\n",
         "<input type=\"text\" class=\"post\" id=\"spuh\" name=\"spuh\" value=\"$stfheight\" size=\"6\" />\n",
         "$ind71 <br /><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"stfscrolls\" name=\"stfscrolls\" value=\"1\"" . checkbox_checked ($stfscrolls) . " />\n",
         "<label for=\"stfscrolls\">$ind72</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"stfresize\" name=\"stfresize\" value=\"1\"" . checkbox_checked ($stfresize) . " />\n",
         "<label for=\"stfresize\">$ind73</label>\n",
         "</fieldset>\n",
         
         '<fieldset>
    <legend>', $ind89, '</legend>
    <input type="checkbox" class="post" id="stf_captcha" name="stf_captcha" value="1"', checkbox_checked ($stf_captcha), ' /> <label for="stf_captcha">', $ind99, '</label>
</fieldset>',

         "<fieldset>\n",
         "<legend><label for=\"plink\">$ind77</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"plink\" name=\"plink\" value=\"$pclink\" size=\"22\" />\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind75a</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"compu\" name=\"compu\" value=\"1\"" . checkbox_checked ($compop) . " />\n",
         "<label for=\"compu\">$ind76a</label><br />\n",
         "$ind69\n",
         "<input type=\"text\" class=\"post\" id=\"compuw\" name=\"compuw\" value=\"$comwidth\" size=\"6\" />\n",
         "$ind70\n",
         "<input type=\"text\" class=\"post\" id=\"compuh\" name=\"compuh\" value=\"$comheight\" size=\"6\" />\n",
         "$ind71 <br /><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"comscrolls\" name=\"comscrolls\" value=\"1\"" . checkbox_checked ($comscrolls) . " />\n",
         "<label for=\"comscrolls\">$ind72</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"comresize\" name=\"comresize\" value=\"1\"" . checkbox_checked ($comresize) . " />\n",
         "<label for=\"comresize\">$ind73</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind78</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"com_validation\" name=\"com_validation\" value=\"1\"" . checkbox_checked ($com_validation) . " /> ",
         "<label for=\"com_validation\">$ind238</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"com_captcha\" name=\"com_captcha\" value=\"1\"" . checkbox_checked ($com_captcha) . " /> ",
         "<label for=\"com_captcha\">$ind322</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"cpbr\" name=\"cpbr\" value=\"1\"" . checkbox_checked ($comallowbr) . " />\n",
         "<label for=\"cpbr\">$ind79</label><br />\n",
         "<input type=\"checkbox\" class=\"post\" id=\"cbf\" name=\"cbf\" value=\"1\"" . checkbox_checked ($cbflood) . " />\n",
         "<label for=\"cbf\">$ind91</label> <input type=\"text\" class=\"post\" id=\"flood\" name=\"flood\" value=\"$floodtime\" size=\"3\" />$ind92<br />\n",
         "$ind91a<input type=\"text\" class=\"post\" id=\"comlength\" name=\"comlength\" value=\"$comlength\" size=\"6\" /><br />\n",
         '<input type="checkbox" name="comments_pages" id="comments_pages" class="post" value="1"' . checkbox_checked ($comments_pages) . ' /> <label for="comments_pages">' . $ind355 . '</label><br />',
         '<input type="text" name="comments_per_page" id="comments_per_page" class="post" size="3" value="' . $comments_per_page . '" /> <label for="comments_per_page">' . $ind356 . '</label>',
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind80</legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"wfcomcbx\" name=\"wfcomcbx\" value=\"1\"" . checkbox_checked ($wfcom) . " />\n",
         "<label for=\"wfcomcbx\">$ind81</label>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"wfpostcbx\" name=\"wfpostcbx\" value=\"1\"" . checkbox_checked ($wfpost) . " />\n",
         "<label for=\"wfpostcbx\">$ind82</label>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"cb_rss\">$ind265</label></legend>\n",
         "<input type=\"checkbox\" class=\"post\" id=\"cb_rss\" name=\"cb_rss\" value=\"1\"" . checkbox_checked ($enable_rss) . " /> <label for=\"cb_rss\">$ind266</label><br /><br />\n",
         "<fieldset>\n",
         "<legend><label for=\"rss_title\">$ind305</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"rss_title\" name=\"rss_title\" value=\"$rss_title\" size=\"20\" />\n",
         "</fieldset><br />\n",
         "<fieldset>\n",
         "<legend><label for=\"rss_description\">$ind306</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"rss_description\" name=\"rss_description\" value=\"$rss_description\" size=\"20\" />\n",
         "</fieldset><br />\n",
         "<fieldset>\n",
         "<legend><label for=\"rss_encoding\">$ind307</label></legend>\n",
         "<input type=\"text\" class=\"post\" id=\"rss_encoding\" name=\"rss_encoding\" value=\"$rss_encoding\" size=\"20\" />\n",
         "</fieldset>\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend><label for=\"s\">$ind186</label></legend>\n",
         "$skin_list\n",
         "</fieldset>\n",

         "<fieldset>\n",
         "<legend>$ind83</legend>\n",
         "<input type=\"submit\" class=\"mainoption\" value=\"$ind36\" />\n",
         "<input type=\"reset\" class=\"mainoption\" value=\"$ind16\" />\n",
         "</fieldset>\n",

         get_form_security(),

         "</form>\n";
}
//------------------------

//save
else if ( $id == 'admin_addons_save' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $fspu = isset ($PVARS['fspu']) ? 1: 0;
    $compu = isset ($PVARS['compu']) ? 1: 0;
    $combr = isset ($PVARS['cpbr']) ? 1: 0;
    $fcpu = isset ($PVARS['cpu']) ? 1: 0;
    $fstfpu = isset ($PVARS['stfpu']) ? 1: 0;
    $fspuz = isset ($PVARS['fpuresize']) ? 1: 0;
    $stfs = isset ($PVARS['stfscrolls']) ? 1: 0;
    $stfz = isset ($PVARS['stfresize']) ? 1: 0;
    $comscrolls = isset ($PVARS['comscrolls']) ? 1: 0;
    $comresize = isset ($PVARS['comresize']) ? 1: 0;
    $wfpost1 = isset ($PVARS['wfpostcbx']) ? 1: 0;
    $wfcom1 = isset ($PVARS['wfcomcbx']) ? 1: 0;
    $cbflood = isset ($PVARS['cbf']) ? 1: 0;
    $cb_rss = isset ($PVARS['cb_rss']) ? 1: 0;
    $fspus = isset ($PVARS['fpuscrolling']) ? 1: 0;

    $fspuw = isset ($PVARS['fspuw']) ? (int)$PVARS['fspuw']: 0;
    $fspuh = isset ($PVARS['fspuh']) ? (int)$PVARS['fspuh']: 0;
    $compuw = isset ($PVARS['compuw']) ? (int)$PVARS['compuw']: 0;
    $compuh = isset ($PVARS['compuh']) ? (int)$PVARS['compuh']: 0;
    $spuw = isset ($PVARS['spuw']) ? (int)$PVARS['spuw']: 0;
    $spuh = isset ($PVARS['spuh']) ? (int)$PVARS['spuh']: 0;
    $stf_captcha = (int)isset ($PVARS['stf_captcha']);
    $flood = isset ($PVARS['flood']) ? (int)$PVARS['flood']: 0;
    $s = isset ($PVARS['s']) ? $PVARS['s']: '';
    $comlength = isset ($PVARS['comlength']) ? (int)$PVARS['comlength']: 0;

    $com_validation = ( isset ($PVARS['com_validation']) ) ? 1 : 0;
    $com_captcha = ( isset ($PVARS['com_captcha']) ) ? 1 : 0;
    $comments_pages = (int)( isset ($PVARS['comments_pages']) );
    $comments_per_page = ( isset ($PVARS['comments_per_page']) ) ? (int)$PVARS['comments_per_page'] : 0;

    $fslink = ( isset ($PVARS["flink"]) ) ? $PVARS['flink'] : $fslink;
    $stflink = ( isset ($PVARS["slink"]) ) ? $PVARS['slink'] : $stflink;
    $pclink = ( isset ($PVARS["plink"]) ) ? $PVARS['plink'] : $pclink;

    $cfg_rss_title = ( isset ($PVARS['rss_title']) ) ? $PVARS['rss_title'] : $rss_title;
    $cfg_rss_description = ( isset ($PVARS['rss_description']) ) ? $PVARS['rss_description'] : $rss_description;
    $cfg_rss_encoding = ( isset ($PVARS['rss_encoding']) && !empty ($PVARS['rss_encoding']) ) ? $PVARS['rss_encoding'] : $rss_encoding;

    $configs = config_array();

    $configs['wfpost']		= $wfpost1;
    $configs['wfcom']		= $wfcom1;
    $configs['skin']		= $s;
    $configs['stfpop']		= $fstfpu;
    $configs['comallowbr']		= $combr;
    $configs['stfwidth']		= $spuw;
    $configs['stfheight']		= $spuh;
    $configs['fslink']		= $fslink;
    $configs['stflink']		= $stflink;
    $configs['pclink']		= $pclink;
    $configs['fsnw']		= $fspu;
    $configs['cbflood']		= $cbflood;
    $configs['floodtime']		= $flood;
    $configs['comlength']		= $comlength;
    $configs['fullnewsw']		= $fspuw;
    $configs['fullnewsh']		= $fspuh;
    $configs['fullnewss']		= $fspus;
    $configs['stfresize']		= $stfz;
    $configs['stfscrolls']		= $stfs;
    $configs['fullnewsz']		= $fspuz;
    $configs['compop']		= $compu;
    $configs['comscrolls']		= $comscrolls;
    $configs['comresize']		= $comresize;
    $configs['comheight']		= $compuh;
    $configs['comwidth']	 	= $compuw;
    $configs['enable_rss']		= $cb_rss;
    $configs['rss_title']	 	= $cfg_rss_title;
    $configs['rss_description']	= $cfg_rss_description;
    $configs['rss_encoding']	= $cfg_rss_encoding;
    $configs['com_validation']	= $com_validation;
    $configs['com_captcha']		= $com_captcha;
    $configs['comments_pages']	= $comments_pages;
    $configs['comments_per_page']	= $comments_per_page;
    $configs['stf_captcha']	= $stf_captcha;

    save_config ($configs);

    include './config.php';

    $title = $ind21;
    echo make_redirect ($ind22);
}

//------------------------
//select template
else if ( $id == 'admin_template' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title	 = $ind23;
    echo <<< html
<form method="get" action="?">
	<p>$ind175a<br />
    <input type="hidden" name="id" value="admin_template_edit" />
	<select id="menu" name="show" onchange="this.form.submit()">
		<option selected="selected">------------------------</option>
		<option value="1">$ind176</option>
		<option value="2">$ind177</option>
		<option value="3">$ind178</option>
		<option value="4">$ind179</option>
		<option value="5">$ind180</option>
		<option value="6">$ind181</option>
		<option value="7">$ind181a</option>
	</select>
	</p>
</form>
html;
}
//-----------------
//edit selected template
else if ( $id == 'admin_template_edit' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $show = ( isset ($GVARS['show']) ) ? (int)$GVARS["show"] : 0;
    $edit1 = $edit2 = "";
    
    if ( $show == 0 || !$show || $show > 7 )
    {
        trigger_error ($error10, E_USER_WARNING);
    }

    $title = $ind23;
    echo "<form action= \"?id=admin_template_save\" method=\"post\" id=\"tmpl_form\">";

    $name1 = '';
    $name2 = '';
    switch ( $show )
    {
        case 1:
            $name1 = 'header.php';
            $name2 = 'footer.php';
            echo $ind187;
        break;
        case 2:
            $name1 = 'com_header.php';
            $name2 = 'com_footer.php';
            echo $ind187;
        break;
        case 3:
            $name1 = 'news_temp.php';
            $name2 = 'fullnews_temp.php';
            echo $ind189 . $ind24;
        break;
        case 4:
            $name1 = 'arch_news_temp.php';
            echo $ind179b . $ind24;
        break;
        case 5:
            $name1 = 'com_temp.php';
            $name2 = 'com_fulltemp.php';
            echo $ind191 . $ind26;
        break;
        case 6:
            $name1 = 'headline_temp.php';
            $name2 = 'sendtofriend_temp.php';
            echo $ind192 . $ind30;
        break;
        case 7:
            $name1 = 'news_a_day_temp.php';
            echo $ind192a . $ind30a;
        break;
        default:
        break;
    }

    $find = array ('&', '<', '>', '"');
    $replace = array ('&amp;', '&lt;', '&gt;', '&quot;');

    $edit1 = @implode ('', @file (FNEWS_ROOT_PATH . 'templates/' . $name1));
    $edit1 = str_replace ($find, $replace, $edit1);

    if( $name2 != "" ){
        $edit2 = @implode ('', @file (FNEWS_ROOT_PATH . 'templates/' . $name2));
        $edit2 = str_replace ($find, $replace, $edit2);
    }
    
    echo <<<html
<div style="text-align:center">
<textarea name="edit1" id="edit1" class="post" cols="75" rows="20" style="width:95%;">$edit1</textarea><br />
<input type="button" onclick="document.getElementById('edit1').rows += 5" value="+" />
<input type="button" onclick="document.getElementById('edit1').rows -= 5" value="-" />
<input id="edited" name="edited" type="hidden" value="$show" />
</div>
html;
    if ( $name2 )
    {
        switch ( $show )
        {
            case 1:
                // fall-through
            case 2:
                echo $ind188;
            break;
            case 3:
                echo $ind25;
            break;
            case 5:
                echo $ind37;
            break;
            case 6:
                echo $ind38;
            break;
            default:
            break;
        }
        
        echo <<<html
<div style="text-align:center">
<textarea name="edit2" id="edit2" class="post" cols="75" rows="20" style="width:95%;">$edit2</textarea><br />
<input type="button" onclick="document.getElementById('edit2').rows += 5" value="+" />
<input type="button" onclick="document.getElementById('edit2').rows -= 5" value="-" />
</div>
<p>
<input type="submit" class="mainoption" value="$ind36" />
</p>
html;
    }
    else
    {
        echo <<<html
<p>
<input id="edit2" name="edit2" type="hidden" value="" />
<input type="submit" class="mainoption" value="$ind36" />
</p>
html;
    }

    echo get_form_security() . '</form>';
}
else if ( $id == 'admin_template_save' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $edit1 = ( isset ($PVARS["edit1"]) ) ? $PVARS['edit1'] : '';
    $edit2 = ( isset ($PVARS["edit2"]) ) ? $PVARS['edit2'] : '';
    $edited = ( isset ($PVARS['edited']) ) ? (int)$PVARS['edited'] : 0;
    
    if ( !$edited || $edited == 0 || $edited > 7 )
    {
        trigger_error ($error10, E_USER_WARNING);
    }

    $edit1 = html_entity_decode ($edit1);
    $edit2 = html_entity_decode ($edit2);

    $find = array ('&#33;DOCTYPE', '&#60;&#33;', '--&#62;', '&#60;script');
    $replace = array ('!DOCTYPE', '<!', '-->', '<script');

    $edit1 = str_replace ($find, $replace, $edit1);
    $edit2 = str_replace ($find, $replace, $edit2);

    $name1 = '';
    $name2 = '';
    if ($edited == 1) {$name1 = 'header.php'; $name2 = 'footer.php';}
    elseif ($edited == 2) {$name1 = 'com_header.php'; $name2 = 'com_footer.php'; }
    elseif ($edited == 3) {$name1 = 'news_temp.php'; $name2 = 'fullnews_temp.php'; }
    elseif ($edited == 4) {$name1 = 'arch_news_temp.php'; }
    elseif ($edited == 5) {$name1 = 'com_temp.php'; $name2 = 'com_fulltemp.php'; }
    elseif ($edited == 6) {$name1 = 'headline_temp.php'; $name2 = 'sendtofriend_temp.php';}
    elseif ($edited == 7) {$name1 = 'news_a_day_temp.php'; }

    safe_write ('templates/' . $name1, 'wb', $edit1);

    if ( $name2 != '' )
    {
        safe_write ('templates/' . $name2, 'wb', $edit2);
    }

    $title = $ind21;
    echo make_redirect ($ind22, '?id=admin_template', $ind337);
}
else if ( $id == 'users')
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind31;
    $datum = date ('Y-m-d H:i:s T');
    echo <<< html
<form action="?id=user_create" method="post">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">$ind31</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="submit" class="mainoption" value="$ind110" />
				<input type="reset" value="$ind16" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td><label for="username">$ind169a</label></td>
			<td><input size="20" type="text" class="post" id="username" name="username" /></td>
		</tr>
		<tr>
			<td><label for="nick">$ind119</label></td>
			<td><input size="20" type="text" class="post" id="nick" name="nick" /></td>
		</tr>
		<tr>
			<td><label for="email">$ind6</label></td>
			<td><input size="20" type="text" class="post" id="email" name="email" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input class="post" type="checkbox" id="hidemail" name="hidemail" /> <label for="hidemail">$ind183</label></td>
		</tr>
		<tr>
			<td>$ind112</td>
			<td>$datum</td>
		</tr>
		<tr>
			<td><label for="timeoffset">$ind111</label></td>
			<td><input size="2" type="text" class="post" id="timeoffset" name="timeoffset" value="0" /></td>
		</tr>
		<tr>
			<td><label for="password">$ind4</label></td>
			<td><input size="20" type="password" class="post" id="password" name="password" /></td>
		</tr>
		<tr>
			<td><label for="icon">$ind7</label></td>
			<td><input size="20" type="text" class="post" id="icon" name="icon" /></td>
		</tr>
		<tr>
			<td><label for="le">$ind8</label></td>
			<td>
				<select id="le" name="le">
					<option value="1">$ind193</option>
					<option value="2">$ind194</option>
					<option value="3">$ind195</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
html;

    echo get_form_security();

    echo <<< html
</form>
<h1>$ind167</h1>
<form action="?id=user_edit" method="post">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">$ind113</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2"><input type="submit" class="mainoption" value="$ind30b" /></th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td style="width:50%"><label for="user">$ind184</label></td>
			<td>
				<select id="user" name="user">
html;
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);

    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);
        echo '<option value="' . $user['username'] . '">' . $user['nickname'] . '</option>' . "\n";
    }

    echo <<< html
				</select>
			</td>
		</tr>
	</tbody>
</table>
</form>
html;
}
elseif ( $id == 'user_create' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $username = ( isset ($PVARS["username"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['username'], 0, 40))) : '';
    $email = ( isset ($PVARS["email"]) ) ? fn_trim (single_line ($PVARS['email'])) : '';
    $password = ( isset ($PVARS["password"]) ) ? fn_trim (utf8_substr ($PVARS['password'], 0, 40)) : '';
    $nick = ( isset ($PVARS["nick"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['nick'], 0, 40))) : '';
    $icon = ( isset ($PVARS["icon"]) ) ? fn_trim (single_line ($PVARS['icon'])) : '';
    $timeoffset = ( isset ($PVARS['timeoffset']) ) ? (int)$PVARS['timeoffset'] : 0;
    $le = ( isset ($PVARS['le']) ) ? (int)$PVARS['le'] : 1;
    $showemail = ( isset ($PVARS['hidemail']) ) ? 0 : 1;

    if ( !$username || !$email || !$password )
    {
        $title = $ind114;
        trigger_error ($ind115, E_USER_WARNING);
    }
    
    if ( !is_valid_email ($email))
    {
        $title = $ind116;
        trigger_error ($ind117, E_USER_WARNING);
    }

    // Clamp between these 2 values
    $timeoffset = $timeoffset > 24 ? 24 : $timeoffset;
    $timeoffset = $timeoffset < -24 ? -24 : $timeoffset;

    if ( get_author ($username, $nick) )
    {
        trigger_error ($ind32, E_USER_WARNING);
    }

    $write = $username . '|<|' . $nick . '|<|' . $showemail . '=' . $email . '|<|' . $icon . '|<|' . $timeoffset . '|<|' . md5 ($password) . '|<|' . $le . '|<|' . "\n";
    safe_write ('users.php', 'ab', $write);
    
    $title = $ind33;
    echo make_redirect ($username . ' ' . $ind118, '?id=users', $ind333);
}
else if ( $id == 'user_edit' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $user = ( isset ($PVARS["user"]) ) ? $PVARS['user'] : '';

    $userinfo = get_author ($user);
    if ( $userinfo === false )
    {
        trigger_error (sprintf ($ind20, $user), E_USER_WARNING);
    }

    $nor1 = ( $userinfo['level'] == 1 ) ? ' selected="selected"' : '';
    $edi1 = ( $userinfo['level'] == 2 ) ? ' selected="selected"' : '';
    $adm1 = ( $userinfo['level'] == 3 ) ? ' selected="selected"' : '';
    $showemail = ( $userinfo['showemail'] ) ? '' : ' checked="checked"';

    $icon_image = $userinfo['icon'] ? '<br /><img src="' . $userinfo['icon'] . '" alt="" />': '';

    $title = $ind113;
    $datum = date ('Y-m-d H:i:s T');

    echo <<< html
<form action="?id=user_update" method="post">
<table class="adminpanel">
	<tr>
		<th colspan="2">{$userinfo['user']}</th>
	</tr>
	<tr>
		<td><label for="nick1">$ind119</label></td>
		<td><input size="20" type="text" class="post" id="nick1" name="nick1" value="{$userinfo['nick']}" /></td>
	</tr>
	<tr>
		<td><label for="mail1">$ind6</label></td>
		<td><input size="20" type="text" class="post" id="mail1" name="mail1" value="{$userinfo['email']}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input size="20" type="checkbox" class="post" id="showemail" name="showemail"$showemail /> <label for="showemail">$ind183</label></td>
	</tr>
    <tr>
        <td>$ind112</td>
        <td>$datum</td>
    </tr>
	<tr>
		<td><label for="timeoffset">$ind111</label></td>
		<td><input size="2" type="text" class="post" id="timeoffset" name="timeoffset" value="{$userinfo['timeoffset']}" /></td>
	</tr>
	<tr>
		<td><label for="new_password">$ind4a $ind4</label></td>
		<td><input size="20" type="password" class="post" id="new_password" name="new_password" value="" /></td>
	</tr>
	<tr>
		<td><label for="confirm_pass">$ind369</label></td>
		<td><input size="20" type="password" class="post" id="confirm_pass" name="confirm_pass" value="" /></td>
	</tr>
	<tr>
		<td valign="top"><label for="icon1">$ind7</label></td>
		<td>
			<input size="20" type="text" class="post" id="icon1" name="icon1" value="{$userinfo['icon']}" />
			$icon_image
		</td>
	</tr>
	<tr>
		<td><label for="fle">$ind8</label></td>
		<td>
			<select id="fle" name="fle">
				<option value="1" $nor1>$ind193</option>
				<option value="2" $edi1>$ind194</option>
				<option value="3" $adm1>$ind195</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" id="name" name="name" value="{$userinfo['user']}" />
			<input type="checkbox" class="post" id="del" name="del" /> <label for="del">$ind97</label>
		</td>
	</tr>
	<tr>
		<th colspan="2">
			<input type="submit" class="mainoption" value="$ind174" />
			<input type="reset" value="$ind16" />
		</th>
	</tr>
</table>
html;
	echo get_form_security() . '</form>';
}
else if ( $id == 'user_update')
{
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $nick1 = ( isset ($PVARS["nick1"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['nick1'], 0, 40))) : '';
    $mail1 = ( isset ($PVARS["mail1"]) ) ? fn_trim (single_line ($PVARS['mail1'])) : '';
    $new_password = ( isset ($PVARS['new_password']) ) ? fn_trim (utf8_substr ($PVARS['new_password'], 0, 40)) : '';
    $confirm_pass = ( isset ($PVARS['confirm_pass']) ) ? fn_trim (utf8_substr ($PVARS['confirm_pass'], 0, 40)) : '';
    $icon1 = ( isset ($PVARS["icon1"]) ) ? single_line ($PVARS['icon1']) : '';
    $timeoffset = ( isset ($PVARS["timeoffset"]) ) ? (int)$PVARS['timeoffset'] : '';
    $name = ( isset ($PVARS["name"]) ) ? fn_trim (single_line (utf8_substr ($PVARS['name'], 0, 40))) : '';
    $deleteuser = ( isset ($PVARS["del"]) ) ? 1 : 0;
    $fle = ( isset ($PVARS["fle"]) ) ? (int)$PVARS['fle'] : 1;
    $showemail = ( isset ($PVARS['showemail']) ) ? 0 : 1;

    if ( !$nick1 || !$mail1 || !$name )
    {
        trigger_error ($ind296, E_USER_WARNING);
    }

    if ( $deleteuser && ($userdata['user'] == $name) )
    {
        trigger_error ($error12, E_USER_WARNING);
    }
    
    if ( !is_valid_email ($mail1) )
    {
        $title = $ind116;
        trigger_error ($ind117, E_USER_WARNING);
    }

    if ( $new_password && !$confirm_pass )
    {
        trigger_error ($ind297, E_USER_WARNING);
    }

    if ( $new_password != $confirm_pass )
    {
        trigger_error ($ind197, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift($file);

    $data = DENIED_MSG;

    // Clamp between these 2 values
    $timeoffset = $timeoffset > 24 ? 24 : $timeoffset;
    $timeoffset = $timeoffset < -24 ? -24 : $timeoffset;

    $editedself = false;
    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);

        if ( $name == $user['username'] )
        {
            if ( $deleteuser )
            {
                revoke_category_access ($name);
                continue;
            }

            $newpass = ( $new_password != '' ) ? md5 ($new_password) : $user['passwordhash'];
            $data .= $name . '|<|' . $nick1 . '|<|' . $showemail . '=' . $mail1 . '|<|' . $icon1 . '|<|' . $timeoffset . '|<|' . $newpass . '|<|' . $fle . '|<|' . "\n";
        }
        else
        {
            $data .= $value;
        }
    }

    safe_write ('users.php', 'wb', $data);

    $title = $ind285;
    echo make_redirect ($ind34a, '?id=users', $ind333);
}
else if ( $id == 'editprofile' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $datum = date('Y-m-d H:i:s T');

    $showemail = ( $userdata['showemail'] ) ? '' : ' checked="checked"';
    $icon_image = $userdata['icon'] ? '<br /><img src="' . $userdata['icon'] . '" alt="" />': '';

    $title = $ind284;
    echo <<<html
<form action="?id=updateprofile" method="post">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">{$userdata['user']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="submit" class="mainoption" value="$ind174" />
				<input type="reset" value="$ind16" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td><label for="nick1">$ind119</label></td>
			<td><input size="20" type="text" class="post" id="nick1" name="nick1" value="{$userdata['nick']}" /></td>
		</tr>
		<tr>
			<td><label for="mail1">$ind6</label></td>
			<td><input size="20" type="text" class="post" id="mail1" name="mail1" value="{$userdata['email']}" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input size="20" type="checkbox" class="post" id="showemail" name="showemail"$showemail /> <label for="showemail">$ind183</label>
			</td>
		</tr>
        <tr>
			<td>$ind112</td>
			<td>$datum</td>
		</tr>
		<tr>
			<td><label for="timeoffset">$ind111</label></td>
			<td><input size="2" type="text" class="post" id="timeoffset" name="timeoffset" value="{$userdata['offset']}" /></td>
		</tr>
		<tr>
			<td><label for="oldpassw">$ind4b $ind4</label></td>
			<td><input size="20" type="password" class="post" id="oldpassw" name="oldpassw" value="" /></td>
		</tr>
		<tr>
			<td><label for="passw">$ind4a $ind4</label></td>
			<td><input size="20" type="password" class="post" id="passw" name="passw" value="" /></td>
		</tr>
		<tr>
			<td><label for="icon1">$ind7</label></td>
			<td><input size="20" type="text" class="post" id="icon1" name="icon1" value="{$userdata['icon']}" />$icon_image</td>
		</tr>
	</tbody>
</table>
html;
    echo get_form_security() . '</form>';
}
else if ( $id == 'updateprofile')
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
	}
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $email = ( isset ($PVARS["mail1"]) ) ? fn_trim (single_line ($PVARS['mail1'])) : '';
    $oldpassw = ( isset ($PVARS["oldpassw"]) ) ? fn_trim (utf8_substr ($PVARS['oldpassw'], 0, 40)) : '';
    $passw = ( isset ($PVARS["passw"]) ) ? fn_trim (utf8_substr ($PVARS['passw'], 0, 40)) : '';
    $nick = ( isset ($PVARS['nick1']) ) ? fn_trim (single_line (utf8_substr($PVARS["nick1"], 0, 40))) : '';
    $icon = ( isset ($PVARS["icon1"]) ) ? single_line ($PVARS['icon1']) : '';
    $timeoffset = ( isset ($PVARS['timeoffset']) ) ? (int)$PVARS['timeoffset'] : 0;
    $showemail = ( isset ($PVARS['showemail']) ) ? 0 : 1;

    if ( !$nick || !$email )
    {
        $title = $ind114;
        trigger_error ($ind296, E_USER_WARNING);
    }
    
    if ( !is_valid_email ($email) )
    {
        $title = $ind116;
        trigger_error ($ind117, E_USER_WARNING);
    }

    if ( $oldpassw != '' && md5 ($oldpassw) != $userdata['password'] )
    {
        trigger_error ($ind288, E_USER_WARNING);
    }

    if ( $userdata['nick'] != $nick && get_author ('', $nick) )
    {
        trigger_error ($ind32, E_USER_WARNING);
    }

    //get file
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);

    $data = DENIED_MSG;

    $pass = ( $passw != '' ) ? md5 ($passw) : $userdata['password'];
    
    $timeoffset = $timeoffset > 24 ? 24 : $timeoffset;
    $timeoffset = $timeoffset < -24 ? -24 : $timeoffset;

    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);

        if ( $user['username'] == $userdata['user'] )
        {
            $data .= $userdata['user'] . "|<|$nick|<|$showemail=$email|<|$icon|<|$timeoffset|<|$pass|<|" . $user['level'] . "|<|\n";
        }
        else
        {
            $data .= $value;
        }
    }

    safe_write ('users.php', 'wb', $data);
    
    $title = $ind286;
    echo make_redirect ($ind287);
}
elseif ( $id == 'postnews' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind34b;

    $action = ( isset ($PVARS['action']) ) ? $PVARS['action'] : '';
    $news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
    $categories = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array(1);
    $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
    $subject = ( isset ($PVARS['post_subject']) ) ? fn_trim ($PVARS['post_subject']) : '';
    $description = ( isset ($PVARS['description']) ) ? fn_trim ($PVARS['description']) : '';
    
    if ( isset ($use_wysiwyg) && $use_wysiwyg )
    {
        echo '<script type="text/javascript" src="ckeditor/ckeditor.js"></script>';
    }

    if ( $action == 'preview' )
    {
        $news1 = format_message ($news, $ht || (isset ($use_wysiwyg) && $use_wysiwyg), $bb, $smilies, $wfpost);
        $fullnews1 = format_message ($fullnews, $ht || (isset ($use_wysiwyg) && $use_wysiwyg), $bb, $smilies, $wfpost);
        
        if ( !$ht && (!isset ($use_wysiwyg) || !$use_wysiwyg) )
        {
            $news1 = str_replace ("\n", '<br />', $news1);
            $fullnews1 = str_replace ("\n", '<br />', $fullnews1);
        }
        else
        {
            // Need to be a bit smarter about new lines.
            $news1 = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $news1);
            $fullnews1 = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $fullnews1);
        }

        echo <<< html
<h2>$ind200</h2>
<table class="adminpanel">
	<tr>
		<th>$ind128</th>
	</tr>
	<tr>
		<td>$news1</td>
	</tr>
html;

        if ( !empty ($fullnews1) )
        {
            echo <<< html
	<tr>
		<th>$ind94</th>
	</tr>
	<tr>
		<td>$fullnews1</td>
	</tr>
html;
        }

        echo <<< html
</table>
<p></p>

html;
    }

    $category_list = build_category_selection ($userdata['user'], $categories);

    $off = '<span style="color:red"><b>' . $ind144 . '</b></span>';
    $on = '<span style="color:green"><b>' . $ind143 . '</b></span>';

    $htmlcheck = ( !$ht && (!isset ($use_wysiwyg) || !$use_wysiwyg) ) ? $off : $on;
    $bbcheck = ( !$bb ) ? $off : $on;
    $smilcheck = ( !$smilies ) ? $off : $on;
    
    $security_fields = get_form_security();

    echo <<< html
<form action="?id=post" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>
            $security_fields
            $ind119
        </td>
		<td>{$userdata['nick']}</td>
		<td rowspan="4">
			$ind121<br />
			- HTML $ind122 $htmlcheck<br />
			- BBCode $ind122 $bbcheck<br />
			- Smilies $ind122 $smilcheck
		</td>
	</tr>
	<tr>
		<td><label for="post_subject">$ind35</label></td>
		<td><input type="text" class="post" id="post_subject" name="post_subject" value="$subject" style="width:95%" /></td>
	</tr>
	<tr>
		<td><label for="description">$ind258</label></td>
		<td><input type="text" class="post" id="description" name="description" value="$description" style="width:95%" /></td>
	</tr>
	<tr>
		<td valign="top">$ind308</td>
		<td valign="middle">
            <div class="category-selection">
                $category_list
            </div>
		</td>
	</tr>
</table>
html;

    if ( $uploads_active )
    {
        echo <<<html
<p>
<a href="javascript:window_pop ('./upload.php', 'fusion_upload', 575, 505)">$ind270</a>&nbsp;&nbsp;&nbsp;
<a href="javascript:window_pop ('./upload.php?id=imagelist', 'fusion_view', 650, 500)">$ind271</a>
</p>

html;
    }

    echo <<<html
<table class="adminpanel">
	<tr>
		<th>$ind93</th>
	</tr>

html;

    $extras = ( !isset ($use_wysiwyg) || !$use_wysiwyg ) ? show_extras ('newsposting', 'news', $smilies, $bb) : '';

    $news = str_replace ("&br;", "\n", $news);
    echo <<< html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="news" name="news" rows="18" cols="75" style="width:95%">$news</textarea>
		</td>
	</tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th>$ind94</th>
	</tr>
html;
    $extras = ( !isset ($use_wysiwyg) || !$use_wysiwyg ) ? show_extras ('newsposting', 'fullnews', $smilies, $bb) : '';

    $fullnews = str_replace("&br;", "\n", $fullnews);
    echo <<< html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="fullnews" name="fullnews" cols="75" rows="18" style="width:95%">$fullnews</textarea>
		</td>
	</tr>
</table>
<p>
<input type="submit" id="com_Submit" name="com_Submit" class="mainoption" value="$ind15" />
<input type="submit" class="mainoption" value="$ind200" onclick="PreviewArticle ('$id', 'newsposting', -1)" />
<input type="reset" value="$ind16" />
</p>
</form>
html;

    if ( isset ($use_wysiwyg) && $use_wysiwyg )
    {
        $smiley_list = get_smiley_list();
        $smileys = '';
        $separator = '';
        foreach ( $smiley_list as $smiley )
        {
            $smileys .= $separator . "'{$smiley['image']}'";
            $separator = ', ';
        }
        echo <<< html
<script type="text/javascript">
//<![CDATA[
(function()
{
    var settings = {
        customConfig: '',
        language: 'en',
        toolbar: [
            [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'TextColor', '-', 'Font', 'FontSize', 'Smiley', /*'Teletype', */, /*'Quote', */ ],
            '/',
            [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'HorizontalRule', 'BulletedList', 'NumberedList', '-', 'Flash', 'Image', 'Link', 'Unlink', '-', 'Source' ]
        ],
        smiley_path: '{$furl}/smillies/',
        smiley_images: [ {$smileys} ]
    };
    CKEDITOR.replace ('news', settings);
    CKEDITOR.replace ('fullnews', settings);
})();
//]]>
</script>
html;
    }
}
else if ( $id == 'delposts' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $delpost = ( isset ($PVARS['delpost']) ) ? $PVARS['delpost'] : array();

    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $lines_to_delete = array();
    
    foreach ( $file as $line => $news_post )
    {
        $toc = get_line_data ('news_toc', $news_post);
        if ( !has_access (NEWS_EDITOR) && $toc['author'] != $userdata['user'] )
        {
            // Can't delete someone else's post if your user level is a news writer.
            continue;
        }

        if ( in_array ($toc['news_id'], $delpost) )
        {
            $lines_to_delete[] = $line;
            @unlink (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        }
    }
    
    foreach ( $lines_to_delete as $line )
    {
        unset ($file[$line]);
    }

    array_unshift ($file, DENIED_MSG);
    $data = @implode ('', $file);
    safe_write ('news/toc.php', 'wb', $data);

    $title = $ind123;
    echo make_redirect ($ind124, '?id=editposts', $ind124a);
}
else if ( $id == 'editposts' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind95;

    $cid = ( isset ($GVARS['category']) ) ? (int)$GVARS['category'] : 0;
    $auth = ( isset ($GVARS['auth']) && !empty ($GVARS['auth']) ) ? urldecode ($GVARS['auth']) : '';
    $before = ( isset ($GVARS['before']) ) ? (int)$GVARS['before'] : 0;
    $after = ( isset ($GVARS['after']) ) ? (int)$GVARS['after'] : 0;

    $pre_d = ( isset ($GVARS['pre_d']) ) ? (int)$GVARS['pre_d'] : 18;
    $pre_m = ( isset ($GVARS['pre_m']) ) ? (int)$GVARS['pre_m'] : 1;
    $pre_y = ( isset ($GVARS['pre_y']) ) ? (int)$GVARS['pre_y'] : 2038;

    $post_d = ( isset ($GVARS['post_d']) ) ? (int)$GVARS['post_d'] : 1;
    $post_m = ( isset ($GVARS['post_m']) ) ? (int)$GVARS['post_m'] : 1;
    $post_y = ( isset ($GVARS['post_y']) ) ? (int)$GVARS['post_y'] : 1970;

    if ( $pre_d > 18 && $pre_m > 1 && $pre_y >= 2038 )
    {
        $pre_d = 18;
        $pre_m = 1;
        $pre_y = 2038;
    }

    if ( $post_d > 18 && $post_m > 1 && $post_y >= 2038 )
    {
        $post_d = 18;
        $post_m = 1;
        $post_y = 2038;
    }

    if ( $pre_d < 1 && $pre_m < 1 && $pre_y < 1970 )
    {
        $pre_d = 1;
        $pre_m = 1;
        $pre_y = 1970;
    }

    if ( $post_d < 1 && $post_m < 1 && $post_y < 1970 )
    {
        $post_d = 1;
        $post_m = 1;
        $post_y = 1970;
    }

    $pre_date = mktime (0, 0, 0, $pre_m, $pre_d, $pre_y);
    $post_date = mktime (0, 0, 0, $post_m, $post_d, $post_y);

    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);

    $user_dropdown = '<select name="auth"><option value="">' . $ind293 . '</option>';
    foreach ( $file as $user )
    {
        $user = get_line_data ('users', $user);
        if ( !has_access (NEWS_EDITOR) && $user['username'] != $userdata['user'] )
        {
            continue;
        }

        $user_dropdown .= '<option value="' . $user['username'] . '"' . (( $user['username'] === $auth ) ? ' selected="selected"' : '') . '>' . $user['nickname'] . '</option>';
    }
    $user_dropdown .= '</select>';

    $category_dropdown = build_category_dropdown ($userdata['user'], $cid);
    $category_dropdown = str_replace ('<option value="1"', '<option value="0">' . $ind293 . '</option><option value="1"', $category_dropdown);

    $dd1_dropdown = '<select name="pre_d" title="' . $ind51 . '">';
    $dd2_dropdown = '<select name="post_d" title="' . $ind51 . '">';
    for ( $i = 1; $i <= 31; $i++ )
    {
        $dd1_dropdown .= '<option' . ( $pre_d == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
        $dd2_dropdown .= '<option' . ( $post_d == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    }
    $dd1_dropdown .= '</select>';
    $dd2_dropdown .= '</select>';

    $mm1_dropdown = '<select name="pre_m" title="' . $ind52 . '">';
    $mm2_dropdown = '<select name="post_m" title="' . $ind52 . '">';
    for ( $i = 1; $i <= 12; $i++ )
    {
        $mm1_dropdown .= '<option' . ( $pre_m == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
        $mm2_dropdown .= '<option' . ( $post_m == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    }
    $mm1_dropdown .= '</select>';
    $mm2_dropdown .= '</select>';

    $yy1_dropdown = '<select name="pre_y" title="' . $ind85 . '">';
    $yy2_dropdown = '<select name="post_y" title="' . $ind85 . '">';

    for ( $i = 2038; $i >= 1970; $i-- )
    {
        $yy1_dropdown .= '<option' . ( $pre_y == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
        $yy2_dropdown .= '<option' . ( $post_y == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    }
    $yy1_dropdown .= '</select>';
    $yy2_dropdown .= '</select>';

    echo <<< html
<form method="get" action="">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="4">$ind175</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4">
				<input type="hidden" name="id" value="editposts" />
				<input type="submit" class="mainoption" value="$ind125" />
				<input type="button" class="mainoption" onclick="window.location='?id=editposts'" value="$ind399" />
				<input type="reset" value="$ind16" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>$ind241</td>
			<td>$user_dropdown</td>
			<td>$ind308</td>
			<td>$category_dropdown</td>
		</tr>
		<tr>
			<td>$ind209</td>
			<td>$dd2_dropdown$mm2_dropdown$yy2_dropdown</td>
			<td>$ind214</td>
			<td>$dd1_dropdown$mm1_dropdown$yy1_dropdown</td>
		</tr>
	</tbody>
</table>
</form>
html;

    $file = get_ordered_toc();

    $news_list = array();

    // Get valid news articles from the TOC file, particular to the user if needed.
    foreach ( $file as $value )
    {
        $toc = get_line_data ('news_toc', $value);

        if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
        {
            continue;
        }

        if ( $toc['timestamp'] > $pre_date || $toc['timestamp'] < $post_date )
        {
            continue;
        }

        $writer = get_author ($toc['author']);
        if ( (!has_access (NEWS_EDITOR) && ($writer['user'] != $userdata['user'])) || ($auth != '' && $writer['user'] != $auth) )
        {
            continue;
        }

        $categories = explode (',', $toc['categories']);
        if ( $cid > 0 && !in_array ($cid, $categories) )
        {
            continue;
        }

        if ( check_category_access ($userdata['user'], $categories) != NULL )
        {
            continue;
        }

        $news_list[] = array (
            'id' => $toc['news_id'],
            'timestamp' => $toc['timestamp'],
            'writer' => $writer === false ? $toc['author'] : $writer['nick'],
            'subject' => $toc['headline'],
            'categories' => $toc['categories']
        );
    }

    $page = ( isset ($GVARS['page']) ) ? intval ($GVARS['page']) : 1;
    $page = ( $page <= 0 && $page != -1 ) ? -1 : $page;

    $num_news = sizeof ($news_list);
    $news_per_page = 20;
    $num_pages = ceil ($num_news / $news_per_page);

    $pos = $news_per_page * ($page - 1);

    $search_criteria = '&amp;auth=' . $auth . '&amp;category=' . $cid . '&amp;post_d=' . $post_d . '&amp;post_m=' . $post_m . '&amp;post_y=' . $post_y . '&amp;pre_d=' . $pre_d . '&amp;pre_m=' . $pre_m . '&amp;pre_y=' . $pre_y;
    $page_list = '<p style="text-align:right">' . $ind292 . ': ';
    for ( $i = 0; $i < $num_pages; $i++ )
    {
        $upper_limit = ($i + 1) * $news_per_page;
        $upper_limit = ( $upper_limit > $num_news || $page == -1 ) ? $num_news : $upper_limit;

        if ( $page == ($i + 1) )
        {
            $page_list .= ($i * $news_per_page) + 1 . ' - ' . $upper_limit;
        }
        else
        {
            $page_list .= '<a href="?id=editposts&amp;page=' . ($i + 1) . $search_criteria . '">' . (($i * $news_per_page) + 1) . ' - ' . $upper_limit . '</a>';
        }

        $page_list .= ', ';
    }

    if ( $page == -1 )
    {
        $page_list .= '<b>' . $ind293 . '</b></p>';
    }
    else
    {
        $page_list .= '<a href="?id=editposts&amp;page=-1' . $search_criteria . '"><b>' . $ind293 . '</b></a></p>';
    }

    echo
<<< html
$page_list
<form method="post" id="deleteform" action="?id=delposts">
<table class="adminpanel">
	<tr>
		<th style="width:10%; text-align:center">$ind97</th>
		<th style="width:35%">$ind35</th>
		<th style="width:15%">$ind241</th>
		<th style="width:10%; text-align:center">$ind81</th>
		<th style="width:30%">$ind96</th>
	</tr>
html;

    $num_comments = 0;
    $pos = ( $page == -1 ) ? 0 : $pos;
    $limit = ( $page == -1 ) ? $num_news : (( ($pos + $news_per_page) > $num_news ) ? $num_news : $pos + $news_per_page);
    for ( $i = $pos; $i < $limit; $i++ )
    {
        $news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_list[$i]['id'] . '.php');
        $article = get_line_data ('news', $news_file[1]);

        $date = date ('Y-m-d H:i:s T', $news_list[$i]['timestamp']);

        $subject = html_entity_decode ($news_list[$i]['subject']);
        $subject = ( utf8_strlen ($subject) > 35 ) ? utf8_substr ($subject,0,35) . '...' : $subject;
        $comment_link = ( has_access (NEWS_EDITOR) ) ? '<a href="?id=editcomments&amp;news_id=' . $news_list[$i]['id'] . '">' . $article['numcomments'] . '</a>' : $article['numcomments'];
        echo
<<< html
	<tr>
		<td style="text-align: center">
			<input class="post" type="checkbox" id="delpost_{$news_list[$i]['id']}" name="delpost[{$news_list[$i]['id']}]" value="{$news_list[$i]['id']}" onclick="javascript:check_if_selected ('deleteform')" />
		</td>
		<td>
			<a href="?id=editposts2&amp;num={$news_list[$i]['id']}">$subject</a>
		</td>
		<td>
			{$news_list[$i]['writer']}
		</td>
		<td align="center">
			$comment_link
		</td>
		<td>
			$date
		</td>
	</tr>
html;
    }

    if ( $i == $pos )
    {
        echo
<<< html
	<tr>
		<td align="center" colspan="5">$ind282</td>
	</tr>
html;
    }
    
    $security_fields = get_form_security();

    echo <<< html
</table>
$page_list
<p>
    <a href="javascript:un_check_all ('deleteform', true)">$ind44</a> | <a href="javascript:un_check_all ('deleteform', false)">$ind44a</a>
    $security_fields
</p>
<p><input class="mainoption" type="submit" disabled="disabled" id="delete" name="delete" value="$ind126" /> <label for="delete">$ind127</label></p>
html;

	echo '</form>';
}
else if ( $id == 'editposts2' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $num = ( isset ($GVARS['num']) ) ? (int)$GVARS['num'] : 0;
    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php') )
    {
        trigger_error ($error4, E_USER_WARNING);
    }

    $file = file(FNEWS_ROOT_PATH . 'news/news.' . $num . '.php');
    $article = get_line_data ('news', $file[1]);

    if ( !has_access (NEWS_EDITOR) && $article['author'] != $userdata['user'] )
    {
        // This is a news reporter, trying to edit an article which
        // he/she didn't post.
        trigger_error ($error14, E_USER_WARNING);
    }
    
    if ( ($category_name = check_category_access ($userdata['user'], explode (',', $article['categories']))) !== NULL )
    {
        trigger_error (sprintf ($ind185, $category_name), E_USER_WARNING);
    }

    $title = $ind95;

    $shortnews = $article['shortnews'];
    $fullnews = $article['fullnews'];
    $subject = $article['headline'];
    $description = $article['description'];
    $timestamp = $article['timestamp'];
    $categories = explode (',', $article['categories']);

    $writer = get_author ($article['author']);
    $writer = $writer === false ? $article['author'] : $writer['nick'];
    
    $action = ( isset ($GVARS['action']) ) ? $GVARS['action'] : '';
    
    if ( isset ($use_wysiwyg) && $use_wysiwyg )
    {
        echo '<script type="text/javascript" src="ckeditor/ckeditor.js"></script>';
    }

    if ( $action == 'preview' )
    {
        $shortnews = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
        $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
        $description = ( isset ($PVARS['description']) ) ? fn_trim ($PVARS['description']) : '';
        $subject = ( isset ($PVARS['subject']) ) ? fn_trim ($PVARS['subject']) : '';
        $categories = ( isset ($PVARS['category']) ) ? $PVARS['category'] : $categories;

        $day = ( isset ($PVARS['edit_day']) ) ? (int)$PVARS['edit_day'] : 0;
        $month = ( isset ($PVARS['edit_month']) ) ? (int)$PVARS['edit_month'] : 0;
        $year = ( isset ($PVARS['edit_year']) ) ? (int)$PVARS['edit_year'] : 0;
        $sec = ( isset ($PVARS['edit_sec']) ) ? (int)$PVARS['edit_sec'] : 0;
        $min = ( isset ($PVARS['edit_min']) ) ? (int)$PVARS['edit_min'] : 0;
        $hour = ( isset ($PVARS['edit_hour']) ) ? (int)$PVARS['edit_hour'] : 0;

        $timestamp = mktime ($hour, $min, $sec, $month, $day, $year);

        $preview_shortnews = format_message ($shortnews, $ht || (isset ($use_wysiwyg) && $use_wysiwyg), $bb, $smilies, $wfpost);
        $preview_fullnews = format_message ($fullnews, $ht || (isset ($use_wysiwyg) && $use_wysiwyg), $bb, $smilies, $wfpost);
        
        if ( !$ht && (!isset ($use_wysiwyg) || !$use_wysiwyg) )
        {
            $preview_shortnews = str_replace ("\n", '<br />', $preview_shortnews);
            $preview_fullnews = str_replace ("\n", '<br />', $preview_fullnews);
        }
        else
        {
            // Need to be a bit smarter about new lines.
            $preview_shortnews = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $preview_shortnews);
            $preview_fullnews = preg_replace ("#([^>\s])(\n\s*){2,}([^<])#m", '$1<br /><br />$3', $preview_fullnews);
        }

        echo <<< html
<h2>$ind200</h2>
<table class="adminpanel">
	<tr>
		<th>$ind128</th>
	</tr>
	<tr>
		<td>$preview_shortnews</td>
	</tr>
html;

        if ( $preview_fullnews != '' )
        {
            echo <<< html
	<tr>
		<th>$ind94</th>
	</tr>
	<tr>
		<td>$preview_fullnews</td>
	</tr>
html;
        }

        echo <<< html
</table>
<p></p>

html;
    }
    else
    {
        $shortnews = str_replace ('<br />', "\n", $shortnews);
        $fullnews = str_replace ('<br />', "\n", $fullnews);
        $shortnews = str_replace ('&br;', "\n", $shortnews);
        $fullnews = str_replace ('&br;', "\n", $fullnews);
    }

    $category_list = build_category_selection ($userdata['user'], $categories);

    $off = '<span style="color:red"><b>' . $ind144 . '</b></span>';
    $on = '<span style="color:green"><b>' . $ind143 . '</b></span>';
    $htmlcheck = ( !$ht && (!isset ($use_wysiwyg) || !$use_wysiwyg) ) ? $off : $on;
    $bbcheck = ( !$bb ) ? $off : $on;
    $smilcheck = ( !$smilies ) ? $off : $on;

    echo <<<html
<form action="?id=savepost" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>$ind119</td>
		<td>$writer</td>
		<td rowspan="6">
			$ind121<br />
			- HTML $ind122 $htmlcheck<br />
			- BBCode $ind122 $bbcheck<br />
			- Smilies $ind122 $smilcheck
		</td>
	</tr>
html;
    $day_dropdown = '<select id="edit_day" name="edit_day">';
    $day_selected = (int)date ('j', $timestamp);
    for ( $i = 1; $i <= 31; ++$i )
    {
        if ( $day_selected == $i )
        {
            $day_dropdown .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
        }
        else
        {
            $day_dropdown .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }
    $day_dropdown .= '</select>';

    $month_dropdown = '<select id="edit_month" name="edit_month">';
    $month_selected = (int)date ('m', $timestamp);
    for ( $i = 1; $i <= 12; ++$i )
    {
        if ( $month_selected == $i )
        {
            $month_dropdown .= '<option value="' . $i . '" selected="selected">' . $months[$i] . '</option>';
        }
        else
        {
            $month_dropdown .= '<option value="' . $i . '">' . $months[$i] . '</option>';
        }
    }
    $month_dropdown .= '</select>';

    $year_dropdown = '<select id="edit_year" name="edit_year">';
    $year_selected = (int)date ('Y', $timestamp);
    for ( $i = 1990; $i <= 2037; ++$i )
    {
        if ( $year_selected == $i )
        {
            $year_dropdown .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
        }
        else
        {
            $year_dropdown .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }
    $year_dropdown .= '</select>';

    $hour_dropdown = '<select id="edit_hour" name="edit_hour">';
    $hour_selected = (int)date ('G', $timestamp);
    for ( $i = 0; $i < 24; ++$i )
    {
        if ( $hour_selected == $i )
        {
            $hour_dropdown .= '<option value="' . $i . '" selected="selected">' . sprintf ('%02d', $i) . '</option>';
        }
        else
        {
            $hour_dropdown .= '<option value="' . $i . '">' . sprintf ('%02d', $i) . '</option>';
        }
    }
    $hour_dropdown .= '</select>';

    $min_dropdown = '<select id="edit_min" name="edit_min">';
    $min_selected = (int)date ('i', $timestamp);
    for ( $i = 0; $i < 60; ++$i )
    {
        if ( $min_selected == $i )
        {
            $min_dropdown .= '<option value="' . $i . '" selected="selected">' . sprintf ('%02d', $i) . '</option>';
        }
        else
        {
            $min_dropdown .= '<option value="' . $i . '">' . sprintf ('%02d', $i) . '</option>';
        }
    }
    $min_dropdown .= '</select>';

    $sec_dropdown = '<select id="edit_sec" name="edit_sec">';
    $sec_selected = (int)date ('s', $timestamp);
    for ( $i = 0; $i < 60; ++$i )
    {
        if ( $sec_selected == $i )
        {
            $sec_dropdown .= '<option value="' . $i . '" selected="selected">' . sprintf ('%02d', $i) . '</option>';
        }
        else
        {
            $sec_dropdown .= '<option value="' . $i . '">' . sprintf ('%02d', $i) . '</option>';
        }
    }
    $sec_dropdown .= '</select>';

    echo <<<html
	<tr>
		<td><label for="subject">$ind35</label></td>
		<td><input type="text" class="post" id="subject" name="subject" value="$subject" style="width:95%" /></td>
	</tr>
	<tr>
		<td><label for="description">$ind258</label></td>
		<td><input type="text" class="post" id="description" name="description" value="$description" style="width:95%" /></td>
	</tr>
	<tr>
		<td>$ind87</td>
		<td>$month_dropdown$day_dropdown$year_dropdown</td>
	</tr>
	<tr>
		<td>$ind291</td>
		<td>$hour_dropdown:$min_dropdown:$sec_dropdown</td>
	</tr>
	<tr>
		<td valign="top">$ind308</td>
		<td>
			<div class="category-selection">$category_list</div>
		</td>
	</tr>
</table>
html;
    if ( $uploads_active )
    {
        echo <<<html
<p>
<a href="javascript:window_pop ('./upload.php', 'fusion_upload', 460, 275)">$ind270</a>&nbsp;&nbsp;&nbsp;
<a href="javascript:window_pop ('./upload.php?id=imagelist', 'fusion_view', 550, 500)">$ind271</a>
</p>
html;
    }

    echo <<<html
<table class="adminpanel">
	<tr>
		<th>$ind93</th>
	</tr>
html;
    $extras = ( isset ($use_wysiwyg) && $use_wysiwyg ) ? '' : show_extras ('newsposting', 'news', $smilies, $bb);
    echo <<<html
	<tr>
		<td align="center">
			$extras
            <textarea class="post" id="news" name="news" rows="18" cols="75" style="width:95%">$shortnews</textarea>
		</td>
	</tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th>$ind94</th>
	</tr>
html;
    $extras = ( isset ($use_wysiwyg) && $use_wysiwyg ) ? '' : show_extras ('newsposting', 'fullnews', $smilies, $bb);
    echo <<<html
	<tr>
		<td align="center">
			$extras
			<textarea class="post" id="fullnews" name="fullnews" rows="18" cols="75" style="width:95%">$fullnews</textarea>
		</td>
	</tr>
</table>
<p>
<input type="checkbox" id="del" class="del" value="1" name="del" /> <label for="del">$ind97</label><br /><br />
<input type="hidden" id="num" name="num" value="$num" />
<input type="hidden" id="date" name="date" value="$timestamp" />
<input type="submit" class="mainoption" value="$ind174" />
<input type="submit" class="mainoption" value="$ind200" onclick="PreviewArticle ('$id', 'newsposting', $num)" />
<input type="reset" value="$ind16" />
</p>
html;

    echo get_form_security() . '</form>';
    
    if ( isset ($use_wysiwyg) && $use_wysiwyg )
    {
        $smiley_list = get_smiley_list();
        $smileys = '';
        $separator = '';
        foreach ( $smiley_list as $smiley )
        {
            $smileys .= $separator . "'{$smiley['image']}'";
            $separator = ', ';
        }
        echo <<< html
<script type="text/javascript">
//<![CDATA[
(function()
{
    var settings = {
        customConfig: '',
        language: 'en',
        toolbar: [
            [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'TextColor', '-', 'Font', 'FontSize', 'Smiley', /*'Teletype', */, /*'Quote', */ ],
            '/',
            [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'HorizontalRule', 'BulletedList', 'NumberedList', '-', 'Flash', 'Image', 'Link', 'Unlink', '-', 'Source' ]
        ],
        smiley_path: '{$furl}/smillies/',
        smiley_images: [ {$smileys} ]
    };
    CKEDITOR.replace ('news', settings);
    CKEDITOR.replace ('fullnews', settings);
})();
//]]>
</script>
html;
    }
}
elseif ( $id == 'post' )
{ /*id Post*/
	if( !has_access( NEWS_REPORTER ))
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
    $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
    $subject = ( isset ($PVARS['post_subject']) ) ? fn_trim (single_line ($PVARS['post_subject'])) : '';
    $description = ( isset ($PVARS['description']) ) ? fn_trim (single_line ($PVARS['description'])) : '';
    $category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();

    if ( !$subject || !$news )
    {
        trigger_error ($ind98, E_USER_WARNING);
    }

    if ( sizeof ($category) < 1 )
    {
        trigger_error ($ind309, E_USER_WARNING);
    }

    $cat_error = check_category_access ($userdata['user'], $category);
    if ( $cat_error )
    {
        trigger_error (sprintf ($ind310, $cat_error), E_USER_WARNING);
    }

    //date
    $date = time() + round (3600 * $userdata['offset']);
    // replace new lines
    $find = array ("\r\n", "\r", "\n");
    $replace = array ('&br;', '&br;', '&br;');

    $news = str_replace ($find, $replace, $news);
    $fullnews = str_replace ($find, $replace, $fullnews);
    $description = str_replace ($find, $replace, $description);

    //info
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    $data_toc = DENIED_MSG;

    $break = '';
    $tmp1 = 0;

    if ( isset ($file[0]) && $file[0] != '' )
    {
        list ($tmp1, $tmp2) = explode ('|<|', $file[0]);
        $break = "\n";
    }

    $tmp1++;

    $new_file = 'news/news.' . $tmp1 . '.php';

    // $cs_ being comma separated, like CSV = comma separated values
    $cs_categories = implode (',', $category);

    $data_toc .= $tmp1 . '|<|' . $date . '|<|' . $userdata['user'] . '|<|' . $subject . '|<|' . $cs_categories . '|<|' . $break;
    $data_toc .= implode ('', $file);

    $data = $news . '|<|' . $fullnews . '|<|' . $userdata['user'] . '|<|' . $subject . '|<|' . $description . '|<|' . $cs_categories . '|<|' . $date . '|<|0|<|' . $tmp1 . '|<|';

    safe_write ($new_file, 'wb', DENIED_MSG . $data);

    if ( !@is_writeable (FNEWS_ROOT_PATH . $new_file) )
    {
        if ( !@chmod (FNEWS_ROOT_PATH . $new_file, 0644) )
        {
            trigger_error ($error7, E_USER_WARNING);
        }
    }

    safe_write ('news/toc.php', 'wb', $data_toc);
    
    $title = $ind43;
    echo make_redirect ($ind100);
}
else if ( $id == 'savepost' )
{
	if ( !has_access (NEWS_REPORTER) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }
    
    $news = ( isset ($PVARS['news']) ) ? fn_trim ($PVARS['news']) : '';
    $fullnews = ( isset ($PVARS['fullnews']) ) ? fn_trim ($PVARS['fullnews']) : '';
    $subject = ( isset ($PVARS['subject']) ) ? fn_trim (single_line ($PVARS['subject'])) : '';
    $description = ( isset ($PVARS['description']) ) ? fn_trim (single_line ($PVARS['description'])) : '';
    $day = ( isset ($PVARS['edit_day']) ) ? intval ($PVARS['edit_day']) : 1;
    $month = ( isset ($PVARS['edit_month']) ) ? intval ($PVARS['edit_month']) : 1;
    $year = ( isset ($PVARS['edit_year']) ) ? intval ($PVARS['edit_year']) : 1970;
    $hour = ( isset ($PVARS['edit_hour']) ) ? intval ($PVARS['edit_hour']) : 0;
    $min = ( isset ($PVARS['edit_min']) ) ? intval ($PVARS['edit_min']) : 0;
    $sec = ( isset ($PVARS['edit_sec']) ) ? intval ($PVARS['edit_sec']) : 0;
    $num = ( isset ($PVARS['num']) ) ? intval ($PVARS['num']) : 0;
    $delete = (int)( isset ($PVARS['del']) );
    $category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();

    if ( !$subject || !$news )
    {
        trigger_error ($ind98, E_USER_WARNING);
    }
    
    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php') )
    {
        trigger_error ($com11, E_USER_WARNING);
    }
    
    if ( sizeof ($category) < 1 )
    {
        trigger_error ($ind309, E_USER_WARNING);
    }
    
    if ( $cat_error = check_category_access ($userdata['user'], $category) )
    {
        trigger_error (sprintf ($ind310, $cat_error), E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php');
    array_shift ($file);

    $data = DENIED_MSG;

    $article = get_line_data ('news', $file[0]);

    if ( $article['author'] != $userdata['user'] && !has_access (NEWS_EDITOR) )
    {
        trigger_error ($ind103, E_USER_WARNING);
    }
    
    if ( !$delete )
    {
        $news = str_replace ("\n", '&br;', $news);
        $fullnews = str_replace ("\n", '&br;', $fullnews);

        $date = mktime ($hour, $min, $sec, $month, $day, $year);
        if ( $date == -1 || $date === false )
        {
            $date = $news_date;
        }

        $cs_categories = implode (',', $category);
        $data .= $news . '|<|' . $fullnews . '|<|' . $article['author'] . '|<|' . $subject . '|<|' . $description . '|<|' . $cs_categories . '|<|' . $date . '|<|' . $article['numcomments'] . '|<|' . $num . '|<|' . "\n";

        // Skip the existing news data line.
        array_shift ($file);

        $data .= implode ('', $file);
        safe_write ('news/news.' . $num . '.php', 'wb', $data);

        $file = file (FNEWS_ROOT_PATH . '/news/toc.php');
        array_shift ($file);

        $data = DENIED_MSG;

        $update = false;
        foreach ( $file as $news_item )
        {
            $toc = get_line_data ('news_toc', $news_item);
            if ( $toc['news_id'] == $num )
            {
                $data .= $num . '|<|' . $date . '|<|' . $article['author'] . '|<|' . $subject . '|<|' . $cs_categories . '|<|' . "\n";
            }
            else
            {
                $data .= $news_item;
            }
        }

        safe_write ('news/toc.php', 'wb', $data);
    }
    else
    {
        $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
        array_shift( $file );

        $data = DENIED_MSG;

        foreach ( $file as $value )
        {
            $toc = get_line_data ('news_toc', $value);
            if( $toc['news_id'] != $num )
            {
                $data .= $value;
            }
            else
            {
                @unlink (FNEWS_ROOT_PATH . 'news/news.' . $num . '.php');
            }
        }

        safe_write ('news/toc.php', 'wb', $data);
    }

     //news posted
    $title = $ind101;
    echo make_redirect ($ind102, '?id=editposts', $ind124a);
}
else if ( $id == 'view')
{
	$title = $ind104;
	ob_start();
	include 'news.php';
	$news = ob_get_clean();

	ob_start();
	$fn_news_url = 'index.php?id=view';
	include 'headlines.php';
	$headlines = ob_get_clean();

	if ( !empty ($news) )
	{
		// html width
		$news = preg_replace ('/<table(\s?.+\s?)width=\"?[0-9]+\"?/mi', '<table\\1width="100%"', $news);
		// css width
		$news = preg_replace ('/<table(\s?.+\s?)width\:\s*[0-9a-z\-]+[%|px|em|pt]?/mi', '<table\\1width:100%', $news);
	}

	echo ( empty ($news) ) ? '<div style="text-align:center">' . $ind41 . '</div>' : $ind40 . '<p></p>' . $headlines . '<br /><br />' . $news;
}
else if ( $id == 'badwordfilter' )
{
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind215;

    $badwords = file (FNEWS_ROOT_PATH . 'badwords.php');
    array_shift ($badwords);
    $num_words = sizeof ($badwords);

    echo <<< html
<form action="?id=savebadwordfile" method="post">
<table class="adminpanel">
	<tr>
		<th>$ind97</th>
		<th>$ind27</th>
		<th>$ind28</th>
		<th>$ind29</th>
		<th>$ind130</th>
	</tr>
html;
    if ( $num_words > 0 )
    {
        foreach ( $badwords as $key => $rule )
        {
            $badword = get_line_data ('badwords', $rule);

            $case_sens = checkbox_checked ($badword['case_sensitive']);
            $radio_strict = checkbox_checked ($badword['type'], 0);
            $radio_loose = checkbox_checked ($badword['type'], 1);
            $radio_regex = checkbox_checked ($badword['type'], 2);
            echo <<< html
	<tr>
		<td style="text-align:center"><input type="checkbox" name="del[$key]" value="1" /></td>
		<td style="text-align:center"><input type="text" name="find[$key]" value="{$badword['find']}" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[$key]" value="{$badword['replace']}" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[$key]" value="1"$case_sens /></td>
		<td><input type="radio" name="type[$key]" value="0"$radio_strict /> $ind190<br /><input type="radio" name="type[$key]" value="1"$radio_loose /> $ind232<br /><input type="radio" name="type[$key]" value="2"$radio_regex /> $ind192b</td>
	</tr>
html;
        }
    }
    else
    {
        echo '<tr><td style="text-align:center" colspan="5">' . $ind273 . '</td></tr>';
    }

    echo <<< html
	<tr><th colspan="5"><input type="hidden" name="num_words" value="$num_words" /><input type="submit" class="mainoption" value="$ind174" /></th></tr>
</table>
html;

    echo get_form_security();

    echo <<< html
</form>
<h2>$ind237</h2>
<form action="?id=addbadwords" method="post">
<table class="adminpanel">
	<tr>
		<th>$ind27</th>
		<th>$ind28</th>
		<th>$ind29</th>
		<th>$ind130</th>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[0]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[0]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[0]" value="1" /></td>
		<td><input type="radio" name="type[0]" value="0" checked="checked" /> $ind190<br /><input type="radio" name="type[0]" value="1" /> $ind232<br /><input type="radio" name="type[0]" value="2" /> $ind192b</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[1]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[1]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[1]" value="1" /></td>
		<td><input type="radio" name="type[1]" value="0" checked="checked" /> $ind190<br /><input type="radio" name="type[1]" value="1" /> $ind232<br /><input type="radio" name="type[1]" value="2" /> $ind192b</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[2]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[2]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[2]" value="1" /></td>
		<td><input type="radio" name="type[2]" value="0" checked="checked" /> $ind190<br /><input type="radio" name="type[2]" value="1" /> $ind232<br /><input type="radio" name="type[2]" value="2" /> $ind192b</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[3]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[3]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[3]" value="1" /></td>
		<td><input type="radio" name="type[3]" value="0" checked="checked" /> $ind190<br /><input type="radio" name="type[3]" value="1" /> $ind232<br /><input type="radio" name="type[3]" value="2" /> $ind192b</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[4]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[4]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[4]" value="1" /></td>
		<td><input type="radio" name="type[4]" value="0" checked="checked" /> $ind190<br /><input type="radio" name="type[4]" value="1" /> $ind232<br /><input type="radio" name="type[4]" value="2" /> $ind192b</td>
	</tr>
	<tr><th colspan="4"><input type="submit" class="mainoption" value="$ind237" /></th></tr>
</table>
html;

	echo get_form_security() . '</form><p>' . $ind131 . '</p>';
}
else if ( $id == 'savebadwordfile' )
{
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $num_words = ( isset ($PVARS['num_words']) ) ? $PVARS['num_words'] : 0;
    $del = ( isset ($PVARS['del']) ) ? $PVARS['del'] : array();
    $find = ( isset ($PVARS['find']) ) ? array_map ('single_line', $PVARS['find']) : array();
    $replace = ( isset ($PVARS['replace']) ) ? array_map ('single_line', $PVARS['replace']) : array();
    $case_sens = ( isset ($PVARS['case_sens']) ) ? $PVARS['case_sens'] : array();
    $type = ( isset ($PVARS['type']) ) ? $PVARS['type'] : array();

    if ( sizeof ($case_sens) != $num_words )
    {
        for ( $i = 0; $i < $num_words; $i++ )
        {
            if ( !isset ($case_sens[$i]) )
            {
                $case_sens[$i] = 0;
            }
        }
        ksort ($case_sens);
    }

    $write = DENIED_MSG;
    for ( $i = 0; $i < $num_words; $i++ )
    {
        if ( isset ($del[$i]) )
        {
            continue;
        }

        $write .= $find[$i] . '|<|' . $replace[$i] . '|<|' . $case_sens[$i] . '|<|' . $type[$i] . '|<|' . "\n";
    }

    safe_write ('badwords.php', 'wb', $write);
    
    $title = $ind202;
    echo make_redirect ($ind202, '?id=badwordfilter', $ind235);
}
else if ( $id == 'addbadwords' )
{
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }
    
    $num_words = ( isset ($PVARS['num_words']) ) ? $PVARS['num_words'] : 0;
    $del = ( isset ($PVARS['del']) ) ? $PVARS['del'] : array();
    $find = ( isset ($PVARS['find']) ) ? array_map ('single_line', $PVARS['find']) : array();
    $replace = ( isset ($PVARS['replace']) ) ? array_map ('single_line', $PVARS['replace']) : array();
    $case_sens = ( isset ($PVARS['case_sens']) ) ? $PVARS['case_sens'] : array();
    $type = ( isset ($PVARS['type']) ) ? $PVARS['type'] : array();

    if ( sizeof ($case_sens) != 5 )
    {
        for ( $i = 0; $i < 5; $i++ )
        {
            if ( !isset ($case_sens[$i]) )
            {
                $case_sens[$i] = 0;
            }
        }
        ksort ($case_sens);
    }

    $write = '';
    for ( $i = 0; $i < 5; $i++ )
    {
        if ( empty ($find[$i]) || empty ($replace[$i]) )
        {
            continue;
        }

        $write .= $find[$i] . '|<|' . $replace[$i] . '|<|' . $case_sens[$i] . '|<|' . $type[$i] . '|<|' . "\n";
    }

    safe_write ('badwords.php', 'ab', $write);
    
    $title = $ind233;
    echo make_redirect ($ind234, '?id=badwordfilter', $ind235);
}

//-----------------
//modify, delete, ban comments/ips
else if ( $id == 'comments_manage')
{
	if ( !has_access (NEWS_EDITOR) )
    {
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind203;
    $banned = file (FNEWS_ROOT_PATH . 'banned.php');
    array_shift ($banned);
    $banned = @implode ('', $banned);

    echo <<< html
<h2>$ind132</h2>
<p>$ind133</p>
<form action="?id=banlist_update" method="post">
<div style="text-align:center">
<textarea id="bannedlist" name="bannedlist" class="post" cols="50" rows="5">$banned</textarea><br />
<input type="submit" class="mainoption" value="$ind174" />
</div>
html;

    echo get_form_security();
    echo <<< html
</form>
html;

    $file = get_ordered_toc();
    $unvalidated_list = array();
    $articles = array();

    foreach ( $file as $value )
    {
        $toc = get_line_data ('news_toc', $value);
        if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
        {
            continue;
        }

        $news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        array_shift ($news_file);
        $article = get_line_data ('news', $news_file[0]);

        $writer = get_author ($toc['author']);
        $articles[] = array (
            'numcomments' => $article['numcomments'],
            'news_id' => $toc['news_id'],
            'headline' => $toc['headline'],
            'writer' => $writer === false ? $toc['author'] : $writer['nick'],
            'date' => date ('Y-m-d H:i:s T', $toc['timestamp'])
        );

        if ( sizeof ($news_file) <= 1 )
        {
            // There are no comments (<= 1 because we still count the news line) at all.
            continue;
        }

        // Get a list of unvalidated comments.
        array_shift ($news_file);

        $comments = array();
        foreach ( $news_file as $v2 )
        {
            $comment = get_line_data ('comments', $v2);
            if ( $comment['validated'] == 0 )
            {
                $comments[] = $comment;
            }
        }

        if ( sizeof ($comments) > 0 )
        {
            $unvalidated_list += array ($toc['headline'] => $comments);
        }
    }

    echo <<< html
<h2>$ind239</h2>
html;
    if ( sizeof ($unvalidated_list) <= 0 )
    {
        echo $ind242;
    }
    else
    {
        echo <<< html
<form method="post" action="?id=validatecomments" id="validating">
html;

        $br_replace = $comallowbr ? '<br />' : '';
        foreach ( $unvalidated_list as $subject => $comments )
        {
            if ( sizeof ($comments) > 0 )
            {
                echo '<h3>' . $subject . '</h3>';
                foreach ( $comments as $value )
                {
                    $date = date ('Y-m-d H:i:s T', $value['timestamp']);
                    $email = ( empty ($value['email']) ) ? $ind275 : $value['email'];
                    
                    $message = str_replace ('&br;', $br_replace, $value['message']);
                    $message = format_message ($message, $htc, $bbc, $smilcom, $wfcom);

                    echo <<< html
<table class="adminpanel">
	<tr>
		<th colspan="2">
			$ind119 <span style="font-weight: normal">{$value['author']} ({$value['ip']})</span>
			$ind6 <span style="font-weight: normal">$email</span>
			$ind96 <span style="font-weight: normal">$date</span>
		</th>
	</tr>
	<tr>
		<td style="width: 20px">
			<input type="checkbox" name="comid[{$value['comment_id']}]" id="comid_{$value['comment_id']}" onclick="javascript:check_if_selected ('validating')" value="{$value['comment_id']}" />
		</td>
		<td>
			$message
		</td>
	</tr>
</table><p></p>
html;
                }
            }
        }

        echo get_form_security();

        echo <<< html
<input type="submit" class="mainoption" disabled="disabled" value="$ind318" />&nbsp;
<input type="submit" class="mainoption" onclick="javascript:deleteComments(this.form);" disabled="disabled" value="$ind126" /></p>
</form>
<script type="text/javascript">
//<![CDATA[
function deleteComments ( form_object )
{
	form_object.action = '?id=deletecomments';
}
//]]>
</script>
html;
    }

    echo <<< html
<h2>$ind134</h2>
<p>$ind135</p>
<table class="adminpanel">
	<tr>
		<th style="width:8%">$ind81</th>
		<th>$ind35</th>
		<th>$ind5</th>
		<th>$ind96</th>
	</tr>

html;

    foreach ( $articles as $article )
    {
        echo
<<< html
	<tr>
		<td style="text-align:center">{$article['numcomments']}</td>
		<td><a href="?id=editcomments&amp;news_id={$article['news_id']}">{$article['headline']}</a></td>
		<td>{$article['writer']}</td>
		<td>{$article['date']}</td>
	</tr>

html;
    }

    echo <<< html
</table>
html;
}
else if ( $id == 'validatecomments' )
{
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }
    
    $comid = ( isset ($PVARS['comid']) ) ? $PVARS['comid'] : array();

    if ( sizeof ($comid) <= 0 )
    {
        trigger_error ('There are no comments to validate.', E_USER_ERROR);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    foreach ( $file as $newsfile )
    {
        $toc = get_line_data ('news_toc', $newsfile);

        if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
        {
            continue;
        }

        $newsfile = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        array_shift ($newsfile);

        $start = DENIED_MSG;

        $article = get_line_data ('news', $newsfile[0]);
        array_shift ($newsfile);

        $write = '';
        foreach ( $newsfile as $comment )
        {
            $comment = get_line_data ('comments', $comment);

            if ( in_array ($comment['comment_id'], $comid) )
            {
                $comment['validated'] = 1;
                ++$article['numcomments'];
            }

            $write .= implode ('|<|', $comment) . '|<|' . "\n";
        }

        $start .= implode ('|<|', $article) . '|<|' . "\n";
        $write = $start . $write;

        safe_write ('news/news.' . $toc['news_id'] . '.php', 'wb', $write);
    }
    
    $title = $ind146;
    echo make_redirect ($ind321, '?id=comments_manage', $ind334);
}
else if ( $id == 'deletecomments' )
{ // Delete unvalidated comments
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $comid = ( isset ($PVARS['comid']) ) ? $PVARS['comid'] : array();

    if ( sizeof ($comid) <= 0 )
    {
        trigger_error ($ind402, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    foreach ( $file as $newsfile )
    {
        $newsfile = explode ('|<|', $newsfile);
        $newsid = $newsfile[0];
        if ( file_exists (FNEWS_ROOT_PATH . 'news/news.' . $newsid . '.php') )
        {
            $newsfile = file (FNEWS_ROOT_PATH . 'news/news.' . $newsid . '.php');
            array_shift ($newsfile);

            $write = DENIED_MSG;

            $article = get_line_data ('news', $newsfile[0]);

            $write .= array_shift ($newsfile);

            foreach ( $newsfile as $comment )
            {
                $com = get_line_data ('comments', $comment);
                if ( in_array ($com['comment_id'], $comid) )
                {
                    continue;
                }

                $write .= $comment;
            }

            safe_write ('news/news.' . $newsid . '.php', 'wb', $write);
        }
    }

    $title = $ind146;
    echo make_redirect ($ind164, '?id=comments_manage', $ind334);
}

//-----------------
//save banned IP's
else if ( $id == "banlist_update")
{ /*id Banned Save*/
	if ( !has_access (NEWS_EDITOR) )
    {
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }
	
    $bannedlist = ( isset ($PVARS['bannedlist']) ) ? $PVARS['bannedlist'] : '';

    $banned_ip = explode ("\n", $bannedlist);

    $bannedlist = DENIED_MSG;
    $invalid_ip = false;
    foreach ( $banned_ip as $ip )
    {
        if ( !$ip )
        {
            continue;
        }

        if ( !strncmp ($ip, '10.', 3) ||
            !strncmp ($ip, '172.16.', 7) ||
            !strncmp ($ip, '192.168.', 8) )
        {
            trigger_error ($ind39, E_USER_WARNING);
        }

        $ip = explode ('.', $ip);

        if ( (sizeof ($ip) != 4) ||
            ($ip[0] < 0 || $ip[1] < 0 || $ip[2] < 0 || $ip[3] < 0) ||
            ($ip[0] > 255 || $ip[1] > 255 || $ip[2] > 255 || $ip[3] > 255) )
        {
            trigger_error ($ind299, E_USER_WARNING);
        }
        
        $bannedlist .= implode ('.', $ip) . "\n";
    }

    safe_write ('banned.php', 'wb', $bannedlist);
    
    $title = $ind137;
    echo make_redirect ($ind162, '?id=comments_manage', $ind334);
}

//--------------
else if ( $id == 'editcomments')
{ /*id Comments Edit*/
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $rand = ( isset ($GVARS['news_id']) ) ? (int)$GVARS['news_id'] : 0;

    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $rand . '.php') )
    {
        trigger_error ($com11, E_USER_WARNING);
    }

    $title = $ind203;

    echo <<< html
				$ind138<br />
				<form method="post" id="comments" action="?id=delete_comments">
				<table class="adminpanel">
					<tr>
						<th style="width:5%">$ind97</th>
						<th style="width:55%">$ind403</th>
						<th style="width:15%">$ind139</th>
						<th style="width:20%">$ind96</th>
						<th style="width:5%">$ind278</th>
					</tr>
html;
    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $rand . '.php');
    array_shift ($file);
    array_shift ($file);

    $found = false;
    foreach ( $file as $value )
    {
        $comment = get_line_data ('comments', $value);

        if ( !$comment['validated'] )
        {
            continue;
        }

        $com_datum = date ('Y-m-d H:i:s T', $comment['timestamp']);
        $com_post = str_replace ('&br;', ($comallowbr ? '<br />' : ''), $comment['message']);
        $com_post = format_message ($com_post, $htc, $bbc, $smilcom, $wfcom);

        $ban_text = is_ip_banned ($comment['ip']) ? $ind396 : '';

        echo <<< html
	<tr>
		<td style="text-align:center">
			<input type="checkbox" class="post" id="delpost_{$comment['comment_id']}" name="delpost[{$comment['comment_id']}]" value="{$comment['comment_id']}" onclick="check_if_selected ('comments');">
		</td>
		<td>$com_post</td>
		<td>
			{$comment['author']}<br />
			<b>{$comment['ip']}</b> $ban_text
		</td>
		<td>$com_datum</td>
		<td style="text-align:center">
			[<a href="?id=editcomment&amp;comment_id={$comment['comment_id']}&amp;news_id=$rand">$ind30b</a>]
		</td>
	</tr>
html;

         $found = true;
    }

    if ( !$found )
    {
        echo
<<< html
		<tr>
			<td align="center" colspan="5">$ind283</td>
		</tr>
html;
    }

    $disabled = ( !$found ) ? ' disabled="disabled"' : '';

    echo '</table>';
    echo get_form_security();
    echo "<p><a href=\"javascript:un_check_all ('comments', true)\">$ind44</a> | <a href=\"javascript:un_check_all ('comments', false)\">$ind44a</a></p>
    <p><input type=\"hidden\" id=\"rand\" name=\"rand\" value=\"$rand\" />
    <input class=\"mainoption\" type=\"submit\" disabled=\"disabled\" id=\"delete\" name=\"delete\" value=\"$ind126\" /></p></form>";
}

//--------------
else if ( $id == 'editcomment')
{ /*id Comment Edit*/
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $news_id	= ( isset ($GVARS['news_id']) ) ? (int)$GVARS['news_id'] : 0;
    $comment_id	= ( isset ($GVARS['comment_id']) ) ? $GVARS['comment_id'] : 0;

    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php') )
    {
        trigger_error ($com11, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);
    array_shift ($file);

    foreach ( $file as $value )
    {
        $comment = get_line_data ('comments', $value);

        if ( $comment_id == $comment['comment_id'] )
        {
            $email = $comment['email'] == '' ? $ind141 : $comment['email'];

            $message = str_replace ('&br;', "\n", $comment['message']);

            $no = '<span style="color:red; font-weight: bold">' . $ind144 .'</span>';
            $yes = '<span style="color:green; font-weight: bold">' . $ind143 .'</span>';

            $htmlcheck = ( !$htc ) ? $no : $yes;
            $bbcheck = ( !$bbc ) ? $no : $yes;
            $smilcheck = ( !$smilcom ) ? $no : $yes;

            $ban_text = is_ip_banned ($comment['ip']) ? $ind396 : '';

            $title = $ind134;
            echo <<< html
<form action="?id=updatecomment&amp;comment_id={$comment['comment_id']}&amp;news_id={$news_id}" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>$ind279</td>
		<td>{$comment['author']}</td>
		<td rowspan="3">
			$ind121<br />
			- HTML $ind122 $htmlcheck<br />
			- BBCode $ind122 $bbcheck<br />
			- Smilies $ind122 $smilcheck
		</td>
	</tr>
	<tr>
		<td>$ind6</td>
		<td>$email</td>
	</tr>
	<tr>
		<td>IP</td>
		<td>{$comment['ip']} $ban_text</td>
	</tr>
html;

            $extras = show_extras ('newsposting', 'comment', $smilcom, $bbc);
            echo <<<html
	<tr>
		<td colspan="3">
			<div style="text-align:center">$extras
			<textarea class="post" name="comment" id="comment" rows="15" cols="75">$message</textarea></div>
			<p><label for="del">$ind97</label> <input type="checkbox" class="post" value="1" id="del" name="del" /></p>
			<p><input class="mainoption" type="submit" value="$ind174" /></p>
		</td>
	</tr>
</table>
html;
            $timestamp = time();
            echo get_form_security() . '</form>';

            break;
        }
    }
}

//-----------------
//edit it
else if ( $id == 'updatecomment')
{ /*id Comment Update*/
	if ( !has_access (NEWS_EDITOR) )
    {
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }
    
    $comment = ( isset ($PVARS['comment']) ) ? $PVARS['comment'] : '';
    $comment_id = ( isset ($PVARS['comment_id']) ) ? $PVARS['comment_id'] : 0;
    $news_id = ( isset ($PVARS['news_id']) ) ? (int)$PVARS['news_id'] : 0;
    $del = isset ($PVARS['del']);
    
    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php') )
    {
        trigger_error ($com11, E_USER_WARNING);
    }
            
    if ( !$comment )
    {
        trigger_error ($ind145, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);

    $start = DENIED_MSG;

    $article = get_line_data ('news', $file[0]);

    array_shift ($file);

    $data = NULL;
    foreach ( $file as $value )
    {
        $com = get_line_data ('comments', $value);
        if ( $comment_id == $com['comment_id'] )
        {
            if ( $del )
            {
                --$article['numcomments'];
                continue;
            }

            if ( $comallowbr )
            {
                $comment = str_replace ("\n", '&br;', $comment);
            }

            $com['message'] = $comment;
            $data .= implode ('|<|', $com) . '|<|' . "\n";
        }
        else
        {
            $data .= $value;
        }
    }

    $start .= implode ('|<|', $article) . '|<|' . "\n";
    $data = $start . $data;

    safe_write ('news/news.' . $news_id . '.php', 'wb', $data);
    
    $title = $ind146;
    echo make_redirect ($ind147, '?id=comments_manage', $ind334);
}

//--------------
else if ( $id == 'delete_comments')
{ /*id Comment Delete*/
	if ( !has_access (NEWS_EDITOR) )
    {
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $delpost = ( isset ($PVARS['delpost']) ) ? $PVARS['delpost'] : array();
    $news_id = ( isset ($PVARS['rand']) ) ? (int)$PVARS['rand'] : 0;

    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php') )
    {
        trigger_error ($com11, E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);

    $start = DENIED_MSG;

    $article = get_line_data ('news', $file[0]);
    array_shift ($file);

    $data = NULL;
    $dc = 0;
    foreach ( $file as $comment_data )
    {
        $comment = get_line_data ('comments', $comment_data);
        if ( in_array ($comment['comment_id'], $delpost) )
        {
            ++$dc;
            --$article['numcomments'];
            continue;
        }

        $data .= $comment_data;
    }

    $start .= implode ('|<|', $article) . '|<|' . "\n";
    $data = $start . $data;

    safe_write ('news/news.' . $news_id . '.php', 'wb', $data);

    $title = $ind150;
    echo make_redirect ($ind151 . ' ' . $dc . ' ' . $ind151a, '?id=comments_manage', $ind334);
}

elseif ( $id == 'uploads' )
{ /*id Uploads*/
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $title = $ind204;

    if ( strtolower (@ini_get ('file_uploads')) == 'off' || @ini_get ('file_uploads') == 0 || @ini_get ('file_uploads') == '' )
    {
        echo $ind105;

        if ( $uploads_active )
        {
            $configs = config_array();

            $configs['uploads_active'] = '';
            save_config ($configs);
        }
    }
    else
    {
        $upload_checkbox = checkbox_checked ($uploads_active);
        $security_fields = get_form_security();
    
        echo <<< html
<form method="post" action="?id=saveuploads">
$security_fields
<p>
    <input class="post" type="checkbox" id="uploads_active" name="uploads_active" value="1" $upload_checkbox /> <label for="uploads_active">$ind220</label>
</p>
<p>
    $ind221 <input class="post" type="text" id="uploads_ext" name="uploads_ext" value="$uploads_ext" size="40" /><br />
    $ind222
</p>
<p>
    $ind223 <input class="post" type="text" id="uploads_size" name="uploads_size" value="$uploads_size" size="25" /><br />
    $ind224
</p>
<p>
    <input class="mainoption" type="submit" value="$ind36" />
</p>
</form>
html;

        if ( !($dir = @opendir ('uploads')) )
        {
            trigger_error ($ind230, E_USER_WARNING);
        }
        
        echo <<< html
<form id="uploaded_images" method="post" action="?id=deluploads">
$security_fields
<table class="adminpanel">
    <thead>
        <tr>
            <th style="width:5%">$ind97</th>
            <th style="width:45%">$ind226</th>
            <th style="width:15%">$ind227</th>
            <th style="width:35%">$ind228</th>
        </tr>
    </thead>
    <tbody>
html;
            
        $directory_size = 0;
        while ( ($file = readdir ($dir)) !== false )
        {
            if ( $file == '.' || $file == '..' )
            {
                continue;
            }

            if ( !preg_match ('/^(.+)\.' . $uploads_ext . '$/', strtolower ($file)) )
            {
                continue;
            }
            
            $image_path = FNEWS_ROOT_PATH . 'uploads/' . $file;
            $image_url = 'uploads/' . $file;

            $image_filesize = filesize ($image_path);
            $directory_size += $image_filesize;
            
            $image_size = getimagesize ($image_path);
            $popup_width = $image_size[0] + 20;
            $popup_height = $image_size[1] + 20;
            $filesize_text = calc_size ($image_filesize);
            $image_uploaded_date = date ("Y-m-d H:i:s T", filemtime ($image_path));
            
            echo <<< html
        <tr>
            <td style="text-align:center">
                <input class="post" type="checkbox" id="del_files_$file" name="del_files[$file]" value="$file" onclick="javascript:check_if_selected('uploaded_images')" />
            </td>
            <td>
                <a href="$image_url" onclick="window.open(this.href,'','height=$popup_height,width=$popup_width,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'); return false">$file</a>
            </td>
            <td>$filesize_text</td>
            <td>$image_uploaded_date</td>
        </tr>
html;
        }
        
        closedir ($dir);

        if ( $directory_size == 0 )
        {
            echo
<<< html
<tr>
	<td style="text-align:center" colspan="4">$ind300</td>
</tr>
html;
        }

        $directorysize_text = calc_size ($directory_size);
        
        echo <<< html
    </tbody>
</table>
<p>
    <a href="javascript:un_check_all ('uploaded_images', true)">$ind44</a> | <a href="javascript:un_check_all ('uploaded_images', false)">$ind44a</a>
</p>
<p>
    <input class="mainoption" id="delete" name="delete" disabled="disabled" type="submit" value="$ind97" />   <b>$ind264 $directorysize_text</b>
</p>
</form>
html;
	}
}

else if ( $id == 'deluploads' )
{
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $delpost = ( isset ($PVARS['del_files']) ) ? $PVARS['del_files'] : array();

    $title = $ind289;
    
    $forbidden_exts = array ('php', 'html');
    
    ob_start();
    foreach ( $delpost as $key => $filename )
    {
        $error = false;
        
        if ( !$error && strpos ($filename, '/') !== false && strpos ($filename, '\\') !== false )
        {
            $error = true;
        }
        
        if ( !$error && in_array (get_file_extension ($filename), $forbidden_exts) )
        {
            $error = true;
        }

        if ( !$error && !@unlink (FNEWS_ROOT_PATH . 'uploads/' . $filename) )
        {
            $error = true;
        }
        
        if ( !$error )
        {
            echo "'$filename' $ind231<br />";
        }
        else
        {
            echo "$ind229 '$filename'<br />";
        }
    }

    echo make_redirect (ob_get_clean(), '?id=uploads', $ind338);
}

else if ( $id == 'saveuploads')
{ /*id Uploads Save Config*/
	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $uploads_size = ( isset ($PVARS['uploads_size']) ) ? (int)$PVARS['uploads_size'] : 0;
    $uploads_active = (int)(isset($PVARS["uploads_active"]));
    $uploads_extensions = ( isset ($PVARS['uploads_ext']) ) ? single_line ($PVARS['uploads_ext']) : '';

    if ( !preg_match ('/^[A-Za-z0-9_\|]+$/', $uploads_extensions) )
    {
        trigger_error ($ind295, E_USER_WARNING);
    }

    $exts = explode ('|', $uploads_extensions);
    $good_exts = array();
    for ( $i = 0, $num_exts = sizeof ($exts); $i < $num_exts; ++$i )
    {
        if ( $exts[$i] && !in_array ($exts[$i], $good_exts) )
        {
            $good_exts[] = $exts[$i];
        }
    }
    
    $uploads_extensions = implode ('|', $good_exts);

    $configs = config_array();

    $configs['uploads_size'] = $uploads_size;
    $configs['uploads_active'] = $uploads_active;
    $configs['uploads_ext'] = $uploads_extensions;

    save_config ($configs);

    $title = $ind21;
    echo make_redirect ($ind22, '?id=uploads', $ind338);
}

//-----------------
//update info + general help
else if ( $id == 'help')
{ /*id Help*/
	if ( !has_access (NEWS_REPORTER) )
    {
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    $message = '';
    $version = '';
    $title = $ind152;

    if ( $fp = @fsockopen ('www.fusionnews.net', 80, $errno, $errstr, 10) )
    {
        $out = 'GET /version/fnews_version.txt HTTP/1.1' . "\r\n";
        $out .= 'Host: www.fusionnews.net' . "\r\n";
        $out .= 'Connection: close' . "\r\n\r\n";

        @fputs ($fp, $out);
        $getinfo = false;

        while ( !@feof ($fp) )
        {
            if ( $getinfo )
            {
                $version .= @fread ($fp, 1024);
            }
            else
            {
                if ( @fgets ($fp, 1024) == "\r\n" )
                {
                    $getinfo = true;
                }
            }
        }
        @fclose ($fp);

        $message = version_compare ($version, FNEWS_CURVE) > 0 ? $ind303 : $ind304;
    }
    else
    {
        $message = $ind401;
    }

    $current_version = FNEWS_CURVE;
    echo <<< html
<table class="adminpanel">
	<tr><th>$ind157</th></tr>
	<tr><td>$ind158</td></tr>

	<tr><th style="text-align:left">$ind159 (<a href="javascript:toggleView('help_1');">$ind160</a>)</th></tr>
	<tr><td><div style="display:none" id="help_1">$ind345</div></td></tr>

	<tr><th style="text-align:left">$ind347 (<a href="javascript:toggleView('help_2');">$ind160</a>)</th></tr>
	<tr><td><div style="display:none" id="help_2">$ind348</div></td></tr>

	<tr><th style="text-align:left">$ind349 (<a href="javascript:toggleView('help_3');">$ind160</a>)</th></tr>
	<tr><td><div style="display:none" id="help_3">$ind350</div></td></tr>

	<tr><th style="text-align:left">$ind351 (<a href="javascript:toggleView('help_4');">$ind160</a>)</th></tr>
	<tr><td><div style="display:none" id="help_4">$ind352</div></td></tr>

	<tr><th style="text-align:left">$ind353 (<a href="javascript:toggleView('help_5');">$ind160</a>)</th></tr>
	<tr><td><div style="display:none" id="help_5">$ind354</div></td></tr>
</table>
<p></p>
<table class="adminpanel">
	<tr><th>$ind323</th></tr>
	<tr><td>$ind236</td></tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th colspan="2">$ind154</th>
	</tr>
	<tr>
		<td style="width:50%">$ind155</td>
		<td style="width:50%">$current_version</td>
	</tr>
	<tr>
		<td>$ind153</td>
		<td>$version</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			$message
		</td>
	</tr>
</table>
html;
}
else if ( $id == 'smillies')
{/*id Smillies*/
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($ind19, E_USER_WARNING);
    }
    
    /**
     * Creates a drop down menu with the specified items, and pre-selects a specific item
     * @param string $name Name of the dropdown menu
     * @param array $list Array of values to put into the dropdown menu, with the keys as the option value, and the values as the displayed text
     * @param string $default_val Value of the pre-selected item
     * @param string $js An optional javascript file can be added to the dropdown menu to allow it to do certain actions
     * @return string HTML for the generated drop down menu
     */
    function form_dropdown($name, $list=array(), $default_val='', $js='') {
        if ($js != '')$js = ' '.$js.' ';
        $html = '<select id="' . $name . '" name="' . $name . '"' . $js . '>' . "\n";

        foreach ($list as $k => $v){
            $selected = '';
            if ( ($default_val != '') and ($v[0] == $default_val) )$selected = ' selected="selected"';
            $html .= '<option value="' . $v[0] . '"' . $selected . '>' . $v[1] . '</option>' . "\n";
        }
        $html .= '</select>' . "\n\n";
        return $html;
    }

    $file = file(FNEWS_ROOT_PATH . 'smillies.php');
    array_shift ($file);
    $num_smilies = sizeof ($file);

    $title = $ind211;
    echo
<<< html
<h2>$ind274</h2>
<form method="post" action="?id=editsmillie">
<table class="adminpanel">
	<tr>
		<th style="width:10%">$ind97</th>
		<th style="width:70%">$ind276</th>
		<th style="width:20%">$ind277</th>
	</tr>

html;
    foreach ( $file as $value )
    {
        $smiley = get_line_data ('smillies', $value);
        echo
<<< html
	<tr>
		<td align="center">
			<input type="checkbox" id="del_smillie_{$smiley['smiley_id']}" name="del_smillie[{$smiley['smiley_id']}]" class="post" />
		</td>
		<td align="center">
			<input type="text" id="code_smillie_{$smiley['smiley_id']}" name="code_smillie[{$smiley['smiley_id']}]" class="post" value="{$smiley['bbcode']}" style="width:95%" />
		</td>
		<td align="center">
			<input type="hidden" name="smiley_image[{$smiley['smiley_id']}]" value="{$smiley['image']}" />
			<img src="$furl/smillies/{$smiley['image']}" alt="{$smiley['image']}" title="{$smiley['image']}" />
		</td>
	</tr>
html;
    }
    echo <<< html
	<tr>
		<th colspan="3">
			<input type="submit" class="mainoption" value="$ind174" />
			<input type="reset" value="$ind16" />
		</th>
	</tr>
</table>
html;
    echo get_form_security() . '</form>';

    $emos = array();
    $dh = opendir (FNEWS_ROOT_PATH . '/smillies');
    if ( $dh === false )
    {
        trigger_error ($error21, E_USER_WARNING);
    }

    while ( ($file = readdir ($dh)) !== false )
    {
        // Blackshadow: Added check to make sure smiley image isn't that dreaded Thumbs.db file
        if ( !preg_match ( '/^..?$|^index|htm$|html$|db$|^\./i', $file) )
        {
            $emos[] = array( $file, $file );
        }
    }

    closedir ($dh);
    sort($emos);
    reset($emos);

    $amos = array();
    $amos = $emos[0];
    $smiley_dropdown = form_dropdown ('smiley_image', $emos, $amos[1], 'onchange="show_emo(\'./smillies\')"');
    echo
<<< html
<form id="theAdminForm" method="post" action="?id=addsmillie">
<h2>$ind243</h2>
<table class="adminpanel">
	<tr>
		<th>$ind276</th>
		<th>$ind245</th>
		<th>$ind244</th>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" id="code" name="code" class="post" size="30" /></td>
		<td style="text-align:center">
			<img src="$furl/smillies/{$amos[1]}" id="emopreview" alt="{$amos[1]}" title="{$amos[1]}" />
			$smiley_dropdown
		</td>
		<td style="text-align:center"><input type="submit" class="mainoption" value="$ind244" /></td>
	</tr>
</table>
html;

    echo get_form_security();

    echo <<< html
</form>
<form method="post" action="?id=uploadsmillie" id="uploadform" enctype="multipart/form-data">
<h2>$ind246</h2>
<table class="adminpanel">
	<thead><tr><th>$ind247</th></tr></thead>
	<tfoot><tr><th><input type="submit" class="mainoption" value="$ind42" /></th></tr></tfoot>
	<tbody><tr><td style="text-align:center"><input type="file" id="FILE_UPLOAD" name="F" class="post" size="50" /></td></tr></tbody>
</table>
html;
	echo get_form_security() . '</form>';
}
//-----------------

else if ( $id == 'uploadsmillie')
{/*id Smillies Upload*/
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $title = $ind246;

    $upload_status = upload_file (-1, 'jpg|gif|jpeg|png|bmp', './smillies/');
    echo make_redirect ($upload_status, '?id=smillies', $ind335);
}

else if ( $id == 'addsmillie' )
{/*id Smillies Add*/
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $code = ( isset ($PVARS['code']) ) ? fn_trim (single_line ($PVARS['code'])) : '';
    $smiley_image = ( isset ($PVARS['smiley_image']) ) ? fn_trim (single_line ($PVARS['smiley_image'])) : '';

    if ( $code == '' || $smiley_image == '' )
    {
        trigger_error ($ind172, E_USER_WARNING);
    }
    
	if ( !file_exists (FNEWS_ROOT_PATH . 'smillies/' . $smiley_image) )
    {
        trigger_error ($ind301, E_USER_WARNING);
    }
    
    $random = mt_rand();
    $info = $random . '|<|' . $code . '|<|' . $smiley_image . '|<|' . "\n";
    
    safe_write ('smillies.php', 'ab', $info);

    $title = $ind248;
    echo make_redirect ($ind249, '?id=smillies', $ind335);
}

else if ( $id == 'editsmillie')
{/*id Smillies Edit*/
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    if ( !check_form_character() )
    {
        trigger_error ($ind298, E_USER_WARNING);
    }

    $code_smillie = ( isset ($PVARS['code_smillie']) ) ? array_map ('single_line', $PVARS['code_smillie']) : array();
    $del_smillie = ( isset ($PVARS['del_smillie']) ) ? $PVARS['del_smillie'] : array();
    $smiley_image = ( isset ($PVARS['smiley_image']) ) ? array_map ('single_line', $PVARS['smiley_image']) : array();
    
    function is_empty ( $str ) { return empty ($str); }
    
    $code_smillie = array_map ('fn_trim', $code_smillie);
    $blank_smileys = array_filter ($code_smillie, 'is_empty');
    if ( count ($blank_smileys) > 0 )
    {
        trigger_error ('One or more smileys have an empty BBCode field.', E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'smillies.php');
    array_shift ($file);

    $smilies = DENIED_MSG;

    foreach ( $code_smillie as $rand_id => $code )
    {
        if ( isset ($del_smillie[$rand_id]) )
        {
            continue;
        }

        $smilies .= $rand_id . '|<|' . $code . '|<|' . $smiley_image[$rand_id] . '|<|' . "\n";
    }

    safe_write ('smillies.php', 'wb', $smilies);

    $title = $ind250;
    echo make_redirect ($ind251, '?id=smillies', $ind335);
}
else if ( $id == 'categories' )
{
	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($ind19, E_USER_WARNING);
	}

    $action = ( isset ($GVARS['action']) ) ? $GVARS['action'] : '';
    if ( !$action )
    {
        $action = ( isset ($PVARS['action']) ) ? $PVARS['action'] : '';
    }

    if ( $action == 'add' )
    {
        if ( !check_form_character() )
        {
            trigger_error ($ind298, E_USER_WARNING);
        }

        $name = ( isset ($PVARS['name']) ) ? single_line ($PVARS['name']) : '';
        $icon = ( isset ($PVARS['icon']) ) ? single_line ($PVARS['icon']) : '';
        $user_access = ( isset ($PVARS['author']) ) ? $PVARS['author'] : array();

        if ( !$name )
        {
            trigger_error ($ind129, E_USER_WARNING);
        }

        if ( sizeof ($user_access) <= 0 )
        {
            trigger_error ($ind374, E_USER_WARNING);
        }

        $file = file (FNEWS_ROOT_PATH . 'categories.php');
        array_shift ($file);

        $data = DENIED_MSG;

        $match = false;
        $last_id = 0;
        foreach ( $file as $category )
        {
            $cat = get_line_data ('categories', $category);

            if ( $cat['name'] == $name )
            {
                $match = true;
            }

            $last_id = $cat['category_id'];
        }

        if ( $match )
        {
            trigger_error (sprintf ($ind315, $name), E_USER_WARNING);
        }

        $data .= implode ('', $file);
        $data .= $last_id + 1 . '|<|' . $name . '|<|' . $icon . '|<|' . implode (',', $user_access) . '|<|' . "\n";

        safe_write ('categories.php', 'wb', $data);

        $title = $ind316;
        echo make_redirect (sprintf ($ind317, $name), '?id=categories', $ind336);
    }
    else if ( $action == 'edit' )
    {
        $submit = ( isset ($PVARS['submit']) ) ? true : false;

        if ( $submit )
        {
            if ( !check_form_character() )
            {
                trigger_error ($ind298, E_USER_WARNING);
            }

            $category_id = ( isset ($PVARS['category_id']) ) ? intval ($PVARS['category_id']) : -1;
            $name = ( isset ($PVARS['name']) ) ? single_line ($PVARS['name']) : '';
            $icon = ( isset ($PVARS['icon']) ) ? single_line ($PVARS['icon']) : '';
            $user_access = ( isset ($PVARS['author']) ) ? $PVARS['author'] : array();
            $delete = ( isset ($PVARS['delete']) );
            $posts_action = ( isset ($PVARS['posts_action']) ) ? intval ($PVARS['posts_action']) : 0;
            $new_category = ( isset ($PVARS['new_category']) ) ? intval ($PVARS['new_category']) : -1;

            if ( !category_exists ($category_id) )
            {
                trigger_error (sprintf ($ind198, $category_id), E_USER_WARNING);
            }
            
            if ( $name == '' )
            {
                trigger_error ($error23, E_USER_WARNING);
            }
            
            if ( $delete && $category_id == 1 )
            {
                trigger_error (sprintf ($ind10, $name), E_USER_WARNING);
            }

            $file = file (FNEWS_ROOT_PATH . 'categories.php');
            array_shift ($file);

            $write = DENIED_MSG;

            foreach ( $file as $line )
            {
                $cat = get_line_data ('categories', $line);

                if ( (int)$cat['category_id'] === $category_id )
                {
                    if ( $delete )
                    {
                        continue;
                    }

                    $write .= $cat['category_id'] . '|<|' . $name . '|<|' . $icon . '|<|' . implode (',', $user_access) . '|<|' . "\n";
                }
                else
                {
                    $write .= $line;
                }
            }

            safe_write ('categories.php', 'wb', $write);

            if ( $delete )
            {
                $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
                array_shift ($file);

                $data = DENIED_MSG;

                foreach ( $file as $news )
                {
                    $toc = get_line_data ('news_toc', $news);

                    if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
                    {
                        continue;
                    }

                    $news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
                    $article = get_line_data ('news', $news_file[1]);

                    $exploded_cat = explode (',', $toc['categories']);
                    if ( in_array ($category_id, $exploded_cat) )
                    {
                        switch ( $posts_action )
                        {
                            case 1: // Move posts
                                $cs_cats = array();
                                foreach ( $exploded_cat as $cat )
                                {
                                    if ( (int)$cat === $category_id )
                                    {
                                        if ( !in_array ($new_category, $exploded_cat) )
                                        {
                                            $cs_cats[] = $new_category;
                                        }
                                    }
                                    else
                                    {
                                        $cs_cats[] = (int)$cat;
                                    }
                                }

                                $article['categories'] = $toc['categories'] = implode (',', $cs_cats);

                                $data .= implode ('|<|', $toc) . '|<|' . "\n";
                                $news_file[1] = implode ('|<|', $article) . '|<|' . "\n";

                                $write = implode ('', $news_file);
                                safe_write ('news/news.' . $article['news_id'] . '.php', 'wb', $write);
                            break;

                            case 2: // Delete posts
                                unlink (FNEWS_ROOT_PATH . 'news/news.' . $article['news_id'] . '.php');
                            break;

                            default:
                            break;
                        }
                    }
                    else
                    {
                        $data .= $news;
                    }
                }

                safe_write ('news/toc.php', 'wb', $data);
            }

            $title = $ind1;
            echo make_redirect ($ind2, '?id=categories', $ind336);
        }
        else
        {
            $catid = ( isset ($GVARS['category']) ) ? intval ($GVARS['category']) : 0;

            if ( !category_exists ($catid) )
            {
                trigger_error (sprintf ($ind198, $catid), E_USER_WARNING);
            }

            $title = $ind314;

            echo '<form method="post" action="?id=categories">';
            echo get_form_security();

            $file = file (FNEWS_ROOT_PATH . 'users.php');
            array_shift ($file);

            $user_array = array();
            foreach ( $file as $user )
            {
                $user = get_line_data ('users', $user);
                $user_array[] = array ('user' => $user['username'], 'nick' => $user['nickname']);
            }

            $file = file (FNEWS_ROOT_PATH . 'categories.php');
            array_shift ($file);

            foreach ( $file as $category )
            {
                $category = get_line_data ('categories', $category);

                if ( (int)$category['category_id'] === $catid )
                {
                    $allowed_users = explode (',', $category['users']);
                    $user_selection = build_author_selection ($allowed_users);

                    $category_select = build_category_dropdown (null, $catid, true);
                    $category_select = str_replace ('id="category" name="category"', 'id="new_category" name="new_category"', $category_select);

                    echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">{$category['name']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="hidden" name="category_id" value="$catid" />
				<input type="hidden" name="action" value="edit" />
				<input type="submit" name="submit" class="mainoption" value="$ind314" />
				<input type="reset" value="$ind16" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>$ind139</td>
			<td><input type="text" name="name" id="name" value="{$category['name']}" size="20" /></td>
		</tr>
		<tr>
			<td>$ind7</td>
			<td>
				<input type="text" name="icon" id="icon" value="{$category['icon']}" size="20" />
			</td>
		</tr>
		<tr>
			<td valign="top">$ind208 (<abbr title="$ind313">?</abbr>)</td>
			<td valign="top">
				$user_selection
			</td>
		</tr>
html;

                    if ( $catid > 1 )
                    {
                        echo <<< html
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="checkbox" name="delete" id="delete" value="1" />
				<label for="delete">$ind97</label>
			</td>
		</tr>
		<tr id="delete-options">
			<td valign="top">$ind136 (<abbr title="$ind201">?</abbr>)</td>
			<td valign="top">
				<input type="radio" name="posts_action" checked="checked" value="1" /> $ind199 $category_select<br />
				<input type="radio" name="posts_action" value="2" /> $ind206
			</td>
		</tr>

html;
                    }

                    echo <<< html
	</tbody>
</table>
<script type="text/javascript">
//<![CDATA[
(function()
{
    var deleteCheckbox = document.getElementById('delete');
    deleteCheckbox.onclick = function()
    {
        var ticked = deleteCheckbox.checked;
        if ( !ticked )
        {
            document.getElementById('delete-options').style.display = 'none';
        }
        else
        {
            document.getElementById('delete-options').style.display = 'table-row';
        }
    }
    
    deleteCheckbox.onclick();
})();
//]]>
</script>
html;

                    break;
                }
            }

            echo <<< html
</form>
html;
        }
    }
    else
    {
        $title = $ind311;

        $file = file (FNEWS_ROOT_PATH . 'users.php');
        array_shift ($file);
        $usernicks = array();
        foreach ( $file as $user )
        {
            $user = get_line_data ('users', $user);
            $usernicks[$user['username']] = $user['nickname'];
        }
        
        $user_selection = build_author_selection();

        echo '<form method="post" action="?id=categories">' . "\n";
        echo get_form_security();
        echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">$ind312</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="hidden" name="action" value="add" />
				<input type="submit" class="mainoption" value="$ind312" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>$ind139</td>
			<td><input type="text" name="name" id="name" size="20" /></td>
		</tr>
		<tr>
			<td>$ind7</td>
			<td>
				<input type="text" name="icon" id="icon" size="20" />
			</td>
		</tr>
		<tr>
			<td valign="top">$ind208 (<abbr title="$ind313">?</abbr>)</td>
			<td valign="top">
				$user_selection
            </td>
		</tr>
	</tbody>
</table>
</form>
<p></p>
html;
        $file = file (FNEWS_ROOT_PATH . 'categories.php');
        array_shift ($file);
        
        $news_file = file (FNEWS_ROOT_PATH . 'news/toc.php');
        array_shift ($news_file);
            
        $count = array();
        if ( sizeof ($file) > 0 )
        {
            // There are categories. Lets do a count of the news posts in each category.
            foreach ( $file as $category )
            {
                $category = get_line_data ('categories', $category);
                $count += array ($category['category_id'] => 0);
            }

            foreach ( $news_file as $news_line )
            {
                $toc = get_line_data ('news_toc', $news_line);

                if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
                {
                    continue;
                }

                $cats = explode (',', $toc['categories']);
                foreach ( $cats as $cat )
                {
                    if ( isset ($count[$cat]) )
                    {
                        ++$count[$cat];
                    }
                }
            }
        }

        $category_list = '<tbody>';
        foreach ( $file as $category )
        {
            $category = get_line_data ('categories', $category);

            $cicon = ( !empty ($category['icon']) ) ? '<img src="' . $category['icon'] . '" alt="" />' : '';

            $user_dropdown = '<select>';
            $userlist = explode (',', $category['users']);
            if ( $category['category_id'] == 1 )
            {
                foreach ( $usernicks as $nick )
                {
                    $user_dropdown .= '<option>' . $nick . '</option>';
                }
            }
            else if ( !empty ($userlist[0]) )
            {
                foreach ( $userlist as $user )
                {
                    $user_dropdown .= '<option>' . $usernicks[$user] . '</option>';
                }
            }
            $user_dropdown .= '</select>';

            $category_list .= '<tr>';
            $category_list .= '<td style="text-align:center">' . $category['category_id'] . '</td>';
            $category_list .= '<td>' . $category['name'] . '</td>';
            $category_list .= '<td style="text-align:center"><a href="?id=editposts&amp;category=' . $category['category_id'] . '">' . $count[$category['category_id']] . '</a></td>';
            $category_list .= '<td style="text-align:center">' . $cicon . '</td>';
            $category_list .= '<td>' . $user_dropdown . '</td>';
            $category_list .= '<td style="text-align:center"><a href="?id=categories&amp;action=edit&amp;category=' . $category['category_id'] . '">' . $ind30b . '</a></td>';
            $category_list .= '</tr>';
        }
        $category_list .= '</tbody>';

        echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="6">$ind314</th>
		</tr>
		<tr>
			<th>$ind346</th>
			<th>$ind139</th>
			<th>$ind82</th>
			<th>$ind140</th>
			<th>$ind208</th>
			<th>$ind30b</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>
	</tfoot>
	$category_list
</table>
html;
	}
}
elseif ($id == 'logout')
{ /*id Logout*/
	login_session_destroy ($sid);

	header ('Location: ./index.php');
}
else
{
	header ('Location: ./index.php');
}

/**
 * And finally display the output
 */
display_output ($title, $skin, $userdata);

?>
