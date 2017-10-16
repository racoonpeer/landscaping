<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

function getSessId($sesName, $arData=array()) {
    if(!empty($arData[$sesName]))   return addslashes($arData[$sesName]);
    if(!empty($_REQUEST[$sesName])) return addslashes($_REQUEST[$sesName]);
    if(!empty($_POST[$sesName]))    return addslashes($_POST[$sesName]);
    if(!empty($_GET[$sesName]))     return addslashes($_GET[$sesName]);
    return session_id();
}

function setSessId($sesName, $arData=array()) {
    $session_id = getSessId($sesName, $arData);
    if(!empty($session_id)){
        session_id($session_id);
        return true;
    }   return false;
}

function checkErrorLoginInSession($key, $setCount=true, $link='/login.php?log=banned'){
    if ($setCount && !isset($_SESSION[$key]))            $_SESSION[$key]['count']  = 1;
    elseif($setCount && isset($_SESSION[$key]['count'])) $_SESSION[$key]['count'] += 1;

    if (isset($_SESSION[$key]['count']) && $_SESSION[$key]['count'] > MAX_WRONG_PASSWORDS) {
        $_SESSION[$key]['time'] = time();
        unset($_SESSION[$key]['count']);
        if($link) Redirect($link);
    } elseif (isset($_SESSION[$key]['time']) && (time() - $_SESSION[$key]['time']) > BANNED_TIME) {
        unset($_SESSION[$key]);
    }
}

function getCacheId($def_cache_id=''){
    global $lang;
    if(empty($_GET)) return $def_cache_id.$lang;
    $def_cache_id = '';
    if(WLCMS_ZONE=='BACKEND'){
        if(!TPL_BACKEND_CACHING)        return null;
        if(!empty($_GET['module']))     $def_cache_id .= $_GET['module'];
        if(!empty($_GET['page']))       $def_cache_id .= '-p'.$_GET['page'];
    } else {
        if(!TPL_FRONTEND_CACHING)       return null;
        if(!empty($_GET['catid']))      $def_cache_id .= 'c'.$_GET['catid'];
        if(!empty($_GET['itemID']))     $def_cache_id .= 'i'.$_GET['itemID'];
        if(!empty($_GET['subItemID']))  $def_cache_id .= 'si'.$_GET['subItemID'];
        if(!empty($_GET['page']))       $def_cache_id .= 'p'.$_GET['page'];
    }
    $cache_id = '';
    foreach($_GET as $k=>$v) $cache_id .= "$k=$v&";
    foreach(getSessionMessages('', false) as $k=>$v) $cache_id .= "$k=$v&";
    foreach(getSessionErrors('', false) as $k=>$v)   $cache_id .= "$k=$v&";
    if(sizeof($_POST)){
        foreach($_POST as $k=>$v) $cache_id .= "$k=$v&";
    } return $def_cache_id.($def_cache_id!='' ? '-' : '').substr(md5($cache_id.$lang), 0, 17);
}

function getTemplateFileName($ajax, $catid=0){
    if(WLCMS_ZONE=='BACKEND')
         return ($ajax ? 'ajax' : 'admin').'.tpl';
    else return ($ajax ? 'ajax' : ($catid>1 ? 'page' : 'index')).'.tpl';
}

function getAuthFileName(){
    return WLCMS_ZONE=='BACKEND' ? 'auth_backend' : 'auth_frontend';
}

function getIValidatorPefix(){
    return WLCMS_ZONE;
}

function getLangKeyNameForSession(){
    return WLCMS_ZONE=='BACKEND' ? 'admlng' : 'lang';
}

function getMessagesKeyNameForSession(){
    return WLCMS_ZONE.'_MESS';
}

function getErrorsKeyNameForSession(){
    return WLCMS_ZONE.'_ERR';
}

function getReturnUrlKeyNameForSession(){
    return WLCMS_ZONE.'_RETURN_URL';
}

function getUserKeyNameForSession(){
    return WLCMS_ZONE=='FRONTEND' ? 'suser_obj' : (WLCMS_ZONE=='BACKEND' ? 'auser_obj' : 'guest_obj');
}

function setZoneToSession($zone=''){
    $_SESSION['WLCMS_ZONE'] = $zone ? $zone : WLCMS_ZONE;
}

function setReturnUrlToSession($url, $prefix='/'){
    $url = trim($url);
    $_SESSION[getReturnUrlKeyNameForSession()] = $url ? $prefix.ltrim($url, $prefix) : '';
}

function getReturnUrlFromSession($defurl=''){
    $key    = getReturnUrlKeyNameForSession();
    $defurl = empty($_SESSION[$key]) ? $defurl : $_SESSION[$key];
    $_SESSION[$key] = '';
    return $defurl;
}

function setUserToSession($objUserInfo){
    $key = getUserKeyNameForSession();
    $_SESSION[$key] = !empty($objUserInfo) ? $objUserInfo : (object)array('login'=>'', 'password'=>md5(''), 'logined'=>0);
    return isset($_SESSION[$key]) ? true : false;
}

function getUserFromSession($arDefKeys=array()){
    $key = getUserKeyNameForSession();
    if(!isset($_SESSION[$key])){
        $arEmptyUser = !empty($arDefKeys) ? array_combine_multi($arDefKeys, '') : array();
        $arEmptyUser['login']    = '';
        $arEmptyUser['password'] = md5('');
        $arEmptyUser['logined']  = 0;
        $_SESSION[$key] = (object)$arEmptyUser;
    }   return $_SESSION[$key];
}

function unsetUserFromSession(){
    $key = getUserKeyNameForSession();
    unset($_SESSION[$key]);
    #session_unregister($key);
    return isset($_SESSION[$key]) ? false : true;
}

function getSessionMessages($key='', $unset=true){
    $sKey  = getMessagesKeyNameForSession();
    $bArr  = empty($key) ? true : false;
    $mixed = $bArr ? array() : '';
    if(isset($_SESSION[$sKey])){
        if($bArr){
            $mixed = $_SESSION[$sKey];
            if($unset) unset($_SESSION[$sKey]);
        }elseif(isset($_SESSION[$sKey][$key])){
            $mixed = $_SESSION[$sKey][$key];
            if($unset) unset($_SESSION[$sKey][$key]);
        }
    } return $mixed;
}

function setSessionMessage($str, $key=''){
    if($key!='')
         $_SESSION[getMessagesKeyNameForSession()][$key]=$str;
    else $_SESSION[getMessagesKeyNameForSession()][]=$str;
}

function getSessionErrors($key='', $unset=true){
    $sKey  = getErrorsKeyNameForSession();
    $bArr  = empty($key) ? true : false;
    $mixed = $bArr ? array() : '';
    if(isset($_SESSION[$sKey])){
        if($bArr){
            $mixed = $_SESSION[$sKey];
            if($unset) unset($_SESSION[$sKey]);
        }elseif(isset($_SESSION[$sKey][$key])){
            $mixed = $_SESSION[$sKey][$key];
            if($unset) unset($_SESSION[$sKey][$key]);
        }
    } return $mixed;
}

function setSessionErrors($str, $key=''){
    if($key!='') 
         $_SESSION[getErrorsKeyNameForSession()][$key]=$str;
    else $_SESSION[getErrorsKeyNameForSession()][]=$str;
}

function replaceLang($ln, $table){
    global $lang;
    return str_replace($lang.DBTABLE_LANG_SEP, $ln.DBTABLE_LANG_SEP, $table);
}

function dbLangsSynchronize($sql, $table, $curr_ln, $arAcceptLangs) {
    $errors = '';
    foreach($arAcceptLangs as $ln){
        $ln_sql = $ln==$curr_ln ? $sql : str_replace($table, replaceLang($ln, $table), $sql);
        if(!($result = mysql_query($ln_sql))){
            $errors .= "ERROR in dbLangsSynchronize function: $ln_sql<br/>\n";
        }
    } return $errors=='' ? true : $errors;
}

function screenData($item, $bApplyTrim=true) {
    if(is_object($item)) $item = (array)$item;
    if(is_array($item)) {
        foreach($item as $key=>$value) {
            $item[$key] = screenData($item[$key], $bApplyTrim);
        }
    } else if(!is_bool($item) && $item) {
        if($bApplyTrim) $item = trim($item);
        if($item) $item = addslashes(htmlspecialchars(stripslashes($item), ENT_QUOTES));
    } return $item;
}

function unScreenData($item, $bApplyTrim=true) {
    if(is_object($item)) $item = (array)$item;
    if(is_array($item)) {
        foreach($item as $key=>$value) {
            $item[$key] = unScreenData($item[$key], $bApplyTrim);
        }
    } else if(!is_bool($item) && $item) {
        if($bApplyTrim) $item = trim($item);
        if($item) $item = htmlspecialchars_decode(stripslashes($item), ENT_QUOTES);
    } return $item;
}

function dataApplayFunc($item, $func) {
    if(is_object($item)) $item = (array)$item;
    if(is_array($item)) {
        foreach($item as $key=>$value) {
            $item[$key] = dataApplayFunc($item[$key], $func);
        }
    } else if(!is_bool($item) && $item) {
        $item = trim($item);
        if($item) $item = $func($item);
    } return $item;
}

function xCallFunc($func, $data, $params = null) {
    if(!is_array($data)) return call_user_func_array($func, array($data, $params));
    foreach($data as $key=>$value) $data[$key] = xCallFunc($func, $value, $params);
    return $data;
}


// IMAGES FUNCTIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function getArrImageSize($idir, $image){
    if(!empty($image)){
        $image = prepareDirPath($idir).$image;
        if(file_exists($image)){
            $arr = getimagesize($image);
            $arr = array_merge($arr, array('w'=>$arr[0], 'h'=>$arr[1], 't'=>$arr[2]));
        }
    } return isset($arr) ? $arr : false;
}

