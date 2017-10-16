<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * Description of DbConnector class
 * @author WebLife
 * @copyright 2010
 */
class DbConnector extends SystemComponent {
    private $query;
    private $link;
    private $result;
    private $arDB = array();

    public function __construct() {
        $this->arDB = parent::getDBSettings();
        $this->link = @mysql_connect($this->arDB['dbhost'], $this->arDB['dbusername'], $this->arDB['dbpassword']);
        if(!$this->link) sys_verifications(1);
        mysql_select_db($this->arDB['dbname']);
        mysql_query("SET NAMES ".WLCMS_DB_ENCODING);
    }

    public function __destruct() {
      return $this->link ? mysql_close($this->link) : false;
    }
    
    public static function listDBTables($refresh=false) {
        static $arDBTables = array();
        if($refresh) $arDBTables = array();
        if(empty($arDBTables)){
            $dbname = parent::getDBName();
            $query = "SHOW TABLES FROM `{$dbname}`";
            $result = mysql_query($query);
            while($row = mysql_fetch_assoc($result)) { $arDBTables[] = $row['Tables_in_'.$dbname]; }
            mysql_free_result($result);
        } return $arDBTables;
    }
    
    public static function isSetDBTable($table, $refreshed=false) {
        if(!empty($table)) return in_array($table, self::listDBTables($refreshed));
        return false;
    }
    
    public static function listDBTablesColumnNames($refresh=false) {
        static $arrDBTables = array();
        if($refresh) $arrDBTables = array();
        if(empty($arrDBTables)){
            $dbname = parent::getDBName();
            foreach(self::listDBTables($refresh) as $table){
                $query = "SHOW COLUMNS FROM `{$dbname}`.`{$table}`";
                $result = mysql_query($query);
                while($row = mysql_fetch_assoc($result)) { $arrDBTables[$table][] = $row['Field']; }
                mysql_free_result($result);
            }
        } return $arrDBTables;
    }

    public static function getListDBTableColumnNames($table, $refreshed=false) {
        $arr = self::listDBTablesColumnNames($refreshed);
        if(array_key_exists($table, $arr)) return $arr[$table];
        return array();
    }

    public static function isSetDBTableColumnName($table, $colname, $refreshed=false) {
        if(self::isSetDBTable($table, $refreshed) && !empty($colname)){
            return in_array($colname, self::getListDBTableColumnNames($table, $refreshed));
        } return false;
    }

    public static function replaceLang($table, $fromLang, $toLang, $separator){
        return ($fromLang == $toLang ? $table : str_replace($fromLang.$separator, $toLang.$separator, $table));
    }

