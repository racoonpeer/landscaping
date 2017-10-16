<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');         // 1. Include base functions
require_once('include/functions/image.php');        // 2. Include image functions
require_once('include/functions/menu.php');         // 3. Include menu functions

require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');     // 3. Include Languages File
require_once('include/system/tables.php');          // 4. Include DB tables File
require_once('include/classes/mySmarty.php');       // 5. Include mySmarty class
require_once('include/classes/DbConnector.php');    // 6. Include DB class
require_once('include/classes/Captcha.php');        // 7. Include Captcha class
require_once('include/classes/Validator.php');      // 8. Include Text Validator class
require_once('include/classes/Currencies.php');     // 9. Include Currencies class
require_once('include/classes/Banners.php');        //10. Include Banners class
require_once('include/classes/Basket.php');         //11. Include Banners class
require_once('include/classes/OAuth.php');          //11. Include Oauth class
require_once('include/helpers/PHPHelper.php');      //12. Custom PHP functions
require_once('include/helpers/HTMLHelper.php');     //13. Custom HTML functions
require_once('include/classes/TrackingEcommerce.php');
require_once('include/classes/Ulogin.php');
$DB         = new DbConnector(); //Initialize DbConnector class
$Captcha    = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);  //Initialize Captcha class
$Validator  = new Validator(); //Initialize Validator class
$smarty     = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING); //Initialize mySmarty class
$Currencies = new Currencies();  //Initialize Currencies class
$Banners    = new Banners($UrlWL, true);  //Initialize Banners class
$Basket     = new Basket();  //Initialize Basket class
$PHPHelper  = new PHPHelper();  //Initialize PHPHelper class with Custom PHP functions
$HTMLHelper = new HTMLHelper();  //Initialize HTMLHelper class with Custom HTML functions
$OAuth      = new OAuth();
$Ulogin     = new Ulogin($DB, $UrlWL);
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////// OPERATION GLOBAL VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Инициализируем обработчик URL 
$UrlWL->init($DB);
// Initialize Current Category ID
$catid  = $UrlWL->getCategoryId();
// SET from $_GET Global Array Page Offset Var = integer
$page   = $UrlWL->getPageNumber();
// SET from $_GET Global Array AJAX Mode Var = int
$ajax   = $UrlWL->getAjaxMode();
// SET from $_GET Global Array Module Of Page Var = string
$module = $UrlWL->getModuleName();
// Detect ajax request
$IS_AJAX = UrlWL::isAjaxRequest();
// Detect local host
$IS_DEV = getenv("IS_DEV");
// Minify output
if (!$IS_DEV) $smarty->loadFilter('output', 'trimwhitespace');

$Basket->setupKitParams(PRODUCT_KIT_PREFIX);

################################################################################
// /////////////////// IMPORTANT CACHE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$cacheID = getCacheId($catid); //cache ID of Smarty compiled template 
if($smarty->caching && $smarty->isCached(getTemplateFileName($ajax, $catid), $cacheID)){
    $smarty->display(getTemplateFileName($ajax, $catid), $cacheID);
    exit();
} // \\\\\\\\\\\\\\\ END IMPORTANT CACHE OPERATIONS ////////////////////////////
# ##############################################################################


