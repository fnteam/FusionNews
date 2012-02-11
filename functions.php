<?php

/**
 * Global functions used throughout the script
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: functions.php 393 2012-02-10 22:37:14Z xycaleth $
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
 
class BBCodeNode
{
    public $name;
    public $attributes;
    public $children;
    
    public function __construct ( $name, $attributes )
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = array();
    }
} 

class BBCodeParser
{
    private static $parse_table = null;
    private $text;
    private $depth;
    private $tag_stack;
    
    public $html;
    
    private static function get_parse_table()
    {
        if ( BBCodeParser::$parse_table === null )
        {
            BBCodeParser::$parse_table = array (
                'b' => null,
                'i' => null,
                'u' => null,
                's' => null,
                'url' =>'parse_url',
                'color' => 'parse_color'
            );
        }
        
        return BBCodeParser::$parse_table;
    }
    
    public function parse ( $text )
    {
        $this->text = $text;
        $this->depth = 0;
        $this->tag_stack = array();
        $this->html = $this->_parse ($text);
    }
    
    private function _parse ( $text )
    {
        if ( $this->depth > 99 )
        {
            return;
        }
        
        $this->depth++;
        $first_tag = strpos ($text, '[');
        if ( $first_tag === false )
        {
            return $text;
        }
        
        $pretext = substr ($text, 0, $first_tag);
        
        
        $last_tag = strrpos ($text, ']');
        if ( $last_tag === false )
        {
            return $text;
        }
        
        $posttext = substr ($text, $last_tag + 1);
        
        $middletext = substr ($text, $first_tag, $last_tag - $first_tag + 1);
    
        if ( !preg_match_all ('#\[([A-Za-z]+)(?:=([^\[\]]+))?\](.*?)\[/\1\]#', $middletext, $matches, PREG_SET_ORDER) )
        {
            return $text;
        }
        
        $return = $pretext;
        foreach ( $matches as $match )
        {
            $return .= $this->parse_tag ($match[0], $match[3], $match[1], $match[2]);
        }
        $return .= $posttext;
        $this->depth--;
        
        return $return;
    }
    
    private function parse_tag ( $match, $content, $tag, $param = '' )
    {
        $parse_table = BBCodeParser::get_parse_table();
        if ( !array_key_exists ($tag, $parse_table) )
        {
            return $match;
        }
        
        if ( $parse_table[$tag] === null )
        {
            if ( $param == '' )
            {
                return '<' . $tag . '>' . $this->_parse ($content) . '</' . $tag . '>';
            }
            else
            {
                return $match;
            }
        }
        
        return call_user_func (array ($this, $parse_table[$tag]), $content, $param);
    }
    
    private function parse_url ( $content, $param )
    {
        if ( $param == '' )
        {
            if ( BBCodeParser::is_valid_url ($content) )
            {
                return '<a href="' . $content . '">' . $content . '</a>';
            }
            else
            {
                return '[url]' . $content . '[/url]';
            }
        }
        else
        {
            if ( BBCodeParser::is_valid_url ($param) )
            {
                return '<a href="' . $param . '">' . $this->_parse ($content) . '</a>';
            }
            else
            {
                return '[url=' . $param . ']' . $this->_parse ($content) . '[/url]';
            }
        }
    }
    
    private static function is_valid_url ( $url )
    {
        return preg_match ('#^((f|ht)tps?:(.*?))$#', $url);
    }
}

function single_line ( $str )
{
    return str_replace ("\n", '', $str);
}

/**
 * Displays a dropdown menu.
 * @param string $label Label for the dropdown menu.
 * @param string $name Name of the dropdown menu.
 * @param string $selected_value Value of the selected item.
 * @param array $values Option values.
 * @param array $text Option text - if null, then $values is used the option text.
 * @param string $description Optional description to be displayed with the label.
 */
function show_dropdown ( $label, $name, $selected_value, $values, $text = null, $description = '' )
{
    $id = substr (md5 ($name), 0, 10);
    $html = make_dropdown ($name, $id, $selected_value, $values, $text === null ? null : $text);
    show_input ($label, $id, $name, $description, $html);
}
 
/**
 * Displays a checkbox with a label and optional description.
 * @param string $label Label for the checkbox.
 * @param string $name Name of the checkbox.
 * @param bool $ticked Whether the checkbox should be ticked.
 * @param string $description Optional description to be displayed with the label.
 */
function show_checkbox ( $label, $name, $ticked = 0, $description = '' )
{
    $id = substr (md5 ($name), 0, 10);
    $html = '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . checkbox_checked ($ticked) . ' />';
    show_input ($label, $id, $name, $description, $html);
}

/**
 * Displays a textbox for passwords with a label and an optional description.
 * @param string $label Label for the textbox.
 * @param string $name Name of the textbox.
 * @param string $value Optional pre-filled value for the textbox.
 * @param string $description Optional description to be displayed with the label.
 * @param int $size Size of the textbox. Default size is 20.
 */
function show_passwordbox ( $label, $name, $description = '', $size = 20 )
{
    $id = substr (md5 ($name), 0, 10);
    $html = '<input type="password" id="' . $id . '" name="' . $name . '" size="' . $size . '" />';
    show_input ($label, $id, $name, $description, $html);
}

/**
 * Displays a label and some html in the same two columns as other input fields.
 * @param string $label Label to be shown.
 * @param string $html HTML to display on the right hand side.
 */
function show_twocolumn ( $label, $html )
{
    if ( $label && $html ):
?>
<p>
    <span class="label"><?php echo $label; ?></span>
    <span><?php echo $html; ?></span>
</p>
<?php
    endif;
}

/**
 * Displays a textbox with a label and an optional description.
 * @param string $label Label for the textbox.
 * @param string $name Name of the textbox.
 * @param string $value Optional pre-filled value for the textbox.
 * @param string $description Optional description to be displayed with the label.
 * @param int $size Size of the textbox. Default size is 20.
 */
function show_textbox ( $label, $name, $value = '', $description = '', $size = 20 )
{
    $id = substr (md5 ($name), 0, 10);
    $html = '<input type="text" id="' . $id . '" name="' . $name . '" value="' . $value . '" size="' . $size . '" />';
    show_input ($label, $id, $name, $description, $html);
}

/**
 * Displays an input field.
 * @param string $label Label to be shown.
 * @param string $id ID for the input field.
 * @param string $name Name of the input field.
 * @param string $description Description to be shown with the label.
 * @param string $input_html HTML to output for the input field.
 */
function show_input ( $label, $id, $name, $description, $input_html )
{
?>
<p>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
<?php if ( $description ): ?>
    <span class="description"><?php echo $description; ?></span>
<?php endif; ?>
    <?php echo $input_html; ?>
</p>
<?php
}

/**
 * @return bool Returns true is file uploads are enabled on the server, otherwise false.
 */