function imageSmartResize($id, $table, $images_dir, $params = false, $colname='image', $arExtAllowed=null, $method = 'crop'){
    $idir = prepareDirPath($images_dir, true);

    if(!empty($_POST[$colname.'_delete'])){
        if(!empty($_POST['toAllLangs'])){ 
            unlinkImageLangsSynchronize($id, $table, $idir, $params, $colname); 
            
        } else { 
            unlinkImage($id, $table, $idir, $params, true, $colname); 
        }
        if(!($_FILES[$colname]['size']>0)) {
            return "NULL";
        }
    }

    if(!empty($_FILES[$colname])){
        $iname     = $_FILES[$colname]['name']; //имя файла до его отправки на сервер (pict.gif)
        $itmp_name = $_FILES[$colname]['tmp_name']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
        list($itmp_w, $itmp_h) = @getimagesize($itmp_name);
        $iw = !empty($_POST[$colname.'_w']) ? intval($_POST[$colname.'_w']) : $itmp_w; // 66 defoult width
        $ih = !empty($_POST[$colname.'_h']) ? intval($_POST[$colname.'_h']) : $itmp_h; // 88 default height
        $origin    = ($iw >= $ih)? 'horz': 'vert';
        if($iname && $itmp_name) {
            unlinkImage($id, $table, $idir, $params, true, $colname);
            $file_ext = getFileExt($iname);
            if(empty($arExtAllowed)) {
                $arExtAllowed = array('jpeg','jpg','gif','png');
            }
            if (in_array($file_ext, $arExtAllowed)) {
                $new_name = createUniqueFileName($idir, $file_ext, basename($iname, '.'.$file_ext));
                $image = WideImage::load($itmp_name);
                switch ($method) {
                    case 'resize':
                        if($origin == 'horz') {
                            $resized = $image->resize($iw, $ih, 'inside')->saveToFile($idir.$new_name);
                        } else {
                            $resized = $image->resize($iw, $ih, 'outside')->saveToFile($idir.$new_name);
                        }
                        break;
                    case 'crop':
                        if($origin == 'horz') {
                            $resized = $image->resize($iw, $ih, 'outside');
                        } else {
                            $resized = $image->resize($iw, round($ih*2), 'outside');
                        }
                        $cropped = $resized->crop('center', 'middle', $iw, $ih)->saveToFile($idir.$new_name);
                            
                        break;
                    case 'fit':
                        if($origin == 'horz') {
                            $resized = $image->resize($iw, $ih, 'inside');
                        } else {
                            $resized = $image->resize($iw, round($ih*2), 'outside');
                        }
                        $cropped = $resized->crop('center', 'middle', $iw, $ih);
                        $fitted = $cropped->resizeCanvas($iw, $ih, 'center', 'middle', $cropped->allocateColorAlpha(255, 255, 255), 'any', TRUE)->saveToFile($idir.$new_name);
                        break;
                }

                if(!file_exists($idir.$new_name)) {
                    return false;
                }

                while($params && (list(, list($partiname, $piw, $pih)) = each($params))){
                    $image = WideImage::load($itmp_name);

                    switch ($method) {
                        case 'resize':
                            if($origin == 'horz') {
                                $resized = $image->resize(round($piw*2), $pih, 'outside')->saveToFile($idir.$partiname.$new_name);
                            } else {
                                $resized = $image->resize($piw, round($pih*2), 'outside')->saveToFile($idir.$partiname.$new_name);
                            }
                            break;
                        case 'crop':
                            if($origin == 'horz') {
                                $resized = $image->resize($piw, $pih, 'outside');
                            } else {
                                $resized = $image->resize($piw, $pih, 'outside');
                            }
                            $cropped = $resized->crop('center', 'middle', $piw, $pih)->saveToFile($idir.$partiname.$new_name);
                            break;
                        case 'fit':
                            if($origin == 'horz') {
                                $resized = $image->resize(round($piw*2), $pih, 'outside');
                            } else {
                                $resized = $image->resize($piw, round($pih*2), 'outside');
                            }
                            $cropped = $resized->crop('center', 'middle', $piw, $pih);
                            $fitted = $cropped->resizeCanvas($piw, $pih, 'center', 'middle', $cropped->allocateColorAlpha(255, 255, 255), 'any', TRUE)->saveToFile($idir.$partiname.$new_name);
                            break;
                    }
                } 
                return $new_name;
            } else { 
                echo 'Недопустимый тип файла'; 
            }
        }
    } return false;
}

function imageManipulation($id, $table, $images_dir, $params = false, $colname='image', $arExtAllowed=null){
    $idir = prepareDirPath($images_dir, true);

    if(!empty($_POST[$colname.'_delete'])){
        if(!empty($_POST['toAllLangs'])){ unlinkImageLangsSynchronize($id, $table, $idir, $params, $colname); }
        else { unlinkImage($id, $table, $idir, $params, true, $colname); }
        if(!($_FILES[$colname]['size']>0)) return "NULL";
    }

    if(!empty($_FILES[$colname])){
        $iname     = $_FILES[$colname]['name']; //имя файла до его отправки на сервер (pict.gif)
        $itmp_name = $_FILES[$colname]['tmp_name']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
        list($itmp_w, $itmp_h) = @getimagesize($itmp_name);
        $iw = !empty($_POST[$colname.'_w']) ? intval($_POST[$colname.'_w']) : $itmp_w; // 66 defoult width
        $ih = !empty($_POST[$colname.'_h']) ? intval($_POST[$colname.'_h']) : $itmp_h; // 88 default height
        if($iname && $itmp_name) {
            unlinkImage($id, $table, $idir, $params, true, $colname);
            $file_ext = getFileExt($iname);
            if(empty($arExtAllowed)) $arExtAllowed = array('jpeg','jpg','gif','png');
            if (in_array($file_ext, $arExtAllowed)) {
                $new_name = createUniqueFileName($idir, $file_ext, basename($iname, '.'.$file_ext));
                if(copy($itmp_name, $idir.$new_name)){
                    if( ($iw != $itmp_w || $ih != $itmp_h) && !createThumb($itmp_name, $iw, $ih, $idir.$new_name) ){ return false; }
                    while($params && (list(, list($partiname, $piw, $pih)) = each($params))){
                        createThumb($itmp_name, $piw, $pih, $idir.$partiname.$new_name);
                    } return $new_name;
                }
            } else { echo 'Недопустимый тип файла'; }
        }
    } return false;
}

function imageManipulationWithBg($id, $table, $images_dir, $params = false, $colname='image', $arExtAllowed=null, $transparent=false, $bgRGB=array('r'=>255, 'g'=>255, 'b'=>255), $quality=90){
    $idir = prepareDirPath($images_dir, true);

    if(!empty($_POST[$colname.'_delete'])){
        if(!empty($_POST['toAllLangs'])){ unlinkImageLangsSynchronize($id, $table, $idir, $params, $colname); }
        else { unlinkImage($id, $table, $idir, $params, true, $colname); }
        if(!($_FILES[$colname]['size']>0)) return "NULL";
    }

    if(!empty($_FILES[$colname])){
        $iname     = $_FILES[$colname]['name']; //имя файла до его отправки на сервер (pict.gif)
        $itmp_name = $_FILES[$colname]['tmp_name']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
        list($itmp_w, $itmp_h) = @getimagesize($itmp_name);
        $iw = !empty($_POST[$colname.'_w']) ? intval($_POST[$colname.'_w']) : $itmp_w; // 66 defoult width
        $ih = !empty($_POST[$colname.'_h']) ? intval($_POST[$colname.'_h']) : $itmp_h; // 88 default height
        if($iname && $itmp_name) {
            unlinkImage($id, $table, $idir, $params, true, $colname);
            $file_ext = getFileExt($iname);
            if(empty($arExtAllowed)) $arExtAllowed = array('jpeg','jpg','gif','png');
            if (in_array($file_ext, $arExtAllowed)) {
                $new_name = createUniqueFileName($idir, $transparent ? 'png' : $file_ext, basename($iname, '.'.$file_ext));
                if(!createThumbWithBg($itmp_name, $idir.$new_name, $iw, $ih, $transparent, $bgRGB, $quality)){ return false; }
                while($params && ($param = each($params))){
                    if(count(($param = $param['value']))<3) continue;
                    //array(0=>префикс, 1=>ширина, 2=>высота, 3=>прозрачный, 4=>массив_цвета_RGB, 5=>jpg_качество)
                    $param = array_replace(array('_', 0, 0, $transparent, $bgRGB, $quality), $param);
                    createThumbWithBg($itmp_name, $idir.$param[0].$new_name, $param[1], $param[2], $param[3], $param[4], $param[5]);
                } return $new_name;
            } else { echo 'Недопустимый тип файла'; }
        }
    } return false;
}

function imageManipulationWithCrop(&$arPostData, &$arUnusedKeys, $files_url,  $files_path, $task, $itemID, $module) {
    if(!empty($arPostData['imagesParams'])) {
        foreach($arPostData['imagesParams'] as $column => $data) {
            if(empty($data)) {
                $arUnusedKeys[] = $column;
            } else {
                $arItem = ImagesUpload::PrepareImagesParams($module, $column);
                $ImagesUpload = new ImagesUpload($files_url, $itemID); 
                $arImageData = (array)json_decode(stripslashes($data)); 
                if(!empty($arImageData)) {
                    $new_name = $ImagesUpload->cropImage($arImageData, $arItem);
                    // сохраняем в базу, если изображение создалось
                    if($new_name && file_exists($files_path.$new_name)) {
                        if($task=='editItem') {
                            unlinkImage($itemID, $arItem['ptable'], $files_path, $arItem['aliases'], false, $arItem['column']);
                        }
                        $arPostData[$column] = $new_name;
                    }
                }
            }
        }
    }
}

function imageUpload($images_dir, $params = false, $colname='image', $arExtAllowed=null, $delImgBefore=''){
    $idir = prepareDirPath($images_dir, true);
    if(is_array($_FILES[$colname])){
        $iname     = $_FILES[$colname]['name']; //имя файла до его отправки на сервер (pict.gif)
        $itmp_name = $_FILES[$colname]['tmp_name']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
        list($itmp_w, $itmp_h) = @getimagesize($itmp_name);
        $iw = !empty($_POST[$colname.'_w']) ? intval($_POST[$colname.'_w']) : $itmp_w; // 66 defoult width
        $ih = !empty($_POST[$colname.'_h']) ? intval($_POST[$colname.'_h']) : $itmp_h; // 88 default height
        if($iname && $itmp_name) {
            $file_ext = getFileExt($iname);
            if(empty($arExtAllowed)) $arExtAllowed = array('jpeg','jpg','gif','png');
            if (in_array($file_ext, $arExtAllowed)) {
                if(!empty($delImgBefore)) unlinkUnUsedImage($delImgBefore, $idir, $params);
                $new_name = createUniqueFileName($idir, $file_ext, basename($iname, '.'.$file_ext));
                if(move_uploaded_file($itmp_name, $idir.$new_name)){
                    if( ($iw != $itmp_w || $ih != $itmp_h) && !createThumb($idir.$new_name, $iw, $ih, $idir.$new_name) ){ return false; }
                    while($params && (list(, list($partiname, $piw, $pih)) = each($params))){
                        createThumb($idir.$new_name, $piw, $pih, $idir.$partiname.$new_name);
                    } return $new_name;
                }
            }
        }
    } return false;
}

