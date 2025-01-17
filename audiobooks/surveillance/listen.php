<?php
include("../../config.php");
include("../../functions.php");

session_start();

$title="surveillance";

// reading user's colors
$waveColor="#000000";
$progressColor="#000000";
$mapWaveColor="#000000";
$mapProgressColor="#000000";
// reading user's colors
$waveColor="#000000";
$progressColor="#000000";
$mapWaveColor="#000000";
$mapProgressColor="#000000";

$ressettings = db_query( "SELECT name, value FROM settings" );

while ( $rowsetting = mysqli_fetch_array( $ressettings) )
{
   if ( $rowsetting['name'] == "waveColor" )
      $waveColor = $rowsetting['value'];
   if ( $rowsetting['name'] == "progressColor" )
      $progressColor = $rowsetting['value'];
   if ( $rowsetting['name'] == "mapWaveColor" )
      $mapWaveColor = $rowsetting['value'];
   if ( $rowsetting['name'] == "mapProgressColor" )
      $mapProgressColor = $rowsetting['value'];
}

?>

<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?></title>

  <style type="text/css">
      .bluebutton { height: 30px; width : 200px; text-align: center;
                   line-height: 30px; vertical-align:middle; opacity : 1;
                   -moz-border-radius: 10px; border-radius: 10px; background : lightblue }
      .databutton { height: 30px; width : 200px; text-align: center;
                   line-height: 30px; vertical-align:middle; opacity : 1;
                   -moz-border-radius: 10px; border-radius: 10px; background : lightgreen }
      .stable { -moz-border-radius: 10px; border-radius: 10px; background : lightgrey }
      h1 { font-weight: normal; font-family: inherit; font-weight: 500; line-height: 1.1; color: inherit; box-sizing: border-box; font-size: 36px; }
  </style>

  <link href="../../css/alertify.core.css" rel="stylesheet">
  <link href="../../css/alertify.default.css" rel="stylesheet">
  <link href="../../css/app.css" rel="stylesheet">
  <link href="../../css/font-awesome.min.css" rel="stylesheet" />

  <script type="text/javascript" src="../../js/circular-json.js"></script>
  <script type="text/javascript" src="../../js/alertify.min.js"></script>
  <script type="text/javascript" src="../../js/jquery.min.js"></script>
  <script type="text/javascript" src="../../js/bootstrap.min.js"></script> 
  <script type="text/javascript" src="../../js/wavesurfer.min.js"></script>
  <script type="text/javascript" src="../../js/jquery.ui.js"></script>

  <script type="text/javascript">

  </script>

</head>

<body background="../../img/background.png">
<a href="../../index.php"><i class="fa fa-chevron-left fa-1x" aria-hidden="true" style="color: #000000; float:left; margin-left:20px;" ></i></a>

<center><table width=90%>
<tr><td align=right>
</td><td align=center>
<h1><?php echo $config['project-name']; ?></h1>
</td</tr>
</table></center>

<center>
<h1>Audiobook : <?php echo $title; ?></h1>
</center>

<?php
$resultdb=db_query("SELECT data, excerpt FROM audiobook, annotation WHERE audiobook.aoid=annotation.id AND audiobook.title='".rawurlencode($title)."' ORDER BY audiobook.norder" );
$nbexcerpts=mysqli_num_rows($resultdb);
if ( $nbexcerpts == 0 )
{
   print("<center><div class='listen-item'>This book is empty</div></center>");
}
else
{
   $counter=0;
   while ($row=mysqli_fetch_array($resultdb))
   {
     $snote = '';
     $lines = explode("\n",$row["data"]);
     for ($l=0; $l<count($lines); $l++ )
     {    
       if ( $lines[$l][2] == ':' ) {
          $snote .= substr($lines[$l], 3)."<br/>";
       } else {
          $snote .= $lines[$l]."<br/>";
       }
     }
     print("<div class='listen-item' id='wave".$counter."' ></div>");
     print("<div class='listen-item' id='excerpt".$counter."' style='display:none;'>../../".$row["excerpt"]."</div>");
     print("<center><div class='listen-item' id='leyenda".$counter."' >".$snote."</div></center>");
     $counter++;
   }
}
?>

<script type="text/javascript">

$(document).ready( function(){

  var nbwaves = <?php echo $nbexcerpts ?>;
  var wavesurfer;
  var wCurrent=-1;
  var wavesurfers = new Array(nbwaves);

  for( w=0; w<nbwaves; w++ )
  {
     excerpt = $("#excerpt"+w).html();
     $("#excerpt"+w).html(w);
     wavesurfers[w] = WaveSurfer.create({
        container: '#wave'+w,
        height: 100,
        pixelRatio: 1,
        scrollParent: false,
        normalize: true,
        minimap: false,
        interact: false,
        mediaControls: true,
        fillParent: true,
        hideScrollbar: true,
        barRadius: 0,
        forceDecode: true,
        waveColor: '<?php echo $waveColor; ?>',
        progressColor: '<?php echo $progressColor; ?>',
        backend: 'MediaElement'
     });
     wavesurfers[w].load(
        excerpt
     );
     wavesurfers[w].on('finish', function () {
        // reset player
        console.log("stopping : " + wCurrent );
        wavesurfers[wCurrent].stop();
        wCurrent=(wCurrent+1)%nbwaves; 
        console.log("playing : " + wCurrent );
        wavesurfers[wCurrent].play();
     });
     wavesurfers[w].on('play', function () {
        if (wCurrent==-1) wCurrent=0;
     });
  }
  // very deceiving but wavesurfer does not send self-reference on callbacks
  if ( nbwaves > 0 ) wavesurfers[0].on('play', function () {
     wCurrent=0;
  });
  if ( nbwaves > 1 ) wavesurfers[1].on('play', function () {
     wCurrent=1;
  });
  if ( nbwaves > 2 ) wavesurfers[2].on('play', function () {
     wCurrent=2;
  });
  if ( nbwaves > 3 ) wavesurfers[3].on('play', function () {
     wCurrent=3;
  });
  if ( nbwaves > 4 ) wavesurfers[4].on('play', function () {
     wCurrent=4;
  });
  if ( nbwaves > 5 ) wavesurfers[5].on('play', function () {
     wCurrent=5;
  });
  if ( nbwaves > 6 ) wavesurfers[6].on('play', function () {
     wCurrent=6;
  });
  if ( nbwaves > 7 ) wavesurfers[7].on('play', function () {
     wCurrent=7;
  });
  if ( nbwaves > 8 ) wavesurfers[8].on('play', function () {
     wCurrent=8;
  });
  if ( nbwaves > 9 ) wavesurfers[9].on('play', function () {
     wCurrent=9;
  });
  if ( nbwaves > 10 ) wavesurfers[10].on('play', function () {
     wCurrent=10;
  });
});

</script>

</body>
</html>
