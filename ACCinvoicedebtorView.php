<?php
/*********************************************************************************
*			Project		: iKOOP.com.my
*			Filename	: ACCinvoicedebtorView.php
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
    if($note){
        $sql = "SELECT a.*, b.* FROM note a, generalacc b WHERE a.companyID = b.ID and a.noteNo = '".$id."'";          
        $rs = $conn->Execute($sql);
        
        $invNo 			= $rs->fields('noteNo');
        $tarikh_inv 	= toDate("d/m/y",$rs->fields('tarikh_note'));
        $name 			= $rs->fields('name');
        $disahkan		= $rs->fields('disahkan');
        $disahkan1		= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
        $sah 			= strtoupper(strip_tags($disahkan1));
        $disedia		= $rs->fields('disedia');
        $disedia1		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 			= strtoupper(strip_tags($disedia1));
        $companyID		= $rs->fields('companyID');
        $departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
        $alamat 		= strtoupper(strip_tags($departmentAdd));
        $address        = ucwords(strtolower($alamat));
        $catatan	    = $rs->fields('catatan');
        $kod_bank		= $rs->fields('kod_bank');
        $kod_bank1		= dlookup("generalacc", "name", "ID=" . tosql($kod_bank, "Number"));
        $accBank		= dlookup("generalacc", "f_noakaun", "ID=" . tosql($kod_bank, "Number"));
    } else{
        $sql = "SELECT a.*, b.* FROM cb_invoice a, generalacc b WHERE a.companyID = b.ID and a.invNo = '".$id."'";          
        $rs = $conn->Execute($sql);
        
        $invNo 			= $rs->fields('invNo');
        $tarikh_inv 	= toDate("d/m/y",$rs->fields('tarikh_inv'));
        $tarikh_akhir 	= toDate("d/m/y",$rs->fields('tarikh_akhir'));
        $name 			= $rs->fields('name');
        $disahkan		= $rs->fields('disahkan');
        $disahkan1		= dlookup("users", "name", "userID=" . tosql($disahkan, "Text"));
        $sah 			= strtoupper(strip_tags($disahkan1));
        $disedia		= $rs->fields('disedia');
        $disedia1		= dlookup("users", "name", "userID=" . tosql($disedia, "Text"));
        $sedia 			= strtoupper(strip_tags($disedia1));
        $companyID		= $rs->fields('companyID');
        $kod_bank		= $rs->fields('kod_bank');
        $kod_bank1		= dlookup("generalacc", "name", "ID=" . tosql($kod_bank, "Number"));
        $accBank		= dlookup("generalacc", "f_noakaun", "ID=" . tosql($kod_bank, "Number"));
        $catatan        = $rs->fields('description');
        $invLhdn 		= $rs->fields('invLhdn');

        if(dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"))) {
            $departmentAdd	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($companyID, "Number"));
            $alamat 	= strtoupper(strip_tags($departmentAdd));
        } else {
            $alamat 	= "-";
        }
        
        $description	= $rs->fields('description');
    }

    if ($note) {
        $sql2       = "SELECT * FROM transactionacc WHERE addminus IN (0) AND docNo = ".tosql($invNo, "Text")." ORDER BY ID";
        $rsDetail   = $conn->Execute($sql2);
    }
    else {
        $sql2       = "SELECT * FROM transactionacc WHERE addminus IN (1) AND docNo = ".tosql($invNo, "Text")." ORDER BY ID";
        $rsDetail   = $conn->Execute($sql2);
    }

    $namaBatch 		= dlookup("generalacc", "name", "ID=" . tosql($rs->fields('batchNo'), "Text"));

    if ($rs->fields('kod_project'))
        $namaProjek = dlookup("generalacc", "name", "ID=" . tosql($rs->fields('kod_project'), "Text"));
    else
        $namaProjek = "-";

    if ($rs->fields('kod_jabatan'))
        $namaJabatan = dlookup("generalacc", "name", "ID=" . tosql($rs->fields('kod_jabatan'), "Text"));
    else
        $namaJabatan = "-";
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

if($note) $subject = "NO. NOTA KREDIT"; else $subject = "NO. INVOIS";

print'
<body>
<div class="form-container">
    <!---------Doc/Date-------->
    <table class="stylish-date">
        <tr>
            <td><b>'.$subject.'</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$invNo.'</td>
        </tr>
        <tr>
            <td><b>TARIKH</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_inv.'</td>
        </tr>
        ';
if(!$note){
    print'
        <tr>
            <td><b>TARIKH BAYARAN AKHIR</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$tarikh_akhir.'</td>
        </tr>
        ';
}
        if ($invLhdn) {
        print'
        <tr>
            <td><b>NO INVOIS LHDN</b></td>
            <td>&nbsp;:&nbsp;</td>
            <td>'.$invLhdn.'</td>
        </tr>
        ';
        }
        print'
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
                <td nowrap="nowrap" align="right"><b>HARGA SEUNIT (RP)</b></td>
                <td nowrap="nowrap" align="right"><b>JUMLAH (RP)</b></td>
            </tr>
    </thead>';

    if ($rsDetail->RowCount() <> 0){
		$i=1;
		while (!$rsDetail->EOF) {
		$deductID   = $rsDetail->fields('deductID');
		$desc_akaun = $rsDetail->fields('desc_akaun');
		$quantity   = $rsDetail->fields('quantity');
		$price      = $rsDetail->fields('price');
		$tarikh     = $rsDetail->fields('createdDate');
		$tarikh 	= substr($tarikh,8,2)."/".substr($tarikh,5,2)."/".substr($tarikh,0,4);
		$addminus   = $rsDetail->fields('addminus');
		$totPymt4   = $rsDetail->fields('pymtAmt');
		print
			'<tr>
				<td>'.$i.'</td>
                <td style="text-align: justify;">'.$desc_akaun.'</td>
				<td nowrap="nowrap" align="center">'.$quantity.'</td>
				<td nowrap="nowrap" align="right">'.$price.'</td>	
				<td nowrap="nowrap" align="right">'.number_format($totPymt4,2).'</td>
			</tr>';
		
			$jumlah += $totPymt4;
            $i++;
			$rsDetail->MoveNext();
			}
			if($jumlah<>0){
			$clsRP->setValue($jumlah);
			$strTotal = strtoupper($clsRP->getValue());
			}
		}
print '
<tr><td colspan="6">&nbsp;</td></tr>
<tr>				
    <td nowrap="nowrap" align="right" colspan="4"><b>JUMLAH</b></td>
    <td nowrap="nowrap" align="right"><b>RP '.number_format($jumlah,2).'</b></td>
</tr>
</table>
<tr><td colspan="5">&nbsp;</td></tr>

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
            <td><b>DISEDIAKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($sedia)).'</td>
        </tr>
        <tr>
            <td><b>DISAHKAN OLEH</b></td>
            <td>&nbsp;:&nbsp;</b></td>
            <td>'.ucwords(strtolower($sah)).'</td>
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