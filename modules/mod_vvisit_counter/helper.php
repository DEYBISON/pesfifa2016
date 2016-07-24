<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'diagram.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ipcountry' . DIRECTORY_SEPARATOR . 'iptocountry.php');

jimport('joomla.environment.browser');
jimport('joomla.language.helper');

/**
 *
 * @version	$Id: helper.php 406 2014-10-27 16:45:41Z mmicha $
 * @package mod_vvisit_counter
 * @copyright Copyright (C) 2014 Majunke Michael http://www.mmajunke.de/
 * @license GNU/GPL
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 */
class modVisitCounterHelper {

   	// Konstanten
   	//
    // da viele ein Problem mit const haben, erstmal rausgenommen
    // Marke : TODO_CO_MOD
    // const MODULE = 'mod_vvisit_counter' ;
    private $MODULE = 'mod_vvisit_counter' ;
	private $mdclsf = null;
	private $isInserted = FALSE;
	private $ABSOLUTE_URLS = FALSE;

  private $baseurl = null;
	private $cssfile = null;

	// shortcut date_fmt;
	private $date_day_fmt = "d.m.Y";

	// parsing PRE and POST
	private $PRE_AND_POST_REPLACES = array(
                    '$today$',              // simple Heute Datum
					'$countAll$' ,          // Gesamt Anzahl
					'$countToday$' ,        // Anzahl Heute
					'$countYesterday$' ,    // Anzahl Gestern
					'$countWeek$' ,         // Anzahl Woche
					'$countMonth$' ,        // Anzahl Monat
					'$minDate$' ,           // kleinstes Datum in DB
					'$callerIP$' ,          // IP
					'$callerCountryCode$' , // Country
					'$callerCountry$' ,     // ISO2 Country
					'$callerCountryFlag$' , // Flag Country
					'$userAgent$',          // UserAgent
					'$browser$',            // Browser - Joomla
					'$versionBrowser$',     // Browser Version - Joomla
					'$platformBrowser$',    // Browser Platform - Joomla
					'$phpbrowser$',         // Browser - PHP
					'$phpversionBrowser$',  // Browser Version - PHP
					'$phpplatformBrowser$', // Browser Platform - PHP
					'$loggedIn$',           // Logged In Users
					'$guests$',              // Guests
					'$regUsers$',           // Registered Users
					'$regUsersToday$',      // Registered Users Today
					'$visitors$'            // summe: $loggedIn$ + $guests$
				);

    // Members
    //
    private $db = null ;
    private $all_visitors = NULL ;

    private $today_visitors = NULL;
    private $yesterday_visitors = NULL;
    private $week_visitors = NULL;
    private $month_visitors = NULL;
    private $min_Date = NULL;

    private $lang = NULL;

    private $loggedinUserCount = NULL;
    private $guestsCount = NULL;
    private $loggedinUserCountText = NULL;
    private $loggedinUserNamensText = NULL;
    private $userNamensOut = NULL;
    private $userNamensLink = NULL;
    private $userNamensLinkLogged = NULL;
    private $guestsCountText = NULL;
    private $registeredUserCount = NULL;
    private $registeredUserCountText = NULL;
    private $registeredUserCountToday = NULL;
    private $registeredUserCountTodayText = NULL;
    private $loggedinUserNamens = NULL;
    private $registeredUserTodayNamensText = NULL;
    private $registeredUserTodayNames = NULL;

    private $maxVisitsOnDayArr = NULL; // 0 = Value ; 1 = Day

    // Read our Parameters
    //
    // Days
    private $translate_table_texts = null ;
    private $today = null;
    private $yesterday = null;
    private $x_month = null;
    private $x_week = null;
    private $all = null;

    // Highest User-Day in Stat Data
    private $show_highestVisitsDay = null;
    private $highestVisitsDayText = null;
    private $highestVisitsDayTrenner = null;
    private $highestVisitsDayText_Value = null;
    private $highestVisitsDayRecalcSec = 43200; // 6h (60 * 60 * 12)

    // IP
    private $ip = null;
    private $ip_type = null;
    private $filterIPs = "" ;
    private $firstCheckForwarderIP = null;

    // Data
    private $countUserAsNewType = null ;
    private $locktime = null;
    private $initialvalue = null;
    private $records = null;
    private $records_days = null;
    // Show Days
    private $s_today = null;
    private $s_yesterday = null;
    private $s_all = null;
    private $s_week = null;
    private $s_month = null;
    private $s_week_startday = null ;
    // Show IP
    private $s_ip = null;
    private $s_ipCcode = null;
    private $s_ipCountry = null;
    private $s_ipCflag = null;
    private $s_ipCflag_width = null;
    private $s_ipCflag_height = null;
    private $s_ipCLic = null;
    private $s_ipCLicText = null;
    private $s_ipAlsoInRaw = null;

    // Show Images before Days ( little Poeples )
    private $show_UsageImgs = null;
    // Show Digit Counter
    private $s_digit = null;
    // Display Type of Digits
    private $disp_type = null;
    // Digits Counter Parameters
    private $s_digits_min = null;
    private $s_digits_max = null;
    private $s_digits_warnimg = null;

    private $pretext = null;
    private $posttext = null;

    private $widthtable = null;
    // call create Table or not
    private $s_trycreatetable = null;
    private $s_createtableName = null;
    private $s_createtableName_raw = null;

    // image
    // erstellen ?
    private $image_create = 0;
    private $image_path = null;
    // Stunden die das Bild aktuell ist
    private $image_newerAsHours = null;
    // Anzahl Tage
    private $image_showDays = null;

    private $image_sizeX = null;
    private $image_sizeY = null;
    // HTML COLORS
    private $image_lineColor = "#000000" ;
    private $image_dia_MainbgColor = "#FFFFFF";
    private $image_dia_bgColor = "#DDDDDD";
    private $image_dia_RandColor = "#FF0000";
    // Read Only Param
    private $read_only_counter = false;

    // UserAgent Filter ( array with str;str;... )
    private $filterUserAgents = "" ;
    private $filterUserAgentsFile = "" ;
    private $autoFilterRobots = FALSE;
    private $saveUserAgents = FALSE ;

    // Filter Users
    private $filterUsers = 0 ;

    // Cookies
    private $useCookies = TRUE ;

    // Mail
    private $mail_sending = 0;
    private $mail_userIds = null;
    private $mail_body = null;
    private $mail_rate = 1000;
    private $mail_datetime_fmt = "d.m.Y";

    // Trigger Script
    private $trigger_script = 0;
    private $trigger_script_rate = 1000;
    private $trigger_script_name = null;

    // Show Users and Guest
    private $show_loggedinUserCount = 0;
    private $show_loggedinUserNamens = 0;
    private $show_registeredUserTodayNames = 0;
    private $show_guestsCount = 0;
    private $show_registeredUserCount = 0;
    private $show_registeredUserCountToday = 0;
    private $show_UsersCountOnlyFrontend = 1;

    // Group filter for show_loggedinUserNamens, $show_registeredUserTodayNames
    //:OnlyJ16
    private $onlyGroupsN_Flag = 0;
    private $onlyGroupsN = NULL;

	private $userCountTrenner = "";
	private $guestInactivTime = 300;
	private $userInactivTime = 0;

    //
    private $callerIP = "";

    // Instanz of IP to Country Mapper
    private $ipToCountry = null;

	/**
	 * Konstruktor
	 */
    public function __construct(&$params)
    {
        // base
        if ( $this->ABSOLUTE_URLS ) {
            $this->baseurl = JURI::base();
        }
        else {
            $this->baseurl = JURI::base(true) . '/';
        }

        //
        $this->mdclsf = @$params->get( 'moduleclass_sfx' , '' ) ;

        // Read our Parameters
        // Translate, Days, ..
        $this->translate_table_texts = @$params->get('s_translate_table_texts', 0);
        $this->today = @$params->get('today', 'Today');
        $this->yesterday = @$params->get('yesterday', 'Yesterday');
        $this->x_month = @$params->get('month', 'This Month');
        $this->x_week = @$params->get('week', 'This Week');
        $this->all = @$params->get('all', 'All Days');

        // IP
        $this->ip = @$params->get('ip', '');
        $this->ip_type = @$params->get('ip_type', 'text');
        $this->filterIPs = @$params->get('filterIPs', "");
        $this->firstCheckForwarderIP = @$params->get('s_firstCheckForwarderIP', 0);

        // Data
        $this->countUserAsNewType = @$params->get('s_countUserAsNewType', 0);
        $this->locktime = @$params->get('locktime', 30);
        $this->initialvalue = @$params->get('initialvalue', 0);
        $this->records = @$params->get('records', 1);
        $this->records_days = @$params->get('recordDays', -1);
        // Show Days
        $this->s_today = @$params->get('s_today', 1);
        $this->s_yesterday = @$params->get('s_yesterday', 1);
        $this->s_all = @$params->get('s_all', 1);
        $this->s_week = @$params->get('s_week', 1);
        $this->s_month = @$params->get('s_month', 1);
        $this->s_week_startday = @$params->get('s_week_startday', 1);
        // Show IP
        $this->s_ip = @$params->get('s_ip', 0);
        $this->s_ipCcode = @$params->get('s_ipCcode', 0);
        $this->s_ipCountry = @$params->get('s_ipCountry', 0);
        $this->s_ipCflag = @$params->get('s_ipCflag', 0);
        $this->s_ipCflag_width = @$params->get('s_ipCflag_width', 20);
        $this->s_ipCflag_height = @$params->get('s_ipCflag_height', 13);
        $this->s_ipAlsoInRaw = @$params->get('s_ipAlsoInRaw', 0);

        $this->s_ipCLic = @$params->get('s_ipCLic', 1);
        $this->s_ipCLicText = @$params->get('s_ipCLicText',
		    "<p style='font-size:25%'>This page uses the IP-to-Country Database provided by WebHosting.Info (http://www.webhosting.info), available from http://ip-to-country.webhosting.info</p>");

        // Show Images before Days ( little Poeples )
        $this->show_UsageImgs = @$params->get('show_UsageImgs', 'peoples');
        // Show Digit Counter
        $this->s_digit = @$params->get('s_digit', 1);
        // Display Type of Digits
        $this->disp_type = @$params->get('disp_type', "text");
        // Digits Counter Parameters
        $this->s_digits_min = @$params->get('s_digits_min', "6");
        $this->s_digits_max = @$params->get('s_digits_max', "6");
        $this->s_digits_warnimg = @$params->get('s_digits_warnimg', "");

        $this->pretext = @$params->get('pretext', "");
        $this->posttext = @$params->get('posttext', "");

        $this->widthtable = @$params->get('widthtable', "100");
        // call create Table or not
        $this->s_trycreatetable = @$params->get('s_trycreatetable', 1);
        $this->s_createtableName_raw = @$params->get('s_createtableName', "vvisitcounter" );
        $this->s_createtableName = '#__' . $this->s_createtableName_raw;

        // Image
        $this->image_create = @$params->get('image_create', 0);
        $this->image_path = @$params->get('image_path', '/tmp/imagemvc.png');
        $this->image_newerAsHours = @$params->get('image_newerAsHours', 6);
        $this->image_sizeX = @$params->get('image_sizeX', 160);
        $this->image_sizeY = @$params->get('image_sizeY', 90);
        $this->image_lineColor = @$params->get('image_lineColor', "#000000");
        $this->image_dia_MainbgColor = @$params->get('image_dia_MainbgColor', "#FFFFFF");
        $this->image_dia_bgColor = @$params->get('image_dia_bgColor', "#DDDDDD");
        $this->image_dia_RandColor = @$params->get('image_dia_RandColor', "#FF0000");
        $this->image_showDays = @$params->get('image_showDays', 0); // <0 all;

        // read only mode
        $this->read_only_counter = @$params->get('read_only_counter', false);

        // Filter UserAgents
        $this->filterUserAgents = @$params->get('filterUserAgents', "");
        $this->filterUserAgentsFile = @$params->get('filterUserAgentsFile', -1 );
        $this->autoFilterRobots = @$params->get('s_autoFilterRobots' , 0 );
        $this->saveUserAgents = @$params->get('saveUserAgents', 0 );

        // Filter Users
        $this->filterUsers = @$params->get('filterUsers', 0 );

        // Cookies
        $this->useCookies = @$params->get('s_useCookies', true );

        // Mail
        $this->mail_sending = @$params->get('mail_sending', 0 );
        $this->mail_userIds = @$params->get('mail_userIds', '' );
        $this->mail_body = @$params->get('mail_body', '$a' );
        $this->mail_rate = @$params->get('mail_rate', 1000 );
        if ( $this->mail_rate < 1 ) {
        	$this->mail_rate = 1;
        }
        $this->mail_datetime_fmt = @$params->get('mail_datetime_fmt', 'd.m.Y' );

		// Trigger Script
        $this->trigger_script = @$params->get('trigger_script', 0 );
        $this->trigger_script_rate = @$params->get('trigger_script_rate', 1000 );
        if ( $this->trigger_script_rate < 1 ) {
        	$this->trigger_script_rate = 1;
        }
        $this->trigger_script_name = @$params->get('trigger_script_name', '' );

		// Users View
		$this->show_loggedinUserCount = @$params->get('s_loggedinUserCount', 0 );
		$this->loggedinUserCountText = @$params->get('loggedinUserCountText', '' );

		$this->show_loggedinUserNamens = @$params->get('s_loggedinUserNamens', 0 );
		$this->loggedinUserNamensText = @$params->get('loggedinUserNamensText', '' );
		$this->userNamensOut = @$params->get('s_userNamensOut', 1 );

		$this->show_guestsCount = @$params->get('s_guestsCount', 0 );
		$this->guestsCountText = @$params->get('guestsText', '' );

		$this->show_registeredUserCount = @$params->get('s_registeredUserCount', 0 );
		$this->registeredUserCountText = @$params->get('registeredUserCountText', '' );

		$this->show_registeredUserCountToday = @$params->get('s_registeredUserCountToday', 0 );
		$this->registeredUserCountTodayText = @$params->get('registeredUserCountTodayText', '' );

		$this->show_registeredUserTodayNames = @$params->get('s_registeredUserTodayNames', 0 );
		$this->registeredUserTodayNamensText = @$params->get('registeredUserTodayNamensText', '' );

		$this->show_UsersCountOnlyFrontend = @$params->get('s_onlyFrontEndCount', 1 );
		$this->s_userCountTrenner = @$params->get('userCountTrenner', ' ' );

		$this->userNamensLink = @$params->get('userNamensLink', NULL );
		$this->userNamensLinkLogged = @$params->get('userNamensLinkLogged', 0 );

		$this->guestInactivTime = @$params->get('s_guestInactivTime', 300 );
		$this->userInactivTime = @$params->get('s_userInactivTime', 0 ); // 0 is joomla default

        // Group filter for show_loggedinUserNamens, $show_registeredUserTodayNames
        //:OnlyJ16
        $this->onlyGroupsN_Flag = @$params->get('s_onlyGroupsN_Flag', 0 );
        $this->onlyGroupsN = @$params->get('s_onlyGroupsN', NULL );
		//
		$this->show_highestVisitsDay = @$params->get('s_highestVisitsDay', 0 );
		$this->highestVisitsDayText = @$params->get('highestVisitsDayText', '' );
		$this->highestVisitsDayTrenner = @$params->get('highestVisitsDayTrenner', '' );
		$this->highestVisitsDayText_Value = @$params->get('highestVisitsDayTValue', '$d : $v' );
		$this->highestVisitsDayRecalcSec = @$params->get('highestVisitsDayUpdInt', '43200' );


		// Auto Translate Texts.. if not found, is the same
		// translate is only needed if show the days
        if ( $this->translate_table_texts > 0 ) {
			if ($this->s_today) {
        		$this->today = $this->get_translated_text($this->today);
	        }
			if ($this->s_yesterday) {
				$this->yesterday = $this->get_translated_text($this->yesterday);
			}
    	    if ($this->s_week) {
        		$this->x_week = $this->get_translated_text($this->x_week);
        	}
			if ($this->s_month) {
    	    	$this->x_month = $this->get_translated_text($this->x_month);
    	    }
        	if ($this->s_all) {
        		$this->all = $this->get_translated_text($this->all);
        	}
        	if ($this->show_loggedinUserCount) {
        		$this->loggedinUserCountText =
				     $this->get_translated_text($this->loggedinUserCountText);
        	}
        	if ($this->show_loggedinUserNamens) {
        		$this->loggedinUserNamensText =
				     $this->get_translated_text($this->loggedinUserNamensText);
        	}
        	if ($this->show_guestsCount) {
        		$this->guestsCountText =
				     $this->get_translated_text($this->guestsCountText);
        	}
        	if ($this->show_registeredUserCount) {
        		$this->registeredUserCountText =
				     $this->get_translated_text($this->registeredUserCountText);
        	}
        	if ($this->show_registeredUserCountToday) {
        		$this->registeredUserCountTodayText =
				     $this->get_translated_text($this->registeredUserCountTodayText);
          }
        	if ($this->show_registeredUserTodayNames) {
        		$this->registeredUserTodayNamensText =
				     $this->get_translated_text($this->registeredUserTodayNamensText);
        	}
          //
        	if ($this->show_highestVisitsDay) {
        		$this->highestVisitsDayText =
				     $this->get_translated_text($this->highestVisitsDayText);
        	}

        }

		// date_fmt translated
		$this->date_day_fmt = $this->get_translated_datefmt('DATE_FMT_DAY');

		// append own CSS
		$this->cssfile = @$params->get('cssfile' , 'mvc.css' );
		// simple -1
		if ( FALSE === is_numeric($this->cssfile) ) {
			$document = JFactory::getDocument();
			$document->addStyleSheet(
						// JURI::base() . 'modules/' . $this->MODULE . '/' .
						$this->baseurl . 'modules/' . $this->MODULE . '/' .
			 			$this->cssfile
			);
		}

		// Extend Filter from File
		// simple -1
		if ( FALSE === is_numeric($this->filterUserAgentsFile) ) {
		    $fileToLoad = JPATH_SITE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->MODULE . DS .
			              $this->filterUserAgentsFile;
			jimport('joomla.filesystem.file');
			if ( JFile::exists($fileToLoad) ) {

				// last is a ;
				$addc = "";
				if ( !empty($this->filterUserAgents) &&
					 !( substr( $this->filterUserAgents, -1) === ";" )
				   ) {
					$addc = ";" ;
				}
				$this->filterUserAgents =
				    $this->filterUserAgents . $addc . JFile::read($fileToLoad);

			}
		}

        // Initalisierung
        $this->init($params);
    }

