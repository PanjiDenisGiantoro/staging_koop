<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCBillViewCustomer.php
*			Date 		: 27/7/2006
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
.'<meta name="GENERATOR" content="'.$yVZcSz2OuGE8U.'">'
.'<meta http-equiv="pragma" content="no-cache">'
.'<meta http-equiv="expires" content="0">'
.'<meta http-equiv="cache-control" content="no-cache">'
.'<LINK rel="stylesheet" href="images/mail.css" >'
.'</head>'
.'<body>';

if($id){
	$sql = "SELECT * FROM billacc WHERE no_bill = '".$id."'";
	$rs = $conn->Execute($sql);

	$no_bill 		= $rs->fields(no_bill);
	$tarikh_bill 	= toDate("d/m/y",$rs->fields(tarikh_bill));
	$tarikh 		= toDate("d/m/y",$rs->fields(tarikh));

    if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
		$PINo           = $rs->fields(PINo);
		$tarikhPI       = toDate("d/m/y",dlookup("cb_purchaseinv", "tarikh_PI", "PINo=".tosql(($PINo),"Text")));
		$totalPI        = number_format(dlookup("cb_purchaseinv", "outstandingbalance", "PINo=".tosql(($PINo))) - dlookup("cb_purchaseinv", "balance", "PINo=".tosql(($PINo))));
	}	
	$bayar_kod 		= $rs->fields(bayar_kod);
	$bayar_nama		= $rs->fields(name);
	$no_anggota 	= $rs->fields(memberID);

    $bank   	    = dlookup("generalacc", "name", "ID=".$rs->fields(kod_bank));

	$carabayar 		= $rs->fields(cara_byr);
	$cara_byr 		= dlookup("general", "name", "ID=".$carabayar);
	
	$Cheque			= $rs->fields(Cheque);
	$akaun_bank 	= $rs->fields(akaun_bank);
	$kod_project 	= $rs->fields(kod_project);
	$kod_jabatan	= $rs->fields(kod_jabatan);	
	$keterangan		= $rs->fields(keterangan);
    $catatan		= $rs->fields(maklumat);
	$diterima_drpd	= $rs->fields(diterima_drpd);
	$namac 			= dlookup("generalacc", "name", "ID=".$diterima_drpd);
	$departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($diterima_drpd, "Number"));
	$alamat 		= strtoupper(strip_tags($departmentAdd));

	$master 		= $rs->fields(masteraccount);
	$masterA 		= dlookup("generalacc", "name", "ID=".$master);

	$kod_bank 		= $rs->fields(kod_bank);
	$kod_bankA 		= dlookup("generalacc", "name", "ID=".$kod_bank);

	$sqltotal       = "SELECT SUM(pymtAmt) AS tot FROM transactionacc WHERE addminus IN (0) AND docNo = '".$id."'";
	$rstotal        = $conn->Execute($sqltotal);
	$jumlah         = $rstotal->fields(tot);
	
	$sql2           = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = ".tosql($no_bill, "Text")." ORDER BY ID";
	$rsDetail       = $conn->Execute($sql2);

    $nomPI 	        = $rs->fields(PINo);
    $amaunPI 	    = $rs->fields(pymtAmt);

    $kerani		    = $rs->fields(kerani);
	$kerani		    = dlookup("users", "name", "userID=" . tosql($kerani, "Text"));
	$kerani		    = strtoupper(strip_tags($kerani));
    $disedia		= $rs->fields(disedia);
	$disedia		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$disedia		= strtoupper(strip_tags($disedia));
	$disahkan 		= $rs->fields(disahkan);
	$disahkan		= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
	$disahkan		= strtoupper(strip_tags($disahkan));

    if ($rs->fields(batchNo)) {
        $namaBatch 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Text"));
    } else {
        $namaBatch 	= "-";
    }

    $invLhdn		= dlookup("cb_purchaseinv", "invLhdn", "PINo=" . tosql($nomPI, "Text"));
    $invComp		= dlookup("cb_purchaseinv", "invComp", "PINo=" . tosql($nomPI, "Text"));
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
            <td><b>NO BIL</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$no_bill.'</td>
        </tr>
        <tr>
            <td><b>TARIKH BIL</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_bill.'</td>
        </tr>
        <tr><td colspan="8"><br></td></tr>
        ';
        if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
        print'
        <tr>
            <td><b>NO PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$PINo.'</td>
        </tr>
        <tr>
            <td><b>TARIKH PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikhPI.'</td>
        </tr>
        <tr>
            <td><b>AMAUN PI</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>RM '.$totalPI.'</td>
        </tr>
        <tr><td colspan="8"><br></td></tr>
        ';
        }
        if ($invLhdn){
            print'
            <tr>
                <td><b>NO INVOIS LHDN</b></td>
                <td>&nbsp;:&nbsp;</td>
                <td>'.$invLhdn.'</td>
            </tr> 
            ';
        }
        
        if ($invComp){
            print'
            <tr>
                <td><b>NO INVOIS SYARIKAT</b></td>
                <td>&nbsp;:&nbsp;</td>
                <td>'.$invComp.'</td>
            </tr> 
            ';
        }
    print'
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
    <tr>
        <td nowrap="nowrap"><b>DITERIMA DARIPADA</b></td>
		<td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($namac)).'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>ALAMAT</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.ucwords(strtolower($alamat)).'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>NAMA BATCH</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$namaBatch.'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>BANK</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$bank.'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>KETERANGAN</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$keterangan.'</td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>CARA BAYARAN</b></td>
        <td>&nbsp;:&nbsp;</td>
        <td>'.$cara_byr.'</td>
    </tr>
    ';

    print'
    <tr><td colspan="3">&nbsp;</td></tr>
    </table>

    <!-------Table Kenakan Bayaran------->
    <div class="header-border"></div>
    <table class="table table-striped" style="margin-bottom:2%;">
    <thead>
            <tr>
                <td nowrap="nowrap"><b>BIL</b></td>
                <td nowrap="nowrap"><b>JABATAN</b></td>
                <td nowrap="nowrap"><b>PROJEK</b></td>
                <td nowrap="nowrap"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="right"><b>AMAUN (RM)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
			
		$accNom 	= $rsDetail->fields(deductID);
		$accN 		= $rsDetail->fields(deductID);
		$codeproject 	= $rsDetail->fields(kod_project);
		$codejabatan 	= $rsDetail->fields(kod_jabatan);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$taxing 	= $rsDetail->fields(taxNo);

		$accNombor 	= dlookup("generalacc", "code", "ID=".$accN);
		$accdet 	= dlookup("generalacc", "name", "ID=".$accNom);
		$kodprojek 	= dlookup("generalacc", "name", "ID=".$codeproject);
		$kodjabatan = dlookup("generalacc", "name", "ID=".$codejabatan);
		$cukai  	= dlookup("generalacc", "name", "ID=".$taxing);

		$totPymt = number_format($rsDetail->fields(pymtAmt),2);
		print
			'<tr>
				<td>'.$i.'</td>
				<td>'.$kodjabatan.'</td>
				<td>'.$kodprojek.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="right">'.$totPymt.'</td>
			</tr>';
			$jumlah1 += $rsDetail->fields(pymtAmt);
			$i++;
			$rsDetail->MoveNext();
			}
			if($jumlah1<>0){
			$clsRM->setValue($jumlah1);
			$strTotal1 = strtoupper($clsRM->getValue());
			}
		}
		if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
            $baki = str_replace(",", "", $totalPI) - str_replace(",", "", $jumlah1);
        }

print '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM '.number_format($jumlah1,2).'</b></td>
</tr>';
if (dlookup("billacc", "PINo", "no_bill=".tosql(($no_bill),"Text")) <> "") {
    print'
        <tr>
            <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH BAKI PI</b></td>
            <td nowrap="nowrap" align="right"><b>RM '.number_format($baki,2).'</b></td>
        </tr>
    ';
}
print'
</table>
<tr><td colspan="5">&nbsp;</td></tr>

    <!-----------Jumlah Dalam Perkataan------------->
    <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($strTotal1)).' Ringgit Malaysia Sahaja.</td>
        </tr>

    <!-----------Catatan/Description------------->
        <tr>
            <td nowrap="nowrap"><b>CATATAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($catatan)).'</td>
        </tr>
    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>DIMASUKKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($kerani)).'</td>
        </tr>
        <tr>
            <td><b>DISAHKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($disahkan)).'</td>
        </tr>
    </table>
    </div>
    </body>

</body></html>';
?>