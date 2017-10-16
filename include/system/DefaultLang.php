<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

error_reporting(WLCMS_ERROR_REPORTING);//SET WAY TO SHOW ERRORS

require_once(WLCMS_ABS_ROOT.'include'.DS.'classes'.DS.'Url.php');
$UrlWL  = new UrlWL(SystemComponent::getAcceptLangsKeys(), URL_INLUDES_THE_LANG, URL_ENABLE_SUFFIX, URL_SEO_SUFFIX);

// Put your site languages in SystemComponent class
// array('ru', 'ua', 'en')
$arAcceptLangs = $UrlWL->getLangs();
    
if(!defined('WLCMS_EXEC')) {

    setSessId(WLCMS_SESSION_NAME); // Set Session ID before start if Exist
    session_start(); //Start Session

    $langKeyName = getLangKeyNameForSession();

    // Define lang if Change
    if(strlen($UrlWL->getLang())>0 && (empty($_SESSION[$langKeyName]) || $UrlWL->getLang()!=$_SESSION[$langKeyName] || (SystemComponent::checkTestCookie(SystemComponent::getTestCookie()) && isset($_COOKIE[$langKeyName]) && $UrlWL->getLang()!=$_COOKIE[$langKeyName]))){
        $lang = $UrlWL->getLang();
        $UrlWL->storeLang($langKeyName, $lang, LANG_LIVE_IN_COOKIE, (URL_INLUDES_THE_LANG ? false : true), $Cookie);
    }
    // DEFINE LANG option from $_SESSION
    else if(isset($_SESSION[$langKeyName]) && in_array($_SESSION[$langKeyName], $arAcceptLangs)){
        $lang = $_SESSION[$langKeyName];
    }
    // DEFINE LANG option from $_COOKIE
    else if(isset($_COOKIE[$langKeyName]) && in_array($_COOKIE[$langKeyName], $arAcceptLangs)){
        $lang = $_SESSION[$langKeyName] = $_COOKIE[$langKeyName];
    }
    // DEFINE LANG option from auto detect client browsers lang
    else if(AUTODETECT_CLIENT_LANG && count($arAcceptLangs)>1) {
        //include this file if you want to detect lang from user
        require(WLCMS_ABS_ROOT.'include'.DS.'classes'.DS.'LanguageDetect.php');
        $languages = new LanguageDetect();
        $foundLanguages = array();
        // Get from user browser
        if($languages->getLanguagesList($arAcceptLangs, $foundLanguages)){
            $lang = key($foundLanguages);
        }
        // Unset all unused variables to clear memory
        unset($languages, $foundLanguages);
    }
    
    // Define or ReDefine lang if wrong after above initializations
    if(empty($lang) || !in_array($lang, $arAcceptLangs)){
        $lang = $UrlWL->getDefaultLang();
        $UrlWL->storeLang($langKeyName, $lang, LANG_LIVE_IN_COOKIE, (URL_INLUDES_THE_LANG ? true : false), $Cookie);
    }
    // Redirect to default or non default lang page
    else if(URL_INLUDES_THE_LANG && WLCMS_ZONE=='FRONTEND'){
        $cleanedRequestUrl = trim($UrlWL->getUrl(), '/');
        if($lang == $UrlWL->getDefaultLang()){
            if($cleanedRequestUrl == $lang){
                Redirect('/'); 
            }
        } else if($cleanedRequestUrl == ''){
            Redirect("/{$lang}/"); 
        }
        // Unset unused variable to clear memory
        unset($cleanedRequestUrl);
    }

    // Unset unused variable to clear memory
    unset($langKeyName);

} else $lang = $UrlWL->getDefaultLang();

// Set computed lang as current
$UrlWL->setLang($lang);

// include lang file with some constant descriptions
require_once(WLCMS_ABS_ROOT.'include'.DS.'languages'.DS.$lang.'.php');
