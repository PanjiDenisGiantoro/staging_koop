<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCsingleEntryView.php
*			Date 		: 4/8/2006
*********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

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

if($id){
	$sql = "SELECT * FROM singleentry WHERE SENO = '".$id."'";
 	
	$rs 				= $conn->Execute($sql);
	$SENO 				= $rs->fields(SENO);
	$tarikh_entry 		= $rs->fields(tarikh_entry);
	$tarikh_entry 		= substr($tarikh_entry,8,2)."/".substr($tarikh_entry,5,2)."/".substr($tarikh_entry,0,4);
	$keterangan 		= $rs->fields(keterangan);
	$description 		= $rs->fields(description);
	$nama 				= $rs->fields(name);
	$maklumat        	= $rs->fields(maklumat);
	$batchNo 			= $rs->fields(batchNo);
	$accountNo 			= $rs->fields(accountNo);
	$taxNo				= $rs->fields(taxNo);
    $kod_project 		= $rs->fields(kod_project);
    $kod_caw            = $rs->fields(kod_jabatan);
    $nama_caw	        = dlookup("generalacc", "name", "ID=".$kod_caw );
    $alamat_caw	        = dlookup("general", "b_Address", "ID=".$kod_caw );
    $nama_project       = dlookup("generalacc","name","ID=".$kod_project);
	$disedia			= $rs->fields('disediakan');
	$disedia1	        = dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$sedia              = strtoupper(strip_tags($disedia1));
	$namabatch	        = dlookup("generalacc", "name", "ID=".$batchNo);
	
	$sql2 = "SELECT * FROM transactionacc WHERE docNo = '". $SENO ."' ORDER BY ID";
	$rsDetail = $conn->Execute($sql2);
}

print'
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .stylish-date{
            float:right;
        }
        .bor-penerima {
            margin-top: 5%;
            margin-bottom: 3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            max-width: 50%;
            table-layout: fixed; /* Ensures a fixed layout */
        }
        .bor-penerima td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
        .header-border{
            margin-top:5%;
            border: solid 2px;
        }
        .stylish-kerani{
            margin-top:1%;
            margin-bottom:3%;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            table-layout: fixed;
        }
        .stylish-kerani td {
            vertical-align: top; /* Aligns content to the top */
            word-wrap: break-word; /* Allows long words to wrap */
            text-align: justify; /* Apply justification */
        }
    </style>
</head>
';

print'
<body>
<div class="form-container">
    <!---------Doc/Date-------->
	<table class="stylish-date">
        <tr>
            <td><b>NO RUJUKAN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$SENO.'</td>
        </tr>
        <tr>
            <td><b>TARIKH</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_entry.'</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
    ';
    if($namabatch <> "") {
        print'
        <td nowrap="nowrap">NAMA BATCH: </td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($namabatch)).'</td>
    </tr>
    ';
	}
    if($nama_project <> "") {
        print'
    <tr>
        <td nowrap="nowrap">PROJEK</td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($nama_project)).'</td>
    </tr>
    ';
    }
    if($nama_caw <> "") {
        print'
    <tr>
        <td nowrap="nowrap">CAWANGAN</td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($nama_caw)).'</td>
    </tr>
        ';
    }
    print'
    </table>';

    print'
    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
                <td nowrap="nowrap" ><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="right"><b>DEBIT (RP)</b></td>
                <td nowrap="nowrap" align="right"><b>KREDIT (RP)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
		$deductID 	= $rsDetail->fields(deductID);
		$taxing 	= $rsDetail->fields(taxNo);
		$tarikh 	= $rsDetail->fields(createdDate);
		$tarikh 	= substr($tarikh,8,2)."/".substr($tarikh,5,2)."/".substr($tarikh,0,4);
		$batchNom 	= $rsDetail->fields(batchNo);
		$batchN 	= $rsDetail->fields(batchNo);
		$perkara 	= $rsDetail->fields(deductID);
		$desc_akaun =	$rsDetail->fields(desc_akaun);

		$desc 			= dlookup("generalacc", "name", "ID=".$deductID);
		$tax 			= dlookup("generalacc", "name", "ID=".$taxing);
		$batchNombor 	= dlookup("generalacc", "ID", "ID=".$batchN);
		$batchdet 		= dlookup("generalacc", "name", "ID=".$batchNom);
		$a_keterangan 	= dlookup("generalacc", "code", "ID=" . tosql($rsDetail->fields(deductID), "Number"));
		$perkara2 		= dlookup("generalacc", "name", "ID=".$perkara);

		$totPymt = number_format($rsDetail->fields(pymtAmt),2);

		if($rsDetail->fields(addminus))
		{
			$kredit = $rsDetail->fields(pymtAmt);
			$jumlahKrt += $rsDetail->fields(pymtAmt);
		}else{
			$debit = $rsDetail->fields(pymtAmt);
			$jumlahDbt += $rsDetail->fields(pymtAmt);
		}
		print
			'<tr>
				<td nowrap="nowrap" align="left">'.$i.'</td>
				<td nowrap="nowrap" align="left">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="right">';
					if($debit<>0) print number_format($debit,2); 
					print '</td>
				<td nowrap="nowrap" align="right">';
					if($kredit<>0) print number_format($kredit,2);
					print '</td>
			</tr>';
			$jumlah += $rsDetail->fields(pymtAmt);
			$debit = '';
			$kredit = '';
			$i++;
			$rsDetail->MoveNext();
			}
			if($jumlah<>0){
			$clsRP->setValue($jumlah);
			$strTotal = ucwords($clsRP->getValue());
			}
		} 

$baki = $jumlahDbt - $jumlahKrt;
print'
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr>
		<td nowrap="nowrap" colspan="1"></td>
		<td nowrap="nowrap" align="right"><b>JUMLAH (RP)<b></td>
		<td nowrap="nowrap" align="right">'.number_format($jumlahDbt,2).'</td>
		<td nowrap="nowrap" align="right">'.number_format($jumlahKrt,2).'</td>
	</tr>
	<tr>
		<td nowrap="nowrap" colspan="1"></td>
		<td nowrap="nowrap" align="right"><b>BAKI BALANCE (RP)<b></td>
		<td nowrap="nowrap" colspan="2" align="right">'.number_format($baki,2).'</td>
	</tr>
    </table>
    <tr><td colspan="5">&nbsp;</td></tr>

    <table class="stylish-kerani">

        <!-----------Catatan/Description------------->
            <tr>
                <td nowrap="nowrap"><b>CATATAN</b></td>
                <td>&nbsp;:&nbsp;</b></td>
                <td>'.ucwords(strtolower($keterangan)).'</td>
            </tr>

        <!-----------Kerani/Akaun/Nama Bank------------->
            <tr>
                <td><b>DISEDIAKAN OLEH</b></td>
                <td>&nbsp;:&nbsp;</b></td>
                <td>'.ucwords(strtolower($disedia)).'</td>
            </tr>
    </table>
    </div>
    </body>
</html>

</body></html>';