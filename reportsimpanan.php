<?php

/*********************************************************************************
 *           Project        :    iKOOP.com.my
 *           Filename        :    reportsimpanan.php
 *           Date            :    06/12/2024
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg)) $pg = 10;
if (!isset($q)) $q = "";
if (!isset($by)) $by = "0";
if (!isset($filter)) $filter = "ALL";
if (!isset($dept)) $dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=reportsimpanan&mn=902';
$title = "Laporan Rekening Simpanan";

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
</div>';

?>

    <head>
        <title>Laporan Simpanan</title>
        <style>
            body {
                font-family: Poppins, sans-serif;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .summary-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .summary-box {
                padding: 20px;
                border-radius: 8px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .summary-box strong {
                display: block;
                margin-bottom: 8px;
                color: #495057;
                font-size: 14px;
            }

            .summary-box .amount {
                font-size: 1.8em;
                font-weight: bold;
                color: #007bff;
            }

            .filter-section {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .filter-row {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
                align-items: end;
            }

            .filter-item {
                flex: 1;
                min-width: 200px;
            }

            .filter-item label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }

            .filter-item input,
            .filter-item select {
                width: 100%;
                padding: 8px;
                border: 1px solid #ced4da;
                border-radius: 4px;
            }

            @media print {
                .filter-section,
                .no-print {
                    display: none;
                }
            }

        </style>
    </head>

    <body>

    <!-- Filter Section -->
    <div class="filter-section no-print">
        <h6 class="mb-3">Filter Laporan</h6>
        <div class="filter-row">
            <div class="filter-item">
                <label>Status</label>
                <select name="filter_status" id="filter_status" class="form-control">
                    <option value="ALL">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Jenis Simpanan</label>
                <select name="filter_jenis" id="filter_jenis" class="form-control">
                    <option value="ALL">Semua Jenis</option>
                    <?php
                    $sSQL_jenis = "SELECT DISTINCT ID, name FROM general WHERE category = 'simpanan' ORDER BY name";
                    $rs_jenis = &$conn->Execute($sSQL_jenis);
                    while (!$rs_jenis->EOF) {
                        echo '<option value="' . $rs_jenis->fields['ID'] . '">' . htmlspecialchars($rs_jenis->fields['name']) . '</option>';
                        $rs_jenis->MoveNext();
                    }
                    ?>
                </select>
            </div>
            <div class="filter-item">
                <label>Cari Nama/IC</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Nama atau No IC">
            </div>
            <div class="filter-item">
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Tampilkan</button>
                <button type="button" class="btn btn-secondary" onclick="resetFilter()">Reset</button>
                <button type="button" class="btn btn-success" onclick="window.print()">Cetak</button>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-container">
        <?php
        // Total Rekening Aktif
        $sSQL_aktif = "SELECT COUNT(*) as total FROM depositoracc WHERE status = 1";
        $rs_aktif = &$conn->Execute($sSQL_aktif);
        $total_aktif = $rs_aktif->fields['total'];

        // Total Rekening Tidak Aktif
        $sSQL_tidak_aktif = "SELECT COUNT(*) as total FROM depositoracc WHERE status = 0";
        $rs_tidak_aktif = &$conn->Execute($sSQL_tidak_aktif);
        $total_tidak_aktif = $rs_tidak_aktif->fields['total'];

        // Total Saldo Keseluruhan
        $sSQL_saldo = "SELECT SUM(balance) as total_saldo FROM depositoracc WHERE status = 1";
        $rs_saldo = &$conn->Execute($sSQL_saldo);
        $total_saldo = $rs_saldo->fields['total_saldo'] ? $rs_saldo->fields['total_saldo'] : 0;

        // Total Nominal Simpanan
        $sSQL_nominal = "SELECT SUM(nominal_simpanan) as total_nominal FROM depositoracc WHERE status = 1";
        $rs_nominal = &$conn->Execute($sSQL_nominal);
        $total_nominal = $rs_nominal->fields['total_nominal'] ? $rs_nominal->fields['total_nominal'] : 0;
        ?>

        <div class="summary-box">
            <strong>Total Rekening Aktif</strong>
            <div class="amount"><?php echo number_format($total_aktif, 0); ?></div>
        </div>
        <div class="summary-box">
            <strong>Total Rekening Tidak Aktif</strong>
            <div class="amount"><?php echo number_format($total_tidak_aktif, 0); ?></div>
        </div>
        <div class="summary-box">
            <strong>Total Saldo Keseluruhan</strong>
            <div class="amount">Rp <?php echo number_format($total_saldo, 2, ',', '.'); ?></div>
        </div>
        <div class="summary-box">
            <strong>Total Nominal Simpanan</strong>
            <div class="amount">Rp <?php echo number_format($total_nominal, 2, ',', '.'); ?></div>
        </div>
    </div>

    <!-- Table Section -->
    <table class="table table-striped table-hover table-bordered" id="tableData">
        <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>No IC</th>
            <th>Nomor Rekening</th>
            <th>Jenis Simpanan</th>
            <th>Nominal Simpanan</th>
            <th>Saldo</th>
            <th>Sumber Dana</th>
            <th>Tanggal Buka</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sSQL = "
SELECT *,
       A.status as status_depositacc,
       A.id as id_unique,
       A.created_at as tanggal_buka,
       c.name as name_user,
       D.name as nama_simpanan
FROM depositoracc A
         JOIN userdetails B
              ON A.UserID COLLATE latin1_general_ci = B.UserID COLLATE latin1_general_ci
         JOIN users C
              ON B.UserID COLLATE latin1_general_ci = C.UserID COLLATE latin1_general_ci
         JOIN general D
              ON CAST(A.Code_simpanan AS CHAR CHARACTER SET latin1) COLLATE latin1_general_ci
                  = CAST(D.ID AS CHAR CHARACTER SET latin1) COLLATE latin1_general_ci
ORDER BY A.created_at DESC";

        $result = &$conn->Execute($sSQL);

        if ($result && !$result->EOF) {
            $no = 1;
            while (!$result->EOF) {
                $status = ($result->fields['status_depositacc'] == 1) ? "Aktif" : "Tidak Aktif";
                $status_class = ($result->fields['status_depositacc'] == 1) ? "badge bg-success" : "badge bg-secondary";

                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['name_user']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['newIC']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['AccountNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['nama_simpanan']) . "</td>";
                echo "<td align='right'>Rp " . number_format($result->fields['nominal_simpanan'], 2, ',', '.') . "</td>";
                echo "<td align='right'>Rp " . number_format($result->fields['balance'], 2, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['sumber_dana']) . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($result->fields['tanggal_buka'])) . "</td>";
                echo "<td><span class='" . $status_class . "'>" . $status . "</span></td>";
                echo "</tr>";

                $no++;
                $result->MoveNext();
            }
        } else {
            echo "<tr><td colspan='10' class='text-center'>Tidak ada data</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        function applyFilter() {
            const status = document.getElementById('filter_status').value;
            const jenis = document.getElementById('filter_jenis').value;
            const search = document.getElementById('search').value.toLowerCase();
            const table = document.getElementById('tableData');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');

                if (cells.length > 0) {
                    const nama = cells[1].textContent.toLowerCase();
                    const ic = cells[2].textContent.toLowerCase();
                    const jenisSimpanan = cells[4].textContent;
                    const statusCell = cells[9].textContent;

                    let showRow = true;

                    // Filter status
                    if (status !== 'ALL') {
                        if (status === '1' && statusCell !== 'Aktif') showRow = false;
                        if (status === '0' && statusCell !== 'Tidak Aktif') showRow = false;
                    }

                    // Filter search
                    if (search !== '' && !nama.includes(search) && !ic.includes(search)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                }
            }
        }

        function resetFilter() {
            document.getElementById('filter_status').value = 'ALL';
            document.getElementById('filter_jenis').value = 'ALL';
            document.getElementById('search').value = '';
            applyFilter();
        }

        // Auto number after filter
        function updateRowNumbers() {
            const table = document.getElementById('tableData');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            let visibleNo = 1;

            for (let i = 0; i < rows.length; i++) {
                if (rows[i].style.display !== 'none') {
                    rows[i].getElementsByTagName('td')[0].textContent = visibleNo;
                    visibleNo++;
                }
            }
        }
    </script>

    </body>
<?php
include("footer.php");
?>
