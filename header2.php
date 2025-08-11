<?php
/*********************************************************************************
*          Project		:	Sistem e-Koperasi(e-Koop) iKOOP
*          Filename		: 	header.php
*          Date 		: 	12/09/2003
*********************************************************************************/
include ("common.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><? print $emaNetis;?></title>
<script>
window.status="Sistem Keanggotaan iKOOP versi 1.01";
</script>
<meta name="Keywords"  content="<?=$siteKeyword?>">
<meta name="Description" content="<?=$siteDesc?>">
<meta name="GENERATOR" content="<?=$yVZcSz2OuGE5U?>">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="images/default.css" >
</head>

<body>
<!-- <img src="images/banner-ikoop1.png" width="480" height="60" />  -->
<!--DISPLAY BANNER!-->
<div class="none">
<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#008080">
<tr>
	<td width="23%"></td>
	<td width="77%" align="right">

	<div style="font-size:16px; font-style:italic; color:#ffffff; width:320px; padding: 10px;">
	<b>SELAMAT DATANG KE ONLINE iKOOP-DEMO</b></font></div></td>
</tr>
</table>
</div>

<div class="none">
<table width="100%" cellpadding="0" cellspacing="0" style="background-image: url(images/shade-bkrm-01.gif)">
	<tr>
	<td width="1"><img src="images/shade-bkrm-01.gif" width="1" height="24"></td>
	<td>
		<div class="none" align="left">
		<table cellpadding="2" cellspacing="0">
			<tr>
				<td>|</td>
				
		<?php 	
				if (get_session("Cookie_userID") <> "") {
					print '<td><b>'.get_session("Cookie_fullName").'</b>
							<td><div class="black">|</div></td>
							<td class="navmain"><font class="blue">
				<a href="logout.php" onClick="return confirm(\'Adakah anda Pasti?\')">
								<b>Keluar</b></a>
						</font>
				</td>';
				
				} else {
					print '<td>&nbsp;<b>Pelawat</b></td>';			
				}
		?>
			<td><div class="black">|</div></td>
			</tr>
		</table>
		
		</td>
	<td>
	<div class="none" align="right">

	<table cellpadding="2" cellspacing="0">
		<tr>
		<td><div class="black">|</div></td>
	<?php
			if (get_session("Cookie_userID") <> "") {
	?>
				<td><div class="black"><a href="mainpage.php" class="black" target="mainFrame"><b>Laman Utama</b></a></div></td>
	<?php
			} else  {
	?>

				<td><div class="black"><a href="../index.php" class="black" target="_parent"><b>Home</b></a></div></td>
<td><div class="black">|</div></td>

				<td><div class="black"><a href="mainpage.php" class="black" target="mainFrame"><b>Login</b></a></div></td>
	<?php
			}
	?>
		<td><div class="black">|</div></td>
		<td><div class="black"><a href="contact.php" class="black" target="mainFrame"><b>Hubungi Kami</b></a></div></td>
		<td><div class="black">|</div></td>
		</tr>
	</table>
	</div>
	</td>
	<td width="1"><img src="images/shade-bkrm-01.gif" width="1" height="24"></td>
</tr>
</table>
</div>

</body>
</html>