<?php
// watermark.php

$watermark          = $_SERVER['DOCUMENT_ROOT']."/images/site/watermark.png";
$watermark_small    = $_SERVER['DOCUMENT_ROOT']."/images/site/watermark_small.png";
$placement          = 'middle=0,center=0'; // vertical: top,middle,bottom; horizontal: left,center,right;
$baseFolder         = "/uploaded/";
$minWidthToUseSmall = array('w'=>150, 'h'=>150);
$optimalWSize       = true;

if(!empty($_GET['img']) && !empty($_GET['dir']))
     waterMark($_SERVER['DOCUMENT_ROOT'].$baseFolder.$_GET['dir'].'/'.$_GET['img'], $watermark, $watermark_small, $minWidthToUseSmall, $placement, $optimalWSize);
else waterMark($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'], $watermark, $watermark_small, $minWidthToUseSmall, $placement, $optimalWSize);    

/*
 * You can add file .htaccess to directory to show all files in directory with watermark
 * and change parameter $original = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']
.htaccess content:
DirectoryIndex index.php
<FilesMatch "\.(gif|jpg|png|JPG|JPEG|jpeg)$">
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} -f
   RewriteRule ^(.*)$ /include/watermark.php [T=application/x-httpd-php,L,QSA]
</FilesMatch>
<Files "*.php">
Deny from all
</Files>
<Files "*.pl">
Deny from all
</Files>
Allow from all

 * OR You can use url in image src like  src="/include/watermark.php?imgsrc=<?=urlencode($image)."&nhash=".date('U');?>"
 * and change parameter $original = $_SERVER['DOCUMENT_ROOT'].$_GET['imgsrc']
 */

############################ FUNCTIONS #########################################
/////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function waterMark($original, $watermark, $watermark_small='', $minWidthToUseSmall = array('w'=>150, 'h'=>150), $placement = 'bottom=5,right=5', $optimalWSize = true, $destination = null) {
    $original       = urldecode($original);
    $originalname   = basename($original);

    // Get Images Size
    $info_o = @getImageSize($original);
    if (!$info_o)
        return false;
    
    if(!empty($watermark_small) AND ($info_o[0] <= $minWidthToUseSmall['w'] OR $info_o[1] <= $minWidthToUseSmall['h'])){
        $watermark = $watermark_small;
    }
    
    $info_w = @getImageSize($watermark);
    if (!$info_w)
        return false;

    if($optimalWSize){
        $newWinfo = getWSizeAndXY($info_o, $info_w, $placement);
    } else {
        // Get and Set Image Position
        list($vertical, $horizontal) = split(',', $placement,2);
        list($vertical, $sy) = split('=', trim($vertical),2);
        list($horizontal, $sx) = split('=', trim($horizontal),2);
        switch (trim($vertical)) { // VERTICAL POSITION OF WATERMARK
            case 'bottom': $y = $info_o[1] - $info_w[1] - (int)$sy; break;
            case 'middle': $y = ceil($info_o[1]/2) - ceil($info_w[1]/2) + (int)$sy; break;
            default: $y = (int)$sy; break;
        }
        switch (trim($horizontal)) { // HORIZONTAL POSITION OF WATERMARK
            case 'right': $x = $info_o[0] - $info_w[0] - (int)$sx; break;
            case 'center': $x = ceil($info_o[0]/2) - ceil($info_w[0]/2) + (int)$sx; break;
            default: $x = (int)$sx; break;
        }
    }

    // Send HEADER of Image type
    header("Content-Type: ".$info_o['mime']);

    // Create Images from files
    $original = @imageCreateFromString(file_get_contents($original));
    $watermark = @imageCreateFromString(file_get_contents($watermark));
    $out = imageCreateTrueColor($info_o[0],$info_o[1]);

    // Copy Image
    imageCopy($out, $original, 0, 0, 0, 0, $info_o[0], $info_o[1]);

    // SET TRANSPARENT
    $trans = imagecolorat($out,0,0);
    imagecolortransparent($out,$trans);

    // Copy Watermark
    if($optimalWSize){
        imagecopyresampled($out, $watermark, $newWinfo['x'], $newWinfo['y'], 0, 0, $newWinfo['w'], $newWinfo['h'], $info_w[0], $info_w[1]);
    } elseif( ($info_o[0] > 10) && ($info_o[1] > 10) ) {
        imageCopy($out, $watermark, $x, $y, 0, 0, $info_w[0], $info_w[1]);
    }

    // create Rezult Image
    switch ($info_o[2]) {
        case 1:
            imageGIF($out);
            break;
        case 2:
            imageJPEG($out);
            break;
        case 3:
            imagePNG($out);
            break;
    }

    // Delete temp images
    imageDestroy($out);
    imageDestroy($original);
    imageDestroy($watermark);

    return true;
}

