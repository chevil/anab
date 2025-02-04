<?php

include("../../config.php");
include("../../functions.php");
include("../../trlangs.php");

session_start();

if ( !isset($_SESSION['schtroumpf']) || !isset($_SESSION['papa']) )
{
    header( "Location: ../../index.php" );
    exit();
}

header('Content-Security-Policy: frame-ancestors '.$_SERVER['HTTP_HOST']);

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

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <!-- Bootstrap -->
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="../../css/style.css" />
        <link rel="stylesheet" href="../../css/app.css" />
        <link rel="stylesheet" href="../../css/alertify.core.css" />
        <link rel="stylesheet" href="../../css/alertify.default.css" />
        <link rel="stylesheet" href="../../css/speech.css" />
        <link rel="stylesheet" href="../../css/spinner.css" />
        <link rel="stylesheet" href="../../css/font-awesome.min.css" />

        <script type="text/javascript" src="../../js/jquery.min.js"></script>
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script> 
        <script type="text/javascript" src="../../js/wavesurfer.min.js"></script>
        <script type="text/javascript" src="../../js/wavesurfer.regions.min.js"></script>
        <script type="text/javascript" src="../../js/wavesurfer.markers.min.js"></script>

        <script type="text/javascript" src="../../js/trivia.js"></script>
        <script type="text/javascript" src="../../js/alertify.min.js"></script>
        <script type="text/javascript" src="../../js/circular-json.js"></script>

        <!-- App -->
        <script type="text/javascript" src="../../js/wlangs.js"></script>
        <script type="text/javascript" src="../../js/sclangs.js"></script>
        <script type="text/javascript" src="appl.js"></script>
    </head>

    <body>

       <div class="modal fade" id="modal-waitl">
           <div class="modal-bdialog modal-dialog">
             <center><strong><h4><br/><br/><div id="message-waitl">Loading waveform...</div></h4></strong></center><br/>
               <div class="lds-spinner" id="spinner-global"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
           </div>
        </div>

        <div class="modal fade" id="modal-help" role="dialog">
            <div class="modal-dialog modal-dialog">
                <center><h3>Mini help</h3></center>
                <div class="modal-content modal-content">
                    <p>
                     In this mode, the archive is automatically divided in sections delimited by silence as it is thought to produce transcriptions or translations that you can download with the "Export" button at the bottom of the page.<br /><br />
                     but, these sections, who can seem to be aleatory can still be modified : <br />
                     Kein Pank Auf Der Titanik, you can still :<br /><br />
                     <ul>
                     <li>Create a new caesura by double-clicking on any point, <br />this will break the current section in two parts.<br />
                     <li>Resize a section by dragging one of its border.<br />
                     <li>Move a section with click-and-drag.<br />
                     <li>Remove a section by clicking on the upper-right red marker.
                     </ul><br />
                     When you select a section, a red bordered box appear at the bottom of the screen where you can enter your transcription or translation : <br />
                     If you want to enter a translation, you have to start your line with the abbreviation of the language. For example : "es: Esta conferencia trata de explicar ..." would be considered as Spanish, if nothing is specified, it is considered as a transcription.<br /><br />
                     You can enter multiple translations this way and the user can choose his language using the right side "Language" drop-down menu.<br /><br /> 
                     You can also export the translations to a specific language as a subtitles file (SRT) using the "Export" button at the bottom of the page, after having selected your language before.<br /><br /> 
                     As in notes mode, you can add a region to an audiobook using the book icon next to its box in the list of transcriptions/translations.<br /><br /> 
                     Finally, cherry on the cake, you can also use the helpers to make the machine work instead of doing it yourself, but, maybe, it will not be 100% accurate and you might have to modify the result by hand, namingly :
                     <br/>
                     <ul>
                     <li>The OpenAI whisper transcription tool with the whisper icon : <img src='../../img/whisper-logo.png' width='30' height='30' /></li>
                     <li>note: this is very ressources consuming on your server, so use it sparingly!!</li>
                     <br/>
                     <li>The python anywhere free translation API ( Google translation engine ) <br/>with the translation icon : <img src='../../img/translate.png' width='30' height='30' /></li>
                     </ul>
                     Enjoy and shout "In your ***, Elon Musk !!" each time you save your work !!<br /><br /> 
                 </p>
                 </div>
             </div>
        </div>

        <div class="modal fade" id="modal-whisper">
           <div class="modal-bdialog modal-dialog">
            <br/><center><strong><h3>Calling OpenAI whisper</h3></strong></center>
            <div class="modal-content modal-bcontent">
             <div class="lds-spinner" id="spinner-whisper" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-whisper'><h4>
             You will call OpenAI whisper for an automatic transcription...<br/>
             Your job will be queued and you can come back a few minutes later<br/>
             to check the result by reloading this page.</h4>
             </div>
             <form role="form" id="callAI" name="callAI" style="transition: opacity 300ms linear; margin: 10px 0;">
             <center>
             <strong>Language</strong>
             <select id='AIlang'>
                <option value='None'>None</option>
             </select>
             <strong>Model</strong>
             <select id='AImodel'>
                <option value='small'>Small</option>
                <option value='turbo'>Turbo (default)</option>
             </select><br/><br/>
             <button type="submit" class="btn btn-success btn-block btn-whisper">Call and Play</button>
             </center>
             </form>
            </div>
           </div>
        </div>

        <div class="modal fade" id="modal-trans">
           <div class="modal-tdialog modal-dialog">
            <br/><center><strong><h3>Translation Service from Python Anywhere</h3></strong></center>
            <div class="modal-content modal-tcontent">
             <div class="lds-spinner" id="spinner-trans" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-trans'><h4><b>
             Please, indicate the source language and the destination tongue(s)<br/>
             You can select several target languages.
             </b></h4></div>
             <form role="form" id="callTRl" name="callTR" style="transition: opacity 300ms linear; margin: 10px 0;">
             <center>
             <strong>Source</strong>
             <select id='TRlangl'>
