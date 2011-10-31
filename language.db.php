<?php

/**
 * English Language file
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: language.db.php 392 2011-10-31 22:10:47Z xycaleth $
 *
 * This file is part of Fusion News.
 *
 * Fusion News is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Fusion News is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Fusion News.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * English Language Pack
 * This language pack is included with the original distribution
 * of Fusion News by default.
 *
 * Variables (e.g. a word with a $ sign in front of it, $fpath)
 * should not be removed when translating to another language
 * because they are there for a reason.
 *
 * Also, quotation marks ", should have a preceeding backslash
 * if you want to use them in the message to prevent PHP errors.
 */

/**#@+
 * @ignore
 */
$months = array (
	'1'=> 'January',
	'2'=> 'February',
	'3'=> 'March',
	'4'=> 'April',
	'5'=> 'May',
	'6'=> 'June',
	'7'=> 'July',
	'8'=> 'August',
	'9'=> 'September',
	'10'=> 'October',
	'11'=> 'November',
	'12'=> 'December'
);

//°***************°
//$ Errormessages $
//°***************°
$error0   = 'FIX ME';
$error1   = 'You may not access this file.';
// %s is replaced by the file name
$error2   = 'Could not open file "%s" for reading/writing.';
$error3   = 'Unable to lock the file "%s" for reading/writing.';
$error4 = 'The requested news post could not be found.';
$error6   = 'FIX ME';
$error7   = 'Cannot open news.*.php for reading/writing!<br />Make sure the "news" directory is CHMODed to 755 and all files in this directory to 644.';
$error8   = 'Cannot open config.php for reading/writing!<br />Make sure it is CHMODed to 644.';
$error9   = 'Cannot open flood.php for reading/writing!<br />Make sure it is CHMODed to 644.';
$error10  = 'An invalid template ID was given.';
$error11  = 'Cannot open users.php for reading/writing!<br />Make sure it is CHMODed to 644.';
$error12  = 'You cannot delete yourself.';
$error13  = 'Cannot open users.php for reading!<br />Make sure it is CHMODed to 644.';
$error14  = 'You are not allowed to edit this post.';
$error15  = 'FIX ME';
$error16  = 'FIX ME';
$error17  = 'Cannot open badwords.php for reading!<br />Make sure badwords.php is CHMODed to 644.';
$error18  = 'Cannot open badwords.php for reading/writing!<br />Make sure badwords.php is CHMODed to 644.';
$error19  = 'Cannot open banned.php for reading/writing!<br />Make sure banned.php is CHMODed to 644.';
$error20  = 'You are not permitted to post news!';
$error21  = 'Could not open the smillies directory for reading - make sure it is CHMODed to 755.';
$error23 = 'One or more of the fields were left blank.';

//°*************°
//$  Index.php  $
//°*************°
$ind0     = "Welcome to Fusion News. Please log in.";
$ind1     = 'Categories Edited';
$ind2     = 'The selected categories have been updated.';
$ind3     = 'Log In';
$ind4a    = 'New';
$ind4b    = 'Old';
$ind4     = 'Password';
$ind5     = 'Poster';
$ind6     = 'Email';
$ind7     = 'Icon URL';
$ind8     = 'User Level';
$ind9     = 'Logged In';
// %s is replaced by the first category's name (usually General)
$ind10    = 'You cannot delete category ID 1 (%s).';
$ind11    = <<<html
<tr>
<td><a href="?id=admin">Admin</a></td>
<td>&nbsp;- manage all script settings</td>
</tr>
html;
$ind12    = <<<html
<tr>
<td><a href="?id=admin">Editor's Panel</a></td>
<td>&nbsp;- manage comments, word filter or uploads</td>
</tr>
html;
$ind13    = <<<html
<table style="margin:0px auto 0px auto; border-width:0px">
	<tr>
		<td><a href="?id=postnews">New Post</a></td>
		<td>&nbsp;- create a new news item</td>
	</tr>
	<tr>
		<td><a href="?id=editposts">Edit Posts</a></td>
		<td>&nbsp;- manage existing news items</td>
	</tr>
	<tr>
		<td><a href="?id=editprofile">Edit Profile</a></td>
		<td>&nbsp;- edit your profile</td>
	</tr>
html;
if ( isset ($hurl) )
{
	// Bit of hackery to stop errors showing on installation
	$ind14    = <<<html
	<tr>
		<td><a href="?id=view">View News</a></td>
		<td>&nbsp;- view the news items in news.php</td>
	</tr>
	<tr>
		<td><a href="$hurl">Home Page</a></td>
		<td>&nbsp;- back to your home page</td>
	</tr>
	<tr>
		<td><a href="?id=help">Help/Update</a></td>
		<td>&nbsp;- general help, checking for updates</td>
	</tr>
	<tr>
		<td><a href="?id=logout">Log Out</a></td>
		<td>&nbsp;- end the log in session</td>
	</tr>
</table><br />
html;
}
$ind15    = 'Post';
$ind16    = 'Reset';
$ind17    = 'Error';
$ind18 = 'Username and password must be entered to log in.';
$ind18b = 'Incorrect username or password.';

