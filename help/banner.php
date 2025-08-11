<? 
include "common.php";
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
<title>Online Usermanual</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../images/default.css">
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

<body bgcolor="#f0f0f0">

<!--div id="Layer1" style="position:absolute; left:92px; top:45px; width:860px; height:61px; z-index:1"><img src="images/ONLINE_USERM_03086.gif" width="750" height="60"></div-->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td class="bgblue"><img src="../images/banner-sekatarakyat.gif" width="480" height="60"></td>
	<td class="bgblue" align="right"><div style="font-size:24px; font-style:italic; color:#4b77b6; width:320px; margin: 10px;">ONLINE USERMANUAL</font></div><!--img src="images/ONLINE_USERM_03086.gif" width="750" height="60"--></td>
  </tr>
  <tr>
    <td height="45" valign="top" colspan="2">
		<div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-image: url(../images/shade-bkrm-01.gif)">
        <!--DWLayoutTable-->
        <form action="search.php" method="post" target="mainFrame" onSubmit="return checkInput()" name="frmSearch">
          <tr> 
		    <td width="1"><img src="../images/shade-bkrm-01.gif" width="1" height="24"></td>
            <td align="left" valign="middle">
			<div style="width: 190px;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td>&nbsp;&nbsp;</td>
					<td><img src="images/atb_calendar.gif" width="18" height="15"></td>
					<td>&nbsp;&nbsp;</td>
					<td><? echo $hari .", "; echo date("d")." ".displayBulan(date("m"))." ".date("Y"); ?></td>
				</tr>
			</table>
			</div>
			</td>
            <td align="left" valign="middle">
			<div style="width: 300px;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td valign="middle"><img src="images/atb_search.gif" width="15" height="15">&nbsp;&nbsp;</td>
					<td valign="middle">Carian&nbsp;&nbsp;</td>
					<td valign="middle"><input name="txtKey" type="text" class="formobj" maxlength="50" size="25">&nbsp;&nbsp;</td>
					<td valign="middle"><input type="submit" name="Submit" value="Cari" class="formObj"></td>
				</tr>
			</table>
			</div>
			</td>
            <td align="right">
			<div style="width: 300px;">
				<table cellpadding="2" cellspacing="0">
				<td><div class="black">|</div></td>
				<td><div class="black"><a href="../index.php?action=laman_utama" target="_parent" class="black"><b>Laman Utama</b></a></div></td>
				<td><div class="black">|</div></td>
				<td><div class="black"><a href="menu.php" target="mainFrame" class="black"><b>Menu</b></a></div></td>
				<td><div class="black">|</div></td>
				<td><div class="black"><a href="../index.php?action=hubungi_kami" target="_parent" class="black"><b>Hubungi Kami</b></a></div></td>
				<td><div class="black">|</div></td>
				</tr>
				</table>
				<!--a href="menu.php" target="mainFrame">Menu</a> | <a href="upload.php" target="mainFrame">Upload</a-->
			</div>
			</td>
			<td width="1"><img src="../images/shade-bkrm-01.gif" width="1" height="24"></td>
          </tr>
        </form>
		</table>
	  	</div>
		<div>
		<table width="100%" cellpadding="0" cellspacing="0" style="background-image: url(../images/shade-bkrm-02.gif);">
		<tr>
		<td width="1"><img src="../images/shade-bkrm-02.gif" width="1" height="21"></td>
		<td>&nbsp;</td>
		<td width="1"><img src="../images/shade-bkrm-02.gif" width="1" height="21"></td>
		</tr>
		</table>
		</div>
	  </td>
  </tr>
</table>
</body>
<script>
function checkInput() {
	//alert();
	/*if (document.frmSearch.txtKey.value=='') {
		alert("Tiada perkataan dimasukkan");
		return false;
	} else
		return true;*/
}
function showCalendar(frm,textbox) {
		window.open("Calendar.php?frm=" + frm + "&txt=" + textbox,"DatePicker","width=250,height=230,status=no,resizable=no,top=270,left=730");
}
</script>
</html>

