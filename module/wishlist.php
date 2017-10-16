<?php defined('WEBlife') or die( 'Restricted access' );

$items  = array();
$itemsIDX = array();
$cid = (!empty($_GET['cid']) AND intval($_GET['cid']))? intval($_GET['cid']): false;
$action = !empty($_GET['action'])? trim(addslashes($_GET['action'])): false;
$itemID = (!empty($_GET['itemID']) AND intval($_GET['itemID']))? intval($_GET['itemID']): false;
$images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));

$arrPageData['files_url']  = UPLOAD_URL_DIR.'catalog/';
$arrPageData['files_path'] = prepareDirPath($arrPageData['files_url']);
$arrPageData['categories'] = array();
$arrPageData['cid']        = intval($cid);

if ($Cookie->isSetCookie($module)) {
    $itemsIDX = unserialize($_COOKIE[$module]);
    if (!empty($itemsIDX)) {   
        $select = 'SELECT distinct mnt.*, (SELECT COUNT(*) FROM `'.CATALOG_TABLE.'` WHERE `id` IN('.implode(',', $itemsIDX).') AND `cid`=mnt.`id`) AS `cnt` FROM `'.MAIN_TABLE.'` mnt  LEFT JOIN '.CATALOG_TABLE.' c ON mnt.`id`=c.`cid`';
        $where  = ' WHERE c.`id` IN('.implode(',', $itemsIDX).') ORDER BY mnt.`title`';
        $query  = $select.$where;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $item["opened"] = ($cid AND $cid==$item["id"]) ? true : false;
                $arrPageData['categories'][] = $item;
            }
        }

        $select = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c ';
        $where = 'WHERE c.`id` IN('.implode(',', $itemsIDX).') AND c.`cid`='. (($cid) ? $cid : $arrPageData['categories'][0]['id']);
        $query = $select.$where;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $item    = PHPHelper::getProductItem($item, $UrlWL, $arrPageData['files_url'], $images_params, true, $item["cid"]);
                $items[] = $item;
            }
        }
        // remove item from wishlist
        if ($itemID AND ($action AND $action=='remove')) {
            $index = array_search($itemID, $itemsIDX, true);
            if ($index!==false){
                unset($itemsIDX[$index]);
                $Cookie->add($module, serialize($itemsIDX), time()+(3600*(24*7)));
                Redirect($UrlWL->buildCategoryUrl($arCategory));
            }
        } 
        // clear wishlist
        elseif ($action AND $action=='clear') {
            $Cookie->del($module);
            Redirect($UrlWL->buildCategoryUrl($arCategory));
        }
    } else Redirect('/');
} else Redirect('/');

$smarty->assign('items', $items);