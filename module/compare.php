<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$arrPids      = isset($_GET['compare']) ? $_GET['compare'] : '';
$action       = !empty($_GET['action'])? trim(addslashes($_GET['action'])): false;
$itemID       = (!empty($_GET['itemID']) && intval($_GET['itemID']))? intval($_GET['itemID']): false;
$showDiff     = isset($_GET['show']) ? true : false;
$arrCompare   = array();
$items        = array(); // Items Array of items Info arrays
$showSubItems = true;
$showEmptyAttr= false; // Show or hide empty product attributes
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################
if(!empty($arrPids)){
    foreach(explode(',', $arrPids) as $pid){
        if(!empty($pid))  {
            $arrCompare[] = intval($pid); 
        } 
    }
    $Cookie->add($module, serialize($arrCompare), time()+(3600*(24*7)));
} else if ($Cookie->isSetCookie($module)) {
    $arrCompare = unserialize($_COOKIE[$module]);
}
// remove item from wishlist
if($itemID && ($action && $action=='remove')) {
    $index = array_search($itemID, $arrCompare, true);
    if($index!==false){
        unset($arrCompare[$index]);
        $Cookie->add($module, serialize($arrCompare), time()+(3600*(24*7)));
        Redirect($UrlWL->buildCategoryUrl($arCategory, 'compare='.implode(',', $arrCompare)));
    }
} 
// clear wishlist
elseif ($action && $action=='clear') {
    $Cookie->del($module);
    Redirect($UrlWL->buildCategoryUrl($arCategory));
}
# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, '', '', $page);
$arrPageData['current_url']   = $arrPageData['backurl'].($arrCompare ? '?compare='.implode(',',$arrCompare) : '');
$arrPageData['files_url']     = UPLOAD_URL_DIR.$arrModules['catalog']['module'].'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 10;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.js" type="text/javascript"></script>';

if(!empty($arrCompare)) {
    $select = 'SELECT m.* FROM '.MAIN_TABLE.' m ';
    $join = 'LEFT JOIN '.CATALOG_TABLE.' c ON(c.`cid` = m.`id`) ';
    $where = 'WHERE m.`active`=1 AND c.`id` IN('.implode(',', $arrCompare).') ';
    $order = 'GROUP BY m.`id` ORDER BY m.`order`';
    $query = $select.$join.$where.$order;
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){
        while ($row = mysql_fetch_assoc($result)) {
            $arCats[] = $row;
        }
    }
    
    $select = 'SELECT c.*, cf.`filename` AS `image` FROM `'.CATALOG_TABLE.'` c ';
    $join = 'LEFT JOIN `'.CATALOGFILES_TABLE.'` cf ON(cf.`pid` = c.`id` AND cf.`isdefault`=1 ) ';
    $where = 'WHERE c.`active`=1 AND c.`id` IN('.implode(',', $arrCompare).') ';
    $order = 'GROUP BY c.`id` ORDER BY c.`order`';
    $query = $select.$join.$where.$order;
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){
        while ($row = mysql_fetch_assoc($result)) {
            $row['price'] = ($row['isdiscount'] && $row['discount']) ? ($row['price'] - ($row['price']*$row['discount']/100)) : $row['price'];
            $row['small_image'] = (!empty($row['image']) && file_exists($arrPageData['files_path'].$row['id'].DS.$row['image']))? $arrPageData['files_url'].$row['id'].'/small_'.$row['image']: $arrPageData['files_url'].'small_noimage.jpg';
            $row['arCategory'] = $UrlWL->getCategoryById($row['cid']);
            $items[] = $row;
        }
    }
} else {
    Redirect('/');
}

