<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

if(!defined('BASKET_TABLE')){ 
    define('BASKET_TABLE', 'basket'); 
}

/**
 * Description of Basket class
  Description: This class provides methods for add, remove, update and remove all items
  from a basket. The basket works with a session cookie, saving the product ID
  and quantity in an array for simple accesing it.
  There's an example in this category... called "Basket example using basket class"
 * @author WebLife
 * @copyright 2011
 */
class Basket {
    
    public $kitPrefix         = PRODUCT_KIT_PREFIX;
    public $kitIndicator      = ':';
    public $kitSeparator      = ':';
    public $kitOperator       = ' + ';
    
    public $optionsIndicator  = "|";
    public $optionsSeparator  = "/";
    public $valueSeparator    = "=";
    public $valueIterator     = ",";
    
    private $couponSessionKey = 'coupon';
    
    private $items            = array();
    private $products_count   = 0;
    private $amount           = 0;
    private $price            = 0.0;
    private $customerID       = 0;
    private $orderid          = 0;
    private $basketName       = 'basket';
    private $cookieName       = 'ckBasket';
    private $cookieExpire     = 86400; // One day In seconds
    private $itemsExpire      = 3600; // In seconds (60 min * 60 sec)
    private $bIsEmpty         = true;
    private $bSaveCookie      = true;
    private $bSaveDataBase    = true;
    private $basketItemsName  = 'basket_items';
    private $basketOrderIdName= 'basket_order_id';
    private $basketExpireName = 'basket_items_expire';
    public  $debug            = false;

    /**
     * Basket::__construct()
     *
     * Construct function.
     * @return
     */
    public function __construct($customer_id=0, $save_to_cookie=true, $basket_name='', $cookie_name='') {
        $this->customerID    = intval($customer_id);
        $this->bSaveDataBase = $this->customerID>0 ? true : false;
        $this->bSaveCookie   = $save_to_cookie;

        if(!empty($basketName))
            $this->basketName = $basket_name;
        if(!empty($cookie_name))
            $this->cookieName = $cookie_name;
        
        $this->basketItemsName  = $this->basketName.'_items';
        $this->basketExpireName = $this->basketItemsName.'_expire';

        $this->init();
    }

    /**
     * Basket::__destruct()
     *
     * Destruct function. Set to session basket items.
     * @return
     */
    public function __destruct() {
        if( (time()-$_SESSION[$this->basketExpireName]) > $this->itemsExpire && isset($_SESSION[$this->basketItemsName]) )
            unset($_SESSION[$this->basketItemsName], $_SESSION[$this->basketExpireName]);
        elseif( $this->amount )
            $_SESSION[$this->basketItemsName] = $this->items;
        
        $_SESSION[$this->basketOrderIdName] = $this->orderid;
        
        if($this->debug) {
            echo '<br/>'.$this->basketItemsName.' = ';  @print_r($_SESSION[$this->basketItemsName]);
            echo '<br/>'.$this->basketExpireName.' = '; @print_r($_SESSION[$this->basketExpireName]);
            echo '<br/>'.$this->basketOrderIdName.' = '; @print_r($_SESSION[$this->basketOrderIdName]);
        }
    }

    public function getName() {
        return $this->basketName;
    }

    public function getCookieName() {
        return $this->cookieName;
    }

    public function setCookieExpire($seconds) {
        if(is_int($expire)) $this->cookieExpire = $seconds;
    }

    public function getCookieExpire() {
        return $this->cookieExpire;
    }

    public function setSaveCookie($bool = true) {
        $this->bSaveCookie = $bool;
    }

    public function getSaveCookie() {
        return $this->bSaveCookie;
    }

    public function setItemsExpire($seconds) {
        if(is_int($expire)) $this->itemsExpire = $seconds;
    }

    public function getItemsExpire() {
        return $this->itemsExpire;
    }

