<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file

// LINK: /interactive/captcha.php?zone=[site|admin]&sid=[uniqid]

$zone =  (!empty($_GET['zone'])) ? $_GET['zone'] : false;
$sid  =  (!empty($_GET['sid']))  ? $_GET['sid']  : false;

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
    require_once('include/classes/Cookie.php');         // 2. Include Cookie class file
    $Cookie     = new CCookie();
    require_once('include/system/SystemComponent.php'); // 3. Include DB configuration file Must be included before other
    require_once('include/system/DefaultLang.php');     // 4. Include Languages File
    require_once('include/system/tables.php');          // 5. Include DB tables File
    require_once('include/classes/DbConnector.php');    // 6. Include DB class
    require_once('include/classes/Captcha.php');        // 7. Include Captcha class
    $DB         = new DbConnector();
    $Captcha    = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################

    $Captcha->SetTTFFontsPath('fonts');
    $Captcha->SetTTFFonts(array('arial.ttf', 'verdana.ttf', 'captcha.ttf', 'elephant.ttf'));

    switch($zone){
        // ---------------------------------------------------------------------
        case 'admin':
            $Captcha->SetEllipsesNumber(0);
            $Captcha->SetLinesNumber(0);
            $Captcha->SetTextTransparent(false);
            $Captcha->SetTextWriting(-10, 10, 7, 12, 14, 12); // ($angleFrom,$angleTo,$startX,$distanceFrom,$distanceTo,$fontSize)
            $Captcha->SetImageSize(110, 22);
            $Captcha->SetBGColor(array(0, 80, 179));
            $Captcha->SetTextColor(array(array(255, 255), array(255, 255), array(255, 255)));
            $Captcha->SetBorderColor(array(180, 180, 180));
            $Captcha->SetCodeLength(IVALIDATOR_MAX_LENTH);
            break;

        // ---------------------------------------------------------------------
        case 'site':
            $Captcha->SetTextTransparent(true, 12);
            $Captcha->SetBGColorRGB("D6E1E8", "D6E1E8");
            $Captcha->SetEllipsesNumber(0);
            $Captcha->SetEllipseColorRGB("7F7F7F", "FFFFFF");
            $Captcha->SetLinesOverText(true);
            $Captcha->SetLinesNumber(0);
            $Captcha->SetLineColorRGB("6E6E6E", "FAFAFA");
            $Captcha->SetTextWriting(-7, 7, 5, 14, 15, 15); // ($angleFrom,$angleTo,$startX,$distanceFrom,$distanceTo,$fontSize)
            $Captcha->SetTextColorRGB("13571E", "13571E");
            $Captcha->SetWaveTransformation(false);
            $Captcha->SetBorderColorRGB("D6E1E8");
            $Captcha->SetImageSize(85, 36);
            break;
        // ---------------------------------------------------------------------
        default: exit(); break;
    }

    // FOR TEST Uncoment This For Lines Below
//    $Captcha->SetCodeChars(str_split("abcdefghijknmpqrstuvwxyz0123456789"));
//    $Captcha->SetCodeLength(5);
//    $Captcha->SetCode();
//    $sid = $Captcha->GetSID();
    # ---------------------------------------

    if ($sid && $Captcha->InitCode($sid))
         $Captcha->Output();
    else $Captcha->OutputError();

}

# ########################## EXAMPLES ##########################################
/* white background, dark chars, lines, ellipses */
//$Captcha->SetBGColor(array(255, 255, 255));
//$Captcha->SetTextColor(array(array(0, 100), array(0, 100), array(0, 100)));
//$Captcha->SetEllipseColor(array(array(127, 255), array(127, 255), array(127, 255)));
//$Captcha->SetLineColor(array(array(110, 250), array(110, 250), array(110, 250)));

/* black background, light chars, lines, ellipses */
/*
$Captcha->SetBGColor(array(0, 0, 0));
$Captcha->SetTextColor(array(array(127, 255), array(127, 255), array(127, 255)));
$Captcha->SetEllipseColor(array(array(10, 120), array(10, 120), array(10, 120)));
$Captcha->SetLineColor(array(array(10, 120), array(10, 120), array(10, 120)));
*/

/* near while background, near gray chars, near gray lines */
/*
$Captcha->SetBGColor(array(array(200, 255), array(200, 255), array(200, 255)));
$Captcha->SetTextColor(array(array(100, 140), array(100, 140), array(100, 140)));
$Captcha->SetEllipsesNumber(0);
$Captcha->SetLinesNumber(20);
$Captcha->SetLineColor(array(array(100, 140), array(100, 140), array(100, 140)));
*/
