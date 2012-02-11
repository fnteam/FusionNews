<?php


if ( !has_access (NEWS_REPORTER) )
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = $lang['ind95'];

$cid = ( isset ($GVARS['category']) ) ? (int)$GVARS['category'] : 0;
$auth = ( isset ($GVARS['auth']) && !empty ($GVARS['auth']) ) ? urldecode ($GVARS['auth']) : '';
$before = ( isset ($GVARS['before']) ) ? (int)$GVARS['before'] : 0;
$after = ( isset ($GVARS['after']) ) ? (int)$GVARS['after'] : 0;

$pre_d = ( isset ($GVARS['pre_d']) ) ? (int)$GVARS['pre_d'] : 18;
$pre_m = ( isset ($GVARS['pre_m']) ) ? (int)$GVARS['pre_m'] : 1;
$pre_y = ( isset ($GVARS['pre_y']) ) ? (int)$GVARS['pre_y'] : 2038;

$post_d = ( isset ($GVARS['post_d']) ) ? (int)$GVARS['post_d'] : 1;
$post_m = ( isset ($GVARS['post_m']) ) ? (int)$GVARS['post_m'] : 1;
$post_y = ( isset ($GVARS['post_y']) ) ? (int)$GVARS['post_y'] : 1970;

if ( $pre_d > 18 && $pre_m > 1 && $pre_y >= 2038 )
{
    $pre_d = 18;
    $pre_m = 1;
    $pre_y = 2038;
}

if ( $post_d > 18 && $post_m > 1 && $post_y >= 2038 )
{
    $post_d = 18;
    $post_m = 1;
    $post_y = 2038;
}

if ( $pre_d < 1 && $pre_m < 1 && $pre_y < 1970 )
{
    $pre_d = 1;
    $pre_m = 1;
    $pre_y = 1970;
}

if ( $post_d < 1 && $post_m < 1 && $post_y < 1970 )
{
    $post_d = 1;
    $post_m = 1;
    $post_y = 1970;
}

$pre_date = mktime (0, 0, 0, $pre_m, $pre_d, $pre_y);
$post_date = mktime (0, 0, 0, $post_m, $post_d, $post_y);

$user_dropdown = '<select name="auth"><option value="">' . $lang['ind293'] . '</option>';
$users = get_users_all();
foreach ( $users as $user )
{
    if ( !has_access (NEWS_EDITOR) && $user['username'] != $userdata['user'] )
    {
        continue;
    }

    $user_dropdown .= '<option value="' . $user['username'] . '"' . (( $user['username'] === $auth ) ? ' selected="selected"' : '') . '>' . $user['nickname'] . '</option>';
}
$user_dropdown .= '</select>';

$category_dropdown = build_category_dropdown ($userdata['user'], $cid);
$category_dropdown = str_replace ('<option value="1"', '<option value="0">' . $lang['ind293'] . '</option><option value="1"', $category_dropdown);

