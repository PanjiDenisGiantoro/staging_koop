<? 
include "common.php";
session_start();
//print $_SESSION['user'].'<br>';
//print $_SESSION['iduser'].'<br>';
//print $_SESSION['parent'].'<br>';
//$conn->debug = true;
$today = date("D");
if ($today=="Mon")
	$hari = "Isnin";
elseif ($today=="Tue")
	$hari = "Selasa";
elseif ($today=="Wed")
	$hari = "Rabu";
elseif ($today=="Thu")
	$hari = "Khamis";
elseif ($today=="Fri")
	$hari = "Jumaat";
elseif ($today=="Sat")
	$hari = "Sabtu";
elseif ($today=="Sun")
	$hari = "Ahad";

$sql = "SELECT * FROM kandungan";
$rs = $conn->Execute($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="default.css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body bgcolor="#CCFFFF" topmargin="0" leftmargin="0" rightmargin="0">

<div id="Layer1" style="position:absolute; left:92px; top:45px; width:860px; height:61px; z-index:1"><img src="images/ONLINE_USERM_03086.gif" width="750" height="60"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td width="100%" height="66" valign="top"><img src="images/online.jpg" width="1033" height="103"></td>
  </tr>
  <tr> 
    <td height="45" valign="top"><table width="100%" height="43" border="0" cellpadding="1" cellspacing="1" background="images/bgB.gif" bgcolor="#CFD2E9">
        <!--DWLayoutTable-->
        <form action="search.php" method="post" target="mainFrame" onSubmit="return checkInput()" name="frmSearch">
          <tr  class="blueText"> 
            <td width="223" height="41" valign="top"><div align="center">&nbsp;&nbsp;<img src="images/atb_calendar.gif" width="18" height="15">&nbsp;<i><? echo $hari .", "; echo date("d")." ".displayBulan(date("m"))." ".date("Y"); ?></i>&nbsp;&nbsp;</div></td>
            <td width="600" valign="top"><div align="center"><img src="images/atb_search.gif" width="15" height="15">&nbsp;&nbsp;&nbsp;<font color="#000000">Carian</font> 
                <input name="txtKey" type="text" class="formobj" size="50">
                &nbsp;&nbsp; <font color="#000000"> 
                <!--Jenama!-->
                </font> 
                <!--
                <select name="selectSumber" class="formObj">
                  <option selected value="">--Pilih Jenama--</option>
                  <? while (!$rs->
                EOF) { print ' 
                <option value="'.$rs->fields(ID).'">'.$rs->fields(text).'</option>
                '; $rs->MoveNext(); } ?> 
                <option value="">--</option></select>
                !--> 
                <input type="submit" name="Submit" value="Cari" class="formObj">
              </div></td>
            <td width="220" valign="top"><? print '<a href="menu.php" target="mainFrame">Menu</a> | <a href="upload_images.php" target="mainFrame">Upload</a>'; ?> </div></td>
  <td width="200" valign="top"><div align="center">[<? print $_SESSION['user'];?>]<strong><a href="index.php?log=out" target="_parent" class="style7 style11">Sign Out</a></strong></div></td>         </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>
<script>
function checkInput(){
	//alert();
	/*if (document.frmSearch.txtKey.value=='') {
		alert("Tiada perkataan dimasukkan");
		return false;
	} else
		return true;*/
}
function showCalendar(frm,textbox){
		window.open("Calendar.php?frm=" + frm + "&txt=" + textbox,"DatePicker","width=250,height=230,status=no,resizable=no,top=270,left=730");
	}
</script>
</html>

