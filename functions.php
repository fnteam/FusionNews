<?php

/**
 * Global functions used throughout the script
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: functions.php 392 2011-10-31 22:10:47Z xycaleth $
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
 
function single_line ( $str )
{
    return str_replace ("\n", '', $str);
}

/**
 * Converts UTF-8 characters to HTML numeric character references.
 * Any 8-bit characters are preserved as they are.
 * 
 * This function was taken from http://www.php.net/manual/en/function.utf8-decode.php#100478, and
 * optimized a little more.
 * @param string $string String to convert.
 * @return string
 */
function utf8_htmlentities ($string) {
    /* note: apply htmlspecialchars if desired /before/ applying this function
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    static $find = null;
    static $replace = null;

    if ( !preg_match("/([\200-\237]|[\241-\377])/", $string) )
    {
        return $string;
    }

    if ( !$find || !$replace )
    {
        $find = array();
        $replace = array();

        // reject too-short sequences
        $find += array (
            "/[\302-\375]([\001-\177])/",
            "/[\340-\375].([\001-\177])/",
            "/[\360-\375]..([\001-\177])/",
            "/[\370-\375]...([\001-\177])/",
            "/[\374-\375]....([\001-\177])/"
        );

        $replace += array (
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1"
        );

        // reject illegal bytes & sequences
        // 2-byte characters in ASCII range
        $find[] = "/[\300-\301]./";
        $replace[] = "&#65533;";

        // 4-byte illegal codepoints (RFC 3629)
        $find[] = "/\364[\220-\277]../";
        $replace[] = "&#65533;";

        // 4-byte illegal codepoints (RFC 3629)
        $find[] = "/[\365-\367].../";
        $replace[] = "&#65533;";

        // 5-byte illegal codepoints (RFC 3629)
        $find[] = "/[\370-\373]..../";
        $replace[] = "&#65533;";

        // 6-byte illegal codepoints (RFC 3629)
        $find[] = "/[\374-\375]...../";
        $replace[] = "&#65533;";

        // undefined bytes
        $find[] = "/[\376-\377]/";
        $replace[] = "&#65533;";

        // reject consecutive start-bytes
        $find[] = "/[\302-\364]{2,}/";
        $replace[] = "&#65533;";

        // decode four byte unicode characters
        $find[] = "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 | (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'";

        // decode three byte unicode characters
        $find[] = "/([\340-\357])([\200-\277])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'";

        // decode two byte unicode characters
        $find[] = "/([\300-\337])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'";

        // reject leftover continuation bytes
        $find[] = "/[\200-\277]/";
        $replace[] = "&#65533;";
    }

    return preg_replace ($find, $replace, $string);
}

/**
 * UTF-8 compatible version of strlen().
 * @param string $string
 * @return int Length of $string.
 */
function utf8_strlen ( $string )
{
    return sizeof (utf8_start_bytes ($string));
}
 
/**
 * UTF-8 compatible version of substr(). See PHP documentation
 * for substr() function.
 * @param string $string
 * @param string $start
 * @param string $length (optional)
 * @return string Substring of original string.
 */
function utf8_substr ( $string, $start )
{
    $num_args = func_num_args();
    $starts = utf8_start_bytes ($string);
    $strlen = sizeof ($starts);
    
    if ( !$strlen )
    {
        return false;
    }
    
    if ( $start < 0 )
    {
        $start += $strlen;
    }
    
    if ( $start >= $strlen )
    {
        return false;
    }
    
    if ( $num_args >= 3 )
    {
        $length = func_get_arg (2);
        if ( !$length ) return '';
        if ( $length < 0 )
        {
            $end = $strlen + $length;
        }
        else
        {
            $end = $start + $length;
        }
        
        if ( $end >= $strlen )
        {
            $starts[$strlen] = strlen ($string);
            $end = $strlen;
        }
        
        return substr ($string, $starts[$start], $starts[$end] - $starts[$start]);
    }
    else
    {
        return substr ($string, $starts[$start]);
    }
}
 
/**
 * @param string $string
 * @return array Array of byte positions corresponding to the start of each code point.
 */
function utf8_start_bytes ( $string )
{
    $start = array();
    for ( $i = 0; isset ($string[$i]); $i++ )
    {
        if ( (ord ($string[$i]) & 0xC0) != 0x80 )
        {
            $start[] = $i;
        }
    }
    
    return $start;
}
 
/**
 * Trims whitespace from the beginning and end of a string. Acts a wrapper
 * for PHP's trim function, but includes non-breaking spaces as well.
 * @param string $str String to trim.
 * @param string $additional_chars Additional characters to trim from the string.
 * @return string Trimmed string.
 */
function fn_trim ( $str, $additional_chars = '' )
{
    return trim ($str, " \r\t\n\0\x0B\xC2\xA0" . $additional_chars);
}
 
/**
 * @return bool True if tell friend feature is disabled. Otherwise false. It is deemeed
 * disabled if both the short news and full news template have ommitted the {send}
 * tag.
 */
function is_tellfriend_disabled()
{
    $short_news_template = get_template ('news_temp.php', false);
    $full_news_template = get_template ('fullnews_temp.php', false);
    return
        strpos ($short_news_template, '{send}') === false &&
        strpos ($full_news_template, '{send}') === false;
}

/**
 * @return bool True if commenting is disabled. Otherwise false. Comments are deemeed
 * disabled if both the short news and full news template have ommitted the {comments}
 * tag.
 */
function is_commenting_disabled()
{
    $short_news_template = get_template ('news_temp.php', false);
    $full_news_template = get_template ('fullnews_temp.php', false);
    return
        strpos ($short_news_template, '{comments}') === false &&
        strpos ($full_news_template, '{comments}') === false;
}
 
/**
 * Revokes a single or multiple users' access to a category
 * @param string|array $usernames The username(s) to revoke category access from.
 */
function revoke_category_access ( $usernames )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    $data = array_shift ($file);
    
    if ( !is_array ($usernames) )
    {
        $usernames = array ($usernames);
    }
    
    foreach ( $file as $line )
    {
        $category = get_line_data ('categories', $line);
        $users = explode (',', $category['users']);
        foreach ( $users as $index => $user )
        {
            if ( in_array ($user, $usernames) )
            {
                unset ($users[$index]);
            }
        }
        
        $category['users'] = implode (',', $users);
        $data .= implode ('|<|', $category) . "|<|\n";
    }
    
    safe_write ('categories.php', 'wb', $data);
}

/**
 * Replaces BBCode in the given text
 * @param string $message The text containing the BBCodes to be replaced
 * @return string The processed text with BBCodes replaced
 * @todo Handle nested tags.
 */
function replace_bbcode ( $message )
{
    static $bbcode_normal_find_pair = array (
       '[move]', '[/move]',
       '[sub]', '[/sub]',
       '[sup]', '[/sup]',
       '[s]', '[/s]',
       '[b]', '[/b]',
       '[u]', '[/u]',
       '[i]', '[/i]',
       '[tt]', '[/tt]',
       '[quote]', '[/quote]',
       '[list]', '[/list]'
    );
    
    static $bbcode_normal_replace_pair = array (
       '<marquee>', '</marquee>',
       '<sub>', '</sub>',
       '<sup>', '</sup>',
       '<del>', '</del>',
       '<strong>', '</strong>',
       '<u>', '</u>',
       '<em>', '</em>',
       '<tt>', '</tt>',
       '<blockquote>quote:<hr style="height:1px" />&quot;', '&quot;<hr style="height:1px" /></blockquote>',
       '<ul>', '</ul>'
    );
    
    static $bbcode_normal_find = array (
        '[hr]'
    );
    
    static $bbcode_normal_replace = array (
        '<hr />'
    );
    
    $message = str_replace ($bbcode_normal_find, $bbcode_normal_replace, $message);
    
    $find = array();
    $replace = array();
    for ( $i = 0, $num_bbcode = sizeof ($bbcode_normal_find_pair); $i < $num_bbcode; $i += 2 )
    {
        $find[] = '#' . preg_quote ($bbcode_normal_find_pair[$i], '#') . '(.*?)' . preg_quote ($bbcode_normal_find_pair[$i + 1], '#') . '#i';
        $replace[] = $bbcode_normal_replace_pair[$i] . '$1' . $bbcode_normal_replace_pair[$i + 1];
    }
    
    $message = preg_replace ($find, $replace, $message);
	
    static $email_regex = '([A-Za-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^_`\{\|\}\~\.]{1,64}@[a-z0-9\-\.]{2,65})';
	$bbcode_preg_find = array (
	   '/\[font=(\w+)](.*?)\[\/font\]/i',
	   '/\[color=(\w+)](.*?)\[\/color\]/i',
	   '/\[size=(\d+)](.*?)\[\/size\]/i',
	   '/\[align=(left|center|right)](.*?)\[\/align\]/i',
	   '/\[img]([^\[\]]*)\[\/img]/i',
	   '/\[img height=(\d+) width=(\d+)]([^\[\]]*)\[\/img]/i',
	   '/\[img width=(\d+) height=(\d+)]([^\[\]]*)\[\/img]/i',
	   '/\[flash=(\d+),(\d+)]([^\[\]]*)\[\/flash]/i',
	   '/\[email]' . $email_regex . '\[\/email\]/i',
	   '/\[email=' . $email_regex . '](.*?)\[\/email\]/i',
	   '#\[\*\]([^\[]+)#',
	   '#\[url\]([^"\[\]]+)\[/url\]#i',
	   '#\[url=([^"\[\]]+)\]([^\[]+)\[/url\]#i',
	);
	
	$bbcode_preg_replace = array (
	   '<span style="font-family:$1">$2</span>',
	   '<span style="color:$1">$2</span>',
	   '<span style="font-size:$1px">$2</span>',
	   '<div style="text-align:$1">$2</div>',
	   '<img src="$1" alt="" />',
	   '<img src="$3" style="height:$1px; width:$2px" alt="" />',
	   '<img src="$3" style="height:$2px; width:$1px" alt="" />',
	   '<object type="application/x-shockwave-flash" data="$3" width="$1" height="$2"><param name="movie" value="$3" /><span>Flash required</span></object>',
	   '<a href="mailto:$1">$1</a>',
	   '<a href="mailto:$1">$2</a>',
	   '<li>$1</li>',
	   '<a href="$1">$1</a>',
	   '<a href="$1">$2</a>'
	);
	
	$message = preg_replace ($bbcode_preg_find, $bbcode_preg_replace, $message);

	return $message;
}