################################################################################
// /////////////////// IMPORTANT GLOBAL VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$objUserInfo     = getUserFromSession(); //user info object
$objSettingsInfo = getSettings(); //settings info object
$arLangsUrls     = $UrlWL->createLangsUrls($DB); //Langs Array to redirect
$arrModules      = getModules(); //Modules Array where array key is module name
$arrPageData     = array( //Page data array
    'catid'         => &$catid,
    'page'          => &$page,
    'ajax'          => &$ajax,
    'itemID'        => 0,    // Item ID
    'backurl'       => '',
    'files_url'     => UPLOAD_URL_DIR,
    'files_path'    => UPLOAD_DIR,
    'def_img_param' => array('w'=>100, 'h'=>100),
    'images_params' => array(),
    'arrOrderLinks' => array(),
    'arrBreadCrumb' => array(),
    'items_on_page' => 10,
    'total_items'   => 0,
    'total_pages'   => 1,
    'seo_separator' => ' - ',
    'css_dir'       => '/css/'.TPL_FRONTEND_NAME.'/',
    'images_dir'    => '/images/site/'.TPL_FRONTEND_NAME.'/',
    'headTitle'     => '',
    'headCss'       => array(),
    'headScripts'   => array(
        "/js/libs/modernizr/modernizr.min.js",
        "/js/libs/jquery/jquery.min.js",
        "/js/libs/jquery-migrate/jquery-migrate.min.js",
        "/js/libs/verge/verge.min.js",
        "/js/common.js",
    ),
    'messages'      => getSessionMessages(),
    'errors'        => getSessionErrors(),
    'success'       => false,
    'wishlist'      => array(),
    'compare'       => array(),
);
$arrPageData['offset']     = ($page-1)*$arrPageData['items_on_page'];
$arrPageData['path_arrow'] = '<img src="'.$arrPageData['images_dir'].'arrow.gif" alt="" />';
// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################


################################################################################
// ///////////// INITIALIZE CATEGORY AND BREADCRUMB ARRAYS  \\\\\\\\\\\\\\\\\\\\
// Initialise the Current category array
$arCategory = getItemRow(MAIN_TABLE, '*', "WHERE id={$catid}");
//Anscreen Data From DB
$arCategory['text'] = unScreenData($arCategory['text']);
// Set to Category array accsess variable taked recursively by parent
$arCategory['access'] = canAccess($catid, true);
// Set arPath to Category
$arCategory['arPath'] = $UrlWL->getCategoryNavPath();
// Set breadCrumb to array
$arrPageData['arrBreadCrumb'] = $UrlWL->getBreadCrumbs();
// Set current category module
$module = $arCategory['module'];
// Init Banners By Current Category ID
$Banners->init($catid);
// Set Root Menu ID
$arrPageData['rootID'] = GetRootId($catid);
// Set Root Menu array
$arrPageData['arRootMenu'] = ($arrPageData['rootID']==$catid) ? $arCategory : getItemRow(MAIN_TABLE, '*', "WHERE id={$arrPageData['rootID']}");
// Set Captcha Default site parameters
$Captcha->SetCodeChars(str_split("abcdefghijknmpqrstuvwxyz0123456789"));
$Captcha->SetCodeLength(5);

// Set to Save basket to DB if user is logined
if($objUserInfo->logined) {
    $Basket->setSaveDataBase($objUserInfo->id);
}
// Get wishlist from cookie
if($Cookie->isSetCookie('wishlist')) {
    $arrPageData['wishlist'] = unserialize($_COOKIE['wishlist']);
}
// Get compare from cookies
if($Cookie->isSetCookie('compare')) {
    $arrPageData['compare'] = unserialize($_COOKIE['compare']);
}

// check error catid
if($arCategory['id'] != UrlWL::ERROR_CATID) {
    // Set last site zone usage to session
    setZoneToSession();
} else if(!headers_sent()) {
    header('HTTP/1.0 404 Not Found');
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// дата в прошлом
    header("Last-Modified: " . gmdate("D, d M Y H(idea)(worry)") . " GMT");
     // всегда модифицируется
    header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");// HTTP/1.0
}
// \\\\\\\\\\\ END INITIALIZE CATEGORY AND BREADCRUMB ARRAYS ///////////////////
################################################################################


// INCLUDE USER AUTHENTICATION FILE
if(file_exists("include".DS.getAuthFileName().".php")) include("include".DS.getAuthFileName().".php");
else die("Файл аутентификации невозможно подключить. Проверьте наличие файла, пути и правильность его подключения!");

// Check User can Accsess to this page
if(!$arCategory['access'] && !$arrPageData['auth']){
    $ajax
        ? RedirectAjax($UrlWL->buildCategoryUrl($arrModules['authorize']), $_SERVER['REQUEST_URI'])
        : Redirect($UrlWL->buildCategoryUrl($arrModules['authorize']), $_SERVER['REQUEST_URI'])
    ;
}

