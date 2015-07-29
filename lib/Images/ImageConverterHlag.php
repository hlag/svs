<?php
class ImageConverter
{
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new ImageConverter();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }




    public function convertBild($fileArray, $filename, $outputPath, $height = false, $width = false)
    {

        switch ($fileArray['type'])
        {
            case 'image/jpeg':
                $im = imagecreatefromjpeg($fileArray['tmp_name']);
                $suffix = 'jpg';
                break;
            case 'image/gif':
                $im = imagecreatefromgif($fileArray['tmp_name']);
                $suffix = 'gif';
                break;
            case 'image/png':
                $im = imagecreatefrompng($fileArray['tmp_name']);
                $suffix = 'png';
                break;
            default:
                $im = imagecreatefromjpeg($fileArray['tmp_name']);
                $suffix = 'jpg';
        }

        $res = exif_read_data($fileArray['tmp_name']);
        $rotation = isset($res['Orientation'])?$res['Orientation']:1;

        $dimensions = getimagesize($fileArray['tmp_name']);

        switch($rotation)
        {
            case 1:
                break;
            case 3:
                $im = imagerotate (  $im , -180 , 00000);
                break;
            case 6:
                $kanten = $dimensions[0]>$dimensions[1]?$dimensions[0]:$dimensions[1];
                $leer = imagecreatetruecolor($kanten, $kanten);
                ImageCopy ( $leer, $im , 0, 0, 0 , 0 , $dimensions[0], $dimensions[1]);
                $leer = imagerotate (  $leer , -90 , 00000);
                $im = imagecreatetruecolor($dimensions[1], $dimensions[0]);
                ImageCopy ( $im, $leer , 0, 0, 0+$dimensions[0]-$dimensions[1] , 0 , $dimensions[1], $dimensions[0]);
                $dimensions = array($dimensions[1],$dimensions[0]);
                break;
            case 8:
                $kanten = $dimensions[0]>$dimensions[1]?$dimensions[0]:$dimensions[1];
                $leer = imagecreatetruecolor($kanten, $kanten);
                ImageCopy ( $leer, $im , 0, 0, 0 , 0 , $dimensions[0], $dimensions[1]);
                $leer = imagerotate (  $leer , 90 , 00000);
                $im = imagecreatetruecolor($dimensions[1], $dimensions[0]);
                ImageCopy ( $im, $leer , 0, 0, 0 , 0 , $dimensions[1], $dimensions[0]);
                $dimensions = array($dimensions[1],$dimensions[0]);
                break;
        }
        $target_dimensions = $dimensions;


        if ($width)
        {
            if ($dimensions[0] > $width)
                $faktor_w = $width / $dimensions[0];
            else
                $faktor_w = 1;
            if ($dimensions[1] > $height)
                $faktor_h = $height / $dimensions[1];
            else
                $faktor_h = 1;
            $factor = $faktor_w < $faktor_h ? $faktor_w : $faktor_h;
            $target_dimensions[0] = (int)$dimensions[0] * $factor;
            $target_dimensions[1] = (int)$dimensions[1] * $factor;
        }
        elseif ($height)
        {
            if ($dimensions[1] > $height)
            {
                $factor = $height / $dimensions[1];
                $target_dimensions[0] = (int)$dimensions[0] * $factor;
                $target_dimensions[1] = (int)$dimensions[1] * $factor;
            }
        }

        $result = imagecreatetruecolor($target_dimensions[0], $target_dimensions[1]);
        imagecopyresampled($result, $im, 0, 0, 0, 0, $target_dimensions[0], $target_dimensions[1], $dimensions[0], $dimensions[1]);

        imagejpeg($result, PATH . $outputPath . $filename.'.'.$suffix);

    }

    private function rotate()
    {

    }




}