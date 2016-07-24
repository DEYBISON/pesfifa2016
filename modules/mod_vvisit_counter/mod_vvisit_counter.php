<?php
/**
* @version		$Id: mod_vvisit_counter.php 406 2014-10-27 16:45:41Z mmicha $
* @copyright	Copyright (C) 2014 Majunke Michael http://www.mmajunke.de/
* @license		GNU/GPL
*
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('MOD_VVIST_COUNTER_BASE')) {
	define( 'MOD_VVIST_COUNTER_BASE', dirname(__FILE__) );
}

require_once( MOD_VVIST_COUNTER_BASE.DIRECTORY_SEPARATOR.'helper.php' );

require(JModuleHelper::getLayoutPath('mod_vvisit_counter'));
?>
