<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$pages_all = !empty($_GET['pages'])  ? trim(addslashes($_GET['pages']))  : false;
$action    = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : false;
$itemID    = $UrlWL->getItemId();
$item      = array(); // Item Info Array
$items     = array(); // Items Array of items Info arrays
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// Manipulation with Page Number
if ($page > 1) {
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
}
// Manipulation with Show Pages All Session Var
if ($pages_all) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['action']        = & $action;
$arrPageData['pagesall']      = & $pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['def_img_param'] = array('w'=>184, 'h'=>184);
$arrPageData['images_params'] = false; //array(array("small_", 120, 100)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
$arrPageData['items_on_page'] = 10;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// User Access verification
if(!$objUserInfo->logined){ 
    Redirect($UrlWL->buildCategoryUrl($arrModules['register']));
}
//Check if user logined. if no - redirect to user view page
if($itemID && $action=='edit' && !$objUserInfo->logined){ 
    Redirect($UrlWL->buildItemUrl($arCategory, array('id'=>$objUserInfo->$itemID, 'seo_path'=>UrlWL::USER_SEOPREFIX.$itemID))); 
}
// User verification
if($itemID && $action=='edit' && $objUserInfo->logined && $itemID!=$objUserInfo->id && $objUserInfo->type!=USER_TYPE_ADMINISTRATOR ){ 
    $itemID = $objUserInfo->id;
}
// Edit and View User Data Limit For Registered User
if((!$itemID || $itemID!=$objUserInfo->id) && !in_array($objUserInfo->type, array(USER_TYPE_ADMINISTRATOR, USER_TYPE_USER))){ 
    Redirect($UrlWL->buildItemUrl($arCategory, array('id'=>$objUserInfo->id, 'seo_path'=>UrlWL::USER_SEOPREFIX.$objUserInfo->id)));
}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Insert Item in Database
if($itemID && $action=='edit' && !empty($_POST)) {
    $_POST['pass']     = trim($_POST['pass']);
    $_POST['confpass'] = trim($_POST['confpass']);
    $_POST['currpass'] = trim($_POST['currpass']);

    // Required: captcha[code], address, city, phone, surname, firstname, currpass, confpass, pass, email
    $Validator->validateEmail($_POST['email'], 'Введите, пожалуйста, корректный Ваш электронный адрес!');
    $Validator->validateGeneral($_POST['firstname'], 'Введите пожалуйста Ваше Имя!');
    $Validator->validateGeneral($_POST['surname'], 'Введите пожалуйста Вашу фамилию!');
    $Validator->validatePhone($_POST['phone'], 'Введите пожалуйста Ваш телефон!');
    $Validator->validateGeneral($_POST['city'], 'Введите пожалуйста Ваш город!');
    $Validator->validateGeneral($_POST['address'], 'Введите пожалуйста Ваш адресс!');

    if ($objUserInfo->type!=USER_TYPE_ADMINISTRATOR)
        $Validator->validateGeneral($_POST['currpass'], 'Чтобы изменить данные, введите пожалуйста Ваш Текущий Пароль!');  
    if(!empty($_POST['currpass'])){
        $query = "SELECT `email` FROM `".USERS_TABLE."` WHERE `active`=1 AND `id`={$itemID} AND `pass`=MD5(CONCAT('".md5(addslashes($_POST['currpass']))."', `salt`)) LIMIT 1";
        $result = mysql_query($query);
        if(!mysql_num_rows($result)) {
            $Validator->addError("Вы ввели неверный текущий пароль!");
        }
    }
    if ($_POST['pass'] != $_POST['confpass'])
        $Validator->addError("Введите пожалуйста Ваш Пароль и Подтвердите пароль!");
    if(!empty($_POST['email']) && $_POST['email']!=$_POST['curremail']){
        $email = addslashes($_POST['email']);
        $query = "SELECT `email` FROM `".USERS_TABLE."` WHERE `email`='{$email}'";
        $result = mysql_query($query);
        if(mysql_num_rows($result)>0)
            $Validator->addError("Данный e-mail: $email уже кем то занят. Используйте ваш предыдущий e-mail.");
    }  

    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значения:  </font>".$Validator->getListedErrors();
    } else {
        $arUnusedKeys = array();
        $password     = !empty($_POST['pass']) ? md5(addslashes($_POST['pass'])) : false;
        $arPostData   = $_POST;
        
        if($password===false) $arUnusedKeys[] = 'pass';
        else {
            $arPostData['salt'] = salt();
            $arPostData['pass'] = md5($password.$arPostData['salt']);
        }
        $arPostData['subscribe']   = is_set($arPostData, 'subscribe');

        $arPostData['image']  = imageManipulation($itemID, USERS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);
        if(empty($arPostData['image'])) $arUnusedKeys[] = 'image';
        
        $result = $DB->postToDB($arPostData, USERS_TABLE, 'WHERE id='.$itemID, $arUnusedKeys, 'update');
        if($result){
            if($objUserInfo->id==$itemID){ // update data in Session if current id is Logined User
                $arUserInfo  = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.$itemID);
                $arUserInfo['password'] = ($password===false) ? $objUserInfo->password : $password;
                $arUserInfo['logined']  = 1;
                $objUserInfo = (object)$arUserInfo;
                setUserToSession($objUserInfo);
            }
            setSessionMessage('Данные успешно обновлены!');
            Redirect($UrlWL->buildItemUrl($arCategory, array('id'=>$itemID, 'seo_path'=>UrlWL::USER_SEOPREFIX.$itemID)));
        } else {
            $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            unlinkUnUsedImage($arPostData['image'], $arrPageData['files_url'], $arrPageData['images_params']);
        }
    }
}

// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
//$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="/js/jquery/jNice/jNice.css" />';
//$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/jNice/jquery.jNice.js"></script>';

// Item Detailed View
if($itemID) {
    $item = unScreenData(getItemRow('`'.USERS_TABLE.'` t LEFT JOIN `'.USERTYPES_TABLE.'` ljt ON ljt.name = t.type', 't.*, ljt.name_'.$lang.' as typename ', 'WHERE t.`active`=1 AND t.`id` = '.$itemID));
    if(!empty($item)) {
        
        if(!empty($_POST) && $action=='edit')
            $item = array_merge($item, $_POST);
        
        $item['fio']   = trim(trim("{$item['surname']} {$item['firstname']}")." {$item['middlename']}");
        $item['title'] = ($action=='edit' ? "Редактирование профиля пользователя " : "Профиль пользователя ").$item['fio'];

        // Set vars
        $arrPageData['headTitle']       = $item['title'];

        $item['arrFiles'] = array();
        $query = "SELECT * FROM `".USERFILES_TABLE."` WHERE `uid`='{$itemID}' ORDER BY `id`";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $row['small_image'] = (!empty($row['filename']) && is_file($arrPageData['files_path'].'small_'.$row['filename'])) ? $arrPageData['files_url'].'small_'.$row['filename'] : $arrPageData['files_url'].'small_noimage.jpg';
            $row['image']       = (!empty($row['filename']) && is_file($arrPageData['files_path'].$row['filename'])) ? $arrPageData['files_url'].$row['filename'] : $arrPageData['files_url'].'noimage.jpg';
            $item['arrFiles'][] = $row;
        }
        
        $item['seo_path']    = UrlWL::USER_SEOPREFIX.$item['id'];
        $item['imageName']   = $item['image'];
//        $item['small_image'] = (!empty($item['image']) && is_file($arrPageData['files_path'].'small_'.$item['image'])) ? $arrPageData['files_url'].'small_'.$item['image'] : $arrPageData['files_url'].'small_noimage.jpg';
        $item['image']       = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
    }

// List Items
} else {
    $query = 'SELECT t.*, ljt.name_'.$lang.' as typename, CONCAT("'.UrlWL::USER_SEOPREFIX.'", t.`id`) `seo_path`
                FROM `'.USERS_TABLE.'` t
                LEFT JOIN `'.USERTYPES_TABLE.'` ljt ON ljt.name = t.type ';
    $where = '  WHERE t.`active`=1'.((!$objUserInfo->logined || $objUserInfo->type!=USER_TYPE_ADMINISTRATOR) ? ' AND t.`isnew`=0 AND t.`type`="User" ' : '');

    if(!$pages_all){
        // Total pages and Pager
        $arrPageData['total_items'] = intval(getValueFromDB('`'.USERS_TABLE.'` t', 'COUNT(*)', $where, 'count'));
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildPagerUrl($arCategory));
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // END Total pages and Pager
    }

    $order  = '  ORDER by t.`surname`, t.`firstname` ';
    $limit  = $pages_all ? '' : "  LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $result = mysql_query($query.$where.$order.$limit) or die(strtoupper($module).' SELECT: ' . mysql_error());

    $items  = array();
    if(mysql_num_rows($result) > 0) {
        while ($item = mysql_fetch_assoc($result)) {
            $item['small_image'] = (!empty($item['image']) && is_file($arrPageData['files_path'].'small_'.$item['image'])) ? $arrPageData['files_url'].'small_'.$item['image'] : $arrPageData['files_url'].'small_noimage.jpg';
            $item['image']       = (!empty($item['image']) && is_file($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';

            $item['title']       = unScreenData(trim(trim("{$item['surname']} {$item['firstname']}")." {$item['middlename']}"));

            $items[] = $item;
        }
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

