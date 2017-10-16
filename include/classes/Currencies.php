<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


/**
 * Description of Currencies class
 * This class provides methods for change currency, init default Currencies, update and remove all items
 * from a session. The Currencies works with a session array for simple accesing it.
 * @author WebLife
 * @copyright 2011
 */
class Currencies {

    private $count     = 0;             // Set int var  for show count items of Currencies
    private $recalc    = false;         // Set bool var if need to reCalculate Currencies
    private $arDefault = array();       // Default Currencies Array
    private $arCurrent = array();       // Current Currencies Array
    private $arrItems  = array();       // Currencies Items Array
    private $ss_prefix = 'SCP_';        // Currencies Prefix For Session Key Name
    private $ss_name   = 'CURRENCIES';  // Currencies Key Name Array For Session store

    /**
     * Currencies::__construct()
     *
     * Construct function.
     * @return
     */
    public function __construct($sessionPrefix='') {
        if(!empty($sessionPrefix)) 
             $this->setPrefix($sessionPrefix);
        else $this->setName();
        $this->init();
        $this->changeCurrentCurrency();
    }

    /**
     * Currencies::__destruct()
     *
     * Destruct function..
     * @return
     */
    public function __destruct() {
        ;
    }

    /**
     * Currencies::__get()
     *
     * get Smarty property in template context
     * @param string $property_name property name
     */
/* 
    public function __get($property_name) {
        if ($property_name=='arDefault' || $property_name=='arCurrent')
             return $this->$property_name;
        else $this->toDie('Fatal error: Cannot access private property <u>'.get_class($this).'::$'.$property_name.'</u>');
    }
*/

    /**
     * Currencies::init()
     *
     * Init function. Parse session if exist default and current Currencies
     * Init it and items.  Set to session default and current item.
     * Init recalc var if need to recalculate
     * @return
     */
    private function init() {
        // init all items to array
        $query = "SELECT t.*, ljt.* FROM `".CURRENCY_TABLE."` t
                    LEFT JOIN `".CURRENCY_INFO_TABLE."` ljt ON ljt.`cid`=t.`id`
                    WHERE t.`id`>0 AND t.`active`=1
                    ORDER BY t.`order`, t.`id`";
        $result = mysql_query($query);
        while($row = mysql_fetch_assoc($result)) {
            $row['name']    = $this->unScreenData($row['name']);
            $row['title']   = $this->unScreenData($row['title']);
            $row['sign']    = $this->unScreenData($row['sign']);
            $row['template']= $this->unScreenData($row['template']);
            
            if($row['def4calc']) $this->arDefault = $row;
            if($row['def4show']) $this->arCurrent = $row;
            $this->arrItems[$row['id']] = $row;
        }
        // init and update Default Currency
        $this->initDefault();
        // equalize Currencies Nominal and Rate
        $this->equalizeCurrenciesToDefault();
        // init and update Current Currency
        $this->initCurrent();
        // set recalc bool var
        $this->setReCalc();
        // set count Currencies var
        $this->setCountCurrencies();
    }

    private function setReCalc() {
        if(!$this->getDefaultId() || !$this->getCurrentId()) $this->toDie('ERROR: Check DB Currency Defaults Values!');
        $this->recalc = ($this->getDefaultId()==$this->getCurrentId()) ? false : true;
    }

    private function setPrefix($sessionPrefix) {
        $this->ss_prefix = $sessionPrefix;
        $this->setName();
    }

    public function getPrefix() {
        return $this->ss_prefix;
    }

    private function setName() {
        return $this->ss_prefix.$this->ss_name;
    }

    public function getName() {
        return $this->ss_name;
    }

    public function isEmptyCurrencies() {
        return $this->count==0;
    }

    private function setCountCurrencies() {
        $this->count = sizeof($this->arrItems);
    }

    public function getCountCurrencies() {
        return $this->count;
    }

    public function getItems() {
        return $this->arrItems;
    }

    public function listItems() {
        return $this->count ? array_values($this->arrItems) : array();
    }

    private function toDie($error) {
        die('<div id="error" style="font-weight:bold;color:#FF0000;text-align:center;">'.($error ? $error : 'ERROR!').'</div>');
    }

    private function screenData($str, $bApplyTrim=false){
        if($bApplyTrim) $str = trim($str);
        return (empty($str) || is_bool($str)) ? $str : addslashes(htmlspecialchars(stripslashes($str), ENT_QUOTES));
    }
    
