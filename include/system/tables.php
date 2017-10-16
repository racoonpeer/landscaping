<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

if(!defined('ASSORTMENT_TABLE')){           define('ASSORTMENT_TABLE',          $lang.DBTABLE_LANG_SEP.'assortment'); }
if(!defined('BANNERS_TABLE')){              define('BANNERS_TABLE',             $lang.DBTABLE_LANG_SEP.'banners'); }
if(!defined('CATALOG_TABLE')){              define('CATALOG_TABLE',             $lang.DBTABLE_LANG_SEP.'catalog'); }
if(!defined('CURRENCY_INFO_TABLE')){        define('CURRENCY_INFO_TABLE',       $lang.DBTABLE_LANG_SEP.'currency_info'); }
if(!defined('GALLERY_TABLE')){              define('GALLERY_TABLE',             $lang.DBTABLE_LANG_SEP.'gallery'); }
if(!defined('GALLERYFILES_TABLE')){         define('GALLERYFILES_TABLE',        $lang.DBTABLE_LANG_SEP.'galleryfiles'); }
if(!defined('GALLERY_FEATURES_TABLE')){     define('GALLERY_FEATURES_TABLE',    $lang.DBTABLE_LANG_SEP.'gallery_features'); }
if(!defined('HOMESLIDER_TABLE')){           define('HOMESLIDER_TABLE',          $lang.DBTABLE_LANG_SEP.'homeslider'); }
if(!defined('STAFF_TABLE')){                define('STAFF_TABLE',               $lang.DBTABLE_LANG_SEP.'staff'); }
if(!defined('MAIN_TABLE')){                 define('MAIN_TABLE',                $lang.DBTABLE_LANG_SEP.'main'); }
if(!defined('NEWS_TABLE')){                 define('NEWS_TABLE',                $lang.DBTABLE_LANG_SEP.'news'); }
if(!defined('STOCKS_TABLE')){               define('STOCKS_TABLE',              $lang.DBTABLE_LANG_SEP.'stocks'); }
if(!defined('SETTINGS_TABLE')){             define('SETTINGS_TABLE',            $lang.DBTABLE_LANG_SEP.'settings'); }
if(!defined('VIDEOS_TABLE')){               define('VIDEOS_TABLE',              $lang.DBTABLE_LANG_SEP.'videos'); }
if(!defined('BRANDS_TABLE')){               define('BRANDS_TABLE',              $lang.DBTABLE_LANG_SEP.'brands'); }
if(!defined('BRANDS_DESCR_TABLE')){         define('BRANDS_DESCR_TABLE',        $lang.DBTABLE_LANG_SEP.'brands_description'); }
if(!defined('ATTRIBUTES_TABLE')){           define('ATTRIBUTES_TABLE',          $lang.DBTABLE_LANG_SEP.'attributes'); }
if(!defined('ATTRIBUTE_GROUPS_TABLE')){     define('ATTRIBUTE_GROUPS_TABLE',    $lang.DBTABLE_LANG_SEP.'attribute_groups'); }
if(!defined('ATTRIBUTE_TYPES_TABLE')){      define('ATTRIBUTE_TYPES_TABLE',     $lang.DBTABLE_LANG_SEP.'attribute_types'); }
if(!defined('ATTRIBUTES_VALUES_TABLE')){    define('ATTRIBUTES_VALUES_TABLE',   $lang.DBTABLE_LANG_SEP.'attributes_values'); }
if(!defined('PRODUCT_ATTRIBUTE_TABLE')){    define('PRODUCT_ATTRIBUTE_TABLE',   $lang.DBTABLE_LANG_SEP.'product_attribute'); }
if(!defined('RANGES_TABLE')){               define('RANGES_TABLE',              $lang.DBTABLE_LANG_SEP.'ranges'); }
if(!defined('FILTERS_TABLE')){              define('FILTERS_TABLE',             $lang.DBTABLE_LANG_SEP.'filters'); }
if(!defined('FILTER_TYPES_TABLE')){         define('FILTER_TYPES_TABLE',        $lang.DBTABLE_LANG_SEP.'filter_types'); }
if(!defined('CATALOGFILES_TABLE')){         define('CATALOGFILES_TABLE',        $lang.DBTABLE_LANG_SEP.'catalogfiles'); }
if(!defined('NEWSFILES_TABLE')){            define('NEWSFILES_TABLE',           $lang.DBTABLE_LANG_SEP.'newsfiles'); }
if(!defined('OPTIONS_TABLE')){              define('OPTIONS_TABLE',             $lang.DBTABLE_LANG_SEP.'options'); }
if(!defined('OPTIONS_VALUES_TABLE')){       define('OPTIONS_VALUES_TABLE',      $lang.DBTABLE_LANG_SEP.'options_values'); }

