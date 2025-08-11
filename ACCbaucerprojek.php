<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCbaucerprojects.php
*			Date 		: 05/02/2024
*********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCvouchersProjectsList&mn='.$mn.'">SENARAI</a><b>'.'&nbsp;>&nbsp;PEMBAYARAN BAUCER</b>';

if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display= 0;
if($no_baucer && $action=="view"){
	$sql = "SELECT * FROM baucerprojekacc WHERE no_baucer = '".$no_baucer."'";

	$rs 						= $conn->Execute($sql);
	$no_baucer 			= $rs->fields(no_baucer);
	$tarikh_baucer 	= $rs->fields(tarikh_baucer);
	$tarikh_baucer 	= substr($tarikh_baucer,8,2)."/".substr($tarikh_baucer,5,2)."/".substr($tarikh_baucer,0,4);

	$kod_bank 			= $rs->fields(kod_bank);
	$bankparent 		= dlookup("generalacc", "parentID", "ID=" . $kod_bank);

	$keterangan 		= $rs->fields(keterangan);
	$tarikh_bayar 	= $rs->fields(tarikh_bayar);
	$tarikh_bayar 	= substr($tarikh_bayar,8,2)."/".substr($tarikh_bayar,5,2)."/".substr($tarikh_bayar,0,4);
	$nama 					= $rs->fields(name);
	$batch 					= $rs->fields(batchNo);
	$deductID 			= $rs->fields(deductID);
	// $keterangan			= $rs->fields(keterangan);
	$bayaran_kpd		= $rs->fields(bayaran_kpd);
	$Cheque					= $rs->fields(Cheque);
	$cara_bayar			= $rs->fields(cara_bayar);
	$catatan				= $rs->fields(catatan);
	$disedia				= $rs->fields(disedia);
	$disahkan				= $rs->fields(disahkan);
	$masterAmt			= $rs->fields(pymtAmt);
	$projectID			= $rs->fields(kod_project);
	$nameproject		= dlookup("investors", "nameproject", "ID=" .$rs->fields(kod_project));
	$kod_jabatan		= $rs->fields(kod_jabatan);
	$nilaiPelaburan		= $rs->fields(nilai_pelaburan);

	// kod carta akaun
	//-----------------
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = '".$no_baucer."' AND addminus IN (0) ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	if($rsDetail->RowCount()<1) 
		$noTran = true;

}elseif($action=="new"){  
	$getNo = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS nombor FROM baucerprojekacc";
	$rsNo = $conn->Execute($getNo);
	if($rsNo){
		$nombor = intval($rsNo->fields(nombor)) + 1; 
		$nombor = sprintf("%06s",  $nombor);
		$no_baucer = 'PVL'.$nombor;
	}else{
		$no_baucer = 'PVL000001';
	} 
}

if (!isset($tarikh_baucer)) $tarikh_baucer = date("d/m/Y");
if (!isset($tarikh_bayar)) $tarikh_bayar = date("d/m/Y");

if($perkara2){
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");  

		$tarikh_baucer = saveDateDb($tarikh_baucer);

		
		$deductID = $perkara2; //perkara to deduct id value
		if($debit2){ //debit 2 field for money value
		$pymtAmt = $debit2;
		$addminus = 0;
		$cajAmt = 0.0;
		}

		else{
		$pymtAmt = $kredit2;
		$addminus = 1;
		$cajAmt = 0.0;
		}

		if($pymtAmt == '') 
			$pymtAmt = '0.0';
		$sSQL	= "INSERT INTO transactionacc (" . 
				"docNo," . 
				"docID," . 
				"yrmth," . 
				"batchNo," . 
				"deductID," .	
				"taxNo," .
				"pymtReferC," .	
				"kod_jabatan," . 		
				"addminus," . 
				"pymtID," .			
				"pymtAmt," . 
				"desc_akaun," .  
				"updatedBy," . 
				"updatedDate	," . 
				"createdBy," . 
				"createdDate) " . 
				" VALUES (" .
				"'". $no_baucer . "', ".
				"'". 3 . "', ".
				"'". $yymm . "', ".
				"'". $batchNo . "', ".
				"'". $deductID . "', ".
				"'". $taxNo . "', ".
				"'". $projectID . "', ".
				"'". $kod_jabatan . "', ".
				"'". $addminus . "', ".
				"'". 66 . "', ".
				"'". $pymtAmt . "', ".
				"'". $desc_akaun2 . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $tarikh_baucer . "')";

		if($display) print $sSQL.'<br />';
		else{ 

			$rs = &$conn->Execute($sSQL);
		print '<script>
		window.location = "?vw=ACCbaucerprojek&mn='.$mn.'&action=view&no_baucer='.$no_baucer.'";
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
	window.location = "?vw=ACCbaucerprojek&mn='.$mn.'&action=view&no_baucer='.$no_baucer.'";
	</script>';
	}
}

