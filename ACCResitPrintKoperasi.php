<?php
/*********************************************************************************
*			Project		:iKOOP.com.my
*			Filename	: voucherPaymentPrint.php
*			Date 		: 4/8/2006
*********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields(name);
$address1 = $rss->fields(address1);
$address2 = $rss->fields(address2);
$address3 = $rss->fields(address3);
$address4 = $rss->fields(address4);
$noPhone = $rss->fields(noPhone);
$email = $rss->fields(email);
$koperasiID = $rss->fields(koperasiID);

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2 OR get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'</title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '
<script>window.print();</script>
</body>

</html>';

print $header;

if($id){
	$sql = "SELECT *
			FROM  resitacc 
			WHERE no_resit = '" . $id ."'";
	
	$rs 			= $conn->Execute($sql);
	$no_resit		= $rs->fields(no_resit);
	$tarikh_resit 	= toDate("d/m/y",$rs->fields(tarikh_resit));
	$bayar_kod 		= $rs->fields(bayar_kod);
	$bayar_nama 	= $rs->fields(name);
	$no_anggota 	= $rs->fields(memberID);
	$cara_bayar 	= $rs->fields(cara_bayar);
	$tarikh 		= toDate("d/m/y",$rs->fields(tarikh));
	$Cheque			= $rs->fields(Cheque);
	$akaun_bank 	= $rs->fields(akaun_bank);
	$kerani 		= $rs->fields(kerani);
	$kod_project 	= $rs->fields(kod_project);
	$kod_jabatan	= $rs->fields(kod_jabatan);
		$keterangan		= $rs->fields(keterangan);
	$diterima_drpd	= $rs->fields(diterima_drpd);
	$catatan 		= $rs->fields(catatan);

	$master 		= $rs->fields(masteraccount);
	$masterA 		= dlookup("generalacc", "name", "ID=".$master);

	$kod_bank 		= $rs->fields(kod_bank);
	$kod_bankA		= dlookup("generalacc", "name", "ID=".$kod_bank);

	$disedia			=  $rs->fields('kerani');
	$disedia1	=  dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$sedia = strtoupper(strip_tags($disedia1));


	$sqltotal 	= "SELECT sum(pymtAmt) as tot FROM transactionacc WHERE docNo = '".$id."'";
	$rstotal 	= $conn->Execute($sqltotal);
	$jumlah 	= $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = ".tosql($no_resit, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
}
print '

<tr><td  colspan="8"><br></td></tr>
<section>
<table width="100%" border="0" cellpadding="1" cellspacing="0" class="textFont">
	
	<tr>
		<td colspan="8" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="midle" class="textFont">
					<b>'.$coopName.'</b><br />
					'.$address1.',<br />
					'.$address2.',<br />
					'.$address3.',<br />
					'.$address4.'.<br />
					TEL: '.$noPhone.'<br />
					EMEL: '.$email.'<br />
					</td>
				</tr>
			</table>&nbsp;
		</td>
	</tr>


	<tr><td colspan="8">&nbsp;</td></tr>



	<tr>
		<td colspan="5"><b>BAYAR KEPADA	:</b>'.$diterima_drpd.'</td>
		<td colspan="4" align="right" style="font-size:20px;" "><b>RESIT PEMBAYARAN&nbsp;</b></td>
	</tr>

	<tr><td  colspan="8"><br></td></tr>

	<tr>
		<td colspan="4"><b>BANK A/C :</b>'.$kod_bankA.'</td>
		<td colspan="4" align="right"><b>NO RESIT: </b>'.$no_resit.'</td>
	</tr>

	<tr><td  colspan="8"><br></td></tr>

	<tr>
		<td colspan="4"><b>CHEQUE NO :</b>'.$Cheque.'</td>
		<td colspan="4" align="right"><b>TARIKH: </b>'.$tarikh_resit.'</td>
	</tr>

	<tr><td  colspan="8"><br></td></tr>

	<tr>
		<td colspan="8"><b>CARA BAYARAN :</b>'.$cara_bayar.'</td>
	</tr>
	
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>


	<tr><td colspan="8"><hr size="2px" /></td></tr>

	<tr>
		<td nowrap="nowrap" align="center"><b>&nbsp;A/C NO.&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;A/C KETERANGAN&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;PROJEK&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;JABATAN&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;CUKAI&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;KETERANGAN&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;DEBIT&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;KREDIT&nbsp;</b></td>		
	</tr>

	<tr><td colspan="8"><hr size="1px" /></td></tr>';
if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 		= $rsDetail->fields(deductID);
		$codeproject 	= $rsDetail->fields(kod_project);
		$codejabatan 	= $rsDetail->fields(kod_jabatan);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$taxing 	= $rsDetail->fields(taxNo);
		//$keterangan_resit 	= $rsDetail->fields(keterangan);


		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$kodprojek 	= dlookup("generalacc", "name", "ID=".$codeproject);
		$kodjabatan = dlookup("generalacc", "name", "ID=".$codejabatan);
		$cukai  	= dlookup("generalacc", "name", "ID=".$taxing);

		//$resitdesc = dlookup("resitacc", "keterangan", "ID=".$keterangan_resit);
		
		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td nowrap="nowrap" align="center">&nbsp;'.$accNombor.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$accdet.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$kodprojek.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$kodjabatan.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$cukai.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$desc_akaun.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$totPymt.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.$totPymt.'&nbsp;</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRM->setValue($jumlah1);
			$strTotal1 = ucwords($clsRM->getValue()).' Ringgit Sahaja.';
			}
		}

print
	'	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>
	<tr><td  colspan="8"><br></td></tr>


	<tr>
		<td nowrap="nowrap" align="left"><b>&nbsp;RINGGIT MALAYSIA :'.$strTotal1.'&nbsp;</b></td>
		<td nowrap="nowrap" align="left"><b>&nbsp;&nbsp;</b></td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		
	</tr>
	
	<tr><td colspan="8"><hr size="1px" /></td></tr>

	<tr>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left">&nbsp;</td>
		<td nowrap="nowrap" align="left"><b>JUMLAH&nbsp;</b></td>
		<td nowrap="nowrap" align="left"><b>&nbsp;RM '.number_format($jumlah,2).'&nbsp;</b></td>
		<td nowrap="nowrap" align="left"><b>&nbsp;RM '.number_format($jumlah,2).'&nbsp;</b></td>
	</tr>
</table>

</td></tr>
	<tr><td colspan="8"><hr size="1px" /></td></tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr><td colspan="8" align="right"></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3" align="center">&nbsp;&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">
	<br></br><br></br><br></br>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap">&nbsp;</td>
				<td nowrap="nowrap" align="right">
					<table cellpadding="0" cellspacing="0">
						<tr>
						<td align="center">(<b>'.$sedia.'</b>)<br />Disediakan Oleh</td></tr>
					</table>
				</td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Diluluskan Oleh</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Disahkan Oleh</td></tr></table></td>
			</tr>
			<tr><td colspan="8"><hr size="1px" /></td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr>
				<td nowrap="nowrap">&nbsp;</td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Pengerusi/Timbalan Pengerusi</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Setiausaha/Timbalan Setiausaha</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Bendehari/Timbalan Bendehari</td></tr></table></td>
			</tr>
			<tr><td colspan="8"><hr size="1px" /></td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr>
		<td colspan="8"><b>DISEDIAKAN OLEH :</b> '.$sedia.'</td>
	</tr>
			
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr>
				<td colspan="8">Nama 	:</td>
			</tr>
			<tr>
				<td colspan="8">No IC :</td>
			</tr>
			<tr>
				<td colspan="8">Tarikh 	:</td>
			</tr>
		</table>
	</td></tr>
</table>
</section>
	';
print $footer;
?>