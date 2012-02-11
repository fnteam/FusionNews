<?php


    if ( !has_access (NEWS_ADMIN) )
	{
		trigger_error ($lang['ind19'], E_USER_WARNING);
	}

    $title = $lang['ind414'];

    $action = isset ($VARS['action']) ? $VARS['action'] : null;
    if ( $action == 'repair' )
    {
        if ( !resync_news_toc() )
        {
            echo '<p>' . $lang['ind415'] . '</p>';
        }
        else
        {
            echo '<p>' . $lang['ind416'] . '</p>';
        }
    }
    else
    {
        echo '<p>This tool will repair the news database by scanning the news/ directory, and re-creating the news/toc.php file from existing news files.</p>
<p><a href="?id=admin_repair_toc&amp;action=repair">Use tool to repair the news database.</a></p>';
    }


?>