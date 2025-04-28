<?php

include("config.php");

if ( empty($_POST['url']) )
{
   header('HTTP/1.1 406 Title is Mandatory');	  
   exit(-1);
}
$url = $_POST['url'];

$link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
if (!$link) {
   error_log( "Couldn't connect to the database : ".$config['dbname']);
   header('HTTP/1.1 500 Could not connect to the database');
   exit(-1);
} else {
   $link->query('SET NAMES utf8');
   $sqls = "SELECT title, author FROM archive WHERE url = '".addslashes($url)."';";
   // error_log( 'Getting title : '.$sqls );
   $results = $link->query($sqls);
   if ( mysqli_num_rows($results) <= 0 ) {
      header('HTTP/1.1 500 Error getting title : '.mysqli_num_rows($results));	  
      mysqli_close($link);
      exit(-1);
   } else {
      $row = mysqli_fetch_array($results);
      if ( $row['author'] != "" && strtolower($row['author']) != "unknown" )
         echo trim($row['title'])." (".trim($row['author']).")";
      else
         echo trim($row['title']);
      mysqli_close($link);
   }
}
exit(0);

?>
