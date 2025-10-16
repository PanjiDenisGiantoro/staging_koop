<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCInvDebtorPayment.php
*			Date 		: 19/10/2006
*********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCInvDebtorList&mn='.$mn.'">SENARAI</a><b>'.'&nbsp;>&nbsp;PENERIMAAN BAYARAN PENGHUTANG</b>';

if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display= 0;
if($PBNo && $action=="view"){
	$sql = "SELECT * FROM pb_payments a, generalacc b WHERE a.companyID = b.ID and a.PBNo = '".$PBNo."'";

	$rs 			= $conn->Execute($sql);
	$PBNo 			= $rs->fields(PBNo);
	$tarikh_PB 		= $rs->fields(tarikh_PB);
	$tarikh_PB 		= substr($tarikh_PB,8,2)."/".substr($tarikh_PB,5,2)."/".substr($tarikh_PB,0,4);
	$tarikh_PB 		= toDate("d/m/y",$rs->fields(tarikh_PB));
	$batchNo 		= $rs->fields(batchNo);
	
	$kod_bank 		= $rs->fields(kod_bank);
	$bankparent 	= dlookup("generalacc", "parentID", "ID=" .$kod_bank);	

	$kod_project 	= $rs->fields(kod_project);
	$kod_jabatan 	= $rs->fields(kod_jabatan);
	$companyID 	    = $rs->fields(companyID);
	$catatan 		= $rs->fields(catatan);
	$createdDate 	= $rs->fields(createdDate);
	$createdBy 		= $rs->fields(createdBy);
	$updatedDate 	= $rs->fields(updatedDate);
	$updatedBy 		= $rs->fields(updatedBy);
	$investNo		= $rs->fields(investNo);
	$amt			= $rs->fields(outstandingbalance);
	$cara_bayar		= $rs->fields(cara_bayar);	
	$disedia		= $rs->fields(disedia);
	$disemak		= $rs->fields(disemak);
	$b_Baddress 	= $rs->fields(b_Baddress);
	$code 			= $rs->fields(code);
	$nama			= $rs->fields(name);
	$kodGL 			= $rs->fields(b_kodGL);

	// kod carta akaun
	//-----------------
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = '".$PBNo."' AND addminus IN (1) ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	if($rsDetail->RowCount()<1) 
		$noTran = true;

}elseif($action=="new"){  
	$getNo = "SELECT MAX(CAST(right(PBNo,6) AS SIGNED INTEGER )) AS nombor FROM pb_payments";

	$rsNo = $conn->Execute($getNo);
	$tarikh_PB = date("d/m/Y");
	$tarikh_batch = date("d/m/Y");
	if($rsNo){
		$nombor = intval($rsNo->fields(nombor)) + 1; 
		$nombor = sprintf("%06s",  $nombor);
		$PBNo = 'PB'.$nombor;
	}else{
		$PBNo = 'PB000001';
	} 
}

if (!isset($tarikh_PB)) $tarikh_PB = date("d/m/Y");
if (!isset($tarikh_batch)) $tarikh_batch = date("d/m/Y");
if($cara_bayar){
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");
	$createdBy 	= get_session("Cookie_userName");
	$createdDate = date("Y-m-d H:i:s");
	
		$deductID = $kodGL; 		
		$addminus = 1;
		$cajAmt = 0.0;

		$project = dlookup("pb_invoice", "kod_project", "investNo=" . tosql($investNo, "Text"));
		
		$tahun = date("Y", strtotime($tarikh_PB));
		$bulan = date("m", strtotime($tarikh_PB));
		
		$yrmth = $tahun . $bulan;
		if($pymtAmt == '') 
			$pymtAmt = '0.0';
		$sSQL	= "INSERT INTO transactionacc (" . 					
				  "docNo," .  
				  "docID," . 
				  "batchNo," . 
				  "deductID," . 
				  "MdeductID," . 
				  "cara_bayar," .
				  "addminus," . 
				  "pymtID," .
				  "pymtAmt," .
				  "pymtRefer," .	
				  "pymtReferC," .		
				  "pymtReferPB," .			  
				  "desc_akaun," .
				  "yrmth," . 
				  "status," .
				  "isApproved," .			
				  "approvedDate," . 
				  "createdDate," . 
				  "createdBy," . 
				  "tarikh_batch) " . 

				  " VALUES (" . 
				"'". $PBNo . "', ".
				"'". 6 . "', ".
				"'". $batchNo . "', ".
				"'". $deductID . "', ".
				"'". $deductID . "', ".
				"'". $cara_bayar . "', ".				
				"'". $addminus . "', ".
				"'". 66 . "', ".
				"'". $kredit2 . "', ".
				"'". $companyID . "', ".
				"'". $project . "', ".
				"'". $investNo . "', ".
				"'". $desc_akaun2 . "', ".
				"'". $yrmth . "', ".
				"'". $status . "', ".
				"'". $isApproved . "', ".
				"'". $createdDate . "', ".
				"'". $createdDate . "', ".
				"'". $createdBy . "', ".
				"'". $tarikh_batch . "')";

		if($display) print $sSQL.'<br />';
		else{ $rs = &$conn->Execute($sSQL);
		print '<script>
		window.location = "?vw=ACCInvDebtorPayment&mn='.$mn.'&action=view&PBNo='.$PBNo.'";
		</script>';
		}
}

