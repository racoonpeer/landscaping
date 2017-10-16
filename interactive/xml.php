<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file

// link = /interactive/xml.php?zone=[site|admin]&action=[getmenu|...]
// link = /interactive/xml.php?params=zone-site_action-getIndices_cid-{$item.id}_id-{$item.main}&curr=xxxxx - for getIndices

$params    = (isset($_GET['params']) && !empty($_GET['params'])) ? addslashes($_GET['params']) : false;
if($params){
    $_SERVER['REQUEST_URI'] = str_replace('params='.$params, str_replace(array('_','-'), array('&','='), $params), $_SERVER['REQUEST_URI']);
    $params = explode('_', $params);
    foreach($params as $param){
        list($k, $v) = explode('-', $param);
        $_GET[$k]=$v;
    }
}

$zone   =  !empty($_GET['zone'])   ? addslashes(trim($_GET['zone']))   : false;
$action =  !empty($_GET['action']) ? addslashes(trim($_GET['action'])) : false;

if($zone){
    
    // Define WLCMS_ZONE from $site_zone var
    switch($zone){
        case 'admin': define('WLCMS_ZONE', 'BACKEND');  break;//Set flag that this is a admin area
        case 'site' : define('WLCMS_ZONE', 'FRONTEND'); break;//Set flag that this is a site area
        default:  exit(); break;
    }


    // change to root dir
    chdir("..".DIRECTORY_SEPARATOR);

# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
    require_once('include/functions/base.php');         // 1. Include base functions
    require_once('include/functions/menu.php');         // 2. Include menu functions
    require_once('include/classes/Cookie.php');         // 3. Include Cookie class file
    $Cookie     = new CCookie();
    require_once('include/system/SystemComponent.php'); // 4. Include DB configuration file Must be included before other
    require_once('include/system/DefaultLang.php');     // 5. Include Languages File
    require_once('include/system/tables.php');          // 6. Include DB tables File
    require_once('include/classes/DbConnector.php');    // 7. Include DB class
    $DB         = new DbConnector();
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################

    //saveLogDebugFile(array('$_SESSION'=>$_SESSION, '$_SERVER'=>$_SERVER, '$_GET'=>$_GET, '$_POST'=>$_POST, 'realpath'=>realpath('./')), '../log.txt');  

################################################################################
// /////////////////// IMPORTANT GLOBAL VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    $objUserInfo     = getUserFromSession(); //user info object
    $objSettingsInfo = getSettings(); //settings info object
// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################


    if($action == 'getmenu'){
        //saveLogDebugFile(array('$_SESSION'=>$_SESSION, '$_FILES'=>$_FILES, '$_GET'=>$_GET, '$_POST'=>$_POST, 'realpath'=>realpath('./')), '../log.txt');
        $catid    = !empty($_GET['catid']) ? intval($_GET['catid']) : false;
        $rootID   = GetRootId($catid);

        $mainMenu = getMenu(1, 0, 1); // $type = 1 :  Главное меню
        
        header("Content-Type: text/xml");
        header("Pragma: no-cache");
        
        /*echo '<?xml version="1.0" encoding="windows-1251"?>' . "\n";*/
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<xml>' . "\n";
        foreach($mainMenu as $arItem)
            echo '  <item text="'.str_conv($arItem['title']).'" link="'.$UrlWL->buildCategoryUrl($arItem).'" active="'.((int)($arItem['id']==$catid || $arItem['id']==$rootID)).'" />' . "\n";
        echo '</xml>' . "\n";

    } else if($action == 'getIndices'){
        //saveLogDebugFile(array('$_SESSION'=>$_SESSION, '$_FILES'=>$_FILES, '$_GET'=>$_GET, '$_POST'=>$_POST, 'realpath'=>realpath('./')), '../log.txt');
        $cid = (isset($_GET['cid']) && !empty($_GET['cid'])) ? intval($_GET['cid']) : false;
        $id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : false;

        if($cid && $id){
                $q = array();
                $select = "SELECT *, date as d, DATE_FORMAT(date,'%e/%c/%y') as date FROM ".INDICES_DATA_TABLE." WHERE indice_id=".$id." AND `date` > DATE_FORMAT(NOW()-INTERVAL 1 YEAR,'%Y-%m-%d') ORDER BY d";
                $query = mysql_query($select);
                while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
                    $q[] = $row;
                }
                //saveLogDebugFile(array('$select'=>$select), '../log.txt');
                $select = "SELECT MIN(price) as minprice, MAX(price) as maxprice FROM ".INDICES_DATA_TABLE." WHERE indice_id=".$id." AND `date` > DATE_FORMAT(NOW()-INTERVAL 1 YEAR,'%Y-%m-%d')";
                $query = mysql_query($select);
                $row = mysql_fetch_assoc($query);
                $minprice = intval(floor($row['minprice']));
                $maxprice = intval(ceil($row['maxprice']));
                $toround = ($maxprice - $minprice) * 0.03;
        header("Content-Type: text/xml");
        header("Pragma: no-cache");

        echo '<?xml version="1.0" encoding="windows-1251"?>' . "\n";
        ?>
        <graph caption='' subcaption='' xAxisName='' yAxisName='' yAxisMinValue='<?=floor($minprice - $toround)?>'
            yAxisMaxValue='<?=ceil($maxprice + $toround)?>' decimalPrecision='2' showLimits='1'
            formatNumberScale='1' numberPrefix='' showNames='1' showValues='0'
            showAnchors='0' drawAnchors='0' anchorradius='0' showToolTip='0'
            chartRightMargin='30' showAlternateHGridColor='1' AlternateHGridColor='37739b'
            lineColor='3083CF' divLineColor='37739b' divLineAlpha='20' alternateHGridAlpha='5' >
        <?php
            $allLines = count($q);
            $divide = round($allLines/4);
            $i=1;
            foreach($q as $qq) {
                $qq['date2'] = $qq['date'];
                if($divide>1) {
                    if($i!=1 && $i!=count($q)) $qq['date'] = ($i%$divide) ? '' : (($i<($allLines-$divide/2)) ? $qq['date'] : '') ;
                } ?>
               <set name='<?=$qq['date']?>' value='<?=$qq['price']?>' hoverText='<?=$qq['date2']?>' />
            <?  $i++; } ?>
         </graph>
            <?

        }

    } else {
        $act = explode('_', @$_GET['page']);
        if($act){
            switch($act[0]) {
                case 'quote':
                    $id = (int)$act[1];
                    if(!empty($act[2]) && !empty($act[3])) {
                        $d1 = explode('/',$act[2]);
                        $tmp = $d1[2].'-'.$d1[1].'-'.$d1[0];
                        $d1 = $tmp;
                        $d2 = explode('/',$act[3]);
                        $tmp = $d2[2].'-'.$d2[1].'-'.$d2[0];
                        $d2 = $tmp;
                        $where = ' AND date>="'.$d1.'" AND date<="'.$d2.'"';
                    } else $where = '';
                    $select = "SELECT *, date as d, DATE_FORMAT(date,'%e/%c/%y') as date FROM ".QUOTES_DATA." WHERE quote_id=".$id." $where ORDER BY d";
                    $query = mysql_query($select);
                    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
                        $q[] = $row;
                    }

                    $select = "SELECT MIN(price) as minprice, MAX(price) as maxprice FROM ".QUOTES_DATA." WHERE quote_id=".$id." $where";
                    $query = mysql_query($select);
                    $row = mysql_fetch_assoc($query);
                    $minprice = intval(floor($row['minprice']));
                    $maxprice = intval(ceil($row['maxprice']));
                    $toround = ($maxprice - $minprice) * 0.03;
            ?>
            <graph caption='' subcaption='' xAxisName='' yAxisName='' yAxisMinValue='<?=floor($minprice - $toround)?>'
                yAxisMaxValue='<?=ceil($maxprice + $toround)?>' decimalPrecision='2' showLimits='1'
                formatNumberScale='1' numberPrefix='' showNames='1' showValues='0'
                showAnchors='0' drawAnchors='0' anchorradius='0' showToolTip='0'
                chartRightMargin='30' showAlternateHGridColor='1' AlternateHGridColor='37739b'
                lineColor='C30F18' divLineColor='37739b' divLineAlpha='20' alternateHGridAlpha='5' >
            <?php
                $divide = round(count($q)/4);
                $i=1;
                foreach($q as $qq) {
                    $qq[date2] = $qq[date];
                    if($divide>1) {
                        if($i!=1 && $i!=count($q)) $qq[date] = ($i%$divide) ? '' : $qq[date] ;
                    } ?>
                   <set name='<?=$qq[date]?>' value='<?=$qq[price]?>' hoverText='<?=$qq[date2]?>' />
                <?php  $i++; } ?>
             </graph>
                <?php
                break;
            }
        }
    }
}
