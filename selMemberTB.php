<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	selMember.php
 *          Date 		: 	06/10/2003
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 25;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";

$sSQL = "SELECT * FROM transaction"; // WHERE yrmnt =".$yrmnt;

$rs = &$conn->Execute($sSQL);

$GetMember = ctMemberStatusDept($q, $by, "1", $dept);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

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
	function selMemberTB(yrmnt)
	{	
		window.opener.document.MyForm.yrmnt.value = yrmnt;	
			
		window.close();
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td	class="Header" colspan="2">Senarai Anggota</b></td>
				</tr>
				<tr>
					<td class="Data">
						Carian melalui 
						<select name="by" class="Data">';
if ($by == 1)	print '<option value="1" selected>Tahun Bulan</option>';
else print '<option value="1">Tahun Bulan</option>';
//if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>'; 	else print '<option value="2">Nama Anggota</option>';				
//if ($by == 3)	print '<option value="3" selected>No KTP Baru</option>'; 	else print '<option value="3">No KTP Baru</option>';							
print '	</select>
				<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
		       	<input type="submit" class="but" value="Cari">';

print '	</select>
					</td>
				</tr>';
if ($q == "") {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan Tahun Bulan -</b>
				</td></tr>';
} else {
	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="Data">
					<tr>
						<td class="header" nowrap>&nbsp;</td>						
						<td class="header" width=80>&nbsp;Nomor Anggota</td>
						<td class="header" >&nbsp;Nama</td>						
					</tr>';
		while (!$GetMember->EOF && $cnt <= $pg) {
			$userid		= $GetMember->fields(userID);
			$memberid	= $GetMember->fields(memberID);
			$name		= $GetMember->fields(name);
			//$newic		= $GetMember->fields(newIC);
			//$oldic		= $GetMember->fields(oldIC);
			//$jabatan 	= $GetMember->fields(departmentID);
			//					$jumlahUnit = ctNumberShare($userid);
			//					$jumlahUnit = $jumlahUnit + $GetMember->fields(unitShare); 
			$jumlahUnit = $GetMember->fields(totalShare); // grab from userdetails (totalshare)
			print '
					<tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '</a></td>
						<td class="Data">&nbsp;<a href="javascript:selAnggota(\'' . $userid . '\',\'' . $memberid . '\',\'' . $name . '</a></td>
											
					</tr>';
			$cnt++;
			$bil++;
			$GetMember->MoveNext();
		}
		print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
		if ($TotalRec > $pg) {
			print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
			if ($TotalRec % $pg == 0) {
				$numPage = $TotalPage;
			} else {
				$numPage = $TotalPage + 1;
			}
			print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>';

		print '
				</td>
			</tr>
				</table>
				
						</td>
					</tr>';
	} else {
		print '
					<tr><td	class="Label" align="center" height=50 valign=middle>
						<b>- Tiada rekod mengenai anggota  -</b>
					</td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';
