<?php

/*******************************************************************************

 *          Project		: iKOOP.com.my
 *          Filename		: informasi.php	
 *          Date 		: 21/10/24	
 *******************************************************************************/
// include("header.php");
// include("koperasiQry.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}


$sFileName = '?vw=dashboard&mn=' . $mn;
$sFileRef  = '?vw=dashboard&mn=' . $mn;
$title     = "Dashboard Koperasi";

/////////////////////////////////// untuk dapatkan yuran dan syer terkumpul anggota ////////////////////////////////////

function getFees1()
{
    global $conn;

    $getYuranOpen = "SELECT 
        SUM(CASE WHEN t.addminus = '0' THEN -t.pymtAmt ELSE t.pymtAmt END) AS jumlahyuran
        FROM transaction t
        INNER JOIN userdetails u ON t.userID = u.userID
        WHERE u.status IN (1,4)
        AND t.deductID IN (1595,1780,1607)";

    $rsYuranOpen = $conn->Execute($getYuranOpen);

    return ($rsYuranOpen && $rsYuranOpen->fields['jumlahyuran']) ? $rsYuranOpen->fields['jumlahyuran'] : 0;
}

function getSharesterkini1()
{
    global $conn;

    $getOpenTK = "SELECT 
        SUM(CASE WHEN t.addminus = '0' THEN -t.pymtAmt ELSE t.pymtAmt END) AS jumlahsyer
        FROM transaction t
        INNER JOIN userdetails u ON t.userID = u.userID
        WHERE u.status IN (1,4)
        AND t.deductID IN (1596,1780)";

    $rsOpenTK = $conn->Execute($getOpenTK);

    return ($rsOpenTK && $rsOpenTK->fields['jumlahsyer']) ? $rsOpenTK->fields['jumlahsyer'] : 0;
}

$sSQL = "SELECT	CAST( b.userID AS SIGNED INTEGER ) as userID, b.name as name, a.totalFee as jumlah 
		 FROM 	userdetails a, users b
		 WHERE 	a.status in (1,4)
	 	 AND	b.userID = a.userID 
		 ORDER BY userID";

$GetMember = &$conn->Execute($sSQL);

$totalsumFee = getFees1();
$totalsumShare = getSharesterkini1();

$totalsumFeeShare = $totalsumFee + $totalsumShare;

/////////////////////////////////// tamat untuk dapatkan yuran dan syer terkumpul anggota ////////////////////////////////////

/////////////////////////////////// untuk dapatkan status pembiayaan //////////////////////////////////// 

// Fetch loan counts by status
$dalam_Proses = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 0")->fields['count'];
$diluluskan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 3")->fields['count'];

$jumLulus = $conn->Execute("SELECT SUM(loanAmt) AS count FROM loans WHERE STATUS = 3")->fields['count'];

// Calculate total entries dynamically
$totalEntries = $dalam_Proses + $diluluskan;

// Define the entries array
$entries = array(
	'Dalam Proses' => array('amount' => $dalam_Proses, 'count' => $dalam_Proses, 'color' => '#C5E0F9'),
	'Diluluskan'   => array('amount' => $diluluskan, 'count' => $diluluskan, 'color' => '#E0BBE4')
);

///////////////////////////////// tamat untuk dapatkan status pembiayaan ////////////////////////////////////

// // Use DLookup to get counts from the database
// $diluluskan = $conn->Execute("SELECT COUNT(userID) AS count FROM userdetails WHERE STATUS = 1")->fields('count');
// $bersara = $conn->Execute("SELECT COUNT(userID) AS count FROM userdetails WHERE STATUS = 4")->fields('count');
// $berhenti = $conn->Execute("SELECT COUNT(userID) AS count FROM userdetails WHERE STATUS = 3")->fields('count');
// $dalam_proses = $conn->Execute("SELECT COUNT(userID) AS count FROM userdetails WHERE STATUS = 0")->fields('count');

// // Use DLookup for Permohonan Pembiayaan
// $pendingPembiayaan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 0")->fields('count');
// $readyPembiayaan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 1")->fields('count');
// $checkPembiayaan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 2")->fields('count');
// $approvedPembiayaan = $conn->Execute("SELECT COUNT(loanID) AS count FROM loans WHERE STATUS = 3")->fields('count');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <h3 class="text-center mt-4">Dashboard Koperasi</h3>
    <div class="container mt-4">
        <div class="row g-4">
            <!-- JUMLAH TERKUMPUL YURAN & SYER -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>JUMLAH TERKUMPUL YURAN & SYER</h6>
                    <h3 class="text-info">RM <?echo number_format($totalsumFeeShare, 2)?></h3>
                    <canvas id="yuran_syerChart"></canvas>
                </div>
            </div>

            <!-- STATUS PEMBIAYAAN -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>STATUS PEMBIAYAAN</h6>
                    <h3><?php echo $totalEntries ?></h3>
                    <canvas id="pembiayaanChart"></canvas>
                </div>
            </div>

            <!-- Invoices -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>JUMLAH PEMBIAYAAN DILULUSKAN</h6>
                    <h3 class="text-info">RM <?echo number_format($jumLulus, 2)?></h3>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 100%"><?php echo $diluluskan ?></div>
                    </div>
                    <p class="mt-2">Sebanyak <?php echo $diluluskan ?> data pembiayaan telah direkodkan dengan jumlah keseluruhan RM <?echo number_format($jumLulus, 2)?>.</p>
                </div>
            </div>

            <!-- Quotations -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>QUOTATIONS</h6>
                    <p>2 Quotations (Last 365 days)</p>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: 50%">1 Pending</div>
                        <div class="progress-bar bg-secondary" style="width: 50%">1 Closed</div>
                    </div>
                    <p class="mt-2">RM 7,888 Total Deal</p>
                </div>
            </div>
        

        <!-- <div class="row g-4 mt-2"> -->
            <!-- Profit and Loss -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>PROFIT AND LOSS</h6>
                    <h3>RM 7,838</h3>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 99%">Income</div>
                        <div class="progress-bar bg-warning" style="width: 1%">Expenses</div>
                    </div>
                </div>
            </div>
            
            <!-- Bank Accounts -->
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <h6>BANK ACCOUNTS</h6>
                    <ul>
                        <li>CASH AT BANK: RM 1,300.00</li>
                        <li>CASH IN HAND: -RM 7,000.00</li>
                        <li>MAYBANK: -RM 450.00</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- </div> -->

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        const ctx1 = document.getElementById('yuran_syerChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Yuran', 'Syer'],
                datasets: [{
                    label: 'Jumlah Terkumpul (RP)',
                    data: [<?php echo $totalsumFee; ?>, <?php echo $totalsumShare; ?>],
                    backgroundColor: ['#F7A8B8', '#F7A8B8']
                }]
            },
            options: {
                indexAxis: 'y', // horizontal bar chart
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

            const ctx2 = document.getElementById('pembiayaanChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Dalam Proses','Diluluskan'],
                    datasets: [{
					data: [<?php echo $dalam_Proses; ?>, <?php echo $diluluskan; ?>],
					backgroundColor: ['#C5E0F9', '#E0BBE4']
				}]
                },
            });
        </script>
</body>

</html>


<?php
// Include footer or any additional code here if necessary
include("footer.php");
?>