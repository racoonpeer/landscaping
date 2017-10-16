<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

/**
 * Description of SystemComponent class
 * This class contains some System Variables of WebLife CMS
 * @author WebLife
 * @copyright 2010
 */
class SystemComponent {
    const WLCMS_INSTALLED = true;  //IF Your site work OK You must set this parameter to true. If you copy to another hosting - set to false for change folders/files permissions automaticaly
    private static function initDBSettings() { //Put your site DB Connection data here
        return getenv('IS_DEV') ? array (
            "dbhost"     => "localhost",
            "dbusername" => "root",
            "dbpassword" => "2c4uk915",
            "dbname"     => "landscaping"
        ) : array (
            "dbhost"     => "localhost",
            "dbusername" => "landscap_db_adm",
            "dbpassword" => "2c4uk915",
            "dbname"     => "landscap_new"
        );
    }
    private static function initAcceptLangs() { //Put your site languages here. Cooment some langs if not need them
        return array(   // First is default
            "ru"        => array("name"=>"Русский",    "title"=>"рус",    "image"=>"ru.gif", "charset"=>"windows-1251"),
            "ua"        => array("name"=>"Українська", "title"=>"укр", "image"=>"ua.gif", "charset"=>"windows-1251"),
//            "en"        => array("name"=>"English",    "title"=>"English",    "image"=>"en.gif", "charset"=>"windows-1251"),
        );
    }
    private static function initTestCookie() { //Put your Test Cookie values to array here
        return array(
            'name'  => 'OC_STARTUP', //set unique name of test cookie
            'val'   => md5($_SERVER['REMOTE_ADDR']), //set unique value of test cookie from md5 of client
            'exp'   => time()+57533 // set time to will be expire test cookie
        );
    }
    protected static function getDBSettings() {
        return self::initDBSettings();
    }
    public static function getAcceptLangs() {
        return self::initAcceptLangs();
    }
    public static function getAcceptLangsKeys() {
        return array_keys(self::initAcceptLangs());
    }
    public static function getDBName() {
        $arDBSettings = self::initDBSettings();
        return $arDBSettings["dbname"];
    }
    public static function getTestCookie() {
        return self::initTestCookie();
    }
    public static function checkTestCookie($arCookie){
        return (!empty($_COOKIE[$arCookie['name']]) && $_COOKIE[$arCookie['name']]==$arCookie['val']) ? true : false;
    }
    public static function setTestCookie($arCookie, $isAdd=true){
        $obj = new CCookie();
        if($isAdd) $obj->add($arCookie['name'], $arCookie['val'], $arCookie['exp']);
        else       $obj->del($arCookie['name']);
        $obj->process();
    }    
    
    //images params
    public static function getArImgAliases(){
        return array( 'big', 'middle', 'small' );
    }
    
    public static function prepareImagesParams($images_params) {
        $params = array();
        if(!empty($images_params)){
            $images_params = unserialize(unScreenData($images_params));
            foreach($images_params as $alias => $param) {
               $params[] = array($alias.'_', $param['width'], $param['height']);
            }
        }
       return $params;
    } 
}

///////////////// OTHER PARAMETERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if(!defined('DS')) define('DS',         DIRECTORY_SEPARATOR); // set WebLife CMS DIRECTORY SEPARATOR
define('WLCMS_ABS_ROOT',                rtrim($_SERVER['DOCUMENT_ROOT'], '/\\').DS); // set WebLife CMS absolute root DIR
define('WLCMS_VERSION',                 'СMS v2.1'); // set WebLife CMS Version
define('WLCMS_JQUERY_VERSION',          '1.10.1'); // set jQuery Library Version http://jquery.com/download/
define('WLCMS_DB_ENCODING',             'cp1251'); // DB encoding. This value can not be changed.
define('WLCMS_WRITABLE_CHMOD',          '0775'); // [ 0775 | 0777 ] SET Chmod to files and directories to can change them

define('WLCMS_ERROR_REPORTING',         E_ALL); // [ E_ALL | 0 ] SET WAY TO SHOW ERRORS
define('WLCMS_SMARTY_ERROR_REPORTING',  E_ALL); // [ E_ALL | 0 ] SET WAY TO SHOW ERRORS

