<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

################################################################################
// //////////////////// ADMIN USER AUTHENTICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$auth = false;

// check to see if suser_timeout in $_SESSION is set and > SESSION_INACTIVE
if(isset($_SESSION['auser_timeout']) ) {
    if((time()-$_SESSION['auser_timeout']) > SESSION_INACTIVE) {
        ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_AUTHORIZATION, 'Выход по истечении времени со страницы '.$_SERVER['REQUEST_URI'], $lang);
        unsetUserFromSession();
        unset($objUserInfo, $_SESSION['auser_timeout']);
    }
}

if(!empty($_GET['action']) && $_GET['action'] == 'logout') {
    ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->save(ActionsLog::ACTION_AUTHORIZATION, 'Выход со страницы '.(!empty($_GET['last_url']) ? addslashes(urldecode($_GET['last_url'])) : $_SERVER['REQUEST_URI']), $lang);
    unsetUserFromSession();
    $objUserInfo->logined = 0;
}

if(!empty($objUserInfo) && $objUserInfo->logined) {
    $query  = "SELECT * FROM `".USERS_TABLE."` WHERE `active` = 1 and `login` = '{$objUserInfo->login}' AND `pass` = MD5(CONCAT('{$objUserInfo->password}', `salt`)) LIMIT 1";
    $result = mysql_query($query);
    if (mysql_num_rows($result) && $UserAccess->init($objUserInfo->id, $objUserInfo->type)->getAuthAccess()) {
        $auth = true;
        $_SESSION['auser_timeout'] = time();
    } unset($query, $result);
}

if($ajax && !$auth) { RedirectAjax('/login.php', $_SERVER['REQUEST_URI']); }
elseif(!$auth) { Redirect('/login.php', (@$_GET['action']=='logout' ? '/admin.php?page=welcome' : $_SERVER['REQUEST_URI'])); }

$arrPageData['auth'] = $auth;
// \\\\\\\\\\\\\\\\\\\ END ADMIN USER AUTHENTICATION ///////////////////////////
################################################################################
