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

if ( empty($_POST['linear']) )
{
   header('HTTP/1.1 406 Linear Mode is Mandatory');
   exit(-1);
}
$linear = $_POST['linear'];
error_log("translate-all.php : linear >".$linear."<");

if ( $linear == 'true' ) {
   $sqlclause = "AND norder >= 4096";
} else {
   $sqlclause = "AND norder < 4096";
}

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Could not connect to the database : ".$config['dbname']);
   exit(-1);
} else {
     $link->query('SET NAMES utf8');

     $transdata="";

     // get annotation text
     $anntext = '';
     $sql="SELECT data, id FROM annotation WHERE source='".addslashes($source)."' ".$sqlclause." ORDER BY norder;";
     error_log("translate-all : translating annotation : ".$sql );
     $results=$link->query($sql);
     while ( $rowres=mysqli_fetch_row($results) ) {
        $anntext = $rowres[0];
        $annid = $rowres[1];
        error_log("translate-all : translating annotation : ".$annid );
        $annlines = preg_split('/\r\n|\r|\n/', $anntext);
        forEach( $annlines as $line ) {
          if ( $line == "" ) continue;
          if ( strstr( $line, $slang.":" ) ) {
             $transdata .= $line."\n";
          } else {
              $transdata .= $slang.":".$line."\n";
          }
        }
        if ( $anntext == "" ) {
           continue;
        }

        // translate the annotation for each out language
        $cmdresult = 0;
        $langsout = explode(',', $target );
        forEach ($langsout as $lo) {
           if ( strstr( $anntext, $lo.":" ) ) {
              // annotation is already translated to that language
              continue;
           }
           $annlines = preg_split('/\r\n|\r|\n/', $anntext);
           forEach( $annlines as $line ) {
              $rline = $line;
              if ( $line[2] == ':' ) { // this is translation
                 if ( substr($line, 0, 2) == $slang ) { // ==> to be translated
                     $rline = substr($line, 3);
                 } else {
                     // ignored, never asked to be translated from this language
                     continue;
                 }
              }
              if ( $rline === "" ) continue;
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
}

header('HTTP/1.1 200 Document has been translated');
mysqli_close($link);
exit(0)
?>
