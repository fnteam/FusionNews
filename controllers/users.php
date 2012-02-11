<?php

if ( !has_access (NEWS_ADMIN) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind167'];

function access_level_name ( $lang, $level )
{
    switch ( $level )
    {
        case NEWS_ADMIN: return $lang['ind195'];
        case NEWS_EDITOR: return $lang['ind194'];
        case NEWS_REPORTER: return $lang['ind193'];
        default: return null;
    }
}

?>
<p><a href="?id=user_add"><?php echo $lang['ind31']; ?></a></p>
<form action="?id=user_edit" method="post">
<table class="adminpanel">
	<thead>
		<tr>
            <th class="checkbox-column"><input type="checkbox" /></th>
            <th><?php echo $lang['ind119']; ?></th>
            <th><?php echo $lang['ind8']; ?></th>
            <th><?php echo $lang['ind82']; ?></th>
		</tr>
	</thead>
    <tfoot>
        <tr>
            <td colspan="4"><input type="submit" value="Delete Selected" /></td>
        </tr>
    </tfoot>
	<tbody>
<?php
$users = get_users_all();
$post_counts = get_user_post_counts();
foreach ( $users as $user ):
?>
        <tr>
            <td><input type="checkbox" name="delete[<?php echo $user['username']; ?>]" ></td>
            <td><a href="?id=user_edit&amp;user=<?php echo $user['username']; ?>"><?php echo $user['nickname']; ?></a></td>
            <td><?php echo access_level_name ($lang, $user['level']); ?></td>
            <td><a href="?id=editposts&amp;auth=<?php echo $user['username']; ?>"><?php echo $post_counts[$user['username']]; ?></a></td>
        </tr>
<?php endforeach; ?>
	</tbody>
</table>
</form>
