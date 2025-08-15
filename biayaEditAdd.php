<?php
/*********************************************************************************
*          	Project	:	iKOOP.com.my
*          	Filename:	biayaEditAdd.php
*          	Date 	: 	06/10/2003
*   		Used By	:	loanApply.php
*********************************************************************************/
//include ("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID){

print '<script>alert("'.$errPage.'"); parent.location.href = "index.php";</script>';

}


if($user) { $pk = get_session('Cookie_userID'); $user = 1; }
if($loanID) $strpk = "&pk=".$loanID; else $strpk = '';
$sFileName		= "?vw=biayaEdit".$strpk;
if (get_session("Cookie_groupID") == 0) $sActionFileName= "?vw=biayaEdit&mn=3"; else $sActionFileName= "?vw=biayaEdit&mn=3&pk=".$loanID;
$title     		= "Kemaskini Maklumat Pembiayaan Peribadi";


$updatedBy 	= get_session("Cookie_userName");
$updatedDate = date("Y-m-d H:i:s");   

if($SubmitForm == 'Kemaskini'){
	foreach($valueD as $key => $value){

	$checkStates = "SELECT * FROM userstates 
	WHERE payID = ".$key." 
	AND userID = ".toSQL($userID,"Text");
	$rscheckStates = $conn->Execute($checkStates);
	//-----------
		if($rscheckStates->RowCount() > 0){
			$sSQL = "";
			$sWhere = "";
			$sWhere = "payID='" . $key . "' and userID = '" . $userID . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL= "UPDATE userstates SET " .
				  " amt='" . $value . "'" .
				  ", updateDate=" . tosql($updatedDate, "Text") .
				  ", updateBy=" . tosql($updatedBy, "Text");
			$sSQL = $sSQL . $sWhere;
			//print $sSQL.'<br>';
			
			$sSQL1 = "";
			$sWhere = "";
			$sWhere = "loanID='" . $loanID . "' and userID = '" . $userID . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL1= "UPDATE loans SET " .
				  " houseLoan='".$valuehouse."'";
			$sSQL1 = $sSQL1 . $sWhere;			
			$rs = &$conn->Execute($sSQL1);
					
			
			$rs = &$conn->Execute($sSQL);
		}elseif($value >0){
			$sSQL = "";
			$sSQL	= "INSERT INTO userstates (" . 
					  "userID, " . 
					  "payType, " . 
					  "payID, " .							
					  "amt, " .							
					  "insertBy, " .							
					  "insertDate)" . 
					  " VALUES (" . 
					  tosql($userID, "Text") . ", " .
					  tosql($payType, "Text") . ", ".
					  tosql($key, "Text") . ", " .
					  tosql($value, "Text") . ", " .
					  tosql($updatedBy, "Text") . ", " .
					  tosql($updatedDate, "Text") . ") ";
			//print $sSQL.'<br />';
			$rs = &$conn->Execute($sSQL);
		}
	
	
	foreach($valueD as $key => $valueOT){

	$checkStates = "SELECT * FROM userstates 
	WHERE payID = ".$key." 
	AND userID = ".toSQL($userID,"Text");
	$rscheckStates = $conn->Execute($checkStates);
	//-----------
		if($rscheckStates->RowCount() > 0){
			$sSQL = "";
			$sWhere = "";
			$sWhere = "payID='" . $key . "' and userID = '" . $userID . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL= "UPDATE userstates SET " .
				  " amt='" . $valueOT . "'" .
				  ", updateDate=" . tosql($updatedDate, "Text") .
				  ", updateBy=" . tosql($updatedBy, "Text");
			$sSQL = $sSQL . $sWhere;
			//print $sSQL.'<br>';
			
			$sSQL1 = "";
			$sWhere = "";
			$sWhere = "loanID='" . $loanID . "' and userID = '" . $userID . "'";
			$sWhere = " WHERE (" . $sWhere . ")";
			$sSQL1= "UPDATE loans SET " .
				  " houseLoan='" . $valuehouse . "'";
			$sSQL1 = $sSQL1 . $sWhere;			
			$rs = &$conn->Execute($sSQL1);
			
			
			$rs = &$conn->Execute($sSQL);
			
		}elseif($value >0){
			$sSQL = "";
			$sSQL	= "INSERT INTO userstates (" . 
					  "userID, " . 
					  "payType, " . 
					  "payID, " .							
					  "amt, " .							
					  "insertBy, " .							
					  "insertDate)" . 
					  " VALUES (" . 
					  tosql($userID, "Text") . ", " .
					  tosql($payType, "Text") . ", ".
					  tosql($key, "Text") . ", " .
					  tosql($valueOT, "Text") . ", " .
					  tosql($updatedBy, "Text") . ", " .
					  tosql($updatedDate, "Text") . ") ";
			//print $sSQL.'<br />';
			$rs = &$conn->Execute($sSQL);
		}
	}
	
	}
     
                alert("Maklumat telah dikemaskinikan ke dalam sistem.");
	print '<script>
	window.location.href = "'.$sActionFileName.'";
	</script>';
}

