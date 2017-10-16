<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 13:02:36
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


if(!defined('CAPTCHA_TABLE')){ define('CAPTCHA_TABLE', 'captcha'); }

/**
 * Description of Captcha class
 * @author WebLife
 * @copyright 2011
 */
class Captcha {
    // Captcha Prefix For Session Captcha Key Name Array
    private $prefix     = 'SCP_';
    // If this param is true - captcha data will be stored to database else to SESSION
    private $db_store   = false;
    // Captcha DB Table name to store the captcha data
    private $table      = 'captcha';
    // Ширина генерируемого изображения
    private $imageWidth = 180;
    // Высота генерируемого изображения
    private $imageHeight = 40;
    // Количество символов в капче
    private $codeLength = 5;
    // Пусть к папке со шрифтами
    private $ttfFilesPath = "/fonts";
    //Используемые шрифты. Массив
    private $arTTFFiles = array("elephant.ttf");
    //Минимальный угол отклонения от вертикали (влево)
    private $textAngleFrom = -20;
    // Максимальный угол отклонения от вертикали (вправо)
    private $textAngleTo = 20;
    //Отступ текста слева
    private $textStartX = 7;
    //Минимальное расстояние между началами символов (при отрицательном значении максимальное расстояние наложения символов)
    private $textDistanceFrom = 27;
    //Максимальное расстояние между началами символов (при отрицательном значении минимальное расстояние наложения символов)
    private $textDistanceTo = 32;
    //Размер шрифта
    private $textFontSize = 20;
    // Прозрачность текста в изображении true - включено, false - выключено
    private $bTransparentText = True;
    //Прозрачность текста в процентах от 0 до 100
    private $transparentTextPercent = 10;
    // IN RGB Цвет шрифта
    // array(green array(from, to), red array(from, to), blue array(from, to))
    private $arTextColor = array(array(0, 100), array(0, 100), array(0, 100));
    // IN RGB Цвет фона
    // array(green array(from, to), red array(from, to), blue array(from, to))
    private $arBGColor = array(array(255, 255), array(255, 255), array(255, 255));
    // IN RGB Массив Цвет фона. Случайное значение если есть диапазон
    private $arRealBGColor = false;
    //Количество кругов
    private $numEllipses = 100;
    // IN RGB Цвет кругов
    // array(green array(from, to), red array(from, to), blue array(from, to))
    private $arEllipseColor = array(array(127, 255), array(127, 255), array(127, 255));
    // Количество линий если больше 0, тогда линии устанавлиются
    private $numLines = 20;
    // IN RGB Цвет линий
    // array(green array(from, to), red array(from, to), blue array(from, to))
    private $arLineColor = array(array(110, 250), array(110, 250), array(110, 250));
    // Линии поверх текста = true поверх | false под
    private $bLinesOverText = False;
    //Цвет границы
    private $arBorderColor = array(0, 0, 0);
    // Нелинейные искажения
    private $bWaveTransformation = False;
    //Допустимые символы (рекомендованный набор ABCDEFGHJKLMNPQRSTWXYZ23456789)
    private $arChars = array(
        'A','B','C','D','E','F','G','H','J','K','L','M',
        'N','P','Q','R','S','T','W','X','Y','Z',
        '2','3','4','5','6','7','8','9'
    );//'1','I','O','0','U','V'
    // ссылка на Изображение
    private $image;
    // Сгенерированный код
    private $code;
    // Сгенерированный уникальный идентификатор для хранинея. Используется как ключ
    private $sid;

    // Методы

    // Конструктор.
    public function __construct($SCPrefix='', $db_table='', $db_store=false) {
        if(!empty($SCPrefix)) $this->prefix   = $SCPrefix.'_';
        if(!empty($db_table)) $this->table = $db_table;
        $this->db_store = $db_store;
    }

    /* SET AND GET */

    public function GetSID() {
        return $this->sid;
    }

    public function GetGeneratedSID($length=0, $bUpperCode = True){
        $this->SetCodeLength($length);
        $this->SetCode($bUpperCode);
        return $this->sid;
    }

    public function GetGeneratedCode(){
        return $this->code;
    }
    
    public function SetImageSize($width, $height) {
        $width = IntVal($width);
        $height = IntVal($height);

        if ($width > 0)
            $this->imageWidth = $width;

        if ($height > 0)
            $this->imageHeight = $height;
    }