function file_uploads_enabled()
{
    // Bit of a backwards way of doing it, but the function name itself makes
    // more sense this way.
    if ( strtolower (@ini_get ('file_uploads')) == 'off' || @ini_get ('file_uploads') == 0 || @ini_get ('file_uploads') == '' )
    {
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * @return array List of all languages in the languages/ directory, with key as the
 * lowercase name, value as display name.
 */
function get_languages()
{
    $languages = scandir ('languages');
    function is_lang_file ( $file )
    {
        return preg_match ('#[a-z]+\.lang\.php$#i', $file);
    }
    
    function get_language_name ( $file )
    {
        $name = substr ($file, 0, strpos ($file, '.'));
        return ucfirst ($name);
    }
    
    $languages = array_filter ($languages, 'is_lang_file');
    $lang_names = array_map ('get_language_name', $languages);
    return array_combine ($languages, $lang_names);
}

/**
 * Converts UTF-8 characters to HTML numeric character references.
 * Any 8-bit characters are preserved as they are.
 * 
 * This function was taken from http://www.php.net/manual/en/function.utf8-decode.php#100478, and
 * optimized a little more.
 * @param string $string String to convert.
 * @return string
 */
function utf8_htmlentities ($string) {
    /* note: apply htmlspecialchars if desired /before/ applying this function
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    static $find = null;
    static $replace = null;

    if ( !preg_match("/([\200-\237]|[\241-\377])/", $string) )
    {
        return $string;
    }

    if ( !$find || !$replace )
    {
        $find = array();
        $replace = array();

        // reject too-short sequences
        $find += array (
            "/[\302-\375]([\001-\177])/",
            "/[\340-\375].([\001-\177])/",
            "/[\360-\375]..([\001-\177])/",
            "/[\370-\375]...([\001-\177])/",
            "/[\374-\375]....([\001-\177])/"
        );

        $replace += array (
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1",
            "&#65533;\\1"
        );

        // reject illegal bytes & sequences
        // 2-byte characters in ASCII range
        $find[] = "/[\300-\301]./";
        $replace[] = "&#65533;";

        // 4-byte illegal codepoints (RFC 3629)
        $find[] = "/\364[\220-\277]../";
        $replace[] = "&#65533;";

        // 4-byte illegal codepoints (RFC 3629)
        $find[] = "/[\365-\367].../";
        $replace[] = "&#65533;";

        // 5-byte illegal codepoints (RFC 3629)
        $find[] = "/[\370-\373]..../";
        $replace[] = "&#65533;";

        // 6-byte illegal codepoints (RFC 3629)
        $find[] = "/[\374-\375]...../";
        $replace[] = "&#65533;";

        // undefined bytes
        $find[] = "/[\376-\377]/";
        $replace[] = "&#65533;";

        // reject consecutive start-bytes
        $find[] = "/[\302-\364]{2,}/";
        $replace[] = "&#65533;";

        // decode four byte unicode characters
        $find[] = "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 | (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'";

        // decode three byte unicode characters
        $find[] = "/([\340-\357])([\200-\277])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'";

        // decode two byte unicode characters
        $find[] = "/([\300-\337])([\200-\277])/e";
        $replace[] = "'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'";

        // reject leftover continuation bytes
        $find[] = "/[\200-\277]/";
        $replace[] = "&#65533;";
    }

    return preg_replace ($find, $replace, $string);
}

/**
 * UTF-8 compatible version of strlen().
 * @param string $string
 * @return int Length of $string.
 */
function utf8_strlen ( $string )
{
    return sizeof (utf8_start_bytes ($string));
}
 
/**
 * UTF-8 compatible version of substr(). See PHP documentation
 * for substr() function.
 * @param string $string
 * @param string $start
 * @param string $length (optional)
 * @return string Substring of original string.
 */
function utf8_substr ( $string, $start )
{
    $num_args = func_num_args();
    $starts = utf8_start_bytes ($string);
    $strlen = sizeof ($starts);
    
    if ( !$strlen )
    {
        return false;
    }
    
    if ( $start < 0 )
    {
        $start += $strlen;
    }
    
    if ( $start >= $strlen )
    {
        return false;
    }
    
    if ( $num_args >= 3 )
    {
        $length = func_get_arg (2);
        if ( !$length ) return '';
        if ( $length < 0 )
        {
            $end = $strlen + $length;
        }
        else
        {
            $end = $start + $length;
        }
        
        if ( $end >= $strlen )
        {
            $starts[$strlen] = strlen ($string);
            $end = $strlen;
        }
        
        return substr ($string, $starts[$start], $starts[$end] - $starts[$start]);
    }
    else
    {
        return substr ($string, $starts[$start]);
    }
}
 
/**
 * @param string $string
 * @return array Array of byte positions corresponding to the start of each code point.
 */
function utf8_start_bytes ( $string )
{
    $start = array();
    for ( $i = 0; isset ($string[$i]); $i++ )
    {
        if ( (ord ($string[$i]) & 0xC0) != 0x80 )
        {
            $start[] = $i;
        }
    }
    
    return $start;
}

/**
 * Trims whitespace from the beginning and end of a string. Acts a wrapper
 * for PHP's trim function, but includes non-breaking spaces as well.
 * @param string $str String to trim.
 * @param string $additional_chars Additional characters to trim from the string.
 * @return string Trimmed string.
 */
function fn_trim ( $str, $additional_chars = '' )
{
    return trim ($str, " \r\t\n\0\x0B\xC2\xA0" . $additional_chars);
}
 
/**
 * @return bool True if tell friend feature is disabled. Otherwise false. It is deemeed
 * disabled if both the short news and full news template have ommitted the {send}
 * tag.
 */
function is_tellfriend_disabled()
{
    $short_news_template = get_template ('news_temp.php', false);
    $full_news_template = get_template ('fullnews_temp.php', false);
    return
        strpos ($short_news_template, '{send}') === false &&
        strpos ($full_news_template, '{send}') === false;
}

/**
 * @return bool True if commenting is disabled. Otherwise false. Comments are deemeed
 * disabled if both the short news and full news template have ommitted the {comments}
 * tag.
 */
function is_commenting_disabled()
{
    $short_news_template = get_template ('news_temp.php', false);
    $full_news_template = get_template ('fullnews_temp.php', false);
    return
        strpos ($short_news_template, '{comments}') === false &&
        strpos ($full_news_template, '{comments}') === false;
}

/**
 * Creates a new data line, replacing any new lines with spaces. This function
 * accepts a variable number of arguments, where each argument is a new field.
 */
function create_line_data()
{
    function nl2space ( $str )
    {
        return str_replace ("\n", '', $str);
    }

    $args = func_get_args();
    $args = array_map ('nl2space', $args);
    
    return implode ('|<|', $args) . "|<|\n";
}

function get_user_post_counts()
{
    $users = get_users_all();
    $posts = get_posts_all();
    
    $count = array();
    foreach ( $users as $user )
    {
        $count[$user['username']] = 0;
    }
    
    foreach ( $posts as $post )
    {
        if ( array_key_exists ($post['author'], $count) )
        {
            $count[$post['author']]++;
        }
    }
    
    return $count;
}

/**
 * @return array Array of entries in the news TOC.
 */
function get_toc_all()
{
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $entries = array();
    foreach ( $file as $line )
    {
        $entry = get_line_data ('news_toc', $line);
        $entry['categories'] = explode (',', $entry['categories']);
        
        $entries[] = $entry;
    }
    
    return $entries;
}
 
/**
 * Creates a new time entry for comments.
 * @param array $flood_data Flood data array.
 */
function create_flood_entry ( $flood_data )
{
    if ( array_keys ($flood_data) != get_fields_for_file ('flood') )
    {
        trigger_error ('Flood data array key mismatch.', E_USER_WARNING);
    }

    safe_write ('flood.php', 'ab', "{$flood_data['ip']}|<|{$flood_data['timestamp']}|<|\n");
} 

/**
 * @param int $post_id News ID to create new comment in.
 * @param array $comment_data Comment data array.
 */
function create_comment ( $post_id, $comment_data )
{
    global $config;

    if ( array_keys ($comment_data) != get_fields_for_file ('comments') )
    {
        trigger_error ('Comment data array key mismatch.', E_USER_WARNING);
    }
    
    if ( $config['comallowbr'] )
    {
        $comment_data['message'] = str_replace ("\n", '&br;', $comment_data['message']);
    }
    else
    {
        $comment_data['message'] = str_replace ("\n", '&nbsp;', $comment_data['message']);
    }

    $comment_data['message'] = str_replace ("\r", '', $comment_data['message']);
    
    $id = 'com' . mt_rand();
    $write = create_line_data (
        $comment_data['ip'],
        $comment_data['validated'],
        $comment_data['message'],
        $comment_data['author'],
        $comment_data['email'],
        $comment_data['timestamp'],
        $id
    );
    safe_write ('news/news.' . $post_id . '.php', 'ab', $write);
    
    if ( $comment_data['validated'] )
    {
        $post = get_post ($post_id);
        $post['numcomments']++;
        
        update_post ($post_id, $post);
    }
}
 
/**
 * Gets post counts for specified categories.
 * @param array $category_ids Category IDs to fetch post counts for.
 * @return array Array containing post counts, indexed by category ID.
 */
function get_post_count_in_categories ( $category_ids )
{
    $counts = array_fill_keys ($category_ids, 0);
    
    $toc = get_toc_all();
    foreach ( $toc as $entry )
    {
        foreach ( $entry['categories'] as $id )
        {
            $counts[$id]++;
        }
    }
    
    return $counts;
}
 
/**
 * @param int $category_id ID of category to retrieve.
 * @return array Category data array for specified category, or null if category doesn't exist.
 */
function get_category ( $category_id )
{
    $category = reset (get_categories (array ($category_id)));
    if ( $category === false )
    {
        return null;
    }
    
    return $category;
}

/**
 * Moves all posts from one category, to another category.
 * @param int $old_category_id Move all posts in this category ID.
 * @param int $new_category_id New category to move posts to.
 */
function move_posts ( $old_category_id, $new_category_id )
{
    $posts = get_posts_all();
    
    $moved_posts = array();
    foreach ( $posts as $post )
    {
        if ( in_array ($old_category_id, $post['categories']) )
        {
            $categories = array_diff ($post['categories'], array ($old_category_id));
            if ( !in_array ($new_category_id, $categories) )
            {
                $categories[] = $new_category_id;
            }
            
            $post['categories'] = $categories;
            
            $moved_posts[$post['news_id']] = array ('id' => $post['news_id'], 'categories' => $post['categories']);
            update_post ($post['news_id'], $post);
        }
    }
    
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    foreach ( $file as $line )
    {
        $post = get_line_data ('news_toc', $line);
        if ( isset ($moved_posts[$post['news_id']]) )
        {
            $post['categories'] = implode (',', $moved_posts[$post['news_id']]['categories']);
            $write .= create_line_data (
                $post['news_id'],
                $post['timestamp'],
                $post['author'],
                $post['headline'],
                $post['categories']
            );
        }
        else
        {
            $write .= $line;
        }
    }
    
    safe_write ('news/toc.php', 'wb', $write);
}

/**
 * Deletes all posts in specified category.
 * @param int $category_id Category to delete posts from.
 */
function delete_posts_in_category ( $category_id )
{
    $posts = get_posts_all();
    
    $deleted_posts = array();
    foreach ( $posts as $post )
    {
        if ( in_array ($category_id, $post['categories']) )
        {
            $deleted_posts[$post['news_id']] = $post['news_id'];
            delete_post ($post['news_id']);
        }
    }
    
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    foreach ( $file as $line )
    {
        $post = get_line_data ('news_toc', $line);
        if ( isset ($deleted_posts[$post['news_id']]) )
        {
            continue;
        }
        else
        {
            $write .= $line;
        }
    }
    
    safe_write ('news/toc.php', 'wb', $write);
}

/**
 * Updates the specified category ID.
 * @param int $category_id Category ID to update.
 * @param array $category_data Category data array.
 */ 
function update_category ( $category_id, $category_data )
{
    if ( array_keys ($category_data) != get_fields_for_file ('categories') )
    {
        trigger_error ('Category data array key mismatch.', E_USER_WARNING);
    }
    
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    foreach ( $file as $line )
    {
        $category = get_line_data ('categories', $line);
        if ( $category['category_id'] == $category_id )
        {
            $write .= $category['category_id'] . '|<|' . $category_data['name'] . '|<|' . $category_data['icon'] . '|<|' . implode (',', $category_data['users']) . "|<|\n";
        }
        else
        {
            $write .= $line;
        }
    }
    
    safe_write ('categories.php', 'wb', $write);
}
 
/**
 * @param int $category_id Category ID to delete.
 */
function delete_category ( $category_id )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    foreach ( $file as $line )
    {
        $category = get_line_data ('categories', $line);
        if ( $category['category_id'] == $category_id )
        {
            continue;
        }
        
        $write .= $line;
    }
    
    safe_write ('categories.php', 'wb', $write);
}
 
/**
 * Creates a new category
 * @param array $category_data Catagory data array.
 */
function create_category ( $category_data )
{
    if ( array_keys ($category_data) != get_fields_for_file ('categories') )
    {
        trigger_error ('Category data array key mismatch.', E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    $last_category = get_line_data ('categories', end ($file));
    $last_id = $last_category['category_id'];

    $data = $last_id + 1 . '|<|' . $category_data['name'] . '|<|' . $category_data['icon'] . '|<|' . implode (',', $category_data['users']) . '|<|' . "\n";

    safe_write ('categories.php', 'ab', $data);
}

/**
 * Checks if the given category name exists.
 * @param string $category_name Name of the category to check.
 * @return bool True if the category name exists, otherwise false.
 */
function category_name_exists ( $category_name )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        if ( $category['name'] == $category_name )
        {
            return true;
        }
    }
    
    return false;
}
 
/**
 * Updates the smiley list with the given data.
 * @param array $smiley_data Array of smiley data arrays.
 */
function update_smileys ( $smiley_data )
{
    $fields = get_fields_for_file ('smillies');
    foreach ( $smiley_data as $smiley )
    {
        if ( array_keys ($smiley) != $fields )
        {
            trigger_error ('Badword data array key mismatch.');
        }
    }
    
    $write = DENIED_MSG;
    foreach ( $smiley_data as $smiley )
    {
        $write .= "{$smiley['smiley_id']}|<|{$smiley['bbcode']}|<|{$smiley['image']}|<|\n";
    }
    
    safe_write ('smillies.php', 'wb', $write);
}
 
/**
 * Deletes uploaded files from the uploads/ directory.
 * @param array $files Array of files to delete.
 * @return array Success/failure results. $results['succeeded'] is an
 * array of all files which were successfully deleted. $results['failed']
 * is an array of all files which failed to be deleted.
 */
function delete_uploaded_files ( $files )
{
    $forbidden_exts = array ('php', 'html');
    
    $results = array ('failed' => array(), 'succeeded' => array());
    foreach ( $files as $key => $filename )
    {
        $error = false;
        
        if ( !$error && strpos ($filename, '/') !== false && strpos ($filename, '\\') !== false )
        {
            $error = true;
        }
        
        if ( !$error && in_array (get_file_extension ($filename), $forbidden_exts) )
        {
            $error = true;
        }

        if ( !$error && !@unlink (FNEWS_ROOT_PATH . 'uploads/' . $filename) )
        {
            $error = true;
        }
        
        if ( !$error )
        {
            $results['succeeded'][] = $filename;
        }
        else
        {
            $results['failed'][] = $filename;
        }
    }
    
    return $results;
}
 
/**
 * @return string Latest Fusion News version number, retrieved from the website.
 */
function get_latest_version_number()
{
    if ( $fp = @fsockopen ('www.fusionnews.net', 80, $errno, $errstr, 10) )
    {
        $out = 'GET /version/fnews_version.txt HTTP/1.1' . "\r\n";
        $out .= 'Host: www.fusionnews.net' . "\r\n";
        $out .= 'Connection: close' . "\r\n\r\n";

        fputs ($fp, $out);
        $getinfo = false;

        while ( fgets ($fp) != "\r\n" );
        $version = fgets ($fp);
        fclose ($fp);

        return $version;
    }
    else
    {
        return null;
    }
}

/**
 * @param string $allowed_extensions Vertical bar separated list of extensions which can be uploaded.
 * @return array Array of all uploaded images.
 */
function get_uploaded_images_all ( $allowed_extensions )
{
    $files = scandir (FNEWS_ROOT_PATH . 'uploads');
    $files = array_filter ($files, create_function ('$f', 'return preg_match ("/^(.+)\.' . $allowed_extensions . '$/i", $f);'));
    
    return array_values ($files);
}
 
/**
 * Adds a smiley
 * @param string $image Smiley image relative to the smillies/ directory.
 * @param string $code Smiley code
 */
function add_smiley ( $image, $code )
{
    $write = mt_rand() . "|<|{$code}|<|{$image}|<|\n";
    safe_write ('smillies.php', 'ab', $write);
}

/**
 * @return array Array of all smiley images in the smillies/ folder.
 */
function get_smiley_images_all()
{
    $files = scandir (FNEWS_ROOT_PATH . 'smillies');
    $images = array_filter ($files, create_function ('$ext', 'return !preg_match ("/^\.\.?$|^index|htm$|html$|db$|^\./i", $ext);'));
    return array_values ($images);
}

/**
 * @return array Array containing all smiley data.
 */
function get_smileys_all()
{
    $file = file(FNEWS_ROOT_PATH . 'smillies.php');
    array_shift ($file);

    $smileys = array();
    foreach ( $file as $value )
    {
        $smileys[] = get_line_data ('smillies', $value);
    }
    
    return $smileys;
}
 
/**
 * @param string $comment_id Comment ID to delete.
 * @param int $news_id News ID the comment belongs to.
 * @param array $commentdata Comment data array.
 */
function update_comment ( $comment_id, $news_id, $commentdata )
{
    if ( array_keys ($commentdata) != get_fields_for_file ('comments') )
    {
        trigger_error ('Comments data array key mismatch.', E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    $write .= array_shift ($file);
    
    foreach ( $file as $comment_text )
    {
        $comment = get_line_data ('comments', $comment_text);
        if ( $comment['comment_id'] == $comment_id )
        {
            $write .= "{$commentdata['ip']}|<|{$commentdata['validated']}|<|{$commentdata['message']}|<|{$commentdata['author']}|<|{$commentdata['email']}|<|{$commentdata['timestamp']}|<|{$commentdata['comment_id']}|<|\n";
        }
        else
        {
            $write .= $comment_text;
        }
    }
    
    safe_write ('news/news.' . $news_id . '.php', 'wb', $write);
}

/**
 * @param string $comment_id Comment ID to delete.
 * @param int $news_id News ID the comment belongs to.
 */
function delete_comment ( $comment_id, $news_id )
{
    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    $post = array_shift ($file);
    $post = get_line_data ('news', $post);
    
    $data = '';
    foreach ( $file as $comment_text )
    {
        $comment = get_line_data ('comments', $comment_text);
        if ( $comment['comment_id'] == $comment_id )
        {
            $post['numcomments']--;
            continue;
        }
        
        $data .= $comment_text;
    }
    
    $write .= implode ('|<|', $post) . "|<|\n";
    $write .= $data;
    
    safe_write ('news/news.' . $news_id . '.php', 'wb', $write);
}

/**
 * @param string $ip IP address to check.
 * @return bool True if IP address is valid, otherwise false.
 */
function is_valid_ip_address ( $ip )
{
    $octets = explode ('.', $ip);
    if ( sizeof ($octets) != 4 )
    {
        return false;
    }
    
    for ( $i = 0; $i < 4; $i++ )
    {
        if ( $octets[$i] < 0 || $octets[$i] >= 255 )
        {
            return false;
        }
    }
    
    return true;
}

/**
 * @param string $ip IP address to add to the ban list.
 */
function banlist_add ( $ip )
{
    safe_write ('banned.php', 'ab', $ip . "\n");
}

function banlist_delete ( $ip )
{
    $ips = get_banned_ips();
    $key = array_search ($ip, $ips);
    if ( $key !== false )
    {
        unset ($ips[$key]);
        banlist_update ($ips);
    }
}
 
/**
 * @param array Array of IPs to fill the ban list with.
 */
function banlist_update ( $ips )
{
    global $lang;

    $bannedlist = DENIED_MSG;
    $invalid_ip = false;
    foreach ( $ips as $ip )
    {
        if ( !$ip )
        {
            continue;
        }

        if ( is_private_address ($ip) )
        {
            trigger_error ($lang['ind39'], E_USER_WARNING);
        }

        if ( !is_valid_ip_address ($ip) )
        {
            trigger_error ($lang['ind299'], E_USER_WARNING);
        }
        
        $bannedlist .= $ip . "\n";
    }

    safe_write ('banned.php', 'wb', $bannedlist);
}

/**
 * @param array $comment Comment data array
 * @return bool True if comment is unmoderated, otherwise false.
 */
function is_comment_unmoderated ( $comment )
{
    return !$comment['validated'];
}

/**
 * @param array $comment Comment data array
 * @return bool True if comment is moderated, otherwise false.
 */
function is_comment_moderated ( $comment )
{
    return !is_comment_unmoderated ($comment);
}

/**
 * @return array Array of all unmoderated comments, grouped by news ID.
 */
function get_unmoderated_comments_all()
{
    $file = get_ordered_toc();
    $comments = array();
    
    foreach ( $file as $post )
    {
        $post_comments = get_comments_all ($post['news_id']);
        $post_comments = array_filter ($post_comments, 'is_comment_unmoderated');
        if ( sizeof ($post_comments) > 0 )
        {
            $comments[$post['news_id']] = $post_comments;
        }
    }
    
    return $comments;
}

/**
 * @param int $news_id News ID to get comments for
 * @return array Array of all moderated comments for specified news ID.
 */
function get_moderated_comments ( $news_id )
{
    return array_filter (get_comments_all ($news_id), 'is_comment_moderated');
}

/**
 * @param int $news_id
 * @return array Array of all comments for specified news ID.
 */
function get_comments_all ( $news_id )
{
    $post_file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($post_file);
    array_shift ($post_file);
    
    $comments = array();
    foreach ( $post_file as $comment )
    {
        $comments[] = get_line_data ('comments', $comment);
    }
    
    return $comments;
}

/**
 * @param string $comment_id
 * @param int $news_id News ID the comment belongs to.
 */
function get_comment ( $comment_id, $news_id )
{
    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $news_id . '.php');
    array_shift ($file);
    array_shift ($file);
    
    foreach ( $file as $comment )
    {
        $comment = get_line_data ('comments', $comment);
        if ( $comment['comment_id'] == $comment_id )
        {
            return $comment;
        }
    }
    
    return null;
}

/**
 * @param array $comment_ids Array of comment IDs to set as validated.
 */
function validate_comments ( $comment_ids )
{
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    foreach ( $file as $newsfile )
    {
        $toc = get_line_data ('news_toc', $newsfile);
        $newsfile = file (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        array_shift ($newsfile);

        $start = DENIED_MSG;

        $article = get_line_data ('news', $newsfile[0]);
        array_shift ($newsfile);

        $write = '';
        foreach ( $newsfile as $comment )
        {
            $comment = get_line_data ('comments', $comment);

            if ( in_array ($comment['comment_id'], $comment_ids) )
            {
                $comment['validated'] = 1;
                ++$article['numcomments'];
            }

            $write .= implode ('|<|', $comment) . '|<|' . "\n";
        }

        $start .= implode ('|<|', $article) . '|<|' . "\n";
        $write = $start . $write;

        safe_write ('news/news.' . $toc['news_id'] . '.php', 'wb', $write);
    }
}

/**
 * @param string $user Username of user to check permissions
 * @param array $categories Array of categories to check against.
 * @return bool True if user can post in any of the categories, otherwise false.
 */
function can_post_in_categories ( $user, $categories )
{
    if ( in_array (0, $categories) )
    {
        return true;
    }

    $catdata = get_categories ($categories);
    foreach ( $catdata as $category )
    {
        if ( in_array ($user, $category['users']) )
        {
            return true;
        }
    }
    
    return false;
}

/**
 * @return array Array of all categories.
 */
function get_categories_all()
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $categories = array();
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        $category['users'] = explode (',', $category['users']);
        $categories[] = $category;
    }
    
    return $categories;
}

/**
 * @param array $categories List of category IDs to fetch.
 * @return array Array of categories
 */
function get_categories ( $categories )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $num_search_categories = sizeof ($categories);
    $cats = array();
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        if ( in_array ($category['category_id'], $categories) )
        {
            $category['users'] = explode (',', $category['users']);
            $cats[] = $category;
            
            if ( sizeof ($cats) == $num_search_categories )
            {
                break;
            }
        }
    }
    
    return $cats;
}

