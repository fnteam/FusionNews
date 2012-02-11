<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$user = ( isset ($GVARS["user"]) ) ? $GVARS['user'] : '';

$userinfo = get_author ($user);
if ( $userinfo === false )
{
    trigger_error (sprintf ($lang['ind20'], $user), E_USER_WARNING);
}

$nor1 = ( $userinfo['level'] == 1 ) ? ' selected="selected"' : '';
$edi1 = ( $userinfo['level'] == 2 ) ? ' selected="selected"' : '';
$adm1 = ( $userinfo['level'] == 3 ) ? ' selected="selected"' : '';
$showemail = ( $userinfo['showemail'] ) ? '' : ' checked="checked"';

$icon_image = $userinfo['icon'] ? '<br /><img src="' . $userinfo['icon'] . '" alt="" />': '';

$title = $lang['ind113'];
$datum = date ('Y-m-d H:i:s T');

echo <<< html
<form action="?id=user_update" method="post">
<table class="adminpanel">
	<tr>
		<th colspan="2">{$userinfo['user']}</th>
	</tr>
	<tr>
		<td><label for="nick1">{$lang['ind119']}</label></td>
		<td><input size="20" type="text" class="post" id="nick1" name="nick1" value="{$userinfo['nick']}" /></td>
	</tr>
	<tr>
		<td><label for="mail1">{$lang['ind6']}</label></td>
		<td><input size="20" type="text" class="post" id="mail1" name="mail1" value="{$userinfo['email']}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input size="20" type="checkbox" class="post" id="showemail" name="showemail"$showemail /> <label for="showemail">{$lang['ind183']}</label></td>
	</tr>
    <tr>
        <td>{$lang['ind112']}</td>
        <td>$datum</td>
    </tr>
	<tr>
		<td><label for="timeoffset">{$lang['ind111']}</label></td>
		<td><input size="2" type="text" class="post" id="timeoffset" name="timeoffset" value="{$userinfo['timeoffset']}" /></td>
	</tr>
	<tr>
		<td><label for="new_password">{$lang['ind4a']} {$lang['ind4']}</label></td>
		<td><input size="20" type="password" class="post" id="new_password" name="new_password" value="" /></td>
	</tr>
	<tr>
		<td><label for="confirm_pass">{$lang['ind369']}</label></td>
		<td><input size="20" type="password" class="post" id="confirm_pass" name="confirm_pass" value="" /></td>
	</tr>
	<tr>
		<td valign="top"><label for="icon1">{$lang['ind7']}</label></td>
		<td>
			<input size="20" type="text" class="post" id="icon1" name="icon1" value="{$userinfo['icon']}" />
			$icon_image
		</td>
	</tr>
	<tr>
		<td><label for="fle">{$lang['ind8']}</label></td>
		<td>
			<select id="fle" name="fle">
				<option value="1" $nor1>{$lang['ind193']}</option>
				<option value="2" $edi1>{$lang['ind194']}</option>
				<option value="3" $adm1>{$lang['ind195']}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" id="name" name="name" value="{$userinfo['user']}" />
			<input type="checkbox" class="post" id="del" name="del" /> <label for="del">{$lang['ind97']}</label>
		</td>
	</tr>
	<tr>
		<th colspan="2">
			<input type="submit" class="mainoption" value="{$lang['ind174']}" />
			<input type="reset" value="{$lang['ind16']}" />
		</th>
	</tr>
</table>
html;
echo get_form_security() . '</form>';


?>