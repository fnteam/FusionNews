<?php

include '../common.php';
define ('ACCESS_DENIED', '<?php die (\'You may not access this file.\'); ?>');

// Word filter
$filter = array ('fuck' => 'f**k', 'shit' => 's**t', 'nigger' => 'n****r', 'damn' => 'd**n');
$num_filter = sizeof ($filter);
$filter_write = ACCESS_DENIED . "\n";
for ( $i = 0; $i < $num_filter; $i++ )
{
	list ($key, $value) = each ($filter);
	$filter_write .= $key . '|<|' . $value . '|<|' . ($i % 2) . '|<|' . ((int)!($i % 2)) . '|<|' . "\n";
}

if ( safe_write ('badwords.php', 'wb', $filter_write) !== false )
{
	echo 'Badwords added successfully.<br />';
}
else
{
	echo 'Adding badwords failed!<br />';
}

// Users
$users = array ('Blackshadow', 'Crak', 'Walter', 'Dannonb', 'equatorsoft', 'Covenant', 'Alex', 'Michael', 'Tim');
$num_users = sizeof ($users);
$user_write = ACCESS_DENIED . "\n";
for ( $i = 0; $i < $num_users; $i++ )
{
	$login_name = strtolower ($users[$i]);

	$user_write .= $login_name . '|<|' . $users[$i] . 'Nick|<|' . ($i % 2) . '=' . $login_name . '@fusionnews.net|<|http://www.fusionnews.net/images/icons/' . $login_name . '.png|<|0|<|' . md5 ($users[$i] . 'Pass') . '|<|' . (($i % 3) + 1) . '|<|' . "\n";
}

if ( safe_write ('users.php', 'wb', $user_write) !== false )
{
	echo 'Users added successfully.<br />';
}
else
{
	echo 'Adding users failed!<br />';
}

// Categories
$categories = array ('General', 'Entertainment', 'Sport', 'Financial', 'Gossip', 'History', 'Health', 'Science & Technology');
$num_categories = sizeof ($categories);
$cat_write = ACCESS_DENIED . "\n";
for ( $i = 1; $i <= $num_categories; $i++ )
{
	$user_list = NULL;
	for ( $j = 0; $j < ($i % $num_users); $j++ )
	{
		$user_list .= strtolower ($users[$j]) . ',';
	}
	$user_list = substr ($user_list, 0, -1);

	$cat_write .= $i . '|<|' . $categories[$i - 1] . '|<|http://www.fusionnews.net/images/categories/' . $categories[$i - 1] . '.png|<|' . $user_list . '|<|' . "\n";
}
if ( safe_write ('categories.php', 'wb', $cat_write) !== false )
{
	echo 'Categories added successfully.<br />';
}
else
{
	echo 'Adding categories failed!<br />';
}

// Posts
$toc_write = ACCESS_DENIED . "\n";

