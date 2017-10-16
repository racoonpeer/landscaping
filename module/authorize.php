<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$action = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : 'login';
$item   = array(); // Item Info Array
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['files_url']     = UPLOAD_URL_DIR.'users/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\

if($action) {
    switch($action){
        case 'login':
                        
            checkErrorLoginInSession('suser_wrong_entries', false, false);

            if(isset($_SESSION['suser_wrong_entries']['time']) && (time() - $_SESSION['suser_wrong_entries']['time']) < BANNED_TIME) {
                $ban_time = BANNED_TIME - (time() - $_SESSION['suser_wrong_entries']['time']);
                $ban_min = intval($ban_time / 60);
                $arrPageData['errors'][] = sprintf(USERS_ERROR_MAX_WRONG_PASS.'<br/>'.TRY_AGAIN_AFTER_TITLE.' <b><span id="banTimer">%s min %s sec</span></b>!', $ban_min, $ban_time - ($ban_min * 60));
            } else {
                if (isset($_POST["captcha"]) || @$_SESSION['suser_wrong_entries']['count']>=WRONG_SUBMITS_TO_CAPTCHA) { // Answer for spec
                    if(empty($_POST["captcha"]["code"]))
                        $arrPageData['errors'][] = sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, FEEDBACK_CONFIRMATION_CODE);
                } elseif (!empty($_POST["captcha"]["code"]) && !$Captcha->CheckCode($_POST["captcha"]["code"], $_POST["captcha"]["sid"])){
                    $arrPageData['errors'][] = sprintf(ENTER_INPUT_ERROR, FEEDBACK_CONFIRMATION_CODE);
                }

                if (sizeof($arrPageData['errors'])==0 && isset($_POST['pass']) && (isset($_POST['login']) || isset($_POST['email'])) ) {
                    $password = md5(addslashes($_POST['pass']));
                    $query = "SELECT * FROM `".USERS_TABLE."` 
                        WHERE `active`=1 
                          AND `pass`=MD5(CONCAT('".$password."', `salt`)) 
                          AND ".(isset($_POST['login']) ? "`login`='".addslashes($_POST['login'])."'" : "`email`='".addslashes($_POST['email'])."'")."
                        LIMIT 1";
                    $result = @mysql_query($query);
                    if (@mysql_num_rows($result)) {
                        $row = mysql_fetch_assoc($result);
                        $row['password'] = $password;
                        $row['logined']  = 1;
                        $objUserInfo = (object)$row;
                        setUserToSession($objUserInfo);
                        $_SESSION['suser_timeout'] = time();
                        if(isset($_SESSION['suser_wrong_entries'])) { unset($_SESSION['suser_wrong_entries']); }
                        if($arCategory['pagetype']!=2) Redirect(getReturnUrlFromSession('/'));
                    } else $arrPageData['errors'][] = USERS_ERROR_LOGIN;
                 }
             }
             if(sizeof($arrPageData['errors']) && $_SERVER['REQUEST_METHOD']=='POST')
                 checkErrorLoginInSession('suser_wrong_entries', true, $UrlWL->buildCategoryUrl($arCategory));
             break;

        case 'logout':
            unset($_SESSION['suser_obj']);
            #session_unregister('suser_obj');
        default:
            Redirect($UrlWL->buildCategoryUrl($arCategory));
            break;
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
//$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="/js/jquery/jNice/jNice.css" />';
//$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/jNice/jquery.jNice.js"></script>';

$item['showCaptcha'] = (@$_SESSION['suser_wrong_entries']['count']>=WRONG_SUBMITS_TO_CAPTCHA) ? 1 : 0;
$item['bannedTime']  = isset($_SESSION['suser_wrong_entries']['time']) ? $_SESSION['suser_wrong_entries']['time']+BANNED_TIME : 0;

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

