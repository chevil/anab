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
        <title></title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="../../css/style.css" />
        <link rel="stylesheet" href="../../css/alertify.core.css" />
        <link rel="stylesheet" href="../../css/alertify.default.css" />
        <link rel="stylesheet" href="../../css/app.css" />
        <link rel="stylesheet" href="../../css/speech.css" />
        <link rel="stylesheet" href="../../css/spinner.css" />
        <link rel="stylesheet" href="../../css/font-awesome.min.css" />
        <link rel="stylesheet" href="../../js/trumbowyg/dist/ui/trumbowyg.css">

        <script type="text/javascript" src="../../js/jquery.min.js"></script>
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script> 
        <script type="text/javascript" src="../../js/wavesurfer.min.js"></script>
        <!-- plugins -->
        <script type="text/javascript" src="../../js/wavesurfer.timeline.min.js"></script>
        <script type="text/javascript" src="../../js/wavesurfer.regions.min.js"></script>
        <script type="text/javascript" src="../../js/wavesurfer.minimap.min.js"></script>
        <script type="text/javascript" src="../../js/wavesurfer.markers.min.js"></script>

        <script type="text/javascript" src="../../js/trivia.js"></script>
        <script type="text/javascript" src="../../js/alertify.min.js"></script>
        <script type="text/javascript" src="../../js/circular-json.js"></script>
        <script type="text/javascript" src="../../js/trumbowyg/dist/trumbowyg.min.js"></script>

        <!-- App -->
        <script type="text/javascript" src="../../js/wlangs.js"></script>
        <script type="text/javascript" src="../../js/sclangs.js"></script>
        <script type="text/javascript" src="app.js"></script>
    </head>

    <body>

      <div id="free-contents">

        <div class="modal fade" id="modal-wait">
           <div class="modal-bdialog modal-dialog">
             <center><strong><h4><br/><br/><div id="message-wait">Loading waveform...<div></h4></strong></center><br/>
               <div class="lds-spinner" id="spinner-global"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
           </div>
        </div>

        <div class="modal fade" id="modal-whisper">
           <div class="modal-bdialog modal-dialog">
            <br/><center><strong><h3>Calling OpenAI whisper</h3></strong></center>
            <div class="modal-content modal-bcontent">
             <div class="lds-spinner" id="spinner-whisper" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-whisper'>
             <h4>You will call OpenAI whisper for an automatic transcription...<br/>
             Your job will be queued and you can come back a few minutes later<br/> 
             to check the result by reloading this page.<br/></h4>
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
                <option value='turbo'>Turbo</option>
             </select><br/><br/>
             <button type="submit" class="btn btn-success btn-block btn-whisper">Call and Pray</button>
             </center>
             </form>
            </div>
           </div>
        </div>

        <div class="modal fade" id="modal-trans-all">
           <div class="modal-tdialog modal-dialog">
            <br/><center><strong><h3>Translation Service from Python Anywhere</h3></strong></center>
            <div class="modal-content modal-tcontent">
             <div class="lds-spinner" id="spinner-trans-all" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-trans-all'><h4><b>
             All annotations in this document will be translated to the desired language(s).<br/>
             Please, indicate the source language and the destination idioma(s)<br/>
             </b></h4></div>
             <form role="form" id="callTRA" name="callTRA" style="transition: opacity 300ms linear; margin: 10px 0;">
             <center>
             <strong>Source</strong>
             <select id='TRAlang'>
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
             <select id='TRAtarget' multiple>
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

        <div class="modal fade" id="modal-trans">
           <div class="modal-tdialog modal-dialog">
            <br/><center><strong><h3>Translation Service from Python Anywhere</h3></strong></center>
            <div class="modal-content modal-tcontent">
             <div class="lds-spinner" id="spinner-trans" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
             <div class='help-whisper' id='help-trans'><h4><b>
             Please, indicate the source language and the destination tongue(s)<br/>
             You can select several target languages.
             </b></h4></div>
             <form role="form" id="callTR" name="callTR" style="transition: opacity 300ms linear; margin: 10px 0;">
             <center>
             <strong>Source</strong>
             <select id='TRlang'>
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
             <select id='TRtarget' multiple>
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

        <div class="modal fade" id="modal-help" role="dialog">
            <div class="modal-dialog modal-hdialog">
                <center><h3>Mini help</h3></center>
                <div class="modal-content modal-hcontent">
                    <p>
                     Select a part of the file to create a region.<br /><br />
                     You can then resize it by moving its border, move it with click-and-drag or remove it by clicking on the upper-right red marker.<br /><br />
                     To edit a note, double-click on a region and a pop-up will appear where you can enter a note in rich text format, save it before closing the pop-up.<br /><br />
                     In edition mode, the region will play in a loop, to resume playing the file normally, close the edition pop-up.<br /><br />
                     In edition mode, you can also add the region to an audio book by clicking on the audiobook icon.<br /><br />
                     Additionnally, you can use the AI transcription ( OpenAI whisper ) by clicking on the whisper icon on all screens : <img src="../../img/whisper-logo.png" width="30px" height="30px" /><br/><br/>
                     And, cherry on the cake!, you can use the translation icon : <img src="../../img/translate.png" width="30px" height="30px" /> to translate to the language(s) of your choice ( multiple ).<br/><br/> After a translation, each line will start with the short code of the language like en, pt, it... and choosing a language in the drop -down menu in the title will only show you one language without abbreviations.<br/><br/>
                     Once you chose a language, you can also export all your notes to a subtitle file (.srt) using the Export button.<br/><br/>
                     Enjoy and shout "In your ***, Elon Musk" each time you save your work !!<br />
                  </p>                              
                 </div>
             </div>
        </div>

        <div class="container" id="container">
            <div class="header" id="archive-header">
                <h3 itemprop="title" id="title"></h3>
                <div id='selectAll' class='select-all'>Select All</div>
                <div id='resetAll' class='reset-all'>Reset All</div>
                <div id='frozen' class='frozen'>Frozen</div>
                <i id="help" class="fa fa-question-circle fa-2x help-question" aria-hidden="true" ></i>
            </div>

            <div id="container-wave" class="outer-wave">
		<div id="subtitle" class="speech">
		    <div id="isubtitle" class="ispeech"></div>
                    <div id="speaker" class="speaker">
                    <div id="ispeaker" class="ispeaker"></div>
                    <i id="sfull" class="fa fa-expand sfull" aria-hidden="true" data-action="pause"></i>
                    </div>
                </div>
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
                <div id="wave-timeline"></div>
                <div id="wave-minimap"></div>
                <div class="modal fade" id="modal-edit" role="dialog">
                  <div class="modal-dialog">
                    <center><h3>Edit Note</h3>
                    <div id="audiobook-div"><i id="audiobook" class="fa fa-book fa-2x" width="30px" height="30px" /></i></div>
                    <div id="whisper-div" class="whisper-div-edit"><img src="../../img/whisper-logo.png" width="30px" height="30px" onclick="whisperStart('')" /></div>
                    <div id="trans-div" class="trans-div-edit"><img src="../../img/translate.png" width="30px" height="30px" onclick="translationStart('')" /></div>
                    <div class="modal-content">
                      <center>
                        <i id="fplay" class="fa fa-play fa-2x" data-action="play-region"></i>  
                      </center>
                      <form role="form" id="edit" name="edit" style="transition: opacity 300ms linear; margin: 30px 0;">
                         <div class="form-group" style="text-align: left;">
                             <label for="note">Note</label>
                             <textarea id="note" class="form-control" name="note"></textarea>
                         </div>
                         <button type="submit" class="btn btn-success btn-block">Save</button>
                         <!--
                         <center><i>or</i></center>
                         <button type="button" class="btn btn-danger btn-block" data-action="delete-region">Delete</button>
                         -->
                      </form>
                    </div>
                  </div>
                </div>

                <div class="modal fade" id="modal-book" role="dialog">
                  <div class="modal-dialog modal-bdialog">
                    <center><h3>Add to Audiobook</h3></center><br/>
                    <div class="modal-content modal-bcontent">
                      <center>
                         <div class="lds-spinner" id="spinner-book" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
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

                <br/><br/>
                <!-- 
                <div class="row" style="width:100%">
                    <center>
                        <i id="backward" class="media-button fa fa-backward fa-2x" data-action="back"></i>
                        <i id="play" class="media-button fa fa-play fa-2x" data-action="play"></i>  
                        <i id="forward" class="media-button fa fa-forward fa-2x" data-action="forth"></i>  
                    </center>
                </div>
                -->
            </div>
            <div id="notes" class="outer-notes">
            </div>
            <div id="linear-notes" class="free-outer-notes"></div>
            <div class="export-notes">
               <button class="btn btn-info btn-block btn-export" data-action="export" title="Export annotations to SRT">
                   <i class="glyphicon glyphicon-file"></i>
                   Export
                </button>
             </div>
        </div>
        <div id="wavecolor" style="display:none;"><?php echo $waveColor; ?></div>
        <div id="progresscolor" style="display:none;"><?php echo $progressColor; ?></div>
        <div id="mapwavecolor" style="display:none;"><?php echo $mapWaveColor; ?></div>
        <div id="mapprogresscolor" style="display:none;"><?php echo $mapProgressColor; ?></div>

      </div> 

    </body>

<script type="text/javascript" >

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
let whisper = <?php echo $_SESSION['whisper']; ?>;

</script>

</html>
