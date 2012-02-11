<?php

/*id Comment Edit*/
if ( !has_access (NEWS_EDITOR) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$news_id	= ( isset ($GVARS['news_id']) ) ? (int)$GVARS['news_id'] : 0;
$comment_id	= ( isset ($GVARS['comment_id']) ) ? $GVARS['comment_id'] : 0;

$comment = get_comment ($comment_id, $news_id);
if ( $comment === null )
{
    trigger_error ($lang['com11'], E_USER_WARNING);
}
$email = $comment['email'] == '' ? $lang['ind141'] : $comment['email'];

$message = str_replace ('&br;', "\n", $comment['message']);

$no = '<span style="color:red; font-weight: bold">' . $lang['ind144'] .'</span>';
$yes = '<span style="color:green; font-weight: bold">' . $lang['ind143'] .'</span>';

$htmlcheck = ( !$config['htc'] ) ? $no : $yes;
$bbcheck = ( !$config['bbc'] ) ? $no : $yes;
$smilcheck = ( !$config['smilcom'] ) ? $no : $yes;

$ban_text = is_ip_banned ($comment['ip']) ? $lang['ind396'] : '';

$title = $lang['ind134'];
echo <<< html
<form action="?id=updatecomment&amp;comment_id={$comment['comment_id']}&amp;news_id={$news_id}" method="post" id="newsposting">
<table class="adminpanel">
	<tr>
		<td>{$lang['ind279']}</td>
		<td>{$comment['author']}</td>
		<td rowspan="3">
			{$lang['ind121']}<br />
			- HTML {$lang['ind122']} $htmlcheck<br />
			- BBCode {$lang['ind122']} $bbcheck<br />
			- Smilies {$lang['ind122']} $smilcheck
		</td>
	</tr>
	<tr>
		<td>{$lang['ind6']}</td>
		<td>$email</td>
	</tr>
	<tr>
		<td>IP</td>
		<td>{$comment['ip']} $ban_text</td>
	</tr>
html;

$extras = show_extras ('newsposting', 'comment', $config['smilcom'], $config['bbc']);
echo <<<html
	<tr>
		<td colspan="3">
			<div style="text-align:center">$extras
			<textarea class="post" name="comment" id="comment" rows="15" cols="75">$message</textarea></div>
			<p><label for="del">{$lang['ind97']}</label> <input type="checkbox" class="post" value="1" id="del" name="del" /></p>
			<p><input class="mainoption" type="submit" value="{$lang['ind174']}" /></p>
		</td>
	</tr>
</table>
html;
$timestamp = time();
echo get_form_security() . '</form>';


?>