//// admin page ////
$ind19    = 'You do not have permission to access this page.';
// %s is replaced by the user's name.
$ind20    = 'The user "%s" does not exist.';
$ind21    = 'Settings Saved';
$ind22    = 'Your settings were saved.';
$ind23    = 'Edit Templates';
$ind24    = <<< html
<p><a href="javascript:toggleView('masks_1');">Show/Hide Text Masks</a></p>
<div id="masks_1" style="display:none">
<table class="adminpanel">
	<tr><td>{post_id}</td><td>Displays the ID number of the post.</td></tr>
	<tr><td>{news}</td><td>Displays the short news.</td></tr>
	<tr><td>{icon}</td><td>Displays the poster's icon.</td></tr>
	<tr><td>{user}</td><td>Displays the poster's name.</td></tr>
	<tr><td>{date}</td><td>Displays the date the post was made.</td></tr>
	<tr><td>{subject}</td><td>Displays the subject of the post.</td></tr>
	<tr><td>{description}</td><td>Displays the description of the post.</td></tr>
	<tr><td>{fullstory}</td><td>Displays a link to view the full story (if any).</td></tr>
	<tr><td>{send}</td><td>Displays a link to tell others about the post.</td></tr>
	<tr><td>{comments}</td><td>Displays a link to make comments about the post.</td></tr>
	<tr><td>{nrc}</td><td>Displays the number of comments made about the post.</td></tr>
	<tr><td>{cat_id}</td><td>Displays the ID number of the category the post belongs to.</td></tr>
	<tr><td>{cat_icon}</td><td>Displays the icon of the category the post belongs to.</td></tr>
	<tr><td>{cat_name}</td><td>Displays the name of the category the post belongs to.</td></tr>
</table><p></p>
</div>
html;
$ind25    = <<< html
<h2>Full News Template</h2>
<p><a href="javascript:toggleView('masks_2');">Show/Hide Text Masks</a></p>
<div id="masks_2" style="display:none">
<table class="adminpanel">
	<tr><td>{post_id}</td><td>Displays the ID number of the post.</td></tr>
	<tr><td>{shortnews}</td><td>Displays the short news.</td></tr>
	<tr><td>{fullnews}</td><td>Displays the full news.</td></tr>
	<tr><td>{icon}</td><td>Displays the poster's icon.</td></tr>
	<tr><td>{user}</td><td>Displays the poster's name.</td></tr>
	<tr><td>{date}</td><td>Displays the date the post was made.</td></tr>
	<tr><td>{subject}</td><td>Displays the subject of the post.</td></tr>
	<tr><td>{description}</td><td>Displays the description of the post.</td></tr>
	<tr><td>{send}</td><td>Displays a link to tell others about the post.</td></tr>
	<tr><td>{comments}</td><td>Displays a link to make comments about the post.</td></tr>
	<tr><td>{nrc}</td><td>Displays the number of comments made about the post.</td></tr>
	<tr><td>{cat_id}</td><td>Displays the ID number of the category the post belongs to.</td></tr>
	<tr><td>{cat_icon}</td><td>Displays the icon of the category the post belongs to.</td></tr>
	<tr><td>{cat_name}</td><td>Displays the name of the category the post belongs to.</td></tr>
</table><p></p>
</div>
html;
$ind26    = <<< html
<p><a href="javascript:toggleView('masks_1');">Show/Hide Text Masks</a></p>
<div id="masks_1" style="display:none">
<table class="adminpanel">
	<tr><td>{comment}</td><td>Displays the comment.</td></tr>
	<tr><td>{date}</td><td>Displays the date the comment was made.</td></tr>
	<tr><td>{poster}</td><td>Displays the commenter's name.</td></tr>
	<tr><td>{posterip}</td><td>Displays the commenter's IP address.</td></tr>
</table><p></p>
</div>
html;
$ind27 = 'Find';
$ind28 = 'Replacement';
$ind29 = 'Case<br />Sensitive';
$ind30    = <<< html
<p><a href="javascript:toggleView('masks_1');">Show/Hide Text Masks</a></p>
<div id="masks_1" style="display:none">
<table class="adminpanel">
	<tr><td>{post_id}</td><td>Displays the ID number of the post.</td></tr>
	<tr><td>{linkstart}</td><td>Represents the beginning of the link (to view the news post).</td></tr>
	<tr><td>{linkend}</td><td>Represents the end of the link (to view the news post).</td></tr>
	<tr><td>{icon}</td><td>Displays the poster's icon.</td></tr>
	<tr><td>{user}</td><td>Displays the poster's name.</td></tr>
	<tr><td>{date}</td><td>Displays the date the post was made.</td></tr>
	<tr><td>{subject}</td><td>Displays the subject of the post.</td></tr>
	<tr><td>{description}</td><td>Displays the description of the post.</td></tr>
	<tr><td>{cat_id}</td><td>Displays the ID number of the category the post belongs to.</td></tr>
	<tr><td>{cat_icon}</td><td>Displays the icon of the category the post belongs to.</td></tr>
	<tr><td>{cat_name}</td><td>Displays the name of the category the post belongs to.</td></tr>
</table><p></p>
</div>
html;
$ind30a    = <<< html
<p><a href="javascript:toggleView('masks_1');">Show/Hide Text Masks</a></p>
<div id="masks_1" style="display:none">
<table class="adminpanel">
	<tr><td>{date}</td><td>Displays the date of the day the posts come under.</td></tr>
	<tr><td>{news_a_day}</td><td>Displays the posts for the specific day.</td></tr>