if($action=="Hapus"){
  if(count($pk)>0){
	$sWhere = "";
	foreach($pk as $val) {
		$sSQL = '';
		$sWhere = "ID='" . $val ."'";
		$sSQL = "DELETE FROM transactionacc WHERE " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
	}
  }
	if(!$display){
	print '<script>
	window.location = "?vw=ACCInvDebtorPayment&mn='.$mn.'&action=view&PBNo='.$PBNo.'";
	</script>';
	}
}

elseif($action == "Kemaskini" || $carabayar || $desc_akaun ) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";		
	  $sWhere = "PBNo='" . $PBNo ."'";
		$tarikh_PB = saveDateDb($tarikh_PB);
		$tarikh_batch =	saveDateDb($tarikh_batch);	

		$tahun = date("Y", strtotime($tarikh_PB));
		$bulan = date("m", strtotime($tarikh_PB));
		
		$yrmth = $tahun . $bulan;
		$sWhere = " WHERE (" . $sWhere . ")";	
		$sSQL	= "UPDATE pb_payments SET " .

					"PBNo='" .$PBNo . "',".
					"tarikh_PB='" .$tarikh_PB . "',".
					"batchNo='" .$batchNo . "',".
					"kod_bank='" .$kod_bank . "',".
					"kod_project='" .$kod_project . "',".
					"kod_jabatan='" .$kod_jabatan . "',".
					"companyID='" .$companyID . "',".
					"catatan='" .$catatan . "',".
					"createdDate='" .$createdDate . "',".
					"createdBy='" .$createdBy . "',".
					"updatedDate='" .$updatedDate . "',".
					"updatedBy='" .$updatedBy . "',".
					"investNo='" .$investNo . "',".
					"outstandingbalance='" .$amt . "',".
					"balance='" .$balance . "',".
					"disedia='" .$disedia . "',".
					"disemak='" .$disemak . "'";

		$sSQL = $sSQL . $sWhere;

		$sSQL1 = "";
		$sWhere1 = "";		
	 	$sWhere1 = "docNo='".$PBNo."' AND addminus='". 0 ."'";
		$sWhere1 = " WHERE (".$sWhere1.")";		
		$sSQL1	= "UPDATE transactionacc SET ".
					"deductID='" .$kod_bank."',".
					"MdeductID='" .$bankparent."',".
					"desc_akaun='" .$catatan."',".
					"batchNo='" .$batchNo."',".
					"pymtAmt='" .$masterAmt . "'";
				
		$sSQL1 = $sSQL1 . $sWhere1;

		$project = dlookup("pb_invoice", "kod_project", "investNo=" . tosql($investNo, "Text"));

		$sSQL2 = "";
		$sWhere2 = "";		
	 	$sWhere2 = "docNo='".$PBNo."'";
		$sWhere2 = " WHERE (".$sWhere2.")";		
		$sSQL2	= "UPDATE transactionacc SET ".
					"yrmth='" .$yrmth . "',".
					"pymtReferC='" .$project . "',".
					"pymtReferPB='" .$investNo . "',".
					"tarikh_doc='" .$tarikh_PB . "'";
					
		$sSQL2 = $sSQL2.$sWhere2;

		if($display) print $sSQL.'<br />';
		else 
			$rs = &$conn->Execute($sSQL);
			$rs = &$conn->Execute($sSQL1);
			$rs = &$conn->Execute($sSQL2);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(count($carabayar)>0){
		foreach($carabayar as $id =>$value){

		$cara_bayar = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 0;
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 1;
		}
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "ID='" . $id ."'";
	    $sSQL	= "UPDATE transactionacc SET " .

	     	"batchNo= '" . $batchNo . "'".
          	",cara_bayar= '" . $cara_bayar . "'".
          	",addminus= '" . $addminus . "'".
          	",pymtAmt= '" . $pymtAmt . "'".
			",updatedDate= '" .$updatedDate . "'".
          	",updatedBy= '" .  $updatedBy . "'" ;
		$sSQL .= " where " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
		}	
	}

	if(count($desc_akaun)>0){
		foreach($desc_akaun as $id =>$value){
		
		$desc_akaun = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 0;
		
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 1;
		}
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "ID='" . $id ."'";
	    $sSQL	= "UPDATE transactionacc SET " .
	     	"batchNo=" . tosql($batchNo, "Number").
          	",desc_akaun=" . tosql($desc_akaun, "Text").
          	",addminus=" . $addminus.
          	",pymtAmt=" . tosql($pymtAmt, "Number").
			",updatedDate=" . tosql($updatedDate, "Text") .
          	",updatedBy=" . tosql($updatedBy, "Text") ;

		$sSQL .= " where " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
		}	
	}
