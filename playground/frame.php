<?php

/**
 * frame.php
 *
 * dependent on GD graphics library for PHP
 * installation instructions for linux at
 * http://www.cyberciti.biz/faq/ubuntu-linux-install-or-add-php-gd-support-to-apache/

 * @author asu
 * ported from processing files orginally written by Mihai & Gotfried
 */

class Frame {
  private $file_name;
  private $img;
  private static $load_image_funcs;
  private $width;
  private $height;
  private $area;
  
  function __construct($file_name) {
    $this->file_name = $file_name;
    if(is_null(self::$load_image_funcs)) {
    self::$load_image_funcs = array(
      IMAGETYPE_GIF  => function($fn) {
        return imagecreatefromgif($fn);
      },
      IMAGETYPE_JPEG => function($fn) { 
        return imagecreatefromjpeg($fn);
      },
      IMAGETYPE_PNG => function($fn) {
        return imagecreatefrompng($fn);
      },
      IMAGETYPE_WBMP => function($fn) {
        imagecreatefromwbmp($fn);
      },
      IMAGETYPE_XBM => function($fn) {
        imagecreatefromxbm($fn);
      }
   );
   }
  }
  
  // return the average RGB color
  public function getAvgColor() {

    $avg = array(0, 0, 0); 
    $width = $this->width;
    $height = $this->height;
    $area = $this->area;

    // get sum
    for($i = 0; $i < $width; $i++) {
      for($j = 0; $j < $height; $j++) {
        $rgb = imagecolorat($this->img, $i, $j);
        $avg[0] += $rgb >> 16 & 0xFF;
        $avg[1] += $rgb >> 8 & 0xFF;
        $avg[2] += $rgb & 0xFF;
     }
    }

    // compute average
    $avg[0] /= $area;
    $avg[1] /= $area;
    $avg[2] /= $area;

    return $avg;
  }
  
  // return the average perceived brightness
  // based on http://bit.ly/18Xor7G
  public function getAvgPerceivedBrightness() {
    $width = $this->width;
    $height = $this->height;
    $area = $this->area;
    $sum = 0;

    for($i = 0; $i < $width; $i++) {
      for($j = 0; $j < $height; $j++) {
      $rgb = imagecolorat($this->img, $i, $j);
      $sum += (0.2126 * ($rgb >> 16 & 0xFF)) +
              (0.7152 * ($rgb >> 8 & 0xFF)) +
              (0.0722 * ($rgb & 0xFF));
      }
    }
    return $sum / $area;
  }
  
  // return the filename
  public function getFn() {
    return $this->file_name;
  }
  
  // return the RMS contrast
  // based on http://bit.ly/18Xor7G
  public function getRMSContrast() {
    $avg_bright = $this->getAvgPerceivedBrightness();
    $width = $this->width;
    $height = $this->height;
    $sum = 0;

    for($i = 0; $i < $width; $i++) {
      for($j = 0; $j < $height; $j++) {
      $rgb = imagecolorat($this->img, $i, $j);
      $bright = (0.2126 * ($rgb >> 16 & 0xFF)) +
                (0.7152 * ($rgb >> 8 & 0xFF)) +
                (0.0722 * ($rgb & 0xFF));
      $sum += pow(($avg_bright - $bright), 2);
      }
    }
    return $sum / $this->area;
  }
  
  public function load() {
    $fn = $this->file_name;
    $type = exif_imagetype($fn);

    if($type == FALSE) {
       echo "Error: $fn could not be loaded or type could not be determined\n";
       return false;
    }

    // get the function to generate image if type is supported
    // by script
    $func = isset(self::$load_image_funcs[$type]) ? 
    self::$load_image_funcs[$type] : 
    function($fn){
      echo "File type of". $fn . "not supported";
      return false;
    }; 

    $this->img = $func($fn);

    if($this->img == false) {
       return false;
    }

    $this->width = imagesx($this->img);
    $this->height = imagesy($this->img);
    $this->area = $this->width * $this->height;

    return true;

  }
}


