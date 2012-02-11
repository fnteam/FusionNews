<?php

/*id Smillies*/
	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }

    $title = $lang['ind211'];
    echo
<<< html
<h2>{$lang['ind274']}</h2>
<form method="post" action="?id=editsmillie">
<table class="adminpanel">
	<tr>
		<th style="width:10%">{$lang['ind97']}</th>
		<th style="width:70%">{$lang['ind276']}</th>
		<th style="width:20%">{$lang['ind277']}</th>
	</tr>

html;

    $smileys = get_smileys_all();
    foreach ( $smileys as $smiley )
    {
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
			<img src="{$config['furl']}/smillies/{$smiley['image']}" alt="{$smiley['image']}" title="{$smiley['image']}" />
		</td>
	</tr>
html;
    }
    echo <<< html
	<tr>
		<th colspan="3">
			<input type="submit" class="mainoption" value="{$lang['ind174']}" />
			<input type="reset" value="{$lang['ind16']}" />
		</th>
	</tr>
</table>
html;
    echo get_form_security() . '</form>';

    $smiley_images = get_smiley_images_all();

    $first_smiley = reset ($smiley_images);
    $smiley_dropdown = make_dropdown ('smiley_image', 'smiley_image', $first_smiley, $smiley_images, NULL, 'onchange="show_emo(\'./smillies\')"');
    echo
<<< html
<form id="theAdminForm" method="post" action="?id=addsmillie">
<h2>{$lang['ind243']}</h2>
<table class="adminpanel">
	<tr>
		<th>{$lang['ind276']}</th>
		<th>{$lang['ind245']}</th>
		<th>{$lang['ind244']}</th>
	</tr>
	<tr>
		<td style="text-align:center"><input type="text" id="code" name="code" class="post" size="30" /></td>
		<td style="text-align:center">
			<img src="{$config['furl']}/smillies/{$first_smiley}" id="emopreview" alt="{$first_smiley}" title="{$first_smiley}" />
			$smiley_dropdown
		</td>
		<td style="text-align:center"><input type="submit" class="mainoption" value="{$lang['ind244']}" /></td>
	</tr>
</table>
html;

    echo get_form_security();

    echo <<< html
</form>
<form method="post" action="?id=uploadsmillie" id="uploadform" enctype="multipart/form-data">
<h2>{$lang['ind246']}</h2>
<table class="adminpanel">
	<thead><tr><th>{$lang['ind247']}</th></tr></thead>
	<tfoot><tr><th><input type="submit" class="mainoption" value="{$lang['ind42']}" /></th></tr></tfoot>
	<tbody><tr><td style="text-align:center"><input type="file" id="FILE_UPLOAD" name="F" class="post" size="50" /></td></tr></tbody>
</table>
html;
	echo get_form_security() . '</form>';


?>