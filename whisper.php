<?php

include("config.php");
include("functions.php");

if ( count($argv) != 7 ) {
   error_log("wrong number of arguments to launch whisper : ".count($argv) );
   error_log("usage : whisper.php <annid> <source> <user> <language> <model> <linear=true|false>");
   exit(-1);
}

$annid = $argv[1];
$annofile = "excerpts/anno_$annid.ogg";
$jsonfile = "excerpts/anno_$annid.json";
if ( !file_exists( $annofile ) ) {
   error_log("file : $annofile does not exist");
   exit(-1);
}

$source=$argv[2];
$user=$argv[3];

$langoption = "";
if ( $argv[4] != "Guess" ) {
   $langoption = " --language $argv[4]";
}

$modeloption = " --model $argv[5]";


$annfilter="";
$forder=0;
if ( $argv[6] ) {
   $annfilter = " AND norder>=4096 ";
   $forder=4095;
} else {
   $annfilter = " AND norder<4096 ";
   $forder=0;
}

$whispres=0;
$starttime = time();
$endtime = $starttime;
$cmdresult=0;
$cmdoutput=array();
$cmd = "whisper --output_dir ./excerpts $annofile $modeloption $langoption";
error_log("launching : $cmd");
$result=exec($cmd, $cmdoutput, $cmdresult);
if ( $cmdresult != 0 ) {
   error_log( __FILE__." : exec failed : ".$cmdresult );
   foreach( $cmdresult as $cresult ) {
      error_log( __FILE__." : output : ".$cresult );
   }
} else {
   $whispres=2;
   $endtime = time();
}
$timewhisper=$endtime-$starttime;
error_log("whisper took : $timewhisper seconds");

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Could not connect to the database : ".$config['dbname']);
   exit(-1);
} else {
     $link->query('SET NAMES utf8');

     // get start and end time
     $annstart = 0;
     $annend = 0;
     $annurl = '';
     $sql="SELECT start, end, url FROM annotation WHERE id=".$annid;
     $results=$link->query($sql);
     if ( mysqli_num_rows($results) != 1 ) {
        error_log( 'Couldn\'t get annotation : '.$annid.' : '.$sql);
        mysqli_close($link);
        exit(-1);
     } else {
        $rowres=mysqli_fetch_row($results);
        $annstart = $rowres[0];
        $annend = $rowres[1];
        $annurl = explode("=",$rowres[2])[0];
     }

     // get user color
     $ucolor='';
     $sql="SELECT color FROM user WHERE user='".$user."'";
     $resultu=$link->query($sql);
     if ( mysqli_num_rows($resultu) != 1 ) {
        error_log( 'Couldn\'t get user : '.$user);
        mysqli_close($link);
        exit(-1);
     } else {
        $rowres=mysqli_fetch_row($resultu);
        $ucolor = $rowres[0];
     }

     $sql = "UPDATE annotation SET whispered=$whispres WHERE id=".$annid.";";
     error_log( 'Updating annotation : '.$sql);
     $result = $link->query($sql);
     if ( $result === FALSE  )
     {
        error_log( 'Couldn\'t update annotation : '.$result);
        mysqli_close($link);
        exit(-1);
     }
     $sql = "UPDATE user SET nbt=nbt+1, tts=ADDTIME(tts,'00:".date("H:i:s",$timewhisper)."')  WHERE user='".$user."';";
     error_log( 'Updating user : '.$sql);
     $result = $link->query($sql);
     if ( $result === FALSE  )
     {
        error_log( 'Couldn\'t update user : '.$result);
        mysqli_close($link);
        exit(-1);
     }

     // get json result and create all new annotations
     if ( file_exists( $jsonfile ) ) {
       $wresults = json_decode(file_get_contents($jsonfile), true);
       foreach($wresults["segments"] as $segment ) {
         error_log("found text : ".$segment["text"]);
         $nastart=$annstart+$segment["start"];
         $naend=$annstart+$segment["end"];
         $isql = "INSERT INTO annotation ( norder, start, end, url, source, data, user, color, whispered ) VALUES ( 100000,".$nastart.",".$naend.",'".$annurl."=".$segment["start"]."','".addslashes($source)."','".addslashes($segment["text"])."','".addslashes($user)."','".addslashes($ucolor)."', 2 )";         
         error_log($isql);
         $resins = $link->query($isql);
         if ( $resins === FALSE )
         {
            error_log( 'Couldn\'t create annotation : '.$resins);
            mysqli_close($link);
            exit(-1);
          }
       }
     } else {
        error_log( 'JSON file not found : '.$jsonfile);
     }

     // delete original annotation
     $dsql = "DELETE FROM annotation WHERE id=".$annid;         
     error_log($dsql);
     $resdel = $link->query($dsql);
     if ( $resdel === FALSE )
     {
        error_log( 'Couldn\'t delete original annotation : '.$resdel);
        mysqli_close($link);
        exit(-1);
     }

     // renumber all annotations
     $ssql = "SELECT id FROM annotation WHERE source='".addslashes($source)."' ".$annfilter." ORDER BY start";         
     error_log($ssql);
     $ressel = $link->query($ssql);
     while ( $resrow = mysqli_fetch_row($ressel) ) {
         $forder++;
         $usql = "UPDATE annotation SET norder=".$forder." WHERE id=".$resrow[0];         
         error_log($usql);
         $resupd = $link->query($usql);
         if ( $resupd === FALSE ) {
            error_log( 'Couldn\'t update annotation order : '.$resupd);
            mysqli_close($link);
            exit(-1);
         }
     }

     mysqli_close($link);
}

exit(0)
?>