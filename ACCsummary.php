<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	ACCsummary.php
 *          Date 		: 	04/03/2025
 *********************************************************************************/
session_start();
if (!isset($sourceMain)) {  
    $sourceMain = "default";
}
// echo "Current Source: " . $sourceMain;  
if ($sourceMain === "default") {
    include("common.php");  
}

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}

print'
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 12px;
    		max-width: 100%; /* Ensures it doesnt overflow */
            box-sizing: border-box;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .container {
                flex-direction: row;
                align-items: stretch;
            }

            .chart-container {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .summary {
                flex: 1;
                display: grid;
    			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }
        }

		.chart-container {
			flex: 1;
			width: 100%;
			min-width: 50%; /* Adjust as needed */
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 10px;
		}

		.chart-container canvas {
			width: 100% !important;
			height: 180px !important; /* Matches summary box height */
			max-height: 180px;
		}

        .summary {
            display: grid;
    		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .hidden-box {
            visibility: hidden; /* Hides content but keeps space */
        }

        .summary-box {
            flex: 1; /* Forces equal width */
            padding: 10px;
            border-radius: 8px;
            background: #fafafa;
            min-width: 200px; /* Prevents it from shrinking too much */
        }

        .summary-box strong {
            display: block;
            margin-bottom: 8px;
        }

        .count {
            font-size: 15px;
            margin: 5px 0;
        }

        .amount {
            font-size: 24px;
			font-weight: bold;
			margin: 5px 0;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 10px 0;
        }

        .header {
            display: flex;
            padding: 5px;
            box-sizing: border-box;
            flex-wrap: wrap;
            position: absolute;
        }

        /* .btn { background: #f1f1f1; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; } */
    </style>
';

// PENGHUTANG
if (in_array($sourceMain, array("default", "debtor"))) {

    function getStatus($balance, $tarikh_akhir, $today) {
        if ($tarikh_akhir == null) {
            return 0; // Tidak Sah
        } elseif ($balance == 0) {
            return 1; // Paid
        } elseif ($balance <> 0 && $today > $tarikh_akhir) {
            return 2; // Late
        } elseif ($balance <> 0 && $today < $tarikh_akhir) {
            return 3; // Unpaid
        }
    }

    // Initialize amounts
    $lateAmount     = 0;
    $unpaidAmount   = 0;
    $paidAmount     = 0;
    $invalidAmount  = 0;

    $sSQLsummary 	= "SELECT DISTINCT A.* FROM cb_invoice A 
                        LEFT JOIN transactionacc c ON a.invNo = c.docNo
                        WHERE c.status NOT IN (5)
                        ";
    $GetSummary 	= &$conn->Execute($sSQLsummary);

    if ($GetSummary->RowCount() <> 0) {
        while (!$GetSummary->EOF) {
            $tarikh_akhir = strtotime($GetSummary->fields('tarikh_akhir'));
            $today = time();
            $amaun = $GetSummary->fields('outstandingbalance');

            // Get total payments made for the invoice
            $sqlPayment = "SELECT SUM(outstandingbalance - balance) AS totalPayment 
                        FROM cb_payments 
                        WHERE invNo = '" . $GetSummary->fields('invNo') . "'";
            $rsBayaran 	= $conn->Execute($sqlPayment);
            $bayaran 	= $rsBayaran->fields['totalPayment'];
            $balance 	= $amaun - $bayaran;

            $statusValue = getStatus($balance, $tarikh_akhir, $today);

            // Update status in the database
            $sSQLstatusPay 	= "UPDATE cb_invoice 
                                SET status = $statusValue 
                                WHERE invNo = '" . $GetSummary->fields('invNo') . "'";
            $rsStatusPay 	= $conn->Execute($sSQLstatusPay);

            // Accumulate amounts based on status
            if ($statusValue == 2) { // Late
                $lateAmount += $amaun;
            } elseif ($statusValue == 3) { // Unpaid
                $unpaidAmount += $amaun;
            } elseif ($statusValue == 1) { // Paid
                $paidAmount += $amaun;
            } elseif ($statusValue == 0) { // Tidak Sah
                $invalidAmount += $amaun;
            }

            $GetSummary->MoveNext();
        }
    }

    // Fetch counts by status
    $lateCount    = $conn->Execute("SELECT COUNT(ID) AS count FROM cb_invoice WHERE STATUS = 2")->fields['count'];
    $unpaidCount  = $conn->Execute("SELECT COUNT(ID) AS count FROM cb_invoice WHERE STATUS = 3")->fields['count'];
    $paidCount    = $conn->Execute("SELECT COUNT(ID) AS count FROM cb_invoice WHERE STATUS = 1")->fields['count'];
    $invalidCount = $conn->Execute("SELECT COUNT(ID) AS count FROM cb_invoice WHERE STATUS = 0")->fields['count'];

    // Define the entries array
    $entries = array(
        'Lewat'          => array('amount' => $lateAmount, 'count' => $lateCount, 'color' => '#f44336'),
        'Belum Selesai'  => array('amount' => $unpaidAmount, 'count' => $unpaidCount, 'color' => '#ff9800'),
        'Bayaran Penuh'  => array('amount' => $paidAmount, 'count' => $paidCount, 'color' => '#4caf50'),
        'Tidak Sah'      => array('amount' => $invalidAmount, 'count' => $invalidCount, 'color' => '#808080')
    );

    // Calculate total entries dynamically
    $totalEntries = $paidCount + $lateCount + $unpaidCount + $invalidCount;

    print'
        <div class="container">
        <div class="header">
            <h5>Ringkasan Invois Keseluruhan</h5>
        </div>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>

            <div class="summary">';
            foreach ($entries as $key => $data) {
                print '
                <div class="summary-box">
                    <strong>' . $key . '</strong>
                    <hr>
                    <div class="count" style="color: ' . $data['color'] . ';">' . $data['count'] . ' entri (' . ($totalEntries > 0 ? round(($data['count'] / $totalEntries) * 100) : 0) . '%)</div>
                    <div class="amount" style="color: ' . $data['color'] . ';">
                        <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
                    </div>
                </div>';
            }

    print '
            </div>
        </div>
        ';



    print '
    <script language="JavaScript">

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("paymentChart").getContext("2d"); 
        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: ["Lewat", "Belum Selesai", "Bayaran Penuh", "Tidak Sah"],
                datasets: [{
                    data: [' . intval($lateCount) . ', ' . intval($unpaidCount) . ', ' . intval($paidCount) . ', ' . intval($invalidCount) . '],
                    backgroundColor: ["#f44336", "#ff9800", "#4caf50", "#808080"]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, 
                plugins: {
                    legend: {
                        position: "right"
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const labels = ["Lewat", "Belum Selesai", "Bayaran Penuh", "Tidak Sah"];
                                const counts = [' . intval($lateCount) . ', ' . intval($unpaidCount) . ', ' . intval($paidCount) . ', ' . intval($invalidCount) . '];
                                const amounts = [' . intval($lateAmount) . ', ' . intval($unpaidAmount) . ', ' . intval($paidAmount) . ', ' . intval($invalidAmount) . '];
                                const index = tooltipItem.dataIndex;
                                return labels[index] + ": RM" + amounts[index] + " (" + counts[index] + " invois)";
                            }
                        }
                    }
                }
            }
        });
    });

    </script>';

}


