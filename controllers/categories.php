<?php


if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$action = ( isset ($GVARS['action']) ) ? $GVARS['action'] : '';
if ( !$action )
{
    $action = ( isset ($PVARS['action']) ) ? $PVARS['action'] : '';
}

if ( $action == 'add' )
{
    if ( !check_form_character() )
    {
        trigger_error ($lang['ind298'], E_USER_WARNING);
    }

    $name = ( isset ($PVARS['name']) ) ? single_line ($PVARS['name']) : '';
    $icon = ( isset ($PVARS['icon']) ) ? single_line ($PVARS['icon']) : '';
    $user_access = ( isset ($PVARS['author']) ) ? $PVARS['author'] : array();

    if ( !$name )
    {
        trigger_error ($lang['ind129'], E_USER_WARNING);
    }

    if ( sizeof ($user_access) <= 0 )
    {
        trigger_error ($lang['ind374'], E_USER_WARNING);
    }
    
    if ( category_name_exists ($name) )
    {
        trigger_error (sprintf ($lang['ind315'], $name), E_USER_WARNING);
    }
    
    create_category (array (
        'category_id' => 0,
        'name' => $name,
        'icon' => $icon,
        'users' => $user_access
    ));

    $title = $lang['ind316'];
    echo make_redirect (sprintf ($lang['ind317'], $name), '?id=categories', $lang['ind336']);
}
else if ( $action == 'edit' )
{
    $submit = ( isset ($VARS['submit']) ) ? true : false;

    if ( $submit )
    {
        if ( !check_form_character() )
        {
            trigger_error ($lang['ind298'], E_USER_WARNING);
        }

        $category_id = ( isset ($PVARS['category_id']) ) ? intval ($PVARS['category_id']) : -1;
        $delete = ( isset ($PVARS['delete']) );

        if ( !category_exists ($category_id) )
        {
            trigger_error (sprintf ($lang['ind198'], $category_id), E_USER_WARNING);
        }
        
        if ( $delete )
        {
            $posts_action = ( isset ($PVARS['posts_action']) ) ? intval ($PVARS['posts_action']) : 0;
            $new_category = ( isset ($PVARS['new_category']) ) ? intval ($PVARS['new_category']) : -1;
        
            if ( $category_id == 1 )
            {
                trigger_error (sprintf ($lang['ind10'], $name), E_USER_WARNING);
            }
            
            switch ( $posts_action )
            {
                case 1:
                    move_posts ($category_id, $new_category);
                    break;
                    
                case 2:
                    delete_posts_in_category ($category_id);
                    break;
            }
            delete_category ($category_id);
        }
        else
        {
            $name = ( isset ($PVARS['name']) ) ? single_line ($PVARS['name']) : '';
            $icon = ( isset ($PVARS['icon']) ) ? single_line ($PVARS['icon']) : '';
            $user_access = ( isset ($PVARS['author']) ) ?  array_map ('single_line', $PVARS['author']) : array();
        
            if ( $name == '' )
            {
                trigger_error ($lang['error23'], E_USER_WARNING);
            }
        
            update_category ($category_id, array (
                'category_id' => $category_id,
                'name' => $name,
                'icon' => $icon,
                'users' => $user_access
            ));
        }

        $title = $lang['ind1'];
        echo make_redirect ($lang['ind2'], '?id=categories', $lang['ind336']);
    }
    else
    {
        $catid = ( isset ($GVARS['category']) ) ? intval ($GVARS['category']) : 0;

        if ( !category_exists ($catid) )
        {
            trigger_error (sprintf ($lang['ind198'], $catid), E_USER_WARNING);
        }

        $title = $lang['ind314'];

        echo '<form method="post" action="?id=categories">';
        echo get_form_security();

        $category = get_category ($catid);
        $user_selection = build_author_selection ($category['users']);

        $category_select = build_category_dropdown (null, $catid, true);
        $category_select = str_replace ('id="category" name="category"', 'id="new_category" name="new_category"', $category_select);

        echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">{$category['name']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="hidden" name="category_id" value="$catid" />
				<input type="hidden" name="action" value="edit" />
				<input type="submit" name="submit" class="mainoption" value="{$lang['ind314']}" />
				<input type="reset" value="{$lang['ind16']}" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>{$lang['ind139']}</td>
			<td><input type="text" name="name" id="name" value="{$category['name']}" size="20" /></td>
		</tr>
		<tr>
			<td>{$lang['ind7']}</td>
			<td>
				<input type="text" name="icon" id="icon" value="{$category['icon']}" size="20" />
			</td>
		</tr>
		<tr>
			<td valign="top">{$lang['ind208']} (<abbr title="{$lang['ind313']}">?</abbr>)</td>
			<td valign="top">
				$user_selection
			</td>
		</tr>
html;

            if ( $catid > 1 )
            {
                echo <<< html
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="checkbox" name="delete" id="delete" value="1" />
				<label for="delete">{$lang['ind97']}</label>
			</td>
		</tr>
		<tr id="delete-options">
			<td valign="top">{$lang['ind136']} (<abbr title="{$lang['ind201']}">?</abbr>)</td>
			<td valign="top">
				<input type="radio" name="posts_action" checked="checked" value="1" /> {$lang['ind199']} $category_select<br />
				<input type="radio" name="posts_action" value="2" /> {$lang['ind206']}
			</td>
		</tr>

html;
        }

        echo <<< html
	</tbody>
</table>
<script type="text/javascript">
//<![CDATA[
(function()
{
    var deleteCheckbox = document.getElementById('delete');
    deleteCheckbox.onclick = function()
    {
        var ticked = deleteCheckbox.checked;
        if ( !ticked )
        {
            document.getElementById('delete-options').style.display = 'none';
        }
        else
        {
            document.getElementById('delete-options').style.display = 'table-row';
        }
    }
    
    deleteCheckbox.onclick();
})();
//]]>
</script>
html;

        echo <<< html
</form>
html;
    }
}
else
{
    $title = $lang['ind311'];

    $users = get_users_all();
    $usernicks = array();
    foreach ( $users as $user )
    {
        $usernicks[$user['username']] = $user['nickname'];
    }
    $user_selection = build_author_selection();

    echo '<form method="post" action="?id=categories">' . "\n";
    echo get_form_security();
    echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">{$lang['ind312']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
				<input type="hidden" name="action" value="add" />
				<input type="submit" class="mainoption" value="{$lang['ind312']}" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>{$lang['ind139']}</td>
			<td><input type="text" name="name" id="name" size="20" /></td>
		</tr>
		<tr>
			<td>{$lang['ind7']}</td>
			<td>
				<input type="text" name="icon" id="icon" size="20" />
			</td>
		</tr>
		<tr>
			<td valign="top">{$lang['ind208']} (<abbr title="{$lang['ind313']}">?</abbr>)</td>
			<td valign="top">
				$user_selection
            </td>
		</tr>
	</tbody>
</table>
</form>
<p></p>
html;
    $categories = get_categories_all();
    $category_ids = array();
    foreach ( $categories as $category )
    {
        $category_ids[] = $category['category_id'];
    }
    
    $count = get_post_count_in_categories ($category_ids);

    $category_list = '<tbody>';
    foreach ( $categories as $category )
    {
        $cicon = ( !empty ($category['icon']) ) ? '<img src="' . $category['icon'] . '" alt="" />' : '';

        $user_dropdown = '<select>';
        $userlist = $category['users'];
        if ( $category['category_id'] == 1 )
        {
            foreach ( $users as $user )
            {
                $user_dropdown .= '<option>' . $user['nickname'] . '</option>';
            }
        }
        else if ( $userlist[0] )
        {
            foreach ( $userlist as $user )
            {
                $user_dropdown .= '<option>' . $usernicks[$user] . '</option>';
            }
        }
        $user_dropdown .= '</select>';

        $category_list .= '<tr>';
        $category_list .= '<td style="text-align:center">' . $category['category_id'] . '</td>';
        $category_list .= '<td>' . $category['name'] . '</td>';
        $category_list .= '<td style="text-align:center"><a href="?id=editposts&amp;category=' . $category['category_id'] . '">' . $count[$category['category_id']] . '</a></td>';
        $category_list .= '<td style="text-align:center">' . $cicon . '</td>';
        $category_list .= '<td>' . $user_dropdown . '</td>';
        $category_list .= '<td style="text-align:center"><a href="?id=categories&amp;action=edit&amp;category=' . $category['category_id'] . '">' . $lang['ind30b'] . '</a></td>';
        $category_list .= '</tr>';
    }
    $category_list .= '</tbody>';

    echo <<< html
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="6">{$lang['ind314']}</th>
		</tr>
		<tr>
			<th>{$lang['ind346']}</th>
			<th>{$lang['ind139']}</th>
			<th>{$lang['ind82']}</th>
			<th>{$lang['ind140']}</th>
			<th>{$lang['ind208']}</th>
			<th>{$lang['ind30b']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>
	</tfoot>
	$category_list
</table>
html;
}


?>