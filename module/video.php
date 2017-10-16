<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$pages_all    = !empty($_GET['pages'])   ? trim(addslashes($_GET['pages']))                   : false;
$itemID       = $UrlWL->getItemId();
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$arrCategories= array();
$showSubItems = true;
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// Manipulation with Page Number
if ($page > 1)                                                          $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page']) )     $page = &$_SESSION[MDATA_KNAME][$module]['page'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['page']))                 unset($_SESSION[MDATA_KNAME][$module]['page']);
// Manipulation with Show Pages All Session Var
if ($pages_all)                                                         $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall']))  $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall']))             unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['pagesall']      = &$pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 10;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.video.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.video.js" type="text/javascript"></script>';

// Item Detailed View
if($itemID) {
    $item = unScreenData(getSimpleItemRow($itemID, VIDEOS_TABLE));
    if(!empty($item)) {
        // Set vars
        $arrPageData['headTitle']       = $item['title'];
        $arCategory['meta_descr']       = $item['meta_descr'];
        $arCategory['meta_key']         = $item['meta_key'];
        $arCategory['meta_robots']      = $item['meta_robots'];
        $arCategory['seo_title']        = $item['seo_title'];

        $item['middle_image'] = (!empty($item['image']) && is_file($arrPageData['files_path'].'middle_'.$item['image'])) ? $arrPageData['files_url'].'middle_'.$item['image'] : $arrPageData['files_url'].'middle_noimage.jpg';
        $item['image']        = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
        $item['filename']     = (!empty($item['filename']) && is_file($arrPageData['files_path'].$item['filename'])) ? $arrPageData['files_url'].$item['filename'] : '';
       
        $item['fileinfo']     = unserialize($item['fileinfo']);
        if(!empty($item['fileinfo']['video'])){
            $item['fileinfo']['highslide']['width']        = $item['fileinfo']['video']['resolution_x'];
            $item['fileinfo']['highslide']['height']       = $item['fileinfo']['video']['resolution_y']+22;
            $item['fileinfo']['highslide']['resolution_x'] = $item['fileinfo']['video']['resolution_x'];
            $item['fileinfo']['highslide']['resolution_y'] = $item['fileinfo']['video']['resolution_y'];
        }        
    }

// List Items
} else {
    // IF you want to show all subcategories  products  - uncomment below line
    $arChildrensID = $showSubItems ? getChildrensIDs($catid, true) : 0;

    $query = 'SELECT t.* FROM `'.VIDEOS_TABLE.'` t ';
    $where = 'WHERE t.`active`=1 AND t.`cid` IN ('.(!empty($arChildrensID) ? $catid.','.implode(',', $arChildrensID) : $catid).') ';

    if(!$pages_all){
        // Total pages and Pager
        $arrPageData['total_items'] = intval(getValueFromDB(VIDEOS_TABLE.' t', 'COUNT(*)', $where, 'count'));
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildPagerUrl($arCategory));
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager
    }

    $order  = 'ORDER by t.`created` DESC, t.`order` ';
    $limit  = $pages_all ? '' : "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $result = mysql_query($query.$where.$order.$limit) or die(strtoupper($module).' SELECT: ' . mysql_error());

    $items  = array();
    if(mysql_num_rows($result) > 0) {
        while ($item = mysql_fetch_assoc($result)) {
            $item['arCategory']   = ($item['cid']>0 && $item['cid']!=$catid) ? $UrlWL->getCategoryById($item['cid']) : $arCategory;

            $item['small_image']  = (!empty($item['image']) && is_file($arrPageData['files_path'].'small_'.$item['image'])) ? $arrPageData['files_url'].'small_'.$item['image'] : $arrPageData['files_url'].'small_noimage.jpg';
//            $item['middle_image'] = (!empty($item['image']) && is_file($arrPageData['files_path'].'middle_'.$item['image'])) ? $arrPageData['files_url'].'middle_'.$item['image'] : $arrPageData['files_url'].'middle_noimage.jpg';
            $item['image']        = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
            $item['filename']     = (!empty($item['filename']) && is_file($arrPageData['files_path'].$item['filename'])) ? $arrPageData['files_url'].$item['filename'] : '';
       
            $item['fileinfo']     = unserialize(unScreenData($item['fileinfo']));
            if(!empty($item['fileinfo']['video'])){
                $item['fileinfo']['highslide']['width']        = $item['fileinfo']['video']['resolution_x'];
                $item['fileinfo']['highslide']['height']       = $item['fileinfo']['video']['resolution_y']+22;
                $item['fileinfo']['highslide']['resolution_x'] = $item['fileinfo']['video']['resolution_x'];
                $item['fileinfo']['highslide']['resolution_y'] = $item['fileinfo']['video']['resolution_y'];
            } 
            $items[] = $item;
        }
    }
    /*
    $query = 'SELECT * FROM `'.MAIN_TABLE.'` WHERE pid='.$catid.' ORDER BY `order`';
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $row['image'] = (!empty($row['image']) && is_file(MAIN_CATEGORIES_URL_DIR.$row['image'])) ? MAIN_CATEGORIES_URL_DIR.$row['image'] : '';
        $arrCategories[] = $row;
    }
   */
}

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
$smarty->assign('items',        $items);
$smarty->assign('arrCategories',$arrCategories);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

