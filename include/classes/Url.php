<?php

/**
 * WEBlife CMS
 * Created on 25.04.2012, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


/**
 * Description of Url class
 * This class provides methods for create and manage SEO Url for simple accesing it.
 * @author WebLife
 * @copyright 2012
 */
class Url {

    protected $url       = '';
    protected $suffix    = '.html';
    protected $incSuffix = false;
    protected $anchor    = '';
    protected $arPath    = array();
    protected $arParams  = array();

    /**
     * Url::__construct()
     *
     * Object Construct function.
     * @return
     */
    public function __construct($link, $incSuffix, $suffix) {
        if($incSuffix) $this->enableSuffix($suffix);
        $this->url = trim($link);
        $arr = self::ParseUrl($this->url, $this->incSuffix, $this->suffix);
        $this->anchor   = $arr['anchor'];
        $this->arPath   = $arr['arPath'];
        $this->arParams = $arr['arParams'];
    }

    /**
     * Url::__destruct()
     *
     * Object Destruct function.
     * @return
     */
    public function __destruct() {
        ;
    }

    /**
     * UrlWL::getParams()
     *
     * Get Current Class Params function.
     * @return
     */
    public function getParams() {
        return $this->arParams;
    }

    /**
     * UrlWL::getAnchor()
     *
     * Get Current Class anchor function.
     * @return
     */
    public function getAnchor() {
        return $this->anchor;
    }

    /**
     * UrlWL::getUrl()
     *
     * Get Current Class Url function.
     * @return
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * UrlWL::setUrl()
     *
     * Set Class Url function.
     * @return
     */
    public function setUrl($url='') {
        $this->url = $url;
    }

    /**
     * UrlWL::setParam()
     *
     * Set param to Class arParam function.
     * @return
     */
    public function setParam($key, $value) {
        if(!empty($key)){
            $this->arParams[$key] = $value;
        }
    }

    /**
     * UrlWL::getParam()
     *
     * Get param from Class arParam.
     * @return mixed
     */
    public function getParam($key, $defval=null) {
        if(array_key_exists($key, $this->arParams)){
            return $this->arParams[$key];
        }
        return $defval;
    }

    /**
     * UrlWL::issetParam()
     *
     * Get bool result if $key isset in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function issetParam($key) {
        return array_key_exists($key, $this->arParams);
    }

    /**
     * UrlWL::emptyParam()
     *
     * Get bool result if $key empty in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function emptyParam($key) {
        return empty($this->arParams[$key]);
    }

    /**
     * UrlWL::unsetParam()
     *
     * Get bool result if $key empty in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function unsetParam($key) {
        if(array_key_exists($key, $this->arParams)){
            unset($this->arParams[$key]);
            return true;
        }
        return false;
    }

    /**
     * UrlWL::enableSuffix()
     *
     * Enable Url Suffix Like .html
     */
    public function enableSuffix($suffix) {
        $suffix = trim($suffix);
        if(!empty($suffix)){
            $this->suffix    = $suffix;
            $this->incSuffix = true;
        }
    }

    /**
     * UrlWL::disableSuffix()
     *
     * Disable Url Suffix Like .html
     */
    public function disableSuffix() {
        $this->incSuffix = false;
    }

    /**
     * UrlWL::getSuffix()
     *
     * Get Url Suffix Like .html If Enabled
     */
    public function getSuffix() {
        return $this->incSuffix ? $this->suffix : '';
    }

    /**
     * UrlWL::getPath()
     *
     * Get array of url path
     */
    public function getPath() {
        return $this->arPath;
    }

    /**
     * UrlWL::setPath()
     *
     * Set Path Array function.
     * @return
     */
    public function setPath( array $arPath = array() ) {
        $this->arPath = $arPath;
    }

    /**
     * UrlWL::setPath()
     *
     * Set Path Array function.
     * @return
     */
    public function addToPath( $seo_path ) {
        $seo_path = trim($seo_path);
        if($seo_path!='') $this->arPath[] = $seo_path;
    }

    /**
     * Url::buildUrl()
     *
     * Url Build function.
     * @return String
     */
    public function buildUrl($left_slashed=true, $right_slashed=false) {
        return self::toUrl($this->arPath, $this->incSuffix, $this->suffix, $this->arParams, $this->anchor, $left_slashed, $right_slashed);
    }

    /**
     * Url::toUrl()
     *
     * Url Build function.
     * @param array $arPath
     * @param bool $incSuffix
     * @param string $suffix
     * @param array $arParams
     * @param string $anchor
     * @param boolean $left_slashed
     * @param boolean $right_slashed
     * @return String
     */
    public static function toUrl(array $arPath, $incSuffix, $suffix, array $arParams, $anchor='', $left_slashed=true, $right_slashed=false) {
        if($incSuffix && $right_slashed) $right_slashed = false;
        $ishome = empty($arPath);
        $params = self::buildParams($arParams);
        $url  = ($ishome || $left_slashed) ? '/' : '';
        $url .= implode('/', $arPath);
        if($incSuffix) $url .= $suffix;
        if($right_slashed && !$ishome) $url .= '/';
        if($params!='') $url .= '?'.$params;
        if($anchor!='') $url .= '#'.$anchor;
        return $url;
    }

    /**
     * Url::ParseUrl()
     *
     * Url Parse function.
     * @return array
     */
    public static function ParseUrl($url='', $incSuffix=false, $suffix='') {
        $arr = array('arPath'=>array(), 'arParams'=>array(), 'anchor'=>'');
        if($url!=''){
            if(strpos($url, "#")!==false){
                $arr['anchor'] = end(explode('#', $url));
                $url = str_replace("#{$arr['anchor']}", '', $url);
            }
            $sep = strpos($url, "?");
            if ($sep!==false) {
                $path   = trim(substr($url, 0, $sep));
                $params = (strlen($url)>$sep+1) ? trim(substr($url, $sep+1)) : '';
                if($params!='') parse_str($params, $arr['arParams']);
            } else $path = trim($url);
            $ar = explode('/', $path);
            $cn = count($ar);
            for($i=0; $i<$cn; $i++){
                $ar[$i] = trim($ar[$i]);
                if($ar[$i]=='') continue;
                if($incSuffix && $i==$cn-1) $ar[$i] = str_replace($suffix, '', $ar[$i]);
                $arr['arPath'][] = $ar[$i];
            }
        } return $arr;
    }

