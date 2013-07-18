<?php

/**
 *  frame_analysis.php 
 *
 *  command line usage:
 *  php frame_analysis.php directory_name -o output_name.csv 
 *  Example:
 *  php frame_analysis.php ../one -o o.csv
 *  dependent on GD graphics library for PHP
 *
 *  determines average rgb values, brightness, and contrast of image set
 *  @author asu
 *  ported from processing files orginally written by Mihai & Gotfried
 *
 */

// this needs PHP(5.3+) and GD Graphics Library
// for linux installation sudo apt-get install php5-gd
require_once 'frame.php';

if(!isset($argv[1])) {
  exit("Please provide directory name");
}
$dirname = $argv[1];
$filename = (isset($argv[2]) && $argv[2] == "-o" && isset($argv[3])) ? 
  $filename = $argv[3] : "output.csv";

echo "START ANALYSIS: Writing output to $filename\n\n";

$frames = scandir($dirname);

// get file pointer
$fp = fopen($filename, 'w');

  // set header
  fputcsv($fp,array("file","red","green","blue","brightness","contrast"));

  //process each image
  foreach($frames as $img) {
    
    // ignore . and ..
    if($img == "." || $img == "..") {
       continue;
    }

    echo "processing: $img\n";

    $cur = new Frame("$dirname/" . $img);
    if($cur->load()) {
      // write to the csv file
      $rgb = $cur->getAvgColor();
      fputcsv($fp,
        array(
          $img,
          $rgb[0] ,  $rgb[1] ,  $rgb[2],
          $cur->getAvgPerceivedBrightness(),
          $cur->getRMSContrast()
        ));
    }
  }

echo "\n\nANALYSIS COMPlETE: $filename\n";
fclose($fp);