    /**
     * render Spacer
     * <div class="mvc_spacer">
     *   <hr/>
     * </div>
     *
     * @return content
     */
    public function renderSpacer()
    {
        return '<div style="text-align: center;" class="mvc_spacer' . $this->mdclsf . '"><hr/></div>';
    }

    /**
     * render IP-Adress
     * <div class="mvc_ip">
     *   für text
	 *   <span class="vcounter_TypeTextIP">..
	 *   or
	 *   <img class="mvc_digitCounterImg" ...
	 * </div>
	 *
     * @return content
     */
    public function renderIP()
    {

        if ( $this->s_ip && ($this->callerIP != null) ) {

			// return '<div style="text-align: center;" class="mvc_ip' . $this->mdclsf . '">' . $this->ip . $this->callerIP . '</div>';

            $content = '<div style="text-align: center;" class="mvc_ip' . $this->mdclsf . '">' . $this->ip ;
            // spezial Type Text
            if ($this->ip_type == 'text') {

			    // Show as Text
                $content .= '<span style="text-align: center;" class="vcounter_TypeTextIP' . $this->mdclsf . '">' . $this->callerIP . '</span>' ;

            } else {

                // show as Images

                $isv4 = strpos( $this->callerIP, ":" );

                if ( FALSE === $isv4 ) {

    				//split
                	$arr_ip_Part = explode( ".", $this->callerIP , 4 );
    				if ( $arr_ip_Part == null || (empty($arr_ip_Part)) ) {
    					return;
    				}
    				//
    				$c=0 ;
                	foreach( $arr_ip_Part as $ip_Part ){
    	                // Render Images
    	                $content .= $this->numToImg(
    					    $ip_Part ,
    	                    1 ,
    	                    3 ,
    	                    // JURI::base() . 'modules/' .
    	                    $this->baseurl . 'modules/' .
    	                    // TODO_CO_MOD modVisitCounterHelper::MODULE
    	                    $this->MODULE . '/images/' . $this->ip_type . '/',
    	                    null,
    						'mvc_digitIPImg' );
    	                if ( $c++ < 3) {
    	                	$dir = // JURI::base() . 'modules/' .
    	                	       $this->baseurl . 'modules/' .
    	                           // TODO_CO_MOD modVisitCounterHelper::MODULE
    	                           $this->MODULE . '/images/' . $this->ip_type . '/';
    	                	$content .= "<img class=\"mvc_digitIPImg" . $this->mdclsf . "\" src=\"{$dir}dot.gif\" alt=\"Dot\" title=\"Dot\"/>" ;
    	                }
                	}

            	}
            	else {

                    // ipv6

    				//split
                	$arr_ip_Part = explode( ":", $this->callerIP , 8 );
    				if ( $arr_ip_Part == null || (empty($arr_ip_Part)) ) {
    					return;
    				}
    				//
    				$c=0 ;
                	foreach( $arr_ip_Part as $ip_Part ){
    	                // Render Images
    	                $content .= $this->numToImg(
    					    $ip_Part ,
    	                    1 ,
    	                    4 ,
    	                    // JURI::base() . 'modules/' .
    	                    $this->baseurl . 'modules/' .
    	                    // TODO_CO_MOD modVisitCounterHelper::MODULE
    	                    $this->MODULE . '/images/' . $this->ip_type . '/',
    	                    null,
    						'mvc_digitIPImg' );
    	                if ( $c++ < 7) {
    	                	$dir = // JURI::base() . 'modules/' .
    	                	       $this->baseurl . 'modules/' .
    	                           // TODO_CO_MOD modVisitCounterHelper::MODULE
    	                           $this->MODULE . '/images/' . $this->ip_type . '/';
    	                	$content .= "<img class=\"mvc_digitIPImg" . $this->mdclsf . "\" src=\"{$dir}colon.gif\" alt=\"Colon\" title=\"Colon\"/>" ;
    	                }
                	}

                }

            }
            $content .= '</div>';
            return $content;

        } else {
            return "";
        }
    }


    /**
     * render Country Code of IP (callerIP)
     * <div class="mvc_ipcountrycode"> ...
     */
    public function renderIPCountryCode()
    {

    	if ( !$this->s_ipCcode ||
		     $this->ipToCountry == null ||
			 $this->callerIP == null ){
    		return "";
    	}
		//
		$code = $this->ipToCountry->getISOAlphaZwei($this->callerIP);
		// Test
		//$code = $this->ipToCountry->getISOAlphaZwei("85.233.46.218");
        if ( empty($code) ) {
            $code = "?";
        }
        return '<div style="text-align: center;" class="mvc_ipcountrycode' . $this->mdclsf . '">' . $code . '</div>';
    }

    /**
     * render Country of IP (callerIP)
     * <div class="mvc_ipcountry"> ...
     */
    public function renderIPCountry()
    {

    	if ( !$this->s_ipCountry ||
		     $this->ipToCountry == null ||
			 $this->callerIP == null ) {
    		return "";
    	}
		//
		$country = $this->ipToCountry->getCountry($this->callerIP);
		// Test
		// $country = $this->ipToCountry->getCountry("85.233.46.218");

        if ( empty($country) ) {
            $country = "?";
        }
        return '<div style="text-align: center;" class="mvc_ipcountry' . $this->mdclsf . '">' . $country . '</div>';
    }


    /**
     * render Flag of IP (callerIP)
     * <div class="mvc_ipflag"> ...
     *   <img class="mvc_ipflagimg"
     */
    public function renderIPFlag()
    {

    	if ( !$this->s_ipCflag ||
		     $this->ipToCountry == null ||
			 $this->callerIP == null ) {
    		return "";
    	}
		//
		$code = $this->ipToCountry->getISOAlphaZwei($this->callerIP);
		// Test
		//$code = $this->ipToCountry->getISOAlphaZwei("85.233.46.218");

		// make an empty check
        $img = $this->isoZweiToFlagImg(
		    $code ,
            // JURI::base() . 'modules/' .
            $this->baseurl . 'modules/' .
            // TODO_CO_MOD modVisitCounterHelper::MODULE
            $this->MODULE . '/images/flags/',
			'mvc_ipflagimg' ,
			$this->s_ipCflag_width,
			$this->s_ipCflag_height );

         return '<div style="text-align: center;" class="mvc_ipflag' . $this->mdclsf . '">' . $img . "</div>";

    }

    /**
     * render PRE-Text
     * <div class="mvc_pre"> ...
     */
    public function renderPRE()
    {
        if ($this->pretext != "") {
            return '<div style="text-align: center;" class="mvc_pre' . $this->mdclsf . '">' . $this->pretext . '</div>';
        } else {
            return "";
        }
    }

    /**
     * render POST-Text
     * <div class="mvc_post"> ...
     */
    public function renderPOST()
    {
    	$other = null;
    	if ( ( $this->s_ipCcode || $this->s_ipCountry || $this->s_ipCflag ) && $this->s_ipCLic ) {
    		$other = $this->s_ipCLicText;
    	}

        if ( ($this->posttext != "") || !empty($other) ) {
            return '<div style="text-align: center;" class="mvc_post' . $this->mdclsf . '">' . $this->posttext . $other . '</div>';
        } else {
            return "";
        }
    }

    /**
     * render POST-Text
     * <div class="mvc_digitCounter"> ...
     *   <span class="vcounter_TypeText"
     * or
     *   <img class="mvc_digitCounterImg"
     */
    public function renderDigitCounter()
    {
        if ($this->s_digit) {
            $content = '<div style="text-align: center;" class="mvc_digitCounter' . $this->mdclsf . '">';
            // spezial Type Text
            if ($this->disp_type == 'text') {
                // Show as Text
                $content .= '<span style="text-align: center;" class="vcounter_TypeText' . $this->mdclsf . '">' . $this->all_visitors . '</span>' ;
            } else {
                // Render Images
                $content .= $this->numToImg($this->all_visitors ,
                    $this->s_digits_min ,
                    $this->s_digits_max ,
                    // JURI::base() . 'modules/' .
                    $this->baseurl . 'modules/' .
                    // TODO_CO_MOD modVisitCounterHelper::MODULE
                    $this->MODULE . '/images/' . $this->disp_type . '/',
                    $this->s_digits_warnimg,
					'mvc_digitCounterImg');
            }
            $content .= '</div>';
            return $content;
        } else {
            return "";
        }
    }

