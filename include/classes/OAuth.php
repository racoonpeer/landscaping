<?php defined('WEBlife') or die( 'Restricted access' );

class OAuth {
    
    public $fb_appID     = '538326546243947';
    public $fb_secret    = '6181a511e732e8a440b7d111ccff5c4b';
    public $fb_resultUrl = '';

    public $vk_appID     = '3935151';
    public $vk_secret    = 'NrjiYZYcUJT0wwKV6SEG';
    public $vk_resultUrl = '';
    
    public $tw           = array();
    public $tw_key       = 'jPzCivxf4gh6EaeF63PUyQ';
    public $tw_secret    = '3elSGvQpy2wNE4Ok4hRQilAKMNKQ1n8THenLvC5qnI';
    public $tw_reqTokenUrl = 'https://api.twitter.com/oauth/request_token';
    public $tw_autTokenUrl = 'https://api.twitter.com/oauth/authorize';
    public $tw_accTokenUrl = 'https://api.twitter.com/oauth/access_token';
    public $tw_accountUrl  = 'https://api.twitter.com/1.1/users/show.json';
    public $tw_resultUrl   = '';

    public $redirectUrl  = '';

    public function __construct(){
        if(!empty($this->redirectUrl)) {
            $this->redirectUrl = base64_decode(trim($this->redirectUrl));
        }
        $this->fb_resultUrl = 'http://'.$_SERVER['HTTP_HOST'].'/interactive/ajax.php?zone=site&action=SocialAuth&soc=fb';
        $this->vk_resultUrl = 'http://'.$_SERVER['HTTP_HOST'].'/interactive/ajax.php?zone=site&action=SocialAuth&soc=vk';
        $this->tw_resultUrl = 'http://'.$_SERVER['HTTP_HOST'].'/interactive/ajax.php?zone=site&action=SocialAuth&soc=tw';
    }
    
    private function initTw(){
        // Формируем oauth_nonce
        $this->tw['nonce'] = md5(uniqid(rand(), true));
        // Получаем текущее время в секундах
        $this->tw['timestamp'] = time();
    }

    public function twRequestToken(){
        $this->initTw();
        
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode($this->tw_reqTokenUrl)."&";
        $oauth_base_text .= urlencode("oauth_callback=".urlencode($this->tw_resultUrl)."&");
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->tw_key."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$this->tw['nonce']."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$this->tw['timestamp']."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");
        
        $key = $this->tw_key."&";
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
        
        // Формируем GET-запрос
        $url  = $this->tw_reqTokenUrl;
        $url .= '?oauth_callback='.urlencode($this->tw_resultUrl);
        $url .= '&oauth_consumer_key='.$this->tw_key;
        $url .= '&oauth_nonce='.$this->tw['nonce'];
        $url .= '&oauth_signature='.urlencode($signature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$this->tw['timestamp'];
        $url .= '&oauth_version=1.0';
        
        // Выполняем запрос
        $response = file_get_contents($url);

        // Парсим строку ответа
        parse_str($response, $result);
        
        // Запоминаем в сессию
        $_SESSION['tw_token'] = $this->tw['token'] = $result['oauth_token'];
        $_SESSION['tw_token_secret'] = $this->tw['token_secret'] = $result['oauth_token_secret'];
    }
    
    public function getTwAuthorizeURL() {
        // Формируем GET-запрос
        $url = $this->tw_autTokenUrl;
        $url .= '?oauth_token='.$this->tw['token'];
        
        return $url;
    }
    
    public function twAccessToken($token, $verifier) {
        $this->initTw();
        
        // Токен из ГЕТ-запроса
        $this->tw['token'] = $token;

        // Вспоминаем oauth_token_secret из сессии (см. функцию request_token)
        $this->tw['token_secret'] = $_SESSION['tw_token_secret'];

        // Токен из ГЕТ-запроса
        $this->tw['verifier'] = $verifier;
        
        // ПОРЯДОК ПАРАМЕТРОВ ДОЛЖЕН БЫТЬ ИМЕННО ТАКОЙ!
        // Т.е. сперва oauth_callback -> oauth_consumer_key -> ... -> oauth_version.
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode($this->tw_accTokenUrl)."&";
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->tw_key."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$this->tw['nonce']."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_token=".$this->tw['token']."&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$this->tw['timestamp']."&");
        $oauth_base_text .= urlencode("oauth_verifier=".$this->tw['verifier']."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        // Формируем ключ (Consumer secret + '&' + oauth_token_secret)
        $key = $this->tw_secret."&".$this->tw['token_secret'];

        // Формируем auth_signature
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        // Формируем GET-запрос
        $url  = $this->tw_accTokenUrl;
        $url .= '?oauth_nonce='.$this->tw['nonce'];
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$this->tw['timestamp'];
        $url .= '&oauth_consumer_key='.$this->tw_key;
        $url .= '&oauth_token='.urlencode($this->tw['token']);
        $url .= '&oauth_verifier='.urlencode($this->tw['verifier']);
        $url .= '&oauth_signature='.urlencode($signature);
        $url .= '&oauth_version=1.0';

        // Выполняем запрос
        $response = file_get_contents($url);

        // Парсим результат запроса
        parse_str($response, $result);

        // Получаем идентификатор Твиттер-пользователя из результата запроса
        $this->tw['token'] = $result['oauth_token'];
        $this->tw['token_secret'] = $result['oauth_token_secret'];
        $this->tw['user_id'] = $result['user_id'];
        $this->tw['screen_name'] = $result['screen_name'];
    }
    