define('WLCMS_DEBUG',                   0); // [1 - true | 0 - false ] SET Script Debug Status

define('WLCMS_SESSION_NAME',            'WLCMSSESSID'); // SET Weblife session name for Request array

define('MDATA_KNAME',                   'modulesData'); // SET string Modules key name for array data storage in SESSION

define('DBTABLE_LANG_SEP',              '_'); // SET separate char for use in DataBase Table Names

define('URL_INLUDES_THE_LANG',          1); // set 1 to include lang code to URL client browes or 0 to not
define('AUTODETECT_CLIENT_LANG',        1); // set 1 to auto detect client browes lang or 0 to not
define('LANG_LIVE_IN_COOKIE',           3600 * 24 * 7); // set live period for lang in cookie in seconds Like (3600 * 24 * 7) - for one week

define('URL_ENABLE_SUFFIX',             0); // set 1 to include seo suffix like .html to URL or 0 to not
define('URL_SEO_SUFFIX',                '.html'); // set String text suffix for append to Url

define('UPLOAD_DIR',                    'uploaded'); // set WebLife CMS UPLOAD DIR
define('UPLOAD_URL_DIR',                '/'.UPLOAD_DIR.'/'); // set WebLife CMS UPLOAD URL DIR

define('MAIN_CATEGORIES_DIR',           UPLOAD_DIR.DS.'main'); // set WebLife CMS MAIN CATEGORIES DIR FROM DB
define('MAIN_CATEGORIES_URL_DIR',       UPLOAD_URL_DIR.'main'.'/'); // set WebLife CMS MAIN CATEGORIES URL DIR FROM DB

define('SESSION_INACTIVE',              60*60); // set timeout period in seconds
define('MAX_WRONG_PASSWORDS',           5); // set maximum inputed passwords by user per same time
define('WRONG_SUBMITS_TO_CAPTCHA',      2); // set maximum submited wrong auth data by user per same time to show captcha
define('BANNED_TIME',                   3*60); // set banned timeout period in seconds
define('IVALIDATOR_MAX_LENTH',          7); // Set count charachters for Ivalidator Check Code
define('CHECKWORD_SALT_LENTH',          7); // Set count charachters for User CheckWord Salt
define('CHECKW_CONFIRMCODE_DAYS_KEEP',  3); // Set count of days to keep checkword or ConfirmCode alive

define('ENABLE_TRACKING_ECOMMERCE',     false); // set ecommerce operations true|false

define('WLCMS_SMARTY_DIR',              WLCMS_ABS_ROOT.DS.'include'.DS.'smarty'.DS); // set WebLife CMS SMARTY absolute path DIR

// Admin Area constans
define('TPL_BACKEND_FORSE_COMPILE',     0); // [ 0 | 1 ] set the forse compile smarty template
define('TPL_BACKEND_CACHING',           0); // [ 0 | 1 ] set the caching smarty template is enabled
define('TPL_BACKEND_NAME',              'weblife'); // set name of smarty tpl folder

// Site Area constans
define('TPL_FRONTEND_FORSE_COMPILE',    0); // [ 0 | 1 ] set the forse compile smarty template
define('TPL_FRONTEND_CACHING',          0); // [ 0 | 1 ] set the caching smarty template is enabled
define('TPL_FRONTEND_NAME',             'smart'); // [ simple | simple_wide | smart ] set name of smarty tpl folder

// Users Types
define('USER_TYPE_SUPERADMIN',          'Superadmin');
define('USER_TYPE_ADMINISTRATOR',       'Administrator');
define('USER_TYPE_MANAGER',             'Manager');
define('USER_TYPE_EDITOR',              'Editor');
define('USER_TYPE_PUBLISHER',           'Publisher');
define('USER_TYPE_USER',                'User');
define('USER_TYPE_CLIENT',              'Client');
define('USER_TYPE_MEMBER',              'Member');
define('USER_TYPE_REGISTERED',          'Registered');
define('USER_TYPE_ANONIMOUS',           'Anonimous');


///////////////// CHECK SYSTEM \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
require_once(WLCMS_ABS_ROOT.'include/sys_verifications.php');
