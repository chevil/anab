<?php

session_start();

if ( !isset($_SESSION['schtroumpf']) || !isset($_SESSION['papa']) )
{
    header( "Location: ../../index.php" );
    exit();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>

        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="../../css/style.css" />
        <link rel="stylesheet" href="../../css/alertify.core.css" />
        <link rel="stylesheet" href="../../css/alertify.default.css" />
        <link rel="stylesheet" href="../../css/spinner.css" />
        <link rel="stylesheet" href="../../css/tabs.css" />
        <link rel="stylesheet" href="../../css/app.css" />
        <link rel="stylesheet" href="../../css/font-awesome.min.css" />
        <link rel="stylesheet" href="../../css/dropzone.css" />
        <link rel="stylesheet" href="../../js/trumbowyg/dist/ui/trumbowyg.css">

        <script type="text/javascript" src="../../js/jquery.min.js"></script>
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script> 

        <script type="text/javascript" src="../../js/trivia.js"></script>
        <script type="text/javascript" src="../../js/alertify.min.js"></script>
        <script type="text/javascript" src="../../js/circular-json.js"></script>
        <script type="text/javascript" src="../../js/dropzone.min.js"></script>
        <script type="text/javascript" src="../../js/trumbowyg/dist/trumbowyg.min.js"></script>

    </head>

    <body background="../../img/background.png">
    <a href="../../index.php"><i class="fa fa-chevron-left fa-1x" aria-hidden="true" style="color: #000000; float:left; margin-left:20px; margin-top:-15px;" ></i></a>

        <center>
        <button id="biography" class="tablinks" onclick="openTab('Biography')">Biography</button>
        <button id="description" class="tablinks" onclick="openTab('Description')">Description</button>
        <button id="free" class="tablinks" onclick="openTab('Free')">Notes</button>
        <button id="linear" class="tablinks" onclick="openTab('Linear')">Transcription</button>
        <button id="documents" class="tablinks" onclick="openTab('Documents')">Documents</button>
        <table width=80%><hr/></table>
        </center>

        <div class="contents-tab">

            <div id="Biography" class="tabcontent">
                <center><h3>Biography</h3></center>
                <div id="biography-edit"></div>
            </div>

            <div id="Description" class="tabcontent">
                <center><h3>Description</h3></center>
                <div id="description-edit"></div>
            </div>

            <div id="Free" class="tabcontent">
                <iframe id="free-frame" src="free-notes.php" width=100% height=800px></iframe>
            </div>

            <div id="Linear" class="tabcontent">
                <iframe id="linear-frame" src="linear-notes.php" width=100% height=800px></iframe>
            </div>

            <div id="Documents" class="tabcontent">
                <div class="upload-header" id="upload-header">
                   <center><h3>Documents</h3></center>
                   <i class="fa fa-upload fa-2x upload-button" aria-hidden="true" onclick="showUpload()"></i>
                   <div class="upload-content" id="upload-content">
                </div>
                <div class="modal fade" id="modal-upload"  role="dialog">
                    <div class="modal-dialog modal-udialog">
                        <center><b>Upload Documents</b></center>
                        <div class="modal-content modal-ucontent">
                           <form id="upload-zone" class="dropzone">
                              <input name="url" id="upurl" type="hidden" value=""/>
                           </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>

<script type="text/javascript">

var soundfile = '__file_url__';
var firstTime = true;

var openTab = function(name) {
  $(".tabcontent").css("display","none");
  $(".tablinks").removeClass("active");
  $("#"+name).css("display","block");
  $("#"+name.toLowerCase()).addClass("active");
  if ( name == "Linear" ) {
     // $("#modal-wait").modal("hide");
     if ( firstTime ) {
        // $("#linear-frame").attr("src","linear-notes.php");
        firstTime = false;
     }
  }
  if ( name == "Free" ) {
     // $("#modal-waitl").modal("hide");
     // $("#free-frame").attr("src","free-notes.php");
  }
} 

var getParameterByName = function(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return 0;
    if (!results[2]) return 0;
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

var fullEncode = function(w)
{
 var map=
 {
          '&': '%26',
          '<': '%3c',
          '>': '%3e',
          '"': '%22',
          "'": '%27'
 };

 var encodedW = encodeURI(w);
 return encodedW.replace(/[&<>"']/g, function(m) { return map[m];});
}

var sstart = getParameterByName( "start" );
var user = '<?php echo $_SESSION['schtroumpf']; ?>';
var ucolor = '<?php echo $_SESSION['color']; ?>';

Dropzone.autoDiscover = false;

var showUpload = function() {
    $("#modal-upload").modal("show");
} 

var HRSize = function(bytes, si=false, dp=1) {
  const thresh = si ? 1000 : 1024;
  if (Math.abs(bytes) < thresh) {
    return bytes + ' B';
  }
  const units = si 
    ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'] 
    : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
  let u = -1;
  const r = 10**dp;
  do {
    bytes /= thresh;
    ++u;
  } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);
  return bytes.toFixed(dp) + ' ' + units[u];
}

var getUploads = function() {
    console.log("soundfile :" + soundfile);
    var jqxhr = $.post( {
       url: '../../get-uploads.php',
       data: {
          url: encodeURIComponent(soundfile),
       },
       dataType: "application/json" 
    }, function(data) {
       console.log("got uploads : " + JSON.stringify(data.responseText));
    }).fail(function(data) {
       if ( data.status === 200 ) {
         // console.log( "got uploads : " + JSON.stringify(data));
         var uploads = JSON.parse(data.responseText);
         if ( uploads == null ) {
           $("#upload-content").html("<br/><br/><br/><center>No documents have been uploaded.</center>");
         } else {
           $("#upload-content").html("");
           $tabhtml = "<table border=2px width=100%>";
           $tabhtml += "<tr><th>Name</th><th>Type</th><th>Size</th><th>Download</th><th>Delete</th></tr>";
           $.each(uploads, function (id, upload) {
              $tabhtml += "<tr><td>"+upload['uri'].replaceAll("uploads/","")+
                          "</td><td>"+upload['type']+
                           "</td><td>"+HRSize(upload['size'])+"</td>"+ 
                           "</td><td><a href='../../"+fullEncode(upload['uri'])+"' target='_blank'><center><i class='fa fa-download fa-1x'></i></center></a></td>"+
                           "</td><td><a href='javascript:deleteUpload(\""+fullEncode(upload['uri'])+"\")'><center><i class='fa fa-trash-o fa-1x'></i></center></a></td></tr>";
           });
           $tabhtml += "</table>";
           $("#upload-content").html($tabhtml);
         }
       } else {
         alertify.alert("Couldn't get documents : " + data.responseText );
       }
    });
}

var deleteUpload = function(uri) {
    var jqxhr = $.post( {
       url: '../../delete-upload.php',
       data: {
          uri: uri,
       },
       dataType: "text/html" 
    }, function(data) {
       console.log("delete returned : " + JSON.stringify(data));
    }).fail(function(data) {
       if ( data.status === 200 ) {
         alertify.alert("The document has been deleted.");
         getUploads();
       } else {
         alertify.alert("Couldn't delete document : " + data.responseText );
       }
    });
}

$(document).ready( function(){

    $("#upurl").val(encodeURIComponent(soundfile));

    var jqxhr = $.post( {
       url: '../../get-title.php',
       data: {
          url: encodeURIComponent(soundfile),
       },
       dataType: "text/html" 
    }).fail(function(data) {
       if ( data.status === 200 ) {
          $(document).attr('title',data.responseText);
       } else {
          console.log("getting title failed : " + JSON.stringify(data));
          alertify.alert("getting title failed : " + JSON.stringify(data));
       }
    });

    var jqxhr = $.post( {
       url: '../../get-biography.php',
       data: {
          url: encodeURIComponent(soundfile),
       },
       dataType: "text/html" 
    }).always(function(data) {
       if ( data.status === 200 ) {
          // console.log( "getting biography success : " + data.responseText );
          $('#biography-edit').html(data.responseText);
          $('#biography-edit').trumbowyg({
             autogrow: true
          })
          .on('tbwchange', function(e){
             var jqxhr = $.post( {
                 url: '../../save-biography.php',
                 data: {
                     url: encodeURIComponent(soundfile),
                     biography: $('#biography-edit').trumbowyg('html').replaceAll("<p>","<div>").replaceAll("</p>","</div>")
                   },
                   dataType: 'text/plain'
                 }, function() {
                   console.log( "saving biography succeeded" );
                 }).fail(function(error) {
                   if ( error.status === 200 ) {
                      console.log( "saving biography success");
                   } else {
                      console.log("saving biography failed : " + JSON.stringify(error));
                      alertify.alert("saving biography failed : " + JSON.stringify(error));
                   }
                 });
          });

       } else {
          console.log("getting biography failed : " + JSON.stringify(data));
          alertify.alert("getting biography failed : " + JSON.stringify(data));
       }
    });

    var jqxhr = $.post( {
       url: '../../get-description.php',
       data: {
          url: encodeURIComponent(soundfile),
       },
       dataType: "text/html" 
    }).always(function(data) {
       if ( data.status === 200 ) {
          // console.log( "getting description success : " + data.responseText );
          $('#description-edit').html(data.responseText);
          $('#description-edit').trumbowyg({
             autogrow: true
          })
          .on('tbwchange', function(e){
             var jqxhr = $.post( {
                 url: '../../save-description.php',
                 data: {
                     url: encodeURIComponent(soundfile),
                     description: $('#description-edit').trumbowyg('html').replaceAll("<p>","<div>").replaceAll("</p>","</div>")
                   },
                   dataType: 'text/plain'
                 }, function() {
                   console.log( "saving description succeeded" );
                 }).fail(function(error) {
                   if ( error.status === 200 ) {
                      console.log( "saving description success");
                   } else {
                      console.log("saving description failed : " + JSON.stringify(error));
                      alertify.alert("saving description failed : " + JSON.stringify(error));
                   }
                 });
          });

       } else {
          console.log("getting description failed : " + JSON.stringify(data));
          alertify.alert("getting description failed : " + JSON.stringify(data));
       }
    });

    $("#upload-zone").dropzone( {
        url: "../../upload-document.php",
        method: "post",
        paramName: "file", // The name that will be used to transfer the file
        addRemoveLinks: false,
        maxFilesize: 100, // MB
        dictDefaultMessage: "Upload Document",
        init: function() {
             this.on("success", function(file, response) { 
                 getUploads();
             });
        }
    });

    openTab("Free");

    getUploads();
});

</script>

</html>
