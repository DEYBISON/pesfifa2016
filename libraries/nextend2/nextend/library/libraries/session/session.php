<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Session
{

    /**
     * @var $storage N2SessionStorageAbstract
     */
    public static $storage = false;

    private static function load() {
        if (!self::$storage) {
            N2Loader::import("libraries.session.storage");
            self::$storage = new N2SessionStorage();
        }
    }

    public static function get($key, $default = null) {
        self::load();
        return self::$storage->get($key, $default);
    }

    public static function set($key, $value) {
        self::load();
        return self::$storage->set($key, $value);
    }

    public static function delete($key) {
        self::load();
        return self::$storage->delete($key);
    }
}