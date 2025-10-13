<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCquotationdebtorView.php
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
	$sql  = "SELECT a.*, b.* FROM cb_quotation a, generalacc b WHERE a.companyID = b.ID and a.quoteNo = '".$id."'";          
	$rs   = $conn->Execute($sql);
	
	$quoteNo 		= $rs->fields(quoteNo);
    $b_Baddress 	= $rs->fields(b_Baddress);
	$tarikh_quote 	= toDate("d/m/y",$rs->fields(tarikh_quote));
	$name 			= $rs->fields(name);
	$disahkan		= $rs->fields('disahkan');
	$disahkan1		= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
	$sah 			= strtoupper(strip_tags($disahkan1));
	$disedia		= $rs->fields('disedia');
	$disedia1		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
	$sedia 			= strtoupper(strip_tags($disedia1));
	$companyID		= $rs->fields('companyID');
	$departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
	$kod_bank		= $rs->fields('kod_bank');
	$kod_bank1		= dlookup("generalacc", "name", "ID=" . tosql($kod_bank, "Number"));
    $accBank		= dlookup("generalacc", "f_noakaun", "ID=" . tosql($kod_bank, "Number"));
	$catatan        = $rs->fields(description);
	
    if(dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"))) {
        $departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
        $alamat 	= strtoupper(strip_tags($departmentAdd));
    } else {
        $alamat     = "-";
    }
        
    $description	= $rs->fields('description');
	
	$sql2           = "SELECT * FROM cb_purchaseinf WHERE addminus IN (1) AND docNo = ".tosql($quoteNo, "Text")." ORDER BY ID";
	$rsDetail       = $conn->Execute($sql2);

    if ($rs->fields(batchNo)) {
        $namaBatch 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Text"));
    } else {
        $namaBatch 	= "-";
    }

    if ($rs->fields(kod_project))
        $namaProjek 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_project), "Text"));
    else
        $namaProjek     = "-";

    if ($rs->fields(kod_jabatan))
        $namaJabatan 	= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(kod_jabatan), "Text"));
    else
        $namaJabatan    = "-";
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
            margin-top: 5.5%;
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
            margin-top:2%;
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
            <td><b>QUOTATION</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$quoteNo.'</td>
        </tr>
        <tr>
            <td><b>TARIKH</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_quote.'</td>
        </tr>
    </table>
    <!-------Name/Address/No IC/Department Name------->
    <table class="bor-penerima">
        <tr>
            <td nowrap="nowrap"><b>KEPADA</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($name)).'</td>
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
            <td nowrap="nowrap"><b>NAMA PROJEK</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($namaProjek)).'</td>
        </tr>

        <tr>
            <td nowrap="nowrap"><b>NAMA JABATAN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.ucwords(strtolower($namaJabatan)).'</td>
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
                <td nowrap="nowrap" align="left"><b>BIL</b></td>
                <td nowrap="nowrap" align="left"><b>KETERANGAN</b></td>
                <td nowrap="nowrap" align="center"><b>KUANTITI</b></td>
                <td nowrap="nowrap" align="right"><b>HARGA (RP)</b></td>
                <td nowrap="nowrap" align="right"><b>DISKAUN (%)</b></td>
                <td nowrap="nowrap" align="right"><b>CUKAI SST (8%)</b></td>
                <td nowrap="nowrap" align="right"><b>JUMLAH (RP)</b></td>
            </tr>
    </thead>';

		if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
		$deductID   = $rsDetail->fields(deductID);
		$desc_akaun = $rsDetail->fields(desc_akaun);
		$quantity   = $rsDetail->fields(quantity);
		$price      = $rsDetail->fields(price);
		$discount   = $rsDetail->fields(discount);
		$tarikh     = $rsDetail->fields(createdDate);
		$tarikh 	= substr($tarikh,8,2)."/".substr($tarikh,5,2)."/".substr($tarikh,0,4);
		$addminus   = $rsDetail->fields(addminus);
		$totPymt4   = $rsDetail->fields(pymtAmt);
        $tax        = $rsDetail->fields(tax) == 1 ? "Ada" : "Tiada";
		print
			'<tr>
				<td>'.$i.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="center">'.$quantity.'</td>
				<td nowrap="nowrap" align="right">'.$price.'</td>
				<td nowrap="nowrap" align="right">'.$discount.'</td>
				<td nowrap="nowrap" align="center">'.$tax.'</td>
				<td nowrap="nowrap" align="right">'.number_format($totPymt4,2).'</td>
			</tr>';
		
			$jumlah += $totPymt4;
            $i++;
			$rsDetail->MoveNext();
			}
			if($jumlah<>0){
			$clsRM->setValue($jumlah);
			$strTotal = strtoupper($clsRM->getValue());
			}
		}
print '
<tr><td colspan="7">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="6"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RM '.number_format($jumlah,2).'</b></td>
</tr>
</table>
<tr><td colspan="7">&nbsp;</td></tr>

    <!-----------Jumlah Dalam Perkataan------------->
    <table class="stylish-kerani">
        <tr>
            <td nowrap="nowrap"><b>JUMLAH DALAM PERKATAAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($strTotal)).' Ringgit Malaysia Sahaja.</td>
        </tr>

    <!-----------Catatan/Description------------->
        <tr>
            <td nowrap="nowrap"><b>CATATAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($catatan)).'</td>
        </tr>

    <!-----------Kerani/Akaun/Nama Bank------------->
        <tr>
            <td><b>KERANI KEWANGAN</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($sedia)).'</td>
        </tr>
    </table>
    <hr>
    <div>
        Akaun Bank : '.$accBank.'<br>
        Nama Bank : '.$kod_bank1.'
    </div>

    </div>
    </body>
</html>

</body></html>';
?>