/////////////////////////////////////////////////////////////////////////////
	if(!$display){
	print '<script>
	window.location = "?vw=ACCInvDebtorPayment&mn='.$mn.'&action=view&PBNo='.$PBNo.'";
	</script>';
	}
}

//pilihan simpan
 elseif($action == "Simpan" || $simpan) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$tarikh_PB = saveDateDb($tarikh_PB);
		$tarikh_batch =	saveDateDb($tarikh_batch);

		$tahun = date("Y", strtotime($tarikh_PB));
		$bulan = date("m", strtotime($tarikh_PB));

		$project = dlookup("pb_invoice", "kod_project", "investNo=" . tosql($investNo, "Text"));
		
		$yrmth = $tahun . $bulan;

		$sSQL = "";
		$sSQL	= "INSERT INTO pb_payments (" . 
					
					"PBNo, " .
					"tarikh_PB, " .
					"batchNo, " .
					"kod_bank, " .
					"kod_project, " .
					"kod_jabatan, " .
					"companyID, " .
					"catatan, ".
					"createdDate, " .
					"createdBy, " .
					"updatedDate, " .
					"updatedBy, " .
					"investNo, ".
					"outstandingbalance, ".
					"disedia, ".
					"disemak) ".					
		            " VALUES (" . 
				
					"'". $PBNo . "', ".
					"'". $tarikh_PB . "', ".
					"'". $batchNo . "', ".
					"'". $kod_bank . "', ".
					"'". $kod_project . "', ".
					"'". $kod_jabatan . "', ".
					"'". $companyID . "', ".
					"'". $catatan . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "', ".
					"'". $investNo . "', ".
					"'". $amt . "', ".
					"'". $disedia . "', ".
					"'". $disemak . "')";

		$sSQL1 = "";
		$sSQL1	= "INSERT INTO transactionacc (" . 
					
				  "docNo," . 
				  "tarikh_doc," .  
				  "docID," .
				  "batchNo," . 
				  "deductID," . 
				  "yrmth," .	
				  "cara_bayar," .
				  "addminus," . 
				  "pymtID," .
				  "pymtAmt," .		
				  "pymtReferC," .	
				  "pymtReferPB," .			  
				  "desc_akaun," .
				  "status," .
				  "isApproved," .			
				  "approvedDate," . 
				  "createdDate," . 
				  "createdBy," . 
				  "tarikh_batch) " . 

				  " VALUES (" . 
				"'". $PBNo . "', ". 
				"'". $tarikh_PB . "', ".
				"'". 6 . "', ".
				"'". $batchNo . "', ".
				"'". $kod_bank . "', ".
				"'". $yrmth . "', ".
				"'". $cara_bayar . "', ".				
				"'". 0 . "', ".
				"'". 66 . "', ".
				"'". $masterAmt . "', ".
				"'". $project . "', ".
				"'". $investNo . "', ".
				"'". $desc_akaun2 . "', ".
				"'". $status . "', ".
				"'". $isApproved . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $tarikh_batch . "')";

		if($display) print $sSQL.'<br />';
		else 
			$rs = &$conn->Execute($sSQL);
			$rs = &$conn->Execute($sSQL1);


	$getMax = "SELECT MAX(CAST(right(PBNo,6) AS SIGNED INTEGER)) AS no FROM pb_payments";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%06s", $rsMax->fields(no));
	if(!$display){
	print '<script>
	window.location = "?vw=ACCInvDebtorPayment&mn='.$mn.'&action=view&add=1&PBNo=PB'.$max.'";
	</script>';
	}
}
 
