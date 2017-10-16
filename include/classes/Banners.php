<?php

/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

if(!defined('BANNERS_TABLE')) define('BANNERS_TABLE',  $lang.DBTABLE_LANG_SEP.'banners');

/**
 * Description of Banners class
 * @author WebLife. Andreas
 * @copyright 2011
 * 
 * ******** SQL ***********
  DROP TABLE IF EXISTS `ru_banners`;
  CREATE TABLE IF NOT EXISTS `ru_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cids` varchar(100) DEFAULT 'all',
  `title` tinytext NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `customcode` text,
  `redirectid` int(11) NOT NULL DEFAULT '0',
  `redirecturl` varchar(255) NOT NULL DEFAULT '',
  `position` varchar(20) NOT NULL,
  `module` varchar(63) DEFAULT '',
  `target` varchar(63) DEFAULT '',
  `views` int(11) NOT NULL DEFAULT '0',
  `maxviews` int(11) NOT NULL DEFAULT '0',
  `countviews` tinyint(1) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `maxclicks` int(11) NOT NULL DEFAULT '0',
  `countclicks` tinyint(1) NOT NULL DEFAULT '0',
  `weight` int(5) NOT NULL DEFAULT '900',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_position` (`position`),
  KEY `idx_order` (`order`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 *
 */
class Banners {

    private $cid;           // Integer. Current page Category ID
    private $UrlWL;         // UrlWL object. Current page Category ID
    private $tableName;     // String. DataBase Banners name
    private $bOrdering;     // Bool. IF set TRUE banners will ordering by them order number ELSE not
    private $orderByWeight; // Bool. IF set TRUE banners will ordering by them weight number ELSE not
    private $arrItems;      // Array. Array of all active banners for $this->cid or all cids BY position
    private static $arrModules;     // Static Array. Array of Banners Modules
    private static $arrPositions;   // Static Array. Array of Banners Position
    private static $folderName = 'banners'; // Static String. Folder name for Banners class files

    /**
     * Banners::__construct()
     *
     * Construct function.
     * @return
     */
    public function __construct(UrlWL $UrlWL, $bOrdering=false, $bOrderByWeight=false) {
        $this->tableName     = BANNERS_TABLE;
        $this->UrlWL         = $UrlWL;
        $this->bOrdering     = $bOrdering;
        $this->orderByWeight = $bOrderByWeight;
        $this->arrItems      = array();
    }

    /**
     * Banners::__destruct()
     *
     * Destruct function..
     * @return
     */
    public function __destruct() {
        ;
    }

    /**
     * Banners::init()
     *
     * Init function.
     * Init $categoryID class and load items from database.
     * @param int $categoryID  - current page category id
     */
    public function init($categoryID) {
        $this->cid = intval($categoryID);
        $this->_loadItems();
    }

    /**
     * Banners::makeAccountClickURL()
     *
     * Make Account Click URL function.
     * @param int $categoryID  - Category id where clicked
     * @param int $bannerID  - Banner id
     * @param String $bannerURL  - Banner link to redirect after account click
     * @return string - built Click Url to use
     */
    public function makeAccountClickURL($categoryID, $bannerID, $bannerURL) {
        return '/interactive/ajax.php?zone=site&action=incrementBannerClick'.
               '&categoryID='.$categoryID.
               '&bannerID='.$bannerID.
               '&bannerURL='.urlencode($bannerURL);
    }

    /**
     * Banners::makeItemLink()
     *
     * Make Account Click URL function.
     * @param Array $item   - Item data array
     * @return string - built Item Link to use
     */
    public function makeItemLink(& $item) {
        return !empty($item['redirecturl']) ? $item['redirecturl'] : (!empty($item['redirectid']) ? $this->UrlWL->buildCategoryUrl($this->UrlWL->getCategoryById($item['redirectid'])) : '');
    }

    // STATIC FUNCTIONS ========================================================

    public static function getFolderURL() {
        return UPLOAD_URL_DIR.self::$folderName.'/';
    }

    public static function getFolderPath() {
        return WLCMS_ABS_ROOT.UPLOAD_DIR.DS.self::$folderName.DS;
    }

    public static function incrementClick($bannerID) {
        $Banners = new Banners();
        $Banners->_incrementDBValue(array($bannerID), 'clicks');
    }

    public static function getListTargets() {
        return array(
            ''        => ' -- Выберите -- ',
            '_blank'  => 'Новое окно',
            '_self'   => 'Текущее окно',
            '_parent' => 'Родительское окно',
            '_top'    => 'Без фреймов'
        );
    }

