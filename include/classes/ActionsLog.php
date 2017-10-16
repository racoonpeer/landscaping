<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access

if(!defined('ACTIONS_LOG_TABLE')){ define('ACTIONS_LOG_TABLE', 'actions_log');}


/**
 * Description of ActionsLog class
   Description: 
 *  
 DROP TABLE IF EXISTS `actions_log`;
CREATE TABLE IF NOT EXISTS `actions_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `oid` int(11) DEFAULT '0',
  `ip` varchar(100) NOT NULL,
  `action` int(11) NOT NULL,
  `comment` tinytext,
  `message` text NOT NULL,
  `object` varchar(256) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `lang` varchar(5) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_oid` (`oid`),
  KEY `idx_ip` (`ip`),
  KEY `idx_module` (`module`),
  KEY `idx_lang` (`lang`),
  KEY `idx_ts` (`ts`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=310 ;
 * @author WebLife
 * @copyright 2011
 */
class ActionsLog {
    const SYSTEM_USER = -1;
    /**
     * 0 - Авторизация 
     */
    const ACTION_AUTHORIZATION = 0;
    /**
     * 1 - Создание
     */
    const ACTION_CREATE = 1;
    /**
     * 2 - Редактирование
     */
    const ACTION_EDIT = 2;
    /**
     * 3 - Публикация 
     */
    const ACTION_PUBLICATION = 3;
    /**
     * 4 - Удаление  
     */
    const ACTION_DELETE = 4;

    /**
     * 10 - Отправка уведомления о подтверждении  
     */
    const ACTION_ORDER_CONFIRM = 10;
    /**
     * 11 - Смена статуса заказа  
     */
    const ACTION_ORDER_STATUS = 11;
    /**
     * 12 - Смена способа оплаты  
     */
    const ACTION_ORDER_PAYMENT = 12;
    /**
     * 13 - Смена способа доставки  
     */
    const ACTION_ORDER_SHIPPING = 13;
    /**
     * 14 - Смена колва товаров
     */
    const ACTION_ORDER_EDIT_PRODUCT_COUNT = 14;
    /**
     * 15 - Добавлен товар 
     */
    const ACTION_ORDER_ADD_PRODUCT = 15;
    /**
     * 16 - Удален товар  
     */
    const ACTION_ORDER_DELETE_PRODUCT = 16;
    /**
     * Actions Array Where key is const and value is title
     */
    public static function getActionsTitle() {
        return array (
            self::ACTION_AUTHORIZATION => "Авторизация",
            self::ACTION_CREATE => "Создание",
            self::ACTION_EDIT => "Редактирование",
            self::ACTION_PUBLICATION => "Публикация",
            self::ACTION_DELETE => "Удаление",
            
            self::ACTION_ORDER_CONFIRM => "Подтверждение",
            self::ACTION_ORDER_STATUS => "Смена статуса",
            self::ACTION_ORDER_PAYMENT => "Смена оплаты",
            self::ACTION_ORDER_SHIPPING => "Смена доставки",
            
            self::ACTION_ORDER_EDIT_PRODUCT_COUNT => "Смена кол-ва",
            self::ACTION_ORDER_ADD_PRODUCT => "Добавление товара",
            self::ACTION_ORDER_DELETE_PRODUCT => "Удаление товара",
        );
    }
     
    /*
     * 
     */
    public static function getModulesTitle() {
        return getRowItemsInKeyValue('module', 'short_title', MODULES_PARAMS_TABLE, '*', 'WHERE `history`=1');
    }
    
    /**
     * Actions Labels Array Where key is const and value is title
     */
    static $ActionsLabels = array (
        self::ACTION_CREATE => "создание обьекта",
    ); 
    
    protected static $_instance = null;
    protected $uid;
    protected $ip;
    
    /**
     * ActionsLog::__construct()
     *
     * Construct function.
     * @return
     */
    protected function __construct($userID=0, $ip='') {
        $this->uid = $userID;
        $this->ip = $ip;
    }

    protected function __clone(){} 
    
    
    /**
     * Создание екземпляра обьекта
     * @param int $uid
     * @param string $ip
     * @return ActionsLog
     * @throws Exception
     */
    public static function createInstance($User, $ip) {
        if (self::$_instance !== null) {
           throw new Exception('Данный экземпляр уже был создан!');
        }
        $uid = ($User && $User->logined) ? $User->id : 0;
        self::$_instance = new self($uid, $ip);
        return self::$_instance;
    } 
    
