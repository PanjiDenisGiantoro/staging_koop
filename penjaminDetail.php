<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
include("header.php");
date_default_timezone_set("Asia/Jakarta");
include("koperasiQry.php");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=penjamin&mn=906&pk=" . $pk;
$sActionFileName = "?vw=penjamin&mn=906&pk=" . $pk;
$title     		= "Maklumat Penjamin";

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
$FormLabel[$a]   	= "Kartu Identitas";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "Alamat";
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
$FormLabel[$a]   	= "Nomor Anggota";
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
$FormLabel[$a]   	= "Tanggal Menjadi Anggota";
$FormElement[$a] 	= "text";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Cabang / Zona";
$FormElement[$a] 	= "dept";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Wajib Bulanan (RP)";
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
$FormLabel[$a]   	= "Nama Suami/Isteri";
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
$FormLabel[$a]   	= "Bil. Tanggungan";
$FormElement[$a] 	= "biltggn";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Alamat Majikan";
$FormElement[$a] 	= "add2";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bil. Sekolah";
$FormElement[$a] 	= "bilsek";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$strMember = "SELECT a. * , b.memberID, b.newIC, b.dateBirth, b.job, b.grossPay, b.address, b.city, b.postcode, b.stateID, b.departmentID, b.approvedDate as startdate, b.monthFee, b.totalFee, (b.grossPay+ b.totalFee) as jumLayak ,c . * , c.gaji + c.elaun + c.gajipsgn + c.elaunpsgn + c.lain_i + c.lain_ii AS jumpdpt, c.saraan + c.pljn + c.p_prmhn + c.p_kereta + c.p_lain + c.epfdll AS jumpblj FROM users a, userdetails b, userloandetails c WHERE a.userID = '" . $pk . "' AND a.userID = b.userID AND b.userID = c.userID";
$GetLoan = &$conn->Execute($strMember);

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
    <h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
        
<div class="mb-3 row m-1">
	';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<div class="card-header mt-3">BUTIR-BUTIR PENJAMIN</div>';
	//if ($i == 17) print '<tr><td class=Header colspan=4>B. BUTIR-BUTIR PEMBIAYAAN</td></tr>';
	if ($i == 21) print '<div class="card-header mt-3">PENYATA PENDAPATAN/PERBELANJAAN</div>';
	if ($cnt == 1) print '<div class="m-1 row">';
	print '<label class="col-md-2 col-form-label">' . $FormLabel[$i];
	if (!($i == 10))
		// print ':';
		print ' </label>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-4 bg-danger">';
	else
		print '<div class="col-md-4">';
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

		print '<b>' . $add . '</b>';
	}

	if ($i == 4) {
		$yric = substr($GetLoan->fields(newIC), 0, 2);
		$yfisrt = substr($GetLoan->fields(newIC), 0, 1);
		$nowyr = (int)date(y);
		if ($yic <> '0') $age = (100 - $yric) + $nowyr;
		else $age = $nowyr;
		print '<b>' . $age . '</b>';
	}

	if ($i == 8) {
		$dept = dlookup("general", "name", "ID=" . $GetLoan->fields(departmentID));
		print '<b>' . $dept . '</b>';
	}

	if ($i == 15) {
		$stradd = str_replace("<pre>", "", $GetLoan->fields(addresspsgn));
		$stradd = str_replace("</pre>", "", $stradd);

		if ($stradd) $add = $stradd;
		else $add = '';
		print '<b>' . $add . '</b>';
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</div>';
	if ($cnt == 0) print '</div>';
}

if ($valAddDpt <= 0) $valAddDpt = 0;
if ($addDpt) $valAddDpt++;
if ($minDpt) $valAddDpt--;
print '<div class="card-header mt-3">PENYATA PENDAPATAN'
	. '<b><input type="hidden" name="valAddDpt" value="' . $valAddDpt . '" class="but"></b>
		<!--input type="submit" name="addDpt" value="Tambah" class="but"><input type="submit" name="minDpt" value="Kurang" class="but"-->
                </div>';


$SQLdpt = "";
$SQLdpt	= "SELECT * FROM `userstates` WHERE userID = '" . $pk . "' AND payType = 'A'";
$rsdpt = &$conn->Execute($SQLdpt);

