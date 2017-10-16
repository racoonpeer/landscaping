<?php

/**
 * WEBlife CMS
 * Created on 10.10.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


/**
 * Description of PHPHelper class
 * This class provides methods for help you to change php data, or do sumsing dunamicaly
 * You can add new methods for your needs
 * @author WebLife
 * @copyright 2011
 */
class PHPHelper {

    /**
     *
     * все выборки в каталоге 'название поля в базе' => название выборки
     */
    public $SELECTIONS = array(
        'new'  =>  LABEL_NEWEST, 
        'pop' =>  LABEL_POPULAR, 
    );

    /**
     * Список сортировок
     * @var array
     */
    static $SORT_COLUMNS = array(
        '1' => array(
            'active' => true,
            'title'  => 'по умолчанию',
            'column' => '`shortcutsOrder`',
            'show'   => 0,
        ),
        '2' => array(
            'active' => false,
            'title' => 'от дешевых к дорогим',
            'column' => '`price` ASC',
            'show'   => 1,
        ),
        '3' => array(
            'active' => false,
            'title'  => 'от дорогих к дешевым',
            'column' => '`price` DESC',
            'show'   => 1,
        ),
        '4' => array(
            'active' => false,
            'title'  => 'акционные',
            'column' => '`discount` DESC, `price` DESC',
            'show'   => 1,
        )
    );
    
    static $meta_template = "{filter_%s}";