    /**
     * Url::buildParams()
     *
     * Build Params String Url function.
     * @return String
     */
    public static function buildParams(array $arParams=array()) {
        $url = array();
        foreach($arParams as $k=>$v){
            if(is_array($v)) {
                $url[] = http_build_query(array($k=>$v));
            } else {
                $url[] = $k.($v!='' ? '='.$v : '');
            }
        } return (string)implode('&', $url);
    }
    /**
     * Url::encodeString()
     *
     * Encode String to Url view function.
     * @return String
     */
    public static function encodeString($str) {
        // Сначала заменяем "односимвольные" фонемы.
        $str = strtr($str, "абвгдеёзийклмнопрстуфхъыэ", "abvgdeeziyklmnoprstufh'ie");
        $str = strtr($str, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ", "ABVGDEEZIYKLMNOPRSTUFH'IE");
        // Затем - "многосимвольные".
        $str = strtr($str,
            array (
                'Г?'=>'A',  'Г‚'=>'A',  'Д‚'=>'A',  'Г„'=>'A', 'Д†'=>'C', 'Г‡'=>'C', 'ДЊ'=>'C',
                'ДЋ'=>'D',  'Д?'=>'D',  'Г‰'=>'E','Д?'=>'E', 'Г‹'=>'E', 'Дљ'=>'E', 'ГЌ'=>'I',
                'ГЋ'=>'I',  'Д№'=>'L', 'Е?'=>'N', 'Е‡'=>'N', 'Г“'=>'O', 'Г”'=>'O', 'Е?'=>'O',
                'Г–'=>'O',  'Е”'=>'R',  'Е?'=>'R', 'Е '=>'S', 'Ељ'=>'O', 'Е¤'=>'T', 'Е®'=>'U',
                'Гљ'=>'U',  'Е°'=>'U', 'Гњ'=>'U', 'Гќ'=>'Y', 'ЕЅ'=>'Z', 'Е№'=>'Z', 'ГЎ'=>'a',
                'Гў'=>'a',  'Д?'=>'a', 'Г¤'=>'a', 'Д‡'=>'c', 'Г§'=>'c', 'ДЌ'=>'c', 'ДЏ'=>'d',
                'Д‘'=>'d',  'Г©'=>'e', 'Д™'=>'e', 'Г«'=>'e', 'Д›'=>'e',  'Г­'=>'i', 'Г®'=>'i',
                'Дє'=>'l',  'Е„'=>'n', 'Е?'=>'n', 'Гі'=>'o', 'Гґ'=>'o', 'Е‘'=>'o',  'Г¶'=>'o',
                'ЕЎ'=>'s',  'Е›'=>'s', 'Е™'=>'r', 'Е•'=>'r', 'ЕҐ'=>'t', 'ЕЇ'=>'u', 'Гє'=>'u',
                'Е±'=>'u',  'Гј'=>'u', 'ГЅ'=>'y', 'Еѕ'=>'z', 'Еє'=>'z', 'Л™'=>'-', 'Гџ'=>'Ss',
                'Д„'=>'A',  'Вµ'=>'u', 'Ґ'=>'G',  'Ё'=>'Yo', 'Є'=>'E',  'Ї'=>'Yi',  'І'=>'I',
                'і' =>'i',  'ґ'=>'g',  'ё'=>'yo', '№'=>'#', 'є'=>'e',  'ї'=>'yi',  'А'=>'A',
                'Б' =>'B',  'В'=>'V',  'Г'=>'G',  'Д'=>'D',  'Е'=>'E',  'Ж'=>'Zh',  'З'=>'Z',
                'И' =>'I',  'Й'=>'Y',  'К'=>'K',  'Л'=>'L',  'М'=>'M',  'Н'=>'N',   'О'=>'O',
                'П' =>'P',  'Р'=>'R',  'С'=>'S',  'Т'=>'T',  'У'=>'U',  'Ф'=>'F',   'Х'=>'H',
                'Ц' =>'Ts', 'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Sch','Ъ'=>'',   'Ы'=>'Yi',  'Ь'=>'',
                'Э' =>'E',  'Ю'=>'Yu', 'Я'=>'Ya', 'а'=>'a',  'б'=>'b',  'в'=>'v',   'г'=>'g',
                'д' =>'d',  'е'=>'e',  'ж'=>'zh', 'з'=>'z',  'и'=>'i',  'й'=>'y',   'к'=>'k',
                'л' =>'l',  'м'=>'m',  'н'=>'n',  'о'=>'o',  'п'=>'p',  'р'=>'r',   'с'=>'s',
                'т' =>'t',  'у'=>'u',  'ф'=>'f',  'х'=>'h',  'ц'=>'ts', 'ч'=>'ch',  'ш'=>'sh',
                'щ' =>'sch','ъ'=>'',   'ы'=>'yi', 'ь'=>'',   'э'=>'e',  'ю'=>'yu',  'я'=>'ya' 
            )
        ); return $str;
    }

    /**
     * Url::stringToUrl()
     *
     * Prepare String to Url function.
     * @return String
     */
    public static function stringToUrl($str) {
        $str = preg_replace('/[^\s\w\d]/', '', $str);        
        $str = preg_replace("/[&].+?[;]/", '', $str);        
        // Кодируем строку и преобразовываем к нижнему регистру
        $str = strtolower(self::encodeString($str));
        // удаляем Экранирующие последовательности
        $str = str_replace(array("\r", "\n", "\t", "\0"), ' ', $str); 
        // заменяем тире на дефиз
        $str = str_replace("–", '-', $str); 
        // Удаляем все лишние символы
        $str = str_replace(array( '?','!',':',';','#','@','$','&','\'','"','~','`','’','%','^','*',
                                  '(',')','«','»','<','>','+','/','\\',',','.','…','{','}','[',']','|'
                                ), '', $str);        
        // Заменяем все двойные пробелы на одинарные
        $str = preg_replace('/\s+/', ' ', $str);
        // Заменяем все символы из списка на нижнее подчеркивание
        $str = trim(str_replace(array(' ', ' - ', '_-_'), '_', $str), '_'); 
        do{ // Заменяем все двойные нижние подчеркивания на одинарные
            $cnt = 0;
            $str = str_replace('__', '_', $str, $cnt); 
        } while($cnt);  
        // Возвращаем результат
        return $str;
    }
    
    /**
    * Returns whether this is an AJAX (XMLHttpRequest) request.
    * @return boolean whether this is an AJAX (XMLHttpRequest) request.
    */
    public static function isAjaxRequest(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }

}



/**
 * Description of UrlWL class. This class extend Url class
 * This class provides methods for create and manage SEO UrlWL. 
 * @author WebLife
 * @copyright 2012
 */
class UrlWL extends Url {
    
    const LANG_KEY_NAME  = 'lang';
    const USER_SEOPREFIX = 'user_';
    const CAT_KEY_NAME   = 'catid';
    const SORT_KEY_NAME  = 'sort';
    const VIEW_KEY_NAME  = 'view';
    const PAGES_KEY_NAME = 'pages';
    const PAGES_ALL_VAL  = 'all';
    
    const LANG_PATH_IDX  = 0;
    const HOME_CATID     = 1;
    const ERROR_CATID    = 2;
    const PAGE_TYPE_STATIC = 0;
    const PAGE_TYPE_AUTO   = 1;
    const PAGE_TYPE_AJAX   = 2;

    private $lang         = '';
    private $defl         = '';
    private $base         = '';
    private $ajax         = null;
    private $page         = null;
    private $module       = null;
    private $itemID       = null;
    private $assortID     = null;
    private $parentID     = null;
    private $categoryID   = null;
    private $Filters      = null;
    private $showLang     = false;
    private $arLangs      = array();
    private $arCatPath    = array();
    private $arNavPath    = array();
    private $arBreadCrumb = array();
    private $_arPath      = array();


    /**
     * UrlWL::__construct()
     *
     * Object Construct function.
     * @return
     */
    public function __construct(array $arLangs, $showLang=false, $incSuffix=false, $suffix='') {
        parent::__construct((isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''), $incSuffix, $suffix);
        $this->base     = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $this->showLang = (bool)$showLang;
        $this->arLangs  = $arLangs;
        $this->setDefaultLang();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                if(array_key_exists(self::LANG_KEY_NAME, $_GET)) 
                    $this->setLang($_GET[self::LANG_KEY_NAME]);
                break;
            case 'FRONTEND':
                if(count($this->arParams)) $HTTP_GET_VARS = $_GET = $this->arParams;
                if(array_key_exists(self::LANG_PATH_IDX, $this->arPath)) 
                    $this->setLang($this->arPath[self::LANG_PATH_IDX]);
                if(array_key_exists(self::LANG_KEY_NAME, $this->arParams)) 
                    $this->setLang($this->arParams[self::LANG_KEY_NAME]);
                break;
            default: break;
        }
        // копируем данные по филььтрам
        $data = array();
        if(isset($this->arParams[UrlFilters::KEY_URL_CASE])){
            $data = $this->arParams[UrlFilters::KEY_URL_CASE];
            unset($this->arParams[UrlFilters::KEY_URL_CASE]);
        }
        $this->Filters = new UrlFilters($data);
    }

    /**
     * UrlWL::__destruct()
     *
     * Object Destruct function.
     * @return
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * UrlWL::__clone()
     *
     * Object Clone function.
     * @return
     */
    public function __clone() {
        $this->url          = '';
        $this->lang         = '';
        $this->arPath       = array();
        $this->arCatPath    = array();
        $this->arNavPath    = array();
        $this->arBreadCrumb = array();
        $this->Filters      = clone $this->Filters;
    }

    /**
     * UrlWL::copy()
     *
     * Object Clone function.
     * @return \UrlWL cloned
     */
    public function copy() {
        $cloned = clone $this;
        $cloned->url          = $this->url;
        $cloned->lang         = $this->lang;
        $cloned->arPath       = $this->arPath;
        $cloned->arCatPath    = $this->arCatPath;
        $cloned->arNavPath    = $this->arNavPath;
        $cloned->arBreadCrumb = $this->arBreadCrumb;
        return $cloned;
    }

