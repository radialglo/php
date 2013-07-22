#!/usr/bin/env php
<?php

/**
 * resize.php
 *
 * @author Anthony Su
 * resizes image or images in directory and outputs corresponding php
 * requires php GD
 * 
 * Command Line Usage:
 *
 * single image:
 * ./resize.php data/001.png -w 150 -h 160 -d test
 * php resize.php data/001.png -w 150 -h 160 -d test
 *
 * ./resize.php data/001.png 
 * php resize.php data/001.png 
 *
 * image in directories:
 * ./resize.php data -w 150 -h 160 -d test
 * php resize.php data -w 150 -h 160 -d test
 *
 * -w width 
 * -h height
 * -d output directory
 *
 * - width and height are measured in pixels
 * - if no dimensions specified default width 150px, and height is scaled
 * - if height or width is not specified  proportional height/width is selected
 * - output directories are built recursively if the don't exist
 */

if(!isset($argv[1])) {
  exit("Please provide a file or directory name" . PHP_EOL);
}

define('DEFAULT_WIDTH', 150);
$filename = $argv[1];
// directory name of input
$dirname = "./";
$isDirectory = false;

if(is_dir($filename)) {
  $isDirectory = true;
  $dirname = $filename;
  $files = scandir($filename);
  // rm . and ..
  array_shift($files);
  array_shift($files);
} else {
  $dir_idx = strrpos($filename,"/");
  if($dir_idx != false ) {
    $dirname = substr($filename, 0, $dir_idx);
    $filename = substr($filename, $dir_idx + 1); //start 1 after /
  }
  $files = array($filename);
}

if(substr($dirname,-1) != "/") {

  $dirname .= "/";

}

// get new dimensions
// -w width
// -h height
// automatic resizing
// default new width
$new_width;
$new_height;
// default output directory to same directory
$output_dir = $dirname;

// pop off name of this file
// and file to be processed 
array_shift($argv);
array_shift($argv);

// parse rest of command line arguments/options
for($i  = 0; $i < count($argv); $i++) {
   $opt = $argv[$i];

   switch($opt) {

     case "-w":
       $new_width_idx = ++$i; // note that we also skip the next idx since we already store the value
       if(!(isset($argv[$new_width_idx]) && is_numeric($argv[$new_width_idx]) )) {

         exit("Please provide an integer value for the width" . PHP_EOL);
         
       } else {
         $new_width = $argv[$new_width_idx];
       }
     break;

     case "-h":
       $new_height_idx = ++$i; // note that we also skip the next idx
       if(!(isset($argv[$new_height_idx]) && is_numeric($argv[$new_height_idx]) )) {

         exit("Please provide an integer value for the height" . PHP_EOL);
         
       } else {
         $new_height = $argv[$new_height_idx];
       }
     break;

     case "-d":
       $dir_idx = ++$i; // note that we also skip the next idx
       if(!(isset($argv[$dir_idx]))) {

         exit("Please provide name for output directory" . PHP_EOL);
         
       } else {
         $output_dir = $argv[$dir_idx];
         if(substr($output_dir,-1) != "/") {

           $output_dir .= "/";

         }
         // create output directory recursively if it doesn't exists
         if(!is_dir($output_dir)) {
           if(!mkdir($output_dir, 0777,true)) {
             exit("Failed to create directory for $output_dir" . PHP_EOL);
           }
         }
       }
      break;
     default: 
      // ignore other options
      break;
   }
}


$load_image_funcs = array(
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
    return imagecreatefromwbmp($fn);
  },
  IMAGETYPE_XBM => function($fn) {
    return imagecreatefromxbm($fn);
  }
);

echo PHP_EOL . "BEGIN RESIZING" . (($isDirectory) ? $dirname : "$dirname$filename") . PHP_EOL;

foreach($files as $file) {

  $output_filename = $output_dir . $file;
  // add absolute path of file
  $file = $dirname . $file;
  
  // ignore directories
  if(is_dir($file)) {
    continue;
  }
  $type = exif_imagetype($file);

  // check that we are actually processing an image
  if($type == false) {
       echo "Error: $file could not be loaded or type could not be determined\n";
       continue;
  }

  $func = isset($load_image_funcs[$type]) ? 
  $load_image_funcs[$type] : 
  function($fn){
    echo "File type of". $fn . "not supported.PHP";
    return false;
  }; 

  if(($img = $func($file)) == false) {
    continue;
  }

  echo "Processing: $file" . PHP_EOL;
  // get the current dimensions of the image
  list($width, $height) = getimagesize($file);

  // if neither dimensions are set set new width to 150
  if(!(isset($new_height) || isset($new_width))) {
    $new_width = DEFAULT_WIDTH; 
  }
  if(!isset($new_width)) {
    
    $new_width = $width * ($new_height  / $height); 
  }
  if(!isset($new_height)) {
    
    $new_height = $height * ($new_width  / $width); 
  }

  $new_img = imagecreatetruecolor($new_width, $new_height);

  // copy image with interpolation
  imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  
  // get position of last dot note this function is strrpos not strpos
  $dot_idx = strrpos($output_filename, ".");
  $extension = substr($output_filename, $dot_idx);

  // drops new image as png into same directory
  // as source image with _newwidth as a suffix
  imagepng($new_img, substr($output_filename, 0, $dot_idx) 
  . "_" . $new_width . $extension);
}

echo PHP_EOL . "DONE RESIZING" . (($isDirectory) ? $dirname : "$dirname$filename") . PHP_EOL;
