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
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$objCurrency   = getCurrencyInfo();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = CURRENCY.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 50;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
$item_title = $itemID ? getValueFromDB(CURRENCY_INFO_TABLE, 'name', 'WHERE `cid`='.$itemID) : '';
// SET Reorder
if($task=='reorderItems' && !empty($_POST)) {
    if($hasAccess) {
        $updated = 0;
        if(!empty($_POST['arItems'])) {
            foreach($_POST['arItems'] as $id=>$arData) {
                $arData['rate']     = str_replace(',', '.', $arData['rate']);
                $arData['active']   = (int)(isset($arData['active']));
                $arData['def4calc'] = (int)(isset($_POST['def4calc']) && $_POST['def4calc']==$id);
                $arData['def4show'] = (int)(isset($_POST['def4show']) && $_POST['def4show']==$id);
                $result = $DB->postToDB($arData, CURRENCY_TABLE, 'WHERE `id`='.$id, array(), 'update');
                if(!$result) $arrPageData['errors'][] = 'Запись ID = '.$id.' <font color="red">НЕ была сохранена</font>!';
                else if(mysql_affected_rows()) {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменены данные', $lang, $item_title, $id, $arrPageData['module']);
                }
                else $updated++;
            }
        }
        if($updated) $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess) {
        if($itemID==$objCurrency->def4calc || $itemID==$objCurrency->def4show){
            $arrPageData['errors'][] = 'Нельзя удалять валюту, которая выбрана "'.HEAD_DEFAULT.'" или имеет значение "'.HEAD_RATE.'" равным 1!';
        } else {
            $result = deleteRecords(CURRENCY_TABLE, ' WHERE `id`='.$itemID);
            if($result)     $result = deleteDBLangsSync(CURRENCY_INFO_TABLE, ' WHERE `cid`='.$itemID);
            if(!$result)    $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
            elseif($result) {
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
                Redirect($arrPageData['current_url']);
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif(!empty($_POST) && ($task=='addItem' || $task=='editItem')) {
    if($hasAccess) {
        if(!$itemID){
            if($objCurrency->count){
                if($_POST['def4calc'] && $objCurrency->def4calc )
                    $Validator->addError('Валюта "'.HEAD_DEFAULT.'" уже выбрана. Нельзя добавлять валюту "'.HEAD_DEFAULT.'" больше 1-й!');
                elseif(!$_POST['active'] && ($_POST['def4calc'] || $_POST['def4show']) )
                    $Validator->addError('Нельзя добавлять неактивную валюту если она выбрана "'.HEAD_DEFAULT.'"!');
                elseif($_POST['def4calc'] && $_POST['rate']!=1 )
                    $Validator->addError('Данная валюта выбрана "'.HEAD_DEFAULT.'", поэтому значение "'.HEAD_RATE.'" должно быть равным 1!');
            // IF IT IS First Currency
            } else $_POST['def4calc'] = $_POST['def4show'] = $_POST['nominal'] = $_POST['rate'] = $_POST['order'] = $_POST['active'] = 1;
            $item['arHistory'] = array();
        }

        if($task=='addItem'){
            $Validator->validateNumber($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');
            $Validator->validateNumber($_POST['nominal'], 'Вы не ввели "'.HEAD_NOMINAL.'"!!!');
            $Validator->validateNumber($_POST['rate'], 'Вы не ввели "'.HEAD_RATE.'"!!!');
        }
        $Validator->validateGeneral($_POST['name'], 'Вы не ввели "'.HEAD_NAME.'"!!!');
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели "'.HEAD_TITLE.'"!!!');
        $Validator->validateGeneral($_POST['code'], 'Вы не ввели "'.HEAD_CURRENCY_CODE.'"!!!');
        $Validator->validateGeneral($_POST['sign'], 'Вы не ввели "'.HEAD_SIGN.'"!!!');

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $query_type = $itemID ? 'update' : 'insert';
            $arData     = $_POST;

            if(strpos($arData['thousands_sep'], 'nbsp;')) $arData['thousands_sep']=' ';
            if(empty($arData['createdDate'])) $arData['createdDate'] = date('Y-m-d');
            if(empty($arData['createdTime'])) $arData['createdTime'] = date('H:i:s');
            $arData['created'] = "{$arData['createdDate']} {$arData['createdTime']}";

            $result = $DB->postToDB($arData, CURRENCY_TABLE, ($itemID ? 'WHERE `id`='.$itemID : ''),  array(), $query_type, false);
            if($result){
                if(!$itemID && $result && is_int($result)){
                    $arData['cid'] = $itemID = $result;
                    $result = $DB->postToDB($arData, CURRENCY_INFO_TABLE, '',  array(), $query_type, true);
                } else $result = $DB->postToDB($arData, CURRENCY_INFO_TABLE, 'WHERE `cid`='.$itemID,  array(), $query_type, false);
                setSessionMessage('Запись успешно сохранена!');
                $item_title = getValueFromDB(CURRENCY_INFO_TABLE, 'name', 'WHERE `cid`='.$itemID);
                if($task=='addItem'){
                    foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                } else {
                     ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                }  
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            }
            if(!$result) $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
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
//$arrPageData['headCss'][]       = '<link href="/js/libs/highslide/highslide.css" type="text/css" rel="stylesheet" />';
//$arrPageData['headScripts'][]   = '<script src="/js/jquery/external/jquery.regex.js" type="text/javascript"></script>';

// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';


if($task=='addItem' || $task=='editItem'){
    $arDecPoints    = getDecimalsPointVariants();
    $arSepVariants  = getThousandsSepVariants();
    $arrTemplates   = getCurrencyTemplates();

    if(!$itemID){
        if($hasAccess) {
            $item = array_combine_multi(array_merge($DB->getTableColumnsNames(CURRENCY_TABLE), $DB->getTableColumnsNames(CURRENCY_INFO_TABLE)), '');
            $item['order']          = getMaxPosition(false, 'order', false, CURRENCY_TABLE);
            $item['thousands_sep']  = key($arSepVariants);
            $item['active']         = 1;
            $item['createdDate']    = date('Y-m-d');
            $item['createdTime']    = date('H:i:s');
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif($itemID) {
        $query = "SELECT t.*, ljt.* FROM `".CURRENCY_TABLE."` t
                    LEFT JOIN `".CURRENCY_INFO_TABLE."` ljt ON ljt.`cid`=t.`id`
                    WHERE t.id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result)
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        elseif(!mysql_num_rows($result))
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        else {
            $item = mysql_fetch_assoc($result);
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    // IF IT IS First Currency
    if(!$objCurrency->count)  $item['def4calc'] = $item['def4show'] = $item['nominal'] = $item['rate'] = $item['order'] = $item['active'] = 1;
    
    if($item['unbreakspace']) $item['thousands_sep'] = '&nbsp;';

    $item['arDecPoints']   = $arDecPoints;
    $item['arSepVariants'] = $arSepVariants;
    $item['arrTemplates']  = $arrTemplates;

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
            array('default'=>HEAD_LINK_SORT_DEFAULT, 'name'=>HEAD_LINK_SORT_NAME, 'created'=>HEAD_LINK_SORTDATEADD),
            $arrOrder['get'], $arrPageData['admin_url'], 'pageorder', '_');

    // Display Items List Data
    $where = "";

    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(CURRENCY_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "t.`order`, t.`id`");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query = "SELECT t.*, ljt.* FROM `".CURRENCY_TABLE."` t
                LEFT JOIN `".CURRENCY_INFO_TABLE."` ljt ON ljt.`cid`=t.`id`
                $where $order $limit";
    $result = mysql_query($query);
    if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            if(!$objCurrency->def4calc && $row['id']==$objCurrency->baseorder){
                $row['def4calc'] = 1;
                $row['rate']     = number_format(1, 4);
                $objCurrency->def4calc = $objCurrency->baserate = $row['id'];
            } else if(!$objCurrency->baserate && $row['id']==$objCurrency->def4calc){
                $row['rate']           = number_format(1, 4);
                $objCurrency->baserate = $row['id'];
            }
            if(!$objCurrency->def4show && $row['id']==$objCurrency->baseorder){
                $row['def4show'] = 1;
                $objCurrency->def4show = $row['id'];
            }
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
$smarty->assign('objCurrency',   $objCurrency);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################


# ##############################################################################
////////////////////////// MODULE FUNCTIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function getDecimalsPointVariants(){
    return array('.'=>'Точка', ','=>'Запятая');
}
function getThousandsSepVariants(){
    return array(''=>'Без разделителя', "`"=>'Тильда', '.'=>'Точка', ','=>'Запятая', ' '=>'Пробел', '&nbsp;'=>'Неразрывный пробел', '#'=>'- Другое значение ->');
}
function getCurrencyTemplates(){
    $arItems = array();
    foreach(getRowItems(CURRENCY_INFO_TABLE, '*') as $cur){
        $number = "1{$cur['thousands_sep']}000";
        if($cur['decimals']>0) $number .= $cur['dec_point'].str_repeat('0', $cur['decimals']);
        $arItems[$cur['template']] = screenData(str_replace('#', $number, unScreenData($cur['template'], false)), false);
    }
    $number = "1.234,50";
    $arItems['&euro;#'] = "&euro;{$number}";
    $arItems['&euro; #'] = "&euro; {$number}";
    $arItems['#&euro;'] = "{$number}&euro;";
    $arItems['# &euro;'] = "{$number} &euro;";
    $arItems['#'] = "- Другой ->";
    return $arItems;
}
function & getCurrencyInfo(){
    $arCurrencyInfo = (object)array('baserate'=>0, 'def4calc'=>0, 'def4show'=>0, 'baseorder'=>0, 'maxorder'=>0, 'count'=>0);
    $query = "SELECT
                   if(t1.`id`   IS NOT NULL, t1.`id`, 0)           as baserate,
                   if(ljt1.`id` IS NOT NULL, ljt1.`id`, 0)         as def4calc, 
                   if(ljt2.`id` IS NOT NULL, ljt2.`id`, 0)         as def4show, 
                   if(ljt3.`id` IS NOT NULL, MIN(ljt3.`order`), 0) as baseorder, 
                   if(ljt4.`id` IS NOT NULL, MAX(ljt4.`order`), 0) as maxorder,
                   (SELECT COUNT(s1.`id`) FROM `".CURRENCY_TABLE."` s1) as count
                FROM `".CURRENCY_TABLE."` t1
                LEFT JOIN `".CURRENCY_TABLE."` ljt1 ON ljt1.`def4calc`=1
                LEFT JOIN `".CURRENCY_TABLE."` ljt2 ON ljt2.`def4show`=1
                LEFT JOIN `".CURRENCY_TABLE."` ljt3 ON ljt3.`id` IS NOT NULL
                LEFT JOIN `".CURRENCY_TABLE."` ljt4 ON ljt4.`id` IS NOT NULL
                WHERE t1.`rate`=1 AND t1.`active`=1
                ORDER BY t1.`order`
                LIMIT 1";
        $result = mysql_query($query);
        if($result && mysql_num_rows($result))
            $arCurrencyInfo = mysql_fetch_object($result);
    return $arCurrencyInfo;
}
//\\\\\\\\\\\\\\\\\\\\\\ END MODULE FUNCTIONS //////////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(3) NOT NULL DEFAULT '',
  `nominal` int(11) NOT NULL DEFAULT '1',
  `rate` decimal(18,4) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `def4calc` tinyint(1) NOT NULL DEFAULT '0',
  `def4show` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
INSERT INTO `currency` (`id`, `code`, `nominal`, `rate`, `order`, `def4calc`, `def4show`, `active`, `created`, `modified`) VALUES
(1, 'UAN', 1, 1.0000, 1, 1, 1, 1, '2011-04-04 17:54:18', '2011-04-12 12:25:05'),
(2, 'USD', 1, 7.9710, 2, 0, 0, 1, '2011-04-04 17:53:35', '2011-04-12 12:25:05'),
(3, 'EUR', 1, 11.4774, 3, 0, 0, 1, '2011-04-08 10:56:50', '2011-04-12 12:25:05');
 -- --------------------------------------------------------
DROP TABLE IF EXISTS `ru_currency_info`;
CREATE TABLE IF NOT EXISTS `ru_currency_info` (
  `cid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `sign` varchar(20) NOT NULL DEFAULT '',
  `template` varchar(50) NOT NULL DEFAULT '#',
  `decimals` int(10) unsigned NOT NULL DEFAULT '2',
  `dec_point` char(1) NOT NULL DEFAULT '.',
  `thousands_sep` varchar(1) NOT NULL DEFAULT '',
  `unbreakspace` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `idx_name` (`name`),
  KEY `idx_sign` (`sign`),
  KEY `idx_unbreakspace` (`unbreakspace`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
INSERT INTO `ru_currency_info` (`cid`, `name`, `title`, `sign`, `template`, `decimals`, `dec_point`, `thousands_sep`, `unbreakspace`) VALUES
(1, 'Гривны', 'Украинская гривна', 'грн.', '# грн.', 2, '.', ' ', 1),
(2, 'Доллары', 'Доллары США', '$', '$#', 2, '.', '', 0),
(3, 'Евро', 'Евро', '&amp;euro;', '&amp;euro;#', 2, '.', '', 0);
 */