/**
 * @return array List of all smileys
 */
function get_smiley_list()
{
    static $smileys = null;
    
    if ( $smileys === null )
    {
        $file = file (FNEWS_ROOT_PATH . 'smillies.php');
        array_shift ($file);
        
        $smileys = array();
        foreach ( $file as $value )
        {
            $smiley = get_line_data ('smillies', $value);
            $smiley['bbcode'] = html_entity_decode ($smiley['bbcode']);
            $smiley['image'] = chop ($smiley['image']);
            $smileys[] = $smiley;
        }
    }
    
    return $smileys;
}

/**
 * Replaces smiley codes in the given text
 * @param string $message Text containing the smiley codes to be replaced
 * @return string The processed text with smiley codes replaced
 */
function replace_smileys ( $message )
{
	global $furl;
	
	static $smiley_table = null;
	
	if ( $smiley_table === null )
	{
        $smileys = get_smiley_list();
	    foreach ( $smileys as $smiley )
	    {
		    $smiley_table['find'][] = $smiley['bbcode'];
		    $smiley_table['replace'][] = '<img src="' . $furl . '/smillies/' . $smiley['image'] . '" alt="' . $smiley['image'] . '" />';
	    }
	}

	$message = str_replace ($smiley_table['find'], $smiley_table['replace'], $message);

	return $message;
}

/**
 * @param string $template File name of template to get
 * @param bool $php_enabled Whether or not to parse PHP in the template file
 * @return string Contents of template file
 */
function get_template ( $template, $php_enabled )
{
	$text = '';

    if ( $php_enabled )
    {
        ob_start();
        include (FNEWS_ROOT_PATH . 'templates/' . $template);
        $text = ob_get_clean();
    }
    else
    {
        $text = file_get_contents (FNEWS_ROOT_PATH . 'templates/' . $template);
    }
        
	return $text;
}

/**
 * @return array Array of toc.php lines, ordered by date
 */
function get_ordered_toc()
{
	$file = file (FNEWS_ROOT_PATH . 'news/toc.php');
	array_shift ($file);

	if ( count ($file) <= 0 )
	{
		return array();
	}
	
	$array = array();
	foreach ( $file as $value )
	{
		$news_data = explode ('|<|', $value);
		$array[$news_data[1]] = chop (trim ($value));
	}

	krsort ($array);
	return array_values ($array);
}

/**
 * Filters words listed in the word filter
 * @param string $message Text to have the word filter applied to
 * @return string Word-filtered text
 */
function filter_badwords ( $message )
{
    static $badword_table = null;
    
    if ( $badword_table === null )
    {
	    $badword_table = array('find' => array(), 'replace' => array());
	    $file = file (FNEWS_ROOT_PATH . 'badwords.php');
	    array_shift ($file);

	    foreach ( $file as $rule )
	    {
		    $badword = get_line_data ('badwords', $rule);

		    if ( $badword['type'] == 2 )
		    {
			    // Regular expressions match
			    $badword_table['find'][] = html_entity_decode ($badword['find']);
			    $badword_table['replace'][] = html_entity_decode ($badword['replace']);
		    }
		    else
		    {
			    // Strict
			    if ( $badword['type'] == 0 )
			    {
				    $badword_table['find'][] = '#\b' . preg_quote ($badword['find'], '#') . '\b#' . (!$badword['case_sensitive'] ? 'i' : '');
			    }
			    // Loose
			    else if ( $badword['type'] == 1 )
			    {
				    $badword_table['find'][] = '#' . preg_quote ($badword['find'], '#') . '#' . (!$badword['case_sensitive'] ? 'i' : '');
			    }
			    $badword_table['replace'][] = $badword['replace'];
		    }
	    }
	}

	$message = preg_replace ($badword_table['find'], $badword_table['replace'], $message);

	return $message;
}

/**
 * Checks if the given user name or nick name exists, and then returns
 * the said user's data.
 * @param string $user User's log in name
 * @param string $nick User's nick name
 * @return array|bool Returns FALSE if the user doesn't exist, otherwise
 * returns the specified user's data.
 */
function get_author ( $user, $nick = '' )
{
    static $user_table = array();

	if ( !$nick && !$user )
	{
		return false;
	}
	
	if ( isset ($user_table[$user]) )
    {
        return $user_table[$user];
    }
    
    if ( isset ($user_table[$nick]) )
    {
        return $user_table[$nick];
    }

	if ( !$nick && $user )
	{
		$nick = $user;
	}
	else if ($nick && !$user)
	{
		$user = $nick;
	}

	$file = file(FNEWS_ROOT_PATH . 'users.php');
	array_shift($file);
	foreach ( $file as $value )
	{
		$userdat = get_line_data ('users', $value);

		if( ($user == $userdat['username']) || ($nick == $userdat['nickname']) )
		{		
			$email = explode ('=', $userdat['email']);

		    $user_array = array (
				'user'		=> $userdat['username'],
				'nick'		=> $userdat['nickname'],
				'email'		=> $email[1],
				'showemail'		=> $email[0],
				'icon'		=> $userdat['icon'],
				'timeoffset'	=> $userdat['timeoffset'],
				'level'		=> $userdat['level']
			);
			
			$user_table[$userdat['username']] = $user_table[$userdat['nickname']] = $user_array;
			
			return $user_array;
		}
	}

	return false;
}

/**
 * Converts a number of bytes into text, taking into account the units (B, KB, MB, etc)
 * @param int $size Number of bytes to convert
 * @return string The converted bytes as text.
 */
function calc_size ( $size )
{
	if ( $size < 1024 )
	{ // Bytes
		return $size . ' B';
	}
	else if ( $size < (1024 * 1024) )
	{ // Kilobytes
		return round (($size / 1024), 2) . ' KB';
	}
	else if ( $size < (1024 * 1024 * 1024) )
	{ // Megabytes
		return round ($size / (1024 * 1024), 2) . ' MB';
	}
}

/**
 * Checks whether an email address has a valid format.
 * @param string $email Email address to validate.
 * @return bool True if the email address is valid, otherwise false.
 */
function is_valid_email ( $email )
{
	// Returns true if email address has a valid form.
	return preg_match ('#^[A-Za-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^_`\{\|\}\~]{1,64}@[a-z0-9\-\.]{2,65}$#', $email);
}

/**
 * Checks if the user is flooding the comments.
 * @return bool True is the user is flooding, otherwise false.
 */
function is_flooding()
{
	global $floodtime;

	$user_ip = get_ip();
	$current_time = time();

	$file = file (FNEWS_ROOT_PATH . 'flood.php');
	$data = array_shift ($file);

	$flooding = false;
	foreach ( $file as $line )
	{
		$record = get_line_data ('flood', $line);

		if ( ($record['timestamp'] + $floodtime) <= $current_time )
		{
			// Times up. Remove this old record.
			continue;
		}

		if ( $record['ip'] == $user_ip )
		{
			// We've added a comment too recently.
			$flooding = true;
		}

		$data .= $line;
	}

	safe_write ('flood.php', 'wb', $data);

	return $flooding;
}

/**
 * Check if the given IP is banned
 * @param string $ip IP address to check if banned
 * @return bool True is the given IP is banned, otherwise false.
 */
function is_ip_banned ( $ip )
{
	$file = file (FNEWS_ROOT_PATH . 'banned.php');
	array_shift($file);

	$my_subnet = explode ('.', $ip);
	foreach ( $file as $value )
	{
		$value = trim ($value);
		$octet = explode ('.', $value);
		if ( ($octet[0] == $my_subnet[0] || $octet[0] == '*') &&
			($octet[1] == $my_subnet[1] || $octet[1] == '*') &&
			($octet[2] == $my_subnet[2] || $octet[2] == '*') &&
			($octet[3] == $my_subnet[3] || $octet[3] == '*') )
		{
			return true;
		}
	}

	return false;
}

/**
 * @return string Returns the user's IP address
 */