function imageRenameWithParams($srcfile, $destname, $destdir, $params = false, $def_params=array('w'=>0, 'h'=>0)){
    $idir  = prepareDirPath($destdir, true);
    if(is_file($srcfile)){
        list($itmp_w, $itmp_h) = @getimagesize($srcfile);
        $iw    = !empty($def_params['w']) ? intval($def_params['w']) : $itmp_w; // defoult width
        $ih    = !empty($def_params['h']) ? intval($def_params['h']) : $itmp_h; // default height
        $iname = basename($srcfile);
        if($iname) {
            unlinkUnUsedImage($destname, $idir, $params);
            $file_ext = getFileExt($iname);
            if(rename($srcfile, $idir.$destname)){
                if( ($iw != $itmp_w || $ih != $itmp_h) && !createThumb($idir.$destname, $iw, $ih, $idir.$destname) ){ return false; }
                while($params && (list(, list($partiname, $piw, $pih)) = each($params))){
                    createThumb($idir.$destname, $piw, $pih, $idir.$partiname.$destname);
                } return $destname;
            }
        }
    } return false;
}

function unlinkImage($id, $table, $idir, $params = false, $checkToDelete = true, $colname='image', $isAsyncid = false) {
    if (intval($id) > 0) { // delete old
        $conditions = ' WHERE `id`='.$id;
        $query = 'SELECT `'.$colname.'` FROM `'.$table.'`'.$conditions;
        $result = mysql_query($query);
        if ($result && ($myrow = mysql_fetch_object($result)) && !empty($myrow->$colname)) {
            if ($checkToDelete){
                if($isAsyncid) $conditions =  ' WHERE `filename`="'.$myrow->$colname.'"';
                if (!isUsedByOtherLangs($myrow->$colname, $table, $colname, $conditions)) 
                    unlinkUnUsedImage($myrow->$colname, $idir, $params);
            } else  unlinkUnUsedImage($myrow->$colname, $idir, $params);
            if(!file_exists(prepareDirPath($idir).$myrow->$colname))
                return updateDBLangsSync($table, "`{$colname}`=NULL", "WHERE `{$colname}`='{$myrow->$colname}'");
        }
    } return false;
}

function unlinkImageLangsSynchronize($id, $table, $idir, $params = false, $colname='image') {
    global $arAcceptLangs;
    foreach($arAcceptLangs as $ln){
        unlinkImage($id, replaceLang($ln, $table), $idir, $params, false, $colname);
    }
}

function unlinkImages($idir, $table, $selcolname, $conditions = '', $valuename = '', $params = false, $deleteFromDB = false, $checkToDelete = true) {
    global $lang;
    $idir = prepareDirPath($idir);
    if(empty($valuename)) $valuename = trim($selcolname, '` ');
    if($checkToDelete && strpos($table, $lang)===false) $checkToDelete = false;
    $query = "SELECT $selcolname FROM $table".((!empty($conditions)) ? " $conditions" : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        if(!empty($row[$valuename])) {
            if ($checkToDelete){
                if(!isUsedByOtherLangs($row[$valuename], $table, $selcolname, $conditions))
                    unlinkUnUsedImage($row[$valuename], $idir, $params);
            } else  unlinkUnUsedImage($row[$valuename], $idir, $params);
            if(!file_exists($idir.$row[$valuename])){
                $func = ($deleteFromDB ? 'delete' : 'update').($checkToDelete ? 'DBLangsSync' : 'Records');
                if($deleteFromDB) $func($table, "WHERE `{$valuename}`='{$row[$valuename]}'");
                else $func($table, "`{$valuename}`=NULL", "WHERE `{$valuename}`='{$row[$valuename]}'");
            }
        }
    }
}

function unlinkImagesLnSync($idir, $table, $selcolname, $conditions = '', $valuename = '', $params = false, $deleteFromDB = false) {
    global $arAcceptLangs;
    foreach($arAcceptLangs as $ln){
        unlinkImages($idir, replaceLang($ln, $table), $selcolname, $conditions, $valuename, $params, $deleteFromDB, false);
    }
}

function unlinkUnUsedImage($image, $idir, $params = false){
    $idir = prepareDirPath($idir);
    if ($image && file_exists($idir.$image)) {
        @unlink($idir.$image);
        while($params && (list(, list($partiname, $piw, $pih)) = each($params))){
            @unlink($idir.$partiname.$image);
        }
    }
}
// END IMAGES FUNCTIONS ////////////////////////////////////////////////////////


// FILES FUNCTIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function copyFile($arFile, $dest_dir, $dest_file='', $arExtAllowed=array('pdf','doc','xls','xlsx','csv','jpg','png','txt')){
    if(!empty($arFile) && is_array($arFile) && $arFile['size'] > 0 && in_array(getFileExt($arFile['name']), $arExtAllowed)){
        $dest_dir  = prepareDirPath($dest_dir, true);
        if(!strlen($dest_file)) $dest_file = $arFile['name'];
        $file_ext  = getFileExt($dest_file);
        $dest_file = createUniqueFileName($dest_dir, $file_ext, basename($dest_file, '.'.$file_ext));

        if(empty($_FILES))  $result = copy($arFile['tmp_name'], $dest_dir.$dest_file);
        else  $result = move_uploaded_file($arFile['tmp_name'], $dest_dir.$dest_file);

        if($result) return $dest_file;
    }
    return false;
}

function fileUpload_addToDB($fileKey, $id, $table, $colname, $conditions='', $dir='', $arExtAllowed=array(), $deleteFirst = false, $toAllLangs = false, $checkToDelete = true){
    if($deleteFirst){
        if($toAllLangs){ deleteFileFromDB_AllLangs($id, $table, $colname, $conditions, $dir); }
        else {           deleteFileFromDB($id, $table, $colname, $conditions, $dir, $checkToDelete); }
        if(!($_FILES[$fileKey]['size']>0)) return "NULL";
    }
    if(!empty($_FILES[$fileKey])){
        $file = copyFile($_FILES[$fileKey], $dir, '', $arExtAllowed);
        if($file){
            if($id && fileCheckAndCleanDB($id, $table, $colname, $conditions, $dir))
                  deleteFileFromDB($id, $table, $colname, $conditions, $dir, $checkToDelete);
            return $file;
        }
    } return false;
}

function deleteFileFromDB($id, $table, $colname, $conditions='', $dir='', $checkToDelete = true) {
    $delResult = false;
    if(!strlen($conditions)) $conditions = ' WHERE `id`='.$id;
    $query = 'SELECT `'.$colname.'` FROM `'.$table.'`'.$conditions;
    $result = mysql_query($query);
    if ($result && ($row = mysql_fetch_assoc($result)) && !empty($row[$colname])) {
        if ($checkToDelete){
            if(!isUsedByOtherLangs($row[$colname], $table, $colname, $conditions))
                $delResult = unlinkFile($row[$colname], $dir);
        } else  $delResult = unlinkFile($row[$colname], $dir);
        if(!file_exists(prepareDirPath($dir).$row[$colname]))
            return updateDBLangsSync($table, '`'.$colname.'`=NULL', 'WHERE `'.$colname.'`="'.$row[$colname].'"');
    }
    return $delResult;
}

function deleteFileFromDB_AllLangs($id, $table, $colname, $conditions='', $dir='') {
    global $arAcceptLangs;
    foreach($arAcceptLangs as $ln)
        deleteFileFromDB($id, replaceLang($ln, $table), $colname, $conditions, $dir, false);
}

function deleteFilesFromDB($colname, $table, $conditions='', $dir='', $updateDB=true) {
    $delResult = 0;
    if(!strlen($conditions)) $conditions = ' WHERE `id`='.$id;
    // get from db
    $query = 'SELECT `'.$colname.'` FROM `'.$table.'` '.$conditions;
    $result = mysql_query($query);

    while(($row = mysql_fetch_assoc($result)) && !empty($row[$colname])) {
        unlinkFile($row[$colname], $dir);
        if($updateDB && !file_exists($dir.$row[$colname])){
            $updResult = updateRecords($table, '`'.$colname.'`=NULL', 'WHERE `'.$colname.'`="'.$row[$colname].'"');
            if($updResult) $delResult+=$updResult;
        }
    }
    return $delResult;
}

function deleteItemsAndFilesFromDB($colname, $table, $conditions='', $dir='', $ln_sync = false) {
    if ($ln_sync)
        deleteFileFromDB_AllLangs(0, $colname, $table, $conditions, $dir, false);
    else
        deleteFilesFromDB($colname, $table, $conditions, $dir, false);
    return  $ln_sync ? deleteDBLangsSync($table, $conditions) : deleteRecords($table, $conditions);
}

function unlinkFile($file, $dir=''){
    $dir = prepareDirPath($dir);
    if (strlen($file) && file_exists($dir.$file)) {
        if(@unlink($dir.$file)) return true;
    } return false;
}

function removeDir($dir, $onlyChildrens=false){
    $dir = prepareDirPath($dir);
    if($dir){
        $hndl = opendir($dir);
        while ($file = readdir($hndl)) {
            if($file!='.' && $file!='..') {
                if(is_dir($dir.$file)) removeDir($dir.$file);
                else @unlink($dir.$file);
            }
        } closedir($hndl);
        if(!$onlyChildrens) return @rmdir($dir);
    } return false;
}

function copier($source, $dest, $mode) {
    $result = false;
    if(is_file($source)) { // Simple copy for a file
        $result = copy($source, $dest);
        funcWithAccessLevelMode('chmod', $dest, $mode);
    } elseif(is_dir($source)) { // Directory recursively copy
        // Make destination directory
        $oldumask = umask(0);
        $result = funcWithAccessLevelMode('mkdir', $dest, $mode);
        umask($oldumask);
        // Loop through the folder
        $dir = dir($source);
        while (($entry = $dir->read()) !== false) {
            if($entry == "." || $entry == "..") continue; // Skip pointers
            copier($source.DS.$entry, $dest.DS.$entry, $mode); // Deep copy directories
        } $dir->close(); // Clean up directory handler
    } return $result;
}

