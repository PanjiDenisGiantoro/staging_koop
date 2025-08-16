<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	dividenPeratusBlnResit.php
*          Date 		: 	15/6/2006
*********************************************************************************/
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 100;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";
if (!isset($mth)) $mth	= date("n");                 		
if (!isset($yr)) $yr	= date("Y");
if (!isset($mm))	$mm=date("m");//"ALL";
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
include("header.php");	
include("koperasiQry.php");	
date_default_timezone_set("Asia/Kuala_Lumpur");
	
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$sFileName = 'dividenPeratusBlnResit.php'; 
$sFileRef  = 'Edit_memberStmt.php';
$title     = "Dividen Peratus Bulanan (resit)";

$updatedDate = date("Y-m-d H:i:s");   
$updatedBy 	= get_session("Cookie_userName");

// ......................................................

//...............................................................
if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

//$GetMember = ctMemberStatusDept($q,$by,"1",$dept);



$yrmth = $yy.$mm;	
$sSQL555 = "SELECT DISTINCT  d.name, a.UserID, a.yrmth, b.memberID, b.newIC,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, userdetails b, resit c , users d
		WHERE
		a.deductID in (1595,1596)
		AND a.userID = b.userID
		AND d.userID = a.userID
		AND a.docNo = c.no_resit
		AND month(c.tarikh_resit) = ".$mm."
        AND year(c.tarikh_resit) = ".$yy."
		GROUP BY a.userID
		order by CAST( b.memberID AS SIGNED INTEGER ) ASC ";
$Get = &$conn->Execute($sSQL555);
		

//$userID123 = $Get->fields(UserID);

$Get->Move($StartRec-1);
$TotalRec = $Get->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '
<form name="MyForm" action=' .$sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="'.$StartRec.'">
<input type="hidden" name="by" value="'.$by.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td ><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>';

