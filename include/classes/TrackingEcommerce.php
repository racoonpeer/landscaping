<?php

/**
 * Description of TrackingEcommerce class
 * Created on 01.08.2013, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2013
 */
class TrackingEcommerce {

    const SESSION_KEY = 'tracking_purchased';
    const REPLACE_KEY = '/* --Async Conversion Replace String-- */';

    public static function OutputJS($enableEcommerce) {
        $str = '';
        if (self::isPurchased() && $enableEcommerce) {
            $str = self::GetJS();
            self::reset();
        }
        return $str;
    }

    public static function save(EcommerceConversionInterface $Conversion, $async = false) {
        self::SetJS($Conversion, $async);
        self::setPurchased($Conversion->isPurchased());
        //self::SetObject($Conversion);
    }
    
    private static function reset() {
        if (!empty($_SESSION[self::SESSION_KEY])) {
            unset($_SESSION[self::SESSION_KEY]);
        }
    }

    private static function isPurchased() {
        return (!empty($_SESSION[self::SESSION_KEY]['purchased']));
    }

    private static function setPurchased($purchased) {
        $_SESSION[self::SESSION_KEY]['purchased'] = $purchased;
    }

    private static function GetJS() {
        return empty($_SESSION[self::SESSION_KEY]['js']) ? '' : $_SESSION[self::SESSION_KEY]['js'];
    }

    private static function SetJS(EcommerceConversionInterface $Conversion, $async = false) {
        $_SESSION[self::SESSION_KEY]['js'] = $async ? $Conversion->ToAsyncJS() : $Conversion->ToJS();
    }

    private static function isSetObject() {
        return (!empty($_SESSION[self::SESSION_KEY]['object']) && $_SESSION[self::SESSION_KEY]['object'] instanceof EcommerceConversionInterface);
    }

    private static function GetObject() {
        return self::isSetObject() ? $_SESSION[self::SESSION_KEY]['object'] : null;
    }

    private static function SetObject(EcommerceConversionInterface $Conversion) {
        $_SESSION[self::SESSION_KEY]['object'] = $Conversion;
    }

}

interface EcommerceConversionInterface {
    
    public function isPurchased();
    
    public function ToArray();

    public function ToJS();

    public function ToAsyncJS();
    
    public static function GetInstance(array $data);
    
}

interface EcommerceConversionItemInterface {
    
    public function ToArray();

    public function ToJs();

    public function ToAsyncJs();
    
    public static function GetInstance(array $data);
    
}

/**
 * Description of GoogleConversionItem class
 * Created on 01.08.2013, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2013
 */
class GoogleConversion implements EcommerceConversionInterface {

    /**
     * 1234 - Transaction ID. Required. Может быть идентификатором заказа
     * @var mixed 
     */
    public $id;

    /**
     * Acme Clothing - Affiliation or store name.
     * @var string 
     */
    public $affiliation;

    /**
     * 11.99 - Grand Total.
     * @var double 
     */
    public $revenue;

    /**
     * 5 - Shipping.
     * @var double 
     */
    public $shipping;

    /**
     * 1.29 - Tax.
     * @var double 
     */
    public $tax;

    /**
     * San Jose - city.
     * @var string 
     */
    public $city;

    /**
     * California - state or province.
     * @var string 
     */
    public $state;

    /**
     * USA - country.
     * @var string 
     */
    public $country;

    /**
     * EUR - local currency code.
     * @var string 
     */
    public $currencyCode;

    /**
     * GoogleConversionItems array
     * @var bool 
     */
    private $purchased;

    /**
     * GoogleConversionItems array
     * @var array 
     */
    private $items;

    public function __construct($id, $revenue, $affiliation, $shipping = 0, $tax = 0, $currencyCode=null) {
        $this->id = $id;
        $this->revenue = $revenue;
        $this->affiliation = $affiliation;
        $this->shipping = $shipping;
        $this->tax = $tax;
        $this->city = $this->state = $this->country = '';
        $this->currencyCode = $currencyCode;
        $this->purchased = false;
        $this->items = array();
    }

    public function isPurchased() {
        return $this->purchased;
    }

    public function ToArray() {
        $arr = get_object_vars($this);
		$arr['items'] = array();
        foreach($this->items as $item){
            $arr['items'][] = $item->ToArray();
        }
        return $arr;
    }

    public function setPurchased($purchased) {
        $this->purchased = is_bool($purchased) ? $purchased : false;
    }

    public function getItems() {
        return $this->items;
    }

    public function addItem(GoogleConversionItem $item) {
        $this->items[] = $item;
    }

    public function resetItems() {
        $this->items = array();
    }

    public function ItemsToJS($async = false) {
        $str = '';
        foreach ($this->items as $item) {
            $str .= ($async ? $item->ToAsyncJs() : $item->ToJs()) . PHP_EOL;
        }
        return $str;
    }

    /**
     * Function to return the JavaScript representation of a TransactionData object.
     * @return string
     */
    public function ToTransactionJs() {
        return <<<HTML
    ga('ecommerce:addTransaction', {
      id: '{$this->id}',
      affiliation: '{$this->affiliation}',
      revenue: '{$this->revenue}',
      shipping: '{$this->shipping}',
      tax: '{$this->tax}'
    });
HTML;
    }