</table><p></p>
</div>
html;
$ind30b  = 'Edit';
$ind31    = 'Add User';
$ind32    = 'That log in name or nickname is already in use.';
$ind33    = 'User Added';
$ind34    = 'Edit Users';
$ind34a   = 'The user was edited succesfully.';
$ind34b   = 'New Post';
$ind35    = 'Subject';
$ind36    = 'Save';
$ind37    = <<< html
<h2>Comment Form Template</h2>
<p><a href="javascript:toggleView('masks_2');">Show/Hide Text Masks</a></p>
<div id="masks_2" style="display:none">
<table class="adminpanel">
	<tr><td>{post_id}</td><td>Displays the ID number of the post.</td></tr>
	<tr><td>{news}</td><td>Displays the short news.</td></tr>
	<tr><td>{fullstory}</td><td>Displays the full news.</td></tr>
	<tr><td>{icon}</td><td>Displays the poster's icon.</td></tr>
	<tr><td>{user}</td><td>Displays the poster's name.</td></tr>
	<tr><td>{date}</td><td>Displays the date the post was made.</td></tr>
	<tr><td>{subject}</td><td>Displays the subject of the post.</td></tr>
	<tr><td>{description}</td><td>Displays the description of the post.</td></tr>
	<tr><td>{send}</td><td>Displays a link to tell others about the post.</td></tr>
	<tr><td>{com}</td><td>Displays a link to make comments about the post (this page).</td></tr>
	<tr><td>{nrc}</td><td>Displays the number of comments made about the post.</td></tr>
	<tr><td>{comments}</td><td>Displays the comments made about the post.</td></tr>
	<tr><td>{cat_id}</td><td>Displays the ID number of the category the post belongs to.</td></tr>
	<tr><td>{cat_icon}</td><td>Displays the icon of the category the post belongs to.</td></tr>
	<tr><td>{cat_name}</td><td>Displays the name of the category the post belongs to.</td></tr>
	<tr><td>{pagination}</td><td>Displays a list of pages (if pagination has been enabled).</td></tr>
	<tr><td>{prev_page|Previous Text}</td><td>Displays a link to the previous page with <i>Previous Text</i> as the link (if pagination has been enabled).</td></tr>
	<tr><td>{next_page|Next Text}</td><td>Displays a link to the next page with <i>Next Text</i> as the link (if pagination has been enabled).</td></tr>

	<tr><td colspan="2" style="border-width:0px">&nbsp;</td></tr>

	<tr><td>[form]</td><td><b>Required</b>. This is where the form starts, and should be placed before any of the masks below.</td></tr>
	<tr><td>[/form]</td><td><b>Required</b>. This is where the form end, and should be placed after any of the masks below.</td></tr>
	<tr><td>[namefld,width]</td><td>Displays the name field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[mailfld,width]</td><td>Displays the email field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[pwfld,width]</td><td>Displays the password field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[comfld,width,height]</td><td>Displays the comments field. <i>width</i> should be replaced with the desired width of the field, <i>height</i> should be replaced with the desired height of the field.</td></tr>
	<tr><td>[comlen]</td><td>Displays a field showing the remaining number of characters allowed for the comment.</td></tr>
	<tr><td>[securityimg]</td><td>Displays the CAPTCHA (visual confirmation) image if enabled. This should be removed if visual confirmation is disabled.</td></tr>
	<tr><td>[securityfld]</td><td>Displays the CAPTCHA (visual confirmation) field. This should be removed if visual confirmation is disabled.</td></tr>
	<tr><td>[buttons]</td><td>Displays the submit and reset button.</td></tr>
	<tr><td>[rememberchk]</td><td>Displays a checkbox to allow the system to remember the user's name and email address.</td></tr>
</table><p></p>
</div>
html;
$ind38 = <<<html
<h2>Send to Friend Template</h2>
<p><a href="javascript:toggleView('masks_2');">Show/Hide Text Masks</a></p>
<div id="masks_2" style="display:none">
<table class="adminpanel">
	<tr><td>{post_id}</td><td>Displays the ID number of the post.</td></tr>
	<tr><td>{news}</td><td>Displays the short news.</td></tr>
	<tr><td>{fullstory}</td><td>Displays the full news.</td></tr>
	<tr><td>{icon}</td><td>Displays the poster's icon.</td></tr>
	<tr><td>{user}</td><td>Displays the poster's name.</td></tr>
	<tr><td>{date}</td><td>Displays the date the post was made.</td></tr>
	<tr><td>{subject}</td><td>Displays the subject of the post.</td></tr>
	<tr><td>{description}</td><td>Displays the description of the post.</td></tr>
	<tr><td>{com}</td><td>Displays a link to make comments about the post (this page).</td></tr>
	<tr><td>{nrc}</td><td>Displays the number of comments made about the post.</td></tr>
	<tr><td>{cat_id}</td><td>Displays the ID number of the category the post belongs to.</td></tr>
	<tr><td>{cat_icon}</td><td>Displays the icon of the category the post belongs to.</td></tr>
	<tr><td>{cat_name}</td><td>Displays the name of the category the post belongs to.</td></tr>

	<tr><td colspan="2" style="border-width:0px">&nbsp;</td></tr>

	<tr><td>[form]</td><td><b>Required</b>. This is where the form starts, and should be placed before any of the masks below.</td></tr>
	<tr><td>[/form]</td><td><b>Required</b>. This is where the form end, and should be placed after any of the masks below.</td></tr>
	<tr><td>[sendnamefld,width]</td><td>Displays the sender's name field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[recnamefld,width]</td><td>Displays the receiver's name field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[sendmailfld,width]</td><td>Displays the sender's email field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[revmailfld,width]</td><td>Displays the receiver's email field. <i>width</i> should be replaced with the desired width of the field.</td></tr>
	<tr><td>[mes,width,height]</td><td>Displays the message field. <i>width</i> should be replaced with the desired width of the field, <i>height</i> should be replaced with the desired height of the field.</td></tr>
	<tr><td>[securityimg]</td><td>Displays the CAPTCHA (visual confirmation) image if enabled. This should be removed if visual confirmation is disabled.</td></tr>
	<tr><td>[securityfld]</td><td>Displays the CAPTCHA (visual confirmation) field. This should be removed if visual confirmation is disabled.</td></tr>
	<tr><td>[buttons]</td><td>Displays the submit and reset button.</td></tr>