if(!defined('CATEGORY_ATTRIBUTE_GROUPS_TABLE')){
    define('CATEGORY_ATTRIBUTE_GROUPS_TABLE',   'category_attribute_groups');
}
if(!defined('CATEGORY_ATTRIBUTES_TABLE')){
    define('CATEGORY_ATTRIBUTES_TABLE',         'category_attributes');
}
if(!defined('CATEGORY_FILTERS_TABLE')){
    define('CATEGORY_FILTERS_TABLE',            'category_filters');
}
if(!defined('PRODUCT_OPTIONS_VALUES_TABLE')){
    define('PRODUCT_OPTIONS_VALUES_TABLE',      'product_options_values');
}
if(!defined('ORDER_TYPES_TABLE')){          define('ORDER_TYPES_TABLE',         'order_types'); }
if(!defined('ORDER_STATUS_TABLE')){         define('ORDER_STATUS_TABLE',        'order_status'); }
if(!defined('ORDER_PRODUCTS_TABLE')){       define('ORDER_PRODUCTS_TABLE',      'order_products'); }
if(!defined('ORDERS_TABLE')){               define('ORDERS_TABLE',              'orders'); }
if(!defined('PAYMENT_TYPES_TABLE')){        define('PAYMENT_TYPES_TABLE',       'payment_types'); }
if(!defined('SHIPPING_TYPES_TABLE')){       define('SHIPPING_TYPES_TABLE',      'shipping_types'); }
if(!defined('STOCKS_RELATED_TABLE')){       define('STOCKS_RELATED_TABLE',      'stocks_related'); }
if(!defined('CATALOG_RELATED_TABLE')){      define('CATALOG_RELATED_TABLE',     'catalog_related'); }
if(!defined('CATALOG_KITS_TABLE')){         define('CATALOG_KITS_TABLE',        'catalog_kits'); }
if(!defined('COUPONS_TABLE')){              define('COUPONS_TABLE',             'coupons'); }
if(!defined('CURRENCY_TABLE')){             define('CURRENCY_TABLE',            'currency'); }
if(!defined('MENUTYPES_TABLE')){            define('MENUTYPES_TABLE',           'menutypes'); }
if(!defined('PAGETYPES_TABLE')){            define('PAGETYPES_TABLE',           'pagetypes'); }
if(!defined('USERS_TABLE')){                define('USERS_TABLE',               'users'); }
if(!defined('USERFILES_TABLE')){            define('USERFILES_TABLE',           'userfiles'); }
if(!defined('USERTYPES_TABLE')){            define('USERTYPES_TABLE',           'usertypes'); }
if(!defined('PRODUCT_SELECTIONS_TABLE')){   define('PRODUCT_SELECTIONS_TABLE',  'product_selections'); }
if(!defined('COMMENTS_TABLE')){             define('COMMENTS_TABLE',            'comments'); }
if(!defined('PRODUCT_OPTIONS_TABLE')){      define('PRODUCT_OPTIONS_TABLE',     'product_options'); }
if(!defined('IMAGES_PARAMS_TABLE')){        define('IMAGES_PARAMS_TABLE',       'images_params'); }
if(!defined('MODULES_PARAMS_TABLE')){       define('MODULES_PARAMS_TABLE',      'modules_params'); }
if(!defined('USERS_ACCESS_TABLE')){         define('USERS_ACCESS_TABLE',        'users_access');}
if(!defined('SHORTCUTS_TABLE')){            define('SHORTCUTS_TABLE',           'shortcuts');}
if(!defined('OPTIONS_TYPES_TABLE')){        define('OPTIONS_TYPES_TABLE',       'options_types');}