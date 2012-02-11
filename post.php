<?php

/**
 * Single post page
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: post.php 393 2012-02-10 22:37:14Z xycaleth $
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

global $config;

$id = ( isset ($GVARS['fn_id']) ) ? (int)$GVARS['fn_id'] : 0;
$action = ( isset ($GVARS['fn_action']) ) ? $GVARS['fn_action'] : '';

if ( !function_exists ('parse_comments') )
{
	/**
	 * Parses the comments for display with the template
	 * @param string &$comment_text Comment message text
	 * @param string &$comment_author Name of the comment's author
	 * @param string $comment_email Email address of the author
	 */
	function parse_comments ( &$comment_text, &$comment_author, $comment_email )
	{
		global $config;
        
        $comment_text = str_replace ('&br;', ($config['comallowbr'] ? '<br />': ''), $comment_text);
        $comment_text = format_message ($comment_text, $config['htc'], $config['bbc'], $config['smilcom'], $config['wfcom']);

		if ( !empty ($comment_email) )
		{
			$comment_author = '<a href="mailto:' . $comment_email . '">' . $comment_author . '</a>';
		}
	}
}

if ( !headers_sent() )
{
	header ('Last-Modified: ' . gmdate ('D, d M Y H:i:s') . ' GMT');
	header ('Cache-Control: no-cache, must-revalidate');
	header ('Pragma: no-cache');
}

function on_error ( $error )
{
    echo $error;
    echo get_template ('com_footer.php', true);
    
    ob_end_flush();
}

ob_start();

echo get_template('com_header.php', true);

if ( !$id )
{
	on_error ($lang['com10']);
	return;
}

if ( is_ip_banned (get_ip()) )
{
	on_error ($lang['com3']);
	return;
}

if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $id . '.php') )
{
    on_error ($lang['com11']);
    return;
}

