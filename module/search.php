<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$pages_all   = !empty($_GET['pages'])   ? trim(addslashes($_GET['pages']))   : false;
$searchtext  = !empty($_POST['stext'])  ? htmlspecialchars(strtolower(addslashes(trim($_POST['stext']))))  : false;
$searchtext  = (!$searchtext && !empty($_GET['stext']))  ? htmlspecialchars(strtolower(addslashes(trim($_GET['stext']))))  : false;
$searchwhere = !empty($_POST['swhere']) ? addslashes(trim($_POST['swhere'])) : 'catalog';
$items       = array(); // Items Array of items Info arrays
$arItemsIDX     = array();
$arCids         = array();
$arCategories   = array();
$cid = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): false;
$images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################



# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// Manipulation with Page Number
if ($page>1) {
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($page==1 && isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
}
// Manipulation with Show Pages All Session Var
if ($pages_all) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
} elseif ($page>1 && isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
}
// Manipulation with Search text
if ($searchtext) {
    $_SESSION[MDATA_KNAME][$module]['stext'] = &$searchtext;
} elseif (!empty($_SESSION[MDATA_KNAME][$module]['stext'])) {
    $searchtext = &$_SESSION[MDATA_KNAME][$module]['stext'];
}
// Manipulation with where Search param
if ($searchwhere) {
    $_SESSION[MDATA_KNAME][$module]['swhere'] = &$searchwhere;
} elseif (!empty($_SESSION[MDATA_KNAME][$module]['swhere'])) {
    $searchwhere = &$_SESSION[MDATA_KNAME][$module]['swhere'];
} else {
    $searchwhere = 'all';
}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['files_url']     = UPLOAD_URL_DIR.'catalog/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 16;
$arrPageData['stext']         = $searchtext;
$arrPageData['swhere']        = $searchwhere;
$arCategory['title']          = SITE_SEARCH_RESULTS;
$arrPageData['filter_url']    = (!empty($cid)? '?cid='.$cid: '');
$arrPageData['cid']           = ($cid && !empty($cid))? $cid: 0;
$arrPageData['category_title']= ($cid) ? getValueFromDB(MAIN_TABLE, 'title', 'WHERE `id`='.$cid) : '';
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

if($searchtext && strlen($searchtext)>=2) {
    if($searchwhere == 'catalog'){
        $arrFields = array('pcode', 'title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title');
        
        $arrText = explode(' ', $searchtext);
        $serachByValue =  $serachByCTitle = $serachByTitle = '';
        foreach($arrText as $text) {
            $serachByValue .= ($serachByValue ? ' AND ' : ''). " LOWER(a.`value`) like '%".$text."%'  ";
            $serachByCTitle .= ($serachByCTitle ? ' AND ' : ''). " LOWER(mt.`title`) like '%".$text."%'  ";
            $serachByTitle .= ($serachByTitle ? ' AND ' : ''). " LOWER(bt.`title`) like '%".$text."%'  ";
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
                $row = PHPHelper::getProductItem($row, $UrlWL, $arrPageData['files_url'], $images_params);
                $arCids[] = $row['cid'];
                $arItemsIDX[] = $row['id'];
                if($cid && $row['cid']==$cid){
                    $items[] = $row;
                } elseif (!$cid) {
                    $items[] = $row;
                }
            }
            
            $arCategories = array();
            if(!empty($arCids)) {
                $arCategories = getComplexRowItems(MAIN_TABLE.' m', 'm.*', 'WHERE m.`id` IN('.implode(",", $arCids).')', 'm.`order`');
                foreach ($arCategories as $key => $category) {
                    $arCategories[$key]['cnt'] = getValueFromDB(CATALOG_TABLE.' c', 'COUNT(c.`id`)', 'WHERE c.`cid`='.$category['id'].' AND c.`id` IN('.implode(",", $arItemsIDX).')', 'cnt');
                }
            }
        }
    } else {
        $arrFields = array('title', 'text', 'descr', 'meta_descr', 'meta_key', 'seo_title');
        $query = "SELECT `id` FROM " . MAIN_TABLE . " WHERE LOWER(".getSqlStrCondition(getSqlListFilter($arrFields, $searchtext, 'LIKE'), 'OR').") AND `active` = 1 ORDER BY `order` ";
        $result = mysql_query($query);
        if($result && mysql_num_rows($result)){
            while ($row = mysql_fetch_assoc($result)) {
                $row = $UrlWL->getCategoryById($row['id'], true);
                $row['descr'] = unScreenData($row['text']);
                $row['name']  = PAGES;
                $items[]=$row;
            }
        }
         
        $arrSearchModules = array(
            array('module'=>'news',    'table'=>NEWS_TABLE,    'title'=>NEWS,      'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title')), 
            array('module'=>'gallery', 'table'=>GALLERY_TABLE, 'title'=>GALLERIES, 'arFields'=>array('title', 'descr', 'meta_descr', 'meta_key', 'seo_title')),
            array('module'=>'video',   'table'=>VIDEOS_TABLE,  'title'=>VIDEOS,    'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title'))
        );
        
        $select = '*';
        $order = " `created` DESC, `cid`, `order`";
        
        foreach($arrSearchModules as $module){
            $where = " WHERE (".getSqlStrCondition(getSqlListFilter($module['arFields'], $searchtext, 'LIKE'), 'OR').") AND `active` = 1";
            $result = getComplexRowItems($module['table'], $select, $where, $order);
            if(!empty($result)){
                foreach($result as $row) {
                    $row['arCategory'] = ($row['cid']>0 && $row['cid']!=$arrModules[$module['module']]['id']) ? $UrlWL->getCategoryById($row['cid']) : $arrModules[$module['module']];
                    $row['name']       = $module['title'];
                    $items[]=$row;
                }
            }
        }
    }
    
    // -------------------------------------------------------------------------
    if (!$pages_all){
        // Total pages and Pager
        $arrPageData['total_items'] = count($items);
        if($cid)   $arrPageData['items_on_page'] = $arrPageData['total_items'];
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildPagerUrl($arCategory));
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager

        // set page limit
        if($arrPageData['total_items'] > $arrPageData['items_on_page']) {
            $items = array_slice($items, $arrPageData['offset'], $arrPageData['items_on_page']);
        }
    }
} elseif(!empty($_POST)) {
    $arrPageData['errors'][] = FOUND_ERROR;
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################

if($cid && ($categoryTitle = getValueFromDB(MAIN_TABLE, 'title', 'WHERE `id`='.$cid))) {
    $arrPageData['arrBreadCrumb'] = UrlWL::addBreadCrumbs($arrPageData['arrBreadCrumb'], $categoryTitle, $UrlWL->buildCategoryUrl($arCategory, 'cid='.$cid.'&stext='.$searchtext.($pages_all ? UrlWL::PAGES_KEY_NAME.'='.UrlWL::PAGES_ALL_VAL : '')));
}

# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('arItemsIDX',           $arItemsIDX);
$smarty->assign('arCategories',         $arCategories);
$smarty->assign('items',                $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