    /**
     * Render the People Table
     * <div class="mvc_people"> ...
     *   <table class="mvc_peopleTable"
     *    <img class="mvc_peopleImg"
     */
    public function renderPeopleTable()
    {
        if ($this->s_today || $this->s_yesterday || $this->s_week || $this->s_month || $this->s_all) {

			//
		   	$content = '<div style="text-align: center;" class="mvc_people' . $this->mdclsf . '"><table align="center" cellpadding="0" cellspacing="0" style="width: ' . $this->widthtable . '%;" class="mvc_peopleTable' . $this->mdclsf . '"><tbody>';

            if ($this->s_today) {

                $today_visitors = $this->sql_today_visitors();

                // compute today title
				// Info: Using not JDate because Error with Joomla < 1.5.2
                $title_day_str_day = date( $this->date_day_fmt );

				$content .= modVisitCounterHelper::spaceer("vtoday.gif", $this->today, $today_visitors, $this->show_UsageImgs, $title_day_str_day );
            }

            if ($this->s_yesterday) {

                $yesterday_visitors = $this->sql_yesterday_visitors();

				// compute yesterday title
				$title_day_str_yesterday = date( $this->date_day_fmt , time() - (1 * 24 * 60 * 60) );

				$content .= modVisitCounterHelper::spaceer("vyesterday.gif", $this->yesterday, $yesterday_visitors, $this->show_UsageImgs, $title_day_str_yesterday );
            }

            if ($this->s_week) {

                $week_visitors = $this->sql_week_visitors();

				// compute this Week (from - to) title
				// use configured week_start_day
				$ft = array();
				if ( $this->s_week_startday == 0 ) {
					// SUNDAY
					$ft = $this->getWeekFromTo( time() , 'Sunday' , $this->date_day_fmt) ;
				}
				else if ( $this->s_week_startday == 2 ) {
					// SATURDAY
					$ft = $this->getWeekFromTo( time() , 'Saturday' , $this->date_day_fmt) ;
				}
				else {
					// MONDAY
					$ft = $this->getWeekFromTo( time() , 'Monday' , $this->date_day_fmt) ;
				}
                $title_day_str_week = $ft["from"] . ' - ' . $ft["to"];

                $content .= modVisitCounterHelper::spaceer("vweek.gif", $this->x_week, $week_visitors, $this->show_UsageImgs, $title_day_str_week );
            }

            if ($this->s_month) {

                $month_visitors = $this->sql_month_visitors();

				// compute this Month ( from - to ) title
				$current_day = date('d') - 1;
				$days_remaining_until = date('t') - $current_day - 1;
				$tm_start = strtotime("-$current_day days");
				$tm_end = strtotime("+$days_remaining_until days");

                $title_day_str_month_s = date( $this->date_day_fmt, $tm_start );
                $title_day_str_month_e = date( $this->date_day_fmt, $tm_end );
                $title_day_str_month = $title_day_str_month_s . ' - ' . $title_day_str_month_e;

                $content .= modVisitCounterHelper::spaceer("vmonth.gif", $this->x_month, $month_visitors, $this->show_UsageImgs, $title_day_str_month );
            }

			// Err ?
			if( $this->db->getErrorNum () ) {
				$e = $this->db->getErrorMsg();
				//print_r( $e );
				JError::raiseWarning( 500, $e );
				return;
			}

            if ($this->s_all) {
	            $content .= modVisitCounterHelper::spaceer("vall.gif", $this->all, $this->all_visitors, $this->show_UsageImgs, "" );
			}

	        $content .= "</tbody></table></div>";
            return $content;

        } else {
            return "";
        }
    }

    /**
     * Render the Statistik Image
     * <div class="mvc_stat"
     *  <img class="mvc_statImg"
     */
    public function renderStatistikImage()
    {
        if ( $this->image_create > 0 ) {
            $content = '';
            // save if not exist and older as time
            // $imagePathFS = JPATH_BASE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . modVisitCounterHelper::MODULE . DIRECTORY_SEPARATOR . 'image.png';
            $imagePathFS = JPATH_BASE . $this->image_path ;
            $timeCreated = time();

            if (! file_exists($imagePathFS)) {

				if ( $this->image_create == 2 ){
                      $this->renderStatImage( $this->db,
                                              $imagePathFS,
                                              $this->image_showDays,
                                              true );
                    }
                    else {
                        $this->renderStatImage( $this->db,
                                              $imagePathFS,
                                              $this->image_showDays,
                                              false );
                    }

            } else {
                $timeCreated = filemtime($imagePathFS);
                $diffinHours = (time() - $timeCreated) / 60 / 60 ;
                // erstelle nur wenn neuer als Stunden
                if ($diffinHours > $this->image_newerAsHours) {

                    if ( $this->image_create == 2 ){
                      $this->renderStatImage( $this->db,
                                              $imagePathFS,
                                              $this->image_showDays,
                                              true );
                    }
                    else {
                        $this->renderStatImage( $this->db,
                                              $imagePathFS,
                                              $this->image_showDays,
                                              false );
                    }
                }
            }


            // URL to Load
            // $imageUri = JURI::base() . 'modules/' . modVisitCounterHelper::MODULE . '/image.png';
            // $imageUri = JURI::base() . $this->image_path;
            // $imageUri = $this->baseurl . $this->image_path;
            $image_path_loc = $this->image_path ;
            if ( strpos( $image_path_loc, "/" ) === 0 ) {
                $image_path_loc = substr( $image_path_loc, 1, strlen($image_path_loc) );
            }
            $imageUri = $this->baseurl . $image_path_loc;
            $content .= '<div style="text-align: center;" class="mvc_stat' . $this->mdclsf . '">' ;
            $crstaltAndtitle = $this->get_translated_text('CREATED_STAT_ALT') . ': ' . date( "c" , $timeCreated ) ;
            $content .= '<img class="mvc_statImg' . $this->mdclsf . '" alt="' . $crstaltAndtitle . '" title="' . $crstaltAndtitle . '" src="' . $imageUri . '"/>';
            $content .= "</div>";
            return $content;
        } else {
            return "";
        }
    }


    /**
     * render Logged In Users
     * <div class="mvc_loggedInUsers">
	 *       <span class="title">...
     *       <span class="trenner">...
	 *       <span class="value">...
	 *
     */
    public function renderLoggedInUserCount()
    {
    	if ( !$this->show_loggedinUserCount ||
		     $this->loggedinUserCount < 0 ){
    		return "";
    	}
        return '<div style="text-align: center;" class="mvc_loggedInUsers' .
		       $this->mdclsf . '">' .
			   '<span class="title">' . $this->loggedinUserCountText . '</span>' .
			   '<span class="trenner">' . $this->s_userCountTrenner . '</span>' .
			   '<span class="value">' . $this->loggedinUserCount . '</span>' .
			   '</div>';
    }

    /**
     * render Logged In User Namens
     *
	 *
     * <div class="mvc_loggedInUserNamens">
	 *       <span class="title">...
     *       1...* <div class="user">... <a class="userlink">.. </div>
     * </div>
	 *
     */
    public function renderLoggedInUserNamens()
    {
    	if ( !$this->show_loggedinUserNamens
		     // || $this->loggedinUserNamens === NULL
			){
    		return "";
    	}
        $erg = '<div class="mvc_loggedInUserNamens' . $this->mdclsf . '">' ;
        if ( !empty($this->loggedinUserNamensText) ) {
       		$erg .= '<div class="title">' . $this->loggedinUserNamensText . '</div>' ;
       	}

 	    if ( empty($this->loggedinUserNamens) ) {
	   		$erg .= '<div class="user"> - </div>' ;
	    } else {
		   	foreach( $this->loggedinUserNamens as $row ){

			    // weiterhin $row['id']

				// Design ( Account, Name, Title .. )
		   	    $titleP = "";
		   	    $nameP = "" ;
				switch($this->userNamensOut){
					case 3:
						$nameP = htmlspecialchars($row['name']);
						break;
					case 2:
						$nameP = htmlspecialchars($row['uname']);
						break;
					case 0:
						$titleP = 'title="' . htmlspecialchars($row['uname']) . '"';
						$nameP = htmlspecialchars($row['name']);
						break;
					default:
						$titleP = 'title="' . htmlspecialchars($row['name']) . '"';
						$nameP = htmlspecialchars($row['uname']);
				}

				// Link .. only for logged in ?
				$guestCheck = TRUE;
				if ( $this->userNamensLinkLogged < 1 ) {
					$guestCheck = ! JFactory::getUser()->guest ;
				}
				// prepare
				if ( $guestCheck &&
				     !empty($this->userNamensLink) ) {
				    $link_rep = $this->userNamensLink ;
					$link_rep = str_replace( '$$id', $row['id'] , $link_rep );
					$link_rep = str_replace( '$$account', $row['uname'] , $link_rep );
					$link_rep = str_replace( '$$name', $row['name'] , $link_rep );
					$nameP = '<a class="userlink" href="' . $link_rep . '">' . $nameP . '</a>';
				}

				$erg .= '<div class="user" ' . $titleP . '>' . $nameP . '</div>' ;

		   	}
	    }
		$erg .= '</div>';
		return $erg;

    }

    /**
     * render Guests
     * <div class="mvc_guests"> ...
     *       <span class="title">...
     *       <span class="trenner">...
	 *       <span class="value">...
     */
    public function renderGuestCount()
    {
    	if ( !$this->show_guestsCount ||
		     $this->guestsCount < 0 ){
    		return "";
    	}
        return '<div style="text-align: center;" class="mvc_guests' .
		        $this->mdclsf . '">' .
				'<span class="title">' . $this->guestsCountText . '</span>' .
				'<span class="trenner">' . $this->s_userCountTrenner . '</span>' .
				'<span class="value">' . $this->guestsCount . '</span>' .
			   '</div>';
    }

    /**
     * render Rwegistered User Count
     * <div class="mvc_reg_users_all"> ...
     *       <span class="title">...
     *       <span class="trenner">...
	 *       <span class="value">...
     */
    public function renderRegisteredUserCount()
    {
    	if ( !$this->show_registeredUserCount ||
		     $this->registeredUserCount < 0 ){
    		return "";
    	}
        return '<div style="text-align: center;" class="mvc_reg_users_all' .
		        $this->mdclsf . '">' .
				'<span class="title">' . $this->registeredUserCountText . '</span>' .
				'<span class="trenner">' . $this->s_userCountTrenner . '</span>' .
				'<span class="value">' . $this->registeredUserCount . '</span>' .
			   '</div>';
    }

    /**
     * render Logged In User Namens
     *
	 *
     * <div class="mvc_regTodayUserNamens">
	 *       <span class="title">...
     *       1...* <div class="user">... <a class="userlink">.. </div>
     * </div>
	 *
     */
    public function renderRegisteredTodayUserNamens(){

    	if ( !$this->show_registeredUserTodayNames
		     // || $this->registeredUserTodayNames === NULL
			){
    		return "";
    	}
        $erg = '<div class="mvc_regTodayUserNamens' . $this->mdclsf . '">' ;
        if ( !empty($this->registeredUserTodayNamensText) ) {
       		$erg .= '<div class="title">' . $this->registeredUserTodayNamensText . '</div>' ;
       	}

 	    if ( empty($this->registeredUserTodayNames) ) {
	   		$erg .= '<div class="user"> - </div>' ;
	    } else {
		   	foreach( $this->registeredUserTodayNames as $row ){

			    // weiterhin $row['id']

				// Design ( Account, Name, Title .. )
		   	    $titleP = "";
		   	    $nameP = "" ;
				switch($this->userNamensOut){
					case 3:
						$nameP = htmlspecialchars($row['name']);
						break;
					case 2:
						$nameP = htmlspecialchars($row['uname']);
						break;
					case 0:
						$titleP = 'title="' . htmlspecialchars($row['uname']) . '"';
						$nameP = htmlspecialchars($row['name']);
						break;
					default:
						$titleP = 'title="' . htmlspecialchars($row['name']) . '"';
						$nameP = htmlspecialchars($row['uname']);
				}

				// Link .. only for logged in ?
				$guestCheck = TRUE;
				if ( $this->userNamensLinkLogged < 1 ) {
					$guestCheck = ! JFactory::getUser()->guest ;
				}
				// prepare
				if ( $guestCheck &&
				     !empty($this->userNamensLink) ) {
				    $link_rep = $this->userNamensLink ;
					$link_rep = str_replace( '$$id', $row['id'] , $link_rep );
					$link_rep = str_replace( '$$account', $row['uname'] , $link_rep );
					$link_rep = str_replace( '$$name', $row['name'] , $link_rep );
					$nameP = '<a class="userlink" href="' . $link_rep . '">' . $nameP . '</a>';
				}

				$erg .= '<div class="user" ' . $titleP . '>' . $nameP . '</div>' ;

		   	}
	    }
		$erg .= '</div>';
		return $erg;
	}

