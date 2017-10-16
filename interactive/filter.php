<?php define('WEBlife', 1);

// change to root dir
chdir("..".DIRECTORY_SEPARATOR);
define('WLCMS_ZONE', 'FRONTEND');
require_once('include/functions/base.php');         // 1. Include base functions
require_once('include/functions/menu.php');         // 1. Include base functions
require_once('include/functions/image.php');        // 2. Include image functions
require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');     // 3. Include Languages File
require_once('include/system/tables.php');          // 4. Include DB tables File
require_once('include/classes/DbConnector.php');    // 5. Include DB class
require_once('include/classes/Filters.php');
require_once('include/classes/Basket.php');
require_once('include/helpers/PHPHelper.php');
require_once('include/helpers/HTMLHelper.php');
$DB         = new ExternalDbConnector();
$UrlWL->init($DB);
$Basket     = new Basket();
$PHPHelper  = new PHPHelper();  
$HTMLHelper = new HTMLHelper();

$objUserInfo     = getUserFromSession($DB->getTableColumnsNames(USERS_TABLE));
$objSettingsInfo = getSettings();

$Basket->setupKitParams(PRODUCT_KIT_PREFIX);
$arrModules      = getModules();
$module = $arrModules['catalog']['module'];

// SET from $_GET Global Array Page Offset Var = integer
$page   = $UrlWL->getPageNumber();
$pages_all = false;

$arrPageData = array( //Page data array
    'page'          => &$page,
    'itemID'        => 0,    // Item ID
    'backurl'       => '',
    'files_url'     => UPLOAD_URL_DIR,
    'files_path'    => UPLOAD_DIR,
    'arrBreadCrumb' => array(),
    'items_on_page' => 12,
    'total_items'   => 0,
    'total_pages'   => 1,
    'seo_separator' => ' - ',
    'css_dir'       => '/css/'.TPL_FRONTEND_NAME.'/',
    'images_dir'    => '/images/site/'.TPL_FRONTEND_NAME.'/',
    'messages'      => getSessionMessages(),
    'errors'        => getSessionErrors(),
    'success'       => false,
    'wishlist'      => array(),
);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
// get wishlist from cookies
$arrPageData['wishlist'] = ($Cookie->isSetCookie('wishlist')) ? unserialize($_COOKIE['wishlist']) : array();
// get compare from cookies
$arrPageData['compare'] = ($Cookie->isSetCookie('compare')) ? unserialize($_COOKIE['compare']) : array();

if(UrlWL::isAjaxRequest()) {
    $sort            = !empty($_GET['sort']) ? intval($_GET['sort']) : 0;
    $catid           = !empty($_GET['cid'])? intval($_GET['cid']): 0;
    $selectedFilters = !empty($_GET['filters'])? PHPHelper::dataConv($_GET['filters'], 'utf-8', 'windows-1251'): array();
    
    $items           = array();
    $images_params   = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="'.$module.'"'));    
    $arCategory      = $catid ? getItemRow(MAIN_TABLE, '*', 'WHERE `id`='.$catid) : $arrModules['catalog'];
    //IF you want to show all subcategories  products  - uncomment below line
    $showSubItems    = true;
    $arCatIdSet      = array_merge(array($catid), ($showSubItems ? getChildrensIDs($catid, true) : array()));
    
    $Filters = new Filters($UrlWL, $catid, $arCatIdSet);
    
    $where = $Filters->generateWhereState($selectedFilters);

    $itemsIDX = PHPHelper::getCatalogItems($arCatIdSet, $lang, $module, '', '', '', true);
    $itemsFIDX = PHPHelper::getCatalogItems($arCatIdSet, $lang, $module, $where,'', '', true);
    
    $arrPageData['sort'] = $sort;
    $arrPageData['arSorting'] = PHPHelper::getCatalogSorting($UrlWL, $sort);
    $arrPageData['selectedFilters'] = $selectedFilters;
    $arrPageData['filter_url'] = $UrlWL->buildQuery();
    $arrPageData['filters'] = $Filters->getFilters($itemsIDX, $itemsFIDX); 

    if(!$pages_all){
        // Total pages and Pager
        $arrPageData['total_items'] = count($itemsFIDX);
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildCategoryUrl($arCategory, '', '', 1, true));
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager
    }    
    $order = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
    $limit = (!$pages_all ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
    $query = PHPHelper::getCatalogItemsSql($arCatIdSet, $lang, $module, $where, $order, $limit);
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if(mysql_num_rows($result) > 0) {
        while ($item = mysql_fetch_assoc($result)) {
            $items[] = PHPHelper::getProductItem($item, $UrlWL, $arrPageData['files_url'], $images_params, true);
        }
    } 

    require_once('include/classes/mySmarty.php');
    $smarty = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
    $smarty->assign('arCategory', $arCategory);
    $smarty->assign('arrPageData', $arrPageData);
    $smarty->assign('HTMLHelper', $HTMLHelper);
    $smarty->assign('UrlWL', $UrlWL);
    $smarty->assign('items', $items);
    $smarty->assign('Basket', $Basket);
    $smarty->assign('arrModules', $arrModules);
    
    $json['url'] = $arrPageData['filter_url'];
    $json['output']['selected'] = $smarty->fetch('ajax/selected_filters.tpl');
    $json['output']['products'] = $smarty->fetch('ajax/products.tpl');
    $json['output']['filters'] = $smarty->fetch('ajax/filter.tpl');
    
    echo json_encode(PHPHelper::dataConv($json));        
}