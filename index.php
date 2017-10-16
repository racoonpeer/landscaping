<?php 
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file
define('WLCMS_ZONE', 'FRONTEND'); //Set flag that this is a site area

// Redirect without index.php in url
if (strpos($_SERVER['REQUEST_URI'], '/index.php')===0 OR (strpos($_SERVER['REQUEST_URI'], "//")!==false and trim($_SERVER['REQUEST_URI'], '/')=='')) {
    header('location: /'.($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''), true, 301);
    exit();
}

//Include Core File
require('kernel.php');


# ##############################################################################
// /////////////////// OPERATIONS BEFORE DISPLAY \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// сохраняем куки
 $Cookie->process();
// \\\\\\\\\\\\\\\\\ END OPERATIONS BEFORE DISPLAY /////////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// SMARTY DISPLAY \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->display(getTemplateFileName($ajax, $catid), $cacheID);
// \\\\\\\\\\\\\\\\\\\\\\\ END SMARTY DISPLAY //////////////////////////////////
# ##############################################################################