    /**
     * UrlWL::init()
     *
     * Object Init function.
     * @return
     */
    public function init( DbConnector $DB ) {
        // сохраняем оригинальный путь
        $this->_arPath = $this->arPath;
        // инициализация остальных параметров
        $arParent = $arCategory = $arItem = $arAssort = $arFilter = false;
        foreach($this->arPath as $idx=>$part){
            $founded = false;
            // язык и страница
            if(($idx==self::LANG_PATH_IDX && $this->setLang($part)) || (is_numeric($part) && $arCategory && $this->setPage($part))){
                $founded = true;
                $this->unSetPathItem($part, $idx, false);
            } else {
                // получаем категорию
                $query = 'SELECT * FROM `'.MAIN_TABLE.'` WHERE `seo_path`=\''.$DB->ForSql($part).'\' LIMIT 1';
                if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && (!$arCategory || $arCategory['id'] == $row['pid']) && $row['active']){
                    $founded    = true;
                    $arParent   = $arCategory;
                    $arCategory = $row;
                    $this->initCategory($arCategory);
                    if($this->module == 'catalog'){
                        $arCategory['iteratedFilters'] = null;
                        $arCategory['selectedFilters'] = array();
                        $arCategory['filters'] = self::getCategoryFilters($arCategory['id'], UrlFilters::LIST_TYPE_SEO);
                        $this->Filters->setCategoryFilters($arCategory['filters']);
                        foreach($arCategory['filters'] as $key => $val){
                            if($val['tid'] == UrlFilters::TYPE_CATEGORY && count($val['values']) > 1){
                                $this->Filters->unsetCategoryFilter($key);
                            }
                        }
                    }
                    if(count($arCategory['arPath']) == 1 && (count(self::buildCategoryPath($row)) != 1 || reset($arCategory['arPath']) != reset($row['arPath']))){
                        $founded = false;
                    }
                } else if ($arCategory && $this->module && ($tbl = strtoupper($this->module).'_TABLE') && defined($tbl)){
                    switch ($this->module) {
                        case 'users':
                            $query = 'SELECT *, CONCAT(\''.self::USER_SEOPREFIX.'\', `id`) `seo_path` FROM `'.constant($tbl).'` WHERE `id`=\''.intval(str_replace(self::USER_SEOPREFIX, '', $part)).'\' LIMIT 1';
                            if(strpos($part, self::USER_SEOPREFIX)!==FALSE && $DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $row['active']){
                                $founded         = true;
                                $arItem          = $row;
                                $this->itemID    = intval($row['id']);
                                $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $row), $row['title']);
                                $this->addToNavPath($row['seo_path']);
                            } break;
                            
                        case 'catalog':
                            // Если товар найден - ищем по ассортиментам
                            if($arItem){
                                $query = 'SELECT * FROM `'.ASSORTMENT_TABLE.'` WHERE `seo_path`=\''.$DB->ForSql($part).'\' LIMIT 1';
                                if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $arItem['id'] == $row['pid']) {
                                    $founded        = true;
                                    $arAssort       = $row;
                                    $this->assortID = intval($row['id']);
                                    $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $row), $row['title']);
                                    $this->addToNavPath($row['seo_path']);
                                }                           
                                break;
                                
                            } else {
                                // Вначале ищем по товарам
                                $query = 'SELECT * FROM `'.constant($tbl).'` WHERE `seo_path`=\''.$DB->ForSql($part).'\' LIMIT 1';
                                if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $row['active']){
                                    $founded      = true;
                                    $arItem       = $row;
                                    $this->itemID = intval($row['id']);
                                    $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $row), $row['title']);
                                    $this->addToNavPath($row['seo_path']);
                                    break;
                                }
                                // далее, если не нашли по товарам - ищем по фильтрам исходя из настройки категории
                                // вначале проверяем на Range сеопуть 
                                $range = UrlFiltersRange::parseSeoPath($part);
                                // далее строим union запрос по брендам, категориям и значениям атрибутов
                                $query = '(SELECT `id`, `title`, `seo_path` FROM `'.BRANDS_TABLE.'` WHERE `active`=1 AND `seo_path`=\''.$DB->ForSql($part).'\')
                                            UNION ALL
                                          (SELECT `id`, `title`, `seo_path` FROM `'.ATTRIBUTES_VALUES_TABLE.'` WHERE `seo_path`=\''.$DB->ForSql($part).'\')
                                            LIMIT 1';
                                if($range || ($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']))){
                                    // смотрим есть ли настройки категории по фильтрам
                                    if($arCategory['iteratedFilters'] === null){
                                        $arCategory['iteratedFilters'] = $arCategory['filters'];
                                        foreach((isset($arParent['filters']) ? $arParent['filters'] : array()) as $key => $val){
                                            if(array_key_exists($arCategory['seo_path'], $val['values'])){
                                                $arCategory['selectedFilters'][$val['id']][] = $val['values'][$arCategory['seo_path']]['alias'];
                                                $this->unSetPathItem($arCategory['seo_path'], null, false);
                                            }
                                        }
                                    }
                                    // смотрим по настройкам категории
                                    $filters = array();
                                    foreach($arCategory['iteratedFilters'] as $key => $val){
                                        $val['seo_path'] = $part;
                                        if(!$founded){
                                            if(array_key_exists($part, $val['values'])){
                                                $founded = true;
                                                $title   = $row ? $row['title'] : $val['values'][$part]['title'];
                                                $arCategory['selectedFilters'][$val['id']][] = $val['values'][$part]['alias'];
                                            } else if($range && !$range->auto && $range->id==$key){
                                                $founded = true;
                                                $title = $val['title'] . ' '.LABEL_FROM.' '.$range->min.($range->max ? ' '.LABEL_TO.' ' . $range->max: '');
                                                $arCategory['selectedFilters'][$val['id']][UrlFiltersRange::KEY_MIN] = $range->min;
                                                $arCategory['selectedFilters'][$val['id']][UrlFiltersRange::KEY_MAX] = $range->max;
                                            }
                                            if($founded){
                                                $arFilter = $val;
                                                $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $val), ucfirst($title));
                                                $this->addToNavPath($val['seo_path']);
                                                $this->unSetPathItem($val['seo_path'], $idx, false);
                                            }
                                        } else if($founded){
                                            $filters[$key] = $val;
                                        }
                                    }
                                    $arCategory['iteratedFilters'] = $filters;
                                    if($founded){
                                        break;
                                    }
                                }
                            }
                            // если ничего не нашли, то прерываем чтобы не попасть в default
                            break;
                            
                        default:
                            $query = 'SELECT * FROM `'.constant($tbl).'` WHERE `seo_path`=\''.$DB->ForSql($part).'\' LIMIT 1';
                            if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $row['active']){
                                $founded      = true;
                                $arItem       = $row;
                                $this->itemID = intval($row['id']);
                                $this->addToBreadCrumbs($this->buildItemUrl(array_merge($arCategory, array('arPath'=>$this->getNavPath())), $row), $row['title']);
                                $this->addToNavPath($row['seo_path']);
                            } break;
                    }
                }
            }
            // если не найдено - прерываем и выдаем ошибку
            if(!$founded){
                $this->addErrorCategory();
                break;
            }
        }
        // определяем все ли в порядке?
        $founded = ($this->getCategoryId() && $this->getCategoryId() != self::ERROR_CATID);
        // если  найдено произмодим необходимые манипуляции
        if($founded){
            // инициализация переменной ajax
            $this->ajax = array_key_exists('ajax', $this->arParams) ? 1 : 0;
            // донастройка категории если модуль каталог
            if($this->module == 'catalog' && !empty($arCategory['selectedFilters'])){
                $this->Filters->prependAttributes($arCategory['selectedFilters']);
            }
        }
        return $founded;
    }

    /**
     * UrlWL::buildUrl()
     *
     * UrlWL Build function.
     * @return String
     */
    public function buildUrl($left_slashed=true, $right_slashed=false) {
        $data = self::prepareUrlData(array('arPath'=>$this->arPath, 'arParams'=>$this->arParams), $this->page, false, $this->copyFilters());
        return self::toUrl($data['arPath'], $this->showLang, $this->lang, $this->incSuffix, $this->suffix, $data['arParams'], $this->anchor, $left_slashed, $right_slashed);
    }

    /**
     * UrlWL::toUrl()
     *
     * UrlWL Build function.
     * @param array $arPath
     * @param type $showLang
     * @param type $lang
     * @param bool $incSuffix
     * @param string $suffix
     * @param array $arParams
     * @param string $anchor
     * @param boolean $left_slashed
     * @param boolean $right_slashed
     * @return String
     */
    public static function toUrl(array $arPath, $showLang, $lang, $incSuffix, $suffix, array $arParams, $anchor='', $left_slashed=true, $right_slashed=false) {
        if($showLang){
            array_unshift($arPath, $lang);
        }
        return parent::toUrl($arPath, $incSuffix, $suffix, $arParams, $anchor, $left_slashed, $right_slashed);
    }

    /**
     * UrlWL::buildQuery()
     *
     * Build Params String Url function.
     * @return String
     */
    public function buildQuery() {
        $data = self::prepareUrlData(array('arPath'=>$this->arPath, 'arParams'=>$this->arParams), $this->page, false, $this->copyFilters());
        return parent::toUrl(array(), $this->incSuffix, $this->suffix, $data['arParams'], $this->anchor, false, false);
    }

    /**
     * UrlWL::buildItemUrl()
     *
     * Build Menu Item Url function.
     * @param array $arCategory
     * @param array $arItem
     * @param mixed $params array or string
     * @param string $anchor
     * @param int $page
     * @param bool $forPager
     * @return String
     */
    public function buildItemUrl(array $arCategory, array $arItem, $params=null, $anchor='', $page=1, $forPager=false, UrlFilters $Filters = null) {
        $data = self::prepareCategoryData($arCategory, $params);
        if(empty($data['url'])){
            if(!empty($arItem['id'])){
                $data['arPath'][] = $arItem['seo_path'];
            }
            $data = self::prepareUrlData($data, $page, $forPager, $Filters);
            $data['url'] = self::toUrl($data['arPath'], $this->showLang, $this->lang, ($forPager ? false : $this->incSuffix), $this->suffix, $data['arParams'], $anchor);
        }
        return $data['url'];
    }

    /**
     * UrlWL::buildCategoryUrl()
     *
     * Build Menu Category Item Url function.
     * @param array $arCategory
     * @param mixed $params array or string
     * @param string $anchor
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @return String
     */
    public function buildCategoryUrl(array $arCategory, $params=null, $anchor='', $page=1, $forPager=false, UrlFilters $Filters = null) {
        $data = self::prepareCategoryData($arCategory, $params);
        if(empty($data['url'])){
            $data = self::prepareUrlData($data, $page, $forPager, $Filters);
            $data['url'] = self::toUrl($data['arPath'], $this->showLang, $this->lang, ($forPager ? false : $this->incSuffix), $this->suffix, $data['arParams'], $anchor);
        }
        return $data['url'];
    }

    /**
     * UrlWL::prepareCategoryData()
     *
     * Build Category Data Url function.
     * @param array $arCategory
     * @param mixed $params array or string
     * @return array
     */
    private static function prepareCategoryData(array & $arCategory, $params) {
        $data = array('url'=>'');
        if(!empty($arCategory['redirectid'])){
            $arCategory = self::getCategoryById($arCategory['redirectid']);
        }
        if(!empty($arCategory['redirecturl'])){
            $data['url'] = $arCategory['redirecturl'];
        } else if($arCategory['id'] == self::HOME_CATID){
            $data['url'] = '/';
        } else {
            $data['arPath'] = self::buildCategoryPath($arCategory);
            $data['arParams'] = array();
            if($params){
                if(is_array($params)){
                    $data['arParams'] = $params;
                } else if(is_string($params)){
                    parse_str($params, $data['arParams']);
                }
            }
            if($arCategory['pagetype'] == self::PAGE_TYPE_AJAX) {
                $data['arParams']['ajax']='';
            }
        }
        return $data;
    }

    /**
     * UrlWL::prepareUrlData()
     *
     * Build Category Data Url function.
     * @param array $arData
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @return array
     */
    private static function prepareUrlData(array $arData, $page, $forPager=false, UrlFilters $Filters = null) {
        if($Filters){
            foreach($Filters->slicePath() as $val){
                $arData['arPath'][] = $val;
            }
            $filterData = $Filters->toArray();
            if($filterData){
                $arData['arParams'][UrlFilters::KEY_URL_CASE] = $filterData;
            }
        }
        if($page > 1 /*&& !$forPager*/){
            $arData['arPath'][] = $page;
        }
        return $arData;
    }

    /**
     * UrlWL::buildCategoryPath()
     *
     * Build Category array Path from Root
     * @return Array
     */
    public static function & buildCategoryPath(array & $arCategory, $table=MAIN_TABLE, $forse=false) {
        $arPath = array();
        if(!empty($arCategory['arPath'])){
            $arPath = $arCategory['arPath'];
        } else {
            if(!empty($arCategory['seo_path'])){
                if(!empty($arCategory['pid'])){
                    static $arPathes = array();
                    $id = $pid = $arCategory['pid'];
                    if($forse || !isset($arPathes[$table]) || !isset($arPathes[$table][$id])){
                        $arPathes[$table][$id] = array();
                        while($pid > 0){
                            $query = "SELECT `id`, `pid`, TRIM(`seo_path`) `seo_path` FROM `".$table."` WHERE TRIM(`seo_path`)<>'' AND `id` = '{$pid}' LIMIT 1";
                            $result = mysql_query($query);
                            if(!mysql_num_rows($result)) break;
                            $row = mysql_fetch_assoc($result);
                            array_unshift($arPathes[$table][$id], $row['seo_path']);
                            $pid = $row['pid'];
                        }
                    }
                    // получаем из статики
                    $arPath = $arPathes[$table][$id];
                }
                // добавляем текущий
                $arPath[] = $arCategory['seo_path'];
            }
            $arCategory['arPath'] = $arPath;
        } return $arPath;
    }

    /**
     * UrlWL::buildPagerUrl()
     *
     * Build Menu Item Url function for Pager.
     * @return String
     */
    public function buildPagerUrl(array $arCategory) {
        return $this->buildCategoryUrl($arCategory, null, '', 1, true);
    }

    /**
     * UrlWL::getCategoryById()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @return array
     */
    public function getCategoryById($id, $withText=false, $forse=false) {
        return self::getCategoryByIdWithSeoPath($id, $withText, MAIN_TABLE, $forse);
    }

    /**
     * UrlWL::getCategoryByIdWithSeoPath()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @return array
     */
    public static function getCategoryByIdWithSeoPath($id, $withText=false, $table=MAIN_TABLE, $forse=false) {
        $arCategory = array();
        if($id > 0){
            static $arCategories = array();
            if($forse || !isset($arCategories[$table]) || !isset($arCategories[$table][$id])){
                $query = "SELECT `id`, `pid`, `redirectid`, `title`, `menutype`, `pagetype`, `text`
                               , `module`, `image`, TRIM(`redirecturl`) `redirecturl`, TRIM(`seo_path`) `seo_path`
                            FROM `".$table."`
                            WHERE `id` = '{$id}' LIMIT 1";
                $result = mysql_query($query);
                if($result && mysql_num_rows($result)>0){
                    $arCategory = mysql_fetch_assoc($result);
                    self::buildCategoryPath($arCategory, $table, $forse);
                    $arCategories[$table][$id] = $arCategory;
                }
            } else {
                $arCategory = $arCategories[$table][$id];
            }
            if(!$withText && isset($arCategory['text'])){
                unset($arCategory['text']);
            }
        } return $arCategory;
    }

    /**
     * UrlWL::getCategoryFilters()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @param int $id
     * @param int $type : 1 - default list; 2 - seo settings
     * @return array
     */
    public static function getCategoryFilters($id, $type=UrlFilters::LIST_TYPE_DEFAULT) {
        $filters = array();
        if(($id = intval($id)) > 0 && ($type = intval($type)) > 0){
            $query = 'SELECT f.`id`, f.`title`, a.*, cf.`order` `seq`, f.`tid`/*, ft.`title` `ttitle`*/
                FROM `'.CATEGORY_FILTERS_TABLE.'` cf
                JOIN `'.FILTERS_TABLE.'` f ON f.`id`=cf.`fid`
                /*JOIN `'.FILTER_TYPES_TABLE.'` ft ON ft.`id`=f.`tid`*/
                LEFT JOIN (
                    SELECT a.`id` `aid`, a.`title` `atitle`, ag.`id` `agid`, ag.`title` `agtitle`, ag.`descr` `agdescr`/*, at.`id` `atid`, at.`title` `attitle`, at.`value` `atvalue`*/
                    FROM `'.CATEGORY_ATTRIBUTES_TABLE.'` ca
                    JOIN `'.ATTRIBUTES_TABLE.'` a ON a.`id`=ca.`aid`
                    JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON ag.`id`=a.`gid` AND ag.`active`=1
                  /*JOIN `'.ATTRIBUTE_TYPES_TABLE.'` at ON at.`id`=a.`tid`*/
                    JOIN `'.CATEGORY_ATTRIBUTE_GROUPS_TABLE.'` cag ON cag.`gid`=a.`gid` AND cag.`cid` = "'.$id.'"
                    WHERE ca.`cid` = "'.$id.'"
                ) a ON a.`aid`=f.`aid`
                WHERE cf.`cid` = "'.$id.'" AND  cf.`type` = "'.$type.' AND (f.`aid`=0 OR a.`aid` IS NOT NULL)"
                ORDER BY cf.`order` 
            ';
            $result1 = mysql_query($query);
            while($row1 = mysql_fetch_assoc($result1)){
                // получаем возможные значения с сеопутями
                $row1['values'] = array();
                switch ($row1['tid']) {
                    case UrlFilters::TYPE_BRAND:
                        $query = 'SELECT `id`, `title`, `seo_path`, `id` `alias` FROM `'.BRANDS_TABLE.'` WHERE `active`=1 AND `seo_path`<>"" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                    case UrlFilters::TYPE_CATEGORY:
                        $query = 'SELECT `id`, `title`, `seo_path`, `id` `alias` FROM `'.MAIN_TABLE.'` WHERE `active`=1 AND `seo_path`<>"" AND `pid`="'.$id.'" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                    case UrlFilters::TYPE_PRICE:
                    case UrlFilters::TYPE_NUMBER:
                        $query = 'SELECT `id`, `title`, IF(`vmin` IS NULL, 0, `vmin`) `vmin`, IF(`vmax` IS NULL, 0, `vmax`) `vmax`, `id` `alias` FROM `'.RANGES_TABLE.'` WHERE `fid`="'.$row1['id'].'" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row2['seo_path'] = UrlFiltersRange::generateSeoPath(($row1['tid']==UrlFilters::TYPE_PRICE ? UrlFiltersRange::SEO_AUTO_PRICE : UrlFiltersRange::SEO_AUTO_RANGE), $row2['id'], $row2['vmin'], $row2['vmax']);
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                    default:
                        $query = 'SELECT av.`id`, av.`title`, av.`seo_path`, (
                                    SELECT pa.`alias` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa 
                                    WHERE pa.`aid`=av.`aid` AND pa.`value`=av.`title` GROUP BY pa.`alias` LIMIT 1
                                ) `alias` 
                             FROM `'.ATTRIBUTES_VALUES_TABLE.'` av 
                             WHERE av.`aid`="'.$row1['aid'].'" AND `seo_path`<>""
                             ORDER BY av.`order`
                        ';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            isset($row2['alias']) or $row2['alias'] = '0';
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                }
                $filters[$row1['id']] = $row1;
            }
        } return $filters;
    }

    /**
     * UrlWL::initCategory()
     *
     * Get Category Array with SEO Path Array From Root function.
     */
    public function initCategory(array & $arCategory) {
        $this->module     = $arCategory['module'];
        $this->parentID   = intval($arCategory['pid']);
        $this->categoryID = intval($arCategory['id']);
        $this->addToCategoryNavPath($arCategory['seo_path']);
        $this->addToNavPath($arCategory['seo_path']);
        $arCategory['arPath'] = $this->getNavPath();
        $this->addToBreadCrumbs($this->buildCategoryUrl($arCategory), $arCategory['title']);
    }

    /**
     * UrlWL::addErrorCategory()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @return array
     */
    protected function addErrorCategory() {
        $this->page         = null;
        $this->itemID       = null;
        $this->assortID     = null;
        $this->module       = null;
        $this->parentID     = 0;
        $this->categoryID   = self::ERROR_CATID;
        if(($row = self::getErrorCategory())!==array()){
            $this->initCategory($row);
            return true;
        }
        return false;
    }

    /**
     * UrlWL::getErrorCategory()
     *
     * Get Category Array
     * @return array
     */
    public static function getErrorCategory() {
        $query = 'SELECT * FROM `'.MAIN_TABLE.'` WHERE `id`='.self::ERROR_CATID.' LIMIT 1';
        $result = mysql_query($query);
        return ($result && mysql_num_rows($result)>0) ? mysql_fetch_assoc($result) : array();
    }

    /**
     * UrlWL::strToUrl()
     *
     * Prepare String to Url function.
     * @return String
     */
    public function strToUrl($str, $prefix='item') {
        $str = trim(parent::stringToUrl($str));
        // Проверяем, является ли item seo_path числом, если да, то дописываем значение $prefix
        if(is_numeric($str)) $str = strtolower(trim($prefix)).'_'.$str;
        // Возвращаем результат
        return $str;
    }

    /**
     * UrlWL::strToUniqueUrl()
     * Prepare String to Url function.
     * @param DbConnector $connector
     * @param string $seo_str
     * @param string $num_prefix
     * @param string $item_tbl
     * @param int $item_id
     * @param bool $lang_sync
     * @return String
     */
    public function strToUniqueUrl(DbConnector $connector, $seo_str, $num_prefix='item', $item_tbl='', $item_id=0, $lang_sync=true) {
        // Возвращаем результат
        return $this->getUniqueSeoPath($connector, $this->strToUrl($seo_str, $num_prefix), $item_tbl, $item_id, /*$lang_sync*/true);
    }

    /**
     * UrlWL::cleanUrlFromLangs()
     *
     * Clean Url From Langs function.
     * @return String
     */
    public function & cleanUrlFromLangs($url=''){
        $url = empty($url) ? $this->url : trim($url);
        if(empty($url)) return $url;

        $arReplaceLangs = array();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                foreach($this->arLangs as $ln){
                    $arReplaceLangs[] = '?lang='.$ln;
                    $arReplaceLangs[] = '&lang='.$ln;
                }
                break;
            case 'FRONTEND':
                foreach($this->arLangs as $ln)
                    $arReplaceLangs[] = "/$ln/";
                break;
            default: break;
        } 
        $url = str_replace($arReplaceLangs, '', $url);
        return $url;
    }

    /**
     * UrlWL::createLangsUrls()
     *
     * Create Langs Url Array to redirect function.
     * @return String
     */
    public function & createLangsUrls(DbConnector $DB){
        $arLangsUrls = array();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                $url = $this->cleanUrlFromLangs();
                $lpref = (strpos($url, '?')===FALSE) ? '?' : '&';
                foreach($this->arLangs as $ln)
                    $arLangsUrls[$ln] = $url.$lpref.'lang='.$ln;
                break;
            case 'FRONTEND':
                foreach($this->arLangs as $ln){
                    $url = '/'.$ln.'/';
                    if($this->lang==$ln) $url = $this->url;
                    elseif($this->categoryID > self::HOME_CATID) {
                        $cloned  = clone $this;
                        $cloned->setLang($ln);
                        $arCategory = self::getCategoryByIdWithSeoPath($cloned->categoryID, false, DbConnector::replaceLang(MAIN_TABLE, $this->lang, $cloned->lang, DBTABLE_LANG_SEP));
                        if($arCategory){
                            $cloned->module = $arCategory['module'];
                            // ищем среди модулей
                            if($cloned->itemID && $cloned->module && ($tbl = strtoupper($cloned->module).'_TABLE') && defined($tbl)){
                                $query = 'SELECT * FROM `'.DbConnector::replaceLang(constant($tbl), $this->lang, $cloned->lang, DBTABLE_LANG_SEP).'` WHERE `id`=\''.$cloned->itemID.'\' LIMIT 1';
                                if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $row['active']){
                                    switch($cloned->module) {
                                        case 'users':
                                            $row['seo_path'] = self::USER_SEOPREFIX.$row['id'];
                                            $arCategory['arPath'][] = $row['seo_path'];
                                            break;
                                        case 'catalog':
                                            $arCategory['arPath'][] = $row['seo_path'];
                                            if($cloned->assortID){
                                                $query = 'SELECT * FROM `'.DbConnector::replaceLang(ASSORTMENT_TABLE, $this->lang, $cloned->lang, DBTABLE_LANG_SEP).'` WHERE `id`=\''.$cloned->assortID .'\' LIMIT 1';
                                                if($DB->Query($query) && ($row = $DB->fetchAssoc()) && !empty($row['id']) && $cloned->itemID == $row['pid']) {
                                                    $arCategory['arPath'][] = $row['seo_path'];
                                                } else {
                                                    $arCategory = null;
                                                }
                                            }
                                            break;
                                        default:
                                            $arCategory['arPath'][] = $row['seo_path'];
                                            break;
                                    }
                                } else {
                                    $arCategory = null;
                                }
                            }
                            /** @todo здесь нужно будет доделать формирование ссылки с фильтрами. пока они очищаются */
                        }
                        if($arCategory){
                            $cloned->Filters->reset();
                            $cloned->setPath($arCategory['arPath']);
                            $url = $cloned->buildUrl();
                        }
                    }
                    $arLangsUrls[$ln] = $url;
                } break;
            default: break;
        } return $arLangsUrls;
    }
    
    
    /**
     * @param DbConnector $connector
     * @param bool $all_langs
     * @return array
     */
    private function getSeoPathTables (DbConnector $connector, $all_langs=true) {
        $tables = array();
        $allTables = $connector->getTables();
        $connector->Query('SELECT * FROM `' . MODULES_PARAMS_TABLE . '` WHERE `seogroup`=1');
        while ($row = $connector->fetchAssoc()) {
            if(defined($row['seotable']) && ($table = constant($row['seotable']))){
                if($all_langs){
                    foreach($this->arLangs as $ln){
                        $tbl = ($this->lang == $ln ? $table : DbConnector::replaceLang($table, $this->lang, $ln, DBTABLE_LANG_SEP));
                        if(in_array($tbl, $allTables)){
                            $tables[$table][$ln] = $tbl;
                        }
                    }
                } else if(in_array($table, $allTables)){
                    $tables[$table][$this->lang] = $table;
                }
            }
        }
        return $tables;
    }
    
    
    /**
     * function provided for quick getting count of existing items
     * with the same seo_path useful in creating or editing operations
     * @param DbConnector $connector
     * @param string $seopath
     * @param string $seotable
     * @param int $itemID
     * @param bool $lang_sync
     * @return bool
     */
    public function isUsedSeoPath (DbConnector $connector, $seopath, $seotable='', $itemID=0, $lang_sync=true) {
        if(UrlFiltersRange::checkSeoPath($seopath)){
            $used = true;
        } else {
            $queries = array();
            foreach($this->getSeoPathTables($connector, $lang_sync) as $key=>$data){
                foreach($data as $lang=>$table){
                    $queries[] = 'SELECT COUNT(`id`) FROM `'.$table.'` WHERE `seo_path`="'.$seopath.'"'.(($itemID && $seotable==$key) ? ' AND `id`<>'.$itemID : '');
                }
            }
            $used = ($queries && $connector->Query('SELECT (('.implode(') + (', $queries).')) AS `cnt`') && $connector->fetchResult());
        }
        return $used;
    }
    
    /*
     * function provided for creating unique seo path for item
     * @param DbConnector $connector
     * @param string $seopath
     * @param string $seotable
     * @param int $itemID
     * @param bool $lang_sync
     * @return string
     */
    public function getUniqueSeoPath (DbConnector $connector, $seopath, $seotable='', $itemID=0, $lang_sync=true) {
        do {
            $used = $this->isUsedSeoPath($connector, $seopath, $seotable, $itemID, $lang_sync);
            if($used){
                $seopath = self::generateUniqueSeoPath($seopath, true);
            }
        } while($used);
        return $seopath;
    }
    
    /*
     * function provided for creating unique seo path for item
     * @param string $seopath
     * @param bool $update_ts
     * @return string
     */
    public static  function generateUniqueSeoPath ($seopath, $update_ts=true) {
        $ts_curr = date('ymdHis'); $ts_sep = '_'; $ts_length = strlen($ts_curr); 
        $ts_mask = '/^.*?('.$ts_sep.'[\d]{'.$ts_length.'})/'; $matches = array();
        if(preg_match($ts_mask, $seopath, $matches)){
            if($update_ts){
                $seopath = substr($seopath, 0, strpos($seopath, $matches[1]));
            } else {
                $ts_curr = trim($matches[1], $ts_sep);
            }
        }
        $seopath .= $ts_sep.$ts_curr;
        return $seopath;
    }

    /**
     * UrlWL::setDefaultLang()
     *
     * Set Default Site Lang from arLangs.
     * @return
     */
    public function setDefaultLang() {
        $this->defl = reset($this->arLangs);
    }


    /**
     * UrlWL::getDefaultLang()
     *
     * Get Default Site Lang.
     * @return String
     */
    public function getDefaultLang() {
        return $this->defl;
    }

    /**
     * UrlWL::setLang()
     *
     * Set Current Site Lang function.
     * @return
     */
    public function setLang( $lang ) {
        if(in_array($lang, $this->arLangs)){
            $this->lang = $lang;
            return true;
        } return false;
    }

    /**
     * UrlWL::getLang()
     *
     * Get Current Site Lang function.
     * @return
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * UrlWL::setLangs()
     *
     * Set Array Site Langs Keys function.
     * @return
     */
    public function setLangs( array $arLangs = array() ) {
        $this->arLangs = $arLangs;
    }

    /**
     * UrlWL::getLangs()
     *
     * Get array Site Langs Keys function.
     * @return
     */
    public function getLangs() {
        return $this->arLangs;
    }

    /**
     * UrlWL::setPage()
     *
     * Set Current Site Lang function.
     * @return
     */
    public function setPage( $page ) {
        if(($page = intval($page))>0){
            $this->page = $page ;
            return true;
        } return false;
    }

    /**
     * UrlWL::getPage()
     *
     * Get Current Page number function.
     * @return
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * UrlWL::getBaseUrl()
     *
     * Get Current Base site url function.
     * @return
     */
    public function getBaseUrl($protocol='http://', $setSep=false) {
        return $protocol.$this->base.($setSep ? '/' : '');
    }
    /**
     * UrlWL::getBreadCrumbs()
     *
     * Get array Bread Crumbs function. 
     * @return
     */
    public function getBreadCrumbs() {
        return $this->arBreadCrumb;
    }

    /**
     * UrlWL::addToBreadCrumbs()
     *
     * Add item array to Bread Crumbs Array function. 
     * @return
     */
    public function addToBreadCrumbs($url, $title) {
        $this->arBreadCrumb = self::addBreadCrumbs($this->arBreadCrumb, $title, $url);
    }

    /**
     * UrlWL::addBreadCrumbs()
     *
     * Add item array to Bread Crumbs Array function. 
     * @param array $arBreadCrumb
     * @param type $title
     * @param type $url
     * @return array
     */
    /**
     * 
     */
    public static function addBreadCrumbs(array $arBreadCrumb, $title, $url) {
        if($title){
            $arBreadCrumb[$url] = $title;
        }
        return $arBreadCrumb;
    }

    /**
     * UrlWL::addToBreadCrumbs()
     *
     * Add item to Bread Crumbs Array function at first position. 
     * @return $this
     */
    public function unShiftToBreadCrumbs($title, $url) {
        if($title){
            $arr = array($url => $title);
            foreach($this->arBreadCrumb as $key=>$val){
                $arr[$key] = $val;
            }
            $this->arBreadCrumb = $arr;
        }
    }

    /**
     * UrlWL::getCategoryParentId()
     *
     * Get Current Category Parent ID function.
     * @return int
     */
    public function getCategoryParentId() {
        return $this->parentID===null ? 0 : $this->parentID;
    }

    /**
     * UrlWL::setCategoryParentId()
     *
     * Set Current Category Parent ID function.
     */
    public function setCategoryParentId($pid) {
        $this->parentID = intval($pid);
    }

    /**
     * UrlWL::getCategoryId()
     *
     * Get Current Category ID function.
     * @return int
     */
    public function getCategoryId() {
        return $this->categoryID===null ? self::HOME_CATID : $this->categoryID;
    }

    /**
     * UrlWL::setCategoryId()
     *
     * Set Current Category ID function.
     */
    public function setCategoryId($catid) {
        $this->categoryID = $catid>0 ? intval($catid) : null;
    }

    /**
     * UrlWL::getCategoryNavPath()
     *
     * Get Current Category Navigation Path Array function.
     * @return int
     */
    public function getCategoryNavPath() {
        return $this->arCatPath;
    }

    /**
     * UrlWL::addToCategoryNavPath()
     *
     * Add string to Category Navigation Path Array function.
     * @return int
     */
    public function addToCategoryNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!==''){
            $this->arCatPath[] = $seo_path;
        }
    }

    /**
     * UrlWL::unShiftToCategoryNavPath()
     *
     * Add string to begin Category Navigation Path Array function.
     * @return int
     */
    public function unShiftToCategoryNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!='') array_unshift($this->arCatPath, $seo_path);
    }

    /**
     * UrlWL::getNavPath()
     *
     * Get Current Navigation Path Array function.
     * @return int
     */
    private function getNavPath() {
        return $this->arNavPath;
    }

    /**
     * UrlWL::addToNavPath()
     *
     * Add string to Navigation Path Array function.
     * @return int
     */
    private function addToNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!==''){
            $this->arNavPath[] = $seo_path;
        }
    }

    /**
     * UrlWL::unShiftToNavPath()
     *
     * Add string to begin Navigation Path Array function.
     * @return int
     */
    private function unShiftToNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!=''){
            array_unshift($this->arNavPath, $seo_path);
        }
    }

    /**
     * UrlWL::_unSetPathItem()
     *
     * UnSet Path Item from $arr Array
     * @return array
     */
    private static function _unSetPathItem(array $arr, $path) {
        if(($path = trim($path)) !== ''){
            $ar = array_values($arr); $arr = array();
            for ($i = 0; $i < count($ar); $i++) {
                if($ar[$i] != $path){
                    $arr[] = $ar[$i];
                }
            }
        }
        return $arr;
    }

    /**
     * UrlWL::unSetPathItem()
     *
     * UnSet Path Item from $arPath Array
     */
    public function unSetPathItem($path, $idx=null, $reindex=true) {
        if($reindex){
            $this->arPath = self::_unSetPathItem($this->arPath, $path);
        } elseif($idx !== null && isset($this->arPath[$idx])){
            unset($this->arPath[$idx]);
        } elseif(($path = trim($path)) !== ''){
            foreach($this->arPath as $idx => $part){
                if($part == $path){
                    unset($this->arPath[$idx]);
                }
            }
        }
    }

    /**
     * UrlWL::unSetCategoryNavPathItem()
     *
     * UnSet Path Item from $arPath Array
     * @return bool
     */
    public function unSetCategoryNavPathItem($path) {
        $this->arCatPath = self::_unSetPathItem($this->arCatPath, $path);
    }

    /**
     * UrlWL::unSetNavPathItem()
     *
     * UnSet Navigation Path Item from $arNavPath Array
     * @return bool
     */
    public function unSetNavPathItem($path) {
        $this->arNavPath = self::_unSetPathItem($this->arNavPath, $path);
    }

    /**
     * UrlWL::getItemId()
     *
     * Get Current Item ID function.
     * @return int
     */
    public function getItemId() {
        return $this->itemID===null ? 0 : $this->itemID;
    }

    /**
     * UrlWL::getAssortId()
     *
     * Get Current Assort Item ID function.
     * @return int
     */
    public function getAssortId() {
        return $this->assortID===null ? 0 : $this->assortID;
    }

    /**
     * UrlWL::getPageNumber()
     *
     * Get Current Page Number function.
     * @return int
     */
    public function getPageNumber() {
        return $this->page===null ? 1 : $this->page;
    }

    /**
     * UrlWL::getAjaxMode()
     *
     * Get Current Page Ajax Mode status value function.
     * @return bool
     */
    public function getAjaxMode() {
        return empty($this->ajax) ? 0 : 1;
    }

    /**
     * UrlWL::getModuleName()
     *
     * Get Current Category Module Name function.
     * @return String
     */
    public function getModuleName() {
        return $this->module===null ? false : $this->module;
    }

    /**
     * UrlWL::getFilters()
     *
     * Get Filters objects function.
     * @return \UrlFilters
     */
    public function getFilters() {
        return $this->Filters;
    }

    /**
     * UrlWL::copyFilters()
     *
     * Get copied Filters objects function.
     * @return \UrlFilters
     */
    public function copyFilters() {
        return $this->Filters->copy();
    }

    /**
     * UrlWL::setFilters()
     *
     * Set Filters objects function.
     * @return \UrlFilters
     */
    public function setFilters(UrlFilters $Filters) {
        return $this->Filters = $Filters;
    }

    /**
     * UrlWL::resetFilters()
     *
     * Get Filters objects function.
     * @return \UrlWL
     */
    public function resetFilters() {
        $this->Filters->reset();
        return $this;
    }

    /**
     * UrlWL::resetPage()
     *
     * Get Filters objects function.
     * @return \UrlWL
     */
    public function resetPage() {
        $this->setPage(1);
        return $this;
    }

    /**
     * UrlWL::storeLang()
     * 
     * Save Lang Parameter to $_SESSION And $_COOKIE.
     * @param string $langKeyName Lang key in $_SESSION And $_COOKIE
     * @param string $lang Lang value
     * @param int $storePeriod Live period for lang in cookie in seconds Like (24 * 7 * 3600) - for one week
     * @param bool $redirect Redirect to new url
     * @param CCookie $Cookie object
     */
    public function storeLang($langKeyName, $lang, $storePeriod, $redirect, CCookie $Cookie) {
        $_SESSION[$langKeyName] = $lang;
        $Cookie->add($langKeyName, $lang, time() + $storePeriod);
        if($redirect){ Redirect('/'.ltrim($this->cleanUrlFromLangs(), '/')); }
        else $Cookie->setCookie($langKeyName, $lang); //Set Cookie Directly for use in this server session
    }

}



