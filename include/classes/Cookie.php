<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * Description of CCookie class
 * @author WebLife
 * @copyright 2010
 */
class CCookie {

    private $cookies = array();

    public function add($name, $value, $expires) {
        $this->cookies[] = array("n" => $name, "v" => $value, "e" => $expires, "o" => "add");
    }

    public function del($name) {
        $this->cookies[] = array("n" => $name, "o" => "del");
    }

    public function process() {
        foreach ($this->cookies as $key => $value) {
            switch ($value["o"]) {
                case "add":
                    setcookie($value["n"], $value["v"], $value["e"], '/');
                    break;
                case "del":
                    setcookie($value["n"], null, time() - 3600, '/');
                    break;
            }
        } $this->cookies = array();
    }

    public function processDirectly() {
        foreach ($this->cookies as $key => $value) {
            switch ($value["o"]) {
                case "add":
                    $_COOKIE[$value["n"]] = $value["v"];
                    break;
                case "del":
                    if(isset($_COOKIE[$value["n"]])) unset($_COOKIE[$value["n"]]);
                    break;
            }
        } $this->cookies = array();
    }

    public function isSetCookie($name) {
        return isset($_COOKIE[$name]);
    }

    public function getCookie($name) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    }

    public function setCookie($name, $value) {
        $_COOKIE[$name] = $value;
    }

}