    public function GetImageSize() {
        return array($this->imageWidth, $this->imageHeight, 'width'=>$this->imageWidth, 'height'=>$this->imageHeight);
    }

    public function SetCodeLength($length) {
        $length = IntVal($length);

        if ($length > 0)
            $this->codeLength = $length;
    }

    public function GetCodeLength() {
        return $this->codeLength;
    }

    public function SetTTFFontsPath($ttfFilesPath) {
        if (strlen($ttfFilesPath) > 0) {
            $filename = trim(str_replace("\\", "/", trim($ttfFilesPath)), "/");
            $filename = rel2abs($_SERVER["DOCUMENT_ROOT"], "/" . $filename);
            if (strlen($filename) > 1 && is_dir($_SERVER["DOCUMENT_ROOT"] . $filename))
                $this->ttfFilesPath = $filename;
        }
    }

    public function GetTTFFontsPath() {
        return $this->ttfFilesPath;
    }

    public function SetTTFFonts($arFonts) {
        if (!is_array($arFonts) || count($arFonts) <= 0)
            $arFonts = array();
        $this->arTTFFiles = $arFonts;
    }

    public function SetTextWriting($angleFrom, $angleTo, $startX, $distanceFrom, $distanceTo, $fontSize) {
        $angleFrom = IntVal($angleFrom);
        $angleTo = IntVal($angleTo);
        $startX = IntVal($startX);
        $distanceFrom = IntVal($distanceFrom);
        $distanceTo = IntVal($distanceTo);
        $fontSize = IntVal($fontSize);

        $this->textAngleFrom = $angleFrom;
        $this->textAngleTo = $angleTo;

        if ($startX > 0)
            $this->textStartX = $startX;

        if ($distanceFrom <> 0)
            $this->textDistanceFrom = $distanceFrom;

        if ($distanceTo <> 0)
            $this->textDistanceTo = $distanceTo;

        if ($fontSize > 0)
            $this->textFontSize = $fontSize;
    }

    public function SetTextTransparent($bTransparentText, $transparentTextPercent = 10) {
        $this->bTransparentText = ($bTransparentText ? True : False);
        $this->transparentTextPercent = IntVal($transparentTextPercent);
    }

    protected function SetColor($arColor) {
        if (!is_array($arColor) || count($arColor) != 3)
            return False;

        $arNewColor = array();
        $bCorrectColor = True;

        for ($i = 0; $i < 3; $i++) {
            if (!is_array($arColor[$i]))
                $arColor[$i] = array($arColor[$i]);

            for ($j = 0; $j < 2; $j++) {
                if ($j > 0) {
                    if (!array_key_exists($j, $arColor[$i]))
                        $arColor[$i][$j] = $arColor[$i][$j - 1];
                }

                $arColor[$i][$j] = IntVal($arColor[$i][$j]);
                if ($arColor[$i][$j] < 0 || $arColor[$i][$j] > 255) {
                    $bCorrectColor = False;
                    break;
                }

                if ($j > 0) {
                    if ($arColor[$i][$j] < $arColor[$i][$j - 1]) {
                        $bCorrectColor = False;
                        break;
                    }
                }

                $arNewColor[$i][$j] = $arColor[$i][$j];

                if ($j > 0)
                    break;
            }
        }

        if ($bCorrectColor)
            return $arNewColor;

        return False;
    }

    public function SetBGColor($arColor) {
        if ( ($arNewColor = $this->SetColor($arColor)) ) {
            $this->arBGColor = $arNewColor;
            $this->arRealBGColor = false;
        }
    }

    // $color_1 = Нижняя граница случайного цвета фона
    // $color_2 = Верхняя граница случайного цвета фона
    public function SetBGColorRGB($color_1, $color_2) {
        if (preg_match("/^[0-9A-Fa-f]{6}$/", $color_1) && preg_match("/^[0-9A-Fa-f]{6}$/", $color_1)) {
            $arColor = array(
                array(hexdec(substr($color_1, 0, 2)), hexdec(substr($color_2, 0, 2))),
                array(hexdec(substr($color_1, 2, 2)), hexdec(substr($color_2, 2, 2))),
                array(hexdec(substr($color_1, 4, 2)), hexdec(substr($color_2, 4, 2))),
            );
            $this->SetBGColor($arColor);
        }
    }

    public function SetTextColor($arColor) {
        if ( ($arNewColor = $this->SetColor($arColor)) )
            $this->arTextColor = $arNewColor;
    }

