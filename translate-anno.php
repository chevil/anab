<?php

include("config.php");
include("functions.php");
include("wlangs.php");
include("sclangs.php");

if ( count($argv) != 4 ) {
   error_log("wrong number of arguments to launch annotation translation : ".count($argv) );
   error_log("usage : $argv[0] <annid> <langin> <langsout>");
   error_log("example : $argv[0] 1245 fr en,es,it,hi");
   exit(-1);
}

$annid = $argv[1];
$slang=$argv[2];
$olang=$argv[3];

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Could not connect to the database : ".$config['dbname']);
   exit(-1);
} else {
     $link->query('SET NAMES utf8');

     // get annotation text
     $anntext = '';
     $sql="SELECT data FROM annotation WHERE id=".$annid;
     $results=$link->query($sql);
     if ( mysqli_num_rows($results) != 1 ) {
        error_log( 'Couldn\'t get annotation : '.$annid.' : '.$sql);
        mysqli_close($link);
        exit(-1);
     } else {
        $rowres=mysqli_fetch_row($results);
        $anntext = $rowres[0];
        if ( strstr( $anntext, $langin.":" ) ) {
           print $anntext;
        } else {
           print $slang.":".$anntext."\n";
        }
     }
     if ( $anntext == "" ) {
        mysqli_close($link);
        exit(0);
     }

     // translate the annotation for each out language
     $cmdresult = 0;
     $langsout = explode(',', $olang );
     forEach ($langsout as $lo) {
        $cmdoutput = array();
        $cmd="php translate.php $slang $lo \"$anntext\"\n";
        // error_log($cmd);
        $result = exec($cmd, $cmdoutput, $cmdresult);
        if ($cmdresult!=0) {
           error_log('Couldn\'t translate annotation : '.$annid." (".$cmdresult.")");
           mysqli_close($link);
           exit(-1);
        } else {
           print $lo.":".$cmdoutput[0]."\n";
        }
     }
}

mysqli_close($link);
exit(0)
?>