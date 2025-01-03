<?php

include("config.php");

if ( empty($_POST['url']) )
{
   header('HTTP/1.1 406 Url is Mandatory');	  
   exit(-1);
}
$url = $_POST['url'];

if ( !isset($_POST['biography']) )
{
   header('HTTP/1.1 406 Biography is Mandatory');	  
   exit(-1);
}
$biography = $_POST['biography'];

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Couldn't connect to the database : ".$config['dbname']);
   header('HTTP/1.1 500 Could not connect to the database');
   exit(-1);
} else {
   $link->query('SET NAMES utf8');
   error_log( 'Updating biography : '.$url . " : " . $biography );
   $sqlu = "UPDATE archive SET biography='".addslashes($biography)."' WHERE url LIKE '%".addslashes($url)."';";
   error_log($sqlu);
   $resultu = $link->query($sqlu);
   if ( $resultu != 1 ) {
      header('HTTP/1.1 500 Error updating biography : '.$resultu);	  
      mysqli_close($link);
      exit(-1);
   } else {
      header('HTTP/1.1 200 OK');	  
      mysqli_close($link);
   }
}
exit(0);

?>
