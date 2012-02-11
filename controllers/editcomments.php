<?php

/*id Comments Edit*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$rand = ( isset ($GVARS['news_id']) ) ? (int)$GVARS['news_id'] : 0;

if ( !file_exists (FNEWS_ROOT_PATH . 'news/news.' . $rand . '.php') )
{
    trigger_error ($lang['com11'], E_USER_WARNING);
}

$title = $lang['ind203'];

echo <<< html
				{$lang['ind138']}<br />
				<form method="post" id="comments" action="?id=delete_comments">
				<table class="adminpanel">
					<tr>
						<th style="width:5%">{$lang['ind97']}</th>
						<th style="width:55%">{$lang['ind403']}</th>
						<th style="width:15%">{$lang['ind139']}</th>
						<th style="width:20%">{$lang['ind96']}</th>
						<th style="width:5%">{$lang['ind278']}</th>
					</tr>
html;
$comments = get_moderated_comments ($rand);

$found = false;
foreach ( $comments as $comment )
{
    $com_datum = date ('Y-m-d H:i:s T', $comment['timestamp']);
    $com_post = str_replace ('&br;', ($config['comallowbr'] ? '<br />' : ''), $comment['message']);
    $com_post = format_message ($com_post, $config['htc'], $config['bbc'], $config['smilcom'], $config['wfcom']);

    $ban_text = is_ip_banned ($comment['ip']) ? $lang['ind396'] : '';

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
			[<a href="?id=editcomment&amp;comment_id={$comment['comment_id']}&amp;news_id=$rand">{$lang['ind30b']}</a>]
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
			<td align="center" colspan="5">{$lang['ind283']}</td>
		</tr>
html;
}

$disabled = ( !$found ) ? ' disabled="disabled"' : '';

echo '</table>';
echo get_form_security();
echo "<p><a href=\"javascript:un_check_all ('comments', true)\">{$lang['ind44']}</a> | <a href=\"javascript:un_check_all ('comments', false)\">{$lang['ind44a']}</a></p>
<p><input type=\"hidden\" id=\"rand\" name=\"rand\" value=\"$rand\" />
<input class=\"mainoption\" type=\"submit\" disabled=\"disabled\" id=\"delete\" name=\"delete\" value=\"{$lang['ind126']}\" /></p></form>";

?>