    // $color_1 = Нижняя граница случайного цвета шрифта
    // $color_2 = Верхняя граница случайного цвета шрифта
    public function SetTextColorRGB($color_1, $color_2) {
        if (preg_match("/^[0-9A-Fa-f]{6}$/", $color_1) && preg_match("/^[0-9A-Fa-f]{6}$/", $color_1)) {
            $arColor = array(
                array(hexdec(substr($color_1, 0, 2)), hexdec(substr($color_2, 0, 2))),
                array(hexdec(substr($color_1, 2, 2)), hexdec(substr($color_2, 2, 2))),
                array(hexdec(substr($color_1, 4, 2)), hexdec(substr($color_2, 4, 2))),
            );
            $this->SetTextColor($arColor);
        }
    }

    public function SetEllipseColor($arColor) {
        if ( ($arNewColor = $this->SetColor($arColor)) )
            $this->arEllipseColor = $arNewColor;
    }

    // $color_1 = Нижняя граница случайного цвета круга
    // $color_2 = Верхняя граница случайного цвета круга
    public function SetEllipseColorRGB($color_1, $color_2) {
        if (preg_match("/^[0-9A-Fa-f]{6}$/", $color_1) && preg_match("/^[0-9A-Fa-f]{6}$/", $color_1)) {
            $arColor = array(
                array(hexdec(substr($color_1, 0, 2)), hexdec(substr($color_2, 0, 2))),
                array(hexdec(substr($color_1, 2, 2)), hexdec(substr($color_2, 2, 2))),
                array(hexdec(substr($color_1, 4, 2)), hexdec(substr($color_2, 4, 2))),
            );
            $this->SetEllipseColor($arColor);
        }
    }

    public function SetLineColor($arColor) {
        if ( ($arNewColor = $this->SetColor($arColor)) )
            $this->arLineColor = $arNewColor;
    }

    // $color_1 = Нижняя граница случайного цвета линии
    // $color_2 = Верхняя граница случайного цвета линии
    public function SetLineColorRGB($color_1, $color_2) {
        if (preg_match("/^[0-9A-Fa-f]{6}$/", $color_1) && preg_match("/^[0-9A-Fa-f]{6}$/", $color_1)) {
            $arColor = array(
                array(hexdec(substr($color_1, 0, 2)), hexdec(substr($color_2, 0, 2))),
                array(hexdec(substr($color_1, 2, 2)), hexdec(substr($color_2, 2, 2))),
                array(hexdec(substr($color_1, 4, 2)), hexdec(substr($color_2, 4, 2))),
            );
            $this->SetLineColor($arColor);
        }
    }

    public function SetBorderColor($arColor) {
        if ( ($arNewColor = $this->SetColor($arColor)) )
            $this->arBorderColor = $arNewColor;
    }

