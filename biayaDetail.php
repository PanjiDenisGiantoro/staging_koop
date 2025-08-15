<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	biayaDetail.php
 *          Date 		: 	12/12/2007
 *********************************************************************************/
include("header.php");

include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=biayaDokumen&mn=906&pk=" . $id;
$sActionFileName = "?vw=biayaDokumen&mn=906&pk=" . $id;
$title     		= "Penjamin-Maklumat Anggota";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$FormLabel[$a]   	= "Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No KP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Alamat&nbsp;";
$FormElement[$a] 	= "add";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Umur";
$FormElement[$a] 	= "umur";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Nombor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jawatan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tarikh Menjadi Anggota";
$FormElement[$a] 	= "text";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Jabatan/Cawangan";
$FormElement[$a] 	= "dept";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Yuran bulanan";
$FormElement[$a] 	= "monthFee";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Nama Suami/isteri";
$FormElement[$a] 	= "namapsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pekerjaan";
$FormElement[$a] 	= "jobpsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Majikan";
$FormElement[$a] 	= "majikanpsgn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Bil tanggungan";
$FormElement[$a] 	= "biltggn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Alamat Majikan&nbsp;";
$FormElement[$a] 	= "add2";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bil. sekolah";
$FormElement[$a] 	= "bilsek";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";