    /**
     * <p>Функция clearModulesData - CLEAR CATEGORY MODULES DATA. <br/>
     * Данная информация должна соответсвовать данным из модуля админки при удалении елемента. <br/>
     *  ! $params обязательно нужно чтобы массив из модуля админки $arrPageData['images_params']; <br/>
     *  ! логика удаления была полностью скопирована в соответствующий case switchа с модуля в папке admin; <br/>
     *  ! путь к файлам модуля должен быть правильный;<br/>
     * </p>
     * 
     * @param int $id           идентификатор удаляемой категории
     * @param String $module    модуль категории, которую нужно удалить
     * @param String $filepath  путь папки с файлами данного модуля
     */
    public static function clearModulesData($id, $module, $filepath){
        // Получаем путь к файлам модуля
        $filepath = prepareDirPath($filepath);
        if(!$filepath) return;
            
        if($id AND $module){
            
            switch ($module) {

                case 'catalog': // CATALOG_TABLE
                    $items = getRowItems(CATALOG_TABLE, '`id`', "`cid` = $id ");

                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                        PHPHelper::deleteProduct($itemID, $filepath, $filepath.$itemID.'/');         
                    }
                    //delete category atribute groups
                    deleteRecords(CATEGORY_ATTRIBUTE_GROUPS_TABLE, ' WHERE `cid`='.$id);  
                    //delete category atributes
                    deleteRecords(CATEGORY_ATTRIBUTES_TABLE, ' WHERE `cid`='.$id);
                    //delete category filters
                    deleteRecords(CATEGORY_FILTERS_TABLE, ' WHERE `cid`='.$id);
                    //update banners redirectid
                    updateRecords(BANNERS_TABLE, '`redirectid`=0', ' WHERE `redirectid`='.$id);
                    //update main redirectid
                    updateRecords(MAIN_TABLE, '`redirectid`=0', ' WHERE `redirectid`='.$id);
                    break;

                case 'gallery': // GALLERY_TABLE
                    $items  = getRowItems(GALLERY_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                      //  unlinkImageLangsSynchronize($itemID, GALLERY_TABLE, $filepath, $params);
                        PHPHelper::deleteImages($itemID, $filepath, $module);
                        deleteFileFromDB_AllLangs($itemID, GALLERY_TABLE, 'filename', ' WHERE `id`='.$itemID, $filepath);
                        deleteDBLangsSync(GALLERY_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'gallery');
                    } break;

                case 'news': // NEWS_TABLE
                    $items  = getRowItems(NEWS_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                       // unlinkImageLangsSynchronize($itemID, NEWS_TABLE, $filepath, $params);
                        PHPHelper::deleteImages($itemID, $filepath, $module);
                        deleteDBLangsSync(NEWS_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'news');              
                    } break;

                case 'video': // VIDEOS_TABLE
                    $items  = getRowItems(VIDEOS_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                     //   unlinkImageLangsSynchronize($itemID, VIDEOS_TABLE, $filepath, $params);
                        PHPHelper::deleteImages($itemID, $filepath, $module);
                        deleteFileFromDB_AllLangs($itemID, VIDEOS_TABLE, 'filename', ' WHERE `id`='.$itemID, $filepath);
                        deleteDBLangsSync(VIDEOS_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'video');
                    } break;
                    
                default: break;
            }
        }
    }
    
    public static function getLastWatched($UrlWL){
        require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
        $Cookie         = new CCookie();
        $items          = array();
        if($Cookie->isSetCookie('watched')) {
            $itemsIDX = unserialize($Cookie->getCookie('watched'));            
            $items = getRowItems(CATALOG_TABLE, '*', '`id` in ('.implode(',', $itemsIDX).')', 'FIND_IN_SET(id, "'.implode(',', $itemsIDX).'") DESC');
            $images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));
           
            if(!empty($items)) {
                foreach ($items as $key => $item) {
                    $items[$key] = PHPHelper::getProductItem($item, $UrlWL, UPLOAD_URL_DIR.'catalog/', $images_params);
                }
            }
        }
       
        return $items;
    }
    
    public static function deleteImages($itemID, $files_path, $module) {
        $images_params = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$module.'"');
        foreach($images_params as $param) {
            $aliases = SystemComponent::prepareImagesParams($param['aliases']);
            if($param['ftable']) {
                unlinkImageLangsSynchronize($itemID, constant($param['ftable']), $files_path, $aliases, $param['column']);
                deleteRecords(constant($param['ftable']), 'WHERE `pid`='.$itemID);
            } else {
                unlinkImageLangsSynchronize($itemID, constant($param['ptable']), $files_path, $aliases, $param['column']);
            }
        }
    }
    
    public static function deleteProduct($itemID, $file_path, $catalog_files_path, $module='catalog') {
        if($itemID) {
            $title = getValueFromDB(CATALOG_TABLE, 'title', 'WHERE `id`='.$itemID);
            self::deleteImages($itemID, $file_path, $module);
            deleteFileFromDB_AllLangs($itemID, CATALOG_TABLE, 'filename', ' WHERE `id`='.$itemID, $file_path);
            $result = deleteDBLangsSync(CATALOG_TABLE, ' WHERE id='.$itemID);
            if (!$result) {
                return false;
            } elseif ($result) {
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$title.'"', $key, $title, 0, $module);
                }
                removeDir($catalog_files_path);
                //delete shortcuts
                deleteRecords(SHORTCUTS_TABLE, ' WHERE `module`="catalog" AND `pid`='.$itemID);
                //delete attributes
                deleteDBLangsSync(PRODUCT_ATTRIBUTE_TABLE, ' WHERE `pid`='.$itemID);
                //delete related products
                deleteRecords(CATALOG_RELATED_TABLE, ' WHERE `pid`='.$itemID.' OR `rid`='.$itemID);
                //delete product comments
                deleteRecords(COMMENTS_TABLE, ' WHERE pid='.$itemID.' AND `module`="'.$module.'"'); 
                //delete product comments
                deleteRecords(CATALOG_KITS_TABLE, ' WHERE `pid`='.$itemID.' OR `kid`='.$itemID); 
                //delete selections
                deleteRecords(PRODUCT_SELECTIONS_TABLE, ' WHERE `pid`='.$itemID);
                //delete options
                deleteRecords(PRODUCT_OPTIONS_TABLE, ' WHERE `pid`='.$itemID);
                deleteRecords(PRODUCT_OPTIONS_VALUES_TABLE, ' WHERE `product_id`='.$itemID);
                return true;
            }
        }
    }

    public static function getProductItem($item, $UrlWL, $images_path, $arAliases, $inList=true, $shortcutCid=false, $module='catalog', $isKitItem = false) {
        if (!empty($item)) {
            // Set vars
            $item['arCategory']   = $UrlWL->getCategoryById($item['cid']);
            $item['descr']        = unScreenData($item['descr']);
            $item['fulldescr']    = unScreenData($item['fulldescr']);
            // item options
            $item['options']         = self::getProductOptions($item['id']);
            $item['selectedOptions'] = self::getSelectedOptions($item['options']);
            $item["idKey"]           = self::makeProductIdKey($item["id"], $item['selectedOptions']);
            
            // Recalc Item price by options
            $item["price"]  = !$isKitItem ? self::recalcItemPriceByOptions($item["price"],  $item["options"]) : $item["price"];
            $item["cprice"] = !$isKitItem ? self::recalcItemPriceByOptions($item["cprice"], $item["options"]) : $item["cprice"];
            // set price with discount
            $item['new_price'] = ($item['isdiscount'] AND $item['discount'])? ($item['price'] - ($item['price']*$item['discount']/100)): 0; 
            // get brand
            $item['arBrand']   = getItemRow(BRANDS_TABLE, '*', 'WHERE `id`='.$item['bid']);
            // update view count
            if(!$inList) {
                $item['viewed']       = ++$item['viewed'];
                updateDBLangsSync(CATALOG_TABLE, '`viewed`='.$item['viewed'], 'WHERE `id`='.$item['id']);
            }
            // get item attribute groups
            $item['attrGroups']   = self::getProductAttributes($item['id'], false, ($inList ? false : true), $shortcutCid);
            // get item comments count
            $item['commentsCount']= intval(getValueFromDB(COMMENTS_TABLE.' t', 'count(*)','WHERE t.`pid`='.$item['id'].' AND t.`active`=1 AND t.`module`="'.$module.'"', 'count'));
            // get selections 
            // $item['is_pop']       = (getValueFromDB(PRODUCT_SELECTIONS_TABLE.' ps', 'ps.`id`', 'WHERE ps.`type`="pop" AND ps.`pid`='.$item['id'], 'hit'))? true: false;            

            // get images
            $item['images']       = array();
            $query = 'SELECT * FROM '.CATALOGFILES_TABLE.' WHERE `pid`='.(int)$item['id'].' AND `active`=1 ORDER BY `fileorder`';
            $result = mysql_query($query);
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    PHPHelper::getProductImage($images_path, $item['id'], &$row, 'filename', $arAliases);
                    if($row['isdefault']) $item['defaultImage'] = $row;
                    else    $item['images'][]    = $row;
                }
            }
            
            if(empty($item['images']) && !empty($item['image'])) {
                PHPHelper::getProductImage($images_path, 0, &$item, 'image', $arAliases);
                $item['defaultImage'] = $item;
            }
            
        }
        return $item;
    }
    
    public static function makeProductIdKey ($id = 0, $options = array()) {
        global $Basket;
        $idKey = !empty($id) ? $id : "";
        if (!empty($options)) {
            $idKey .= $Basket->optionsIndicator;
            foreach ($options as $optionID=>$valueID) {
                $idKey .= $optionID.$Basket->valueSeparator.(is_array($valueID) ? implode($Basket->valueIterator, $valueID) : $valueID).$Basket->optionsSeparator;
            }
        }
        return $idKey;
    }

    public static function getSelectedOptions ($options) {
        $arOptions = array();
        $selected = array();
        if (!empty($options)) {
            foreach ($options as $optID => $option) {
                $selected[$optID] = array();
                foreach ($option["values"] as $valID => $value) {
                    if ($value["selected"] > 0) $selected[$optID][] = $valID;
                }
            }
        }
        if (!empty($selected)) {
            foreach ($selected as $optID => $arVal) {
                if (!empty($arVal))
                    $arOptions[$optID] = (count($arVal)>1) ? $arVal : $arVal[0];
            }
        }
        return $arOptions;
    }

    public static function recalcItemPriceByOptions ($price, $options) {
        if (!empty($options)) {
            foreach ($options as $optionID => $option) {
                foreach ($option["values"] as $valueID => $value) {
                    if ($value["selected"] > 0 AND $value["operator"]=="+") {
                        $price += $value["price"];
                    } elseif ($value["selected"] > 0 AND $value["operator"]=="-") {
                        $price -= $value["price"];
                    }
                }
            }
        }
        return $price;
    }

    public static function getProductImage($img_path, $itemID, $item, $filename='image', $arAliases=array('')){  
        $item_img_path = $img_path.($itemID ? $itemID : '').'/';
        if(!empty($arAliases)) {
            foreach($arAliases as $arAlias) {
                // $arAlias[0] - 'big_' , $arAlias[1] - width, $arAlias[2] - height
                $item[$arAlias[0].'image'] = (!empty($item[$filename]) AND is_file(prepareDirPath($item_img_path).$arAlias[0].$item[$filename])) ? $item_img_path.$arAlias[0].$item[$filename] : $img_path.$arAlias[0].'noimage.jpg';
            } 
        } else {
            $item[$filename] = (!empty($item[$filename]) AND is_file(prepareDirPath($item_img_path).$item[$filename])) ? $item_img_path.$item[$filename] : $img_path.'noimage.jpg';
        }
    }
    
    public static function getProductAttributes($itemID, $showEmptyAttr=false, $showAll = false, $shortcutCid=false){        
        $attrGroups = array();
        $cid = $shortcutCid ? $shortcutCid : getValueFromDB(CATALOG_TABLE, 'cid', 'WHERE `id`='.$itemID);
        if ($cid) {
            $select = 'SELECT ag.* FROM `'.ATTRIBUTE_GROUPS_TABLE.'` ag ';
            $join = 'LEFT JOIN `'.CATEGORY_ATTRIBUTE_GROUPS_TABLE.'` cag ON(cag.`gid` = ag.`id`) ';
            $where = 'WHERE ag.`active`>0 AND cag.`cid`='.$cid.' ';
            $order = 'ORDER BY ' . (!$showAll ? '  cag.`order` ' : ' ag.`order` ');
            $query = $select.$join.$where.$order;
            $result = mysql_query($query);
            if(mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $attrGroups[] = $row;
                }
                // item attributes
                foreach ($attrGroups as $k => $v) {
                    $attrGroups[$k]['attributes'] = array();
                    $select = 'SELECT DISTINCT a.*, pa.`value`, avt.`image` FROM `'.ATTRIBUTES_TABLE.'` a ';
                    $join  = 'LEFT JOIN `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ON(pa.`aid` = a.`id` AND pa.`pid`='.$itemID.') '.(!$showAll ? 'LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) ' : '');
                    $join2 = ' LEFT JOIN `'.ATTRIBUTES_VALUES_TABLE.'` avt ON (a.`id`=avt.`aid` AND pa.`value`=avt.`title`) ';
                    $where = 'WHERE pa.`pid`='.$itemID.' AND a.`gid`='.$attrGroups[$k]['id'].' '.(!$showAll ? ' AND ca.`cid`='.$cid.' ' : '');
                    $order = 'ORDER BY ' . (!$showAll ? '  ca.`order` ' : ' a.`order` ');
                    $query = $select.$join.$join2.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($r = mysql_fetch_assoc($result)) {
                            if($showEmptyAttr) {
                                $attrGroups[$k]['attributes'][$r['id']]['title'] = $r['title'];
                                $attrGroups[$k]['attributes'][$r['id']]['image'] = $r['image'];
                                $attrGroups[$k]['attributes'][$r['id']]['values'][] = $r['value']; 
                            } else {
                                if(!empty($r['value'])) {
                                    $attrGroups[$k]['attributes'][$r['id']]['title'] = $r['title'];
                                    $attrGroups[$k]['attributes'][$r['id']]['image'] = $r['image'];
                                    $attrGroups[$k]['attributes'][$r['id']]['values'][] = $r['value']; 
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attrGroups;
    }
    
    public static function getProductOptions($itemID, $selected = array()) {
        $arOptions = array();
        $filesUrl = UPLOAD_URL_DIR.'options/';
        $filesPath = prepareDirPath($filesUrl);
        $query = 'SELECT o.*, po.`required`, ot.`title` AS `typename` FROM `'.OPTIONS_TABLE.'` o '.PHP_EOL
                .'LEFT JOIN `'.OPTIONS_TYPES_TABLE.'` ot ON(ot.`id` = o.`type_id`) '.PHP_EOL
                .'LEFT JOIN `'.PRODUCT_OPTIONS_TABLE.'` po ON(po.`oid` = o.`id`) '.PHP_EOL
                .'WHERE o.`active`>0 AND po.`pid`='.$itemID.' '.PHP_EOL
                .'GROUP BY o.`id` ORDER BY o.`order`';
        $result = mysql_query($query);
        if($result AND mysql_num_rows($result) > 0) {
            while ($option = mysql_fetch_assoc($result)) {
                $option['descr']  = unScreenData($option['descr']);
                $option['image']  = (!empty($option['image']) AND is_file($filesPath.$option['image'])) ? $filesUrl.$option['image'] : '';
                $option["values"] = array();
                $qry  = "SELECT ov.*, pov.`operator`, pov.`price`, pov.`primary`, '0' AS `selected` FROM `".OPTIONS_VALUES_TABLE."` ov ";
                $qry .= "LEFT JOIN `".PRODUCT_OPTIONS_VALUES_TABLE."` pov ON(pov.`value_id`=ov.`id`) ";
                $qry .= "WHERE pov.`option_id`={$option["id"]} AND pov.`product_id`={$itemID} ";
                $qry .= "GROUP BY ov.`id` ORDER BY ov.`order`";
                $res  = mysql_query($qry);
                if ($res AND mysql_num_rows($res) > 0) {
                    $primary = 0;
                    while ($value = mysql_fetch_assoc($res)) {
                        $value['image'] = (!empty($value['image']) AND is_file($filesPath.$value['image'])) ? $filesUrl.$value['image'] : '';
                        if ($option['required']==0) $value['primary'] = 0;
                        if ($value['primary'] > 0) $primary = $value['id'];
                        $option["values"][$value["id"]] = $value;
                    }
                    if (isset($selected[$option['id']]) AND !is_array($selected[$option['id']]) AND isset($option["values"][$selected[$option['id']]])) {
                        $primary = $selected[$option['id']];
                    } elseif (isset($selected[$option['id']]) AND is_array($selected[$option['id']])) {
                        foreach ($option["values"] as $key=>$value) {
                            if (in_array($key, $selected[$option['id']])) $option["values"][$key]["selected"] = 1;
                        }
                    }
                    if ($primary) $option["values"][$primary]['selected'] = 1;
                    $arOptions[$option["id"]] = $option;
                }
            }
        }
        return $arOptions;
    }
    
        
    public static function getCatalogItemsSql($arCatsIDX, $lang, $module, $allwhere = '', $allorder = '', $alllimit = '', $onlyCnt = false) {
        $select = 'SELECT t.* ';
        // include comments if it needs 
        // $select .= ', (SELECT COUNT(id) FROM comments WHERE pid=t.id AND active=1 AND module="catalog") as commentsCnt ';
        $from   = ' FROM `'.CATALOG_TABLE.'` t ';
        $where  = ' WHERE t.`active`=1  ';   
        $cidin  = '`cid` IN ('.implode(',', $arCatsIDX).') ';

        // shortcuts query
        $squery  = $select.', st.`order` AS `shortcutsOrder`, "1" AS `isshortcut` '.$from.' LEFT JOIN `'.SHORTCUTS_TABLE.'` st ON(t.`id`=st.`pid`) '.
                   $where.' AND st.`active`=1 AND st.`lang`="'.$lang.'" AND st.`module`="'.$module.'" AND st.'.$cidin;
        
        // catalog query
        $cquery = $select.', t.`order` AS `shortcutsOrder`, "0" AS `isshortcut` '.$from.$where.' AND t.'.$cidin;
        
        return  ($onlyCnt ? 'SELECT COUNT(*) FROM (' : '').'('.$squery.$allwhere.') UNION ('.$cquery.$allwhere.') '.$allorder.' '.$alllimit.($onlyCnt ? ') sbt ' : ''); 
    }
    
    public static function getCatalogItems($arCatsIDX, $lang, $module, $where = '', $order = '', $limit = '', $onlyIDX = false) {
        $items = array();
        $query = self::getCatalogItemsSql($arCatsIDX, $lang, $module, $where, $order, $limit);
        $result = mysql_query($query);
        if($result) {
            while(($row = mysql_fetch_assoc($result))) { 
                if($onlyIDX) {
                    $items[] = $row['id']; 
                } else {
                    $items[$row['id']] = $row; 
                }
            }              
        }
        return $items;
    }   
    
    public static function getCatalogItemsCnt($arCatsIDX, $lang, $module, $where = '', $order = '', $limit = '') {
        $query = self::getCatalogItemsSql($arCatsIDX, $lang, $module, $where, $order, $limit, true);
        $result = mysql_query($query);
        return $result ? mysql_result($result, 0) : 0;
    }

    /**
     * @param type $sortID
     * @return type
     */
    public static function getCatalogSorting(UrlWL $UrlWL, $sortID=0) {
        $columns = self::$SORT_COLUMNS;
        $_UrlWL = $UrlWL->copy();
        $_isDef = (!self::checkCatalogSort($sortID) || $sortID == self::getDefaultCatalogSort());
        foreach(array_keys($columns) as $key){
            $columns[$key]['active'] = ($sortID == $key);
            if($columns[$key]['active'] && $_isDef){
                $_UrlWL->unsetParam(UrlWL::SORT_KEY_NAME);
            } else {
                $_UrlWL->setParam(UrlWL::SORT_KEY_NAME, $key);
            }
            $columns[$key]['url'] = $_UrlWL->buildUrl();
        }
        unset($_UrlWL);
        return $columns;
    }

    /**
     * @param type $sortID
     * @return bool
     */
    public static function checkCatalogSort($sortID) {
        return ($sortID && array_key_exists($sortID, self::$SORT_COLUMNS));
    }

    /**
     * @param type $sortID
     * @return int
     */
    public static function getCorrectCatalogSort($sortID) {
        return self::checkCatalogSort($sortID) ? $sortID : self::getDefaultCatalogSort() ;
    }

    /**
     * @param type $sortID
     * @return type
     */
    public static function getDefaultCatalogSort() {
        return reset(array_keys(self::$SORT_COLUMNS));
    }
    
    public static function getSliderItems() {
        static $items = array();
        if(empty($items)){
            $query  = "SELECT * FROM `".HOMESLIDER_TABLE."` WHERE `active`=1 AND `image`<>'' ORDER BY `order`, `id`";
            $result = mysql_query($query);
            while ($row = mysql_fetch_assoc($result)) {
                $row['path']  = UPLOAD_URL_DIR.'homeslider/';
                $row['title'] = unScreenData($row['title']);
                $items[] = $row;
            }
        } return $items;
    }
    
    public static function dataConv($item, $from = "windows-1251", $to = "utf-8", $translit = false, $bApplyTrim = false) {
        if (is_object($item) AND $item instanceof stdClass) {
            $item = (array) $item;
        }
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = PHPHelper::dataConv($value, $from, $to, $translit, $bApplyTrim);
            }
        } else if (!is_bool($item) AND $item) {
            if ($bApplyTrim) $item = trim($item);
            if ($item)  $item = iconv($from, $to . ($translit ? "//TRANSLIT" : ''), $item);
        } return $item;
    }
    
    public static function mb_dataConv($item, $to = "CP1251", $from = 'UTF-8') {
        if (is_object($item) AND $item instanceof stdClass) {
            $item = (array) $item;
        }
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = self::mb_dataConv($value, $to, $from);
            }
        } else if (!is_bool($item) AND $item) {
            if ($item)  $item = mb_convert_encoding($item, $to, $from);
        } return $item;
    }
    
    public static function makeAttributeAlias($val) {
        $sibling = intval(getValueFromDB(PRODUCT_ATTRIBUTE_TABLE, '`alias`', 'WHERE `value`="'.$val.'"', 'val'));
        $increment = intval(getValueFromDB(PRODUCT_ATTRIBUTE_TABLE, 'MAX(`alias`)', '', 'maxval'));
        if(is_int($sibling) AND $sibling>0) {
            return $sibling;
        } else {
            if($increment > 0) {
                return $increment+1;
            } else {
                return 1;
            }
        }
    }

    /**
     * This function emulates php internal function basename
     * but does not behave badly on broken locale settings
     * @param string $path
     * @param string $ext
     * @return string
     */
    function BaseName($path, $ext="") {
        $path = rtrim($path, "\\/");
        if (preg_match("#[^\\\\/]+$#", $path, $match))
            $path = $match[0];
        if ($ext) {
            $ext_len = strlen($ext);
            if (strlen($path) > $ext_len AND substr($path, -$ext_len) == $ext)
                $path = substr($path, 0, -$ext_len);
        } return $path;
    }

    function StrRPos($haystack, $needle) {
        $idx = strpos(strrev($haystack), strrev($needle));
        if($idx === false) return false;
        $idx = strlen($haystack) - strlen($needle) - $idx;
        return $idx;
    }
    
    public static function BuildFilterMetaData ($meta, $filters) {
        foreach ($filters as $key=>$title) {
            $replace = sprintf(self::$meta_template, $key);
            $meta = str_replace($replace, $title, $meta);
        }
        $meta = preg_replace("/".sprintf(self::$meta_template, "\d+")."/", "", $meta);
        return trim(str_replace("  ", " ", $meta));
    }
}


