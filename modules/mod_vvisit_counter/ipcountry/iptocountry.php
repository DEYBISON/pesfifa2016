<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Part of Counter mod_vvisit_counter
 *
 * @version $Id: iptocountry.php 406 2014-10-27 16:45:41Z mmicha $
 * @copyright Copyright (C) 2014 Majunke Michael http://www.mmajunke.de/
 * @license GNU/GPL
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 */

class IPtoCountry {

    // call create Table or not
    private $s_trycreateIPCtable = null;
    private $s_IPCtableName = null;

    private $db = null ;

    // don't read twice
    private $ipToISOAlphaZwei_Cache = array();
    private $ipToCountry_Cache = array();

    /**
     * Konstruktor
     */
    public function __construct(&$params)
    {
        // call create Table or not
        $this->s_trycreatetable = @$params->get('s_trycreateIPCtable', 0);
        // fest: Tabellenname
        $this->s_IPCtableName = 'jos_mvc_iptocountry';

        // Initalisierung
        $this->init();
    }

	/**
	 * liefert f¸r eine IP-Adresse den ISO2 Code
	 *
	 * @param ip_adress IP-Adresse
	 * @return ISO Code z.B. US
	 *         or null
	 */
	public function getISOAlphaZwei( $ip_adress ){

		if ( empty($ip_adress)) {
			return null ;
		}

		//print_r( $this->ipToISOAlphaZwei_Cache );
		if ( array_key_exists( $ip_adress , $this->ipToISOAlphaZwei_Cache ) ) {
			// echo "IS :" . $this->ipToISOAlphaZwei_Cache[$ip_adress] ;
			return $this->ipToISOAlphaZwei_Cache[$ip_adress];
		}
		// echo "NOT :" ;

		//
		$ipAsLong = sprintf("%u", ip2long($ip_adress) );

		//
		//
        // $query = "select isoAlphaZwei from " . $this->s_IPCtableName . " WHERE ipFrom <= " . $ipAsLong . " AND ipTo >= " . $ipAsLong . " LIMIT 1";
        $query = "select isoAlphaZwei from " .
		         $this->s_IPCtableName .
				 " WHERE " . $ipAsLong .
				 " between ipFrom and ipTo LIMIT 1";
        $this->db->setQuery($query);
        $code = $this->db->loadResult();

		if( empty($code)) {
		    $this->ipToISOAlphaZwei_Cache[$ip_adress] = null ;
			return null;
		} else {
			$this->ipToISOAlphaZwei_Cache[$ip_adress] = $code ;
			return $code;
		}

	}

	/**
	 * liefert f¸r eine IP-Adresse den L‰ndername
	 *
	 * @param ip_adress IP-Adresse
	 * @return L‰ndername z.B. GERMANY
	 *         or null
	 */
	public function getCountry($ip_adress ){

		if ( empty($ip_adress)) {
			return null ;
		}

		// print_r( $this->ipToCountry_Cache );
		if ( array_key_exists( $ip_adress , $this->ipToCountry_Cache ) ) {
			// echo "IS :" . $this->$ipToCountry_Cache[$ip_adress] ;
			return $this->ipToCountry_Cache[$ip_adress];
		}
		// echo "NOT :" ;

		//
		$ipAsLong = sprintf("%u", ip2long($ip_adress) );

		//
		//
        // $query = "select name from " . $this->s_IPCtableName . " WHERE ipFrom <= " . $ipAsLong . " AND ipTo >= " . $ipAsLong . " LIMIT 1";
        $query = "select name from " . $this->s_IPCtableName .
                 " WHERE " . $ipAsLong .
				 " between ipFrom and ipTo LIMIT 1";
        $this->db->setQuery($query);
        $name = $this->db->loadResult();

		if( empty($name)) {
			$this->ipToCountry_Cache[$ip_adress] = null ;
			return null;
		} else {
			$this->ipToCountry_Cache[$ip_adress] = $name ;
			return $name;
		}

	}



