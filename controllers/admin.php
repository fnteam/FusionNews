<?php

if ( has_access (NEWS_ADMIN) )
{
    $title = $lang['ind280'];
?>
<div class="two-column-left">
	<h2><?php echo $lang['ind302']; ?></h2>
	<ul>
		<li><a href="?id=admin_paths"><?php echo $lang['ind45']; ?></a></li>
		<li><a href="?id=admin_news"><?php echo $lang['ind55']; ?></a></li>
		<li><a href="?id=admin_addons"><?php echo $lang['ind65']; ?></a></li>
        <li><a href="?id=admin_cpanel"><?php echo $lang['ind216']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind205']; ?></h2>
	<ul>
        <li><a href="?id=uploads_configure"><?php echo $lang['ind106']; ?></a></li>
		<li><a href="?id=uploads"><?php echo $lang['ind204']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind81']; ?></h2>
	<ul>
		<li><a href="?id=comments_manage"><?php echo $lang['ind203']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind320']; ?></h2>
	<ul>
		<li><a href="?id=categories"><?php echo $lang['ind311']; ?></a></li>
	</ul>
</div>
<div class="two-column-right">
	<h2><?php echo $lang['ind208']; ?></h2>
	<ul>
        <li><a href="?id=user_add"><?php echo $lang['ind31']; ?></a></li>
		<li><a href="?id=users"><?php echo $lang['ind167']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind210']; ?></h2>
	<ul>
		<li><a href="?id=smillies"><?php echo $lang['ind211']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind212']; ?></h2>
	<ul>
		<li><a href="?id=admin_template"><?php echo $lang['ind213']; ?></a></li>
	</ul>

	<h2><?php echo $lang['ind173']; ?></h2>
	<ul>
		<li><a href="?id=badwordfilter"><?php echo $lang['ind215']; ?></a></li>
	</ul>
    
    <h2><?php echo $lang['ind149']; ?></h2>
    <ul>
        <li><a href="?id=admin_syndication"><?php echo $lang['ind324']; ?></a></li>
        <li><a href="?id=admin_repair_toc"><?php echo $lang['ind166']; ?></a></li>
    </ul>
</div>
<div style="clear:both"></div>
<?php
}
else if ( has_access (NEWS_EDITOR) )
{
    $title = $lang['ind161'];
?>
<div class="two-column-left">
<h2><?php echo $lang['ind81']; ?></h2>
<ul>
	<li><a href="?id=comments_manage"><?php echo $lang['ind203']; ?></a></li>
</ul>
<h2><?php echo $lang['ind205']; ?></h2>
<ul>
	<li><a href="?id=uploads"><?php echo $lang['ind204']; ?></a></li>
</ul>
</div>
<div class="two-column-right">
<h2><?php echo $lang['ind173']; ?></h2>
<ul>
	<li><a href="?id=badwordfilter"><?php echo $lang['ind215']; ?></a></li>
</ul>
</div>
<div style="clear:both"></div>
<?php
}
else
{
    trigger_error ($lang['ind19'], E_USER_WARNING);
}


?>