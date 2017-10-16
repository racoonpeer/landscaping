<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$action = !empty($_GET['action']) ? trim(addslashes($_GET['action'])) : false;
$result = !empty($_GET['result']) ? trim(addslashes($_GET['result'])) : false;
$item   = array(); // Item Info Array
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
//$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
//$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Send Feedback Item to email
if(!empty($_POST)) {
    // clean textarea
    $_POST['text'] = cleanText($_POST['text']);

    $Validator->validateGeneral($_POST['firstname'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_FIRST_NAME));
    $Validator->validateGeneral($_POST['text'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_STRING_TEXT));
    $Validator->validateGeneral($_POST["captcha"]["code"], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_CONFIRMATION_CODE));

    if (!empty($_POST['phone']))
        $Validator->validatePhone($_POST['phone'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, FEEDBACK_TEL));
    if (!empty($_POST['email']))
        $Validator->validateEmail($_POST['email'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, FEEDBACK_EMAIL));
    if (!$Captcha->CheckCode($_POST["captcha"]["code"], $_POST["captcha"]["sid"]))
        $Validator->addError(sprintf(ENTER_INPUT_ERROR, FEEDBACK_CONFIRMATION_CODE));

    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>".FEEDBACK_ERROR_INPUT_STRING."</font>".$Validator->getListedErrors();
    } else {
        $arData = screenData($_POST);
        
        $arData['created']  = date('Y-m-d H:i:s');
        $arData['ip']       = $_SERVER['REMOTE_ADDR'];
        $arData['server']   = $_SERVER['SERVER_NAME'];
        $arData['sitename'] = $objSettingsInfo->websiteName;
        $smarty->assign('arData', $arData);
        $text    = $smarty->fetch('mail/feedback_admin.tpl');
        $subject = $objSettingsInfo->websiteName.': '.sprintf(FEEDBACK_MESSAGE_FROM, $arCategory['title']);
        if(sendMail($objSettingsInfo->notifyEmail, $subject, $text, $objSettingsInfo->siteEmail)){
            setSessionMessage(FEEDBACK_STRING_SEND_EMAIL);
            Redirect($UrlWL->buildCategoryUrl($arCategory, 'result=success', '', $page));
        } else $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR.'. '.TRY_AGAIN_TITLE;

    }
} elseif ($action=='result' && $result=='success' && empty($arrPageData['messages'])) {
    setSessionMessage(FEEDBACK_EMAIL_RESEND_ERROR);
    Redirect($UrlWL->buildCategoryUrl($arCategory));
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]       = '<link rel="stylesheet" type="text/css" href="'.$arrPageData['css_dir'].$module.'.css" />';
//$arrPageData['headScripts'][]   = '<script type="text/javascript" src="/js/'.$module.'.js"></script>';

if(!empty($_POST)) 
    $item = array_merge($item, $_POST);

$item['action'] = $action;
$item['result'] = $result;
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