/**
 * Gets a filtered list of all posts, given a set of parameters.
 * @param int $after All listed posts must be made after this timestamp.
 * @param int $before All listed posts must be made before this timestamp.
 * @param string|array $author If not empty, list of posts must be made by at least one of these authors.
 * @param int|array $category If not empty, list of posts must be in at least one of these categories.
 * @return array Filtered array of posts which match the given parameters.
 */
function get_posts_filtered ( $after, $before, $author, $category )
{
    $posts = get_posts_all();
    $filtered_posts = array();
    
    if ( !is_array ($category) )
    {
        if ( $category == 0 )
        {
            $category = array();
        }
        else
        {
            $category = array ($category);
        }
    }
    $num_categories = sizeof ($category);
    
    if ( !is_array ($author) )
    {
        if ( $author )
        {
            $author = array ($author);
        }
        else
        {
            $author = array();
        }
    }
    $num_authors = sizeof ($author);
    
    foreach ( $posts as $post )
    {
        if ( $before != -1 && $after != -1 )
        {
            if ( $post['timestamp'] >= $before || $post['timestamp'] < $after )
            {
                continue;
            }
        }
        
        if ( $num_authors > 0 )
        {
            if ( !in_array ($post['author'], $author) )
            {
                continue;
            }
        }
        
        if ( $num_categories > 0 )
        {
            if ( !sizeof (array_intersect ($category, $post['categories'])) )
            {
                continue;
            }
        }
        
        $filtered_posts[] = $post;
    }
    
    return $filtered_posts;
}

/**
 * Resynchronizes the news table of contents file (toc.php).
 */
function resync_news_toc()
{
    $files = @scandir ('news');
    if ( $files === null )
    {
        return false;
    }

    $news_files = array_filter ($files, create_function ('$f', 'return preg_match ("#^news\.\d+\.php$#", $f);'));
    natsort ($news_files);
    $news_files = array_reverse ($news_files);

    $toc_data = DENIED_MSG;
    foreach ( $news_files as $file )
    {
        $news_file = file (FNEWS_ROOT_PATH . 'news/' . $file);
        $news_data = get_line_data ('news', $news_file[1]);
        $toc_data .= "{$news_data['news_id']}|<|{$news_data['timestamp']}|<|{$news_data['author']}|<|{$news_data['headline']}|<|{$news_data['categories']}|<|\n";
    }
    
    safe_write ('news/toc.php', 'wb', $toc_data);
    
    return true;
}

/**
 * @return array Array of all news posts.
 */
function get_posts_all()
{
    $toc = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($toc);
    
    $posts = array();
    foreach ( $toc as $post )
    {
        $post = get_line_data ('news_toc', $post);
        
        $post_file = file (FNEWS_ROOT_PATH . 'news/news.' . $post['news_id'] . '.php');
        $post = get_line_data ('news', $post_file[1]);
        $post['categories'] = explode (',', $post['categories']);
        $posts[] = $post;
    }
    
    return $posts;
}
 
/**
 * @return array Array of all banned IP addresses.
 */
function get_banned_ips()
{
    $file = file (FNEWS_ROOT_PATH . 'banned.php');
    array_shift ($file);
    
    return array_map ('trim', $file);
}

/**
 * @param string $ip IP address to check.
 * @return bool Returns true if the given IP address is a private one, otherwise false.
 */
function is_private_address ( $ip )
{
    return !strncmp ($ip, '10.', 3) || !strncmp ($ip, '172.16.', 7) || !strncmp ($ip, '192.168.', 8);
}

/**
 * @param array $badword_data Array of badword data arrays to add.
 */
function add_badwords ( $badword_data )
{
    $fields = get_fields_for_file ('badwords');
    foreach ( $badword_data as $rule )
    {
        if ( array_keys ($rule) != $fields )
        {
            trigger_error ('Badword data array key mismatch.');
        }
    }
    
    $write = '';
    foreach ( $badword_data as $rule )
    {
        $write .= "{$rule['find']}|<|{$rule['replace']}|<|{$rule['case_sensitive']}|<|{$rule['type']}|<|\n";
    }
    
    safe_write ('badwords.php', 'ab', $write);
} 

/**
 * @param array $badword_data Array of badword data arrays to write to file. By not including
 * existing entries into this array, you can effectively delete those entries from the file.
 */
function update_badwords ( $badword_data )
{
    $fields = get_fields_for_file ('badwords');
    foreach ( $badword_data as $rule )
    {
        if ( array_keys ($rule) != $fields )
        {
            trigger_error ('Badword data array key mismatch.');
        }
    }
    
    $write = DENIED_MSG;
    foreach ( $badword_data as $rule )
    {
        $write .= "{$rule['find']}|<|{$rule['replace']}|<|{$rule['case_sensitive']}|<|{$rule['type']}|<|\n";
    }
    
    safe_write ('badwords.php', 'wb', $write);
}

/**
 * @return array Array of all word filter rules.
 */
function get_badwords()
{
    $file = file (FNEWS_ROOT_PATH . 'badwords.php');
    array_shift ($file);
    
    $badwords = array();
    foreach ( $file as $line )
    {
        $badwords[] = get_line_data ('badwords', $line);
    }
    
    return $badwords;
}

/**
 * Creates a dropdown select-box.
 * @param string $select_name Name of the dropdown box
 * @param string $id ID of the dropdown box
 * @param string $selected_value Value of the selected item
 * @param array $values Option values
 * @param array $text Option text - if null, then $values is used the option text.
 * @param string $extra_html Optional additional HTML to add to the <select> tag.
 */
function make_dropdown ( $select_name, $id, $selected_value, $values, $text = null, $extra_html = null )
{
    $html = '<select name="' . $select_name . '" id="' . $id . '" ' . $extra_html . '>';
    if ( $text === null )
    {
        $text = $values;
    }
    
    for ( $i = 0; isset ($values[$i]); $i++ )
    {
        if ( $values[$i] == $selected_value )
        {
            $html .= '<option value="' . $values[$i] . '" selected="selected">' . $text[$i] . '</option>';
        }
        else
        {
            $html .= '<option value="' . $values[$i] . '">' . $text[$i] . '</option>';
        }
    }
    
    $html .= '</select>';
    return $html;
}

/**
 * @param int $post_id
 * @return array Post data array for specified post ID, or null if the post doesn't exist.
 */
function get_post ( $post_id )
{
    $filename = FNEWS_ROOT_PATH . 'news/news.' . $post_id . '.php';
    if ( file_exists ($filename) )
    {
        $file = file ($filename);
        array_shift ($file);
        
        $post = get_line_data ('news', $file[0]);
        $post['categories'] = explode (',', $post['categories']);
        
        return $post;
    }
    
    return null;
}

/**
 * @param int $post_id Post ID to update
 * @param array $postdata Post data to update with.
 */
function update_post ( $post_id, $postdata )
{
    global $lang;
    
    if ( array_keys ($postdata) != get_fields_for_file ('news') )
    {
        trigger_error ('News data array key mismatch.', E_USER_WARNING);
    }

    $file = file (FNEWS_ROOT_PATH . 'news/news.' . $post_id . '.php');
    array_shift ($file);

    $article = get_line_data ('news', $file[0]);

    $postdata['shortnews'] = str_replace ("\n", '&br;', $postdata['shortnews']);
    $postdata['fullnews'] = str_replace ("\n", '&br;', $postdata['fullnews']);

    $cs_categories = implode (',', $postdata['categories']);
    $data = DENIED_MSG;
    $data .= "{$postdata['shortnews']}|<|{$postdata['fullnews']}|<|{$postdata['author']}|<|{$postdata['headline']}|<|{$postdata['description']}|<|{$cs_categories}|<|{$postdata['timestamp']}|<|{$article['numcomments']}|<|{$post_id}|<|\n";

    // Skip the existing news data line.
    array_shift ($file);

    $data .= implode ('', $file);
    safe_write ('news/news.' . $post_id . '.php', 'wb', $data);

    $file = file (FNEWS_ROOT_PATH . '/news/toc.php');
    array_shift ($file);

    $data = DENIED_MSG;

    $update = false;
    foreach ( $file as $news_item )
    {
        $toc = get_line_data ('news_toc', $news_item);
        if ( $toc['news_id'] == $post_id )
        {
            $data .= "{$post_id}|<|{$postdata['timestamp']}|<|{$postdata['author']}|<|{$postdata['headline']}|<|{$cs_categories}|<|\n";
        }
        else
        {
            $data .= $news_item;
        }
    }

    safe_write ('news/toc.php', 'wb', $data);
}

/**
 * @param array $postdata Post data array for the new post.
 */
function create_post ( $postdata )
{
    global $lang;

    $postdata['numcomments'] = 0;
    $postdata['news_id'] = 0;
    if ( array_keys ($postdata) != get_fields_for_file ('news') )
    {
        trigger_error ('News data array key mismatch.');
    }
    
    // replace new lines
    $find = array ("\r\n", "\r", "\n");
    $replace = array ('&br;', '&br;', '&br;');

    $postdata['shortnews'] = str_replace ($find, $replace, $postdata['shortnews']);
    $postdata['fullnews'] = str_replace ($find, $replace, $postdata['fullnews']);
    $postdata['description'] = str_replace ($find, $replace, $postdata['description']);

    //info
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    $data_toc = DENIED_MSG;

    $break = '';
    $post_id = 0;

    // Should do this properly? File fomat might change.
    if ( isset ($file[0]) && $file[0] != '' )
    {
        list ($post_id, $tmp2) = explode ('|<|', $file[0]);
        $break = "\n";
    }

    $post_id++;
    $postdata['news_id'] = $post_id;

    $new_file = 'news/news.' . $postdata['news_id'] . '.php';

    // $cs_ being comma separated, like CSV = comma separated values
    $cs_categories = implode (',', $postdata['categories']);

    $data_toc .= "{$postdata['news_id']}|<|{$postdata['timestamp']}|<|{$postdata['author']}|<|{$postdata['headline']}|<|{$cs_categories}|<|{$break}";
    $data_toc .= implode ('', $file);

    $data = DENIED_MSG;
    $data .= "{$postdata['shortnews']}|<|{$postdata['fullnews']}|<|{$postdata['author']}|<|{$postdata['headline']}|<|{$postdata['description']}|<|{$cs_categories}|<|{$postdata['timestamp']}|<|0|<|{$postdata['news_id']}|<|";

    safe_write ($new_file, 'wb', $data);
    safe_write ('news/toc.php', 'wb', $data_toc);
}

/**
 * @return array Array of all users in user data array form
 */
function get_users_all()
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);
    
    $users = array();
    foreach ( $file as $user )
    {
        $users[] = get_line_data ('users', $user);
    }
    
    return $users;
}

function get_user ( $username )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);
    
    $users = array();
    foreach ( $file as $user )
    {
        $user = get_line_data ('users', $user);
        if ( $user['username'] == $username )
        {
            return $user;
        }
    }
    
    return null;
}

/**
 * @return string Randomly generated password salt.
 */
function create_password_salt()
{
    return substr (md5 (uniqid (time(), true)), 0, 10);
}

/**
 * @param array $userdata User data array
 */
function create_user ( $userdata )
{
    if ( array_keys ($userdata) != get_fields_for_file ('users') )
    {
        trigger_error ('Mismatching user array keys');
    }
    
    $write = "{$userdata['username']}|<|{$userdata['nickname']}|<|{$userdata['email']}|<|{$userdata['icon']}|<|{$userdata['timeoffset']}|<|{$userdata['passwordhash']}|<|{$userdata['passwordsalt']}|<|{$userdata['level']}|<|\n";
    safe_write ('users.php', 'ab', $write);
}
 
/**
 * @param string $user Username to reset password
 * @param string $password New password
 */
function reset_user_password ( $user, $password, $hash = false )
{
    if ( $hash )
    {
        $password = md5 ($password);
    }
    
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    foreach ( $file as $line )
    {
        $user2 = get_line_data ('users', $line);
        if ( $user2['username'] == $user )
        {
            $user2['passwordhash'] = $password;
            $write .= implode ('|<|', $user2) . "|<|\n";
        }
        else
        {
            $write .= $line;
        }
    }

    safe_write ('users.php', 'wb', $write);
}

/**
 * @param array $current_user Current user data array.
 * @return array Array containing the total number of posts, total number of posts
 * by the given user, and the number of posts made today.
 */
function get_news_statistics ( $current_user )
{
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);

    $num_news_items = sizeof ($file);
    $num_own_items = 0;
    $num_items_today = 0;

    $today = strtotime ('today');
    $tomorrow = $today + 86400;

    foreach ( $file as $toc_line )
    {
        $news_toc = get_line_data ('news_toc', $toc_line);
        if ( $news_toc['author'] == $current_user['user'] )
        {
            ++$num_own_items;
        }

        if ( $news_toc['timestamp'] >= $today && $news_toc['timestamp'] < $tomorrow )
        {
            ++$num_items_today;
        }
    }
    
    return array (
        'posts_total' => $num_news_items,
        'posts_by_user' => $num_own_items,
        'posts_today' => $num_items_today
    );
}

/**
 * Deletes specified comments.
 * @param array $comment_ids IDs of comments to delete.
 * @param int $post_id Post ID to delete comments from. If left as default, all news posts are searched.
 * @return int Number of comments deleted.
 */
function delete_comments ( $comment_ids, $post_id = 0 )
{
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $post_ids = array();
    if ( $post_id != 0 )
    {
        $post_ids[] = $post_id;
    }
    else
    {
        foreach ( $file as $newsfile )
        {
            $newsfile = explode ('|<|', $newsfile);
            $newsid = $newsfile[0];
            if ( file_exists (FNEWS_ROOT_PATH . 'news/news.' . $newsid . '.php') )
            {
                $post_ids[] = $newsid;
            }
        }
    }
    
    $num_comments_deleted = 0;
    foreach ( $post_ids as $newsid )
    {
        $newsfile = file (FNEWS_ROOT_PATH . 'news/news.' . $newsid . '.php');
        array_shift ($newsfile);

        $article = get_line_data ('news', $newsfile[0]);

        array_shift ($newsfile);
        $comments_deleted = false;
        $write = '';
        foreach ( $newsfile as $comment )
        {
            $com = get_line_data ('comments', $comment);
            if ( in_array ($com['comment_id'], $comment_ids) )
            {
                $comments_deleted = true;
                $article['numcomments'] -= $com['validated'];
                $num_comments_deleted++;
                continue;
            }

            $write .= $comment;
        }

        if ( $comments_deleted )
        {
            $data = DENIED_MSG;
            $data .= implode ('|<|', $article) . "|<|\n";
            $data .= $write;
            safe_write ('news/news.' . $newsid . '.php', 'wb', $data);
        }
    }
    
    return $num_comments_deleted;
}