elseif($action == "Kemaskini" || $perkara || $desc_akaun|| $projecting || $jabatan1) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
		$sWhere = "";		
	 	$sWhere = "no_baucer='" . $no_baucer ."'";
		$tarikh_baucer = saveDateDb($tarikh_baucer);
		$tarikh_bayar =	saveDateDb($tarikh_bayar);
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE baucerprojekacc SET " .
					"tarikh_baucer='" .$tarikh_baucer . "',".
					"batchNo='" .$batchNo . "',".
					"kod_bank='" .$kod_bank . "',".
					"tarikh_bayar='" .$tarikh_bayar . "',".
					"keterangan='" .$keterangan . "',".
					"bayaran_kpd='" .$bayaran_kpd . "',".
					"Cheque='" .$Cheque . "',".
					"cara_bayar='" .$cara_bayar . "',".
					"disedia='" .$disedia . "',".
					"disahkan='" .$disahkan . "',".
					"catatan='" .$catatan . "',".
					"pymtAmt='" .$masterAmt . "',".
					"kod_project='" .$projectID . "',".
					"kod_jabatan='" .$kod_jabatan . "',".
					"nilai_pelaburan='" .$nilaiPelaburan . "',".
					"StatusID_Pymt='1',".
					"updatedDate='" .$updatedDate . "',".
					"updatedBy='" .$updatedBy . "'";
					
		$sSQL = $sSQL . $sWhere;

		$sSQL1 = "";
		$sWhere1 = "";		
	 	$sWhere1 = "docNo='" . $no_baucer ."' AND addminus='" . 1 ."'";
		$sWhere1 = " WHERE (" . $sWhere1 . ")";		
		$sSQL1	= "UPDATE transactionacc SET ".
					"deductID='" .$kod_bank . "',".
					"MdeductID='" .$bankparent . "',".
					"yrmth='" .$yymm . "',".
					"batchNo='" .$batchNo . "',".
					"pymtReferC='" .$projectID . "',".
					"kod_jabatan='" .$kod_jabatan . "',".
					"pymtAmt='" .$masterAmt . "'";
					
		$sSQL1 = $sSQL1 . $sWhere1;

		$sSQL2 = "";
		$sWhere2 = "";		
	 	$sWhere2 = "docNo='" . $no_baucer ."'";
		$sWhere2 = " WHERE (" . $sWhere2 . ")";		
		$sSQL2	= "UPDATE transactionacc SET ".
					"tarikh_doc='" .$tarikh_baucer . "'";
					
		$sSQL2 = $sSQL2 . $sWhere2;

		if($display) print $sSQL.'<br />';
		else 
			$rs = &$conn->Execute($sSQL);
			$rs = &$conn->Execute($sSQL1);
			$rs = &$conn->Execute($sSQL2);
///////////////////|||PERKARA|||////////////////////////////////////////////////////////////////
	if(count($perkara)>0){
		foreach($perkara as $id =>$value){

		$deductID = $value;
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
	    
	      "batchNo= '" . $batchNo . "',".
	      "yrmth= '" . $yymm . "',".
          "deductID= '" . $deductID . "',".
          "addminus= '" . $addminus . "',".
          "pymtReferC= '" . $projectID . "',".
          "kod_jabatan= '" . $kod_jabatan . "',".
          "pymtAmt= '" . $pymtAmt . "',".
		  "updatedDate= '" .$updatedDate . "',".
          "updatedBy= '" .  $updatedBy . "'" ;

	$sSQL .= " where " . $sWhere;
	if($display) print $sSQL.'<br />';
	else $rs = &$conn->Execute($sSQL);
		}	
	}
//////////////////////////PROJEK//////////////////////////////////////////////////////////////
		if(count($kod_akaun)>0){
		foreach($kod_akaun as $id =>$value){

		//$deductID = $value;
		$MdeductID = $value;
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
	    
	      "batchNo= '" . $batchNo . "',".
	      "yrmth= '" . $yymm . "',".
        "MdeductID= '" . $MdeductID . "',".
		  	"updatedDate= '" .$updatedDate . "',".
        "updatedBy= '" .  $updatedBy . "'" ;


		$sSQL .= " where " . $sWhere;
		if($display) print $sSQL.'<br />';
		else $rs = &$conn->Execute($sSQL);
		}	
	}
	///////////////////////DESC AKAUN//////////////////////////////////////
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
        ",yrmth=" . tosql($yymm, "Text"). 
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
/////////////////////////////////////////////////////////////////////////////	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(!$display){
	print '<script>
	window.location = "?vw=ACCbaucerprojek&mn='.$mn.'&action=view&no_baucer='.$no_baucer.'";
	</script>';
	}
}

