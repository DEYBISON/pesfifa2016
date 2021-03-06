<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderFeatureTranslateUrl
{

    private $slider;

    public $from = '';

    public $to = '';

    public function __construct($slider) {

        $this->slider = $slider;
        list($this->from, $this->to) = (array)N2Parse::parse(N2SmartSliderSettings::get('translate-url', '||'));
    }

    public function renderSlider($sliderHTML) {

        if (!$this->slider->isAdmin && !empty($this->from) && !empty($this->to)) {
            return str_replace($this->from, $this->to, $sliderHTML);
        }

        return $sliderHTML;
    }
}