/**
 * Description of ImportExport class
 * This class provides methods for Import / Export data
 * @author WebLife
 * @copyright 2013
 */
class ImportExport {

    const CSV_DELIMITER     = ';';
    const CSV_ENCLOSURE     = '"';
    
    const YML_TYPE_DEFAULT  = 'vendor.model';
    
    public function __construct() {
        setlocale(LC_ALL, array('ru_RU.CP1251', 'ru_RU', 'ru', 'rus_RUS'));
    }
    
    public static function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals, self::CSV_DELIMITER, self::CSV_ENCLOSURE); // add parameters if you want
    }
    
    public static function __outputYML($filehandler, $str) {
        fwrite($filehandler, $str); 
    }
    
    public static function __fgetcsv($handle, $length, $delimiter=',', $enclosure='"'){
        if (version_compare(PHP_VERSION, "5.2.1", ">")) {
            $arLine = fgetcsv($handle, $length, $delimiter, $enclosure);
        } else {
            $line   = fgets($handle);
            $arLine = $line ? explode($delimiter, trim($line)) : $line;
            if(is_array($arLine)){
                foreach($arLine as $k=>$v) { 
                    $arLine[$k] = ltrim(rtrim($v, $enclosure), $enclosure);
                }
            }
        } return $arLine;
    }
    
    public static function outputCSV(array $arCSVData, $filename='output', $exit=true){
        //  Вывод примера файла с десятю строчками информации о товарах    
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"{$filename}.csv\";");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        $outstream = fopen("php://output", "w");
        array_walk($arCSVData, "ImportExport::__outputCSV", $outstream);
        fclose($outstream);
        if($exit) exit();
    }
    
    public static function outputYML(array $arYMLData, $filename='output', $type="", $exit=true){ 
        header("Content-type: text/xml; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"{$filename}.yml\";");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        $outstream = fopen("php://output", "w");
        //add <!DOCTYPE yml_catalog SYSTEM "shops.dtd">
        ImportExport::__outputYML($outstream, iconv('windows-1251', 'utf-8', ImportExport::generateYML($arYMLData)));
        fclose($outstream);
        if($exit) exit();
    }
    
    public static function generateYML(array $arYMLData){ 
        // http://partner.market.yandex.ru/legal/tt/#id1164252226643
        $dom = new domDocument("1.0", "utf-8");  
        $root = $dom->createElement("yml_catalog"); 
        $root->setAttribute("date", date("Y-m-d h:m")); 
        $dom->appendChild($root);

        $shop = $dom->createElement("shop");
        // основные данные про гамазин
        $shop->appendChild($dom->createElement("name", $arYMLData['name']));
        $shop->appendChild($dom->createElement("company", $arYMLData['company']));
        $shop->appendChild($dom->createElement("url", $arYMLData['url']));
        $shop->appendChild($dom->createElement("email", $arYMLData['email']));

        // валюты
        $currencies = $dom->createElement("currencies");
        if(!empty($arYMLData['arCurrencies'])) {
            foreach($arYMLData['arCurrencies'] as $value){
                $currency = $dom->createElement("currency");
                $currency->setAttribute("id", $value['id']);
                $currency->setAttribute("rate", $value['rate']);
                $currencies->appendChild($currency);
            }
        }
        $shop->appendChild($currencies);

        // категории
        $categories = $dom->createElement("categories");
        if(!empty($arYMLData['arCategories'])) {
            foreach($arYMLData['arCategories'] as $cat) {
                $category = $dom->createElement("category", $cat['title']);
                $category->setAttribute("id", $cat['id']);
                if($cat['pid']!=$arYMLData['catalog_root_id'])
                    $category->setAttribute("parentId", $cat['pid']);
                $categories->appendChild($category);
            }
        }
        $shop->appendChild($categories);    

        // стоимость доставки 
        $shop->appendChild($dom->createElement("local_delivery_cost", $arYMLData['local_delivery_cost'])); 
        
        // товары        
        // произвольный тип (vendor.model), параметры: ? - необязательный, !-обязательный
        // 1. url(? max 512) price(!) currencyId(!) categoryId(!) picture(? (для одежды обязательно)) store(?true|false) 
        // pickup(?true|false) delivery(?true|false) local_delivery_cost(?) typePrefix(? категория/группа)
        // name(!) vendor(!) vendorCode(?(артикул от производитеял)) model(!) description(?) 
        // sales_notes(? мин сум заказа) seller_warranty и manufacturer_warranty(?false|true - имеет|нет оф гарантию/строка гарантии (ISO 8601, например: P1Y2M10DT2H30M) 
        // country_of_origin(?) downloadable(?) adult(?) age(? =0, 6, 12, 16, 18) 
        // barcode(? штирхкод производителя) cpa(?) rec(? рекомендованные товары) expiry(?)
        // weight(? kg+tara kg) dimensions(?) param(? атрибуты - значения)
        $offers = $dom->createElement("offers");
        if(!empty($arYMLData['arProducts'])) {
            foreach($arYMLData['arProducts'] as $product) {
                $offer = $dom->createElement("offer");
                $offer->setAttribute("type", self::YML_TYPE_DEFAULT);
                $offer->setAttribute("id", $product['id']);
                $offer->setAttribute("bid", '');
                $offer->setAttribute("available", 'true');

                foreach($product['arParams'] as $key=>$value) {
                    $offer->appendChild($dom->createElement($key, $value));
                }

                if(!empty($product['arAttributes'])) {
                    foreach($product['arAttributes'] as $attr) {
                        $param = $dom->createElement('param', $attr['value']);
                        $param->setAttribute("name", $attr['title']);
                        $offer->appendChild($param);
                    }  
                }
                $offers->appendChild($offer);
            }
        }  
        $shop->appendChild($offers);  
        $root->appendChild($shop);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
    
    public static function outputSqlFiles(array $arFiles, $outfilename, $exit=true){
        // set headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");   
        header("Content-type: text/sql");
        header("Content-Disposition: attachment; filename=\"{$outfilename}.sql\"");
        header("Content-Transfer-Encoding: binary");
            
        $outstream = fopen("php://output", "w");
        foreach ($arFiles as $file) {
            if(file_exists($file) AND is_file($file) AND is_readable($file)){
                $file = @fopen($file, "rb");
                if($file) {
                    while(!feof($file)) {
                        fwrite($outstream, fread($file, 1024 * 8));
                        if( connection_status()!=0 ) {
                            @fclose($file);
                            die();
                        }
                    } @fclose($file);
                }
            }
        }
        fclose($outstream);
        if($exit) exit();
    }

    public static function outputFile($file, $exit=true){
        if( $file AND strpos($file, "\0") === FALSE/*Nullbyte hack fix*/){
            // Make sure program execution doesn't time out
            // Set maximum script execution time in seconds (0 means no limit)
            @set_time_limit(0);

            // Make sure that header not sent by error
            // Sets which PHP errors are reported
            @error_reporting(0);

            // Allow direct file download (hotlinking)?  Empty - allow hotlinking
            // If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
            $allowed_referrer = $_SERVER['SERVER_NAME'];

            // If hotlinking not allowed then make hackers think there are some server problems
            if ( !empty($allowed_referrer) AND
                 (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper($allowed_referrer)) === false)
            ) die("Internal server error. Please contact system administrator.");

            // if don't exist and isn't file and  can't read them - die
            if (!file_exists($file) AND !is_file($file) AND !is_readable($file)) {
              header ("HTTP/1.0 404 Not Found");
              exit();
            }

            // Get real file name.
            // Remove any path info to avoid hacking by adding relative path, etc.
            $fname = basename($file);

            // file size in bytes
            $fsize = filesize($file);

            // get mime type
            $mtype = '';
            // mime type is not set, get from server settings
            if (function_exists('mime_content_type')) {
                $mtype = mime_content_type($file);
            } else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME); // return mime type
                $mtype = finfo_file($finfo, $file);
                finfo_close($finfo);
            }
            if ($mtype == '') {
                $mtype = "application/force-download";
            }

            // Browser will try to save file with this filename, regardless original filename.
            // You can override it if needed.

            // remove some bad chars
            $asfname = str_replace(array('"',"'",'\\','/'), '', $fname);
            if ($asfname === '') $asfname = 'NoName';

            // set headers
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Type: $mtype");
            header("Content-Disposition: attachment; filename=\"$asfname\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $fsize);

            // download
            // @readfile($file);
            $file = @fopen($file, "rb");
            if($file) {
                while(!feof($file)) {
                    print(fread($file, 1024 * 8));
                    flush();
                    if( connection_status()!=0 ) {
                        @fclose($file);
                        die();
                    }
                } @fclose($file);
            }
            if($exit) exit();
        } else { die(); }
    }

    public static function parseFile($csvfile, $presentColumnNames=true) {
        if(empty($csvfile) || !is_file($csvfile)) return false;
        
	$filesize       = filesize($csvfile);
	$handle         = fopen($csvfile, "r"); 
        if(!$filesize || !$handle) return false;

        $csvfilename    = basename($csvfile);
        $arLine         = array();
        if($presentColumnNames){
            //Получаем первую строку из CSV файла и приводим ее в нужный вид (удаляем пробелы в начале и в конце строки, понижаем в нижний регистр)
            $arLine = self::__fgetcsv($handle, $filesize, self::CSV_DELIMITER, self::CSV_ENCLOSURE);
            if(!empty($arLine)){
                foreach($arLine as $column=>$val) {
                    $arLine[$column] = strtolower(trim($val));
                }
            } //print_r($arLine); exit();
            // создаем массив с ключем(название колонки в базе) и позицией в строке CSV начиная с 0
            $arLine = array_flip($arLine); // Поменять местами ключи и значения массива, т.е. ключи становятся значениями, а значения становятся ключами
        }
        
        // Создаем результирующий массив
        $arData = array('columns'=>$arLine, 'data'=>array(), 'count'=>0);
        
        // Импортируем из CSV файла данные в пустую базу
        while( ($arLine = self::__fgetcsv($handle, $filesize, self::CSV_DELIMITER, self::CSV_ENCLOSURE)) !== FALSE ) {
            // проверка на правильное количество колонок. если не одинаковое - пропускаем
            if($presentColumnNames AND count($arLine)!=count($arData['columns'])) continue; //например строки где разделение каким-то текстом или неверный формат
            $arData['data'][] = $arLine;
            $arData['count']++;
        }
        // закрываем файл
        fclose($handle);
        // удаляем файл
//        @unlink($csvfile);
        // возвращаем 
        return $arData;
    }

    public static function assocArrayToQuery(array $arData, $arSkip=array(), $type='insert') {
        if(empty($arData)) return false;
        $keys = $values = $arSkiped = array();
        foreach($arSkip as $key) { if(key_exists($key, $arData)) $arSkiped[$key] = $arData[$key]; }
        switch($type) {
            case 'insert':
                foreach($arData as $key=>$value) {
                    if (!in_array($key, $arSkip)) {
                        $keys[]   = $key;
                        $values[] = ($value===NULL || $value==="NULL") ? 'NULL' : ($value==="NOW()" ? "NOW()" : "'".self::forSql($value)."'");
                    }
                } break;
            case 'update':
                foreach($arData as $key => $value) {
                    if (!in_array($key, $arSkip)) {
                        $values[] = ($value===NULL || $value==="NULL") ? $key."=NULL" : ($value==="NOW()" ? "NOW()" : $key."='".self::forSql($value)."'");
                    }
                } break;
        } return array("keys"=>implode(", ",$keys), "values"=>implode(", ",$values), 'arSkiped'=>$arSkiped);
    }

    public static function forSql($str, $imaxln=0) {
        $str = str_replace("\0",  '', $str);
        $str = str_replace("'", '"', $str);
        if($imaxln>0) $str = substr($str, 0, $imaxln);
        return $str;
    }
}


