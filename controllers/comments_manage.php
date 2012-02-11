<?php


	if ( !has_access (NEWS_EDITOR) )
    {
        trigger_error ($lang['ind19'], E_USER_WARNING);
    }
    
    $title = $lang['ind203'];
    $banned = get_banned_ips();
    $banned = implode ("\n", $banned);

    echo <<< html
<h2>{$lang['ind132']}</h2>
<p>{$lang['ind133']}</p>
<form action="?id=banlist_update" method="post">
<div style="text-align:center">
<textarea id="bannedlist" name="bannedlist" class="post" cols="50" rows="5">$banned</textarea><br />
<input type="submit" class="mainoption" value="{$lang['ind174']}" />
</div>
html;

    echo get_form_security();
    echo <<< html
</form>
html;

    $all_articles = get_posts_all();
    
    $articles = array();
    foreach ( $all_articles as $article )
    {
        $articles[$article['news_id']] = $article;
    }
    
    $unvalidated_list = get_unmoderated_comments_all();

    echo "<h2>{$lang['ind239']}</h2>";
    if ( sizeof ($unvalidated_list) == 0 )
    {
        echo $lang['ind242'];
    }
    else
    {
        echo <<< html
<form method="post" action="?id=validatecomments" id="validating">
html;

        $br_replace = $config['comallowbr'] ? '<br />' : '';
        foreach ( $unvalidated_list as $news_id => $comments )
        {
            if ( sizeof ($comments) > 0 )
            {
                echo '<h3>' . $articles[$news_id]['headline'] . '</h3>';
                foreach ( $comments as $value )
                {
                    $date = date ('Y-m-d H:i:s T', $value['timestamp']);
                    $email = ( empty ($value['email']) ) ? $lang['ind275'] : $value['email'];
                    
                    $message = str_replace ('&br;', $br_replace, $value['message']);
                    $message = format_message ($message, $config['htc'], $config['bbc'], $config['smilcom'], $config['wfcom']);

                    echo <<< html
<table class="adminpanel">
	<tr>
		<th colspan="2">
			{$lang['ind119']} <span style="font-weight: normal">{$value['author']} ({$value['ip']})</span>
			{$lang['ind6']} <span style="font-weight: normal">$email</span>
			{$lang['ind96']} <span style="font-weight: normal">$date</span>
		</th>
	</tr>
	<tr>
		<td style="width: 20px">
			<input type="checkbox" name="comid[{$value['comment_id']}]" id="comid_{$value['comment_id']}" onclick="javascript:check_if_selected ('validating')" value="{$value['comment_id']}" />
		</td>
		<td>
			$message
		</td>
	</tr>
</table><p></p>
html;
                }
            }
        }

        echo get_form_security();

        echo <<< html
<input type="submit" class="mainoption" disabled="disabled" value="{$lang['ind318']}" />&nbsp;
<input type="submit" class="mainoption" onclick="javascript:deleteComments(this.form);" disabled="disabled" value="{$lang['ind126']}" /></p>
</form>
<script type="text/javascript">
//<![CDATA[
function deleteComments ( form_object )
{
	form_object.action = '?id=deletecomments';
}
//]]>
</script>
html;
    }

    echo <<< html
<h2>{$lang['ind134']}</h2>
<p>{$lang['ind135']}</p>
<table class="adminpanel">
	<tr>
		<th style="width:8%">{$lang['ind81']}</th>
		<th>{$lang['ind35']}</th>
		<th>{$lang['ind5']}</th>
		<th>{$lang['ind96']}</th>
	</tr>

html;

    foreach ( $articles as $article )
    {
        $writer = get_author ($article['author']);
        if ( !$writer )
        {
            $writer = $article['author'];
        }
        else
        {
            $writer = $writer['nick'];
        }
        
        $timestamp = date ('Y-m-d H:i:s T', $article['timestamp']);
        
        echo
<<< html
	<tr>
		<td style="text-align:center">{$article['numcomments']}</td>
		<td><a href="?id=editcomments&amp;news_id={$article['news_id']}">{$article['headline']}</a></td>
		<td>{$writer}</td>
		<td>{$timestamp}</td>
	</tr>

html;
    }

    echo <<< html
</table>
html;

?>