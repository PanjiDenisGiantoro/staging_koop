<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	dividenPeratusBlnKhd.php
 *          Date 		: 	15/6/2006
 *********************************************************************************/
session_start();
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 100;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");
if (!isset($mm))	$mm = date("n"); //"ALL";
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=dividenPeratusBlnKhd&mn=$mn";
$sFileRef  = "?vw=Edit_memberStmt&mn=$mn";
$title     = "Persentase Dividen (Dilayani/Disalurkan)";

$updatedDate = date("Y-m-d H:i:s");
$updatedBy 	= get_session("Cookie_userName");

//float round ( float $val [, int $precision = 0 [, int $mode = PHP_ROUND_HALF_UP ]] )
// ......................................................

//...............................................................
if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

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
		//array_push ($deptList, $rs->fields(deptCode).'-'.$rs->fields(deptName));
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}


//$GetMember = ctMemberStatusDept($q,$by,"1",$dept);
$sSQL = "";
$sWhere = " a.userID = b.userID AND b.status IN ('1')";
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

if ($ID) {
	$sWhere .= " AND b.userID = " . tosql($ID, "Text");
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b  ";
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';

if (get_session("Cookie_groupID") > 0) {
	print '    <tr valign="top" class="textFont">
	   	<td align="left">
			<table>
				<tr>
				<td class="textFont">&nbsp;

						Tahun  
						<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
	for ($j = 2013; $j <= 2079; $j++) {
		print '	<option value="' . $j . '"';
		if ($yy == $j) print 'selected';
		print '>' . $j;
	}
	print '</select>					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<table width="100%" border="0">
  <tr class="DataB">
    <td colspan="3" class="headerteal" > Masukkan Persentase Pembayaran Dividen (%) </td>
  </tr>
  <tr class="DataB">
    <td width="120" >Persentase Dividen</td>
    <td width="1487"><input type="text" class="form-control-sm" name="MYA" value="' . $MYA . '" size="5" /></td>
  </tr>
  <tr class="DataB">
  </tr>
      <td><input type="submit" size="3" class="btn btn-sm btn-primary" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply" value="Hitung" />  </td>
  </tr>
</table>';



	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;

		//if (get_session("Cookie_groupID") > 0) { //just for staf
		print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">
						
						</td>
					</tr>
				</table>
			</td>
		</tr>';
		//}

		print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap colspan="3" height="20">&nbsp;</td>
						<td nowrap height="20" colspan="2" nowrap><div align="right">Simpanan Wajib Dan Syer (RP)</div></td>

					</tr>
					<tr class="table-primary">
						<td nowrap align="center" height="20">No</td>
						<td nowrap>Nomor - Nama Anggota</td>
						<td nowrap align="center">Kartu Identitas</td>

						<td nowrap align="right">Saldo Awal Tahun ' . $yy . ' (Yuran)</td>
						<td nowrap align="right">Syer</td>
					</tr>';
		$totalFees = 0;
		$totalShares = 0;



		while (!$GetMember->EOF  && $cnt <= $pg) {

			//..................... checking data Wajib ............................
			$yrmth = $yy . $mm;
			$totalFees = number_format(getFeesAwalthn($GetMember->fields(userID), $yy), 2);
			$totalShares = number_format(getShares($GetMember->fields(userID), $yy), 2);

			if ($apply) {

				$sSQL11 = "SELECT *
			FROM dividen
			WHERE yearDiv = '" . $yy . "' AND statusKhd IN (1)";
				$rsChecking = &$conn->Execute($sSQL11);
				if ($rsChecking->RowCount() <= 0) {

					while (!$GetMember->EOF) {

						$totalFees = getFeesAwalthn($GetMember->fields(userID), $yy);
						$totalShares = getSharesDiv($GetMember->fields(userID), $yy);
						$jumDiv = ($MYA / 100) / 12;
						//$jumDiv = number_format(($Div), 2, '.', '');


						$DivAmtAwalBln = number_format(($totalFees + $totalShares) * $jumDiv, 2, '.', '');


						$mm1 = 01;
						$yymm1 = sprintf("%04d%02d", $yy, $mm1);
						$feeYuranTkiniBln1 = getFeesAwlDiv($GetMember->fields(userID), $yymm1);
						$DivAmtBln1 = number_format(($feeYuranTkiniBln1 + $totalShares) * $jumDiv, 2, '.', '');

						$mm2 = 02;
						$yymm2 = sprintf("%04d%02d", $yy, $mm2);
						$feeYuranTkiniBln2 = getFeesAwlDiv($GetMember->fields(userID), $yymm2);
						$DivAmtBln2 = number_format(($feeYuranTkiniBln2 + $totalShares) * $jumDiv, 2, '.', '');

						$mm3 = 03;
						$yymm3 = sprintf("%04d%02d", $yy, $mm3);
						$feeYuranTkiniBln3 = getFeesAwlDiv($GetMember->fields(userID), $yymm3);
						$DivAmtBln3 = number_format(($feeYuranTkiniBln3 + $totalShares) * $jumDiv, 2, '.', '');

						$mm4 = 04;
						$yymm4 = sprintf("%04d%02d", $yy, $mm4);
						$feeYuranTkiniBln4 = getFeesAwlDiv($GetMember->fields(userID), $yymm4);
						$DivAmtBln4 = number_format(($feeYuranTkiniBln4 + $totalShares) * $jumDiv, 2, '.', '');

						$mm5 = 05;
						$yymm5 = sprintf("%04d%02d", $yy, $mm5);
						$feeYuranTkiniBln5 = getFeesAwlDiv($GetMember->fields(userID), $yymm5);
						$DivAmtBln5 = number_format(($feeYuranTkiniBln5 + $totalShares) * $jumDiv, 2, '.', '');

						$mm6 = 06;
						$yymm6 = sprintf("%04d%02d", $yy, $mm6);
						$feeYuranTkiniBln6 = getFeesAwlDiv($GetMember->fields(userID), $yymm6);
						$DivAmtBln6 = number_format(($feeYuranTkiniBln6 + $totalShares) * $jumDiv, 2, '.', '');

						$mm7 = 07;
						$yymm7 = sprintf("%04d%02d", $yy, $mm7);
						$feeYuranTkiniBln7 = getFeesAwlDiv($GetMember->fields(userID), $yymm7);
						$DivAmtBln7 = number_format(($feeYuranTkiniBln7 + $totalShares) * $jumDiv, 2, '.', '');


						$yymm8 = $yy . '08';
						$feeYuranTkiniBln8 = getFeesAwlDiv($GetMember->fields(userID), $yymm8);
						$DivAmtBln8 = number_format(($feeYuranTkiniBln8 + $totalShares) * $jumDiv, 2, '.', '');

						$yymm9 = $yy . '09';
						$feeYuranTkiniBln9 = getFeesAwlDiv($GetMember->fields(userID), $yymm9);
						$DivAmtBln9 = number_format(($feeYuranTkiniBln9 + $totalShares) * $jumDiv, 2, '.', '');

						$mm10 = 10;
						$yymm10 = sprintf("%04d%02d", $yy, $mm10);
						$feeYuranTkiniBln10 = getFeesAwlDiv($GetMember->fields(userID), $yymm10);
						$DivAmtBln10 = number_format(($feeYuranTkiniBln10 + $totalShares) * $jumDiv, 2, '.', '');

						$mm11 = 11;
						$yymm11 = sprintf("%04d%02d", $yy, $mm11);
						$feeYuranTkiniBln11 = getFeesAwlDiv($GetMember->fields(userID), $yymm11);
						$DivAmtBln11 = number_format(($feeYuranTkiniBln11 + $totalShares) * $jumDiv, 2, '.', '');

						$JumDivAll = number_format(($DivAmtAwalBln + $DivAmtBln1 + $DivAmtBln2 + $DivAmtBln3 + $DivAmtBln4 + $DivAmtBln5 + $DivAmtBln6 + $DivAmtBln7 + $DivAmtBln8 + $DivAmtBln9 + $DivAmtBln10 + $DivAmtBln11), 2, '.', '');


						if ($JumDivAll > 0) {
							$userID = $GetMember->fields(userID);
							$docNo = DIV . $yy;
							$sSQL4	= "INSERT INTO dividen (" .

								"startYear," .
								"yearDiv," .
								"docNo," .
								"userID," .
								"amtFee," .
								"issueDate," .
								"clearDate," .
								"createdDate," .
								"createdBy," .
								"updatedDate," .
								"updatedBy," .
								"status," .
								"AmtDiv," .
								"AmtYuranT," .
								"statusKhd," .
								"AmtShareD)" .
								" VALUES (" .
								"'" . $yymm . "', " .
								"'" . $yy . "', " .
								"'" . $docNo . "', " .
								"'" . $userID . "', " .
								"'" . $MYA . "', " .
								"'" . $updatedDate . "', " .
								"'" . $updatedDate . "', " .
								"'" . $updatedDate . "', " .
								"'" . $updatedBy . "', " .
								"'" . $updatedDate . "', " .
								"'" . $updatedBy . "', " .
								"'" . 1 . "', " .
								"'" . $JumDivAll . "', " .
								"'" . $feeYuranTkiniBln11 . "', " .
								"'" . 1 . "', " .
								"'" . $totalShares . "')";

							$rsInstDiv = &$conn->Execute($sSQL4);
						}
						$GetMember->MoveNext();
					}
				} else {
					print '<script>alert("Pastikan Tahun Dividen yang Betul !")';
				}
				$strActivity = $_POST['Submit'] . 'Pengiraan Peratusan Dividen';
				activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
			}

			//print '<script>alert("Permohonan Dividen telah dikemaskini di dalam sistem !");


			//................. end test apply...................

			//$feeMonth = number_format($feeMonthly,2);
			//$totalShares = number_format(getShares($GetMember->fields(userID), $yr),2);

			print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields(name) . '</td>
						<td class="Data" align="center">' . $GetMember->fields('newIC') . '</td>						
					
						<td class="Data" align="right">' . $totalFees . '</td>
						<td class="Data" align="right">' . $totalShares . '</td>
					</tr>';
			$cnt++;
			$bil++;
			//$totalFee += $GetMember->fields('totalFee');
			//$totalShare += $GetMember->fields('totalShare');				
			$GetMember->MoveNext();
		}
		$GetMember->Close();

		//.......... check ...............
		if ($apply) {
			$sSQL10 = "SELECT *
			FROM dividen
			WHERE startYear = " . $yymm . " AND statusKhd = 1";
			$rsChecking = &$conn->Execute($sSQL10);
			if ($rsChecking->RowCount() > 0) {
				print '<script>alert("Permohonan Dividen telah dikemaskini di dalam sistem !");</script>';
			}
		}


		//..........end check ............


		print '</table>
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
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr-->';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
		}
	} // end of ($GetMember->RowCount() <> 0)
	// end of ($q == "" AND $dept == "")

} else {

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesMonthly\')">Penyata Wajib Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesYearly\')">Penyata Wajib Tahunan</a>
		</td>
	</tr>
    ';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareMonthly\')">Penyata Syer Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareYearly\')">Penyata Syer Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'loanUserYearly\')">Penyata Pembiayaan Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberMonthly\')">Penyata Urusniaga Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberYearly\')">Penyata Urusniaga Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberPenyataYearly\')">Penyata Tahunan Anggota</a>
		</td>
	</tr>';
}
print ' <br><br>
</table>
</form>
</div>';

