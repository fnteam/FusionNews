<?php

 /*id Help*/
	if ( !has_access (NEWS_REPORTER) )
    {
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }
    
    $message = '';
    $title = $lang['ind152'];

    $version = get_latest_version_number();
    if ( $version !== null )
    {
        $message = version_compare ($version, FNEWS_VERSION) > 0 ? $lang['ind303'] : $lang['ind304'];
    }
    else
    {
        $message = $lang['ind401'];
    }

    $current_version = FNEWS_VERSION;
    echo <<< html
<table class="adminpanel">
	<tr><th>{$lang['ind157']}</th></tr>
	<tr><td>{$lang['ind158']}</td></tr>

	<tr><th style="text-align:left">{$lang['ind159']} (<a href="javascript:toggleView('help_1');">{$lang['ind160']}</a>)</th></tr>
	<tr><td><div style="display:none" id="help_1">{$lang['ind345']}</div></td></tr>

	<tr><th style="text-align:left">{$lang['ind347']} (<a href="javascript:toggleView('help_2');">{$lang['ind160']}</a>)</th></tr>
	<tr><td><div style="display:none" id="help_2">{$lang['ind348']}</div></td></tr>

	<tr><th style="text-align:left">{$lang['ind349']} (<a href="javascript:toggleView('help_3');">{$lang['ind160']}</a>)</th></tr>
	<tr><td><div style="display:none" id="help_3">{$lang['ind350']}</div></td></tr>

	<tr><th style="text-align:left">{$lang['ind351']} (<a href="javascript:toggleView('help_4');">{$lang['ind160']}</a>)</th></tr>
	<tr><td><div style="display:none" id="help_4">{$lang['ind352']}</div></td></tr>

	<tr><th style="text-align:left">{$lang['ind353']} (<a href="javascript:toggleView('help_5');">{$lang['ind160']}</a>)</th></tr>
	<tr><td><div style="display:none" id="help_5">{$lang['ind354']}</div></td></tr>
</table>
<p></p>
<table class="adminpanel">
	<tr><th>{$lang['ind323']}</th></tr>
	<tr><td>{$lang['ind236']}</td></tr>
</table>
<p></p>
<table class="adminpanel">
	<tr>
		<th colspan="2">{$lang['ind154']}</th>
	</tr>
	<tr>
		<td style="width:50%">{$lang['ind155']}</td>
		<td style="width:50%">$current_version</td>
	</tr>
	<tr>
		<td>{$lang['ind153']}</td>
		<td>$version</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			$message
		</td>
	</tr>
</table>
html;


?>