if($payType=='A') {
	$GetExt = ctGeneral("","P"); 
	$GetExtOT = ctGeneral("","L"); 
	$pse = "Pendapatan";
}else{
	$GetExt = ctGeneral("","Q");
	$pse = "Perbelanjaan";
}
/*
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
<LINK rel="stylesheet" href="images/default.css" >
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';
*/
print '
<form name="MyForm" action="" method="post">
<input type="hidden" name="userID" value="'.$userID.'">
<input type="hidden" name="loanID" value="'.$loanID.'">
<input type="hidden" name="payType" value="'.$payType.'">
<input type="hidden" name="action">
                            
<div class="col-lg-8">
<table class="table mb-0">

<tr>
<td><h6 class="card-subtitle" colspan="2">Masukkan amaun mengikut jenis '.$pse.' anda. Mengikut Slip Gaji</h6></td>
</tr>';
if ($GetExt->RowCount() <> 0) {  
print '<tr>
<td class="Data">
                <table class="table mb-3">
                <thead>
                <tr class="table-primary">
                <th>&nbsp;<b>Jenis '.$pse.'</b></th>
                <th>&nbsp;<b>Amaun '.$pse.'</b></th>
                </tr></thead>';
                while (!$GetExt->EOF) {
                $id= $GetExt->fields(ID);
                $exist = 0;
                $value= 0.0;
                if ($userID) {
                $checkExt = "SELECT * FROM userstates 
                WHERE payID = ".$id." 
                AND userID = ".toSQL($userID,"Text");
                $rsCheckExt = $conn->Execute($checkExt);
                $value = $rsCheckExt->fields(amt);
                //--------------------
                $idDapat = $rsCheckExt->fields('payID');
                $stateID = $rsCheckExt->fields('ID');

                }
                $code= $GetExt->fields(code);
                $c_deduct = $GetExt->fields(c_Deduct);
                if ($c_deduct<>"") {
                $getKod = "SELECT code, name FROM general WHERE ID = ".$c_deduct;
                $rsKod = $conn->Execute($getKod);
                $kod_potongan = $rsKod->fields(code);
                } else {
                $kod_potongan = "";
                }
                $name= $GetExt->fields(name);

                print '
                <tr class="table-light">
                <td>&nbsp;'.strtoupper($name).'</td>
                <td>&nbsp; ';
                if ($id == 1569){
                print ' <input name="valueD['.$id.']" size="10" class="form-control" maxlength="15" type="text" value="'.$value.'"> &nbsp;';
                }else{
                        print '<input name="valueD['.$id.']" size="10" class="form-control" maxlength="15" type="text" value="'.$value.'"> &nbsp;
                '; } print '
                </td>
                </tr>';
                $GetExt->MoveNext();
                }
                print '</table>
</td>
</tr>';
} 

print '<tr>
<td colspan=3 align=center class=Data>
<input type=Submit name=SubmitForm class="btn btn-primary w-md waves-effect waves-light mb-3" value=Kemaskini>
<input type=button class="btn btn-secondary w-md waves-effect waves-light mb-3" value=Kembali onClick="window.location.href=\'?vw=biayaEdit&mn=3\';">
</td>
</tr>

';
print '</table>';

print '</div></form>'; 

print ' <div class="row">                
                <div class="col-xl-8">
                                    <div class="card bg-light text-dark">
                                        <div class="card-body">
                                            <h6 class="mb-4 text-dark"><i class="mdi mdi-alert-circle-outline me-3"></i>* Nota :- Di bawah ini tersenarai pendapatan yang tidak diambil kira.</h6>
                                            <p class="card-text"><b>
                                            <ol type="1">
                                                <li>Elaun Penjagaan Anak</li>
                                                <li>Perbantuan / Kenderaan Sendiri (Tugas Rasmi)</li>
                                                <li>Meal - Allowance - Pengurusan</li>
                                                <li>Tunggakan Gaji</li>
                                                <li>Memangku / Pemangku Gred</li>
                                                <li>Tunggakan Bonus</li>
                                                <li>Bonus Kontrak/Tahunan</li>
                                                <li>Insurance House Owner</li>
                                                <li>Cuti Tanpa Gaji</li>
                                                <li>Insentif Jualan - PPKewangan</li>
                                                <li>Hadiah Hari Jadi</li>
                                                <li>Insentif FE</li>
                                                <li>Elaun Perbantuan</li>
                                                <li>Merit Token</li>
                                          </ol> 
                                            </b></p>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                </div>';




?>
<!--</body></html>-->