/**
 * Description of UrlFilters class
 * This class provides methods for create and manage SEO Filters in UrlWL.
 * @author WebLife
 * @copyright 2015
 */
class UrlFilters {

    /**
     * brand id
     * @var int
     */
    protected $brandID;

    /**
     * category id
     * @var int
     */
    protected $categoryID;

    /**
     * filter type
     * @var int
     */
    protected $filterType;

    /**
     * show filters method
     * @var int
     */
    protected $showType;

    /**
     * selected filters where key = filterID, value = attribute values where key is attributeID and value is attributeAlias
     * @var array
     */
    protected $selected;

    /**
     * category filters id array for seo url
     * @var array
     */
    protected $categoryFilters;

    /**
     * mode type fo ranges
     * @var bool
     */
    protected $maskRanges;
    
    // все фильтры
    const SHOW_ALL = 1;
    // важные фильтры
    const SHOW_IMPORTANT = 2;
    // не важные фильтры
    const SHOW_UNIMPORTANT = 3;

    // типы фильтров
    const TYPE_BRAND = 1; //Производитель
    const TYPE_PRICE = 2; //Цена
    const TYPE_TEXT = 3; //Текстовый
    const TYPE_NUMBER = 4; //Числовой
    const TYPE_CATEGORY = 5; //Категория
    
