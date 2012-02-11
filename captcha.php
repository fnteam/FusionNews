<?php

/**
 * CAPTCHA image
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id$
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

if ( defined ('FNEWS_ROOT_PATH') )
{
    exit;
}

/**@ignore*/
define ('FNEWS_ROOT_PATH', str_replace ('\\', '/', dirname (__FILE__)) . '/');
include_once FNEWS_ROOT_PATH . 'common.php';

function hsl2rgb ( $h, $s, $l )
{
    $s /= 255.0;
    $l /= 255.0;
    
    $c = (1.0 - abs (2.0 * $l - 1.0)) * $s;
    $hprime = $h / 60.0;
    $x = $c * (1.0 - abs (fmod ($hprime, 2.0) - 1.0));
    
    $r = 0.0;
    $g = 0.0;
    $b = 0.0;
    if ( $hprime > 0.0 )
    {
        if ( $hprime < 1.0 )
        {
            $r = $c;
            $g = $x;
        }
        else if ( $hprime < 2.0 )
        {
            $r = $x;
            $g = $c;
        }
        else if ( $hprime < 3.0 )
        {
            $g = $c;
            $b = $x;
        }
        else if ( $hprime < 4.0 )
        {
            $g = $x;
            $b = $c;
        }
        else if ( $hprime < 5.0 )
        {
            $r = $x;
            $b = $c;
        }
        else if ( $hprime < 6.0 )
        {
            $r = $c;
            $b = $x;
        }
    }
    
    $m = $l - 0.5 * $c;
    $r += $m;
    $g += $m;
    $b += $m;
    
    return array ('r' => 255 * $r, 'g' => 255 * $g, 'b' => 255 * $b);
}

define ('CAPTCHA_WIDTH', 250);
define ('CAPTCHA_HEIGHT', 100);

$fus_sid = ( isset ($PVARS['fn_sid']) ) ? $PVARS['fn_sid'] : '';
$news_id = ( isset ($PVARS['fn_id']) ) ? (int)$PVARS['fn_id'] : 0;
$type = ( isset ($PVARS['fn_type']) ) ? $PVARS['fn_type'] : '';

if ( $type != 'comments' && $type != 'send' )
{
    exit;
}

if ( $news_id == 0 )
{
    exit;
}

$image = imagecreatetruecolor (CAPTCHA_WIDTH, CAPTCHA_HEIGHT);

$color = array (
    'black' => imagecolorallocate ($image, 0x00, 0x00, 0x00),
    'grey' => imagecolorallocate ($image, 0x4B, 0x4B, 0x4B),
    'white' => imagecolorallocate ($image, 0xFF, 0xFF, 0xFF),
    'transparent' => imagecolorallocatealpha ($image, 0, 0, 0, 0)
);

imagefill ($image, 0, 0, $color['white']);

// Fancy background pattern
for ( $i = 0; $i < 20; $i++ )
{
    $random_x = mt_rand (-20, CAPTCHA_WIDTH + 20);
    $random_y = mt_rand (-20, CAPTCHA_HEIGHT + 20);
    $random_w = mt_rand (10, CAPTCHA_WIDTH);
    $random_h = mt_rand (10, CAPTCHA_HEIGHT);
    $color = hsl2rgb (mt_rand (0, 359), mt_rand (0, 127), mt_rand (0, 100));
    $random_col = imagecolorallocate ($image, $color['r'], $color['g'], $color['b']);

    $func = 'imagefilledrectangle';
    if ( $i % 2 ) $func = 'imagefilledellipse';
    $func ($image, $random_x, $random_y, $random_w, $random_h, $random_col);
}

// Draw text as normal
$confirm_code = get_captcha_code ($fus_sid);
$code_length = strlen ($confirm_code);

$i = mt_rand (15, 40);
for ( $n = 0; $n < $code_length; $n++ )
{
    $rand_size = mt_rand (20, 45);
    $rand_angle= mt_rand (-45, 45);
    
    $color = hsl2rgb (mt_rand (0, 359), mt_rand (0, 255), mt_rand (120, 255));
    $random_col = imagecolorallocate ($image, $color['r'], $color['g'], $color['b']);

    imagettftext ($image,
            $rand_size,
            $rand_angle,
            $i, mt_rand (50, 75),
            0.5 * $random_col,
            FNEWS_ROOT_PATH . 'news/fonts/VeraMono.ttf',
            $confirm_code[$n]);
    $i += mt_rand ($rand_size * 0.8, $rand_size * 0.8 + 20);
}

// Fancy foreground pattern
imagealphablending ($image, true);
for ( $i = 0; $i < 5; $i++ )
{
    $random_x = mt_rand (0, CAPTCHA_WIDTH);
    $random_y = mt_rand (0, CAPTCHA_HEIGHT);
    $random_w = mt_rand (CAPTCHA_WIDTH * 0.5, CAPTCHA_WIDTH);
    $random_h = mt_rand (CAPTCHA_HEIGHT * 0.5, CAPTCHA_HEIGHT);
    $color = hsl2rgb (mt_rand (0, 359), mt_rand (0, 127), mt_rand (0, 100));
    $random_col = imagecolorallocatealpha ($image, $color['r'], $color['g'], $color['b'], mt_rand (90, 120));

    $func = 'imagefilledrectangle';
    if ( $i % 2 ) $func = 'imagefilledellipse';
    $func ($image, $random_x, $random_y, $random_w, $random_h, $random_col);
}

header ('Content-Type: image/png');
header ('Cache-control: no-cache, no-store');

imagepng ($image);
imagedestroy ($image);

?>