</table><p></p>
</div>
html;
$ind39 = 'You cannot ban a local network IP.';
$ind40 = '<div style="color:red; border:1px solid #FFAAAA">The news below might not look the same when included in your website because of the Fusion News skin that is in use.</div>';
$ind41 = 'There are no news to display.';
$ind42 = 'Upload';
$ind43 = 'News Added';
$ind44 = 'Check All';
$ind44a = 'Uncheck All';
$ind45 = 'URL Settings';
$ind46 = 'These are the most important settings, because they allow Fusion News to work properly.';
$ind47 = 'Full URL to your website';
$ind48 = 'Example: http://www.yourdomain.com';
$ind49 = 'Full URL to your Fusion News directory';
$ind50 = 'Example: http://www.yourdomain.com/news';
$ind51 = 'Day';
$ind52 = 'Month';
$ind53 = 'Full URL to your news page';
$ind54 = 'URL of the page, which includes news.php. Example: http://www.yourdomain.com/index.php';
$ind55 = 'News Post Settings';
$ind56 = 'Date Format';
$ind57 = 'help';
$ind58 = 'Number of news posts to display per page';
$ind59 = 'News Options';
$ind60 = 'Number of headlines to display';
$ind61 = 'Allow in News Posts';
$ind62 = 'Allow BBCode';
$ind63 = 'Allow HTML';
$ind64 = 'Allow Smilies';
$ind65 = 'Addon Settings';
$ind66 = 'Full Story Link Text';
$ind67 = 'Full Story Pop-up Options';
$ind68 = 'Enable pop-up window';
$ind69 = 'Width:';
$ind70 = 'Height:';
$ind71 = '(in pixels; "100" is minimum)';
$ind72 = 'Enable scrollbars (only shown when needed)';
$ind73 = 'Resizeable';
$ind74 = 'Send to Friend Link Text';
$ind75 = 'Send to Friend Pop-up Options';
$ind75a = 'Comment Pop-up Options';
$ind76 = 'Return to the main page';
$ind76a = 'Enable comment form pop-up window';
$ind77 = 'Comments Link Text';
$ind78 = 'Comments Options';
$ind79 = 'Allow new lines in comments';
$ind80 = 'Word Filter Enabled In';
$ind81 = 'Comments';
$ind82 = 'Posts';
$ind83 = 'Please check your options before submitting them';
$ind84 = '<b>Note:</b> all fields must be filled in.';
$ind85 = 'Year';
$ind86 = 'Post Per Day Date Format';
$ind87 = 'Date Posted';
$ind88 = 'Enable WYSIWYG Editor';
$ind89 = 'Send to Friend Options';
$ind90 = 'Unknown include-type given.';
$ind91 = 'Enable flood protection';
$ind91a = 'Maximum Comment Length (0 = no comment length)';
$ind92 = 'seconds between posts from the same IP (min. is 1)';
//END - admin section
$ind93 = 'Short News';
$ind94 = 'Full Story';
$ind95 = 'Edit Posts';
$ind96 = 'Date';
$ind97 = 'Delete';
$ind98 = 'The subject and short news fields must be filled in.';
$ind99 = 'Require CAPTCHA (visual confirmation) when emailing';
$ind100 = 'Your news post has been added.';
$ind101 = 'Posts Edited';
$ind102 = 'The news post has been updated.';
$ind103 = 'You can only edit your own posts as a news reporter.';
$ind104 = 'Viewing News';
$ind105 = 'The server has disabled file uploading, therefore this feature in Fusion News has been disabled automatically.';
$ind106 = 'FIX ME';
$ind107 = 'log out';
$ind108 = 'new post';
$ind109 = 'edit posts';
$ind110 = 'Add';
$ind111 = 'Time Offset';
$ind112 = 'Current Server Time';
$ind113 = 'Edit/Remove User';
$ind114 = 'Field left blank';
$ind115 = 'The log in name, email address or password field was left blank.';
$ind116 = 'Invalid email';
$ind117 = 'You entered an invalid email address.';
$ind118 = 'was added.';
$ind119 = 'Nickname';
$ind120 = 'Stay Logged In';
$ind121 = 'Message Options';
$ind122 = 'is/are turned';
$ind123 = 'Selected posts deleted.';
$ind124 = 'The selected posts were deleted.';
$ind124a = 'Return to the edit posts page';
$ind125 = 'Submit';
$ind126 = 'Delete selected';
$ind127 = '(Deletes selected posts and their comments)';
$ind128 = 'News';
$ind129 = 'A category name was not entered.';
$ind130 = 'Search<br />Method*';
$ind131 = '<p>*<b>Search Method</b> refers to the way the text is searched for.</p><dl><dt>Strict</dt><dd>Matches exact word.</dd><dt>Loose</dt><dd>Looks for partial match, e.g. if <i>ran</i> is being looked for, <i>ty<b>ran</b>t</i> will match because it contains the word.</dd><dt>Regex</dt><dd><b>For advanced users only.</b> This gives absolute control over what should words should be replaced. The Case Sensitive checkbox does not apply when using this method. <i>Note: An incorrect regular expression format will cause an error.</i></dd></dl>';
$ind132 = 'Banned IP List';
$ind133 = 'One line should hold a single IP. To ban a range of IPs, replace the IP octet with an asterisk (*), e.g. To ban IPs 127.0.0.1 - 127.0.255.254, input 127.0.*.*';
$ind134 = 'Edit Comments';
$ind135 = 'Click on the subject to edit the post\'s comments.';
$ind136 = 'With Posts...';
$ind137 = 'Banned IPs updated';
$ind138 = 'Click on the subject to edit the comment.';
$ind139 = 'Name';
$ind140 = 'Icon';
$ind141 = 'No email address';
$ind142 = 'Please select only <strong>one</strong> category for the RSS feed.';
$ind143 = 'ON';
$ind144 = 'OFF';
$ind145 = 'The comment field must be filled in.';
$ind146 = 'Comments updated';
$ind147 = 'The edited comment was saved.';
$ind148 = 'You do not have permission to use this feature.';
$ind149 = 'FIX ME';
$ind150 = 'Selected comments have been deleted.';
$ind151 = 'Selected comments were deleted [a total of';
$ind151a= 'comment(s)]';
$ind152 = 'General Help';
$ind153 = 'Latest Version';
$ind154 = 'Version Check';
$ind155 = 'Your Version';
$ind156 = 'The number of headlines to display must be greater than 0.';
$ind157 = 'Frequently Asked Questions';
$ind158 = 'Below are some problems you are most likely to come across during the time you use Fusion News.';
$ind159 = 'How do I make the news appear on my website?';
$ind160 = 'Show/Hide';
$ind161 = 'Editor\'s Panel';
$ind162 = 'The banned IPs list has been updated.';
$ind163 = 'main page';
$ind164 = 'The selected comments have been deleted.';
$ind165 = 'help/update';
$ind166 = 'FIX ME';
$ind167 = 'Manage Users';
$ind168 = 'Please provide the URL to your news page.';
$ind169 = 'admin';
$ind169a = 'Log In Name';
$ind170 = 'Logged In As:';
$ind171 = 'Not Logged In';
$ind172 = 'The BBCode and/or smiley path is missing.';
$ind173 = 'Word Filter';
$ind174 = 'Update';
$ind175 = 'Filter Posts';
$ind175a = 'Select the template you want to edit:';
$ind176 = 'Archive Header/Footer';
$ind177 = 'Comments Header/Footer';
$ind178 = 'Short and Full News Templates';
$ind179 = 'News Archive Template';
$ind179b = '<h2>News Archive Template</h2>';
$ind180 = 'Comment Templates';
$ind181 = 'Headlines and Send to Friend Templates';
$ind181a = 'Post Per Day template';
$ind182 = 'The number of news items to display must be greater than 0.';
$ind183 = 'Hide Email?';
$ind184 = 'User';
$ind185 = 'You cannot edit posts in the category, "%s".';
///////
$ind186 = 'Control Panel Skin';
$ind187 = '<h2>Header</h2>';
$ind188 = '<h2>Footer</h2>';
$ind189 = '<h2>Short News Template</h2>';
$ind190 = 'Strict';
$ind191 = '<h2>Comments Template</h2>';
$ind192 = '<h2>Headline Template</h2>';
$ind192a = '<h2>Post Per Day Template</h2>';
$ind192b = 'Regex';
$ind193 = 'reporter';
$ind194 = 'editor';
$ind195 = 'administrator';
$ind196 = 'FIX ME';
$ind197 = 'The passwords you entered do not match.';
// %d will be replaced by the category ID.
$ind198 = 'The category ID %d does not exist.';
$ind199 = 'Move posts to';
$ind200 = 'Preview';
$ind201 = 'What to do with posts if the category is being deleted';
$ind202 = 'Word Filter Updated';
$ind203 = 'Manage Comments';
$ind204 = 'Manage/Configure Uploads';
$ind205 = 'Uploaded Images';
$ind206 = 'Delete posts (cannot be undone)';
$ind207 = 'The template must exist in the <strong>templates</strong>. For example, entering <em>news_temp</em> would use the <em>templates/<strong>news_temp</strong>.php</em> template.';
$ind208 = 'Users';
$ind209 = 'Posted After';
$ind210 = 'Smilies';
$ind211 = 'Manage Smilies';
$ind212 = 'Templates';
$ind213 = 'Manage Templates';
$ind214 = 'Posted Before';
$ind215 = 'Manage Word Filter';
$ind216 = 'FIX ME';
$ind217 = 'FIX ME';
$ind218 = 'FIX ME';
$ind219 = 'FIX ME';
$ind220 = 'Enable image uploads for news';
$ind221 = 'Allowed image file extensions:';
$ind222 = '(separate each extension with a &quot;|&quot;. For example &quot;gif|jpg|jpeg|png&quot;)';
$ind223 = 'Maximum image file size (in bytes):';
$ind224 = '(1MB = 1024KB; 1KB = 1024B)';
$ind225 = 'FIX ME';
$ind226 = 'File Name';
$ind227 = 'Size';
$ind228 = 'Last Modified';
$ind229 = 'Failed to delete ';
$ind230 = 'Unable to open the "uploads" directory for reading.';
$ind231 = 'was deleted succesfully.';
$ind232 = 'Loose';
$ind233 = 'New Words Added';
$ind234 = 'The new words have been added to the filter.';
$ind235 = 'Return to the word filter page';
$ind236 = 'Visit the <a href="http://www.fusionnews.net/forums/" onclick="window.open (this.href); return false;">Fusion News Support Forum</a> if you need help and/or support and our support team, and other members of our community will help you out. Remember that we too have other priorites so the time for a response will vary.';
$ind237 = 'Add New Words';
$ind238 = 'Require validation before showing comment';
$ind239 = 'Unvalidated Comments';
$ind240 = '(leave blank to use default template)';
$ind241 = 'Writer';
$ind242 = 'There are no comments waiting to be validated.';
$ind243 = 'Add Smilies';
$ind244 = 'Add Smiley';
$ind245 = 'Smiley';
$ind246 = 'Upload Smilies';
$ind247 = 'File to Upload:';
$ind248 = 'Smiley Added';
$ind249 = 'The selected smiley was added.';
$ind250 = 'Smiley Updated';
$ind251 = 'The changes to the selected smiley have been made.';
// %s replaced by the image filename
$ind252 = '%s exceeds the maximum file size limit.';
// %s is replaced by the missing file's name
$ind253 = '%s is missing.';
$ind254 = 'No file to upload.';
$ind255 = 'The type of file you tried to upload has been disallowed ({';
$ind255a = '} are allowed).';
$ind256 = 'The upload failed';
//in the sense of: 'Love.jpg succesfully uploaded'
$ind257 = 'was successfully uploaded.';
$ind258 = 'Description';
$ind259 = 'FIX ME';
$ind260 = 'Post Accumulation';
$ind261 = 'Accumulate posts made on the same day.';
$ind262 = 'File Uploads:';
$ind263 = 'FIX ME';
$ind264 = 'Total Size of Folder:';
$ind265 = 'RSS Feed';
$ind266 = 'Enable RSS Feed';
$ind267 = 'Link headlines to full story (items not shown in news.php, will be automatically linked to the full story).';
$ind268 = 'Reverse News Display Order';
$ind269 = 'Display news in ascending date order';
$ind270 = 'Upload Images';
$ind271 = 'Insert Uploaded Images';
$ind272 = 'view news';
$ind273 = 'There are currently no words in the word filter.';
$ind274 = 'Edit Smilies';
$ind275 = 'none';
$ind276 = 'BBCode';
$ind277 = 'Smiley Image';
$ind278 = 'Action';
$ind279 = 'Commenter';
$ind280 = 'Administration';
$ind281 = 'No skin file could be found. Please make sure you have uploaded the skin file, and directory.';
$ind282 = 'No posts available to edit or delete.';
$ind283 = 'No comments available to edit or delete.';
$ind284 = 'Edit Profile';
$ind285 = 'User Updated';
$ind286 = 'Profile Updated';
$ind287 = 'Your profile has been updated.';
$ind288 = 'The previous password entered is incorrect.';
$ind289 = 'Deleting Uploaded Images';
$ind290 = 'Headline Options';
$ind291 = 'Time Posted';
$ind292 = 'More news items';
$ind293 = 'All';
$ind294 = 'You are already logged in.';
$ind295 = 'The allowed file extensions field contains errors.';
$ind296 = 'The nickname, username or email field was left blank.';
$ind297 = 'The password must be confirmed to verify the entered password was correct.';
$ind298 = 'Invalid form submission';
$ind299 = 'An invalid IP address was entered.';
$ind300 = 'No images have been uploaded.';
$ind301 = 'The smiley image chosen does not exist.';
$ind302 = 'Configuration';
$ind303 = 'Your version of Fusion News (' . FNEWS_CURVE . ') is currently out-of-date. Please visit the Fusion News website to download the latest version.';
$ind304 = 'Your version of Fusion News (' . FNEWS_CURVE . ') is currently up-to-date!';
$ind305 = 'RSS Title';
$ind306 = 'RSS Description';
$ind307 = 'RSS Encoding (utf-8 will be used if left blank)';
$ind308 = 'Category';
$ind309 = 'You must select at least one category to place the news post in.';
// %s is replaced by the category name.
$ind310 = 'You cannot post in the category, "%s".';
$ind311 = 'Manage Categories';
$ind312 = 'Add Category';
$ind313 = 'Users who can post in this category';
$ind314 = 'Edit Category';
// %s is replaced by the category name.
$ind315 = 'The category name "%s" already exists.';
$ind316 = 'Category Added';
$ind317 = 'Category "%s" has been added!';
$ind318 = 'Validate selected';
$ind319 = 'FIX ME';
$ind320 = 'Categories';
$ind321 = 'The selected comments have been validated, and are now publicly viewable.';
$ind322 = 'Require CAPTCHA (visual confirmation) when making comment';
$ind323 = 'Help and Support';
$ind324 = 'News and RSS Syndication';
$ind325 = 'Include';
$ind326 = 'Template to use';
$ind327 = 'Headlines';
$ind328 = 'Archive';
$ind329 = 'Search';
$ind330 = 'RSS';
$ind331 = 'FIX ME';
$ind332 = 'Enable news pagination';
$ind333 = 'Return to the manage users page';
$ind334 = 'Return to the manage comments page';
$ind335 = 'Return to the smilies page';
$ind336 = 'Return to the categories page';
$ind337 = 'Return to the edit templates page';
$ind338 = 'Return to the manage uploads page';
$ind339 = 'Use page numbers';
$ind340 = 'Use next/prev arrows';
$ind341 = 'Previous Text';
$ind342 = 'Next Text';
$ind343 = 'FIX ME';
$ind344 = 'editor\'s panel';
$ind345 = '<p>The file <b>news.php</b> must be included into your page using PHP. Below is an example PHP code:</p><p><code>&lt;?php include &quot;fusion/news.php&quot;; ?&gt;</code></p><p>The <b>fusion</b> directory in the example should be replaced with the name of the directory where Fusion News is installed.</p><p>When a single category of news needs to be displayed, the variable <b>$fn_category</b> must be defined. Below is another example PHP code which displays news from only category 1.</p><p><code>&lt;?php $fn_category = 1; include &quot;fusion/news.php&quot;; ?&gt;</code></p><p>Each category ID can be found at the Manage Categories page in the admin panel.</p>';
$ind346 = 'ID';
$ind347 = 'How can the layout of the news be changed?';
$ind348 = '<p>The news template, can be changed by going to the <b>Manage Templates</b> page, in the admin panel. On this page, select the <b>' . $ind178 . '</b> option from the drop down menu. The first text box is for the short news, the second text box is for the full news.</p>';
$ind349 = 'Can the number of displayed news posts be changed?';
$ind350 = '<p>Yes - go to the <b>News Post Settings</b> page in the admin panel. Under the <b>' . $ind58 . '</b> heading, change the number in the first text box to the number of posts to be shown.</p>';
$ind351 = 'How does the news archive work?';
$ind352 = '<p>All news posts are automatically archived from the moment it is created. At the moment, the only way of displaying the archive is as a list of months (and their years) which link to another page displaying all the news posts from that particular month.</p>';
$ind353 = 'Feature \'x\' does not work, what should be done to fix the problem?';
$ind354 = '<p>With each release of Fusion News, the developers try to make it as complete and error-free as possible. However, this is not always possible and there will be some bugs remaining. When something is found to be broken, or isn\'t working as it should, please report the problem at our support forum (link is below).</p><p>Before posting, please make sure you follow these steps:</p><ul><li>Use the <b>search</b> facility provided at the forums as there may be a chance someone else has had the same problem and had it fixed</li><li>If there are any visible error messages on the page, include these in the post.</li><li>Explain what was being done before the error message appeared.</li></ul>';
$ind355 = 'Paginate Comments';
$ind356 = 'Comments Per Page (if zero, all comments are displayed on one page)';
$ind357 = 'PHP Notice';
$ind358 = 'PHP Warning';
$ind359 = 'Forgot your password?';
$ind360 = 'Recover Password';
$ind361 = 'Either a username or e-mail address must be given.';
$ind362 = 'No user found with that username or e-mail address.';
$ind363 = '%s,' . "\n\n" .
		'This e-mail was sent to you from the Fusion News installation at %s, in order to reset your password.' . "\n\n" .
		'Your new log in details are below:' . "\n\n" .
		'Log In Name: %s' . "\n" .
		'Password: %s' . "\n\n" .
		'Once logged in, your password can be changed by going to the \'Edit Profile\' page.' . "\n\n" .
		'Regards,' . "\n\n" .
		'Fusion News Mailer';
