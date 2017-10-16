<?php

/**
 * WEBlife CMS
 * Created on 10.10.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


/**
 * Description of HTMLHelper class
 * This class provides methods for help you to change HTML output, new html format 
 * You can add new methods to help you make php data display correct
 * @author WebLife
 * @copyright 2011
 */
class HTMLHelper {

    public function prepareHeadTitle(array $arCategory) {
        return $arCategory['seo_title'];
    }

    public function prepareCategoryImage(array $arCategory, array $arrModules, $filekey='image', $defimage='default.png') {
        $image = $arCategory[$filekey];
        if(!$image && $arCategory['module'] && !empty($arrModules[$arCategory['module']][$filekey]))
            $image = $arrModules[$arCategory['module']][$filekey];
        return MAIN_CATEGORIES_URL_DIR.($image ? $image : $defimage);
    }

    public function prepareItemsToDisplay($maxItems, $UrlWL, $arrModules, $categoryid=false, $pid=false, $module='news', $table=NEWS_TABLE, $order='') {
        if(!$categoryid){
            $select = "SELECT id FROM ".MAIN_TABLE." WHERE module='{$module}' AND `active`=1".((!empty($pid) || @$pid === 0) ? " AND pid = {$pid}" : '')." ORDER BY `order`, title";
            $result = mysql_query($select);
            $catids_str = '';
            $sep = '';
            while($row = mysql_fetch_assoc($result)){
                $catids_str .= $sep.$row['id'];
                $sep = ',';
            }
        } elseif($categoryid > 0) { $catids_str = "$categoryid"; }
        $items = array();
        if(strlen($catids_str) > 0 && isset($arrModules[$module])){
            $select = "SELECT `id`, `cid`, `title`, `descr`, `image`, `seo_path`, `created` FROM $table WHERE cid IN ($catids_str) AND `active` = 1 ORDER by ".(!empty($order) ? $order : "`created` DESC")." LIMIT 0, $maxItems";
            $result = mysql_query($select);
            while($row = mysql_fetch_assoc($result)) {
                $row['arCategory'] = ($row['cid']>0) ? $UrlWL->getCategoryById($row['cid']) : $arrModules[$module];
                $row['title'] = unScreenData($row['title']);
                $row['descr'] = unScreenData($row['descr']);
                $items[] = $row;
            }
        } return $items;
    }

    public function getSliderItems() {
        return PHPHelper::getSliderItems();
    }

    public function dataConv($item, $from = "windows-1251", $to = "utf-8", $translit = false, $bApplyTrim = false) {
        return PHPHelper::dataConv($item, $from, $to, $translit, $bApplyTrim);
    }

    public function mb_dataConv($item, $to = "CP1251", $from = "UTF-8") {
        return PHPHelper::mb_dataConv($item, $to, $from);
    }
    
    public function getLastWatched($UrlWL) {
        return PHPHelper::getLastWatched($UrlWL);
    }