/**
 * Description of Pager class
 * This class provides methods for create and manage pages
 * @author WebLife
 * @copyright 2015
 */
class Pager {

    /**
     * @var UrlWL
     */
    protected $UrlWL;

    /**
     * @var int
     */
    protected $first;

    /**
     * @var int
     */
    protected $last;

    /**
     * @var int
     */
    protected $prev;

    /**
     * @var int
     */
    protected $next;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var array
     */
    protected $pages;
    /**
     * @var UrlWL for url generate
     */
    private $_UrlWL;

    const PAGES_SEP = '...';

    /**
     * @param UrlWL $UrlWL
     * @param int $current
     * @param int $total
     * @param int $limit
     * @param mixed $separator | string or false
     */
    public function __construct(UrlWL $UrlWL, &$current, $total, $limit, $separator=self::PAGES_SEP) {
        $this->UrlWL = $UrlWL;
        $this->count = 0;
        $this->pages = array();
        $this->prev = $this->next = $this->first = $this->last = 1;
        $this->calc($current, $total, $limit, $separator);
        $this->UrlWL->setPage($current);
    }


    private function calc(&$current, $total, $limit, $separator=false){
        if(!$total) $total = 1;
        $this->count = $this->last = intval(ceil($total / $limit));
        if($current > $this->count) {
            $current = $this->count;
        }
        if ($this->count > 1) {
            $this->prev = ($current > 1)            ? $current-1 : 1;
            $this->next = ($current < $this->count) ? $current+1 : $this->count;

            $this->pages[] = 1;
            if($this->count <= 5 || $separator === false) {
                for($i = 2; $i < $this->count; $i++){
                    $this->pages[] = $i;
                }
            }
            else if($current <= 3) {
                for($i = 2; $i <= 5; $i++){
                    $this->pages[] = $i;
                }
                $this->pages[] = $separator;
            }
            else {
                $start = $this->count- ($this->count - $current + 3);
                if($current == $this->count){
                    $start -= 2;
                }
                if($current == $this->count - 1){
                    $start--;
                }
                if($start < 0) {
                    $start = 0;
                }

                $end = $this->count - ($this->count - $current-3);
                if( $end > $this->count){
                    $end=$this->count;
                }

                if($current > 4){
                    $this->pages[] = $separator;
                }
                for($i = 1+$start; $i < $end; $i++){
                    $this->pages[] = $i;
                }
                if($current < $this->count-3){
                    $this->pages[] = $separator;
                }
            }
            $this->pages[] = $this->count;
        }
        return $this;
    }

