<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID       = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$arrNewItems  = array(); // Items Array of New User items Info arrays
$arrUserTypes = getRowItems(USERTYPES_TABLE, '*,`name_'.$lang.'` as title', '`active`=1', 'id');
$hasAccess    = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['headTitle']     = USERS_TITLE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
$arrPageData['def_img_param'] = array('w'=>250, 'h'=>210);
$arrPageData['images_params'] = array(array("small_", 120, 100)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
$arrPageData['files_params']  = array(array("small_", 100, 100)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
$item_title = $itemID ? getValueFromDB(USERS_TABLE, 'CONCAT_WS(" ", `firstname`, `surname`)', 'WHERE `id`='.$itemID, 'title') : '';
if($itemID && $task=='deleteItem') {
    if($hasAccess) {
        $result = unlinkImage($itemID, USERS_TABLE, $arrPageData['files_path'], $arrPageData['images_params'], false);
        if($result===false) $arrPageData['errors'][] = '<font color="red">НЕ удалось удалить изображение с сервера</font>!';
        $result = deleteRecords(USERS_TABLE, 'WHERE `id`='.$itemID.' LIMIT 1');
        if($result===false) $arrPageData['errors'][] = '<p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        elseif($result){
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
// Activate Status Item
elseif($itemID && $task=='activateItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $arUser = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.$itemID);
        $result = updateRecords(USERS_TABLE, "`type`='User', `isnew`='".($_GET['status']?0:1)."', `active`='{$_GET['status']}'", 'WHERE `id`='.$itemID.' LIMIT 1');
        if($result===false) $arrPageData['errors'][]   = 'Пользователь <font color="red">НЕ был</font> активирован и новое состояние <font color="red">НЕ было</font> сохранено! Error Update: '. mysql_error();
        elseif($result)     $arrPageData['messages'][] = 'Пользователь был активирован и новое состояние сохранено!';
        if($result && $_GET['status'] && !empty($arUser)) {
            // Send Activation email to user
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Активирован пользователь "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
            $arUser['username'] = trim(trim("{$arUser['firstname']} {$arUser['middlename']}")." {$arUser['surname']}");
            $arUser['sitename'] = $objSettingsInfo->websiteName;
            $arUser['links']['recovery'] = 'http://'.$_SERVER['SERVER_NAME'].$UrlWL->buildCategoryUrl($arrModules['recovery']);
            $arUser['links']['login'] = 'http://'.$_SERVER['SERVER_NAME'].$UrlWL->buildCategoryUrl($arrModules['authorize']);
            $smarty->assign('arUser', $arUser);
            $to      = $arUser['username']." <{$arUser['email']}>";
            $from    = $objSettingsInfo->websiteName.' <'.$objSettingsInfo->ownerEmail.'>';
            $text    = $smarty->fetch('mail/register_success.tpl');
            $subject = $objSettingsInfo->websiteName.': Успешная регистрация. Модерация администратором пройдена';
            if(sendMail($to, $subject, $text, $from))
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отправлено уведомление об активации пользователю "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module'], $text);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(USERS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID.' LIMIT 1');
        if($result===false) $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        elseif($result) {
            $arrPageData['messages'][] = 'Новое состояние сохранено!';
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация пользователя на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
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
        $_POST['confpass']  = trim($_POST['confpass']);
        $_POST['email']     = trim($_POST['email']);

        $Validator->validateLogin($_POST['login'], 'Login (only latin and 3 or more chars)');
        $Validator->validateGeneral($_POST['firstname'], 'Firstname');
        $Validator->validateGeneral($_POST['surname'], 'Surname');
        $Validator->validateEmail($_POST['email'], 'E-mail (wrong)');

        if($itemID && $objUserInfo->type != USER_TYPE_ADMINISTRATOR)
            $Validator->validateGeneral($_POST['old_pass'], 'Old Password (Re-enter old password to applay)');
        if ($_POST['pass'] != $_POST['confpass']) 
            $Validator->addError("Your password was not confirmed properly. Please, insert password and confirmation again.");
        if(!$itemID){
            $Validator->validateGeneral($_POST['pass'], 'Password');
            $Validator->validateGeneral($_POST['confpass'], 'Confirm Password');
        }
        if(!empty($_POST['old_pass'])){
            $query = "SELECT `login` FROM `".USERS_TABLE."` WHERE `active`=1 AND `id`={$itemID} AND `pass`=MD5(CONCAT('".md5(addslashes($_POST['old_pass']))."', `salt`)) LIMIT 1";
            $result = mysql_query($query);
            if(!mysql_num_rows($result)) {
                $Validator->addError("Your enter wrong old password!");
            }
        }
        if(!empty($_POST['login']) && $_POST['login'] != $_POST['old_login']){
            $query = "SELECT `login` FROM `".USERS_TABLE."` WHERE `login`='{$_POST['login']}' LIMIT 1";
            $result = mysql_query($query);
            if(mysql_num_rows($result)>0) {
                $Validator->addError("Данный login <u>{$_POST['login']}</u> уже используется в системе!");
            }
        }
        if(!empty($_POST['email']) && $_POST['email'] != $_POST['old_email']){
            $query = "SELECT `email` FROM `".USERS_TABLE."` WHERE `email`='{$_POST['email']}' LIMIT 1";
            $result = mysql_query($query);
            if(mysql_num_rows($result)>0) {
                $Validator->addError("Данный email <u>{$_POST['email']}</u> уже используется в системе!");
            }
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $password = !empty($_POST['pass']) ? md5(addslashes($_POST['pass'])) : false;
            $arPostData = $_POST;
            $arPostData['image'] = imageManipulation($itemID, USERS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);

            if(empty($arPostData['image'])) $arUnusedKeys[] = 'image';
            if($password===false)           $arUnusedKeys[] = 'pass';
            else {
                $arPostData['salt'] = salt();
                $arPostData['pass'] = md5($password.$arPostData['salt']);
            }

            $result = $DB->postToDB($arPostData, USERS_TABLE, $conditions,  $arUnusedKeys, $query_type);
            if($result){
                if(!$itemID && $result && is_int($result)) $itemID = $result;
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(USERS_TABLE, 'CONCAT_WS(" ", `firstname`, `surname`)', 'WHERE `id`='.$itemID, 'title');
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                } 
                if($objUserInfo->id==$itemID){ // update data in Session if current id is Logined User
                    $arUserInfo  = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.$itemID);
                    $arUserInfo['password'] = ($password===false) ? $objUserInfo->password : $password;
                    $arUserInfo['logined']  = 1;
                    $objUserInfo = (object)$arUserInfo;
                    setUserToSession($objUserInfo);
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
    $query = "SELECT u.*, ut.`name_{$lang}` as typename FROM ".USERS_TABLE." u LEFT JOIN ".USERTYPES_TABLE." ut ON u.`type`=ut.`name` WHERE u.`id`=$itemID LIMIT 1";
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
 
    }
}

if(!empty($_POST)) $item = array_merge($item, $_POST);

// //////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// New User List Data
$query = "SELECT u.* FROM ".USERS_TABLE." u WHERE u.`isnew`=1 AND u.`type`!='Registered' ORDER BY u.`created` DESC";
$result = mysql_query($query);
if(!$result) $arrPageData['errors'][] = "SELECT NEW Users OPERATIONS: " . mysql_error();
else {
    while ($row = mysql_fetch_assoc($result)) {
        $arrNewItems[]    = $row;
    }
}

// //////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Display User List Data
$where = "WHERE u.`isnew`=0 AND u.`type`!='Registered' ";

// Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(USERS_TABLE." u", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager

$order = "";
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

$query = "SELECT u.*, ut.`name_{$lang}` as typename FROM ".USERS_TABLE." u LEFT JOIN ".USERTYPES_TABLE." ut ON u.`type`=ut.`name` $where $order $limit";
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
$smarty->assign('arrNewItems',  $arrNewItems);
$smarty->assign('arrUserTypes', $arrUserTypes);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(40) NOT NULL,
  `pass` varchar(32) DEFAULT NULL,
  `salt` varchar(7) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'Registered',
  `firstname` varchar(32) DEFAULT NULL,
  `middlename` varchar(63) DEFAULT NULL,
  `surname` varchar(32) DEFAULT NULL,
  `info` text,
  `descr` text,
  `bdate` date DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `skype` varchar(32) DEFAULT NULL, 
  `icq` varchar(20) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `confirmcode` varchar(50) DEFAULT NULL,
  `checkword` varchar(50) DEFAULT NULL,
  `checkword_time` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_login` (`login`),
  UNIQUE KEY `udx_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 */