$lorem_ipsum = array (
	'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam viverra velit eget leo. Curabitur in dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Donec at quam. Curabitur tristique nonummy urna. Aenean libero eros, dictum vitae, pulvinar in, placerat vitae, purus. Suspendisse eu neque. Vivamus facilisis tincidunt augue. Proin hendrerit. Phasellus a nulla. Donec orci sapien, nonummy iaculis, pulvinar nec, pellentesque in, lacus. Praesent lacus est, ullamcorper eu, suscipit et, dapibus eget, leo. Morbi a leo a libero imperdiet tincidunt.',
	'Ut rutrum velit facilisis velit. Sed pulvinar tellus quis leo. Nulla sit amet ipsum. Nunc at arcu eget lorem interdum dictum. In hac habitasse platea dictumst. Donec accumsan accumsan sem. Nullam tortor mi, lobortis sit amet, euismod sed, sollicitudin luctus, nisl. Praesent vulputate. Curabitur cursus, leo at dictum suscipit, enim lorem dictum justo, consequat consectetuer dolor magna sit amet erat. Integer ante enim, eleifend ac, ultricies vestibulum, condimentum vel, diam. Sed laoreet mi et augue. Vivamus arcu orci, consectetuer id, congue nec, venenatis id, dolor. Vestibulum bibendum, purus ultricies accumsan dictum, mauris erat sollicitudin erat, eget gravida diam sem semper massa. Duis nonummy, tellus ac gravida euismod, eros enim rutrum quam, eu tincidunt ante nisi nec justo. In ipsum. Aliquam mauris. Sed eget ligula vel purus sagittis volutpat. Aliquam erat volutpat. Phasellus et odio.',
	'Curabitur erat ipsum, cursus nec, rhoncus et, volutpat eu, lectus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla laoreet sodales mi. Nam ante lorem, gravida quis, fermentum eu, semper et, turpis. Nam mollis. Donec dui. Pellentesque at ligula sit amet risus fringilla ullamcorper. Maecenas lectus. Suspendisse nibh turpis, lobortis quis, hendrerit eu, auctor nec, purus. Duis lectus.',
	'Fusce risus augue, aliquet et, tempus nec, egestas vel, arcu. Quisque tortor arcu, sodales nec, sagittis in, dapibus a, odio. Vestibulum turpis dolor, mattis et, posuere non, aliquet sed, libero. Sed vulputate, nulla eu viverra sagittis, urna lorem pulvinar justo, eget laoreet arcu justo sit amet erat. Etiam ornare nibh ut est. Maecenas venenatis mi a orci. Nulla nisi augue, vulputate non, faucibus eu, lacinia laoreet, urna. Praesent nibh elit, porta non, nonummy id, hendrerit eu, quam. Donec tempor. Nullam quis metus. Aliquam tempus. Vestibulum tincidunt sagittis sapien. Sed tortor elit, laoreet et, sagittis quis, facilisis eu, lorem. Morbi mattis. Maecenas ultrices, dolor eu bibendum hendrerit, nisi turpis euismod orci, nec pretium nisl lectus id tellus.',
	'Curabitur dignissim dapibus neque. Sed vel urna facilisis sapien vestibulum sodales. Suspendisse mattis aliquam augue. Mauris faucibus odio a turpis. Sed dictum tempus nunc. Proin id erat. Donec elementum magna luctus elit. Maecenas bibendum consectetuer magna. In mollis purus ut quam. Quisque consequat fringilla eros. Nam pretium, justo et vehicula porta, quam ipsum faucibus sem, id consequat mi neque nec justo. Nunc blandit massa sit amet est. Morbi tortor lacus, tincidunt vel, suscipit at, eleifend ut, ante.',
	'Curabitur vel ipsum. Ut tempor, mi eget tincidunt rutrum, mi orci consequat ipsum, nec posuere nisi ante sed neque. Mauris rutrum nunc. Nulla auctor tellus vel diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam nonummy ultricies pede. In hac habitasse platea dictumst. Pellentesque cursus, metus et tincidunt lacinia, tellus metus semper elit, ac bibendum urna lacus nec turpis. Praesent ligula turpis, pretium quis, sagittis quis, pellentesque eget, lectus. Etiam pretium. Aenean gravida fringilla velit. Cras sit amet lacus sit amet quam fermentum bibendum. Praesent tempor ipsum vitae neque. Curabitur congue. Integer at arcu eget tortor vehicula lacinia. Morbi cursus commodo urna. Aliquam lacus odio, vulputate vitae, dictum vitae, placerat non, ante. Morbi suscipit.',
	'Ut in nulla at justo facilisis sollicitudin. Nulla facilisi. Pellentesque justo est, placerat vestibulum, aliquet vel, varius id, dui. Suspendisse sed mauris. In condimentum imperdiet enim. Fusce in quam sit amet neque tincidunt euismod. Cras semper magna ut lectus. Donec orci. Nulla malesuada molestie enim. Phasellus condimentum tellus sed arcu. Vestibulum augue arcu, blandit vel, porttitor non, dictum eu, pede. Etiam tellus ipsum, pellentesque eget, feugiat a, placerat quis, dui. Aliquam nibh. Etiam a felis vel lacus egestas nonummy. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce suscipit. Vestibulum vulputate libero a metus. Vivamus fringilla, augue nec accumsan pharetra, enim risus nonummy purus, vel tempus risus nulla quis nisl. Nunc justo felis, congue eu, pharetra nec, pellentesque ornare, lorem. Cras in arcu.',
);

define ('NUM_POSTS', 100);
$current_time = time();
for ( $i = NUM_POSTS; $i > 0; $i-- )
{
	$timestamp = $current_time - ($i * 10000);
	$user = strtolower ($users[$i % $num_users]);
	$subject = substr ($lorem_ipsum[$i % 7], 0, 10);
	$news = $lorem_ipsum[$i % 7];
	$category_list = NULL;
	$category_limit = rand (1, $num_categories);
	for ( $j = 0; $j < $category_limit; $j++ )
	{
		$category_list .= rand (1, $num_categories) . ',';
	}
	$category_list = substr ($category_list, 0, -1);

	$toc_write .= $i . '|<|' . $timestamp . '|<|' . $user . '|<|' . $subject . '|<|' . $category_list . '|<|' . "\n";
	
	$news_write = ACCESS_DENIED . "\n";
	$news_write .= $news . '|<|' . $news . '|<|' . $user . '|<|' . substr ($lorem_ipsum[$i % 7], 0, 10) . '|<|' . substr ($lorem_ipsum[$i % 7], 0, 5) . '|<|' . $category_list . '|<|' . $timestamp . '|<|0|<|' . $i . '|<|' . "\n";
	
	if ( safe_write ('news/news.' . $i . '.php', 'wb', $news_write) !== false )
	{
		echo '...News ID ' . $i . ' added successfully.<br />';
	}
	else
	{
		echo '...Adding News ID ' . $i . ' failed!<br />';
	}
}

if ( safe_write ('news/toc.php', 'wb', $toc_write) !== false )
{
	echo 'TOC written successfully.<br />';
}
else
{
	echo 'Writing TOC failed!<br />';
}

?>