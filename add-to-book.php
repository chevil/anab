<?php

include("config.php");

  if ( empty($_POST['user']) )
  {
     header('HTTP/1.1 406 User is Mandatory');	  
     exit(-1);
  }
  $user = $_POST['user'];

  if ( empty($_POST['order']) )
  {
     header('HTTP/1.1 406 Order is Mandatory');	  
     exit(-1);
  }
  $order = $_POST['order'];

  if ( empty($_POST['source']) )
  {
     header('HTTP/1.1 406 Source is Mandatory');	  
     exit(-1);
  }
  $source = $_POST['source'];

  $book='';
  if ( !empty($_POST['newbook']) )
  {
     $book = $_POST['newbook'];
  } else if ( !empty($_POST['oldbook']) ) {
     $book = $_POST['oldbook'];
  }
  if ( $book === '' ) {
     header('HTTP/1.1 406 Book is mandatory');	  
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
     $sql = "SELECT id, source, start, end FROM annotation WHERE source='".addslashes($source)."' AND norder=".$order.";";
     $result = $link->query($sql);
     if ( mysqli_num_rows($result) !== 1 )
     {
        header('HTTP/1.1 404 Annotation not found');	  
        exit(-1);
     } else {
        $annrow = mysqli_fetch_row($result);
        $annid = $annrow[0];
        $source = $annrow[1];
        $start = $annrow[2];
        $end = $annrow[3];
        error_log( "extracting : ".$start." -- ".$end );
     }
     $border = 0;
     $sqlo = "SELECT MAX(norder) FROM audiobook WHERE title='".addslashes($book)."';";
     $resulto = $link->query($sqlo);
     if ( mysqli_num_rows($resulto) === 0 )
     {
        $border = 1;
     } else {
        $bookrow = mysqli_fetch_row($resulto);
        $border = $bookrow[0]+1;
     }
     $excerpt = "excerpts/anno_".$annid.".mp3";
     $sqli = "INSERT INTO audiobook ( title, aoid, norder, excerpt, user ) VALUES ('".addslashes($book)."',".$annid.",".$border.",'".$excerpt."', '".addslashes($user)."' );";
     $resulti = $link->query($sqli);
     if ( $resulti !== true )
     {
        header('HTTP/1.1 406 Could not add to audiobook!');	  
        exit(-1);
     }
     // generate the audio file if necessary
     $duration = $end - $start;
     $dirname = urldecode($book);
     $cmd = "./create-excerpt.sh ".$start." ".$duration." \"".urldecode($source)."\" \"".$excerpt."\" \"".$dirname."\" 2>/dev/null";
     error_log($cmd);
     if ( strstr( $result=exec($cmd), "ERR:" ) )
     {
        header('HTTP/1.1 406 '.str_replace("ERR: ","",$result) );	  
        exit(-1);
     }
     error_log( __FILE__." : excerpt creation returned : ".$result );
  }

  header('HTTP/1.1 200 OK');	  
  mysqli_close($link);
  exit(0);

?>