if (get_session("Cookie_groupID") > 0) {
print'    <tr valign="top" class="textFont">
	   	<td align="left">
			<table>
				<tr>
					<td class="textFont">Pilihan Bulan/Tahun</td>
					<td class="textFont">:&nbsp;
						Bulan   : 
						<select name="mm" class="data" onchange="document.MyForm.submit();">
						<option value="ALL"';
						if ($mm == "ALL") print 'selected';
						for ($j = 1; $j < 13; $j++) {
							print '	<option value="'.$j.'"';
							if ($mm == $j) print 'selected';
							print '>'.$j;
						}
							print '</select>
						Tahun  : 
						<select name="yy" class="data" onchange="document.MyForm.submit();">';
						for ($j = 1989; $j <= 2079; $j++) {
							print '	<option value="'.$j.'"';
							if ($yy == $j) print 'selected';
							print '>'.$j;
						}
							print '</select>					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<table width="100%" border="0">
  <tr class="DataB">
    <td colspan="3" class="headerblue" >Pembahagian Peratus Bulanan (%) </td>
  </tr>
  <tr class="DataB">
    <td width="120" >MYA</td>
    <td width="10">:</td>
    <td width="1487"><input type="text" name="MYA" value="'.$MYA.'" size="5" /></td>
  </tr>
  <tr class="DataB">
    <td>Akaun Tabungan </td>
    <td>:</td>
    <td>
      <input type="text" name="tbg" value="'.$tbg.'" size="5" />
   </td>
  </tr>
  <tr class="DataB">
  </tr>
      <td><input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="apply" value="apply" />  </td>
  </tr>
</table>';
	
	
		
	if ($Get->RowCount() <> 0) {  
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
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap colspan="3" height="20">&nbsp;</td>
						<td nowrap height="20" colspan="2" nowrap><div align="center">Simpanan</div></td>

					</tr>
					<tr class="header">
						<td nowrap rowspan="1" height="20">&nbsp; Bil</td>
						<td nowrap>&nbsp;No/Nama Anggota</td>
						<td nowrap>&nbsp;No KP Baru</td>

						<td nowrap align="center">&nbsp;Yuran Bulan</td>
						<td nowrap align="center">&nbsp;Dividen Syer '.$peratusaham.'</td>
					</tr>';	
		$totalFee = 0;
		$totalShare = 0;
		
			
	
		while (!$Get->EOF) {
	
//..................... checking data Yuran ............................
	
	
	    $yrmth = $yy.$mm;
		
		
		$sSQL = "SELECT   b.name, a.UserID, a.yrmth,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, users b, resit c
		WHERE
		a.deductID in (1595,1596)
		AND a.userID = b.userID
		AND a.userID = ".$Get->fields(memberID)."
		AND a.docNo = c.no_resit
		AND month(c.tarikh_resit) = ".$mm."
        AND year(c.tarikh_resit) = ".$yy."
		GROUP BY a.userID";
        $rsFee = &$conn->Execute($sSQL);	
		
			$feeDB = $rsFee->fields(yuranDb);
			$feeKT = $rsFee->fields(yuranKt);
			$feekiraMonth = $feeKT - $feeDB;
           $feeMonth = number_format($feeKT - $feeDB,2);
			
					  	
		$sSQL8 = "SELECT  * FROM dividen 
		WHERE
		userID = ".$Get->fields(memberID)."
		AND startYear = ".$yymm."
		AND status = '2' ";
		$rsFeeDiv = &$conn->Execute($sSQL8);

		$peratusaham = $rsFeeDiv->fields(amtFee);
		
	        $feeMonthDiv = number_format($rsFeeDiv->fields(AmtFeeD),2);	
			$feeYuranTkini = $feeKT  - $feeDB;
			$feeYuranTkiniP = number_format($feeKT  - $feeDB,2);
// ................... Apply Insert .................


if ($apply) {

$sSQL15 = "SELECT *
			FROM dividen
			WHERE startYear = ".$yymm."
			AND status = '2' ";
 			$rsChecking5 = &$conn->Execute($sSQL15);
			$kiraBil =$rsChecking5->RowCount();
						
if ($kiraBil > 0 ) {

$sSQL16 = "DELETE FROM dividen WHERE startYear = ".$yymm."
			AND status = '2' ";
			$rsChck16 = &$conn->Execute($sSQL16);

}

while (!$Get->EOF) {

$jumDiv = ($MYA + $tbg);

$sSQL = "SELECT   b.name, a.UserID, a.yrmth,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, users b, resit c
		WHERE
		a.deductID in (1595,1596)
		AND a.userID = b.userID
		AND a.userID = ".$Get->fields(memberID)."
		AND a.docNo = c.no_resit
		AND month(c.tarikh_resit) = ".$mm."
        AND year(c.tarikh_resit) = ".$yy."
		GROUP BY a.userID";
$rsCheck2 = &$conn->Execute($sSQL);			
	       	
			$feeDB2 = $rsCheck2->fields(yuranDb);
			$feeKT2 = $rsCheck2->fields(yuranKt);
			$feekiraMonth2 = $feeKT2 - $feeDB2;
		   
		    $feeMonthDiv2 = number_format($rsCheck2->fields(AmtFeeD),2);	
			$feeYuranTkini2 = $feeKT2  - $feeDB2;

			$userID = $Get->fields(memberID);
			$deductID ='1607';
			
			if ($mm<=11){
			
			$divMth = (12-$mm)/ 12 ;
			$DivAmt = ($feeYuranTkini2 * $MYA)/100 * $divMth ;
			$DivAmtShare = ($feeYuranTkini2 * $tbg)/100 * $divMth ;   
			
			}


		
if ($feekiraMonth2 > 0) {			



$sSQL4	= "INSERT INTO dividen (" . 
				
				  "startYear," . 
				  "userID," . 
				  "amtFee," .			
				  "amtShare," . 
				  "issueDate," . 
				  "clearDate," . 
				  "createdDate," . 
				  "createdBy," . 
				  "updatedDate," . 
				  "updatedBy," . 
				  "status,".
				  "AmtFeeD," .
				  "AmtYuranT," .
				  "AmtShareD)" . 
				  " VALUES (" . 
				"'". $yymm. "', ".
				"'". $userID . "', ".
				"'". $MYA . "', ".
				"'". $tbg . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". 2 . "', ".
				"'". $DivAmt . "', ".
				"'". $feeYuranTkini2 . "', ".
				"'". $DivAmtShare . "')";
				
$rsInstDiv = &$conn->Execute($sSQL4);							

}

$Get->MoveNext();
}
//}
	
}

//print '<script>alert("Permohonan Dividen telah dikemaskini di dalam sistem !");


//................. end test apply...................

			//$feeMonth = number_format($feeMonthly,2);
			//$totalShares = number_format(getShares($GetMember->fields(userID), $yr),2);

			print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">'.$Get->fields(memberID).' - '.$Get->fields('name').'</td>
						<td class="Data">&nbsp;'.$Get->fields(newIC).'</td>						
					
						<td class="Data" align="right">'.$feeYuranTkiniP.'&nbsp;</td>
						<td class="Data" align="right">'.$feeMonthDiv.'&nbsp;</td>
					</tr>';
				$cnt++;
				$bil++;
				//$totalFee += $GetMember->fields('totalFee');
				//$totalShare += $GetMember->fields('totalShare');				
			$Get->MoveNext();
		}
		$Get->Close();

//.......... check ...............
if($apply){
$sSQL10 = "SELECT *
			FROM dividen
			WHERE startYear = ".$yymm."" ;
 			$rsChecking = &$conn->Execute($sSQL10);


if($rsChecking->RowCount() > 0)
{
print '<script>alert("Permohonan Dividen telah dikemaskini di dalam sistem !");</script>';

}
}


//..........end check ............


		print '</table>
			</td>
		</tr>		
		<tr>
			<td>
			
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $Get->RowCount() . '</b></td>
		</tr-->';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk '.$title.'  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	} // end of ($GetMember->RowCount() <> 0)
// end of ($q == "" AND $dept == "")

}else{
	
print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesMonthly\')">Penyata Yuran Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesYearly\')">Penyata Yuran Tahunan</a>
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
print ' 
</table>
</form>';

include("footer.php");	

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
	
	function ITRActionButtonClick(v) {
	e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
	          if(confirm(\'Adakah Anda Pasti Untuk Di Rekodkan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
		}
	}

	function selectPenyata(rpt) {
		if (rpt == "feesMonthly" || rpt == "shareMonthly" || rpt == "memberMonthly") {
			url = "selMthYear.php?rpt="+rpt+"&id='.$ID.'";
		} else if (rpt == "rptG2Dept") {
			url = "selYear.php?rpt="+rpt+"&id='.$ID.'";
		} else {
			url = "selYear.php?rpt="+rpt+"&id='.$ID.'";
		}
		
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&dept='.$dept.'";
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
			url = "memberMonthly.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "TahunanU") {
			url = "membe Yearly.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "SenaraiP") {
			url = "memberLoan.php?pk='.$pk[0].'";
		} else if (rpt == "TahunanP") {
			url = "loanUserYearly.php?pk='.$pk[0].'&yr='.$yrS.'";
		} else if (rpt == "BulananS") {
			url = "shareMonthly.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "TahunanS") {
			url = "shareYearly.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "TahunanY") {
			url = "feesYearly.php?yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "PenyataTahunan") {
			url = "memberPenyataYearly.php?pk='.$pk[0].'&yr='.$yrS.'&id='.$pk[0].'";
		} else if (rpt == "Import") {
			url = "greImportPot.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		} else if (rpt == "Eksport") {
			url = "greEksportPot.php?yrmth='.sprintf("%04d%02d",$yrS,$mthS).'&id='.$pk[0].'";
		}

		window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
	}
</script>';

?>