if (in_array($sourceSub, array("default", "accinvoicelist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total invoice based on year selected
$sqljumlahInv = "SELECT SUM(outstandingbalance) AS totalInvoice,
GROUP_CONCAT(DISTINCT a.invNo) AS docNoInv 
FROM cb_invoice a
WHERE
a.invNo IN (
   SELECT DISTINCT c.docNo 
   FROM transactionacc c 
   WHERE c.status NOT IN (5)
)	
AND YEAR(a.tarikh_inv) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
$sqljumlahInv .= " AND MONTH(tarikh_inv) = $mm";
$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);
$totalInvoice = $rsjumlahInv->fields['totalInvoice'];
$docNoInv = $rsjumlahInv->fields['docNoInv'];

// handle case where no invNo is found
if (empty($docNoInv)) {
$docNoInv = 'NULL'; // ainvalids SQL error in IN clause
} else {
$docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}

// query total payment
$sqljumlahPayment = "SELECT SUM(outstandingbalance - balance) AS totalPayment 
    FROM cb_payments 
    WHERE invNo IN ($docNoInv)";

$rsjumlahPayment = $conn->Execute($sqljumlahPayment);
$totalPayment = $rsjumlahPayment->fields['totalPayment'];

//sum of invoice
$totalInvoice = $rsjumlahInv->fields['totalInvoice'];

//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    'Jumlah Invois'   => array('amount' => $totalInvoice, 'color' => '#f44336'),
    'Jumlah Bayaran'  => array('amount' => $totalPayment, 'color' => '#ff9800')
);

print'
<div class="summary">
';

foreach ($overallSummary as $key => $data) {
    print '
    <div class="summary-box">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

print'
        </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "accdebtorlist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total payment
$sqljumlahPayment = "SELECT SUM(outstandingbalance - balance) AS totalPayment,
					 GROUP_CONCAT(invNo) AS docNoInv 
                     FROM cb_payments 
					 WHERE YEAR(tarikh_RV) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahPayment .= " AND MONTH(tarikh_RV) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahPayment = $conn->Execute($sqljumlahPayment);
$totalPayment = $rsjumlahPayment->fields['totalPayment'];
$docNoInv = $rsjumlahPayment->fields['docNoInv'];

// handle case where no invNo is found
if (empty($docNoInv)) {
	$docNoInv = 'NULL'; // avoids SQL error in IN clause
} else {
	$docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}
// Query total invoice from normal invoice and also company's opening balance
$sqljumlahInv = "SELECT SUM(outstandingbalance) AS totalInvoice
                 FROM cb_invoice
                 WHERE invNo IN ($docNoInv) 
                 UNION ALL
                 SELECT SUM(outstandingbalance) AS totalInvoice
                 FROM cb_payments
                 WHERE invNo = ''
				 AND YEAR(tarikh_RV) = $yy
				 AND (companyID, id) IN ( SELECT companyID, MIN(ID) 
				 FROM cb_payments 
				 WHERE invNo = '' 
				 AND YEAR(tarikh_RV) = $yy GROUP BY companyID )";
// Concatenate query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahInv .= " AND MONTH(tarikh_RV) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);

// Initialize totalInvoice fields
$totalInvoice1 = 0;
$totalInvoice2 = 0;

// Process the result set
if ($rsjumlahInv && !$rsjumlahInv->EOF) {
	$totalInvoice1 = isset($rsjumlahInv->fields['totalInvoice']) ? $rsjumlahInv->fields['totalInvoice'] : 0;
	$rsjumlahInv->MoveNext();
	$totalInvoice2 = isset($rsjumlahInv->fields['totalInvoice']) ? $rsjumlahInv->fields['totalInvoice'] : 0;
}

// Sum both totalInvoice values
$totalInvoice = $totalInvoice1 + $totalInvoice2;

//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    'Jumlah Invois'   => array('amount' => $totalInvoice, 'color' => '#f44336'),
    'Jumlah Bayaran'  => array('amount' => $totalPayment, 'color' => '#ff9800')
);

print'
<div class="summary">
';

foreach ($overallSummary as $key => $data) {
    print '
    <div class="summary-box">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

// print'
//         </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "accpurchaseinvoicelist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total invoice based on year selected
$sqljumlahInv = "SELECT SUM(outstandingbalance - balance) AS totalInvoice,
				 GROUP_CONCAT(DISTINCT a.PINo) AS docNoInv 
                 FROM cb_purchaseinv a
				 WHERE
				 a.PINo IN (
					SELECT DISTINCT c.docNo 
					FROM transactionacc c 
					WHERE c.status NOT IN (6)
				 )	
                 AND YEAR(a.tarikh_PI) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahInv .= " AND MONTH(tarikh_PI) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);

$totalInvoice = $rsjumlahInv->fields['totalInvoice'];
$docNoInv = $rsjumlahInv->fields['docNoInv'];

// handle case where no PINo is found
if (empty($docNoInv)) {
	$docNoInv = 'NULL'; // avoids SQL error in IN clause
} else {
	$docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}

// query total payment
$sqljumlahPayment = "SELECT SUM(pymtAmt - balance) AS totalPayment 
                     FROM billacc 
                     WHERE PINo IN ($docNoInv)";

$rsjumlahPayment = $conn->Execute($sqljumlahPayment);
$totalPayment = $rsjumlahPayment->fields['totalPayment'];

//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    'Jumlah Invois'   => array('amount' => $totalInvoice, 'color' => '#f44336'),
    'Jumlah Bayaran'  => array('amount' => $totalPayment, 'color' => '#ff9800')
);

print'
<div class="summary">
';

foreach ($overallSummary as $key => $data) {
    print '
    <div class="summary-box">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

// print'
//         </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "accbilllist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total payment
$sqljumlahPayment = "SELECT SUM(pymtAmt - balance) AS totalPayment,
					 GROUP_CONCAT(PINo) AS docNoInv 
                     FROM billacc 
					 WHERE YEAR(tarikh_bill) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahPayment .= " AND MONTH(tarikh_bill) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahPayment = $conn->Execute($sqljumlahPayment);
$totalPayment = $rsjumlahPayment->fields['totalPayment'];
$docNoInv = $rsjumlahPayment->fields['docNoInv'];

// handle case where no invNo is found
if (empty($docNoInv)) {
	$docNoInv = 'NULL'; // avoids SQL error in IN clause
} else {
	$docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}
// Query total invoice from normal invoice and also company's opening balance
$sqljumlahInv = "SELECT SUM(outstandingbalance - balance) AS totalInvoice
                 FROM cb_purchaseinv
                 WHERE PINo IN ($docNoInv) 
				 AND PINo != ''
                 UNION ALL
                 SELECT SUM(pymtAmt) AS totalInvoice
                 FROM billacc
                 WHERE PINo = ''
				 AND YEAR(tarikh_bill) = $yy
				 AND (diterima_drpd, id) IN ( SELECT diterima_drpd, MIN(ID) 
				 FROM billacc 
				 WHERE PINo = '' 
				 AND YEAR(tarikh_bill) = $yy GROUP BY diterima_drpd )";
// Concatenate query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahInv .= " AND MONTH(tarikh_bill) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);

// Initialize totalInvoice fields
$totalInvoice1 = 0;
$totalInvoice2 = 0;

// Process the result set
if ($rsjumlahInv && !$rsjumlahInv->EOF) {
	$totalInvoice1 = isset($rsjumlahInv->fields['totalInvoice']) ? $rsjumlahInv->fields['totalInvoice'] : 0;
	$rsjumlahInv->MoveNext();
	$totalInvoice2 = isset($rsjumlahInv->fields['totalInvoice']) ? $rsjumlahInv->fields['totalInvoice'] : 0;
}

// Sum both totalInvoice value
$totalInvoice = $totalInvoice1 + $totalInvoice2;

//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    'Jumlah Invois'   => array('amount' => $totalInvoice, 'color' => '#f44336'),
    'Jumlah Bayaran'  => array('amount' => $totalPayment, 'color' => '#ff9800')
);

print'
<div class="summary">
';

foreach ($overallSummary as $key => $data) {
    print '
    <div class="summary-box">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

// print'
//         </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "accdebitnotelist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total debit note based on year selected
$sqljumlahInv = "SELECT SUM(pymtAmt) AS totalInvoice,
				 GROUP_CONCAT(DISTINCT a.noteNo) AS docNoInv 
                 FROM note a
				 WHERE
				 a.noteNo IN (
					SELECT DISTINCT c.docNo 
					FROM transactionacc c 
					WHERE c.status IN (6) and c.addminus IN (0)
				 )	
                 AND YEAR(a.tarikh_note) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahInv .= " AND MONTH(tarikh_note) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);
$totalInvoice = $rsjumlahInv->fields['totalInvoice'];

//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    '',
    'Jumlah'   => array('amount' => $totalInvoice, 'color' => '#f44336')
);

print'
<div class="summary">
';

$first = true; // Flag to track the first iteration

foreach ($overallSummary as $key => $data) {
    // Apply different class only to the first item
    $class = $first ? 'summary-box hidden-box' : 'summary-box';
    $first = false; // Mark that the first iteration is done

    print '
    <div class="' . $class . '">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

print'
        </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "acccreditnotelist"))) {
    //--------------------------------------------------Display total invoice and payment depends on year and month---------START

//-------------Calculation total invoice and payment depends on year and month---------START

// query total credit note
$sqljumlahInv = "SELECT SUM(a.pymtAmt) AS totalInvoice,
				 GROUP_CONCAT(a.noteNo) AS docNoInv 
                 FROM note a
				 LEFT JOIN transactionacc c ON a.noteNo = c.docNo
				 WHERE c.status IN (5) AND c.addminus IN (1)
                 AND YEAR(a.tarikh_note) = $yy";

// concat query if any month is selected
if ($mm !== "ALL") {
	$sqljumlahInv .= " AND MONTH(a.tarikh_note) = $mm";
	$stringDesc = "Bulan $mm ";
}

$rsjumlahInv = $conn->Execute($sqljumlahInv);
$totalInvoice = $rsjumlahInv->fields['totalInvoice'];
$docNoInv = $rsjumlahInv->fields['docNoInv'];

// handle case where no noteNo is found
if (empty($docNoInv)) {
	$docNoInv = 'NULL'; // avoids SQL error in IN clause
} else {
	$docNoInv = "'" . str_replace(',', "','", $docNoInv) . "'";
}
//-------------Calculation total invoice and payment depends on year and month---------END

        print'
        <div class="container">
        <div class="header">
            <h5>Jumlah Pada ' . $stringDesc . ' Tahun ' . $yy . '</h5>
        </div>
            <div class="chart-container">
    Bulan  
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
while (!$rsYears->EOF) {
	$year = $rsYears->fields['year'];
	print '	<option value="' . $year . '"';
	if ($yy == $year) print 'selected';
	print '>' . $year;
	$rsYears->MoveNext();
}
print '		</select>
			<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div>  
';

$overallSummary = array(
    '',
    'Jumlah'   => array('amount' => $totalInvoice, 'color' => '#f44336')
);

print'
<div class="summary">
';

$first = true; // Flag to track the first iteration

foreach ($overallSummary as $key => $data) {
    // Apply different class only to the first item
    $class = $first ? 'summary-box hidden-box' : 'summary-box';
    $first = false; // Mark that the first iteration is done

    print '
    <div class="' . $class . '">
        <strong>' . $key . '</strong>
        <hr>
        <div class="amount" style="color: ' . $data['color'] . ';">
            <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
        </div>
    </div>
</div>
';
}

print'
        </div>'; // end of second container

//--------------------------------------------------Display total invoice and payment depends on year and month---------END
}

