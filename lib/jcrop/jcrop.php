<?php


class jcropper
{


    private static $instance;

    /**
     *
     * @returns jcropper
     * @return jcropper
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new jcropper();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function getJcropRendered($bild, $x, $y, $w, $h, $actionURL = '')
    {
        $data['bild'] = $bild;
        $data['x1'] = $x;
        $data['y1'] = $y;
        $data['x2'] = $x + $w;
        $data['y2'] = $y + $h;
        $data['h'] = $h;

        $data['w'] = $w;

        $data['ratio'] = $w/$h;
        $data['actionURL'] = $actionURL;
        return $this->parseTemplate($data);

    }

    public function cropImage($tempBild, $dst_w, $dst_h , $zielBild, $src_x,$src_y,$src_w,$src_h)
    {

        $src = PATH.$tempBild;
        $img_r = imagecreatefromjpeg($src);
        $dst_r = ImageCreateTrueColor($dst_w, $dst_h);
        imagecopyresampled($dst_r, $img_r, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_r, $zielBild, 100);
    }



    public function parseTemplate($data)
    {
        $fileContent = file_get_contents(PATH.'lib/jcrop/jcropTemplate.html');
        foreach ($data AS $key => $value)
        {
            if (!is_array($value))
                $fileContent = str_replace('{' . $key . '}', $value, $fileContent);
        }
        return $fileContent;
    }
}