    /**
     * render Rwegistered User Count
     * <div class="mvc_reg_users_today"> ...
     *       <span class="title">...
     *       <span class="trenner">...
	 *       <span class="value">...
     */
    public function renderRegisteredTodayUserCount()
    {
    	if ( !$this->show_registeredUserCountToday ||
		     $this->registeredUserCountToday < 0 ){
    		return "";
    	}
        return '<div style="text-align: center;" class="mvc_reg_users_today' .
		        $this->mdclsf . '">' .
				'<span class="title">' . $this->registeredUserCountTodayText . '</span>' .
				'<span class="trenner">' . $this->s_userCountTrenner . '</span>' .
				'<span class="value">' . $this->registeredUserCountToday . '</span>' .
			   '</div>';
    }

    /**
     * render Highest User Day
     * <div class="mvc_max_user_day"> ...
     *       <span class="title">...
     *       <span class="trenner">...
	 *       <span class="value">...
     */
    public function renderHighestVisitsDay()
    {
    	if ( !$this->show_highestVisitsDay ||
		     empty($this->maxVisitsOnDayArr) ){
    		return "";
    	}

    	$highestVisitsDayText_rep = str_replace( '$v', $this->maxVisitsOnDayArr[0] , $this->highestVisitsDayText_Value );
    	$highestVisitsDayText_rep = str_replace( '$d', $this->maxVisitsOnDayArr[1] , $highestVisitsDayText_rep );

        return '<div class="mvc_max_user_day' . $this->mdclsf . '">' .
				'<span class="title">' . $this->highestVisitsDayText. '</span>' .
				'<span class="trenner">' . $this->highestVisitsDayTrenner . '</span>' .
				'<span class="value">' . $highestVisitsDayText_rep . '</span>' .
			   '</div>';
    }



	/**
	 * Translate a Text with Language from Request (Client-Language)
	 *
	 * need a Language-File
	 *
	 * @param  $string toTranslate
	 * @return tranlated text or same
	 */
	protected function get_translated_text( $string ){
		if ( !isset($this->lang) ) {
	        // there is no default in Joomla 1.6 ;(
			$lang_str = 'en-GB';
            // lang from Browser or Joomla
    		if ( $this->translate_table_texts == 1 ) {
    		    // detect from Browser
    			$lang_str = JLanguageHelper::detectLanguage();
    		}
    		else {
        		// use Joomla Setting
                $lang_str = JFactory::getLanguage()->getTag();
            }
			// Default
		    if ( empty($lang_str) ) {
  				$lang_str = 'en-GB';
  			}
			$this->lang = JLanguage::getInstance($lang_str);
			$loaded = $this->lang->load(  $this->MODULE );
		}
		if ( $this->lang->hasKey($string) === true ) {
			return $this->lang->_($string, false);
		}
		else {
			return $string;
		}
	}

	/**
	 * Translate a Date_FMT_Const with Language from Request (Client-Language)
	 *
	 * need a Language-File
	 *
	 * @param  $string toTranslate
	 * @return tranlated text or Default d.m.Y
	 */
	protected function get_translated_datefmt( $string ){
		if ( $string == null ) {
			return "d.m.Y";
		}
		if ( !isset($this->lang) ) {
	        // there is no default in Joomla 1.6 ;(
			$lang_str = 'en-GB';
            // lang from Browser or Joomla
    		if ( $this->translate_table_texts == 1 ) {
    		    // detect from Browser
    			$lang_str = JLanguageHelper::detectLanguage();
    		}
    		else {
        		// use Joomla Setting
                $lang_str = JFactory::getLanguage()->getTag();
            }
			// Default
		    if ( empty($lang_str) ) {
  				$lang_str = 'en-GB';
  			}
			$this->lang = JLanguage::getInstance($lang_str);
			$loaded = $this->lang->load(  $this->MODULE );
		}
		if ( $this->lang->hasKey($string) === true ) {
			return $this->lang->_($string, false);
		}
		else {
			return "d.m.Y";
		}
	}

    /**
     * Init Clazz
     */
    protected function init($params)
    {

		 // Got CallerIP from Cookie or Servers RemoteAddr
		 //
		 // use cookies
		 // readonly or hitcounter dont need cookies
		 if ( (!$this->useCookies) ||
		      $this->read_only_counter ||
		      ($this->locktime <= 0) ) {
			// ip
			$this->callerIP = $this->parseCallerIP( $this->firstCheckForwarderIP );
		 }
		 else {

			// each Datatabelle have own Cookie
			$cookie_name = "cip_" . $this->s_createtableName_raw ;

			// load stored Cookie
			$cookie_ip = null;
			if (isset($_COOKIE[$cookie_name])) {
				$cookie_ip = $_COOKIE[$cookie_name];
			}

			// Callers IP-Adress from Cookie or Request
			if ( empty($cookie_ip) ) {
				// ip
				$this->callerIP = $this->parseCallerIP( $this->firstCheckForwarderIP );
				// cookie_expired_time
				$cookie_expire = -1;
				// User is count as new by Locktime or Daily
	            if ( $this->countUserAsNewType == 1 ) {
	            	// Daily
	            	$cookie_expire = time() + (60*60*24);
	            }
	            else {
	            	// By Locktime
					if ( $this->locktime <= 0 ) {
						// nothing - no cookie
						// hitcounter ( should be handled before )
					}
					else {
	            		//
	            		$cookie_expire = time() + (60 * $this->locktime);
	        		}
	            }

				if ( $cookie_expire > 0 ) {
					// new cookie only if expire set
					setcookie( $cookie_name ,
					           base64_encode( $this->callerIP ),
							   $cookie_expire );
				}

			}
			else {
				// ip
				$this->callerIP = base64_decode( $cookie_ip );
			}

		}

        // Simple Test
	    // $this->callerIP = "85.233.46.218";

        // Database init
        $this->db = JFactory::getDBO();

		// UserAgent
		$userAgent = NULL;
      	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ){
        	$userAgent = $_SERVER['HTTP_USER_AGENT'];
      	}

	   	// Filter
	   	$is_filter = FALSE ;

		// Filter Robots
		if ( $this->autoFilterRobots ) {
			$browser = JBrowser::getInstance();
			$is_filter = $browser->isRobot() ;
		}

		//
		$user = JFactory::getUser();

		// Filter Users
		// Test nur wenn nicht sowieso schon gefiltert wird.. langform
    //
		if ( $is_filter === FALSE ) {
			if ( $this->filterUsers > 0 ) {
				// to Filter what
				if ( ($this->filterUsers == 1) && ($user->guest) ) {
					// filter Guests
					$is_filter = TRUE ;
				}
				else if ( ($this->filterUsers == 2 ) && (!$user->guest) ) {
					// filter Members
					$is_filter = TRUE ;
				}
				// else can't be
			}
		}


		// Filter durch UserAgent
		// Test nur wenn nicht sowieso schon gefiltert wird.. langform
		if ( $is_filter === FALSE ) {
		   	if ( (!empty($userAgent)) && (!empty( $this->filterUserAgents )) ) {
		 	    //
				$userAgent = trim($userAgent);
			   	// max length
			   	if ( strlen($userAgent) > 1024 ) {
			   		$userAgent = substr( $userAgent, 0, 1023 );
			   	}
			   	// search Skiped UserAgents
			   	$arr_filterUserAgents = explode( ";", $this->filterUserAgents );
			   	foreach( $arr_filterUserAgents as $toFilterUA ){
			   	    if ( empty($toFilterUA) ) {
			   	    	continue;
			   	    }
				   	if ( strpos( $userAgent, $toFilterUA ) !== FALSE ) {
				   		$is_filter = TRUE ;
				   		break;
				   	}
			   	}
		   	}
	   	}

		// Filter durch IP
		// Test nur wenn nicht sowieso schon gefiltert wird.. langform
		if ( $is_filter === FALSE ) {
		   	if ( (!empty($this->callerIP)) &&
			     (!empty( $this->filterIPs )) ) {
			   	// search Skiped UserAgents
			   	$arr_filterIPs = explode( ";", $this->filterIPs );
			   	foreach( $arr_filterIPs as $toFilterIP ){
			   		if ( empty($toFilterIP) ) {
			   	    	continue;
			   	    }
			   		// geht von gleicher Schreibweise aus
				   	if ( $this->callerIP == $toFilterIP ) {
				   		$is_filter = TRUE ;
				   		break;
				   	}
			   	}
		   	}
		}

	   	// not filtered UserAgent then save
	   	// dies könnte man auch hier einbauen anstatt unten
		//  dann sehen diese kein MAX
	   	// if ( $is_filter === FALSE ) {

		// Check if table exists. When not, create it
        if ($this->s_trycreatetable) {
            $query = "CREATE TABLE IF NOT EXISTS " . $this->s_createtableName . "(id int(11) unsigned NOT NULL auto_increment, tm int not null, ip BINARY(20) not null, ipraw VARCHAR( 40 ) default NULL , userAgent varchar(1024) default NULL, data longtext, PRIMARY KEY (id), INDEX (tm), INDEX (ip) , INDEX iptm (ip,tm) ) ENGINE=MyISAM AUTO_INCREMENT=1";
            $this->db->setQuery($query);
            $this->db->query();
			if( $this->db->getErrorNum () ) {
				$e = $this->db->getErrorMsg();
				//print_r( $e );
				JError::raiseWarning( 500, $e );
				return;
			}
        }

        // MAX ID - Sizeof All Users
        // RECORDS all Real Entries
        $query = "SELECT count( * ) AS records, MAX( id ) AS max FROM " . $this->s_createtableName . "";
        $this->db->setQuery($query);
        $row = $this->db->loadRow();
		if( $this->db->getErrorNum () ) {
			$e = $this->db->getErrorMsg();
			//print_r( $e );
			JError::raiseWarning( 500, $e );
			return;
		}
		//print_r($row );
        $entries = $row[0];
        $this->all_visitors = $row[1];

        if ($this->all_visitors == null) {
            $this->all_visitors = $this->initialvalue ;
        } else {
            $this->all_visitors += $this->initialvalue ;
        }

