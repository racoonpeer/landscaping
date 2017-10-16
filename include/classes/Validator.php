<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * Description of Validator class
 * @author Andreas. WebLife
 * @edited by Andreas. WebLife. 2013
 * @copyright 2010
 */
class Validator {

    protected $errors = array();

    public function __construct($errors = array()) {
        if($errors){
            if(is_array($errors)){
                $this->addErrors($errors);
            } else if(trim($errors) != ''){
                $this->addError($errors);
            }
        }
    }
    
    public function isNotEmptyString($subject) {
        return (trim($subject) != '');
    }

    public function checkKey($key) {
        return (trim($key) != '' && is_string($key) && !is_numeric($key));
    }

    public function clearErrors() {
        $this->errors = array();
    }

    public function foundErrors() {
        return ($this->errors != array());
    }

    public function hasError($key) {
        return $this->checkKey($key) ? array_key_exists($key, $this->errors) : false;
    }

    public function hasErrors() {
        return $this->foundErrors();
    }

    public function getError($key) {
        return $this->hasError($key) ? $this->errors[$key] : false;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function addError($description, $key = '') {
        if(trim($description) != ''){
            if($this->checkKey($key)){
                $this->errors[$key] = $description;
            } else {
                $this->errors[] = $description;
            }
        }
    }

    public function addErrors(array $errors) {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function listErrors($delim = ' ') {
        return $this->foundErrors() ? implode($delim, $this->errors) : '';
    }
    
    public function getListedErrors($listType='ul') {
        return '<'.$listType.'><li>'.implode('</li><li>',$this->errors).'</li></'.$listType.'>';
    }


    /* =========================  VALIDATORS ================================ */
    public function validateGeneral($subject, $description = '', $key = '') {
        if($this->isNotEmptyString($subject)){
            return true;
        }
        $this->addError($description, $key);
        return false;
    }

    public function validateTextOnly($subject, $description = '', $key = '') {
        $pattern = "/^[A-Za-z0-9\?\!\,\.\;\)\:\(\) ]+$/";
        return $this->validateByPattern($pattern, $subject, $description, $key);
    }

    public function validateTextOnlyNoSpaces($subject, $description = '', $key = '') {
        $pattern = "/^[a-zA-Z0-9]+$/";
        return $this->validateByPattern($pattern, $subject, $description, $key);
    }

    public function validateNumber($subject, $description = '', $key = '', $pattern = false) {
        if($pattern !== false){
            return $this->validateByPattern($pattern, $subject, $description, $key);
        } else if ($this->isNotEmptyString($subject) && is_numeric($subject)){
            return true;
        }
        $this->addError($description, $key);
        return false;
    }

    public function validateDate($subject, $description = '', $key = '', $pattern = false) {
        if($pattern !== false){
            return $this->validateByPattern($pattern, $subject, $description, $key);
        } else if ($this->isNotEmptyString($subject) && strtotime($subject) != false){
            return true;
        }
        $this->addError($description, $key);
        return false;
    }

    public function validatePhone($subject, $description = '', $key = '', $pattern = false, $minln=7, $maxln=17) {
        if($pattern === false)
            $pattern = "/^[0-9 \-]{{$minln},{$maxln}}$/";
        return $this->validateByPattern($pattern, $subject, $description, $key);
    }

    public function validateLogin($subject, $description = '', $key = '', $pattern = false, $minln=3, $maxln=40) {
        if($pattern === false)
            $pattern = "/^[a-z0-9_]{{$minln},{$maxln}}$/";
        return $this->validateByPattern($pattern, $subject, $description, $key);
    }

    public function validateEmail($subject, $description = '', $key = '', $pattern = false) {
        if($pattern === false)
            $pattern = "/^[^@ ]+@[^@ ]+\.[^@ \.]+$/";
        return $this->validateByPattern($pattern, $subject, $description, $key);
    }

    public function validateByPattern($pattern, $subject, $description, $key = '') {
        if($this->isNotEmptyString($pattern)){
            if($this->isNotEmptyString($subject) && preg_match($pattern, $subject)){
                return true;
            }
            $this->addError($description, $key);
            return false;
        }
        throw new Exception('Empty regular expression in var $pattern. Pattern Template like: /^[\d]+/');
    }

}
