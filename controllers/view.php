<?php


	$title = $lang['ind104'];
	ob_start();
	include 'news.php';
	$news = ob_get_clean();

	ob_start();
	$fn_news_url = 'index.php?id=view';
	include 'headlines.php';
	$headlines = ob_get_clean();

	if ( !empty ($news) )
	{
		// html width
		$news = preg_replace ('/<table(\s?.+\s?)width=\"?[0-9]+\"?/mi', '<table\\1width="100%"', $news);
		// css width
		$news = preg_replace ('/<table(\s?.+\s?)width\:\s*[0-9a-z\-]+[%|px|em|pt]?/mi', '<table\\1width:100%', $news);
	}

	echo ( empty ($news) ) ? '<div style="text-align:center">' . $lang['ind41'] . '</div>' : $lang['ind40'] . '<p></p>' . $headlines . '<br /><br />' . $news;


?>