    /**
     * initalise
     */
    protected function init()
    {

        // Database init
        $this->db = JFactory::getDBO();

        // Check if table exists. When not, create it
        if ( $this->s_trycreatetable ) {

            // create
            $query = "CREATE TABLE IF NOT EXISTS " .
			         $this->s_IPCtableName .
			         "( ipFrom int(11) unsigned NOT NULL , " .
					 "  ipTo int(11) unsigned NOT NULL , " .
					 "  isoAlphaZwei CHAR( 2 ) NOT NULL , " .
					 "  isoAlphaDrei CHAR( 3 ) NOT NULL , " .
					 "  name VARCHAR( 150 ) NOT NULL , " .
					 "  KEY ipFT( ipFrom, ipTo ) , KEY(ipFrom) , KEY(ipTo) )" .
					 " ENGINE=MyISAM AUTO_INCREMENT=1";

            $this->db->setQuery($query);
            $this->db->query();
            if ($this->db->getErrorNum ()) {
                $e = $this->db->getErrorMsg();
                //print_r( $e );
                JError::raiseWarning(500, $e);
                return;
            }

		    //
			$query = "LOCK TABLE " . $this->s_IPCtableName . " WRITE";
			$this->db->setQuery($query);
			$this->db->query();
            if ($this->db->getErrorNum ()) {
                $e = $this->db->getErrorMsg();
                // print_r( $e );
                JError::raiseWarning(500, $e);
                return;
            }

            // count Data
            $counts = 0 ;
            $query = "select count(*) from " . $this->s_IPCtableName ;
            $this->db->setQuery($query);
            $counts = $this->db->loadResult();
            if ($this->db->getErrorNum ()) {
                $e = $this->db->getErrorMsg();
                // print_r( $e );
            	$query = "UNLOCK TABLES ";
				$this->db->setQuery($query);
				$this->db->query();
                JError::raiseWarning(500, $e);
                return;
            }
            // wenn leer , insert Daten from File
            if ($counts < 1) {

                $this->insertNewDB = TRUE;

                // Pfad zum File TODO Konstante
                $dataPath = (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ip-to-country.csv');
                // print_r($dataPath);
                if (! file_exists($dataPath)) {
	   	            $query = "UNLOCK TABLES ";
					$this->db->setQuery($query);
					$this->db->query();
                    JError::raiseWarning(500, "Missing File:[" + $dataPath + "]");
                    // TODO hier DROP Tabel ausf¸hren ??
                    return;
                }
                // Daten einspielen
                // CSV-Datei ˆffnen
                $handle = fopen($dataPath, "r");

                // each row
                $ins = 0 ;
                while ($rowcsv = fgetcsv($handle, 2048, ',', '"')) {
                    $query = "INSERT INTO " . $this->s_IPCtableName . " (ipFrom, ipTo, isoAlphaZwei, isoAlphaDrei, name ) VALUES( '$rowcsv[0]' , '$rowcsv[1]', " . $this->db->Quote($rowcsv[2]) . ", " . $this->db->Quote($rowcsv[3]) . ", " . $this->db->Quote($rowcsv[4]) . " )";
                    $this->db->setQuery($query);
                    $insT = $this->db->query();
                    $ins += $insT;
                    if ($this->db->getErrorNum ()) {
                        $e = $this->db->getErrorMsg();
                        // print_r( $e );
                        JError::raiseWarning(500, $e);
                        // TODO irgendein Rollback ?
                        break;
                    }
                }

                // print_r( "<h2> I=" . $ins . "</h2>" );
                // CSV-Datei schlieﬂen
                fclose($handle);
                //print_r("<h1>Inserted [" . $ins . "]</h1>");
            }

            //
            $query = "UNLOCK TABLES ";
			$this->db->setQuery($query);
			$this->db->query();
			if ($this->db->getErrorNum ()) {
                $e = $this->db->getErrorMsg();
                // print_r( $e );
                JError::raiseWarning(500, $e);
                return;
            }


        }
    }
}

?>