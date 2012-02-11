<?php


	if ( !has_access (NEWS_ADMIN) )
	{
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }
    
    $title	 = $lang['ind23'];
    echo <<< html
<form method="get" action="?">
	<p>{$lang['ind175a']}<br />
    <input type="hidden" name="id" value="admin_template_edit" />
	<select id="menu" name="show" onchange="this.form.submit()">
		<option selected="selected">------------------------</option>
		<option value="1">{$lang['ind176']}</option>
		<option value="2">{$lang['ind177']}</option>
		<option value="3">{$lang['ind178']}</option>
		<option value="4">{$lang['ind179']}</option>
		<option value="5">{$lang['ind180']}</option>
		<option value="6">{$lang['ind181']}</option>
		<option value="7">{$lang['ind181a']}</option>
	</select>
	</p>
</form>
html;


?>