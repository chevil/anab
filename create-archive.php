<?php

include("config.php");
include("functions.php");

session_start();

if ( !isset($_SESSION['schtroumpf']) || !isset($_SESSION['papa']) )
{
    print "ERR: You are not logged in, please reconnect!";
    exit();
}

if ( $_SERVER['SERVER_PORT'] == 80 )
{
   $servroot = "https://".$_SERVER['HTTP_HOST'];
}
else
{
   $servroot = "https://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'];
}

if ( empty($_GET['file']) )
{
   print "ERR: File is not set";
   exit(-1);
}
$file=$_GET['file'];
if ( !strstr($file, "https" ) )
{
   print "ERR: File must be loaded over HTTPS";
   exit(-1);
}

if ( empty($_GET['user']) )
{
   print "ERR: User is not set";
   exit(-1);
}
$user=$_GET['user'];

error_log("ANAB : Calling : ./create-archive.sh '".urldecode($file)."'");
if ( strstr( $result=exec("./create-archive.sh \"".urldecode($file)."\""), "ERR:" ) )
{
   print( $result );
   exit(-1);
}
else
{
   error_log("ANAB : ".$result);
   $infos=explode('√',$result);

   // saving in the new archive in the database
   $link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
   if (!$link) {
      print( "ERR: Couldn't connect to the database : ".$config['dbname']);
      exit(-1);
   } else {
      $link->query("SET NAMES utf8");
      $sql = "SELECT id FROM archive WHERE url='".urldecode($file)."';";
      // error_log($sql);
      $result = $link->query($sql);
      if ( $result && mysqli_num_rows($result) === 0) {
         $isql = "INSERT INTO archive ( uri, url, author, title, collection, date, creator ) VALUES ( '".addslashes($infos[0])."','".addslashes($file)."','".addslashes($infos[1])."','".addslashes($infos[2])."','".addslashes($infos[3])."','".addslashes($infos[4])."','".addslashes($_SESSION['schtroumpf'])."' )";
         $insert = $link->query($isql);
         if ( $insert !== true ) {
            error_log( "ERROR : ".__FILE__." : could not create archive : ".$file." : ".mysqli_error($link) );
            mysqli_close($link);
            print( "ERR: Couldn't create archive : ".$file);
            exit(-1);
         }
      }
   }
   print( $servroot.dirname($_SERVER['SCRIPT_NAME'])."/manage-archives.php" );
}

?>