    public function getTwUser(){
        $this->initTw();
        
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode($this->tw_accountUrl).'&';
        $oauth_base_text .= urlencode('oauth_consumer_key='.$this->tw_key.'&');
        $oauth_base_text .= urlencode('oauth_nonce='.$this->tw['nonce'].'&');
        $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
        $oauth_base_text .= urlencode('oauth_timestamp='.$this->tw['timestamp']."&");
        $oauth_base_text .= urlencode('oauth_token='.$this->tw['token']."&");
        $oauth_base_text .= urlencode('oauth_version=1.0&');
        $oauth_base_text .= urlencode('screen_name=' . $this->tw['screen_name']);

        $key = $this->tw_secret . '&' . $this->tw['token_secret'];
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        // Формируем GET-запрос
        $url  = $this->tw_accountUrl;
        $url .= '?oauth_consumer_key=' . $this->tw_key;
        $url .= '&oauth_nonce=' . $this->tw['nonce'];
        $url .= '&oauth_signature=' . urlencode($signature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp=' . $this->tw['timestamp'];
        $url .= '&oauth_token=' . urlencode($this->tw['token']);
        $url .= '&oauth_version=1.0';
        $url .= '&screen_name=' . $this->tw['screen_name'];

        // Выполняем запрос
        $response = file_get_contents($url);
        
        // Возвращаем результат
        return $response;
    }

    public function getFBurl(){ 
        return $this->fb_resultUrl; 
    }
    
    public function getVKurl(){ 
        return $this->vk_resultUrl; 
    }
    
    public function getFBtoken($soc){
        return '<script type="text/javascript">
                var href = window.location.href;
                var token = href.substr(href.indexOf("access_token")+13);
                token = token.substr(0, token.indexOf("&"));
                location.href="'.$this->fb_resultUrl.'&token="+token;
                </script>'; 
    }

    public function getSocUser($token, $soc='', $user_id=0){
        $arUser = array();
        if($soc=="fb") {
            $url = "https://graph.facebook.com/me?fields=id,first_name,last_name,email,birthday,location,link,picture.type(large)&access_token=$token";
            $data = json_decode(file_get_contents($url));
            if(!empty($data)) {
                $arUser['fbid']         = (int)$data->id;
                $arUser['active']       = 1;
                $arUser['network']      = 'fb';
                $arUser['network2']     = '';
                $arUser['net_url']      = $data->link;
                $arUser['net2_url']     = '';
                $arUser['login']        = (!empty($data->email))? $data->email: '';
                $arUser['salt']         = salt();
                $arUser['password']     = md5('pass'.$arUser['login']);
                $arUser['pass']         = md5($arUser['password'].$arUser['salt']);
                $arUser['type']         = 'Registered';
                $arUser['created']      = date('Y-m-d H:i:s');
                $arUser['email']        = (!empty($data->email))? $data->email: '';
                $arUser['firstname']    = (!empty($data->first_name))? $data->first_name: '';
                $arUser['surname']      = (!empty($data->last_name))? $data->last_name: '';
                $arUser['bdate']        = (!empty($data->birthday))? substr($data->birthday, 6).'-'.substr($data->birthday, 0, 2).'-'.substr($data->birthday, 3, 2): '';
                $arUser['image']        = (!empty($data->picture))? $data->picture->data->url: '';
                $arUser['city']         = (!empty($data->location))? $data->location->name: '';
                $arUser['phone']        = '';
            }
        } elseif ($soc=="vk") {
            $url    = "https://api.vk.com/method/users.get?uid=$user_id&access_token=$token&fields=bdate,contacts,city,photo_max_orig,screen_name,email&v=5.2";
            $data   = (object)json_decode(file_get_contents($url));
            $data   = $data->response[0];
            
            if(!empty($data)) {
                $arUser['vkid']         = (int)$data->id;
                $arUser['active']       = 1;
                $arUser['network']      = 'vk';
                $arUser['network2']     = '';
                $arUser['net_url']      = (!empty($data->screen_name))? 'http://vk.com/'.$data->screen_name: '';
                $arUser['net2_url']     = '';
                $arUser['login']        = '';
                $arUser['salt']         = salt();
                $arUser['password']     = md5('pass'.$arUser['login']);
                $arUser['pass']         = md5($arUser['password'].$arUser['salt']);
                $arUser['type']         = 'Registered';
                $arUser['created']      = date('Y-m-d H:i:s');
                $arUser['email']        = (isset($data->email) && !empty($data->email))? $data->email: '';
                $arUser['firstname']    = (!empty($data->first_name))? $data->first_name: '';
                $arUser['surname']      = (!empty($data->last_name))? $data->last_name: '';
                $arUser['bdate']        = '';
                $arUser['image']        = (!empty($data->photo_max_orig))? $data->photo_max_orig: '';
                $arUser['city']         = '';
                $arUser['phone']        = '';
            }
        }
        return $arUser;
    }
    
    public function getFakeUser($soc) {
        switch ($soc) {
            case 'fb':
                $arUser['email'] = '';
                $arUser['firstname'] = 'Евлампий';
                $arUser['surname'] = 'Еблаковъ';
                $arUser['bdate'] = '1986-08-28';
                $arUser['image'] = 'http://fbcdn-sphotos-g-a.akamaihd.net/hphotos-ak-frc1/229072_266517660131172_754580631_n.jpg';
                $arUser['city'] = '';
                $arUser['phone'] = '';
                break;
            case 'vk':
                $arUser['email'] = '';
                $arUser['firstname'] = 'Евлампий';
                $arUser['surname'] = 'Еблаковъ';
                $arUser['bdate'] = '1986-08-28';
                $arUser['image'] = 'http://cs403817.vk.me/v403817360/59a3/w6umTDdQ5F4.jpg';
                $arUser['city'] = '';
                $arUser['phone'] = '';
                break;
            
        }
        $arUser['login']    = '';
        $arUser['password'] = md5('');
        return $arUser;
    }

    public function __destruct(){}
    
}