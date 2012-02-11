<?php

if ( has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind294'], E_USER_WARNING);
}

$submit = ( isset ($PVARS['submit']) );
$title = $lang['ind360'];

if ( $submit )
{
    $username = ( isset ($PVARS['username']) ) ? fn_trim ($PVARS['username']) : '';
    $email = ( isset ($PVARS['email']) ) ? fn_trim ($PVARS['email']) : '';

    if ( !$username && !$email )
    {
        trigger_error ($lang['ind361'], E_USER_WARNING);
    }

    $user = get_user_for_lostpw ($username, $email);
    if ( $user === null )
    {
        trigger_error ($lang['ind362'], E_USER_WARNING);
    }

    $new_password = strtolower (create_security_id (12));

    $to = get_email ($user['email']);

    $message = sprintf ($lang['ind363'], $user['nickname'], $config['furl'], $user['username'], $new_password);
    $message = prepare_string_for_mail ($message);
    $headers = 'From: ' . $admin_nick . ' <' . $admin_email . '>' . "\r\n" .
            'X-Mailer: PHP/ ' . phpversion() . "\r\n";

    if ( !@mail ($to, $lang['ind364'], $message, $headers) )
    {
        trigger_error ($lang['ind365'], E_USER_WARNING);
    }
    
    reset_user_password ($user['username'], md5 ($new_password));

    echo $lang['ind366'];
}
else
{
    echo <<< html
<p>{$lang['ind367']}</p>
<form method="post" action="?id=lostpassword">
<table class="adminpanel">
	<tfoot>
		<tr>
			<th colspan="2"><input type="submit" name="submit" class="mainoption" value="{$lang['ind360']}" /></th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>{$lang['ind169a']}</td>
			<td><input type="text" name="username" class="post" size="20" /></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:left">{$lang['ind368']}</td>
		</tr>
		<tr>
			<td>{$lang['ind6']}</td>
			<td><input type="text" name="email" class="post" size="20" /></td>
		</tr>
	</tbody>
</table>
</form>
html;
}

?>