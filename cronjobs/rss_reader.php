<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/ 
define('WEBlife',    1); //Set flag that this is a parent file
define('WLCMS_EXEC', 1); //Set flag that this process by exec
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

//error_reporting(0);
set_time_limit(60);

require_once('include/classes/domit_xml/xml_domit_rss.php');// Include DBDOMIT! RSS class
$RSSDoc     = & new xml_domit_rss_document();       //instantiate RSS document
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
$RSSDoc->disableCache(); //Disables caching mechanism

// SELECT URL from different table langs 
$count = 0; $line  = 1;
foreach( SystemComponent::getAcceptLangsKeys() as $ln ){
    $query  = "SELECT * FROM `".(($ln==$lang) ? RSSFEEDS_TABLE : replaceLang($ln, RSSFEEDS_TABLE))."`;"; //   fields: id, title, url, channels
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        //parse feed at http://www.somesite.com/rss.xml
        if($RSSDoc->loadRSS($row['url'])){
            //echo $RSSDoc->toString(true); //echo document to browser
            $arSqlValues = array();
            $numChannels = $RSSDoc->getChannelCount(); //get number of channels
            for ($i = 0; $i < $numChannels && $i < $row['channels']; $i++) {  //set up a loop to iterate through each channel      
                $currChannel = & $RSSDoc->getChannel($i); //obtain a reference to the current channel
                $numItems    = $currChannel->getItemCount(); //get number of items        
                for ($j = 0; $j < $numItems; $j++) { //set up a loop to iterate through each item
                    $currItem = & $currChannel->getItem($j); //get reference to current item
                    $title   = htmlspecialchars(str_conv($currItem->getTitle(), "UTF-8", "WINDOWS-1251")); // add title
                    $link    = str_conv($currItem->getLink(), "UTF-8", "WINDOWS-1251"); // add link
                    $descr   = str_conv($currItem->getDescription(), "UTF-8", "WINDOWS-1251"); // add description
                    $created = $currItem->hasElement('pubDate') ? date('Y-m-d H:i:s', strtotime($currItem->getPubDate())) : date('Y-m-d H:i:s'); //check if pubDate element exists and add

                    $arSqlValues[] = "('{$row['id']}', '{$title}', '{$link}', '{$descr}', '{$created}')";
                }
            }
            $table = ($ln==$lang) ? RSSCONTENTS_TABLE : replaceLang($ln, RSSCONTENTS_TABLE);
            $DB->Query("INSERT IGNORE INTO `{$table}` (`rssfeedID`, `title`, `link`, `descr`, `created`) VALUES \n".implode(",\n", $arSqlValues));            
            $affected = (($affected=intval($DB->getAffectedRows()))>0) ? $affected : 0;
            if($affected) $log_message .= "\t\t".$line++.". В таблицу `{$table}` было добавлено {$affected} новых строк с url rss ленты {$row['url']};"."\n";
            $count += $affected;
        }
    }
 }

$endTime = microtime(1);
$time    = round($endTime-$startTime, 4);

if($count){
       $log_message .= "\t"."Автоматическая работа системы закончена. Время работы скрипта: {$time} секунд."."\n";
       $log_message = "\tВ базу внесено {$count} записей:"."\n".$log_message;
} else $log_message .= "\t"."Автоматическое чтение rss ленты не добавило ни одной записи в базу данных. Время работы скрипта: {$time} секунд."."\n";

$log_message = "\n\n".date("Y.m.d (l dS of F Y h:i:s A)")."\n".$log_message;

//Write log file
if (($fp = @fopen($logfile, 'a'))) {
    fwrite($fp, $log_message);
    fclose($fp);
}

if($print) print($log_message);