    /**
     * Получение текущего обьекта
     * @return ActionsLog
     * @throws Exception
     */
    public static function getInstance() {
        if (self::$_instance === null || self::$_instance->uid==0)
            throw new Exception('Пользователь не авторизован!');
        return self::$_instance;
    } 
    
    /**
     * Получение текущего обьекта
     * @return ActionsLog
     * @throws Exception
     */
    public static function getAuthInstance($uid, $ip) {
        return new self($uid, $ip);
    } 
    /**
     * Сохранение
     * @param int $action
     * @param string $comment
     * @param string $lang
     * @param string $object
     * @param int $ownerID
     * @param string $module
     * @return bool
     * @throws Exception
     */
    public function save($action, $comment, $lang, $object='', $ownerID=0, $module='', $message=''){
        // проверки      
        if(!array_key_exists($action, self::getActionsTitle()))
            throw new Exception("Необходимо установить действие (action)!");
        if(empty($comment))
            throw new Exception("Необходимо установить комментарий (comment)!");
        if(empty($lang))
            throw new Exception("Необходимо установить язык (lang)!");
        
        // вставка в базу и возврат результата
        $query = 'INSERT INTO '.ACTIONS_LOG_TABLE.' (uid, oid, ip, action, comment, message, module, lang, object, ts) 
                  VALUES ('.$this->uid.', 
                          '.$ownerID.', 
                          "'.$this->ip.'", 
                          "'.$action.'", 
                          "'.addslashes($comment).'",  
                          "'.addslashes(unScreenData($message)).'",  
                          "'.$module.'", 
                          "'.$lang.'", 
                          "'.addslashes($object).'", 
                          "'.date("Y-m-d H:i:s").'")';
        $result = mysql_query($query);
        return ($result && mysql_affected_rows()>0);
    } 
    
    public function saveGroup($items, $action, $comment, $lang, $module, $table){
        foreach($items as $itemID => $value){
            $result = mysql_query('SELECT `title` FROM '.$table.' WHERE `id`='.$itemID);
            $title = mysql_fetch_assoc($result);
            ActionsLog::getInstance()->save($action, $comment, $lang, $title['title'], $itemID, $module);
        }
    }
    
    /**
     * Получение записей
     * @param int $action
     * @param string $module
     * @param int $ownerID
     * @param string $lang
     * @param timestamp $date
     * @return array
     * @throws Exception
     */
    public function getHistory($filters=array(), $items_on_page=12){
        $items = array();

        $select = 'SELECT alt.*, ut.`firstname` as user FROM '.ACTIONS_LOG_TABLE.' alt ';
        $join   = 'LEFT JOIN '.USERS_TABLE.' ut ON alt.`uid`=ut.`id` ';
        $where  =  self::generateWhereState($filters);
        $order  = ' ORDER BY alt.`ts` DESC ';

        // генерируем пейджер и лимит
        $page = !empty($filters['page']) ? intval($filters['page']) : 1;
        $count = mysql_fetch_assoc(mysql_query('SELECT count(*) as count FROM '.ACTIONS_LOG_TABLE.' alt '.$where));
        $total_items = intval($count['count']);
        $pager       = getPager($page, $total_items, $items_on_page, '');
        $total_pages = $pager['count'];
        $offset      = ($page-1)*$items_on_page;
        
        $limit = "LIMIT {$offset}, {$items_on_page}"; 
        
        $query = $select.$join.$where.$order.$limit;
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { 
            $items[] = $row; 
        }
        
        $arResult = array(
            'history' => $items,
            'page' => $page, 
            'pager' => $pager, 
            'total_pages' => $total_pages, 
            'filtersUrl'=>self::generateFiltersUrl($filters)
        );
        
        return $arResult;
    }
    
