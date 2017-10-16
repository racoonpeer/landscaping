<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access
require_once('include/classes/ImagesUpload.php');
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID       = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$pid          = (isset($_GET['pid']) && intval($_GET['pid']))       ? intval($_GET['pid'])    : 0;
$pmodule      = !empty($_GET['pmodule'])                            ? trim($_GET['pmodule'])  : '';
$type         = !empty($_GET['type'])                               ? trim($_GET['type'])  : '';
$items        = array(); // Items Array of items Info arrays
$arItems      = isset($_GET['arItems']) ? $_GET['arItems'] : array();
$hasAccess    = $UserAccess->getAccessToModule($pmodule); 
$arItem       = ImagesUpload::PrepareImagesParams($pmodule, $type);
// prepare images settings
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['pid']           = $pid;
$arrPageData['pmodule']       = $pmodule;
$arrPageData['parent_url']    = $pid ? '&pid='.$pid : '';
$arrPageData['filter_url']    = ($pmodule ? '&pmodule='.$pmodule : '').($type ? '&type='.$type : $type);
$arrPageData['type_url']      = $type ? '&type='.$type : '';
$arrPageData['admin_url']     = $pmodule ? $arrPageData['admin_url'].'&pmodule='.$pmodule : '';
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['parent_url'].$arrPageData['page_url'].$arrPageData['type_url'];
$arrPageData['arrParent']     = getItemRow($arItem['ptable'], '*', 'WHERE id='.$pid);
$arrPageData['headTitle']     = ADMIN_AJAX_MODE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = $arItem['diffTables'] ? UPLOAD_URL_DIR.$pmodule.'/'.$pid.'/' : UPLOAD_URL_DIR.$pmodule.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 12;