    public function setSaveDataBase($customer_id = 0) {
        $this->customerID    = intval($customer_id);
        $this->bSaveDataBase = $this->customerID>0 ? true : false;
        $this->init();
    }

    public function getSaveDataBase() {
        return $this->bSaveDataBase;
    }

    public function isEmptyBasket() {
        return $this->bIsEmpty;
    }

    public function getTotalPrice() {
        return $this->price;
    }

    public function getTotalAmount() {
        return $this->amount;
    }

    public function getProductsCount() {
        return $this->products_count;
    }
    
    public function getItems() {
        return $this->items;
    }

    public function getOrderId() {
        return $this->orderid;
    }

    public function setOrderId($orderid) {
        $this->orderid = intval($orderid);
    }
    
    public function setupKitParams($kitPrefix='Kit') {
        $this->kitPrefix = $kitPrefix;
    }
        /**
     * Basket::get()
     *
     * Returns the basket session as an array of item => qty
     * @return array
     */
    public function get() {
        return isset($_SESSION[$this->basketName]) ? $_SESSION[$this->basketName] : array();
    }

    /**
     * Basket::add()
     *
     * Adds item to basket. If $id already exists in array then qty updated
     * @param mixed $id - ID of item
     * @param integer $qty - Qty of items to be added to cart
     * @return bool
     */
    public function add($id, $qty = 1, $setNewQty=0, $options = array()) {
        if($id == 0) return -1;
        if (isset($_SESSION[$this->basketName][$id]) && !$setNewQty) {
             $_SESSION[$this->basketName][$id] += $qty;
        }
        else {
            $_SESSION[$this->basketName][$id] = $qty;
        }
        $this->setOrderId(0);
        $this->recalc();
        $this->SetCookie();
        $this->SetDB();
    }

    /**
     * Basket::remove()
     *
     * Removes item from basket. If final qty less than 1 then item deleted.
     * @param mixed $id - Id of item
     * @param integer $qty - Qty of items to be removed to cart
     * @see delete()
     * @return bool
     */
    public function remove($id, $qty = 1) {
        if (isset($_SESSION[$this->basketName][$id])) {
            $_SESSION[$this->basketName][$id] = $qty ? $_SESSION[$this->basketName][$id] - $qty : 0;
            
            if ($_SESSION[$this->basketName][$id] <= 0) {
                $this->delete($id);
            }

            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
        }
    }

    /**
     * Basket::updateItem()
     *
     * Updates a basket item with a specific qty
     * @param mixed $id - ID of item
     * @param mixed $qty - Qty of items in basket
     * @return bool
     */
    public function updateItem($id, $qty) {
            $qty = intval($qty);
            if (isset($_SESSION[$this->basketName][$id])) {
            $_SESSION[$this->basketName][$id] = $qty;
            if ($_SESSION[$this->basketName][$id] <= 0)
                    $this->delete($id);
                $this->recalc();
                $this->SetCookie();
                $this->SetDB();
                return true;
            }
        return false;
    }

    /**
     * Basket::update()
     *
     * Updates a basket items
     * @param array $items Contains the array( array($id, $qty), ... )
     * @return bool
     */
    public function update(array $items) {
        if(sizeof($items)>0){
            foreach($items as $id=>$qty){
                if (isset($_SESSION[$this->basketName][$id])) {
                    $_SESSION[$this->basketName][$id] = $qty;
                    if ($_SESSION[$this->basketName][$id] <= 0)
                        $this->delete($id);
                }
            }
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
            return true;
        }
        return false;
    }

    /**
     * Basket::dropBasket()
     *
     * Completely removes the basket from session
     */
    public function dropBasket() {
        if(isset($_SESSION[$this->basketName])){
            unset($_SESSION[$this->basketName]);
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
        }
    }

