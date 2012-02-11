<?php

/**
 * Initial upgrade page
 *
 * @package FusionNews
 * @subpackage Upgrader
 * @copyright (c) 2006 - 2010, FusionNews.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL 3.0 License
 * @version $Id: index.php 333 2010-11-25 23:19:47Z xycaleth $
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

include 'header.html';

echo <<< eof
<p>Please select the version of Fusion News you are currently using, to upgrade to the latest version:</p>
<ul>
	<li><a href="upgrade_37x_to_39x.php">3.7.x</a></li>
	<li><a href="upgrade_38x_to_39x.php">3.8.x</a></li>
</ul>
<p>If you are still using Fusion News 3.6.1, please upgrade to Fusion News 3.7.6 before upgrading to the latest version.</p>
eof;

include 'footer.html';

?>