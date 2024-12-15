<?php

include("config.php");

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

  if ( empty($_POST['source']) )
  {
     header('HTTP/1.1 406 Source is Mandatory');	  
     exit(-1);
  }
  $source = $_POST['source'];

  if ( empty($_POST['order']) )
  {
     header('HTTP/1.1 406 Order is Mandatory');	  
     exit(-1);
  }
  $order = $_POST['order'];

  if ( empty($_POST['lang']) )
  {
     header('HTTP/1.1 406 Langage is Mandatory');	  
     exit(-1);
  }
  $lang = $_POST['lang'];

  if ( empty($_POST['model']) )
  {
     header('HTTP/1.1 406 Model is Mandatory');	  
     exit(-1);
  }
  $model = $_POST['model'];

  if (!file_exists('/usr/bin/whisper') && !file_exists('/usr/local/bin/whisper')) {
     header('HTTP/1.1 406 Whisper is not installed on the back-end, you should install it with : pip3 install openai-whisper');	  
     exit(-1);
  }

  $link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
  if (!$link) {
     error_log( "Couldn't connect to the database : ".$config['dbname']);
     header('HTTP/1.1 500 Could not connect to the database');
     exit(-1);
  } else {
     $link->query('SET NAMES utf8');
     error_log( 'Selecting annotation : '.urldecode($source).':'.$order);
     $sql = "SELECT id, source, start, end, whispered FROM annotation WHERE source='".addslashes($source)."' AND norder=".$order.";";
     $result = $link->query($sql);
     if ( mysqli_num_rows($result) !== 1 )
     {
        error_log( 'Annotation not found !');
        header('HTTP/1.1 404 Annotation not found');	  
        exit(-1);
     } else {
        $annrow = mysqli_fetch_row($result);
        if ( $annrow[4] != 0 ) {
           error_log( 'Annotation already transcripted by Open-AI!');
           header('HTTP/1.1 404 Annotation already transcripted by Open-AI!');	  
           exit(-1);
        }
        $annid = $annrow[0];
        $source = $annrow[1];
        $start = $annrow[2];
        $end = $annrow[3];
        error_log( "extracting : ".$start." -- ".$end );
     }
     $excerpt = "excerpts/anno_".$annid.".ogg";
     // generate the audio file 
     $duration = $end - $start;
     $dirname = '../../excerpts';
     $cmd = "./create-excerpt.sh ".$start." ".$duration." \"".urldecode($source)."\" \"".$excerpt."\" \"".$dirname."\" 2>/dev/null";
     error_log($cmd);
     if ( strstr( $result=exec($cmd), "ERR:" ) )
     {
        error_log( __FILE__." : excerpt creation returned : ".$result );
        header('HTTP/1.1 406 '.str_replace("ERR: ","",$result) );	  
        exit(-1);
     }
     error_log( __FILE__." : excerpt creation returned : ".$result );
  }

  // update annotation set the whispered state to 1 until job finishes, then state will be 2 : processed
  $sql = "UPDATE annotation SET whispered=1 WHERE source='".addslashes($source)."' AND norder=".$order.";";
  $uresult = $link->query($sql);
  if ( !$uresult ) {
        error_log( __FILE__." : update annotation failed : ".$sql );
        header("HTTP/1.1 406  : update annotation failed : ".$sql);	  
        exit(-1);
  }

  // call whisper in background and wait for result

  header('HTTP/1.1 200 OK');	  
  mysqli_close($link);
  exit(0);

?>
