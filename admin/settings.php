<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// ///////////////////// REQUIRED LOCAL PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\
$item         = array();
$success      = null;
$arActionsLog = array();
$filters      = (!empty($_GET['filters']))  ? $_GET['filters']   : array('time'=>1);
$key          = (!empty($_GET['key']))  ?trim($_GET['key'])   : '';
$hasAccess    = $UserAccess->getAccessToModule($arrPageData['module']);
// //////////////////// END REQUIRED LOCAL PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['headTitle']     = TITLE_SETTINGS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['current_url']   = $arrPageData['admin_url'];
$arrPageData['activeTab']     = (isset($filters['tab'])) ? trim($filters['tab']) : '';
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if (!empty($_POST)) {
    if($hasAccess) {
        $items = getRowItemsValue(SETTINGS_TABLE, 'name', 'WHERE `require`=1');
        $arPostData = screenData($_POST['arSettings']);

        // verify existing require fields in template
        foreach($items as $name){
            if(!isset($arPostData[$name])) {
                $Validator->addError( 'поле '.$name);
            }
            if ($Validator->foundErrors()){
                setSessionErrors('В шаблоне отсутствуют обязательные поля: '.$Validator->getListedErrors());
                Redirect($arrPageData['current_url']);
            }
        }

        // validate require field 
        if(!empty($_POST['arValidate'])) {
            foreach($_POST['arValidate'] as $k => $v){
                $params = explode('|', $v);
                // $v - array( [0]=>{error} - error value, [1]=>{email|phone} - method name, [2]=>{empty|notempty} - is empty,  ...)
                if(count($params)>1) {
                    if(isset($params[2]) && $params[2]=='empty' && empty($arPostData[$k])) 
                        continue;
                    $Validator->{"validate$params[1]"}($arPostData[$k], $params[0]);
                } else {
                    $Validator->validateGeneral($arPostData[$k], $v);
                }
            }
        }

        if ($Validator->foundErrors()){
            $success = false;
            $arrPageData['errors'][] = ERROR_PLEASE_INSERT.$Validator->getListedErrors();
        } else {
            foreach($arPostData as $name => $value) {
                $result = mysql_query('INSERT INTO '.SETTINGS_TABLE.' (`name`, `value`) values ("'.$name.'", "'.$value.'") ON DUPLICATE KEY UPDATE `value`="'.$value.'"');
                if(!$result) break;  
                else if (mysql_affected_rows()){
                     ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Редактирования поля "'.$name.'"', $lang, '', 0, $arrPageData['module']);
                }
            }
            if(!empty($arPostData)){ 
                mysql_query('DELETE FROM '.SETTINGS_TABLE.' WHERE `require`<>1 AND `name` NOT IN ("'.implode('", "', array_keys($arPostData)).'")');                
            }
            if(($success = $result) == true)  {
                setSessionMessage(DATABASE_SUCCESS);
                Redirect($arrPageData['admin_url']);
            }
            else $arrPageData['errors'][]   = ERROR_SAVE_DATA;
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if($success) $objSettingsInfo = getSettings();

if($success===false) 
     $item = dataApplayFunc($arPostData, 'stripslashes');
else {
    foreach(getRowItems(SETTINGS_TABLE) as $arItem) {
        $item[$arItem['name']] = $arItem['value'];
    }
}

// получаем историю по конкретному модулю
$params = array();
$params['modules'][] = $arrPageData['module'];
$params['langs'][] = $lang;
$item['arHistory'] = ActionsLog::getInstance()->getHistory($params, 25);

//получаем всю историю
$arActionsLog['arHistory'] = ActionsLog::getInstance()->getHistory($filters);
$arActionsLog['arFilters'] = ActionsLog::getInstance()->getFilters($filters, $key);
$arActionsLog['selectedFilters'] = $filters;
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################
$arrPageData['headCss'][]       = '<link href="/js/jquery/themes/smoothness/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />';
//$arrPageData['headCss'][]       = '<link href="/js/jquery/themes/smoothness/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css" />';
//$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.slider.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery.ui.datepicker.js" type="text/javascript"></script>';
//$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/jquery-ui.timepicker.addon.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/ui/1251/jquery.ui.datepicker-ru.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]   = '<script src="/js/jquery/history/jquery.history.js" type="text/javascript" ></script>';

# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item', $item);
$smarty->assign('arActionsLog', $arActionsLog);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `ru_settings`;
CREATE TABLE IF NOT EXISTS `ru_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `websiteName` tinytext,
  `websiteSlogan` tinytext,
  `websiteUrl` tinytext,
  `ownerFirstName` varchar(150) DEFAULT NULL,
  `ownerLastName` varchar(150) DEFAULT NULL,
  `ownerEmail` varchar(150) DEFAULT NULL,
  `ownerAddress` text,
  `notifyEmail` tinytext,
  `siteEmail` tinytext,
  `copyright` text,
  `logo` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;
 */

/*
 DROP TABLE IF EXISTS `settings`;
 CREATE TABLE `settings` (
  `name` varchar(100) NOT NULL,
  `value` text DEFAULT '',
  `require` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251; 
 */