    // типы списков фильтров
    const LIST_TYPE_DEFAULT = 1;
    const LIST_TYPE_SEO = 2;

    // keys
    const KEY_URL_CASE = 'filter';
    const KEY_CATEGORY = 'category';
    const KEY_BRAND = 'brand';
    const KEY_SELECTED = 'selected';
    const KEY_FIILTER_TYPE = 'type';
    const KEY_SHOW_TYPE = 'show';

    /**
     * @param array $data
     */
    public function __construct($data=array()) {
        $this->init($data);
    }
    /**
     * get new Instance
     * @param array $data
     * @return UrlFilters
     */
    public static function getNewInstance($data=array()) {
        $instance = new UrlFilters($data);
        return $instance;
    } 
    /**
     *
     * @param array $data
     * @return \UrlFilters
     */
    protected function parse(array $data=array()) {
        foreach($data as $key=>$val){
            switch ($key) {
                case self::KEY_CATEGORY:
                    $this->setCategoryID($val);
                    break;
                case self::KEY_BRAND:
                    $this->setBrandID($val);
                    break;
                case self::KEY_FIILTER_TYPE:
                    $this->setFilterType($val);
                    break;
                case self::KEY_SHOW_TYPE:
                    $this->setShowType($val);
                    break;
                case self::KEY_SELECTED:
                    $this->setSelected($val);
                    break;
                default:
                    break;
            }
        }
        return $this;
    }
    /**
     * init from data array
     * @param array $data
     * @return \UrlFilters
     */
    public function init($data=array()) {
        $this->reset();
        if(is_array($data)) {
            $this->parse($data);
        }
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function copy() {
        $cloned = clone $this;
        return $cloned;
    }
    /**
     * set to default
     * @return \UrlFilters
     */
    public function reset() {
        $this->brandID = $this->categoryID = $this->filterType = $this->showType = 0;
        $this->selected = $this->categoryFilters = array();
        $this->maskRanges = false;
        return $this;
    }
    /**
     * @return int
     */
    public function getBrandID() {
        return $this->brandID;
    }
    /**
     * @return int
     */
    public function getCategoryID() {
        return $this->categoryID;
    }
    /**
     * @return int
     */
    public function getFilterType() {
        return $this->filterType;
    }
    /**
     * @return int
     */
    public function getShowType() {
        return $this->showType;
    }
    /**
     * @return array
     */
    public function getSelected() {
        return $this->selected;
    }
    /**
     * @return array
     */
    public function getSelectedTitles() {
        $selected = array();
        foreach($this->sortSelected()->selected as $key=>$val){
            if(isset($this->categoryFilters[$key])){
                $title = $this->categoryFilters[$key]['title'];
                if(isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) || isset($this->selected[$key][UrlFiltersRange::KEY_MAX])){
                    $min = isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) ? $this->selected[$key][UrlFiltersRange::KEY_MIN] : 0;
                    $max = isset($this->selected[$key][UrlFiltersRange::KEY_MAX]) ? $this->selected[$key][UrlFiltersRange::KEY_MAX] : 0;
                    $title .= ' '.LABEL_FROM.' '.$min.($max ? ' '.LABEL_TO.' ' . $max: '');
                } else {
                    $alias = reset($val);
                    foreach($this->categoryFilters[$key]['values'] as $value){
                        if($value['alias'] == $alias){
                            $title = $value['title'];
                            break;
                        }
                    }
                }
                $selected[$key] = $title;
            }
        }
        return $selected;
    }
    /**
     * @return array
     */
    public function getCategoryFilters() {
        return $this->categoryFilters;
    }
    /**
     * @param int $brandID
     * @return \UrlFilters
     */
    public function setBrandID($brandID) {
        $this->brandID = intval($brandID);
    }
    /**
     * @param int $categoryID
     * @return \UrlFilters
     */
    public function setCategoryID($categoryID) {
        $this->categoryID = intval($categoryID);
    }
    /**
     * @param int $filterType
     * @return \UrlFilters
     */
    public function setFilterType($filterType) {
        $this->filterType = $filterType;
    }
    /**
     * @param int $showType
     * @return \UrlFilters
     */
    public function setShowType($showType) {
        $this->showType = $showType;
        return $this;
    }
    /**
     * @param array $selected
     * @return \UrlFilters
     */
    public function setSelected($selected) {
        $this->selected = ($selected && is_array($selected)) ? $selected : array();
        return $this;
    }
    /**
     * @param array $categoryFilters
     * @return \UrlFilters
     */
    public function setCategoryFilters($categoryFilters) {
        $this->categoryFilters = ($categoryFilters && is_array($categoryFilters)) ? $categoryFilters : array();
        return $this;
    }
    /**
     * @param int $categoryFilter
     * @return bool
     */
    public function issetCategoryFilter($categoryFilter) {
        return isset($this->categoryFilters[$categoryFilter]);
    }
    /**
     * @param int $categoryFilter
     * @return \UrlFilters
     */
    public function unsetCategoryFilter($categoryFilter) {
        if($this->issetCategoryFilter($categoryFilter)){
            unset($this->categoryFilters[$categoryFilter]);
        }
        return $this;
    }
     /**
      * @return int
      */
    public function countSelectedFilters() {
        return count($this->selected);
    }    
     /**
      * @return int
      */
    public function countSelectedFilterAttributes() {
        $cnt = 0;
        foreach ($this->selected as $val) {
            if(isset($val[UrlFiltersRange::KEY_MIN]) || isset($val[UrlFiltersRange::KEY_MAX])){
                $cnt++;
            } else {
                $cnt += count($val);
            }
        }
        return $cnt;
    }    
    /**
     * @param int $filterID
     * @param array $selected
     * @return bool
     */
    private static function issetSelectedFilter($filterID, array $selected) {
        return isset($selected[$filterID]);
    }
    /**
     * @param int $filterID
     * @return array
     */
    public function getFilter($filterID, $defval=array()) {
        return isset($this->selected[$filterID]) ? $this->selected[$filterID] : $defval;
    }
    /**
     * @param int $filterID
     * @return bool
     */
    public function issetFilter($filterID) {
        return isset($this->selected[$filterID]);
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param array $selected
     * @return bool
     */
    private static function issetSelectedFilterAttribute($filterID, $attributeAlias, array $selected) {
        return (isset($selected[$filterID]) && in_array($attributeAlias, $selected[$filterID]));
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @return bool
     */
    public function issetAttribute($filterID, $attributeAlias) {
        return (isset($this->selected[$filterID]) && in_array($attributeAlias, $this->selected[$filterID]));
    }
    /**
     * @param int $filterID
     * @param mixed $attributeKey int or string
     * @return mixed
     */
    public function getAttribute($filterID, $attributeKey, $defval=null) {
        return isset($this->selected[$filterID][$attributeKey]) ? $this->selected[$filterID][$attributeKey] : $defval;
    }
    /**
     * @param int $filterID
     * @return int
     */
    public function countAttributes($filterID) {
        return isset($this->selected[$filterID]) ? count($this->selected[$filterID]) : 0;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function appendAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID){
            if($attributeKey){
                $this->selected[$filterID][$attributeKey] = $attributeAlias;
            } else if($attributeAlias && (!isset($this->selected[$filterID]) || !in_array($attributeAlias, $this->selected[$filterID]))){
                $this->selected[$filterID][] = $attributeAlias;
            }
        }
        return $this;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function prependAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID){
            $key = $attributeKey ? $attributeKey : array_search($attributeAlias, $this->selected);
            if($key!==FALSE && isset($this->selected[$filterID][$key])){
                unset($this->selected[$filterID][$key]);
            }
            if(is_numeric($attributeKey)){
                $attributeKey = 0;
            }
            $selected = array($filterID => array($attributeKey => $attributeAlias));
            foreach($this->selected as $key=>$val){
                foreach($val as $k=>$v){
                    if(is_numeric($k)){
                        $selected[$key][]=$v;
                    } else {
                        $selected[$key][$k]=$v;
                    }
                }
            }
            $this->selected = $selected;
        }
        return $this;
    }
    /**
     * @param array $selectedFilters
     * @return \UrlFilters
     */
    public function appendAttributes(array $selectedFilters) {
        foreach($selectedFilters as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($this->selected[$key]) || !in_array($v, $this->selected[$key])){
                        $this->selected[$key][]=$v;
                    }
                } else {
                    $this->selected[$key][$k]=$v;
                }
            }
        }
        return $this;
    }
    /**
     * @param array $selectedFilters
     * @return \UrlFilters
     */
    public function prependAttributes(array $selectedFilters) {
        $selected = array();
        foreach($selectedFilters as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($selected[$key]) || !in_array($v, $selected[$key])){
                        $selected[$key][]=$v;
                    }
                } else {
                    $selected[$key][$k]=$v;
                }
            }
        }
        foreach($this->selected as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($selected[$key]) || !in_array($v, $selected[$key])){
                        $selected[$key][]=$v;
                    }
                } else if(!isset($selected[$key][$k])){
                    $selected[$key][$k]=$v;
                }
            }
        }
        $this->selected = $selected;
        return $this;
    }
    /**
     * @param int $filterID
     * @return \UrlFilters
     */
    public function removeFilter($filterID) {
        if(isset($this->selected[$filterID])){
            unset($this->selected[$filterID]);
        }
        return $this;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function removeAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID && isset($this->selected[$filterID])){
            $key = $attributeKey ? $attributeKey : array_search($attributeAlias, $this->selected[$filterID]);
            if($key!==FALSE && isset($this->selected[$filterID][$key])){
                if(count($this->selected[$filterID]) > 1){
                    unset($this->selected[$filterID][$key]);
                    if(!isset($this->selected[$filterID][UrlFiltersRange::KEY_MIN]) && !isset($this->selected[$filterID][UrlFiltersRange::KEY_MAX])){
                        $this->selected[$filterID] = array_values($this->selected[$filterID]);
                    }
                } else {
                    unset($this->selected[$filterID]);
                }
            }
        }
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function removeAttributes() {
        $this->selected = array();
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function setMaskRangesOn() {
        $this->maskRanges = true;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function setMaskRangesOff() {
        $this->maskRanges = false;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function sortSelected() {
        $selected = array();
        foreach(array_keys($this->categoryFilters) as $filterID){
            if(isset($this->selected[$filterID])){
                $selected[$filterID] = $this->selected[$filterID];
                unset($this->selected[$filterID]);
            }
        }
        foreach(array_keys($this->selected) as $filterID){
            $selected[$filterID] = $this->selected[$filterID];
        }
        $this->selected = $selected;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function slicePath() {
        $arPath = array();
        foreach($this->sortSelected()->selected as $key=>$val){
            if(isset($this->categoryFilters[$key])){
                if(isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) || isset($this->selected[$key][UrlFiltersRange::KEY_MAX])){
                    $prefix = '';
                    switch($this->categoryFilters[$key]['tid']){
                        case self::TYPE_PRICE:
                            $prefix = UrlFiltersRange::SEO_HAND_PRICE;
                            break;
                        case self::TYPE_NUMBER:
                            $prefix = UrlFiltersRange::SEO_HAND_RANGE;
                            break;
                        default:
                            break;
                    }
                    if($prefix){
                        if($this->maskRanges){
                            $arPath[] = UrlFiltersRange::maskSeoPath($prefix, $key);
                        } else {
                            $min = isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) ? $this->selected[$key][UrlFiltersRange::KEY_MIN] : 0;
                            $max = isset($this->selected[$key][UrlFiltersRange::KEY_MAX]) ? $this->selected[$key][UrlFiltersRange::KEY_MAX] : 0;
                            $arPath[] = UrlFiltersRange::generateSeoPath($prefix, $key, $min, $max);
                        }
                        $this->removeFilter($key);
                    }
                } else {
                    $alias = reset($val);
                    foreach($this->categoryFilters[$key]['values'] as $value){
                        if($value['alias'] == $alias){
                            $arPath[] = $value['seo_path'];
                            $this->removeAttribute($key, $alias);
                            break;
                        }
                    }
                }
            }
        }
        return $arPath;
    }
    /**
     * @return array
     */
    public function toUrlParams() {
        return array(self::KEY_URL_CASE => $this->toArray());
    }
    /**
     * @return array
     */
    public function toArray() {
        $data = array();
        if($this->getCategoryID()){
            $data[self::KEY_CATEGORY] = $this->getCategoryID();
        }
        if($this->getBrandID()){
            $data[self::KEY_BRAND] = $this->getBrandID();
        }
        if($this->getFilterType()){
            $data[self::KEY_FIILTER_TYPE] = $this->getFilterType();
        }
        if($this->getShowType()){
            $data[self::KEY_SHOW_TYPE] = $this->getShowType();
        }
        if($this->getSelected()){
            $selected = $this->sortSelected()->getSelected();
            if($this->maskRanges){
                foreach($selected as $key => &$val){
                    if(isset($val[UrlFiltersRange::KEY_MIN]) || isset($val[UrlFiltersRange::KEY_MAX])){
                        $val[UrlFiltersRange::KEY_MIN] = UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MIN);
                        $val[UrlFiltersRange::KEY_MAX] = UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX);
                    }
                }
            }
            $data[self::KEY_SELECTED] = $selected;
        }
        return $data;
    }
}