/**
 * @param array $actor User data array of the user who is deleting the posts.
 * @param array $post_ids Array of post IDs to delete.
 */
function delete_posts ( $actor, $post_ids )
{
    $file = file (FNEWS_ROOT_PATH . 'news/toc.php');
    array_shift ($file);
    
    $write = DENIED_MSG;
    $lines_to_delete = array();
    foreach ( $file as $line => $news_post )
    {
        $toc = get_line_data ('news_toc', $news_post);
        if ( !has_access (NEWS_EDITOR) && $toc['author'] != $actor['user'] )
        {
            // Can't delete someone else's post if your user level is a news writer.
            continue;
        }

        if ( in_array ($toc['news_id'], $post_ids) )
        {
            $lines_to_delete[] = $line;
            @unlink (FNEWS_ROOT_PATH . 'news/news.' . $toc['news_id'] . '.php');
        }
        else
        {
            $write .= $news_post;
        }
    }
    
    foreach ( $lines_to_delete as $line )
    {
        unset ($file[$line]);
    }

    safe_write ('news/toc.php', 'wb', $write);
}

/**
 * @param string $username Username of user to update
 * @param array $userdata New user data array to update with.
 */
function update_user ( $username, $userdata )
{
    if ( array_keys ($userdata) != get_fields_for_file ('users') )
    {
        trigger_error ('Mismatching user array keys');
    }

    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift($file);

    $data = DENIED_MSG;

    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);

        if ( $username == $user['username'] )
        {
            if ( $userdata['passwordhash'] === null )
            {
                $userdata['passwordhash'] = $user['passwordhash'];
            }
            
            //$newpass = ( $new_password != '' ) ? md5 ($new_password) : $user['passwordhash'];
            $data .= "{$userdata['username']}|<|{$userdata['nickname']}|<|{$userdata['email']}|<|{$userdata['icon']}|<|{$userdata['timeoffset']}|<|{$userdata['passwordhash']}|<|{$userdata['passwordsalt']}|<|{$userdata['level']}|<|\n";
        }
        else
        {
            $data .= $value;
        }
    }

    safe_write ('users.php', 'wb', $data);
}

/**
 * @param array Array of all skin names
 */
function get_all_skins()
{
    $dir = opendir (FNEWS_ROOT_PATH . 'skins');
    $skin = array();
    while ( ($file = readdir ($dir)) !== false )
    {
        if ( $file == '.' || $file == '..' )
        {
            continue;
        }
        
        if ( !is_dir (FNEWS_ROOT_PATH . 'skins/' . $file) )
        {
            continue;
        }
        
        if ( !file_exists (FNEWS_ROOT_PATH . 'skins/' . $file . '/index.html') )
        {
            continue;
        }

        $skins[] = $file;
    }
    closedir ($dir);
    
    return $skins;
}

/**
 * Gets the hide email section of a user data email string.
 * @param string $email_string
 * @return bool True if email is hidden, otherwise false.
 */
function is_email_hidden ( $email_string )
{
    return $email_string[0] == '0';
}

/**
 * Gets the email section of a user data email string.
 * @param string $email_string
 * @return string Email address
 */
function get_email ( $email_string )
{
    return utf8_substr ($email_string, strpos ($email_string, '=') + 1);
}

/**
 * Finds the user corresponding to the given username or email address.
 * @param string $username
 * @param string $email
 * @return array|null Returns the user data array if user is found, otherwise false.
 */
function get_user_for_lostpw ( $username, $email )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);

    $found = false;
    $admin_email = '';
    $admin_nick = '';
    foreach ( $file as $line )
    {
        $user = get_line_data ('users', $line);
        $useremail = explode ('=', $user['email']);

        if ( $user['username'] == $username || $useremail[1] == $email )
        {
            return $user;
        }
    }
    
    return null;
}
 
/**
 * @return array|null If no data files are inaccessible, then null is returned. Otherwise
 * an array of all problematic files are returned.
 */
function find_inaccessible_files()
{
    static $data_files = array ('news/toc.php', 'badwords.php', 'banned.php', 'categories.php', 'config.php',
						'flood.php', 'logins.php', 'sessions.php', 'smillies.php', 'users.php');

    $files = null;
    foreach ( $data_files as $file )
    {
        if ( !file_exists (FNEWS_ROOT_PATH . $file) )
        {
            $files[$file] = 'missing';
        }
        else if ( !is_writeable (FNEWS_ROOT_PATH . $file) )
        {
            $files[$file] = 'nowrite';
        }
    }
    
    return $files;
}

/**
 * Checks login credentials.
 * @param string $username Username to check.
 * @param string $password Password hash to check.
 * @return bool True if login is valid, otherwise false.
 */
function is_login_valid ( $username, $password )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift($file);
    
    foreach ( $file as $value )
    {
        $user = get_line_data ('users', $value);

        if ( $username == $user['username'] && md5 ($user['passwordsalt'] . $password) == $user['passwordhash'] )
        {
            return true;
        }
    }
    
    return false;
}

function login_form ( $message = null, $next_url = null )
{
    global $lang;
?>
<?php if ( $message !== null ): ?>
<p><?php echo $message; ?></p>
<?php endif; ?>
<form action="index.php?id=login" method="post">
<?php if ( $next_url !== null ): ?>
<input type="hidden" name="next" value="<?php echo htmlspecialchars ($next_url); ?>" />
<?php endif; ?>
<table cellspacing="0" cellpadding="2">
	<tr>
		<td><label for="username"><?php echo $lang['ind169a']; ?></label></td>
		<td><input type="text" class="post" id="username" name="username" size="20" /></td>
	</tr>
	<tr>
		<td><label for="password"><?php echo $lang['ind4']; ?></label></td>
		<td><input type="password" class="post" id="password" name="password" size="20" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" name="keep_login" id="keep_login" value="1" /> <label for="keep_login"><?php echo $lang['ind120']; ?></label></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<p><input type="submit" class="mainoption" value="<?php echo $lang['ind3']; ?>" /></p>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><a href="?id=lostpassword"><?php echo $lang['ind359']; ?></a></td>
	</tr>
</table>
</form>
<?php
}

/**
 * Revokes a single or multiple users' access to a category
 * @param string|array $usernames The username(s) to revoke category access from.
 */
function revoke_category_access ( $usernames )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    $data = array_shift ($file);
    
    if ( !is_array ($usernames) )
    {
        $usernames = array ($usernames);
    }
    
    foreach ( $file as $line )
    {
        $category = get_line_data ('categories', $line);
        $users = explode (',', $category['users']);
        foreach ( $users as $index => $user )
        {
            if ( in_array ($user, $usernames) )
            {
                unset ($users[$index]);
            }
        }
        
        $category['users'] = implode (',', $users);
        $data .= implode ('|<|', $category) . "|<|\n";
    }
    
    safe_write ('categories.php', 'wb', $data);
}
 
/**
 * Deletes a user
 * @param string $username Username of user to delete.
 */
function delete_user ( $username )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    $data = array_shift ($file);
    
    foreach ( $file as $line )
    {
        $user = get_line_data ('users', $line);
        if ( $user['username'] == $username )
        {
            continue;
        }
        
        $data .= $line;
    }
    
    safe_write ('users.php', 'wb', $data);
}

/**
 * Replaces BBCode in the given text
 * @param string $message The text containing the BBCodes to be replaced
 * @return string The processed text with BBCodes replaced
 * @todo Handle nested tags.
 */
function replace_bbcode ( $message )
{
    static $bbcode_normal_find_pair = array (
       '[move]', '[/move]',
       '[sub]', '[/sub]',
       '[sup]', '[/sup]',
       '[s]', '[/s]',
       '[b]', '[/b]',
       '[u]', '[/u]',
       '[i]', '[/i]',
       '[tt]', '[/tt]',
       '[quote]', '[/quote]',
       '[list]', '[/list]'
    );
    
    static $bbcode_normal_replace_pair = array (
       '<marquee>', '</marquee>',
       '<sub>', '</sub>',
       '<sup>', '</sup>',
       '<del>', '</del>',
       '<strong>', '</strong>',
       '<u>', '</u>',
       '<em>', '</em>',
       '<tt>', '</tt>',
       '<blockquote>quote:<hr style="height:1px" />&quot;', '&quot;<hr style="height:1px" /></blockquote>',
       '<ul>', '</ul>'
    );
    
    static $bbcode_normal_find = array (
        '[hr]'
    );
    
    static $bbcode_normal_replace = array (
        '<hr />'
    );
    
    $message = str_replace ($bbcode_normal_find, $bbcode_normal_replace, $message);
    
    $find = array();
    $replace = array();
    for ( $i = 0, $num_bbcode = sizeof ($bbcode_normal_find_pair); $i < $num_bbcode; $i += 2 )
    {
        $find[] = '#' . preg_quote ($bbcode_normal_find_pair[$i], '#') . '(.*?)' . preg_quote ($bbcode_normal_find_pair[$i + 1], '#') . '#i';
        $replace[] = $bbcode_normal_replace_pair[$i] . '$1' . $bbcode_normal_replace_pair[$i + 1];
    }
    
    $message = preg_replace ($find, $replace, $message);
	
    static $email_regex = '([A-Za-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^_`\{\|\}\~\.]{1,64}@[a-z0-9\-\.]{2,65})';
	$bbcode_preg_find = array (
	   '/\[font=(\w+)](.*?)\[\/font\]/i',
	   '/\[color=(\w+)](.*?)\[\/color\]/i',
	   '/\[size=(\d+)](.*?)\[\/size\]/i',
	   '/\[align=(left|center|right)](.*?)\[\/align\]/i',
	   '/\[img]([^\[\]]*)\[\/img]/i',
	   '/\[img height=(\d+) width=(\d+)]([^\[\]]*)\[\/img]/i',
	   '/\[img width=(\d+) height=(\d+)]([^\[\]]*)\[\/img]/i',
	   '/\[flash=(\d+),(\d+)]([^\[\]]*)\[\/flash]/i',
	   '/\[email]' . $email_regex . '\[\/email\]/i',
	   '/\[email=' . $email_regex . '](.*?)\[\/email\]/i',
	   '#\[\*\]([^\[]+)#',
	   '#\[url\]([^"\[\]]+)\[/url\]#i',
	   '#\[url=([^"\[\]]+)\]([^\[]+)\[/url\]#i',
	);
	
	$bbcode_preg_replace = array (
	   '<span style="font-family:$1">$2</span>',
	   '<span style="color:$1">$2</span>',
	   '<span style="font-size:$1px">$2</span>',
	   '<div style="text-align:$1">$2</div>',
	   '<img src="$1" alt="" />',
	   '<img src="$3" style="height:$1px; width:$2px" alt="" />',
	   '<img src="$3" style="height:$2px; width:$1px" alt="" />',
	   '<object type="application/x-shockwave-flash" data="$3" width="$1" height="$2"><param name="movie" value="$3" /><span>Flash required</span></object>',
	   '<a href="mailto:$1">$1</a>',
	   '<a href="mailto:$1">$2</a>',
	   '<li>$1</li>',
	   '<a href="$1">$1</a>',
	   '<a href="$1">$2</a>'
	);
	
	$message = preg_replace ($bbcode_preg_find, $bbcode_preg_replace, $message);

	return $message;
}

/**
 * @return array List of all smileys
 */
function get_smiley_list()
{
    static $smileys = null;
    
    if ( $smileys === null )
    {
        $file = file (FNEWS_ROOT_PATH . 'smillies.php');
        array_shift ($file);
        
        $smileys = array();
        foreach ( $file as $value )
        {
            $smiley = get_line_data ('smillies', $value);
            $smiley['bbcode'] = html_entity_decode ($smiley['bbcode']);
            $smiley['image'] = chop ($smiley['image']);
            $smileys[] = $smiley;
        }
    }
    
    return $smileys;
}

/**
 * Replaces smiley codes in the given text
 * @param string $message Text containing the smiley codes to be replaced
 * @return string The processed text with smiley codes replaced
 */
function replace_smileys ( $message )
{
	global $config;
	
	static $smiley_table = null;
	
	if ( $smiley_table === null )
	{
        $smileys = get_smiley_list();
	    foreach ( $smileys as $smiley )
	    {
		    $smiley_table['find'][] = $smiley['bbcode'];
		    $smiley_table['replace'][] = '<img src="' . $config['furl'] . '/smillies/' . $smiley['image'] . '" alt="' . $smiley['image'] . '" />';
	    }
	}

	$message = str_replace ($smiley_table['find'], $smiley_table['replace'], $message);

	return $message;
}

/**
 * Saves template HTML.
 * @param string $template Template name (excluding directory and file extension).
 * @param string $template_html Template HTML
 */
function save_template ( $template, $template_html )
{
    static $find = array ('&#33;DOCTYPE', '&#60;&#33;', '--&#62;', '&#60;script');
    static $replace = array ('!DOCTYPE', '<!', '-->', '<script');

    $html = html_entity_decode ($template_html);
    $html = str_replace ($find, $replace, $html);
    
    safe_write ('templates/' . $template . '.php', 'wb', $html);
}

/**
 * @param string $template File name of template to get
 * @param bool $php_enabled Whether or not to parse PHP in the template file
 * @return string Contents of template file
 */
function get_template ( $template, $php_enabled = false )
{
	$text = '';

    if ( $php_enabled )
    {
        ob_start();
        include (FNEWS_ROOT_PATH . 'templates/' . $template);
        $text = ob_get_clean();
    }
    else
    {
        $text = file_get_contents (FNEWS_ROOT_PATH . 'templates/' . $template);
    }
        
	return $text;
}

/**
 * @return array Array of toc.php lines, ordered by date
 */
function get_ordered_toc()
{
	if ( !function_exists ('toc_entry_compare') )
    {
        function toc_entry_compare ( $a, $b )
        {
            if ( $a['timestamp'] == $b['timestamp'] )
            {
                return 0;
            }
            
            return $a['timestamp'] > $b['timestamp'] ? -1 : 1;
        }
    }
    
    $entries = get_toc_all();
    usort ($entries, 'toc_entry_compare');
    
    return $entries;
}

/**
 * Filters words listed in the word filter
 * @param string $message Text to have the word filter applied to
 * @return string Word-filtered text
 */
function filter_badwords ( $message )
{
    static $badword_table = null;
    
    if ( $badword_table === null )
    {
	    $badword_table = array('find' => array(), 'replace' => array());
	    $file = file (FNEWS_ROOT_PATH . 'badwords.php');
	    array_shift ($file);

	    foreach ( $file as $rule )
	    {
		    $badword = get_line_data ('badwords', $rule);

		    if ( $badword['type'] == 2 )
		    {
			    // Regular expressions match
			    $badword_table['find'][] = html_entity_decode ($badword['find']);
			    $badword_table['replace'][] = html_entity_decode ($badword['replace']);
		    }
		    else
		    {
			    // Strict
			    if ( $badword['type'] == 0 )
			    {
				    $badword_table['find'][] = '#\b' . preg_quote ($badword['find'], '#') . '\b#' . (!$badword['case_sensitive'] ? 'i' : '');
			    }
			    // Loose
			    else if ( $badword['type'] == 1 )
			    {
				    $badword_table['find'][] = '#' . preg_quote ($badword['find'], '#') . '#' . (!$badword['case_sensitive'] ? 'i' : '');
			    }
			    $badword_table['replace'][] = $badword['replace'];
		    }
	    }
	}

	$message = preg_replace ($badword_table['find'], $badword_table['replace'], $message);

	return $message;
}

