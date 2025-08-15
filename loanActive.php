<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanActive.php
 *          Date 		: 	28/08/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
if (!isset($strDate))	$strDate = date("d/m/Y");
if ($action == 'Kemaskini') {
	$pk = explode(":", $pk);
	$str = array();
	foreach ($pk as $val) {
		$str[] = "'" . $val . "'";
	}
	$pk = implode(",", $str);
	if (isset($selAktif)) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = '';
		$sWhere = '';
		$sWhere = ' userID  in (' . $pk . ')';
		$sSQL	= ' UPDATE loans ';
		$sSQL	.= ' SET ' .
			' status =' . tosql($selAktif, "Text") .
			' ,updatedBy =' . tosql($updatedBy, "Text") .
			' ,updatedDate=' . tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		$rs = &$conn->Execute($sSQL);
		print 	'
		<script>
			window.location = "memberProfil.php";
		</script>';
		//exit;
	}
}

if (isset($pk)) $pkall = explode(":", $pk);
unset($pk);

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
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">


<table border="0" cellspacing="1" cellpadding="0" width="60%" align="left" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Header" colspan="2">Profil Login sistem.</b></td>
				</tr>';
for ($s = 0; $s < count($pkall); $s++) {
	//foreach($pkall as $pk) {
	if ($s > 0) {
		$pk = $pkall[$s];

		if ($s == 1) {
			$strpk = $pk;
		} else {
			$strpk = $strpk . ":" . $pk;
		}
		$GetUser = ctMember("", $pk);
		if ($GetUser->RowCount() == 0) {
			print '		<tr>
					<td	class="Data" colspan="2" align="center" height="50" valign="middle">- Tiada Maklumat Mengenai Anggota -</b></td>
				</tr>';
			//exit;
		}
		$status			= dlookup("userdetails", "status", "userID=" . tosql($pk, "Text"));
		$memberID		= dlookup("userdetails", "memberID", "userID=" . tosql($pk, "Text"));
		$approvedDate	= dlookup("userdetails", "approvedDate", "userID=" . tosql($pk, "Text"));
		$rejectedDate	= dlookup("userdetails", "rejectedDate", "userID=" . tosql($pk, "Text"));
		$remark			= dlookup("userdetails", "remark", "userID=" . tosql($pk, "Text"));
		print '		<tr>
					<td class="Data">Nombor Anggota</td>
					<td class="DataB">:&nbsp;' . $memberID . '</td>
				</tr>
				<tr>
					<td class="Data">Nama Anggota</td>
					<td class="DataB">:&nbsp;' . $GetUser->fields(name) . '</td>
				</tr>								
				<tr>
					<td class="Data">Tarikh Memohon</td>
					<td class="DataB">:&nbsp;' . toDate("d/m/Y", $GetUser->fields(applyDate)) . '</td>
				</tr>
				<tr><td colspan="2"><hr size=1></td></tr>';
	} //end if
} //end foreach
//------------------------

print ' <tr>
					<td class="Data">&nbsp;</td>
					<td class="DataB">&nbsp;</td>
				</tr>';


print ' <tr>
					<td class="Data">Ubah status login sistem kepada:</td><td class="DataB">:
					<select name="selAktif" class="entry">';
for ($i = 0; $i < count($activeList); $i++) {
	if ($activeVal[$i] <> 3 and $activeVal[$i] <> 4)
		print '		<option value="' . $activeVal[$i] . '">' . $activeList[$i];
}
print '		</select>
		        	</td>
				</tr>		
		       <tr>
					<td class="Data">&nbsp;</td>
					<td class="DataB">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<input type="hidden" name="pk" value="' . $strpk . '">
					<input type="submit" name="action" value="Kemaskini" class="but">&nbsp;<input type="button" name="batal" value="Batal" class="but"  onclick= "Javascript:(window.location.href=\'memberProfil.php\')"></td>
				</tr>';
print ' <tr>
					<td class="Data">&nbsp;</td>
					<td class="DataB">&nbsp;</td>
				</tr>';


print '		</table>
		</td>
	</tr>
</table>';

print '</form>';
include("footer.php");

print '</body>
</html>';
