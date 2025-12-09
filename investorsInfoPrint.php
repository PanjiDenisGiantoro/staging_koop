<?php
/*********************************************************************************
*			Project		:iKOOP.com.my
*			Filename	: komoditiPrint.php
*			Date 		: 1/3/2024
*********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
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
.'<body>'
.'<div class="page-container">
';

$footer = '
<script>window.print();</script>
</body></html>';

print $header;

if($id){ 
    $strinvestors = "SELECT * FROM investors WHERE ID = '".$id."'";
    $Getinvestors = &$conn->Execute($strinvestors);
	
	$nameproject 		= $Getinvestors->fields(nameproject);
	$location 			= $Getinvestors->fields(location);
	$area 				= $Getinvestors->fields(area);
	$lulusDate 			= toDate("d/m/Y",$Getinvestors->fields(lulusDate));
	$startDate 			= toDate("d/m/Y",$Getinvestors->fields(startDate));
	$endDate 			= toDate("d/m/Y",$Getinvestors->fields(endDate));
	$area 				= $Getinvestors->fields(area);
	$period 			= $Getinvestors->fields(period);
	$amount 			= number_format($Getinvestors->fields(amount), 2);
	$picharge 			= $Getinvestors->fields(picharge);
	$alkselia 			= $Getinvestors->fields(alkselia);
	$openbalpro 		= number_format($Getinvestors->fields(openbalpro), 2);
}

print '
<style>
.page-container {
    margin-top: 30px;
    margin-bottom: 20px;
}
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
    .page-break {
        page-break-after: always;
    }
</style>

<table width="100%" cellpadding="0" cellspacing="0" class="textFont">
	<tr><td colspan="4"></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>
	<tr><td  colspan="4"><br></td></tr>

	<tr>
		<td colspan="3"><b>SYARIKAT: '.$namacomp = dlookup("generalacc", "name", "ID=" . tosql($Getinvestors->fields(compID), "Text")).'</b>&nbsp;</td>
	</tr>
	
	<tr><td  colspan="4"><br></td></tr>

	<tr>
		<td colspan="3"><b>MAKLUMAT PROJEK</b>&nbsp;</td>
	</tr>

	<tr><td  colspan="4"><br></td></tr>

	<div class="page-container">
		<table width="100%" cellpadding="7" cellspacing="5" class="textFont">
			<tr>
				<td align="right" class="borderSideTitle"><b>Nama Projek:</b></td>
				<td align="left" class="borderSideTitle">'.$nameproject.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Lokasi:</b></td>
				<td align="left" class="borderSideTitle">'.$location.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Keluasan Tanah:</b></td>
				<td align="left" class="borderSideTitle">'.$area.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Tanggal Kelulusan Mesyuarat (ALK):</b></td>
				<td align="left" class="borderSideTitle">'.$lulusDate.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Tanggal Mula (Perjanjian/Penubuhan):</b></td>
				<td align="left" class="borderSideTitle">'.$startDate.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Tanggal Akhir (Perjanjian):</b></td>
				<td align="left" class="borderSideTitle">'.$endDate.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Tempoh Perjanjian (Bulan):</b></td>
				<td align="left" class="borderSideTitle">'.$period.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Nilai Pelaburan (RP):</b></td>
				<td align="left" class="borderSideTitle">'.$amount.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>PIC:</b></td>
				<td align="left" class="borderSideTitle">'.$picharge.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>ALK Selian:</b></td>
				<td align="left" class="borderSideTitle">'.$alkselia.'</td>
			</tr>
			<tr>
				<td align="right" class="borderSideTitle"><b>Opening Balance (RP):</b></td>
				<td align="left" class="borderSideTitle">'.$openbalpro.'</td>
			</tr>
		</table>
	</div>

    ';

print $footer;
?>