    /**
     * Basket::isSetKey()
     *
     * Check if key exist in Basket
     * @param mixed $id
     * @param mixed $var_type  - Set if you want to determinate how check BASKET $id variable type
     * @return bool
     */
    public function isSetKey($key, $var_type=false) {
        if (strlen($key)>0 && !empty($_SESSION[$this->basketName])) {
            if($var_type===false) {
                return array_key_exists($key, $_SESSION[$this->basketName]);
            } else {
                foreach ($_SESSION[$this->basketName] as $id=>$qty) {
                    switch($var_type){
                        case 'intval':
                        case 'strval':
                        case 'floatval':
                        case 'doubleval':   if($var_type($id)===$key) return true; break;
                        case 'integer':     if((integer)$id===$key)   return true; break;
                        case 'int':         if((int)$id===$key)       return true; break;
                        case 'string':      if((string)$id===$key)    return true; break;
                        case 'float':       if((float)$id===$key)     return true; break;
                        case 'double':      if((double)$id===$key)    return true; break;
                        case 'boolean':     if((boolean)$id===$key)   return true; break;
                        case 'bool':        if((bool)$id===$key)      return true; break;
                        default:            if($id===$key)            return true; break;
                    }
                }                
            }
        } return false;
    }


    /**
     * Basket::init()
     *
     * Init function. Parses cookie if set and Set to session basket items.
     * @return
     */
    private function init() {
        if (!isset($_SESSION[$this->basketName]) && (isset($_COOKIE[$this->cookieName])))
            $_SESSION[$this->basketName] = unserialize(base64_decode($_COOKIE[$this->cookieName]));

        if (empty($_SESSION[$this->basketName]) && $this->bSaveDataBase)
            $_SESSION[$this->basketName] = $this->getDB();

        if (!isset($_SESSION[$this->basketName]))
            $_SESSION[$this->basketName] = array();

        if (!empty($_SESSION[$this->basketItemsName]))
            $this->items = $_SESSION[$this->basketItemsName];
        
        if (!isset($_SESSION[$this->basketExpireName]))
            $_SESSION[$this->basketExpireName] = time();
        
        $this->setOrderId(isset($_SESSION[$this->basketOrderIdName]) ? $_SESSION[$this->basketOrderIdName] : 0);
        
        $this->recalc();
    }

    /**
     * Basket::recalc()
     *
     * Returns the total amount of items in the basket
     * @return int quantity of items in basket
     */
    private function recalc() {
        $quantity    = 0;
        $products_count = 0;
        $total_price = (float)0;
        $items       = array();
        if (!empty($_SESSION[$this->basketName])) {
            foreach ($_SESSION[$this->basketName] as $id=>$qty) {
              //  if(array_key_exists($id, $this->items)) {
          //          $items[$id] = $this->items[$id];
          //      } else {
                    $items[$id] = $this->getItemRow($id);
            //    }                  
                
                if($items[$id]===false){
                    unset($items[$id]);
                    $this->delete($id);
                    $this->SetCookie();
                    $this->SetDB();
                    continue;
                }
                
                $price = isset($items[$id]['price']) ? (float)$items[$id]['price'] : 0;
                $items[$id]['price']    = $price;
                $items[$id]['amount']   = $price*$qty;
                $items[$id]['quantity'] = $qty;
                
                $total_price += $items[$id]['amount'];
                $quantity    += $qty;
                
                if(!empty($items[$id]['arKits'])) $products_count += $qty*2; // must be count($items[$id]['arKits']) but...
                else $products_count += $qty;
            }
        }
        $this->items    = $items;
        $this->price    = $total_price;
        $this->amount   = $quantity;
        $this->products_count   = $products_count;
        $this->bIsEmpty = !($this->amount>0);
    }

    /**
     * Basket::delete()
     *
     * Completely removes item from basket
     * @param mixed $id
     * @return bool
     */
    private function delete($id) {
        if(isset($_SESSION[$this->basketName][$id])) {
            unset($_SESSION[$this->basketName][$id]);
        }
    }

