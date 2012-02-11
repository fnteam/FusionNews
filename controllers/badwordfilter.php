<?php


	if ( !has_access (NEWS_EDITOR) )
	{
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }
    
    $title = $lang['ind215'];

    $badwords = get_badwords();

    echo <<< html
<form action="?id=savebadwordfile" method="post">
<table class="adminpanel">
	<tr>
		<th>{$lang['ind97']}</th>
		<th>{$lang['ind27']}</th>
		<th>{$lang['ind28']}</th>
		<th>{$lang['ind29']}</th>
		<th>{$lang['ind130']}</th>
	</tr>
html;
    $num_words = sizeof ($badwords);
    if ( $num_words > 0 )
    {
        foreach ( $badwords as $key => $badword )
        {
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
		<td><input type="radio" name="type[$key]" value="0"$radio_strict /> {$lang['ind190']}<br /><input type="radio" name="type[$key]" value="1"$radio_loose /> {$lang['ind232']}<br /><input type="radio" name="type[$key]" value="2"$radio_regex /> {$lang['ind192b']}</td>
	</tr>
html;
        }
    }
    else
    {
        echo '<tr><td style="text-align:center" colspan="5">' . $lang['ind273'] . '</td></tr>';
    }

    echo <<< html
	<tr><th colspan="5"><input type="hidden" name="num_words" value="$num_words" /><input type="submit" class="mainoption" value="{$lang['ind174']}" /></th></tr>
</table>
html;

    echo get_form_security();

    echo <<< html
</form>
<h2>{$lang['ind237']}</h2>
<form action="?id=addbadwords" method="post">
<table class="adminpanel">
	<tr>
		<th>{$lang['ind27']}</th>
		<th>{$lang['ind28']}</th>
		<th>{$lang['ind29']}</th>
		<th>{$lang['ind130']}</th>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[0]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[0]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[0]" value="1" /></td>
		<td><input type="radio" name="type[0]" value="0" checked="checked" /> {$lang['ind190']}<br /><input type="radio" name="type[0]" value="1" /> {$lang['ind232']}<br /><input type="radio" name="type[0]" value="2" /> {$lang['ind192b']}</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[1]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[1]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[1]" value="1" /></td>
		<td><input type="radio" name="type[1]" value="0" checked="checked" /> {$lang['ind190']}<br /><input type="radio" name="type[1]" value="1" /> {$lang['ind232']}<br /><input type="radio" name="type[1]" value="2" /> {$lang['ind192b']}</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[2]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[2]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[2]" value="1" /></td>
		<td><input type="radio" name="type[2]" value="0" checked="checked" /> {$lang['ind190']}<br /><input type="radio" name="type[2]" value="1" /> {$lang['ind232']}<br /><input type="radio" name="type[2]" value="2" /> {$lang['ind192b']}</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[3]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[3]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[3]" value="1" /></td>
		<td><input type="radio" name="type[3]" value="0" checked="checked" /> {$lang['ind190']}<br /><input type="radio" name="type[3]" value="1" /> {$lang['ind232']}<br /><input type="radio" name="type[3]" value="2" /> {$lang['ind192b']}</td>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" name="find[4]" size="20" /></td>
		<td style="text-align:center"><input type="text" name="replace[4]" size="20" /></td>
		<td style="text-align:center"><input type="checkbox" name="case_sens[4]" value="1" /></td>
		<td><input type="radio" name="type[4]" value="0" checked="checked" /> {$lang['ind190']}<br /><input type="radio" name="type[4]" value="1" /> {$lang['ind232']}<br /><input type="radio" name="type[4]" value="2" /> {$lang['ind192b']}</td>
	</tr>
	<tr><th colspan="4"><input type="submit" class="mainoption" value="{$lang['ind237']}" /></th></tr>
</table>
html;

	echo get_form_security() . '</form><p>' . $lang['ind131'] . '</p>';


?>