function changeRecursivelyChmod($dir, $filename='', $childrens=true, $mode='', array $skipNames=array()){
    $dir      = prepareDirPath($dir);
    $filename = trim($filename);
    if(empty($mode)) $mode = WLCMS_WRITABLE_CHMOD;
    if($dir && empty($filename)){
        if($childrens){
            $hndl = opendir($dir);
            while ($file = readdir($hndl)) {
                if($file!='.' && $file!='..' && !in_array($file, $skipNames)) {
                    if(is_dir($dir.$file)) changeRecursivelyChmod($dir.$file, '', $childrens, $mode, $skipNames);
                    else changeRecursivelyChmod($dir, $file, $childrens, $mode, $skipNames);
                }
            } closedir($hndl);
        } return funcWithAccessLevelMode('chmod', $dir, $mode);
    } elseif(!empty($filename) && !in_array($filename, $skipNames)) {
        return funcWithAccessLevelMode('chmod', $dir.$filename, $mode);
    }   return false;
}

function funcWithAccessLevelMode($func, $path, $mode){
    if(function_exists($func) && !empty($path) && !empty($mode)){
        switch($mode){
            case '0644' : return @$func($path, 0644); break;
            case '0665' : return @$func($path, 0665); break;
            case '0666' : return @$func($path, 0666); break;
            case '0755' : return @$func($path, 0755); break;
            case '0775' : return @$func($path, 0775); break;
            case '0777' : return @$func($path, 0777); break;
        }
    } return false;
}

function fileCheckAndCleanDB($id, $table, $colname, $conditions='', $dir=''){
    $checkResult = false;
    if(intval($id) > 0) {
        $dir = rtrim($dir, '/\\');
        if(!strlen($conditions)) $conditions = ' WHERE `id`='.$id;
        $query = 'SELECT `'.$colname.'` FROM `'.$table.'`'.$conditions;
        $result = mysql_query($query);
        $row = @mysql_fetch_assoc($result);
        if(!empty($row[$colname]) && file_exists($dir.$row[$colname])) 
            return true;
        else if(!empty($row[$colname]))
            updateRecords($table, '`'.$colname.'`=NULL', $conditions);
    } return false;
}

function getFileExt($file){
    return strtolower(end(explode('.', $file)));
}

function checkFileExist($dir, $file){
    if(file_exists(prepareDirPath($dir).$file)) return true;
    return false;
}

function createUniqueFileName($dir, $ext, $filename=''){
    $file = ((empty($filename)) ? substr(md5(uuid()),10) : setFilePathFormat($filename)).".".$ext;
    if(checkFileExist($dir, $file)) $file = createUniqueFileName($dir, $ext);
    return $file;
}

function createUniqueDirName($root, $dirname='', $maxLenth=63){
    if(empty($dirname)) $dirname = substr(md5(uuid()),0,$maxLenth);
    $dirname = strtolower(setFilePathFormat(substr($dirname, 0, $maxLenth-5).'_'.substr(uuid(),3,4)));
    if(is_dir(prepareDirPath($root.$dirname))) $dirname = createUniqueDirName($root, '', $maxLenth=63);
    return $dirname;
}

function cleanDirPath($dir, $ds=DS){
    $dir = str_replace(array('\\\\//','\\/','//\\\\','/\\','//','\\\\','/','\\'), $ds, $dir);
    $dir = trim(trim($dir), './\\');
    return $dir;
}

function prepareDirPath($dir, $create = false){
    $dir = cleanDirPath($dir, DS);
    if(strlen($dir)==0) return '';
    if(is_dir($dir))    
       return $dir.DS;
    if(is_dir('.'.DS.$dir))
       return '.'.DS.$dir.DS;
    if($create){
        if(funcWithAccessLevelMode('mkdir', $dir, WLCMS_WRITABLE_CHMOD))
           return $dir.DS;
        if(funcWithAccessLevelMode('mkdir', '..'.DS.$dir, WLCMS_WRITABLE_CHMOD))
           return '..'.DS.$dir.DS;
        if(funcWithAccessLevelMode('mkdir', '..'.DS.'..'.DS.$dir, WLCMS_WRITABLE_CHMOD))
           return '..'.DS.'..'.DS.$dir.DS;
        if(funcWithAccessLevelMode('mkdir', '..'.DS.'..'.DS.'..'.DS.$dir, WLCMS_WRITABLE_CHMOD))
           return '..'.DS.'..'.DS.'..'.DS.$dir.DS;
    } return '';
}

function setFilePathFormat($str){
    return str_replace(
        array(' ','?','!',':',';','#','@','$','&','\'','"','~','`','’','%','^','*',
              '(',')','«','»','<','>','+','/','\\',',','{','}','[',']','|'),
        '_',
        translitRichStr($str)
    );
}

function saveStrToFile($str, $file, $mode='a'){
    if(($fp = fopen($file, $mode))) {
      fwrite($fp, $str);
      fclose($fp);
    } funcWithAccessLevelMode('chmod', $file, WLCMS_WRITABLE_CHMOD);
    return ( file_exists($file) && filesize($file>0) );
}

function saveLogDebugFile($arParams=array(), $file='../logfile.txt', $mode='a'){
    ob_start();
    print("\n");print_r($arParams);
    if(($fp = fopen($file, $mode))) {
      fwrite($fp, ob_get_contents());
      fclose($fp);
    } ob_end_clean();
}
// END FILES FUNCTIONS ////////////////////////////////////////////////////////


// ANALIZE BOOL FUNCTIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function isUsedByOtherLangs($value, $table, $selectcol, $conditions=''){
    global $arAcceptLangs, $lang;
    $value = trim($value);
    foreach($arAcceptLangs as $ln){
        if($ln!=$lang && $value==trim(getValueFromDB(replaceLang($ln, $table), $selectcol, $conditions)))
            return true;
    } return false;
}

function isUsedByAllLangs($value, $table, $selectcol, $conditions=''){
    global $arAcceptLangs;
    $value = trim($value);
    foreach($arAcceptLangs as $ln){
        if($value != trim(getValueFromDB(replaceLang($ln, $table), $selectcol, $conditions)))
            return false;
    } return true;
}
// END ANALIZE BOOL FUNCTIONS //////////////////////////////////////////////////


// FUNCTIONS WITH DB DATA \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function getRecursivelyCatsForRedirect($menutype, $pid=false, $level=1){
    $items  = array();
    $query  = ($pid===false) ?
        'SELECT m.`id`, m.`title`, m.`active`, m.`redirectid`, m.`redirecturl` 
            FROM `'.MAIN_TABLE.'` m LEFT JOIN `'.MAIN_TABLE.'` rm ON rm.`id`=m.`pid` AND rm.`menutype`=m.`menutype`
            WHERE m.`menutype`='.$menutype.' AND rm.`id` IS NULL ORDER BY m.`pid`, m.`order`, m.`id`' :
        'SELECT `id`, `title`, `active`, `redirectid`, `redirecturl` FROM `'.MAIN_TABLE.'` WHERE `menutype`='.$menutype.' AND `pid`='.intval($pid).' ORDER BY `order`, `id`';
    $result = mysql_query($query);
    while($row = mysql_fetch_assoc($result)) {
        $row['level']         = $level;
        $row['disabled']      = ($row['active']==0 || $row['redirectid']>0 || $row['redirecturl']!='');
        $row['subcategories'] = getRecursivelyCatsForRedirect($menutype, $row['id'], $level+1);
        $items[] = $row;
    } return $items;
}

function getCategoriesForRedirect($lang) {
    $items  = array();
    $query  = 'SELECT *, `title_'.$lang.'` as menutitle FROM `'.MENUTYPES_TABLE.'` ';
    $result = mysql_query($query);
    while ($item = mysql_fetch_assoc($result)) {
        $item['categories'] = getRecursivelyCatsForRedirect($item['menutype']);
        $items[] = $item;
    } return $items;
}

/**
 * @ FUNCTION FOR GET Recurce Categories and Sub Categories with unlimited sublevels
 * FOR USING:  $categories =  getRecurceSubCatsRows($catid);
 * SMARTY: show two levels
 *          {section name=i loop=$categories} {$categories[i].curr_cat.title}
 *            {section name=j loop=$categories[i].sub_cats} {$categories[i].sub_cats[j].curr_cat.title}{/section}
 *          {/section}
 * @param type $id
 * @param type $level
 * @return array
 */
function getRecurceSubCatsRows($id, $level=0) {
    $query = "SELECT `id`, `title` FROM ".MAIN_TABLE." WHERE `active`=1 AND `pid`={$id} ORDER BY `order`, `title`";
    $result = mysql_query($query);
    if($result && mysql_num_rows($result) > 0){
        while ($row = mysql_fetch_assoc($result)) {
            $categories[] = array(
                'level'    => $level,
                'curr_cat' => $row,
                'sub_cats' => getRecurceSubCatsRows($row['id'], $level+1)
                );
        }; return $categories;
    }; return array();
}

function & getCategoriesTree($lang, $pid=0, $level=0, $showOnlyActive=true, $module=false, $order='', $extraterm='', $arPath=array()) {
    $module    = trim((string)$module);
    $extraterm = trim((string)$extraterm);
    $items     = array();
    $query     = "SELECT t.*, ljt1.`title_{$lang}` as pagetitle, ljt2.`title_{$lang}` as menutitle
                  FROM `".MAIN_TABLE."` t
                  LEFT JOIN `".PAGETYPES_TABLE."` ljt1 USING(`pagetype`)
                  LEFT JOIN `".MENUTYPES_TABLE."` ljt2 USING(`menutype`)
                  WHERE t.`pid`={$pid}".($showOnlyActive ? " AND t.`active`=1" : '')
                    .($module ? " AND t.`module`='{$module}' " : '')
                    .($extraterm ? ' AND '.preg_replace("/^AND/i", '', $extraterm, 1) : '')."
                    ORDER BY ".(!empty($order) ? $order : "t.`menutype`, t.`pid`, t.`order`, t.`id`");
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){
        while($row = mysql_fetch_assoc($result)){
            $row['arPath']     = $arPath;
            $row['arPath'][]   = trim($row['seo_path']);
            $row['id']         = intval($row['id']);
            $row['pid']        = intval($row['pid']);
            $row['level']      = $level;
            $row['margin']     = $level==0 ? '&nbsp;' : '&nbsp;'.str_repeat('&middot;&nbsp;&middot;&nbsp;&middot;&nbsp;', $level);
            $row['childrens']  = getCategoriesTree($lang, $row['id'], $level+1, $showOnlyActive, $module, $order, '', $row['arPath']);
            $items[]           = $row;
        }
    } return $items;
}