/*
          "kod_project= '" . $projectID . "',".
          "kod_jabatan= '" . $kod_jabatan . "',".*/

//pilihan simpan
 elseif($action == "Simpan" || $simpan) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$tarikh_baucer = saveDateDb($tarikh_baucer);
		$tarikh_bayar =	saveDateDb($tarikh_bayar);
		$sSQL = "";
		$sSQL	= "INSERT INTO baucerprojekacc (" . 
					"no_baucer, " .
					"tarikh_baucer, " .
					"batchNo, " .
					"kod_bank, " .
					"tarikh_bayar, " .
					"keterangan, ".
					"bayaran_kpd, ".
					"Cheque, ".
					"cara_bayar, ".
					"disedia, ".
					"disahkan, ".
					"pymtAmt, ".
					"kod_project, ".
					"kod_jabatan, ".
					"nilai_pelaburan, ".
					"StatusID_Pymt, ".
					"createdDate, " .
					"createdBy, " .
					"updatedDate, " .
					"updatedBy, " .
					"catatan) " .
					
		    " VALUES (" . 

					"'". $no_baucer . "', ".
					"'". $tarikh_baucer . "', ".
					"'". $batchNo . "', ".
					"'". $kod_bank . "', ".
					"'". $tarikh_bayar . "', ".
					"'". $keterangan . "', ".
					"'". $bayaran_kpd . "', ".
					"'". $Cheque . "', ".
					"'". $cara_bayar . "', ".
					"'". $disedia . "', ".
					"'". $disahkan . "', ".
					"'". $masterAmt . "', ".
					"'". $projectID . "', ".
					"'". $kod_jabatan . "', ".
					"'". $nilaiPelaburan . "', ".
					"'". 1 . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy. "', ".
					"'". $catatan . "')";

		$sSQL1 = "";
		$sSQL1	= "INSERT INTO transactionacc (" . 
				"docNo," . 
				"tarikh_doc," . 
				"docID," . 
				"yrmth," . 
				"batchNo," . 
				"deductID," .			
				"taxNo," .
				"pymtReferC," .	
				"kod_jabatan," . 		
				"addminus," . 
				"pymtID," .			
				"pymtAmt," . 
				"desc_akaun," . 
				"updatedBy," . 
				"updatedDate," . 
				"createdBy," . 
				"createdDate) " . 
				" VALUES (" .
				"'". $no_baucer . "', ".
				"'". $tarikh_baucer . "', ".
				"'". 3 . "', ".
				"'". $yymm . "', ".
				"'". $batchNo . "', ".
				"'". $kod_bank . "', ".
				"'". $taxNo . "', ".
				"'". $projectID . "', ".
				"'". $kod_jabatan . "', ".
				"'". 1 . "', ".
				"'". 66 . "', ".
				"'". $masterAmt . "', ".
				"'". $keterangan . "', ".	
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $tarikh_baucer . "')";

		if($display) print $sSQL.'<br />';
		else 

			$rs = &$conn->Execute($sSQL);		
			$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(no_baucer,6) AS SIGNED INTEGER)) AS no FROM baucerprojekacc";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%06s", $rsMax->fields(no));
	if(!$display){
	print '<script>
	window.location = "?vw=ACCbaucerprojek&mn='.$mn.'&action=view&add=1&no_baucer=PVL'.$max.'";
	</script>';
	}
}

$strTemp .=
'<div class="maroon" align="left">'.$strHeaderTitle.'</div>'
.'<div style="width: 100%; text-align:left">'
.'<div>&nbsp;</div>'
.'<div class="table-responsive"><form name="MyForm" action="?vw=ACCbaucerprojek&mn='.$mn.'" method="post">'
.'<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';