if ( !$action )
{
	$session_id = create_security_id();

	$post = get_post ($id);
	$news_info = parse_news_to_view ($post);

	$fn_page = 1;

    $validated_comments = get_moderated_comments ($id);
    
	$start = 0;
	$end = sizeof ($validated_comments);
	$pagination = '';
	$next_page = '';
	$prev_page = '';
	$qs = clean_query_string();

	if ( $config['comments_pages'] && $config['comments_per_page'] > 0 )
	{
		$fn_page = ( isset ($GVARS['fn_page']) ) ? (int)$GVARS['fn_page'] : $fn_page;
		$fn_page = max (1, $fn_page);

		if ( $end == 0 )
		{
			// Slight hack to display the pagination even if there are no
			// comments to display.
			$end = 1;
		}

        $num_pages = ceil ($end / $config['comments_per_page']);
        $separator = '';
		for ( $i = 0, $j = 1; $i < $end; $i += $config['comments_per_page'], $j++ )
		{
            $pagination .= $separator;
			if ( $j != $fn_page )
			{
				$pagination .= '<a href="?fn_mode=post&amp;fn_id=' . $id . '&amp;fn_page=' . $j . $qs . '">' . $j . '</a>';
			}
			else
			{
				$pagination .= '<b>' . $j . '</b>';
			}
            
            $separator = '&nbsp;';
		}

		$prev_page = ( ($fn_page - 1) >= 1 ) ? '<a href="?fn_mode=post&amp;fn_id=' . $id . '&amp;fn_page=' . ($fn_page - 1) . $qs . '">$1</a>' : '$1';
		$next_page = ( ($fn_page + 1) <= $num_pages ) ? '<a href="?fn_mode=post&amp;fn_id=' . $id . '&amp;fn_page=' . ($fn_page + 1) . $qs . '">$1</a>' : '$1';

		$start = $config['comments_per_page'] * ($fn_page - 1);
		$end = $start + $config['comments_per_page'];
		$end = ( $end > sizeof ($validated_comments) ) ? sizeof ($validated_comments) : $end;
	}

	//replace user variables
	$temp_short = get_template ('com_fulltemp.php', true);
	$temp_short .= '<script src="' . $config['furl'] . '/js/jsfunc.js" type="text/javascript"></script>' . "\n";
	$temp_short = replace_masks ($temp_short, array (
		'post_id'		=> $news_info['post_id'],
		'subject'		=> $news_info['subject'],
		'description'	=> $news_info['description'],
		'user'		=> $news_info['writer'],
		'date'		=> $news_info['date'],
		'send'		=> $news_info['link_tell_friend'],
		'news'		=> $news_info['news'],
		'fullstory'		=> $news_info['fullnews'],
		'icon'		=> $news_info['icon'],
		'nrc'			=> $news_info['nrc'],
		'com'			=> $news_info['link_comments'],
		'cat_id'		=> $news_info['cat_id'],
		'cat_name'		=> $news_info['cat_name'],
		'cat_icon'		=> $news_info['cat_icon'],
		'pagination'	=> $pagination
	));

    if ( preg_match ('#\{prev_page\|(.*?)\}#', $temp_short, $matches) )
    {
        $prev_page = str_replace ('$1', htmlspecialchars ($matches[1]), $prev_page);
        $temp_short = str_replace ($matches[0], $prev_page, $temp_short);
    }
    
    if ( preg_match ('#\{next_page\|(.*?)\}#', $temp_short, $matches) )
    {
        $next_page = str_replace ('$1', htmlspecialchars ($matches[1]), $next_page);
        $temp_short = str_replace ($matches[0], $next_page, $temp_short);
    }

	$comlen = '';
    if ( !is_commenting_disabled() )
    {
        $count = 0;
        $comment_template = get_template ('com_temp.php', true);
        $comments = '';

        $validated_comments = array_reverse ($validated_comments);
        foreach ( $validated_comments as $comment )
        {
            if ( $count < $start || $count >= $end )
            {
                // Valid comment, but not to be displayed on this post.
                $count++;
                continue;
            }

            parse_comments ($comment['message'], $comment['author'], $comment['email']);
            $commenthtml = $comment_template;

            $comments .= replace_masks ($commenthtml, array (
                'poster'	=> $comment['author'],
                'comment'	=> $comment['message'],
                'date'	=> date ($config['datefor'], (int)$comment['timestamp']),
                'posterip'	=> $comment['ip']
            ));

            $count++;
        }

        if ( empty ($comments) )
        {
            $comments = $lang['com12'];
        }

        $extras = show_extras ('comment_form', 'comment', $config['smilcom'], $config['bbc']);
        $box = $extras . '<textarea id="comment" name="comment" rows="$2" cols="$1"></textarea>';
        $temp_short = str_replace('{comments}', $comments, $temp_short);
        $temp_short = str_replace('[form]', '<form action="?fn_mode=post&amp;fn_action=post&amp;fn_id=' . $id . $qs . '" method="post" id="comment_form">', $temp_short);
        $temp_short = str_replace('[/form]', '</form>', $temp_short);
        $temp_short = str_replace('[buttons]', '<input type="hidden" name="confirm_id" value="' . $session_id . '" />
<input type="hidden" name="fn_next" value="' . htmlspecialchars (current_url()) . '" />
<input type="submit" id="com_Submit" value="' . $lang['com15'] . '" />
<input type="reset" value="' . $lang['com16'] . '" />', $temp_short);

        if ( $config['comlength'] <= 0 )
        {
            $temp_short = str_replace('[comlen]', '', $temp_short);
        }
        else
        {
            $comment_too_long = sprintf ($lang['com17'], $config['comlength']);
            $comlen .= <<< html
<script type="text/javascript">
//<![CDATA[
document.getElementById('comment').onkeyup = updateCharactersRemaining;
document.getElementById('comment').onkeydown = updateCharactersRemaining;
function updateCharactersRemaining ( e )
{
	var maxchars = {$config['comlength']};
	var comment = document.getElementById('comment');
	var comment_length = comment.value.length;
	var characters_left = maxchars - comment_length;

	if ( comment_length > maxchars )
	{
		comment.value = comment.value.substring (0, maxchars);
		characters_left = 0;
		alert("$comment_too_long");
	}

	document.getElementById('chars').value = characters_left;
}
//]]>
</script>
html;
            $temp_short = str_replace('[comlen]', '<input id="chars" name="chars" size="5" value="' . $config['comlength'] . '" disabled="disabled" />', $temp_short);
        }
        
        $name = ( isset ($_COOKIE['fn_comment_name']) ) ? $_COOKIE['fn_comment_name'] : '';
        $email = ( isset ($_COOKIE['fn_comment_email']) ) ? $_COOKIE['fn_comment_email'] : '';
        $remember = ( isset ($_COOKIE['fn_comment_remember']) ) ? 1 : 0;

        $temp_short = preg_replace ('/\[pwfld,\s*([0-9]+)\]/i', '<input type="password" size="$1" name="pass" />', $temp_short);
        $temp_short = preg_replace ('/\[namefld,\s*([0-9]+)\]/i', '<input type="text" size="$1" name="name" id="name" value="' . $name . '" />', $temp_short);
        $temp_short = preg_replace ('/\[mailfld,\s*([0-9]+)\]/i', '<input type="text" size="$1" name="email" id="email" value="' . $email . '" />', $temp_short);
        $temp_short = preg_replace ('/\[rememberchk]/', '<input type="checkbox" name="remember" value="1" ' . checkbox_checked ($remember) . '/>', $temp_short);
        $temp_short = preg_replace ('/\[comfld,\s*([0-9]+),\s*([0-9]+)]/i', $box, $temp_short);

        // Image verification
        if ( $config['com_captcha'] )
        {
            generate_captcha_code ($session_id, $id, get_ip(), 'comments');
            $temp_short = str_replace ('[securityimg]', '<img src="' . $config['furl'] . '/captcha.php?fn_type=comments&amp;fn_id=' . $id . '&amp;fn_sid=' . $session_id . '&amp;t=' . time() . '" alt="CAPTCHA" id="captcha" />', $temp_short);
            $temp_short = str_replace ('[securityfld]', '<input type="text" name="code" size="5" maxlength="5" />', $temp_short);
        }
        else
        {
            $temp_short = str_replace ('[securityimg]', '', $temp_short);
            $temp_short = str_replace ('[securityfld]', '', $temp_short);
        }

        $comlen .= '<script type="text/javascript">
//<![CDATA[
document.getElementById("com_Submit").onclick = function()
{
    var msg = document.getElementById ("comment");
    var name = document.getElementById ("name");
    
    if ( !msg.value.length || !name.value.length )
    {
        alert ("' . $lang['com18'] . '");
        return false;
    }';
    
        if ( $config['comlength'] > 0 )
        {
            $comlen .= '
        var maxCommentLength = ' . $config['comlength'] . ';
        if ( msg.value.length > maxCommentLength )
        {
            msg.value = msg.value.substring (0, maxCommentLength);
        }';
        }
        
        $comlen .= '
}
//]]>
</script>';
    }

	$temp_short .= $comlen;
	echo $temp_short;
}
//---------------

//Post Comment
elseif ( $action == 'post' )
{  /*id Post comment*/
    if ( is_commenting_disabled() )
    {
        return;
    }
    
	$comment	= ( isset ($PVARS['comment']) ) ? fn_trim ($PVARS['comment']) : '';
	$name		= ( isset ($PVARS['name']) ) ? fn_trim(single_line (utf8_substr($PVARS["name"], 0, 40))) : '';
	$email	= ( isset ($PVARS['email']) ) ? fn_trim (single_line ($PVARS['email'])) : '';
	$pass		= ( isset ($PVARS['pass']) ) ? fn_trim (utf8_substr ($PVARS['pass'], 0, 40)) : '';
	$code		= ( isset ($PVARS['code']) ) ? $PVARS['code'] : '';
	$confirm_id	= ( isset ($PVARS['confirm_id']) ) ? $PVARS['confirm_id'] : '';
    $remember   = ( isset ($PVARS['remember']) );
    $next       = ( isset ($PVARS['fn_next']) ) ? html_entity_decode ($PVARS['fn_next']) : null;
    
    if ( $next === null )
    {
        return;
    }
    else
    {
        $next_url = parse_url ($next);
        $current_url = parse_url (current_url());
        
        // Don't redirect to completely different website.
        if ( $next_url['scheme'] != $current_url['scheme'] || $next_url['host'] != $current_url['host'] )
        {
            return;
        }
    }
    
    if ( $config['com_captcha'] && !is_valid_captcha_code ($code, $confirm_id, $id, 'comments') )
	{
		echo $lang['com13'];
	}
	else if ( !$name || !$comment )
	{
		echo $lang['com1'];
	}
	else if ( $config['comlength'] > 0 && utf8_strlen ($comment) > $config['comlength'] )
	{
		printf ($lang['com14'], $config['comlength']);
	}
	else if ( !is_valid_email ($email) && $email != '' )
	{
		echo $lang['com2'];
	}
	elseif ( is_flooding() )
	{
		echo $lang['com4'] . ' ' . $config['floodtime'] . ' ' . $lang['com5'];
	}
	else
	{
		$news_user = false;
		$passok = false;

		$file = file (FNEWS_ROOT_PATH . 'users.php');
		array_shift ($file);

		$passhash = md5 ($pass);

		foreach ( $file as $value )
		{
			$user = get_line_data ('users', $value);
			if ( $name == $user['username'] || $name == $user['nickname'] )
			{
				$news_user = true;
				if ( $passhash == $user['passwordhash'] )
				{
					$name = $user['nickname'];
					$passok = true;
					if ( !$email )
					{
						$femail = explode ('=', $user['email']);
						if ( $femail[0] )
						{
							$email = $femail[1];
						}
					}
				}

				break;
			}
		}

		if ( $passok == $news_user )
		{
			$ip = get_ip();

            $now = time();
            create_comment ($id, array (
                'ip'        => get_ip(),
                'validated' => (($config['com_validation'] && !$news_user) ? 0 : 1),
                'message'   => $comment,
                'author'    => $name,
                'email'     => $email,
                'timestamp' => $now,
                'comment_id'=> 0
            ));
            create_flood_entry (array (
                'ip'        => $ip,
                'timestamp' => $now
            ));
			//safe_write ('flood.php', 'ab', $ip . '|<|' . $time . '|<|' . "\n");

			echo <<< html
<script type="text/javascript">
//<![CDATA[
setTimeout ('window.location="{$next}"', 2000);
//]]>
</script>
html;

			if ( $config['com_validation'] && !$news_user )
			{
				echo $lang['com6a'];
			}
			else
			{
				echo $lang['com6'] . ' <a href="' . $next . '">' . $lang['com7'] . '</a>';
			}
		}
		else
		{
			echo $lang['com8'];
		}
        
        $expire_time = time();
        if ( $remember )
        {
            $expire_time += 365 * 86400;
            setcookie ('fn_comment_name', $name, $expire_time);
            setcookie ('fn_comment_email', $email, $expire_time);
            setcookie ('fn_comment_remember', true, $expire_time);
        }
        else if ( isset ($_COOKIE['fn_comment_name']) || 
                    isset ($_COOKIE['fn_comment_email']) ||
                    isset ($_COOKIE['fn_comment_remember']) )
        {
            $expire_time -= 3600;
            setcookie ('fn_comment_name', null, $expire_time);
            setcookie ('fn_comment_email', null, $expire_time);
            setcookie ('fn_comment_remember', null, $expire_time);
        }
	}
}

echo get_template('com_footer.php', true);

ob_end_flush();

?>
