<?php
include("config.php");
include("functions.php");

session_start();

$servroot = "https://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'];

if (!isset($_SESSION['schtroumpf']) || !isset($_SESSION['papa']) )
{
   header("Location: index.php");
   die();
}

if ( isset( $_GET['search'] ) )
{
   $search = strtolower($_GET['search']);
}
else
{
   $search = "";
}

if ( isset( $_GET['start'] ) )
{
   $start = $_GET['start'];
}
else
{
   $start = 0;
}

if ( isset( $_GET['size'] ) )
{
   $size = $_GET['size'];
}
else
{
   $size = 20;
}

$clause = " WHERE ( ( LOWER(title) LIKE '%".addslashes($search)."%' ) OR ( LOWER(author) LIKE '%".addslashes($search)."%' ) OR ( LOWER(collection) LIKE '%".addslashes($search)."%' ) OR ( LOWER(date) LIKE '%".addslashes($search)."%' ) ) ORDER BY ID DESC ";

if (isset($_SESSION['schtroumpf']) && isset($_SESSION['papa']) )
{
   $resallarchives = db_query( "SELECT id FROM archive".$clause );
   $allcount = mysqli_num_rows( $resallarchives );
   $nbpages = intval( $allcount / $size );
   if ( $nbpages*$size < $allcount )
   {
      $nbpages += 1;
   }

   $respageusers = db_query( "SELECT id, uri, url, author, title, collection, date, creator FROM archive ".$clause."LIMIT ".$size." OFFSET ".$start );
}
else
{
   header( "Location: ./index.php" );
}

?>

<html>
<head>
  <meta charset="UTF-8">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/app.css" rel="stylesheet">
  <link href="css/alertify.core.css" rel="stylesheet">
  <link href="css/alertify.default.css" rel="stylesheet">
  <link href="css/all.css" rel="stylesheet">
  <link rel="stylesheet" href="css/font-awesome.min.css" />

  <script src="js/jquery.min.js"></script>
  <script src="js/alertify.min.js"></script>
  <script src="js/sort-table.min.js"></script>

  <script type="text/javascript">

    function editArchive(id) {
      document.location="./edit-archive.php?_id="+id;
    }

    function deleteArchive(id) {
      alertify.confirm( "Are you sure that you want to delete that archive?",
        function (e) {
           if (e) 
           {
              $.post( "delete-archive.php", { _id: id }, function(data) {
                if ( data == "OK" )
                {
                  alertify.alert( "The archive has been deleted.",
                    function () {
                      document.location = "manage-archives.php?start=<?php echo $start; ?>&size=<?php echo $size; ?>&search=<?php echo $search; ?>";
                    }
                  );
                }
                else
                {
                  alertify.alert( data.replace("ERR: ","") );
                }
              })
              .fail(function() {
                 alertify.alert("Couldn't delete archive");
              });
           }
        });
     }

  </script>

</head>

<body background="img/background.png">
<a href="./index.php"><i class="fa fa-home fa-2x" aria-hidden="true" style="color: #999; float:left; margin-left:20px; margin-top:28px;" ></i></a>


<center><table width=90%>
<tr>
<td align=center>
<h1><?php echo $config['project-name']; ?></h1>
</td>
</tr>
</table></center>

<center>
<h1>Audio Archives</h1>
<h3>Count : <?php echo $allcount; ?></h3>
</center>

<?php
print "<form id='search-form' method='get' enctype='multipart/form-data'>";
print "<div class='search'>";
print "Search : ";
print "<input type='text' id='search' name='search' value='".$search."' />";
print "</div>";
print "</form>";
?>
<br/>
<br/>
<center><table width=80% border=0px></table></center>

<div class="pages">
<?php
$page=0;
print "Pages : ";
while ( $page < $nbpages )
{
   print "<a href='manage-archives.php?start=".($page*$size)."&search=".$search."' >".($page+1)."</a>&nbsp;";
   if ( $page%30 == 29 )
   {
      // print "<br/>";
   }
   $page++;
}
?>
</div>
<div>&nbsp;</div>

<center><table class="js-sort-table" width=80% border=2px>

<?php

$count = $start+1;
print "<th align=left>Author</th><th align=left>Title</th><th align=left>Genre / Collection</th><th align=left>Date</th><th align=left>Creator</th><th align=center>Edit</th><th align=center>Notes</th><th align=center>Delete</th>";
while ( $rowuser = mysqli_fetch_row( $respageusers) )
{
   print "<tr><td align=left>".$rowuser[3]."</td>";
   print "<td align=left>".$rowuser[4]."</td>";
   print "<td align=left>".$rowuser[5]."</td>";
   print "<td align=left>".$rowuser[6]."</td>";
   print "<td align=left>".$rowuser[7]."</td>";
   print "<td align=center><a href='javascript:editArchive(".$rowuser[0].");'><img src='img/edit.png' width=20px height=20px /></a></td>";
   // print "<td align=center><a href='".$servroot.dirname($_SERVER['SCRIPT_NAME'])."/".htmlentities($rowuser[1], ENT_QUOTES)."'><img src='img/see.png' width=20px height=20px /></a></td>";
   print "<td align=center><a href='".htmlentities($rowuser[1], ENT_QUOTES)."'><img src='img/see.png' width=20px height=20px /></a></td>";
   print "<td align=center><a href='javascript:deleteArchive(".$rowuser[0].");'><img src='img/delete.png' width=20px height=20px /></a></td>";
   print "</tr>";
   $count++;
}

?>

</table></center>

<script type="text/javascript">
$(document).ready( function(){

});
</script>

<br/><br/><br/>
</body>
</html>
