<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

$items = array();

$arrPageData["files_url"]     = UPLOAD_URL_DIR."homeslider/";
$arrPageData["files_path"]    = prepareDirPath($arrPageData["files_url"]);
$arrPageData["headCss"][]     = "/css/store/{$module}.css";
$arrPageData["headScripts"][] = "/js/libs/Swiper/js/swiper.min.js";
$arrPageData["headScripts"][] = "/js/home.js";

$DB->Query("SELECT * FROM `".HOMESLIDER_TABLE."` WHERE `active`>0 ORDER BY `order`, `created` DESC");
while ($row = $DB->fetchAssoc()) {
    $row["title"] = unScreenData($row["title"]);
    $row["descr"] = unScreenData($row["descr"]);
    $row["image"] = (!empty($row["image"]) and file_exists($arrPageData["files_path"].$row["image"])) ? $arrPageData["files_url"].$row["image"] : $arrPageData["files_url"]."noimage.jpg";
    array_push($items, $row);
}

$smarty->assign('items', $items);