print $strTemp;
print 
'<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>No. Rujukan</td>
				<td valign="top"></td>
				<td>
					<input  name="no_baucer" value="'.$no_baucer.'" type="text" size="20" maxlength="50" class="form-control-sm" readonly/>
				</td>
			</tr>

			<tr>
				<td>Batch</td>
				<td valign="top"></td>
				<td>'.selectbatchBAUCER($batch,'batchNo').'</td>
			</tr>

			<tr>
			<td>Bank</td>
			<td valign="top"></td>
			<td>'.selectbanks($kod_bank,'kod_bank').'</td>
			</tr>
			
		</table>
	</td>
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tarikh Baucer</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="tarikh_baucer" value="'.$tarikh_baucer.'" type="text" size="20" maxlength="10" /></td>
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
			</td><td><input type=hidden name="projectID" value="'.$projectID.'" type="text" size="4" maxlength="50" class="data" />
			</td>
		</tr>
        <tr>
            </td><td><input type=hidden name="nama_syarikat" value="'.$companyName.'" type="text" size="4" maxlength="50" class="data" />
            </td>
        </tr>
        <tr>
            </td><td><input type=hidden name="kod_syarikat" value="'.$companyCode.'" type="text" size="4" maxlength="50" class="data" />
            </td>
        </tr>
			<tr>
				<td valign="top">Bayar Kepada </td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="bayaran_kpd" value="'.$bayaran_kpd.'" type="text" size="30" maxlength="100" readonly/>
                <input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'ACCprojects.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
                print '&nbsp;
                </td>
			</tr>

			<tr>
				<td valign="top">Keterangan</td>
				<td valign="top"></td>
				<td>
					<textarea name="keterangan" cols="50" rows="4" class="form-control-sm">'.$keterangan.'</textarea>
				</td>
			</tr>

			<tr>
				<td valign="top">Projek</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="nama_projek" value="'.$nameproject.'" type="text" size="30" maxlength="100" readonly/>
			</tr>

			<tr>
				<td valign="top">Jabatan</td>
				<td valign="top"></td>
				<td>'.selectjabatan($kod_jabatan,'kod_jabatan').'</td>
			</tr>
			
			<tr>
			<td valign="top">Nilai Pelaburan (RM)</td>
			<td valign="top"></td>
			<td><input class="form-control-sm" name="nilaiPelaburan" value="'.$nilaiPelaburan.'" type="text" size="30" maxlength="100" readonly/>
			</tr>
		  
		</table>
	</td>


	<td valign="top">&nbsp;</td>
	<td width="48%" align="right" valign="top">
		<table border="0" cellspacing="1" cellpadding="2">

			<tr>
				<td valign="top" align="right">Cara Bayaran</td><td valign="top"></td>
				<td>'.selectbayar($cara_bayar,'cara_bayar').'</td>
			</tr>

			<tr>
				<td valign="top" align="right">Cheque No.</td><td valign="top"></td>
				<td><input class="form-control-sm" name="Cheque" value="'.$Cheque.'" type="text" size="20" maxlength="10" /></td>
			</tr>

			<tr>
				<td valign="top" align="right">Tarikh Bayaran</td><td valign="top"></td>
				<td><input  class="form-control-sm" name="tarikh_bayar" value="'.$tarikh_bayar.'" type="text" size="20" maxlength="10"/></td>
			</tr>

			<tr>
				<td valign="top" align="right">Master Amaun (RM)</td><td valign="top"></td>
				<td><input class="form-control-sm" value="'.$masterAmt.'" type="text" size="20" maxlength="10" readonly/></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>';
	//----------
	if ($action=="view"  && !is_int(dlookup("transactionacc", "ID", "docNo='" .$no_baucer."'"))){
	print '
	<tr>
			<td align= "right" colspan="3">';
	    if(!$add) print '
		<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCbaucerprojek&mn='.$mn.'&action='.$action.'&no_baucer='.$no_baucer.'&add=1\';">'; 
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
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="table table-sm table-striped">
			<tr class="table-primary">
				<td nowrap="nowrap"><b>Bil</b></td>
				<td nowrap="nowrap"><b>Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>
				<td nowrap="nowrap" align="right"><b>* Jumlah (RM)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if (($action=="view") ){
	$i = 0;
	while (!$rsDetail->EOF) {

	$id = $rsDetail->fields(ID);
	$perkara = $rsDetail->fields(deductID);
	$kod_akaun = dlookup("generalacc", "parentID", "ID=" . $perkara);
	$namaparent= dlookup("generalacc", "name", "ID=" . $kod_akaun);
	$desc_akaun =	$rsDetail->fields(desc_akaun);	

	if($rsDetail->fields(addminus)){
	$kredit = $rsDetail->fields(pymtAmt);
	}else{
	$debit = $rsDetail->fields(pymtAmt);
	}

	print	   '
			<tr>
				<td class="Data">&nbsp;'.++$i.'.</td>	
				<td class="Data" nowrap="nowrap">'.strSelect3($id,$perkara).'&nbsp;
				<input class="Data" name="kod_akaun['.$id.']" type="hidden" size="10" maxlength="10" value="'.$kod_akaun.'"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<input name="desc_akaun['.$id.']" type="text" class="form-control-sm" size="35" maxlength="50" value="'.$desc_akaun.'"/>&nbsp;
				</td>
				
				<td class="Data" align="right">
					<input name="debit['.$id.']" type="text" size="10" class="form-control-sm" maxlength="10" value="'.$debit.'" style="text-align:right;"/>&nbsp;
				</td>

				<td class="Data" nowrap="nowrap"><input type="checkbox" name="pk[]" value="'.$id.'">&nbsp;</td>

			</tr>';
	$totalDb += $debit;		
	$baki=$nilaiPelaburan-$totalDb;  
	$debit = '';
		  
	$rsDetail->MoveNext();
	}
}

$strDeductIDList = deductListb2(1);
$strDeductCodeList = deductListb2(2);
$strDeductNameList = deductListb2(3);
$name = 'perkara2';

$strSelect = '<select name="'.$name.'" class="form-select-sm">
			 <option value="">- Pilih -';

			for ($i = 0; $i < count($strDeductIDList); $i++) {
				$strSelect .= '	<option value="'.$strDeductIDList[$i].'" ';
				if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
				$strSelect .=  '>'.$strDeductCodeList[$i] .'&nbsp;&nbsp;'.$strDeductNameList[$i].'';
			}
$strSelect .= '</select>';

if($add){
print	   '
			<tr>
				<td class="Data" nowrap="nowrap">&nbsp;</td>

				<td class="Data">'.$strSelect.' 
				<input name="kod_akaun2" type="hidden" size="10" maxlength="10" value="'.$kod_akaun2.'" class="Data"/>

				</td>

				<td class="Data" align="left">
					<input name="desc_akaun2" type="text" size="35" maxlength="50" value="'.$desc_akaun2.'" class="form-control-sm" align="right"/>&nbsp;
				</td>

				<td class="Data" align="right">
					<input type="hidden" name="ruj2" val="0">
					<input  name="debit2" type="text" class="form-control-sm" size="10" maxlength="10" value="'.$loanAmt.'" />&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>

			</tr>';
}

//bahagian bawah skali
if($totalDb<>0){
	$clsRM->setValue($baki);
	$clsRM->setValue($totalDb);
	$strTotal = ucwords($clsRM->getValue()).' Ringgit Sahaja.';
}

$idname = get_session('Cookie_fullName');

print 		
			'<tr class="table-secondary">
				<td class="Data" colspan="3" align="right"><b>Jumlah (RM)</b></td>
				<td class="Data" align="right"><b>'.number_format($totalDb,2).'&nbsp;</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>

			<tr class="table-secondary">
				<td class="Data" colspan="3" align="right"><b>Baki (RM)</b></td>
				<td class="Data" align="right"><b>'.number_format($baki,2).'&nbsp;	
				</b></td>
				<td class="Data" align=""><b>&nbsp;</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr colspan="3">
	<td valign="top">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr>
				<td nowrap="nowrap">Jumlah Dalam Perkataan</td>
				<td valign="top"></td>
				<td>
					<input class="form-control-sm" name="" size="80" maxlength="80" value="'.$strTotal.'" readonly>
					<input class="Data" type="hidden" name="masterAmt" value="'.$totalDb.'">
					<input class="form-controlx" type="hidden" name="balance" value="'.$baki.'">	
					<input class="Data" type="hidden" name="bankparent" value="'.$bankparent.'">
				</td>
			</tr>

			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>'.selectAdmin($disedia,'disedia').'</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>'.selectAdmin($disahkan,'disahkan').'</td></tr>

			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td>
				<td valign="top"></td>
				<td valign="top">
					<textarea  class="form-control-sm" name="catatan" cols="50" rows="4">'.$catatan.'</textarea>
				</td>
			</tr>
		</table>
	</td>';

$straction = ($action=='view'?'Kemaskini':'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetakan Koperasi" class="btn btn-secondary" onClick= "print_(\'ACCbaucerprojekPrint.php?id='. $no_baucer .'\')">&nbsp;
	<input type="button" name="print" value="Cetakan Pelanggan" class="btn btn-secondary" onClick= "print_(\'ACCbaucerprojekPrintPelanggan.php?id='. $no_baucer .'\')">&nbsp;
	<input type="button" name="action" value="'.$straction.'" class="btn btn-primary" onclick="CheckField(\''. $straction. '\')">';
if($straction=='Simpan') print '
	<input type="hidden" name="simpan" value="1">';
print '
	</td>
</tr>';


$strTemp = '
	</table>
</form>
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
  
		  if(e.elements[c].name=="debit2" && e.elements[c].value==\'\') {
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