/**
 * Checks if the given user name or nick name exists, and then returns
 * the said user's data.
 * @param string $user User's log in name
 * @param string $nick User's nick name
 * @return array|bool Returns FALSE if the user doesn't exist, otherwise
 * returns the specified user's data.
 */
function get_author ( $user, $nick = '' )
{
    static $user_table = array();

	if ( !$nick && !$user )
	{
		return false;
	}
	
	if ( isset ($user_table[$user]) )
    {
        return $user_table[$user];
    }
    
    if ( isset ($user_table[$nick]) )
    {
        return $user_table[$nick];
    }

	if ( !$nick && $user )
	{
		$nick = $user;
	}
	else if ($nick && !$user)
	{
		$user = $nick;
	}

	$file = file(FNEWS_ROOT_PATH . 'users.php');
	array_shift($file);
	foreach ( $file as $value )
	{
		$userdat = get_line_data ('users', $value);

		if( ($user == $userdat['username']) || ($nick == $userdat['nickname']) )
		{		
			$email = explode ('=', $userdat['email']);

		    $user_array = array (
				'user'		=> $userdat['username'],
				'nick'		=> $userdat['nickname'],
				'email'		=> $email[1],
				'showemail'		=> $email[0],
				'icon'		=> $userdat['icon'],
				'timeoffset'	=> $userdat['timeoffset'],
				'level'		=> $userdat['level']
			);
			
			$user_table[$userdat['username']] = $user_table[$userdat['nickname']] = $user_array;
			
			return $user_array;
		}
	}

	return false;
}

/**
 * Converts a number of bytes into text, taking into account the units (B, KB, MB, etc)
 * @param int $size Number of bytes to convert
 * @return string The converted bytes as text.
 */
function calc_size ( $size )
{
	if ( $size < 1024 )
	{ // Bytes
		return $size . ' B';
	}
	else if ( $size < (1024 * 1024) )
	{ // Kilobytes
		return round (($size / 1024), 2) . ' KB';
	}
	else if ( $size < (1024 * 1024 * 1024) )
	{ // Megabytes
		return round ($size / (1024 * 1024), 2) . ' MB';
	}
}

/**
 * Checks whether an email address has a valid format.
 * @param string $email Email address to validate.
 * @return bool True if the email address is valid, otherwise false.
 */
function is_valid_email ( $email )
{
	// Returns true if email address has a valid form.
	return preg_match ('#^[A-Za-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^_`\{\|\}\~]{1,64}@[a-z0-9\-\.]{2,65}$#', $email);
}

/**
 * Checks if the user is flooding the comments.
 * @return bool True is the user is flooding, otherwise false.
 */
function is_flooding()
{
	global $config;

	$user_ip = get_ip();
	$current_time = time();

	$file = file (FNEWS_ROOT_PATH . 'flood.php');
	$data = array_shift ($file);

	$flooding = false;
	foreach ( $file as $line )
	{
		$record = get_line_data ('flood', $line);

		if ( ($record['timestamp'] + $config['floodtime']) <= $current_time )
		{
			// Times up. Remove this old record.
			continue;
		}

		if ( $record['ip'] == $user_ip )
		{
			// We've added a comment too recently.
			$flooding = true;
		}

		$data .= $line;
	}

	safe_write ('flood.php', 'wb', $data);

	return $flooding;
}

/**
 * Check if the given IP is banned
 * @param string $ip IP address to check if banned
 * @return bool True is the given IP is banned, otherwise false.
 */
function is_ip_banned ( $ip )
{
	$file = file (FNEWS_ROOT_PATH . 'banned.php');
	array_shift($file);

	$my_subnet = explode ('.', $ip);
	foreach ( $file as $value )
	{
		$value = trim ($value);
		$octet = explode ('.', $value);
		if ( ($octet[0] == $my_subnet[0] || $octet[0] == '*') &&
			($octet[1] == $my_subnet[1] || $octet[1] == '*') &&
			($octet[2] == $my_subnet[2] || $octet[2] == '*') &&
			($octet[3] == $my_subnet[3] || $octet[3] == '*') )
		{
			return true;
		}
	}

	return false;
}

/**
 * @return string Returns the user's IP address
 */
function get_ip()
{
	$realip = '';

	if ( isset ($_SERVER) )
	{
		if ( isset ( $_SERVER['REMOTE_ADDR']) )
		{
			$realip = $_SERVER['REMOTE_ADDR'];
		}
		else if ( isset ($_SERVER['HTTP_CLIENT_IP']) )
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	else
	{
		if ( getenv ('REMOTE_ADDR') )
		{
			$realip = getenv ('REMOTE_ADDR');
		}
		else if ( getenv ('HTTP_CLIENT_IP') )
		{
			$realip = getenv ('HTTP_CLIENT_IP');
		}
		else
		{
			$realip = getenv ('HTTP_X_FORWARDED_FOR');
		}
	}

	return $realip;
}

/**
 * Checks if the user has sufficient access rights.
 * @param int $level Access level to check against
 * @return bool True if the user has sufficient access rights, otherwise false.
 */
function has_access ( $level )
{
	global $userdata;

	return ( $userdata != NULL ) ? ($userdata['level'] >= $level) : false;
}

/**
 * Cleans array key of characters other than A-Z, a-z, 0-9, underscore
 * @param string $key Key to be cleaned
 * @return string Cleaned key
 */
function clean_key ( $key )
{
	if ( $key != '' )
	{
		$key = preg_replace ('/[^A-Za-z0-9_]+/',	'',	$key);
		$key = preg_replace ('/\.\./',			'',	$key);
		$key = preg_replace ('/\_\_(.+?)\_\_/',		'',	$key);
		$key = preg_replace ('/^([\w\.\-\_]+)$/',	'$1',	$key);
	}

	return $key;
}

/**
 * Cleans array value of potentially dangerous characters
 * @param string $val Value to be cleaned
 * @return string Cleaned value
 */
function clean_value ( $val )
{
	if ( $val != '' )
	{
		// I think this block of slightly modified code came from IPB.
		/*$val = str_replace ('&#032;',		' ',		$val);
		$val = str_replace ('&',		'&amp;',	$val);
		$val = str_replace ('<!',		'&#60;&#33;',	$val);
		$val = str_replace ('-->',		'--&#62;',	$val);
		$val = preg_replace ('/<script/i',	'&#60;script',	$val);
		$val = str_replace ('>',		'&gt;',		$val);
		$val = str_replace ('<',		'&lt;',		$val);
		$val = preg_replace ('/\|/',		'&#124;',	$val);*/
		
        if ( get_magic_quotes_gpc() )
        {
            $val = stripslashes ($val);
        }
		$val = str_replace ("\r\n",		"\n",		$val);	// Win32 => new line
		$val = str_replace ("\r",		"\n",		$val);	// Mac => new line
		//$val = str_replace ('!',		'&#33;',	$val);
		
		$val = htmlspecialchars ($val);

		// Multibyte characters fix (could do it properly, but this is the easiest way and it works relatively well)
		$val = preg_replace ('/&amp;#([0-9]+);/', '&#$1;', $val);
		$val = preg_replace ('/&amp;#x([A-Z0-9]+);/i', '&#x$1', $val);
	}

	return $val;
}

/**
 * Saves configuration settings to file.
 * @param array $configs Config data array
 */
function save_config ( $configs )
{
	require './config.php';

	// Replace ' with \'
	$find = '\'';
	$replace = '\\\'';
	$variables = array ('site', 'furl', 'hurl', 'datefor', 'skin', 'fslink', 'stflink', 'pclink',
				'rss_title', 'rss_description', 'rss_encoding', 'news_pagination_nxt', 'news_pagination_prv');
	foreach ( $variables as $config_var )
	{
		$configs[$config_var] = str_replace ($find, $replace, $configs[$config_var]);
	}
    
    // Override HTML setting if WYSIWYG is enabled.
    if ( $configs['use_wysiwyg'] )
    {
        $configs['ht'] = 1;
    }

	// Restrict values between specified ranges
	$variables = array (
		'numofposts' => array ('min' => 1),
		'numofh' => array ('min' => 1),
		'fullnewsw' => array ('min' => 100),
		'fullnewsh' => array ('min' => 100),
		'comheight' => array ('min' => 100),
		'comwidth' => array ('min' => 100),
		'stfwidth' => array ('min' => 100),
		'stfheight' => array ('min' => 100),
		'floodtime' => array ('min' => 1),
		'comlength' => array ('min' => 0),
		'comments_per_page' => array ('min' => 0),
        'uploads_size' => array ('min' => 1000)
	);
	foreach ( $variables as $config_var => $range )
	{
		if ( isset ($range['min']) && $configs[$config_var] < $range['min'] )
		{
			$configs[$config_var] = isset ($config[$config_var]) ? $config[$config_var] : $range['min'];
		}

		if ( isset ($range['max']) && $configs[$config_var] > $range['max'] )
		{
			$configs[$config_var] = isset ($config[$config_var]) ? $config[$config_var] : $range['max'];
		}
	}

	$save  = "<?php\n\n";
	$save .= '// Auto generated by Fusion News v' . FNEWS_VERSION . "\n\n";
	$save .= "\$config = array();\n";
	$save .= "\$config['fusion_id'] = '".$configs['fusion_id']."';\n";
	$save .= "\$config['site'] = '".$configs['site']."';\n";
	$save .= "\$config['furl'] = '".$configs['furl']."';\n";
	$save .= "\$config['hurl'] = '".$configs['hurl']."';\n";
	$save .= "\$config['datefor'] = '".$configs['datefor']."';\n";
	$save .= "\$config['numofposts'] = ".$configs['numofposts'].";\n";
	$save .= "\$config['numofh'] = ".$configs['numofh'].";\n";
	$save .= "\$config['bb'] = ".$configs['bb'].";\n";
	$save .= "\$config['ht'] = ".$configs['ht'].";\n";
	$save .= "\$config['post_per_day'] = ".$configs['post_per_day'].";\n";
	$save .= "\$config['wfpost'] = ".$configs['wfpost'].";\n";
	$save .= "\$config['wfcom'] = ".$configs['wfcom'].";\n";
	$save .= "\$config['skin'] = '".$configs['skin']."';\n";
	$save .= "\$config['smilies'] = ".$configs['smilies'].";\n";
	$save .= "\$config['stfpop'] = ".$configs['stfpop'].";\n";
	$save .= "\$config['comallowbr'] = ".$configs['comallowbr'].";\n";
	$save .= "\$config['stfwidth'] = ".$configs['stfwidth'].";\n";
	$save .= "\$config['stfheight'] = ".$configs['stfheight'].";\n";
	$save .= "\$config['fslink'] = '".$configs['fslink']."';\n";
	$save .= "\$config['stflink'] = '".$configs['stflink']."';\n";
	$save .= "\$config['pclink'] = '".$configs['pclink']."';\n";
	$save .= "\$config['fsnw'] = ".$configs['fsnw'].";\n";
	$save .= "\$config['cbflood'] = ".$configs['cbflood'].";\n";
	$save .= "\$config['floodtime'] = ".$configs['floodtime'].";\n";
	$save .= "\$config['comlength'] = ".$configs['comlength'].";\n";
	$save .= "\$config['fullnewsw'] = ".$configs['fullnewsw'].";\n";
	$save .= "\$config['fullnewsh'] = ".$configs['fullnewsh'].";\n";
	$save .= "\$config['fullnewss'] = ".$configs['fullnewss'].";\n";
	$save .= "\$config['stfresize'] = ".$configs['stfresize'].";\n";
	$save .= "\$config['stfscrolls'] = ".$configs['stfscrolls'].";\n";
	$save .= "\$config['fullnewsz'] = ".$configs['fullnewsz'].";\n";
	$save .= "\$config['htc'] = ".$configs['htc'].";\n";
	$save .= "\$config['smilcom'] = ".$configs['smilcom'].";\n";
	$save .= "\$config['bbc'] = ".$configs['bbc'].";\n";
	$save .= "\$config['compop'] = ".$configs['compop'].";\n";
	$save .= "\$config['comscrolls'] = ".$configs['comscrolls'].";\n";
	$save .= "\$config['comresize'] = ".$configs['comresize'].";\n";
	$save .= "\$config['comheight'] = ".$configs['comheight'].";\n";
	$save .= "\$config['comwidth'] = ".$configs['comwidth'].";\n";
	$save .= "\$config['uploads_active'] = ".$configs['uploads_active'].";\n";
	$save .= "\$config['uploads_size'] = ".$configs['uploads_size'].";\n";
	$save .= "\$config['uploads_ext'] = '".$configs['uploads_ext']."';\n";
	$save .= "\$config['enable_rss'] = ".$configs['enable_rss'].";\n";
	$save .= "\$config['link_headline_fullstory'] = ".$configs['link_headline_fullstory'].";\n";
	$save .= "\$config['flip_news'] = ".$configs['flip_news'].";\n";
	$save .= "\$config['rss_title'] = '" . $configs['rss_title'] . "';\n";
	$save .= "\$config['rss_description'] = '" . $configs['rss_description'] . "';\n";
	$save .= "\$config['rss_encoding'] = '" . $configs['rss_encoding'] . "';\n";
	$save .= '$config[\'com_validation\'] = ' . $configs['com_validation'] . ';' . "\n";
	$save .= '$config[\'com_captcha\'] = ' . $configs['com_captcha'] . ';' . "\n";
	$save .= '$config[\'news_pagination\'] = ' . $configs['news_pagination'] . ';' . "\n";
	$save .= '$config[\'news_pagination_prv\'] = \'' . $configs['news_pagination_prv'] . '\';' . "\n";
	$save .= '$config[\'news_pagination_nxt\'] = \'' . $configs['news_pagination_nxt'] . '\';' . "\n";
	$save .= '$config[\'news_pagination_numbers\'] = ' . $configs['news_pagination_numbers'] . ';' . "\n";
	$save .= '$config[\'news_pagination_arrows\'] = ' . $configs['news_pagination_arrows'] . ';' . "\n";
	$save .= '$config[\'ppp_date\'] = \'' . $configs['ppp_date'] . '\';' . "\n";
	$save .= '$config[\'comments_pages\'] = ' . $configs['comments_pages'] . ';' . "\n";
	$save .= '$config[\'comments_per_page\'] = ' . $configs['comments_per_page'] . ';' . "\n";
	$save .= '$config[\'use_wysiwyg\'] = ' . $configs['use_wysiwyg'] . ';' . "\n";
	$save .= '$config[\'stf_captcha\'] = ' . $configs['stf_captcha'] . ';' . "\n";
	$save .= '$config[\'language\'] = \'' . $configs['language'] . '\';' . "\n\n";
	$save .= '?>';

	safe_write ('config.php', 'w', $save);
}

/**
 * Checks whether CAPTCHA code is correct for a given news article, and removes the code from database.
 * @param string $captcha_code 5 character CAPTCHA code
 * @param string $page_session_id User's session ID for the page
 * @param int $news_id News ID of the article
 * @param string $page Page the code applies to
 * @return bool True is the given code is correct, otherwise false.
 */
function is_valid_captcha_code ( $captcha_code, $page_session_id, $news_id, $page )
{
	global $lang;

	if ( empty ($page_session_id) || strlen ($captcha_code) != 5 )
	{
		return false;
	}

	$file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	$valid_code = false;
	$data = '<?php die (\'' . $lang['error1'] . '\'); ?>' . "\n";
	$current_time = time();
	$user_ip = get_ip();
	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $page_session_id == $session['session_id'] && $session['page'] == $page )
		{
			if ( $captcha_code == $session['code'] &&
				$news_id == $session['news_id'] &&
				$session['ip'] == $user_ip &&
				($session['last_visit'] + 600 >= $current_time) )
			{
				$valid_code = true;
			}
		}
		else
		{
			if ( ($session['last_visit'] + 600) >= $current_time )
			{
				$data .= $value;
			}
		}
	}

	safe_write ('sessions.php', 'wb', $data);

	return $valid_code;
}