//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
$uid = $pk;
$lid = $id;
//$uid = dlookup("loans", "userID", "loanID='".$pk."'");
//$conn->debug =1;
$strMember = "SELECT a. * , b.memberID, b.newIC, b.dateBirth, b.job, b.grossPay, b.address, b.city, b.postcode, b.stateID, b.departmentID, b.approvedDate as startdate, b.monthFee, b.totalFee, (b.grossPay+ b.totalFee) as jumLayak ,c . * , c.gaji + c.elaun + c.gajipsgn + c.elaunpsgn + c.lain_i + c.lain_ii AS jumpdpt, c.saraan + c.pljn + c.p_prmhn + c.p_kereta + c.p_lain + c.epfdll AS jumpblj FROM users a, userdetails b, userloandetails c WHERE a.userID = '" . $pk . "' AND a.userID = b.userID AND b.userID = c.userID";
$GetLoan = &$conn->Execute($strMember);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$$FormElement[$i],
				$FormCheck[$i][$j],
				$i
			);
		}
	}
	//--- End   : Call function FormValidation ---  
	if ($startPymtDate <> "") {
		$startPymtDate = substr($startPymtDate, 6, 4) . '-' . substr($startPymtDate, 3, 2) . '-' . substr($startPymtDate, 0, 2);
	}
	if ($applyDate <> "") {
		$applyDate = substr($applyDate, 6, 4) . '-' . substr($applyDate, 3, 2) . '-' . substr($applyDate, 0, 2);
	}
	if ($approvedDate <> "") {
		$approvedDate = substr($approvedDate, 6, 4) . '-' . substr($approvedDate, 3, 2) . '-' . substr($approvedDate, 0, 2);
	}
	if ($rejectedDate <> "") {
		$rejectedDate = substr($rejectedDate, 6, 4) . '-' . substr($rejectedDate, 3, 2) . '-' . substr($rejectedDate, 0, 2);
	}
	if ($updatedDate <> "") {
		$updatedDate = substr($updatedDate, 6, 4) . '-' . substr($updatedDate, 3, 2) . '-' . substr($updatedDate, 0, 2);
	}
	if (count($strErrMsg) == "0") {
		$totalInterest 	= number_format($loanAmt * ($loanCaj / 100) * ($loanPeriod / 12), 2, '.', '');
		$totalLoan 		= number_format($loanAmt + $totalInterest, 2, '.', '');
		if ($loanAmt <> "") $monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
		$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');
		if ($totalInterest <> "" and $loanPeriod <> "") $interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
		$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
		$monthlyPymt	= $monthlyPay + $interestPay;

		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "loanID=" . tosql($pk, "Number");
		$sSQL	= "UPDATE loans SET " .
			"startPymtDate=" . tosql($startPymtDate, "Text") .
			",loanAmt=" . tosql($loanAmt, "Number") .
			",loanPeriod=" . tosql($loanPeriod, "Number") .
			",monthlyPymt=" . tosql($monthlyPymt, "Number") .
			",outstandingAmt=" . tosql($totalLoan, "Number") .
			",insuranceID=" . tosql($insuranceID, "Number") .
			",insurancePymt=" . tosql($insurancePymt, "Number") .
			",paymentID=" . tosql($paymentID, "Number") .
			",guarantorID=" . tosql($sellUserID, "Text") .
			",purpose=" . tosql($purpose, "Text") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",updatedBy=" . tosql($updatedBy, "Text");
		$sSQL .= " where " . $sWhere;
		print '<script>
					//alert ("Status permohonan pinjaman telah dikemaskinikan ke dalam sistem.");
					//window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if (!isset($pic)) $pic = dlookup("userloandetails", "gaji_img", "userID=" . tosql($pk, "Text"));
$Gambar = "upload_gaji/" . $pic;
if (!isset($picic)) $picic = dlookup("userloandetails", "ic_img", "userID=" . tosql($pk, "Text"));
$Gambaric = "upload_ic/" . $pic;
print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $GetLoan->fields(userID) . '">
<input type="hidden" name="loanType" value="' . $GetLoan->fields(loanType) . '">
<input type="hidden" name="loanID" value="' . $GetLoan->fields(loanID) . '">
<table border=0 cellpadding=3 cellspacing=0 width=95% align="center" class="lineBG">
<h5 class="card-title">' . strtoupper($title) . ' : ' . $GetLoan->fields(loanID) . '</h5>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<tr><td colspan=4><div class="card-header">BUTIR-BUTIR PENJAMIN</div></td></tr>';
	//if ($i == 17) print '<tr><td class=Header colspan=4>B. BUTIR-BUTIR PEMBIAYAAN</td></tr>';
	if ($i == 21) print '<tr><td colspan=4><div class="card-header">PENYATA PENDAPATAN/PERBELANJAAN</div></td></tr>';
	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	//if (!($i == 1 or $i == 2 or $i == 8 or $i ==30 or $i == 32)) 
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata><b>';
	else
		print '<td class=Data><b>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetLoan->fields($FormElement[$i]));
	if ($strFormValue == '') $strFormValue = $$FormElement[$i];

	if ($i == 7) {
		$strFormValue = todate("d/m/y", $GetLoan->fields(startdate));
	}

	FormEntry(
		$FormLabel[$i],
		$FormElement[$i],
		$FormType[$i],
		$strFormValue,
		$FormData[$i],
		$FormDataValue[$i],
		$FormSize[$i],
		$FormLength[$i]
	);


	if ($i == 2) {
		print str_repeat("&nbsp;", 20);
	}

	if ($i == 3) {
		$stradd = str_replace("<pre>", "", $GetLoan->fields(address));
		$stradd = str_replace("</pre>", "", $stradd);

		$add = $stradd . ', ' . tohtml($GetLoan->fields(city)) . ', <br />  ' . tohtml($GetLoan->fields(postcode)) . ', ' . dlookup("general", "name", "ID=" . $GetLoan->fields(stateID));

		print $add;
	}

	if ($i == 4) {
		$yric = substr($GetLoan->fields(newIC), 0, 2);
		$yfisrt = substr($GetLoan->fields(newIC), 0, 1);
		$nowyr = (int)date(y);
		if ($yic <> '0') $age = (100 - $yric) + $nowyr;
		else $age = $nowyr;
		print $age;
	}

	if ($i == 8) {
		$dept = dlookup("general", "name", "ID=" . $GetLoan->fields(departmentID));
		print $dept;
	}

	if ($i == 15) {
		$stradd = str_replace("<pre>", "", $GetLoan->fields(addresspsgn));
		$stradd = str_replace("</pre>", "", $stradd);

		if ($stradd) $add = $stradd;
		else $add = '';
		// .', '.tohtml($GetLoan->fields(citypsgn)).', <br />  '.tohtml($GetLoan->fields(postcodepsgn)).', '.dlookup("general", "name", "ID=" . $GetLoan->fields(stateIDpsgn)); 
		print '<b>' . $add . '</b>';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

//pendapatan / perbelanjaan
//print '<tr>
//		<td class=Header colspan=4>PENYATA PENDAPATAN/PERBELANJAAN&nbsp;&nbsp;<!--font face="Verdana, Poppins, Helvetica, sans-serif" size="-2" color="#0000FF"><a class="data" href="biayaEdit.php?pk='.$pk.'">[MENGUBAH]	</a></font-->';

if ($valAddDpt <= 0) $valAddDpt = 0;
if ($addDpt) $valAddDpt++;
if ($minDpt) $valAddDpt--;
print '<tr><td colspan=4><div class="card-header bg-soft-primary">PENYATA PENDAPATAN&nbsp;&nbsp;';
if ($pic) {
	print '
<input type=button value="Paparan Slip Gaji" class="btn btn-sm btn-secondary" onClick=window.open(\'upload_gaji/' . $pic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");> ';
}
if ($picic) {
	print '
<input type=button value="Paparan IC" class="btn btn-sm btn-secondary" onClick=window.open(\'upload_ic/' . $picic . '\',"pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
}
print '	</div></td></tr><input type="hidden" name="valAddDpt" value="' . $valAddDpt . '" class="but">
		<!--input type="submit" name="addDpt" value="Tambah" class="but"><input type="submit" name="minDpt" value="Kurang" class="but"--></td></tr>';

$SQLdpt = "";
$SQLdpt	= "SELECT * FROM `userstates` WHERE userID = '" . $uid . "' AND payType = 'A'";
$rsdpt = &$conn->Execute($SQLdpt);

if ($rsdpt->RowCount() <> 0) {
	$i = 1;
	$tot = 0;
	while (!$rsdpt->EOF) {
		$id = $rsdpt->fields('payID');
		$stateID = $rsdpt->fields('ID');
		//$code = dlookup("general", "code", "ID=" . tosql($id, "Number"));
		$name = dlookup("general", "name", "ID=" . tosql($id, "Number"));
		$value = $rsdpt->fields('amt');

		print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">
					<input name="idD' . $i . '" type="hidden" value="' . $id . '">
					<input name="stateIDd' . $i . '" type="hidden" value="' . $stateID . '"> 
					<input name="nameD' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . $name . ': </td>
				<td class="Data">
					<b><input name="valueD' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"> ' . number_format($value, 2) . '&nbsp;</b>
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>';
		$totD = $i;
		$i++;
		$tot += $value;
		$rsdpt->MoveNext();
	}
	print 	'<tr valign="top">
				<td class="DataB" align="right" width="20%">
				Jumlah (RM): </td>
				<td class="DataB">
				<b>' . number_format($tot, 2) . '</b>&nbsp;
				</td>
				<td class="DataB" align="right">&nbsp; </td>
				<td class="DataB">&nbsp;</td>
				</tr>';
}
//------------------------
$sqlGet = "select sum(amt) as amt from userstates where userID = '" . $uid . "' and payType = 'A'";
$GettotA =  &$conn->Execute($sqlGet);
$totalPA1 = $GettotA->fields(amt);
$totalTA1 = $totalPA1 * 15;
$sumyrnshm =  getTotFees($uid, date("Y")); //dlookup("userdetails", "totalFee", "userID=" . $uid) + dlookup("userdetails", "totalShare", "userID=" . $uid);
$totalFeePA1 = $totalTA1 + $sumyrnshm;

$sqlGet = "select sum(amt) as amt from userstates where userID = '" . $uid . "' and payType = 'B'";
$GettotPB1 =  &$conn->Execute($sqlGet);
$totalPB1 = $GettotPB1->fields(amt);


print '	<tr><td class=Header colspan=4>Maklumat Kelayakan: &nbsp;&nbsp;
		<input type="hidden" name="valAddBlj" value="' . $valAddBlj . '" class="but">
		<!--input type="submit" name="addBlj" value="Tambah" class="but"><input type="submit" name="minBlj" value="Kurang" class="but"--></td></tr>';
print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">15 x gaji: </td>
				<td class="Data"><b>' . number_format($totalTA1, 2) . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>
				<tr valign="top">
				<td class="Data" align="right" width="20%">Pegangan yuran dan syer: </td>
				<td class="Data"><b>' . number_format($sumyrnshm, 2) . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>
				<tr valign="top">
				<td class="Data" align="right" width="20%">Jumlah kelayakan: </td>
				<td class="Data"><b>' . number_format($totalFeePA1, 2) . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>';

print '	<tr><td class=Header colspan=4>Senarai tanggungan: &nbsp;&nbsp;</td></tr>';

$chkloanType = array();
$chkuserID = array();
$applyDate = dlookup("loans", "applyDate", "loanID=" . $lid);
$chkloanType[] = dlookup("loans", "loanType", "loanID=" . $lid);
$chkuserID[] = dlookup("loans", "userID", "loanID=" . $lid);

$sqlGet = "SELECT a.loanID, a.loanType, a.loanAmt, a.userID, b.ajkDate2 "
	. " FROM loans a, loandocs b "
	. " WHERE a.loanID = b.loanID and "
	. " (a.penjaminID1 = '" . $uid . "' OR a.penjaminID2 = '" . $uid . "' OR a.penjaminID3 = '" . $uid . "') "
	. " AND a.status = 3 AND a.loanID <> " . $lid . " AND b.ajkDate2 < '" . $applyDate . "' ORDER BY applyDate DESC";

$GetLoan =  &$conn->Execute($sqlGet);
print '	<tr><td class=Data colspan=4>&nbsp;&nbsp;
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';
if ($GetLoan->RowCount() <> 0) {
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap>&nbsp;Nombor anggota</td>
						<td nowrap>&nbsp;Nama</td>
						<td nowrap align="center">&nbsp;Nombor bond</td>						
						<td nowrap align="center">&nbsp;Yuran/Syer terkumpul</td>
						<td nowrap align="center">&nbsp;Baki Pinjaman</td>
						<td nowrap align="center">&nbsp;80% yuran/syer</td>
						<td nowrap align="center">&nbsp;Baki sebenar</td>
					</tr>';
	$bil = 1;
	$tot1 = 0;
	while (!$GetLoan->EOF) {
		//----
		//$loanBalance = $GetLoan->fields(outstandingAmt);
		//print $GetLoan->fields(userID). '-' . $chkuserID . ':' .  $GetLoan->fields(loanType) . '-' .  $chkloanType;
		if (in_array($GetLoan->fields(userID), $chkuserID) && in_array($GetLoan->fields(loanType), $chkloanType)) {
			$GetLoan->MoveNext();
			continue;
		} else {
			$chkloanType[] = $GetLoan->fields(loanType);
			$chkuserID[] = $GetLoan->fields(userID);
		}
		//------------------- actively check balance from transaction ---------------------
		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
		$Get =  &$conn->Execute($sqlLoan);

		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
		}

		$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "select rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $nobond = $Get->fields(rnoBond);

		$getOpen = "SELECT 
					SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
					SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
					FROM transaction
					WHERE
					pymtRefer = '" . $nobond . "'
					AND deductID = '" . $c_Deduct . "' 
					AND month(createdDate) <= " . date("m") . "
					AND year(createdDate) <= " . date("Y") . "
					GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiPkk =  $loanAmt - $rsOpen->fields(yuranKt); //$bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
		else   $bakiPkk =  $loanAmt;

		//------------------- end -----------------------------------

		$loanBalance = $bakiPkk;
		$monthFee = dlookup("userdetails", "monthFee", "userID='" . $GetLoan->fields(userID) . "'");
		$tot =  $loanBalance - (0.8 * $monthFee);
		$tot1 = $tot1 + $tot;
		//----
		$userID = $GetLoan->fields(userID);
		$nama = dlookup("users", "name", "userID='" . $userID . "'");
		//$nobond = dlookup("loandocs", "rnoBond", "loanID='".$GetLoan->fields(loanID)."'");
		$totYrnShm =  getTotFees($userID, date("Y"));
		//$totYrnShm = dlookup("userdetails", "totalFee", "userID=" . $userID) + dlookup("userdetails", "totalShare", "userID=" . $userID);
		$tot80 = $totYrnShm * 0.8;
		//$bal = dlookup("userdetails", "totalFee", "userID=" . $userID) - $tot80;
		//$bal = getFees($userID, date("Y")) - $tot80;
		$bal = $loanBalance - $tot80;
		$tbal = $tbal + $bal;
		print ' <tr>
						<td class="Data" align="right" height="20">' . $bil . '.&nbsp;</td>
						<td class="Data">&nbsp;' . $userID . '</td>
						<td class="Data">&nbsp;' . $nama . '</td>
						<td class="Data" align="right">' . $nobond . '&nbsp;</td>
						<td class="Data" align="center">&nbsp;' . number_format($totYrnShm, 2) . '</td>
						<td class="Data" align="right">' . number_format($loanBalance, 2) . '&nbsp;</td>
						<td class="Data" align="center">&nbsp;' . number_format($tot80, 2) . '</td>
						<td class="Data" align="center">&nbsp;' . number_format($bal, 2) . '</td>
					</tr>';
		$bil++;
		$GetLoan->MoveNext();
	}
	$totalPB1 = $tbal;
	$balPA1 = $totalFeePA1 - $totalPB1;
	print ' </table>
			</td>
		</tr>';
} else {
	$totalPB1 = 0;
	$balPA1 = $totalFeePA1 - $tot1; //$totalPB1;	
	print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada sebarang tanggungan. -</b><hr size=1"></td></tr>';
}
print '	<tr><td class=Header colspan=4>Baki tanggungan yang boleh dijamin: &nbsp;&nbsp;</td></tr>';
print ' 
</table>
</td></tr>';
$balGet = $totalFeePA1 - $totalPB1;
print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">Jumlah semua tanggungan: </td>
				<td class="Data"><b>' . number_format($totalPB1, 2) . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>
				<tr valign="top">
				<td class="Data" align="right" width="20%">Baki kelayakan penjamin: </td>
				<td class="Data"><b>' . number_format($balGet, 2) . '</b>&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>';

print '<tr><td colspan=4 align=center class=Data>
			<!--input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
		<input type="button" class="label" value="Dokumen Tawaran" onclick="window.open(\'dokPP.php?pk=' . $pk . '&obj=5\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">	
		<input type=Submit name=SubmitForm class="but" value="Semakan Komiti"-->
			<input type=button name=ResetForm class="btn btn-secondary" value=<< onClick="window.location.href =\'' . $sActionFileName . '\'">		</td>
		</tr>

</table>
</form>';
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }
		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				//strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          //alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}



</script>';
print '
<script>
	function selectPengurusan(rpt) {
		window.open(rpt+".php" ,"pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");
	}	  
</script>';
include("footer.php");