function & getCategoriesTreeWithItems($lang, $table=CATALOG_TABLE, $pid=0, $level=0, $showOnlyActive=true, $module=false, $order='', $extraterm='', $arPath=array(), $showShortcuts=false) {
    $module    = trim((string)$module);
    $extraterm = trim((string)$extraterm);
    $items     = array();
    $query     = "SELECT t.*, ljt1.`title_{$lang}` as pagetitle, ljt2.`title_{$lang}` as menutitle
                  FROM `".MAIN_TABLE."` t
                  LEFT JOIN `".PAGETYPES_TABLE."` ljt1 USING(`pagetype`)
                  LEFT JOIN `".MENUTYPES_TABLE."` ljt2 USING(`menutype`)
                  WHERE t.`pid`={$pid}".($showOnlyActive ? " AND t.`active`=1" : '')
                    .($module ? " AND t.`module`='{$module}' " : '')
                    .($extraterm ? ' AND '.preg_replace("/^AND/i", '', $extraterm, 1) : '')."
                    ORDER BY ".(!empty($order) ? $order : "t.`menutype`, t.`pid`, t.`order`, t.`id`");
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){
        while($row = mysql_fetch_assoc($result)){
            $row['arPath']     = $arPath;
            $row['arPath'][]   = trim($row['seo_path']);
            $row['id']         = intval($row['id']);
            $row['pid']        = intval($row['pid']);
            $row['level']      = $level;
            $row['margin']     = $level==0 ? '&nbsp;' : '&nbsp;'.str_repeat('&middot;&nbsp;&middot;&nbsp;&middot;&nbsp;', $level);
            $row['childrens']  = getCategoriesTreeWithItems($lang, $table, $row['id'], $level+1, $showOnlyActive, $module, $order, '', $row['arPath'], $showShortcuts);
            $row['items']      = array();
            if($showShortcuts) {
                $query             = 'SELECT id, title, "object" as `type`, `order` FROM `'.$table.'`   
                                      WHERE `cid`='.$row['id'].' ORDER BY `order` ';
                $shortcuts_query   = 'SELECT s.`pid` as `id`, c.`title`, "shortcut" as `type`, s.`order`  
                                      FROM '.SHORTCUTS_TABLE.' s LEFT JOIN '.$table.' c ON s.`pid`=c.`id` 
                                      WHERE s.`cid` = '.$row['id'].' AND s.`lang`="'.$lang.'" AND s.`module`="'.$module.'"';
                $sresult = mysql_query('(' .$query.') UNION ALL ('.$shortcuts_query.') ORDER BY `order`');
                if(mysql_num_rows($sresult) > 0) {
                    while ($itm = mysql_fetch_assoc($sresult)) {
                        $row['items'][] = $itm;
                    }
                }
            } else {
                $row['items']      = getComplexRowItems($table, 'id, title', 'WHERE `cid`='.$row['id'], '`order`');
            }
            $items[]           = $row;
        }
    } return $items;
}

function getItemsCountInCategories($idkeyname, $valuename, $table, $selcolnames = '', $conditions = '') {
    if(empty($selcolnames)) $selcolnames = "$idkeyname, $valuename";
    $items = array();
    $query = "SELECT $selcolnames FROM $table ".((!empty($conditions)) ? $conditions : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) { $items[intval($row[$idkeyname])] = $row[$valuename]; }
    return $items;
}

function getMaxPosition($id, $colname, $ordcolname = false, $table = MAIN_TABLE) {
    $where = ((isset($id) && $id) && !empty($ordcolname)) ? " WHERE `{$ordcolname}`={$id}" : '';
    $select = "SELECT MAX(`{$colname}`) as maxpos FROM $table".$where;
    $result = mysql_query($select) or die('SELECT MAX POSITION: MySQL Error #'.mysql_errno().'</b> <br/> SQL: '. $select."<br/>".mysql_error());
    if($result && ($row = mysql_fetch_assoc($result))){
        return (intval($row['maxpos']) + 1);
    } else { return 0; }
}

function reorderItems($items, $setcolname, $wherecolname, $table, $saveParams = array()) {
    $error = '';
    $aff_rows = 0;
    foreach($items as $id=>$val){
        $update = "UPDATE {$table} SET `{$setcolname}`='{$val}' WHERE `{$wherecolname}`='{$id}'\n";
        if(@mysql_query($update) === FALSE) $error .= "Не выполнена команда: <br/>{$update}<br/>";
        elseif(mysql_affected_rows()) {
            $aff_rows++;
            if(!empty($saveParams) && isset($saveParams['action']) && isset($saveParams['comment']) && isset($saveParams['lang']) && isset($saveParams['module'])) {
                ActionsLog::getInstance()->save($saveParams['action'], $saveParams['comment'], $saveParams['lang'], getValueFromDB($table, 'title', 'WHERE `id`='.$id), $id, $saveParams['module']);
            }
        }
    }
    return ($aff_rows==0) ? false :(empty($error) ? true : "UPDATE REORDER: MySQL Error #<b>".mysql_errno()."</b><br/>SQL:<br/>{$error}<br/><br/>Error: ".mysql_error());
}

function updateItems(array $items, array $keys, $wherecolname, $table, $saveParams=array()) {
    $error = '';
    $aff_rows = 0;
    if(!empty($items)){
        $arColNames = array_keys($items);
        foreach($keys as $id=>$val){
            $arSets = array();
            foreach($arColNames as $setcolname)
                $arSets[] = "`{$setcolname}`=".(array_key_exists($id, $items[$setcolname]) ? "'{$items[$setcolname][$id]}'" : 'NULL');
            if(sizeof($arSets)>0){
                $update = "UPDATE {$table} SET ".  implode(', ', $arSets)." WHERE `{$wherecolname}`='{$id}'\n";
                if(@mysql_query($update) === FALSE) $error .= "Не выполнена команда: <br/>{$update}<br/>";
                elseif(mysql_affected_rows()){
                    $aff_rows++;
                    if(!empty($saveParams) && isset($saveParams['action']) && isset($saveParams['comment']) && isset($saveParams['lang']) && isset($saveParams['module'])) {
                        if($saveParams['module'] == 'shortcuts') $title = getValueFromDB($table.' st LEFT JOIN '.CATALOG_TABLE.' ct ON st.`pid`=ct.`id`', 'ct.`title`', 'WHERE st.`id`='.$id, 'title');
                        else $title = getValueFromDB($table, 'title', 'WHERE `id`='.$id);
                        ActionsLog::getInstance()->save($saveParams['action'], $saveParams['comment'], $saveParams['lang'], $title, $id, $saveParams['module']);
                    } 
                }
            }
        }
    }
    return ($aff_rows==0) ? false :(empty($error) ? true : "UPDATE REORDER: MySQL Error #<b>".mysql_errno()."</b><br/>SQL:<br/>{$error}<br/><br/>Error: ".mysql_error());
}

function updateRecords($table, $setStr, $conditions = '') {
    $query = 'UPDATE '.$table.' SET '.$setStr.' '.$conditions;
    $result = mysql_query($query);
    if($result) { return mysql_affected_rows(); }
    else {        return false; }
}

function updateDBLangsSync($table, $setStr, $conditions = '') {
    global $arAcceptLangs;
    $affectedRows = 0;
    foreach($arAcceptLangs as $ln){
        $affected = updateRecords(replaceLang($ln, $table), $setStr, $conditions);
        if($affected) $affectedRows += $affected;
    }
    return $affectedRows;
}

function deleteRecords($table, $conditions = '') {
    $query = 'DELETE FROM '.$table.' '.$conditions;
    $result = mysql_query($query);
    if($result) { return mysql_affected_rows(); }
    else {        return false; }
}

function truncateTable($table) {
    $query = 'TRUNCATE TABLE '.$table;
    $result = mysql_query($query);
    if($result) { return mysql_affected_rows(); }
    else {        return false; }
}

function execQuery($query) {
    $result = mysql_query($query) or die('MySQL ERROR: '.mysql_error()."\n Query:".$query);
    if($result) { return mysql_affected_rows(); }
    else {        return false; }
}

function deleteDBLangsSync($table, $conditions = '') {
    global $arAcceptLangs;
    $affectedRows = 0;
    foreach($arAcceptLangs as $ln){
        $affected = deleteRecords(replaceLang($ln, $table), $conditions);
        if($affected) $affectedRows += $affected;
    }
    return $affectedRows;
}

function deleteRecursivelyCategories($id, $images_dir, $arImagesParams = false, $conditions = '', $table= MAIN_TABLE) {
    $affectedRows = 0;
    $query  = 'SELECT `id`, `title` FROM `'.$table.'` WHERE `pid`='.intval($id).(!empty($conditions) ? ' AND '.$conditions : '');
    $result = mysql_query($query);
    while($row = mysql_fetch_assoc($result)) {
        $affected = deleteRecursivelyCategories($row['id'], $images_dir, $arImagesParams, $conditions, $table);
        if($affected) {
            $affectedRows += $affected;   
        }
    }

    //Устанавливае условие для операций в базе данный
    $item_condition = 'WHERE `id`='.$id;
    //Получаем модуль обьекта
    $item_module    = getValueFromDB($table, 'module', $item_condition);
    $item_title     = getValueFromDB($table, 'title', $item_condition);
    //Удаляем обьект
   // unlinkImage($id, $table, $images_dir, $arImagesParams);
    PHPHelper::deleteImages($id, $images_dir, 'main');
    $affected = deleteRecords($table, $item_condition);

    if($affected) {
        // добавляем к общему количеству удаленных обьектов
        $affectedRows += $affected;
        // Подчищаем по модулям.
        PHPHelper::clearModulesData($id, $item_module, UPLOAD_URL_DIR.$item_module);
        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, 'main');
    } return $affectedRows;
}