    /**
     * @param int $page
     * @return string
     */
    public function getUrl($page){
        if($this->_UrlWL===null){
            $this->_UrlWL = $this->UrlWL->copy();
        }
        $this->_UrlWL->setPath($this->UrlWL->getPath());
        if($page == UrlWL::PAGES_ALL_VAL){
            $page = 1;
            $this->_UrlWL->setParam(UrlWL::PAGES_KEY_NAME, UrlWL::PAGES_ALL_VAL);
        } else {
            $this->_UrlWL->unsetParam(UrlWL::PAGES_KEY_NAME);
        }
        $this->_UrlWL->setPage($page);
        return $this->_UrlWL->buildUrl();
    }
    /**
     * @return string
     */
    public function getAllUrl() {
        return $this->getUrl(UrlWL::PAGES_ALL_VAL);
    }
    /**
     * @return int
     */
    public function getFirst() {
        return $this->first;
    }
    /**
     * @return int
     */
    public function getLast() {
        return $this->last;
    }
    /**
     * @return int
     */
    public function getPrev() {
        return $this->prev;
    }
    /**
     * @return int
     */
    public function getNext() {
        return $this->next;
    }
    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
    }
    /**
     * @return array
     */
    public function getPages() {
        return $this->pages;
    }
    /**
     * @return string
     */
    public function getSeparator() {
        return self::PAGES_SEP;
    }
}