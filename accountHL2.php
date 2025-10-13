<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	accountHL2.php
 *          Date 		: 	29/12/2018
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
if (!isset($strDate))	$strDate = date("d/m/Y");
$sFileName = "?vw=accountHL2&mn=$mn";

if (get_session('Cookie_userID') == "") {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) $member = 0;
else $member = 1;


$groupid = get_session("Cookie_userID");
$title = "Status Account Hutang Lapuk";

$sSQL = "	SELECT a.* , b.* FROM userdetails a, users b
			WHERE a.userID = '" . $userID . "' AND a.userID = b.userID
			order BY a.userID";
$rs = &$conn->Execute($sSQL);

$sSQL3 = "	SELECT a.* , b.* FROM loans a, loandocs b
			WHERE a.loanID = '" . $loanID . "' AND a.loanID = b.loanID
			order BY a.userID";
$rsGetLoan = &$conn->Execute($sSQL3);

$sSQL2 = "	SELECT * FROM accounthl
			WHERE loanID = '" . $loanID . "' order BY userID";
$rsaccount = &$conn->Execute($sSQL2);

$pokok = $rsaccount->fields(ByrnPokok);
$untung = $rsaccount->fields(ByrnUtng);
$lateCharge = $rsaccount->fields(LateCharge);
$lewatBln = $rsaccount->fields(SumBlnSb);
$lewatDhl = $rsaccount->fields(SumBlnLatest);
$catatan = $rsaccount->fields(Catatan);
$ID = $rsaccount->fields(ID);


$name = $rs->fields(name);
$tarikhAkhirByr = toDate("d/m/Y", $rs->fields(DatePymt));

$ByrnPokok = $_POST['ByrnPokok'];
$ByrnUtng = $_POST['ByrnUtng'];
$lateCharge = $_POST['lateCharge'];
$lewatBln = $_POST['lewatBln'];
$lewatDhl = $_POST['lewatDhl'];
$catatan = $_POST['catatan'];
$BakiHutang = $_POST['BakiHutang'];
$status = 1;