$strTemp .=
'<div class="maroon" align="left">'.$strHeaderTitle.'</div>'
.'<div style="width: 100%; text-align:left">'
.'<div>&nbsp;</div>'
.'<div class="table-responsive"><form name="MyForm" action="?vw=ACCInvDebtorPayment&mn='.$mn.'" method="post">'
.'<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print 
'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>No. PB</td>
				<td valign="top"></td>
				<td>
					<input  name="PBNo" value="'.$PBNo.'" type="text" size="20" maxlength="50" class="form-control-sm" readonly/>
				</td>
			</tr>

			<tr>
				<td>Batch</td>
				<td valign="top"></td>
				<td>'.selectbatch($batchNo,'batchNo').'</td>
			</tr>
		
			<tr>
				<td>Tanggal</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="tarikh_PB" value="'.$tarikh_PB.'" type="text" size="20" maxlength="10" /></td>
			</tr>
			
			<tr>
				<td>Bank</td>
				<td valign="top"></td>
				<td>'.selectbanks($kod_bank,'kod_bank').'</td>
			</tr>

			<tr>
				<td>Projek</td>
				<td valign="top"></td>
				<td>'.selectproject($kod_project,'kod_project').'</td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td valign="top"></td>
				<td>'.selectjabatan($kod_jabatan,'kod_jabatan').'</td>
			</tr>
		</table>
	</td>
</tr>	

<tr><td colspan="3"><hr class="mt-3" /></td></tr>';

print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">

<table border="0" cellspacing="1" cellpadding="2">

<tr>
<td>* Kod Penghutang</td><td valign="top"></td>
<td><input name="code" value="'.$code.'" type="text" size="20" maxlength="50"  class="form-control-sm" readonly/>&nbsp;';

print '<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'ACCmemberpelaburanL.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
print '&nbsp;

</td>
</tr>

<tr>
 <td valign="top">Nama Serikat</td>
 <td valign="top"></td>
 <td><input name="nama"  value="'.$nama.'" size="40" maxlength="50"  class="form-control-sm" readonly /></td>
 </tr>
<tr>
<td valign="top">Alamat Syarikat</td>
<td valign="top"></td>
<td><textarea name="b_Baddress" cols="50" rows="4" class="form-control-sm" readonly>'.$b_Baddress.'</textarea></td>
</tr>

   <tr>
<td valign="top">Amaun Invois (RP)</td>
<td valign="top"></td>
<td><input name="amt"  value="'.$amt.'" size="10" maxlength="50"  class="form-control-sm" readonly/></td>
</tr>
<tr>
<td valign="top">No. Invois</td>
<td valign="top"></td>
<td><input name="investNo" value="'.$investNo.'" size="40" maxlength="50"  class="form-control-sm" readonly /></td>
</tr>
<tr>
</td><td><input type=hidden name="companyID" value="'.$companyID.'" type="text" size="4" maxlength="50" class="form-control-sm" />
</td>
</tr>
<tr>
</td><td><input type=hidden name="kodGL" value="'.$kodGL.'" type="text" size="4" maxlength="50" class="form-control-sm" />
</td>
</tr>

</table>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>';
	//----------
	if ($action=="view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $PBNo ."'"))){

print '
	<tr>
		<td align= "right" colspan="3">';
	    if(!$add) print '
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCInvDebtorPayment&mn='.$mn.'&action='.$action.'&PBNo='.$PBNo.'&add=1\';">'; 
	    else print '
			<input type="button" name="action" value="Simpan" class="btn btn-sm btn-primary" onclick="CheckField(\'Kemaskini\')">';
		print '&nbsp;<input type="submit" name="action" value="Hapus" class="btn btn-sm btn-danger">
		</td>
	</tr>';
}
//----------
print 
'<tr>
	<td colspan="3">
		<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>Cara Bayaran</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>Jumlah (Rp)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action=="view"){

	$i = 0;
	while (!$rsDetail->EOF) {

	$id = $rsDetail->fields(ID);
	$ruj = $rsDetail->fields(pymtRefer);
	$carabayar = $rsDetail->fields(cara_bayar);
	$c_bayar = dlookup("generalacc", "name", "ID=" . $carabayar);
	$kredit = $rsDetail->fields(pymtAmt);
	$desc_akaun =	$rsDetail->fields(desc_akaun);


		if($rsDetail->fields(addminus)){
			$kredit = $rsDetail->fields(pymtAmt);
		}else{
			$debit = $rsDetail->fields(pymtAmt);
		}
	print	   
			'<tr>
				<td class="Data">'.++$i.'.</td>	

				

				<td class="Data" nowrap="nowrap">'.strSelectCB($id,$carabayar).'</td>

				<td class="Data" nowrap="nowrap">
					<input name="desc_akaun['.$id.']" type="text" class="form-control-sm" size="60" maxlength="100" value="'.$desc_akaun.'"/>
				</td>

				<td class="Data" align="right">
					<input name="kredit['.$id.']" type="text" size="10" class="form-control-sm" maxlength="10" value="'.$kredit.'" style="text-align:right;"/>
				</td>

				<td class="Data" align="left"><input type="checkbox" class="form-check-input" name="pk[]" value="'.$id.'"></td>

			</tr>';
			$totalKt += $kredit;
			$baki=$amt-$totalKt;
		  $kredit = '';
	$rsDetail->MoveNext();
	}
}

