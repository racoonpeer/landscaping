<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/ 
define('WEBlife', 1); //Set flag that this is a parent file
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area

# ##############################################################################
// ////////////////// OPERATION GLOBAL VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Page Offset Var = integer
$page   = (isset($_GET['page']) && $_GET['page']) ? intval($_GET['page']) : 1;
// SET from $_GET Global Array AJAX Module Var = int
$ajax   = (isset($_GET['ajax']) && $_GET['ajax']) ? 1                     : 0;
// SET from $_GET Global Array Task Var = string
$task   = !empty($_GET['task'])                   ? trim($_GET['task'])   : 'default';
// SET from $_GET Global Array Module Page Name Var = string
$module = !empty($_GET['module'])                 ? trim($_GET['module']) : 'welcome';
// //////////////// END OPERATION GLOBAL VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


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
require_once('include/classes/Validator.php');      // 7. Include Text Validator class
require_once('include/classes/IValidator.php');     // 8. Include Image IValidator class
require_once('include/helpers/PHPHelper.php');      // 9. Custom PHP functions
require_once('include/helpers/HTMLHelper.php');     //10. Custom HTML functions
require_once('include/classes/wideimage/WideImage.php');
require_once('include/classes/ImagesUpload.php');
require_once('include/classes/ActionsLog.php');
require_once('include/classes/UserAccess.php');     // 11. Include User Access class 
$DB         = new DbConnector();
$Validator  = new Validator();
$IValidator = new IValidator(getIValidatorPefix());
$smarty     = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
$PHPHelper  = new PHPHelper();  //Initialize PHPHelper class with Custom PHP functions
$HTMLHelper = new HTMLHelper();  //Initialize HTMLHelper class with Custom HTML functions
$UserAccess = new UserAccess();
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


################################################################################
// /////////////////// IMPORTANT CACHE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$cacheID = getCacheId(); //cache ID of Smarty compiled template 
if($smarty->caching && $smarty->isCached(getTemplateFileName($ajax), $cacheID)){
    $smarty->display(getTemplateFileName($ajax), $cacheID);
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
                    'page'          => &$page,
                    'ajax'          => &$ajax,
                    'task'          => &$task,
                    'module'        => &$module,
                    'cid'           => 0,    // category ID
                    'pid'           => 0,    // parent category ID
                    'itemID'        => 0,    // Item ID
                    'admin_url'     => '/'.basename(__FILE__).'?module='.$module.($ajax ? '&ajax='.$ajax : ''),
                    'page_url'      => $page>1 ? '&page='.$page : '',
                    'category_url'  => '',
                    'parent_url'    => '',
                    'filter_url'    => '',
                    'css_dir'       => '/css/'.TPL_BACKEND_NAME.'/',
                    'files_url'     => UPLOAD_URL_DIR,
                    'files_path'    => UPLOAD_DIR,
                    'def_img_param' => array('w'=>100, 'h'=>100),
                    'images_params' => array(),
                    'arrOrderLinks' => array(),
                    'arrBreadCrumb' => array(),
                    'arrParent'     => array(),
                    'moduleRootID'  => getCatIdByModule($module, 0, false),
                    'items_on_page' => 10,
                    'total_items'   => 0,
                    'total_pages'   => 1,
                    'seo_separator' => ' - ',
                    'system_images' => '/images/operation/',
                    'images_dir'    => '/images/admin/',
                    'headTitle'     => 'TYPO3',
                    'headCss'       => array(),
                    'headScripts'   => array(),
                    'robots'        => array('index,follow','noindex,nofollow','index,nofollow','noindex,follow','index','follow','noarchive','noodp'),
                    'messages'      => getSessionMessages(),
                    'errors'        => getSessionErrors(),
                    'success'       => false
                  );
$arrPageData['offset']              = ($page-1)*$arrPageData['items_on_page'];
$arrPageData['path_arrow']          = '&#62;';//'<img src="'.$arrPageData['images_dir'].'arrow.gif" alt="" />';
$arrPageData['current_url']         = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['newComments'] = getValueFromDB(COMMENTS_TABLE, 'COUNT(`id`)', 'WHERE `isnew`=1');
$arrPageData['newOrders'] = getValueFromDB(ORDERS_TABLE, 'COUNT(`id`)', 'WHERE `status`=1');
$arrPageData['main_menu'] = getRowItems(MODULES_PARAMS_TABLE, '*', '`menu`=1', '`order`');
// Set last site zone usage to session
setZoneToSession();
// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################

// INCLUDE ADMIN USER AUTHENTICATION FILE
if(file_exists("include".DS.getAuthFileName().".php")) include("include".DS.getAuthFileName().".php");
else die("Файл аутентификации невозможно подключить. Проверьте наличие файла, пути и правильность его подключения!");

// SAVE HISTORY ACTIONS AND USER ACCESS
ActionsLog::createInstance($objUserInfo, getRealIp());
$UserAccess->init($objUserInfo->id, $objUserInfo->type);

// INCLUDE ADMIN MODULE
$ajaxDir = $ajax ? 'ajax'.DS : '';
if ($module && file_exists('admin'.DS.$ajaxDir.$module.'.php')) include_once('admin'.DS.$ajaxDir.$module.'.php');

################################################################################
// //////////////// READY PARAMS TO SMARTY FLASH \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('sessionID',                session_id());
$smarty->assign('lang',                     $lang);
$smarty->assign('UrlWL',                    $UrlWL);
$smarty->assign('arLangsUrls',              $arLangsUrls);
$smarty->assign('arAcceptLangs',            $arAcceptLangs);
$smarty->assign('arrLangs',                 SystemComponent::getAcceptLangs());
$smarty->assign('arrModules',               $arrModules);
$smarty->assign('arrPageData',              $arrPageData);
$smarty->assign('objUserInfo',              $objUserInfo);
$smarty->assign('objSettingsInfo',          $objSettingsInfo);
$smarty->assign('HTMLHelper',               $HTMLHelper);
$smarty->assign('PHPHelper',                $PHPHelper);
// \\\\\\\\\\\\\\\\ END READY PARAMS TO SMARTY FLASH ///////////////////////////
################################################################################


# ##############################################################################
// /////////////////// OPERATIONS BEFORE DISPLAY \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 $Cookie->process();
// \\\\\\\\\\\\\\\\\ END OPERATIONS BEFORE DISPLAY /////////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// SMARTY DISPLAY \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->display(getTemplateFileName($ajax), $cacheID);
// \\\\\\\\\\\\\\\\\\\\\\\ END SMARTY DISPLAY //////////////////////////////////
# ##############################################################################
