<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$pages_all    = !empty($_GET['pages']) ? trim(addslashes($_GET['pages'])) : false;
$itemID       = $UrlWL->getItemId();
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
// Manipulation with Page Number
if ($page > 1)                                                          $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page']) )     $page = &$_SESSION[MDATA_KNAME][$module]['page'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['page']))                 unset($_SESSION[MDATA_KNAME][$module]['page']);
// Manipulation with Show Pages All Session Var
if ($pages_all)                                                         $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall']))  $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall']))             unset($_SESSION[MDATA_KNAME][$module]['pagesall']);

$arrPageData['pagesall']      = &$pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 6;
$arrPageData['headCss'][]     = '/css/store/gallery.css';

// Item Detailed View
if ($itemID and $item = getSimpleItemRow($itemID, GALLERY_TABLE) and !empty($item)) {
    // Set vars
    $arrPageData['headTitle']     = $item['title'];
    $arCategory['meta_descr']     = $item['meta_descr'];
    $arCategory['meta_key']       = $item['meta_key'];
    $arCategory['meta_robots']    = $item['meta_robots'];
    $arCategory['seo_title']      = $item['seo_title'];
    $arrPageData['files_url']    .= $itemID.'/';
    $arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
    $arrPageData["headScripts"][] = "/js/libs/slick-carousel/slick.min.js";
    $arrPageData['headScripts'][] = '/js/project-more.js';
    $item['descr']  = unScreenData($item['descr']);
    // Get images
    $item['images'] = array();
    $DB->Query("SELECT * FROM `".GALLERYFILES_TABLE."` WHERE `active`>0 AND `pid`={$itemID} ORDER BY `fileorder`");
    while ($row = $DB->fetchAssoc()) {
        $row["image"] = (!empty($row['filename']) and file_exists($arrPageData['files_path'].$row['filename'])) ? $arrPageData['files_url'].$row['filename'] : $arrPageData['files_url'].'noimage.jpg';
        $item['images'][] = $row;
    } $DB->Free();
    // Get features
    $item['features'] = array();
    $DB->Query("SELECT * FROM `".GALLERY_FEATURES_TABLE."` WHERE `pid`={$itemID} ORDER BY `order`");
    while ($row = $DB->fetchAssoc()) {
        $item['features'][] = $row;
    } $DB->Free();
    
} else {

    $query = "SELECT DISTINCT t.*, gf.`filename` AS `cover` FROM `".GALLERY_TABLE."` t "
            . "LEFT JOIN `".GALLERYFILES_TABLE."` gf ON(gf.`pid`=t.`id` AND gf.`isdefault`=1) "
            . "WHERE t.`active`>0 "
            . (($arrModules[$module]['id']!=$catid) ? "AND t.`cid`={$catid} " : "")." "
            . "ORDER BY t.`order`";

    if (!$pages_all) {
        $arrPageData['total_items'] = intval(getValueFromDB(GALLERY_TABLE." t", "COUNT(*)", "WHERE t.`active`>0 ".(($arrModules[$module]['id']!=$catid) ? "AND t.`cid`={$catid} " : ""), "cnt"));
        $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);
        $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    }    

    $query .= ($pages_all ? '' : "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}");
    $result = mysql_query($query);
    if ($result and mysql_num_rows($result)>0) {
        while ($row = mysql_fetch_assoc($result)) {
            $files_url  = $arrPageData['files_url'].$row['id'].'/';
            $files_path = prepareDirPath($files_url);
            $row['arCategory'] = ($row['cid']>0 and $row['cid']!=$catid) ? $UrlWL->getCategoryById($row['cid']) : $arCategory;
            $row['cover']      = (!empty($row['cover']) and file_exists($files_path."small_".$row['cover'])) ? $files_url."small_".$row['cover'] : $arrPageData['files_url'].'small_noimage.jpg';
            $items[] = $row;
        }
    }
}

$smarty->assign('item',  $item);
$smarty->assign('items', $items);