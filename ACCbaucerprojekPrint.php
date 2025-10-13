<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: accprintresit.php
*			Date 		: 27/7/2006
*********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$ssSQL = "SELECT name, address1, address2, address3, address4, noPhone, email, koperasiID FROM setup
        WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$coopName = $rss->fields('name');
$address1 = $rss->fields('address1');
$address2 = $rss->fields('address2');
$address3 = $rss->fields('address3');
$address4 = $rss->fields('address4');
$noPhone = $rss->fields('noPhone');
$email = $rss->fields('email');
$koperasiID = $rss->fields('koperasiID');

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$header =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
.'<html>'
.'<head>'
.'<title>'.$emaNetis.'</title>'
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE8U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '
<script>window.print();</script>
</body></html>

<center>
	<div class="bottom"><hr size="1px">
  		<b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
	</div>
</center>'
;

if($id){
	$sql = "SELECT * FROM baucerprojekacc WHERE no_baucer = '".$id."'";
	$rs = $conn->Execute($sql);

	$no_baucer 		= $rs->fields(no_baucer);
	$tarikh_baucer 	= toDate("d/m/y",$rs->fields(tarikh_baucer));
	$Cheque 		= $rs->fields(Cheque);
	$name 			= $rs->fields(name);
	$bayaran_kpd	= $rs->fields(bayaran_kpd);
	$cara_bayar 	= $rs->fields(cara_bayar);
	$Ncara_bayar 	= dlookup("general","name","ID=".tosql($cara_bayar,"Text"));
	$keterangan		= $rs->fields(keterangan);
	$disedia		= $rs->fields(disedia);
	$disedia1		= dlookup("users","name","userID=".tosql($disedia,"Text"));
	$sedia 			= strtoupper(strip_tags($disedia1));
	$kod_bank 		= $rs->fields(kod_bank);
	$k_bank			= dlookup("generalacc","name","ID=".$kod_bank);

	$sqltotal = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE addminus IN (0) AND docNo = '".$id."'";
	$rstotal = $conn->Execute($sqltotal);
	$jumlah = $rstotal->fields(tot);
	
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = ".tosql($no_baucer, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
}


		$codeproject= $rsDetail->fields(kod_project);
		$codejabatan= $rsDetail->fields(kod_jabatan);

		$kodprojek	= dlookup("generalacc", "name", "ID=".$codeproject);
		$kodjabatan	= dlookup("generalacc", "name", "ID=".$codejabatan);


$header .=

'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="textFont">
	<tr>
		<td colspan="3" align="right"><div class="boxGray" align="center"><b>BAUCER BAYARAN</b></div>
	</tr>

	<tr>
	<td colspan="3" align="right"><div class="box" align="center"><b>'.$no_baucer.'</b></div></td></tr>
  
</table>'

.'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="textFont">'

	.'<tr>'
		.'<td colspan="2" align="center">'
		.'<div class="boxTitle" align="center"><b>' . $coopName . '</b></div>'
		.'</td>'
	.'</tr>'

	.'<tr>'
		.'<td align="center" valign="middle" class="textFont">'
		.'' . ucwords(strtolower($address1)) . '<br />'
		.'' . ucwords(strtolower($address2)) . '<br />'
		.'' . ucwords(strtolower($address3)) . '<br />'
		.'' . ucwords(strtolower($address4)) . '<br />'
		.'EMEL : ' . $email . '<br />'
		.'NO. TEL : ' . $noPhone . '<br />'
		.'</td>'
	.'</tr>'
.'</table>';

print $header;
if($jumlah<>0){
	$clsRM->setValue($jumlah);
	$strTotal = ucwords($clsRM->getValue()).' Ringgit Sahaja.';
}
$jumlah = number_format($jumlah,2);

print 
'<style>
	.boxTitle {
    	padding: 5px;
    	font-size: 20px;
    	width: fit-content;
    	height: fit-content;
    	border: 1px solid black;
	}

	.boxGray {
		padding: 5px;
		width: 150px;
		background-color: lightgray;
		word-wrap: break-word;
	}

	.box {
		padding: 5px;
		width: 150px;
	}
	
	.bottom {
		position: fixed;
		bottom: 10px;
		text-align: center;
		width: 100%;
	}		
