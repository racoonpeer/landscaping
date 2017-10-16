<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file

// link = /interactive/ajax.php?zone=[site|admin]&action=[|]
$site_zone =  (isset($_GET['zone']) && !empty($_GET['zone'])) ? addslashes($_GET['zone']) : false;
$action    =  (isset($_GET['action']) && !empty($_GET['action'])) ? addslashes($_GET['action']) : false;

if($site_zone){

    // Define WLCMS_ZONE from $site_zone var 
    switch($site_zone){
        case 'admin': define('WLCMS_ZONE', 'BACKEND');  break;//Set flag that this is a admin area
        case 'site' : define('WLCMS_ZONE', 'FRONTEND'); break;//Set flag that this is a site area
        default:  exit(); break;
    }

    // change to root dir
    chdir("..".DIRECTORY_SEPARATOR);


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
    require_once('include/functions/base.php');         // 1. Include base functions
    require_once('include/functions/image.php');        // 2. Include image functions

    require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
    $Cookie     = new CCookie();
    require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
    require_once('include/system/DefaultLang.php');     // 3. Include Languages File
    require_once('include/system/tables.php');          // 4. Include DB tables File
    require_once('include/classes/DbConnector.php');    // 5. Include DB class
    require_once('include/helpers/PHPHelper.php');
    require_once('include/helpers/HTMLHelper.php');
    require_once('include/classes/OAuth.php');
    require_once('include/classes/wideimage/WideImage.php');
    require_once('include/classes/Validator.php');      // 8. Include Text Validator class
    require_once('include/classes/ActionsLog.php');     // 9. 
    require_once('include/classes/UserAccess.php');     // 10. Include User Access class 
    $DB         = new ExternalDbConnector();
    $PHPHelper  = new PHPHelper();  
    $HTMLHelper = new HTMLHelper();
    $OAuth      = new OAuth(false);
    $Validator  = new Validator();
    $UserAccess = new UserAccess();
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


################################################################################
// /////////////////// IMPORTANT GLOBAL VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    $objUserInfo     = getUserFromSession($DB->getTableColumnsNames(USERS_TABLE));
    $objSettingsInfo = getSettings();
    //init user access
    if(isset($objUserInfo->id) && isset($objUserInfo->type))
        $UserAccess->init($objUserInfo->id, $objUserInfo->type);
    $arrModules      = getModules();
    
    $arrPageData     = array( //Page data array
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
        'headScripts'   => array(),
        'messages'      => getSessionMessages(),
        'errors'        => getSessionErrors(),
        'success'       => false,
        'wishlist'      => ($Cookie->isSetCookie('wishlist')) ? unserialize($_COOKIE['wishlist']) : array(),
        'compare'       => ($Cookie->isSetCookie('compare')) ? unserialize($_COOKIE['compare']) : array()
    );

    // Global json array for json outputs
    $json = array();  

// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################
//    saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), 'log_ajax.txt');
        
    if($action){
        
        //header('Content-type: text/html; charset=windows-1251');
        
        switch($action){
            
            case "AjaxChangeProductOptions":
                $idKey          = !empty($_GET["idKey"]) ? $_GET["idKey"] : false;
                $list           = !empty($_GET["list"])  ? (bool)$_GET["list"] : false;
                $pmodule        = !empty($_GET["module"])? trim(addslashes($_GET["module"])) : "catalog";
                $itemID         = 0;
                $images_params  = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));
                $files_url      = UPLOAD_URL_DIR."catalog/";
                $files_path     = prepareDirPath($files_url);
                if ($idKey) {
                    require_once('include/classes/mySmarty.php');
                    $smarty = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
                    require_once('include/classes/Basket.php');         
                    $Basket = new Basket();
                    $Basket->setupKitParams(PRODUCT_KIT_PREFIX);
                    $opts = array();
                    if (strpos($idKey, $Basket->optionsIndicator)!==false) {
                        $parts  = $Basket->separateIdKey($idKey);//explode($Basket->optionsIndicator, $idKey);
                        $itemID = (int)$parts[0];
                        $opts   = $Basket->separateIdKey($parts[1]);//explode($Basket->optionsSeparator, $parts[1]);
                    } else {
                        $itemID = (int)$idKey;
                    }
                    if ($itemID) {
                        $item = getSimpleItemRow($itemID, CATALOG_TABLE);
                        if (!empty($item)) {
                            $options = array();
                            if (!empty($opts)) {
                                foreach ($opts as $key=>$opt) {
                                    if (!empty($opt)) {
                                        $opt = $Basket->separateIdKey($opt);//explode($Basket->valueSeparator, $opt);
                                        if (strpos($opt[1], $Basket->valueIterator)) $options[$opt[0]] = $Basket->separateIdKey($opt[1]);
                                        else $options[$opt[0]] = $opt[1];
                                    }
                                }
                            }
                            $item = PHPHelper::getProductItem($item, $UrlWL, $files_url, $images_params, $list, $item["cid"], $pmodule, true);
                            $item['selectedOptions'] = $options;
                            $item["options"] = PHPHelper::getProductOptions($itemID, $item["selectedOptions"]);
                            $item["price"]   = PHPHelper::recalcItemPriceByOptions($item["price"],  $item["options"]);
                            $item["cprice"]  = PHPHelper::recalcItemPriceByOptions($item["cprice"], $item["options"]);
                            $item["idKey"]   = PHPHelper::makeProductIdKey($itemID, $item['selectedOptions']);

                            $item['new_price'] = ($item['isdiscount'] AND $item['discount'])? ($item['price'] - ($item['price']*$item['discount']/100)): 0;

                            $smarty->assign("item", $item);
                            $smarty->assign("UrlWL", $UrlWL);
                            $smarty->assign("Basket", $Basket);
                            $smarty->assign("arrModules", $arrModules);
                            $smarty->assign("list", $list);
                            $json["button"] = $smarty->fetch("core/buy_button.tpl");
                            $json["price"]  = $smarty->fetch("core/product_price.tpl");
                            $json["item"]  = $item;
                            echo json_encode(PHPHelper::dataConv($json));
                        }
                    }
                }
                
                
                break;
            
            case "AjaxGetProductItem":
                $itemID = !empty($_GET["itemID"]) ? $_GET["itemID"] : false;
                $cid    = !empty($_GET["cid"])    ? intval($_GET["cid"])    : false;
                $list   = isset($_GET["list"])    ? intval($_GET["list"])   : true;
                $opts   = array();
                if (strpos($itemID, "|")!==false) {
                    $parts = explode("|", $itemID);
                    $itemID = (int)$parts[0];
                    $opts = explode("/", $parts[1]);
                }
                
                if ($itemID AND $cid) {
                    $row = getItemRow(CATALOG_TABLE, "*", "WHERE `id`=$itemID");
                    if (!empty($row)) {
                        $images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));
                        $images_path   = UPLOAD_URL_DIR.'catalog/';
                        $item          = PHPHelper::getProductItem($row, $UrlWL, $images_path, $images_params, (bool)$list, $cid, "catalog");
                        if (!empty($item)) {
                            require_once('include/classes/Basket.php');         
                            $Basket = new Basket();
                            $Basket->setupKitParams(PRODUCT_KIT_PREFIX);
                            require_once('include/classes/mySmarty.php');
                            $smarty = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
                            $smarty->assign("UrlWL", $UrlWL);
                            $smarty->assign("arrModules", $arrModules);
                            $smarty->assign("arrPageData", $arrPageData);
                            $smarty->assign("Basket", $Basket);
                            $smarty->assign("item",  $item);
                            $json["output"] = $smarty->fetch("core/product.tpl");
                            $json["info"] = $smarty->fetch("core/product_info.tpl");
                            echo json_encode(PHPHelper::dataConv($json));
                        }
                    }
                }
                break;
            
            case 'updateShortcuts': 
                    $json['items'] = array(); 
                    $stext  = (!empty($_GET['stext'])) ? htmlspecialchars(strtolower(addslashes(trim(PHPHelper::dataConv($_GET['stext'], 'utf-8', 'windows-1251'))))): '';
                    $cid    = (!empty($_GET['cid']))   ? intval($_GET['cid']): 0;
                    $pid    = (!empty($_GET['pid']))   ? intval($_GET['pid']): 0;
                    $module = (!empty($_GET['object_module']))  ? trim($_GET['object_module']): '';
                    
                    // products search
                    if($module == 'catalog') {
                        $arIdx = array();
                        
                        $squery = 'SELECT `pid` as `id` FROM '.SHORTCUTS_TABLE.' 
                                   WHERE `module`="'.$module.'" AND `cid`='.$cid.
                                   ($pid ? ' OR (`cid`='.$cid.' AND `pid`='.$pid.')' : '');
                        
                        $cquery = 'SELECT `id` FROM '.CATALOG_TABLE.' WHERE `cid`='.$cid;
                        
                        $result = mysql_query('('.$squery.') UNION ALL ('.$cquery.')');
                        if(mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $arIdx[] = $row['id'];
                            }
                        }

                        $json['enable'] = ($pid && !empty($arIdx) && in_array($pid, $arIdx)) ? false : true;
                        
                        if($stext) {
                            $select = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                            $join   = 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`)  ';
                            $where  = 'WHERE (c.`title` LIKE "%'.$stext.'%" OR c.`pcode` LIKE "%'.$stext.'%")  ';
                            $where .= (!empty($arIdx) ? ' AND c.`id` NOT IN ('.implode(',', $arIdx).')' : '');
                            $order  = 'GROUP BY c.`id` ORDER BY c.`title`, m.`title`';
                            $query  = $select.$join.$where.$order;
                            $result = mysql_query($query);
                            if(mysql_num_rows($result) > 0) {
                                while ($row = mysql_fetch_assoc($result)) {
                                    $row['title'] = unScreenData($row['title']);
                                    $row['ctitle'] = unScreenData($row['ctitle']);
                                    $json['items'][] = $row;
                                }
                            }
                        }
                    }
                    echo json_encode(PHPHelper::dataConv($json));
               
                break;
            case 'getImgSettings':
                $module = (!empty($_GET['module']))  ? PHPHelper::dataConv($_GET['module'], 'utf-8', 'windows-1251')   : '';
                $index = (!empty($_GET['index']))  ? intval($_GET['index'])   : 0;
                $json = array();

                if($module) {
                    require_once('include/classes/mySmarty.php');
                    $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);

                    $smarty->assign('module', $module);
                    $smarty->assign('index', $index);
                    $smarty->assign('aliases', SystemComponent::getArImgAliases());
                    $smarty->assign('arImages', array());
                    
                    $json['image'] = PHPHelper::dataConv($smarty->fetch('common/module_images_settings.tpl'));
                }
                
                echo json_encode($json);
                break;
            case 'getAccessSettings':
                $option = (!empty($_GET['option'])) ? $_GET['option'] : '';
                $modules = (!empty($_GET['modules'])) ? $_GET['modules'] : '';
                $gid = (!empty($_GET['gid']))  ? intval($_GET['gid'])   : 0;
                $uid = (!empty($_GET['uid']))  ? intval($_GET['uid'])   : 0;
                $json = array();
                
                
                switch ($option) {
                    case 'reset':
                        deleteRecords(USERS_ACCESS_TABLE, 'WHERE `uid`='.$uid.' AND `gid`='.$gid);
                        $arModulesParams = getRowItems(MODULES_PARAMS_TABLE, '*', '`access`=1');
                        uasort($arModulesParams, 'mySort');
                        $settings = getItemRow(USERS_ACCESS_TABLE, 'modules', 'WHERE `gid`='.$gid);
                        
                        require_once('include/classes/mySmarty.php');
                        $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                        $smarty->assign('arModules', $arModulesParams);
                        $smarty->assign('gid', $gid);
                        $smarty->assign('uid', $uid);
                        $smarty->assign('availableModules', !empty($settings) ? explode(',', $settings['modules']) : array());
                        
                        $json['messages'] = 'Значения восстановлены до значений группы';
                        $json['settings'] = $smarty->fetch('ajax/access_settings.tpl'); 
                        break;
                    case 'update':
                        if(!empty($gid)){
                            $arModulesParams = getRowItems(MODULES_PARAMS_TABLE, '*', '`access`=1');
                            uasort($arModulesParams, 'mySort');
                            $settings = getItemRow(USERS_ACCESS_TABLE, '*', 'WHERE `gid`='.$gid.' AND (`uid`='.$uid.' OR `uid`=0) ORDER BY `uid` DESC');
                           
                            require_once('include/classes/mySmarty.php');
                            $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                            $smarty->assign('arModules', $arModulesParams);
                            $smarty->assign('gid', $gid);
                            $smarty->assign('uid', $uid);
                            $smarty->assign('availableModules', !empty($settings) ? explode(',', $settings['modules']) : array());
                            
                            
                            $json['settings'] = $smarty->fetch('ajax/access_settings.tpl'); 
                        } else $json['errors'] = 'Ошибка сохранения';
                        break;
                    case 'save':
                        if(!empty($gid)){
                            deleteRecords(USERS_ACCESS_TABLE, 'WHERE `uid`='.$uid.' AND `gid`='.$gid);
                            $result = $DB->postToDB(array('uid'=>$uid, 'gid'=>$gid, 'modules'=>$modules), USERS_ACCESS_TABLE);
                            if($result) $json['messages'] = 'Данные успешно сохранены';
                            else $json['errors'] = 'Ошибка сохранения';
                        } else $json['errors'] = 'Ошибка сохранения';
                        break;
                }

                
                echo json_encode(PHPHelper::dataConv($json));
                break;
            
            case 'filterActionsLog':
                $filters = (!empty($_GET['filters']))  ? PHPHelper::dataConv($_GET['filters'], 'utf-8', 'windows-1251')   : array('time'=>1);
                $key = (!empty($_GET['key']))  ?trim($_GET['key'])   : '';
                $showMore = (!empty($_GET['type']))  ?  true  : false;
                $json = array();

                $arActionsLog['arHistory'] = ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getHistory($filters);
                $arActionsLog['arFilters'] = ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getFilters($filters, $key);
                
                require_once('include/classes/mySmarty.php');
                $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                
                $smarty->assign('arHistoryData', $arActionsLog['arHistory']);
                $smarty->assign('arFilters', $arActionsLog['arFilters']);
                $smarty->assign('selectedFilters', $filters);
                
                $json['history'] = $showMore ? PHPHelper::dataConv($smarty->fetch('ajax/object_actions_log_body.tpl')) : PHPHelper::dataConv($smarty->fetch('ajax/actions_log.tpl'));  
                $json['filters'] = PHPHelper::dataConv($smarty->fetch('ajax/actions_log_filters.tpl'));
                $json['url'] = $arActionsLog['arHistory']['filtersUrl'];
                $json['page'] = $arActionsLog['arHistory']['page']+1 <= $arActionsLog['arHistory']['total_pages'] ? $arActionsLog['arHistory']['page']+1 : '';
                
                echo json_encode($json);
                break;
            
            case 'orderProducts':
                $option    = (!empty($_GET['option']))  ? trim($_GET['option'])   : die('Choose method option!');
                $itemID    = (!empty($_GET['itemID']))  ? intval($_GET['itemID']) : die('Undefined item ID!');
                $hasAccess = $UserAccess->getAccessToModule('orders');
                $images_path = UPLOAD_URL_DIR."catalog/";
                $images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));
                // обновление данных на странице
                if($option=='update') {
                    $arItem = getItemRow(ORDERS_TABLE, '*', 'WHERE `id`='.$itemID);
                    $arShipping = getItemRow(SHIPPING_TYPES_TABLE, '*', 'WHERE `id`='.$arItem['shipping']);
                    $sPrice  = $arShipping['price'];
                    $price = 0;
                    $arItems = array();
                    $select = 'SELECT op.* FROM `'.ORDER_PRODUCTS_TABLE.'` op ';
                    $where = 'WHERE op.`oid`='.$itemID.' ';
                    $order = 'ORDER BY op.`id`';
                    $query = $select.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $arOptions = unserialize(unScreenData($row["options"]));
                            // для товара
                            if($row['type']=="product") {
                                $product = getItemRow(CATALOG_TABLE, '*', 'WHERE `id`='.$row['pid']);
                                if(!empty($product))  {
                                    $row['link'] = $UrlWL->buildItemUrl($UrlWL->getCategoryById($product['cid']), $product);
                                    $row['ptitle'] = $product['title'];
                                    $row["selectedOptions"] = isset($arOptions[$product["id"]]) ? $arOptions[$product["id"]] : array();
                                    $row["options"] = PHPHelper::getProductOptions($product["id"], $row["selectedOptions"]);
                                } else {
                                    $row['link'] = null;
                                    $row['ptitle'] = $row['title'];
                                    $row["selectedOptions"] = array();
                                    $row["options"] = array();
                                }
                            } 
                            // для комплекта
                            elseif ($row['type']=="kit"){
                                $row['link'] = null;
                                $row['ptitle'] = $row['title'];
                                $row["children"] = array();
                                $kitx = !empty($row["kitx"]) ? explode(",", $row["kitx"]) : array(0);
                                $qry  = "SELECT c.*, IF(ck.`pid`=c.`id`, 1, 0) AS `primary` FROM `".CATALOG_TABLE."` c ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck ON(ck.`pid` = c.`id`) ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck2 ON(ck2.`kid` = c.`id`) ";
                                $qry .= "WHERE c.`id` IN(".implode(",", $kitx).") ";
                                $qry .= "GROUP BY c.`id` ORDER BY `primary` DESC LIMIT 1";
                                $res  = mysql_query($qry);
                                if ($res AND mysql_num_rows($res) > 0) {
                                    while ($kit = mysql_fetch_assoc($res)) {
                                        $kit["selectedOptions"] = isset($arOptions[$kit["id"]]) ? $arOptions[$kit["id"]] : array();
                                        $kit["options"] = PHPHelper::getProductOptions($kit["id"], $kit["selectedOptions"]);
                                        $row["children"][] = $kit;
                                    }
                                }
                            }
                            $price += ($row['price'] * $row['qty']);
                            $arItems[] = $row;
                        }
                    }
                    
                    // пересчет стоимости доставки
                    if($price > $arShipping['minprice']) {
                        $sPrice = 0;
                    }
                    
                    // обновление цены
                    updateRecords(ORDERS_TABLE, '`price`="'.$price.'"', 'WHERE `id`='.$itemID);
                    
                    require_once('include/classes/mySmarty.php');
                    $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                    
                    $smarty->assign('arItems', $arItems);
                    $smarty->assign('price', $price);
                    $smarty->assign('sPrice', $sPrice);
                    $smarty->assign('arrPageData', $arrPageData);
                    $smarty->assign('arHistoryData', ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getHistory(array('modules' => array('orders'), 'oid'=>$itemID, 'langs'=>array($lang))));
                    
                    $json['history'] = $smarty->fetch('common/object_actions_log.tpl');
                    $json['output'] = $smarty->fetch('ajax/order_products.tpl');
                    echo json_encode(PHPHelper::dataConv($json));
                }
                // поиск товаров
                elseif ($option=="search") {
                    $json['items'] = array(); 
                    $stext = (!empty($_GET['stext']))? htmlspecialchars(strtolower(addslashes(trim(PHPHelper::dataConv($_GET['stext'], 'utf-8', 'windows-1251'))))): '';
                    $exItems = (!empty($_GET['exItems']))? $_GET['exItems']: array(0);
                    
                    // products search
                    $select = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                    $join   = 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                    $where  = 'WHERE c.`active`=1 AND m.`active`=1 AND c.`id` NOT IN('.implode(',', $exItems).') ';
                    $where .= 'AND (c.`title` LIKE "%'.$stext.'%" OR c.`pcode` LIKE "%'.$stext.'%") ';
                    $order  = 'GROUP BY c.`id` ORDER BY c.`title`, m.`title`';
                    $query  = $select.$join.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $row['title'] = unScreenData($row['title']);
                            $row['ctitle'] = unScreenData($row['ctitle']);
                            $row['type'] = 'product';
                            $json['items'][] = $row;
                        }
                    }

                    // product kits search
                    $select = 'SELECT c.`id`, CONCAT_WS(", ", m.`title`, GROUP_CONCAT(mt.`title` SEPARATOR ", ")) AS `ctitle`, CONCAT_WS(" + ", c.`title`, GROUP_CONCAT(ct.`title` SEPARATOR " + ")) AS `title` FROM `'.CATALOG_KITS_TABLE.'` ck ';
                    $join   = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id`=ck.`pid`) ';
                    $join  .= 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id`=c.`cid`) ';
                    $join  .= 'LEFT JOIN `'.CATALOG_TABLE.'` ct ON(ct.`id`=ck.`kid`) ';
                    $join  .= 'LEFT JOIN `'.MAIN_TABLE.'` mt ON(mt.`id`=ct.`cid`) ';
                    $where  = 'WHERE (c.`active`=1 AND ct.`active`=1) AND (m.`active`=1 AND mt.`active`=1) AND (ck.`id` NOT IN('.implode(',', $exItems).')) ';
                    $where .= 'AND ((c.`title` LIKE "%'.$stext.'%" OR c.`pcode` LIKE "%'.$stext.'%") OR (ct.`title` LIKE "%'.$stext.'%" OR ct.`pcode` LIKE "%'.$stext.'%")) ';
                    $order  = 'GROUP BY ck.`pid` ORDER BY ck.`id`';
                    $query  = $select.$join.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $row['title'] = 'Комплект: '.unScreenData($row['title']);
                            $row['ctitle'] = unScreenData($row['ctitle']);
                            $row['type'] = 'kit';
                            $json['items'][] = $row;
                        }
                    }

                    echo json_encode(PHPHelper::dataConv($json));
                }
                // добавление товара
                elseif ($option=="add") {
                    if($hasAccess) {
                        $pid = (!empty($_GET['pid']) && intval($_GET['pid']))? intval($_GET['pid']): 0;
                        $type = (!empty($_GET['type']))? trim($_GET['type']): die('Item type required!');

                        // добавление товара
                        if($type=="product") {
                            $arItem = getItemRow(CATALOG_TABLE, '*', 'WHERE `id`='.$pid);
                            if(!empty($arItem)) {
                                $arItem = PHPHelper::getProductItem($arItem, $UrlWL, $images_path, $images_params, true, $arItem["cid"], "catalog", false);
                                $arData = array(
                                    'oid'   => $itemID,
                                    'pid'   => $arItem['id'],
                                    'title' => $arItem['title'],
                                    'price' => $arItem['price'],
                                    'discount' => $arItem['discount'],
                                    'qty'   => 1,
                                    "options" => serialize(screenData($arItem["selectedOptions"]))
                                );
                                if($DB->postToDB($arData, ORDER_PRODUCTS_TABLE)) {                                
                                    ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_ORDER_ADD_PRODUCT, 'Товар "'.$arItem['title'].'" добавлен к заказу', $lang, 'Заказ №'.$itemID, $itemID, 'orders');
                                }
                            }
                        }
                        // добавление комплекта
                        elseif ($type=="kit") {
                            $arItem = array();
                            $select = 'SELECT ck.`id`, c.`id` AS `primary_id`, CONCAT_WS(" + ", c.`title`, GROUP_CONCAT(ct.`title` SEPARATOR " + ")) AS `title`, CONCAT_WS(",", c.`id`, GROUP_CONCAT(ct.`id` SEPARATOR ",")) AS `kitx`, "0" AS `price`, "0" AS `discount` FROM `'.CATALOG_KITS_TABLE.'` ck ';
                            $join   = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id` = ck.`pid`) ';
                            $join  .= 'LEFT JOIN `'.CATALOG_TABLE.'` ct ON(ct.`id` = ck.`kid`) ';
                            $where  = 'WHERE ck.`pid`='.$pid.' ';
                            $order  = 'GROUP BY ck.`pid` LIMIT 1';
                            $query  = $select.$join.$where.$order;
                            $result = mysql_query($query);
                            if(mysql_num_rows($result) > 0) {
                                $arItem = mysql_fetch_assoc($result);
                                $arItem["options"] = array();
                                $qry  = "SELECT c.*, IF(ck.`pid`=c.`id`, 1, 0) AS `primary` FROM `".CATALOG_TABLE."` c ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck ON(ck.`pid` = c.`id`) ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck2 ON(ck2.`kid` = c.`id`) ";
                                $qry .= "WHERE c.`id` IN(".$arItem["kitx"].") ";
                                $qry .= "GROUP BY c.`id` ORDER BY `primary` DESC";
                                $res  = mysql_query($qry);
                                if ($res AND mysql_num_rows($res) > 0) {
                                    $primaryOptions = array();
                                    while ($kit = mysql_fetch_assoc($res)) {
                                        $primary = (bool)$kit["primary"];
                                        $kit = PHPHelper::getProductItem($kit, $UrlWL, $images_path, $images_params, true, $kit["cid"], "catalog", true);
                                        if ($primary) $primaryOptions = PHPHelper::getProductOptions($kit["id"], $kit["selectedOptions"]);
                                        $kit["selectedOptions"] = PHPHelper::getSelectedOptions($primaryOptions);
                                        $kit["options"] = PHPHelper::getProductOptions($kit["id"], $kit["selectedOptions"]);
                                        $kit["price"]  = PHPHelper::recalcItemPriceByOptions($kit["price"], $kit["options"]);
                                        $kit["cprice"] = PHPHelper::recalcItemPriceByOptions($kit["cprice"], $kit["options"]);
                                        $arItem["options"][$kit["id"]] = $kit["selectedOptions"];
                                        if ($primary)
                                            $arItem["price"] += $kit["price"];
                                        else 
                                            $arItem["price"] += $kit["cprice"];
                                    }
                                }
                                $arData = array(
                                    'oid'   => $itemID,
                                    'pid'   => $arItem['id'],
                                    'title' => 'Комплект: '.$arItem['title'],
                                    'price' => $arItem['price'],
                                    'discount' => 0,
                                    'qty'      => 1,
                                    'type'     => 'kit',
                                    "options"  => serialize(screenData($arItem["options"])),
                                    "kitx"     => $arItem["kitx"]
                                );
                                if($DB->postToDB($arData, ORDER_PRODUCTS_TABLE)) {
                                    ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_ORDER_ADD_PRODUCT, 'Комплект "'.$arItem['title'].'" добавлен к заказу', $lang, 'Заказ №'.$itemID, $itemID, 'orders');
                                }
                            }
                        }
                    }
                }
                // удаление товара
                elseif ($option=="delete") {
                    if($hasAccess) {
                        $pid = (!empty($_GET['pid']) && intval($_GET['pid']))? intval($_GET['pid']): 0;
                        $arItem = getItemRow(ORDER_PRODUCTS_TABLE, '*', 'WHERE `id`='.$pid);
                        deleteRecords(ORDER_PRODUCTS_TABLE, 'WHERE `id`='.$pid);
                        $arHistory['comment'] = ($arItem['type']=='kit') ? 'Комплект "'.$arItem['title'].'" удален из заказа' : 'Товар "'.$arItem['title'].'" удален из заказа';
                        ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_ORDER_DELETE_PRODUCT, $arHistory['comment'], $lang, 'Заказ №'.$itemID, $itemID, 'orders');
                    }
                }
                // пересчет количества
                elseif ($option=="recalc") {
                    if ($hasAccess) {
                        $pid = (!empty($_GET['pid']) && intval($_GET['pid']))? intval($_GET['pid']): 0;
                        $qty = (!empty($_GET['qty']) && intval($_GET['qty']))? intval($_GET['qty']): 0;
                        $selectedOptions = (!empty($_GET['options'])) ? $_GET['options'] : array();
                        if ($qty > 0) {
                            $arItem = getItemRow(ORDER_PRODUCTS_TABLE, '*', 'WHERE `id`='.$pid);
                            $oQty = $arItem['qty'];
                            $arItem["options"] = array();
                            if ($arItem["type"]=="product") {
                                $product = getItemRow(CATALOG_TABLE, "*", "WHERE `id`={$arItem["pid"]}");
                                if (!empty($product)) {
                                    $product = PHPHelper::getProductItem($product, $UrlWL, $images_path, $images_params, true, $product["cid"], "catalog", false);
                                    $arItem["selectedOptions"] = isset($selectedOptions[$product["id"]]) ? $selectedOptions[$product["id"]] : array();
                                    $arItem["options"][$product["id"]] = PHPHelper::getProductOptions($product["id"], $arItem["selectedOptions"]);
                                    $arItem["price"] = PHPHelper::recalcItemPriceByOptions($product["price"], $arItem["options"][$product["id"]]);
                                    $arItem["options"][$product["id"]] = $arItem["selectedOptions"];
                                }
                            } elseif ($arItem["type"]=="kit") {
                                $arItem["children"] = array();
                                $arItem["selectedOptions"] = array();
                                $kitx = !empty($arItem["kitx"]) ? explode(",", $arItem["kitx"]) : array(0);
                                $qry  = "SELECT c.*, IF(ck.`pid`=c.`id`, 1, 0) AS `primary` FROM `".CATALOG_TABLE."` c ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck ON(ck.`pid` = c.`id`) ";
                                $qry .= "LEFT JOIN `".CATALOG_KITS_TABLE."` ck2 ON(ck2.`kid` = c.`id`) ";
                                $qry .= "WHERE c.`id` IN(".implode(",", $kitx).") ";
                                $qry .= "GROUP BY c.`id` ORDER BY `primary` DESC";
                                $res  = mysql_query($qry);
                                if ($res AND mysql_num_rows($res) > 0) {
                                    $arItem["price"] = 0;
                                    while ($kit = mysql_fetch_assoc($res)) {
                                        if ($kit["primary"] > 0) {
                                            $arItem["selectedOptions"] = isset($selectedOptions[$kit["id"]]) ? $selectedOptions[$kit["id"]] : array();
                                        }
                                        $kit = PHPHelper::getProductItem($kit, $UrlWL, $images_path, $images_params, true, $kit["cid"], "catalog", true);
                                        $arItem["children"][] = $kit;
                                    }
                                    foreach ($arItem["children"] as $kit) {
                                        $arItem["options"][$kit["id"]] = PHPHelper::getProductOptions($kit["id"], $arItem["selectedOptions"]);
                                        $arItem["price"] += PHPHelper::recalcItemPriceByOptions((($kit["primary"] > 0) ? $kit["price"] : $kit["cprice"]), $arItem["options"][$kit["id"]]);
                                        $arItem["options"][$kit["id"]] = $arItem["selectedOptions"];
                                    }
                                }
                            }
                            $resID = updateRecords(ORDER_PRODUCTS_TABLE, '`qty`='.$qty.', `options`=\''.serialize(screenData($arItem["options"])).'\', `price`="'.$arItem["price"].'"', 'WHERE `id`='.$pid);
                            if ($oQty!=$qty) {
                                $arHistory['comment'] = ($arItem['type']=='kit') ? 'Комплект "'.$arItem['title'].'", кол-во: '.$qty : 'Товар "'.$arItem['title'].'", кол-во: '.$qty;
                                ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_ORDER_EDIT_PRODUCT_COUNT, $arHistory['comment'], $lang, 'Заказ №'.$itemID, $itemID, 'orders');
                            }
                        }  
                    }
                }
                break;
            
            case 'checkUniqueEmail':
                $email = (!empty($_GET['email']))? trim($_GET['email']): '';
                if(!empty($email)) {
                    $select = 'SELECT u.id FROM `'.USERS_TABLE.'` u ';
                    $where  = 'WHERE u.`email`="'.$email.'" OR u.`login`="'.$email.'"';
                    $query  = $select.$where;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        $json['result'] = 'error';
                    } else {
                        $json['result'] = 'success';
                    }
                    echo json_encode($json);
                }
                break;
            // ACTION WITH callback ---------------------------------------------
            case 'getAttributeValue':
                $json = array();
                $aid = !empty($_GET['aid']) ? trim($_GET['aid']) : '';
                $vidx = !empty($_GET['vidx']) ? trim($_GET['vidx']) : '';
                $searchStr = !empty($_GET['searchStr'])? htmlspecialchars(strtolower(addslashes(trim(PHPHelper::dataConv($_GET['searchStr'], 'utf-8', 'windows-1251'))))): '';
                $json['items'] = getComplexRowItems(ATTRIBUTES_VALUES_TABLE.' av', 'av.*', 'WHERE av.`aid`='.$aid.' AND av.`title` like "%'.$searchStr.'%"'.(!empty($vidx) ? ' AND av.`id` NOT IN ('.$vidx.')' : ''), 'av.`order`');
                echo json_encode(PHPHelper::dataConv($json));
                break;
            // ACTION WITH addToWishList ---------------------------------------------          
            case 'WishList':
                $json = array();
                $itemsIDX = array();
                $option = !empty($_GET['option'])? trim($_GET['option']): die('Choose the basket option!');
                $itemID = (!empty($_GET['itemID']) && intval($_GET['itemID']))? intval($_GET['itemID']): FALSE;
                
                switch ($option) {
                    case 'add':
                        if($itemID) {
                            if($Cookie->isSetCookie('wishlist')) {
                                $itemsIDX = unserialize($_COOKIE['wishlist']);
                            }
                            if(!in_array($itemID, $itemsIDX)) {
                                $itemsIDX[] = $itemID;
                            }
                            $Cookie->add('wishlist', serialize($itemsIDX), time()+(3600*(24*7)));
                        }
                        $Cookie->process();
                        break;
                    case 'delete':
                        if($itemID) {
                            if($Cookie->isSetCookie('wishlist')) {
                                $itemsIDX = unserialize($_COOKIE['wishlist']);
                            }
                            $pos = array_search($itemID, $itemsIDX, true);
                            if(is_int($pos)) {
                                unset($itemsIDX[$pos]);
                            }
                            $Cookie->add('wishlist', serialize($itemsIDX), time()+(3600*(24*7)));
                        }
                        $Cookie->process();
                        break;
                }
                
                $json['items'] = $itemsIDX;
                echo json_encode($json);
                
                break;
                
            // ACTION WITH Compare ---------------------------------------------
            case 'Compare':
                $json = array();
                $itemsIDX = array();
                $option = !empty($_GET['option'])? trim($_GET['option']): die('Choose the compare option!');
                $itemID = (!empty($_GET['itemID']) && intval($_GET['itemID']))? intval($_GET['itemID']): FALSE;
                
                switch ($option) {
                    case 'add':
                        if($itemID) {
                            if($Cookie->isSetCookie('compare')) {
                                $itemsIDX = unserialize($_COOKIE['compare']);
                            }
                            if(!in_array($itemID, $itemsIDX)) {
                                $itemsIDX[] = $itemID;
                            }
                            $Cookie->add('compare', serialize($itemsIDX), time()+(3600*(24*7)));
                        }
                        $Cookie->process();
                        break;
                    case 'delete':
                        if($itemID) {
                            if($Cookie->isSetCookie('compare')) {
                                $itemsIDX = unserialize($_COOKIE['compare']);
                            }
                            $pos = array_search($itemID, $itemsIDX, true);
                            if(is_int($pos)) {
                                unset($itemsIDX[$pos]);
                            }
                            $Cookie->add('compare', serialize($itemsIDX), time()+(3600*(24*7)));
                        }
                        $Cookie->process();
                        break;
                }
                
                $json['items'] = $itemsIDX;
                echo json_encode($json);
                
                break;
                
            // ACTION WITH editOrder ---------------------------------------------
            case 'editOrder':
                $json = array();
                $option = !empty($_GET['option'])? trim($_GET['option']): '';
                $orderID = (!empty($_GET['orderID']) && intval($_GET['orderID']))? intval($_GET['orderID']) : FALSE;
                $optionID = (!empty($_GET['optionID'])) ? intval($_GET['optionID']) : array();
                $optionComment = (!empty($_GET['optionComment'])) ? PHPHelper::dataConv($_GET['optionComment'], 'utf-8', 'windows-1251') : '';
                
                require_once('include/classes/mySmarty.php');
                $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                if($UserAccess->getAccessToModule('orders')) {          
                    if($orderID) {
                        if ($option == 'status'){
                            $option_title = getValueFromDB(ORDER_STATUS_TABLE, 'title', 'WHERE `id`='.$optionID, 'title');
                            $arHistoryData['action'] = ActionsLog::ACTION_ORDER_STATUS;
                            $arHistoryData['comment'] = 'Изменено на "'.$option_title.'". '.screenData($optionComment);
                        }
                        else if($option == 'payment') {
                            $option_title = getValueFromDB(PAYMENT_TYPES_TABLE, 'title', 'WHERE `id`='.$optionID, 'title');
                            $arHistoryData['action'] = ActionsLog::ACTION_ORDER_PAYMENT;
                            $arHistoryData['comment'] = 'Изменено на "'.$option_title.'". '.screenData($optionComment);
                        }
                        else if($option == 'shipping') {
                            $option_title = getValueFromDB(SHIPPING_TYPES_TABLE, 'title', 'WHERE `id`='.$optionID, 'title');
                            $arHistoryData['action'] = ActionsLog::ACTION_ORDER_SHIPPING;
                            $arHistoryData['comment'] = 'Изменено на "'.$option_title.'". '.screenData($optionComment);
                        }
                        else if ($option == 'admin_comment') {
                            $option_title = '';
                            $arHistoryData['comment'] = 'Изменен комментарий на "'.$optionComment.'"';
                            $arHistoryData['action'] = ActionsLog::ACTION_EDIT;
                        } else if($option ==  'confirm') {
                            $item = getItemRow(ORDERS_TABLE, '*', 'WHERE `id`='.$orderID);

                            $arSendData = $item;
                            $arSendData['oid']      = $orderID;
                            $arSendData['payment']  = getItemRow(PAYMENT_TYPES_TABLE, '*', 'WHERE `id`='.(int)$item['payment']);
                            $arSendData['shipping'] = getItemRow(SHIPPING_TYPES_TABLE, '*', 'WHERE `id`='.(int)$item['status']);
                            $arSendData['price'] = ($item['price'] + intval(getValueFromDB(SHIPPING_TYPES_TABLE, 'price', 'WHERE `id`='.(int)$item['shipping'], 'price')));
                            $arSendData['children'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', '`oid`='.$orderID );

                            $smarty->assign('arData', $arSendData);
                            $text    = $smarty->fetch('mail/order_confirm.tpl');
                            $subject = $objSettingsInfo->websiteName.': '.ORDER_CONFIRMATION_SUBJECT;

                            if(sendMail($item['email'], $subject, $text, $objSettingsInfo->siteEmail, 'html')){
                                $DB->postToDB(array('confirmed' => 1), ORDERS_TABLE, 'WHERE `id`='.$orderID,  array(), 'update');
                                $optionComment = ($item['confirmed']==1) ? ORDER_CONFIRM_LETTER_RESEND.' на '.$item['email'] : ORDER_CONFIRM_LETTER_SEND.' на '.$item['email'];
                            }
                            $arHistoryData['action'] = ActionsLog::ACTION_ORDER_CONFIRM;
                            $option_title = $optionComment;
                            $arHistoryData['comment'] = $optionComment;
                        }

                        ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save($arHistoryData['action'], $arHistoryData['comment'], $lang, 'Заказ №'.$orderID, $orderID, 'orders',  (isset($text) ? $text : ''));

                        $smarty->assign('arHistoryData', ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getHistory(array('modules' => array('orders'), 'oid'=>$orderID, 'langs'=>array($lang))));
                        $json['history'] = PHPHelper::dataConv($smarty->fetch('common/object_actions_log.tpl'));

                        $DB->postToDB(array($option => ($option != 'admin_comment' ? $optionID : $optionComment)), ORDERS_TABLE, 'WHERE `id`='.$orderID,  array(), 'update');   
                        $json['option_title'] = PHPHelper::dataConv($option_title);
                    }
                }
                echo json_encode($json);
                
                break;
                
            case 'SocialAuth':
                $soc        = (isset($_GET['soc']) && !empty($_GET['soc']))? trim($_GET['soc']): die('Incorrectly query soc parameter!');
                
                // facebook
                if($soc=="fb") {
                    // определение опции диалога авторизации
                    $option = (!empty($_GET['option']))? trim($_GET['option']): die('Dialog option required!');
                    if($option=="auth") {
                        // получаем Access token для получения данных из Facebook
                        $token = (!empty($_GET['token']))? trim($_GET['token']): die('Access token required!');
                        
                        // Массив данных пользователя из соц.сети
                        $arUserInfo = PHPHelper::dataConv($OAuth->getSocUser($token, $soc), 'utf-8', 'windows-1251', true, true);
                        if(!empty($arUserInfo)) {
                            // Проверяем, регистрировался ли пользователь раньше
                            $whereOptions = 'WHERE (`fbid`='.$arUserInfo['fbid'].' AND `network`="'.$arUserInfo['network'].'") OR (`email`="'.$arUserInfo['email'].'" AND `email` IS NOT NULL AND `network` IS NOT NULL) AND `id`>1';
                            $registered = getItemRow(USERS_TABLE, '*', $whereOptions);
                            //saveLogDebugFile(array($whereOptions), 'backup/query.txt');
                            // Если уже регистрировался
                            if(!empty($registered)) {
                                $arUserInfo = $registered;
                                $arUserInfo['auth_type'] = $soc;
                                $arUserInfo['logined'] = 1;
                                setUserToSession((object)$arUserInfo);
                            } 
                            // Если не регистрировался
                            else {
                                $resultID = $DB->postToDB($arUserInfo, USERS_TABLE);
                                if ($resultID && is_int($resultID) && $resultID > 1) {
                                    if(!empty($arUserInfo['image'])) {
                                        $idir       = UPLOAD_DIR.DS.'users/';
                                        $ext        = getFileExt($arUserInfo['image']);
                                        $filename   = basename($arUserInfo['image'], '.'.$ext);
                                        $new_name   = createUniqueFileName($idir, $ext, $filename);
                                        $image      = WideImage::load($arUserInfo['image']);
                                        $iW         = $image->getWidth();
                                        $iH         = $image->getHeight();
                                        $imagesParams = array(
                                            array('', $iW, $iH),
                                            array('small_', 40, 40)
                                        );
                                        foreach ($imagesParams as $param) {
                                            $resized = $image->resize($param[1], $param[2], 'outside');
                                            $cropped = $resized->crop('center', 'middle', $param[1], $param[2])->saveToFile($idir.$param[0].$new_name);
                                        }
                                        if(file_exists($idir.$new_name)) {
                                            updateRecords(USERS_TABLE, '`image`="'.$new_name.'"', 'WHERE `id`='.$resultID);    
                                        }
                                    }
                                    $arUserInfo = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.$resultID);
                                    $arUserInfo['logined'] = 1;
                                    $arUserInfo['auth_type'] = $soc;
                                    setUserToSession((object)$arUserInfo);
                                }
                            }
                            
                            $script =   '<script type="text/javascript">
                                            window.opener.OAuth.submit();
                                            window.close();
                                        </script>';
                            echo $script;
                            
                        } 
                    } elseif ($option=="token") {
                        echo    '<script type="text/javascript">
                                var href = location.href;
                                var token = href.substr(href.indexOf("access_token")+13);
                                var token = token.substr(0, token.indexOf("&"));
                                location.href="'.$OAuth->getFBurl().'&option=auth&token="+token;
                                </script>';
                    }
                } 
                // vkontakte
                elseif ($soc=="vk") {
                    
                    // определение опции диалога авторизации
                    $option = (!empty($_GET['option']))? trim($_GET['option']): die('Dialog option required!');
                    
                    // получение данных с готовым access token
                    if($option=="auth") {
                        
                        // обработка присылаемых параметров
                        $qstr = (!empty($_GET['qstr']))? substr(trim(urldecode($_GET['qstr'])), 1): die('Access token needed!');
                        parse_str($qstr);
                        
                        // начало авторизации
                        if(!empty($access_token) && !empty($user_id)) {
                            $user_id = intval($user_id);
                            // Проверяем, регистрировался ли пользователь раньше
                            $registered = getItemRow(USERS_TABLE, '*', 'WHERE (`vkid`='.$user_id.' AND `network`="'.$soc.'") OR (`email`="'.$arUserInfo['email'].'" AND `email` IS NOT NULL AND `network` IS NOT NULL) AND `id`>1');
                            // Если уже регистрировался
                            if(!empty($registered)) {
                                $arUserInfo = $registered;
                                $arUserInfo['logined'] = 1;
                                $arUserInfo['auth_type'] = $soc;
                                setUserToSession((object)$arUserInfo);
                                
                                $script =   '<script type="text/javascript">
                                            window.opener.OAuth.submit();
                                            window.close();
                                            </script>';
                                echo $script;
                            } 
                            // Если не регистрировался
                            else {
                                // получение обьекта пользователя из соц.сети
                                $arUserInfo = PHPHelper::dataConv($OAuth->getSocUser($access_token, $soc, $user_id), 'utf-8', 'windows-1251', true, true);
                                // авторизация пользователя
                                if(!empty($arUserInfo)) {
                                    // перенос данных в сессию 
                                    $_SESSION['arUserInfo'] = $arUserInfo;
                                    $link    = $OAuth->getVKurl().'&option=register';
                                    $script  = '<script type="text/javascript">';
                                    $script .= 'var Modal = window.opener.Modal; Modal.expand("'.$link.'");';
                                    $script .= 'window.close();';
                                    $script .= '</script>';
                                    echo $script;
                                }
                            }
                        }
                        
                    } 
                    // получение access token
                    elseif ($option=="token") {
                        echo    '<script type="text/javascript">
                                var token = encodeURIComponent(window.location.hash);
                                location.href="'.$OAuth->getVKurl().'&option=auth&qstr="+token;
                                </script>';
                    }
                    // авторизация | регистрация пользователя в БД
                    elseif ($option=="register") {
                        $item = array();
                        $arUserInfo = (isset($_SESSION['arUserInfo']))? $_SESSION['arUserInfo']: array();
                        if (!empty($arUserInfo)) {
                            $arrPageData['result'] = 'default';
                            $item = array_merge($item, $arUserInfo);
                            
                            // post операциии
                            if(!empty($_POST)) {
                                $arPostData = $_POST = PHPHelper::dataConv($_POST, 'utf-8', 'windows-1251', true, true);

                                if (!$Validator->validateEmail($arPostData['email'])) {
                                    $arrPageData['errors']['email'] = 'E-mail введен неверно!';
                                }
                                if (getValueFromDB(USERS_TABLE, 'email', 'WHERE `email`="'.$arPostData['email'].'"', 'eml')) {
                                    $arrPageData['errors']['email'] = 'Данный e-mail уже зарегистрирован в системе!';
                                }

                                if(empty($arrPageData['errors'])) {
                                    $arPostData = array_merge($arUserInfo, $arPostData);

                                    $arPostData['login'] = $arPostData['email'];

                                    $resultID = $DB->postToDB($arPostData, USERS_TABLE);
                                    if($resultID && is_int($resultID) && $resultID > 1) {

                                        // сохранение изображения профиля
                                        if(!empty($arUserInfo['image'])) {
                                            $idir       = UPLOAD_DIR.DS.'users/';
                                            $ext        = getFileExt($arUserInfo['image']);
                                            $filename   = basename($arUserInfo['image'], '.'.$ext);
                                            $new_name   = createUniqueFileName($idir, $ext, $filename);
                                            $image      = WideImage::load($arUserInfo['image']);
                                            $iW         = $image->getWidth();
                                            $iH         = $image->getHeight();
                                            $imagesParams = array(
                                                array('', $iW, $iH),
                                                array('small_', 40, 40)
                                            );
                                            foreach ($imagesParams as $param) {
                                                $resized = $image->resize($param[1], $param[2], 'outside');
                                                $cropped = $resized->crop('center', 'middle', $param[1], $param[2])->saveToFile($idir.$param[0].$new_name);
                                            }
                                            if(file_exists($idir.$new_name)) {
                                                updateRecords(USERS_TABLE, '`image`="'.$new_name.'"', 'WHERE `id`='.$resultID);    
                                            }
                                        }

                                        $arUserInfo = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.$resultID);
                                        $arUserInfo['logined'] = 1;
                                        $arUserInfo['auth_type'] = $soc;
                                        setUserToSession((object)$arUserInfo);
                                        $arrPageData['result'] = 'success';
                                        unset($_SESSION['arUserInfo']);
                                    }
                                } else {
                                    $arrPageData['result'] = 'error';
                                }

                                $item = array_merge($item, $_POST);
                            }

                            require_once('include/classes/mySmarty.php');
                            $smarty     = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);

                            $smarty->assign('item', $item);
                            $smarty->assign('arrPageData', $arrPageData);
                            $smarty->assign('OAuth', $OAuth);

                            $json['output'] = $smarty->fetch('ajax/vk_dialog.tpl');

                            echo json_encode(PHPHelper::dataConv($json));
                        }
                    }
                }
                break;
                
            case 'getCommentForm':
                
                $item = array(
                    'comment' => array()
                );
                
                require_once('include/classes/mySmarty.php');
                $smarty     = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
                
                $smarty->assign('item', $item);
                $smarty->assign('arrPageData', $arrPageData);
                $smarty->assign('UrlWL', $UrlWL);
                $smarty->assign('objUserInfo', $objUserInfo);
                $smarty->assign('objSettingsInfo', $objSettingsInfo);
                $smarty->assign('OAuth', $OAuth);
                
                $json['output'] = $smarty->fetch('ajax/comment_form.tpl');
                echo json_encode(PHPHelper::dataConv($json));
                
                break;          

            // ACTION WITH liveSearch ---------------------------------------------
            case 'liveSearch':
                $json = array();
                $searchStr = !empty($_GET['searchStr'])? htmlspecialchars(strtolower(addslashes(trim(PHPHelper::dataConv($_GET['searchStr'], 'utf-8', 'windows-1251'))))): '';

                switch ($site_zone) {
                    
                    // admin zone search
                    case 'admin':
                        $type = (!empty($_GET['type']) && array_key_exists($_GET['type'], $PHPHelper->SELECTIONS)) ? $_GET['type'] : '';
                        $module = !empty($_GET['module'])? trim(PHPHelper::dataConv($_GET['module'], 'utf-8', 'windows-1251')): die('Select module param');
                        switch ($module) {
                            // catalog search
                            case 'catalog':
                                $json['items'] = array();
                                $cid = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): 0;
                                
                                if($type != 'kits') {
                                    $select = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                                    $join = 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                                    if($type) $where  = 'WHERE NOT EXISTS (SELECT * FROM '.PRODUCT_SELECTIONS_TABLE.' WHERE `pid`=c.`id` AND `type`="'.$type.'") AND (c.`title` LIKE "%'.$searchStr.'%" OR c.`pcode` LIKE "%'.$searchStr.'%") ';
                                    else $where = 'WHERE (c.`title` LIKE "%'.$searchStr.'%" OR c.`pcode` LIKE "%'.$searchStr.'%") '.((!empty($cid))? 'AND c.`cid`='.$cid.' ': ' ');
                                    $order = 'GROUP BY c.`id` ORDER BY c.`order`';
                                    $query = $select.$join.$where.$order;
                                    $result = mysql_query($query);
                                    if(mysql_num_rows($result) > 0) {
                                        while ($row = mysql_fetch_assoc($result)) {
                                            $row['title'] = unScreenData($row['title']);
                                            $json['items'][] = $row;
                                        }
                                    }
                                } else {
                                    $select = 'SELECT c.*, c2.title as kit_title, c2.id as kit_id, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                                    $join = 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) LEFT JOIN '.CATALOG_KITS_TABLE.' ck ON c.id=ck.pid LEFT JOIN '.CATALOG_TABLE.' c2 ON c2.id=ck.kid ';
                                    $where  = 'WHERE NOT EXISTS (SELECT * FROM '.PRODUCT_SELECTIONS_TABLE.' WHERE `pid`=c.`id` AND `type`="'.$type.'") AND (c.`title` LIKE "%'.$searchStr.'%" OR c.`pcode` LIKE "%'.$searchStr.'%") ';
                                    $order = 'GROUP BY c.`id` ORDER BY c.`order`';
                                    $query = $select.$join.$where.$order;
                                    $result = mysql_query($query);
                                    if(mysql_num_rows($result) > 0) {
                                        while ($row = mysql_fetch_assoc($result)) {
                                            if($row['kit_id']) {
                                                $row['id'] = $row['id'].'+'.$row['kit_id'];
                                                $row['title'] = unScreenData($row['title'].'+'.$row['kit_title']);
                                                $json['items'][] = $row;
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                        break;
                    // site zone search
                    case 'site':
                        $json = array();
                        $items = array();
                        $searchtext  = !empty($_GET['stext'])? strtolower(trim(PHPHelper::dataConv($_GET['stext'], 'utf-8', 'windows-1251'))): '';
                        $searchwhere = !empty($_GET['swhere'])? trim(PHPHelper::dataConv($_GET['swhere'], 'utf-8', 'windows-1251')): false;
                        
                        require_once('include/classes/mySmarty.php');
                        $smarty     = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
                
                        switch ($searchwhere) {
                            // catalog search
                            case 'catalog':
                                $files_url  = UPLOAD_URL_DIR.'catalog/';
                                $files_path = prepareDirPath($files_url);
								$arrFields = array('pcode', 'title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title');
								
								$arrText = explode(' ', $searchtext);
								$serachByValue =  $serachByCTitle = $serachByTitle = '';
								foreach($arrText as $text) {
									$serachByValue .= ($serachByValue ? ' AND ' : ''). " (LOWER(a.`value`) like '%".$text."%' OR LOWER(a.`value`) like '".$text."%') ";
									$serachByCTitle .= ($serachByCTitle ? ' AND ' : ''). " (LOWER(ct.`title`) like '%".$text."%' OR LOWER(ct.`title`) like '".$text."%') ";
									$serachByTitle .= ($serachByTitle ? ' AND ' : ''). " (LOWER(bt.`title`) like '%".$text."%' OR LOWER(bt.`title`) like '".$text."%')  ";
								}
								
								$query = "SELECT t.*, cf.`filename` AS `image`, IF (ps.`id` IS NULL, 0, 2) AS `hit`, IF (t.`isdiscount`=0 OR t.`discount`=0, 0, 1) AS `dis`, (SELECT COUNT(*) FROM `comments` WHERE `module`='catalog' AND `pid`=t.`id`) AS `com` FROM ((SELECT ct.* FROM " . CATALOG_TABLE . " ct LEFT JOIN ". MAIN_TABLE ." mt ON(ct.`cid` = mt.`id`) ".
										" WHERE ( LOWER(".getSqlStrCondition(getSqlListFilter($arrFields, $searchtext, "LIKE", 'ct.'), 'OR').") OR (".$serachByCTitle.") ) AND ct.`active` = 1)".
										" UNION (SELECT ca.* FROM " . CATALOG_TABLE . " ca LEFT JOIN ".PRODUCT_ATTRIBUTE_TABLE." a ON ca.`id`=a.`pid` ".
										" WHERE (".$serachByValue.") AND ca.`active` = 1)".
										" UNION (SELECT ca.* FROM " . CATALOG_TABLE . " ca LEFT JOIN ".BRANDS_TABLE." bt ON bt.`id`=ca.`bid` ".
										" WHERE (".$serachByTitle." ) AND bt.`active` = 1)".
										" ) t LEFT JOIN ". MAIN_TABLE ." m ON(t.`cid` = m.`id`) LEFT JOIN `".CATALOGFILES_TABLE."` cf ON(cf.`pid`=t.`id`) AND cf.`isdefault`=1 LEFT JOIN `".PRODUCT_SELECTIONS_TABLE."` ps ON(ps.`pid` = t.`id`) AND ps.`type`='hit' WHERE t.`active`=1 AND m.`active`=1 ORDER BY (`hit` + `dis` + `com` + t.`viewed`), t.`price` DESC, t.`order`";
								$result = mysql_query($query);     
                                if($result && mysql_num_rows($result)){
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['arCategory'] = $UrlWL->getCategoryById($row['cid']);
                                        $row['title'] = unScreenData($row['title']);
                                        $row['small_image'] = (!empty($row['image']) && file_exists($files_path.$row['id'].DS.$row['image']))? $files_url.$row['id'].'/small_'.$row['image']: $files_url.'small_noimage.jpg';
                                        $row['new_price'] = ($row['isdiscount'] && $row['discount']) ? $row['price'] - ($row['price']*$row['discount']/100) : 0;
                                        $items[] = $row;
                                    }
                                }
                                
                                $smarty->assign('items',        $items);
                                $smarty->assign('UrlWL',        $UrlWL);
                                $smarty->assign('arrModules',   $arrModules);
                                $smarty->assign('searchtext',  $searchtext);
                                $json['output'] = $smarty->fetch('ajax/live_search.tpl');

                                break;
                                
                            // all site search
                            case 'site':
                                $json['items'] = array();
                                $arrFields = array('title', 'text', 'descr', 'meta_descr', 'meta_key', 'seo_title');
                                $query = "SELECT `title` FROM " . MAIN_TABLE . " WHERE LOWER(".getSqlStrCondition(getSqlListFilter($arrFields, $searchtext, 'LIKE'), 'OR').") AND `pid`>0 AND `active` = 1 ORDER BY `order` ";
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)){
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['title'] = unScreenData($row['title']);
                                        $json['items'][] = $row;
                                    }
                                }

                                $arrSearchModules = array(
                                    array('module'=>'news',    'table'=>NEWS_TABLE,    'title'=>NEWS,      'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title')), 
                                    array('module'=>'gallery', 'table'=>GALLERY_TABLE, 'title'=>GALLERIES, 'arFields'=>array('title', 'descr', 'meta_descr', 'meta_key', 'seo_title')),
                                    array('module'=>'video',   'table'=>VIDEOS_TABLE,  'title'=>VIDEOS,    'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title'))
                                );

                                $select = 'title';
                                $order = " `created` DESC, `cid`, `order`";

                                foreach($arrSearchModules as $module){
                                    $where = " WHERE (".getSqlStrCondition(getSqlListFilter($module['arFields'], $searchtext, 'LIKE'), 'OR').") AND `active` = 1";
                                    $result = getComplexRowItems($module['table'], $select, $where, $order);
                                    if(!empty($result)){
                                        foreach($result as $row) {
                                            $row['title'] = unScreenData($row['title']);
                                            $json['items'][] = $row;
                                        }
                                    }
                                }
                                break;
                        }
                        break;
                }
                
                echo json_encode(PHPHelper::dataConv($json));
                
                break;
                
            // ACTION WITH Basket ---------------------------------------------
            case 'basket':
                $json = array();
                require_once('include/classes/Basket.php');         
                $Basket     = new Basket();
                $Basket->setupKitParams(PRODUCT_KIT_PREFIX);
                
                require_once('include/classes/mySmarty.php');
                $smarty     = new mySmarty(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CACHING);
                $smarty->assign('Basket',       $Basket);
                $smarty->assign('UrlWL',        $UrlWL);
                $smarty->assign('HTMLHelper',   $HTMLHelper);
                $smarty->assign('arrModules',   $arrModules);

                $arrPageData['wishlist'] = array();
                if($Cookie->isSetCookie('wishlist')) {
                    $arrPageData['wishlist'] = unserialize($_COOKIE['wishlist']);
                }
                $smarty->assign('arrPageData',  $arrPageData);            

                $option = !empty($_GET['option'])? trim($_GET['option']): die('Choose the basket option!');
                $itemID = (!empty($_GET['itemID']))? trim($_GET['itemID']): false;
                $qty    = (!empty($_GET['qty']) && intval($_GET['qty']))? intval($_GET['qty']): 1;
                $setNewQty = (isset($_GET['setNewQty'])) ? $_GET['setNewQty'] : 0;
                $list = (isset($_GET['list'])) ? (bool)$_GET['list'] : false;
                $smarty->assign('list',  $list);
                        
                switch ($option){
                    // Add To Basket
                    case 'add':
                        if($itemID) {
                            $Basket->add($itemID, $qty, $setNewQty);
                        }
                        break;
                    // Remove From Basket
                    case 'remove':
                        if($itemID) {
                            $Basket->remove($itemID, $qty);
                        }
                        $json['output'] = array(
                            'isEmpty'  => $Basket->isEmptyBasket()
                        );
                        break;
                    // Update Basket
                    case 'update':
                        $json['output'] = array(
                            'minicart'  => $smarty->fetch('ajax/minicart.tpl'),
                            'basket'    => !$Basket->isEmptyBasket() ? $smarty->fetch('ajax/basket.tpl') : ''
                        );
                        break;
                    // Clear Basket
                    case 'clear':
                        $Basket->dropBasket();
                        break;
                }
                
                echo json_encode(PHPHelper::dataConv($json));
                
                break;
                        
            // ACTION WITH getKitItems ---------------------------------------------
            case 'getKitItems':
                $json = array();
                $cid = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): 0;
                $exItems = !empty($_GET['exclude'])? $_GET['exclude']: array(0);
                if($cid) {
                    $json['items'] = array();
                    $select = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c ';
                    $where = 'WHERE c.`cprice`>0 AND c.`cid`='.$cid.' AND c.`id` NOT IN('.implode(',', $exItems).') ';
                    $order = 'ORDER BY c.`order`';
                    $query = $select.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $json['items'][$row['id']] = $row['title'];
                        }
                    }
                }
                echo json_encode(PHPHelper::dataConv($json));
                break;
                        
            // ACTION WITH getRelatedItems ---------------------------------------------
            case 'getRelatedItems':
                $json = array();
                $module = !empty($_GET['module'])? trim(PHPHelper::dataConv($_GET['module'], 'utf-8', 'windows-1251')): die('Select module param');
                $exItems = !empty($_GET['exclude'])? PHPHelper::dataConv($_GET['exclude'], 'utf-8', 'windows-1251'): array();
                $searchStr = !empty($_GET['searchStr'])? trim(PHPHelper::dataConv($_GET['searchStr'], 'utf-8', 'windows-1251')): '';
                $cid = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): false;
                
                switch ($module) {
                    case 'catalog':
                        $json['items'] = array();
                        $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                        $query .= 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                        $query .= 'WHERE c.`title` LIKE "%'.$searchStr.'%" '.((!empty($exItems))? 'AND c.`id` NOT IN('.implode(',', $exItems).') ': ' ').(($cid)? 'AND c.`cid`='.$cid.' ': ' ');
                        $query .= 'GROUP BY c.`id` ORDER BY c.`order`';
                        $result = mysql_query($query);
                        if(mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $json['items'][] = $row;
                            }
                        }
                        break;
                    case "stocks":
                        $json['items'] = array();
                        $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                        $query .= 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                        $query .= 'WHERE c.`title` LIKE "%'.$searchStr.'%" '.((!empty($exItems))? 'AND c.`id` NOT IN('.implode(',', $exItems).') ': ' ').(($cid)? 'AND c.`cid`='.$cid.' ': ' ');
                        $query .= 'GROUP BY c.`id` ORDER BY c.`order`';
                        $result = mysql_query($query);
                        if(mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $json['items'][] = $row;
                            }
                        }
                        break;
                }
                
                echo json_encode(PHPHelper::dataConv($json));
                
                break;
            
            // ACTION WITH sortable lists ---------------------------------------------
            case 'updateSortableList':
                $json = array();
                $item = array();
                $type = !empty($_GET['listType'])? trim($_GET['listType']): FALSE;
                
                if($type) {
                    
                    $item['type'] = $type;
                    
                    switch ($type) {
                        case 'attributes':
                            $gid = !empty($_GET['gid'])? intval($_GET['gid']): 0;
                            if($gid && $gid>0) {
                                $item['attributes'] = array();
                                $query = 'SELECT DISTINCT a.*, ag.`title` AS `gtitle` FROM `'.ATTRIBUTES_TABLE.'` a LEFT JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON(ag.`id` = a.`gid`) ';
                                $where = 'WHERE a.`gid`='.$gid.' ';
                                $order = 'ORDER BY a.`order`';
                                $result = mysql_query($query.$where.$order);
                                if(mysql_num_rows($result) > 0) {
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $item['attributes'][] = $row;
                                    }
                                }
                            }
                            break;
                            
                        case 'filters':
                            $fid = !empty($_GET['fid'])? intval($_GET['fid']): 0;
                            $filterType = !empty($_GET['filterType'])? trim($_GET['filterType']): FALSE;
                            if(($fid && $fid>0) && $filterType) {
                                $item['filters'] = array();
                                $query = 'SELECT f.*, CONCAT("{filter_", f.`id`, "}") AS `alias` FROM `'.FILTERS_TABLE.'` f ';
                                $where = 'WHERE f.`id`='.$fid;
                                $result = mysql_query($query.$where);
                                if(mysql_num_rows($result) > 0) {
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['type'] = $filterType;
                                        $item['filters'][] = $row;
                                    }
                                }
                            }
                            break;
                    }
                    
                    require_once('include/classes/mySmarty.php');
                    $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                    $smarty->assign('item', $item);
                    $json['output'] = PHPHelper::dataConv($smarty->fetch('ajax/sortable_list.tpl'));
                    
                    echo json_encode($json);
                }
                break;
            
            // ACTION WITH addAttributeRow ---------------------------------------------
            case 'addAttributeRow':
                $json = array();
                $item = array();
                $itemID = !empty($_GET['itemID'])? intval($_GET['itemID']): 0;
                $groupID = !empty($_GET['groupID'])? intval($_GET['groupID']): 0;
                $arGroups = !empty($_GET['arGroups'])? $_GET['arGroups']: array();
                if($groupID > 0) {
                    $item['attrGroup'] = getItemRow(ATTRIBUTE_GROUPS_TABLE, '*', 'WHERE `active`=1 AND `id`='.$groupID);
                    if(!empty($item['attrGroup'])) {
                        $item['attrGroup']['attributes'] = getComplexRowItems(ATTRIBUTES_TABLE, '*', 'WHERE `gid`='.$groupID, '`order`');
                        foreach ($item['attrGroup']['attributes']  as $k => $v){
                            $item['attrGroup']['attributes'][$k]['values'] = getComplexRowItems(ATTRIBUTES_VALUES_TABLE.' av', 'av.*', 'WHERE av.`aid`='.$v['id'], 'av.`order`');
                        }
                        require_once('include/classes/mySmarty.php');
                        $smarty = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                        $smarty->assign('item', $item);
                        $json['tpl'] = PHPHelper::dataConv($smarty->fetch('ajax/attributes_form.tpl'));
                    }

                    $json['select'] = array();
                    $where = 'WHERE ag.`active`=1 AND ag.`id`!='.$groupID;
                    if(!empty($arGroups)) {
                        $where .= ' AND ag.`id` NOT IN('.implode(',', $arGroups).')';
                    }
                    $arAcceptedGroups = getComplexRowItems(ATTRIBUTE_GROUPS_TABLE.' ag', 'ag.*', $where, 'ag.`order`');
                    if(!empty($arAcceptedGroups)) {
                        foreach ($arAcceptedGroups as $group) {
                            $json['select'][$group['id']] = PHPHelper::dataConv($group['title'].($group['descr'] ? '( '.$group['descr'].')' : ''));
                        }
                    }
                }
                echo json_encode($json);
                break;
            
            // ACTION WITH getFilterFormLayout ---------------------------------------------
            case 'getFiltersFormLayout':
                $json = array();
                $tid = !empty($_GET['tid'])? intval($_GET['tid']): 0;
                if($tid && $tid>0) {
                    require_once('include/classes/mySmarty.php');
                    $smarty     = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CACHING);
                    $arType = getItemRow(FILTER_TYPES_TABLE, '*', 'WHERE `id`='.$tid);
                    $smarty->assign('type', $arType['id']);
                    $json['output'] = PHPHelper::dataConv($smarty->fetch('ajax/filters_form.tpl'));
                }
                echo json_encode($json);
                break;
            // ACTION WITH generateSeoPath ---------------------------------------------
            case 'generateSeoPath':
                $path   = !empty($_GET['path'])   ? addslashes(urldecode(trim($_GET['path'])))   : '';
                $prefix = !empty($_GET['prefix']) ? addslashes(trim($_GET['prefix'])) : '';                
                if($path) {
                    $path = preg_replace('/[^\s\w\d]/u', '', $path);
                    $path = preg_replace('/\s+/u', ' ', $path);
                    $path = trim($UrlWL->strToUrl(str_conv($path, "UTF-8", "WINDOWS-1251"), $prefix));
                }
