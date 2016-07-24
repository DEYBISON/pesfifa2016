<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 *
 * @version $Id: default.php 405 2014-10-27 16:37:47Z mmicha $
 * @copyright Copyright (C) 2014 Majunke Michael http://www.mmajunke.de/
 * @license GNU/GPL
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 */

if(JDEBUG) {
	$startTime = microtime();
}

// new Counter Clazz
$vcounter = new modVisitCounterHelper($params);

// Default Sorted Method
$arr_methods = array ( $vcounter->renderPRE(),
                       $vcounter->renderDigitCounter(),
                       $vcounter->renderPeopleTable(),
                       $vcounter->renderHighestVisitsDay(),
                       $vcounter->renderStatistikImage(),
                       $vcounter->renderIP(),
                       $vcounter->renderIPCountryCode(),
                       $vcounter->renderIPCountry(),
                       $vcounter->renderIPFlag(),
                       $vcounter->renderLoggedInUserCount(),
                       $vcounter->renderGuestCount(),
                       $vcounter->renderRegisteredUserCount(),
                       $vcounter->renderRegisteredTodayUserCount(),
                       $vcounter->renderLoggedInUserNamens(),
                       $vcounter->renderRegisteredTodayUserNamens(),
                       $vcounter->renderPOST()
					   );

// Array with Custon Sort
// $arr_sort = explode( ";", $params->get( 'the_order', '1;2;3;4;5;6;7;8;9;10;11;12;13;14;15' ) , 15 );
$arr_sort = explode( ";", $params->get( 'the_order', '1;2;3;4;5;6;7;8;9;10;11;12;13;14;15;16' ) );

// Link on a View
$linkonviewView = $params->get('linkonviewView', '');
$linkonviewLink = $params->get('linkonviewLink', '');
$linkonviewTarget = $params->get('linkonviewTarget', '');

$link = NULL;
$arr_linkviews = NULL;
if ( !empty($linkonviewView) && !empty($linkonviewLink) ) {
	// views
	$arr_linkviews = explode( ";", $linkonviewView );
	// link
	$link = '<a href="' . $linkonviewLink . '" class="mvc_mainlink" ' ;
	if ( !empty($linkonviewTarget) ) {
		$link .= ' target="' . $linkonviewTarget . '"';
	}
	$link .= '>';
}

// Outer Div
$m_content = '<div class="mvc_main' . $params->get( 'moduleclass_sfx' ) . '">';

// out all with Order
for ( $i=0; $i < count($arr_sort) ; $i++){
    if( is_numeric( $arr_sort[$i] ) ){
	  // check to set link on a view
      if ( !empty($arr_linkviews) &&
	         !empty($link) &&
		       in_array( $arr_sort[$i], $arr_linkviews  ) ) {

      	 $m_content .= $link . $arr_methods[ $arr_sort[$i] - 1 ] . '</a>' ;
      }
      else {
        $m_content .= $arr_methods[ $arr_sort[$i] - 1 ] ;
      }
    }
    else {
      $m_content .= $vcounter->renderSpacer();
    }
}

// Close Outer Div
$m_content .= '</div>';

if(JDEBUG) {
  list($old_usec, $old_sec) = explode(' ', $startTime );
  list($new_usec, $new_sec) = explode(' ', microtime());
  $old_mt = ((float)$old_usec + (float)$old_sec);
  $new_mt = ((float)$new_usec + (float)$new_sec);
  $m_content .= "<div class=\"profiler\"><b>DEBUG</b><br/>Time:[" . ($new_mt - $old_mt) . "sec]</div>";
}

// Never delete This !
echo $m_content . "<!-- Mod_VVisit_Counter :  http://www.mmajunke.de/ -->";
?>
