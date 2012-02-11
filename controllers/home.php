<?php

	if ( has_access (NEWS_REPORTER) )
	{
		$title = $lang['ind9'];
		$welcome = sprintf ($lang['ind376'], $userdata['nick']);

		echo <<< html
<div style="text-align:center">
<p>$welcome</p>
<p><b>{$lang['ind377']}</b><br />
html;

        $failed_files = find_inaccessible_files();
		if ( $failed_files !== null )
		{
            echo '<table style="width:400px; margin:0 auto">';
            foreach ( $failed_files as $file => $reason )
            {
                switch ( $reason )
                {
                    case 'missing':
                        echo '<tr><td>' . sprintf ($lang['ind253'], $file) . '</td></tr>';
                        break;
                        
                    case 'nowrite':
                        echo '<tr><td>' . sprintf ($lang['ind375'], $file) . '</td></tr>';
                        break;
                }
            }
			echo '</table>';
		}
		else
		{
			echo $lang['ind378'];
		}

        $stats = get_news_statistics ($userdata);
		$database_status = sprintf ($lang['ind379'], $stats['posts_total'], $stats['posts_by_user'], $stats['posts_today']);

		echo <<< html
</p>
<p>
$database_status
html;

		if ( has_access (NEWS_EDITOR) && $config['com_validation'] )
		{
			$num_comments = get_pending_comments();
			if ( $num_comments > 0 )
			{
				echo '<br /><a href="?id=comments_manage">' . sprintf ($lang['ind384'], $num_comments) . '</a>';
			}
		}

		echo <<< html
</p>
<p><b>{$lang['ind385']}</b></p>
</div>
html;

		echo $lang['ind13'];

		if ( has_access (NEWS_ADMIN) )
		{
			echo $lang['ind11'];
		}
		elseif ( has_access (NEWS_EDITOR) )
		{
			echo $lang['ind12'];
		}

		echo $lang['ind14'];
	}
	else
	{
		$title = $lang['ind3'];
        login_form ($lang['ind0']);
	}
    
?>