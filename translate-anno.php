<?php

include("config.php");
include("functions.php");
include("wlangs.php");
include("sclangs.php");

if ( empty($_POST['slang']) )
{
   header('HTTP/1.1 406 Source language is Mandatory');
   exit(-1);
}
$slang = $_POST['slang'];

if ( empty($_POST['target']) )
{
   header('HTTP/1.1 406 Target language(s) is(are) Mandatory');
   exit(-1);
}
$target = $_POST['target'];

if ( empty($_POST['source']) )
{
   header('HTTP/1.1 406 Source is Mandatory');
   exit(-1);
}
$source = $_POST['source'];

if ( empty($_POST['order']) )
{
   header('HTTP/1.1 406 Annotation order is Mandatory');
   exit(-1);
}
$order = $_POST['order'];

if ( empty($_POST['user']) )
{
   header('HTTP/1.1 406 User is Mandatory');
   exit(-1);
}
$user = $_POST['user'];

if ( empty($_POST['color']) )
{
   header('HTTP/1.1 406 Color is Mandatory');
   exit(-1);
}
$color = $_POST['color'];

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Could not connect to the database : ".$config['dbname']);
   exit(-1);
} else {
     $link->query('SET NAMES utf8');
     $transdata="";

     // get annotation text
     $anntext = '';
     $sql="SELECT data, id FROM annotation WHERE source='".addslashes($source)."' AND norder=".$order.";";
     $results=$link->query($sql);
     if ( mysqli_num_rows($results) != 1 ) {
        error_log( 'Couldn\'t get annotation for : '.$source.' : '.$sql);
        mysqli_close($link);
        exit(-1);
     } else {
        $rowres=mysqli_fetch_row($results);
        $anntext = $rowres[0];
        $annid = $rowres[1];
        $annlines = preg_split('/\r\n|\r|\n/', $anntext);
        forEach( $annlines as $line ) {
          if ( $line == "" ) continue;
          if ( $line[2] == ':' ||
               $line[3] == ':' ) { // this is translation, copy as such
             $transdata .= $line."\n";
          }
        }
     }
     if ( $anntext == "" ) {
        mysqli_close($link);
        header('HTTP/1.1 200 Translation success');
        exit(0);
     }

     // translate the annotation for each out language
     $cmdresult = 0;
     $langsout = explode(',', $target );
     forEach ($langsout as $lo) {
        if ( strstr( $anntext, $lo.":" ) || ( $lo == $slang ) ) {
           // annotation is already translated to that language
           continue;
        }
        $annlines = preg_split('/\r\n|\r|\n/', $anntext);
        forEach( $annlines as $line ) {
           $rline = "";
           if ( !strstr( $line, ":" ) ) { // not a translated text
              $rline = $line;
           }
           if ( $line[2] == ':' ||
                $line[3] == ':' ) { // this is translation
              if ( substr($line, 0, 2) == $slang ) { // ==> translation to the source language
                  $rline = substr($line, 3);
              } else if ( substr($line, 0, 3) == $slang ) { // ==> to be translated
                  $rline = substr($line, 4);
              } else {
                  // ignored, never asked to be translated from this language
                  continue;
              }
           }
           if ( $rline === "" ) continue; // ligne ignored as it is a translation to another language
           $cmdoutput = array();
           $cmd="php translate.php $slang $lo \"$rline\"\n";
           // error_log($cmd);
           $result = exec($cmd, $cmdoutput, $cmdresult);
           if ($cmdresult!=0) {
              header('HTTP/1.1 406 Couldn\'t translate annotation : '.$annid);
              error_log('Couldn\'t translate annotation : '.$annid." (".$cmdresult.")");
              mysqli_close($link);
              exit(-1);
           } else {
              if ( $cmdoutput[0] != "" )  
                 $transdata .= $lo.":".$cmdoutput[0]."\n";
           }
        }
     }
     $sql="UPDATE annotation SET data='".addslashes($transdata)."' WHERE id=".$annid.";";
     // error_log($sql);
     $resupd=$link->query($sql);
     if ( $resupd != TRUE ) {
        header('HTTP/1.1 406 Couldn\'t update annotation : '.$annid);
        error_log('Couldn\'t update annotation : '.$annid." (".$sql.")");
        mysqli_close($link);
        exit(-1);
     }
}

header('HTTP/1.1 200 Annotation '.$annid.' has been translated');
mysqli_close($link);
exit(0)
?>
