<?php defined('WEBlife') or die( 'Restricted access' );

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) AND intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) AND intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################

# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = PRODUCT_OPTIONS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
$arrPageData['types']         = getComplexRowItems(OPTIONS_TYPES_TABLE, "*");
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################

# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// SET Reorder
$item_title = $itemID ? getValueFromDB(OPTIONS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
if($task=='reorderItems' AND !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', OPTIONS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) {
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        } elseif($result) {
            $arrPageData['errors'][] = $result;
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif ($itemID AND $task=='deleteItem') {
    if ($hasAccess) {
      //  unlinkImageLangsSynchronize($itemID, OPTIONS_TABLE, $arrPageData['files_path'], $arrPageData['images_params']);
        PHPHelper::deleteImages($itemID, $arrPageData['files_path'], $arrPageData['module']);
        $result = deleteDBLangsSync(OPTIONS_TABLE, ' WHERE `id`='.$itemID);
        if (!$result) {
            $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        } elseif ($result) {
            deleteFileFromDB_AllLangs(0, OPTIONS_VALUES_TABLE, "image", 'WHERE `option_id`='.$itemID, $arrPageData['files_path']);
            deleteDBLangsSync(OPTIONS_VALUES_TABLE, 'WHERE `option_id`='.$itemID);
            deleteRecords(PRODUCT_OPTIONS_TABLE, 'WHERE `oid`='.$itemID);
            deleteRecords(PRODUCT_OPTIONS_VALUES_TABLE, 'WHERE `option_id`='.$itemID);
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            }
            Redirect($arrPageData['current_url']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID AND $task=='publishItem' AND isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(OPTIONS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
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
elseif(!empty($_POST) AND ($task=='addItem' OR $task=='editItem')) {
    if($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = ($itemID)? 'update': 'insert';
        $conditions   = ($itemID)? 'WHERE `id`='.$itemID : '';

        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');

        if(!empty($_POST['basket']) AND intval($_POST['basket'])>0) {
            $Validator->validateGeneral($_POST['stitle'], 'Вы не ввели краткого названия страницы (необходимо для отображения в корзине)!');
        } else {
            $_POST['basket'] = 0;
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $arPostData = $_POST;
           // $arPostData['image'] = imageManipulation($itemID, OPTIONS_TABLE, $arrPageData['files_url'], $arrPageData['images_params']);

            imageManipulationWithCrop($arPostData, $arUnusedKeys, $arrPageData['files_url'], $arrPageData['files_path'], $task, $itemID, $module);

            if(empty($arPostData['createdDate'])) {
                $arPostData['createdDate'] = date('Y-m-d');
            }
            if(empty($arPostData['createdTime'])) {
                $arPostData['createdTime'] = date('H:i:s');
            }
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";

            $result = $DB->postToDB($arPostData, OPTIONS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if($result){
                if(!$itemID AND $result AND is_int($result)) $itemID = $result;
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(OPTIONS_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                }
                setSessionMessage('Запись успешно сохранена!');
                // Fill option values
                $arResults = array();
                if(!empty($_POST['arValues'])) {
                    $order = 1;
                    foreach ($_POST['arValues'] as $key => $arValue){
                        $valueItem = !empty($arValue['id']) ? getItemRow(OPTIONS_VALUES_TABLE, '*', 'WHERE `id`='.$arValue['id']) : array();
                        
                        $new_name = '';
                        if (isset($arValue['delete_image']) AND !empty($valueItem)) {
                            unlinkImage($valueItem['id'], OPTIONS_VALUES_TABLE, $arrPageData['files_url'], false, false);
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                        }
                        if (isset($_FILES['arValues']['tmp_name'][$key])) {
                            $iname        = $_FILES['arValues']['name'][$key]['image']; //имя файла до его отправки на сервер (pict.gif)
                            $itmp_name    = $_FILES['arValues']['tmp_name'][$key]['image']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
                            $arExtAllowed = array('jpeg','jpg','gif','png');

                            if ($iname AND $itmp_name) {
                                $file_ext = getFileExt($iname);
                                if (in_array($file_ext, $arExtAllowed)) {
                                    if (!empty($valueItem)) unlinkImage($valueItem['id'], OPTIONS_VALUES_TABLE, $arrPageData['files_url']);
                                    $new_name = createUniqueFileName($arrPageData['files_url'], $file_ext, basename($iname, '.'.$file_ext));
                                    $image = WideImage::load($itmp_name);
                                    $image->saveToFile($arrPageData['files_path'].$new_name);
                                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Добавлено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                                }
                            }
                        }
                        
                        $arData = array(
                            'option_id' => $itemID,
                            'title'     => $arValue['value'],
                            'image'     => !empty($new_name) ? $new_name : (!empty($valueItem) ? $valueItem['image'] : ''),
                            'order'     => $order++
                        );
                        $result = $DB->postToDB($arData, OPTIONS_VALUES_TABLE, !empty($valueItem) ? 'WHERE `id`='.$valueItem['id'] : '', array(), (!empty($valueItem) ? 'update' : 'insert'), (!empty($valueItem) ? false : true));
                        $arResults[] = !empty($valueItem) ? $valueItem['id'] : $result;
                    }
                }
                deleteItemsAndFilesFromDB('image', OPTIONS_VALUES_TABLE, 'WHERE `option_id`='.$itemID.(!empty($arResults) ? ' AND `id` NOT IN ('.  implode(',', $arResults).')' : ''), $arrPageData['files_url'], true);
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) AND $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################

// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';

if($task=='addItem' OR $task=='editItem'){
    if(!$itemID){
        if($hasAccess) {
            if($copyID){
                $item = getSimpleItemRow($copyID, OPTIONS_TABLE);
                $item = array_merge($item, array('id'=>'', 'image'=>''));
                $item['arValues']  = getRowItems(OPTIONS_VALUES_TABLE, '*', '`option_id`='.$copyID, '`order`');
                if(!empty($item['arValues'])){
                    foreach($item['arValues'] as $key => $val) {
                        $item['arValues'][$key]['edit'] = count(getRowItems(PRODUCT_OPTIONS_VALUES_TABLE, '*', '`option_id`='.$copyID.' AND `value_id`="'.$val['id'].'"')) > 0 ? false : true;
                    }
                }
                $item['arValuesMaxID'] = intval(getValueFromDB(OPTIONS_VALUES_TABLE, 'MAX(id)', 'WHERE `option_id`='.$copyID, 'max'));
            } else {    
                $item = array_combine_multi($DB->getTableColumnsNames(OPTIONS_TABLE), '');
                $item['arValues'] = array();
                $item['arValuesMaxID'] = 0;
            }
            $item['order']  = getMaxPosition(null, 'order', false, OPTIONS_TABLE);
            $item['active'] = 1;
            $item['createdDate'] = date('Y-m-d');
            $item['createdTime'] = date('H:i:s');
            $item['arHistory'] = array();
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif($itemID) {
        $query = "SELECT * FROM ".OPTIONS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif(!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);
            $item['arValues']  = getRowItems(OPTIONS_VALUES_TABLE, '*', '`option_id`='.$itemID, '`order`');
            if(!empty($item['arValues'])){
                foreach($item['arValues'] as $key => $val) {
                    $item['arValues'][$key]['edit'] = count(getRowItems(PRODUCT_OPTIONS_VALUES_TABLE, '*', '`option_id`='.$itemID.' AND `value_id`="'.$val['id'].'"')) > 0 ? false : true;
                }
            }
            $item['arValuesMaxID'] = intval(getValueFromDB(OPTIONS_VALUES_TABLE, 'MAX(id)', 'WHERE `option_id`='.$itemID, 'max'));
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arImageData'] = ($item['image'])? getArrImageSize($arrPageData['files_url'], $item['image']): array();
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    $item['arImagesSettings'] = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arrPageData['module'].'"');
    if(!empty($_POST)) {
        $item = array_merge($item, $_POST);
    }

    // Include Need CSS and Scripts For This Page To Array
    $arrPageData['headCss'][]       = '<link href="/js/jquery/themes/base/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.widget.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.datepicker.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/1251/jquery.ui.datepicker-ru.js" type="text/javascript"></script>';

} else {
    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(
        array(
            'default'   => HEAD_LINK_SORT_DEFAULT, 
            'title'     => HEAD_LINK_SORT_TITLE,
            'created'   => HEAD_LINK_SORTDATEADD
        ),
        $arrOrder['get'], $arrPageData['admin_url'], 'pageorder', '_'
    );

    // Display Items List Data
    $where = ' ';
    
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(OPTIONS_TABLE.' o', 'COUNT(*)', $where, 'cnt'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = 'ORDER BY '.(!empty($arrOrder['mysql']) ? 'o.'.implode(', o.', $arrOrder['mysql']) : 'o.`order`, o.`id`').' ';
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query  = 'SELECT o.*, (SELECT COUNT(DISTINCT `pid`) FROM `'.PRODUCT_OPTIONS_TABLE.'` WHERE `oid`=o.`id`) AS `cnt` FROM `'.OPTIONS_TABLE.'` o '.$where.$order.$limit;
    $result = mysql_query($query);
    if(!$result) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    } else {
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
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `ru_options`;
CREATE TABLE IF NOT EXISTS `ru_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `stitle` varchar(255) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT '0',
  `basket` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order`),
  KEY `idx_basket` (`basket`),
  KEY `idx_active` (`active`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 * 
DROP TABLE IF EXISTS `options_types`;
CREATE IF NOT EXISTS TABLE `options_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 * 
DROP TABLE IF EXISTS `ua_options_values`;
CREATE TABLE IF NOT EXISTS `ua_options_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_oid` (`option_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=cp1251 COMMENT='Product Options Values';
 * 
DROP TABLE IF EXISTS `product_options`;
CREATE TABLE IF NOT EXISTS `product_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oid` int(11) unsigned NOT NULL DEFAULT '0',
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_oid` (`oid`),
  KEY `idx_pid` (`pid`),
  KEY `idx_required` (`required`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 * 
DROP TABLE IF EXISTS `product_options_values`;
CREATE TABLE `product_options_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID Product',
  `option_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID Option',
  `value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID Option Value',
  `operator` char(1) NOT NULL DEFAULT '+' COMMENT 'Price Operator',
  `price` float unsigned NOT NULL DEFAULT '0' COMMENT 'Option Value Price',
  `primary` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Option',
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`product_id`),
  KEY `idx_oid` (`option_id`),
  KEY `idx_vid` (`value_id`),
  KEY `idx_primary` (`primary`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Значения опций товаров';

 */