function delCategoriesDBLangsSync($id, $images_dir, $arImagesParams = false, $conditions = '', $table= MAIN_TABLE) {
    global $arAcceptLangs;
    $affectedRows = 0;
    foreach($arAcceptLangs as $ln){
        $affectedRows += deleteRecursivelyCategories($id, $images_dir, $arImagesParams, $conditions, replaceLang($ln, $table));
    }
    return $affectedRows;
}

function getSimpleItemRow($id, $table, $colname = 'id') {
    $query = "SELECT * FROM $table WHERE {$colname}={$id}";
    $result = mysql_query($query);
    $row = @mysql_fetch_assoc($result);
    return (empty($row)) ? array() : $row;
}

function getItemRow($tablenames, $selectcols, $conditions='') {
    $query  = "SELECT {$selectcols} FROM {$tablenames} ".((!empty($conditions)) ? $conditions : '').' LIMIT 1';
    $result = mysql_query($query);
    return mysql_num_rows($result) ? mysql_fetch_assoc($result) : array();
}

function getItemObj($tablenames, $selectcols, $conditions='') {
    $query  = "SELECT {$selectcols} FROM {$tablenames} ".((!empty($conditions)) ? $conditions : '').' LIMIT 1';
    $result = mysql_query($query);
    return mysql_num_rows($result) ? mysql_fetch_object($result) : array();
}

function getValueFromDB($table, $selectcol, $whereoptions='', $colname='') {
    $query = "SELECT {$selectcol}".((!empty($colname)) ? " as $colname" : '')." FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '');
    $result = mysql_query($query);
    return ($result && mysql_num_rows($result) > 0) ? mysql_result($result, 0, ((empty($colname)) ? $selectcol : $colname)) : false;
}

function getArrValueFromDB($table, $selectcol, $whereoptions='', $colname='') {
    $items = array();
    $query = "SELECT {$selectcol}".((!empty($colname)) ? " as $colname" : '')." FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '');
    $result = mysql_query($query);
    while($row = mysql_fetch_assoc($result)) { $items[] = $row[((empty($colname)) ? $selectcol : $colname)]; }
    return $items;
}

function getCatIdByModule($module, $pid=0, $showOnlyActive=true) {
    $query = "SELECT `id`,`pid` 
        FROM `".MAIN_TABLE."` 
        WHERE `module`='{$module}'".($showOnlyActive ? " AND `active`=1" : '').($pid>0 ? " AND id={$pid}" : '')."
        ORDER BY pid
        LIMIT 1";
    $result = mysql_query($query);
    if(mysql_num_rows($result)){
        $row = mysql_fetch_assoc($result);
        return $row['id'];
    } else {
        $query = "SELECT `id`,`pid` 
            FROM `".MAIN_TABLE."` 
            WHERE `module`='{$module}' AND pid=".intval($pid).($showOnlyActive ? " AND `active`=1" : '');
        $result = mysql_query($query); 
        while ($row = mysql_fetch_assoc($result)) {
            $id = getCatIdByModule($module, $row['id'], $showOnlyActive);
            if($id) return $id;
        }
    } return 0;
}

function getRowItems($table, $selcolnames = '*', $where = '', $order = '', $limit = '') {
    $items = array();
    $query = "SELECT $selcolnames FROM $table".((!empty($where)) ? " WHERE $where" : '').((!empty($order)) ? " ORDER BY $order" : '').((!empty($limit)) ? " LIMIT $limit" : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) { $items[] = $row; }
    return $items;
}

function getRowItemsInKey($keyname, $table, $selcolnames = '*', $whereoptions = '', $orderConditions = '', $limitConditions = '') {
    if(empty($keyname)) $keyname = 'id';
    $items = array();
    $query = "SELECT $selcolnames FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '').((!empty($orderConditions)) ? " $orderConditions" : '').((!empty($limitConditions)) ? " $limitConditions" : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) { $items[$row[$keyname]] = $row; }
    return $items;
}

function getRowItemsInKeyValue($keyname, $valuename, $table, $selcolnames = '', $whereoptions = '', $orderConditions = '', $limitConditions = '') {
    if(empty($selcolnames)) $selcolnames = "$keyname, $valuename";
    $items = array();
    $query = "SELECT $selcolnames FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '').((!empty($orderConditions)) ? " $orderConditions" : '').((!empty($limitConditions)) ? " $limitConditions" : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) { $items[$row[$keyname]] = $row[$valuename]; }
    return $items;
}

function getRowItemsValue($table, $selcolname, $whereoptions = '', $orderConditions = '', $limitConditions = '', $valuename = '') {
    $items = array();
    $query = "SELECT $selcolname FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '').((!empty($orderConditions)) ? " $orderConditions" : '').((!empty($limitConditions)) ? " $limitConditions" : '');
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) { $items[] = $row[(empty($valuename)?$selcolname:$valuename)]; }
    return $items;
}

function getComplexRowItems($table, $selectcols, $whereoptions='', $order = '', $limit = '') {
    $items = array();
    $query = "SELECT {$selectcols} FROM $table".((!empty($whereoptions)) ? " $whereoptions" : '').((!empty($order)) ? " ORDER BY $order" : '').((!empty($limit)) ? " LIMIT $limit" : '');
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) { 
            $items[] = $row; 
        }
    }
    return $items;
}

function getMaxRowItems($table, $where = '', $selcolname = '*') {
    $query = "SELECT COUNT($selcolname) as count FROM $table".((!empty($where)) ? " $where" : '');
    $result = mysql_query($query);
    if( ($row = mysql_fetch_assoc($result)) ) { $item = $row['count']; }
    return (empty($item)) ? false : intval($item);
}

function getModules() {
    $items = array();
    $query = "SELECT t.* FROM `".MAIN_TABLE."` t
                 WHERE t.`module`!='' AND t.`module` IS NOT NULL
                       AND (SELECT t2.`id` FROM `".MAIN_TABLE."` t2 WHERE t2.`active`=1 AND t2.`module`=t.`module` AND t2.`id`=t.`pid`) IS NULL
                 GROUP BY t.`module`
                 ORDER BY t.`active` DESC, t.`pid`";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $items[$row['module']] = $row;
   } return $items;
}

function getSettings() {  
    $items = getRowItemsInKeyValue('name', 'value', SETTINGS_TABLE, '*');
    if(!empty($items)){
        $item  = (object)$items;
        $item->ownerEmail   = parseEmailString($item, 'ownerEmail', trim("{$item->ownerLastName} {$item->ownerFirstName}"));
        $item->notifyEmail  = parseEmailString($item, 'notifyEmail', $item->websiteName);
        $item->siteEmail    = parseEmailString($item, 'siteEmail', $item->websiteName);
        $item->copyright    = preg_replace("/(\d{4}\s?)[-|–](\s?\d{4})/", '\1-'.date('Y'), $item->copyright);
    } else {
        $item = new stdClass();
    }
    return $item;
}

function parseEmailString(stdClass $objSettings, $objkey, $name=''){
    $email = '';
    // Set email address[es]
    if($objkey && !empty($objSettings->$objkey)){
        if(strpos($objSettings->$objkey, ',')!==false) {
            $arr = explode(',', $objSettings->$objkey);
            foreach($arr as $k=>$v){
                $v = trim($v);
                $arr[$k] = (strpos($v, '<')===false && strpos($v, $_SERVER['SERVER_NAME'])!==false && !empty($name)) ? "$name <".$v.">" : $v;
            }  $email = implode(',', $arr);
        } else $email = !empty($name) ? "$name <".$objSettings->$objkey.">" : $objSettings->$objkey;
    } return $email;
}

function getPager(&$curPage, $cntItems, $itemsOnPage, $baseUrl='', $sep='...'){
    $pager = array('all'=>'all', 'first'=>1, 'last'=>2, 'prev'=>1, 'next'=>2, 'count'=>0, 'pages'=>array(), 'baseurl'=>$baseUrl, 'sep'=>$sep);
    if(!$cntItems) $cntItems = 1;
    $totals = $pager['count'] = $pager['last'] = intval(ceil($cntItems/$itemsOnPage));
    if($curPage>$totals) $curPage = $totals;
    if ($totals>1) {
        $pager['prev']    = ($curPage > 1)       ? $curPage-1 : 1;
        $pager['next']    = ($curPage < $totals) ? $curPage+1 : $totals;
        
        $pager['pages'][] = 1;       
        if($totals<=5 || $sep===false) {
            for($i=2; $i<$totals; $i++) $pager['pages'][] = $i;
        }elseif($curPage<=3) {
            for($i=2; $i<=5; $i++)       $pager['pages'][] = $i;
            $pager['pages'][] = $sep;
        }else{
            $start=$totals-($totals-$curPage+3);
            if($curPage==$totals)   $start-=2;
            if($curPage==$totals-1) $start--;
            if($start<0)            $start=0;

            $end=$totals-($totals-$curPage-3);
            if($end>$totals)        $end=$totals;

            if($curPage>4)                  $pager['pages'][] = $sep;
            for($i=1+$start; $i<$end; $i++) $pager['pages'][] = $i;
            if($curPage<$totals-3)          $pager['pages'][] = $sep;
        }
        $pager['pages'][] = $totals;
    } return $pager;
}

function getSqlStrCondition(array $arr, $operator='AND') {
    return !empty($arr) ? implode(" $operator ", $arr) : '';
}

function & getSqlListFilter(array $arKeys, $values='', $type='LIKE', $prefix='') {
    $arr = array();
    $arItems = array_combine_multi($arKeys, $values);
    foreach ($arItems as $key => $value) {
        $arr[$key] = getSqlListFilterField($key, $value, $type, $prefix);
    } return $arr;
}

function getSqlListFilterField($key, $value, $type, $prefix='') {
    switch ($type) {
        case 'LIKE':     return "{$prefix}`{$key}` LIKE '%$value%'";    break;
        case '=':        return "{$prefix}`{$key}` = '$value'";         break;
        case '>':        return "{$prefix}`{$key}` > '$value'";         break;
        case '<':        return "{$prefix}`{$key}` < '$value'";         break;
        case '<>':       return "{$prefix}`{$key}` <> '$value'";        break;
        case 'IN':       return "{$prefix}`{$key}` IN ($value)";        break;
        case 'BEETWEEN': return "{$prefix}`{$key}` BEETWEEN ($value)";  break;
        default: break;
    } return '';
}
// END FUNCTIONS WITH DB DATA //////////////////////////////////////////////////

