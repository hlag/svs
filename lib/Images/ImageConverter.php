<?php
/*
 * Created on 13.09.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class ImageConverter
{
	public function resizeJPGwithFixedWidth($jpgFile, $width, $newFileName, $filetype="image/jpeg")
    {
        // Get new dimensions
		list($width_orig, $height_orig,$image_type) = getimagesize($jpgFile);
		//echo $image_type;
		$height =  (int)(($width / $width_orig) * $height_orig);
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		//$image=NULL;
		if ($filetype=="")
			$filetype='image/jpeg';
		switch ($filetype)
		{
			case 'image/jpeg':
				$image = imagecreatefromjpeg($jpgFile);
				break;
			case 'image/gif':
				$image =imagecreatefromgif($jpgFile);
				break;
			case 'image/png':
				$image = imagecreatefrompng($jpgFile);
				break;
		}
		if ($image==NULL)
		{
			$newFilename = date('Y-m-d',time());
			copy($jpgFile,$jpgFile.".err");
		}
		//imagecopyres
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Output
		imagejpeg($image_p, $newFileName, 100);
	}
    
    public function resizeJPGwithFixedHeight($jpgFile, $height, $newFileName, $filetype="image/jpeg")
    {
        // Get new dimensions
        list($width_orig, $height_orig, $image_type) = getimagesize($jpgFile);
        //echo $image_type;
        $width =  (int)(($height / $height_orig) * $width_orig);
        // Resample
        $image_p = imagecreatetruecolor($width, $height);
        //$image=NULL;
        if ($filetype=="")
            $filetype='image/jpeg';
        switch ($filetype)
        {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($jpgFile);
                break;
            case 'image/gif':
                $image =imagecreatefromgif($jpgFile);
                break;
            case 'image/png':
                $image = imagecreatefrompng($jpgFile);
                break;
        }
        if ($image==NULL)
        {
            $newFilename = date('Y-m-d',time());
            copy($jpgFile,$jpgFile.".err");
        }
        //imagecopyres
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        // Output
        imagejpeg($image_p, $newFileName, 100);
    }
}
?>
