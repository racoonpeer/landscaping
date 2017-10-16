<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access

class Filters {
    
    // category id
    protected $catid = 0;
    
    // UrlWl
    protected $UrlWL = null;
    
    // children cats idx
    protected $arCatsIDX = array();
    
    // UrlWl for url generate
    private $_UrlWL = null;
    
    // array of filters by categories
    private $_categoriesFilters = array();
    
    /**
     * 
     * @param UrlWL $UrlWL
     * @param type $categoryID     
     * @param type $arCatsIDX
     */
    public function __construct($UrlWL = null, $categoryID = 0, array $arCatsIDX = array()) {
        if(!$categoryID || !($UrlWL instanceof UrlWL)) die('Нет категории и урлов - нет фильтров!');
        
        $this->catid  = $categoryID;         
        $this->UrlWL  = $UrlWL;

        $this->arCatsIDX = $arCatsIDX;
        
    }
        
    /**
     * 
     * @param type $excludePrice
     * @return string
     */
    public function generateWhereState($filters = null, $excludePrice = false) {
        $where = '';
        $filters = ($filters === null ? $this->UrlWL->getFilters()->getSelected() : (is_array($filters) ? $filters : array()));
        foreach ($filters as $filterID => $filter) {
            $frow = getItemRow(FILTERS_TABLE, '*', 'WHERE `id`='.(int)$filterID.($this->UrlWL->getFilters()->getFilterType() ? ' AND `tid`='.$this->UrlWL->getFilters()->getFilterType() : ''));
            if(!empty($frow)) {
                $ftype = (int)$frow['tid'];
                // filtering by brand
                if($ftype==UrlFilters::TYPE_BRAND) {
                    $where .= ' AND t.`bid` IN('.  implode(',', $filter).') ';
                }
                // filtering by price range or min/max price
                elseif ($ftype==UrlFilters::TYPE_PRICE) {
                    $ranges = getComplexRowItems(RANGES_TABLE, '*', 'WHERE `fid`='.(int)$filterID.' AND `id` IN('.implode(',', $filter).')');
                    // price range
                    if(!empty($ranges)) {
                        $where .= 'AND (';
                        for ($i = 0; $i < count($ranges); $i++) {
                            $where .= 'IF(t.isdiscount=1 AND t.discount>0, (t.`price`-t.`price`*t.`discount`/100), t.`price`)';
                            if($ranges[$i]['vmin'] > 0 AND $ranges[$i]['vmax'] > 0) {
                                $where .= ' BETWEEN '.$ranges[$i]['vmin'].' AND '.$ranges[$i]['vmax'].' ';
                            } else if ($ranges[$i]['vmax'] > 0) {
                                $where .= '<'.$ranges[$i]['vmax'].' ';
                            } else {
                                $where .= '>'.floatval($ranges[$i]['vmin']).' ';
                            }
                            if($i < (count($ranges) - 1)) {
                                $where .= 'OR ';
                            }
                        }
                        $where .= ') ';
                    } 
                    // min/max
                    else {
                        if(!$excludePrice) {
                            $where .= 'AND IF(t.isdiscount=1 AND t.discount>0, (t.`price`-t.`price`*t.`discount`/100), t.`price`)';
                            if(!empty($filter[UrlFiltersRange::KEY_MIN]) AND !empty($filter[UrlFiltersRange::KEY_MAX])) {
                                $where .= ' BETWEEN "'.floatval($filter[UrlFiltersRange::KEY_MIN]).'" AND "'.floatval($filter[UrlFiltersRange::KEY_MAX]).'" ';
                            } else if (!empty($filter[UrlFiltersRange::KEY_MAX])) {
                                $where .= '<'.floatval($filter[UrlFiltersRange::KEY_MAX]).' ';
                            } else {
                                $where .= '>'.floatval($filter[UrlFiltersRange::KEY_MIN]).' ';
                            }
                        }
                    }
                }
                // filtering by attributes (simple)
                elseif ($ftype==UrlFilters::TYPE_TEXT AND !empty($frow['aid'])) {
                    $where .= 'AND t.`id` IN(SELECT pa.`pid` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa WHERE pa.`aid`='.$frow['aid'].' AND pa.`alias` IN('.implode(',', $filter).')) ';
                }
                // filtering by attributes (range or simple)
                elseif ($ftype==UrlFilters::TYPE_NUMBER AND !empty($frow['aid'])) {
                    $ranges = getComplexRowItems(RANGES_TABLE.' r LEFT JOIN `'.FILTERS_TABLE.'` f ON(f.`id` = r.`fid`) LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON(a.`id` = f.`aid`)', 'r.*', 'WHERE r.`fid`='.(int)$filterID.' AND r.`id` IN('.implode(',', $filter).')');
                    // range mode
                    if(!empty($ranges)) {
                        $where .= 'AND t.`id` IN(SELECT pa.`pid` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa WHERE pa.`aid`='.$frow['aid'].' AND (';
                        for ($i = 0; $i < count($ranges); $i++) {
                            if($ranges[$i]['vmin'] > 0 AND $ranges[$i]['vmax'] > 0) {
                                $where .= '(pa.`value` BETWEEN '.$ranges[$i]['vmin'].' AND '.$ranges[$i]['vmax'].') ';
                            } elseif ($ranges[$i]['vmax'] > 0) {
                                $where .= '(pa.`value`<'.$ranges[$i]['vmax'].') ';
                            } else {
                                $where .= '(pa.`value`>'.floatval($ranges[$i]['vmin']).') ';
                            }
                            if($i < (count($ranges) - 1)) {
                                $where .= 'OR ';
                            }
                        }
                        $where .= ')) ';
                    }
                    // simple mode
                    else {
                        $where .= 'AND t.`id` IN(SELECT pa.`pid` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa WHERE pa.`aid`='.$frow['aid'].' AND pa.`alias` IN('.implode(',', $filter).')) ';
                    }
                }
                // filtering by category
                elseif($ftype==UrlFilters::TYPE_CATEGORY) {
                    $where .= 'AND t.`cid` IN('.  implode(',', $filter).') ';
                }
            }
        }        
        return $where;
    }
    
