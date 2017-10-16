<?php

/*
  WEBlife CMS
  Developed by http://weblife.ua/
 */
define('WEBlife', 1); //Set flag that this is a parent file
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');         // 1. Include base functions
require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');     // 3. Include Languages File
require_once('include/system/tables.php');          // 4. Include DB tables File
require_once('include/classes/mySmarty.php');       // 5. Include mySmarty class
require_once('include/classes/DbConnector.php');    // 6. Include DB class
require_once('include/classes/Validator.php');      // 7. Include Text Validator class
require_once('include/classes/IValidator.php');     // 8. Include Image IValidator class
require_once('include/classes/ActionsLog.php');     // 9. Include Actions logging class
require_once('include/classes/UserAccess.php');     // 10. Include User Access class 
$DB         = new DbConnector();
$Validator  = new Validator();
$IValidator = new IValidator(getIValidatorPefix());
$smarty     = new mySmarty(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE);
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


if (empty($_GET) && isset($_SESSION['auser_wrong_entries']['count'])) {
    Redirect('/login.php?log=retry');
}elseif (empty($_GET) && isset($_SESSION['auser_wrong_entries']['time']) && (time() - $_SESSION['auser_wrong_entries']['time']) < BANNED_TIME) {
    Redirect('/login.php?log=banned');
}
checkErrorLoginInSession('auser_wrong_entries', false);


# ##############################################################################
// ///////////////////// REQUIRED LOCAL PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\
//GET and SET Log Task Action
$logTask     = (isset($_GET['log']) && strlen($_GET['log'])>0) ? addslashes(trim($_GET['log'])) : false;
$refresh     = array('head'=>'', 'body'=>'');
$messages    = array('top'=>'', 'bottom'=>'');
$arrPageData = array( //Page data array
                    'css_dir'       => '/css/'.TPL_BACKEND_NAME.'/',
                    'images_dir'    => '/images/admin/',
                    'headTitle'     => 'TYPO3 CMS Login'
              );
// //////////////////// END REQUIRED LOCAL PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################

//Post actions
if (!empty($_POST)) {

    if (isset($_POST['fConfirmationCode']) || @$_SESSION['auser_wrong_entries']['count']>=WRONG_SUBMITS_TO_CAPTCHA) { ////////// Answer for spec
        if (!$IValidator->checkCode(addslashes(@$_POST['fConfirmationCode']))) {
            $_POST['login'] = $_POST['pass'] = '';
            $logTask = 'codefailed';
            $refresh['head'] = '<meta http-equiv="refresh" content="0; url=login.php?log='.$logTask.'">';
            checkErrorLoginInSession('auser_wrong_entries');
        }
    }

    if (empty($refresh['head']) && isset($_POST['Submit2'])) {
        $password = md5(addslashes($_POST['pass']));
        $query = "SELECT * FROM `".USERS_TABLE."` WHERE `active`=1 AND `login`='".addslashes($_POST['login'])."' AND `pass`=MD5(CONCAT('".$password."', `salt`)) LIMIT 1";
        $result = @mysql_query($query);
        if (@mysql_num_rows($result)) {
            $row = mysql_fetch_assoc($result);
            $UserAccess = new UserAccess($row['id'], $row['type']);
            // проверить имеется ли разрешение на авторизацию
            if (!$UserAccess->getAuthAccess()) {
                $logTask = 'denied';
                $refresh['head'] = '<meta http-equiv="refresh" content="0; url=login.php?log='.$logTask.'">';
                ActionsLog::getAuthInstance($row['id'], getRealIp())->save(ActionsLog::ACTION_AUTHORIZATION, 'Попытка входа. Отказано в доступе.', $lang);
                checkErrorLoginInSession('auser_wrong_entries');
            } else {
                $row['password'] = $password;
                $row['logined']  = 1;
                setUserToSession((object)$row);                
                $returnUrl = getReturnUrlFromSession('');
                ActionsLog::getAuthInstance($row['id'], getRealIp())->save(ActionsLog::ACTION_AUTHORIZATION, 'Вход со страницы '.($returnUrl ? $returnUrl : '/login.php'), $lang);
                $_SESSION['auser_timeout'] = time();
                if (isset($_SESSION['auser_wrong_entries'])) unset($_SESSION['auser_wrong_entries']);
                $refresh['head'] = '<meta http-equiv="refresh" content="0; url='.($returnUrl ? $returnUrl : 'admin.php?page=welcome').'">';
                $refresh['body'] = "<div style='position:relative; text-align:center; top:70px;'><img src='/images/admin/processing.gif'></div>";
            }
            
        } else {
            checkErrorLoginInSession('auser_wrong_entries');
            $logTask = 'failed';
            $refresh['head'] = '<meta http-equiv="refresh" content="0; url=login.php?log='.$logTask.'">';
        }
    }
}


if($logTask){
    $messages['top'] = '<font color="#FF0000">Error! See details below!</font>';
    switch ($logTask) {
        case 'banned':
            $ban_time = isset($_SESSION['auser_wrong_entries']['time']) ? BANNED_TIME - (time() - $_SESSION['auser_wrong_entries']['time']) : 0;
            $ban_min  = intval($ban_time / 60);
            $messages['bottom'] .= sprintf(USERS_ERROR_MAX_WRONG_PASS."<br/>Попробуйте через <b><span id='banTimer'>%s min %s sec</span></b>!", $ban_min, $ban_time - ($ban_min * 60));
            break;
        case 'retry':
            $messages['bottom'] .= 'You have entered some <b>required field wrong</b>. Please enter the correct details and try again.<br/>';
            break;
        case 'failed':
            $messages['bottom'] .= 'You have entered an <b>invalid username or password</b>. Please enter the correct details and try again.<br/>';
            break;
        case 'denied':
            $messages['bottom'] .= 'Acces denied. You can enter only from Main Page <a href="http://'.$_SERVER['SERVER_NAME'].'">'.$_SERVER['SERVER_NAME'].'</a>.<br/>';
            break;
        case 'codefailed':
            $messages['bottom'] .= 'You have entered an <b>invalid confirmation code</b>. Please enter the correct code and try again.<br/>';
            break;
        default: break;
    }
} else $messages['top'] = 'System function - ok.';



///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('refresh',      $refresh);
$smarty->assign('messages',     $messages);
$smarty->assign('arrPageData',  $arrPageData);
$smarty->assign('showCode',     ((isset($_GET['log']) || isset($_GET['code'])) && @$_SESSION['auser_wrong_entries']['count']>=WRONG_SUBMITS_TO_CAPTCHA) ? 1 : 0);
$smarty->assign('bannedTime',   isset($_SESSION['auser_wrong_entries']['time']) ? $_SESSION['auser_wrong_entries']['time']+BANNED_TIME : 0);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES//////////////////////////////


# ##############################################################################
// ///////////////////////// SMARTY DISPLAY \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->display('login.tpl');
// \\\\\\\\\\\\\\\\\\\\\\\ END SMARTY DISPLAY //////////////////////////////////
# ##############################################################################