function get_ip()
{
	$realip = '';

	if ( isset ($_SERVER) )
	{
		if ( isset ( $_SERVER['REMOTE_ADDR']) )
		{
			$realip = $_SERVER['REMOTE_ADDR'];
		}
		else if ( isset ($_SERVER['HTTP_CLIENT_IP']) )
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	else
	{
		if ( getenv ('REMOTE_ADDR') )
		{
			$realip = getenv ('REMOTE_ADDR');
		}
		else if ( getenv ('HTTP_CLIENT_IP') )
		{
			$realip = getenv ('HTTP_CLIENT_IP');
		}
		else
		{
			$realip = getenv ('HTTP_X_FORWARDED_FOR');
		}
	}

	return $realip;
}

/**
 * Checks if the user has sufficient access rights.
 * @param int $level Access level to check against
 * @return bool True if the user has sufficient access rights, otherwise false.
 */
function has_access ( $level )
{
	global $userdata;

	return ( $userdata != NULL ) ? ($userdata['level'] >= $level) : false;
}

/**
 * Cleans array key of characters other than A-Z, a-z, 0-9, underscore
 * @param string $key Key to be cleaned
 * @return string Cleaned key
 */
function clean_key ( $key )
{
	if ( $key != '' )
	{
		$key = preg_replace ('/[^A-Za-z0-9_]+/',	'',	$key);
		$key = preg_replace ('/\.\./',			'',	$key);
		$key = preg_replace ('/\_\_(.+?)\_\_/',		'',	$key);
		$key = preg_replace ('/^([\w\.\-\_]+)$/',	'$1',	$key);
	}

	return $key;
}

/**
 * Cleans array value of potentially dangerous characters
 * @param string $val Value to be cleaned
 * @return string Cleaned value
 */
function clean_value ( $val )
{
	if ( $val != '' )
	{
		// I think this block of slightly modified code came from IPB.
		/*$val = str_replace ('&#032;',		' ',		$val);
		$val = str_replace ('&',		'&amp;',	$val);
		$val = str_replace ('<!',		'&#60;&#33;',	$val);
		$val = str_replace ('-->',		'--&#62;',	$val);
		$val = preg_replace ('/<script/i',	'&#60;script',	$val);
		$val = str_replace ('>',		'&gt;',		$val);
		$val = str_replace ('<',		'&lt;',		$val);
		$val = preg_replace ('/\|/',		'&#124;',	$val);*/
		
        if ( get_magic_quotes_gpc() )
        {
            $val = stripslashes ($val);
        }
		$val = str_replace ("\r\n",		"\n",		$val);	// Win32 => new line
		$val = str_replace ("\r",		"\n",		$val);	// Mac => new line
		//$val = str_replace ('!',		'&#33;',	$val);
		
		$val = htmlspecialchars ($val);

		// Multibyte characters fix (could do it properly, but this is the easiest way and it works relatively well)
		$val = preg_replace ('/&amp;#([0-9]+);/', '&#$1;', $val);
		$val = preg_replace ('/&amp;#x([A-Z0-9]+);/i', '&#x$1', $val);
	}

	return $val;
}

/**
 * @return array Returns configuration settings
 */
function config_array()
{
	require './config.php';

	return array(
		'fusion_id'		=> $fusion_id,
		'site'			=> $site,
		'furl'			=> $furl,
		'hurl'			=> $hurl,
		'datefor'		=> $datefor,
		'numofposts'		=> $numofposts,
		'numofh'		=> $numofh,
		'bb'			=> $bb,
		'ht'			=> $ht,
		'post_per_day'		=> $post_per_day,
		'ppp_date'		=> $ppp_date,
		'wfpost'		=> $wfpost,
		'wfcom'			=> $wfcom,
		'skin'			=> $skin,
		'smilies'		=> $smilies,
		'stfpop'		=> $stfpop,
		'comallowbr'		=> $comallowbr,
		'stfwidth'		=> $stfwidth,
		'stfheight'		=> $stfheight,
		'fslink'		=> $fslink,
		'stflink'		=> $stflink,
		'pclink'		=> $pclink,
		'fsnw'			=> $fsnw,
		'cbflood'		=> $cbflood,
		'floodtime'		=> $floodtime,
		'comlength'		=> $comlength,
		'fullnewsw'		=> $fullnewsw,
		'fullnewsh'		=> $fullnewsh,
		'fullnewss'		=> $fullnewss,
		'stfresize'		=> $stfresize,
		'stfscrolls'		=> $stfscrolls,
		'fullnewsz'		=> $fullnewsz,
		'htc'			=> $htc,
		'smilcom'		=> $smilcom,
		'bbc'			=> $bbc,
		'compop'		=> $compop,
		'comscrolls'		=> $comscrolls,
		'comresize'		=> $comresize,
		'comheight'		=> $comheight,
		'comwidth'		=> $comwidth,
		'uploads_active'	=> $uploads_active,
		'uploads_size'		=> $uploads_size,
		'uploads_ext'		=> $uploads_ext,
		'enable_rss'		=> $enable_rss,
		'link_headline_fullstory' => $link_headline_fullstory,
		'flip_news'		=> $flip_news,
		'rss_title'		=> $rss_title,
		'rss_description'	=> $rss_description,
		'rss_encoding'		=> $rss_encoding,
		'com_validation'	=> $com_validation,
		'com_captcha'		=> $com_captcha,
		'news_pagination'	=> $news_pagination,
		'ppp_date'		=> $ppp_date,
		'news_pagination_arrows'=> $news_pagination_arrows,
		'news_pagination_numbers'=> $news_pagination_numbers,
		'news_pagination_prv'	=> $news_pagination_prv,
		'news_pagination_nxt'	=> $news_pagination_nxt,
		'comments_pages'	=> $comments_pages,
		'comments_per_page'	=> $comments_per_page,
        'use_wysiwyg'   => isset ($use_wysiwyg) ? $use_wysiwyg : 0,
        'stf_captcha'   => isset ($stf_captcha) ? $stf_captcha : 0,
	);
}

/**
 * Saves configuration settings to file.
 * @param array $configs Config data array
 */
function save_config ( $configs )
{
	require './config.php';

	// Replace ' with \'
	$find = '\'';
	$replace = '\\\'';
	$variables = array ('site', 'furl', 'hurl', 'datefor', 'skin', 'fslink', 'stflink', 'pclink',
				'rss_title', 'rss_description', 'rss_encoding', 'news_pagination_nxt', 'news_pagination_prv');
	foreach ( $variables as $config_var )
	{
		$configs[$config_var] = str_replace ($find, $replace, $configs[$config_var]);
	}
    
    // Override HTML setting if WYSIWYG is enabled.
    if ( $configs['use_wysiwyg'] )
    {
        $configs['ht'] = 1;
    }

	// Restrict values between specified ranges
	$variables = array (
		'numofposts' => array ('min' => 1),
		'numofh' => array ('min' => 1),
		'fullnewsw' => array ('min' => 100),
		'fullnewsh' => array ('min' => 100),
		'comheight' => array ('min' => 100),
		'comwidth' => array ('min' => 100),
		'stfwidth' => array ('min' => 100),
		'stfheight' => array ('min' => 100),
		'floodtime' => array ('min' => 1),
		'comlength' => array ('min' => 0),
		'comments_per_page' => array ('min' => 0),
        'uploads_size' => array ('min' => 1000)
	);
	foreach ( $variables as $config_var => $range )
	{
		if ( isset ($range['min']) && $configs[$config_var] < $range['min'] )
		{
			$configs[$config_var] = isset ($$config_var) ? $$config_var : $range['min'];
		}

		if ( isset ($range['max']) && $configs[$config_var] > $range['max'] )
		{
			$configs[$config_var] = isset ($$config_var) ? $$config_var : $range['max'];
		}
	}

	$save  = "<?php\n\n";
	$save .= '// Auto generated by Fusion News v' . FNEWS_CURVE . "\n\n";
	$save .= "\$fusion_id = '".$configs['fusion_id']."';\n";
	$save .= "\$site = '".$configs['site']."';\n";
	$save .= "\$furl = '".$configs['furl']."';\n";
	$save .= "\$hurl = '".$configs['hurl']."';\n";
	$save .= "\$datefor = '".$configs['datefor']."';\n";
	$save .= "\$numofposts = ".$configs['numofposts'].";\n";
	$save .= "\$numofh = ".$configs['numofh'].";\n";
	$save .= "\$bb = ".$configs['bb'].";\n";
	$save .= "\$ht = ".$configs['ht'].";\n";
	$save .= "\$post_per_day = ".$configs['post_per_day'].";\n";
	$save .= "\$wfpost = ".$configs['wfpost'].";\n";
	$save .= "\$wfcom = ".$configs['wfcom'].";\n";
	$save .= "\$skin = '".$configs['skin']."';\n";
	$save .= "\$smilies = ".$configs['smilies'].";\n";
	$save .= "\$stfpop = ".$configs['stfpop'].";\n";
	$save .= "\$comallowbr = ".$configs['comallowbr'].";\n";
	$save .= "\$stfwidth = ".$configs['stfwidth'].";\n";
	$save .= "\$stfheight = ".$configs['stfheight'].";\n";
	$save .= "\$fslink = '".$configs['fslink']."';\n";
	$save .= "\$stflink = '".$configs['stflink']."';\n";
	$save .= "\$pclink = '".$configs['pclink']."';\n";
	$save .= "\$fsnw = ".$configs['fsnw'].";\n";
	$save .= "\$cbflood = ".$configs['cbflood'].";\n";
	$save .= "\$floodtime = ".$configs['floodtime'].";\n";
	$save .= "\$comlength = ".$configs['comlength'].";\n";
	$save .= "\$fullnewsw = ".$configs['fullnewsw'].";\n";
	$save .= "\$fullnewsh = ".$configs['fullnewsh'].";\n";
	$save .= "\$fullnewss = ".$configs['fullnewss'].";\n";
	$save .= "\$stfresize = ".$configs['stfresize'].";\n";
	$save .= "\$stfscrolls = ".$configs['stfscrolls'].";\n";
	$save .= "\$fullnewsz = ".$configs['fullnewsz'].";\n";
	$save .= "\$htc = ".$configs['htc'].";\n";
	$save .= "\$smilcom = ".$configs['smilcom'].";\n";
	$save .= "\$bbc = ".$configs['bbc'].";\n";
	$save .= "\$compop = ".$configs['compop'].";\n";
	$save .= "\$comscrolls = ".$configs['comscrolls'].";\n";
	$save .= "\$comresize = ".$configs['comresize'].";\n";
	$save .= "\$comheight = ".$configs['comheight'].";\n";
	$save .= "\$comwidth = ".$configs['comwidth'].";\n";
	$save .= "\$uploads_active = ".$configs['uploads_active'].";\n";
	$save .= "\$uploads_size = ".$configs['uploads_size'].";\n";
	$save .= "\$uploads_ext = '".$configs['uploads_ext']."';\n";
	$save .= "\$enable_rss = ".$configs['enable_rss'].";\n";
	$save .= "\$link_headline_fullstory = ".$configs['link_headline_fullstory'].";\n";
	$save .= "\$flip_news = ".$configs['flip_news'].";\n";
	$save .= "\$rss_title = '" . $configs['rss_title'] . "';\n";
	$save .= "\$rss_description = '" . $configs['rss_description'] . "';\n";
	$save .= "\$rss_encoding = '" . $configs['rss_encoding'] . "';\n";
	$save .= '$com_validation = ' . $configs['com_validation'] . ';' . "\n";
	$save .= '$com_captcha = ' . $configs['com_captcha'] . ';' . "\n";
	$save .= '$news_pagination = ' . $configs['news_pagination'] . ';' . "\n";
	$save .= '$news_pagination_prv = \'' . $configs['news_pagination_prv'] . '\';' . "\n";
	$save .= '$news_pagination_nxt = \'' . $configs['news_pagination_nxt'] . '\';' . "\n";
	$save .= '$news_pagination_numbers = ' . $configs['news_pagination_numbers'] . ';' . "\n";
	$save .= '$news_pagination_arrows = ' . $configs['news_pagination_arrows'] . ';' . "\n";
	$save .= '$ppp_date = \'' . $configs['ppp_date'] . '\';' . "\n";
	$save .= '$comments_pages = ' . $configs['comments_pages'] . ';' . "\n";
	$save .= '$comments_per_page = ' . $configs['comments_per_page'] . ';' . "\n";
	$save .= '$use_wysiwyg = ' . $configs['use_wysiwyg'] . ';' . "\n";
	$save .= '$stf_captcha = ' . $configs['stf_captcha'] . ';' . "\n\n";
	$save .= '?' . '>';	// Split it up to prevent some editors from thinking it's a real PHP end tag.

	safe_write ('config.php', 'w', $save);
}

/**
 * Checks whether CAPTCHA code is correct for a given news article, and removes the code from database.
 * @param string $captcha_code 5 character CAPTCHA code
 * @param string $page_session_id User's session ID for the page
 * @param int $news_id News ID of the article
 * @param string $page Page the code applies to
 * @return bool True is the given code is correct, otherwise false.
 */
function is_valid_captcha_code ( $captcha_code, $page_session_id, $news_id, $page )
{
	global $error1;

	if ( empty ($page_session_id) || strlen ($captcha_code) != 5 )
	{
		return false;
	}

	$file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	$valid_code = false;
	$data = '<?php die (\'' . $error1 . '\'); ?>' . "\n";
	$current_time = time();
	$user_ip = get_ip();
	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $page_session_id == $session['session_id'] && $session['page'] == $page )
		{
			if ( $captcha_code == $session['code'] &&
				$news_id == $session['news_id'] &&
				$session['ip'] == $user_ip &&
				($session['last_visit'] + 600 >= $current_time) )
			{
				$valid_code = true;
			}
		}
		else
		{
			if ( ($session['last_visit'] + 600) >= $current_time )
			{
				$data .= $value;
			}
		}
	}

	safe_write ('sessions.php', 'wb', $data);

	return $valid_code;
}

