<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	mails.php
*          Date 		: 	
*********************************************************************************/
include("header.php");	

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	$temp = '<script>alert("'.$errPage.'"); top.location="index.php";</script>';
	print $temp;
}

$sFileName	= 'mails.php';
$sFileRef	= 'mails.php';
$title		= 'Surat/Email';
$blueIcon	= '<td><img src="images/orb-blue-bkrm-02.gif" /></td><td>&nbsp;</td>';
?>
<div class="maroon" align="left"><a class="maroon" href="index.php">LAMAN UTAMA</a><b>&nbsp;>&nbsp;<? print strtoupper($title);?></b></div>
<div style="width: 100%; text-align:left">
<div>&nbsp;</div>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
	<tr>
		<td class="Label" valign="top" colspan="3">
		<p><font class="blue"><u><b>SENARAI&nbsp;<? print strtoupper($title);?></b></u></font></p>
		<p>
			<li id="print">&nbsp;&nbsp;<a href="memberList.php">Senarai Anggota</a></li>
			<li id="print">&nbsp;&nbsp;<a href="memberListT.php">Senarai Anggota Berhenti/Bersara</a></li>
			<li id="print">&nbsp;&nbsp;<a href="loanList.php">Senarai Pembiayaan</a></li>
			<li id="print">&nbsp;&nbsp;<a href="dividenList.php">Senarai Dividen</a></li>
		</p>
		<p><hr size=1></p>
		</td>
	</tr>
</table>
</div>
<?
include("footer.php");	
?>
