<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$pages_all    = !empty($_GET['pages']) ? trim(addslashes($_GET['pages'])) : false;
$itemID       = $UrlWL->getItemId();
$cid          = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): false;
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays

if ($page > 1) {
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
}
// Manipulation with Show Pages All Session Var
if ($pages_all) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
}

$arrPageData['cid']           = $cid;
$arrPageData['pagesall']      = &$pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 5;

// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.js" type="text/javascript"></script>';

// Item Detailed View
if($itemID) {
    $item = getSimpleItemRow($itemID, BRANDS_TABLE);
    if(!empty($item)) {
        // Set vars
        $arrPageData['headTitle']       = $item['title'];
        $arCategory['meta_descr']       = $item['meta_descr'];
        $arCategory['meta_key']         = $item['meta_key'];
        $arCategory['meta_robots']      = $item['meta_robots'];
        $arCategory['seo_title']        = $item['seo_title'];
        $item['descr']        = unScreenData($item['descr']);
        $item['fulldescr']    = unScreenData((($cid)? getValueFromDB(BRANDS_DESCR_TABLE, '`fulldescr`', 'WHERE `pid`='.$itemID.' AND `cid`='.$cid, 'fdscr'): $item['fulldescr']));
        $item['image']        = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
    
        $item['arCategories'] = array();
        $select = 'SELECT m.* FROM `'.MAIN_TABLE.'` m ';
        $join = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`cid` = m.`id`) LEFT JOIN `'.BRANDS_DESCR_TABLE.'` bd ON(bd.`cid` = m.`id`) ';
        $where = 'WHERE m.`active`>0 AND c.`active`>0 AND bd.`fulldescr` IS NOT NULL AND bd.`pid`='.$itemID.' ';
        $order = 'GROUP BY m.`id` ORDER BY m.`order`';
        $query = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $item['arCategories'][] = $row;
            }
        }
    }

// List Items
} else {

    $query = 'SELECT b.* FROM `'.BRANDS_TABLE.'` b ';
    $where = 'WHERE b.`active`=1 ';

    if(!$pages_all){
        // Total pages and Pager
        $arrPageData['total_items'] = intval(getValueFromDB(BRANDS_TABLE.' b', 'COUNT(*)', $where, 'count'));
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildPagerUrl($arCategory));
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager
    }

    $order  = 'ORDER by b.`created` DESC, b.`order` ';
    $limit  = $pages_all ? '' : "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $result = mysql_query($query.$where.$order.$limit) or die(strtoupper($module).' SELECT: ' . mysql_error());

    $items  = array();
    if(mysql_num_rows($result) > 0) {
        while ($item = mysql_fetch_assoc($result)) {
            $item['arCategory']  = $arCategory;
            $item['descr']       = unScreenData($item['descr']);
            $item['image']       = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
            $items[] = $item;
        }
    }
}

$smarty->assign('item',          $item);
$smarty->assign('items',         $items);