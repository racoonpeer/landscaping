<?php defined('WEBlife') or die('Restricted access');

class Ulogin {
    
    private $DB;
    private $UrlWL;
    
    public function __construct(DbConnector $DB, UrlWL $UrlWL) {
        $this->DB       = $DB;
        $this->UrlWL    = $UrlWL;
    }
    
    public function process ($user) {
        
        $arUser = array(
            'firstname'     => $user['first_name'],
            'firstname_lat' => translitStr($user['first_name']),
            'surname'       => $user['last_name'],
            'surname_lat'   => translitStr($user['last_name']),
            'middlename'    => $user['nickname'],
            'network'       => $user['network'],
            'net_url'       => $user['profile'],
            'bdate'         => !empty($user['bdate'])? date('Y-m-d', strtotime($user['bdate'])): '',
            'photo'         => !empty($user['photo_big'])? $user['photo_big']: $user['photo'],
            'email'         => $user['email'],
            'login'         => $user['email'],
            'salt'          => salt(),
            'password'      => $user['uid'],
            'active'        => 1,
        );
        $arUser['pass'] = md5($arUser['password'].$arUser['salt']);
        
        if ($this->findInDB($arUser)) {
            $arUserInfo = $this->getFromDB($arUser);
            $arUserInfo['password'] = $arUser['password'];
            $this->addToSession($arUserInfo);
        } else {
            $this->addToDB($arUser);
        }
    }
    
    private function addToDB ($user) {
        $user['image'] = $this->processImage($user['photo']);
        $result = $this->DB->postToDB($user, USERS_TABLE);
        if ($result && is_int($result)) {
            $arUserInfo = getItemRow(USERS_TABLE, '*', 'WHERE `id`='.(int)$result);
            $arUserInfo['password'] = $user['password'];
            $this->addToSession($arUserInfo);
        }
    }


    private function addToSession ($arUserInfo) {
        $arUserInfo['logined'] = 1;
        setUserToSession((object)$arUserInfo);
        Redirect($this->UrlWL->getUrl());
    }

    private function findInDB ($user) {
        return (bool)intval(getValueFromDB(USERS_TABLE, 'COUNT(*)', 'WHERE `login`="'.$user['login'].'" AND `pass`=MD5(CONCAT("'.$user['password'].'", `salt`))', 'cnt'));
    }
    
    private function getFromDB ($user) {
        return getItemRow(USERS_TABLE, '*', 'WHERE `login`="'.$user['login'].'" AND `pass`=MD5(CONCAT("'.$user['password'].'", `salt`))');
    }

    private function processImage ($src) {
        $image = WideImage::load($src);
        if ($image->isValid()) {
            $files_url  = UPLOAD_URL_DIR.'users/';
            $files_path = prepareDirPath($files_url);
            $new_name   = createUniqueFileName($files_path, 'jpg', md5(time()).'.jpg');
            $ready      = $image->resize(200, 200, 'outside')->crop('center', 'middle', 200, 200)->saveToFile($files_path.$new_name);
            return file_exists($files_path.$new_name)? $new_name: '';
        } else {
            return '';
        }
    }
    
    private function loadFromHttps($url) {
        /* run mozilla agent */
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $agent); //make it act decent
        curl_setopt($ch, CURLOPT_URL, $url);         //set the $url to where your request goes
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //set this flag for results to the variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //This is required for HTTPS certs if
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //you don't have some key/password action
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        /* execute the request */
        $result = curl_exec($ch);

        if(!curl_errno($ch)) {
            $handle = @imageCreateFromString($result);
            $WideImage = WideImage::loadFromHandle($handle);
        } else {
            $WideImage = WideImage::load($url);
        }
        curl_close($ch);
        return $WideImage;
    }
}