/**
 * Generates HTML to display smiley and BBCode buttons if enabled.
 * @param string $form_name Name of the form to which the buttons should add text to.
 * @param string $textbox_name Name of the textbox which should have text added to.
 * @param bool $show_smilies Whether or not to display smiley buttons
 * @param bool $show_bbcode Whether or not to display BBCode buttons
 * @return string HTML for displaying smiley and BBCode buttons
 */
function show_extras ( $form_name, $textbox_name, $show_smilies, $show_bbcode )
{
	global $config;

	$extra_html = '';

	if ( $show_smilies )
	{
		$file = file (FNEWS_ROOT_PATH . 'smillies.php');
		array_shift ($file);
		foreach ( $file as $smiley )
		{
			$smiley = get_line_data ('smillies', $smiley);

			$text = addslashes ($smiley['bbcode']);
			$extra_html .= '<a href="javascript:smiley_bbcode(\'' . $textbox_name . '\', \'' . $text . '\');"><img src="' . $config['furl'] . '/smillies/' . $smiley['image'] . '" alt="' . $text . '" /></a>';
		}

		$extra_html .= '<br />' . "\n";
	}

	if ( $show_bbcode )
	{
		$extra_html .=
<<< html
<a href="javascript:smiley_bbcode('$textbox_name', '[b]', '[/b]');"><img src="{$config['furl']}/img/bold.gif" alt="Bold" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[i]', '[/i]');"><img src="{$config['furl']}/img/italic.gif" alt="Italic" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[u]', '[/u]');"><img src="{$config['furl']}/img/underline.gif" alt="Underline" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[s]', '[/s]');"><img src="{$config['furl']}/img/strike.gif" alt="Strikethrough" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[sub]', '[/sub]');"><img src="{$config['furl']}/img/sub.gif" alt="Subscript" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[sup]', '[/sup]');"><img src="{$config['furl']}/img/sup.gif" alt="Superscript" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[color=red]', '[/color]');"><img src="{$config['furl']}/img/color.gif" alt="Font color" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[font=verdana]', '[/font]');"><img src="{$config['furl']}/img/fontface.gif" alt="Font Family" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[size=16]', '[/size]');"><img src="{$config['furl']}/img/fontsize.gif" alt="Font Size" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=left]', '[/align]');"><img src="{$config['furl']}/img/fontleft.gif" alt="Left Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=center]', '[/align]');"><img src="{$config['furl']}/img/center.gif" alt="Center Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[align=right]', '[/align]');"><img src="{$config['furl']}/img/right.gif" alt="Right Align" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[tt]', '[/tt]');"><img src="{$config['furl']}/img/tele.gif" alt="Teletype" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[hr]');"><img src="{$config['furl']}/img/hr.gif" alt="Horizontal Line" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[move]', '[/move]');"><img src="{$config['furl']}/img/move.gif" alt="Marquee" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[quote]', '[/quote]');"><img src="{$config['furl']}/img/quote2.gif" alt="Quote" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[flash=200,200]', '[/flash]');"><img src="{$config['furl']}/img/flash.gif" alt="Flash Image" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[img]', '[/img]');"><img src="{$config['furl']}/img/img.gif" alt="Image" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[email=username@site.com]', '[/email]');"><img src="{$config['furl']}/img/email2.gif" alt="E-mail link" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[url=http://www.url.com]', '[/url]');"><img src="{$config['furl']}/img/url.gif" alt="hyperlink" /></a>
<a href="javascript:smiley_bbcode('$textbox_name', '[list]', '[/list]');"><img src="{$config['furl']}/img/list.gif" alt="List" /></a><br />
html;
	}

	return $extra_html;
}

/**
 * Generates HTML for a category dropdown menu
 * @param string $user_login Login in name of user to determine what categories to show.
 * @param int $selected_category Preselected category ID.
 * @return string HTML for category drop down menu
 */
function build_category_dropdown ( $user_login = null, $selected_category = 0, $remove_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    $dropdown = '<select class="post" id="category" name="category">' . "\n";
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        $cusers = explode (',', $category['users']);
        
        if ( $category['category_id'] != 1 && $user_login !== null && !in_array ($user_login, $cusers) )
        {
            continue;
        }
        
        $selected = '';
        if ( $selected_category == $category['category_id'] )
        {
            if ( $remove_selected )
            {
                continue;
            }
            
            $selected = ' selected="selected"';
        }
        
        $dropdown .= "<option value=\"{$category['category_id']}\"$selected>{$category['name']}</option>\n";
    }
    
    $dropdown .= "</select>\n";
    
    return $dropdown;
}

/**
 * Generates HTML for a category selection menu
 * @param string $user_login Login in name of user to determine what categories to show.
 * @param int|array $selected_categories Preselected category IDs. If only one category is selected, then a single integer can be given.
 * @param bool $invert_selected Whether or not to invert the selection.
 * @return string HTML for category selection menu.
 */
function build_category_selection ( $user_login = null, $selected_categories = array(1), $invert_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'categories.php');
    array_shift ($file);
    
    if ( !is_array ($selected_categories) )
    {
        $selected_categories = array ($selected_categories);
    }
    
    ob_start();
    $line_break = '';
    foreach ( $file as $category )
    {
        $category = get_line_data ('categories', $category);
        $cusers = explode (',', $category['users']);
        
        if ( $category['category_id'] != 1 && $user_login !== null && !in_array ($user_login, $cusers) )
        {
            continue;
        }
        
        $id = $category['category_id'];
        $name = $category['name'];
        
        $selected = '';
        if ( in_array ($id, $selected_categories) != $invert_selected )
        {
            $selected = ' checked="checked"';
        }
        
        echo $line_break . "<input type=\"checkbox\" name=\"category[$id]\" value=\"$id\" id=\"category_$id\"$selected /> <label for=\"category_$id\">$name</label>";
        $line_break = '<br />';
    }
    
    return ob_get_clean();
}

/**
 * Generates HTML for an author selection menu
 * @param int|array $selected_authors Preselected author usernames. If only one author is selected, then a single username can be given.
 * @param bool $invert_selected Whether or not to invert the selection.
 * @return string HTML for author selection menu.
 */
function build_author_selection ( $selected_authors = array(1), $invert_selected = false )
{
    $file = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($file);
    
    if ( !is_array ($selected_authors) )
    {
        $selected_authors = array ($selected_authors);
    }
    
    ob_start();
    $line_break = '';
    $i = 0;
    foreach ( $file as $author )
    {
        $author = get_line_data ('users', $author);
        
        $selected = '';
        
        $username = $author['username'];
        $nickname = $author['nickname'];
        
        if ( in_array ($username, $selected_authors) != $invert_selected )
        {
            $selected = ' checked="checked"';
        }
        
        echo $line_break . "<input type=\"checkbox\" name=\"author[$username]\" value=\"$username\" id=\"author_$i\"$selected /> <label for=\"author_$i\">$nickname</label>";
        $line_break = '<br />';
        
        ++$i;
    }
    
    return ob_get_clean();
}

/**
 * Checks if a given user is allowed to post in all given categories
 * @param string $user_login User's log in name to check
 * @param array $category_array Array of categories to check for access
 * @return mixed Returns the name of the first category which the user cannot post in. If there are
 * none, then NULL is returned
 */
function check_category_access ( $user_login, $category_array = array() )
{
	if ( sizeof ($category_array) > 0 )
	{
		$file = file (FNEWS_ROOT_PATH . 'categories.php');
		array_shift ($file);

		foreach ( $file as $category )
		{
			$category = get_line_data ('categories', $category);

			if ( $category['category_id'] == 1 )
			{
				continue;
			}

			if ( in_array ($category['category_id'], $category_array) )
			{
				$cusers = explode (',', $category['users']);
				if ( !in_array ($user_login, $cusers) )
				{
					return $category['name'];
				}
			}
		}
	}

	return NULL;
}

/**
 * Check if a given category ID exists
 * @param int $category_id Category ID to check that exists
 * @return bool Returns true if the given category ID exists, otherwise false.
 */
function category_exists ( $category_id = -1 )
{
	$category_id = (int)$category_id;

	$file = file (FNEWS_ROOT_PATH . 'categories.php');
	array_shift ($file);

	$found = false;
	foreach ( $file as $category )
	{
		$category = get_line_data ('categories', $category);
		if ( (int)$category['category_id'] === $category_id )
		{
			$found = true;
		}
	}

	return $found;
}

/**
 * Writes data to a specified file.
 * @param string $filename File name of file to write data to.
 * @param string $accessmode Mode of access.
 * @param string $data Data to be written to the file.
 */
function safe_write ( $filename, $accessmode, $data )
{
	global $lang;

	if ( !($fp = @fopen (FNEWS_ROOT_PATH . $filename, $accessmode)) )
	{
		trigger_error (sprintf ($lang['error2'], $filename), E_USER_WARNING);
	}
    
    @flock ($fp, LOCK_EX);
    fputs ($fp, $data, strlen ($data));
    @flock ($fp, LOCK_UN);
    fclose ($fp);
}

/**
 * Generates HTML for use in checkboxes.
 * @param int $value The value to match against
 * @param int $match The value which $value must match to generate the HTML
 * @return string Returns HTML to select a checkbox if $value equals $match
 */
function checkbox_checked ( $value, $match = 1 )
{
	return $value == $match ? ' checked="checked"' : '';
}

/**
 * Formats the given message.
 * @param string $message Message to format
 * @param bool $allowHTML Allow HTML in message
 * @param bool $allowBBCode Allow BBCode in message
 * @param bool $allowSmileys Allow Smileys in message
 * @param bool $wordFilter Use the word filter on this message
 * @param bool $clearHTML Clear HTML completely from the message
 * @return string Formatted message
 */
function format_message ( $message, $allowHTML, $allowBBCode, $allowSmileys, $wordFilter, $clearHTML = false )
{
	if ( $wordFilter )
	{
		$message = filter_badwords ($message);
	}

    if ( $clearHTML )
    {
        $message = html_entity_decode ($message);
        $message = strip_tags ($message);
    }
	else if ( $allowHTML )
	{
		$message = html_entity_decode ($message);
	}

	if ( $allowBBCode )
	{
		$message = replace_bbcode ($message);
	}

	if ( $allowSmileys )
	{
		$message = replace_smileys ($message);
	}

	return $message;
}

/**
 * Parses news text or news data so that it is displayed along with its template.
 * @param string|array $post_data Post data array.
 * @param array $settings Override settings when parsing.
 * @return Parsed news text
 */
function parse_news_to_view ( $post_data, $settings = array() )
{
	assert (is_array ($settings));
	
	global $config;

	$news_text = array();

    $icon = '';
    $email = '';
    $writer = '';
    $link_full_news = '';
    $link_comments = '';
    $link_tell_friend = '';

    $article = $post_data;

    // Get the template HTML
    $news_tmpl = get_template ((isset ($settings['template']) ? $settings['template'] : 'news_temp') . '.php', false);
    $other_qs = clean_query_string();
    
    $news_url = isset ($settings['news_url']) ? $settings['news_url'] : '';
    $sep = ( strpos ($news_url, '?') === false ) ? '?' : '&amp;';

    // Create the 'read more...' link
    if ( $article['fullnews'] != '' )
    {
        $fullnews_url = "{$config['furl']}/post.php?fn_id={$article['news_id']}";
        $fullnews_url_include = "{$news_url}{$sep}fn_mode=post&amp;fn_id={$article['news_id']}{$other_qs}";
        if ( $config['fsnw'] )
        {
            $link_full_news = '<a href="' . $fullnews_url . '" onclick="window.open(this.href,\'\',\'height=' . $config['fullnewsh'] . ',width=' . $config['fullnewsw'] . ',toolbar=no,menubar=no,scrollbars=' . $config['fullnewss'] . ',resizable=' . $config['fullnewsz'] . '\'); return false">' . $config['fslink'] . '</a>';
        }
        else
        {
            $link_full_news = '<a href="' . $fullnews_url_include . '">' . $config['fslink'] . '</a>';
        }
    }

    // Create the comments link
    if ( $config['compop'] )
    {
        $link_comments = '<a href="' . $config['furl'] . '/post.php?fn_id=' . $article['news_id'] . '" onclick="window.open(this.href,\'\',\'height=' . $config['comheight'] . ',width=' . $config['comwidth'] . ',toolbar=no,menubar=no,scrollbars=' . $config['comscrolls'] . ',resizable=' . $config['comresize'] . '\'); return false">' . $config['pclink'] . '</a>';
    }
    else
    {
        $link_comments = '<a href="' . $news_url . $sep . 'fn_mode=post&amp;fn_id=' . $article['news_id'] . $other_qs . '">' . $config['pclink'] . '</a>';
    }

    // Create the tell a friend link
    if ( $config['stfpop'] )
    {
        $link_tell_friend = '<a href="' . $config['furl'] . '/send.php?fn_id=' . $article['news_id'] . '" onclick="window.open(this.href,\'\',\'height=' . $config['stfheight'] . ',width=' . $config['stfwidth'] . ',toolbar=no,menubar=no,scrollbars=' . $config['stfscrolls'] . ',resizable=' . $config['stfresize'] . '\'); return false">' . $config['stflink']. '</a>';
    }
    else
    {
        $link_tell_friend = '<a href="' . $news_url . $sep . 'fn_mode=send&amp;fn_id=' . $article['news_id'] . $other_qs . '">' . $config['stflink']. '</a>';
    }

    // Make sure the number of comments is 0 or above.
    $num_comments = max ((int)$article['numcomments'], 0);

    // Get author information
    $author = get_author ($article['author']);
    if ( $author === false )
    {
        $author = array ('showemail' => false, 'nick' => $article['author']);
    }

    // Create the icon
    if ( strpos ($news_tmpl, '{icon}') !== false && !empty ($author['icon']) )
    {
        $icon = '<img src="' . $author['icon'] . '" alt="" />';
    }

    // Put the writer's name with his email as a link, or in some cases not.
    $email = ( $author['showemail'] ) ? $author['email'] : '';
    if ( !$email )
    {
        $writer = $author['nick'];
    }
    else
    {
        $writer = '<a href="mailto:' . $author['email'] . '">' . $author['nick'] . '</a>';
    }

    // Get our new lines back
    $article['headline'] = format_message ($article['headline'], $config['ht'], $config['bb'], $config['smilies'], $config['wfpost']);
    $article['shortnews'] = format_message ($article['shortnews'], $config['ht'], $config['bb'], $config['smilies'], $config['wfpost']);
    $article['fullnews'] = format_message ($article['fullnews'], $config['ht'], $config['bb'], $config['smilies'], $config['wfpost']);
    
    // Get our new lines back
    if ( !$ht && !$config['use_wysiwyg'] )
    {
        $article['shortnews'] = str_replace ('&br;', '<br />', $article['shortnews']);
        $article['fullnews'] = str_replace ('&br;', '<br />', $article['fullnews']);
    }
    else
    {
        // Need to be a bit smarter about new lines.
        $article['shortnews'] = str_replace ('&br;', "\n", $article['shortnews']);
        $article['fullnews'] = str_replace ('&br;', "\n", $article['fullnews']);
        
        $article['shortnews'] = preg_replace ("#([^>\s])(&br;\s*){2,}([^<])#m", '$1<br /><br />$3', $article['shortnews']);
        $article['fullnews'] = preg_replace ("#([^>\s])(&br;\s*){2,}([^<])#m", '$1<br /><br />$3', $article['fullnews']);
    }

    $categories = $article['categories'];
    $cat_icon = '';
    $cat_id = 0;
    $cat_name = '';
    
    $category_filter = isset ($settings['category']) ? $settings['category'] : array();
    $num_category_filter = sizeof ($category_filter);
    
    $cats = get_categories_all();
    foreach ( $cats as $category )
    {
        if ( ($num_category_filter > 0 && in_array ($category['category_id'], $category_filter)) ||
            ($num_category_filter == 0 && in_array ($category['category_id'], $categories)) )
        {
            $cat_icon = $category['icon'] != '' ? '<img src="' . $category['icon'] . '" alt="" />' : '';
            $cat_id = $category['category_id'];
            $cat_name = $category['name'];
            break;
        }
    }

    $news_text = array (
        'post_id'		=> $article['news_id'],
        'link_tell_friend'=> utf8_htmlentities ($link_tell_friend),
        'link_full_news'	=> utf8_htmlentities ($link_full_news),
        'subject'		=> utf8_htmlentities ($article['headline']),
        'description'	=> utf8_htmlentities ($article['description']),
        'writer'		=> utf8_htmlentities ($writer),
        'email'		=> utf8_htmlentities ($email),
        'date'		=> date ($config['datefor'], (int)$article['timestamp']),
        'icon'		=> $icon,
        'news'		=> utf8_htmlentities ($article['shortnews']),
        'fullnews'		=> utf8_htmlentities ($article['fullnews']),
        'cat_icon'		=> $cat_icon,
        'cat_id'		=> $cat_id,
        'cat_name'		=> utf8_htmlentities ($cat_name),
    );

    if ( strpos ($news_tmpl, '{comments}') !== false )
    {
        $news_text += array (
            'nrc'			=> $num_comments,
            'link_comments'	=> utf8_htmlentities ($link_comments),
        );
    }
    else
    {
        $news_text += array ('nrc' => '', 'link_comments' => '');
    }

    // Replace in the values!
    $news_tmpl = replace_masks ($news_tmpl, array (
        'post_id'		=> $news_text['post_id'],
        'user'		=> $news_text['writer'],
        'date'		=> $news_text['date'],
        'icon'		=> $news_text['icon'],
        'send'		=> $news_text['link_tell_friend'],
        'nrc'			=> $news_text['nrc'],
        'cat_id'		=> $news_text['cat_id'],
        'cat_name'		=> $news_text['cat_name'],
        'cat_icon'		=> $news_text['cat_icon'],
        'fullstory'		=> $news_text['link_full_news'],
        'comments'		=> $news_text['link_comments'],
        'subject'		=> '<a id="fus_' . $news_text['post_id'] . '"></a>' . $news_text['subject'],
        'news'		=> $news_text['news'],
        'description'	=> $news_text['description']
    ));

    $news_text['display'] = $news_tmpl;

	return $news_text;
}