// INCLUDE CATEGORY  MODULE
if ($module && file_exists('module'.DS.$module.'.php')) {
    include_once('module'.DS.$module.'.php');
}


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if(!empty($_SESSION[MDATA_KNAME])){ // Clear unUsed Session Modules Data
    foreach($_SESSION[MDATA_KNAME] as $mdkey=>$mdvalue){
        if($mdkey==$module || $mdkey=='search') continue;
        unset($_SESSION[MDATA_KNAME][$mdkey]);
    }
}

// Clear unUsed Session Basket of Modules Data
if(!in_array($arCategory['module'], array('catalog', 'search', 'basket', 'newest', 'popular'))){
    if(isset($_SESSION['basket_lastid'])) unset($_SESSION['basket_lastid']);
    if(isset($_SESSION['basket_lastpage']))  unset($_SESSION['basket_lastpage']);
    if(isset($_SESSION['basket_pagesall']))  unset($_SESSION['basket_pagesall']);
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


################################################################################
// //////////////// READY PARAMS TO SMARTY FLASH \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('Basket',                   $Basket);
$smarty->assign('sessionID',                session_id());
$smarty->assign('Banners',                  $Banners);
$smarty->assign('Currencies',               $Currencies);
$smarty->assign('Captcha',                  $Captcha);
$smarty->assign('UrlWL',                    $UrlWL);
$smarty->assign('lang',                     $lang);
$smarty->assign('arLangsUrls',              $arLangsUrls);
$smarty->assign('arAcceptLangs',            $arAcceptLangs);
$smarty->assign('arrLangs',                 SystemComponent::getAcceptLangs());
$smarty->assign('arCategory',               $arCategory);
$smarty->assign('arrModules',               $arrModules);
$smarty->assign('arrPageData',              $arrPageData);
$smarty->assign('objUserInfo',              $objUserInfo);
$smarty->assign('objSettingsInfo',          $objSettingsInfo);
$smarty->assign('HTMLHelper',               $HTMLHelper);
$smarty->assign('trackingEcommerceJS',      TrackingEcommerce::OutputJS(ENABLE_TRACKING_ECOMMERCE));
$smarty->assign('OAuth',                    $OAuth);
// \\\\\\\\\\\\\\\\ END READY PARAMS TO SMARTY FLASH ///////////////////////////
################################################################################

$subMenu = getMenu(1, GetRootId($catid));
if ($module=="news" or $module=="gallery") $subMenu = getMenu(1, GetRootId($catid), 0, 1, array($arrModules[$module]["seo_path"]));
################################################################################
// ///////////// ADDITIONAL DYNAMIC PARAMS TO SMARTY FLASH \\\\\\\\\\\\\\\\\\\\\
// Menus: Main, Top, User, Bottom, etc.    getMenu($type, $pid, $incLevels)
$smarty->assign('mainMenu',             getMenu(1, 0, (strpos(TPL_FRONTEND_NAME, 'simple')!==false ? 1 : 0))); // $type = 1 :  Главное меню
$smarty->assign('subMenu',              $subMenu); // $type = 1 :  Главное меню. Подменю
//$smarty->assign('topMenu',              getMenu(2)); // $type = 2 :  Верхнее меню
//$smarty->assign('leftMenu',             getMenu(3)); // $type = 3 :  Меню слевой стороны
$smarty->assign('bottomMenu',           getMenu(1, 0, 1)); // $type = 4 :  Нижнее меню !IMPORTENT in this case this menu used us type 1
//$smarty->assign('rightMenu',            getMenu(5)); // $type = 5 :  Меню справой стороны 
//$smarty->assign('catalogMenu',          getMenu(6, $arrModules['catalog']['id'])); // $type = 6 :  Меню каталога
//$smarty->assign('userMenu',             getMenu(7)); // $type = 7 :  Меню пользователя
//$smarty->assign('systemMenu',           getMenu(8)); // $type = 8 :  Системное меню
//$smarty->assign('otherMenu',            getMenu(9)); // $type = 9 :  Другое меню

// \\\\\\\\\\\ END ADDITIONAL DYNAMIC PARAMS TO SMARTY FLASH ///////////////////
################################################################################