    /**
     * Function to return the JavaScript representation of a TransactionData object.
     *   _gaq.push(['_addTrans',
     *     '1234',           // transaction ID - required
     *     'Acme Clothing',  // affiliation or store name
     *     '11.99',          // total - required
     *     '1.29',           // tax
     *     '5',              // shipping
     *     'San Jose',       // city
     *     'California',     // state or province
     *     'USA'             // country
     *   ]);
     */
    public function ToTransactionAsyncJs() {
        return <<<HTML
     _gaq.push(['_addTrans', '{$this->id}', '{$this->affiliation}', '{$this->revenue}', '{$this->tax}', '{$this->shipping}', '{$this->city}', '{$this->state}', '{$this->country}']);
HTML;
    }

    /**
     * https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce
     */
    public function ToJS() {
        $str = '';
        $str .= PHP_EOL;
        $str .= '<script type="text/javascript">' . PHP_EOL;
        $str .= '  (function() {' . PHP_EOL;
        $str .= '    ga(\'require\', \'ecommerce\', \'ecommerce.js\');' . PHP_EOL;
        // Transaction Data
        $str .= $this->ToTransactionJs() . PHP_EOL;
        // List of Items Purchased.
        $str .= $this->ItemsToJS() . PHP_EOL;
        // Local Currencies if set
        if ($this->currencyCode) {
            $str .= "    ga('set', 'currencyCode', '{$this->currencyCode}');" . PHP_EOL;
        }
        // Submits transaction
        $str .= '    ga(\'ecommerce:send\');' . PHP_EOL;
        $str .= '  })();' . PHP_EOL;
        $str .= '</script>' . PHP_EOL;
        return $str;
    }

    /**
     * Google Asynchronous Syntax 
     * https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce?hl=ru#localcurrencies
     * 
     * http://support.google.com/analytics/bin/answer.py?hl=ru&answer=2444872
     * Список источников может быть например таким: http://www.web-metrics.ru/category/google-analytics/
     */
    public function ToAsyncJS() {
        $str = '';
        $str .= PHP_EOL;
        // Transaction Data
        $str .= $this->ToTransactionAsyncJS() . PHP_EOL;
        // List of Items Purchased.
        $str .= $this->ItemsToJS(true) . PHP_EOL;
        // Local Currencies
        if ($this->currencyCode) {
            $str .= "    _gaq.push(['_set', 'currencyCode', '{$this->currencyCode}']);" . PHP_EOL;
        }
        // Submits transaction
        $str .= '    _gaq.push([\'_trackTrans\']);' . PHP_EOL;
        return $str;
    }

    public static function GetInstance(array $data) {
        if(!empty($data['id'])){
            $GC = new GoogleConversion($data['id'], 0, '');
            foreach($data as $property=>$value){
                if($property == 'items' && is_array($value)){
                    foreach($value as $itemData){
                        $GC->addItem(GoogleConversionItem::GetInstance($itemData));
                    }
                } else if(property_exists($GC, $property)){
                    $GC->$property = $value;
                }
            }
            return $GC;
        }
        return null;
    }

}

/**
 * Description of GoogleConversionItem class
 * Created on 01.08.2013, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2013
 */
class GoogleConversionItem implements EcommerceConversionItemInterface {

    /**
     * transaction ID - required. Например. идентификатор заказа 1234
     * @var mixed 
     */
    public $id;

    /**
     * SKU/code - required. Например: DD23444
     * @var string 
     */
    public $sku;

    /**
     * unit price - required. Например: 11.99
     * @var double 
     */
    public $price;

    /**
     * quantity - required. Например: 1
     * @var int 
     */
    public $quantity;

    /**
     * product name - . Required. Например: Fluffy Pink Bunnies
     * @var string 
     */
    public $name;

    /**
     * category name  or variation. Например: Party Toys
     * @var string 
     */
    public $category;

    public function __construct($id, $sku, $price, $quantity, $name = '', $category = '') {
        $this->id = $id;
        $this->sku = $sku;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->name = $name;
        $this->category = $category;
    }

    public function ToArray() {
        return get_object_vars($this);
    }

    /**
     * Function to return the JavaScript representation of an ItemData object.
     * @return string
     */
    public function ToJs() {
        return <<<HTML
     ga('ecommerce:addItem', {
        id: '{$this->id}',
        name: '{$this->name}',
        sku: '{$this->sku}',
        category: '{$this->category}',
        price: '{$this->price}',
        quantity: '{$this->quantity}'
     });
HTML;
    }

    /**
     * Function to return the JavaScript representation of an ItemData object.
     *   // add item might be called for every item in the shopping cart
     *   // where your ecommerce engine loops through each item in the cart and
     *   // prints out _addItem for each
     *  _gaq.push(['_addItem',
     *    '1234',           // transaction ID - required
     *    'DD44',           // SKU/code - required
     *    'T-Shirt',        // product name
     *    'Green Medium',   // category or variation
     *    '11.99',          // unit price - required
     *    '1'               // quantity - required
     *  ]);
     */
    public function ToAsyncJs() {
        return <<<HTML
     _gaq.push(['_addItem', '{$this->id}', '{$this->sku}', '{$this->name}', '{$this->category}', '{$this->price}', '{$this->quantity}']);
HTML;
    }
    
    public static function GetInstance(array $data) {
        if(!empty($data['id'])){
            $GCI = new GoogleConversionItem($data['id'], 0, 0, 0);
            foreach($data as $property=>$value){
                if(property_exists($GCI, $property)){
                    $GCI->$property = $value;
                }
            }
            return $GCI;
        }
        return null;
    }

}