/**
 * Checks and returns the number of comments awaiting to be validated.
 * @return int Number of comments awaiting to be validated.
 */
function get_pending_comments()
{
	global $config;
	if ( !$config['com_validation'] )
	{
		return 0;
	}

	$count = 0;

    $comments_by_id = get_unmoderated_comments_all();
    foreach ( $comments_by_id as $comments )
    {
        $count += sizeof ($comments);
    }

	return $count;
}

// PHP 4.3.2 compatability
if ( !function_exists ('session_regenerate_id') )
{
	/**
	 * Regenerates the internal (PHP) session id
	 * @return bool True is successful, otherwise false.
	 */
	function session_regenerate_id()
	{
		$str = '';
		mt_srand ((double)microtime() * 100000);
		for ( $i = 0; $i < 32; $i++ )
		{
			$x = mt_rand (1, 3);
			$str .= ($x == 1) ? chr (mt_rand (48, 57)) : (( $x == 2 ) ? chr (mt_rand (65, 90)) : chr (mt_rand (97, 122)));
		}

		if ( session_id ($str) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

/**
 * Login session functions
 */

/**
 * Creates a pseudo-random string of alphanumeric characters of a specified length.
 * This function is shared by the installer too to create the login identifier
 * @param int $length Length of the pseudo-random string
 * @return string Pseudo-random string
 */
function create_security_id ( $length = 32 )
{
	$str = '';
	mt_srand ((double)microtime() * 100000);
	for ( $i = 0; $i < $length; $i++ )
	{
		$x = mt_rand (1, 3);
		$str .= ($x == 1) ? chr (mt_rand (48, 57)) : (( $x == 2 ) ? chr (mt_rand (65, 90)) : chr (mt_rand (97, 122)));
	}

	return $str;
}

/**
 * Creates a login session for the given user log in name.
 * @param string $uid Log in name of the user logging in
 * @param int $autologin Whether or not the user wishes to auto log in next time he/she visits the page
 * @return array User data array for the newly logged in user.
 */
function login_session_create ( $uid, $autologin = 0 )
{
	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);

	$user_ip = get_ip();
	$current_time = time();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( $login['user_id'] == $uid && $login['autologin'] != 1 )
		{
			// Purge old existing security id
			continue;
		}

		if ( $current_time <= ($login['login_time'] + 1800) )
		{
			$write .= $login_data;
		}
	}

	$security_id = create_security_id();

	$write .= $security_id . '|<|' . $uid . '|<|' . $user_ip . '|<|' . $autologin . '|<|' . $current_time . '|<|' . "\n";

	safe_write ('logins.php', 'wb', $write);

	setcookie ('fus_uid', $uid, $current_time + (365 * 86400));
	setcookie ('fus_sid', $security_id, $current_time + (365 * 86400));
	
    $userfile = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($userfile);

    $userdata = array();
    foreach ( $userfile as $userinfo )
    {
        $user = get_line_data ('users', $userinfo);

        if ( $user['username'] == $uid )
        {
            $email = explode ('=', $user['email']);
            $userdata = array (
                'user'  => $user['username'],
                'nick'  => $user['nickname'],
                'showemail' => $email[0],
                'email' => $email[1],
                'icon'  => $user['icon'],
                'offset'    => (int)$user['timeoffset'],
                'password'  => $user['passwordhash'],
                'level' => (int)$user['level']
            );
            
            break;
        }
    }
    
    return $userdata;
}

/**
 * Updates the user's log in session time, and removes old log in sessions from the data file
 * @param string $uid User log in name of the user logged in.
 * @param string $sid Session log in ID for the user
 * @return array If an invalid $uid or $sid is given, a blank array, otherwise the data for the
 * logged in user.
 */
function login_session_update ( $uid, $sid )
{
	if ( !$uid || !$sid )
	{
		return array();
	}

	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);
	
	$userfile = file (FNEWS_ROOT_PATH . 'users.php');
    array_shift ($userfile);

	$valid_sid = false;
	$userdata = NULL;
	$current_time = time();
	$user_ip = get_ip();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( ($current_time >= ($login['login_time'] + 1800)) && $login['autologin'] != 1 )
		{
			continue;
		}

		if ( $login['security_id'] != $sid )
		{
			$write .= $login_data;
			continue;
		}

		if ( ($user_ip != $login['ip']) || ($uid != $login['user_id']) )
		{
			continue;
		}

		foreach ( $userfile as $userinfo )
		{
			$user = get_line_data ('users', $userinfo);

			if ( $user['username'] == $login['user_id'] )
			{
				$valid_sid = true;

				$email = explode ('=', $user['email']);
				$userdata = array (
					'user'	=> $user['username'],
					'nick'	=> $user['nickname'],
					'showemail'	=> $email[0],
					'email'	=> $email[1],
					'icon'	=> $user['icon'],
					'offset'	=> (int)$user['timeoffset'],
					'password'	=> $user['passwordhash'],
                    'salt'  => $user['passwordsalt'],
					'level'	=> (int)$user['level']
				);

				$write .= $login['security_id'] . '|<|' . $login['user_id'] . '|<|' . $login['ip'] . '|<|' . $login['autologin'] . '|<|' . $current_time . '|<|' . "\n";
				break;
			}
		}
	}

	safe_write ('logins.php', 'wb', $write);

	if ( !$valid_sid || $userdata === NULL )
	{
		setcookie ('fus_sid', '', $current_time - 86400);
		setcookie ('fus_uid', '', $current_time - 86400);
	}

	return $userdata;
}

/**
 * Destroy the log in session for the given log in session ID and removes
 * old log in data from the data file
 * @param string $sid Log in session ID to destroy
 */
function login_session_destroy ( $sid )
{

	$file = file (FNEWS_ROOT_PATH . 'logins.php');
	$write = array_shift ($file);

	$valid_sid = false;
	$current_time = time();
	foreach ( $file as $login_data )
	{
		$login = get_line_data ('logins', $login_data);

		if ( ($current_time >= ($login['login_time'] + 1800)) && $login['autologin'] != 1 )
		{
			continue;
		}

		if ( $login['security_id'] == $sid )
		{
			continue;
		}

		$write .= $login_data;
	}

	safe_write ('logins.php', 'wb', $write);
}

/**
 * Retrieves the file extension from given file.
 * @param string $filename Filename to get extension from
 * @return string Returns the file's extension
 */
function get_file_extension ( $filename )
{
    return strtolower (utf8_substr ($filename, strrpos ($filename, '.') + 1));
}

/**
 * Uploads an image to the directory in $directory.
 * @param int $file_number The ID number of the file in the $_FILES['F']['xxx'] array. If -1, then only one file was uploaded.
 * @param string $allowedExts The allowed file extensions to be uploaded, separated by HTML encoded |.
 * @param string $directory The directory to upload the file to.
 * @return string Message showing whether the upload was successful or not.
 * @todo Make this function more generalized. I don't like the beginning if() statement
 * which could be solved by passing a custom array instead of specifying a file number.
 * Also the returned messages need to be turned into return codes.
 */
function upload_file ( $file_number, $allowedExts, $directory = './uploads/' )
{
	global $lang;

	if ( $file_number == -1 )
	{
		// We only have one file uploaded...
		if ( $_FILES['F']['error'] != UPLOAD_ERR_OK )
		{
			// Problem with the upload. Let's take a closer look
			switch ( $_FILES['F']['error'] )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					return sprintf ($lang['ind252'], $_FILES['F']['name']);
				break;

				case UPLOAD_ERR_PARTIAL:
				break;

				case UPLOAD_ERR_NO_FILE:
					return $lang['ind254'];
				break;

				default:
					return 'Uh-oh, this isn\'t supposed to happen. Error code: ' . $_FILES['F']['error'];
				break;
			}
		}

		$filename = ( isset ($_FILES['F']['name']) ) ? $_FILES['F']['name'] : '';
		$tmpname = ( isset ($_FILES['F']['tmp_name']) ) ? $_FILES['F']['tmp_name'] : '';
	}
	else
	{
		if ( $_FILES['F']['error'][$file_number] != UPLOAD_ERR_OK )
		{
			// Problem with the upload. Let's take a closer look
			switch ( $_FILES['F']['error'][$file_number] )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					return sprintf ($lang['ind252'], $_FILES['F']['name'][$file_number]);
				break;

				case UPLOAD_ERR_PARTIAL:
				break;

				case UPLOAD_ERR_NO_FILE:
					return $lang['ind254'];
				break;

				default:
					return 'Uh-oh, this isn\'t supposed to happen.';
				break;
			}
		}

		$filename = ( isset ($_FILES['F']['name'][$file_number]) ) ? $_FILES['F']['name'][$file_number] : '';
		$tmpname = ( isset ($_FILES['F']['tmp_name'][$file_number]) ) ? $_FILES['F']['tmp_name'][$file_number] : '';
	}

	$extensions = explode ('|', $allowedExts);
	$num_exts = sizeof ($extensions);
	$valid_ext = false;

	foreach ( $extensions as $ext )
	{
		if ( get_file_extension ($filename) == strtolower ($ext) )
		{
			$valid_ext = true;
			break;
		}
	}

	if ( !$valid_ext )
	{
		return $lang['ind255'] . $allowedExts . $lang['ind255a'];
	}

	$uploaded = false;

	$origname = $filename;
	$i = 1;

	while ( file_exists ($directory . $filename) )
	{
		$filename = utf8_substr ($origname, 0, strrpos ($origname, '.')) . '_' . $i . strrchr ($origname, '.');
		$i++;
	}

	if ( @move_uploaded_file ($tmpname, $directory . $filename) )
	{
		$uploaded = true;
	}
	else if ( @copy ($tmpname, $directory . $filename) )
	{
		$uploaded = true;
	}

	if ( !$uploaded )
	{
		return $lang['ind256'];
	}

	chmod ($directory . $filename, 0644);
	if ( file_exists ($tmpname) )
	{
		unlink ($tmpname);
	}

	return $filename . ' ' . $lang['ind257'];
}

/**
 * Generates HTML for a redirect page
 * @param string $message Message to display before the user is redirected
 * @param string $return_url URL to return to
 * @param string $return_text Text to display to return to the given URL
 * @return string HTML to display the redirect page
 */
function make_redirect ( $message, $return_url = '', $return_text = '' )
{
	global $lang;

	$text = '<p>' . $message . '</p>';
	if ( $return_url && $return_text )
	{
		$text .= '<p><a href="' . $return_url . '">' . $return_text . '</a></p>';
	}
	$text .= '<p><a href="./">' . $lang['ind76'] . '</a></p>';

	return $text;
}

/**
 * Failsafe method of retrieving $_SERVER['QUERY_STRING']
 * @return string Query string
 */
