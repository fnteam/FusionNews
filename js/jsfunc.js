/**
 * Javascript utility file
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: jsfunc.js 393 2012-02-10 22:37:14Z xycaleth $
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

function show_emo ( path )
{
	var image_obj = document.getElementById('emopreview');
	var after_obj = document.getElementById('smiley_image');

	var emoticon = after_obj.options[after_obj.selectedIndex].value;
	
	image_obj.src = './' + path + '/' + emoticon;
	image_obj.title = emoticon;
	image_obj.alt = emoticon;
}

function window_pop ( url, name, width, height )
{
	var popup = window.open (url, name, "height=" + height + ", width=" + width + ", resizable=yes, scrollbars=yes");
	// Just in case the window already exists, so the window will appear still!
	popup.focus();
}

function check_if_selected ( form_id )
{
	var form_obj = document.forms[form_id];
	var button_disabled = true;
	var i, e;
	
	// Find all checkboxes in form.
	for ( i in form_obj.elements )
	{
		e = form_obj.elements[i];
        if ( e == null ) continue;
        
		if ( e.type == 'checkbox' && e.checked )
		{
			button_disabled = false;
			break;
		}
	}
	
	// Disable/enable the Delete button.
	for ( i in form_obj.elements )
	{
		e = form_obj.elements[i];
        if ( e == null ) continue;
        
		if ( e.className == "mainoption" )
		{
			e.disabled = button_disabled;
		}
	}
}

function un_check_all ( form_id, checked )
{
	var form = document.forms[form_id];
	for ( var i in form.elements )
	{
		var control = form.elements[i];
        if ( control == null ) continue;
        
		if ( control.type === 'checkbox' )
		{
			control.checked = checked;
		}
	}
	
	check_if_selected (form_id);
}

function smiley_bbcode ( textbox_name, preText, postText )
{
	// Thanks to ricrat51 @ fusionnews.net forums for reporting this
	// bug and then providing me with a fix :)
	var textbox = document.getElementById(textbox_name);
	var textRange;
	
	postText = postText || "";
	
	textbox.focus();
	
	// First block has been tested and works in Safari 3, Firefox 3, Chrome, and Opera 9
	if ( textbox.selectionStart !== undefined )
	{
		var startPos = textbox.selectionStart;
		var endPos = textbox.selectionEnd;
		var selectedText = textbox.value.substring (startPos, endPos);
		
		textbox.value = textbox.value.substring (0, startPos) +
						preText + selectedText + postText +
						textbox.value.substring (endPos, textbox.value.length);
		
		textbox.selectionStart = startPos + preText.length;
        textbox.selectionEnd = textbox.selectionStart;
	}
	// IE support
	else if ( document.selection !== undefined )
	{
		textRange = document.selection.createRange();
		var originalText = textRange.text;
		
		textRange.text = preText + textRange.text + postText;
		textRange.moveStart ("character", -(postText.length + originalText.length));
		textRange.moveEnd ("character", -(postText.length + originalText.length));
		textRange.select();
	}
	// Don't know what to do, just append BBCode to the end.
	else
	{
		textbox.value += text;
	}
	
	textbox.focus();
}

function jump_template ( form )
{
	var lists = form.menu.options[form.menu.selectedIndex].value;
	
	if ( lists >= 0 && lists <= 7 )
	{
		window.location.href = './index.php?id=edittemps&show=' + lists;
	}
}

function PreviewArticle ( page_id, form_name, article_id )
{
	var form_obj = document.forms[form_name];
	form_obj.action = "index.php?id=" + page_id + "&action=preview";
	if ( article_id >= 0 )
	{
		form_obj.action += "&num=" + article_id;
	}
}

function toggleView ( identifier )
{
	var element = document.getElementById(identifier);
	if ( element !== undefined )
	{
		if ( element.style.display === 'block' )
		{
			element.style.display = 'none';
		}
		else
		{
			element.style.display = 'block';
		}
	} 
}