    /**
     * получение фильтров
     */
    public function getFilters($filters = array(), $init_key=''){
        $arFilters = array('users'=> array(), 'modules' => array(), 'actions' => array(), 'langs' => array());
                   
        $where = self::generateWhereState($filters);
        if(!empty($init_key) && array_key_exists($init_key, $filters)) {
            unset($filters[$init_key]);
            $init_where = self::generateWhereState($filters);
        } else $init_where = '';
        
        $query = 'SELECT DISTINCT alt.`module` FROM '.ACTIONS_LOG_TABLE.' alt '.($init_key=='modules' ? $init_where : $where);
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { 
            if(!empty($row['module'])){
                $arFilters['modules'][] = $row['module'];
            }
        }
        
        $query = 'SELECT DISTINCT alt.`action` FROM '.ACTIONS_LOG_TABLE.' alt '.($init_key=='actions' ? $init_where : $where);
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { 
            if($row['action'] < 10)   $arFilters['actions']['main'][] = $row['action'];
            else $arFilters['actions']['order'][] = $row['action'];
        }
        
        $query = 'SELECT DISTINCT alt.`lang` FROM '.ACTIONS_LOG_TABLE.' alt '.($init_key=='langs' ? $init_where : $where);
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { 
            $arFilters['langs'][] = $row['lang'];
        }
        
        $query = 'SELECT DISTINCT alt.`uid`, ut.`firstname` as user FROM '.ACTIONS_LOG_TABLE.
                 ' alt LEFT JOIN '.USERS_TABLE.' ut ON alt.`uid`=ut.`id` '.($init_key=='action' ? $init_where : $where);
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { 
            $user['name'] =$row['user'];
            $user['id'] = $row['uid'];
            if(!empty($row['uid']) && !in_array($user, $arFilters['users'])){
                $arFilters['users'][] = $user;
            }
        }

        if(empty($arFilters['modules']) && empty($arFilters['users']) && empty($arFilters['actions']) && empty($arFilters['langs'])) {
            if(isset($filters['actions'])){
                foreach($filters['actions'] as $action)
                    $arFilters['actions'][] = $action;
            }
            if(isset($filters['modules'])){
                foreach($filters['modules'] as $action)
                    $arFilters['modules'][] = $action;
            }
            if(isset($filters['langs'])){
                foreach($filters['langs'] as $action)
                    $arFilters['langs'][] = $action;
            }
            if(isset($filters['user'])){
                $user['id'] = $filters['user']['uid'];
                $name = mysql_fetch_assoc(mysql_query('SELECT firstname FROM '.USERS_TABLE.' WHERE `id`='.$user['id']));
                $user['name'] = $name['firstname'];
                $arFilters['users'][] = $user;
            }
        }

        return $arFilters;
    }

    /**
     * 
     * @param array $filters
     * @return string
     */
    public function generateFiltersUrl($filters) {
        $url = '';
        if(!empty($filters)) {
            foreach ($filters as $key => $filter) {
                if(is_array($filter)) {
                    foreach ($filters[$key] as $k => $f) {
                        $url .= '&filters['.$key.']['.$k.']'.'='.$f;
                    }
                } else {
                    $url .= '&filters['.$key.']='.$filter;
                }
            }
        } 
        return $url;
    }
    
    /**
     * 
     * @param array $filters
     * @return string
     */
    public function generateWhereState($filters) {
        $where = '';
        // фильтруем записи
        if(!empty($filters)) {
            if(!empty($filters['modules'])){
                $where .= $where ? ' AND ' : ' WHERE ';
                $where .= '( alt.`module` in ("'.implode('","', $filters['modules']).'") )';
            }
            if(!empty($filters['actions'])){
                $where .= $where ? ' AND ' : ' WHERE ';
                $where .= '( alt.`action` in ('.implode(',', $filters['actions']).') )';
            }
            if(!empty($filters['langs'])){
                $where .= $where ? ' AND ' : ' WHERE ';
                $where .= '( alt.`lang` in ("'.implode('","', $filters['langs']).'") )';
            }
            if(!empty($filters['user'])){
                $where .= $where ? ' AND ' : ' WHERE ';
                $where .= '( alt.`uid` = '.intval($filters['user']).' )';
            }
            if(!empty($filters['time'])){
                if($filters['time'] == 1) {
                    $where .= $where ? ' AND ' : ' WHERE ';
                    $where .= '( alt.`ts` >= CURDATE() )';
                } elseif ($filters['time'] == 2){
                    if(isset($filters['from']) || isset($filters['to'])) {
                        //add "H:i:s" to generate time
                        $from   = self::checkDate($filters, 'from') ? date("Y-m-d", $filters['from']) : '0';
                        $to     = self::checkDate($filters, 'to') ? date("Y-m-d", $filters['to']) : date("Y-m-d H:i:s", strtotime(date("Y-m-d").' 23:59:59'));
                        $where .= $where ? ' AND ' : ' WHERE ';
                        $where .= '( alt.`ts` BETWEEN "'.$from.'" AND "'.$to.'" )';
                    }
                }
            } 
            if(!empty($filters['oid'])){
                $where .= $where ? ' AND ' : ' WHERE ';
                $where .= '( alt.`oid` = '.intval($filters['oid']).' )';
            }
        } 

        return $where;
    }
    
    public function checkDate($array, $key) {
        return (isset($array[$key]) && is_numeric($array[$key]) && (int)$array[$key] == $array[$key]);
    }
}
