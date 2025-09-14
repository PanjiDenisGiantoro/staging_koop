<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCinvestdebtor.php
*			Date 		: 19/10/2006
*********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}
$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCinvestList&mn='.$mn.'">SENARAI</a><b>'.'&nbsp;>&nbsp;INVOIS/PELABUR</b>';

if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$display= 0;
if($investNo && $action=="view"){
	$sql = "SELECT a.*, b.*
			FROM   pb_invoice a, generalacc b
			WHERE   a.companyID = b.ID and investNo = '".$investNo."'";

	$rs 				= $conn->Execute($sql);
	$investNo 			= $rs->fields(investNo);
	
	$tarikh_invest 		= $rs->fields(tarikh_invest);
	$tarikh_invest 		= substr($tarikh_invest,8,2)."/".substr($tarikh_invest,5,2)."/".substr($tarikh_invest,0,4);
	
	$projectID			= $rs->fields(kod_project);
	$kod_jabatan 		= $rs->fields(kod_jabatan);

	$disahkan 			= $rs->fields(disahkan);
	$disedia 			= $rs->fields(disedia);
	$disemak 			= $rs->fields(disemak);
	$tarikh_disedia 	= $rs->fields(tarikh_disedia);
	$tarikh_disedia 	= substr($tarikh_disedia,8,2)."/".substr($tarikh_disedia,5,2)."/".substr($tarikh_disedia,0,4);
	$tarikh_disemak		= $rs->fields(tarikh_disemak);
	$tarikh_disemak 	= substr($tarikh_disemak,8,2)."/".substr($tarikh_disemak,5,2)."/".substr($tarikh_disemak,0,4);
	$tarikh_disahkan	= $rs->fields(tarikh_disahkan);
	$tarikh_disahkan 	= substr($tarikh_disahkan,8,2)."/".substr($tarikh_disahkan,5,2)."/".substr($tarikh_disahkan,0,4);
	
	$description 		= $rs->fields(description);
	$nama 				= $rs->fields(name);
	//$maklumat        	= $rs->fields(maklumat);
	$batchNo 			= $rs->fields(batchNo);
	$companyID        	= $rs->fields(companyID);
	$companyName		= dlookup("generalacc", "name", "ID=" .$rs->fields(companyID));
	$b_Baddress 		= $rs->fields(b_Baddress);
	$companyCode 		= $rs->fields(code);
	$b_kodGL 			= $rs->fields(b_kodGL);
	$nameproject		= dlookup("investors", "nameproject", "ID=" .$rs->fields(kod_project));
	$nilaiPelaburan		= $rs->fields(nilai_pelaburan);

	//-----------------
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = '". $investNo ."' AND addminus IN (1) ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
	if($rsDetail->RowCount()<1) 
		$noTran = true;

}elseif($action=="new"){  
	$getNo = "SELECT MAX(CAST(right(investNo,6)
	 		  AS SIGNED INTEGER )) AS nombor 
	          FROM pb_invoice";

	$rsNo = $conn->Execute($getNo);
	if($rsNo){
		$nombor = intval($rsNo->fields(nombor)) + 1; 
		$nombor = sprintf("%06s",$nombor);
		$investNo = 'PBI'.$nombor;
	}else{
		$investNo = 'PBI000001';
	} 
}

if (!isset($tarikh_invest)) $tarikh_invest = date("d/m/Y");
if (!isset($tarikh_disedia)) $tarikh_disedia = date("d/m/Y");

if($perkara2){
	$updatedBy 	= get_session("Cookie_userName");
	$updatedDate = date("Y-m-d H:i:s");               		

		$deductID = $perkara2;
		$addminus = 1;
		$cajAmt = 0.0;	



		if($pymtAmt == '') 
			$pymtAmt = '0.0';
		$sSQL	= "INSERT INTO transactionacc (" . 
				  "docNo," . 
				  "docID," . 
				  "yrmth," . 
				  "batchNo," . 
				  "deductID," .	
				  "addminus," . 
				  "price," . 
				  "quantity," . 
				  "pymtID," .
				  "pymtRefer," .
				  "pymtReferC," .			
				  "pymtAmt," . 
				  "desc_akaun," . 
				  "updatedBy," . 
				  "updatedDate," . 
				  "createdBy," . 
				  "createdDate) " . 
				  " VALUES (" . 
				"'". $investNo . "', ".
				"'". 15 . "', ".
				"'". $yymm . "', ".
				"'". $batchNo . "', ".
				"'". $deductID . "', ".
				"'". $addminus . "', ".
				"'". $price2 . "', ".
				"'". $quantity2 . "', ".
				"'". 66 . "', ".
				"'". $b_kodGL . "', ".
				"'". $projectID . "', ".
				"'". $debit2 . "', ".
				"'". $desc_akaun2 . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "')";

		if($display) print $sSQL.'<br />';
		else{ $rs = &$conn->Execute($sSQL);
		print '<script>
		window.location = "?vw=ACCinvestdebtor&mn='.$mn.'&action=view&investNo='.$investNo.'";
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
	window.location = "?vw=ACCinvestdebtor&mn='.$mn.'&action=view&investNo='.$investNo.'";
	</script>';
	}
}