if ($action == 'Kemaskini') {
	if ($rsaccount->RowCount() > 0) {

		$ID = $rsaccount->fields(ID);
		$updatedBy	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL =	'';
		$sWhere	= '';
		$sWhere	= '	ID	= ' . $ID;
		$sSQL	= '	UPDATE accounthl ';
		$sSQL	.= ' SET ' .
			' lateCharge	=' . tosql($lateCharge, "Text") .
			' ,SumBlnSb	=' . tosql($lewatBln, "Text") .
			' ,SumBlnLatest	=' . tosql($lewatDhl, "Text") .
			' ,BalanceHL	=' . tosql($BakiHutang, "Text") .
			' ,ByrnPokok	=' . tosql($ByrnPokok, "Text") .
			' ,ByrnUtng	=' . tosql($ByrnUtng, "Text") .
			' ,Catatan	=' . tosql($catatan, "Text") .
			' ,CreatedDate='	. tosql($CreatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		$rs	= &$conn->Execute($sSQL);
	} else {

		$sSQL5	= "INSERT INTO accounthl (" .
			"userID," .
			"lateCharge," .
			"SumBlnSb," .
			"SumBlnLatest," .
			"BalanceHL," .
			"ByrnPokok," .
			"ByrnUtng," .
			"Catatan," .
			"status," .
			"loanID," .
			"CreatedDate)" .
			" VALUES (" .
			"'" . $userID . "', " .
			"'" . $lateCharge . "', " .
			"'" . $lewatBln . "', " .
			"'" . $lewatDhl . "', " .
			"'" . $BakiHutang . "', " .
			"'" . $ByrnPokok . "', " .
			"'" . $ByrnUtng . "', " .
			"'" . $catatan . "', " .
			"'" . $status . "', " .
			"'" . $loanID . "', " .
			"'" . $CreatedDate . "')";
		$rsInstAccount = &$conn->Execute($sSQL5);
	}

	print '<script>alert("Maklumat Telah DIKEMASKINI");</script>';
	$sSQL = "	SELECT a.* , b.* FROM userdetails a, users b
			WHERE a.userID = '" . $userID . "' AND a.userID = b.userID
			order BY a.userID";
	$rs = &$conn->Execute($sSQL);

	$sSQL3 = "	SELECT a.* , b.* FROM loans a, loandocs b
			WHERE a.loanID = '" . $loanID . "' AND a.loanID = b.loanID
			order BY a.userID";
	$rsGetLoan = &$conn->Execute($sSQL3);


	$sSQL2 = "	SELECT * FROM accounthl
			WHERE loanID = '" . $loanID . "' order BY userID";
	$rsaccount = &$conn->Execute($sSQL2);

	$loanNo = dlookup("loans", "loanNo", "loanID=" . tosql($loanID, "Text"));

	$strActivity = $_POST['Submit'] . ' Kemaskini Maklumat Akaun Hutang Lapuk - ' . $loanNo;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 2);
}
?>
<div class="table-responsive">
	<div style="width: 500px; text-align:left">
		<div>&nbsp;</div>
		<? print ' <form name="MyForm" action=' . $sFileName . ' method="post"> '; ?>
		<input type="hidden" name="userID" id="userID" value="<? echo $userID ?>" />
		<input type="hidden" name="loanID" id="loanID" value="<? echo $loanID ?>" />

		<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
			<tr>
				<td class="borderallblue" align="left" valign="middle">
					<div class="headerblue"><b>KEMASKINI HUTANG LAPUK</b></div>
				</td>
			</tr>
			<tr>
				<td class="borderleftrightbottomblue">
					<table width="510" cellpadding="4" cellspacing="4" style="background-color: #f8f8f8;">
						<tr class="table-success">
							<td colspan="4" height="38px" style="background-color: #d4f1f9;">Maklumat Anggota Hutang Lapuk</td>
						</tr>
						<tr>
							<td width="201">Nombor Anggota</td>
							<td width="5"><strong>:</strong></td>
							<td width="203"><? echo $userID ?>&nbsp;</td>
							<td width="81">&nbsp;</td>
						</tr>
						<tr>
							<td>Nama </td>
							<td><strong>:</strong></td>
							<td colspan="2"><? echo $name ?></td>
						</tr>
						<tr>
							<td>Tarikh Akhir Bayaran</td>
							<td><strong>:</strong></td>
							<td><? echo $tarikhAkhirByr ?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Jenis Pembiayaan</td>
							<td><strong>:</strong></td>
							<td colspan="2"><? echo dlookup("general", "name", "ID=" . tosql($rsGetLoan->fields(loanType), "Number")) ?>&nbsp;</td>
						</tr>
						<tr>
							<td>No Bond</td>
							<td><strong>:</strong></td>
							<td><? echo $bondNo ?>&nbsp;<input type="hidden" name="bondNo" value="<? $bondNo = $rsGetLoan->fields(rnoBond)  ?>" size="15" maxlength="10"></td>
						</tr>
						<tr class="table-success">
							<td colspan="4" height="38px" style="background-color: #d4f1f9;">Maklumat Akaun Hutang Lapuk</td>
						</tr>
						<tr>
							<td>Baki Hutang Semasa</td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="BakiHutang" value="<? echo $BakiHutang = $rsaccount->fields(BalanceHL);  ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td>Bayaran Pokok</td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="ByrnPokok" value="<? echo $pokok = $rsaccount->fields(ByrnPokok); ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td>Bayaran Untung</td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="ByrnUtng" value="<? echo $untung = $rsaccount->fields(ByrnUtng); ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td>Denda Lewat Sebulan (RP)</td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="lateCharge" value="<? echo $lateCharge = $rsaccount->fields(LateCharge); ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td>Jumlah Bulan Terdahulu </td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="lewatBln" value="<? echo $lewatBln = $rsaccount->fields(SumBlnSb); ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td>Jumlah Bulan Semasa</td>
							<td><strong>:</strong></td>
							<td colspan="2"><input type="text" class="form-controlx form-control-sm" name="lewatDhl" value="<? echo $lewatDhl = $rsaccount->fields(SumBlnLatest); ?>" size="15" maxlength="10">&nbsp;</td>
						</tr>
						<tr>
							<td valign="top">Catatan</td>
							<td><strong>:</strong></td>
							<td colspan="2"><textarea class="form-controlx form-control-sm" name="catatan" id="textarea" cols="40" rows="2"><? echo $catatan = $rsaccount->fields(Catatan); ?></textarea></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="submit" class="btn btn-secondary" name="action" value="Kemaskini">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
	</div>
</div>
<?
include("footer.php");
?>