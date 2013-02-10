<?php

namespace Prosperia;

use Prosperia\Stor as Stor;

class Thumbnail
{
    const CHUNK_SIZE = 4096;
    
    private $type = null;
    private $content = null;
    private $filename = null;
    
    public function __construct(Stor $stor)
    {
        $this->type = $stor->getType();
        $this->content = $stor->getContent();
        
        $this->filename = "var/temp".generateRandomChars(4, "0123456789");
    }
    
    public function get()
    {
        $width = 100;
        list($width_orig, $height_orig, $type, $attr) = @getimagesizefromstring($this->content);
        $height = ($width / $width_orig) * $height_orig;
        
        $thumbnail = imagecreatetruecolor($width, $height);
        $image = imagecreatefromstring($this->content);
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        
        switch ($this->type)
        {
            case "image/jpeg":
                imagejpeg($thumbnail, $this->filename);
                break;
            case "image/png":
                imagepng($thumbnail, $this->filename);
                break;
            case "image/gif":
                imagegif($thumbnail, $this->filename);
                break;
        }
        
        $handle = fopen($this->filename, "rb");
        $thumbstring = null;
        while (!feof($handle))
        {
            $thumbstring .= fread($handle, self::CHUNK_SIZE);
        }
        
        $thumb = "<img src=\"data:".$this->type.";base64," .
            base64_encode($thumbstring) . "\" />";
        
        fclose($handle);
        imagedestroy($thumbnail);
        unlink($this->filename);
        
        return $thumb;
    }
}
