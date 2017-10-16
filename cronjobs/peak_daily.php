<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/ 
define('WEBlife',    1); //Set flag that this is a parent file
define('WLCMS_EXEC', 1);//Set flag that this process by exec
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area

// change to current work file dir  [fix for exec, that current work dir is user dir]
chdir(dirname(__FILE__));
// change to root WLCMS dir
chdir("..".DIRECTORY_SEPARATOR);
// Set DOCUMENT_ROOT in global $_SERVER var if empty
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = rtrim(getcwd(), '/\\').DIRECTORY_SEPARATOR;


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');         // 1. Include base functions

require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');     // 3. Include Languages File
require_once('include/system/tables.php');          // 4. Include DB tables File
require_once('include/classes/DbConnector.php');    // 5. Include DB class
$DB         = new ExternalDbConnector();
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


################################################################################
// /////////////////// IMPORTANT GLOBAL VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$print           = !empty($_GET['print']) ? intval($_GET['print']) : 0;
$logfile         = UPLOAD_DIR.DS."files".DS.'cronlog.txt';
$log_message     = "";
$startTime       = microtime(1);
$endTime         = 0;
// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################


# ##############################################################################
// ////////////////////////   ACTION PAGE SECTION   \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$count = 0;
$query  = "SELECT * FROM `".AUCTIONS_TYPES_TABLE."` WHERE `active`=1";
$result = mysql_query($query);
while ($arAType = mysql_fetch_assoc($result)) {
    $query  = "SELECT CURDATE() as `peakdate`, `typename`, `productID`, MAX(price) as `peakprice`
                FROM `".AUCTIONS_TABLE."` 
                WHERE `typename`='".$arAType['name']."' AND `active`=1 AND CURDATE() BETWEEN datefrom AND dateto
                GROUP BY `productID`
                ORDER BY `created`";
    $sub_result = mysql_query($query);
    while ($arData = mysql_fetch_assoc($sub_result)) {
        if(empty($arData['peakprice'])) $arData['peakprice'] = 0;
        $arrSqlInj = $DB->postToQuery($arData, array_keys($arData), 'insert');
        $DB->Query("INSERT IGNORE INTO `".AUCTIONS_DP_TABLE."` ({$arrSqlInj["keys"]}) VALUES ({$arrSqlInj["values"]})");
        if($DB->getAffectedRows()) {
            $log_message .= "\t\t".(++$count).". Продукт ID {$arData['productID']} с типом заявки '{$arAType['title']}' имеет на сегодня наибольшую цену {$arData['peakprice']};"."\n";
        }
    }
}

$endTime = microtime(1);
$time    = round($endTime-$startTime, 4);

if($count){
       $log_message .= "\t"."Автоматическая работа системы закончена. Время работы скрипта: {$time} секунд."."\n";
       $log_message = "\tВ базу внесено {$count} записей:"."\n".$log_message;
} else $log_message .= "\t"."Автоматическую архивацию не удалось произвести. Было внесено 0 записей. Время работы скрипта: {$time} секунд."."\n";

$log_message = "\n\n".date("Y.m.d (l dS of F Y h:i:s A)")."\n".$log_message;

//Write log file
if (($fp = @fopen($logfile, 'a'))) {
    fwrite($fp, $log_message);
    fclose($fp);
}

if($print) print($log_message);
