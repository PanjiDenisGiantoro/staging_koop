<?php
/*********************************************************************************
*          Project       :   iKOOP.com.my
*          Filename      :   rptInvestAll.php
*          Date          :   28/02/2024
*********************************************************************************/
session_start();

include ("common.php");
include ("koperasiinfo.php");
include ("koperasiQry.php");
$today = date("F j, Y");                 

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

$sSQL = "";
$sSQL = "SELECT a.*, b.name AS compName 
         FROM investors a 
         JOIN generalacc b ON a.compID = b.ID 
         ORDER BY b.name, a.nameproject";
$GetData = $conn->Execute($sSQL);
$title  = 'Senarai Projek Pelaburan Keseluruhan';

//----------------------------------------------------------------------

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>'.$emaNetis.'</title>
    <LINK rel="stylesheet" href="images/default.css" >       
</head>
<body>';
print '
<form name="MyForm" action='.$PHP_SELF.' method="post">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <td align="right">'.strtoupper($emaNetis).'</td>
    </tr>
    <tr bgcolor="#008080" style="font-family: Arial, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
        <th height="40"><font color="#FFFFFF">'.$title.'
        </th>
    </tr>
    <tr>
        <td><font size=1>Cetak Pada : '.$today.'<br />Oleh : '.get_session('Cookie_fullName').'</font></td>
    </tr>
';

$previousCompany = '';

$totalAmount = 0;
$totalDisburse = 0;
$totalBaki = 0;
$totalOpenBal = 0;

$totalAmountAll = 0;
$totalDisburseAll = 0;
$totalBakiAll = 0;
$totalOpenBalAll = 0;

$count = 0;
$countProject = 0;