$ind364 = 'Fusion News - Recover Password';
$ind365 = 'The e-mail could not be sent to give you your new password - your password has not been reset.';
$ind366 = 'Your new password has been sent to your e-mail address! Once you have received your password, you can <a href="index.php">click here to login</a>.';
$ind367 = 'Please enter your username or email address below, and an email will be sent to you with instructions on how to create a new password:';
$ind368 = '- or -';
$ind369 = 'Confirm New Password';
$ind370 = 'Generate Code';
$ind371 = 'URL to news page';
$ind372 = 'FIX ME';
$ind373 = 'The URL or PHP code for the specified type of syndication is below. Don\'t forget to give the page the PHP code is inserted to, a <strong>.php</strong> file extension, otherwise the code might not work.';
$ind374 = 'At least one user must be chosen to have access to this category.';
// %s is replaced by the problematic file's name
$ind375 = '%s could not be opened with write access.';
// %s is replaced by the user's nick name
$ind376 = 'Welcome <b>%s</b>!';
$ind377 = 'Data Files Check';
$ind378 = 'All files are accessible!';
// first %d = number of news items in the database
// second %d = number of news items the logged in user has written
// third %d = number of news items posted today
$ind379 = 'There are <b>%d</b> news items in the database. You have written <b>%d</b> of them.<br />There have been <b>%d</b> news items posted today.';
// %d replaced by the number of unvalidated comments
$ind384 = 'There is/are <b>%d</b> unvalidated comment(s).';
$ind385 = 'What would you like to do:';
$ind396 = '(banned)';
$ind397 = 'Log In Successful';
$ind398 = 'You have been successfully logged in.';
$ind399 = 'Clear Filter';
$ind400 = 'edit profile';
$ind401 = 'Unable to communicate with the Fusion News website to get latest version number.';
$ind402 = 'No comments selected to be deleted.';
$ind403 = 'Comment';
$ind404 = 'Display Options';
$ind405 = 'In category';
$ind406 = 'Use basic search form';
$ind407 = 'By author';
$ind408 = 'Use pagination';
$ind409 = 'Date order';
$ind410 = 'Ascending';
$ind411 = 'Descending';