/**
 * Generates HTML to display smiley and BBCode buttons if enabled.
 * @param string $form_name Name of the form to which the buttons should add text to.
 * @param string $textbox_name Name of the textbox which should have text added to.
 * @param bool $show_smilies Whether or not to display smiley buttons
 * @param bool $show_bbcode Whether or not to display BBCode buttons
 * @return string HTML for displaying smiley and BBCode buttons
 */
function show_extras ( $form_name, $textbox_name, $show_smilies, $show_bbcode )
{
	global $furl;

	$extra_html = '';

	if ( $show_smilies )
	{
		$file = file (FNEWS_ROOT_PATH . 'smillies.php');
		array_shift ($file);
		foreach ( $file as $smiley )
		{
			$smiley = get_line_data ('smillies', $smiley);

			$text = addslashes ($smiley['bbcode']);
			$extra_html .= '<a href="javascript:smiley_bbcode(\'' . $textbox_name . '\', \'' . $text . '\');"><img src="' . $furl . '/smillies/' . $smiley['image'] . '" alt="' . $text . '" /></a>';
		}

		$extra_html .= '<br />' . "\n";
	}

	if ( $show_bbcode )
	{
		$extra_html .=
<<< html
<a href="javascript:smiley_bbcode('$textbox_name', '[b]', '[/b]');"><img src="{$furl}/img/bold.gif" alt="Bold" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[i]', '[/i]');"><img src="{$furl}/img/italic.gif" alt="Italic" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[u]', '[/u]');"><img src="{$furl}/img/underline.gif" alt="Underline" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[s]', '[/s]');"><img src="{$furl}/img/strike.gif" alt="Strikethrough" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[sub]', '[/sub]');"><img src="{$furl}/img/sub.gif" alt="Subscript" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[sup]', '[/sup]');"><img src="{$furl}/img/sup.gif" alt="Superscript" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[color=red]', '[/color]');"><img src="{$furl}/img/color.gif" alt="Font color" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[font=verdana]', '[/font]');"><img src="{$furl}/img/fontface.gif" alt="Font Family" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[size=16]', '[/size]');"><img src="{$furl}/img/fontsize.gif" alt="Font Size" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=left]', '[/align]');"><img src="{$furl}/img/fontleft.gif" alt="Left Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=center]', '[/align]');"><img src="{$furl}/img/center.gif" alt="Center Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=right]', '[/align]');"><img src="{$furl}/img/right.gif" alt="Right Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[tt]', '[/tt]');"><img src="{$furl}/img/tele.gif" alt="Teletype" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[hr]');"><img src="{$furl}/img/hr.gif" alt="Horizontal Line" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[move]', '[/move]');"><img src="{$furl}/img/move.gif" alt="Marquee" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[quote]', '[/quote]');"><img src="{$furl}/img/quote2.gif" alt="Quote" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[flash=200,200]', '[/flash]');"><img src="{$furl}/img/flash.gif" alt="Flash Image" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[img]', '[/img]');"><img src="{$furl}/img/img.gif" alt="Image" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[email=username@site.com]', '[/email]');"><img src="{$furl}/img/email2.gif" alt="E-mail link" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[url=http://www.url.com]', '[/url]');"><img src="{$furl}/img/url.gif" alt="hyperlink" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[list]', '[/list]');"><img src="{$furl}/img/list.gif" alt="List" /></a><br />
html;
	}

	return $extra_html;
}

/**
 * Generates HTML for a category dropdown menu
 * @param string $user_login Login in name of user to determine what categories to show.
 * @param int $selected_category Preselected category ID.
 * @return string HTML for category drop down menu
 */
function build_category_dropdown ( $user_login = null, $selected_category = 0, $remove_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $dropdown = '<select class="post" id="category" name="category">' . "\n";
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        $cusers = explode (',', $category['users']);
        
        if ( $category['category_id'] != 1 && $user_login !== null && !in_array ($user_login, $cusers) )
        {
            continue;
        }
        
        $selected = '';
        if ( $selected_category == $category['category_id'] )
        {
            if ( $remove_selected )
            {
                continue;
            }
            
            $selected = ' selected="selected"';
        }
        
        $dropdown .= "<option value=\"{$category['category_id']}\"$selected>{$category['name']}</option>\n";
    }
    
    $dropdown .= "</select>\n";
    
    return $dropdown;
}

/**
 * Generates HTML for a category selection menu
 * @param string $user_login Login in name of user to determine what categories to show.
 * @param int|array $selected_categories Preselected category IDs. If only one category is selected, then a single integer can be given.
 * @param bool $invert_selected Whether or not to invert the selection.
 * @return string HTML for category selection menu.
 */
function build_category_selection ( $user_login = null, $selected_categories = array(1), $invert_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    if ( !is_array ($selected_categories) )
    {
        $selected_categories = array ($selected_categories);
    }
    
    ob_start();
    $line_break = '';
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        $cusers = explode (',', $category['users']);
        
        if ( $category['category_id'] != 1 && $user_login !== null && !in_array ($user_login, $cusers) )
        {
            continue;
        }
        
        $id = $category['category_id'];
        $name = $category['name'];
        
        $selected = '';
        if ( in_array ($id, $selected_categories) != $invert_selected )
        {
            $selected = ' checked="checked"';
        }
        
        echo $line_break . "<input type=\"checkbox\" name=\"category[$id]\" value=\"$id\" id=\"category_$id\"$selected /> <label for=\"category_$id\">$name</label>";
        $line_break = '<br />';
    }
    
    return ob_get_clean();
}

/**
 * Generates HTML for an author selection menu
 * @param int|array $selected_authors Preselected author usernames. If only one author is selected, then a single username can be given.
 * @param bool $invert_selected Whether or not to invert the selection.
 * @return string HTML for author selection menu.
 */
function build_author_selection ( $selected_authors = array(1), $invert_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);
    
    if ( !is_array ($selected_authors) )
    {
        $selected_authors = array ($selected_authors);
    }
    
    ob_start();
    $line_break = '';
    $i = 0;
    foreach ( $file as $author )
    {
        $author = get_line_data ('users', $author);
        
        $selected = '';
        
        $username = $author['username'];
        $nickname = $author['nickname'];
        
        if ( in_array ($username, $selected_authors) != $invert_selected )
        {
            $selected = ' checked="checked"';
        }
        
        echo $line_break . "<input type=\"checkbox\" name=\"author[$username]\" value=\"$username\" id=\"author_$i\"$selected /> <label for=\"author_$i\">$nickname</label>";
        $line_break = '<br />';
        
        ++$i;
    }
    
    return ob_get_clean();
}

/**
 * Checks if a given user is allowed to post in all given categories
 * @param string $user_login User's log in name to check
 * @param array $category_array Array of categories to check for access
 * @return mixed Returns the name of the first category which the user cannot post in. If there are
 * none, then NULL is returned
 */
function check_category_access ( $user_login, $category_array = array() )
{
	if ( sizeof ($category_array) > 0 )
	{
		$file = file (FNEWS_ROOT_PATH . 'categories.php');
		array_shift ($file);

		foreach ( $file as $category )
		{
			$category = get_line_data ('categories', $category);

			if ( $category['category_id'] == 1 )
			{
				continue;
			}

			if ( in_array ($category['category_id'], $category_array) )
			{
				$cusers = explode (',', $category['users']);
				if ( !in_array ($user_login, $cusers) )
				{
					return $category['name'];
				}
			}
		}
	}

	return NULL;
}