    public function getFilterMenuLevel($UrlWL, $category) {
        $items = array();
        $select = 'SELECT f.* FROM `'.FILTERS_TABLE.'` f ';
        $join = 'LEFT JOIN `'.CATEGORY_FILTERS_TABLE.'` cf ON(cf.`fid` = f.`id`) ';
        $where = 'WHERE cf.`cid`='.$category['id'].' AND cf.`type`=3 ';
        $order = 'GROUP BY cf.`id` ORDER BY cf.`order`';
        $query = mysql_query($select.$join.$where.$order);
        if(mysql_num_rows($query) > 0) {
            while ($row = mysql_fetch_assoc($query)) {
                $row['children'] = array();
                // brand
                if($row['tid'] == 1) {
                    $row['type'] = 'brand';
                    $sel = 'SELECT b.*, COUNT(c.`id`) AS `cnt` FROM `'.BRANDS_TABLE.'` b ';
                    $jn = 'LEFT JOIN `'.CATALOG_TABLE.'` c ON(b.`id` = c.`bid`) ';
                    $whr = 'WHERE c.`cid`='.$category['id'].' AND b.`active`>0 AND c.`active`>0 ';
                    $ord = 'GROUP BY b.`id` ORDER BY b.`order`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(mysql_num_rows($res) > 0) {
                        while ($r = mysql_fetch_assoc($res)) {
                            $r['href'] = $UrlWL->buildCategoryUrl($category, UrlFilters::getNewInstance()->appendAttribute($row['id'], $r['id'], $r['id'])->toUrlParams());
                            $row['children'][$r['id']] = $r;
                        }
                    }
                }
                // range price
                elseif ($row['tid'] == 2) {
                    $sel = 'SELECT r.*, COUNT(c.`id`) AS `cnt` FROM `'.RANGES_TABLE.'` r, `'.CATALOG_TABLE.'` c ';
                    $whr = 'WHERE (
                            (r.`vmin`>0 OR r.`vmax`>0) 
                            AND
                            (
                                (r.`vmin`=0 AND c.`price` < r.`vmax` )
                                OR 
                                (r.`vmax`=0 AND c.`price` > r.`vmin`)
                                OR 
                                (c.`price` BETWEEN r.`vmin` AND r.`vmax`)
                            )
                        ) AND c.`price`>0 AND r.`fid`='.$row['id'].' ';
                    $whr .= 'AND c.`cid`='.$category['id'].' ';
                    $ord = 'GROUP BY r.`id` ORDER BY r.`order`';
                    $qry = $sel.$whr.$ord;
                    $res = mysql_query($qry);
                    // if type is range
                    if(mysql_num_rows($res) > 0) {
                        $row['type'] = 'range';
                        while ($r = mysql_fetch_assoc($res)) {
                            $r['alias'] = $r['id'];
                            $r['href'] = $UrlWL->buildCategoryUrl($category, UrlFilters::getNewInstance()->appendAttribute($row['id'], $r['id'], $r['alias'])->toUrlParams());
                            $row['children'][$r['id']] = $r;
                        }
                    }
                }
                // attribute (simple)
                elseif ($row['tid'] == 3) {
                    $row['type'] = 'simple';
                    $sel = 'SELECT pa.*, COUNT(c.`id`) AS `cnt` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ';
                    $jn = 'LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON(a.`id` = pa.`aid`) LEFT JOIN `'.CATALOG_TABLE.'` c ON(pa.`pid` = c.`id`) LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) ';
                    $whr = 'WHERE c.`active`>0 AND c.`cid`='.$category['id'].' AND a.`id`='.$row['aid'].' AND c.`cid`='.$category['id'].' ';
                    $ord = 'GROUP BY pa.`value` ORDER BY a.`order`';
                    $qry = $sel.$jn.$whr.$ord;
                    $res = mysql_query($qry);
                    if(mysql_num_rows($res) > 0) {
                        while ($r = mysql_fetch_assoc($res)) {
                            $r['href'] = $UrlWL->buildCategoryUrl($category, UrlFilters::getNewInstance()->appendAttribute($row['id'], $r['id'], $r['alias'])->toUrlParams());
                            $r['title'] = trim($r['value']);
                            $row['children'][$r['id']] = $r;
                        }
                    }

                }
                // attribute (simple/range)
                elseif ($row['tid'] == 4) {
                    $sel = 'SELECT r.*, (SELECT COUNT(*) FROM `ru_catalog` s WHERE s.`id` = pa.`pid`) AS `cnt` FROM `'.RANGES_TABLE.'` r ';
                    $jn  = 'LEFT JOIN `'.FILTERS_TABLE.'` f ON(f.`id` = r.`fid`) LEFT JOIN `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ON(pa.`aid` = f.`aid`) ';
                    $whr = 'WHERE f.`id`='.$row['id'].' AND pa.`pid` IN(SELECT `id` FROM `'.CATALOG_TABLE.'` WHERE `cid`='.$category['id'].') ';
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
                            $r['href'] = $UrlWL->buildCategoryUrl($category, UrlFilters::getNewInstance()->appendAttribute($row['id'], $r['id'], $r['alias'])->toUrlParams());
                            $row['children'][$r['id']] = $r;
                        }
                    } else {
                        $row['type'] = 'simple';
                        $sel = 'SELECT pa.*, COUNT(c.`id`) AS `cnt` FROM `'.PRODUCT_ATTRIBUTE_TABLE.'` pa ';
                        $jn = 'LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON(a.`id` = pa.`aid`) LEFT JOIN `'.CATALOG_TABLE.'` c ON(pa.`pid` = c.`id`) LEFT JOIN `'.CATEGORY_ATTRIBUTES_TABLE.'` ca ON(ca.`aid` = a.`id`) ';
                        $whr = 'WHERE c.`active`>0 AND c.`cid`='.$category['id'].' AND a.`id`='.$row['aid'].' ';
                        $ord = 'GROUP BY pa.`value` ORDER BY a.`order`';
                        $qry = $sel.$jn.$whr.$ord;
                        $res = mysql_query($qry);
                        if(mysql_num_rows($res) > 0) {
                            $ikey = 0;
                            while ($r = mysql_fetch_assoc($res)) {
                                $r['href'] = $UrlWL->buildCategoryUrl($category, UrlFilters::getNewInstance()->appendAttribute($row['id'], $r['id'], $r['alias'])->toUrlParams());
                                $row['children'][$r['id']] = $r;
                            }
                        }
                    }
                }
                $items[$row['id']] = $row;
            }
        }

        return $items;
    }

}