elseif (in_array($sourceSub, array("default", "accsingleentrylist"))) {
    // Initialize amounts
    $balancedAmount     = 0;
    $unbalancedAmount   = 0;
    $balanceAmount     = 0;
    $invalidAmount  = 0;

    $sSQLsummary 	= "SELECT a.* FROM singleentry a
                        ";
    $GetSummary 	= &$conn->Execute($sSQLsummary);

    if ($GetSummary->RowCount() <> 0) {
        while (!$GetSummary->EOF) {
			$sqlPayment 	= 	"SELECT SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END) AS totDb,
			SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END) AS totKr 
			FROM transactionacc 
			WHERE docNo 	= '" . $GetSummary->fields(SENO) . "'";
			$rsBayaran 		= $conn->Execute($sqlPayment);
			$db 			= $rsBayaran->fields['totDb'];
			$kr 			= $rsBayaran->fields['totKr'];

            $sqlBalanced = "( SELECT COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.SENO) = 0 ";
            $sqlUnbalanced = "( SELECT COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0) FROM transactionacc WHERE docNo = a.SENO) != 0 ";
            // $GetVouchers = &$conn->Execute($sqlBalanced);
            // $GetVouchers = &$conn->Execute($sqlUnbalanced);
			// $isBalanced = $rsBayaran->fields['sqlBalanced'];
			// $isUnbalanced = $rsBayaran->fields['sqlUnbalanced'];
        }
    }

    // Fetch counts by balanced or unbalanced amounts
    $sqlBalanced = "
    SELECT COUNT(*) AS count
    FROM singleentry a
    WHERE (
        SELECT COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0)
             - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0)
        FROM transactionacc
        WHERE docNo = a.SENO
    ) = 0
