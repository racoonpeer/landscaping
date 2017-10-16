<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$sort         = intval($UrlWL->getParam(UrlWL::SORT_KEY_NAME, 0));
$pages        = trim(addslashes($UrlWL->getParam(UrlWL::PAGES_KEY_NAME, '')));
$itemID       = $UrlWL->getItemId();
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$itemsIDX     = array();
$arrCategories= array();
$showSubItems = true;
$showEmptyAttr= true; // Show or hide empty product attributes
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// Manipulation with Sort
if ($sort) {
    $sort = PHPHelper::getCorrectCatalogSort($sort);
    $_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME] = &$sort;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME]) ) {
    $sort = &$_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME];
} elseif (isset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME])) {
    unset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME]);
}
// Manipulation with Page Number
if ($page > 1) {                                                         
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module]['page']) ) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
}
// Manipulation with Show Pages All Session Var
if ($pages) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['pagesall']      = &$pages;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages ? UrlWL::PAGES_KEY_NAME.'='.UrlWL::PAGES_ALL_VAL : '').($sort ? UrlWL::SORT_KEY_NAME.'='.$sort : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 12;
$arrPageData['selectedFilters'] = $UrlWL->getFilters()->getSelected();
$arrPageData['filters']         = array();
$images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="'.$module.'"'));
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
//$arrPageData['headCss'][]     = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
//$arrPageData['headScripts'][] = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
//$arrPageData['headScripts'][] = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
//$arrPageData['headScripts'][] = '<script src="/js/libs/highslide/highslide.config.js" type="text/javascript"></script>';

// Item Detailed View
if($itemID) {
    $item = getSimpleItemRow($itemID, CATALOG_TABLE);
    if(!empty($item)) {
        // Set vars 
        $item = PHPHelper::getProductItem($item, $UrlWL, $arrPageData['files_url'], $images_params, false);
        // add itemID to watched
        if($Cookie->isSetCookie('watched')) {
            $itemsIDX = unserialize($Cookie->getCookie('watched'));
        }
        if(!in_array($itemID, $itemsIDX)) {
            $itemsIDX[] = $itemID;
        }
        $Cookie->add('watched', serialize($itemsIDX), time()+(3600*(24*7)));
        
        // related items
        $item['alsoView'] = $item['alsoBuy'] = $item['additions'] = array();
        $select = 'SELECT c.*, m.`title` AS `ctitle`, cr.`type` FROM `'.CATALOG_RELATED_TABLE.'` cr ';
        $join = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id` = cr.`rid`) LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
        $where = 'WHERE cr.`pid`='.$itemID.' ';
        $order = 'ORDER BY cr.`id`';
        $query = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {                  
                if($row['type'] == 0) {
                    $item['alsoView'][] = PHPHelper::getProductItem($row, $UrlWL, $arrPageData['files_url'], $images_params, true, $catid);
                } elseif ($row['type'] == 1) {
                    $item['alsoBuy'][] = PHPHelper::getProductItem($row, $UrlWL, $arrPageData['files_url'], $images_params, true, $catid);
                } elseif ($row['type'] == 2) {
                    $item['additions'][] = PHPHelper::getProductItem($row, $UrlWL, $arrPageData['files_url'], $images_params, true, $catid);
                }
            }
        }
                
        // item comments
        $item['comments'] = array();
        $select = 'SELECT c.* FROM `'.COMMENTS_TABLE.'` c ';
        $join = '';
        $where = 'WHERE c.`pid`='.$itemID.' AND c.`active`=1 AND c.`cid`=0 AND `module`="'.$arCategory['module'].'"';
        $order = 'GROUP BY c.`id` ORDER BY c.`created` DESC';
        $query = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $row['children'] = getComplexRowItems(COMMENTS_TABLE, '*', 'WHERE `pid`='.$itemID.' AND `active`=1 AND `cid`='.$row['id'], '`created` DESC');
                $item['comments'][] = $row;
            }
        }

        // product kits
        $item['arKitsIDX'] = array($item["idKey"]);
        $item['arKits']    = array();
        $select = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c ';
        $join = 'LEFT JOIN `'.CATALOG_KITS_TABLE.'` ck ON(ck.`kid` = c.`id`) ';
        $where = 'WHERE ck.`pid`='.$itemID.' AND c.`cprice`>0 ';
        $order = 'GROUP BY ck.`id` ORDER BY ck.`id`';
        $query = $select.$join.$where.$order;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $row = PHPHelper::getProductItem($row, $UrlWL, $arrPageData['files_url'], $images_params, true, $catid, "catalog", true);
                $row["options"] = PHPHelper::getProductOptions($row["id"], $item["selectedOptions"]);
                $row["selectedOptions"] = PHPHelper::getSelectedOptions($row["options"]);
                $row["price"]   = PHPHelper::recalcItemPriceByOptions($row["price"],  $row["options"]);
                $row["cprice"]  = PHPHelper::recalcItemPriceByOptions($row["cprice"], $row["options"]);
                $row['idKey']   = PHPHelper::makeProductIdKey($row["id"], $row["selectedOptions"]);
                $item['arKits'][] = $row;
                $item['arKitsIDX'][] = $row['idKey'];
            }
        }
    
        $arrPageData['headTitle']       = $item['title'];
        $arCategory['meta_descr']       = $item['meta_descr'];
        $arCategory['meta_key']         = $item['meta_key'];
        $arCategory['meta_robots']      = $item['meta_robots'];
        $arCategory['seo_title']        = $item['seo_title'];
       
        // action with comments
        if(!empty($_POST) AND isset($_POST['comment'])) {
            $_POST['comment']['descr'] = cleanText($_POST['comment']['descr']);
            
            $Validator->validateGeneral($_POST['comment']['title'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_FIRST_NAME));
            $Validator->validateGeneral($_POST['comment']['descr'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_STRING_TEXT));
            
            if ($Validator->foundErrors()) {
                $arrPageData['errors'][] = "<font color='#990033'>".FEEDBACK_ERROR_INPUT_STRING."</font>".$Validator->getListedErrors();
                $item = array_merge($item, $_POST);
            } else {
                $arPostData = screenData($_POST['comment']);
                $arPostData['pid'] = $itemID;
                $arPostData['isnew'] = 1;
                $arPostData['module'] = $arCategory['module'];
                $arPostData['created'] = date('Y-m-d h:i:s');
                $arPostData['order'] = getMaxPosition(null, 'order', false, COMMENTS_TABLE);
                if($DB->postToDB($arPostData, COMMENTS_TABLE)) {
                    setSessionMessage(FEEDBACK_STRING_SEND_EMAIL);
                    Redirect($UrlWL->buildItemUrl($arCategory, $item, 'result=success'));
                } else {
                    $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR.'. '.TRY_AGAIN_TITLE;
                }
            }
        }
    }

// List Items
} else {
    require_once('include/classes/Filters.php');
    
    //IF you want to show all subcategories  products  - uncomment below line
    $arCatIdSet = array_merge(array($catid), ($showSubItems ? getChildrensIDs($catid, true) : array()));
   
    $Filters = new Filters($UrlWL, $catid, $arCatIdSet);
    
    $where = $Filters->generateWhereState($arrPageData['selectedFilters']);

    $itemsIDX = PHPHelper::getCatalogItems($arCatIdSet, $lang, $module, '', '', '', true);
    $itemsFIDX = PHPHelper::getCatalogItems($arCatIdSet, $lang, $module, $where,'', '', true);
    
    $arrPageData['sort']      = ($sort = PHPHelper::getCorrectCatalogSort($sort));
    $arrPageData['arSorting'] = PHPHelper::getCatalogSorting($UrlWL, $sort);
    $arrPageData['filters']   = $Filters->getFilters($itemsIDX, $itemsFIDX);
    
    if(!$pages){
        // Total pages and Pager
        $arrPageData['total_items'] = count($itemsFIDX);
        $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);
        $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager
    }    
    $order = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
    $limit = (!$pages ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
    $query = PHPHelper::getCatalogItemsSql($arCatIdSet, $lang, $module, $where, $order, $limit);
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if(mysql_num_rows($result) > 0) {
        while ($item = mysql_fetch_assoc($result)) {
            $items[] = PHPHelper::getProductItem($item, $UrlWL, $arrPageData['files_url'], $images_params, true);
        }
    }
    
    $seoFilters = $UrlWL->getFilters()->getSelectedTitles();
    if (!empty($seoFilters)) {
        $arCategory["seo_title"]  = !empty($arCategory["filter_seo_title"]) ? PHPHelper::BuildFilterMetaData($arCategory["filter_seo_title"], $seoFilters) : $arCategory["seo_title"];
        $arCategory["seo_text"]   = !empty($arCategory["filter_seo_text"])  ? PHPHelper::BuildFilterMetaData($arCategory["filter_seo_text"], $seoFilters)  : $arCategory["seo_text"];
        $arCategory["meta_descr"] = !empty($arCategory["filter_meta_descr"])? PHPHelper::BuildFilterMetaData($arCategory["filter_meta_descr"], $seoFilters): $arCategory["meta_descr"];
        $arCategory["meta_key"]   = !empty($arCategory["filter_meta_key"])  ? PHPHelper::BuildFilterMetaData($arCategory["filter_meta_key"], $seoFilters)  : $arCategory["meta_key"];
    }
}

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################

//var_export($arrPageData['selectedFilters']);

# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
$smarty->assign('items',        $items);
$smarty->assign('arrCategories',$arrCategories);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