/**
 * Check if a given category ID exists
 * @param int $category_id Category ID to check that exists
 * @return bool Returns true if the given category ID exists, otherwise false.
 */
function category_exists ( $category_id = -1 )
{
	$category_id = (int)$category_id;

	$file = file (FNEWS_ROOT_PATH . 'categories.php');
	array_shift ($file);

	$found = false;
	foreach ( $file as $category )
	{
		$category = get_line_data ('categories', $category);
		if ( (int)$category['category_id'] === $category_id )
		{
			$found = true;
		}
	}

	return $found;
}

/**
 * Writes data to a specified file.
 * @param string $filename File name of file to write data to.
 * @param string $accessmode Mode of access.
 * @param string $data Data to be written to the file.
 */
function safe_write ( $filename, $accessmode, $data )
{
	global $ind17, $error2;

	if ( !($fp = @fopen (FNEWS_ROOT_PATH . $filename, $accessmode)) )
	{
		trigger_error (sprintf ($error2, $filename), E_USER_WARNING);
	}
    
    @flock ($fp, LOCK_EX);
    fputs ($fp, $data, strlen ($data));
    @flock ($fp, LOCK_UN);
    fclose ($fp);
}

/**
 * Generates HTML for use in checkboxes.
 * @param int $value The value to match against
 * @param int $match The value which $value must match to generate the HTML
 * @return string Returns HTML to select a checkbox if $value equals $match
 */
function checkbox_checked ( $value, $match = 1 )
{
	return $value == $match ? ' checked="checked"' : '';
}

/**
 * Formats the given message.
 * @param string $message Message to format
 * @param bool $allowHTML Allow HTML in message
 * @param bool $allowBBCode Allow BBCode in message
 * @param bool $allowSmileys Allow Smileys in message
 * @param bool $wordFilter Use the word filter on this message
 * @param bool $clearHTML Clear HTML completely from the message
 * @return string Formatted message
 */
function format_message ( $message, $allowHTML, $allowBBCode, $allowSmileys, $wordFilter, $clearHTML = false )
{
	if ( $wordFilter )
	{
		$message = filter_badwords ($message);
	}

    if ( $clearHTML )
    {
        $message = html_entity_decode ($message);
        $message = strip_tags ($message);
    }
	else if ( $allowHTML )
	{
		$message = html_entity_decode ($message);
	}

	if ( $allowBBCode )
	{
		$message = replace_bbcode ($message);
	}

	if ( $allowSmileys )
	{
		$message = replace_smileys ($message);
	}

	return $message;
}

/**
 * Parses news text or news data so that it is displayed along with its template.
 * @param string|array $news_string News text to parse, or the array from the required news file.
 * @param array $settings Override settings when parsing.
 * @return Parsed news text
 */
function parse_news_to_view ( $news_string, $settings = array() )
{
	assert (is_array ($settings));
	
	global $fsnw, $wfpost, $ht, $smilies, $bb, $stfpop, $compop,
		$furl, $fullnewsh, $fullnewsw, $fullnewss, $fullnewsz, $fslink, $datefor,
		$stflink, $stfheight, $stfwidth, $stfscrolls, $stfresize, $pclink, $datefor,
		$link_headline_fullstory, $hurl, $comheight, $comwidth, $comscrolls,
		$comresize;

	$news_text = array();

	if ( !empty ($news_string) )
	{
		$icon = '';
		$email = '';
		$writer = '';
		$link_full_news = '';
		$link_comments = '';
		$link_tell_friend = '';

		$article = $news_string;
		if ( !is_array ($news_string) )
		{
			$article = get_line_data ('news', $news_string);
		}

		// Get the template HTML
		$news_tmpl = get_template ((isset ($settings['template']) ? $settings['template'] : 'news_temp') . '.php', false);
		$other_qs = clean_query_string();
		
		$news_url = isset ($settings['news_url']) ? $settings['news_url'] : '';
		$sep = ( strpos ($news_url, '?') === false ) ? '?' : '&amp;';

		// Create the 'read more...' link
		if ( $article['fullnews'] != '' )
		{
			if ( $fsnw )
			{
				$link_full_news = '<a href="' . $furl . '/fullnews.php?fn_id=' . $article['news_id'] . '" onclick="window.open(this.href,\'\',\'height=' . $fullnewsh . ',width=' . $fullnewsw . ',toolbar=no,menubar=no,scrollbars=' . $fullnewss . ',resizable=' . $fullnewsz . '\'); return false">' . $fslink . '</a>';
			}
			else
			{
				$link_full_news = '<a href="' . $news_url . $sep . 'fn_mode=fullnews&amp;fn_id=' . $article['news_id'] . $other_qs . '">' . $fslink . '</a>';
			}
		}

		// Create the comments link
		if ( $compop )
		{
			$link_comments = '<a href="' . $furl . '/comments.php?fn_id=' . $article['news_id'] . '" onclick="window.open(this.href,\'\',\'height=' . $comheight . ',width=' . $comwidth . ',toolbar=no,menubar=no,scrollbars=' . $comscrolls . ',resizable=' . $comresize . '\'); return false">' . $pclink . '</a>';
		}
		else
		{
			$link_comments = '<a href="' . $news_url . $sep . 'fn_mode=comments&amp;fn_id=' . $article['news_id'] . $other_qs . '">' . $pclink . '</a>';
		}

		// Create the tell a friend link
		if ( $stfpop )
		{
			$link_tell_friend = '<a href="' . $furl . '/send.php?fn_id=' . $article['news_id'] . '" onclick="window.open(this.href,\'\',\'height=' . $stfheight . ',width=' . $stfwidth . ',toolbar=no,menubar=no,scrollbars=' . $stfscrolls . ',resizable=' . $stfresize . '\'); return false">' . $stflink . '</a>';
		}
		else
		{
			$link_tell_friend = '<a href="' . $news_url . $sep . 'fn_mode=send&amp;fn_id=' . $article['news_id'] . $other_qs . '">' . $stflink . '</a>';
		}

		// Make sure the number of comments is 0 or above.
		$num_comments = max ((int)$article['numcomments'], 0);

		// Get author information
		$author = get_author ($article['author']);
		if ( $author === false )
		{
			$author = array ('showemail' => false, 'nick' => $article['author']);
		}

		// Create the icon
		if ( strpos ($news_tmpl, '{icon}') !== false && !empty ($author['icon']) )
		{
			$icon = '<img src="' . $author['icon'] . '" alt="" />';
		}

		// Put the writer's name with his email as a link, or in some cases not.
		$email = ( $author['showemail'] ) ? $author['email'] : '';
		if ( !$email )
		{
			$writer = $author['nick'];
		}
		else
		{
			$writer = '<a href="mailto:' . $author['email'] . '">' . $author['nick'] . '</a>';
		}

		$article['headline'] = format_message ($article['headline'], $ht, $bb, $smilies, $wfpost);
		$article['shortnews'] = format_message ($article['shortnews'], $ht, $bb, $smilies, $wfpost);
		$article['fullnews'] = format_message ($article['fullnews'], $ht, $bb, $smilies, $wfpost);
        
        // Get our new lines back
        if ( !$ht && (!isset ($use_wysiwyg) || !$use_wysiwyg) )
        {
            $article['shortnews'] = str_replace ('&br;', '<br />', $article['shortnews']);
            $article['fullnews'] = str_replace ('&br;', '<br />', $article['fullnews']);
        }
        else
        {
            // Need to be a bit smarter about new lines.
            $article['shortnews'] = str_replace ('&br;', "\n", $article['shortnews']);
            $article['fullnews'] = str_replace ('&br;', "\n", $article['fullnews']);
            
            $article['shortnews'] = preg_replace ("#([^>\s])(&br;\s*){2,}([^<])#m", '$1<br /><br />$3', $article['shortnews']);
            $article['fullnews'] = preg_replace ("#([^>\s])(&br;\s*){2,}([^<])#m", '$1<br /><br />$3', $article['fullnews']);
        }

		$file = file (FNEWS_ROOT_PATH . 'categories.php');
		array_shift ($file);
		$categories = explode (',', $article['categories']);
		$cat_icon = '';
		$cat_id = 0;
		$cat_name = '';
		
		$category_filter = isset ($settings['category']) ? $settings['category'] : array();
		$num_category_filter = sizeof ($category_filter);
		
		foreach ( $file as $category )
		{
			$category = get_line_data ('categories', $category);

			if ( ($num_category_filter > 0 && in_array ($category['category_id'], $category_filter)) ||
				($num_category_filter == 0 && in_array ($category['category_id'], $categories)) )
			{
				$cat_icon = $category['icon'] != '' ? '<img src="' . $category['icon'] . '" alt="" />' : '';
				$cat_id = $category['category_id'];
				$cat_name = $category['name'];
				break;
			}
		}

		$news_text = array (
			'post_id'		=> $article['news_id'],
			'link_tell_friend'=> utf8_htmlentities ($link_tell_friend),
			'link_full_news'	=> utf8_htmlentities ($link_full_news),
			'subject'		=> utf8_htmlentities ($article['headline']),
			'description'	=> utf8_htmlentities ($article['description']),
			'writer'		=> utf8_htmlentities ($writer),
			'email'		=> utf8_htmlentities ($email),
			'date'		=> date ($datefor, (int)$article['timestamp']),
			'icon'		=> $icon,
			'news'		=> utf8_htmlentities ($article['shortnews']),
			'fullnews'		=> utf8_htmlentities ($article['fullnews']),
			'cat_icon'		=> $cat_icon,
			'cat_id'		=> $cat_id,
			'cat_name'		=> utf8_htmlentities ($cat_name),
            'nrc'			=> $num_comments,
            'link_comments'	=> utf8_htmlentities ($link_comments),
        );

		// Replace in the values!
		$news_tmpl = replace_masks ($news_tmpl, array (
			'post_id'		=> $news_text['post_id'],
			'user'		=> $news_text['writer'],
			'date'		=> $news_text['date'],
			'icon'		=> $news_text['icon'],
			'send'		=> $news_text['link_tell_friend'],
			'nrc'			=> $news_text['nrc'],
			'cat_id'		=> $news_text['cat_id'],
			'cat_name'		=> $news_text['cat_name'],
			'cat_icon'		=> $news_text['cat_icon'],
			'fullstory'		=> $news_text['link_full_news'],
			'comments'		=> $news_text['link_comments'],
			'subject'		=> '<a id="fus_' . $news_text['post_id'] . '"></a>' . $news_text['subject'],
			'news'		=> $news_text['news'],
			'description'	=> $news_text['description']
		));

		$news_text += array ('display' => $news_tmpl);
	}

	return $news_text;
}