";

$sqlUnbalanced = "
    SELECT COUNT(*) AS count
    FROM singleentry a
    WHERE (
        SELECT COALESCE(SUM(CASE WHEN addminus = 0 THEN pymtAmt ELSE 0 END), 0)
             - COALESCE(SUM(CASE WHEN addminus = 1 THEN pymtAmt ELSE 0 END), 0)
        FROM transactionacc
        WHERE docNo = a.SENO
    ) != 0
";

$balancedCount   = $conn->Execute($sqlBalanced)->fields['count'];
$unbalancedCount = $conn->Execute($sqlUnbalanced)->fields['count'];

    // $balanceCount    = $conn->Execute("SELECT COUNT(ID) AS count FROM singleentry WHERE STATUS = 1")->fields['count'];
    // $invalidCount = $conn->Execute("SELECT COUNT(ID) AS count FROM singleentry WHERE STATUS = 0")->fields['count'];

    // Define the entries array
    $entries = array(
        'Seimbang'          => array('amount' => $balancedAmount, 'count' => $balancedCount, 'color' => '#f44336'),
        'Tidak Seimbang'  => array('amount' => $unbalancedAmount, 'count' => $unbalancedCount, 'color' => '#ff9800'),
        // 'Bayaran Penuh'  => array('amount' => $balanceAmount, 'count' => $balanceCount, 'color' => '#4caf50'),
        // 'Tidak Sah'      => array('amount' => $invalidAmount, 'count' => $invalidCount, 'color' => '#808080')
    );

    // Calculate total entries dynamically
    // $totalEntries = $balancedCount + $unbalancedCount + $balanceCount + $invalidCount;
    $totalEntries = $balancedCount + $unbalancedCount;


    print'
        <div class="container">
        <div class="header">
            <h5>Ringkasan Invois Keseluruhan</h5>
        </div>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>

            <div class="summary">';
            foreach ($entries as $key => $data) {
                print '
                <div class="summary-box">
                    <strong>' . $key . '</strong>
                    <hr>
                    <div class="count" style="color: ' . $data['color'] . ';">' . $data['count'] . ' entri (' . ($totalEntries > 0 ? round(($data['count'] / $totalEntries) * 100) : 0) . '%)</div>
                    <div class="amount" style="color: ' . $data['color'] . ';">
                        <span style="font-size: 0.8em; font-weight: normal;">RM </span>' . number_format($data['amount'], 2) . '
                    </div>
                </div>';
            }

    print '
            </div>
        </div>
        ';



    print '
    <script language="JavaScript">

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("paymentChart").getContext("2d"); 
        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: ["Lewat", "Belum Selesai", "Bayaran Penuh", "Tidak Sah"],
                datasets: [{
                    data: [' . intval($lateCount) . ', ' . intval($unpaidCount) . ', ' . intval($paidCount) . ', ' . intval($invalidCount) . '],
                    backgroundColor: ["#f44336", "#ff9800", "#4caf50", "#808080"]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, 
                plugins: {
                    legend: {
                        position: "right"
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const labels = ["Lewat", "Belum Selesai", "Bayaran Penuh", "Tidak Sah"];
                                const counts = [' . intval($lateCount) . ', ' . intval($unpaidCount) . ', ' . intval($paidCount) . ', ' . intval($invalidCount) . '];
                                const amounts = [' . intval($lateAmount) . ', ' . intval($unpaidAmount) . ', ' . intval($paidAmount) . ', ' . intval($invalidAmount) . '];
                                const index = tooltipItem.dataIndex;
                                return labels[index] + ": RM" + amounts[index] + " (" + counts[index] + " invois)";
                            }
                        }
                    }
                }
            }
        });
    });

    </script>';

}

?>