if ($rsdpt->RowCount() <> 0) {
	$i = 1;
	$tot = 0;
	while (!$rsdpt->EOF) {
		$id = $rsdpt->fields('payID');
		$stateID = $rsdpt->fields('ID');
		$name = dlookup("general", "name", "ID=" . tosql($id, "Number"));
		$value = $rsdpt->fields('amt');

		print 	'<div class="row m-3 mt-3">
                                                                                            <div class="col-md-3">
					<b><input name="idD' . $i . '" type="hidden" value="' . $id . '"></b>
					<b><input name="stateIDd' . $i . '" type="hidden" value="' . $stateID . '"></b>
					<b><input name="nameD' . $i . '" type="hidden" class="Data" value="' . $name . '" size="20" align="right" onfocus="this.blur()">' . $name . ': </b></div>
                                                                                            <div class="col-md-3">
					<b><input name="valueD' . $i . '" size="10" maxlength="15" type="hidden" value="' . $value . '"></b><b>' . $value . '</b>&nbsp;
				</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>';

		/*
				print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">
					<input name="idD'.$i.'" type="hidden" value="'.$id.'">
					<input name="stateIDd'.$i.'" type="hidden" value="'.$stateID.'"> 
					<input name="nameD'.$i.'" type="hidden" class="Data" value="'.$name.'" size="20" align="right" onfocus="this.blur()">'.$name.': </td>
				<td class="Data">
					<input name="valueD'.$i.'" size="10" maxlength="15" type="hidden" value="'.$value.'"> '.$value.'&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>'; */
		$totD = $i;
		$i++;
		$tot += $value;
		$rsdpt->MoveNext();
	}

	print 	'<div class="row m-3 mt-3">
                                                                                          <div class="col-md-3"><b>Jumlah :</b></div>
                                                                                            <div class="col-md-3"><b>' . number_format($tot, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>';
	/*
                                                            print 	'<tr valign="top">
				<td class="DataB" align="right" width="20%">
				Jumlah : </td>
				<td class="DataB">
				'.number_format($tot,2).'&nbsp;
				</td>
				<td class="DataB" align="right">&nbsp; </td>
				<td class="DataB">&nbsp;</td>
				</tr>'; */
}

//------------------------
$sqlGet = "select sum(amt) as amt from userstates where userID = '" . $pk . "' and payType = 'A'";
$GettotA =  &$conn->Execute($sqlGet);
$totalPA1 = $GettotA->fields(amt);
$totalTA1 = $totalPA1 * 15;
$sumyrnshm =  getTotFees($pk, date("Y"));
$totalFeePA1 = $totalTA1 + $sumyrnshm;

$sqlGet = "select sum(amt) as amt from userstates where userID = '" . $pk . "' and payType = 'B'";
$GettotPB1 =  &$conn->Execute($sqlGet);
$totalPB1 = $GettotPB1->fields(amt);

print '<div class="card-header mt-3">Maklumat Kelayakan: &nbsp;&nbsp;
		<input type="hidden" name="valAddBlj" value="<b>' . $valAddBlj . '</b>" class="but">
		<!--input type="submit" name="addBlj" value="Tambah" class="but"><input type="submit" name="minBlj" value="Kurang" class="but"--></div>';

print '<div class="row m-1">
                                                                       <div class="col-md-3"><b>15 x gaji:</b></div>
                                                                        <div class="col-md-3"><b>' . number_format($totalTA1, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>
                                <div class="row m-1">
                                                                       <div class="col-md-3"><b>Pegangan yuran dan syer:</b></div>
                                                                        <div class="col-md-3"><b>' . number_format($sumyrnshm, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>
                                <div class="row m-1">
                                                                       <div class="col-md-3"><b>Jumlah kelayakan:</b></div>
                                                                        <div class="col-md-3"><b>' . number_format($totalFeePA1, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>';
/*
				print 	'<tr valign="top">
				<td class="Data" align="right" width="20%">15 x gaji: </td>
				<td class="Data">'.number_format($totalTA1,2).'&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>
				<tr valign="top">
				<td class="Data" align="right" width="20%">Pegangan yuran dan syer: </td>
				<td class="Data">'.number_format($sumyrnshm,2).'&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>
				<tr valign="top">
				<td class="Data" align="right" width="20%">Jumlah kelayakan: </td>
				<td class="Data">'.number_format($totalFeePA1,2).'&nbsp;
				</td>
				<td class="Data" align="right">&nbsp; </td>
				<td class="Data">&nbsp;</td>
				</tr>'; */

