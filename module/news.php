<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$pages_all     = !empty($_GET['pages']) ? trim(addslashes($_GET['pages'])) : false;
$itemID        = $UrlWL->getItemId();
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$arrCategories = array();
$showSubItems  = true;
// Manipulation with Page Number
if ($page > 1)                                                          $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
elseif ($itemID and isset($_SESSION[MDATA_KNAME][$module]['page']))     $page = &$_SESSION[MDATA_KNAME][$module]['page'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['page']))                 unset($_SESSION[MDATA_KNAME][$module]['page']);
// Manipulation with Show Pages All Session Var
if ($pages_all)                                                         $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
elseif ($itemID and isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall']))             unset($_SESSION[MDATA_KNAME][$module]['pagesall']);

$arrPageData['pagesall']      = &$pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 5;
// Item Detailed View
if ($itemID and $item = getSimpleItemRow($itemID, NEWS_TABLE)) {
    $item['descr']     = unScreenData($item['descr']);
    $item['fulldescr'] = unScreenData($item['fulldescr']);
    $item['image']     = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
    $item["images"]    = array();
    $query  = "SELECT * FROM `".NEWSFILES_TABLE."` WHERE `active`>0 AND `pid`={$itemID} ORDER BY `isdefault` DESC, `fileorder` ASC";
    $result = mysql_query($query);
    if ($result and mysql_num_rows($result)>0) {
        $files_url  = $arrPageData['files_url']."$itemID/";
        $files_path = prepareDirPath($files_url);
        while ($row = mysql_fetch_assoc($result)) {
            $row["image"]       = (!empty($row["filename"]) and file_exists($files_path.$row["filename"])) ? $files_url.$row["filename"] : $arrPageData['files_url'].'noimage.jpg';
            $row["small_image"] = (!empty($row["filename"]) and file_exists($files_path."small_".$row["filename"])) ? $files_url."small_".$row["filename"] : $arrPageData['files_url'].'small_noimage.jpg';
            $item["images"][]   = $row;
        }
        mysql_free_result($result);
    }
    // Set vars
    $arrPageData['headTitle']     = $item['title'];
    $arCategory['meta_descr']     = $item['meta_descr'];
    $arCategory['meta_key']       = $item['meta_key'];
    $arCategory['meta_robots']    = $item['meta_robots'];
    $arCategory['seo_title']      = $item['seo_title'];
    $arrPageData['headCss'][]     = '/css/store/news-more.css';
    $arrPageData['headScripts'][] = '/js/news-more.js';
} else {
    // List Items
    $arrPageData['headCss'][] = '/css/store/news.css';
    
    // IF you want to show all subcategories  products  - uncomment below line
    $arChildrensID = $showSubItems ? getChildrensIDs($catid, true) : 0;

    $query = 'SELECT t.* FROM `'.NEWS_TABLE.'` t ';
    $where = 'WHERE t.`active`>0 '.(($catid!=$arrModules[$module]["id"]) ? "AND `cid`={$catid} " : "");

    if (!$pages_all) {
        $arrPageData['total_items'] = intval(getValueFromDB(NEWS_TABLE.' t', 'COUNT(*)', $where, 'count'));
        $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);
        $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    }

    $order  = 'ORDER BY t.`created` DESC, t.`order` ';
    $limit  = $pages_all ? '' : "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $result = mysql_query($query.$where.$order.$limit) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if ($result and mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['arCategory'] = $arCategory;
            $row['descr']      = unScreenData($row['descr']);
            $row['image']      = (!empty($row['image']) and is_file($arrPageData['files_path'].$row['image'])) ? $arrPageData['files_url'].$row['image'] : $arrPageData['files_url'].'noimage.jpg';
            $items[]           = $row;
        }
    }
}

$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
$smarty->assign('arrCategories', $arrCategories);