$ImagesUpload = new ImagesUpload($arrPageData['files_url'], ($arItem['diffTables'] ? $pid : false));
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
$item_title = $pid ?  getValueFromDB($arItem['ptable'], 'title', 'WHERE `id`='.$pid) : '';
if($itemID && $task=='deleteItem') {
    if($hasAccess) {
        if(!$arItem['diffTables']) {
            $allLangs = 0;
            $filename = getValueFromDB($arItem['ptable'], $arItem['column'], 'WHERE `id`='.$pid);
            foreach(SystemComponent::getAcceptLangs() as $ln => $value) {
                $table = explode('_', $arItem['ptable']);
                $allLangs += intval(getValueFromDB($ln.'_'.$table[1], 'count(id)', 'WHERE `id`='.$pid.' AND `'.$arItem['column'].'`="'.$filename.'"', 'count'));
            }
            $result = $allLangs ? unlinkImageLangsSynchronize($pid, $arItem['ptable'],  $arrPageData['files_path'], $arItem['aliases'], $arItem['column']) : unlinkImage($pid, $arItem['ptable'], $arrPageData['files_path'], $arItem['aliases'], false, $arItem['column']);
            if($result)
                 ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалено изображение', $lang, $item_title, $pid, $pmodule);
        }
        else {
            unlinkImage($itemID, $arItem['ftable'], $arrPageData['files_path'], $arItem['aliases'], true, $arItem['column'], true);
            $result = deleteRecords($arItem['ftable'], ' WHERE `id`='.$itemID);
            if(!$result) {
                $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
            } elseif($result) {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалено изображение', $lang, $item_title, $pid, $pmodule);
                $default = getItemRow($arItem['ftable'], 'id', 'WHERE `isdefault`=1 AND `pid`='.$pid);
                if(empty($default)) updateRecords($arItem['ftable'], '`isdefault`=1', 'WHERE `pid`='.$pid. ' ORDER BY `fileorder` LIMIT 1');
                Redirect($arrPageData['current_url']);
            }
        }
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    } 
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])  && $arItem['diffTables']) {
    if($hasAccess) {
        $result = updateRecords($arItem['ftable'], "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменена публикация изображения на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $pid, $pmodule);
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        }
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}
// Set Default Item
elseif($itemID && $task=='setDefaultItem' && $arItem['diffTables']) {
    if($hasAccess) {
        updateRecords($arItem['ftable'], "`isdefault`=0", 'WHERE `pid`='.$pid);
        $result = updateRecords($arItem['ftable'], "`isdefault`=1", 'WHERE `id`='.$itemID.' AND `pid`='.$pid);
        if($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Установлено изображение по умолчанию', $lang, $item_title, $pid, $pmodule);
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        }
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}
elseif(!empty($arItems) && $task == 'publishItems' && $arItem['diffTables']){
    if($hasAccess) {
        $result = updateItems(array('active'=>$arItems), $arItems, 'id', $arItem['ftable']);
        if($result === true) {
            setSessionMessage('Новое состояние успешно сохранено!');
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменена публикация изображений', $lang, $item_title, $pid, $pmodule);
            Redirect($arrPageData['current_url']);
        }
        elseif($result === false) {
            setSessionMessage('Не нуждается в сохранении!');
            Redirect($arrPageData['current_url']);
        }
        else $arrPageData['errors'][] = $result;
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}
elseif(!empty($arItems) && $task == 'deleteItems'  && $arItem['diffTables']){
    if($hasAccess) {
        unlinkImages($arrPageData['files_path'], $arItem['ftable'], $arItem['column'], "WHERE `id` IN (".implode(',' , array_keys($arItems)).")", '', $arItem['aliases'], false, false);
        $result = deleteRecords($arItem['ftable'], " WHERE `id` IN (".implode(',' , array_keys($arItems)).")");
        if($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалены изображения '.implode(',' , array_keys($arItems)), $lang, $item_title, $pid, $pmodule);
            setSessionMessage('Новое состояние успешно сохранено!');
            Redirect($arrPageData['current_url']);
        }
        else $arrPageData['errors'][] = 'Ошибка удаления файлов!';
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}
// Insert Or Update Item in Database
elseif($task=='editItems' && isset($_POST['submit_order']) && $arItem['diffTables']) {
    if($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = 'update';
        foreach($_POST['arItemsId'] as $itemID) {
            $conditions   = 'WHERE `id`='.$itemID;
            $arPostData = array(
                'fileorder' => $_POST['arOrder'][$itemID],
                'title'     => $_POST['arTitle'][$itemID],
                'isdefault'   => isset($_POST['default']) && (intval($_POST['default']) == $itemID) ? 1 : 0
            );
            $result = $DB->postToDB($arPostData, $arItem['ftable'], $conditions,  $arUnusedKeys, $query_type, FALSE);
            if($result) {
                $arrPageData['messages'][] = 'Запись с ID='.$itemID.' успешно сохранена!';
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Редактирование изображения ', $lang, $item_title, $pid, $pmodule);
            } else {
                $arrPageData['errors'][] = 'Запись с ID='.$itemID.' <font color="red">НЕ была сохранена</font>!';
            }
        }
        if(isset($_POST['coords']) && !empty($_POST['coords'])) {
            $arrPageData['coords']['path'] = $_POST['coords']['path'];
            $arrPageData['coords']['name'] = $_POST['coords']['name'];   
        }
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}
elseif(!empty($_FILES['file']) && empty($_POST['crop']) ){
    $arData = $ImagesUpload->checkUploadedFile($_FILES['file']);
    if(isset($arData['error'])){
        $arrPageData['errors'][] = $arData['error'];
    } else {
        $arrPageData['image']['path'] = $arData['path'];
        $arrPageData['image']['name'] = translitRichStr($arData['name']); 
    }
}
// Create new image
elseif(isset($_POST['coords']) && !empty($_POST['crop']) && $task=='editItems' ) {
    if($hasAccess) {
        $new_name = $ImagesUpload->cropImage($_POST['coords'], $arItem);   
        // сохраняем в базу, если изображение создалось
        if($new_name && file_exists($arrPageData['files_path'].$new_name)) {
            if($arItem['diffTables']) {
                $arPostData = array(
                    $arItem['column']  => $new_name,
                    'pid'       => $pid,
                    'fileorder' => intval(getValueFromDB($arItem['ftable']." t", 'COUNT(*)', 'WHERE `pid`="'.$pid.'"', 'count')),
                    'title'     => ''
                );
                $result = $DB->postToDB($arPostData, $arItem['ftable'], '', array(), 'insert', (isset($_POST['allLangs'])));
                
                $default = getItemRow($arItem['ftable'], 'id', 'WHERE `isdefault`=1 AND `pid`='.$pid);
                if(empty($default)) updateRecords($arItem['ftable'], '`isdefault`=1', 'WHERE `pid`='.$pid. ' ORDER BY `fileorder` LIMIT 1');
            }
            if($result) {
                setSessionMessage('Изображение загружено!');
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Загружено изображение', $lang, $item_title, $pid, $pmodule);
            } else {
                setSessionErrors('Ошибка загрузки изображения!');
            } 
        }
        Redirect($arrPageData['current_url']);
    } else {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
}

// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link href="/js/jquery/uploadify/uploadify.css" type="text/css" rel="stylesheet" />';
$arrPageData['headCss'][]       = '<link href="/js/jquery/uploadify/uploadify_custom.css" type="text/css" rel="stylesheet" />';

$arrPageData['headScripts'][]   = '<script src="/js/swfobject.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/uploadify/jquery.uploadify.v2.1.4.min.js" type="text/javascript"></script>';
$arrPageData['headCss'][]       = '<link href="/js/jquery/jgrowl/jGrowl.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/jgrowl/jgrowl_minimized.js" type="text/javascript"></script>';

// Display Items List Data
$where = $arItem['diffTables'] ? "WHERE t.`pid` = $pid" : "WHERE t.`id`= $pid";
$limit = '';
$order = '';
if($arItem['diffTables']){
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB($arItem['ftable']." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['parent_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = $arItem['diffTables'] ? "ORDER BY t.`fileorder`" : '';
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
} 
$query = "SELECT t.* FROM `".($arItem['diffTables'] ? $arItem['ftable'] : $arItem['ptable'])."` t $where $order $limit";
$result = mysql_query($query);
if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
else {
    while ($row = mysql_fetch_assoc($result)) {
        if(!$arItem['diffTables']) {
            $row['allLangs'] = 0;
            foreach(SystemComponent::getAcceptLangs() as $ln => $value) {
                $table = explode('_', $arItem['ptable']);
                $row['allLangs'] += intval(getValueFromDB($ln.'_'.$table[1], 'count(id)', 'WHERE `id`='.$pid.' AND `'.$arItem['column'].'`="'.$row[$arItem['column']].'"', 'count'));
            }
            $row['allLangs'] =  $row['allLangs']==count(SystemComponent::getAcceptLangs()) ? true : false;
        }
        if(!empty($row[$arItem['column']]))
            $row['filename'] = is_file($arrPageData['files_path'].$row[$arItem['column']]) ? $arrPageData['files_url'].$row[$arItem['column']] : $arrPageData['files_url'].$row[$arItem['column']];
        $items[]    = $row;
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('items',        $items);
$smarty->assign('arItem',       $arItem);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
 * 
DROP TABLE IF EXISTS `ru_catalogfiles`;
CREATE TABLE IF NOT EXISTS `ru_catalogfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL,
  `title` varchar(255) NULL,
  `filename` varchar(255) NOT NULL,
  `fileorder` int(11) NOT NULL DEFAULT '1',
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_fileorder` (`fileorder`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 * 
 */