<?php

/**
 * CAPTCHA image
 *
 * @package FusionNews
 * @copyright (c) 2006 - 2011, FusionNews.net
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

define ('CAPTCHA_WIDTH', 200);
define ('CAPTCHA_HEIGHT', 100);

$fus_sid = ( isset ($GVARS['fn_sid']) ) ? $GVARS['fn_sid'] : '';
$news_id = ( isset ($GVARS['fn_id']) ) ? (int)$GVARS['fn_id'] : 0;
$type = ( isset ($GVARS['fn_type']) ) ? $GVARS['fn_type'] : '';

if ( $type != 'comments' && $type != 'send' )
{
    exit;
}

if ( $news_id == 0 )
{
    exit;
}

header ('Content-Type: image/png');
header ('Cache-control: no-cache, no-store');

$image = imagecreatetruecolor (CAPTCHA_WIDTH, CAPTCHA_HEIGHT);
$textimage = imagecreatetruecolor (CAPTCHA_WIDTH, CAPTCHA_HEIGHT);
$textimage2 = imagecreatetruecolor (CAPTCHA_WIDTH, CAPTCHA_HEIGHT);

$color = array (
    'black' => imagecolorallocate ($image, 0x00, 0x00, 0x00),
    'grey' => imagecolorallocate ($image, 0x4B, 0x4B, 0x4B),
    'white' => imagecolorallocate ($image, 0xFF, 0xFF, 0xFF),
    'transparent' => imagecolorallocatealpha ($image, 0, 0, 0, 0)
);

imagefill ($image, 0, 0, $color['white']);
imagefill ($textimage, 0, 0, $color['white']);
imagefill ($textimage2, 0, 0, $color['white']);

/*for ( $i = 0; $i < 10; $i++ )
{
    imageline ($image, mt_rand (5, 80), mt_rand (5, 75), mt_rand (90, 170), mt_rand (5, 75), imagecolorallocate ($image, mt_rand (200, 255), mt_rand(200, 255), mt_rand(200, 255)));
}*/

// Draw text as normal
$confirm_code = get_captcha_code ($fus_sid);
$code_length = strlen ($confirm_code);

$i = 15;
for ( $n = 0; $n < $code_length; $n++ )
{
    $rand_size = mt_rand (20, 40);
    $rand_angle= mt_rand (-15, 15);

    imagettftext ($textimage,
            $rand_size,
            $rand_angle,
            $i, 60,
            0.5 * imagecolorallocate ($image, mt_rand (145, 230), mt_rand(145, 230), mt_rand(145, 230)),
            FNEWS_ROOT_PATH . 'news/fonts/VeraMono.ttf',
            $confirm_code[$n]);
    $i += 35;
}

// Distort the text
$amplitude = mt_rand (5, 10);
$period = mt_rand (13, 18);
for ( $x = 0; $x < CAPTCHA_WIDTH; $x++ )
{
    imagecopy ($textimage2, $textimage, $x, sin ((float)$x / $period) * $amplitude, $x, 0, 1, CAPTCHA_HEIGHT);
}

$amplitude = mt_rand (5, 10);
$period = mt_rand (10, 18);
for ( $y = 0; $y < CAPTCHA_HEIGHT; $y++ )
{
    imagecopy ($image, $textimage2, sin ((float)$y / $period) * $amplitude, $y, 0, $y, CAPTCHA_WIDTH, 1);
}

/*for ( $i = 0; $i < 10; $i++ )
{
    imageline ($image, mt_rand (5, 80), mt_rand (5, 75), mt_rand (90, 170), mt_rand (5, 75), imagecolorallocate ($image, mt_rand (200, 255), mt_rand(200, 255), mt_rand(200, 255)));
}*/

imagepng ($image);
imagedestroy ($image);
imagedestroy ($textimage);
imagedestroy ($textimage2);

?>