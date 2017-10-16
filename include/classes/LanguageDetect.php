<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * Description of LanguageDetect class
 * Implements language negotiation
 * @author WebLife
 * @copyright 2010
 */
class LanguageDetect {

/**
 * Counter for how many languages are supported by user browser
 */
    private $_languages;

    /**
     * Language list pairs [language,qualifier]
     */
    private $_languagesList;

    /**
     * Original HTTP_ACCEPT_LANGUAGES
     */
    private $_httpAcceptLanguages;


    /**
     * Constructor: loads http_accept_language
     */
    function LanguageDetect() {
        $this->_httpAcceptLanguages = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '';
        $this->languageInit();
    }

    /**
     * Returns true if given locale prefix matches primary lang prefix
     */
    function IsPrimaryLanguage($languagePrefix) {
        $this->ResetList();
        $subList1 = explode("-",$languagePrefix);
        $prefix1 = $subList1[0] ;
        $subList2 = explode("-",key($this->_languagesList)) ;
        $prefix2 = $subList2[0] ;
        if ($prefix1==$prefix2)
            return true;
        else
            return false;
    }

    /**
     * Return full locale string for primary language
     */
    function getPrimaryLanguage() {
        $this->resetList();
        return key($this->_languagesList);
    }

    /**
     * Return prefix string for primary language
     */
    function getPrimaryPrefix() {
        $this->resetList();
        $subList = explode("-",key($this->_languagesList));
    }

    /**
     * Return qualifier value for the prefix in given locale
     */
    function findLanguage($locale) {
        $found = 0;
        $this->resetList();
        $subList = explode("-",$locale);
        $prefix1 = $subList[0];
        while ($current = each($this->_languagesList)) {
            $subList = explode("-",$current[0]);
            $prefix2 = $subList[0];
            if ($prefix2 == $prefix1) {
                return $current[1];
            }
        }
        return false;
    }

    /**
     * Fills sublist with an ordered list of accepted languages from given
     * array based on each locale prefix. Ordering is on qualifier so first
     * element in the array is best match from the given list.
     */
    function getLanguagesList($arr,&$subList1) {
        if(!is_array($subList1)) $subList1 = Array();

        $this->resetList();
        while ($current = each($this->_languagesList)) {
            $subList2 = explode("-",$current[0]);
            if((isset($subList2[1]) && $subList2[1] == 'ua') || $subList2[0] == 'uk') $subList2[0] = 'ua';
            $locale = $subList2[0];
            if (in_array($locale,$arr)) {
                $subList1 += Array($locale => $current[1]) ;
            }
        }
        // This will order based on qualifier descending
        if (count($subList1)) {
            arsort($subList1);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prints the languages list
     */
    function printList() {
        $this->resetList();
        while($current = each($this->_languagesList)) {
            echo $current[0] . ": " . $current[1] ."<br />";
        }
    }

    /**
     * Initialize language list
     */
    private function languageInit() {
        $this->_languagesList = Array();
        $list = explode(",",$this->_httpAcceptLanguages);
        for ($i = 0; $i < count($list); $i++) {
            $counter = strchr($list[$i],";");
            if ($counter === false) {
            // No qualifier, it is only a locale
                $this->_languagesList += Array($list[$i] => 100);
                $this->_languages++;
            } else {
            // Has a qualifier rating
                $q = explode(";",$list[$i]);
                $locale = $q[0];
                $q = explode("=",$q[1]);
                $this->_languagesList += Array($locale => ($q[1]*100));
                $this->_languages++;
            }
        }
        return ($this->_languages);
    }

    /**
     * Resets languages list
     */
    private function resetList() {
        reset($this->_languagesList);
    }

}
