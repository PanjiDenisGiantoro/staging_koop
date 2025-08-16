<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selToMember.php
 *          Date 		: 	06/10/2003
 *		   Used By		:	transAddUpdate.php
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

$GetDept = ctGeneral("", "B");

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<script language="JavaScript">
	function selAnggota(userid,memberid,name)
	{	
		window.opener.document.MyForm.sellUserID.value = userid;	
		window.opener.document.MyForm.sellMemberID.value = memberid;	
		window.opener.document.MyForm.sellUserName.value = name;	
		window.close();
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Header" colspan="2">Klik pada Jabatan dan klik pada Nombor Anggota Atau Nama untuk pilihan.</b></td>
				</tr>';
$totalUser = 0;
if ($GetDept->RowCount() <> 0) {
	print '		<tr>
					<td class="Data"><ul>';
	while (!$GetDept->EOF) {
		$GetUserDept = ctUserDept("ALL", $GetDept->fields(ID));
		print '	<li class="Data"><a href="#" onclick="return toggleMenu(\'' . $GetDept->fields(ID) . '\')">' .
			$GetDept->fields(name) . '</a>&nbsp;&nbsp;&nbsp;';
		if ($GetUserDept->RowCount() <> 0) print '-	(' . $GetUserDept->RowCount() . ')';
		print '	</li><span style="display:none;" id="' . $GetDept->fields(ID) . '">';
		$totalUser = $totalUser + $GetUserDept->RowCount();
		if ($GetUserDept->RowCount() <> 0) {
			print '
			<table border="0" cellpadding="2" cellspacing="1" width="500" class="Data">
				<tr>
					<td class="header">&nbsp;Nombor Anggota</td><td class="header">&nbsp;Nama</td>
				</tr>';
			while (!$GetUserDept->EOF) {
				$userid		= $GetUserDept->fields(userID);
				$memberid	= $GetUserDept->fields(memberID);
				$name		= $GetUserDept->fields(name);
				print '
				<tr>
					<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\');">' . $memberid . '</a></td>
					<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '\');">' . $name . '</a></td>
				</tr>';
				$GetUserDept->MoveNext();
			}
			print '
			</table>';
		}
		print '	</span>';
		$GetDept->MoveNext();
	}
	print '			</ul></td>
				</tr>
				<tr><td class="Data"><br><i>Jumlah Keseluruhan Anggota Aktif : <b>' . $totalUser . '</b></i></li></td></tr>';
} else {
	print '
				<tr><td	class="Label" align="center">
					<hr size="1"><b>- Tiada rekod mengenai Kod Cawangan/Zon -</b><hr size="1">
				</td></tr>';
}
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';

print '
<SCRIPT TYPE="TEXT/JAVASCRIPT" LANGUAGE="JAVASCRIPT">
<!-- Hide script from older browsers
	function toggleMenu(currMenu) {
		if (document.getElementById) {
			thisMenu = document.getElementById(currMenu).style
			if (thisMenu.display == "block") {
				thisMenu.display = "none"
			}
			else {
				thisMenu.display = "block"
			}
			return false
		}
		else {
			return true
		}
	}

// End hiding script -->
</SCRIPT>';
