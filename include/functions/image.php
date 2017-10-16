<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

function createThumb($file, $maxwdt, $maxhgt, $dest) {
    list($owdt,$ohgt,$otype)=@getimagesize($file);

    switch($otype) {
        case 1:
            $newimg=imagecreatefromgif($file);
            break;
        case 2:
            $newimg=imagecreatefromjpeg($file);
            break;
        case 3:
            $newimg=imagecreatefrompng($file);
            break;
        default:
            echo "Unkown filetype (file: $file; type: $otype)";
            return false;
    }

    if($newimg) {
        if($owdt>1500 || $ohgt>1200) list($owdt, $ohgt) = Resample($newimg, $owdt, $ohgt, 1024,768,0);
        if (($owdt > $maxwdt) || ($ohgt > $maxhgt)) Resample($newimg, $owdt, $ohgt, $maxwdt, $maxhgt);
        if(!$dest) return $newimg;
        if(!is_dir(dirname($dest))) mkdir(dirname($dest));

        switch($otype) {
            case 1:
                imagegif($newimg,$dest);
                break;
            case 2:
                imagejpeg($newimg,$dest,90);
                break;
            case 3:
                imagepng($newimg,$dest);
                break;
        }
        imagedestroy($newimg);
        chmod($dest,0644);
        return true;
    }
    return false;
}

function Resample(&$img, $owdt, $ohgt, $maxwdt, $maxhgt, $quality=1) {
    if(!$maxwdt) $divwdt=0;
    else $divwdt=Max(1,$owdt/$maxwdt);

    if(!$maxhgt) $divhgt=0;
    else $divhgt=Max(1,$ohgt/$maxhgt);

    if($divwdt>=$divhgt) {
        $newwdt=$maxwdt;
        $newhgt=round($ohgt/$divwdt);
    }
    else {
        $newhgt=$maxhgt;
        $newwdt=round($owdt/$divhgt);
    }

    $tn=imagecreatetruecolor($newwdt,$newhgt);
    if($quality) imagecopyresampled($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);
    else imagecopyresized($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);
    imagedestroy($img);

    $img = $tn;
    return array($newwdt, $newhgt);
}

/**
 * Examples:
 *  createThumbWithBg('uploaded/img.jpg', 'uploaded/img1.jpg', 150, 150, array(228, 238, 252), 90, 1);
 *  createThumbWithBg('uploaded/img.png', 'uploaded/img1.png', 150, 150, array('r'=>255, 'g'=>0, 'b'=>255), 90, 1);
 * @param String $file
 * @param String $dest
 * @param int $bgwdt
 * @param int $bghgt
 * @param bool $transparent if true image will be gif file
 * @param Array $bgRGB
 * @param int $quality
 * @param bool $isQuality
 * @return mixed  - if $dest empty return new image resourse else return bool result
 */
function createThumbWithBg($file, $dest, $bgwdt, $bghgt, $transparent=false, $bgRGB=array('r'=>255, 'g'=>255, 'b'=>255), $quality=90){
    if(!$bgwdt || !$bghgt) return false;
    
    list($owdt, $ohgt, $otype)=@getImageSize($file);
    $img = @imageCreateFromString(file_get_contents($file));
    if($img) {
        // create bg for image with $bgWidth and $bgHeight params
        list($r, $g, $b) = array_values($bgRGB);
        $out = imageCreateTrueColor($bgwdt, $bghgt);
        $bgc = imageColorAllocate($out, intval($r), intval($g), intval($b));
        
        // fill new image
        imageFilledRectangle ($out, 0, 0, $bgwdt, $bghgt, $bgc);
        
        // reInit new img size to resample if width or height bigger than bg size
        if($owdt>$bgwdt || $ohgt>$bghgt) {
            $divwdt = Max(1, $owdt/$bgwdt);
            $divhgt = Max(1, $ohgt/$bghgt);
            if($divwdt>=$divhgt){ $newwdt = $bgwdt; $newhgt = round($ohgt/$divwdt); }
            else {                $newhgt = $bghgt; $newwdt = round($owdt/$divhgt); }
        } else {                  $newwdt = $owdt;  $newhgt = $ohgt; }
        
        // center image
        $dstx = ($bgwdt==$newwdt) ? 0 : floor(($bgwdt-$newwdt)/2);
        $dsty = ($bghgt==$newhgt) ? 0 : floor(($bghgt-$newhgt)/2);
        
        // copy resampled image to bg
        imageCopyResampled($out, $img, $dstx, $dsty, 0, 0, $newwdt, $newhgt, $owdt, $ohgt);
        
        // destroy $newimg
        imagedestroy($img);
        
        // if $dest is undefined - return image resourse 
        if(empty($dest)) return $out;
        
        if($transparent){
            imageColorTransparent($out, $bgc); // делаем цвет фона прозрачным 
            if(strtolower(($ext = end(explode('.', $dest))))!='png') $dest = str_replace('.'.$ext, '.png', $dest);
            $otype = 3; // измен€ем тип вывода
        }
        
        // dir is upsent - create it
        if(!is_dir(dirName($dest))) mkDir(dirName($dest));
        
        // create file image
        switch($otype) {
            case 1: imageGIF($out,  $dest);           break;
            case 2: imageJPEG($out, $dest, $quality); break;
            case 3: imagePNG($out,  $dest);           break;
        } imageDestroy($out);
        @chmod($dest, 0644);
        return true;
    } return false;
}