//                saveLogDebugFile(array('$path'=>$path, '$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array( 'seo_path'=>$path ) );
                break;

            // ACTION WITH generatePassword --------------------------------------------------------------------
            case 'generatePassword': // Password
                $length  = !empty($_GET['length']) ? intval($_GET['length']): 12;
                echo json_encode( array( "code"=>randString($length)) );
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                break;

            // ACTION WITH incrementBannerClick --------------------------------------------------------------------
            case 'incrementBannerClick': // Currency
                $categoryID = !empty($_GET['categoryID']) ? intval($_GET['categoryID'])               : 0;
                $bannerID   = !empty($_GET['bannerID'])   ? intval($_GET['bannerID'])                 : 0;
                $bannerURL  = !empty($_GET['bannerURL'])  ? urldecode(addslashes($_GET['bannerURL'])) : '/';
                require_once('include/classes/Banners.php');
                Banners::incrementClick($bannerID);
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                Redirect($bannerURL);
                break;

            // ACTION WITH ajaxChangeCurrency --------------------------------------------------------------------
            case 'ajaxChangeCurrency': // Currency
                $cid = !empty($_GET['cid']) ? intval($_GET['cid']) : 0;
                require_once('include/classes/Currencies.php');        // 10.Include Currencies class
                $Currencies = new Currencies();  //Initialize Currencies class
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array( "result"=>(int)$Currencies->setCurrent($cid) ) );
                break;

            // ACTION WITH ajaxCapchaCheck --------------------------------------------------------------------
            case 'ajaxCapchaCheck': // Captcha
                $captchaSID  = !empty($_GET['sid'])  ? trim(addslashes($_GET['sid']))  : '';
                $captchaCode = !empty($_GET['code']) ? trim(addslashes($_GET['code'])) : '';
                require_once('include/classes/Captcha.php');        // 8. Include Captcha class
                $Captcha = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);  //Initialize Captcha class
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array("result"=>(int)$Captcha->CheckCode($captchaCode, $captchaSID, true, false)) );
                break;

            // ACTION WITH ajaxCapchaUpdate --------------------------------------------------------------------
            case 'ajaxCapchaUpdate': // Captcha
                require_once('include/classes/Captcha.php');        // 8. Include Captcha class
                $Captcha    = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);  //Initialize Captcha class
                $Captcha->SetCode();
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode(array("sid"=>str_conv($Captcha->GetSID()),"code"=>str_conv($Captcha->GetGeneratedCode()),"length"=>$Captcha->GetCodeLength()));
                break;

            // ACTION WITH ajaxUserSessionTimeUpdate -----------------------------------------------------------
            case 'ajaxUserSessionTimeUpdate':
                if($objUserInfo->logined) $_SESSION[(WLCMS_ZONE=='BACKEND' ? 'a' : 's').'user_timeout'] = time();
                echo '1';// Required to trigger onComplete function on Mac OSX
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                break;

            // ACTION WITH ajaxFilesUpload ---------------------------------------------
            case 'ajaxUserLogOut':
                $success = 0;
                if($objUserInfo->logined)
                    $success = unsetUserFromSession();
                echo json_encode($success);
                break;

            // ACTION WITH ajaxUserFilesUpload ---------------------------------
            case 'ajaxUserFilesUpload':
                $userID        = (isset($_GET['UID']) && intval($_GET['UID'])) ? intval($_GET['UID']) : 0;
                $filePrefix    = !empty($_GET['file_prefix']) ? addslashes($_GET['file_prefix']) : "u{$userID}_";
                $targetFolder  = !empty($_POST['folder']) ? prepareDirPath(UPLOAD_DIR.DS.$_POST['folder']) : '';
                //saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_COOKIE'=>$_COOKIE, '$real__FILE__'=>realpath(__FILE__)), 'log_ajaxUserFilesUpload.txt');
                
                if(isset($_GET['uploadifyData']) && isset($_POST['Upload']) && !empty($_FILES) && $userID){
                    $files_params  = !empty($_GET['files_params']) ? unserialize(base64_decode(urldecode($_GET['files_params']))) : array();
                    $ext           = getFileExt($_FILES['Filedata']['name']);
                    $targetFName   = $filePrefix.strtolower(setFilePathFormat(str_conv($_FILES['Filedata']['name'], "UTF-8", "WINDOWS-1251")));
                    if($targetFolder && in_array($ext, explode(';', str_replace('*.', '', $_POST['fileext'])))){
                        $fileExists = file_exists($targetFolder.$targetFName);
                        $moved      = move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFolder.$targetFName);
                        while($files_params && (list(, list($partiname, $piw, $pih)) = each($files_params))){
                            createThumb($targetFolder.$targetFName, $piw, $pih, $targetFolder.$partiname.$targetFName);
                        }
                        if($moved && !$fileExists) $DB->postToDB(array('uid'=>$userID, 'filename'=>$targetFName), USERFILES_TABLE);
                    }  echo '1';// Required to trigger onComplete function on Mac OSX

                } else if(isset($_GET['uploadifyCheck'])){
                    $fileArray = array();
                    if($targetFolder){
                        foreach ($_POST as $key => $value) {
                            if ($key == 'folder') continue;
                            $value = strtolower(setFilePathFormat(str_conv($value, "UTF-8", "WINDOWS-1251")));
                            if (file_exists($targetFolder.$filePrefix.$value)) $fileArray[$key] = $value;
                        }
                    } echo json_encode($fileArray);
                } else echo '0';
                break;

            // ACTION WITH ajaxCatalogFilesUpload ---------------------------------
            case 'ajaxCatalogFilesUpload':
                $itemID        = (isset($_GET['PID']) && intval($_GET['PID'])) ? intval($_GET['PID']) : 0;
                $filePrefix    = !empty($_GET['file_prefix']) ? addslashes($_GET['file_prefix']) : "p{$itemID}_";
                $targetFolder  = !empty($_POST['folder']) ? prepareDirPath(UPLOAD_DIR.DS.$_POST['folder']).DS.$itemID.DS : '';
                //saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_COOKIE'=>$_COOKIE, '$real__FILE__'=>realpath(__FILE__)), 'log_ajaxUserFilesUpload.txt');
                
                if(isset($_GET['uploadifyData']) && isset($_POST['Upload']) && !empty($_FILES) && $itemID){
                    $files_params  = !empty($_GET['files_params']) ? unserialize(base64_decode(urldecode($_GET['files_params']))) : array();
                    $ext           = getFileExt($_FILES['Filedata']['name']);
                    $targetFName   = $filePrefix.strtolower(setFilePathFormat(str_conv($_FILES['Filedata']['name'], "UTF-8", "WINDOWS-1251")));
                    if($targetFolder && in_array($ext, explode(';', str_replace('*.', '', $_POST['fileext'])))){
                        $fileExists = file_exists($targetFolder.$targetFName);
                        $moved      = move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFolder.$targetFName);
                        while($files_params && (list(, list($partiname, $piw, $pih)) = each($files_params))){
                            createThumb($targetFolder.$targetFName, $piw, $pih, $targetFolder.$partiname.$targetFName);
                        }
                        if($moved && !$fileExists) {
                            $DB->postToDB(array('pid'=>$itemID, 'filename'=>$targetFName), CATALOGFILES_TABLE, '', array(), 'insert', true);
                        }
                        $default = getItemRow(CATALOGFILES_TABLE, 'id', 'WHERE `isdefault`=1 AND `pid`='.$itemID);
                        if(empty($default)) updateRecords(CATALOGFILES_TABLE, '`isdefault`=1', 'WHERE `pid`='.$itemID. ' ORDER BY `fileorder` LIMIT 1');
                    }  echo '1';// Required to trigger onComplete function on Mac OSX

                } else if(isset($_GET['uploadifyCheck'])){
                    $fileArray = array();
                    if($targetFolder){
                        foreach ($_POST as $key => $value) {
                            if ($key == 'folder') continue;
                            $value = strtolower(setFilePathFormat(str_conv($value, "UTF-8", "WINDOWS-1251")));
                            if (file_exists($targetFolder.$filePrefix.$value)) $fileArray[$key] = $value;
                        }
                    } echo json_encode($fileArray);
                } else echo '0';
                break;

            // ACTION WITH fileDownload FROM private AREA ----------------------
            case 'dbFileBackUpDownload':
                // Cookie dbFileBackUpDownload names
                define('DCOOKIE',      'sxd');

                //variables from GET Array
                $uid = (isset($_GET['uid']) && intval($_GET['uid']) > 0) ? intval($_GET['uid']) : false;
                $file = (isset($_GET['file']) && strlen($_GET['file'])>0) ? trim(urldecode($_GET['file'])) : false;

                if($uid==$objUserInfo->id)
                   $uid=true;
                elseif($Cookie->getCookie(DCOOKIE)!=''){
                    $cUser = explode(":", base64_decode($dCookie->getCookie(DCOOKIE)));
                    $dbUser = $DB->getDBSettings();
                    if ($DB->getDBUser()==$cUser[1] && $DB->getDBPassword()==$cUser[2]) {
                        $uid=true;
                    }
                }

                if( $uid==true && $file && strpos($file, "\0") === FALSE/*Nullbyte hack fix*/){
                    // Make sure program execution doesn't time out
                    // Set maximum script execution time in seconds (0 means no limit)
                    @set_time_limit(0);

                    // Make sure that header not sent by error
                    // Sets which PHP errors are reported
                    @error_reporting(0);

                    // Allow direct file download (hotlinking)?  Empty - allow hotlinking
                    // If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
                    $allowed_referrer = $_SERVER['SERVER_NAME'];

                    // Allowed extensions list in format 'extension' => 'mime type'
                    // If myme type is set to empty string then script will try to detect mime type
                    // itself, which would only work if you have Mimetype or Fileinfo extensions
                    // installed on server.
                    $allowed_ext = array(
                        'sql'   => 'text/x-sql', 
                        'bz'    => 'application/x-bzip', 
                        'bz2'   => 'application/x-bzip2', 
                        'boz'   => 'application/x-bzip2', 
                        'gz'    => 'application/x-gzip', 
                        'tgz'   => 'application/x-gzip', 
                        'tar'   => 'application/x-tar', 
                        'tgz'   => 'application/x-tar', 
                        'zip'   => 'application/zip'
                    );
                    
                    // Download base folder, i.e. folder where you keep all user dirs with files for download.
                    $baseFolder = prepareDirPath('backup/');

                    // log file name
                    $log_file = $baseFolder.'downloads.log';

                    // If hotlinking not allowed then make hackers think there are some server problems
                    if ( !empty($allowed_referrer) &&
                         (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper($allowed_referrer)) === false)
                    ) die("Internal server error. Please contact system administrator.");

                    // Get real file name.
                    // Remove any path info to avoid hacking by adding relative path, etc.
                    $fname = basename($file);

                    // file extension
                    $fext = getFileExt($fname);

                    // check if allowed extension
                    if (!array_key_exists($fext, $allowed_ext)) {
                      die("Not allowed file type.");
                    }


                    $file = $baseFolder.$fname;

                    // if don't exist and isn't file and  can't read them - die
                    if (!file_exists($file) && !is_file($file) && !is_readable($file)) {
                      header ("HTTP/1.0 404 Not Found");
                      exit();
                    }

                    // if Time file last modified mor then SESSION_INACTIVE - die
                    if ((time() - filectime($file)) > SESSION_INACTIVE){
                      $Cookie->del(DCOOKIE);
                      $Cookie->process();
                      header ("HTTP/1.0 404 Not Found");
                      die("Not allowed to download this file more.");
                    }


                    // file size in bytes
                    $fsize = filesize($file);

                    // get mime type
                    if (empty($allowed_ext[$fext])) {
                        $mtype = '';
                        // mime type is not set, get from server settings
                        if (function_exists('mime_content_type')) {
                            $mtype = mime_content_type($file);
                        } else if (function_exists('finfo_file')) {
                            $finfo = finfo_open(FILEINFO_MIME); // return mime type
                            $mtype = finfo_file($finfo, $file);
                            finfo_close($finfo);
                        }
                        if ($mtype == '') {
                            $mtype = "application/force-download";
                        }
                    } else  $mtype = $allowed_ext[$fext]; // get mime type defined by admin

                    // Browser will try to save file with this filename, regardless original filename.
                    // You can override it if needed.

                    // remove some bad chars
                    $asfname = str_replace(array('"',"'",'\\','/'), '', $fname);
                    if ($asfname === '') $asfname = 'NoName'.'.'.$fext;

                    // set headers
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Type: $mtype");
                    header("Content-Disposition: attachment; filename=\"$asfname\"");
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: " . $fsize);

                    // download
                    // @readfile($file);
                    $file = @fopen($file, "rb");
                    if($file) {
                        while(!feof($file)) {
                            print(fread($file, 1024 * 8));
                            flush();
                            if( connection_status()!=0 ) {
                                @fclose($file);
                                die();
                            }
                        } @fclose($file);
                    }

                    // log downloads
                    if (!empty($log_file)){
                        $f = @fopen($log_file, 'a+');
                        if ($f) {
                          @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$folder."  ".$fname."\n");
                          @fclose($f);
                          @chmod($log_file, 0775);
                        }
                    }

                } else { die(); }
                break;

            default:
                exit();
                break;
        }
    }
}

function mySort($a, $b) {  
    if($a['order'] != $b['order']) 
        return ($a['order'] < $b['order']) ? -1 : 1;  
    return 0;
}