elseif($action == "Kemaskini" || $perkara || $desc_akaun ) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "investNo='" . $investNo ."'";
		$tarikh_invest = saveDateDb($tarikh_invest);
		$tarikh_disedia =	saveDateDb($tarikh_disedia);
		$sWhere = " WHERE (" . $sWhere . ")";		
		$sSQL	= "UPDATE pb_invoice SET " .

					"batchNo='" .$batchNo . "',".
					"tarikh_invest='" .$tarikh_invest . "',".
					"kod_project='" .$projectID . "',".
					"kod_jabatan='" .$kod_jabatan . "',".
					"nilai_pelaburan='" .$nilaiPelaburan . "',".
					"companyID='" .$companyID . "',".
					"outstandingbalance='" .$totalDb . "',".					
					"description='" .$description . "',".
					"disedia='" .$disedia . "',".
					"disemak='" .$disemak . "',".
					"disahkan='" .$disahkan . "',".					
					"tarikh_disedia='" .$tarikh_disedia . "',".
					"updatedDate='" .$updatedDate . "',".
					"updatedBy='" .$updatedBy . "'";

		$sSQL = $sSQL . $sWhere;

		$sSQL1 = "";
		$sWhere1 = "";		
	 	$sWhere1 = "docNo='" . $investNo ."' AND addminus='" . 0 ."'";
		$sWhere1 = " WHERE (" . $sWhere1 . ")";		
		$sSQL1	= "UPDATE transactionacc SET ".
					"MdeductID='" .$b_kodGL . "',".
					"deductID='" .$b_kodGL . "',".
					"yrmth='" .$yymm . "',".
					"batchNo='" .$batchNo . "',".
					"pymtReferC='" .$projectID . "',".
					"pymtAmt='" .$masterAmt . "'";
				
		$sSQL1 = $sSQL1 . $sWhere1;

		$sSQL2 = "";
		$sWhere2 = "";		
	 	$sWhere2 = "docNo='" . $investNo ."'";
		$sWhere2 = " WHERE (" . $sWhere2 . ")";		
		$sSQL2	= "UPDATE transactionacc SET ".
					"pymtReferC='" .$projectID . "',".
					"yrmth='" .$yymm . "',".
					"tarikh_doc='" .$tarikh_invest . "'";
					
		$sSQL2 = $sSQL2 . $sWhere2;

		if($display) print $sSQL.'<br />';
		else 
			$rs = &$conn->Execute($sSQL);
			$rs = &$conn->Execute($sSQL1);
			$rs = &$conn->Execute($sSQL2);


	////////////////////////////////////////////////////////////////////////////////////////////
	if(count($perkara)>0){
		foreach($perkara as $id =>$value){

		$deductID = $value;

		$priceA = $price[$id];
		$quantityA = $quantity[$id];
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 1;
		}
		else{
		$pymtAmt = $kredit[$id];
		$addminus = 0;
		}

		$sSQL = "";
		$sWhere = "";		
	    $sWhere = "ID='" . $id ."'";
	    $sSQL	= "UPDATE transactionacc SET " .

	      	"yrmth= '" . $yymm . "',".
	      	"batchNo= '" . $batchNo . "',".
          "deductID= '" . $deductID . "',".
          "addminus= '" . $addminus . "',".
		  "price= '" . $priceA . "',".
		  "quantity= '" . $quantityA . "',".
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

		$MdeductID = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 1;
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 0;
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
	/////////////////////////////////////////////////////////////
	if(count($desc_akaun)>0){
		foreach($desc_akaun as $id =>$value){
		
		$desc_akaun = $value;
		if($debit[$id]){
		$pymtAmt = $debit[$id];
		$addminus = 1;
		
		}else{
		$pymtAmt = $kredit[$id];
		$addminus = 0;
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
/////////////////////////////////////////////////////////////////////////////

	if(!$display){
	print '<script>
	window.location = "?vw=ACCinvestdebtor&mn='.$mn.'&action=view&investNo='.$investNo.'";
	</script>';
	}
}
//pilihan simpan
 elseif($action == "Simpan" || $simpan) {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");               
		$tarikh_invest = saveDateDb($tarikh_invest);
		$tarikh_bayar =	saveDateDb($tarikh_bayar);

		$sSQL = "";
		$sSQL	= "INSERT INTO pb_invoice (" . 
					"investNo, " .
					"batchNo, " .
					"kod_project, " .
					"kod_jabatan, " .
					"nilai_pelaburan, " .
					"companyID, " .
					"kodGL, " .
					"tarikh_invest, " .
					"outstandingbalance, ".
					"description, ".
					"disedia, ".
					"disemak, ".
					"tarikh_disedia, " .
					"tarikh_disemak, " .
					"disahkan, " .
					"tarikh_disahkan, " .
					"createdDate, " .
					"createdBy, " .
					"updatedDate, " .
					"updatedBy) " .

		            " VALUES (" . 
					"'". $investNo . "', ".
					"'". $batchNo . "', ".
					"'". $projectID . "', ".
					"'". $kod_jabatan . "', ".
					"'". $nilaiPelaburan . "', ".
					"'". $companyID . "', ".
					"'". $b_kodGL . "', ".
					"'". $tarikh_invest . "', ".
					"'". $totalDb . "', ".
					"'". $description . "', ".
					"'". $disedia . "', ".
					"'". $disemak . "', ".
					"'". $tarikh_disedia . "', ".
					"'". $tarikh_disemak . "', ".
					"'". $disahkan . "', ".
					"'". $tarikh_disahkan . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "', ".
					"'". $updatedDate . "', ".
					"'". $updatedBy . "')";

		$sSQL1 = "";
		$sSQL1	= "INSERT INTO transactionacc (" . 
				  "docNo," . 
				  "tarikh_doc," . 
				  "yrmth," . 
				  "docID," . 
				  "batchNo," . 
				  "deductID," .		
				  "addminus," . 
				  "price," . 
				  "quantity," .
				  "pymtID," .
				  "pymtRefer," .
				  "pymtReferC," .			
				  "pymtAmt," . 
				  "desc_akaun," . 
				  "updatedBy," . 
				  "updatedDate," . 
				  "createdBy," . 
				  "createdDate) " . 

				  " VALUES (" . 
				"'". $investNo . "', ".
				"'". $tarikh_invest . "', ".
				"'". $yymm . "', ".
				"'". 15 . "', ".
				"'". $batchNo . "', ".
				"'". $b_kodGL . "', ".
				"'". 0 . "', ".
				"'". $price2 . "', ".
				"'". $quantity2 . "', ".
				"'". 66 . "', ".
				"'". $companyID . "', ".
				"'". $projectID . "', ".
				"'". $masterAmt . "', ".
				"'". $desc_akaun2 . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "', ".
				"'". $updatedBy . "', ".
				"'". $updatedDate . "')";

		if($display) print $sSQL.'<br />';
		else 
			$rs = &$conn->Execute($sSQL);
			$rs = &$conn->Execute($sSQL1);

	$getMax = "SELECT MAX(CAST(right(investNo,6) AS SIGNED INTEGER )) AS no FROM pb_invoice";
	$rsMax = $conn->Execute($getMax);
	$max = sprintf("%06s", $rsMax->fields(no));
	
	if(!$display){
	print '<script>
	window.location = "?vw=ACCinvestdebtor&mn='.$mn.'&action=view&add=1&investNo=PBI'.$max.'";
	</script>';
	}
}

$strTemp .=
'<div class="maroon" align="left">'.$strHeaderTitle.'</div>'
.'<div style="width: 100%; text-align:left">'
.'<div>&nbsp;</div>'
.'<div class="table-responsive"><form name="MyForm" action="?vw=ACCinvestdebtor&mn='.$mn.'" method="post">'
.'<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">';


print $strTemp;
print '
<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>INV number</td>
				<td valign="top"></td><td><input class="form-controlx"  name="investNo" value="'.$investNo.'" type="text" size="20" maxlength="50"></td>
				<tr><td nowrap="nowrap">Batch</td><td valign="top"></td><td>'.selectbatchINV($batchNo,'batchNo').'</td></tr>				
				<!--tr><td nowrap="nowrap">Projek</td><td valign="top"></td><td>'.selectproject($kod_project,'kod_project').'</td></tr>
				<tr><td nowrap="nowrap">Jabatan</td><td valign="top"></td><td>'.selectjabatan($kod_jabatan,'kod_jabatan').'</td></tr-->
		</table>
	</td>	
	<td valign="top">&nbsp;</td>
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top" align="right">Tanggal</td><td valign="top"></td><td><input class="form-controlx" name="tarikh_invest" value="'.$tarikh_invest.'" type="text" size="20" maxlength="10" /></td>

			
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3"><hr class="mt-3"></td></tr>';

print '
<tr colspan="3">
	<td valign="top"><input name="j" type="hidden" value="tiada">
	
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td valign="top">Kepada</td>
			</tr>

			<tr>
				<td>* Kod Pelabur</td><td valign="top"></td>
				<td><input name="kod_syarikat" value="'.$companyCode.'" type="text" size="20" maxlength="50"  class="form-controlx" readonly/>&nbsp;';			

			print '<input type="button" class="btn btn-info btn-sm" value="Pilih" onclick="window.open(\'ACCidpelaburan.php?refer=f\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';

			print '&nbsp;<input name="loan_no" type="hidden" value=""></td>
				</td>
			</tr>

			<tr>
				<td valign="top">Nama</td><td valign="top"></td><td><input name="nama_syarikat"  value="'.$companyName.'" type="text" size="40" maxlength="50" class="form-controlx" readonly/>
		    	</td>
		    </tr>

			<tr>
				<td valign="top">Alamat</td>
				<td valign="top"></td>
				<td><textarea name="b_Baddress" cols="50" rows="4" class="form-controlx" readonly>'.$b_Baddress.'</textarea></td>
			</tr>

			<tr>
				</td><td><input type=hidden name="projectID" value="'.$projectID.'" type="text" size="4" maxlength="50" class="data" />
				</td>
			</tr>

			<tr>
				<td valign="top">Projek</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="nama_projek" value="'.$nameproject.'" type="text" size="30" maxlength="100" readonly/>
			</tr>  

		  	<tr>
				</td><td><input type=hidden name="companyID" value="'.$companyID.'" type="text" size="4" maxlength="50" class="data" />
		    	</td>
		    </tr>

		    <tr>
				</td><td><input type=hidden name="b_kodGL" value="'.$b_kodGL.'" size="4" maxlength="50" class="data" />
		    	</td>
		    </tr>
			
			<tr>
				<td valign="top">Nilai Pelaburan (RM)</td>
				<td valign="top"></td>
				<td><input class="form-control-sm" name="nilaiPelaburan" value="'.$nilaiPelaburan.'" type="text" size="30" maxlength="100" readonly/>
			</tr>

		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>';
	//----------
	if ($action=="view" && !is_int(dlookup("transactionacc", "ID", "docNo='" . $investNo ."'"))){
	print '
	<tr>
			<td align= "right" colspan="3">';
	    if(!$add) print '
			<input type="button" name="add" value="Tambah" class="btn btn-sm btn-primary" onClick="window.location.href=\'?vw=ACCinvestdebtor&mn='.$mn.'&action='.$action.'&investNo='.$investNo.'&add=1\';">'; 
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
				<td nowrap="nowrap"><b>Perkara Akaun</b></td>
				<td nowrap="nowrap"><b>Keterangan</b></td>	
				<!--td nowrap="nowrap">Kuantiti</td>	
				<td nowrap="nowrap">Harga Seunit (RM)</td-->			
				<td nowrap="nowrap" align="right"><b>Jumlah (RM)</b></td>
				<td nowrap="nowrap">&nbsp;</td>
			</tr>';

if ($action=="view"){
	$i = 0;
	while (!$rsDetail->EOF) {

	$id = $rsDetail->fields(ID);
	
	$perkara = $rsDetail->fields(deductID);
	$kod_akaun = dlookup("generalacc", "parentID", "ID=" . $perkara);
	$namaparent= dlookup("generalacc", "name", "ID=" . $kod_akaun);
	$debit = $rsDetail->fields(pymtAmt);
	$desc_akaun =	$rsDetail->fields(desc_akaun);
	$quantity = $rsDetail->fields(quantity);
	$price = $rsDetail->fields(price);

		if($rsDetail->fields(addminus)){
		$kredit = $rsDetail->fields(pymtAmt);
		}else{
		$debit = $rsDetail->fields(pymtAmt);
		}
	print	   '
			<tr>
				<td class="Data">'.++$i.'.</td>	

				<td class="Data" nowrap="nowrap">'.strSelectINV1($id,$perkara).'
				<input class="form-control-sm" name="kod_akaun['.$id.']" type="hidden" size="10" maxlength="10" value="'.$kod_akaun.'"/>
				</td>

				<td class="Data" nowrap="nowrap">
					<input name="desc_akaun['.$id.']" type="text" size="40" class="form-control-sm" maxlength="100" value="'.$desc_akaun.'"/>
				</td>';

			
				//column for kuantiti dan harga
				// print '
				// <td class="Data" >
				// 	<input name="quantity['.$id.']" class="form-control-sm" type="text" size="10" maxlength="10" value="'.$quantity.'" "/>
				// &nbsp;
				// </td>

				// <td class="Data">
				// 	<input name="price['.$id.']" class="form-control-sm" type="text" size="10" maxlength="10" value="'.$price.'" "/>
				// &nbsp;
				// </td>';

				print '

				<td class="Data" align="right">
					<input name="debit['.$id.']" type="text" size="10" class="form-control-sm" maxlength="10" value="'.$debit.'" style="text-align:right;"/>
				
				</td>

				<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="'.$id.'"></td>

			</tr>';
		  $totalDb += $debit;

		  $debit = '';
		  print '<input type="hidden" name="totalDb" value="'.$totalDb.'">';
	$rsDetail->MoveNext();
	}
}

$strDeductIDList = deductListINV1(1);
$strDeductCodeList = deductListINV1(2);
$strDeductNameList = deductListINV1(3);
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
				<input name="kod_akaun2" type="hidden" size="10" maxlength="10" value="'.$kod_akaun2.'" class="form-control-sm"/>
				</td>
				<td class="Data" align="left">
					<input name="desc_akaun2" type="text" class="form-control-sm" size="35" maxlength="100" value="'.$desc_akaun2.'" align="right"/>&nbsp;
				</td>';

				//column for kuantiti dan harga
				// print'
				// <td class="Data" align="left">
				// 	<input  name="quantity2" type="text" class="form-control-sm" size="10" maxlength="10" value="'.$quantity2.'" />&nbsp;
				// </td>

				// <td class="Data" align="left">
				// 	<input  name="price2" type="text" class="form-control-sm" size="10" maxlength="10" value="'.$price2.'" />&nbsp;
				// </td>';

				print '

				<td class="Data" align="right">
					<input  name="debit2" type="text" class="form-control-sm" size="10" maxlength="10" value="'.$debit2.'" />&nbsp;
				</td>

				<td class="Data" align="right"><b>&nbsp;</b></td>
			</tr>';
}

if($totalDb<>0){
	$clsRM->setValue($totalDb);
	$strTotal = ucwords($clsRM->getValue()).' Sahaja.';
}

print 		'<tr>
				<td class="Data" colspan="3" align="right"><b>Jumlah</b></td>
				<td class="Data" align="right"><b>'.number_format($totalDb,2).'&nbsp;</b></td>
				<!--td class="Data" align=""><b>&nbsp;</b></td-->
			</tr>
		</table>
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td width="60%" valign="top" colspan="3">
		<table border="0" cellspacing="1" cellpadding="3">

			<tr>
			
			<td nowrap="nowrap">Jumlah Dalam Perkataan</td>
			<td valign="top"></td>
			<td>
				<input class="form-controlx" name="" size="80" maxlength="80" value="'.$strTotal.'" readonly>
				<input class="form-controlx" type="hidden" name="masterAmt" value="'.$totalDb.'">
			</td>

			</tr>


			<tr><td nowrap="nowrap">Disediakan Oleh</td><td valign="top"></td><td>'.selectAdmin($disedia,'disedia').'</td></tr>
			<tr><td nowrap="nowrap">Disahkan Oleh</td><td valign="top"></td><td>'.selectAdmin($disahkan,'disahkan').'</td></tr>
			<tr>
				<td nowrap="nowrap" valign="top">Catatan</td><td valign="top"></td><td valign="top">
					<textarea class="form-controlx" name="description" cols="50" rows="4">'.$description.'</textarea>
				</td>
			</tr>
		</table>
	</td>
</tr>';
print '<input name="kod_caw" type="hidden" value="321"><input name="no_siri" type="hidden" value="S112">

<input name="tarikh_bayar" type="hidden" value="01/10/2006"></tr>';

$straction = ($action=='view'?'Kemaskini':'Simpan');
print '
<tr>
	<td>
	<input type="button" name="print" value="Cetak" class="btn btn-secondary" onClick= "print_(\'ACCinvestdebtorPrint.php?id='. $investNo .'\')">&nbsp;
	<input type="button" name="action" value="'.$straction.'" class="btn btn-primary" onclick="CheckField(\''. $straction. '\')">';
if($straction=='Simpan') print '
	<input type="hidden" name="simpan" value="1">';
print '
	</td>
</tr>';

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