if(!empty($arCats) && !empty($items)) {
    foreach ($arCats as $key=>$category){
        
        // заполнение массива товаров для каждой категории
        $arCats[$key]['items'] = array();
        foreach ($items as $k=>$item){
            if($item['cid'] == $category['id']) {
                $arCats[$key]['items'][] = $item;
            }
        }
        
        // заполнение массива атрибутов для категории
        $arCats[$key]['attrGroups'] = array();
        $select = 'SELECT ag.* FROM `'.ATTRIBUTE_GROUPS_TABLE.'` ag ';
        $join = 'LEFT JOIN `'.CATEGORY_ATTRIBUTE_GROUPS_TABLE.'` cag ON(cag.`gid` = ag.`id`) ';
        $where = 'WHERE ag.`active`=1 AND cag.`cid`='.$category['id'].' ';
        $order = 'GROUP BY ag.`id` ORDER BY ag.`order`';
        $query = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($group = mysql_fetch_assoc($result)) {
                $group['attributes'] = array();
                $sel = 'SELECT a.* FROM `'.ATTRIBUTES_TABLE.'` a ';
                $jon = 'LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) ';
                $whr = 'WHERE a.`gid`='.$group['id'].' ';
                $ord = 'GROUP BY a.`id` ORDER BY a.`order`';
                $qry = $sel.$whr.$ord;
                $res = mysql_query($qry);
                if(mysql_num_rows($res) > 0) {
                    while ($r = mysql_fetch_assoc($res)) {
                        $group['attributes'][$r['id']] = $r;
                    }
                }
                $arCats[$key]['attrGroups'][$group['id']] = $group;
            }
        }
        
        // заполнение массива атрибутов для товаров в каждой категории
        if(!empty($arCats[$key]['items'])) {
            foreach ($arCats[$key]['items'] as $i=>$item) {
                $arCats[$key]['items'][$i]['attrGroups'] = array();
                if(!empty($arCats[$key]['attrGroups'])) {
                    foreach ($arCats[$key]['attrGroups'] as $g => $group) {
                        $arCats[$key]['items'][$i]['attrGroups'][$group['id']] = array();
                        if(!empty($arCats[$key]['attrGroups'][$g]['attributes'])) {
                            foreach ($arCats[$key]['attrGroups'][$g]['attributes'] as $a => $attribute) {
                                $atr = getValueFromDB(PRODUCT_ATTRIBUTE_TABLE, 'value', 'WHERE `pid`='.$item['id'].' AND `aid`='.$attribute['id'], 'val');
                                if($atr && !empty($atr)) {
                                    $arCats[$key]['items'][$i]['attrGroups'][$group['id']][$attribute['id']] = $atr;
                                } else {
                                    $arCats[$key]['items'][$i]['attrGroups'][$group['id']][$attribute['id']] = '--';
                                }
                            }
                        }
                    }
                }
            }
        } else {
            unset($arCats[$key]);
        }
        
        // показ различий
        if($showDiff && !empty($arCats[$key]['items']) && !empty($arCats[$key]['attrGroups'])) {
            $itemCount = count($arCats[$key]['items']);
            $firstItem = $arCats[$key]['items'][0];
            $firstAttr = $arCats[$key]['items'][0]['attrGroups'];
            
            // только если в категории больше 1 товара
            $compares = array();
            if($itemCount > 1) {
                foreach ($arCats[$key]['items'] as $i => $item) {
                    if($i > 0) {
                        if(!empty($arCats[$key]['items'][$i]['attrGroups'])) {
                            foreach ($arCats[$key]['items'][$i]['attrGroups'] as $gid=>$gval) {
                                if (!empty($gval) && is_array($gval)) {
                                    foreach ($arCats[$key]['items'][$i]['attrGroups'][$gid] as $aKey=>$val) {
                                        if (!isset($firstAttr[$gid][$aKey]) || (isset($firstAttr[$gid][$aKey]) && $firstAttr[$gid][$aKey] == $val && isset($arCats[$key]['attrGroups'][$gid]['attributes'][$aKey]))) {
                                            $compares[] = $aKey;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(!empty($compares)) {
                    foreach ($arCats[$key]['attrGroups'] as $g => $group) {
                        if(!empty($group['attributes'])) {
                            foreach ($group['attributes'] as $a => $attribute) {
                                if(in_array($a, $compares)) {
                                    unset($arCats[$key]['attrGroups'][$g]['attributes'][$a]);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        
    }
} else {
    Redirect('/');
}

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('items',        $items);
$smarty->assign('arCats',       $arCats);
$smarty->assign('showDiff',     $showDiff);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################