    private function unScreenData($str, $bApplyTrim=false){
        if($bApplyTrim) $str = trim($str);
        return (empty($str) || is_bool($str)) ? $str : htmlspecialchars_decode(stripslashes($str), ENT_QUOTES);
    }

    /**
     * Currencies::isSetCurrency()
     *
     * Get Currency Info Array By ID
     * @param mixed $var - ID of item OR Currency Info Array
     * @return bool
     */
    public function isSetCurrency($var) {
        if(is_int($var) && $var)
             return array_key_exists($var, $this->arrItems);
        elseif(is_array($var) && array_key_exists('id', $var))
             return array_key_exists($var['id'], $this->arrItems);
        else return false;
    }

    /**
     * Currencies::getCurrencyById()
     *
     * Get Currency Info Array By ID
     * @param int $id - ID of item
     * @return array Currency Info
     */
    public function getCurrencyById($id) {
        return $this->isSetCurrency($id) ? $this->arrItems[$id] : array();
    }

    /**
     * Currencies::initDefault()
     *
     * Init Default Currency Info Array. You can reInit Default Currency - Base Price Currency
     */
    private function initDefault() {
        if(empty($this->arDefault) || $this->arDefault['rate']!=1){
            foreach($this->arrItems as $ind=>$arItem){
                if($arItem['rate']==1){
                    $this->arDefault = $arItem;
                    $this->arrItems[$ind]['def4calc'] = 1;
                } elseif($arItem['def4calc']==1)
                    $this->arrItems[$ind]['def4calc'] = 0;
            }
        } // $_SESSION[$this->ss_name]['default'] = $this->arDefault;
    }

    /**
     * Currencies::getDefault()
     *
     * Get Default Currency Info Array
     * @return array of Default Currency Info
     */
    public function getDefault() {
        return $this->arDefault;
    }

    /**
     * Currencies::getDefaultId()
     *
     * Get Default Currency Id
     * @return int Id of Default Currency
     */
    public function getDefaultId() {
        return !empty($this->arDefault['id']) ? $this->arDefault['id'] : 0;
    }

    /**
     * Currencies::initCurrent()
     *
     * Init Current Currency Info Array. You can reInit Current Currency to display
     */
    private function initCurrent() {
        if (isset($_SESSION[$this->ss_name]['current'])){
            $this->arCurrent = $_SESSION[$this->ss_name]['current'];
            $this->arCurrent = $this->arrItems[$this->getCurrentId()];
        }   $_SESSION[$this->ss_name]['current'] = $this->arCurrent;
    }

    /**
     * Currencies::resetCurrent()
     *
     * reInit Current Currency Info Array. You can reInit Current Currency to display
     */
    public function resetCurrent() {
        if(isset($_SESSION[$this->ss_name]['current'])) unset($_SESSION[$this->ss_name]['current']);
        foreach($this->arrItems as $ind=>$arItem){
            if($arItem['def4show']){
                $_SESSION[$this->ss_name]['current'] = $this->arCurrent = $arItem;
                break;
            }
        }
    }

    /**
     * Currencies::setCurrent()
     *
     * Set Current Currency Info Array. You can change Current Currency for display
     * @param int $id - ID of item
     * @return bool of Trying Change Current Currency Info Array
     */
    public function setCurrent($id) {
        if($this->isSetCurrency($id) && $id!=$this->getCurrentId()){
            $_SESSION[$this->ss_name]['current'] = $this->arCurrent = $this->getCurrencyById($id);
            $this->setReCalc();
            return true;
        } return false;
    }

    /**
     * Currencies::changeCurrentCurrency()
     *
     * Change Current Currency Info Array for display from POST
     * @return bool of Trying Change Current Currency Info Array
     */
    private function changeCurrentCurrency() {
        if(!empty($_POST['currency_id'])) return $this->setCurrent(intval($_POST['currency_id']));
        return false;
    }

    /**
     * Currencies::getCurrent()
     *
     * Get Current Currency Info Array
     * @return array of Current Currency Info
     */
    public function getCurrent() {
        return $this->arCurrent;
    }

    /**
     * Currencies::getCurrentId()
     *
     * Get Current Currency ID
     * @return int Id of Default Currency
     */
    public function getCurrentId() {
        return !empty($this->arCurrent['id']) ? $this->arCurrent['id'] : 0;
    }