function getWSizeAndXY($info_o, $info_w, $placement){
    $placement = trim($placement);
    if(empty($placement)) return array('w'=>(($info_o[0] < $info_w[0]) ? $info_o[0] : $info_w[0]),'h'=>(($info_o[1] < $info_w[1]) ? $info_o[1] : $info_w[1]), 'x'=>0, 'y'=>0);

    $w = $info_o[0];
    $h = $info_o[1];

    $oWBig = ($w > $h) ? true : false;
    $wBig = ($info_w[0] > $info_w[1]) ? true : false;
    $whWRate = (($wBig) ? floatval($info_w[0]/$info_w[1]) : (($info_w[0] == $info_w[1]) ? 1 : floatval($info_w[1]/$info_w[0])));
    $placements = array();

    $aligns = split(',', $placement, 2);
    foreach($aligns as $align){
        list($align, $px) = split('=', trim($align), 2);
        $align = trim($align);
        $px = intval($px);
        $margins = $px * 2;
        $placements[$align]=$px;
        switch ($align) {
            //// MAX width OF WATERMARK
            case 'right'    : $wmax = ($wBig) ? $w - $margins : $h/$whWRate; $x = $w - $wmax - $px; break;
            case 'center'   : $wmax = ($wBig) ? $w - $margins : $h/$whWRate; $x = ceil($w/2) - ceil($wmax/2) + $px; break;
            case 'left'     : $wmax = ($wBig) ? $w - $margins : $h/$whWRate; $x = $px; break;
            //// MAX height OF WATERMARK
            case 'bottom'   : $hmax = ($wBig) ? $w/$whWRate : $h - $margins; $y = $h - $hmax - $px; break;
            case 'middle'   : $hmax = ($wBig) ? $w/$whWRate : $h - $margins; $y = ceil($h/2) - ceil($hmax/2) + $px; break;
            case 'top'      : $hmax = ($wBig) ? $w/$whWRate : $h - $margins; $y = $px; break;
        }
    }

    if($wmax > $w){
        $wmax = $w;
        $hmax = ($wBig) ? $wmax*$whWRate : $wmax/$whWRate;
    }
    if($hmax > $h){
        $hmax = $h;
        $wmax = ($wBig) ? $hmax/$whWRate : $hmax*$whWRate;
    }

    foreach($placements as $align=>$px){
        switch ($align) {
            //// HORIZONTAL POSITION OF WATERMARK
            case 'right'    :  $x = $w - $wmax - $px; break;
            case 'center'   :  $x = ceil($w/2) - ceil($wmax/2) + $px; break;
            case 'left'     :  $x = $px; break;
            //// VERTICAL POSITION OF WATERMARK
            case 'bottom'   :  $y = $h - $hmax - $px; break;
            case 'middle'   :  $y = ceil($h/2) - ceil($hmax/2) + $px; break;
            case 'top'      :  $y = $px; break;
        }
    }    

    return array('w'=>round($wmax),'h'=>round($hmax), 'x'=>$x, 'y'=>$y);
}
?>