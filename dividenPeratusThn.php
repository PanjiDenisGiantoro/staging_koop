<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	dividenPeratusThn.php
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
date_default_timezone_set("Asia/Jakarta");
	
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$sFileName = 'dividenPeratusThn.php'; 
$sFileRef  = 'Edit_memberStmt.php';
$title     = "Dividen Peratus Tahunan";
$updatedDate = date("Y-m-d H:i:s");
$updatedBy 	= get_session("Cookie_userName");

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

//--- Prepare department list
$deptList = Array();
$deptVal  = Array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0){
	while (!$rs->EOF) {
		//array_push ($deptList, $rs->fields(deptCode).'-'.$rs->fields(deptName));
		array_push ($deptList, $rs->fields(deptName));
		array_push ($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

//$GetMember = ctMemberStatusDept($q,$by,"1",$dept);
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.status <> 0";;
	if ($dept <> "") 	{
		$sWhere .= " AND b.departmentID = " . tosql($dept,"Number");
	}

	if ($q <> "") 	{
		if ($by == 1) {
			$sWhere .= " AND b.memberID like '%" .$q ."%'";			
		} else if ($by == 2) {
			$sWhere .= " AND a.name like '%" . $q. "%'";
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like '%" . $q. "%'";		
		}
	}

	if($ID) {
		$sWhere .= " AND b.userID = " . tosql($ID,"Text");
	}
	
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
	//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) ASC";


$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec-1);
$TotalRec = $GetMember->RowCount();
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
    <td colspan="3" class="headerblue" >Pembahagian Peratus Tahunan (%) </td>
  </tr>
  <tr class="DataB">
    <td width="120" >MYA</td>
    <td width="10">:</td>
    <td width="1487"><input type="text" name="MYA" value = "'.$MYA.'" size="5" /></td>
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
$yyr = $yy -1 ;

print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap colspan="3" height="20">&nbsp;</td>
						<td nowrap height="20" colspan="2" nowrap><div align="center">Simpanan</div></td>
					</tr>
					<tr class="header">
						<td nowrap align="center"> Bil</td>
						<td nowrap>&nbsp;No/Nama Anggota</td>
						<td nowrap>&nbsp;No KP Baru</td>
						<td nowrap align="center">&nbsp;Yuran '.$yyr.'</td>
						<td nowrap align="center">&nbsp;Dividen Syer '.$yy.'</td>
					</tr>';	
		$totalFee = 0;
		$totalShare = 0;					
		
		while (!$GetMember->EOF && $cnt <= $pg) {
		
		
//..................... checking data Yuran ............................
	

	   
	   
	    $yrmth = $yy.$mm;
		
		$sSQL = "SELECT   b.name, a.UserID, a.yrmth,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, users b
		WHERE
		a.deductID in (1595,1596,1607)
		AND a.userID = b.userID
		AND a.userID = ".$GetMember->fields(userID)."
		AND SUBSTRING(a.yrmth,1,4) < ".$yy."
		GROUP BY a.userID";
        $rsFee = &$conn->Execute($sSQL);	
		
			$feeDB = $rsFee->fields(yuranDb);
			$feeKT = $rsFee->fields(yuranKt);
			
			$feeYuranTkini = $feeKT- $feeDB;
			$feeYuranTkiniP = number_format($feeKT- $feeDB,2);
					  	
			$sSQL14 = "SELECT *
			FROM dividenyear
			WHERE YEAR = ".$yy."
			AND UserID = ".$GetMember->fields(userID)." ";
 			$rsDividen = &$conn->Execute($sSQL14);
            $feeYearDiv = number_format($rsDividen->fields(yuranAmtIssue),2);

			
			
			
// ................... Apply Insert .................

if ($apply) {


while (!$GetMember->EOF ) {

$sSQL = "SELECT   b.name, a.UserID, a.yrmth,
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction a, users b
		WHERE
		a.deductID in (1595,1596,1607)
		AND a.userID = b.userID
		AND a.userID = ".$GetMember->fields(userID)."
		AND SUBSTRING(a.yrmth,1,4) < ".$yy."
		GROUP BY a.userID";
        $rsFee3 = &$conn->Execute($sSQL);	
		
			$feeDB2 = $rsFee3->fields(yuranDb);
			$feeKT2 = $rsFee3->fields(yuranKt);
			$feeYuranTkini2 = $feeKT2- $feeDB2;


$DocDiv = $yy;
$docNoDiv = 'DIV'.$DocDiv;
$sSQL11 = "SELECT *
			FROM dividenyear
			WHERE YEAR = ".$yy."
			AND UserID = ".$GetMember->fields(userID)." ";
			$rsChecking = &$conn->Execute($sSQL11);

	
		   // $yrmth = $yy.$mm;
			$userID = $GetMember->fields(userID);
			$deductID ='1607';
			$DivAmt = ($feeYuranTkini2 * $MYA)/100 * 1 ;
			$tbgAmt = ($feeYuranTkini2 * $tbg)/100 * 1 ;
		
			
			
if($rsChecking->RowCount() <= 0)	{				
if ($feeYuranTkini2 > 0) {			

$sSQL4	= "INSERT INTO dividenyear (" . 
				
				  "yuranrate," . 
				  "yuranAmtIssue," . 
				  "TbgRate," .			
				  "TbgAmtIssue," . 
				  "status," . 
				  "approvedDate," . 
				  "approvedBy," .
				  "createdDate," .  
				  "createdBy," .
				  "AmtYuranT," . 
				  "userID," . 
				  "YEAR)" . 
				  " VALUES (" . 
				"'". $MYA . "', ".
				"'". $DivAmt . "', ".
				"'". $tbg . "', ".
				"'". $tbgAmt . "', ".
				"'". 1 . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $feeYuranTkini2 . "', ".
				"'". $userID . "', ".
				"'". $yy . "')";
				
$rsInstDiv = &$conn->Execute($sSQL4);							

}
}

$GetMember->MoveNext();
}



}



//................. end test apply...................		


			$totalFees = number_format(getFees($GetMember->fields(userID), $yr),2);
			//sprintf(number_format($GetMember->fields('totalFee'),2,'.',','))
			$totalShares = number_format(getShares($GetMember->fields(userID), $yr),2);
			//sprintf(number_format($GetMember->fields('totalShare'),2,'.',','))
			print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">'.$GetMember->fields('memberID').' - '.$GetMember->fields(name).'</td>
						<td class="Data">&nbsp;'.$GetMember->fields('newIC').'</td>						
				
						<td class="Data" align="right">'.$feeYuranTkiniP.'&nbsp;</td>
						<td class="Data" align="right">'.$feeYearDiv.'&nbsp;</td>
					</tr>';
				$cnt++;
				$bil++;
				//$totalFee += $GetMember->fields('totalFee');
				//$totalShare += $GetMember->fields('totalShare');				
			$GetMember->MoveNext();
		}
		$GetMember->Close();





//.......... check ...............
if($apply){
$sSQL10 = "SELECT *
			FROM dividenyear
			WHERE YEAR = ".$yy."" ;
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
			<td>';
				if ($TotalRec > $pg) {
					print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
					if ($TotalRec % $pg == 0) {
						$numPage = $TotalPage;
					} else {
						$numPage = $TotalPage + 1;
					}
					print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'?&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&by='.$by.'&dept='.$dept.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a> &nbsp; &nbsp;';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
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
	
	function ITRActionButtonClick(rpt) {
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
				alert(\'Sila pilih satu anggota sahaja \');
			} else {
				if (rpt == "memberMonthly" )  {
					url = "memberMonthly.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "memberYearly" )  {
					url = "memberYearly.php?yr='.$yy.'&id=" + pk;
				} else if (rpt == "memberLoan" )  {
					url = "memberLoan.php?pk=" + pk;
				} else if (rpt == "loanUserYearly" )  {
					url = "loanUserYearly.php?pk="+ pk +"&yr='.$yy.'";
				} else if (rpt == "shareMonthly" )  {
					url = "shareMonthly.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "shareYearly" )  {
					url = "shareYearly.php?yr='.$yy.'&id=" + pk;
				} else if (rpt == "feesMonthly" )  {
					url = "feesMonthly.php?yrmth='.$yy.$mm.'&id=" + pk;
				} else if (rpt == "feesYearly" )  {
					url = "feesYearly.php?yr='.$yy.'&id=" + pk;
				}else if (rpt == "feesYearly_all" )  {
					url = "../kofrim/feesYearly_all.php?yr='.$yy.';
				}else if (rpt == "memberPenyataYearly" )  {
					url = "memberPenyataYearly.php?pk="+ pk +"&yr='.$yy.'&id=" + pk;
				}else if (rpt == "loanUserYearly_all" )  {
					url = "../kofrim/loanUserYearly_all2.php?pk="+ pk +"&yr='.$yy.'&id=" + pk;
				}
				
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
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
			url = "memberYearly.php?yr='.$yrS.'&id='.$pk[0].'";
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