	   	// not filtered UserAgent then save ( dennoch sollen sie max sehen )
	   	if ( $is_filter === FALSE ) {

	        // keinerlei Update,Delete, etc wenn read_only
	        if ( ! $this->read_only_counter ) {

    	        if ( $this->records_days > 0 ||
                     $this->show_highestVisitsDay > 0 ) {

	                // MEMORY - one table for all
		            // $query = "CREATE TABLE IF NOT EXISTS " . $this->s_createtableName . "memo (counter varchar(240) NOT NULL, lastMaxPerDay DATE NULL DEFAULT NULL , PRIMARY KEY (counter) ) ENGINE=MEMORY";
		            $query = "CREATE TABLE IF NOT EXISTS #__" .
                             $this->MODULE . "_memo (counter varchar(240) NOT NULL, lastMaxPerDay DATE NULL DEFAULT NULL , " .
                             " valMaxVisits INT NULL DEFAULT NULL, valMaxVisitsDay int NULL DEFAULT NULL, lastMaxVisits DATETIME NULL , PRIMARY KEY (counter) ) ENGINE=MEMORY";
					$this->db->setQuery($query);
		            $this->db->query();
					if( $this->db->getErrorNum () ) {
						$e = $this->db->getErrorMsg();
						//print_r( $e );
						JError::raiseWarning( 500, $e );
						return;
					}

    	        }

                //
	            // wenn eingeschalten, behalte nur X Tage Var 3 !
	            // dies wird aber nur einmal pro Tag getan
	            if ( $this->records_days > 0 ) {

                    //
		            $query = "INSERT INTO #__" . $this->MODULE . "_memo ( counter ) values ( '" . $this->s_createtableName_raw . "' ) ON DUPLICATE KEY UPDATE counter='" . $this->s_createtableName_raw . "' ";
					$this->db->setQuery($query);
		            $this->db->query();
          if( $this->db->getErrorNum () ) {
						$e = $this->db->getErrorMsg();
						//print_r( $e );
						JError::raiseWarning( 500, $e );
						return;
					}

                // halte das Datum (einmal pro Tag) in einer Temp Table

            // $query = "select lastMaxPerDay, curdate() as cur FROM " . $this->s_createtableName . "memo where counter = '" . $this->s_createtableName_raw . "'";
            $query = "select lastMaxPerDay, DATEDIFF( curdate(), lastMaxPerDay ) as ddiff FROM #__" . $this->MODULE . "_memo where counter = '" . $this->s_createtableName_raw . "'";
            $this->db->setQuery($query);
            $lastDeletedRes = $this->db->loadObject();
						if( $this->db->getErrorNum () ) {
							$e = $this->db->getErrorMsg();
							//print_r( $e );
							JError::raiseWarning( 500, $e );
							return;
						}

					$lastDelDiff = 1; // immer unterschied

					if ( !empty($lastDeletedRes) && !empty($lastDeletedRes->ddiff) ) {
    					$lastDelDiff = $lastDeletedRes->ddiff ;
					}

					// echo $lastDelDiff . " / " . $lastDeletedRes->lastMaxPerDay . " / " . $lastDeletedRes->ddiff ;
                    // echo $lastDelDiff;

					// nur wenn Diff >= 1 Tag
					if ( $lastDelDiff > 0 ) {

						// zuerst wg Sperre
						// $query = "UPDATE " . $this->s_createtableName . "memo set lastMaxPerDay = CURDATE() WHERE counter = '" . $this->s_createtableName_raw . "'";
			            $query = "UPDATE #__" . $this->MODULE . "_memo set lastMaxPerDay = CURDATE() WHERE counter = '" . $this->s_createtableName_raw . "'";
						$this->db->setQuery($query);
			            $this->db->query();
						if( $this->db->getErrorNum () ) {
							$e = $this->db->getErrorMsg();
							//print_r( $e );
							JError::raiseWarning( 500, $e );
							return;
						}

		             	// Var 1 $query = "DELETE FROM " . $this->s_createtableName . " WHERE from_unixtime(tm) < DATE_SUB( NOW(), INTERVAL " . $this->records_days . " DAY )"; // rechnet von heute genaue Zeit zurück
		             	// Var 2 $query = "DELETE FROM " . $this->s_createtableName . " WHERE DATEDIFF( NOW() , from_unixtime(tm) ) > " . $this->records_days ; // rechnet von heute Tageweise zurück
		             	// Var 3 wir behalten wirklich x Tage in der Datenbank, unabhängig von heute
		             	//  Leider braucht MySQL eine Workaround beim nutzen von SubQuerys in DELETE
		             	//  hier muss die Performance getetstet werden, da temp-table angelegt wird
		             	$query = "DELETE FROM " . $this->s_createtableName .
						         " WHERE " .
									"DATE(from_unixtime(tm)) <=	(" .
									  " SELECT toDEL FROM (" .
									  "   SELECT DATE_SUB(" .
									  "     MAX( DISTINCT DATE( from_unixtime(tm) ) ) , " .
									  "     INTERVAL " . $this->records_days  . " DAY ) AS toDel " .
									  "   FROM " . $this->s_createtableName . " AS theCore " .
									  ") AS workAround" .
									")";
			            $this->db->setQuery($query);
			            $this->db->query();
			            if( $this->db->getErrorNum () ) {
			   	            $e = $this->db->getErrorMsg();
			   	            //print_r( $e );
			   	            JError::raiseWarning( 500, $e );
							// todo delete mem table
			   	            return;
			            }

						// Optimize
						//$affectedRows   = mysql_affected_rows();
						$affectedRows   = $this->db->getAffectedRows();
						if ( $affectedRows > 1500 ) {
							// echo "-- OPTIMIZE -- " . $affectedRows ;
			             	$query = "OPTIMIZE TABLE  " . $this->s_createtableName ;
							$this->db->setQuery($query);
			            	$this->db->query();
						}

					}

	            }

			    // Delete old records wenn eingeschalten
	            $temp = $this->all_visitors - $this->records - $this->initialvalue ;
				//print_r($temp);
	            if ($this->records > 0  && $temp >= 0 ) {
	            	// should delete
	            	// nicht löschen wenn reale Anzahl kleiner
	            	if ( $entries >= $this->records ) {
		                $query = "DELETE FROM " . $this->s_createtableName . " WHERE id<='$temp'";
		                $this->db->setQuery($query);
		                $this->db->query();
		                if( $this->db->getErrorNum () ) {
		   	                $e = $this->db->getErrorMsg();
		   	                //print_r( $e );
		   	                JError::raiseWarning( 500, $e );
		   	                return;
		                }

       					// Optimize
						// $affectedRows   = mysql_affected_rows();
						$affectedRows   = $this->db->getAffectedRows();
						if ( $affectedRows > 1500 ) {
							// echo "-- OPTIMIZE -- " . $affectedRows ;
			             	$query = "OPTIMIZE TABLE  " . $this->s_createtableName ;
							$this->db->setQuery($query);
			            	$this->db->query();
						}

	            	}
	            }

	            // IP and now
	            // $ip = $_SERVER['REMOTE_ADDR'];
	            $ip = $this->callerIP ;

	            // User is count as new by Locktime or Daily
	            if ( $this->countUserAsNewType == 1 ) {
	            	// Daily

					$query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
                             // " WHERE ip=UNHEX(SHA1('$ip')) AND ( DATE(FROM_UNIXTIME(tm)) = CURDATE() ) ";
                             " WHERE ip=UNHEX(SHA1('$ip')) AND" .
                             " tm BETWEEN UNIX_TIMESTAMP(DATE(NOW())) AND UNIX_TIMESTAMP(DATE(NOW()) + INTERVAL 1 DAY)";
	            }
	            else {
	            	// By Locktime

					if ( $this->locktime <= 0 ) {
						// keine Abfrage - HitCounter - PerfImprovement
						$query = NULL; // simulate Erg
					}
					else {
	            		// Now we are checking if the ip was logged in the database. Depending of the value in minutes in the locktime variable.
	            		$query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
                                 " WHERE ip=UNHEX(SHA1('$ip')) AND ( ((unix_timestamp(NOW()) - tm ) / 60 ) < '$this->locktime' ) ";
            		}

	            }

				// keine oder Abfrage
				if ( !empty($query) ) {
					$this->db->setQuery($query);
	            	$items = $this->db->loadResult();
					if( $this->db->getErrorNum () ) {
						$e = $this->db->getErrorMsg();
						//print_r( $e );
						JError::raiseWarning( 500, $e );
						return;
					}
				}

				//
				$add_data = 'NULL';
				// add anon if a user
				if ( !$user->guest ) {
    				// später evt auslagern
				    $val_arr = array( 'user' => TRUE );
				    //$arr["otherkey"] = otherval;
				    $add_data = $this->db->Quote( serialize($val_arr) ); // we need 'value'
				}

	            if (empty($items)) {
			   		// nicht enthalten in AusschlussListe
			   		if ( $this->saveUserAgents ) {
			   		    // do not  $this->db->Quote( $add_data ) !
    			   		if ( empty($this->s_ipAlsoInRaw) ) {
    			   		    $query = "INSERT INTO " . $this->s_createtableName . " ( tm, ip, userAgent, data ) VALUES ( unix_timestamp(NOW()) , UNHEX(SHA1('$ip')), " . $this->db->Quote( $userAgent ) . ", " . $add_data . " )";
    			   		}
    			   		else {
        			   		$query = "INSERT INTO " . $this->s_createtableName . " ( tm, ip, ipraw, userAgent, data ) VALUES ( unix_timestamp(NOW()) , UNHEX(SHA1('$ip')), '$ip' , " . $this->db->Quote( $userAgent ) . ", " . $add_data . " )";
                        }
			   		}
			   		else {
    			   		if ( empty($this->s_ipAlsoInRaw) ) {
    			 		   $query = "INSERT INTO " . $this->s_createtableName . " ( tm, ip, data ) VALUES ( unix_timestamp(NOW()) , UNHEX(SHA1('$ip')) , " . $add_data. " )";
    			 		}
    			 		else {
                           $query = "INSERT INTO " . $this->s_createtableName . " ( tm, ip, ipraw, data ) VALUES ( unix_timestamp(NOW()) , UNHEX(SHA1('$ip')) , '$ip' , " . $add_data . " )";
                        }
					}
                  $this->db->setQuery($query);
	                $this->db->query();
	                if( $this->db->getErrorNum () ) {
	   	                $e = $this->db->getErrorMsg();
	   	                //print_r( $e );
	   	                JError::raiseWarning( 500, $e );
	   	                return;
	                }

                  $this->isInserted = TRUE;

                  // mit +1 kann u.U. all_visitors an der Stelle nicht uptodate
                  // sein. Das ist in Anzeigen ok und spart eine Abfrage.
                  // Bei den Verwendung der Trigger usw. soll die Zahl aber
        					// immer korrekt sein

               		// ein Trigger aktiv ? todo methode isAktivTrigger
        					if ( ! $this->mail_sending == 0
        						 // || other Trigger aktiv
        					) {

        						// MAX ID - Sizeof All Users
        				        $query = "SELECT MAX( id ) AS max FROM " . $this->s_createtableName . "";
        				        $this->db->setQuery($query);
        				        $row = $this->db->loadRow();
        						if( $this->db->getErrorNum () ) {
        							$e = $this->db->getErrorMsg();
        							//print_r( $e );
        							JError::raiseWarning( 500, $e );
        							return;
        						}
        						//print_r($row );
        				        $this->all_visitors = $row[0];
        				        $this->all_visitors += $this->initialvalue ;

        					}
        					else {

        						// must be
        		                $this->all_visitors += 1;

        					}

				}


                // wenn eingeschalten Highest User Day in Stat
                if ( $this->show_highestVisitsDay > 0 ) {

                    //
		            $query = "INSERT INTO #__" . $this->MODULE . "_memo ( counter ) values ( '" . $this->s_createtableName_raw . "' ) ON DUPLICATE KEY UPDATE counter='" . $this->s_createtableName_raw . "' ";
					$this->db->setQuery($query);
		            $this->db->query();
					if( $this->db->getErrorNum () ) {
						$e = $this->db->getErrorMsg();
						//print_r( $e );
						JError::raiseWarning( 500, $e );
						return;
					}

                    //
                    $lastUpdDiff = $this->highestVisitsDayRecalcSec; // immer unterschied

                    if ( $this->highestVisitsDayRecalcSec > 0 ) {

    		            $query = "select TIMESTAMPDIFF( SECOND, lastMaxVisits, now() ) as ddiff FROM #__" . $this->MODULE . "_memo where counter = '" . $this->s_createtableName_raw . "'";
    		            $this->db->setQuery($query);
    		            $lastUpdMaxVisits = $this->db->loadObject();
    					if( $this->db->getErrorNum () ) {
    						$e = $this->db->getErrorMsg();
    						//print_r( $e );
    						JError::raiseWarning( 500, $e );
    						return;
    					}

                     	if ( !empty($lastUpdMaxVisits) && !empty($lastUpdMaxVisits->ddiff) ) {
    						$lastUpdDiff = $lastUpdMaxVisits->ddiff ;
    					}
                    }

                    // echo $lastUpdDiff . " / " . $this->highestVisitsDayRecalcSec ;
                    if ( $lastUpdDiff >= $this->highestVisitsDayRecalcSec ) {
                        // recalc and insert maxVisits Value's
                        $query = "SELECT date( from_unixtime( tm ) ) tag, tm tagut, count(*) c FROM " . $this->s_createtableName .
                                 " GROUP BY tag HAVING c = ( " .
                                 "   SELECT MAX( inn.cc ) FROM ( SELECT date(from_unixtime( tm )) tag, count(*) cc FROM " . $this->s_createtableName .
                                 "     GROUP BY tag ) AS inn )";
    		            $this->db->setQuery($query);
    		            $selMaxVisDay = $this->db->loadObject();
    					if( $this->db->getErrorNum () ) {
    						$e = $this->db->getErrorMsg();
    						//print_r( $e );
    						JError::raiseWarning( 500, $e );
    						return;
    					}
    					// es kann kein Tag ermittelt werden
        				// echo " -:" . date( $this->date_day_fmt , $selMaxVisDay->tagut ) . " / " . $selMaxVisDay->c ;
                        // in local Vars
                        $this->maxVisitsOnDayArr = array();
                        if ( !empty($selMaxVisDay) ) {
                            $this->maxVisitsOnDayArr[0] = $selMaxVisDay->c;
                            $this->maxVisitsOnDayArr[1] = date( $this->date_day_fmt , $selMaxVisDay->tagut );
                            //
    			            $query = "UPDATE #__" . $this->MODULE . "_memo set" .
                                     " lastMaxVisits = now() ," .
                                     " valMaxVisitsDay = " . $selMaxVisDay->tagut . " ," .
                                     " valMaxVisits = " . $selMaxVisDay->c .
                                     " WHERE counter = '" . $this->s_createtableName_raw . "'";
    						$this->db->setQuery($query);
    			            $this->db->query();
    						if( $this->db->getErrorNum () ) {
    							$e = $this->db->getErrorMsg();
    							//print_r( $e );
    							JError::raiseWarning( 500, $e );
    							return;
    						}
                        }
                        else {
                            $this->maxVisitsOnDayArr[0] = $this->all_visitors;
                            $this->maxVisitsOnDayArr[1] = date( $this->date_day_fmt , time() );
                            //
    			            $query = "UPDATE #__" . $this->MODULE . "_memo set" .
                                     " lastMaxVisits = now() ," .
                                     " valMaxVisitsDay = " . time() . " ," .
                                     " valMaxVisits = " . $this->all_visitors .
                                     " WHERE counter = '" . $this->s_createtableName_raw . "'";
    						$this->db->setQuery($query);
    			            $this->db->query();
    						if( $this->db->getErrorNum () ) {
    							$e = $this->db->getErrorMsg();
    							//print_r( $e );
    							JError::raiseWarning( 500, $e );
    							return;
    						}
                        }
                    }
                }
          }
        }
        // else {
			// DEBUG
			// echo "Filtered : " . $is_filter;
		// }

