<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$action = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : false;
$items  = array(); // Items Array of items Info arrays
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
//if(!empty($_SESSION['basket_lastid'])) {
//     $arrPageData['basket_lastid'] = $_SESSION['basket_lastid'];
//     unset($_SESSION['basket_lastid']);
//}
//if(!empty($_SESSION['basket_lastpage'])) {
//     $arrPageData['basket_lastpage'] = $_SESSION['basket_lastpage'];
//     unset($_SESSION['basket_lastpage']);
//}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['files_url']     = UPLOAD_URL_DIR.'catalog/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if($action) {
    switch($action){
        // ADD -----------------------------------------------------------------
        case 'add':
            $Basket->add(@$_POST['id']);
            if(isset($_POST['ajax'])){
                echo '1'; exit();
            }
            break;

        // REMOVE --------------------------------------------------------------
        case 'remove':
            $Basket->remove(@$_POST['id'], 0);
            if(isset($_POST['ajax'])){
                echo '1'; exit();
            }
            break;

        // UPDATE --------------------------------------------------------------
        case 'update':
            if(array_key_exists('basket', $_POST)){
                $Basket->update($_POST['basket']);
            }
            break;

        // CLEAR ---------------------------------------------------------------
        case 'clear':
            $Basket->dropBasket();
            break;

        // DEFAULT -------------------------------------------------------------
        default: break;
    }
    Redirect('/basket/');
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
//$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="/js/jquery/jNice/jNice.css" />';
//$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/jNice/jquery.jNice.js"></script>';
$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="/js/jquery/tooltip/jquery.tooltip.css" />';
$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/dimensions/jquery.dimensions.min.js"></script>';
$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/tooltip/jquery.tooltip.min.js"></script>';
$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/initTooltip.js"></script>';

$items           = $Basket->getItems();

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('items',        $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

