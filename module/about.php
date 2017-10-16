<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$items = array();

$arrPageData['files_url']  = UPLOAD_URL_DIR.'staff/';
$arrPageData['files_path'] = prepareDirPath($arrPageData['files_url']);

$query  = 'SELECT s.* FROM `'.STAFF_TABLE.'` s WHERE s.`active`>0 ORDER BY `order`';
$result = mysql_query($query);
if ($result and mysql_num_rows($result)>0) {
    while ($row = mysql_fetch_assoc($result)) {
        $row['descr'] = unScreenData($row['descr']);
        $row['image'] = (!empty($row['image']) and is_file($arrPageData['files_path'].$row['image'])) ? $arrPageData['files_url'].$row['image'] : $arrPageData['files_url'].'noimage.jpg';
        $items[] = $row;
    }
}

$smarty->assign('items', $items);