		// soll etwas angezeigt werden das IPtoCountry braucht ?
		if ( $this->s_ipCflag || $this->s_ipCcode || $this->s_ipCountry ) {
			// create IPtoCountry Instance
			$this->ipToCountry = new IPtoCountry($params);
			/* TestCase
			$code = $ipToCountry->getISOAlphaZwei("85.233.46.218");
			//$code = $ipToCountry->getISOAlphaZwei("127.0.0.1");
			print_r($code);
            $imgTest .= $this->isoZweiToFlagImg(
			    $code ,
                JURI::base() . 'modules/' .
                // TODO_CO_MOD modVisitCounterHelper::MODULE
                $this->MODULE . '/images/flags/',
				'mvc_flagImage' ,
				20,
				13);
			print_r($imgTest);
			*/
		}

        // Users
        if ( $this->show_loggedinUserCount ) {
            // logged in users
            $this->sql_loggedinUserCount();
        }
        if ( $this->show_loggedinUserNamens ) {
            // logged in user namens
            $this->sql_loggedinUserNamens();
        }
        if ( $this->show_guestsCount ) {
            // guests
            $this->sql_guestsCount();
        }
        if ( $this->show_registeredUserCount ) {
            // guests
            $this->sql_registeredUserCount();
        }
        if ( $this->show_registeredUserTodayNames ) {
            // reg today user namens
            $this->sql_renderRegisteredTodayUserNamens();
        }
        if ( $this->show_registeredUserCountToday ) {
            // guests
            $this->sql_registeredUserCountToday();
        }
        if ( $this->show_highestVisitsDay ) {
            // max Users an Tag
            $this->sql_highestVisitsDay();
        }

	    // Paren von Pre and Post und ersetzen
		$this->replacePREANDPOSTREPLACES( $this->pretext , $params );
		$this->replacePREANDPOSTREPLACES( $this->posttext , $params  );

        // Example
        // $content = '<div>';
        // Show pre
        // $content .= $this->renderPRE();
        // Show digit counter
        // $content .= $this->renderDigitCounter();
        // show counts ( Table - PeopleImg, Text, Digit )
        // $content .= $this->renderPeopleTable();
        // Show Image
        // $content .= $this->renderStatistikImage();
        // post
        // $content .= $this->renderPOST();
        // $content = '</div>';
        // return $content;


	/**
	    So.. the Triggers fired before render the views !
	    All Outputs are in before the Counter View and
		 ! before ! some Initialising. So you need to use the sql_X Methods !
		An todo can be to check wheter is better to fire the Triggers
		after the Counter is rendered, so in the default.php. Then is needed
		a method to ask isInserted.
	*/

