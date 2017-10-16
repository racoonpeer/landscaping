<?php

/*
  WEBlife CMS
  Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$action = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : 'register';
$step = (isset($_GET['step']) && intval($_GET['step'])) ? intval($_GET['step']) : ($action == 'register' ? 1 : false);
$code = !empty($_GET['code']) ? trim(addslashes($_GET['code'])) : (!empty($_POST['confirmcode']) ? trim(addslashes($_POST['confirmcode'])) : false);
$item = array(); // Item Info Array
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################
# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['files_url'] = UPLOAD_URL_DIR . 'users/';
$arrPageData['files_path'] = prepareDirPath($arrPageData['files_url']);
$arrPageData['def_img_param'] = array('w' => 184, 'h' => 184);
$arrPageData['images_params'] = false; //array(array("small_", 120, 100)); // array(array(addname, width, height), array(addname, width, height)[, ..]);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################
# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
//Check if user logined. if yes - redirect to user view page
if ($objUserInfo->logined) {
    Redirect($UrlWL->buildItemUrl($arrModules["users"], array('id' => $objUserInfo->id, 'seo_path' => UrlWL::USER_SEOPREFIX . $objUserInfo->id)));
}
//Create ItemID from session after step 1
$itemID = !empty($_SESSION[MDATA_KNAME][$module]['itemID']) ? $_SESSION[MDATA_KNAME][$module]['itemID'] : 0;
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################
# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
switch ($action) {

    case 'register':
        if ($step == 1) {
            if (!empty($_POST)) {
                if(!$Validator->validateGeneral($_POST['firstname'])) {
                    $arrPageData['errors']['firstname'] = sprintf(FEEDBACK_FILL_REQUIRED_FIELD, USERS_FNAME);
                }
                if(!$Validator->validateEmail($_POST['email'])) {
                    $arrPageData['errors']['email'] = sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, USERS_MAIL);
                }
                if(!$Validator->validatePhone($_POST['phone'])) {
                    $arrPageData['errors']['phone'] = sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, USERS_PHONE);
                }
                if(!$Validator->validateGeneral($_POST["captcha"]["code"])) {
                    $arrPageData['errors']["captcha"] = sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, FEEDBACK_CONFIRMATION_CODE);
                }
                if(!$Validator->validateGeneral($_POST["pass"])) {
                    $arrPageData['errors']["pass"] = USERS_ERROR_PASSWORDS;
                }
                if(!$Validator->validateGeneral($_POST["confpass"]) || $_POST['pass'] != $_POST['confpass']) {
                    $arrPageData['errors']["confpass"] = 'Пароли не совпадают';
                }
                if (!empty($_POST["captcha"]["code"]) && !$Captcha->CheckCode($_POST["captcha"]["code"], $_POST["captcha"]["sid"])) {
                    $arrPageData['errors']["captcha"] = sprintf(ENTER_INPUT_ERROR, FEEDBACK_CONFIRMATION_CODE);
                }
                if (!empty($_POST['email'])) {
                    $email = addslashes($_POST['email']);
                    $select = 'SELECT COUNT(u.`id`) as `logins`, (SELECT COUNT(ut.`id`) FROM `'. USERS_TABLE .'` ut WHERE ut.`email`="'.$email.'") as `emails` FROM `'.USERS_TABLE.'` u ';
                    $where  = 'WHERE u.`login`="'.$email.'" ';
                    $query  = $select.$where;
                    $result = mysql_query($query);
                    if ($result && mysql_num_rows($result) > 0) {
                        $row = mysql_fetch_assoc($result);
                        if ($row['logins'] > 0 || $row['emails'] > 0) {
                            $arrPageData['errors']['email'] = 'Такой e-mail уже зарегистрирован в системе!';
                        }
                    }
                }

                if (empty($arrPageData['errors'])) {
                    $arPostData = $_POST;
                    $confirmcode = randString(12);
                    $arPostData['login'] = trim($arPostData['email']);
                    $arPostData['confirmcode'] = md5($confirmcode);
                    $arPostData['salt'] = salt();
                    $arPostData['pass'] = md5(md5(addslashes($arPostData['pass'])) . $arPostData['salt']);
                    $arPostData['type'] = 'Registered';
                    $arPostData['active'] = 0;
                    $arPostData['subscribe'] = is_set($arPostData, 'subscribe');
                    $arPostData['created'] = date('Y-m-d H:i:s');

                    $result = $DB->postToDB($arPostData, USERS_TABLE);
                    if ($result && is_int($result)) {
                        $itemID = $result;
                        $item['username'] = trim($arPostData['firstname']);
                        $item['sitename'] = $objSettingsInfo->websiteName;
                        $item['server'] = $_SERVER['SERVER_NAME'];
                        $item['confirmcode'] = $confirmcode;
                        $item['links']['generate'] = 'http://' . $_SERVER['SERVER_NAME'] . $UrlWL->buildCategoryUrl($arCategory, 'action=confirm');
                        $item['links']['confirm'] = 'http://' . $_SERVER['SERVER_NAME'] . $UrlWL->buildCategoryUrl($arCategory, 'action=confirm&code=' . $item['confirmcode']);
                        $smarty->assign('item', $item);
                        $to = $item['username'] . " <{$arPostData['email']}>";
                        $from = $objSettingsInfo->siteEmail;
                        $text = $smarty->fetch('mail/register_confirm.tpl');
                        $subject = $objSettingsInfo->websiteName . ': ' . REGISTER_SUBJECT_CONFIRM;
                        if (sendMail($to, $subject, $text, $from)) {
                            $_SESSION[MDATA_KNAME][$module]['itemID'] = $itemID;
                            $_SESSION[MDATA_KNAME][$module]['steps'][$step] = 1;
                            setSessionMessage(REGISTER_SEND_CONFIRMCODE);
                            Redirect($UrlWL->buildCategoryUrl($arCategory, 'action=confirm'));
                        }
                    }
                }
            }
        }
        break;

    case 'confirm':
        if (isset($_GET['code']) || isset($_POST['confirmcode'])) {
            $query = "SELECT * FROM `" . USERS_TABLE . "` WHERE `confirmcode` = MD5('" . $code . "')";
            $result = mysql_query($query);
            if ($result && mysql_num_rows($result) == 1) {
                $item = mysql_fetch_assoc($result);
            } else {
                $Validator->addError(REGISTER_ENTER_CODE_ERROR);
            }

            if ($Validator->foundErrors()) {
                $arrPageData['errors'][] = $Validator->getListedErrors();
            } elseif (!empty($item['id'])) {
                $arData['active'] = 1;
                $arData['confirmcode'] = NULL;
                if (!$DB->postToDB($arData, USERS_TABLE, 'WHERE `id`=' . $item['id'], array(), 'update'))
                    $arrPageData['errors'][] = ENTER_SYSTEM_ERROR . '. ' . TRY_AGAIN_TITLE;
                else {
                    $item = unScreenData($item);
                    $item['username'] = trim(trim("{$item['firstname']} {$item['middlename']}") . " {$item['surname']}");
                    $item['sitename'] = $objSettingsInfo->websiteName;
                    $item['links']['recovery'] = 'http://' . $_SERVER['SERVER_NAME'] . $UrlWL->buildCategoryUrl($arrModules['recovery']);
                    $item['links']['login'] = 'http://' . $_SERVER['SERVER_NAME'] . $UrlWL->buildCategoryUrl($arrModules['authorize']);
                    $item['links']['user'] = 'http://' . $_SERVER['SERVER_NAME'] . $UrlWL->buildItemUrl($arrModules["users"], array('id' => $item['id'], 'seo_path' => UrlWL::USER_SEOPREFIX . $item['id']));
                    $smarty->assign('item', $item);
                    $to = $item['username'] . " <{$item['email']}>";
                    $from = $objSettingsInfo->siteEmail;
                    $text = $smarty->fetch('mail/register_success.tpl');
                    $subject = $objSettingsInfo->websiteName . ': ' . REGISTER_SUBJECT_SUCCSESS;
                    if (sendMail($to, $subject, $text, $from)) {
                        $to = $objSettingsInfo->siteEmail;
                        $from = $item['username'] . " <{$item['email']}>";
                        $text = $smarty->fetch('mail/register_admin.tpl');
                        sendMail($to, $subject, $text, $from);
                        setSessionMessage(REGISTER_SUBJECT_SUCCSESS);
                        Redirect($UrlWL->buildCategoryUrl($arCategory, 'action=confirm&step=2'));
                    } else {
                        $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR . '. ' . TRY_AGAIN_TITLE;
                    }
                }
            }
        } else
            $arrPageData['errors'][] = REGISTER_ENTER_CONFIRMCODE;
        break;

    default:
        break;
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################
# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
//$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="/js/jquery/jNice/jNice.css" />';
//$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/jquery/jNice/jquery.jNice.js"></script>';
//if($step!=1 && !$itemID) Redirect($UrlWL->buildCategoryUrl($arCategory));

$item = array_combine_multi($DB->getTableColumnsNames(USERS_TABLE), '');
if (!empty($_POST)) {
    $item = array_merge($item, $_POST);
}

$item['action'] = $action;
$item['step'] = $step;
$item['itemID'] = $itemID;

// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################
# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item', $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