    /**
     * Basket::SetCookie()
     *
     * Creates cookie of basket items
     * @return bool
     */
    private function SetCookie() {
        if ($this->bSaveCookie) {
            $basket = @$_SESSION[$this->basketName];
            $string = base64_encode(serialize($basket));
            $expire = time() + ($basket ? $this->cookieExpire : -$this->cookieExpire);
            setcookie($this->cookieName, $string, $expire, '/');
            return true;
        }
        return false;
    }

    /**
     * Basket::getDB()
     *
     * get Variables from DataBase
     * @return array
     */
    private function getDB() {
        global $DB;
        $basket = array();
        if ($this->bSaveDataBase) {
            $DB->Query("SELECT * FROM `".BASKET_TABLE."` WHERE `uid`=".$this->customerID);
            while($item = $DB->fetchAssoc()){
                $basket[$item['code']] = $item['quantity'];
            }
        } return $basket;
    }

    /**
     * Basket::SetDB()
     *
     * Creates DB Rows of basket items
     * @return bool
     */
    private function SetDB() {
        global $DB;
        if ($this->bSaveDataBase) {
            $DB->Query("DELETE FROM `".BASKET_TABLE."` WHERE `uid`=".$this->customerID);
            $DB->Query("OPTIMIZE TABLE `".BASKET_TABLE);
            if(!empty($_SESSION[$this->basketName])){
                foreach($_SESSION[$this->basketName] as $code=>$qty){
                    $arFields = array(
                        'uid'   => $this->customerID,
                        'code'     => $code,
                        'quantity' => $qty
                    );
                    $DB->postToDB($arFields, BASKET_TABLE);
                }
            } return true;
        } return false;
    }