//°**************°
//$  Archive.php  $
//°**************°
$arch0 = "No month and/or year was given.";

//°**************°
//$  Fullnews.php  $
//°**************°
$flns0	= "An invalid news ID was given.";

//°**************°
//$  Search.php  $
//°**************°
$srch0	= 'Search for author:';
$srch1	= 'Search for keywords:';
$srch2	= 'Search in categories:';
$srch3	= 'Show results from previous:';
$srch4	= 'All results';
$srch5	= 'Days';
$srch6	= 'Weeks';
$srch7	= 'Months';
$srch8	= 'Search';
$srch9	= 'You did not enter any keywords to search for.';
$srch10	= 'You must enter a number greater than zero for the number of days/weeks/months to show news posts.';
$srch11 = 'One or more of your keywords are too short (minimum 3 characters long).';
$srch12 = 'No results were found using your search criteria.';

//°**************°
//$   Send.php   $
//°**************°
$snd0     = 'Hi';
$snd1     = 'check out this news post!';
$snd2     = 'wants to tell you about the news post titled';
$snd3     = 'Follow this link to read the post:';
$snd4     = 'The article has been sent to';
$snd5     = 'Return to the website';
$snd6     = 'An error occured when attempting to send the email.';
$snd7     = 'One or more of the email addresses entered is/are invalid.';
$snd8	    = 'This email was sent using Fusion News, from';