# ##############################################################################
//SEND EMAIL FUNCTIONS
function sendMail($recipients, $subject, $text, $from = '', $contentType = 'plain', $replyTo = '', $cc = '', $bcc = '') {
    if(empty($from)){ $from = 'info@'.$_SERVER['SERVER_NAME']; }
    
    // HEADERS
    $headers = '';
    if($contentType == 'plain'){
        $headers .= "Content-Type: text/plain; charset=\"windows-1251\"\r\n";
    } else {
    /* Для отправки HTML-почты вы можете установить шапку Content-type. */
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=\"windows-1251\"\r\n";
    }

    /* дополнительные шапки */
    $headers .= "From: $from\r\n";
    if($replyTo) { $headers .= "Reply-To: $replyTo\r\n"; }
    if($cc) {      $headers .= "Cc: $cc\r\n"; }
    if($bcc) {     $headers .= "Bcc: $bcc\r\n"; }
    return mail($recipients, $subject, $text, $headers);
}

function sendMultipartMail($mailTo, $mailFrom, $subject, $html, $path, $name) {
    # Is the OS Windows or Mac or Linux
    $EOL = getEOL(); // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём

    # File for Attachment
    if (file_exists($path)) {
        $fp = fopen($path,"rb");
        if (!$fp) {
            print "Cannot open file";
            exit();
        }
        $file = fread($fp, filesize($path));
        $filetype = filetype($path);
        fclose($fp);
    } else {
        print "File not exists";
        exit();
    }

    # Boundry for marking the split & Multitype Headers
    $boundary   = "==Multipart_Boundary_x{".md5(uniqid(time()))."}x";  // любая строка, которой не будет ниже в потоке данных.

    # Common Headers
    $headers  = "MIME-Version: 1.0;$EOL";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";
    $headers .= "From: $mailFrom".$EOL;
    $headers .= "Reply-To: $mailFrom".$EOL;
    $headers .= "Return-Path: $mailFrom".$EOL;     // these two to set reply address
    $headers .= "Message-ID: <".time()." TheSystem@".$_SERVER['SERVER_NAME'].">".$EOL;
    $headers .= "X-Mailer: PHP v".phpversion().$EOL; // These two to help avoid spam-filters

    # HTML Version
    $multipart  = "--$boundary$EOL";
    $multipart .= "Content-Type: text/html; charset=windows-1251$EOL";
    $multipart .= "Content-Transfer-Encoding: base64$EOL";
    $multipart .= $EOL; // раздел между заголовками и телом html-части !! IMPORTANT !!
    $multipart .= chunk_split(base64_encode($html));//Encode The Data For Transition using base64_encode();

    # Attachment
    $multipart .=  "$EOL--$boundary$EOL";
    //$multipart .= "Content-Type: application/$filetype; name=\"".$name."\"$EOL";   // sometimes i have to send MS Word, use 'msword' instead of 'pdf'
    $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
    $multipart .= "Content-Transfer-Encoding: base64$EOL";
    $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
    $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла !! IMPORTANT !!
    $multipart .= chunk_split(base64_encode($file));

    # Finished
    $multipart .= "$EOL--$boundary--$EOL"; // finish with two eol's for better security. see Injection.

    # SEND THE EMAIL
    if(mail($mailTo, $subject, $multipart, $headers)) { return true; }//если письмо отправлено
    // если  письмо не отправлено
    return false;
}
//SEND EMAIL FUNCTIONS
# ##############################################################################


# ##############################################################################
//COMPATIBLE FUNCTIONS 4 vs 5 PHP versions
//array_replace
//(PHP 5 >= 5.3.0)
//http://www.php.net/manual/en/function.array-replace.php
if (!function_exists('array_replace')) {
    function array_replace(array $array, array $array1) {
        $args = func_get_args();
        for ($i=1; $i<func_num_args(); $i++) {
            if (is_array($args[$i])) {
                foreach ($args[$i] as $key => $val) { $array[$key] = $val; }
            } else {
                trigger_error( __FUNCTION__ . '(): Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING );
                return NULL;
            }
        } return $array;
    }
}
//END COMPATIBLE FUNCTIONS 4 vs 5 PHP versions
# ##############################################################################



# ##############################################################################
//OTHER FUNCTIONS
function Redirect($url, $backurl='') {
    global $Cookie;
    if($backurl) setReturnUrlToSession ($backurl);
    if(headers_sent()) {
        $Cookie->processDirectly();
        echo "<script>document.location.href='{$url}';</script>\n";
    } else {
        $Cookie->process();
        header('HTTP/1.1 301 Moved Permanently');
        header("Request-URI: {$url}");
        header("Content-Location: {$url}");
        header("Location: {$url}");
    } exit();
}

function RedirectAjax($url, $backurl='') {
    global $Cookie;
    if($backurl) setReturnUrlToSession ($backurl);
    $Cookie->processDirectly();
    echo "<script>window.parent.location.href='{$url}';</script>\n";
    exit(1);
}

function uuid(){ // version 4 UUID
    return sprintf(
        '%08x-%04x-%04x-%02x%02x-%012x',
        mt_rand(),
        mt_rand(0, 65535),
        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)),
        bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
        mt_rand(0, 255),
        mt_rand()
    );
}

function salt($length=0) {// ---> ГЕНЕРИМ SALT;
    $salt   = '';
    $skip   = array(34,38,39,60,62,92);
    $length = intval($length);
    if(!$length) $length = rand(3, 7); // задаём длину salt строки от 3 до 7 символов если данный параметер не передан в функцию
    for ($i=$num=0; $i<$length; $i++) {
        do{ $num = rand(33, 126); }while(in_array($num, $skip));
        $salt .= chr($num); // генерим символами из ASCII-table url:http://www.asciitable.com
    } return $salt;
}

function randString($length=12, $spec_chars=false) {
    static $allchars = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
    $string = "";
    if (is_array($spec_chars)) {
        while (strlen($string) <= $length) {
            shuffle($spec_chars);
            foreach($spec_chars as $chars) 
                $string .= $chars[mt_rand(0, strlen($chars)-1)];
        }
    } else {
        if ($spec_chars !== false)
             $chars = $spec_chars;
        else $chars = $allchars;
        for ($i=0; $i < $length; $i++)
            $string .= $chars[mt_rand(0, strlen($chars)-1)];
    } return $string;
}

function str_conv($str, $from = "WINDOWS-1251", $to ="UTF-8", $translit = false) {
    return iconv($from, $to.($translit ? "//TRANSLIT" : ''), $str);
}

function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function arrayDelNull($arr) {
    if (!function_exists("notnull")) {
        function notnull($i) { return ($i ? 1 : 0); }
    }
    return array_filter($arr, "notnull");
}

function getOrdersByKeyExplodeFilteredArray($arr, $firstKeyPart, $sep='_') {
    $items = array('get'=>array(), 'url'=>array(), 'mysql'=>array());
    if(is_array($arr)){
        foreach ($arr as $key=>$value){
            $ar = explode($sep, $key);
            if($ar[0]==$firstKeyPart){
               $items['get'][$key] = $value;
               $items['url'][$key] = $key.'='.$value;
               $key = str_replace('!', '.', str_replace($firstKeyPart.$sep, '', $key));
               $items['mysql'][$key] = $key.' '.$value;
            }
        }
    } return $items;
}

function getOrdersLinks($arOrder, $arGET, $baseURL, $firstKeyPart, $sep='_') {
    $items = array();
    if(is_array($arOrder)){
        foreach ($arOrder as $order=>$title){
            $item['link'] = '';
            if($order=='default'){
                $item['link']  = $baseURL;
                $item['title'] = $title;
            } else {
                $key = $firstKeyPart.$sep.$order;
                $by  = @$arGET[$key]=="ASC" ? 'DESC' : 'ASC';
                foreach($arGET as $k=>$v)
                    $item['link'] .= $k!=$key ? "&{$k}={$v}" : '';
                $item['link']  = "{$baseURL}&{$key}={$by}{$item['link']}";
                $item['title'] = "{$title} ({$by})";
            }
            $items[] = $item;
        }
    } return $items;
}

/**
 * @param array $keys
 * @param mixed $values
 * @return array
 * Используя значения массива keys в качестве ключей и значения [масссива] values
 * в качестве соответствующих значений. Возвращает пустой массив,
 * если массив keys не является массивом и если values не инициализирован
 */
function array_combine_multi($keys, $values){
    $combined = array();
    if(is_array($keys) && is_array($values) && sizeof($keys) == sizeof($values)){
        $combined = array_combine($keys, $values);
    }elseif(is_array($keys) && is_array($values) && sizeof($keys) > sizeof($values)){
        $val = reset($values);
        foreach($keys as $k => $v){
            if(isset($values[$k])){$val=$values[$k];}
            $combined[$v] = $val;
        }
    }elseif(is_array($keys) && !is_array($values)){
        foreach($keys as $v){$combined[$v] = $values;}
    }
    return $combined;
}

function strToArray($str, $separator = ' ') {
    $arr = explode($separator, $str);
    $arr = arrayDelNull($arr);
    return $arr;
}

function is_set(&$var, $key=false){
    if($key===false)        return isset($var) ? 1 : 0;
    else if(is_array($var)) return array_key_exists($key, $var) ? 1 : 0;
    else                    return 0;
}

function getDBStatus($dblink, $standart=false){
    $stat=mysql_stat($dblink);

    if($standart)
        $result = implode("<BR/>".PHP_EOL, explode('  ', mysql_stat($dblink)));
    else {
        preg_match_all('#\s*([0-9,.]+)\s*#ui', $stat, $m);
        $date=date("H:i:s", mktime(0, 0, $m[0][0]));
        $result = PHP_EOL.'Время работы сервера:&nbsp;'.$date.'<BR>';
        $result.= PHP_EOL.'Кол-во соединений:&nbsp;'.$m[0][1].'<BR>';
        $result.= PHP_EOL.'Кол-во отосланных запросов (за всё время):&nbsp;'.$m[0][2].'<BR>';
        $result.= PHP_EOL.'Медленных запросов:&nbsp;'.$m[0][3].'<BR>';
        $result.= PHP_EOL.'Кол-во открытых таблиц (за всё время):&nbsp;'.$m[0][4].'<BR>';
        $result.= PHP_EOL.'Очищенных таблиц:&nbsp;'.$m[0][5].'<BR>';
        $result.= PHP_EOL.'Открытых таблиц:&nbsp;'.$m[0][6].'<BR>';
        $result.= PHP_EOL.'Запросов в секунду:&nbsp;'.$m[0][7].'<BR>';
    }
    return $result;
}