    public function SetBorderColorRGB($color) {
        if (preg_match("/^[0-9A-Fa-f]{6}$/", $color)) {
            $arColor = array(
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)),
                hexdec(substr($color, 4, 2)),
            );
            $this->SetBorderColor($arColor);
        }
    }

    public function SetEllipsesNumber($num) {
        $this->numEllipses = IntVal($num);
    }

    public function SetLinesNumber($num) {
        $this->numLines = IntVal($num);
    }

    public function SetLinesOverText($bLinesOverText) {
        $this->bLinesOverText = ($bLinesOverText ? True : False);
    }

    public function SetCodeChars($arChars) {
        if (is_array($arChars) && count($arChars) > 0)
            $this->arChars = $arChars;
    }

    public function GetCodeChars() {
        return $this->arChars;
    }

    public function SetWaveTransformation($bWaveTransformation) {
        $this->bWaveTransformation = ($bWaveTransformation ? True : False);
    }


    /* UTIL */

    protected function GetColor($arColor) {
        $arResult = array();
        for ($i = 0; $i < count($arColor); $i++) {
            $arResult[$i] = round(rand($arColor[$i][0], $arColor[$i][1]));
        } return $arResult;
    }

    protected function InitImage($width = false, $height = false) {
        if (!$width)  $width  = $this->imageWidth;
        if (!$height) $height = $this->imageHeight;
        $image = imagecreatetruecolor($width, $height);
        if (!$this->arRealBGColor) 
             $this->arRealBGColor = $this->GetColor($this->arBGColor);
        $bgColor = imagecolorallocate($image, $this->arRealBGColor[0], $this->arRealBGColor[1], $this->arRealBGColor[2]);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $bgColor);
        return $image;
    }

    protected function CreateImage() {
        $this->image = $this->InitImage();
        $this->DrawEllipses();

        if (!$this->bLinesOverText)
            $this->DrawLines();

        $this->DrawText();

        if ($this->bLinesOverText)
            $this->DrawLines();

        if ($this->bWaveTransformation) {
            $this->Wave();
        }

        $arBorderColor = $this->GetColor($this->arBorderColor);
        $borderColor = imagecolorallocate($this->image, $arBorderColor[0], $arBorderColor[1], $arBorderColor[2]);
        imageline($this->image, 0, 0, $this->imageWidth - 1, 0, $borderColor);
        imageline($this->image, 0, 0, 0, $this->imageHeight - 1, $borderColor);
        imageline($this->image, $this->imageWidth - 1, 0, $this->imageWidth - 1, $this->imageHeight - 1, $borderColor);
        imageline($this->image, 0, $this->imageHeight - 1, $this->imageWidth - 1, $this->imageHeight - 1, $borderColor);
    }

    protected function CreateImageError($arMsg) {
        $this->image = imagecreate($this->imageWidth, $this->imageHeight);
        $bgColor = imagecolorallocate($this->image, 0, 0, 0);
        $textColor = imagecolorallocate($this->image, 255, 255, 255);

        if (!is_array($arMsg))
            $arMsg = array($arMsg);

        $bTextOut = False;
        $y = 5;
        for ($i = 0; $i < count($arMsg); $i++) {
            if (strlen(Trim($arMsg[$i])) > 0) {
                $bTextOut = True;
                imagestring($this->image, 3, 5, $y, $arMsg[$i], $textColor);
                $y += 15;
            }
        }

        if (!$bTextOut) {
            imagestring($this->image, 3, 5, 5, "Error!", $textColor);
            imagestring($this->image, 3, 5, 20, "Reload the page!", $textColor);
        }
    }

    protected function Wave() {
        $img = $this->image;
        $img2 = $this->InitImage();

        // случайные параметры (можно поэкспериментировать с коэффициентами):
        // частоты
        $rand1 = mt_rand(700000, 1000000) / 15000000;
        $rand2 = mt_rand(700000, 1000000) / 15000000;
        $rand3 = mt_rand(700000, 1000000) / 15000000;
        $rand4 = mt_rand(700000, 1000000) / 15000000;
        // фазы
        $rand5 = mt_rand(0, 3141592) / 1000000;
        $rand6 = mt_rand(0, 3141592) / 1000000;
        $rand7 = mt_rand(0, 3141592) / 1000000;
        $rand8 = mt_rand(0, 3141592) / 1000000;
        // амплитуды
        $rand9 = mt_rand(400, 600) / 500;
        $rand10 = mt_rand(400, 600) / 200;

        $height = $this->imageHeight;
        $height_1 = $height - 1;
        $width = $this->imageWidth;
        $width_1 = $width - 1;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // координаты пикселя-первообраза.
                $sx = $x + ( sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6) ) * $rand9;
                $sy = $y + ( sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8) ) * $rand10;

                // первообраз за пределами изображения
                if ($sx < 0 || $sy < 0 || $sx >= $width_1 || $sy >= $height_1) {
                    $color_xy = $color_y = $color_x = $color = $this->arRealBGColor;
                } else { // цвета основного пикселя и его 3-х соседей для лучшего антиалиасинга
                    $rgb = imagecolorat($img, $sx, $sy);
                    $color_r = ($rgb >> 16) & 0xFF;
                    $color_g = ($rgb >> 8) & 0xFF;
                    $color_b = $rgb & 0xFF;

                    $rgb = imagecolorat($img, $sx + 1, $sy);
                    $color_x_r = ($rgb >> 16) & 0xFF;
                    $color_x_g = ($rgb >> 8) & 0xFF;
                    $color_x_b = $rgb & 0xFF;

                    $rgb = imagecolorat($img, $sx, $sy + 1);
                    $color_y_r = ($rgb >> 16) & 0xFF;
                    $color_y_g = ($rgb >> 8) & 0xFF;
                    $color_y_b = $rgb & 0xFF;

                    $rgb = imagecolorat($img, $sx + 1, $sy + 1);
                    $color_xy_r = ($rgb >> 16) & 0xFF;
                    $color_xy_g = ($rgb >> 8) & 0xFF;
                    $color_xy_b = $rgb & 0xFF;
                }
                // сглаживаем
                $frsx = $sx - floor($sx); //отклонение координат первообраза от целого
                $frsy = $sy - floor($sy);
                $frsx1 = 1 - $frsx;
                $frsy1 = 1 - $frsy;
                // вычисление цвета нового пикселя как пропорции от цвета основного пикселя и его соседей
                $i11 = $frsx1 * $frsy1;
                $i01 = $frsx * $frsy1;
                $i10 = $frsx1 * $frsy;
                $i00 = $frsx * $frsy;
                $red = floor($color_r    * $i11 +
                             $color_x_r  * $i01 +
                             $color_y_r  * $i10 +
                             $color_xy_r * $i00
                );
                $green = floor($color_g    * $i11 +
                               $color_x_g  * $i01 +
                               $color_y_g  * $i10 +
                               $color_xy_g * $i00
                );
                $blue = floor($color_b    * $i11 +
                              $color_x_b  * $i01 +
                              $color_y_b  * $i10 +
                              $color_xy_b * $i00
                );
                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $red, $green, $blue));
            }
        }
        $this->image = $img2;
    }

    protected function DestroyImage() {
        imagedestroy($this->image);
    }

    protected function ShowImage() {
        imagejpeg($this->image);
    }

    protected function DrawText() {
        if ($this->bTransparentText)
            $alpha = floor($this->transparentTextPercent / 100 * 127);

        $yMin = ($this->imageHeight / 2) + ($this->textFontSize / 2) - 2;
        $yMax = ($this->imageHeight / 2) + ($this->textFontSize / 2) + 2;

        $bPrecise = $this->textDistanceFrom < 0 && $this->textDistanceTo < 0;

        if ($bPrecise) {
            //We'll need inversed color to draw on background
            $bg_color_hex = $this->arRealBGColor[0] << 16 | $this->arRealBGColor[1] << 8 | $this->arRealBGColor[2];
            $not_bg_color = array(
                (!$bg_color_hex >> 16) & 0xFF,
                (!$bg_color_hex >> 8) & 0xFF,
                (!$bg_color_hex) & 0xFF
            );
        }

        $arPos = array();

        for ($i = 0; $i < $this->codeLength; $i++) {
            $char = substr($this->code, $i, 1);
            $ttfFile = $_SERVER["DOCUMENT_ROOT"] . $this->ttfFilesPath . "/" . $this->arTTFFiles[rand(1, count($this->arTTFFiles)) - 1];
            $angle = rand($this->textAngleFrom, $this->textAngleTo);

            $bounds = imagettfbbox($this->textFontSize, $angle, $ttfFile, $char);

            $height = max($bounds[1], $bounds[3], $bounds[5], $bounds[7]) - min($bounds[1], $bounds[3], $bounds[5], $bounds[7]);
            $width = max($bounds[0], $bounds[2], $bounds[4], $bounds[6]) - min($bounds[0], $bounds[2], $bounds[4], $bounds[6]);

            $y = $height + rand(0, ($this->imageHeight - $height) * 0.9);

            if ($bPrecise) {
                //Now for precise positioning we need to draw characred and define it's borders
                $img = $this->InitImage($width, $this->imageHeight);
                $tmp = imagecolorallocate($img, $not_bg_color[0], $not_bg_color[1], $not_bg_color[2]);
                $dx = -min($bounds[0], $bounds[2], $bounds[4], $bounds[6]);
                imagettftext($img, $this->textFontSize, $angle, $dx, $y, $tmp, $ttfFile, $char);

                $arLeftBounds = array();
                for ($yy = 0; $yy < $this->imageHeight; $yy++) {
                    $arLeftBounds[$yy] = 0;
                    for ($xx = 0; $xx < $width; $xx++) {
                        $rgb = imagecolorat($img, $xx, $yy);
                        if ($rgb !== $bg_color_hex && $arLeftBounds[$yy] === 0) {
                            $arLeftBounds[$yy] = $xx;
                            break;
                        }
                    }

                    $arRightBounds[$yy] = 0;
                    if ($arLeftBounds[$yy] > 0) {
                        for ($xx = $width; $xx > 0; $xx--) {
                            $rgb = imagecolorat($img, $xx - 1, $yy);
                            if ($rgb !== $bg_color_hex && $arRightBounds[$yy] === 0) {
                                $arRightBounds[$yy] = $xx - 1;
                                break;
                            }
                        }
                    }
                }

                imagedestroy($img);
            } else {
                $arLeftBounds = array();
                $arRightBounds = array();
                $dx = 0;
            }

            if ($i > 0) {
                if ($bPrecise) {
                    $arDX = array();
                    for ($yy = 0; $yy < $this->imageHeight; $yy++) {
                        if ($arPos[$i - 1][6][$yy] > 0 && $arLeftBounds[$yy] > 0)
                            $arDX[$yy] = ($arPos[$i - 1][6][$yy] - $arPos[$i - 1][7]) - ($arLeftBounds[$yy] - $dx);
                        else
                            $arDX[$yy] = $arPos[$i - 1][5][$yy] - $arPos[$i - 1][7];
                    }
                    $x += max($arDX) + (rand($this->textDistanceFrom, $this->textDistanceTo));
                }
                else {
                    $x += rand($this->textDistanceFrom, $this->textDistanceTo);
                }
            } else {
                $x = rand($this->textStartX / 2, $this->textStartX * 2);
            }

            $arPos[$i] = array(
                $angle, //0
                $x, //1
                $y, //2
                $ttfFile, //3
                $char, //4
                $arLeftBounds, //5
                $arRightBounds, //6
                $dx, //7
            );
        }

        foreach ($arPos as $pos) {
            $arTextColor = $this->GetColor($this->arTextColor);
            if ($this->bTransparentText)
                 $color = imagecolorallocatealpha($this->image, $arTextColor[0], $arTextColor[1], $arTextColor[2], $alpha);
            else $color = imagecolorallocate($this->image, $arTextColor[0], $arTextColor[1], $arTextColor[2]);
            $bounds = imagettftext($this->image, $this->textFontSize, $pos[0], $pos[1], $pos[2], $color, $pos[3], $pos[4]);
            $x2 = $pos[1] + ($bounds[2] - $bounds[0]);
        }

        return $x2;
    }

    protected function DrawEllipses() {
        if ($this->numEllipses > 0) {
            for ($i = 0; $i < $this->numEllipses; $i++) {
                $arEllipseColor = $this->GetColor($this->arEllipseColor);
                $color = imagecolorallocate($this->image, $arEllipseColor[0], $arEllipseColor[1], $arEllipseColor[2]);
                imagefilledellipse($this->image, round(rand(0, $this->imageWidth)), round(rand(0, $this->imageHeight)), round(rand(0, $this->imageWidth / 8)), round(rand(0, $this->imageHeight / 2)), $color);
            }
        }
    }

    protected function DrawLines() {
        if ($this->numLines > 0) {
            for ($i = 0; $i < $this->numLines; $i++) {
                $arLineColor = $this->GetColor($this->arLineColor);
                $color = imagecolorallocate($this->image, $arLineColor[0], $arLineColor[1], $arLineColor[2]);
                imageline($this->image, rand(1, $this->imageWidth), rand(1, $this->imageHeight / 2), rand(1, $this->imageWidth), rand($this->imageHeight / 2, $this->imageHeight), $color);
            }
        }
    }


    /* OUTPUT */

    public function Output() {
        header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-Type: image/jpeg");
        $this->CreateImage();
        $this->ShowImage();
        $this->DestroyImage();
    }

    public function OutputError() {
        header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-Type: image/jpeg");

        $numArgs = func_num_args();
        if ($numArgs > 0)
             $arMsg = func_get_arg(0);
        else $arMsg = array();

        $this->CreateImageError($arMsg);
        $this->ShowImage();
        $this->DestroyImage();
    }


    /* CODE */

    public function SetCode($bUpperCode = True) {
        if ($this->db_store)
            return Captcha::SetCaptchaCode();

        $max = count($this->arChars);

        $this->code = "";
        for ($i = 0; $i < $this->codeLength; $i++){
            $codechar = $this->arChars[rand(1, $max) - 1];
            $this->code .= $bUpperCode ? strtoupper($codechar) : $codechar;
        }
        
        $this->sid = time();

        if (!isset($_SESSION[$this->prefix."CAPTCHA"]) || !is_array($_SESSION[$this->prefix."CAPTCHA"]))
            $_SESSION[$this->prefix."CAPTCHA"] = array();

        $_SESSION[$this->prefix."CAPTCHA"][$this->sid] = $this->code;
    }

    public function InitCode($sid) {
        if ($this->db_store)
            return Captcha::InitCaptchaCode($sid);

        if (!isset($_SESSION[$this->prefix."CAPTCHA"]) || !is_array($_SESSION[$this->prefix."CAPTCHA"]) || count($_SESSION[$this->prefix."CAPTCHA"]) <= 0)
            return False;

        if (!array_key_exists($sid, $_SESSION[$this->prefix."CAPTCHA"]))
            return False;

        $this->code = $_SESSION[$this->prefix."CAPTCHA"][$sid];
        $this->sid = $sid;
        $this->codeLength = strlen($this->code);

        return True;
    }

    public function CheckCode($userCode, $sid, $bUpperCode = True, $bDelCode = True) {
        if ($this->db_store)
            return Captcha::CheckCaptchaCode($userCode, $sid, $bUpperCode);

        if (!is_array($_SESSION[$this->prefix."CAPTCHA"]) || count($_SESSION[$this->prefix."CAPTCHA"]) <= 0)
            return False;

        if (!array_key_exists($sid, $_SESSION[$this->prefix."CAPTCHA"]))
            return False;

        if ($bUpperCode)
            $userCode = strtoupper($userCode);

        if ($_SESSION[$this->prefix."CAPTCHA"][$sid] != $userCode)
            return False;

        if($bDelCode)
            unset($_SESSION[$this->prefix."CAPTCHA"][$sid]);

        return True;
    }

    protected function SetCaptchaCode($sid = false) {
        $max = count($this->arChars);

        $this->code = "";
        for ($i = 0; $i < $this->codeLength; $i++)
            $this->code .= $this->arChars[rand(1, $max) - 1];

        $this->sid = $sid === false ? md5(uniqid(microtime())) : $sid;

        Captcha::Add(
                        Array(
                            "code" => $this->code,
                            "id" => $this->sid
                        )
        );
    }

    protected function InitCaptchaCode($sid) {
        global $DB;

        $res = $DB->Query("SELECT `code` FROM `".$this->table."` WHERE `id` = '" . $DB->ForSQL($sid, 32) . "' ");
        if (!$res || !($ar = $DB->fetchAssoc())) {
            $this->SetCaptchaCode($sid);
            $res = $DB->Query("SELECT `code` FROM `".$this->table."` WHERE `id` = '" . $DB->ForSQL($sid, 32) . "' ");
            if (!$res || !($ar = $DB->fetchAssoc()))
                return false;
        }

        $this->code = $ar["code"];
        $this->sid = $sid;
        $this->codeLength = strlen($this->code);

        return true;
    }

    protected function CheckCaptchaCode($userCode, $sid, $bUpperCode = true) {
        global $DB;

        if (strlen($userCode) <= 0 || strlen($sid) <= 0)
            return false;

        if ($bUpperCode)
            $userCode = strtoupper($userCode);

        $res = $DB->Query("SELECT `code` FROM `".$this->table."` WHERE `id` = '" . $DB->ForSQL($sid, 32) . "' ");
        if (!$res || !($ar = $DB->fetchAssoc()))
            return false;

        if ($ar["code"] != $userCode)
            return false;

        Captcha::Delete($sid);

        return true;
    }

    protected function Add($arFields) {
        global $DB;

        if (!is_set($arFields, "code") || strlen($arFields["code"]) <= 0)
            return false;

        if (!is_set($arFields, "id") || strlen($arFields["id"]) <= 0)
            $arFields["id"] = md5(uniqid(microtime()));

        if (!is_set($arFields, "ip") || strlen($arFields["ip"]) <= 0)
            $arFields["ip"] = $_SERVER["REMOTE_ADDR"];

        if (!$DB->postToDB($arFields, $this->table)) return false;

        return $arFields["id"];
    }

    protected function Delete($sid) {
        global $DB;
        if (!$DB->Query("DELETE FROM `".$this->table."` WHERE id='" . $DB->ForSQL($sid) . "' "))
            return false;
        return true;
    }

}

/*
DROP TABLE IF EXISTS `captcha`;
CREATE TABLE IF NOT EXISTS `captcha` (
  `id` varchar(32) NOT NULL,
  `code` varchar(20) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `udx_id` (`id`)
) ENGINE=MyISAM;
 */

