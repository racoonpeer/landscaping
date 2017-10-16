<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID         = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$item           = array(); // Item Info Array
$items          = array(); // Items Array of items Info arrays
$userType       = 'Registered';
$arFilters      = !empty($_GET['arFilters'])? $_GET['arFilters']: false;
$hasAccess      = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['headTitle']     = USERS_TITLE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.'users/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
$arrPageData['def_img_param'] = array('w'=>200, 'h'=>200);
$arrPageData['images_params'] = array(array("small_", 40, 40)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
$arrPageData['files_params']  = array(array("small_", 40, 40)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
$arrPageData['filter_url']    = $arrPageData['admin_url'];
$arrPageData['filters']       = $arFilters;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
$item_title = $itemID ? getValueFromDB(USERS_TABLE, 'CONCAT_WS(" ", `firstname`, `surname`)', 'WHERE `id`='.$itemID, 'title') : '';
if($itemID && $task=='deleteItem') {
    if($hasAccess) {
        $result = unlinkImage($itemID, USERS_TABLE, $arrPageData['files_path'], $arrPageData['images_params'], false);
        if($result===false) {
            $arrPageData['errors'][] = '<font color="red">НЕ удалось удалить изображение с сервера</font>!';
        }
        $result = deleteRecords(USERS_TABLE, 'WHERE `id`='.$itemID.' LIMIT 1');
        if($result===false) {
            $arrPageData['errors'][] = '<p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        } elseif($result) {
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            unlinkImages($arrPageData['files_path'], USERFILES_TABLE, 'filename', "WHERE `uid`='$itemID'", '', $arrPageData['files_params'], false, false);
            deleteRecords(USERFILES_TABLE, 'WHERE `uid`='.$itemID);
            Redirect($arrPageData['admin_url'].$arrPageData['page_url']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(USERS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID.' LIMIT 1');
        if($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация пользователя на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
            $arrPageData['messages'][] = 'Новое состояние сохранено!';
        }
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

        $_POST['login']     = trim($_POST['login']);
        $_POST['pass']      = trim($_POST['pass']);
        $_POST['email']     = trim($_POST['email']);

        $Validator->validateGeneral($_POST['firstname'], 'Firstname');
        $Validator->validateGeneral($_POST['surname'], 'Surname');


        if(!$itemID || ($itemID && !empty($_POST['pass']))){
            $Validator->validateGeneral($_POST['pass'], 'Password');
        } else {
            $arUnusedKeys[] = 'pass';
        }
        if(!empty($_POST['login'])){
            $Validator->validateLogin($_POST['login'], 'Login (only latin and 3 or more chars)');
            $query = "SELECT `login` FROM `".USERS_TABLE."` WHERE `login`='{$_POST['login']}' LIMIT 1";
            $result = mysql_query($query);
            if(mysql_num_rows($result)>0) {
                $arUnusedKeys[] = 'login';
            }
        } else {
            $arUnusedKeys[] = 'login';
        }
        if(!empty($_POST['email'])){
            $Validator->validateEmail($_POST['email'], 'E-mail (wrong)');
            $query = "SELECT `email` FROM `".USERS_TABLE."` WHERE `email`='{$_POST['email']}' LIMIT 1";
            $result = mysql_query($query);
            if(mysql_num_rows($result)>0) {
                $Validator->addError("Данный email <u>{$_POST['email']}</u> уже используется в системе!");
            }
        } else {
            $arUnusedKeys[] = 'email';
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $password = !empty($_POST['pass']) ? md5(addslashes($_POST['pass'])) : false;
            $arPostData = $_POST;
            $arPostData['image'] = imageManipulation($itemID, USERS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);

            if(empty($arPostData['image'])) {
                $arUnusedKeys[] = 'image';
            }
            if($password===false) {
                $arUnusedKeys[] = 'pass';
            }
            else {
                $arPostData['salt'] = salt();
                $arPostData['pass'] = md5($password.$arPostData['salt']);
            }

            $result = $DB->postToDB($arPostData, USERS_TABLE, $conditions,  $arUnusedKeys, $query_type);
            if($result){
                if(!$itemID && $result && is_int($result)) {
                    $itemID = $result;
                }
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(USERS_TABLE, 'CONCAT_WS(" ", `firstname`, `surname`)', 'WHERE `id`='.$itemID, 'title');
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                } 
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['admin_url'].$arrPageData['page_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
                unlinkUnUsedImage($arPostData['image'], $arrPageData['files_url'], $arrPageData['images_params']);
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
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide-full.packed.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/langs/'.$lang.'.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/libs/highslide/highslide.config.admin.js" type="text/javascript"></script>';

if(!$itemID){
    $item = array_combine_multi($DB->getTableColumnsNames(USERS_TABLE), '');
    $item['arHistory'] = array();
} elseif($itemID) {
    $query = "SELECT u.* FROM ".USERS_TABLE." u WHERE u.`id`=$itemID AND u.`type`='{$userType}' LIMIT 1";
    $result = mysql_query($query);
    if(!$result)
        $arrPageData['errors'][] = "SELECT User OPERATIONS: " . mysql_error();
    elseif(!mysql_num_rows($result))
        $arrPageData['errors'][] = "SELECT User OPERATIONS: No this Item in DataBase";
    else {
        $item = mysql_fetch_assoc($result);
        $item['arImageData'] = getArrImageSize($arrPageData['files_url'], $item['image']);
        $item['arrFiles']    = getRowItems(USERFILES_TABLE, '*', '`uid`='.$itemID, '`id`');
        $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
 
        // Customer orders
        $item['orders'] = array();
        $select = 'SELECT o.*, os.`title` FROM `'.ORDERS_TABLE.'` o ';
        $join   = 'LEFT JOIN `'.ORDER_STATUS_TABLE.'` os ON(os.`id` = o.`status`) ';
        $where  = 'WHERE o.`uid`='.$itemID.' ';
        $order  = 'GROUP BY o.`id` ORDER BY o.`created` DESC';
        $query  = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $row['products'] = getArrValueFromDB(ORDER_PRODUCTS_TABLE, '`title`', 'WHERE `oid`='.$row['id'], 'ttx');
                $item['orders'][] = $row;
            }
        }
        
        // Customer comments
        $item['comments'] = array();
        $select = 'SELECT c.* FROM `'.COMMENTS_TABLE.'` c ';
        $where  = 'WHERE c.`uid`='.$itemID.' ';
        $order  = 'ORDER BY c.`created` DESC';
        $query  = $select.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $row['descr'] = (!empty($row['descr']))? substr($row['descr'], 0, 255): '';
                $item['comments'][] = $row;
            }
        }
    }
}

if(!empty($_POST)) $item = array_merge($item, $_POST);

$where = "WHERE u.`type`='{$userType}'";

if($arFilters) {
    if(!empty($arFilters['firstname'])) {
        $where .= 'AND u.`firstname` LIKE "%'.trim(urldecode($arFilters['firstname'])).'%" ';
    }
    if(!empty($arFilters['surname'])) {
        $where .= 'AND u.`surname` LIKE "%'.trim(urldecode($arFilters['surname'])).'%" ';
    }
    if(!empty($arFilters['network'])) {
        $where .= 'AND u.`network` LIKE "%'.trim(urldecode($arFilters['network'])).'%" ';
    }
    
    $arrPageData['filters'] = $arFilters;
    
    foreach ($arFilters as $key => $value) {
        $arrPageData['filter_url'] .= '&arFilters['.$key.']='.$value;
    }
} else {
    $arrPageData['filter_url'] = '';
}

// Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(USERS_TABLE." u", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager

$order = "";
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

$query = "SELECT u.* FROM ".USERS_TABLE." u $where $order $limit";
$result = mysql_query($query);
if(!$result) $arrPageData['errors'][] = "SELECT Users OPERATIONS: " . mysql_error();
else {
    while ($row = mysql_fetch_assoc($result)) {
        $items[]    = $row;
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
$smarty->assign('items',        $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################