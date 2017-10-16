<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$itemID        = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$filters       = !empty($_GET['filters'])? $_GET['filters']: array();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']);
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrOrder                     = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = ORDERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 20;
$arrPageData['arStatus']      = getComplexRowItems(ORDER_STATUS_TABLE, '*', '', 'id');
$arrPageData['arPayments']    = getComplexRowItems(PAYMENT_TYPES_TABLE, '*');
$arrPageData['arShippings']   = getComplexRowItems(SHIPPING_TYPES_TABLE, '*');
$arrPageData['arTypes']       = getComplexRowItems(ORDER_TYPES_TABLE, '*');
$arrPageData['filters']       = $filters;
$arrPageData['orders_url']    = $arrOrder['get'] ? $arrOrder['get'] : array();
$arrPageData['options']       = array();
$query  = "SELECT * FROM `".OPTIONS_TABLE."` ORDER BY `order`";
$result = mysql_query($query);
if ($result AND mysql_num_rows($result) > 0) {
    while ($option = mysql_fetch_assoc($result)) {
        $option["values"] = getRowItemsInKey("id", OPTIONS_VALUES_TABLE, "*", "WHERE `option_id`=".$option["id"], "ORDER BY `order`");
        $arrPageData['options'][$option["id"]] = $option;
    }
}
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if($itemID && $task=='deleteItem') {
    if($hasAccess) {
        $result = deleteDBLangsSync(ORDERS_TABLE, ' WHERE `id`='.$itemID);
        if(!$result) {
            $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        } elseif($result) {
            deleteRecords(ORDER_PRODUCTS_TABLE, 'WHERE `oid`='.$itemID);
            Redirect($arrPageData['current_url']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

if (!$itemID AND $task=="addItem") {
    $item = array_combine_multi($DB->getTableColumnsNames(ORDERS_TABLE), '');
    $item['type'] = 2;
    $item['payment'] = 1;
    $item['shipping'] = 1;
    $item['status'] = 1;
    $item['created'] = date('Y-m-d H:i:s');
    $result = $DB->postToDB($item, ORDERS_TABLE);
    if ($result AND is_int($result)) {
        $itemID = $result;
        setSessionMessage('Заказ №'.$itemID.' успешно создан');
        Redirect($arrPageData['admin_url'].'&task=editItem&itemID='.$itemID);
    }
}

if($itemID && $task=='editItem'){
    $select='SELECT o.* FROM `'.ORDERS_TABLE.'` o ';
    $where = ' WHERE o.`id`='.$itemID.' LIMIT 1';
    $query =  $select.$where;
    $result = mysql_query($query);
    if(!$result) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    } elseif(!mysql_num_rows($result)) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
    } else {
        $item = mysql_fetch_assoc($result);
        $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
        $item['createdTime'] = date('H:i:s', strtotime($item['created']));
        
        $item['arStatus']    = getItemRow(ORDER_STATUS_TABLE, '*', 'WHERE `id`='.$item['status']);
        $item['arPayment']   = getItemRow(PAYMENT_TYPES_TABLE, '*', 'WHERE `id`='.$item['payment']);
        $item['arShipping']  = getItemRow(SHIPPING_TYPES_TABLE, '*', 'WHERE `id`='.$item['shipping']);
        
        if($item['price'] > $item['arShipping']['minprice']) {
            $item['shipping_price'] = 0;
        } else {
            $item['shipping_price'] = $item['arShipping']['price'];
        }
        
        $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        
        // order products
        $item['children'] = array();
        $select = 'SELECT op.* FROM `'.ORDER_PRODUCTS_TABLE.'` op ';
        $where  = 'WHERE op.`oid`='.$itemID.' ';
        $order  = 'ORDER BY op.`id`';
        $query  = $select.$where.$order;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $arOptions = unserialize(unScreenData($row["options"]));
                if ($row['type']=="product") {
                    $product = getItemRow(CATALOG_TABLE, '*', 'WHERE `id`='.$row['pid']);
                    if (!empty($product)) {
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
                } elseif ($row['type']=="kit") {
                    $row['link']     = null;
                    $row['ptitle']   = $row['title'];
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
                $item['children'][] = $row;
            }
        }
        
        if (!empty($_POST)) {
            $Validator->validateGeneral($_POST['firstname'], 'Укажите имя клиента');
            $Validator->validateGeneral($_POST['phone'], 'Укажите номер телефона клиента');
            if ($Validator->foundErrors()) {
                $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
            } else {
                $arPostData = $_POST;
                $arUnusedKeys = array('id');
                $query_type = "update";
                $whereOptions = "WHERE `id`=$itemID";
                
                $result = $DB->postToDB($arPostData, ORDERS_TABLE, $whereOptions, $arUnusedKeys, $query_type);
                if ($result) {
                    $item_title = "Заказ №$itemID";
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    setSessionMessage("{$item_title} успешно сохранен!");
                    Redirect($arrPageData['admin_url']."&task={$arrPageData['task']}&itemID={$itemID}");
                }
            }
            
            $item = array_merge($item, $_POST);
        }
    }    
} else {
    // Display Items List Data
    $where = "";
    
    if(!empty($filters)) {
        if(!empty($filters['title'])) {
            $searchStr = htmlspecialchars(strtolower(addslashes(trim($filters['title']))));
            $arName = explode(' ', $searchStr);
            if(count($arName)>1) {
                foreach ($arName as $name) {
                    $where .= ($where ? ' OR ' : ' WHERE ( ').' t.`firstname` LIKE "%'.$name.'%" OR t.`surname` LIKE "%'.$name.'%" ';
                }
                $where .= ') ';
            } else {                                
                $where .= (!empty($where)? ' AND ': ' WHERE ( ').' t.`firstname` LIKE "%'.$searchStr.'%" OR t.`surname` LIKE "%'.$searchStr.'%"  OR t.`id`="'.$searchStr.'" ) ';
            }
            $arrPageData['filter_url'] .= '&filters[title]='.$filters['title'];
        }
        
        if(!empty($filters['status'])){
            $where_status = '';
            foreach($filters['status'] as $status){
                $where_status .= ($where_status ? ' OR ' : ' ').' t.`status`='.$status;
                $arrPageData['filter_url'] .= '&filters[status][]='.$status;
            }
            $where .= ($where ? ' AND ' : ' WHERE ').' ('.$where_status.') ';
        }
        
        if(!empty($filters['type'])){
            $where .= ($where ? ' AND ' : ' WHERE ').' t.`type`="'.$filters['type'].'" ';
        }
        
        if(!empty($filters['date']) && ($filters['date']['from'] || $filters['date']['to'])){
            $from = $filters['date']['from'] ? date("Y-m-d", strtotime($filters['date']['from'])) : '0';
            $to = $filters['date']['to'] ? date("Y-m-d", strtotime($filters['date']['to'])) : date("Y-m-d");
            $where .= ($where ? ' AND ' : ' WHERE ').' (t.`created` BETWEEN "'.$from.'" AND  "'.$to.'") ';
            $arrPageData['filter_url'] .= '&filters[date][from]='.$filters['date']['from'];
            $arrPageData['filter_url'] .= '&filters[date][to]='.$filters['date']['to'];
        }
    }


    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(
            array('default'=>HEAD_LINK_SORT_DEFAULT, 'firstname'=>'Имени', 'status'=>'Статус', 'created'=>'По дате'),
            $arrOrder['get'], $arrPageData['admin_url'].$arrPageData['filter_url'], 'pageorder', '_');
   
    // Total pages and Pager
    $orders_url = '';
    if($arrPageData['orders_url']) {
        foreach($arrPageData['orders_url'] as $k=> $value){
            $orders_url .= '&'.$k.'='.$value;
        }
    }
    
    $arrPageData['total_items'] = intval(getValueFromDB(ORDERS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url'].$orders_url);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? 't.'.implode(', t.', $arrOrder['mysql']) : "t.`created` DESC, t.`id`");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query  = "SELECT t.* FROM `".ORDERS_TABLE."` t $where $order $limit";
    $result = mysql_query($query);
    if(!$result) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    } else {
        while ($row = mysql_fetch_assoc($result)) {
            $row['title'] = $row['firstname'].' '.$row['surname'];
            $items[]      = $row;
        }
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
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