/**
 * Description of UrlFiltersRange class
 * This class provides methods for create and manage SEO Filters Ranges in UrlWL.
 * @author WebLife
 * @copyright 2015
 */
class UrlFiltersRange {
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $min;
    /**
     * @var int
     */
    public $max;
    /**
     * @var int
     */
    public $type;
    /**
     * @var bool
     */
    public $auto;
    /**
     * @var string
     */
    public $prefix;
    
    // keys
    const KEY_MIN = 'min';
    const KEY_MAX = 'max';
    const KEY_SEP_MAX = 'maxsep';
    
    // url seo replacements
    const SEO_SEP = '-';
    const SEO_SIGN = '_';
    const SEO_AUTO_RANGE = 'range';
    const SEO_AUTO_PRICE = 'price';
    const SEO_HAND_RANGE = 'handrange';
    const SEO_HAND_PRICE = 'handprice';
    
    /**
     * @return string
     */
    public function generate() {
        return self::generateSeoPath($this->prefix, $this->id, $this->min, $this->max);
    }
    /**
     * @param string $seopath
     * @return \UrlFiltersRange
     */
    public function parse($seopath) {
        return self::parseSeoPath($seopath, $this);
    }
    /**
     * @param string $seopath
     * @return bool
     */
    public function check($seopath) {
        return self::checkSeoPath($seopath);
    }
    /**
     * 
     * @param string $key
     * @return string
     */
    public static function maskKey($key) {
        return '{'.$key.'}';
    }
    /**
     * 
     * @param string $max
     * @return string
     */
    public static function generateMaxPart($max) {
        return self::SEO_SEP.$max;
    }
    /**
     * 
     * @param string $prefix
     * @param int $id
     * @return string
     */
    public static function maskSeoPath($prefix, $id) {
        return $prefix.$id.self::SEO_SIGN.self::maskKey(self::KEY_MIN).self::generateMaxPart(self::maskKey(self::KEY_MAX));
    }
    /**
     * 
     * @param string $prefix
     * @param int $id
     * @param float $min
     * @param float $max
     * @return string
     */
    public static function generateSeoPath($prefix, $id, $min, $max) {
        return $prefix.$id.self::SEO_SIGN.intval($min).(($max = intval($max))>0 ? self::generateMaxPart($max) : '');
    }
    /**
     * @param string $seopath
     * @param \UrlFiltersRange $Range
     * @return \UrlFiltersRange
     */
    public static function parseSeoPath($seopath, self $Range=null) {
        if($seopath){
            foreach(array(self::SEO_AUTO_RANGE=>UrlFilters::TYPE_NUMBER, self::SEO_HAND_RANGE=>UrlFilters::TYPE_NUMBER, self::SEO_AUTO_PRICE=>UrlFilters::TYPE_PRICE, self::SEO_HAND_PRICE=>UrlFilters::TYPE_PRICE) as $prefix=>$type){
                $matches = array();
                if(preg_match('/^'.$prefix.'(\d+)'.self::SEO_SIGN.'(\d+)'.self::SEO_SEP.'?(\d*)$/i', $seopath, $matches)){
                    ($Range instanceof self) OR $Range = new self();
                    $Range->id = intval($matches[1]);
                    $Range->min = intval($matches[2]);
                    $Range->max = intval($matches[3]);
                    $Range->type = $type;
                    $Range->auto = ($prefix==self::SEO_AUTO_RANGE || $prefix==self::SEO_AUTO_PRICE);
                    $Range->prefix = $prefix;
                    return $Range;
                }
            }
        }
        return null;
    }
    /**
     * @param string $seopath
     * @return bool
     */
    public static function checkSeoPath($seopath) {
        return (self::parseSeoPath($seopath) instanceof self);
    }
}