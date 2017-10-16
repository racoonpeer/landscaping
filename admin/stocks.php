<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

if(!$arrPageData['moduleRootID']){
    $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_ID_ERROR, STOCKS, $arrPageData['module']);
    $arrPageData['module']      = 'module_messages';
    $arrPageData['moduleTitle'] = STOCKS;
    return;
} else {
    foreach($arAcceptLangs as $ln){
        $dbTable = replaceLang($ln, STOCKS_TABLE);
        if(!$DB->isSetDBTable($dbTable)){
            $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_TABLE_ERROR, STOCKS, $arrPageData['module'], $dbTable);
            $arrPageData['module']      = 'module_messages';
            $arrPageData['moduleTitle'] = STOCKS;
            return;
        }
    }
}

// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) AND intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) AND intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 

$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['page_url'];
$arrPageData['arrBreadCrumb'] = getBreadCrumb($arrPageData['moduleRootID'], 1);
$arrPageData['arrParent']     = getItemRow(MAIN_TABLE, '*', 'WHERE id='.$arrPageData['moduleRootID']);
$arrPageData['headTitle']     = STOCKS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
// available related categories
$arrPageData['arRelatedCats'] = getComplexRowItems(MAIN_TABLE.' m', 'm.*', 'WHERE m.`module`="catalog" AND m.`pid`>0', 'm.`order`');
if(!empty($arrPageData['arRelatedCats'])) {
    foreach ($arrPageData['arRelatedCats'] as $key => $value) {
        $arrPageData['arRelatedCats'][$key]['children'] = getComplexRowItems(CATALOG_TABLE.' c', 'c.*', 'WHERE c.`cid`='.$arrPageData['arRelatedCats'][$key]['id']);
        if(empty($arrPageData['arRelatedCats'][$key]['children'])) {
            unset($arrPageData['arRelatedCats'][$key]);
        }
    }
    ksort($arrPageData['arRelatedCats']);
}