</style>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td colspan="8">&nbsp;</td></tr>
	<td valign="top">&nbsp;</td>
	
	<td width="48%" align="right">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td colspan="2" align="right"><b>TARIKH</b></td>
				<td colspan="2">:</td>
				<td colspan="2">'.$tarikh_baucer.'</td>
			</tr>
		</table>
	</td>

	<tr><td colspan="8">&nbsp;</td></tr>
	<table>
	<tr>
		<td nowrap="nowrap" align="left">BAYAR KEPADA</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$bayaran_kpd.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">KETERANGAN</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$keterangan.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">BANK</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$k_bank.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">CHEQUE NO</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$Cheque.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">CARA BAYARAN</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$Ncara_bayar.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">PROJEK</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$kodprojek.'</td>
	</tr>

	<tr>
		<td nowrap="nowrap" align="left">JABATAN</td>
		<td nowrap="nowrap" align="left">:</td>
		<td nowrap="nowrap" align="left">'.$kodjabatan.'</td>
	</tr>
	</table>

	<tr><td colspan="4"><hr size="2px"></td></tr>


		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap" align="left">&nbsp;A/C KETERANGAN&nbsp;</td>
				<td nowrap="nowrap" align="left">&nbsp;KETERANGAN&nbsp;</td>
				<td nowrap="nowrap" align="center"><b>&nbsp;DEBIT&nbsp;</b></td>
				<td nowrap="nowrap" align="center"><b>&nbsp;KREDIT&nbsp;</b></td>	
			</tr>
		<tr><td colspan="4"><hr size="2px" /></td></tr>';
		$jumlah1=0;
		$jumlah2=0;

	if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {


		

		$accNom 	= $rsDetail->fields(deductID);
		$accN 		= $rsDetail->fields(deductID);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$accNombor	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$tarikh 	= $rsDetail->fields(createdDate);
		$tarikh 	= substr($tarikh,8,2)."/".substr($tarikh,5,2)."/".substr($tarikh,0,4);
		$addminus 	= $rsDetail->fields(addminus);

		if ($addminus == 1) {
			$totPymt3 = $rsDetail->fields(pymtAmt);
		print
			'<tr>
				<td nowrap="nowrap" align="left">&nbsp;'.$accNombor.'&nbsp;-&nbsp;'.$accdet.'&nbsp;</td>
				<td nowrap="nowrap" align="left">&nbsp;'.$desc_akaun.'&nbsp;</td>
				<td nowrap="nowrap" align="left"></td>
				<td nowrap="nowrap" align="center">&nbsp;'.number_format($totPymt3,2).'&nbsp;</td>
			</tr>';
			$jumlah1 += $totPymt3;
		
		}


		if ($addminus == 0) {
			$totPymt4 = $rsDetail->fields(pymtAmt);
		print
			'<tr>
				<td nowrap="nowrap" align="left">&nbsp;'.$accNombor.'&nbsp;-&nbsp;'.$accdet.'&nbsp;</td>
				<td nowrap="nowrap" align="left">&nbsp;'.$desc_akaun.'&nbsp;</td>
				<td nowrap="nowrap" align="center">&nbsp;'.number_format($totPymt4,2).'&nbsp;</td>
				<td nowrap="nowrap" align="left"></td>
			</tr>';
			$jumlah2 += $totPymt4;
		}
			
			$rsDetail->MoveNext();

			
			

			}
			if($jumlah1<>0){
			$clsRM->setValue($jumlah1);
			$strTotal1 = strtoupper($clsRM->getValue()).' RINGGIT SAHAJA.';
			}
		}

print '<tr><td colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr>
		<td nowrap="nowrap" colspan="2" align="right"><b>JUMLAH (RP)<b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;'.number_format($jumlah1,2).'&nbsp;</b></td>	
		<td nowrap="nowrap" align="center"><b>&nbsp;'.number_format($jumlah2,2).'&nbsp;</b></td>		
	</tr>
	


	</table>
	</td></tr>

	<tr><td  colspan="4"><br></td></tr>
	<tr>
		<td nowrap="nowrap" align="center"><b>&nbsp;JUMLAH:&nbsp;</b></td>
		<td nowrap="nowrap" align="left"><b>&nbsp;'.$strTotal1.'&nbsp;</b></td>
	</tr>


	<tr><td colspan="8"><hr size="1px" /></td></tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr><td colspan="8" align="right"></td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3" align="center">&nbsp;&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">
	<br></br><br></br>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="nowrap">&nbsp;</td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center"><b>'.$sedia.'</b><br />DISEDIAKAN OLEH</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />DILULUSKAN OLEH</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />DISAHKAN OLEH</td></tr></table></td>
			</tr>
			<tr><td colspan="7"><hr size="1px" /></td></tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr>
				<td nowrap="nowrap">&nbsp;</td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Pengerusi/Timbalan Pengerusi</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Setiausaha/Timbalan Setiausaha</td></tr></table></td>
				<td nowrap="nowrap" align="right"><table cellpadding="0" cellspacing="0"><tr><td align="center">_____________________________<br />Bendehari/Timbalan Bendehari</td></tr></table></td>
			</tr>
			<tr><td colspan="7"><hr size="1px" /></td></tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr><td colspan="7">&nbsp;</td></tr>
			<tr>
		<td colspan="7"><b>DISEDIAKAN OLEH :</b> '.$sedia.'</td>
	</tr>
		</table>
<center>
<div class="bottom"><hr size="1px">
  <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
</div>
</center>';
print $footer;?>