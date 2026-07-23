<?php

include("config.php");
include("functions.php");
include("wlangs.php");
include("sclangs.php");

$freetransapi="http://giss.tv:8077/translate?";

if ( count($argv) != 4 ) {
   error_log("wrong number of arguments to launch free translate api : ".count($argv) );
   error_log("usage : $argv[0] <langin> <langout> <text>");
   exit(-1);
}

// Takes raw data from the request
error_log($freetransapi."sl=".$argv[1]."&dl=".$argv[2]."&text=".urlencode($argv[3]));
$trans = file_get_contents($freetransapi."sl=".$argv[1]."&dl=".$argv[2]."&text=".urlencode($argv[3]));

// Converts it into a PHP object
$trad = json_decode($trans,true);

print $trad["destination-text"]."\n";

?>
