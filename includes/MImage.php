<?php

/*
* Developer: Mohammad Sharaf Ali
* Version: 1.0
* Description: Class for basic image manipulation tasks
* Dated: 31-10-2016
*/

namespace MSharaf;

class MImage 
{
    private $image;
    private $imageType;

    public function __construct()
    {
    }

    public function load($filename) 
    {
        $imageInfo = getimagesize($filename);
        $this->imageType = $imageInfo[2];

        if ($this->imageType == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } else if ($this->imageType == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } else if ($this->imageType == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    private function getWidth() 
    {
        return imagesx($this->image);
    }
    
    private function getHeight() 
    {
        return imagesy($this->image);
    }

    public function resize($width, $height) 
    {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $newImage;
    }

    public function resizeToHeight($height) 
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    public function resizeToWidth($width) 
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    public function save($filename, $imageType = IMAGETYPE_JPEG, $compression = 75, $permissions = NULL) {
        if ($imageType == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } else if ($imageType == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } else if ($imageType == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }

        if ($permissions != NULL) {
            chmod($filename, $permissions);
        }
    }

    public function decodeImageBase64($base64Data, $targetFile) 
    {
        $data = substr($base64Data, strpos($base64Data, ',') + 1);
        $decodedData = base64_decode($data);
        
        $fp = fopen($targetFile, 'wb');
        fwrite($fp, $decodedData);
        fclose($fp);
    }

    public function getMimeType($targetFile) 
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $targetFile);
        finfo_close($finfo);

        return $mimeType;
    }

    public function removeFile($targetFile) 
    {
        $path = realpath($targetFile);

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
