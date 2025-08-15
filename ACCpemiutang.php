<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	ACCpemiutang.php
*          Date 		: 	16/5/2024 - list all syarikat pemiutang with their opening balance and due amounts
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";
if (!isset($page))		$page="pmtg";

if ($q <> "") 	{
	if ($by == 1) {
		$getQ .= " AND name like '%" . $q. "%'";	
	} else if ($by == 2) {
		$getQ .= " AND code like '%" . $q. "%'";
	} 
}
// sql select dari table mana 
$sSQL  = "	SELECT ID AS companyID, code, name, b_Baddress FROM generalacc WHERE category='AB'";
$sSQL .= $getQ." ORDER BY companyID ";

$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec-1);

$TotalRec 	= $GetMember->RowCount();
$TotalPage 	=  ($TotalRec/$pg);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>'.$emaNetis.'</title>
<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />	
</head>

<script language="JavaScript">

	function selSetAnggota(companyID,name,b_Baddress,code,totTunggak,type) 
	{
		if(type == "f"){
		window.opener.document.MyForm.companyID.value = companyID;
		window.opener.document.MyForm.name.value = name;
		window.opener.document.MyForm.b_Baddress.value = b_Baddress;
		window.opener.document.MyForm.code.value = code;
        window.opener.document.MyForm.amt.value = totTunggak;
		window.opener.document.MyForm.PINo.value = "";
		//window.opener.document.MyForm.kodGL.value = b_kodGL;

		window.close();
		}
	}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';


print '
<form name="MyForm" action="'.$PHP_SELF.'?refer='.$refer.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="'.$by.'">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
		<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
		<h5 class="card-title">Senarai Syarikat Pemiutang</h5>
				
		<tr>
		<td class="Data">
		Carian melalui 
		<select name="by" class="form-select-xs">'; 
		if ($by==1)	
		print '<option value="1" selected>Nama Syarikat</option>';else print '<option value="1">Nama Syarikat</option>';	
		if ($by==2)	print '<option value="2" selected>No Kod</option>'; else print '<option value="2">No Kod</option>';				
		print '		
		</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="form-control-sm">
           	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-primary" id="compButton" 
				value="Tambah" 
				onclick="openPopupAndRefresh();">
		</td>
		</tr>';

if ($GetMember->RowCount() == 0) {
	print '<tr><td	class="Label" align="center" height=50 valign=middle>
			<b>- Tiada sebarang maklumat syarikat.  -</b>
			</td></tr>';
} else {				
	if ($GetMember->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr class="table-primary">
					<td class="header" align="center"><b>Bil</b></td>
					<td class="header" ><b>Nama Syarikat</b></td>
					<td class="header" ><b>Kod Syarikat</b></td>
					<td class="header" ><b>Alamat Billing</b></td>
					<td class="header" ><b>Jumlah Opening Balance (RM)</b></td>
					<td class="header" ><b>Jumlah Terbayar (RM)</b></td>
					<td class="header" ><b>Jumlah Tertunggak (RM)</b></td>
					</tr>';

					while (!$GetMember->EOF && $cnt <= $pg) {
						$companyID			= $GetMember->fields(companyID);
						$name 				= str_replace("'", "", $GetMember->fields(name));
						$code 	    		= str_replace("'", "", $GetMember->fields(code));	
						// $code				= $GetMember->fields(code);				
						$b_Baddress			= $GetMember->fields(b_Baddress);

						$sql        		= "SELECT COALESCE(SUM(b_crelim), 0) AS totOpenBalComp FROM generalacc WHERE category = 'AB' AND ID = '$companyID'";
						$rs 				= $conn->Execute($sql);
						$totOpenBalComp		= $rs->fields(totOpenBalComp);	
						if(dlookup("PINo","billacc","diterima_drpd='" . $companyID ."'") == ''){
							$sqlT			= "SELECT balance 
												FROM billacc 
												WHERE PINo = '' 
												AND diterima_drpd = '$companyID' 
												ORDER BY id DESC 
												LIMIT 1
												";	
							$rsT 			= $conn->Execute($sqlT);
							if (!$rsT->EOF) {
								$totTunggak = $rsT->fields['balance'];
							} else {
								$totTunggak = $totOpenBalComp;
							}
						}
						print '
						<tr>
							<td class="Data" align="center">'.$bil.'</td>';
							//////////////////////////////////
							print '
							<td class="Data nowrap">';
							
					

				print '
                <a href="javascript:selSetAnggota(\''.$companyID. '\',\''.$name. '\',\''. $b_Baddress.'\',\''.$code. '\',\''.$totTunggak. '\',\''.  $refer.  '\');">'.$name.'</a>';	
						
							/////////////////////
							print '
							<td class="Data" align="left">'.$code.'&nbsp;</td>
							<td class="Data" align="left">'.$b_Baddress.'&nbsp;</td>
							<td class="Data" align="left">'.number_format($totOpenBalComp,2).'&nbsp;</td>
							<td class="Data" align="left">'.number_format($totOpenBalComp-$totTunggak,2).'&nbsp;</td>
							<td class="Data" align="left">'.number_format($totTunggak,2).'&nbsp;</td>

						</tr>';
					$cnt++;
					$bil++;
					$GetMember->MoveNext();
				}	
		print ' 
		</table>
			</td>
		</tr>		
		<tr>
			<td>';
				if ($TotalRec > $pg) {
					print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
					
					if ($TotalRec % $pg == 0) {
						$numPage = $TotalPage;
					} 

					else {
						$numPage = $TotalPage + 1;
					}
					print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
					if($refer) $rfr = '&refer='.$refer; else $rfr= '';

					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'?StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&by='.$by.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a> &nbsp; &nbsp;';
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
						<b>- Tiada rekod mengenai syarikat  -</b>
					</td></tr>';
	} // end of ($GetMember->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">'.$retooFetis.'</p>
</body>
</html>';

print '
<script language="JavaScript">

//refresh this parent page after adding new creditor
function openPopupAndRefresh() {
    const popup = window.open(
        \'generalAddUpdateACC.php?action=tambah&cat=AB&sub=&page='.$page.'\',
        \'sort\',
        \'top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\'
    );

    const timer = setInterval(function () {
        if (popup.closed) {
            clearInterval(timer);  // Stop checking once the window is closed
            location.reload();     // Refresh the parent page
        }
    }, 500); // Check every 500ms if the pop-up has closed
}

</script>';
?>