//°*************°
//$ Comment.php $
//°*************°
$com0	= "Off limits for direct use!";
$com1	= '<span style="color:red; font-weight: bold">You didn\'t fill in a name or comment. Please use the back button and fill out both fields.</span><br />';
$com2	= '<span style="color:red; font-weight: bold">You entered an invalid email address or left the email field blank.</span><br />';
$com3	= '<span style="color:red; font-weight: bold">You are banned.</span><br />';
//$com4 and $com5 are one sentence they become ex. ...last 60 seconds...
$com4	= '<span style="color:red; font-weight: bold">You have posted in the last';
$com5	= 'seconds. You\'ll have to wait until this time has passed to comment again.</span><br />';
$com6	= "Thank you for your comment. You will now be redirected to the comments page.<br /><br />\nIf it takes too long press";
$com6a	= '<p>Thank you for your comment - it will need to be validated by an administrator before it is viewable by the public.</p><p>You will now be redirected to the previous page...';
$com7	= "here";
$com8	= "You tried using a reserved name. If you are a registered news user, please check if your password is correct.";
$com9	= "You left your name or comment blank. Please use your back button, and fill in these fields.";
$com10	= "No news item ID was specified.";
$com11	= "The news item ID specified does not exist.";
$com12	= "<tr><td>No comments have been made yet.</td></tr>";
$com13	= '<span style="color:red; font-weight: bold">Incorrect security code.</span><br />';
// %i is replaced by the maximum number of characters.
$com14	= '<span style="color:red; font-weight: bold">Your comment exceeded the %i character limit.</span><br />';
$com15	= 'Submit';
$com16	= 'Reset';
// %d replaced by maximum comment length
$com17	= 'Your comment has reached the maximum comment length (%d). Further text will be ignored.';
$com18	= 'A name as well as a comment must be entered.';