/**
 * Checks and returns the number of comments awaiting to be validated.
 * @return int Number of comments awaiting to be validated.
 */
function get_pending_comments()
{
	global $com_validation;
	if ( !$com_validation )
	{
		return 0;
	}

	$count = 0;

	$file = get_ordered_toc();
	foreach ( $file as $value )
	{
		$toc = get_line_data ('news_toc', $value);
		if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php') )
		{
			continue;
		}

		$news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        if ( sizeof ($news_file) <= 2 )
        {
            continue;
        }
        
		array_shift ($news_file);
		array_shift ($news_file);

		foreach ( $news_file as $value2 )
		{
			$comment = get_line_data ('comments', $value2);
			if ( $comment['validated'] == 0 )
			{
				++$count;
			}
		}
	}

	return $count;
}

function get_validated_comments_for ( $news_id )
{
    global $com_validation;

    $news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($news_file);
    array_shift ($news_file);
    
    $validated_comments = array();
    foreach ( $news_file as $comment )
    {
        $comment = get_line_data ('comments', $comment);
        if ( $comment['validated'] )
        {
            $validated_comments[] = $comment;
        }
    }
    
    return $validated_comments;
}

// PHP 4.3.2 compatability
if ( !function_exists ('session_regenerate_id') )
{
	/**
	 * Regenerates the internal (PHP) session id
	 * @return bool True is successful, otherwise false.
	 */
	function session_regenerate_id()
	{
		$str = '';
		mt_srand ((double)microtime() * 100000);
		for ( $i = 0; $i < 32; $i++ )
		{
			$x = mt_rand (1, 3);
			$str .= ($x == 1) ? chr (mt_rand (48, 57)) : (( $x == 2 ) ? chr (mt_rand (65, 90)) : chr (mt_rand (97, 122)));
		}

		if ( session_id ($str) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

/**
 * Login session functions
 */

/**
 * Creates a pseudo-random string of alphanumeric characters of a specified length.
 * This function is shared by the installer too to create the login identifier
 * @param int $length Length of the pseudo-random string
 * @return string Pseudo-random string
 */
function create_security_id ( $length = 32 )
{
	$str = '';
	mt_srand ((double)microtime() * 100000);
	for ( $i = 0; $i < $length; $i++ )
	{
		$x = mt_rand (1, 3);
		$str .= ($x == 1) ? chr (mt_rand (48, 57)) : (( $x == 2 ) ? chr (mt_rand (65, 90)) : chr (mt_rand (97, 122)));
	}

	return $str;
}

/**
 * Creates a login session for the given user log in name.
 * @param string $uid Log in name of the user logging in
 * @param int $autologin Whether or not the user wishes to auto log in next time he/she visits the page
 * @return array User data array for the newly logged in user.
 */
function login_session_create ( $uid, $autologin = 0 )
{
	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);

	$user_ip = get_ip();
	$current_time = time();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( $login['user_id'] == $uid && $login['autologin'] != 1 )
		{
			// Purge old existing security id
			continue;
		}

		if ( $current_time <= ($login['login_time'] + 1800) )
		{
			$write .= $login_data;
		}
	}

	$security_id = create_security_id();

	$write .= $security_id . '|<|' . $uid . '|<|' . $user_ip . '|<|' . $autologin . '|<|' . $current_time . '|<|' . "\n";

	safe_write ('logins.php', 'wb', $write);

	setcookie ('fus_uid', $uid, $current_time + (365 * 86400));
	setcookie ('fus_sid', $security_id, $current_time + (365 * 86400));
	
    $userfile = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($userfile);

    $userdata = array();
    foreach ( $userfile as $userinfo )
    {
        $user = get_line_data ('users', $userinfo);

        if ( $user['username'] == $uid )
        {
            $email = explode ('=', $user['email']);
            $userdata = array (
                'user'  => $user['username'],
                'nick'  => $user['nickname'],
                'showemail' => $email[0],
                'email' => $email[1],
                'icon'  => $user['icon'],
                'offset'    => (int)$user['timeoffset'],
                'password'  => $user['passwordhash'],
                'level' => (int)$user['level']
            );
            
            break;
        }
    }
    
    return $userdata;
}

/**
 * Updates the user's log in session time, and removes old log in sessions from the data file
 * @param string $uid User log in name of the user logged in.
 * @param string $sid Session log in ID for the user
 * @return array If an invalid $uid or $sid is given, a blank array, otherwise the data for the
 * logged in user.
 */
function login_session_update ( $uid, $sid )
{
	if ( !$uid || !$sid )
	{
		return array();
	}

	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);
	
	$userfile = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($userfile);

	$valid_sid = false;
	$userdata = NULL;
	$current_time = time();
	$user_ip = get_ip();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( ($current_time >= ($login['login_time'] + 1800)) && $login['autologin'] != 1 )
		{
			continue;
		}

		if ( $login['security_id'] != $sid )
		{
			$write .= $login_data;
			continue;
		}

		if ( ($user_ip != $login['ip']) || ($uid != $login['user_id']) )
		{
			continue;
		}

		foreach ( $userfile as $userinfo )
		{
			$user = get_line_data ('users', $userinfo);

			if ( $user['username'] == $login['user_id'] )
			{
				$valid_sid = true;

				$email = explode ('=', $user['email']);
				$userdata = array (
					'user'	=> $user['username'],
					'nick'	=> $user['nickname'],
					'showemail'	=> $email[0],
					'email'	=> $email[1],
					'icon'	=> $user['icon'],
					'offset'	=> (int)$user['timeoffset'],
					'password'	=> $user['passwordhash'],
					'level'	=> (int)$user['level']
				);

				$write .= $login['security_id'] . '|<|' . $login['user_id'] . '|<|' . $login['ip'] . '|<|' . $login['autologin'] . '|<|' . $current_time . '|<|' . "\n";
				break;
			}
		}
	}

	safe_write ('logins.php', 'wb', $write);

	if ( !$valid_sid || $userdata === NULL )
	{
		setcookie ('fus_sid', '', $current_time - 86400);
		setcookie ('fus_uid', '', $current_time - 86400);
	}

	return $userdata;
}

/**
 * Destroy the log in session for the given log in session ID and removes
 * old log in data from the data file
 * @param string $sid Log in session ID to destroy
 */
function login_session_destroy ( $sid )
{

	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);

	$valid_sid = false;
	$current_time = time();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( ($current_time >= ($login['login_time'] + 1800)) && $login['autologin'] != 1 )
		{
			continue;
		}

		if ( $login['security_id'] == $sid )
		{
			continue;
		}

		$write .= $login_data;
	}

	safe_write ('logins.php', 'wb', $write);
}

/**
 * Retrieves the file extension from given file.
 * @param string $filename Filename to get extension from
 * @return string Returns the file's extension
 */
function get_file_extension ( $filename )
{
    return strtolower (utf8_substr ($filename, strrpos ($filename, '.') + 1));
}

/**
 * Uploads an image to the directory in $directory.
 * @param int $file_number The ID number of the file in the $_FILES['F']['xxx'] array. If -1, then only one file was uploaded.
 * @param string $allowedExts The allowed file extensions to be uploaded, separated by HTML encoded |.
 * @param string $directory The directory to upload the file to.
 * @return string Message showing whether the upload was successful or not.
 * @todo Make this function more generalized. I don't like the beginning if() statement
 * which could be solved by passing a custom array instead of specifying a file number.
 * Also the returned messages need to be turned into return codes.
 */
function upload_file ( $file_number, $allowedExts, $directory = './uploads/' )
{
	global $ind252, $ind254, $ind255, $ind255a, $ind256, $ind257;

	if ( $file_number == -1 )
	{
		// We only have one file uploaded...
		if ( $_FILES['F']['error'] != UPLOAD_ERR_OK )
		{
			// Problem with the upload. Let's take a closer look
			switch ( $_FILES['F']['error'] )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					return sprintf ($ind252, $_FILES['F']['name']);
				break;

				case UPLOAD_ERR_PARTIAL:
				break;

				case UPLOAD_ERR_NO_FILE:
					return $ind254;
				break;

				default:
					return 'Uh-oh, this isn\'t supposed to happen. Error code: ' . $_FILES['F']['error'];
				break;
			}
		}

		$filename = ( isset ($_FILES['F']['name']) ) ? $_FILES['F']['name'] : '';
		$tmpname = ( isset ($_FILES['F']['tmp_name']) ) ? $_FILES['F']['tmp_name'] : '';
	}
	else
	{
		if ( $_FILES['F']['error'][$file_number] != UPLOAD_ERR_OK )
		{
			// Problem with the upload. Let's take a closer look
			switch ( $_FILES['F']['error'][$file_number] )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					return sprintf ($ind252, $_FILES['F']['name'][$file_number]);
				break;

				case UPLOAD_ERR_PARTIAL:
				break;

				case UPLOAD_ERR_NO_FILE:
					return $ind254;
				break;

				default:
					return 'Uh-oh, this isn\'t supposed to happen.';
				break;
			}
		}

		$filename = ( isset ($_FILES['F']['name'][$file_number]) ) ? $_FILES['F']['name'][$file_number] : '';
		$tmpname = ( isset ($_FILES['F']['tmp_name'][$file_number]) ) ? $_FILES['F']['tmp_name'][$file_number] : '';
	}

	$extensions = explode ('|', $allowedExts);
	$num_exts = sizeof ($extensions);
	$valid_ext = false;

	foreach ( $extensions as $ext )
	{
		if ( get_file_extension ($filename) == strtolower ($ext) )
		{
			$valid_ext = true;
			break;
		}
	}

	if ( !$valid_ext )
	{
		return $ind255 . $allowedExts . $ind255a;
	}

	$uploaded = false;

	$origname = $filename;
	$i = 1;

	while ( file_exists ($directory . $filename) )
	{
		$filename = utf8_substr ($origname, 0, strrpos ($origname, '.')) . '_' . $i . strrchr ($origname, '.');
		$i++;
	}

	if ( @move_uploaded_file ($tmpname, $directory . $filename) )
	{
		$uploaded = true;
	}
	else if ( @copy ($tmpname, $directory . $filename) )
	{
		$uploaded = true;
	}

	if ( !$uploaded )
	{
		return $ind256;
	}

	chmod ($directory . $filename, 0644);
	if ( file_exists ($tmpname) )
	{
		unlink ($tmpname);
	}

	return $filename . ' ' . $ind257;
}