    // Positions methods
    private static function _initPositions($refresh=false) {
        if($refresh || empty(self::$arrPositions)){
            self::$arrPositions = array(
                array('active'=>1, 'id'=>0, 'title'=>' -- Выберите позицию -- '),
                array('active'=>1, 'id'=>1, 'title'=>'Левый блок'),
                array('active'=>1, 'id'=>2, 'title'=>'Правый блок'),
                array('active'=>1, 'id'=>3, 'title'=>'Верхний блок'),
                array('active'=>1, 'id'=>4, 'title'=>'Нижний блок'),
            );
        }
    }

    public static function activatePositions(array $arPositionID, $refresh=false) {
        if(!empty($arPositionID)){
            foreach(self::getPositions($refresh) as $ind=>$arItem){
                if(in_array($arItem['id'], $arPositionID)) self::$arrPositions[$ind]['active']=1;
            }
        }
    }

    public static function deActivatePositions(array $arPositionID, $refresh=false) {
        if(!empty($arPositionID)){
            foreach(self::getPositions($refresh) as $ind=>$arItem){
                if(in_array($arItem['id'], $arPositionID)) self::$arrPositions[$ind]['active']=0;
            }
        }
    }

    public static function getPositions($refresh=false) {
        self::_initPositions($refresh);
        return self::$arrPositions;
    }

    public static function getListPositions($refresh=false) {
        static $positions = array();
        if($refresh) $positions = array();
        if(empty($positions)){
            foreach(self::getPositions($refresh) as $arItem){
                if($arItem['active']) $positions[$arItem['id']] = $arItem['title'];
            }
        } return $positions;
    }

    // Modules methods
    private static function _initModules($refresh=false) {
        if($refresh || empty(self::$arrModules)){
            self::$arrModules = array(
                array('active'=>1, 'name'=>'',           'title'=>' -- Выберите модуль -- '),
                array('active'=>1, 'name'=>'image',      'title'=>'Изображение'),
                array('active'=>1, 'name'=>'text',       'title'=>'Текст'),
                array('active'=>1, 'name'=>'image_text', 'title'=>'Изображение и Текст')
            );
        }
    }

    public static function activateModules(array $arModulesNames, $refresh=false) {
        if(!empty($arModulesNames)){
            foreach(self::getModules($refresh) as $ind=>$arItem){
                if(in_array($arItem['name'], $arModulesNames)) self::$arrModules[$ind]['active']=1;
            }
        }
    }

    public static function deActivateModules(array $arModulesNames, $refresh=false) {
        if(!empty($arModulesNames)){
            foreach(self::getModules($refresh) as $ind=>$arItem){
                if(in_array($arItem['name'], $arModulesNames)) self::$arrModules[$ind]['active']=0;
            }
        }
    }

    public static function getModules($refresh=false) {
        self::_initModules($refresh);
        return self::$arrModules;
    }

    public static function getListModules($refresh=false) {
        static $modules = array();
        if($refresh) $modules = array();
        if(empty($modules)){
            foreach(self::getModules($refresh) as $ind=>$arItem){
                if($arItem['active']) $modules[$arItem['name']] = $arItem['title'];
            }
        } return $modules;
    }

    // GENERAL Banners Methods =================================================

    /**
     * Banners::_loadItems()
     *
     * Load Banners function.
     * Load Banners items to array by position key and filtered by cid.
     */
    private function _loadItems() {
        $query = "SELECT * FROM `{$this->tableName}` ".
                        "WHERE `active`=1 AND (cids='all' OR CONCAT(',', TRIM(cids), ',') LIKE '%,{$this->cid},%') ".
                        ($this->bOrdering ? "ORDER BY `position`, `order` ASC" : '');
        $result = mysql_query($query) or die("SELECT BANNERS OPERATIONS: ".mysql_error());
        while($item = mysql_fetch_assoc($result)) {
            $add = true;
            
            if ($item['cids'] == 'all')
                 $item['arCids'] = array();
            else $item['arCids'] = explode(',', $item['cids']);

            $item['redirecturl'] = $this->_makeCorrectLink($item['redirecturl']);

            $item['content'] = '';
            $item['link']    = self::makeItemLink($item);

            if($item['countviews']  && $item['maxviews']  <= $item['views'])  $add = false;
            if($item['countclicks'] && $item['maxclicks'] <= $item['clicks']) $add = false;

            if($add) $this->arrItems[$item['position']][] = $item;
        }
    }