//°*************°
//$ Upload.php $
//°*************°
$upld1	= 'This feature has been disabled by an administrator.';
$upld2	= 'Image uploading has been disabled by the server administrator.';
$upld3	= 'Upload Images';
$upld4	= 'Upload More Images';
$upld5	= 'Refresh';
$upld6	= 'Insert';
$upld7	= 'Short News';
$upld8	= 'Full News';
$upld9 = 'Could not access the \'uploads\' directory';
$upld10 = 'No images were selected to be uploaded.';
$upld11 = 'Images will be uploaded to';
$upld12 = 'Image file extensions allowed are:';
$upld13 = 'The maximum image filesize allowed is:';
$upld14 = 'Image';

/**
 * Installer File
 * Using updated standard.
 */
$lang['install'] = array (
	// General language text
	'Title'				=> 'Fusion News v%s Installation',
	'Continue'			=> 'Continue',
	'Fusion_Step_x'			=> 'Fusion News: Step %d',
	'Step_x'			=> 'Step %d',
	'Powered_by'			=> '<p>Powered by <a href="http://www.fusionnews.net/" onclick="window.open (this.href); return false;">Fusion News</a> v%s &copy; 2006 - 2011, FusionNews.net</p>',
    'Error'             => 'Error',

	// Step 1
	'Intro'				=> 'Welcome to the Fusion News installation process! Before you can start using Fusion News, you need to follow a few quick and simple steps.',
	'Begin'				=> 'Begin',

	// Step 2
	'Yes'				=> 'Yes',
	'No'				=> 'No',
	'Check_settings'		=> 'The installer will now check particular settings to determine whether or not to enable or disable settings in Fusion News.',
	'PHP_installed'			=> 'PHP Version Installed',
	'File_uploads_allowed'		=> 'File Uploads Allowed',
	'Need_Higher_PHP_Ver'		=> 'PHP version 4.3 or higher is required to be installed for Fusion News to work.',
	'File_uploads_disabled'		=> 'The ability to upload files, for news posts and smilies, will be disabled.',
	'GD_library_installed'		=> 'GD Library Enabled w/ TrueType Font (TTF) support',
	'GD_library_require_201'	=> 'The GD library version 2.0 is required in order for visual confirmation images to be created. This will be automatically disabled for you.',
    'No_TTF'                    => 'The visual confirmation images require TTF support to be enabled. The visual confirmation will be automatically disabled for you.',
	'GD_library_required'		=> 'The GD library is required to be installed in order for visual confirmation images to be created. This will be automatically disabled for you.',
	'Not_meet_min_requirements'	=> 'Your webhost/server does not meet the minimum requirements needed for Fusion News to run properly.',

	// Step 3
	'Checking_files'		=> '<p>Here the installer will first check if all the essential files exist, and then check if the correct file permissions on your files have been applied. Any files or directories which do not have a CHMOD value can be left as it was when it was uploaded.</p><p>If any results don\'t come out as <span style="color:#00BB00"><b>Good</b></span>, then you should use a FTP client (such as SmartFTP, FileZilla, WS_FTP, etc) and correct the problems shown in the results column of the table.</p>',
	'Directory_name'		=> 'Directory Name',
	'File_name'			=> 'File Name',
	'File_permission'		=> 'CHMOD Value',
	'Result'			=> 'Result',
	'Missing'			=> 'Missing',
	'Incorrect_permission'		=> 'Incorrect Permission',
	'Good'				=> 'Good',
	'FN_directory'			=> 'Fusion News directory',
	'Found_problems'		=> '<p><span style="color:#FF0000">The installer found %d problems. Please correct these problems before refreshing the page to continue.</span></p>',

	// Step 4
	'Fill_form'			=> 'The form below will create the first user for your Fusion News installation.',
	'Website_url'			=> 'Website URL',
	'Website_url_colon'		=> 'Website URL:',
	'Administrator'			=> 'Administrator User',
	'Username'			=> 'Log In Name:',
	'Nickname'			=> 'Nick Name:',
	'Email'				=> 'Email Address:',
	'Hide_email'			=> 'Hide Email?',
	'Password'			=> 'Password:',
	'Confirm'			=> 'Confirm Password:',

	// Step 5
	'Fields_left_blank'		=> 'One or more of the fields were left blank',
	'Invalid_email'			=> 'You entered an invalid email address.',
	'Passwords_not_matching'	=> 'The passwords you entered do not match.',
	'Install_success'		=> 'And that\'s it! You have successfully installed Fusion News. You can now log in using the username and password you entered on the previous page',
	'Delete_install_file'		=> 'Before logging in, <b>you should delete the install.php file</b> as there is a chance someone else could reinstall Fusion News.',
	'Create_install_lock'		=> 'You will also need to create a blank file called <b>install.lock</b>, and upload it to to your Fusion News directory.',
	'Login_link'			=> 'Click here to log in!',
);

/**#@-*/

?>