    /**
     * Currencies::equalizeCurrencyNominal()
     *
     * Equalize Currency Nominal and Rate beetween two currencies
     * @param array $arItem1 - 1-st currency array info
     * @param array $arItem2 - 2-nd currency array info
     * @param bool $auto     - if $auto=true - convert to less else convert to  $arItem1 nominal
     */
    private function equalizeCurrencyNominal(&$arItem1, &$arItem2, $auto=true) {
        if($arItem1['nominal'] != $arItem2['nominal']){
            if(!$auto || $arItem1['nominal'] < $arItem2['nominal']){
                $arItem2['rate']    = ($arItem2['rate']/$arItem2['nominal'])*$arItem1['nominal'];
                $arItem2['nominal'] = $arItem1['nominal'];
            } else {
                $arItem1['rate']    = ($arItem1['rate']/$arItem1['nominal'])*$arItem2['nominal'];
                $arItem1['nominal'] = $arItem2['nominal'];
            }
        }
    }

    /**
     * Currencies::equalizeCurrenciesToDefault()
     *
     * Equalize All Currencies Nominal and Rate To arDefault
     */
    private function equalizeCurrenciesToDefault() {
        if(!empty($this->arDefault)){
            foreach($this->arrItems as $ind=>$arItem){
                if($this->arDefault['nominal'] != $arItem['nominal']){
                    $this->equalizeCurrencyNominal($this->arDefault, $this->arrItems[$ind], false);
                }
            }
        }
    }

    /**
     * Currencies::convert()
     *
     * Convert from one currency to another
     * @param float $value - currency value
     * @param int $from - could be currency ID
     * @param int $to   - could be currency ID
     * @return mixed  - int or false
     */
    public function convert($value, $from, $to) {
        if($this->isSetCurrency($from)) $arFrom = $this->arrItems[$from];
        if($this->isSetCurrency($to))   $arTo   = $this->arrItems[$to];
        if(!isset($arFrom) || !isset($arTo)) return false;
        $this->equalizeCurrencyNominal($arFrom, $arTo);
        return (($value*$arFrom['rate'])/$arTo['rate']);
    }

    /**
     * Currencies::convertCurrency()
     *
     * Convert from one currency to another if need
     * @param float $value - currency value
     * @return float
     */
    public function convertCurrency($value) {
        return $this->recalc ? (($value*$this->arDefault['rate'])/$this->arCurrent['rate']) : $value;
    }

    /**
     * Currencies::formatNumber()
     *
     * Format number for display by Currency Data
     * @param float $value - currency value
     * @param int   $cid   - could be currency ID
     * @return string
     */
    public function formatNumber($value, $cid) {
        $arItem = $this->isSetCurrency($cid) ? $this->arrItems[$cid] : $this->arCurrent;
        $value  = number_format($value, $arItem['decimals'], $arItem['dec_point'], $arItem['thousands_sep']);
        return $arItem['unbreakspace'] ? str_replace(" ", "&nbsp;", $value) : $value;
    }

    /**
     * Currencies::showCurrencyByTemplate()
     *
     * Format Currency number for show by Currency Template
     * @param float $value - currency value
     * @param int   $cid   - could be currency ID
     * @return string
     */
    public function showCurrencyByTemplate($value, $cid) {
        $arItem = $this->isSetCurrency($cid) ? $this->arrItems[$cid] : $this->arCurrent;
        return str_replace("#", $this->formatNumber($value, $arItem['id']), $arItem['template']);
    }

    /**
     * Currencies::showCurrency()
     *
     * Format Currency number for show by Currency Template
     * @param float $value  - currency value
     * @param int   $cid    - could be currency ID
     * @param bool  $before - where to show sign before or after
     * @param mixed $sign   - currency sign
     * @return string
     */
    public function showCurrency($value, $cid, $before=true, $sign=false) {
        $arItem = $this->isSetCurrency($cid) ? $this->arrItems[$cid] : $this->arCurrent;
        $value  = $this->formatNumber($value, $arItem['id']);
        if($sign===false){
            if(!empty($arItem['sign']))     $sign = $arItem['sign'];
            elseif(!empty($arItem['code'])) $sign = $arItem['code'];
            else                            $sign = '';
        }   return $before ? $sign.$value : $value.$sign;
    }
}
