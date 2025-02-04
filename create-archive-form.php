<?php
include("config.php");
include("functions.php");

session_start();

if ( !isset($_SESSION['schtroumpf']) || !isset($_SESSION['papa']) )
{
    header( "Location: ./index.php" );
    exit();
}

?>

<html>
<head>
  <meta charset="UTF-8">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/app.css" rel="stylesheet">
  <link rel="stylesheet" href="css/spinner.css" />
  <link href="css/alertify.core.css" rel="stylesheet">
  <link href="css/alertify.default.css" rel="stylesheet">
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/all.css" />

  <script src="js/jquery.min.js"></script>
  <script src="js/alertify.min.js"></script>
  <script type="text/javascript">

    function doCreateArchive() {
       url=$("#createform :input[name='url']").val().replaceAll('&','%26');
       if ( url == "" )
       {
          alertify.alert("Please, enter a url!");
          return;
       }
       $('#error-zone').css({background:'lightblue'});
       $('#error-zone').html("");
       $('#error-zone').animate({ opacity : 0.0 },{queue:false,duration:1000});
       $('.lds-spinner').css('opacity','1.0');
       $('#create').prop('disabled', true);
       $.get( "create-archive.php", 
       { 
         file : encodeURIComponent(url), 
         user : '<?php echo $_SESSION['schtroumpf']; ?>' 
       }, function(data) {
        $('.lds-spinner').css('opacity','0.0');
        if ( data.indexOf("ERR:")>=0 )
        {
          $('#error-zone').css({background:'red'});
          $('#error-zone').css({color:'white'});
          $('#error-zone').html(data.replace("ERR: ",""));
          $('#error-zone').animate({ opacity : 1.0 },{queue:false,duration:1000});
          $('#create').prop('disabled', false);
        }
        else
        {
          document.location = data;
        }
       })
       .fail(function() {
          $('#error-zone').css({background:'red'});
          $('#error-zone').css({color:'white'});
          $('#error-zone').html("Archive creation error");
          $('#error-zone').animate({ opacity : 1.0 },{queue:false,duration:1000});
          $('#create').prop('disabled', false);
       });
    }

    function back() {
       document.location='index.php';
    }

    $(document).ready( function(){
      $('.lds-spinner').css('opacity','0.0');
    });

  </script>

</head>

<body background="img/background.png">
<a href="./index.php"><i class="fa fa-home fa-2x" aria-hidden="true" style="color: #999; float:left; margin-left:20px; margin-top:28px;" ></i></a>

<center><table width=90%>
<tr><td align=right>
</td><td align=center>
<h1><?php echo $config['project-name']; ?></h1>
</td</tr>
</table></center>

<center>
<h1>Create Archive</h1>
</center>

<?php
print "
<form action='javascript:doCreateArchive()' id='createform' name='createform' action=post>
<table width=50% align=center>
<tr><td align=center colspan=2>
<label for='user'>Media file URL ( audio or video file ) <br/>If you encounter a problem, write to gissnetwork@giss.tv<br/></label><br/><br/>Here, dont put a link to youtube as this is a stream format that needs to be extracted first with a tool like <a href='https://publer.com/tools/youtube-video-downloader'>Publer</a><br/><br/>Check also that the server hosting the file allows the Cross Domain file loading.
</td></tr>
<tr><td colspan=2>
<input type='text' id='url' name='url' style='width:100%;'/>
</td></tr>
<tr><td colspan=2 align=center>
<br/><br/>
<input type='submit' id='create' class='bluebutton' value='Create' />
</td></tr>
<tr><td align=center colspan=2>
<div id='error-zone'>Error text</div>
</td></tr>
</table>
</form>
";
?>

<center><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></center>

</body>
</html>
