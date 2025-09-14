<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	ACCprojects.php
*          Date 		: 	05/02/2024
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($by))		$by="1";
if (!isset($dept))		$dept="";

if ($q <> "") 	{
	if ($by == 1) {
		$getQ .= " AND name like '%" . $q. "%'";	
	} else if ($by == 2) {
		$getQ .= " AND code like '%" . $q. "%'";
	} else if ($by == 3) {
		$getQ .= " AND nameproject like '%" . $q. "%'";
	}  
}
// sql select dari table mana 
$sSQL = "	SELECT a.ID AS projectID, a.nameproject, a.compID, a.amount AS nilaiPelaburan, b.code AS companyCode, b.name AS companyName FROM investors a, generalacc b WHERE b.ID = a.compID";
$sSQL .= $getQ." ORDER BY projectID ";

$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec-1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec/$pg);

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

	function selSetAnggota(projectID,companyName,companyCode,nameproject,nilaiPelaburan,type) 
	{
		if(type == "f"){
		window.opener.document.MyForm.projectID.value = projectID;
		window.opener.document.MyForm.bayaran_kpd.value = companyName;
        window.opener.document.MyForm.kod_syarikat.value = companyCode;
		window.opener.document.MyForm.nama_projek.value = nameproject;
        window.opener.document.MyForm.nilaiPelaburan.value = nilaiPelaburan;

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
		<h5 class="card-title">Senarai Projek oleh Syarikat</h5>
				
		<tr>
		<td class="Data">
		Cari Berdasarkan 
		<select name="by"  class="form-select-xs">'; 
		if ($by==1)	
		print '<option value="1" selected>Nama Syarikat</option>';else print '<option value="1">Nama Syarikat</option>';	
		if ($by==2)	print '<option value="2" selected>No. Kod</option>'; else print '<option value="2">No. Kod</option>';
		if ($by==3)	print '<option value="3" selected>Nama Projek</option>'; else print '<option value="3">Nama Projek</option>';				
		print '		
		</select>
			<input type="text" name="q" value="" class="form-control-sm" maxlength="50" size="30" class="Data">
           	<input type="submit" class="btn btn-sm btn-secondary" value="Cari">
		</td>
		</tr>';

if ($GetMember->RowCount() == 0) {
	print '<tr><td	class="Label" align="center" height=50 valign=middle>
			<b>- Tiada sebarang maklumat projek syarikat.  -</b>
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
						<td class="header" ><b>Nama Projek</b></td>
					</tr>';

					while (!$GetMember->EOF && $cnt <= $pg) {
						$projectID			= $GetMember->fields(projectID);
						$nameproject 		= str_replace("'", "", $GetMember->fields(nameproject));
						//$code 	= str_replace("'", "", $GetMember->fields(code));	
						$companyCode		= $GetMember->fields(companyCode);			
                        $companyName 		= str_replace("'", "", $GetMember->fields(companyName));
						$nilaiPelaburan = $GetMember->fields(nilaiPelaburan);
                        // $nilaiPelaburan = number_format($GetMember->fields(nilaiPelaburan), 2, '.', ',');
						print '
						<tr>
							<td class="Data" align="center">'.$bil.'</td>';
							//////////////////////////////////
							print '
							<td class="Data">';
							
					

				print '<a href="javascript:selSetAnggota(\''.$projectID. '\',\''.$companyName.'\',\''. $companyCode.'\',\''.$nameproject. '\',\''.$nilaiPelaburan. '\',\''.  $refer.  '\');">'.$companyName.'</a>';	
						
							/////////////////////
							print '
							<td class="Data" align="left">'.$companyCode.'</td>
                            <td class="Data" align="left">'.$nameproject.'</td>

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
					print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
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
						<b>- Tiada rekod mengenai projek syarikat  -</b>
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
?>