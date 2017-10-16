<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

################################################################################
// ///////////////////// SITE USER AUTHENTICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$auth = false;

// check to see if suser_timeout in $_SESSION is set and > SESSION_INACTIVE
if(isset($_SESSION['suser_timeout']) ) {
    if((time() - $_SESSION['suser_timeout']) > SESSION_INACTIVE) {
        $objUserInfo->logined = 0;
        unsetUserFromSession();
        unset($_SESSION['suser_timeout']);
    }
}

if (!empty($objUserInfo) && $objUserInfo->logined) {
    if(isset($objUserInfo->auth_type) && !empty($objUserInfo->auth_type)) {
        $query  = "SELECT * FROM `".USERS_TABLE."` WHERE `active`=1 and `login`='{$objUserInfo->login}' AND (`fbid`>0 OR `vkid`>0) LIMIT 1";
    } else {
        $query  = "SELECT * FROM `".USERS_TABLE."` WHERE `active`=1 and `login`='{$objUserInfo->login}' AND `pass`=MD5(CONCAT('{$objUserInfo->password}', `salt`)) LIMIT 1";
    }
    
    $result = mysql_query($query);
    if (mysql_num_rows($result)) {
        $auth = true;
        $_SESSION['suser_timeout'] = time();
    } unset($query, $result);
}

if (!$auth && $objUserInfo) {
//    unsetUserFromSession();
    $objUserInfo->logined = 0;
}

$arrPageData['auth'] = $auth;
// \\\\\\\\\\\\\\\\\\ END  SITE USER AUTHENTICATION ////////////////////////////
################################################################################