$dd1_dropdown = '<select name="pre_d" title="' . $lang['ind51'] . '">';
$dd2_dropdown = '<select name="post_d" title="' . $lang['ind51'] . '">';
for ( $i = 1; $i <= 31; $i++ )
{
    $dd1_dropdown .= '<option' . ( $pre_d == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    $dd2_dropdown .= '<option' . ( $post_d == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
}
$dd1_dropdown .= '</select>';
$dd2_dropdown .= '</select>';

$mm1_dropdown = '<select name="pre_m" title="' . $lang['ind52'] . '">';
$mm2_dropdown = '<select name="post_m" title="' . $lang['ind52'] . '">';
for ( $i = 1; $i <= 12; $i++ )
{
    $mm1_dropdown .= '<option' . ( $pre_m == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    $mm2_dropdown .= '<option' . ( $post_m == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
}
$mm1_dropdown .= '</select>';
$mm2_dropdown .= '</select>';

$yy1_dropdown = '<select name="pre_y" title="' . $lang['ind85'] . '">';
$yy2_dropdown = '<select name="post_y" title="' . $lang['ind85'] . '">';

for ( $i = 2038; $i >= 1970; $i-- )
{
    $yy1_dropdown .= '<option' . ( $pre_y == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
    $yy2_dropdown .= '<option' . ( $post_y == $i  ? ' selected="selected"' : '') . '>' . $i . '</option>';
}
$yy1_dropdown .= '</select>';
$yy2_dropdown .= '</select>';

echo <<< html
<form method="get" action="">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="4">{$lang['ind175']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4">
				<input type="hidden" name="id" value="editposts" />
				<input type="submit" class="mainoption" value="{$lang['ind125']}" />
				<input type="button" class="mainoption" onclick="window.location='?id=editposts'" value="{$lang['ind399']}" />
				<input type="reset" value="{$lang['ind16']}" />
			</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>{$lang['ind241']}</td>
			<td>$user_dropdown</td>
			<td>{$lang['ind308']}</td>
			<td>$category_dropdown</td>
		</tr>
		<tr>
			<td>{$lang['ind209']}</td>
			<td>$dd2_dropdown$mm2_dropdown$yy2_dropdown</td>
			<td>{$lang['ind214']}</td>
			<td>$dd1_dropdown$mm1_dropdown$yy1_dropdown</td>
		</tr>
	</tbody>
</table>
</form>
html;

$find_author = '';
if ( $auth )
{
    if ( has_access (NEWS_EDITOR) )
    {
        $find_author = $auth;
    }
    else
    {
        $find_author = $userdata['user'];
    }
}

$news_list = get_posts_filtered ($post_date, $pre_date, $find_author, $cid);
if ( !has_access (NEWS_ADMIN) )
{
    function user_can_post ( $post )
    {
        global $userdata;

        return can_post_in_categories ($userdata['user'], $post['categories']);
    }

    $news_list = array_values (array_filter ($news_list, 'user_can_post'));
}

$page = ( isset ($GVARS['page']) ) ? intval ($GVARS['page']) : 1;
$page = ( $page <= 0 && $page != -1 ) ? -1 : $page;

$num_news = sizeof ($news_list);
$news_per_page = 20;
$num_pages = ceil ($num_news / $news_per_page);

$pos = $news_per_page * ($page - 1);

$search_criteria = '&amp;auth=' . $auth . '&amp;category=' . $cid . '&amp;post_d=' . $post_d . '&amp;post_m=' . $post_m . '&amp;post_y=' . $post_y . '&amp;pre_d=' . $pre_d . '&amp;pre_m=' . $pre_m . '&amp;pre_y=' . $pre_y;
$page_list = '<p style="text-align:right">' . $lang['ind292'] . ': ';
for ( $i = 0; $i < $num_pages; $i++ )
{
    $upper_limit = ($i + 1) * $news_per_page;
    $upper_limit = ( $upper_limit > $num_news || $page == -1 ) ? $num_news : $upper_limit;

    if ( $page == ($i + 1) )
    {
        $page_list .= ($i * $news_per_page) + 1 . ' - ' . $upper_limit;
    }
    else
    {
        $page_list .= '<a href="?id=editposts&amp;page=' . ($i + 1) . $search_criteria . '">' . (($i * $news_per_page) + 1) . ' - ' . $upper_limit . '</a>';
    }

    $page_list .= ', ';
}

if ( $page == -1 )
{
    $page_list .= '<b>' . $lang['ind293'] . '</b></p>';
}
else
{
    $page_list .= '<a href="?id=editposts&amp;page=-1' . $search_criteria . '"><b>' . $lang['ind293'] . '</b></a></p>';
}

echo
<<< html
$page_list
<form method="post" id="deleteform" action="?id=delposts">
<table class="adminpanel">
	<tr>
		<th style="width:10%; text-align:center">{$lang['ind97']}</th>
		<th style="width:35%">{$lang['ind35']}</th>
		<th style="width:15%">{$lang['ind241']}</th>
		<th style="width:10%; text-align:center">{$lang['ind81']}</th>
		<th style="width:30%">{$lang['ind96']}</th>
	</tr>
html;

$num_comments = 0;
$pos = ( $page == -1 ) ? 0 : $pos;
$limit = ( $page == -1 ) ? $num_news : (( ($pos + $news_per_page) > $num_news ) ? $num_news : $pos + $news_per_page);
for ( $i = $pos; $i < $limit; $i++ )
{
    $news_file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_list[$i]['news_id'] . '.php');
    $article = get_line_data ('news', $news_file[1]);

    $date = date ('Y-m-d H:i:s T', $news_list[$i]['timestamp']);

    $subject = html_entity_decode ($news_list[$i]['headline']);
    $subject = ( utf8_strlen ($subject) > 35 ) ? utf8_substr ($subject, 0, 35) . '...' : $subject;
    $comment_link = ( has_access (NEWS_EDITOR) ) ? '<a href="?id=editcomments&amp;news_id=' . $news_list[$i]['news_id'] . '">' . $article['numcomments'] . '</a>' : $article['numcomments'];
    $author = get_author ($news_list[$i]['author']);
    $author = !$author ? $news_list[$i]['author'] : $author['nick'];
    echo
<<< html
	<tr>
		<td style="text-align: center">
			<input class="post" type="checkbox" id="delpost_{$news_list[$i]['news_id']}" name="delpost[{$news_list[$i]['news_id']}]" value="{$news_list[$i]['news_id']}" onclick="javascript:check_if_selected ('deleteform')" />
		</td>
		<td>
			<a href="?id=editposts2&amp;num={$news_list[$i]['news_id']}">$subject</a>
		</td>
		<td>
			{$author}
		</td>
		<td align="center">
			$comment_link
		</td>
		<td>
			$date
		</td>
	</tr>
html;
}

if ( $i == $pos )
{
    echo
<<< html
	<tr>
		<td align="center" colspan="5">{$lang['ind282']}</td>
	</tr>
html;
}

$security_fields = get_form_security();

echo <<< html
</table>
$page_list
<p>
    <a href="javascript:un_check_all ('deleteform', true)">{$lang['ind44']}</a> | <a href="javascript:un_check_all ('deleteform', false)">{$lang['ind44a']}</a>
    $security_fields
</p>
<p><input class="mainoption" type="submit" disabled="disabled" id="delete" name="delete" value="{$lang['ind126']}" /> <label for="delete">{$lang['ind127']}</label></p>
html;

echo '</form>';


?>