<?php
             print "<option value='None'>None</option>\n";
             forEach ( $trlangs as $key => $value ) {
                print "<option value='$key'>$value</option>\n";
             }
?>
             </select>
             <br/><br/><center>
             <strong>Translate to ( 1 to many )</strong>
             </center>
             <select id='TRtargetl' multiple>
<?php
             forEach ( $trlangs as $key => $value ) {
                print "<option value='$key'>$value</option>\n";
             }
?>
             </select><br/><br/><br/>
             <button type="submit" class="btn btn-success btn-block btn-whisper">Translate now!</button>
             </center>
             </form>
            </div>
           </div>
        </div>

        <div class="modal fade" id="modal-trans-alll">
          <div class="modal-tdialog modal-dialog">
            <br/><center><strong><h3>Translation Service from Python Anywhere</h3></strong></center>
            <div class="modal-content modal-tcontent">
             <div class="lds-spinner" id="spinner-trans-alll" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-trans-all'><h4><b>
             All annotations in this document will be translated to the desired language(s).<br/>
             Please, indicate the source language and the destination idioma(s)<br/>
             </b></h4></div>
             <form role="form" id="callTRAl" name="callTRA" style="transition: opacity 300ms linear; margin: 10px 0;">
             <center>
             <strong>Source</strong>
             <select id='TRAlangl'>
<?php
             print "<option value='None'>None</option>\n";
             forEach ( $trlangs as $key => $value ) {
                print "<option value='$key'>$value</option>\n";
             }
?>
             </select>
             <br/><br/><center>
             <strong>Translate to ( 1 to many )</strong>
             </center>
             <select id='TRAtargetl' multiple>
<?php
             forEach ( $trlangs as $key => $value ) {
                print "<option value='$key'>$value</option>\n";
             }
?>
             </select><br/><br/>
             <button type="submit" class="btn btn-success btn-block btn-whisper">Translate now!</button>
             </center>
             </form>
            </div>
          </div>
        </div>

        <div class="container">
            <div class="header" id="archive-header">
                <h3 itemprop="title" id="title"></h3>
                <div id='selectAlll' class='select-all'>Select All</div>
                <div id='resetAlll' class='reset-all'>Reset All</div>
                <div id='frozenl' class='frozen'>Frozen</div>
                <i id="help" class="fa fa-question-circle fa-2x help-question" aria-hidden="true" ></i>
            </div>

            <div id="demo" class="outer-wave-full">
                <table width=100%>
                <tr>
                <td>
                  <div id="ptime" class="play-time"></div>
                </td>
                <td align=center>
                    <div>
                       <span id="zlabel" class="zoom-label">Zoom</span>
                       <!-- <i id="zplus" class="glyphicon glyphicon-zoom-in float-center"></i> -->
                       <input id="zoomZoom" data-action="zoom" class="float-center" type="range" min="1" max="500" value="0" style="width: 100px" />
                       <!-- <i id="zminus" class="glyphicon glyphicon-zoom-out float-center"></i> -->
                    </div>
                </td>
                <td align=center>
                    <div id="slabel" class="speed-label">Speed</div>
                     <i id="sminus" class="fa fa-minus-square-o fa-2x" width=20px height=20px ></i>
                     <i id="splus" class="fa fa-plus-square-o fa-2x" width=20px height=20px ></i>
                     <div id="svalue"></div>
                </td>
                </tr>
                </table>

                <div id="waveform"></div>
                <div id="subtitle" class="linear-subtitle"></div>
                <div id="subtitle-left" class="linear-subtitle-left"></div>
                <div id="linear-notes" class="linear-outer-notes"></div>
                <div class="export-notes" id="export-subtitles" onclick="exportSRT()">
                  <button class="btn btn-info btn-block btn-export" data-action="export" title="Export annotations to SRT">
                   <i class="glyphicon glyphicon-file"></i>
                   Export
                  </button>
                </div>
                <br/><br/>

                <div class="modal fade" id="modal-book" role="dialog">
                  <div class="modal-dialog modal-bdialog">
                    <center><h3>Add to Audiobook</h3></center><br/>
                    <div class="modal-content modal-bcontent">
                      <center>
                         <div class="lds-spinner" id="spinner-modal"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                      </center>
                      <form role="form" id="addbook" name="addbook" style="transition: opacity 300ms linear; margin: 30px 0;">
                         <div class="form-group">
                             <label for="oldbook">Add To Existing Book</label>
                             <select id="oldbook" name="oldbook">
                                <option value="none">None</option>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="newbook">Create New Book</label>
                             <input class="form-control" id="newbook" name="newbook" />
                         </div>
                         <button type="submit" class="btn btn-success btn-block">Add</button>
                      </form>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div id="wavecolor" style="display:none;"><?php echo $waveColor; ?></div>
        <div id="progresscolor" style="display:none;"><?php echo $progressColor; ?></div>
        <div id="mapwavecolor" style="display:none;"><?php echo $mapWaveColor; ?></div>
        <div id="mapprogresscolor" style="display:none;"><?php echo $mapProgressColor; ?></div>
    </body>

<script type="text/javascript" >

let whisper = <?php echo $_SESSION['whisper']; ?>;

function getParameterByName(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return 0;
    if (!results[2]) return 0;
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

var sstart = getParameterByName( "start" );
var user = '<?php echo $_SESSION['schtroumpf']; ?>';
var ucolor = '<?php echo $_SESSION['color']; ?>';

</script>

</html>