/**
 * Generates HTML for a redirect page
 * @param string $message Message to display before the user is redirected
 * @param string $return_url URL to return to
 * @param string $return_text Text to display to return to the given URL
 * @return string HTML to display the redirect page
 */
function make_redirect ( $message, $return_url = '', $return_text = '' )
{
	global $ind76;

	$text = '<p>' . $message . '</p>';
	if ( $return_url && $return_text )
	{
		$text .= '<p><a href="' . $return_url . '">' . $return_text . '</a></p>';
	}
	$text .= '<p><a href="./">' . $ind76 . '</a></p>';

	return $text;
}

/**
 * Failsafe method of retrieving $_SERVER['QUERY_STRING']
 * @return string Query string
 */
function get_query_string()
{
	return (isset ($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
}

/**
 * Gets query string, removing any Fusion News added variables.
 * @return string Clean string query (without any Fusion News added variables)
 */
function clean_query_string()
{
	$query_string = get_query_string();
	$query_string = preg_replace ('/fn_[a-z_]+=[^&]*&?/', '', $query_string);
	$query_string = str_replace ('&', '&amp;', $query_string);

	// Clear possible leading ampersand
	if ( utf8_substr ($query_string, -5) == '&amp;' )
	{
		$query_string = utf8_substr ($query_string, 0, -5);
	}

	if ( empty ($query_string) )
	{
		return '';
	}
	else
	{
		return '&amp;' . $query_string;
	}
}

/**
 * Generates HTML for the form security
 * @return string HTML for the form security
 */
function get_form_security()
{
	$timestamp = time();

	return '<input type="hidden" name="post_form" value="' . get_form_character ($timestamp) . '" />' . "\n" .
			'<input type="hidden" name="post_time" value="' . $timestamp . '" />' . "\n";
}

/**
 * Gets the form's security character
 * @param int $timestamp Timestamp for the form
 * @return string Form's security character
 */
function get_form_character ( $timestamp )
{
	global $fusion_id;

	$total = 0;
	$time = (string)$timestamp;
	for ( $i = 0, $end = strlen ($time); $i < $end; $i++ )
	{
		$total += $i * $time{$i};
	}

	return $fusion_id{0x1F ^ ($total % 32)};
}

/**
 * Checks whether the given form character is valid
 * @return bool True if the given form character is valid, otherwise false.
 */
function check_form_character()
{
	global $PVARS;

	if ( !isset ($PVARS['post_time']) || !isset ($PVARS['post_form']) )
	{
		return false;
	}

	if ( $PVARS['post_time'] == '' || $PVARS['post_form'] == '' )
	{
		return false;
	}

	if ( ($PVARS['post_time'] + 3600) <= time() )
	{
		// Allow a maximum of one hour to submit a form.
		return false;
	}

	return (get_form_character ($PVARS['post_time']) == $PVARS['post_form']);
}

/**
 * Creates a list of page numbers, reducing the list if
 * the page list is too long to show.
 * @param int $num_pages Number of pages
 * @param int $current_page The current page number
 * @param string $url URL of the page to go to with the selected page number
 * @param string $page_variable Variable to use in the query string for the page number
 * @return string The list of page numbers.
 */
function create_page_numbers ( $num_pages, $current_page, $url, $page_variable )
{
	static $page_reduction_limit = 15;
	$pagination_text = '';

	// Replace intitial ampersand just in case we have a query string created by clean_query_string().
	$url = str_replace ('?&amp;', '?', $url);

	$i = 1;

	$should_reduce = ($num_pages >= $page_reduction_limit);
	$prev_page_no = $current_page - 1;
	$next_page_no = $current_page + 1;
	$two_from_last = $num_pages - 2;
	while ( $i <= $num_pages )
	{
		if ( !$should_reduce ||
			$i <= 3 ||
			($i >= $prev_page_no && $i <= $next_page_no) ||
			$i >= $two_from_last )
		{
			if ( $current_page == $i )
			{
				$pagination_text .= ' <b class="fn-news-pagination-current">' . $i . '</b>';
			}
			else
			{
				$pagination_text .= ' <a href="' . $url . '&amp;' . $page_variable . '=' . $i . '" class="fn-news-pagination-number">' . $i . '</a>';
			}

			++$i;
		}
		else
		{
			$pagination_text .= ' &#133;';

			if ( $i < $prev_page_no && $prev_page_no < $two_from_last )
			{
				$i = $prev_page_no;
			}
			else if ( $i < $two_from_last )
			{
				$i = $two_from_last;
			}
		}
	}

	// utf8_substr to remove leading space.
	return utf8_substr ($pagination_text, 1);
}

/**
 * Parses field masks in given text
 * @param string $template Text containing field masks to replace
 * @param array $masks Array of masks (key contains field mask, value contains text to replace the mask with)
 * @return string Text with field masks parsed.
 */
function replace_masks ( $template, $masks )
{
	foreach ( $masks as $key => $value )
	{
		$template = str_replace ('{' . $key . '}', $value, $template);
	}

	return $template;
}

if ( !function_exists ('array_combine') )
{
	/**
	 * This function exists in PHP5 by default.
	 * Creates an array by using one array for keys and another for its values
	 * @param array $keys Array of keys to be used.
	 * @param array $values Array of values to be used
	 * @return array|bool Returns the combined array, FALSE if the number of elements for each array isn't equal or if the arrays are empty.
	 */
	function array_combine ( $keys, $values )
	{
		$num_keys = sizeof ($keys);
		$num_values = sizeof ($values);

		if ( $num_keys != $num_values )
		{
			trigger_error ('array_combine(): Both parameters should have an equal number of elements.', E_USER_WARNING);
			return false;
		}

		$return = array();
		for ( $i = 0; $i < $num_keys; $i++ )
		{
			$return[$keys[$i]] = $values[$i];
		}

		return $return;
	}
}

/**
 * Gets the current URL, including the query string. Made to work with Apache and IIS.
 */
function current_url()
{
    $url = 'http';
    if ( isset ($_SERVER['HTTPS']) )
    {
        $url .= 's';
    }
    
    if ( isset ($_SERVER['REQUEST_URI']) )
    {
        $request_uri = $_SERVER['REQUEST_URI'];
    }
    else
    {
        $request_uri = $_SERVER['SCRIPT_NAME'];
        if ( isset ($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '' )
        {
            $request_uri .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    $url .= '://' . $_SERVER['SERVER_NAME'] . $request_uri;
    
    return $url;
}

/**
 */
function generate_captcha_code ( $session_id, $news_id, $user_ip, $type )
{
    $captcha_code = '';
	for ( $i = 0; $i < 5; $i++ )
	{
		$x = mt_rand (1, 10);
		$captcha_code .= ( $x % 2 ) ? chr (mt_rand (48, 57)) : chr (mt_rand (65, 90));
	}
    
    save_captcha_code ($session_id, $captcha_code, $news_id, $user_ip, $type);
    
    return $captcha_code;
}

function get_captcha_code ( $session_id )
{
    $file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $session['session_id'] == $session_id )
		{
			return $session['code'];
		}
	}

	return null;
}

function save_captcha_code ( $session_id, $captcha_code, $news_id, $user_ip, $type )
{
    global $error1;

    $file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	$current_time = time();
	$data = '<?php die (\'' . $error1 . '\'); ?>' . "\n";
	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $session['session_id'] == $session_id )
		{
			continue;
		}

		if ( (($session['last_visit'] + 600) >= $current_time) && ($session['ip'] != $user_ip) )
		{
			$data .= $value;
		}
	}

	$data .= create_line_data ('sessions', true, $session_id, $captcha_code, $news_id, $user_ip, $current_time, $type);
	safe_write ('sessions.php', 'wb', $data);
}

/**
 * @param string $file File to get fields for.
 * @return array List of fields for file type.
 */
function get_fields_for_file ( $file )
{
    static $valid_files = array (
        'badwords'      => array ('find', 'replace', 'case_sensitive', 'type'),
        'categories'    => array ('category_id', 'name', 'icon', 'users'),
        'comments'      => array ('ip', 'validated', 'message', 'author', 'email', 'timestamp', 'comment_id'),
        'flood'         => array ('ip', 'timestamp'),
        'logins'        => array ('security_id', 'user_id', 'ip', 'autologin', 'login_time'),
        'news_toc'      => array ('news_id', 'timestamp', 'author', 'headline', 'categories'),
        'news'          => array ('shortnews', 'fullnews', 'author', 'headline', 'description', 'categories', 'timestamp', 'numcomments', 'news_id'),
        'sessions'      => array ('session_id', 'code', 'news_id', 'ip', 'last_visit', 'page'),
        'smillies'      => array ('smiley_id', 'bbcode', 'image'),
        'users'         => array ('username', 'nickname', 'email', 'icon', 'timeoffset', 'passwordhash', 'level')
    );

    if ( !array_key_exists ($file, $valid_files) )
    {
        trigger_error ('get_line_data(): Invalid data file name given', E_USER_WARNING);
	}
    
    return $valid_files[$file];
}

/**
 * Creates a line of data for specified file.
 * @param string $file File type to create data for.
 * @param bool $trailing_eol Whether or not to append \n to end of line
 * @param string $data Data to use
 * @param string Line of data for specified file
 */
function create_line_data ( $file, $trailing_eol, $data )
{
    $data = func_get_args();
    array_shift ($data); // $file
    array_shift ($data); // $trailing_eol
    
    $fields = get_fields_for_file ($file);
    if ( sizeof ($fields) != sizeof ($data) )
    {
        trigger_error ('create_line_data(): The number of fields given does not much the number of fields required for file "' . $file . '"');
    }
    
    return implode ('|<|', $data) . '|<|' . ($trailing_eol ? "\n" : '');
}

/**
 * Gets an array of fields and their data from a specified line of data from a file.
 * @param string $file Data file the line of data was retrieved from
 * @param string $line_data Line of data to convert to an array
 * @param string $delimiter The delimiter between each field data
 * @return array|bool Array combining the field names for the specified file and the given data,
 * FALSE if no data was found or the line data was blank.
 */
function get_line_data ( $file, $line_data, $delimiter = '|<|' )
{
	if ( $line_data == '' )
	{
		trigger_error ('get_line_data(): No line data for ' . $file, E_USER_WARNING);
	}

	$data = explode ($delimiter, $line_data);
	if ( sizeof ($data) < 0 )
	{
		trigger_error ('get_line_data(): No data found in the given data.', E_USER_WARNING);
		return NULL;
	}

    $fields = get_fields_for_file ($file);
    
	array_pop ($data); // Pop new line off the end
	$return_data = array_combine ($fields, $data);

	return $return_data;
}

/**
 * Prepares a message for email
 * @param string $string String to prepare
 * @return string Prepared message for email.
 */
function prepare_string_for_mail ( $string )
{
    $message = wordwrap ($string, 70);
    
    return $message;
}

/**
 * Displays the output of the control panel.
 * @param string $title Page title
 * @param string $skin Skin to use
 * @param array $userdata User's login session data
 */
function display_output ( $title, $skin, $userdata )
{
	global $start_time,
		$ind163, $ind108, $ind109, $ind344, $ind169, $ind272, $ind165,
		$ind107, $ind281, $ind193, $ind194, $ind195, $ind170, $ind171,
        $ind400, $notice_buffer, $warning_buffer, $ind358, $ind357,
        $errored_out;

	// Now sort out the skin
	$links_list = array (
		array ($ind163, '', GUEST), // home
		array ($ind108, '?id=postnews', NEWS_REPORTER), // new post
		array ($ind109, '?id=editposts', NEWS_REPORTER), // edit posts
		array ($ind400, '?id=editprofile', NEWS_REPORTER), // edit profile
		array (array (
            NEWS_EDITOR => $ind344,
            NEWS_ADMIN => $ind169),
        '?id=admin', NEWS_EDITOR), // editor's panel/admin
		array ($ind272, '?id=view', GUEST), // view news
		array ($ind165, '?id=help', NEWS_REPORTER), // help/update
		array ($ind107, '?id=logout', NEWS_REPORTER) // logout
	);
	
	$cs1 = file_get_contents (FNEWS_ROOT_PATH . 'skins/' . $skin . '/index.html');
	if ( $cs1 === false )
	{
		trigger_error ($ind281, E_USER_ERROR);
	}

	$row_links = '';
	$col_links = '';
    
    $row_sep = '';
    $col_sep = '';
    
	$user_level = ( !isset ($userdata['level']) ) ? 0 : $userdata['level'];
    
	foreach ( $links_list as $link_data )
	{
		if ( !is_array ($link_data) )
		{
			continue;
		}

		if ( $user_level >= $link_data[2] )
		{
			$row_links .= $row_sep;
			$col_links .= $col_sep;

			$row_links .= '<a href="index.php' . $link_data[1] . '">';
			$col_links .= '<a href="index.php' . $link_data[1] . '">';
			if ( is_array ($link_data[0]) )
			{
				$row_links .= $link_data[0][$userdata['level']];
				$col_links .= $link_data[0][$userdata['level']];
			}
			else
			{
				$row_links .= $link_data[0];
				$col_links .= $link_data[0];
			}
			$row_links .= '</a>';
			$col_links .= '</a>';
            
            $row_sep = ' | ';
            $col_sep = '<br />';
		}
	}
    
	if ( has_access (NEWS_REPORTER) )
	{
		switch ( $userdata['level'] )
		{
			case 3:
				$status = $ind195;
				break;

			case 2:
				$status = $ind194;
				break;

			case 1:
				$status = $ind193;
				break;

			default:
				break;
		}

		$login = $ind170 . ' <b>' . $userdata['nick'] . '</b> [<b>' . $status . '</b>]';
	}
	else
	{
		$login = $ind171;
	}
    
    $content = ob_get_clean();

	if ( !$errored_out )
	{
		if ( $notice_buffer != '' )
		{
			$content = '<ul id="fn_notice"><li class="title">' . $ind357 . '</li>' . $notice_buffer . '</ul>' . $content;
		}

		if ( $warning_buffer != '' )
		{
			$content = '<ul id="fn_warning"><li class="title">' . $ind358 . '</li>' . $warning_buffer . '</ul>' . $content;
		}
	}

	$cs = replace_masks ($cs1, array (
		'main'  => $content,
		'title' => $title,
		'linksn'=> $row_links,
		'linksb'=> $col_links,
		'login' => $login,
		'curve' => FNEWS_CURVE
	));

	//-----------------------------------------------
	// Do this very last to get the most accurate
	// result possible
	//-----------------------------------------------
	$split = explode (' ', microtime());
	$end_time = (float)$split[0] + (float)$split[1];
	$cs = str_replace ('{loadtime}', sprintf ('%.6f', $end_time - $start_time), $cs);

	//-----------------------------------------------
	// Blammo, out comes the end product :p
	//-----------------------------------------------
	echo $cs;
}

/**
 * Error handling
 */
$warning_buffer = '';
$notice_buffer = '';
/**
 * When TRUE the page should stop wherever it is in processing, and just display the error messages
 * @global bool $errored_out
 */
$errored_out = false;

/**
 * Callback function for PHP error handling (not to be used directly - it will be called by PHP when needed)
 * @param int $errno Error number
 * @param string $errstr Error text
 * @param string $errfile File containing the error
 * @param int $errline Line number containing the error
 * @return bool False if the default PHP internal error handler should handle the error
 */
function fn_error_handler ( $errno, $errstr, $errfile, $errline )
{
	if ( !(error_reporting() & $errno) )
	{
		return true;
	}

	$errfile = str_replace (FNEWS_ROOT_PATH, '', str_replace ('\\', '/', $errfile));

	switch ( $errno )
	{
		// Very bad error indeed...
		// Show a complete failure page.
		case E_USER_ERROR:
		case E_ERROR:
            ob_end_clean();
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
				'<html xmlns="http://www.w3.org/1999/xhtml">',
				'<head>',
                '	<meta http-equiv="content-type" content="text/html; charset=utf-8" />',
				'	<title>Fusion News &bull; Error</title>',
				'	<style type="text/css">',
				'	<!--',
				'	body { background-color:#fff; color:#000; line-height:140%; font-size:100%; font-family:"Trebuchet MS", Helvetica, sans-serif }',
				'	blockquote { padding:0; margin: 0 30px }',
				'	#wrapper { width:95%; margin:0 auto }',
				'	//-->',
				'	</style>',
				'</head>',
				'<body>',
				'<h1>Fusion News has encountered a fatal error</h1>',
				'<div id="wrapper">',
				'<p>A fatal error was encountered by Fusion News, and cannot continue to run.</p>',
				'<p>The error message is as follows:</p>',
				'<blockquote><i>', $errstr, '</i></blockquote>',
				'<p>If there are any instructions in the error message above, please follow them to try to solve the problem. If the error repeats itself, after refreshing the page after 30 seconds, please create a new topic at the <a href="http://www.fusionnews.net/">Fusion News Support Forum</a>, and copy and paste the text below:</p>',
				'<blockquote><code>Error: ', $errstr, '<br />',
                'File: ', $errfile, '<br />',
                'Line No.: ', $errline, '<br />',
                'Version: ', FNEWS_CURVE,
                '</code></blockquote>',
				'</div>',
				'</body>',
				'</html>';

			exit;

			return true;
		break;

		case E_USER_NOTICE:
		case E_USER_WARNING:
            global $title, $ind17, $skin, $userdata;
			if ( !$title )
			{
				$title = $ind17;
			}

			echo '<div id="fn_warning">',
					'<li class="title">' . $ind17 . '</li>',
					'<li>' . $errstr . '</li>',
					'</div>', "\n";

			$errored_out = true;

			display_output ($title, $skin, $userdata);
			exit;

			return true;
		break;

		case E_NOTICE:
            global $notice_buffer;
            
			$notice_buffer .= '<li> ' . $errstr . ' in /' . $errfile . ' on line ' . $errline . '</li>' . "\n";

			return true;
		break;

		case E_WARNING:
            global $warning_buffer;
            
			$warning_buffer .= '<li>' . $errstr . ' in /' . $errfile . ' on line ' . $errline . '</li>' . "\n";

			return true;
		break;

		default:
		break;
	}

	return true;
}

?>
