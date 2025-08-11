<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA4.php
 *		   Description	:	
 *          Date 		: 	1/6/06
 *********************************************************************************/
session_start();
if (!isset($dept))		$dept = "ALL";

include("common.php");
include("koperasiinfo.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Anggota Masih Berkhidmat';

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
	//array_push ($deptList, 'Bersara');
	//array_push ($deptVal, 'BSR');
}

$sSQL = "";
$sSQL = "SELECT	a.name, a.loginID, a.email, CAST( b.memberID AS SIGNED INTEGER ) as memberID,
		 b.approvedDate, b.newIC, b.oldIC, c.name as department  
		 FROM 	users a, userdetails b
		 INNER JOIN general c
		 ON		c.ID = b.departmentID 
		 WHERE  a.userID = b.userID 
		 AND 	b.status = '1' ";
if ($dept <> "ALL")
	$sSQL .= " AND b.departmentID  = " . tosql($dept, "Number");
$sSQL .= " ORDER BY memberID";
$rs = &$conn->Execute($sSQL);
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/default.css" >		
</head>
<body>';
print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<p class="textFont">Pilihan Cawangan/Zon
		<select name="dept" class="textFont" onchange="document.MyForm.submit();">
			<option value="ALL">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '</select>
</p>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="2" cellspacing="1" align=left width="100%">';
$tempDept = '';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		if (($dept <> 'ALL')) {
			if ($tempDept <> $rs->fields(department)) {
				if ($tempDept <> "") {
					print '
								<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
									<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
								</tr>';
				}
				print '
							<tr><td colspan="7"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">
							Cawangan/Zon : ' . $rs->fields(department) . '</td></tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<td nowrap>&nbsp;</td>
								<td nowrap align="center">Nombor Anggota</td>
								<td nowrap align="left">Nama</td>
								<td nowrap align="center">Kad Pengenalan</td>
								<td nowrap>Cawangan/Zon</td>
								<td nowrap align="center">Tarikh Keanggotaan</td>
							</tr>';
				$bil = 0;
			}
		} elseif ($bil == 0) {
			print '
							<tr><td colspan="7"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">
							Cawangan/Zon : ' . $rs->fields(department) . '</td></tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<td nowrap>&nbsp;</td>
								<td nowrap align="center">Nombor Anggota</td>
								<td nowrap align="left">Nama</td>
								<td nowrap align="center">Kad Pengenalan</td>
								<td nowrap>Cawangan/Zon</td>
								<td nowrap align="center">Tarikh Keanggotaan</td>
							</tr>';
		}
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td align="center">' . (int)$rs->fields(memberID) . '</a></td>
							<td>' . $rs->fields(name) . '</a></td>
							<td align="center">' . $rs->fields(newIC) . '</a></td>
							<td>' . $rs->fields(department) . '</a></td>
							<td align="center">' . toDate("d/m/Y", $rs->fields(approvedDate)) . '</a></td>
						</tr>';
		$tempDept = $rs->fields(department);
		$rs->MoveNext();
	}
	if ($dept <> "ALL") {
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>' . $bil . '</b></td>
						</tr>';
	}
}
/*//
					if($dept=="ALL" || $dept=="BSR"){
					$sSQL = "";
					$sSQL = "SELECT	a.name, a.loginID, a.email, 
							 b.memberID, b.approvedDate, b.newIC, b.oldIC, c.name as department  
							 FROM 	users a, userdetails b, userterminate d
							 INNER JOIN general c
							 ON		c.ID = b.departmentID 
							 WHERE  a.userID = b.userID and a.userID=d.userID and d.type = '1'
							 AND 	b.status = '3' "; 
					$sSQL.= " ORDER BY memberID, b.approvedDate DESC";
					$rs = &$conn->Execute($sSQL);
					$sSQL = "";
					$tempDept = '';
					if ($rs->RowCount() <> 0) {	
						if($dept=="BSR"){
							print '
							<tr><td colspan="7"  style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;" height="30" valign="bottom">
							Kategori : Bersara</td></tr>
							<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
								<th nowrap>&nbsp;</th>
								<th nowrap width="100">&nbsp;Nombor Anggota</th>
								<th nowrap align="left">&nbsp;Nama</th>
								<th nowrap width="80">&nbsp;Nombor KP Baru</th>
								<th nowrap width="80">&nbsp;Jabatan/Cawangan</th>
								<th nowrap width="150">&nbsp;Email</th>
								<th nowrap align="center" width="150">&nbsp;Tarikh Keanggotaan</th>
							</tr>';							
							$bil=0;
						}
						while(!$rs->EOF) {	
						$bil++;		
						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">'.$bil.')&nbsp;</td>
							<td>&nbsp;'.(int)$rs->fields(memberID).'</a></td>
							<td>&nbsp;'.$rs->fields(name).'</a></td>
							<td>&nbsp;'.$rs->fields(newIC).'</a></td>
							<td>&nbsp;'.$rs->fields(department).'</a></td>
							<td>&nbsp;'.$rs->fields(email).'</a></td>
							<td align="center">&nbsp;'.toDate("d/m/Y",$rs->fields(approvedDate)).'</a></td>
						</tr>';
						$rs->MoveNext();
					}	
						if($dept=="BSR"){
						print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="7" height="30" valign="bottom">Jumlah Anggota : <b>'.$bil.'</b></td>
						</tr>';					
						}
					}
					//
					print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" height="30" valign="bottom">Jumlah Keseluruhan Anggota : <b>'.$bil.'</b></td>
					</tr>';
				} else {
					print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
				}*/
print '		</table> 
		</td>
	</tr>
	
</table>
</form>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