if ($GetData->RowCount() <> 0) {    
    while(!$GetData->EOF) {
        $nameproject = $GetData->fields('nameproject');
        $compID = $GetData->fields('compID');

        // Check if company has changed
        if ($compID !== $previousCompany) {
			// Print total amount for previous company if it's not the first iteration
			if ($previousCompany !== '') {
				print '
				<tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="8" align="right"><b>Jumlah:</b></td>
					<td align="right" valign="top"><b>'.number_format($totalAmount,2).'</b></td>
					<td align="right" valign="top"><b>'.number_format($totalDisburse,2).'</b></td>
					<td align="right" valign="top"><b>'.number_format($totalBaki,2).'</b></td>
                    <td align="right" valign="top"><b>'.number_format($totalOpenBal,2).'</b></td>
					<td colspan="3"></td>
				</tr>';

                print '
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
                    <td colspan="5" align="left"><b>Jumlah Projek : '.$countProject.'</b></td>
                </tr>';

                print '<tr><td colspan="15">&nbsp;</td></tr>';
			}

            $totalAmountAll += $totalAmount;
            $totalDisburseAll += $totalDisburse;
            $totalBakiAll += $totalBaki;
            $totalOpenBalAll += $totalOpenBal;

            $totalAmount = 0;
            $totalDisburse = 0;
            $totalBaki = 0;
            $totalOpenBal = 0;

			$countProject = 0;

            print '
            <tr>
                <td colspan="15"><b>NAMA SYARIKAT : '.strtoupper(dlookup("generalacc", "name", "ID=" . tosql($compID, "Text"))).'</b></td>
            </tr>
            <table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
            <tr bgcolor="#C0C0C0" style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
                <th nowrap>&nbsp;</th>
                <th align="left" nowrap width="20%">Nama Projek</th>
                <th align="left" nowrap width="15%">Lokasi</th>
                <th align="left" nowrap width="10%">Keluasan Tanah</th>
                <th nowrap width="5%">Tarikh Lulus</th>  
                <th nowrap width="5%">Tarikh Mula</th>  
                <th nowrap width="5%">Tarikh Akhir</th>
                <th nowrap width="5%">Tempoh Perjanjian (Bulan)</th>
                <th align="right" nowrap width="10%">Nilai Pelaburan (RP)</th>
                <th align="right" nowrap width="10%">Nilai Disbursement (RP)</th>
                <th align="right" nowrap width="10%">Saldo (RP)</th>
                <th align="right" nowrap width="10%">Opening Balance (RP)</th>
                <th align="right" nowrap width="10%">Rate</th>
                <th align="left" nowrap width="5%">PIC</th>
                <th align="left" nowrap width="5%">ALK Selian</th>
                <th align="center" nowrap width="5%">Status</th>
            </tr>';
        }
        
        $formattedEndDate = toDate('d/m/Y', $GetData->fields('endDate'));

        if ($formattedEndDate == '00/00/0000') {
            $status = 'Maklumat Tidak Lengkap';
        } elseif (strtotime($GetData->fields('endDate')) <= strtotime($today)) {
            $status = '<span class="redText">Tamat</span>';
        } else {
            $status = '';
        }

        $count++;
        $location = $GetData->fields('location');
        $area = $GetData->fields('area');
        $lulusDate = ($GetData->fields('lulusDate') && toDate('d/m/Y', $GetData->fields('lulusDate')) != '00/00/0000') ? toDate('d/m/Y', $GetData->fields('lulusDate')) : 'Maklumat Tidak Lengkap';
        $startDate = ($GetData->fields('startDate') && toDate('d/m/Y', $GetData->fields('startDate')) != '00/00/0000') ? toDate('d/m/Y', $GetData->fields('startDate')) : 'Maklumat Tidak Lengkap';
        $endDate = ($GetData->fields('endDate') && toDate('d/m/Y', $GetData->fields('endDate')) != '00/00/0000') ? toDate('d/m/Y', $GetData->fields('endDate')) : 'Maklumat Tidak Lengkap';
        $period = $GetData->fields('period');
        $amount = $GetData->fields('amount');
		$baucer = "SELECT SUM(pymtAmt) AS total_baucer FROM baucerprojekacc WHERE kod_project = '" . $GetData->fields('ID') . "'";
		$GetBaucer = &$conn->Execute($baucer);
		$totalBaucer = $GetBaucer->fields('total_baucer');
		$baki = $amount - $totalBaucer;
		$openbalpro = $GetData->fields('openbalpro');
		$rate = dlookup("investorsrate", "rate", "projectID=" . "'" . $GetData->fields('ID') . "' ORDER BY createdDate ASC LIMIT 1");
		$picharge = $GetData->fields('picharge');
		$alkselia = $GetData->fields('alkselia');

		$totalAmount += $amount;
		$totalDisburse += $totalBaucer;
		$totalBaki += $baki;
        $totalOpenBal += $openbalpro;

        $countProject++;

        print '
        <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
            <td align="center" valign="top" width="2%">'.$countProject.'</td>
            <td align="left" valign="top">'.strtoupper($nameproject).'</td>
            <td align="left" valign="top">'.strtoupper($location).'</td>
            <td align="left" valign="top">'.$area.'</td>
			<td align="center" valign="top">'.$lulusDate.'</td>
            <td align="center" valign="top">'.$startDate.'</td>
            <td align="center" valign="top">'.$endDate.'</td>
            <td align="center" valign="top">'.$period.'</td>
            <td align="right" valign="top">'.number_format($amount,2).'</td>
			<td align="right" valign="top">'.number_format($totalBaucer,2).'</td>
			<td align="right" valign="top">'.number_format($baki,2).'</td>
            <td align="right" valign="top">'.number_format($openbalpro,2).'</td>
			<td align="right" valign="top">'.$rate.'</td>
			<td align="left" valign="top">'.$picharge.'</td>
            <td align="left" valign="top">'.$alkselia.'</td>
            <td align="left" valign="top">'.$status.'</td>
        </tr>';

        // Update previous company ID
        $previousCompany = $compID;

        $GetData->MoveNext();
    }

    $totalAmountAll += $totalAmount;
    $totalDisburseAll += $totalDisburse;
    $totalBakiAll += $totalBaki;
    $totalOpenBalAll += $totalOpenBal;

    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="8" align="right"><b>Jumlah:</b></td>
        <td align="right" valign="top"><b>'.number_format($totalAmount,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalDisburse,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalBaki,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalOpenBal,2).'</b></td>
        <td colspan="3"></td>
    </tr>';

    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="5" align="left"><b>Jumlah Projek : '.$countProject.'</b></td>
    </tr>';

    print '<tr><td colspan="15">&nbsp;</td></tr>';

    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="8" align="right"><b>Jumlah Keseluruhan:</b></td>
        <td align="right" valign="top"><b>'.number_format($totalAmountAll,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalDisburseAll,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalBakiAll,2).'</b></td>
        <td align="right" valign="top"><b>'.number_format($totalOpenBalAll,2).'</b></td>
        <td colspan="3"></td>
    </tr>';

    print '</table>';
} else {
    print '
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
    </tr>';
}

print '</table>
    <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
        <td colspan="5" align="left"><b>Jumlah Projek Keseluruhan: '.$count.'</b></td>
    </tr>
</form>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>'.$retooFetis.'</b></font></td></tr></center>';
?>