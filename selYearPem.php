<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	selYear.php
*		   Description	:	Selection Year Option
*		   Parameter	:   $rpt, $id
*          Date 		: 	15/12/2003
*********************************************************************************/
include("common.php");	
	

$today = date("F j, Y, g:i a");                 
//$c_deduct = 
//--- Prepare Loan List
$deptList = Array();
$deptVal  = Array();
$sSQL = "SELECT code, name, ID FROM general WHERE c_deduct IN('1549','1539','1540','1567','1568','1575','1579','1613','1614','1615','1616','1619','1620','1622','1623','1624','1625','1626','1627','1628','1629','1630','1631','1665','1666','1667','1673','1675','1691','1702','1703','1704','1705','1710','1709','1768')";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0){
	while (!$rs->EOF) {
		//array_push ($deptList, $rs->fields(deptCode).'-'.$rs->fields(deptName));
		array_push ($deptList, $rs->fields(name));
		array_push ($deptVal, $rs->fields(ID));
		$rs->MoveNext();
	}
}



if ($rpt == "") {
	print '	<script>
				alert ("Pengguna tidak boleh akses mukasurat ini...!");
				window.close();
			</script>';
			exit;
}

if (!isset($yr)) $yr	= date("Y");   
if (!isset($mth)) $mth	= date("n");                  		

if ($action == "Jana Laporan") {
	$msg	= "";
	$yrmth = sprintf("%04d%02d", $yr, $mth);
	if ($yrmth == "") $msg = "Tiada Bulan/Tahun dimasukkan...!";
	if ($msg <> "") {
		print '<script>alert("'.$msg.'");</script>';
	} else {
		print '
		<script>
			var rptURL;
			rptURL = "'.$rpt.'.php?yrmth='.$yrmth.'&id='.$dept.'";
			window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			window.close();
		</script>	';
	}
}

print '
<html>
<head>
	<title>'.$emaNetis.'</title>
	<LINK rel="stylesheet" href="images/default.css" >	
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="FrmSelection" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="rpt" value="'.$rpt.'">
	<input type="hidden" name="id" value="'.$id.'">
	<table border="0" cellpadding="3" cellspacing="0" class="contentD" style="padding: 1 0 0 0" height="100" width="750">
		<tr valign="top">
			<td width="17%" align="right" class="textFont"><b>Bulan/Tahun :&nbsp;</b></td>
			<td width="17%" class="textFont">
		 				<select name="mth" class="data">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="'.$j.'"';
	if ($mth == $j) print 'selected';
	print 			'>'.$j;
}
print '			</select>/
		  <input type="text" name="yr" size="5" maxlength="4" value="'.$yr.'" class="data">			</td>
		   
		   
		    <td width="16%" class="textFont"><strong>Jenis Pembiayaan : </strong></td>
		    <td width="50%" class="textFont"><select name="dept" class="Data" >
				<option value="">- Sila Pilih -';
			for ($i = 0; $i < count($deptList); $i++) {
				print '	<option value="'.$deptVal[$i].'" ';
				if ($dept == $deptVal[$i]) print ' selected';
				print '>'.$deptList[$i];
			}
print '		</select>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><input type="submit" name="action" value="Jana Laporan" class="but"></td>
		</tr>
	</table>
</form>


</body>
</html>';
?>