function getEOL(){
    # Is the OS Windows or Mac or Linux
    if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
        return "\r\n";
    } elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
        return "\r";
    } else {
        return "\n";
    }
}

function hex2dec($color = "#000000"){
    $tbl_color = array();
    $tbl_color['R']=hexdec(substr($color, 1, 2));
    $tbl_color['G']=hexdec(substr($color, 3, 2));
    $tbl_color['B']=hexdec(substr($color, 5, 2));
    return $tbl_color;
}


function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb; 
}


function px2mm($px){
    return $px*25.4/72;
}

function getRealIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function txtEntities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}

function checkDateString($date) {
//  Проверяет дату
    if (preg_match("/[0-3][0-9][-][0-1][0-9][-][2][0][0-5][0-9]/", $date)) return true;
    else return false;
}

function checkValuesInString($arr, $str) {
//  Проверяет строку на наличие значений в $arr
    $str = trim($str);
    if(empty($str) || empty($str)) return false;
    if (preg_match("/[".implode("|", $arr)."]/", $str)) return true;
    else return false;
}

function validString($string, $mode) {
//  Экранирует строку
//  $mode = true  -  Преобразует HTML теги
//  $mode = false -  Без преобразования HTML тегов

    if ($mode) return stripslashes(mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES)));
    else return (str_replace("'", "&rsquo;", $string));
}

function printMessage($text, $type='', $style='', $delayBefore=0, $delayAfter=0) {
//  Выводит информационное сообщение
//  $type = E_ERROR   -  Сообщение об ошибке
//  $type = E_WARNING -  Информационное сообщение
//  default -  Информационное сообщение с пользовательскими стилями : lightgreen green | #FFA69F red
    if($delayBefore){ flush(); sleep($delayBefore); }
    switch($type){
        case E_ERROR:
            print '<table width="100%"><tr><td style="font-weight:bold;color:#FF0000;text-align:center;">'.$text.'</td></tr></table>';
            break;
        case E_WARNING:
            print '<table width="100%"><tr><td align="center" style="padding:10px;font-weight:bold;color:#2b79a8;">'.$text.'</td></tr></table>';
            break;
        case 'error':
            print '<div style="'.($style ? $style : 'background-color:#FFA69F;font-weight:bold;margin:20px;padding:20px;border:1px dashed red;').'">'.$text.'</div>';
            break;
        case 'success':
            print '<div style="'.($style ? $style : 'background-color:lightgreen;font-weight:bold;margin:20px;padding:20px;border:1px dashed green;').'">'.$text.'</div>';
            break;
        default:
            print '<div style="'.$style.'">'.$text.'</div>';
            break;
    }
    if($delayAfter){ flush(); sleep($delayAfter); }
}

function checkEmail($email) {
//  Проверят e-mail
//  Проверят на допустимые символы
//  Проверят хост

    $pattern = "/^[\w-]+(\.[\w-]+)*@";
    $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
    if (preg_match($pattern, $email)) {
        $parts = explode("@", $email);
        if (function_exists('checkdnsrr')) {
            if (checkdnsrr($parts[1], "MX")) return true;
            else return false;
        } else return true;
    } else return false;
}

function translitStr($st) {
    $st=strtr($st, "абвгдеёзийклмнопрстуфхыэ_", "abvgdeeziyklmnoprstufhiei");
    $st=strtr($st, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЫЭ_", "ABVGDEEZIYKLMNOPRSTUFHIEI");
    $st=strtr($st,
        array(
        "ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh",
        "щ"=>"sch","ь"=>"", "ъ" => "", "ю"=>"yu", "я"=>"ya",
        "Ж"=>"Zh", "Ц"=>"Ts", "Ч"=>"Ch", "Ш"=>"Sh",
        "Щ"=>"Sch","Ь"=>"", "Ъ" => "", "Ю"=>"Yu", "Я"=>"Ya",
        "ї"=>"yi", "Ї"=>"Yi", "є"=>"ie", "Є"=>"Ye"
        )
    );
    return $st;
}

function translitRichStr($str){
    return strtr($str,
        array(
            'Г?'=>'A',  'Г‚'=>'A',  'Д‚'=>'A',  'Г„'=>'A', 'Д†'=>'C', 'Г‡'=>'C', 'ДЊ'=>'C',
            'ДЋ'=>'D',  'Д?'=>'D',  'Г‰'=>'E','Д?'=>'E', 'Г‹'=>'E', 'Дљ'=>'E', 'ГЌ'=>'I',
            'ГЋ'=>'I',  'Д№'=>'L', 'Е?'=>'N', 'Е‡'=>'N', 'Г“'=>'O', 'Г”'=>'O', 'Е?'=>'O',
            'Г–'=>'O',  'Е”'=>'R',  'Е?'=>'R', 'Е '=>'S', 'Ељ'=>'O', 'Е¤'=>'T', 'Е®'=>'U',
            'Гљ'=>'U',  'Е°'=>'U', 'Гњ'=>'U', 'Гќ'=>'Y', 'ЕЅ'=>'Z', 'Е№'=>'Z', 'ГЎ'=>'a',
            'Гў'=>'a',  'Д?'=>'a', 'Г¤'=>'a', 'Д‡'=>'c', 'Г§'=>'c', 'ДЌ'=>'c', 'ДЏ'=>'d',
            'Д‘'=>'d',  'Г©'=>'e', 'Д™'=>'e', 'Г«'=>'e', 'Д›'=>'e',  'Г­'=>'i', 'Г®'=>'i',
            'Дє'=>'l',  'Е„'=>'n', 'Е?'=>'n', 'Гі'=>'o', 'Гґ'=>'o', 'Е‘'=>'o',  'Г¶'=>'o',
            'ЕЎ'=>'s',  'Е›'=>'s', 'Е™'=>'r', 'Е•'=>'r', 'ЕҐ'=>'t', 'ЕЇ'=>'u', 'Гє'=>'u',
            'Е±'=>'u',  'Гј'=>'u', 'ГЅ'=>'y', 'Еѕ'=>'z', 'Еє'=>'z', 'Л™'=>'-', 'Гџ'=>'Ss',
            'Д„'=>'A',  'Вµ'=>'u', 'Ґ'=>'G',  'Ё'=>'Yo', 'Є'=>'E',  'Ї'=>'Yi',  'І'=>'I',
            'і' =>'i',  'ґ'=>'g',  'ё'=>'yo', '№'=>'#', 'є'=>'e',  'ї'=>'yi',  'А'=>'A',
            'Б' =>'B',  'В'=>'V',  'Г'=>'G',  'Д'=>'D',  'Е'=>'E',  'Ж'=>'Zh',  'З'=>'Z',
            'И' =>'I',  'Й'=>'Y',  'К'=>'K',  'Л'=>'L',  'М'=>'M',  'Н'=>'N',   'О'=>'O',
            'П' =>'P',  'Р'=>'R',  'С'=>'S',  'Т'=>'T',  'У'=>'U',  'Ф'=>'F',   'Х'=>'H',
            'Ц' =>'Ts', 'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Sch','Ъ'=>'',   'Ы'=>'Yi',  'Ь'=>'',
            'Э' =>'E',  'Ю'=>'Yu', 'Я'=>'Ya', 'а'=>'a',  'б'=>'b',  'в'=>'v',   'г'=>'g',
            'д' =>'d',  'е'=>'e',  'ж'=>'zh', 'з'=>'z',  'и'=>'i',  'й'=>'y',   'к'=>'k',
            'л' =>'l',  'м'=>'m',  'н'=>'n',  'о'=>'o',  'п'=>'p',  'р'=>'r',   'с'=>'s',
            'т' =>'t',  'у'=>'u',  'ф'=>'f',  'х'=>'h',  'ц'=>'ts', 'ч'=>'ch',  'ш'=>'sh',
            'щ' =>'sch','ъ'=>'',   'ы'=>'yi', 'ь'=>'',   'э'=>'e',  'ю'=>'yu',  'я'=>'ya' 
            )
    );
}

function cleanText($text){
    $text = trim($text);
    if(!empty($text)){
        $text = strip_tags($text);
        $text = htmlspecialchars($text);
        $text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
        $text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        $text = preg_replace( '/<!--.+?-->/', '', $text );
        $text = preg_replace( '/{.+?}/', '', $text );
        $text = preg_replace( '/&nbsp;/', '', $text );
        $text = preg_replace( '/&amp;/', '', $text );
        $text = preg_replace( '/&quot;/', '', $text );
        $text = str_replace("'","\'",$text);
        $text = str_replace("%","",$text);
        $text = str_replace("^","",$text);
        $text = str_replace("<","",$text);
        $text = str_replace(">","",$text);
    } return $text;
}

function rel2abs($curdir, $relpath) {
	if (strlen($relpath) <= 0)
        return false;

    $relpath = preg_replace("'[\\\/]+'", "/", $relpath);
    if ($relpath[0] == "/" || preg_match("#^[a-z]:/#i", $relpath))
        $res = $relpath;
    else {
        $curdir = preg_replace("'[\\\/]+'", "/", $curdir);
        if ($curdir[0] != "/" && !preg_match("#^[a-z]:/#i", $curdir))
            $curdir = "/" . $curdir;
        if (substr($curdir, -1) != "/")
            $curdir .= "/";
        $res = $curdir . $relpath;
    }

    if (($p = strpos($res, "\0")) !== false)
        $res = substr($res, 0, $p);

    while (strpos($res, "/./") !== false)
        $res = str_replace("/./", "/", $res);

    while (($pos = strpos($res, "../")) !== false) {
        $lp = substr($res, 0, $pos - 1);
        $posl = bxstrrpos($lp, "/");
        if ($posl === false) return;
        $lp = substr($lp, 0, $posl + 1);
        $rp = substr($res, $pos + 3);
        $res = $lp . $rp;
    }

    $res = preg_replace("'[\\\/]+'", "/", $res);
    $res = rtrim($res, "\0");
    return $res;
}
//END OTHER FUNCTIONS
# ##############################################################################
