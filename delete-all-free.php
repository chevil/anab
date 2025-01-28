<?php

include("config.php");

  if ( empty($_POST['source']) )
  {
     header('HTTP/1.1 406 Source is Mandatory');	  
     exit(-1);
  }
  $source = $_POST['source'];
  error_log("Deleting free annotations for : ".$source );

  $link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
  if (!$link) {
     error_log( "Couldn't connect to the database : ".$config['dbname']);
     header('HTTP/1.1 500 Could not connect to the database');
     exit(-1);
  } else {
     $link->query('SET NAMES utf8');
     $link->query("LOCK TABLES annotation WRITE, audiobook WRITE");
     // error_log( 'Deleting annotation : '.urldecode($source).':'.$order);
     $sqls = "SELECT id FROM annotation WHERE source='".addslashes($source)."' AND norder<4096;";
     error_log( "sqls : ".$sqls );
     $results = $link->query($sqls);
     while ( $annrow = mysqli_fetch_row( $results ) ) {
        $annid = $annrow[0];
        // error_log( "annotation id : ".$annid );
        $sqld = "DELETE FROM audiobook WHERE aoid=".$annid.";";
        $resultdd = $link->query($sqld);
        if ( $resultdd <= 0 ) {
           error_log( __FILE__." : Could not delete annotation from audiobooks" );
        }
        $sql = "DELETE FROM annotation WHERE id=".$annid.";";
        $resultd = $link->query($sql);
        if ( $resultd <= 0 )
        {
           header('HTTP/1.1 500 Error deleting annotation');	  
           $link->query("UNLOCK TABLES");
           mysqli_close($link);
           exit(-1);
        }
     }
  }

  header('HTTP/1.1 200 OK');	  
  $link->query("UNLOCK TABLES");
  mysqli_close($link);
  exit(0);

?>