    /**
     * Basket::getItemRow()
     *
     * get Item Row For basket From DB : array
     */
    private function getItemRow ($id) {
        global $DB;
        $files_url     = UPLOAD_URL_DIR.'catalog/';
        $files_path    = prepareDirPath($files_url);
               
        if (strlen($id)>0) {
            $idx  = array();
            if(strpos($id, $this->kitSeparator)!==false) {
                $parts = explode($this->kitSeparator, $id);
            } else {
                $parts = array($id);
            }
            foreach ($parts as $part) {
                if (strpos($part, $this->optionsIndicator)!==false) {
                    $arr = $this->separateIdKey($part);//explode($this->optionsIndicator, $part);
                    $partID = intval($arr[0]);
                    $idx[$partID] = array();
                    if (count($arr) > 1 AND !empty($arr[1])) {
                        foreach (explode($this->optionsSeparator, $arr[1]) as $val) {
                            $arr2 = explode($this->valueSeparator, $val);
                            if (count($arr2) > 1) {
                                $idx[$partID][$arr2[0]] = (strpos($arr2[1], $this->valueIterator) ? explode($this->valueIterator, $arr2[1]) : $arr2[1]);
                            }
                        }
                    }
                } else {
                    $idx[intval($part)] = array();
                }
            }
                
            // формирование родительского элемента комплекта
            $id = reset(array_keys($idx)); // ID родителя
            
            $query = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c WHERE c.`id`='.$id.' LIMIT 1';
            $DB->Query($query);
            $item = $DB->fetchAssoc();
            if ($item) {
                $images_url           = $files_url.$item['id'].'/';
                $item['arCategory']   = UrlWL::getCategoryByIdWithSeoPath($item['cid']);
                $item['image']        = getValueFromDB(CATALOGFILES_TABLE." t", 'filename', 'WHERE t.`pid`='.$item['id'].' AND t.`isdefault`=1' );
                $item['middle_image'] = (!empty($item['image']) && is_file(prepareDirPath($images_url).'middle_'.$item['image'])) ? $images_url.'middle_'.$item['image'] : $files_url.'middle_noimage.jpg';
                $item['brand']        = getItemRow(BRANDS_TABLE, '*', 'WHERE `id`='.$item['bid']);
                
                $item["options"] = PHPHelper::getProductOptions($id, $idx[$id]);
                $item["selectedOptions"] = $idx[$id];
                $item["price"]   = PHPHelper::recalcItemPriceByOptions($item["price"],  $item["options"]);
                $item["cprice"]  = PHPHelper::recalcItemPriceByOptions($item["cprice"], $item["options"]);
                
               // $item['old_price'] = ($item['isdiscount'] && $item['discount']) ? ($item['price'] - ($item['price']*$item['discount']/100)) : $item['price'];
                $item['old_price'] =  $item['price'];
                $item['price'] = ($item['isdiscount'] && $item['discount']) ? ($item['price'] - ($item['price']*$item['discount']/100)) : $item['price'];
                if($this->issetCoupon()) {
                    $item['price'] = ($item['cprice']>0) ? $item['cprice'] : $item['price'];
                }
                
                $item['arKits'] = array();
                $arTitle = array();
                
                if (!empty($idx) AND count($idx) > 1) {
                    $item['kit_price'] = $item['price'];
                    foreach ($idx as $kitID=>$opts) {
                        if ($kitID==$id) continue; // skip first product
                        $query = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c WHERE c.`id`='.$kitID;
                        $DB->Query($query);
                        while ($row = $DB->fetchAssoc()) {
                            $row["options"]      = PHPHelper::getProductOptions($row["id"], $opts);
                            $row["selectedOptions"] = $opts;
                            $row["price"]        = PHPHelper::recalcItemPriceByOptions($row["price"],  $row["options"]);
                            $row["cprice"]       = PHPHelper::recalcItemPriceByOptions($row["cprice"], $row["options"]);
                            $item['price']      += $row['cprice'];
                            $row['old_price']    = $row['price'];
                            $row['price']        = ($row['cprice'] > 0) ? $row['cprice'] : $row['price'];   
                            $row['arCategory']   = UrlWL::getCategoryByIdWithSeoPath($row['cid']);
                            $images_url          = $files_url.$row['id'].'/';
                            $row['image']        = getValueFromDB(CATALOGFILES_TABLE." t", 'filename', 'WHERE t.`pid`='.$row['id'].' AND t.`isdefault`=1' );
                            $row['middle_image'] = (!empty($row['image']) && is_file(prepareDirPath($images_url).'middle_'.$row['image'])) ? $images_url.'middle_'.$row['image'] : $files_url.'middle_noimage.jpg';
                            $row['brand']        = getItemRow(BRANDS_TABLE, '*', 'WHERE `id`='.$row['bid']);
                            $item['arKits'][]    = $row;
                            $arTitle[]           = $row['title'];
                        }
                    } 
                    $newTitle = $this->kitPrefix.' '.$item['title'].' '.$this->kitOperator.implode($this->kitOperator, $arTitle);
                    $item['set_title'] = $newTitle;
                }
            }
            return $item;
        } return false;
    }
    
    public function recalcItemPriceByOptions ($price, $options) {
        if (!empty($options)) {
            foreach ($options as $optionID => $option) {
                foreach ($option["values"] as $valueID => $value) {
                    if ($value["operator"]=="+") {
                        $price += $value["price"];
                    } elseif ($value["operator"]=="-") {
                        $price -= $value["price"];
                    }
                }
            }
        }
        return $price;
    }
    
