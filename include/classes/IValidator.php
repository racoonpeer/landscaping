<?php
/**
 * WEBlife CMS
 * Created on 26.11.2010, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * Description of IValidator class
 * @author WebLife
 * @copyright 2010
 */
class IValidator {

    public $Themes;
    public $Fonts;
    public $Width      = 360;
    public $Height     = 48;
    public $BorderWidth= 1;
    public $ImageType  = 'jpeg';
    public $FontsDir   = '.';
    public $RndCodes   = '0123456789'; //"abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789"
    public $RndLength  = 6;
    public $SPrefix    = 'SS_';

    public function __construct($SPrefix) {
        if(!empty($SPrefix)) $this->SPrefix = $SPrefix.'_';
        $this->Themes = array (
            array(
                'background' => array(248, 248, 248),
                'border' => array(154, 154, 154),
                'font' => array(61,72,54)
            )
        );
        $this->Fonts = array(
            'arial.ttf',
            'verdana.ttf'
        );
    }

    function IValidator($SPrefix) {
        $this->__construct($SPrefix);
    }

    function generateImage($_Code = '') {
        if(!$_Code)
            $_Code = $this->rndCode();

        $this->storeCode($_Code);

        $Theme = mt_rand(0, count($this->Themes)-1);
        $Theme = $this->Themes[$Theme];
        $FontFile = mt_rand(0, count($this->Fonts)-1);
        $FontFile = $this->FontsDir.DIRECTORY_SEPARATOR.$this->Fonts[$FontFile];

        if(function_exists('imagecreatetruecolor'))
            $Image = imagecreatetruecolor($this->Width, $this->Height);
        else
            $Image = imagecreate($this->Width, $this->Height);

        $Fill   = ImageColorAllocate($Image, $Theme['background'][0], $Theme['background'][1], $Theme['background'][2]);
        $Border = ImageColorAllocate($Image, $Theme['border'][0], $Theme['border'][1], $Theme['border'][2]);

        ImageFilledRectangle($Image, $this->BorderWidth, $this->BorderWidth, $this->Width-$this->BorderWidth-1, $this->Height-$this->BorderWidth-1, $Fill);
        ImageRectangle($Image, 0, 0, $this->Width-1, $this->Height-1, $Border);

        $Font	= imagecolorallocate($Image, $Theme['font'][0], $Theme['font'][1], $Theme['font'][2]);

        $TrFontSize = 32;
        $_TC = strlen($_Code)-1;
        $LettersStart = 9;
        $LetterOffset = ceil(($this->Width-$LettersStart*2)/($_TC+1));

        for(;$_TC>=0;$_TC--) {
            $RSize = mt_rand(4, 11);
//	    $RSize = 1;
            imagestring($Image,$RSize,$LettersStart+($_TC)*$LetterOffset, $RSize, $_Code{$_TC}, $Font);
//	    imagettftext($Image, $TrFontSize+$RSize, 0, $LettersStart+($_TC)*$LetterOffset, 25+$RSize*2, $Font, $FontFile, $_Code{$_TC});
        }

        if(0 && function_exists('imagecreatetruecolor')) {
            $TrFont 	= imagecolorallocatealpha($Image, $Theme['font'][0], $Theme['font'][1], $Theme['font'][2], 100);
            $TrFontSize = 20;
            $_TC = strlen($_Code)-1;
            $LetterOffset = ceil($this->Width/($_TC+1));
            for(;$_TC>=0;$_TC--) {
                $RSize = mt_rand(1, 5);
                imagettftext($Image, $TrFontSize+$RSize, 0, ($_TC)*$LetterOffset, 25+$RSize, $TrFont, $FontFile, $_Code{$_TC});
            }
        }

        if($this->ImageType == "jpeg") {
            header("Content-type: image/jpeg");
            imagejpeg($Image, false, 95);
        }else {
            header("Content-type: image/png");
            imagepng($Image);
        }
        imagedestroy($Image);
    }

    function rndCode() {
        $l_name='';
        $top = strlen($this->RndCodes)-1;
        srand((double) microtime()*1000000);
        for($j=0; $j<$this->RndLength; $j++)$l_name .= $this->RndCodes{rand(0,$top)};
        return $l_name;
    }

    function storeCode($_Code) {
        $_SESSION[$this->SPrefix.'IVAL'] = $_Code;
    }

    function checkCode($_Code) {
        if(!isset($_SESSION[$this->SPrefix.'IVAL']) || !$_Code)return false;
        if($_SESSION[$this->SPrefix.'IVAL'] == $_Code) {
            $_SESSION[$this->SPrefix.'IVAL'] = '';
            return true;
        } else {
            $_SESSION[$this->SPrefix.'IVAL'] = '';
            return false;
        }
    }
}