$item_title = $itemID ? getValueFromDB(STOCKS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
// SET Reorder
if($task=='reorderItems' AND !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', STOCKS_TABLE,  array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        elseif($result)    $arrPageData['errors'][] = $result;
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID AND $task=='deleteItem') {
    if($hasAccess) {
        PHPHelper::deleteImages($itemID, $arrPageData['files_path'], $arrPageData['module']);
        $result = deleteDBLangsSync(STOCKS_TABLE, ' WHERE id='.$itemID);
        if(!$result)    $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        elseif($result) {
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            Redirect($arrPageData['current_url']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID AND $task=='publishItem' AND isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(STOCKS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        elseif($result) {
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
//Copy item
elseif($copyID AND $task=='addItem'){
    if($hasAccess) {
        $arrPageData['messages'][] = 'Запись успешно скопирована!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif(!empty($_POST) AND ($task=='addItem' || $task=='editItem')) {
    if($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // SEO path manipulation
            $_POST['seo_path'] = $UrlWL->strToUniqueUrl($DB, (empty($_POST['seo_path']) ? $_POST['title'] : $_POST['seo_path']), $module, STOCKS_TABLE, $itemID, empty($itemID));
            // copy post data
            $arPostData = $_POST;
            imageManipulationWithCrop($arPostData, $arUnusedKeys, $arrPageData['files_url'], $arrPageData['files_path'], $task, $itemID, $module);
            if(empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if(empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
            if(!empty($arPostData['date_start'])) $arPostData['date_start'] = date('Y-m-d H:i:s', strtotime($arPostData['date_start']));
            if(!empty($arPostData['date_end'])) $arPostData['date_end'] = date('Y-m-d H:i:s', strtotime($arPostData['date_end']));
            $result = $DB->postToDB($arPostData, STOCKS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result){
                if (!$itemID AND $result AND is_int($result)) $itemID = $result;
                // operation with related & similar products
                deleteRecords(STOCKS_RELATED_TABLE, 'WHERE `pid`='.$itemID);
                if (!empty($arPostData['related'])) {
                    $arRelated = explode(',', $arPostData['related']);
                    foreach ($arRelated as $value) {
                        $arData = array(
                            'pid'       => $itemID,
                            'rid'       => $value,
                            'type'      => 0
                        );                            
                        $DB->postToDB($arData, STOCKS_RELATED_TABLE);
                    }
                }
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(STOCKS_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                } 
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) AND $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';

if($task=='addItem' || $task=='editItem'){
    if(!$itemID){
        if($hasAccess) {
            if($copyID){
                $item = getSimpleItemRow($copyID, STOCKS_TABLE);
                $item = array_merge($item, array('id'=>'', 'image'=>'', 'seo_path'=>''));
                // related & similar items
                $item['related'] = array();
                $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.STOCKS_RELATED_TABLE.'` sr ';
                $query .= 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id` = sr.`rid`) LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                $query .= 'WHERE sr.`pid`='.$copyID.' ';
                $query .= 'ORDER BY sr.`id`';
                $result = mysql_query($query);
                if(mysql_num_rows($result) > 0) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $item['related'][] = $row;
                    }
                }
            } else {    
                $item = array_combine_multi($DB->getTableColumnsNames(STOCKS_TABLE), '');
                $item['related'] = array();
            }
            $item['order']  = getMaxPosition(0, 'order', false, STOCKS_TABLE);
            $item['active'] = 1;
            $item['createdDate'] = date('Y-m-d');
            $item['createdTime'] = date('H:i:s');
            $item['arImageData'] = array();
            $item['arHistory']   = array();
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif($itemID) {
        $query = "SELECT * FROM ".STOCKS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif(!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            
            // related & similar items
            $item['related'] = array();
            $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.STOCKS_RELATED_TABLE.'` sr ';
            $query .= 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id` = sr.`rid`) LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
            $query .= 'WHERE sr.`pid`='.$itemID.' ';
            $query .= 'ORDER BY sr.`id`';
            $result = mysql_query($query);
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $item['related'][] = $row;
                }
            }
            
            $item['arImageData'] = $item['image'] ? getArrImageSize($arrPageData['files_url'], $item['image']) : array();
            $item['arHistory']   = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    $item['arImagesSettings'] = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arrPageData['module'].'"');
    if(!empty($_POST)) $item = array_merge($item, $_POST);

    // Include Need CSS and Scripts For This Page To Array
    $arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
    $arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.admin.js" type="text/javascript"></script>';
    $arrPageData['headCss'][]       = '<link href="/js/jquery/themes/base/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.widget.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.datepicker.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/1251/jquery.ui.datepicker-ru.js" type="text/javascript"></script>';

    $arrPageData['arrBreadCrumb'][] = array('title'=>($task=='addItem' ? ADMIN_ADD_NEW_PAGE : ADMIN_EDIT_PAGE));

} else {
    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(
            array('default'=>HEAD_LINK_SORT_DEFAULT, 'title'=>HEAD_LINK_SORT_TITLE, 'created'=>HEAD_LINK_SORTDATEADD),
            $arrOrder['get'], $arrPageData['admin_url'].$arrPageData['category_url'], 'pageorder', '_');

    // Display Items List Data
    $where = "";

    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(STOCKS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "t.order, t.id");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query  = "SELECT t.* FROM `".STOCKS_TABLE."` t $where $order $limit";
    $result = mysql_query($query);
    if (!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            $row["outdated"] = ($row["date_end"] < date("Y-m-d")) ? true : false;
            $items[] = $row;
        }
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
CREATE TABLE `ru_stocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` text,
  `fulldescr` text,
  `image` varchar(255) DEFAULT NULL,
  `relations` varchar(255) NOT NULL DEFAULT '',
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` varchar(63) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_rel` (`relations`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_start` (`date_start`),
  KEY `idx_end` (`date_end`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
 */