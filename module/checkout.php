<?php defined('WEBlife') or die( 'Restricted access' );

$item                       = array();

$arrPageData['result']      = !empty($_GET['result']) ? trim(addslashes($_GET['result'])) : false;
$arrPageData['arPayment']   = getComplexRowItems(PAYMENT_TYPES_TABLE, '*', 'WHERE `active`=1');
$arrPageData['arShipping']  = getComplexRowItems(SHIPPING_TYPES_TABLE, '*', 'WHERE `active`=1', '`order`');

$item['children'] = $Basket->getItems();

if(!empty($_POST) && !empty($item['children'])) {
    
    foreach ($item['children'] as $key => $children) {
        $item['children'][$key]['link'] = $UrlWL->buildItemUrl($children['arCategory'], $children);
    }
    
    $_POST['descr'] = cleanText($_POST['descr']);
    $Validator->validateGeneral($_POST['firstname'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_FIRST_NAME));
    $Validator->validateEmail($_POST['email'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_EMAIL));
    $Validator->validatePhone($_POST['phone'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_TEL));
    
    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>".ORDER_ERROR_INPUT_STRING."</font>".$Validator->getListedErrors();
    } else {
        $arPostData = screenData($_POST);
        
        $arPostData['created']  = date('Y-m-d H:i:s');
        $arPostData['price']    = $Basket->getTotalPrice();
        $arPostData['qty']      = $Basket->getTotalAmount();
        
        $result = $DB->postToDB($arPostData, ORDERS_TABLE);
        if($result && is_int($result)) { 
            $orderID = $result;
            require_once('include/classes/ActionsLog.php');
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getAuthInstance(ActionsLog::SYSTEM_USER, getRealIp())->save(ActionsLog::ACTION_CREATE, 'Создан новый заказ', $key, 'Заказ №'.$orderID, $orderID, 'orders');
            
            $payment  = getItemRow(PAYMENT_TYPES_TABLE, '*', 'WHERE `id`='.(int)$arPostData['payment']);
            $shipping = getItemRow(SHIPPING_TYPES_TABLE, '*', 'WHERE `id`='.(int)$arPostData['shipping']);
            
            // Start Google Conversion 
            $GoogleConversion = new GoogleConversion($orderID, $arPostData['price'], $objSettingsInfo->websiteName, $shipping['price']);
                
            foreach ($item['children'] as $arItem) {
                $arOptions = array();
                $arKitx    = array();
                if (!empty($arItem["options"])) {
                    $arOptions[$arItem["id"]] = PHPHelper::getSelectedOptions($arItem["options"]);
                }
                $data = array(
                    'oid'       => $orderID,
                    'pid'       => $arItem['id'],
                    'title'     => isset($arItem['set_title']) ? $arItem['set_title'] : $arItem['title'],
                    'qty'       => $arItem['quantity'],
                    'price'     => $arItem['price'],
                    'discount'  => $arItem['discount'],
                    'type'      => 'product'
                );
                
                if(!empty($arItem['arKits'])) {
                    $arKitx[] = $arItem['id'];
                    $data['type'] = 'kit';
                    $data['discount'] = 0;
                    $data['pid'] = getValueFromDB(CATALOG_KITS_TABLE, 'id', 'WHERE `pid`='.$arItem['id'].' AND `kid`='.$arItem['arKits'][0]['id'], 'idx');
                    foreach ($arItem['arKits'] as $arKit) {
                        if (!empty($arKit["options"])) {
                            $arOptions[$arKit['id']] = PHPHelper::getSelectedOptions($arKit["options"]);
                        }
                        $arKitx[] = $arKit['id'];
                    }
                }
                
                $data["options"] = serialize($arOptions);
                $data["kitx"]    = !empty($arKitx) ? implode(",", $arKitx) : "";
                
                // Add Google Conversion Item
                $GoogleConversion->addItem(new GoogleConversionItem($GoogleConversion->id, $arItem['pcode'], $arItem['price'], $arItem['quantity'], htmlspecialchars_decode($arItem['title'])));

                $DB->postToDB($data, ORDER_PRODUCTS_TABLE);
            }
            
            // email notifications
            $arData = array_merge($arPostData, $item);
            $arData['oid']      = $orderID;
            $arData['payment']  = $payment;
            $arData['shipping'] = $shipping;
            $arData['price']    = ($arData['price'] + $arData['shipping']['price']);
            $arData['server']   = 'http://'.$_SERVER['HTTP_HOST'];
            
            $smarty->assign('arData', $arData);
            $text    = $smarty->fetch('mail/order_admin.tpl');
            $subject = $objSettingsInfo->websiteName.': '.sprintf(NEW_ORDER_NUMBER, $orderID);
            
            if(sendMail($objSettingsInfo->notifyEmail, $subject, $text, $objSettingsInfo->siteEmail, 'html')){
                $text = $smarty->fetch('mail/order_user.tpl');
                $subject = $objSettingsInfo->websiteName.': '.sprintf(NEW_ORDER_COMPLETED, $orderID);
                sendMail($arData['email'], $subject, $text, $objSettingsInfo->siteEmail, 'html');
                $GoogleConversion->setPurchased(true);
            }
            TrackingEcommerce::save($GoogleConversion, true);
            $Basket->dropBasket();
            setSessionMessage(ORDER_STRING_COMPLETE);
            Redirect($UrlWL->buildCategoryUrl($arCategory, 'result=success'));
        }
    }
} elseif (empty($item['children']) && empty($arrPageData['messages'])) {
    Redirect('/');
}

if(!empty($_POST)) {
    $item = array_merge($item, $_POST);
}

$smarty->assign('item', $item);