		// Triggers: Sending Mail / Trigger Script
		if ( $this->isInserted === TRUE ) {

			$this->invokeMailSend();

			$this->invokeExternalTriggerScript();

		}

    }

    /**
     *
     */
	private function sql_highestVisitsDay(){
	    if ( isset($this->maxVisitsOnDayArr) ) {
	    	return $this->maxVisitsOnDayArr;
	    }
        $query = "select valMaxVisits val, valMaxVisitsDay day FROM #__" . $this->MODULE . "_memo where counter = '" . $this->s_createtableName_raw . "'";
        $this->db->setQuery($query);
        $selMaxVisDay = $this->db->loadObject();
		if( $this->db->getErrorNum () ) {
			$e = $this->db->getErrorMsg();
			//print_r( $e );
			JError::raiseWarning( 500, $e );
			return null;
		}
        $this->maxVisitsOnDayArr = array();
        $this->maxVisitsOnDayArr[0] = ( $selMaxVisDay->val === NULL ?
                                     $this->all_visitors : $selMaxVisDay->val );
        $this->maxVisitsOnDayArr[1] = ( $selMaxVisDay->day === NULL ?
                                    date( $this->date_day_fmt , time() ) :
                             date( $this->date_day_fmt , $selMaxVisDay->day ) );
        return $this->maxVisitsOnDayArr;
	}

    /**
     *
     */
	private function sql_today_visitors(){
	    if ( isset($this->today_visitors) ) {
	    	return $this->today_visitors;
	    }
	    $query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
               " WHERE tm BETWEEN UNIX_TIMESTAMP(DATE(NOW())) AND UNIX_TIMESTAMP(DATE(NOW()) + INTERVAL 1 DAY)";
               // " WHERE (YEAR(NOW())= YEAR(FROM_UNIXTIME(tm))) AND (DAYOFYEAR(NOW())=DAYOFYEAR(FROM_UNIXTIME(tm)))" ;
        $this->db->setQuery($query);
        $this->today_visitors = $this->db->loadResult();
        return $this->today_visitors;
	}

	/**
     *
     */
	private function sql_yesterday_visitors(){
	    if ( isset($this->yesterday_visitors) ) {
	    	return $this->yesterday_visitors;
	    }
	    $query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
               " WHERE tm BETWEEN UNIX_TIMESTAMP(DATE(NOW() - INTERVAL 1 DAY)) AND UNIX_TIMESTAMP(DATE(NOW()))" ;
               // " WHERE (YEAR(NOW())= YEAR(FROM_UNIXTIME(tm))) AND (DAYOFYEAR(FROM_UNIXTIME(tm))=DAYOFYEAR(now())-1)" ;
        $this->db->setQuery($query);
        $this->yesterday_visitors = $this->db->loadResult();
        return $this->yesterday_visitors;
	}

	/**
     *
     */
	private function sql_week_visitors(){
	    if ( isset($this->week_visitors) ) {
	    	return $this->week_visitors;
	    }
    	/*
    	$week_mode = $this->getWeekMode();
    	if ( $week_mode == -1 ) {
    		// Saturday .. special because no MySQL Mode for Saturday
			$query = "SELECT COUNT(*) FROM " . $this->s_createtableName . " WHERE ( YEAR(NOW())= YEAR(FROM_UNIXTIME(tm)) ) AND ( WEEK( DATE_ADD(NOW(), INTERVAL 1 DAY) , 6 ) = WEEK( DATE_ADD(FROM_UNIXTIME(tm), INTERVAL 1 DAY), 6))";
    	}
    	else {
		    // Monday and Sonnday direkt with MySQL Mode
        	$query = "SELECT COUNT(*) FROM " . $this->s_createtableName . " WHERE (YEAR(NOW())= YEAR(FROM_UNIXTIME(tm))) AND (WEEK(NOW()," . $week_mode . ")=WEEK(FROM_UNIXTIME(tm)," . $week_mode . "))" ;
       	}
        */

		$ft = array();
		$fmt = "d.m.Y";
		if ( $this->s_week_startday == 0 ) {
			// SUNDAY
			$ft = $this->getWeekFromTo( time() , 'Sunday' , $fmt) ;
		}
		else if ( $this->s_week_startday == 2 ) {
			// SATURDAY
			$ft = $this->getWeekFromTo( time() , 'Saturday' , $fmt) ;
		}
		else {
			// MONDAY
			$ft = $this->getWeekFromTo( time() , 'Monday' , $fmt) ;
		}
        // $ft["from"] . ' - ' . $ft["to"];

        $query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
                 " WHERE tm BETWEEN " . strtotime($ft["from"]) .
                     " AND " . strtotime( '+1 day' . $ft["to"])  ;

        $this->db->setQuery($query);
        $this->week_visitors = $this->db->loadResult();
        return $this->week_visitors;
	}

	/**
     *
     */
	private function sql_month_visitors(){
	    if ( isset($this->month_visitors) ) {
	    	return $this->month_visitors;
	    }
    	$query = "SELECT COUNT(*) FROM " . $this->s_createtableName .
               ' WHERE tm BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-01")) AND UNIX_TIMESTAMP(DATE_FORMAT(NOW(), "%Y-%m-01") + INTERVAL 1 MONTH)' ;
               // " WHERE (YEAR(NOW())=YEAR(FROM_UNIXTIME(tm))) AND (MONTH(NOW())=MONTH(FROM_UNIXTIME(tm)))" ;
        $this->db->setQuery($query);
        $this->month_visitors = $this->db->loadResult();
        return $this->month_visitors;
	}

	/**
     *
     */
	private function sql_min_Date( $format = "%d.%m.%Y" ){
	    if ( isset($this->min_Date) ) {
	    	return $this->min_Date;
	    }
    	$query = "SELECT DATE_FORMAT( FROM_UNIXTIME( MIN(tm) ) , '" . $format . "' ) FROM " . $this->s_createtableName ;
        $this->db->setQuery($query);
        $this->min_Date = $this->db->loadResult();
        return $this->min_Date;
	}

    /**
     *
     */
	private function sql_loggedinUserCount(){
	    if ( isset($this->loggedinUserCount) ) {
	    	return $this->loggedinUserCount;
	    }
		// logged in users
		// $query = 'SELECT COUNT( session_id ) FROM #__session WHERE userid != 0';
		// nur noch eindeutige User zählen, Doppelanmeldungen etc ignoroeren
		$query = 'SELECT COUNT( distinct( userid ) ) FROM #__session WHERE userid != 0';
		if ( $this->show_UsersCountOnlyFrontend ) {
			$query = $query . ' and client_id = 0' ;
		}
		// check Inaktive Time
		if ( $this->userInactivTime > 0 ) {
			$query = $query . ' and (UNIX_TIMESTAMP(now()) - time ) <= ' . $this->userInactivTime ;
		}

		$this->db->setQuery($query);
		$this->loggedinUserCount = intval( $this->db->loadResult() );
		return $this->loggedinUserCount;
	}

    /**
     * Array (
     * [0] => Array ( [id] => id [uname] => username [name] => name )
     * ...
     * )
     */
	private function sql_loggedinUserNamens(){
	    if ( isset($this->loggedinUserNamens) ) {
	    	return $this->loggedinUserNamens;
	    }

	    if ( isset($this->loggedinUserCount) &&
			 $this->loggedinUserCount < 1 ) {
	    	return NULL;
	    }

		// logged in user Namens
		$query = "SELECT s.userid id, u.username uname , u.name name FROM #__session s " .
                 "INNER JOIN #__users u ON s.userid = u.id " ;

        //:OnlyJ16 need only this groups
		if ( !empty( $this->onlyGroupsN_Flag ) ) {

		    if ( empty($this->onlyGroupsN) ) {
		        $this->onlyGroupsN = array();
		    }

		    $query = $query .
                ' INNER JOIN #__user_usergroup_map m ON s.userid = m.user_id ' .
                ' AND m.group_id in (' . implode( "," , $this->onlyGroupsN ) . ') ';
		}

        //
        $query = $query . ' WHERE userid != 0';

		if ( $this->show_UsersCountOnlyFrontend ) {
			$query = $query . ' and client_id = 0' ;
		}
		// check Inaktive Time
		if ( $this->userInactivTime > 0 ) {
			$query = $query . ' and (UNIX_TIMESTAMP(now()) - time ) <= ' . $this->userInactivTime ;
		}
		// unique ( above with distinct )
		$query = $query . ' GROUP BY id ORDER BY id' ;
		$this->db->setQuery($query);
		// as array
		$this->loggedinUserNamens = $this->db->loadAssocList();
		return $this->loggedinUserNamens;

	}

    /**
     * Array (
     * [0] => Array ( [id] => id [uname] => username [name] => name )
     * ...
     * )
     */
	private function sql_renderRegisteredTodayUserNamens(){
	    if ( isset($this->registeredUserTodayNames) ) {
	    	return $this->registeredUserTodayNames;
	    }

	    if ( isset($this->registeredUserCountToday) &&
			 $this->registeredUserCountToday < 1 ) {
	    	return NULL;
	    }

		// user Namens
		$query = "SELECT DISTINCT(id) id, username uname, name name FROM #__users u ";

        //
        //:OnlyJ16 need only this groups
		if ( !empty( $this->onlyGroupsN_Flag ) ) {

		    if ( empty($this->onlyGroupsN) ) {
		        $this->onlyGroupsN = array();
		    }

		    $query = $query .
                ' INNER JOIN #__user_usergroup_map m ON u.id = m.user_id ' .
                ' AND m.group_id in (' . implode( "," , $this->onlyGroupsN ) . ') ';
		}

        $query = $query . " WHERE block = 0 and ( DATE( registerDate ) = CURDATE() )";
		/*
		if ( $this->show_UsersCountOnlyFrontend ) {
			$query = $query .
		}
		*/
		$query = $query . 'ORDER BY id' ;
		$this->db->setQuery($query);
		// as array
		$this->registeredUserTodayNames = $this->db->loadAssocList();
		return $this->registeredUserTodayNames;

	}

    /**
     *
     */
	private function sql_guestsCount(){
	    if ( isset($this->guestsCount) ) {
	    	return $this->guestsCount;
	    }
    	// logged in users
		$query = 'SELECT COUNT(*) FROM #__session WHERE guest = 1';
		if ( $this->show_UsersCountOnlyFrontend == 1 ) {
			$query = $query . ' and client_id = 0' ;
		}
		// check Inaktive Time
		if ( $this->guestInactivTime > 0 ) {
			$query = $query . ' and (UNIX_TIMESTAMP(now()) - time ) <= ' . $this->guestInactivTime ;
		}
		$this->db->setQuery($query);
		$this->guestsCount = intval( $this->db->loadResult() );

		/**
		 * TODO :: Find a way to parse Session.data an do a new Parameter for
		 * Filter Bots ( use also FilterUserAgent Parameter )
		 **/

		return $this->guestsCount;
	}

    /**
     *
     */
	private function sql_registeredUserCount(){
	    if ( isset($this->registeredUserCount) ) {
	    	return $this->registeredUserCount;
	    }
		// users
		$query = 'SELECT COUNT(*) FROM #__users WHERE block = 0';
		/* TODO keine SuperAdmins ???
		if ( $this->show_UsersCountOnlyFrontend ) {
			$query = $query . ' and gid != 25' ;
		}
		*/
		$this->db->setQuery($query);
		$this->registeredUserCount = intval( $this->db->loadResult() );
		return $this->registeredUserCount;

	}

    /**
     *
     */
	private function sql_registeredUserCountToday(){
	    if ( isset($this->registeredUserCountToday) ) {
	    	return $this->registeredUserCountToday;
	    }
		// users
		$query = 'SELECT COUNT(*) FROM #__users WHERE block = 0 and ( DATE( registerDate ) = CURDATE() )';
		/* TODO keine SuperAdmins ???
		if ( $this->show_UsersCountOnlyFrontend ) {
			$query = $query . ' and gid != 25' ;
		}
		*/
		$this->db->setQuery($query);
		$this->registeredUserCountToday = intval( $this->db->loadResult() );
		return $this->registeredUserCountToday;

	}


	/**
 	 * ersetzt den String mit den PRE_POST_REPLACES
	 */
    private function replacePREANDPOSTREPLACES( &$replacing, $params ){

        if ( !isset($replacing) || empty($replacing) ) {
        	// do nothing
			return ;
        }


		foreach ( $this->PRE_AND_POST_REPLACES as $value ) {

                $browser = NULL;
                $phpbrowser = NULL;
				$contains = strpos($replacing , $value);
				if ( $contains !== false ) {

	                if ( $value == '$today$' ) {
					   $replacing = str_replace( $value,
					                             "" . date( $this->date_day_fmt ) ,
												 $replacing );
					}
					else if ( $value == '$countAll$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->all_visitors,
												 $replacing );
					}
					else if ( $value == '$countToday$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->sql_today_visitors(),
												 $replacing );
					}
					else if ( $value == '$countYesterday$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->sql_yesterday_visitors(),
												 $replacing );
					}
					else if ( $value == '$countWeek$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->sql_week_visitors(),
												 $replacing );
					}
					else if ( $value == '$countMonth$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->sql_month_visitors(),
												 $replacing );
					}
					else if ( $value == '$minDate$' ) {
					   $replacing = str_replace( $value,
					                             "" . date(
												       $this->date_day_fmt,
													   strtotime( $this->sql_min_Date( "%d.%m.%Y" ) ) ),
												 $replacing );
					}
	                else if ( $value == '$callerIP$' ) {
					   $replacing = str_replace( $value,
					                             "" . $this->callerIP,
												 $replacing );
					}
	                else if ( $value == '$callerCountryCode$' ) {
	                   if ( !isset($this->ipToCountry) ) {
	                   		$this->ipToCountry = new IPtoCountry($params);
	                   }
					   $replacing = str_replace( $value,
					                             "" . $this->ipToCountry->getISOAlphaZwei($this->callerIP),
												 $replacing );
					}
	                else if ( $value == '$callerCountry$' ) {
	                   if ( !isset($this->ipToCountry) ) {
	                   		$this->ipToCountry = new IPtoCountry($params);
	                   }
					   $replacing = str_replace( $value,
					                             "" . $this->ipToCountry->getCountry($this->callerIP),
												 $replacing );
					}
	                else if ( $value == '$callerCountryFlag$' ) {
	                   if ( !isset($this->ipToCountry) ) {
	                   		$this->ipToCountry = new IPtoCountry($params);
	                   }

					   $code = $this->ipToCountry->getISOAlphaZwei($this->callerIP);

					   // make an empty check
				       $img = $this->isoZweiToFlagImg(
						    $code,
				            // JURI::base() . 'modules/' .
				            $this->baseurl . 'modules/' .
				            // TODO_CO_MOD modVisitCounterHelper::MODULE
				            $this->MODULE . '/images/flags/',
							'mvc_ipflagimg' ,
							$this->s_ipCflag_width,
							$this->s_ipCflag_height );

					   $replacing = str_replace( $value,
					                             $img,
												 $replacing );
					}
	                else if ( $value == '$userAgent$' ) {

						$userAgent = "";
				      	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ){
				        	$userAgent = $_SERVER['HTTP_USER_AGENT'];
  						   	if ( strlen($userAgent) > 1024 ) {
						   		$userAgent = substr( $userAgent, 0, 1023 );
						   	}
					   	}
					    $replacing = str_replace( $value,
					                              "" . $userAgent,
						  					      $replacing );
					}
	                else if ( $value == '$browser$' ) {

						// beachte php_browscap.ini
						// print_r( get_browser(NULL,true) ) ;
						if( empty($browser) ){
							$browser = JBrowser::getInstance() ;
							//$browser = get_browser();
						}
					    $replacing = str_replace( $value,
					                              $browser->getBrowser(),
					                              //"" . $browser->browser,
						  					      $replacing );
					}
	                else if ( $value == '$versionBrowser$' ) {

						// beachte php_browscap.ini
						if( empty($browser) ){
							$browser = JBrowser::getInstance() ;
							//$browser = get_browser();
						}
						$replacing = str_replace( $value,
						                          $browser->getVersion(),
						                          //"" . $browser->version,
												  $replacing );
					}
	                else if ( $value == '$platformBrowser$' ) {

						// beachte php_browscap.ini
						if( empty($browser) ){
							$browser = JBrowser::getInstance() ;
							//$browser = get_browser();
						}
						$replacing = str_replace( $value,
						                          $browser->getPlatform(),
						                          // "" . $browser->platform,
												  $replacing );
					}


	                else if ( $value == '$phpbrowser$' ) {

						// beachte php_browscap.ini
						// print_r( get_browser(NULL,true) ) ;
						if( empty($phpbrowser) ){
							//$browser = JBrowser::getInstance() ;
							$phpbrowser = get_browser();
						}
					    $replacing = str_replace( $value,
					                              //$browser->getBrowser(),
					                              "" . $phpbrowser->browser,
						  					      $replacing );
					}
	                else if ( $value == '$phpversionBrowser$' ) {

						// beachte php_browscap.ini
						if( empty($phpbrowser) ){
							//$browser = JBrowser::getInstance() ;
							$phpbrowser = get_browser();
						}
						$replacing = str_replace( $value,
						                          //$browser->getVersion(),
						                          "" . $phpbrowser->version,
												  $replacing );
					}
	                else if ( $value == '$phpplatformBrowser$' ) {

						// beachte php_browscap.ini
						if( empty($phpbrowser) ){
							//$browser = JBrowser::getInstance() ;
							$phpbrowser = get_browser();
						}
						$replacing = str_replace( $value,
						                          //$browser->getPlatform(),
						                          "" . $phpbrowser->platform,
												  $replacing );
					}
	                else if ( $value == '$loggedIn$' ) {
						$replacing = str_replace( $value,
						                          "" . $this->sql_loggedinUserCount(),
												  $replacing );
					}
	                else if ( $value == '$guests$' ) {
						$replacing = str_replace( $value,
						                          "" . $this->sql_guestsCount(),
												  $replacing );
					}
	                else if ( $value == '$regUsers$' ) {
						$replacing = str_replace( $value,
						                          "" . $this->sql_registeredUserCount(),
												  $replacing );
					}
	                else if ( $value == '$regUsersToday$' ) {
						$replacing = str_replace( $value,
						                          "" . $this->sql_registeredUserCountToday(),
												  $replacing );
					}
	               else if ( $value == '$visitors$' ) {
						$replacing = str_replace( $value,
						                          "" .
								( intval($this->sql_loggedinUserCount()) +
								  intval($this->sql_guestsCount()) ) ,
												  $replacing );
					}


                }
        }

	}


	/**
	 * liefert CallerIP Abhängig aus REMOTE_ADDR oder aus HTTP_X_FORWARDED_FOR
	 *
	 * @param bool $preferForwarder Prefer HTTP_X_FORWARDED_FOR
	 * @return IP as string
	**/
    private function parseCallerIP( $preferForwarder = FALSE ){

		//
		$ip = "";
		// from HTTP_X_FORWARDED_FOR
		if ( $preferForwarder ) {
		  if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		    $ip = @trim( @end( @explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] )));
		  }
		}
		// check and/or get
		if( empty($ip) ) {
		  $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;

	}

	/**
	 * Sending EMail Depend on Configuration
	 */
	private function invokeMailSend()
	{

		// on/off
		if ( $this->mail_sending == 0 ) {
			return ;
		}
		// alle x
		if ( ($this->all_visitors % $this->mail_rate) !== 0 ) {
			// Kein Senden
			return;
		}

		// Parse Users
		$arr_userids = explode( ";", $this->mail_userIds );
		if( empty( $arr_userids ) ){
			return;
		}

		//
		jimport( 'joomla.mail.mail' );

		/* To Admin
		// Application Referenz laden
		$app = JFactory::getApplication();
		// Empfänger setzen
		$mail->addRecipient( $app->getCfg( 'mailfrom' ) );
		*/

		// Subject
		$subject = $this->MODULE . ' - ' . $this->s_createtableName_raw;
		// Parse Body
		$body_in = $this->mail_body ;
		$body_in = str_replace( '$a', $this->all_visitors  , $body_in );
		$body_in = str_replace( '$d',
		                         date( $this->mail_datetime_fmt, time() ),
								 $body_in );

		// Mails an User
		for ( $i=0; $i < count($arr_userids) ; $i++){

			// JMail Instanz laden
			$mail = JFactory::getMailer();
			if( empty( $mail ) ){
				// TODO Warnung ?
				return;
			}

			// User Mail if User ok
			$user = JFactory::getUser( $arr_userids[$i] );
			if ( empty($user) ||
			     empty($user->email) ||
				 $user->block > 0 ) {
				continue;
			}

			//
			$mail->addRecipient( $user->email );
			// Den Betreff setzen
			$mail->setSubject( $subject  );
			// Den Mailtext setzen
			$mail->setBody( $body_in );
			// Email(s) absenden - alle zusammen
			$mail->Send();
			/*
			if ( $mail->Send() !== TRUE ) {
				// TODO catch JError ??
			  	// echo "Email konnte nicht versendet werden.";
			}
			*/

		}
	}


	/**
	 * Invoke Script for Trigger
	 */
	private function invokeExternalTriggerScript()
	{

		// on/off
		if ( $this->trigger_script == 0 ||
		       empty( $this->trigger_script_name) ) {
			return ;
		}
		// alle x
		if ( ($this->all_visitors % $this->trigger_script_rate) !== 0 ) {
			// Kein ausführen
			return;
		}

		// trigger_script_name
		// we search only and only the script here
		jimport('joomla.filesystem.file');
		$filename = JFile::makeSafe( $this->trigger_script_name );
		require_once ( dirname(__FILE__) .
		               DIRECTORY_SEPARATOR . 'trigger' .
					   DIRECTORY_SEPARATOR . $filename . '.php' );

	}


    /**
     * Read Date and Store ImageDiagramm
     */
    private function renderStatImage($db, $imagePathFS, $days , $detailed )
    {
        // Anzahl Darzustellender Tage
        if ($days <= 0) {
            $query = "SELECT DATE(FROM_UNIXTIME(tm)) AS day, COUNT(*) AS c FROM " . $this->s_createtableName . " GROUP BY DATE(FROM_UNIXTIME(tm))";
        } else {
            // dies sucht 10 Einträge ( unabhängig ob dies wirklich 10Tage sind )
            // $query = "select day, c from (SELECT DATE(FROM_UNIXTIME(tm)) AS day,COUNT(*) AS c FROM " . $this->s_createtableName . " GROUP BY DATE(FROM_UNIXTIME(tm)) ORDER BY day DESC LIMIT 0," . $days . ") AS o order by day";
            // es wurder auf echte Tage verändert, dies bringt auch eine bessere Nutzung der DB
            $query = "select day, c from ( " .
                     " SELECT DATE(FROM_UNIXTIME(tm)) AS day,COUNT(*) AS c FROM " . $this->s_createtableName .
                     "   WHERE tm BETWEEN UNIX_TIMESTAMP(DATE(NOW() - INTERVAL " . $days . "  DAY)) AND UNIX_TIMESTAMP(DATE(NOW()) + INTERVAL 1 DAY) " .
                     " GROUP BY DATE(FROM_UNIXTIME(tm)) ORDER BY day DESC LIMIT 0," . $days .
                     ") AS o order by day";
        }
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		if( $this->db->getErrorNum () ) {
			$e = $this->db->getErrorMsg();
			//print_r( $e );
			JError::raiseWarning( 500, $e );
			return;
		}

        // überführen in DAtenarray und fehlende Tag(e) adden
        $gr_val = array();
        $lrow = '';
        foreach ($rows as $row) {
            if ($lrow != '') {
                // echo $lrow . "/" . $row->day . " : " . $this->datediff( $lrow, $row->day ) . "<br/>";
                $dateDiff = $this->datediff($lrow, $row->day);
                if ($dateDiff > 1) {
                    for($i = 1 ; $i < $dateDiff ; $i++) {
                        // Füge fehlende Daten als Dummy zu
                        // TODO richtig das Datum berechnen day = $lrow + $i
                        $gr_val[ $lrow . '_' . $i ] = 0;
                    }
                }
            }
            $gr_val[$row->day] = $row->c;
            $lrow = $row->day;
        }

        $this->create_image($imagePathFS,
            $this->image_sizeX, $this->image_sizeY,
            $gr_val,
            $this->image_lineColor ,
            $this->image_dia_bgColor,
            $this->image_dia_RandColor,
            $detailed ,
			$this->image_dia_MainbgColor );
    }

    /**
     * store an Image Diagramm
     */
    private function create_image( $imagePath, $size_x , $size_y , $ar ,
        $lineColor, $dia_bgColor, $dia_RandColor , $detailsImage = false ,
		$main_bgColor = '#FFFFFF' )
    {
        //
        $ar_values = array_values($ar);
        $ar_keys = array_keys($ar);

        // TODO COLORS übergeben
        // $bgColor = array(255, 255, 255);
		// now i comes from Hex-Code (WebColors)
		$main_bgColor = str_replace( "#", "" , $main_bgColor );
        $bgColor = array(
        	base_convert(substr($main_bgColor, 0, 2), 16, 10),
        	base_convert(substr($main_bgColor, 2, 2), 16, 10),
			base_convert(substr($main_bgColor, 4, 2), 16, 10) );

        $line_width = 1.5;

        // Wertebereich
        $valuesize_x_from = 0;
        $valuesize_x_to = count($ar_values) ; // days
        $valuesize_y_from = 0;
        if ($valuesize_x_to > 0) {
            $valuesize_y_to = max($ar_values) * 1.1 ; // hits
        } else {
            $valuesize_y_to = 1;
        }

        // nicht auf 0 !  Default 0.1 !
        $left = 0.1 ;
        $right = 0.1 ;
        $top = 0.1 ;
        $bottom = 0.1 ;
        if ( $detailsImage ) {
          $left = 30 ;
          $right = 0.1 ;
          $top = 20 ;
          $bottom = 20 ;
        }

        //
        $D = new Diagram();
        $D->Img = @ImageCreate($size_x, $size_y) or JError::raiseWarning( 0, "Cannot create a new GD image." );
        //background color
        ImageColorAllocate($D->Img, $bgColor[0], $bgColor[1], $bgColor[2]);
        //
        //$D->SetFrame( $rand, $rand, $size_x - $rand, $size_y - $rand);
        $D->SetFrame( $left, $top, $size_x - $right, $size_y - $bottom);
        $D->SetBorder($valuesize_x_from, $valuesize_x_to, $valuesize_y_from, $valuesize_y_to);

        $D->Font=1;
        $D->XScale = 0;
        $D->YScale = 0;
        if ( $detailsImage ) {
          $D->XScale = 1;
          $D->YScale = 1;
          $D->SetText( 0, 0, 'Start : ' . $ar_keys[0] );
        }

        $D->Draw($dia_bgColor, $dia_RandColor, false);

        // skip steps X
        if (($valuesize_x_to - $valuesize_x_from) > $size_x) {
            $stepsX = floor(($valuesize_x_to - $valuesize_x_from) / $size_x);
        } else {
            $stepsX = 1 ;
        }
        // skip steps X
        if (($valuesize_y_to - $valuesize_y_from) > $size_y) {
            $stepsY = floor(($valuesize_y_to - $valuesize_y_from) / $size_y);
        } else {
            $stepsY = 1 ;
        }
        $valuesize_y_to = $valuesize_y_to / $stepsY;
        // echo $stepsX . '/' . $stepsY ;
        // erstmal nur die Values
        $T0 = null;
        $T1 = null;
        for ($t = 0; $t < $valuesize_x_to; $t += $stepsX) {
            $T0 = $T1;
            // $K0 = $K1;
            $T1 = $ar_values[$t];
            $K1 = $ar_keys[$t];

            $D->Line($D->ScreenX($t), $D->ScreenY($T0),
                $D->ScreenX($t + 1), $D->ScreenY($T1),
                $lineColor, $line_width, "", "");

            // need $ar_keys above !
            // echo "CI -> " . $ar_keys[$t] . ":" . $T1 . "<br />" ;
            /*
            ImageMap
            "alert(\"".$ar_keys[$t]." -> ".$T1."\")");

			    $D->Line( $D->ScreenX($t), $D->ScreenY($T0),
            	   	      $D->ScreenX($t + 1), $D->ScreenY($T1),
                          "#000000", 1, "",
                          "alert(\"".round($T0,1)." -> ".round($T1,1)."\")");
	            $D->Box($D->ScreenX($t), $D->ScreenY(0),
    		            $D->ScreenX($t + 1), $D->ScreenY($T1),
            		    "#000000", "", "#00ff00", 0, "#0000ff");
            		    before Destroy
        $imageUri = JURI::base() . $this->image_path;
        $content = '<img src="' . $imageUri . '" usemap="#map1"></img>';
        $content .= '<map name="map1">' . $D->ImgMapData . '</map>';
        return $content;
        	*/
            //imagestring( $D->Img, 2 ,  $D->ScreenX($t + 1), $D->ScreenY($T1) , $T1 ,2  );
        }

        // Save
        $this->storeImageByType($D->Img, $imagePath);
        // Free up resources
        ImageDestroy($D->Img);
    }

    /**
     * Render Usage Table Row
     *
     * @param mixed $a1 Image Name
     * @param mixed $a2 Description Text
     * @param mixed $a3 Number
     * @param boolean $show_UsageImgs Show Image or Not
     * @param mixed rowTitle
     */
    private function spaceer($a1, $a2, $a3, $show_UsageImgs, $rowTitle )
    {
        $ret = '<tr align="left" title="'.$rowTitle.'">';
        if ( $show_UsageImgs != -1 ) {
            $ret .= '<td><img class="mvc_peopleImg' . $this->mdclsf . '" src="modules/' .
            // TODO_CO_MOD modVisitCounterHelper::MODULE
            $this->MODULE . '/images/tbl/' . $show_UsageImgs . '/'.
                         $a1 . '" alt="' . $a2 . '" title="' . $a2 . '"/></td>';
        }
        $ret .= '<td>' . $a2 . '</td>';
        $ret .= '<td align="right">' . $a3 . '</td></tr>';
        return $ret;
    }

    /**
     * numToImg()
     * eine Zahl zu einem HTML-Image String wandeln
     *
     * @param mixed $number zu wandelnde Zahl
     * @param mixed $size_min optional mindest Anzahl von Zahlen
     * @param mixed $size_max optional maximale Anzahl Zahlen
     * @param mixed $dir optional Pfad-Prefix
     * @param mixed $warn_picture optional Bild das bei Überlauf angezeigt wird
     * @param mixed $styleClass CSS Style
     * @return string z.B. <img src="images/4.gif"/><img src="images/7.gif"/>
     */
    private function numToImg($number , $size_min , $size_max , $dir , $warn_picture, $styleClass )
    {
        // maximal Größe setzen
        if (empty($size_max)) {
            $size_max = strlen($number);
        }
        // minimale Größe setzen
        if (empty($size_min)) {
            $size_min = 0;
        }
        // Slash anhängen
        if (! empty($dir) AND $dir[strlen($dir)-1] != '/') {
            $dir .= '/' ;
        }
        // Warnung anzeigen wenn Überlauf ( size_max ) ?
        $erg  = '';
        if (! empty($warn_picture)) {
            $number_lenght = strlen($number);
            if ($number_lenght > $size_max) {
                // Warnungsbild
                $erg = "<img class=\"{$styleClass}" . $this->mdclsf . "\" src=\"{$dir}{$warn_picture}\" alt=\"Counter-Overflow\" title=\"Counter-Overflow\"/>" ;
            } else {
                $erg = "" ;
            }
        }
        // auffüllen mit Nullen und auf maximale Länge
        $number = substr(str_pad(trim($number) , $size_min , '0' , STR_PAD_LEFT) ,
            0 , $size_max) ;
        if ( is_string($number) ) {
            $number = strtoupper($number);
        }
        // String zu Array
        $numbers = str_split($number);
        // für jede Zahl ein Image Object
        foreach($numbers as $number) {
            $erg .= "<img class=\"{$styleClass}" . $this->mdclsf . "\" src=\"{$dir}{$number}.gif\" alt=\"{$number}\" title=\"{$number}\"/>" ;
        }
        return $erg ;
    }


    /**
     * isoZweiToFlagImg()
     * einen ISO2 Country Code zu einem HTML-Image String wandeln
     *
     * UNKNOWN Image if ISO Code empty
     *
     * @param mixed $isoZweiCode ISO Code z.B. DE
     * @param mixed $dir Pfad-Prefix
     * @param mixed $styleClass CSS Style
     * @param mixed $width Image Width
     * @param mixed $height Image Height
     * @return string z.B. <img src="images/flags/flag_DE.gif"/>
     */
    private function isoZweiToFlagImg( $isoZweiCode , $dir, $styleClass, $width, $height )
    {
        // maximal Größe setzen
        if (empty($width)) {
            $width = 20;
        }
        // minimale Größe setzen
        if (empty($height)) {
            $height = 13;
        }
        //
        if (empty($isoZweiCode)) {
            $isoZweiCode = "UNKNOWN";
        }
        // Slash anhängen
        if (! empty($dir) AND $dir[strlen($dir)-1] != '/') {
            $dir .= '/' ;
        }
        // für jede Zahl ein Image Object
        $erg = "<img class=\"{$styleClass}" . $this->mdclsf . "\" src=\"{$dir}flag_{$isoZweiCode}.gif\" alt=\"{$isoZweiCode}\" title=\"{$isoZweiCode}\" width=\"{$width}\" height=\"{$height}\"/>" ;
        return $erg ;
    }

    /**
     * Absolute Wert von Tagesunterschied
     */
    private function datediff($dateF , $dateT)
    {
        // English Format '2008-05-21'
        $datefrom = strtotime($dateF);
        $dateto = strtotime($dateT);
        $difference = $dateto - $datefrom; // Difference in seconds
        $datediff = floor($difference / 86400);
        return abs($datediff);
    }

	/**
	 * Save Image by FileType
	 */
    private function storeImageByType($image , $filename)
    {
        $haystack = $filename;
        $needle = '.' ;
        $strTmp = strrchr($haystack, $needle);
        $fileType = substr($strTmp, strlen($needle), strlen($strTmp) - strlen($needle));

        $image_type = -1;
        switch ($fileType) {
            case 'gif':
            case 'GIF':
                imagegif($image , $filename);
                break;
            case 'jpg':
            case 'JPG':
            case 'jpeg':
            case 'JPEG':
                imagejpeg($image , $filename);
                break;
            case 'png':
            case 'PNG':
                imagepng($image , $filename);
                break;
            default:
                echo "Unknown ImageType :" . $fileType;
                break;
        }
        return $image_type;
    }


    /**
     * modVisitCounterHelper::getWeekMode()
     *
     * @return WEEK Mode
     *
    private function getWeekMode(){
		switch( $this->s_week_startday ){
			case 0: // Sunday
				return 6;
			case 1: // Monday
				return 3;
			case 2: // Saturday ! it's NOT a MySQL Mode !!
				return -1;
			default:
				return 3;
		}
	}
	*/

	/**
	 * Return an Array with From and To Week-Range for a given Date
	 * by an Offset ( Week_Start )
	 *
	 * @param $current_date = Date
	 * @param $dayOffset = Week Startday ( Sunday,Monday,... )
	 * @param $current_date = Format
	 * @return an Array with from an to as Formated Strings
	 *
	 */
	private function getWeekFromTo( $current_date,
	                                $dayOffset = 'Sunday',
									$date_day_fmt = "d.m.Y" ){

      // now
      if ( !isset($current_date) ){
        $current_date = time();
      }
      // Monday, Sunday...
      $dayStr = date('l' , $current_date );
      // is the start self
      $title_day_str_week_s_t = $current_date ;
      if ( $dayStr != $dayOffset ){
        // compute start Time
        // $title_day_str_week_s_t = strtotime( 'last ' . $dayOffset , $current_date ); // siehe php4 win problem
        $title_day_str_week_s_t = strtotime( '-1 ' . $dayOffset , $current_date );
      }
      // formated startAt String
      $title_day_str_week_s =  date( $date_day_fmt, $title_day_str_week_s_t );
      // formated endAt String ( startAt +6 days )
      $title_day_str_week_e =  date( $date_day_fmt,
                          strtotime( '+6 days' , $title_day_str_week_s_t ) ) ;
      // fill the erg and ret
      return array ( "from" => $title_day_str_week_s ,
                     "to" => $title_day_str_week_e );

  }

}

if (!function_exists('str_split')) {
    function str_split($str, $split_lengt = 1)
    {
        $cnt = strlen($str);

        for ($i = 0;$i < $cnt;$i += $split_lengt)
        $rslt[] = substr($str, $i, $split_lengt);

        return $rslt;
    }
}

?>