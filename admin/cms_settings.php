<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// ///////////////////// REQUIRED LOCAL PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\
$item    = array();
$tab = !empty($_POST['tab']) ? $_POST['tab'] : '';
if(!$tab) $tab  = (isset($_GET['tab']) ? $_GET['tab'] : 'main');
// //////////////////// END REQUIRED LOCAL PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['headTitle']     = TITLE_SETTINGS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['current_url']   = $arrPageData['admin_url'].('&tab='.$tab);
$arrPageData['tab']           = $tab;
$item['arImgAliases']         = SystemComponent::getArImgAliases();
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Clear Templates
if ($task=='clearTemplates') {
    $result = $smarty->clearAllCompiledFiles();
    if($result) setSessionMessage('Скомпилированные ранее шаблоны удалены!');
    else        setSessionErrors('Ни один файл не был удален с папки компилирования шаблонов!');
    Redirect($arrPageData['current_url']);
}
// Clear Cache
else if ($task=='clearCache') {
    $result = $smarty->clearAllCachedFiles();
    if($result) setSessionMessage('Файлы кеша удалены!');
    else        setSessionErrors('Ни один файл не был удален с папки кеш файлов!');
    Redirect($arrPageData['current_url']);
}
// Repair DataBase Tables
else if ($task=='repairDBTables') {
    $result = 0;
    foreach($DB->listDBTables(true) as $dbTable){
        @mysql_query("LOCK TABLES `{$DB->getDBName()}`.`{$dbTable}` WRITE");
        if(mysql_query("REPAIR TABLE `{$DB->getDBName()}`.`{$dbTable}`")) $result++;
        @mysql_query("UNLOCK TABLES");
        break;
    }
    if($result) setSessionMessage('Таблицы в базе данных успешно восстановлены!');
    else        setSessionErrors('Ни одной таблицы в базе данных не удалось восстановить!');
    Redirect($arrPageData['current_url']);
}
// Optimize DataBase Tables
else if ($task=='optimizeDBTables') {
    $result = 0;
    foreach($DB->listDBTables(true) as $dbTable){
        @mysql_query("LOCK TABLES `{$DB->getDBName()}`.`{$dbTable}` WRITE");
        if(mysql_query("OPTIMIZE TABLE `{$DB->getDBName()}`.`{$dbTable}`")) $result++;
        @mysql_query("UNLOCK TABLES");
    }
    if($result) setSessionMessage('Таблицы в базе данных успешно оптимизированы!');
    else        setSessionErrors('Ни одной таблицы в базе данных не удалось оптимизировать!');
    Redirect($arrPageData['current_url']);
}
// sql query
else if(!empty($_POST['sql_query'])) {
    $queries = stripslashes(trim($_POST['sql_query']));
    foreach(explode(';', $queries) as $query){
        if(!empty($query)){
            $result = mysql_query($query);
            if($result) setSessionMessage ('Запрос <br/>"'.$query.'"<br/> успешно выполнен!');
            else  {
                $arrPageData['errors'][] = 'Ошибка выполнения запроса <br/>"'.$query.'"<br/> '.mysql_error();
                break;
            }
        }
    }
    if($result) Redirect($arrPageData['current_url']);
    else $item['sql_query'] = $queries;
}
else if (!empty($_POST)) {    
    // IMAGES SETTINGS OPERATION   
    if($tab == 'images') {
        deleteRecords(IMAGES_PARAMS_TABLE);
        if(!empty($_POST['arModulesImg'])) {
            $aff_rows = 0;
            foreach($_POST['arModulesImg'] as $module => $arItems) {           
                foreach ($arItems as $arItem) {
                    // add resize alias
                    $arMiniatures = array();
                    if(!empty($arItem['arMiniatures'])) {
                        foreach($arItem['arMiniatures'] as $key => $miniature) {
                            if(!empty($miniature['width']) && !empty($miniature['height']))
                                $arMiniatures[$key] = array( "width"  => $miniature['width'], "height" => $miniature['height']);
                        }
                    }
                    $arData = array(
                        'module'     => $module,
                        'aliases'    => !empty($arMiniatures) ? mysql_real_escape_string(serialize($arMiniatures)) : '',
                        'max_width'  => $arItem['max_width'],
                        'max_height' => $arItem['max_height'],
                        'crop_width' => $arItem['crop_width'],
                        'crop_height'=> $arItem['crop_height'],
                        'crop_color' => $arItem['crop_color'],
                        'title'      => $arItem['title'],
                        'column'     => $arItem['column'],
                        'ptable'     => $arItem['ptable'],
                        'ftable'     => $arItem['ftable'],
                    );
                    $result = $DB->postToDB($arData, IMAGES_PARAMS_TABLE);
                    if(!$result) break 2; 
                    else if(mysql_affected_rows())  $aff_rows++;
                }
            }
            if($result && $aff_rows>0) {
                setSessionMessage('Настройки изображений сохранены!');
                Redirect($arrPageData['current_url']);
            } else if(!$result) {
                $arrPageData['errors'][] = 'Ошибка сохранения настроек изображений: '.mysql_error();                
            }
        } 
    }

    else if ($tab == 'modules') {
        // Save modules settings
        if(isset($_POST['arModules'])) {
            $error = false;
            foreach($_POST['arModules'] as $module_name => $arModule) {
                if(isset($arModule['seogroup']) && empty($arModule['seotable'])) {
                    $error = true;
                    $arrPageData['errors'][] = 'Заполните СЕО таблицу для модуля '.$module_name;
                    break;
                }
            }    

            if(!$error) {
                // delete old modules
                deleteRecords(MODULES_PARAMS_TABLE, 'WHERE `module` NOT IN ("'.implode('", "', array_keys($_POST['arModules'])).'")');
                $aff_rows = 0;
                foreach($_POST['arModules'] as $module_name => $arModule) {
                    $result = $DB->query('INSERT INTO '.MODULES_PARAMS_TABLE.' 
                                          VALUES ("'.$module_name.'", 
                                                  "'.$arModule['title'].'", 
                                                  "'.$arModule['short_title'].'", 
                                                  "'.$arModule['seotable'].'", 
                                                  '.(!empty($arModule['seogroup']) ? 1 : 0).', 
                                                  '.(!empty($arModule['images']) ? 1 : 0).', 
                                                  '.(!empty($arModule['access']) ? 1 : 0).', 
                                                  '.(!empty($arModule['history']) ? 1 : 0).',
                                                  '.(!empty($arModule['menu']) ? 1 : 0).', 
                                                  '.$arModule['order'].')
                                          ON DUPLICATE KEY UPDATE 
                                            `title`="'.$arModule['title'].'", 
                                            `short_title`="'.$arModule['short_title'].'", 
                                            `seotable` = "'.$arModule['seotable'].'",
                                            `seogroup`='.(!empty($arModule['seogroup']) ? 1 : 0).', 
                                            `images`='.(!empty($arModule['images']) ? 1 : 0).', 
                                            `access`='.(!empty($arModule['access']) ? 1 : 0).',
                                            `history`='.(!empty($arModule['history']) ? 1 : 0).',
                                            `menu`='.(!empty($arModule['menu']) ? 1 : 0).',
                                            `order`='.$arModule['order']); 
                    if(!$result) {
                        $arrPageData['errors'][] = 'Ошибка сохранения настроек модуля '.$module_name.': '.  mysql_error();
                        break;
                    } else if(mysql_affected_rows())  $aff_rows++;
                }    
                if($result) {
                    setSessionMessage('Настройки модулей сохранены!');
                    Redirect($arrPageData['current_url']);
                } 
            }
        }
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// images settings block
$item['arImgModules'] = getRowItems(MODULES_PARAMS_TABLE, '*', '`images`=1');
if(!empty($item['arImgModules'])) {
    foreach($item['arImgModules'] as $key=> $arModule){
        $row = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arModule['module'].'"');
        foreach($row as $k => $r) {
            $row[$k]['aliases'] = !empty($r['aliases']) ? unserialize(unScreenData($r['aliases'])) : array();
        }
        $item['arImgModules'][$key]['arImages']  = $row;
    }
}
if(!empty($_POST['arModulesImg'])) $item['arImgModules'] = array_merge ($item['arImgModules'], $_POST['arModulesImg']);

// modules settings block
$excludeModules = array('cms_settings', 'mysqldumper', 'welcome');
$modules_cnt = getValueFromDB(MODULES_PARAMS_TABLE, 'max(`order`)', '', 'max_cnt');
$item['arModulesParams'] = array();
$hndl = opendir(WLCMS_ABS_ROOT.'admin/');
while ($file = readdir($hndl)) {
    if($file!='.' && $file!='..' && getFileExt($file) == 'php' && !in_array(($module_name = basename($file, '.php')), $excludeModules)){
        $arModule = getItemRow(MODULES_PARAMS_TABLE, '*', 'WHERE `module`="'.$module_name.'"');
        if(empty($arModule)){
            $arModule = $DB->getTableColumnValues(MODULES_PARAMS_TABLE);
            foreach($arModule as $key=>$val){
                switch ($key) {
                    case 'module':
                    case 'title':
                    case 'short_title':
                        $val = $module_name;
                        break;
                    case 'access':
                    case 'history':
                    case 'menu':
                        $val = 1;
                        break;
                    case 'order':
                        $val = ++$modules_cnt;
                        break;
                    default:
                        break;
                }
                $arModule[$key]=$val;
            }
            $DB->query('INSERT INTO '.MODULES_PARAMS_TABLE.' (`'.implode('`,`', array_keys($arModule)).'") VALUES ("'.implode('","', array_values($arModule)).'")'); 
        }
        $item['arModulesParams'][$module_name] = $arModule;
    }
} closedir($hndl);

if(!empty($item['arModulesParams'])){
    // clear unused modules
    deleteRecords(MODULES_PARAMS_TABLE, 'WHERE `module` NOT IN ("'.implode('", "', array_keys($item['arModulesParams'])).'")');
    // sort by order field
    uasort($item['arModulesParams'], function($a, $b) {
        return ($a['order'] == $b['order'] ? 0 : ($a['order'] < $b['order'] ? -1 : 1));
    });
}
//if(!empty($_POST['arModules'])) $item['arModulesParams'] = array_merge ($item['arModulesParams'], $_POST['arModules']);

// user settings block
$item['arModules'] = getRowItems(MODULES_PARAMS_TABLE, '*', '`access`=1', '`order`');
$item['arrUserTypes'] = getRowItems(USERTYPES_TABLE, '*', '`active`=1', 'id');
if(!empty($item['arrUserTypes'])) {
    foreach($item['arrUserTypes'] as $key => $arItem) {
        $item['arrUserTypes'][$key]['users'] = getRowItems(USERS_TABLE, '*', '`type`="'.$arItem['name'].'"');
        $settings = getItemRow(USERS_ACCESS_TABLE, '*', 'WHERE `gid`='.$arItem['id'].' AND `uid`=0');
        $item['arrUserTypes'][$key]['modules'] = !empty($settings) ? explode(',', $settings['modules']) : array();
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item', $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
 DROP TABLE IF EXISTS `ru_settings`;
 CREATE TABLE `ru_settings` (
  `name` varchar(100) NOT NULL,
  `value` text DEFAULT '',
  `require` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251; 
 * 

 DROP TABLE IF EXISTS `images_params`;
 CREATE TABLE `images_params` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `aliases` varchar(100) NOT NULL,
  `max_width` varchar(100) DEFAULT '',
  `max_height` varchar(100) DEFAULT '',
  `crop_width` varchar(100) DEFAULT '',
  `crop_height` varchar(100) DEFAULT '',
  `crop_color` varchar(100) DEFAULT '',
  `title` varchar(255) NOT NULL,
  `column` varchar(255) NOT NULL,
  `ptable` varchar(255) NOT NULL,
  `ftable` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
 * 
 * 
DROP TABLE IF EXISTS `modules_params`;
CREATE TABLE IF NOT EXISTS `modules_params` (
  `module` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_title` varchar(255) NOT NULL,
  `seotable` varchar(50) NOT NULL DEFAULT '' COMMENT 'Таблица c сеопутем',
  `seogroup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Уникальный сеопуть: 0-нет, 1-есть',
  `images` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Настройки изображений: 0-нет, 1-есть',
  `access` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Настройки доступов: 0-нет, 1-есть',
  `history` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Сохранять историю: 0-нет, 1-да',
  `menu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Отображать в меню: 0-нет, 1-да',
  `order` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Сортировка модулей',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
 * 
 */