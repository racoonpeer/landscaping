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
$pid          = (isset($_GET['pid']) && intval($_GET['pid']))       ? intval($_GET['pid'])    : 0;
$pmodule      = !empty($_GET['pmodule'])                            ? trim($_GET['pmodule'])  : '';
$files_params = !empty($_GET['files_params']) ? unserialize(base64_decode(urldecode($_GET['files_params']))) : array();
$items        = array(); // Items Array of items Info arrays
$arItems      = isset($_GET['arItems']) ? $_GET['arItems'] : array();
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['pid']           = $pid;
$arrPageData['pmodule']       = $pmodule;
$arrPageData['files_params']  = $files_params;
$arrPageData['parent_url']    = $pid ? '&pid='.$pid : '';
$arrPageData['admin_url']     = $pmodule ? $arrPageData['admin_url'].'&pmodule='.$pmodule : '';
$arrPageData['fparams_url']   = $files_params ? '&files_params='.urlencode(base64_encode(serialize($files_params))) : '';
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['parent_url'].$arrPageData['page_url'].$arrPageData['fparams_url'];
$arrPageData['arrParent']     = getItemRow(USERS_TABLE, '*', 'WHERE id='.$pid);
$arrPageData['headTitle']     = USERS_EDIT_TITLE.$arrPageData['seo_separator'].ADMIN_AJAX_MODE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$pmodule.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
$arrPageData['userfilesmax']  = 5;
$arrPageData['userfileprefix']= "u{$pid}_";
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
if($itemID && $task=='deleteItem') {
    unlinkImage($itemID, USERFILES_TABLE, $arrPageData['files_path'], $arrPageData['files_params'], false, 'filename');
    //deleteFileFromDB($itemID, USERFILES_TABLE, 'filename', 'WHERE `id`='.$itemID, $arrPageData['files_path'], false);
    $result = deleteRecords(USERFILES_TABLE, ' WHERE id='.$itemID);
    if(!$result)    $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
    elseif($result) Redirect($arrPageData['current_url']);
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    $result = updateRecords(USERFILES_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
    if($result===false) $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
    elseif($result)     $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
}
elseif(!empty($arItems) && $task == 'publishItems'){
    $result = updateItems(array('active'=>$arItems), $arItems, 'id', USERFILES_TABLE);
    if($result === true) {
        setSessionMessage('Новое состояние успешно сохранено!');
        Redirect($arrPageData['current_url']);
    }
    elseif($result === false) {
        setSessionMessage('Не нуждается в сохранении!');
        Redirect($arrPageData['current_url']);
    }
    else $arrPageData['errors'][] = $result;
}
elseif(!empty($arItems) && $task == 'deleteItems'){
    unlinkImages($arrPageData['files_path'], USERFILES_TABLE, 'filename', "WHERE `id` IN (".implode(',' , array_keys($arItems)).")", '', $arrPageData['files_params'], false, false);
    $result = deleteRecords(USERFILES_TABLE, " WHERE `id` IN (".implode(',' , array_keys($arItems)).")");
    if($result) {
        setSessionMessage('Новое состояние успешно сохранено!');
        Redirect($arrPageData['current_url']);
    }
    else $arrPageData['errors'][] = 'Ошибка удаления файлов!';
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
/*
$arrPageData['headCss'][]       = '<link href="/js/jquery/uploadify/uploadify.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/swfobject.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/uploadify/uploadify.min.js" type="text/javascript"></script>';
$arrPageData['headCss'][]       = '<link href="/js/jquery/jgrowl/jGrowl.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/jgrowl/jgrowl_minimized.js" type="text/javascript"></script>';
*/
$arrPageData['headCss'][]       = '<link href="/js/jquery/uploadify/uploadify.css" type="text/css" rel="stylesheet" />';
$arrPageData['headCss'][]       = '<link href="/js/jquery/uploadify/uploadify_custom.css" type="text/css" rel="stylesheet" />';

$arrPageData['headScripts'][]   = '<script src="/js/swfobject.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/uploadify/jquery.uploadify.v2.1.4.min.js" type="text/javascript"></script>';
$arrPageData['headCss'][]       = '<link href="/js/jquery/jgrowl/jGrowl.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/jgrowl/jgrowl_minimized.js" type="text/javascript"></script>';

// Display Items List Data
$where = "WHERE t.uid = $pid";

// Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(USERFILES_TABLE." t", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['parent_url'].$arrPageData['filter_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager

$order = "ORDER BY t.id";
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

$query = "SELECT * FROM `".USERFILES_TABLE."` t $where $order $limit";
$result = mysql_query($query);
if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
else {
    while ($row = mysql_fetch_assoc($result)) {
        $items[]    = $row;
    }
}

$arrPageData['user_can_upload'] = $arrPageData['userfilesmax']-intval(getValueFromDB(USERFILES_TABLE, 'COUNT(*)', 'WHERE `uid`='.$pid, 'count'));
if($arrPageData['user_can_upload']<0) $arrPageData['user_can_upload'] = 0;

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('items',        $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
 * 
DROP TABLE IF EXISTS `userfiles`;
CREATE TABLE IF NOT EXISTS `userfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `filename` varchar(63) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 */