    private function _loadModule($module, $item) {
        switch ($module) {
            case 'image':
                if(!empty($item['image']) && is_file(self::getFolderPath().$item['image'])){
                    $arImage = getimagesize(self::getFolderPath().$item['image']);
                    $target  = !empty($item['target']) ? ' target="'.$item['target'].'"' : '';
                    $item['content'] = '<img src="'.self::getFolderURL().$item['image'].'" width="'.$arImage[0].'" height="'.$arImage[1].'" alt="" />';
                    if($item['link']) $item['content'] = '<a href="'.$item['link'].'" title="'.$item['title'].'"'.$target.'>'.$item['content'].'</a>';
                } $item['customcode'] = '';
                break;

            case 'text':
                $item['image']   = '';
                $item['content'] = unScreenData($item['customcode']);
                break;

            case 'image_text':
                if(!empty($item['image']) && is_file(self::getFolderPath().$item['image'])){
                    $item['arImage'] = getimagesize(self::getFolderPath().$item['image']);
                    $item['image']   = self::getFolderURL().$item['image'];
                } else {
                    $item['image']   = 'noimage.jpg';
                    $item['arImage'] = getimagesize(self::getFolderPath().$item['image']);
                    $item['image']   = self::getFolderURL().$item['image'];
                }   $item['content'] = unScreenData($item['customcode']);
                break;

            default:
                break;
        } return $item;
    }

    private function _switchWeightOrder($bOrderByWeight=false) {
        $this->orderByWeight = $bOrderByWeight;
        $this->bOrdering = !$this->orderByWeight;
    }

    private function _makeCorrectLink($str) {
        $str = trim($str);
        if(!empty($str) && strpos($str, "/")!==0 && !$this->_checkPresenceProtocolInLink($str)){
            if((strpos($str, "category_")>0 || strpos($str, ".")>0 || strpos($str, "www")===0) && !$this->_checkPresenceProtocolInLink($str)) 
                 $str = "http://".$str;
            else $str = "/".$str;
        } return $str;
    }
    
    private function _checkPresenceProtocolInLink($link) {
        return (strpos($link, "http://")!==false || strpos($link, "https://")!==false || strpos($link, "mailto:")!==false);
    }

    private function _incrementDBValue(array $arItemsID, $colname) {
        if (count($arItemsID) > 0) {
            $query = "UPDATE `{$this->tableName}` SET `{$colname}`=`{$colname}`+1 WHERE `id` IN (".implode(', ', $arItemsID).")";
            $result = mysql_query($query) or die("UPDATE {$colname} BANNERS OPERATIONS FAILED: ".mysql_error());
        }
    }
    
    private function _sortByWeight(array $arrItems, $count) {
        $iCount = sizeof($arrItems);
        if($iCount){
            $arrTmp = array();
            function cmpByWeight($a, $b){
                if ($a['weight']==$b['weight']) return 0;
                return ($a['weight']>$b['weight']) ? -1 : 1; //<
            } uksort($arrItems, 'cmpByWeight'); 
        } return array_slice($arrItems, 0, $count);
    }

    public function getBanners($position, $count=false, $bOrderByWeight=false) {
        $arrItems = $arItemsID = array();
        if(array_key_exists($position, $this->arrItems)) {
            $this->_switchWeightOrder($bOrderByWeight);
            $iCount = sizeof($this->arrItems[$position]);

            // set array items to temp array defined by count
            if($count && $iCount > $count){
                if($this->orderByWeight) $arrTmp = $this->_sortByWeight($this->arrItems[$position], $count);
                elseif($this->bOrdering) $arrTmp = array_slice($this->arrItems[$position], 0, $count);
                else                     $arrTmp = array_rand($this->arrItems[$position], $count);
            } else $arrTmp = $this->arrItems[$position];

            // prepare output items and load module if exist
            foreach($arrTmp as $item) {
                if(!empty($item['module'])) $item = $this->_loadModule($item['module'], $item);
                if($item['countviews'])     $arItemsID[]  = $item['id'];
                if($item['countclicks'])    $item['link'] = self::makeAccountClickURL($this->cid, $item['id'], $item['link']);
                $arrItems[] = $item;
            }
        }
        $this->_incrementDBValue($arItemsID, 'views');
        return $arrItems;
    }

}
