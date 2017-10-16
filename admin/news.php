<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\
if(!$arrPageData['moduleRootID']){
    $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_ID_ERROR, NEWS, $arrPageData['module']);
    $arrPageData['module']      = 'module_messages';
    $arrPageData['moduleTitle'] = NEWS;
    return;
} else {
    foreach($arAcceptLangs as $ln){
        $dbTable = replaceLang($ln, NEWS_TABLE);
        if(!$DB->isSetDBTable($dbTable)){
            $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_TABLE_ERROR, NEWS, $arrPageData['module'], $dbTable);
            $arrPageData['module']      = 'module_messages';
            $arrPageData['moduleTitle'] = NEWS;
            return;
        }
    }
}
// /////////////////////// END MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) && intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$cid           = (isset($_GET['cid']) && intval($_GET['cid']))       ? intval($_GET['cid'])    : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$categoryTree  = getCategoriesTreeWithItems($lang, NEWS_TABLE, $arrPageData['moduleRootID'], 0, false);
$arCidCntItems = getItemsCountInCategories('cid', 'count', NEWS_TABLE, '`cid`,COUNT(`cid`) as count', 'WHERE `active`=1 GROUP BY `cid`');
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['cid']           = $cid; //= ((!$cid && !empty($categoryTree)) ? $categoryTree[0]['id'] : (!$cid ? $arrPageData['moduleRootID'] : $cid));
$arrPageData['category_url']  = $cid ? '&cid='.$cid : '';
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['page_url'];
$arrPageData['arrBreadCrumb'] = getBreadCrumb($cid, 1);
$arrPageData['arrParent']     = getItemRow(MAIN_TABLE, '*', 'WHERE id='.$cid);
$arrPageData['headTitle']     = NEWS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
$item_title = $itemID ? getValueFromDB(NEWS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
// SET Reorder
if($task=='reorderItems' && !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', NEWS_TABLE,  array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        elseif($result)    $arrPageData['errors'][] = $result;
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess) {
      //  unlinkImageLangsSynchronize($itemID, NEWS_TABLE, $arrPageData['files_path'], $arrPageData['images_params']);
        PHPHelper::deleteImages($itemID, $arrPageData['files_path'], $arrPageData['module']);
        $result = deleteDBLangsSync(NEWS_TABLE, ' WHERE id='.$itemID);
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
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(NEWS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
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
elseif($copyID && $task=='addItem'){
    if($hasAccess) {
        $arrPageData['messages'][] = 'Запись успешно скопирована!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif(!empty($_POST) && ($task=='addItem' || $task=='editItem')) {
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
            $_POST['seo_path'] = $UrlWL->strToUniqueUrl($DB, (empty($_POST['seo_path']) ? $_POST['title'] : $_POST['seo_path']), $module, NEWS_TABLE, $itemID, empty($itemID));
            // copy post data
            $arPostData = $_POST;
//            $arPostData['image'] = imageManipulation($itemID, NEWS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);
            imageManipulationWithCrop($arPostData, $arUnusedKeys, $arrPageData['files_url'], $arrPageData['files_path'], $task, $itemID, $module);
            if($cid) $arPostData['cid'] = $cid;
            if(empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if(empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";

            $result = $DB->postToDB($arPostData, NEWS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if($result){
                if(!$itemID && $result && is_int($result)) $itemID = $result;
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(NEWS_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                } 
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
              //  unlinkUnUsedImage($arPostData['image'], $arrPageData['files_url'], $arrPageData['images_params']);
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}

// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';

if ($task=='addItem' or $task=='editItem'){
    if (!$itemID){
        if ($hasAccess) {
            if ($copyID) {
                $item = getSimpleItemRow($copyID, NEWS_TABLE);
                $item = array_merge($item, array('id'=>'', 'image'=>'', 'seo_path'=>''));
            } else {    
                $item = array_combine_multi($DB->getTableColumnsNames(NEWS_TABLE), '');
            }
            $item['order']       = getMaxPosition($cid, 'order', 'cid', NEWS_TABLE);
            $item['active']      = 1;
            $item['createdDate'] = date('Y-m-d');
            $item['createdTime'] = date('H:i:s');
            $item['arHistory']   = array();
            $item['imagesCount'] = 0;
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif ($itemID) {
        $query = "SELECT * FROM ".NEWS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif (!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arImageData'] = $item['image'] ? getArrImageSize($arrPageData['files_url'], $item['image']) : array();
            $item['arHistory']   = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
            $item['imagesCount'] = mysql_num_rows(mysql_query('SELECT * FROM '.NEWSFILES_TABLE.' WHERE `pid`='.$item['id'].' AND `active`=1 ORDER BY `fileorder`'));
        }
    }
    $item['arImagesSettings'] = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arrPageData['module'].'"');
    if (!empty($_POST)) $item = array_merge($item, $_POST);

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
    $where = $cid ? "WHERE t.cid = $cid " : "";

    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(NEWS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "t.order, t.id");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query  = "SELECT t.*, m.`title` as category FROM `".NEWS_TABLE."` t LEFT JOIN `".MAIN_TABLE."` m ON t.`cid`=m.`id` $where $order $limit";
    $result = mysql_query($query);
    if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            $items[]           = $row;
        }
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
$smarty->assign('categoryTree',  $categoryTree);
$smarty->assign('arCidCntItems', $arCidCntItems);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `ru_news`;
CREATE TABLE IF NOT EXISTS `ru_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `descr` tinytext,
  `fulldescr` text,
  `image` varchar(100) DEFAULT NULL,
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` varchar(63) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cid` (`cid`),
  KEY `idx_title` (`title`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 */