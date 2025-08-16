<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my//
*          Filename		: 	laporanpecahanALLjubli.php
*          Date 		: 	15/03/2017
*********************************************************************************/
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";

$yr = (int)substr($yrmth,0,4);
$mth = (int)substr($yrmth,4,2);
$yymm = substr($yrmth,0,4).substr($yrmth,4,2);

include("header.php");	
include("sekataQry.php"); 
date_default_timezone_set("Asia/Kuala_Lumpur");	

if (get_session("Cookie_groupID") <> 1 
AND get_session("Cookie_groupID") <> 2 ) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';}
$sFileName = 'laporanpecahanSPSN.php'; 
$sFileRef  = 'Edit_memberStmtPotongan.php';
$title     = "Laporan Pecahan Pembiayaan Jubli Emas Anak Syarikat Pada Bulan ".$mth." Tahun ".$yr.".";

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

$yyT = $yr-1;
if ($mth == 12){
$mmT2 = '01';
$yyT = $yr; 
}elseif($mth == 10 or $mth == 11 or $mth == 9){ 
$mmT2 = $mth + 1;
}else{
$mmT = $mth + 01 ; 
$mmT2 = '0'.$mmT ;
}
$yymmTT = $yyT.$mmT2;

$sSQL = "SELECT DISTINCT (a.userID),a.name,c.departmentID FROM users a, potbulan b, userdetails c, potbulanlook d WHERE b.loanID = d.loanID AND a.userID = b.userID AND a.userID = c.userID AND b.status IN (1) AND c.status IN (1) AND d.loanType IN (2057) AND c.departmentID IN (1598,1600,1599,1584,1706,1841,1770,1664) AND d.yrmth between '".$yymmTT."' and '".$yymm."' ORDER BY CAST(a.userID AS SIGNED INTEGER) DESC";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec-1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="'.$StartRec.'">
<input type="hidden" name="by" value="'.$by.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td ><b class="maroonText">'.strtoupper($title).'</b></td>
	</tr>';

if (get_session("Cookie_groupID") > 0) {
print'<tr valign="top" class="textFont"><td align="left"><table><tr></tr></table></td></tr>';

if ($GetMember->RowCount() <> 0) {  
print ' <table border="0" id="example" cellspacing="1" cellpadding="2" width="100%" class="lineBG" >
		<tr class="header">
		<tr class="header">
		<td nowrap rowspan="1" height="20">Bil</td>
		<td nowrap>MEMBER</td>
		<td nowrap>Nama Anggota</td>
		<td nowrap>Jabatan</td>
		<td nowrap>BOND 36</td>
		<td nowrap>JE01</td>
		<td nowrap>UJBE</td>
		</tr>';	
		$bil = 1;	
		while (!$GetMember->EOF) {
		//TUNAI
		$getJubli = getJubli($GetMember->fields(userID),$yymmTT,$yymm);
		$jubli = $getJubli->fields(bondNo);
		$pokokjubli = $getJubli->fields(pokok);
		$untungjubli = $getJubli->fields(untung);
print ' <tr>
		<td class="Data" align="right">' .$bil. '&nbsp;</td>
		<td class="Data">'.$GetMember->fields(userID).'</td>
		<td class="Data">'.$GetMember->fields(name).'</td>
		<td class="Data">'.dlookup("general","name","ID=".tosql($GetMember->fields('departmentID'),"Number")).'</td>
		<td class="Data">'.$jubli.'</td>	
		<td class="Data">'.$pokokjubli.'</td>
		<td class="Data">'.$untungjubli.'</td>';
print '</tr>';
		$GetMember->MoveNext();
		$bil = $bil+1;
		}
		$GetMember->Close();
		print '</table><tr><td>';			
		print '</td></tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk '.$title.'  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b><hr size=1"></td></tr>';
		}
	} // end of ($GetMember->RowCount() <> 0)
}

else{
	
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
		document.location = "' . $sFileName . '?yy='.$yy.'&mm='.$mm.'&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

	function selectPop(rpt) {
		if (rpt == "greImportBul") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  
</script>
';?>