if($add){
print	   '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>	


				<td class="Data">'.selectcarabayar($cara_bayar,'cara_bayar').'</td>
				<td class="Data" align="left">
					<input name="desc_akaun2" type="text" size="60" maxlength="100" class="form-control-sm" value="'.$desc_akaun2.'" align="right"/>
				</td>
				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input name="kredit2" type="text" size="10" class="form-control-sm" maxlength="10" value="'.$kredit2.'" />
				</td>

				<td class="Data" align="left"></td>
			</tr>';
}

//bahagian bawah skali
if($totalKt<>0){
	$clsRM->setValue($baki);
	$clsRM->setValue($totalKt);
	$strTotal = ucwords($clsRM->getValue()).' Sahaja.';
}

$idname = get_session('Cookie_fullName');

print 		'<tr class="table-secondary">
				<td class="Data" align=""><b>&nbsp;</b></td>
				<td class="Data" colspan="2" align="right"><b>Jumlah (RP)</b></td>
				<td class="Data" align="right"><b>'.number_format($totalKt,2).'&nbsp;</b></td>
				<td class="Data" align="left"></td>
			</tr>

			<tr class="table-secondary">

				<td class="Data" align=""><b>&nbsp;</b></td>
				<td class="Data" colspan="2" align="right"><b>Saldo (RP)</b></td>
				<td class="Data" align="right"><b>'.number_format($baki,2).'&nbsp;</b></td>
				<td class="Data" align="left"></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr colspan="3">
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="3">

		<tr>
			<td nowrap="nowrap"></td>
			<td>
				<input class="Data" type="hidden" name="masterAmt" value="'.$totalKt.'">
				<input class="Data" type="hidden" name="balance" value="'.$baki.'">				
				<input class="Data" type="hidden" name="bankparent" value="'.$bankparent.'">
			</td>
		</tr>


		<tr>
				<td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td>
				<td><input class="form-controlx" name="disedia" value="'.$idname.'" type="text" size="20" maxlength="15"/></td>
			</tr>

			<tr>
				<td nowrap="nowrap">Disemak Oleh</td><td valign="top"></td>
				<td>'.selectAdmin($disemak,'disemak').'</td>
			</tr>
			
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td>
				<td valign="top">
					<textarea  class="form-controlx" name="catatan" cols="50" rows="4">'.$catatan.'</textarea></td>
			</tr>
		
		</table>
	</td>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112"><input name="tarikh" type="hidden" value="01/10/2006"></tr>';


if($PBNo) { 
$straction = ($action=='view'?'Kemaskini':'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCInvDebtorPaymentPrint.php?id='. $PBNo .'\')">&nbsp;
	<input type="button" name="action" value="'.$straction.'" class="btn btn-primary" onclick="CheckField(\''. $straction. '\')">';
if($straction=='Simpan') print '
	<input type="hidden" name="simpan" value="1">';
print '
	</td>
</tr>';

}

$strTemp = '
	</table>
</form></div>
</div>';

print $strTemp;
print '
<script language="JavaScript">
	function print_(url) {
		window.open(url,"pop","top=100, left=100, width=600, height=400, scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=yes");					
	}

	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  //if(!e.debit2.value == \'\') alert(e.nama_anggota.value);
		  if(e.elements[c].name=="no_anggota" && e.elements[c].value==\'\') {
			alert(\'Sila pilih anggota!\');
            count++;
		  }
		  
		  if(act == \'Kemaskini\') {
  
		  if(e.elements[c].name=="kredit2" && e.elements[c].value==\'\') {
			alert(\'Ruang amaun perlu diisi!\');
            count++;
		  }
		  }

		  if(act == \'Simpan\') {
  
		  if(e.elements[c].name=="batchNo" && e.elements[c].value==\'\') 
		  	{
			alert(\'Ruang batch perlu diisi!\');
            count++;
		 	}
		  }
		}
		if(count==0) {
			e.submit();
		}
	}
</script>';
include("footer.php");
?>