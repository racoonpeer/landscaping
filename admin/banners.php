<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/Banners.php');
Banners::deActivatePositions(array(1,3,4));

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = !empty($_GET['itemID'])  ? intval($_GET['itemID'])      : 0;
$copyID       = (isset($_GET['copyID']) && intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$posid         = !empty($_GET['posid'])   ? intval($_GET['posid'])       : 0;
$modname       = !empty($_GET['modname']) ? addslashes($_GET['modname']) : '';
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$categoryTree  = getCategoriesTree($lang, 0, 0, false);
$hasAccess    = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = & $itemID;
$arrPageData['posid']         = & $posid;
$arrPageData['posid_url']     = $posid ? '&posid='.$posid : '';
$arrPageData['modname']       = & $modname;
$arrPageData['modname_url']   = $modname ? '&modname='.$modname : '';
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['posid_url'].$arrPageData['modname_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = BANNERS_TITLE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['arTargets']     = Banners::getListTargets();
$arrPageData['arModules']     = Banners::getListModules();
$arrPageData['arPositions']   = Banners::getListPositions();
$arrPageData['files_url']     = Banners::getFolderURL();
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
$item_title = $itemID ? getValueFromDB(BANNERS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
// SET Reorder
if($task=='reorderItems' && !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', BANNERS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) $arrPageData['messages'][] = 'Новая сортировка елементов успешно сохранена!';
        elseif($result)    $arrPageData['errors'][] = $result;
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError();
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess) {
     //   unlinkImageLangsSynchronize($itemID, BANNERS_TABLE, $arrPageData['files_path'], $arrPageData['images_params']);
        PHPHelper::deleteImages($itemID, $arrPageData['files_path'], $arrPageData['module']);
        $result = deleteDBLangsSync(BANNERS_TABLE, ' WHERE id='.$itemID);
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
        $result = updateRecords(BANNERS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
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
        $query_type   = $itemID ? 'update'              : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия блока!!!');
        $Validator->validateGeneral($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');

        if(empty($_POST['position']))   $Validator->addError('Вы не выбрали позицию блока!!!');
        if(empty($_POST['module']))     $Validator->addError('Вы не выбрали модуль блока!!!');

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {        
            $arPostData = $_POST;

            $arPostData['cids']  = empty($arPostData['cids']) ? 'all' : implode(',', $arPostData['cids']);
           // $arPostData['image'] = imageManipulation($itemID, BANNERS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);

            if(isset($arPostData['redirectype'])) $arPostData['redirectid'] = 0;
            else                                  $arPostData['redirecturl'] = '';

            if(!empty($arPostData['reset']))  foreach($arPostData['reset'] as $colname=>$val) $arPostData[$colname]=0;

            if(empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if(empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";

            if(!empty($arPostData['customcode']) && $arPostData['module']=='image'){
                $arPostData['customcode'] = 'NULL';
            } else if(isset($arPostData['upimage']) && $arPostData['module']=='text'){
                $arPostData['image'] = 'NULL';
             //  unlinkUnUsedImage($arPostData['upimage'], $arrPageData['files_url'], $arrPageData['images_params']);
            }

            imageManipulationWithCrop($arPostData, $arUnusedKeys, $arrPageData['files_url'], $arrPageData['files_path'], $task, $itemID, $module);

            $result = $DB->postToDB($arPostData, BANNERS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if($result){
                if(!$itemID && $result && is_int($result)) $itemID = $result;
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(BANNERS_TABLE, 'title', 'WHERE `id`='.$itemID);
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
             //   unlinkUnUsedImage($arPostData['image'], $arrPageData['files_url'], $arrPageData['images_params']);
            }
        }
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


if($task=='addItem' || $task=='editItem'){
    if(!$itemID){
        if($hasAccess) {
            if($copyID){
                $item = getSimpleItemRow($copyID, BANNERS_TABLE);
                $item = array_merge($item, array('id'=>'', 'image'=>''));
            } else {    
                $item = array_combine_multi($DB->getTableColumnsNames(BANNERS_TABLE), '');
            }
            $item['order']  = getMaxPosition($posid, 'order', 'position', BANNERS_TABLE);
            $item['active'] = 1;
            $item['createdDate'] = date('Y-m-d');
            $item['createdTime'] = date('H:i:s');
            $item['arHistory'] = array();
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif($itemID) {
        $query = "SELECT * FROM ".BANNERS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result)
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        elseif(!mysql_num_rows($result))
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        else {
            $item = mysql_fetch_assoc($result);
            
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arImageData'] = $item['image'] ? getArrImageSize($arrPageData['files_url'], $item['image']) : array();
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    
    $item['cids'] = (empty($item['cids']) || $item['cids']=='all') ? array() : explode(',', $item['cids']);
    $item['arImagesSettings'] = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arrPageData['module'].'"');
    
    if(!empty($_POST)) $item = array_merge($item, $_POST);

    // Include Need CSS and Scripts For This Page To Array
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
            $arrOrder['get'], $arrPageData['admin_url'].$arrPageData['posid_url'].$arrPageData['modname_url'], 'pageorder', '_');

    // Display Items List Data
    if($posid)   $where[] = "t.position=$posid";
    if($modname) $where[] = "t.module='$modname'";
    $where = !empty($where) ? 'WHERE '. implode(' AND ', $where) : '';
    
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(BANNERS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['posid_url'].$arrPageData['modname_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? 't.'.implode(', t.', $arrOrder['mysql']) : "t.position, t.order");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query = "SELECT t.* FROM `".BANNERS_TABLE."` t $where $order $limit";
    $result = mysql_query($query);
    if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            $row['mtitle']     = $arrPageData['arModules'][$row['module']];
            $row['ptitle']     = $arrPageData['arPositions'][$row['position']];
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
$smarty->assign('Banners',       new Banners($UrlWL));
$smarty->assign('categoryTree',  $categoryTree);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

