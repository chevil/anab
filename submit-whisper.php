<?php

require("config.php");
require("html2text.php");

function detached_exec($cmd) {
    $pid = pcntl_fork();
    switch($pid) {
         // fork error
         case -1 : return false;

         // this code runs in child process
         case 0 :
             // obtain a new process group
             posix_setsid();
             // exec the command
             error_log($cmd);
             exec($cmd);
             break;

         // return the child pid in father
         default: 
             error_log("new process : ".$pid);
             return $pid;
    }
}

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

  if ( empty($_POST['linear']) )
  {
     header('HTTP/1.1 406 Linear mode is Mandatory');	  
     exit(-1);
  }
  $linear = $_POST['linear'];

  // check that /var/www/ is writable by apache
  $cmd="touch /var/www/dummy";
  $cmdoutput = array();
  $cmdresult = 0;
  error_log($cmd);
  $result = exec($cmd, $cmdoutput, $cmdresult);
  if ($cmdresult!=0) {
     header('HTTP/1.1 406 apache (www-data?) doesn\'t have permissions to write in /var/www which is needed!');	  
     exit(-1);
  }

  $cmd="export PATH='/usr/local/bin':$PATH; which whisper";
  $cmdoutput = array();
  $cmdresult = 0;
  error_log($cmd);
  $result = exec($cmd, $cmdoutput, $cmdresult);
  error_log($cmdresult);
  // if ($cmdoutput[0]=="") {
  //    header('HTTP/1.1 406 Whisper is not installed on the back-end, you should install it with : pip3 install openai-whisper and create a link to whisper in /usr/local/bin');	  
  //   exit(-1);
  // }

  $link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
  if (!$link) {
     error_log( "Couldn't connect to the database : ".$config['dbname']);
     header('HTTP/1.1 500 Could not connect to the database');
     exit(-1);
  } else {
     $link->query('SET NAMES utf8');
     error_log( 'Selecting annotation : '.urldecode($source).':'.$order);
     $sql = "SELECT id, source, start, end, whispered, data FROM annotation WHERE source='".addslashes($source)."' AND norder=".$order.";";
     $result = $link->query($sql);
     if ( mysqli_num_rows($result) !== 1 )
     {
        error_log( 'Annotation not found !');
        header('HTTP/1.1 404 Annotation not found');	  
        mysqli_close($link);
        exit(-1);
     } else {
        $annrow = mysqli_fetch_row($result);
        if ( $annrow[4] != 0 ) {
           error_log( 'Annotation already transcripted by Open-AI!');
           header('HTTP/1.1 406 Annotation already transcripted by Open-AI!');	  
           mysqli_close($link);
           exit(-1);
        }
        $annid = $annrow[0];
        $source = $annrow[1];
        $start = $annrow[2];
        $end = $annrow[3];
        $anntext = convert_html_to_text($annrow[4]);
        $annlines = preg_split('/\r\n|\r|\n/', $anntext);
        if ( $annlines[0] == ':' )
        {
          $excerpt_title= substr(substr($annlines[0],3),0,40)."...";
        }
        else
        {
          $excerpt_title= substr($annlines[0],0,40)."...";
        }
        $duration = $end - $start;
         error_log( "Annotation duration ; ".$duration." language : ".$lang );
        if ( ($duration<=30.0 ) && ($lang=="Guess") ) {
           error_log( 'Annotation is less than 30 seconds, so please, specify the language, it\'s impossible to guess!');
           header('HTTP/1.1 406 Annotation is less than 30 seconds, so please, specify the language, it\'s impossible to guess!');
           mysqli_close($link);
           exit(-1);
        }
        error_log( "extracting : ".$start." -- ".$end." duration : ".$duration);
     }
     $excerpt = "excerpts/anno_".$annid.".ogg";
     // generate the audio file 
     $duration = $end - $start;
     $dirname = '../../excerpts';
     $cmd = "./create-excerpt.sh ".$start." ".$duration." \"".urldecode($source)."\" \"".$excerpt."\" \"".$excerpt_title."\" 2>/dev/null";
     error_log($cmd);
     if ( strstr( $result=exec($cmd), "ERR:" ) )
     {
        error_log( __FILE__." : excerpt creation returned : ".$result );
        header('HTTP/1.1 406 '.str_replace("ERR: ","",$result) );	  
        mysqli_close($link);
        exit(-1);
     }
     error_log( __FILE__." : excerpt creation returned : ".$result );
  }

  // call whisper in background and detach it from process
  $cmd = "php whisper.php $annid $source $user $lang $model $linear >excerpts/whisper_$annid.log 2>&1 &";
  // $pid = detached_exec($cmd);
  $cmdoutput = array();
  $cmdresult = 0;
  error_log($cmd);
  $result = exec($cmd, $cmdoutput, $cmdresult);
  if($cmdresult !== 0) {
    error_log( __FILE__." : launching whisper.php failed : ".$cmdresult);
    header("HTTP/1.1 406  : launching whisper.php failed!");	  
    foreach($cmdoutput as $output ) {
        error_log("output : ".$output );
    }
    mysqli_close($link);
    exit(-1);
  } else {
    error_log("launched whisper.php in background : ".$cmdresult );
  }

  // update annotation set the whispered state to 1 until job finishes, then state will be 2 : processed
  $sql = "UPDATE annotation SET whispered=1 WHERE id='".$annid."';";
  $uresult = $link->query($sql);
  if ( !$uresult ) {
        error_log( __FILE__." : update annotation failed : ".$sql );
        header("HTTP/1.1 406  : update annotation failed : ".$sql);	  
        mysqli_close($link);
        exit(-1);
  } else {
        error_log( __FILE__." : updated annotation ".$sql );
  }

  header('HTTP/1.1 200 OK');	  
  mysqli_close($link);
  exit(0);

?>
