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
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

$footer = '
<script>window.print();</script>
</body></html>';

print $header;

if($id){
	$sql = "SELECT a.*, b.* FROM pb_invoice a, generalacc b WHERE a.companyID = b.ID and a.investNo = '".$id."'";          
	$rs = $conn->Execute($sql);
	
	$investNo 			= $rs->fields(investNo);
	$tarikh_inv 		= toDate("d/m/y",$rs->fields(tarikh_inv));
	$name 					= $rs->fields(name);
	$disahkan				= $rs->fields('disahkan');
	$disahkan1			= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
	$sah 						= strtoupper(strip_tags($disahkan1));
	$disedia				= $rs->fields('disedia');
	$disedia1				= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$sedia 					= strtoupper(strip_tags($disedia1));
	$companyID			= $rs->fields('companyID');
	$departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
	$alamat 				= strtoupper(strip_tags($departmentAdd));
	$description		= $rs->fields('description');
	
	$sql2 = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($investNo, "Text")." ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
}

print '
<style>
.boxGray {
	padding: 7px;
	width: 110px;
	background-color: lightgray;
	word-wrap: break-word;
	}
	.box {
	padding: 7px;
	width: 110px; 
	}
	.boxTitle {
	padding: 5px;
	font-size: 20px;
	width: fit-content;
	height: fit-content;
	border: 1px solid black;
	}
	.bottom {
		position: fixed;
		bottom: 0px;
		text-align: center;
		width: 100%;
	}
	.borderRight {
		border-right: 1px solid;
	}
	.borderSide {
		border-right: 1px solid;
		border-left: 1px solid;
	}
	.borderLeft {
		border-left: 1px solid;
	}
	.borderRightTitle {
		border-right: 1px solid;
		border-bottom: 1px solid;
		border-top: 1px solid;
	}
	.borderSideTitle {
		border: 1px solid;
	}
	.borderLeftTitle {
		border-left: 1px solid;
		border-bottom: 1px solid;
		border-top: 1px solid;
	}
	.borderTop {
		border-top: 1px solid;
	}
</style>

<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
<tr><td align="right"><div class="boxGray" align="center">Invoice</div></td></tr>
<tr><td align="right"><div class="box" align="center"><b>'.$investNo.'</b></div></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr>
		<td colspan="4" align="center">		
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="midle" class="textFont">
					<tr><td align="center"><div class="boxTitle" align="center"><b>' . $coopName . '</b></div></td></tr>
					<tr><td align="center" valign="midle" class="textFont">
					' . ucwords(strtolower($address1)) . '<br />
					' . ucwords(strtolower($address2)) . '<br />
					' . ucwords(strtolower($address3)) . '<br />
					' . ucwords(strtolower($address4)) . '<br />
					EMEL : ' . $email . '<br />
					NO. TEL : ' . $noPhone . '<br />
					</td>
				</tr>
			</table>&nbsp;
		</td>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr><td colspan="4"></td></tr>

	<tr>
		<td colspan="3"><b>KEPADA :</b> '.$name.'&nbsp;</td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>
	<tr>
		<td style="max-width:50px"><b>ALAMAT :</b> '.$alamat.'</td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr>

		<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center"><b>&nbsp;MUKASURAT&nbsp;</b></td>
		<td nowrap="nowrap" align="center"><b>&nbsp;TARIKH&nbsp;</b></td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>

	<tr>
	<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center">&nbsp;&nbsp;</td>
		<td nowrap="nowrap" align="center">&nbsp;1&nbsp;</td>
		<td nowrap="nowrap" align="center">'.$tarikh_inv.'</td>
	</tr>
	<tr><td  colspan="4"><br></td></tr>
	

	<table width="100%" cellpadding="2" cellspacing="0" class="textFont">
<tr>
    	<td align="left" class="borderLeftTitle"><b>&nbsp;NO AKAUN / NAMA AKAUN&nbsp;</b></td>
		<td align="left" class="borderSideTitle"><b>&nbsp;KETERANGAN&nbsp;</b></td>	
        <td align="right" class="borderRightTitle"><b>&nbsp;JUMLAH (RM)&nbsp;</b></td>
	</tr>';

		if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
		$deductID 	= $rsDetail->fields(deductID);
		$taxing 		= $rsDetail->fields(taxNo);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$tarikh 		= $rsDetail->fields(createdDate);
		$tarikh 		= substr($tarikh,8,2)."/".substr($tarikh,5,2)."/".substr($tarikh,0,4);

		$desc 		= dlookup("generalacc", "name", "ID=".$deductID);
		$codedesc = dlookup("generalacc", "code", "ID=".$deductID);
		$tax 			= dlookup("generalacc", "name", "ID=".$taxing);
		
		$addminus = $rsDetail->fields(addminus);
		$totPymt4 = $rsDetail->fields(pymtAmt);
		print
			'<tr>
				<td nowrap="nowrap" align="left" class="borderLeft">&nbsp;'.$codedesc.' - '.$desc.'&nbsp;</td>
				<td nowrap="nowrap" align="left" class="borderSide">&nbsp;'.$desc_akaun.'&nbsp;</td>
				<td nowrap="nowrap" align="right" class="borderRight">&nbsp;'.number_format($totPymt4,2).'&nbsp;</td>
			</tr>';
		
			$jumlah += $totPymt4;
			$rsDetail->MoveNext();
			}
			if($jumlah<>0){
			$clsRM->setValue($jumlah);
			$strTotal = strtoupper($clsRM->getValue());
			}
		}
print
	'
	<tr>
	<td nowrap="nowrap" align="center" class="borderTop">&nbsp;</td>
	<td nowrap="nowrap" align="right" class="borderTop">&nbsp;</td>
	<td nowrap="nowrap" align="right" class="borderTop">&nbsp;</td>
</tr>
	<tr>
	<td nowrap="nowrap" align="center">&nbsp;</td>
		<td nowrap="nowrap" align="right"><b>JUMLAH (RM):&nbsp;</b></td>
		<td nowrap="nowrap" align="right"><b>RM&nbsp;'.number_format($jumlah,2).'&nbsp;</b></td>
	</tr>
	</table>
	<tr><td  colspan="4"><br></td></tr>
	<tr><td colspan="5"><hr size="1px" /></td></tr>
	<left><tr><td nowrap="nowrap" align="left"><b>&nbsp;JUMLAH DALAM PERKATAAN :&nbsp;</b></td>
		<td nowrap="nowrap" align="left"><b>&nbsp;'.$strTotal.' RINGGIT MALAYSIA SAHAJA.&nbsp;</b></td></tr>
		</center>
	<tr><td colspan="5"><hr size="1px" /></td></tr>
				<tr>
				<table>
	<tr>
		<td colspan="4"><b>DISEDIAKAN OLEH :</b> '.$sedia.'</td>
	</tr>

	<tr>
		<td colspan="4"><b>DISAHKAN OLEH :</b> '.$sah.'</td>
	</tr>
</table>
	
	</section>
	<table width="100%" border="1" cellpadding="10" cellspacing="0" class="textFont">
<tr><td>Akaun Bank: <br />
Nama Bank: <br />
&nbsp;</td></tr>&nbsp;
</table>
<center>
<div class="bottom"><hr size="1px">
  <b>INI ADALAH CETAKAN KOMPUTER DAN TIDAK PERLU DITANDATANGAN</b>
</div> 
</center>
	';
print $footer;
?>
