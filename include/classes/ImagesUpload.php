<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access
require_once('include/classes/wideimage/WideImage.php');

class ImagesUpload {
    /*
     * 
     * allowed extensions for upload files
     */
    protected $allowedExt = array('jpeg','jpg','gif','png');
    /**
     * 
     * max uploaded file size in bytes (default 5MB)
     */
    protected $maxUploadSize = 5000000;
    /**
     *
     * images params
     */   
    protected $filesUrl = '';
    protected $preparedUrl = '';
    protected $pid = false;

    /**
     * По идентификатору пользователя и его типу, определяет есть ли разрешение на авторизацию и массив доступных для него модулей
     * @param obj $user
     */
    public function __construct($filesUrl, $pid = false, $maxUploadSize=false, $allowedExt=false) {
        
        if($filesUrl) {
            $this->filesUrl = $filesUrl;
            $this->preparedUrl = prepareDirPath($this->filesUrl, true);
        } else die('Set all required params!');

        if($allowedExt) $this->allowedExt = $allowedExt;
        if($maxUploadSize) $this->maxUploadSize = $maxUploadSize;
        if($pid) $this->pid = $pid;
    }
    
    public function checkUploadedFile($file) {
        $arData = array();
        $ext = getFileExt($file['name']);
        if(in_array($ext, $this->allowedExt) && $file['size'] <  $this->maxUploadSize){
            $arData['path'] = 'data:image/'.$ext.';base64,'.base64_encode(file_get_contents($file['tmp_name']));
            $arData['name'] = $file['name']; 
        }
        else 
            $arData['error'] = 'Данный формат не поддерживается или размер файла превышает '.($this->maxUploadSize/(10^6)).'mb';
        return $arData;
    }
    
    public function cropImage($coords, $cropParams) {
        $file_ext = getFileExt($coords['name']);        
        $basename = setFilePathFormat((!empty($this->pid) ? $prefix = 'p'.$this->pid.'_' : '').basename($coords['name'], '.'.$file_ext));
        
        if(file_exists($this->preparedUrl.$basename.'.'.$file_ext)) {
            $new_name = $prefix.createUniqueFileName($this->filesUrl, $file_ext, '');
        } else $new_name = $basename.'.'.$file_ext;
        
        // сохраняем исходное, если надо уменьшаем до допустимых размеров
        // @set_time_limit( 0 );
        @ini_set('memory_limit','768M');
        $image = WideImage::load($coords['path']); 
        
        // если не установлен размер кропинга, то если установлен размер обрезки - обрезаем
        if((empty($cropParams['crop_width']) || empty($cropParams['crop_height'])) && !empty($cropParams['max_width']) && !empty($cropParams['max_height'])) {
            $resize = $image->resizeDown($cropParams['max_width'], $cropParams['max_height']);
            $resize->saveToFile($this->preparedUrl.$new_name);
        } 
        // если установлен размер кропинга, то кропим
        else if(!empty($cropParams['crop_width']) && !empty($cropParams['crop_height']) && !empty($coords)) {
            // создаем временное изображение для резалки
            if($image->getWidth() < $coords['bg_w'] || $image->getHeight() < $coords['bg_h']){
                $bgRGB  = $cropParams['crop_color'] ? hex2rgb($cropParams['crop_color']) : array(255, 255, 255);       
                $color  = $image->allocateColor($bgRGB[0], $bgRGB[1], $bgRGB[2]);
                $canvas = $image->resizeCanvas( $coords['bg_w'], $coords['bg_h'], 'center', 'center', $color);
            } else {
                $canvas = $image->copy();
            }

            // кропим главное изображение
            $cropped = $canvas->crop($coords['x'], $coords['y'], $coords['w'], $coords['h'])->resize($cropParams['crop_width'], $cropParams['crop_height']);
            $cropped->saveToFile($this->preparedUrl.$new_name);
        // если нет никаких параметров, то загружаем без изменений
        } else {
            $image->saveToFile($this->preparedUrl.$new_name);
        }
        
        // создаем изображение для каждого существующего алиаса
        if(file_exists($this->preparedUrl.$new_name)) {
            $image = WideImage::load($this->preparedUrl.$new_name); 
            if(!empty($cropParams['aliases'])) {
                foreach($cropParams['aliases'] as $param) {
                    list($alias, $w, $h) = $param;
                    $cropped = $image->resize($w, $h);
                    $cropped->saveToFile($this->preparedUrl.$alias.$new_name);
                }
            }
            return $new_name;
        } else return false;
    }

    public static function PrepareImagesParams($module, $column='image') {
        $arItem  = getItemRow(IMAGES_PARAMS_TABLE, '*', 'WHERE `module`="'.$module.'" AND `column`="'.$column.'"');
        $arItem['diffTables'] = !empty($arItem['ftable']) ? true : false;
        $arItem['ptable']     = constant($arItem['ptable']);
        $arItem['ftable']     = $arItem['ftable'] ? constant($arItem['ftable']) : '';
        $arItem['aliases']    = SystemComponent::prepareImagesParams($arItem['aliases']);
        return $arItem;
    }
}
