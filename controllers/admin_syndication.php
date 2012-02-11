<?php


	if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($lang['ind19'], E_USER_WARNING);
	}

	$title = $lang['ind324'];
	$submit = ( isset ($PVARS['submit']) );

	if ( $submit )
	{
		$include = ( isset ($PVARS['include']) ) ? (int)$PVARS['include'] : -1;
		$category = ( isset ($PVARS['category']) ) ? $PVARS['category'] : array();
        $newsurl = ( isset ($PVARS['news_url']) ) ? $PVARS['news_url'] : '';
        
        // Remove any duplicates
        $category = array_unique ($category);
        
        $text = '';
        switch ( $include )
		{
			case 0: // news
                $author = ( isset ($PVARS['author']) ) ? $PVARS['author'] : array();
                $pagination = (int)isset ($PVARS['pagination']);
                $num_news = ( isset ($PVARS['news_per_page']) ) ? (int)$PVARS['news_per_page'] : $config['numofposts'];
                $date_order = ( isset ($PVARS['order']) ) ? $PVARS['order'] : '';
                
                if ( $date_order != 'asc' && $date_order != 'desc' )
                {
                    $date_order = $config['flip_news'] ? 'asc' : 'desc';
                }
                
                $template = ( isset ($PVARS['template']) ) ? $PVARS['template'] : '';
            
                if ( $num_news <= 0 )
                {
                    trigger_error ($lang['ind182'], E_USER_WARNING);
                }
            
				$text = "&lt;?php\n\n";
                
                $file = file (FNEWS_ROOT_PATH . 'categories.php');
                $total_categories = sizeof ($file) - 1; // -1 to remove first php line
                
                $num_categories = sizeof ($category);
                if ( $num_categories != $total_categories && $num_categories > 0 )
                {
                    $text .= '$fn_category = array (' . implode (',', $category) . ");\n";
                }
                
                $file = file (FNEWS_ROOT_PATH . 'users.php');
                $total_users = sizeof ($file) - 1;
                
                $num_authors = sizeof ($author);
                if ( $num_authors != $total_users && $num_authors > 0 )
                {
                    $text .= '$fn_author = array (';
                    $comma = '';
                    foreach ( $author as $user )
                    {
                        $text .= $comma . "'" . addslashes ($user) . "'";
                        $comma = ', ';
                    }
                    $text .= ");\n";
                }
                
                if ( $pagination != $config['news_pagination'] )
                {
                    $text .= '$fn_pagination = ' . $pagination . ";\n";
                }
                
                if ( $num_news != $config['numofposts'] )
                {
                    $text .= '$fn_news_per_page = ' . $num_news . ";\n";
                }
                
                if ( ($date_order == 'asc' && !$config['flip_news']) || ($date_order == 'desc' && $config['flip_news']) )
                {
                    $text .= "\$fn_date_order = '" . $date_order . "';\n";
                }
                
                if ( $template != '' && $template != 'news_temp' )
                {
                    if ( !file_exists (FNEWS_ROOT_PATH . 'templates/' . $template . '.php') )
                    {
                        trigger_error ("The template '" . $template . "' does not exist.", E_USER_WARNING);
                    }
                    
                    $text .= '$fn_template = \'' . $template . "';\n";
                }
                
                $text .= "include '" . FNEWS_ROOT_PATH . "news.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 1: // headlines
                $num_headlines = ( isset ($PVARS['headlines_to_show']) ) ? (int)$PVARS['headlines_to_show'] : $config['numofh'];
                
                if ( $newsurl == '' )
                {
                    trigger_error ($lang['ind168'], E_USER_WARNING);
                }
                
                if ( $num_headlines <= 0 )
                {
                    trigger_error ($lang['ind156'], E_USER_WARNING);
                }
                
                $text = "&lt;?php\n\n";
                
                $file = file (FNEWS_ROOT_PATH . 'categories.php');
                $total_categories = sizeof ($file) - 1; // -1 to remove first php line
                
                $num_categories = sizeof ($category);
                if ( $num_categories != $total_categories && $num_categories > 0 )
                {
                    $text .= '$fn_category = array (' . implode (',', $category) . ");\n";
                }
                
                if ( $num_headlines != $config['numofh'] )
                {
                    $text .= "\$fn_num_headlines = $num_headlines;\n";
                }
                
                $text .= "\$fn_news_url = '$newsurl';\n";
				$text .= "include '" . FNEWS_ROOT_PATH . "headlines.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 2: // archives
                if ( $newsurl == '' )
                {
                    trigger_error ($lang['ind168'], E_USER_WARNING);
                }
            
                $text = "&lt;?php\n\n" .
                        "\$fn_news_url = '$newsurl';\n" .
                        "include '" . FNEWS_ROOT_PATH . "archive.php';\n\n" .
                        "?&gt;";
			break;

			case 3: // search
                $simple_search = ( isset ($PVARS['simple_search']) ) ? (int)$PVARS['simple_search'] : 0;
                
                $text = "&lt;?php\n\n";
                
                if ( $simple_search )
                {
                    $text .= "\$fn_simple_search = 1;\n";
                }
            
				$text .= "include '" . FNEWS_ROOT_PATH . "search.php';\n";
                
                $text .= "\n?&gt;";
			break;

			case 4: // rss
                if ( sizeof ($category) > 1 )
                {
                    trigger_error ($lang['ind142'], E_USER_WARNING);
                }
            
				$text = $config['furl'] . '/rss.php';
				if ( isset ($category[0]) )
				{
					$text .= '?fn_category=' . $category;
				}
			break;
            
            default:
                trigger_error ($lang['ind90'], E_USER_WARNING);
            break;
		}

		echo $lang['ind373'] . '
<div style="text-align:center"><textarea rows="12" cols="60" style="width:80%">' . $text . '</textarea></div>';
	}
	else
	{
		$category_selection = build_category_selection (null, array(), true);
        $author_selection = build_author_selection (array(), true);
        
        $asc_selected = $config['flip_news'] ? ' selected="selected"' : '';
        $desc_selected = !$config['flip_news'] ? ' selected="selected"' : '';
        
        $pagination_checked = checkbox_checked ($config['news_pagination']);
        
		echo <<< html
<form method="post" action="?id=admin_syndication">
<table class="adminpanel">
	<thead>
		<tr>
			<th colspan="2">{$lang['ind324']}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="2">
                <input type="submit" name="submit" class="mainoption" value="{$lang['ind370']}" />
            </th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td style="width:30%"><label for="include">{$lang['ind325']}</label></td>
			<td>
				<select id="include" name="include" onchange="display_options(this.options[this.selectedIndex].value);">
					<option value="0">{$lang['ind128']}</option>
					<option value="1">{$lang['ind327']}</option>
					<option value="2">{$lang['ind328']}</option>
					<option value="3">{$lang['ind329']}</option>
					<option value="4">{$lang['ind330']}</option>
				</select>
			</td>
		</tr>
        
        <tr id="options-header">
            <th colspan="2">{$lang['ind404']}</th>
        </tr>
        
        <!-- Category option: used by many -->
        <tr id="category-option">
			<td>{$lang['ind405']}</td>
			<td>
                $category_selection
            </td>
		</tr>
        
        <!-- Search options -->
        <tr id="simple-search-option">
            <td><label for="simple_search">{$lang['ind406']}</label></td>
            <td>
                <input type="checkbox" name="simple_search" id="simple_search" value="1" />
            </td>
        </tr>
        
        <!-- Headline options -->
        <tr id="news-url-option">
            <td><label for="news_url">{$lang['ind371']}</label></td>
            <td>
                <input type="text" class="post" name="news_url" id="news_url" value="{$config['hurl']}" />
            </td>
        </tr>
        <tr id="num-headlines-option">
            <td><label for="headlines_to_show">{$lang['ind60']}</label></td>
            <td>
                <input type="text" class="post" name="headlines_to_show" id="headlines_to_show" size="3" value="{$config['numofh']}" />
            </td>
        </tr>
        
        <!-- News options -->
        <tr id="author-option">
            <td>{$lang['ind407']}</td>
            <td>
                $author_selection
            </td>
        </tr>
        <tr id="pagination-option">
            <td><label for="pagination">{$lang['ind408']}</label></td>
            <td>
                <input type="checkbox" name="pagination" value="1" id="pagination"$pagination_checked />
            </td>
        </tr>
        <tr id="num-news-option">
            <td><label for="news_per_page">{$lang['ind58']}</label></td>
            <td>
                <input type="text" class="post" name="news_per_page" id="news_per_page" size="3" value="{$config['numofposts']}" />
            </td>
        </tr>
        <tr id="date-order-option">
            <td><label for="order">{$lang['ind409']}</label></td>
            <td>
                <select class="post" id="order" name="order">
                    <option value="asc"$asc_selected>{$lang['ind410']}</option>
                    <option value="desc"$desc_selected>{$lang['ind411']}</option>
                </select>
            </td>
        </tr>
        <tr id="template-option">
            <td>
                <label for="template">{$lang['ind326']}</label><br />
                <small>{$lang['ind207']}</small>
            </td>
            <td>
                <input type="text" class="post" name="template" id="template" />
                {$lang['ind240']}
            </td>
        </tr>
	</tbody>
</table>
</form>
<script type="text/javascript">
//<![CDATA[

var options = ["category-option", "simple-search-option", "news-url-option", "num-headlines-option",
                "author-option", "pagination-option", "num-news-option", "date-order-option", "template-option"];
                
var options_header_id = "options-header";

function hide_all_options()
{
    document.getElementById(options_header_id).style.display = 'none';
    for ( var i in options )
    {
        document.getElementById(options[i]).style.display = 'none';
    }
}

var include_options = [
    ["category-option", "author-option", "pagination-option", "num-news-option", "date-order-option", "template-option"],
    ["category-option", "news-url-option", "num-headlines-option"],
    ["news-url-option"],
    ["simple-search-option"],
    ["category-option"]
];

// Some 'constants'
var NEWS_INCLUDE = 0;
var HEADLINE_INCLUDE = 1;
var ARCHIVE_INCLUDE = 2;
var SEARCH_INCLUDE = 3;
var RSS_INCLUDE = 4;

function display_options ( include_type )
{
    if ( include_type < NEWS_INCLUDE || include_type > RSS_INCLUDE )
    {
        return;
    }
    
    hide_all_options();
    
    if ( include_options[include_type].length > 0 )
    {
        document.getElementById(options_header_id).style.display = 'table-row';
    }
    
    for ( var i in include_options[include_type] )
    {
        document.getElementById(include_options[include_type][i]).style.display = 'table-row';
    }
}

display_options (NEWS_INCLUDE);

//]]>
</script>
html;
	}

?>