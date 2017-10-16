<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$cid           = (isset($_GET['cid']) && intval($_GET['cid']))       ? intval($_GET['cid'])    : 0;
$items         = array(); // Items Array of items Info arrays
$type          =  isset($_GET['type']) ? $_GET['type'] : '';    
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']);
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'].'&type='.$type;
$arrPageData['arrBreadCrumb'] = getBreadCrumb($cid, 1);
$arrPageData['type']          = $type;
$arrPageData['headTitle']     = SELECTIONS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 20;
if($type && array_key_exists($type, $PHPHelper->SELECTIONS)){
    $arrPageData['typeTitle'] = $PHPHelper->SELECTIONS[$type];
} else {
    foreach($PHPHelper->SELECTIONS as $field=>$title){
        $type = $field;
        $arrPageData['typeTitle'] = $title;
        break;
    }
}
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// SET Reorder
if($task=='reorderItems' && !empty($_POST)) {
    if($hasAccess) {
        $error = '';
        $aff_rows = 0;
        foreach($_POST['arOrder'] as $id=>$val){
            $update = "UPDATE `".PRODUCT_SELECTIONS_TABLE."` SET `order`=".$val." WHERE `type`='".$type."' AND `pid`=".$id;
            if(@mysql_query($update) === FALSE) $error .= "Не выполнена команда: <br/>{$update}<br/>";
            elseif(mysql_affected_rows()) $aff_rows++;
        }
        $result = ($aff_rows==0) ? false :(empty($error) ? true : "UPDATE REORDER: MySQL Error #<b>".mysql_errno()."</b><br/>SQL:<br/>{$error}<br/><br/>Error: ".mysql_error());
        if($result===true) {
            setSessionMessage('Новая сортировка елементов успешно сохранена!'); 
        } elseif($result) {
            $arrPageData['errors'][] = $result;
        }
        Redirect($arrPageData['current_url']); 
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess) {
        $result = deleteRecords(PRODUCT_SELECTIONS_TABLE, 'WHERE `pid`='.$itemID.' AND `type`="'.$type.'"');
        Redirect($arrPageData['current_url']); 
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}

elseif (isset($_POST['searchItemID']) && $_POST['searchItemID']>0 && $task=='addItem') {
    if($hasAccess) {
        $arPostData['pid'] = intval($_POST['searchItemID']);
        $arPostData['type'] = $type;
        $arPostData['order'] = getMaxPosition('"'.$type.'"', 'order', 'type', PRODUCT_SELECTIONS_TABLE);
        $item = getItemRow(PRODUCT_SELECTIONS_TABLE, '*', 'WHERE `pid`='.$arPostData['pid'].' AND `type`="'.$arPostData['type'].'"');
        if(empty($item)) $result = $DB->postToDB($arPostData, PRODUCT_SELECTIONS_TABLE, '',  '', 'insert');
        else setSessionErrors ('Данный товар уже выбран!');
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
 

// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';

 // Display Items List Data
$where = $type ? "WHERE ps.`type` = '".$type."'" : "";
   
// Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(PRODUCT_SELECTIONS_TABLE." ps", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['filter_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager

$order = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "ps.order");
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

$query  = "SELECT ps.*, t.`title`, t.`id`, mnt.`title` as cat_title FROM `".PRODUCT_SELECTIONS_TABLE."` ps LEFT JOIN `".CATALOG_TABLE."` t ON ps.`pid` = t.`id` LEFT JOIN ".MAIN_TABLE." mnt ON t.cid = mnt.id $where $order $limit";
$result = mysql_query($query);
if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
else {
    while ($row = mysql_fetch_assoc($result)) {
        $items[]           = $row;
    }
}
   
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.admin.js" type="text/javascript"></script>';
$arrPageData['headCss'][]       = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.autocomplete.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.widget.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.datepicker.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/1251/jquery.ui.datepicker-ru.js" type="text/javascript"></script>';

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/**
 * 
  DROP TABLE IF EXISTS `product_selections`;
  CREATE TABLE IF NOT EXISTS `product_selections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `type` varchar(256) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_type` (`type`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 */