//include("footer.php");	

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}
	
	function ITRActionButtonClick(rpt) {
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
				alert(\'Sila pilih satu anggota sahaja \');
			} else {
				if (rpt == "memberMonthly" )  {
					url = "memberMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "memberYearly" )  {
					url = "memberYearly.php?yr=' . $yy . '&id=" + pk;
				} else if (rpt == "memberLoan" )  {
					url = "memberLoan.php?pk=" + pk;
				} else if (rpt == "loanUserYearly" )  {
					url = "loanUserYearly.php?pk="+ pk +"&yr=' . $yy . '";
				} else if (rpt == "shareMonthly" )  {
					url = "shareMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "shareYearly" )  {
					url = "shareYearly.php?yr=' . $yy . '&id=" + pk;
				} else if (rpt == "feesMonthly" )  {
					url = "feesMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "feesYearly" )  {
					url = "feesYearly.php?yr=' . $yy . '&id=" + pk;
				}else if (rpt == "feesYearly_all" )  {
					url = "../kofrim/feesYearly_all.php?yr=' . $yy . ';
				}else if (rpt == "memberPenyataYearly" )  {
					url = "memberPenyataYearly.php?pk="+ pk +"&yr=' . $yy . '&id=" + pk;
				}else if (rpt == "loanUserYearly_all" )  {
					url = "../kofrim/loanUserYearly_all2.php?pk="+ pk +"&yr=' . $yy . '&id=" + pk;
				}
				
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			}
		}
	}

	function selectPenyata(rpt) {
		if (rpt == "feesMonthly" || rpt == "shareMonthly" || rpt == "memberMonthly") {
			url = "selMthYear.php?rpt="+rpt+"&id=' . $ID . '";
		} else if (rpt == "rptG2Dept") {
			url = "selYear.php?rpt="+rpt+"&id=' . $ID . '";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=' . $ID . '";
		}
		
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&dept=' . $dept . '";
	}

	function selectPop(rpt) {
		if (rpt == "greImportBul") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

	function ITRActionButtonClick_old(rpt) {
		if (rpt == "BulananU") {
			url = "memberMonthly.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanU") {
			url = "memberYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "SenaraiP") {
			url = "memberLoan.php?pk=' . $pk[0] . '";
		} else if (rpt == "TahunanP") {
			url = "loanUserYearly.php?pk=' . $pk[0] . '&yr=' . $yrS . '";
		} else if (rpt == "BulananS") {
			url = "shareMonthly.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanS") {
			url = "shareYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanY") {
			url = "feesYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "PenyataTahunan") {
			url = "memberPenyataYearly.php?pk=' . $pk[0] . '&yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "Import") {
			url = "greImportPot.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "Eksport") {
			url = "greEksportPot.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		}

		window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
	}
</script>';

function RoundCurrency($fValue_)
{
	if ((ceil($fValue_) - $fValue_) >= 0.5) {
		$fTemp_ = floor($fValue_) + 0.5;
	} else {
		$fTemp_ = ceil($fValue_);
	}


	return $fTemp_;
}
