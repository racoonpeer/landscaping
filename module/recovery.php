<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$action = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : 'generate';
$code   = !empty($_GET['code']) ? trim(addslashes($_GET['code'])) : (!empty($_POST['checkword']) ? trim(addslashes($_POST['checkword'])) : false);
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
        case 'generate':
            if(!empty($_POST)) {
                /*if(empty($_POST['login']) && empty($_POST['email']))
                    $Validator->addError("Введите пожалуйста Логин или Email!");
                elseif(!empty($_POST['login']))
                    $Validator->validateLogin($_POST['login'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, USERS_LOGIN));
                elseif(!empty($_POST['email']))*/
                $Validator->validateEmail($_POST['email'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, LABEL_YOUR_EMAIL));

                if ($Validator->foundErrors()) {
                    $arrPageData['errors'][] = $Validator->getListedErrors();
                } else {
                    //$login = trim(addslashes($_POST['login']));
                    $email = trim(addslashes($_POST['email']));
                    $query = "SELECT *,
                         if(`checkword_time`>NOW()-INTERVAL ".CHECKW_CONFIRMCODE_DAYS_KEEP." DAY, checkword, '') as checkword,
                         if(`checkword_time`>NOW()-INTERVAL ".CHECKW_CONFIRMCODE_DAYS_KEEP." DAY, checkword_time, '') as checkword_time
                        FROM `".USERS_TABLE."`
                        WHERE `active`=1 AND ".(!empty($login) ? "`login`='{$login}'" : "`email`='{$email}'")."
                        LIMIT 1";
                    $result = mysql_query($query);
                    if($result && mysql_num_rows($result)>0){
                        $item = mysql_fetch_assoc($result);
                        $item['checkword'] = randString(12);

                        $arData['checkword']      = sha1($item['salt'].md5($item['salt'].$item['checkword']));
                        $arData['checkword_time'] = date('Y-m-d H:i:s');
                        if(!$DB->postToDB($arData, USERS_TABLE, 'WHERE `id`='.$item['id'], array(), 'update'))
                            $arrPageData['errors'][] = ENTER_SYSTEM_ERROR.'. '.TRY_AGAIN_TITLE.'.';
                        else {
                            $item['username'] = trim(trim("{$item['firstname']} {$item['middlename']}")." {$item['surname']}");
                            $item['sitename'] = $objSettingsInfo->websiteName;
                            $item['links']['generate'] = 'http://'.$_SERVER['SERVER_NAME'].$UrlWL->buildCategoryUrl($arCategory, 'action=confirm');
                            $item['links']['confirm']  = $item['links']['generate'].'&code='.$item['checkword'];
                            $smarty->assign('item', $item);
                            $to      = $item['username']." <{$item['email']}>";
                            $from    = $objSettingsInfo->siteEmail;
                            $text    = $smarty->fetch('mail/recovery_generate.tpl');
                            $subject = $objSettingsInfo->websiteName.': '.RECOVERY_SUBJECT_CODE;
                            if(sendMail($to, $subject, $text, $from)){
                                setSessionMessage(RECOVERY_SEND_CODE);
                                Redirect($UrlWL->buildCategoryUrl($arCategory, 'action=confirm'));
                            } else $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR.'. '.TRY_AGAIN_TITLE;
                        }
                    } else $arrPageData['errors'][] = RECOVERY_SEARCH_ERROR;
                }
            }
            break;

        case 'confirm':
            if(!$code){
                $Validator->addError(RECOVERY_ENTER_CODE);
            } elseif($code) {
                $query = "SELECT * FROM `".USERS_TABLE."`
                    WHERE `active`=1 AND `checkword` = SHA1(CONCAT(`salt`, MD5(CONCAT(`salt`, '".$code."')))) AND `checkword_time` > NOW()-INTERVAL ".CHECKW_CONFIRMCODE_DAYS_KEEP." DAY";
                $result = mysql_query($query);
                if($result && mysql_num_rows($result)==1)
                     $item = mysql_fetch_assoc($result);
                else $Validator->addError(RECOVERY_ENTER_CODE_ERROR);
            }
            
            if ($Validator->foundErrors()) {
                $arrPageData['errors'][] = $Validator->getListedErrors();
            } elseif(!empty($item['id'])) {
                $password = str_shuffle(str_replace('-', '', uuid().randString(32)));
                $password = substr($password, rand(0, strlen($password)-14), 12);

                $arData['salt'] = salt();
                $arData['pass'] = md5(md5($password).$arData['salt']);
                $arData['checkword']      = NULL;
                $arData['checkword_time'] = NULL;
                if(!$DB->postToDB($arData, USERS_TABLE, 'WHERE `id`='.$item['id'], array(), 'update'))
                    $arrPageData['errors'][] = ENTER_SYSTEM_ERROR.'. '.TRY_AGAIN_TITLE.'.';
                else {
                    $item['password'] = $password;
                    $item['username'] = trim(trim("{$item['firstname']} {$item['middlename']}")." {$item['surname']}");
                    $item['sitename'] = $objSettingsInfo->websiteName;
                    $item['link'] = 'http://'.$_SERVER['SERVER_NAME'].$UrlWL->buildItemUrl($arrModules['authorize']);
                    $smarty->assign('item', $item);
                    $to      = $item['username']." <{$item['email']}>";
                    $from    = $objSettingsInfo->siteEmail;
                    $text    = $smarty->fetch('mail/recovery_confirm.tpl');
                    $subject = $objSettingsInfo->websiteName.': '.RECOVERY_SUBJECT_PASS;
                    if(sendMail($to, $subject, $text, $from)){
                        setSessionMessage(RECOVERY_SEND_PASS);
                        Redirect($UrlWL->buildCategoryUrl($arCategory, 'action=success'));
                    } else $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR.'. '.TRY_AGAIN_TITLE;
                }
            }
            break;

        case 'success':
            if(empty($arrPageData['messages']))
                Redirect($UrlWL->buildCategoryUrl($arrModules['authorize']));
            break;

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

$item['action'] = $action;

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