    public function getItemOption ($itemID, $optID, $valID) {
        $option    = array();
        $arValIDX  = array();
        if (is_array($valID)) $arValIDX = $valID;
        else array_push (&$arValIDX, $valID);
        $filesUrl  = UPLOAD_URL_DIR.'options/';
        $filesPath = prepareDirPath($filesUrl);
        $query = 'SELECT o.*, ot.`title` AS `typename` FROM `'.OPTIONS_TABLE.'` o '.PHP_EOL
                .'LEFT JOIN `'.OPTIONS_TYPES_TABLE.'` ot ON(ot.`id` = o.`type_id`) '.PHP_EOL
                .'LEFT JOIN `'.PRODUCT_OPTIONS_TABLE.'` po ON(po.`oid` = o.`id`) '.PHP_EOL
                .'WHERE o.`active`>0 AND po.`pid`='.$itemID.' '.' AND o.`id`='.$optID.' '.PHP_EOL
                .'GROUP BY o.`id` ORDER BY o.`order` LIMIT 1';
        $result = mysql_query($query);
        if ($result AND mysql_num_rows($result) > 0) {
            foreach ($arValIDX as $valID) {
                $option = mysql_fetch_assoc($result);
                $option['descr']  = unScreenData($option['descr']);
                $option['image']  = (!empty($option['image']) AND file_exists($filesPath.$option['image'])) ? $filesUrl.$option['image'] : '';
                $option["values"] = array();
                $qry  = "SELECT ov.*, pov.`operator`, pov.`price`, pov.`primary` FROM `".OPTIONS_VALUES_TABLE."` ov ";
                $qry .= "LEFT JOIN `".PRODUCT_OPTIONS_VALUES_TABLE."` pov ON(pov.`value_id`=ov.`id`) ";
                $qry .= "WHERE pov.`option_id`={$optID} AND pov.`product_id`={$itemID} AND ov.`id`={$valID} ";
                $qry .= "GROUP BY ov.`id` ORDER BY ov.`order` LIMIT 1";
                $res  = mysql_query($qry);
                if ($res AND mysql_num_rows($res) > 0) {
                    while ($value = mysql_fetch_assoc($res)) {
                        $value['image'] = (!empty($value['image']) AND file_exists($filesPath.$value['image'])) ? $filesUrl.$value['image'] : '';
                        $option["values"][$valID] = $value;
                    }
                    $option[$valID] = $option;
                } else $option = null;
            }
        } else $option = null;
        return $option;
    }
    
    public function separateIdKey ($idKey) {
        if (strpos($idKey, $this->optionsIndicator)) return explode($this->optionsIndicator, $idKey);
        elseif (strpos($idKey, $this->optionsSeparator) AND strpos($idKey, $this->optionsIndicator)===false) return explode($this->optionsSeparator, $idKey);
        elseif (strpos($idKey, $this->valueSeparator) AND strpos($idKey, $this->optionsSeparator)===false) return explode($this->valueSeparator, $idKey);
        elseif (strpos($idKey, $this->valueIterator) AND strpos($idKey, $this->valueSeparator)===false) return explode($this->valueIterator, $idKey);
    }
    
    public function setCoupon ($coupon) {
        $res = '';
        if ($coupon && strlen($coupon)>3) {
            $check = getValueFromDB(COUPONS_TABLE, 'coupon', 'WHERE `active`=1 AND `coupon`="'.$coupon.'"');
            if ($check) {
                $_SESSION[$this->couponSessionKey] = $coupon; 
                $res = 'validate';
            } else { 
                unset($_SESSION[$this->couponSessionKey]); 
                $res = 'error';
            }
            $this->recalc();
            $this->SetCookie();
        } else {
            unset($_SESSION[$this->couponSessionKey]);
            $res = 'deleted';
        }
        return $res;
    }
       
    public function issetCoupon(){ 
        if (isset($_SESSION[$this->couponSessionKey]) AND !empty($_SESSION[$this->couponSessionKey])) return true;
        else return false;
    }
    
    public function getCoupon() {
        if ($this->issetCoupon()) {
            return $_SESSION[$this->couponSessionKey];
        } else return false;
    }

}



/*
DROP TABLE IF EXISTS `basket`;
CREATE TABLE IF NOT EXISTS `basket` (
  `uid` int(11) unsigned NOT NULL,
  `code` tinytext NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT '0',
 KEY `idx_uid` (`uid`)
) ENGINE=MyISAM;
 */