function get_query_string()
{
	return (isset ($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
}

/**
 * Gets query string, removing any Fusion News added variables.
 * @return string Clean string query (without any Fusion News added variables)
 */
function clean_query_string()
{
	$query_string = get_query_string();
	$query_string = preg_replace ('/fn_[a-z_]+=[^&]*&?/', '', $query_string);
	$query_string = str_replace ('&', '&amp;', $query_string);

	// Clear possible leading ampersand
	if ( utf8_substr ($query_string, -5) == '&amp;' )
	{
		$query_string = utf8_substr ($query_string, 0, -5);
	}

	if ( empty ($query_string) )
	{
		return '';
	}
	else
	{
		return '&amp;' . $query_string;
	}
}

/**
 * Generates HTML for the form security
 * @return string HTML for the form security
 */
function get_form_security()
{
	$timestamp = time();

	return '<input type="hidden" name="post_form" value="' . get_form_character ($timestamp) . '" />' . "\n" .
			'<input type="hidden" name="post_time" value="' . $timestamp . '" />' . "\n";
}

/**
 * Gets the form's security character
 * @param int $timestamp Timestamp for the form
 * @return string Form's security character
 */
function get_form_character ( $timestamp )
{
	global $config;

	$total = 0;
	$time = (string)$timestamp;
	for ( $i = 0, $end = strlen ($time); $i < $end; $i++ )
	{
		$total += $i * $time{$i};
	}

	return $config['fusion_id']{0x1F ^ ($total % 32)};
}

/**
 * Checks whether the given form character is valid
 * @return bool True if the given form character is valid, otherwise false.
 */
function check_form_character()
{
	global $PVARS;

	if ( !isset ($PVARS['post_time']) || !isset ($PVARS['post_form']) )
	{
		return false;
	}

	if ( $PVARS['post_time'] == '' || $PVARS['post_form'] == '' )
	{
		return false;
	}

	if ( ($PVARS['post_time'] + 3600) <= time() )
	{
		// Allow a maximum of one hour to submit a form.
		return false;
	}

	return (get_form_character ($PVARS['post_time']) == $PVARS['post_form']);
}

/**
 * Creates a list of page numbers, reducing the list if
 * the page list is too long to show.
 * @param int $num_pages Number of pages
 * @param int $current_page The current page number
 * @param string $url URL of the page to go to with the selected page number
 * @param string $page_variable Variable to use in the query string for the page number
 * @return string The list of page numbers.
 */
function create_page_numbers ( $num_pages, $current_page, $url, $page_variable )
{
	static $page_reduction_limit = 15;
	$pagination_text = '';

	// Replace intitial ampersand just in case we have a query string created by clean_query_string().
	$url = str_replace ('?&amp;', '?', $url);

	$i = 1;

	$should_reduce = ($num_pages >= $page_reduction_limit);
	$prev_page_no = $current_page - 1;
	$next_page_no = $current_page + 1;
	$two_from_last = $num_pages - 2;
	while ( $i <= $num_pages )
	{
		if ( !$should_reduce ||
			$i <= 3 ||
			($i >= $prev_page_no && $i <= $next_page_no) ||
			$i >= $two_from_last )
		{
			if ( $current_page == $i )
			{
				$pagination_text .= ' <b class="fn-news-pagination-current">' . $i . '</b>';
			}
			else
			{
				$pagination_text .= ' <a href="' . $url . '&amp;' . $page_variable . '=' . $i . '" class="fn-news-pagination-number">' . $i . '</a>';
			}

			++$i;
		}
		else
		{
			$pagination_text .= ' &#133;';

			if ( $i < $prev_page_no && $prev_page_no < $two_from_last )
			{
				$i = $prev_page_no;
			}
			else if ( $i < $two_from_last )
			{
				$i = $two_from_last;
			}
		}
	}

	// substr to remove leading space.
	return utf8_substr ($pagination_text, 1);
}

/**
 * Parses field masks in given text
 * @param string $template Text containing field masks to replace
 * @param array $masks Array of masks (key contains field mask, value contains text to replace the mask with)
 * @return string Text with field masks parsed.
 */
function replace_masks ( $template, $masks )
{
	foreach ( $masks as $key => $value )
	{
		$template = str_replace ('{' . $key . '}', $value, $template);
	}

	return $template;
}

if ( !function_exists ('array_combine') )
{
	/**
	 * This function exists in PHP5 by default.
	 * Creates an array by using one array for keys and another for its values
	 * @param array $keys Array of keys to be used.
	 * @param array $values Array of values to be used
	 * @return array|bool Returns the combined array, FALSE if the number of elements for each array isn't equal or if the arrays are empty.
	 */
	function array_combine ( $keys, $values )
	{
		$num_keys = sizeof ($keys);
		$num_values = sizeof ($values);

		if ( $num_keys != $num_values )
		{
			trigger_error ('array_combine(): Both parameters should have an equal number of elements.', E_USER_WARNING);
			return false;
		}

		$return = array();
		for ( $i = 0; $i < $num_keys; $i++ )
		{
			$return[$keys[$i]] = $values[$i];
		}

		return $return;
	}
}

/**
 * Gets the current URL, including the query string. Made to work with Apache and IIS.
 */
function current_url()
{
    $url = 'http';
    if ( isset ($_SERVER['HTTPS']) )
    {
        $url .= 's';
    }
    
    if ( isset ($_SERVER['REQUEST_URI']) )
    {
        $request_uri = $_SERVER['REQUEST_URI'];
    }
    else
    {
        $request_uri = $_SERVER['SCRIPT_NAME'];
        if ( isset ($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '' )
        {
            $request_uri .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    $url .= '://' . $_SERVER['SERVER_NAME'] . $request_uri;
    
    return $url;
}

/**
 */
function generate_captcha_code ( $session_id, $news_id, $user_ip, $type )
{
    $captcha_code = '';
	for ( $i = 0; $i < 5; $i++ )
	{
		$x = mt_rand (1, 10);
		$captcha_code .= ( $x % 2 ) ? chr (mt_rand (48, 57)) : chr (mt_rand (65, 90));
	}
    
    save_captcha_code ($session_id, $captcha_code, $news_id, $user_ip, $type);
    
    return $captcha_code;
}

function get_captcha_code ( $session_id )
{
    $file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $session['session_id'] == $session_id )
		{
			return $session['code'];
		}
	}

	return null;
}

function save_captcha_code ( $session_id, $captcha_code, $news_id, $user_ip, $type )
{
    global $lang;

    $file = file (FNEWS_ROOT_PATH . 'sessions.php');
	array_shift ($file);

	$current_time = time();
	$data = '<?php die (\'' . $lang['error1'] . '\'); ?>' . "\n";
	foreach ( $file as $value )
	{
		$session = get_line_data ('sessions', $value);

		if ( $session['session_id'] == $session_id )
		{
			continue;
		}

		if ( (($session['last_visit'] + 600) >= $current_time) && ($session['ip'] != $user_ip) )
		{
			$data .= $value;
		}
	}

	$data .= create_line_data ($session_id, $captcha_code, $news_id, $user_ip, $current_time, $type);
	safe_write ('sessions.php', 'wb', $data);
}

/**
 * @param string $file File to get fields for.
 * @return array List of fields for file type.
 */
function get_fields_for_file ( $file )
{
    static $valid_files = array (
        'badwords'      => array ('find', 'replace', 'case_sensitive', 'type'),
        'categories'    => array ('category_id', 'name', 'icon', 'users'),
        'comments'      => array ('ip', 'validated', 'message', 'author', 'email', 'timestamp', 'comment_id'),
        'flood'         => array ('ip', 'timestamp'),
        'logins'        => array ('security_id', 'user_id', 'ip', 'autologin', 'login_time'),
        'news_toc'      => array ('news_id', 'timestamp', 'author', 'headline', 'categories'),
        'news'          => array ('shortnews', 'fullnews', 'author', 'headline', 'description', 'categories', 'timestamp', 'numcomments', 'news_id'),
        'sessions'      => array ('session_id', 'code', 'news_id', 'ip', 'last_visit', 'page'),
        'smillies'      => array ('smiley_id', 'bbcode', 'image'),
        'users'         => array ('username', 'nickname', 'email', 'icon', 'timeoffset', 'passwordhash', 'passwordsalt', 'level')
    );

    if ( !isset ($valid_files[$file]) )
    {
        trigger_error ('get_fields_for_file(): Invalid data file name given', E_USER_WARNING);
	}
    
    return $valid_files[$file];
}

/**
 * Gets an array of fields and their data from a specified line of data from a file.
 * @param string $file Data file the line of data was retrieved from
 * @param string $line_data Line of data to convert to an array
 * @param string $delimiter The delimiter between each field data
 * @return array|bool Array combining the field names for the specified file and the given data,
 * FALSE if no data was found or the line data was blank.
 */
function get_line_data ( $file, $line_data, $delimiter = '|<|' )
{
	if ( $line_data == '' )
	{
		trigger_error ('get_line_data(): No line data for ' . $file, E_USER_WARNING);
	}

	$data = explode ($delimiter, $line_data);
	if ( sizeof ($data) < 0 )
	{
		trigger_error ('get_line_data(): No data found in the given data.', E_USER_WARNING);
		return NULL;
	}

    $fields = get_fields_for_file ($file);
    
	array_pop ($data); // Pop new line off the end
	$return_data = array_combine ($fields, $data);

	return $return_data;
}

/**
 * Prepares a message for email
 * @param string $string String to prepare
 * @return string Prepared message for email.
 */
function prepare_string_for_mail ( $string )
{
    $message = wordwrap ($string, 70);
    
    return $message;
}

/**
 * Displays the output of the control panel.
 * @param string $title Page title
 * @param string $skin Skin to use
 * @param array $userdata User's login session data
 */
function display_output ( $title, $skin, $userdata )
{
	global $start_time,
		$lang, $notice_buffer, $warning_buffer,
        $errored_out;

	// Now sort out the skin
	$links_list = array (
		array ($lang['ind163'], '', GUEST), // home
		array ($lang['ind108'], '?id=postnews', NEWS_REPORTER), // new post
		array ($lang['ind109'], '?id=editposts', NEWS_REPORTER), // edit posts
		array ($lang['ind400'], '?id=editprofile', NEWS_REPORTER), // edit profile
		array (array (
            NEWS_EDITOR => $lang['ind344'],
            NEWS_ADMIN => $lang['ind169']),
        '?id=admin', NEWS_EDITOR), // editor's panel/admin
		array ($lang['ind272'], '?id=view', GUEST), // view news
		array ($lang['ind165'], '?id=help', NEWS_REPORTER), // help/update
		array ($lang['ind107'], '?id=logout', NEWS_REPORTER) // logout
	);
	
	$cs1 = file_get_contents (FNEWS_ROOT_PATH . 'skins/' . $skin . '/index.html');
	if ( $cs1 === false )
	{
		trigger_error ($lang['ind281'], E_USER_ERROR);
	}

	$row_links = '';
	$col_links = '';
    
    $row_sep = '';
    $col_sep = '';
    
	$user_level = ( !isset ($userdata['level']) ) ? 0 : $userdata['level'];
    
	foreach ( $links_list as $link_data )
	{
		if ( !is_array ($link_data) )
		{
			continue;
		}

		if ( $user_level >= $link_data[2] )
		{
			$row_links .= $row_sep;
			$col_links .= $col_sep;

			$row_links .= '<a href="index.php' . $link_data[1] . '">';
			$col_links .= '<a href="index.php' . $link_data[1] . '">';
			if ( is_array ($link_data[0]) )
			{
				$row_links .= $link_data[0][$userdata['level']];
				$col_links .= $link_data[0][$userdata['level']];
			}
			else
			{
				$row_links .= $link_data[0];
				$col_links .= $link_data[0];
			}
			$row_links .= '</a>';
			$col_links .= '</a>';
            
            $row_sep = ' | ';
            $col_sep = '<br />';
		}
	}
    
	if ( has_access (NEWS_REPORTER) )
	{
		switch ( $userdata['level'] )
		{
			case 3:
				$status = $lang['ind195'];
				break;

			case 2:
				$status = $lang['ind194'];
				break;

			case 1:
				$status = $lang['ind193'];
				break;

			default:
				break;
		}

		$login = $lang['ind170'] . ' <b>' . $userdata['nick'] . '</b> [<b>' . $status . '</b>]';
	}
	else
	{
		$login = $lang['ind171'];
	}
    
    $content = ob_get_clean();

	if ( !$errored_out )
	{
		if ( $notice_buffer != '' )
		{
			$content = '<ul id="fn_notice"><li class="title">' . $lang['ind357'] . '</li>' . $notice_buffer . '</ul>' . $content;
		}

		if ( $warning_buffer != '' )
		{
			$content = '<ul id="fn_warning"><li class="title">' . $lang['ind358'] . '</li>' . $warning_buffer . '</ul>' . $content;
		}
	}

	$cs = replace_masks ($cs1, array (
		'main'  => $content,
		'title' => $title,
		'linksn'=> $row_links,
		'linksb'=> $col_links,
		'login' => $login,
		'curve' => FNEWS_VERSION
	));

	//-----------------------------------------------
	// Do this very last to get the most accurate
	// result possible
	//-----------------------------------------------
	$split = explode (' ', microtime());
	$end_time = (float)$split[0] + (float)$split[1];
	$cs = str_replace ('{loadtime}', sprintf ('%.6f', $end_time - $start_time), $cs);

	//-----------------------------------------------
	// Blammo, out comes the end product :p
	//-----------------------------------------------
	echo $cs;
}

/**
 * Error handling
 */
$warning_buffer = '';
$notice_buffer = '';
/**
 * When TRUE the page should stop wherever it is in processing, and just display the error messages
 * @global bool $errored_out
 */
$errored_out = false;

/**
 * Callback function for PHP error handling (not to be used directly - it will be called by PHP when needed)
 * @param int $errno Error number
 * @param string $errstr Error text
 * @param string $errfile File containing the error
 * @param int $errline Line number containing the error
 * @return bool False if the default PHP internal error handler should handle the error
 */
function fn_error_handler ( $errno, $errstr, $errfile, $errline )
{
	if ( !(error_reporting() & $errno) )
	{
		return true;
	}

	$errfile = str_replace (FNEWS_ROOT_PATH, '', str_replace ('\\', '/', $errfile));

	switch ( $errno )
	{
		// Very bad error indeed...
		// Show a complete failure page.
		case E_USER_ERROR:
		case E_ERROR:
            ob_end_clean();
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
				'<html xmlns="http://www.w3.org/1999/xhtml">',
				'<head>',
                '	<meta http-equiv="content-type" content="text/html; charset=utf-8" />',
				'	<title>Fusion News &bull; Error</title>',
				'	<style type="text/css">',
				'	<!--',
				'	body { background-color:#fff; color:#000; line-height:140%; font-size:100%; font-family:"Trebuchet MS", Helvetica, sans-serif }',
				'	blockquote { padding:0; margin: 0 30px }',
				'	#wrapper { width:95%; margin:0 auto }',
				'	//-->',
				'	</style>',
				'</head>',
				'<body>',
				'<h1>Fusion News has encountered a fatal error</h1>',
				'<div id="wrapper">',
				'<p>A fatal error was encountered by Fusion News, and cannot continue to run.</p>',
				'<p>The error message is as follows:</p>',
				'<blockquote><i>', $errstr, '</i></blockquote>',
				'<p>If there are any instructions in the error message above, please follow them to try to solve the problem. If the error repeats itself, after refreshing the page after 30 seconds, please create a new topic at the <a href="http://www.fusionnews.net/">Fusion News Support Forum</a>, and copy and paste the text below:</p>',
				'<blockquote><code>Error: ', $errstr, '<br />',
                'File: ', $errfile, '<br />',
                'Line No.: ', $errline, '<br />',
                'Version: ', FNEWS_VERSION,
                '</code></blockquote>',
				'</div>',
				'</body>',
				'</html>';

			exit;

			return true;
		break;

		case E_USER_NOTICE:
		case E_USER_WARNING:
            global $title, $lang, $config, $userdata;
			if ( !$title )
			{
				$title = $lang['ind17'];
			}

			echo '<div id="fn_warning">',
					'<li class="title">' . $lang['ind17'] . '</li>',
					'<li>' . $errstr . '</li>',
					'</div>', "\n";

			$errored_out = true;

			display_output ($title, $config['skin'], $userdata);
			exit;

			return true;
		break;

		case E_NOTICE:
            global $notice_buffer;
            
			$notice_buffer .= '<li> ' . $errstr . ' in /' . $errfile . ' on line ' . $errline . '</li>' . "\n";

			return true;
		break;

		case E_WARNING:
            global $warning_buffer;
            
			$warning_buffer .= '<li>' . $errstr . ' in /' . $errfile . ' on line ' . $errline . '</li>' . "\n";

			return true;
		break;

		default:
		break;
	}

	return true;
}

?>