    public function getFilters($itemsIDX=array(), $itemsFIDX=array()) {
        $arFilters = array();
        $arFilters['childrensCount'] = 0;
       // $arFilters['ni_childrensCount'] = 0;
        
        // filling the filters array        
        $select = 'SELECT f.* FROM `'.FILTERS_TABLE.'` f ';
        $join   = 'LEFT JOIN `'.CATEGORY_FILTERS_TABLE.'` cf ON(cf.`fid` = f.`id` AND cf.`type`=1) ';
//        $join  .= 'LEFT JOIN `'.CATEGORY_FILTERS_TABLE.'` cff ON(cff.`fid` = f.`id` AND cff.`type`=2) ';
        $where  = 'WHERE f.id>0 ';
        $where .= !empty($this->arCatsIDX) ? ' AND cf.`cid` IN ('.implode(',', $this->arCatsIDX).') ' : ' ';
        $where .= ($this->UrlWL->getFilters()->getFilterType()  ? ' AND f.`tid`='.$this->UrlWL->getFilters()->getFilterType().' ' : '');
        $order  = 'GROUP BY f.`id` ORDER BY cf.`order`';
        $query  = $select.$join.$where.$order;
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                // fill children array for each filter
                // important children
                $row['children'] = array();
                // not important children
                // $row['ni_children'] = array();
                
                // for brand filter type
                if($row['tid']==UrlFilters::TYPE_BRAND) { 
                    $row['type'] = 'brand';
                    $sel  = 'SELECT b.*, COUNT(op.`id`) AS `rel`, COUNT(c.`id`) AS `cnt` FROM `'.BRANDS_TABLE.'` b ';
                    $jn   = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(b.`id` = c.`bid`) ';
                    $jn  .= 'LEFT JOIN `'.ORDER_PRODUCTS_TABLE.'` op ON(op.`pid` = c.`id`) ';
                    /*if(!in_array($row['id'], $selFilterIDX) || count($selFilterIDX)>1) {
                        $whr = 'WHERE b.`active`>0 AND c.`id` IN('.implode(',', (!empty($itemsFIDX))? $itemsFIDX: array(0)).') ';
                    } else {
                        $whr = 'WHERE b.`active`>0 AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    }*/
                    $whr = 'WHERE b.`active`>0 AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    $ord = 'GROUP BY b.`id` ORDER BY b.`title`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(mysql_num_rows($res) > 0) {
                        while ($r = mysql_fetch_assoc($res)) {
                            $this->prepareFilter($r, $row['id'], $r['id']);
                            //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                            //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                            //else $row['children'][$r['id']] = $r;
                            $row['children'][$r['id']] = $r;
                        }
                    }
                }
                // for fixed or range price filter type
                elseif ($row['tid']==UrlFilters::TYPE_PRICE) {
                    $sel = 'SELECT r.*, COUNT(c.`id`) AS `cnt` FROM `'.RANGES_TABLE.'` r, `'.CATALOG_TABLE.'` c ';
                    $whr = 'WHERE (
                            (r.`vmin`>0 OR r.`vmax`>0) 
                            AND
                            (
                                (r.`vmin`=0 AND IF(c.isdiscount=1 AND c.discount>0, (c.`price`-c.`price`*c.`discount`/100), c.`price`) < r.`vmax` )
                                OR 
                                (r.`vmax`=0 AND IF(c.isdiscount=1 AND c.discount>0, (c.`price`-c.`price`*c.`discount`/100), c.`price`) > r.`vmin`)
                                OR 
                                (IF(c.isdiscount=1 AND c.discount>0, (c.`price`-c.`price`*c.`discount`/100), c.`price`) BETWEEN r.`vmin` AND r.`vmax`)
                            )
                        ) AND IF(c.isdiscount=1 AND c.discount>0, (c.`price`-c.`price`*c.`discount`/100), c.`price`)>0 AND r.`fid`='.$row['id'].' ';
                    /*if(!in_array($row['id'], $selFilterIDX) || count($selFilterIDX)>1) {
                        $whr .= 'AND c.`id` IN('.implode(',', (!empty($itemsFIDX))? $itemsFIDX: array(0)).') ';
                    } else {
                        $whr .= 'AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    }*/
                    $whr .= 'AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    $ord = 'GROUP BY r.`id` ORDER BY r.`order`';
                    $qry = $sel.$whr.$ord;
                    $res = mysql_query($qry);

                    // if type is range
                    if(mysql_num_rows($res) > 0) {
                        $row['type'] = 'range';
                        while ($r = mysql_fetch_assoc($res)) {
                            $this->prepareFilter($r, $row['id'], $r['id']);
                            //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                            //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                            //else $row['children'][$r['id']] = $r;
                            $row['children'][$r['id']] = $r;
                        }
                    }
                    // if type is fixed
                    else {   
                        $row['type'] = 'price';
                        $where = self::generateWhereState($this->UrlWL->getFilters()->getSelected(), $this->UrlWL->getFilters()->issetFilter($row['id']));
                        $price = getItemRow(CATALOG_TABLE.' t ', 'IFNULL(MIN(t.`price`),0) as '.UrlFiltersRange::KEY_MIN.', IFNULL(MAX(t.`price`),0) as '.UrlFiltersRange::KEY_MAX, 'WHERE t.`active`>0 AND t.`price`>0 AND t.id IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') '.$where);
                        $r = empty($price) ? array(UrlFiltersRange::KEY_MIN => 0, UrlFiltersRange::KEY_MAX => 0) : $price;
                        $this->prepareFilterRange($r, $row['id']);
                        //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'] = $r;
                        //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'] = $r;
                        //else $row['children'] = $r;
                        $row['children'] = $r;
                    }
                }
                // for attribute filter type
                elseif ($row['tid']==UrlFilters::TYPE_TEXT) {
                    $row['type'] = 'simple';
                    $sel = 'SELECT pa.*, COUNT(c.`id`) AS `cnt` , avt.image FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ';
                    $jn = 'LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON(a.`id` = pa.`aid`) LEFT JOIN `'.CATALOG_TABLE.'` c ON(pa.`pid` = c.`id`) 
                           LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) LEFT JOIN '.ATTRIBUTES_VALUES_TABLE.' avt ON avt.title=pa.value AND avt.aid=a.id ';
                    /*if(!in_array($row['id'], $selFilterIDX) || count($selFilterIDX)>1) {
                        $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsFIDX))? $itemsFIDX: array(0)).') ';
                    } else {
                        $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    }*/
                    $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    $ord = 'GROUP BY pa.`value` ORDER BY a.`order`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(mysql_num_rows($res) > 0) {
                        while ($r = mysql_fetch_assoc($res)) {
                            $this->prepareFilter($r, $row['id'], $r['alias']);
                            //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                            //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                            //else $row['children'][$r['id']] = $r;
                            $row['children'][$r['id']] = $r;
                        }
                    }
                }
                // for attribute filter type
                elseif ($row['tid']==UrlFilters::TYPE_NUMBER) {
                    $sel = 'SELECT r.*, COUNT(c.`id`) AS `cnt`, pa.alias FROM `'.RANGES_TABLE.'` r ';
                    $jn  = 'LEFT JOIN `'.FILTERS_TABLE.'` f ON(f.`id` = r.`fid`) LEFT JOIN `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ON(pa.`aid` = f.`aid`) LEFT JOIN `'.CATALOG_TABLE.'` c ON(c.`id` = pa.`pid`) ';
                    /*if(!in_array($row['id'], $selFilterIDX) || count($selFilterIDX)>1) {
                        $whr = 'WHERE f.`id`='.$row['id'].' AND pa.`pid` IN('.implode(',',  (!empty($itemsFIDX))? $itemsFIDX: array(0)).') ';
                    } else {
                        $whr = 'WHERE f.`id`='.$row['id'].' AND pa.`pid` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    }*/
                    $whr = 'WHERE f.`id`='.$row['id'].' AND pa.`pid` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    $whr .= 'AND ((r.`vmin`>0 OR r.`vmax`>0) AND (
                                    (r.`vmin`=0 AND pa.`value` < r.`vmax` )
                                    OR 
                                    (r.`vmax`=0 AND pa.`value` > r.`vmin`)
                                    OR 
                                    (pa.`value` BETWEEN r.`vmin` AND r.`vmax`)
                                ) 
                           ) ';
                    $ord = 'GROUP BY r.`id` ORDER BY r.`order`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(mysql_num_rows($res) > 0) {
                        $row['type'] = 'range';
                        while ($r = mysql_fetch_assoc($res)) {
                            $this->prepareFilter($r, $row['id'], $r['alias']);
                            //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                            //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                            //else $row['children'][$r['id']] = $r;
                            $row['children'][$r['id']] = $r;
                        }
                    } else {
                        $row['type'] = 'simple';
                        $sel = 'SELECT pa.*, COUNT(c.`id`) AS `cnt` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ';
                        $jn = 'LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON(a.`id` = pa.`aid`) LEFT JOIN `'.CATALOG_TABLE.'` c ON(pa.`pid` = c.`id`) LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) ';
                        /*if(!in_array($row['id'], $selFilterIDX) || count($selFilterIDX)>1) {
                            $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsFIDX))? $itemsFIDX: array(0)).') ';
                        } else {
                            $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                        }*/
                        $whr = 'WHERE a.`id`='.$row['aid'].' AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                        $ord = 'GROUP BY pa.`value` ORDER BY a.`order`';
                        $qry = $sel.$jn.$whr.$ord;
                        $res = mysql_query($qry);
                        if(mysql_num_rows($res) > 0) {
                            while ($r = mysql_fetch_assoc($res)) {
                                $this->prepareFilter($r, $row['id'], $r['alias']);
                                //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                                //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                                //else $row['children'][$r['id']] = $r;
                                $row['children'][$r['id']] = $r;
                            }
                        }
                    }
                // for category filter type
                } else if ($row['tid'] == UrlFilters::TYPE_CATEGORY) {
                    $row['type'] = 'category';
                    $sel  = 'SELECT m.*, COUNT(op.`id`) AS `rel`, COUNT(c.`id`) AS `cnt` FROM `'.MAIN_TABLE.'` m ';
                    $jn   = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(m.`id` = c.`cid`) ';
                    $jn  .= 'LEFT JOIN `'.ORDER_PRODUCTS_TABLE.'` op ON(op.`pid` = c.`id`) ';
                    $whr  = 'WHERE m.`active`>0 AND m.`module`="catalog" AND m.`pid`='.(int)$this->catid.' AND c.`id` IN('.implode(',', (!empty($itemsIDX))? $itemsIDX: array(0)).') ';
                    $ord  = 'GROUP BY m.`id` ORDER BY m.`title`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(($cnt = mysql_num_rows($res)) > 0) {
                        while ($r = mysql_fetch_assoc($res)) {
                            $this->prepareFilterChildCategory($r, $row['id'], $cnt);
                            //if($row['filters_type']==UrlFilters::SHOW_IMPORTANT ) $row['children'][$r['id']] = $r;
                            //else if ($row['filters_type']==UrlFilters::SHOW_UNIMPORTANT ) $row['ni_children'][$r['id']] = $r;
                            //else $row['children'][$r['id']] = $r;
                            $row['children'][$r['id']] = $r;
                        }
                    }
                }
                
                // if(!empty($row['ni_children'])) $arFilters['ni_childrensCount']++;
                if(!empty($row['children'])) $arFilters['childrensCount']++;
                $arFilters[$row['id']] = $row;
            } 
        }
        return $arFilters;
    }

    public function getFilterCnt($filters, $module = 'catalog', $lang = 'ru') { 
        return PHPHelper::getCatalogItemsCnt($this->arCatsIDX, $lang, $module, $this->generateWhereState($filters));
    }

    /**
     * @param array $row
     * @param int $fid
     * @param mixed $alias int or string
     */
    public function prepareFilter(array &$row, $fid, $alias) {
        // проверяем выбран ли на данный момент этот фильтр
        $row['selected'] = $this->UrlWL->getFilters()->issetAttribute($fid, $alias);
        // копируем фильтры для манипуляции ими
        $Filters = $this->UrlWL->copyFilters();
        // добавляем атрибут по любому даже если он там есть
        if($row['selected']){
            $cnt_sel = $this->getFilterCnt($Filters->getSelected());
            $Filters->removeAttribute($fid, $alias);
            $cnt_less = $this->getFilterCnt($Filters->getSelected());
            $cnt_diff = $cnt_sel > 0 ? $cnt_less - $cnt_sel : 0;
        } else {
            $cnt_less = $this->getFilterCnt($Filters->getSelected());
            $Filters->appendAttribute($fid, $alias);
            $cnt_sel = $this->getFilterCnt($Filters->getSelected());
            $cnt_diff = $cnt_sel > 0 ? $cnt_sel - $cnt_less : 0;
        }
        $row['cnt'] = $cnt_sel;
        $row['cnt_diff'] = $cnt_diff;
        $row['url'] = $this->getUrl($Filters);
        unset($Filters);
    }

    /**
     * @param array $row
     * @param UrlFilters $Filters
     * @param int $cnt
     * @return string
     */
    protected function prepareFilterChildCategory(array &$row, $fid, $cnt) {
        // чистим память чтобы не было засорения копиями
        if($this->_UrlWL !== null){
            unset($this->_UrlWL);
        }
        $this->_UrlWL = $this->UrlWL->copy();
        // получаем фильтры
        $Filters = $this->_UrlWL->getFilters();
        // устанавливаем родительские фильтры
        $Filters->setCategoryFilters($this->getCategoryFilters($this->_UrlWL->getCategoryParentId()));
        // проверяем выбран ли на данный момент этот фильтр
        $row['selected'] = $Filters->issetAttribute($fid, $row['id']);
        // подсчет и другие операции взависимости от выбрн или не выбран
        if($row['selected']){
            $cnt_sel = $this->getFilterCnt($Filters->getSelected());
            $Filters->removeAttribute($fid, $row['id']);
            $cnt_less = $this->getFilterCnt($Filters->getSelected());
            $cnt_diff = $cnt_sel > 0 ? $cnt_less - $cnt_sel : 0;
        } else {
            $cnt_less = $this->getFilterCnt($Filters->getSelected());
            $Filters->appendAttribute($fid, $row['id']);
            $cnt_sel = $this->getFilterCnt($Filters->getSelected());
            $cnt_diff = $cnt_sel > 0 ? $cnt_sel - $cnt_less : 0;
            $categoryFilters = $Filters->getCategoryFilters();
            if($cnt==1 && !array_key_exists($fid, $categoryFilters)){
                // добавляем текущую категорию в путь
                $this->_UrlWL->addToPath($row['seo_path']);
                $Filters->removeAttribute($fid, $row['id']);
            } elseif($cnt>1 && isset($categoryFilters[$fid])){
                unset($categoryFilters[$fid]);
                $Filters->setCategoryFilters($categoryFilters);
            }
        }
        $row['cnt'] = $cnt_sel;
        $row['cnt_diff'] = $cnt_diff;
        $row['url'] = $this->getUrl($Filters, false);
        unset($Filters);
    } 

    /**
     * @param array $row
     * @param int $fid
     */
    public function prepareFilterRange(array &$row, $fid) {
        $row['selected'] = array (
            UrlFiltersRange::KEY_MIN => $this->UrlWL->getFilters()->getAttribute($fid, UrlFiltersRange::KEY_MIN, 0),
            UrlFiltersRange::KEY_MAX => $this->UrlWL->getFilters()->getAttribute($fid, UrlFiltersRange::KEY_MAX, 0),
        );
        $Filters = $this->UrlWL->copyFilters()
            ->appendAttribute($fid, $row['selected'][UrlFiltersRange::KEY_MIN], UrlFiltersRange::KEY_MIN)
            ->appendAttribute($fid, $row['selected'][UrlFiltersRange::KEY_MAX], UrlFiltersRange::KEY_MAX)
        ;
        $row['cnt'] = $this->getFilterCnt($Filters->getSelected());
        $row['url'] = $this->getUrl($Filters->setMaskRangesOn());
        $row['masks'] = json_encode(array(
            UrlFiltersRange::KEY_MIN => UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MIN),
            UrlFiltersRange::KEY_MAX => UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX),
            UrlFiltersRange::KEY_SEP_MAX => UrlFiltersRange::generateMaxPart(UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX)),
        ));
        $Filters->removeFilter($fid);
        $row['selected']['url'] = $this->getUrl($Filters);
        unset($Filters);
    }
    
    /**
     * @param int $categoryID
     * @return array
     */
    private function getCategoryFilters($categoryID) {
        if(!isset($this->_categoriesFilters[$categoryID])){
            $this->_categoriesFilters[$categoryID] = UrlWL::getCategoryFilters($categoryID, UrlFilters::LIST_TYPE_SEO);
        }
        return $this->_categoriesFilters[$categoryID];
    }
    
    /**
     * @param UrlFilters $Filters
     * @param bool $reCopyUrlWL
     * @return string
     */
    public function getUrl(UrlFilters $Filters=null, $reCopyUrlWL=true) {
        if($reCopyUrlWL || $this->_UrlWL === null){
            // чистим память чтобы не было засорения копиями
            if($this->_UrlWL !== null){
                unset($this->_UrlWL);
            }
            $this->_UrlWL = $this->UrlWL->copy();
        }
        // если переданы фильтры - то устанавливаем их
        if($Filters !== null){
            $this->_UrlWL->setFilters($Filters);
        }
        return $this->_UrlWL->buildUrl();
    }

}