print '<div class="card-header mt-3">Senarai Tanggungan: &nbsp;&nbsp;</div>';

$sqlGet = "SELECT a.loanID, a.loanAmt, a.userID, b.ajkDate2 "
	. " FROM loans a, loandocs b "
	. " WHERE a.loanID = b.loanID and "
	. " (a.penjaminID1 = '" . $pk . "' OR a.penjaminID2 = '" . $pk . "' OR a.penjaminID3 = '" . $pk . "') "
	. " AND a.status = 3 ORDER BY applyDate DESC";

$GetLoan =  &$conn->Execute($sqlGet); //ctLoanStatusDept($q,$by,$filter,$dept); OR  A.userID = '".$uid."'

print '<div class="table-responsive">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';
if ($GetLoan->RowCount() <> 0) {
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm">
					<tr class="table-primary">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap><b>Nomor Anggota</b></td>
						<td nowrap><b>Nama</b></td>
						<td nowrap align="center"><b>Nombor Bond</b></td>						
						<td nowrap align="right"><b>Wajib/Pokok terkumpul</b></td>
						<td nowrap align="right"><b>Baki Pinjaman</b></td>
						<td nowrap align="right"><b>80% yuran/syer</b></td>
						<td nowrap align="right"><b>Baki sebenar</b></td>
					</tr>';
	$bil = 1;
	$tot1 = 0;
	while (!$GetLoan->EOF) {
		//----
		//$loanBalance = $GetLoan->fields(outstandingAmt);

		//------------------- actively check balance from transaction ---------------------
		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
		$Get =  &$conn->Execute($sqlLoan);

		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
		}

		$sql = "SELECT c_Deduct FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "SELECT rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
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
		$totYrnShm =  getTotFees($userID, date("Y"));
		$tot80 = $totYrnShm * 0.8;
		$bal = $loanBalance - $tot80;
		$tbal = $tbal + $bal;
		print ' <tr class="table-primary">
						<td class="Data" align="right" height="20"><b>' . $bil . '</b></td>
						<td class="Data"><b>' . $userID . '</b></td>
						<td class="Data"><b>' . $nama . '</b></td>
						<td class="Data" align="center"><b>' . $nobond . '</b></td>
						<td class="Data" align="right"><b>' . number_format($totYrnShm, 2) . '</b></td>
						<td class="Data" align="right"><b>' . number_format($loanBalance, 2) . '</b></td>
						<td class="Data" align="right"><b>' . number_format($tot80, 2) . '</b></td>
						<td class="Data" align="right"><b>' . number_format($bal, 2) . '</b></td>
					</tr>';
		$bil++;
		$GetLoan->MoveNext();
	}
	$totalPB1 = $tbal;
	$balPA1 = $totalFeePA1 - $totalPB1;
	print ' </table>
                                        </td></tr>
			';
} else {
	$totalPB1 = 0;
	$balPA1 = $totalFeePA1 - $tot1; //$totalPB1;	
	print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada sebarang maklumat tanggungan. -</b><hr size=1"></td></tr>';
}

print ' 
</table>
</div>';
print '<div class="card-header mt-3">Baki tanggungan yang boleh dijamin: &nbsp;&nbsp;</div>';
$balGet = $totalFeePA1 - $totalPB1;

print '<div class="row m-1">
                                                                       <div class="col-md-3"><b>Jumlah semua tanggungan:</b></div>
                                                                        <div class="col-md-3"><b>' . number_format($totalPB1, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>
                                                    <div class="row m-1">
                                                                       <div class="col-md-3"><b>Baki kelayakan penjamin:</b></div>
                                                                        <div class="col-md-3"><b>' . number_format($balGet, 2) . '</b>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				</div>';



print '<div class="mb-3 row">
                                    <center>
                                            <input type=button name=ResetForm class="btn btn-sm btn-secondary" value=<< onClick="window.location.href =\'' . $sActionFileName . '\'">
                                    </center>
                                </div>

</div>
</form>';
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
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
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
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
	          //alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          //window.location.href ="?vw=memberApply&mn=906&pk=" + strStatus;
	          window.location.href ="' . $sActionFileName . '";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "?vw=memberStatus&mn=906&pk=" + pk;
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