    public function getTables() {
        $items = array();
        $query = "SHOW TABLES FROM `{$this->arDB['dbname']}`";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) { $items[] = $row['Tables_in_'.$this->arDB['dbname']]; }
        return $items;
    }

    public function getTableColumns($table) {
        $items = array();
        if(!empty($table)) {
            $query = "SHOW COLUMNS FROM `{$this->arDB['dbname']}`.`{$table}`";
            $result = mysql_query($query);
            while ($row = mysql_fetch_assoc($result)) { $items[] = $row; }
        } return $items;
    }
    
    public function getTableColumnsNames($table) {
        $items = array();
        if(!empty($table)) {
            $query = "SHOW COLUMNS FROM `{$this->arDB['dbname']}`.`{$table}`";
            $result = mysql_query($query);
            while ($result && ($row = mysql_fetch_assoc($result))) { $items[] = $row['Field']; }
        }   return $items;
    }
    
    public function getTableColumnValues($table) {
        $items = array();
        if(!empty($table)) {
            $query = "SHOW COLUMNS FROM `{$this->arDB['dbname']}`.`{$table}`";
            $result = mysql_query($query);
            while ($result && ($row = mysql_fetch_assoc($result))) { $items[$row['Field']] = ($row['Default']==='NULL' ? NULL : $row['Default']); }
        }   return $items;
    }

    public function Query($query) {
        $this->query = $query;
        $this->result = mysql_query($this->query, $this->link);
        return $this->result;
    }

    public function ForSql($strValue, $iMaxLength=0) {
        if ($iMaxLength > 0)
            $strValue = substr($strValue, 0, $iMaxLength);

        if (function_exists("mysql_real_escape_string"))
            return mysql_real_escape_string($strValue);
        elseif (function_exists("mysql_escape_string"))
            return mysql_escape_string($strValue);

        //unreachable
        return str_replace("'", "\'", str_replace("\\", "\\\\", $strValue));
    }

    public function Free() {
      if (is_resource($this->result))
          mysql_free_result($this->result);
    }

    public function getQuery() {
        return $this->query;
    }

    public function getDBLink() {
        return $this->link;
    }

    public function getResult() {
        return $this->result;
    }

    public function getBoolResult() {
        return is_resource($this->result) ? true : $this->result;
    }

    public function getAffectedRows() {
        return $this->link ? mysql_affected_rows($this->link) : false;
    }

    public function getNumRows() {
        return is_resource($this->result) ? mysql_num_rows($this->result) : false;
    }

    public function fetchResult($row = 0, $field = 0) {
        return is_resource($this->result) ? mysql_result($this->result, $row, $field) : false;
    }

    public function fetchArray() {
        return is_resource($this->result) ? mysql_fetch_array($this->result) : false;
    }

    public function fetchAssoc() {
        return is_resource($this->result) ? mysql_fetch_assoc($this->result) : false;
    }

    public function screenData($str, $bApplyTrim=false){
        if($bApplyTrim) $str = trim($str);
        return (empty($str) || is_bool($str)) ? $str : addslashes(htmlspecialchars(stripslashes($str), ENT_QUOTES));
    }
    
    public function unScreenData($str, $bApplyTrim=false){
        if($bApplyTrim) $str = trim($str);
        return (empty($str) || is_bool($str)) ? $str : htmlspecialchars_decode(stripslashes($str), ENT_QUOTES);
    }

    public function postToQuery(array $arPost, $arUsedKeys=array(), $type='insert') {
        if(empty($arPost)) return false;
        $keys = array();
        $values = array();
        switch($type) {
            case 'insert':
                foreach($arPost as $key=>$value) {
                    if (in_array($key, $arUsedKeys)) {
                        $keys[]   = "`".$key."`";
                        $values[] = ($value===null || $value==="NULL") ? 'NULL' : "'".$this->screenData($value)."'";
                    }
                }
                break;
            case 'update':
                foreach($arPost as $key => $value) {
                    if (in_array($key, $arUsedKeys)) {
                        $values[] = ($value===null || $value==="NULL") ? "`".$key."`=NULL" : "`".$key."`='".$this->screenData($value)."'";
                    }
                }
                break;
        }
        return array("keys"=>implode(", ",$keys), "values"=>implode(", ",$values));
    }
    
    public function postToDB($arPost, $table, $conditions='', $arUnUsedKeys=array(), $type = 'insert', $ln_sync=false, $ignore=false) {
        global $lang;
        if(empty($arPost) OR empty($table)) return false;
        $arUsedKeys = self::getListDBTableColumnNames($table);
        if(!empty($arUnUsedKeys)){
            sort($arUnUsedKeys);
            for($i=0; $i<sizeof($arUnUsedKeys); $i++){ if(!in_array($arUnUsedKeys[$i], $arUsedKeys)){ unset($arUnUsedKeys[$i]); } }
            $arUsedKeys = array_diff($arUsedKeys, $arUnUsedKeys);
        }
        $arData = $this->postToQuery($arPost, $arUsedKeys, $type);
        $ignore = $ignore ? 'IGNORE' : '';

        switch($type) {
            case 'insert':
                if(!empty($arData["keys"]) && !empty($arData["values"])){
                    $insert_id = 0;
                    $this->query  = "INSERT {$ignore} INTO `{$table}` ( {$arData["keys"]} ) VALUES ({$arData["values"]})";
                    if($ln_sync){
                        foreach( parent::getAcceptLangsKeys() as $ln ){
                            $query = ($ln==$lang) ? $this->query : str_replace($table, replaceLang($ln, $table), $this->query);
                            $this->result = mysql_query($query) or die('<br />ERROR in Langs Synchronize INSERT: '.mysql_error().'<br />Таблица: '.$table.'<br/>SQL: '.$query);
                            if($ln==$lang && $this->result){ $insert_id = intval(mysql_insert_id()); }
                         }
                    } else {
                        $this->result = mysql_query($this->query) or die('<br />Ошибка: '.mysql_error().'<br />Таблица: '.$table.'<br/>SQL: '.$this->query);
                        $insert_id = intval(mysql_insert_id());
                    }
                    if($this->result && !$insert_id) return true;
                    if($insert_id)                   return $insert_id;
                } else { $this->query = ''; $this->result = false; }
                break;
            case 'update':
                if(!empty($arData["values"])){
                    $this->query  = "UPDATE {$ignore} `".$table."` SET ".trim($arData["values"]).(!empty($conditions) ? ' '.$conditions : '');
                    if($ln_sync){
                        foreach( parent::getAcceptLangsKeys() as $ln ){
                            $query = ($ln==$lang) ? $this->query : str_replace($table, replaceLang($ln, $table), $this->query);
                            $this->result = mysql_query($query) or die('<br />ERROR in Langs Synchronize UPDATE: '.mysql_error().'<br />Таблица: '.$table.'<br/>SQL: '.$query);
                        }
                    } else $this->result = mysql_query($this->query) or die('<br />Ошибка: '.mysql_error().'<br />Таблица: '.$table.'<br/>SQL: '.$this->query);
                    if($this->result) return true;
                } else { $this->query = ''; $this->result = false; }
                break;
        } return false;
    }
    
}

/**
 * Description of ExternalDbConnector class
 * This class need for files from interactive folder
 * @author WebLife
 * @copyright 2010
 */
class ExternalDbConnector extends DbConnector {

    private $arDBSettings = array();

    public function  __construct() {
        parent::__construct();
        $this->arDBSettings = parent::getDBSettings();
    }

    public function getDBUser() {
        return $this->arDBSettings["dbusername"];
    }

    public function getDBPassword() {
        return $this->arDBSettings["dbpassword"];
    }

    public function getDBHost() {
        return $this->arDBSettings["dbhost"];
    }

}

