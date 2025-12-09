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
$title = "Laporan Transaksi Simpanan";

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
</div>';

?>

    <head>
        <title>Laporan Transaksi Simpanan</title>
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

            .summary-box.setor .amount {
                color: #28a745;
            }

            .summary-box.tarik .amount {
                color: #dc3545;
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
                <label>Tanggal Dari</label>
                <input type="date" name="filter_dari" id="filter_dari" class="form-control"
                       value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="filter-item">
                <label>Tanggal Sampai</label>
                <input type="date" name="filter_sampai" id="filter_sampai" class="form-control"
                       value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="filter-item">
                <label>Jenis Transaksi</label>
                <select name="filter_jenis" id="filter_jenis" class="form-control">
                    <option value="ALL">Semua</option>
                    <option value="SETOR">Setor</option>
                    <option value="TARIK">Tarik</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Status</label>
                <select name="filter_status" id="filter_status" class="form-control">
                    <option value="ALL">Semua Status</option>
                    <option value="1" selected>Berhasil</option>
                    <option value="0">Batal</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Cari</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Nama/Rekening/No Jurnal">
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
        // Ambil parameter filter
        $tanggal_dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
        $tanggal_sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');

        // Total Transaksi Setor
        $sSQL_setor = "SELECT COUNT(*) as total, SUM(Nominal) as total_nominal
                       FROM transactionsimpanan
                       WHERE JenisTransaksi = 'SETOR' AND Status = 1
                       AND DATE(TanggalTransaksi) BETWEEN " . tosql($tanggal_dari, "Text") . "
                       AND " . tosql($tanggal_sampai, "Text");
        $rs_setor = &$conn->Execute($sSQL_setor);
        $total_setor = $rs_setor->fields['total'] ? $rs_setor->fields['total'] : 0;
        $nominal_setor = $rs_setor->fields['total_nominal'] ? $rs_setor->fields['total_nominal'] : 0;

        // Total Transaksi Tarik
        $sSQL_tarik = "SELECT COUNT(*) as total, SUM(Nominal) as total_nominal
                       FROM transactionsimpanan
                       WHERE JenisTransaksi = 'TARIK' AND Status = 1
                       AND DATE(TanggalTransaksi) BETWEEN " . tosql($tanggal_dari, "Text") . "
                       AND " . tosql($tanggal_sampai, "Text");
        $rs_tarik = &$conn->Execute($sSQL_tarik);
        $total_tarik = $rs_tarik->fields['total'] ? $rs_tarik->fields['total'] : 0;
        $nominal_tarik = $rs_tarik->fields['total_nominal'] ? $rs_tarik->fields['total_nominal'] : 0;

        // Total Semua Transaksi
        $total_transaksi = $total_setor + $total_tarik;

        // Selisih (Setor - Tarik)
        $selisih = $nominal_setor - $nominal_tarik;
        ?>

        <div class="summary-box setor">
            <strong>Total Transaksi Setor</strong>
            <div class="amount"><?php echo number_format($total_setor, 0); ?> Trx</div>
            <small>Rp <?php echo number_format($nominal_setor, 0, ',', '.'); ?></small>
        </div>
        <div class="summary-box tarik">
            <strong>Total Transaksi Tarik</strong>
            <div class="amount"><?php echo number_format($total_tarik, 0); ?> Trx</div>
            <small>Rp <?php echo number_format($nominal_tarik, 0, ',', '.'); ?></small>
        </div>
        <div class="summary-box">
            <strong>Total Semua Transaksi</strong>
            <div class="amount"><?php echo number_format($total_transaksi, 0); ?> Trx</div>
        </div>
        <div class="summary-box">
            <strong>Selisih (Setor - Tarik)</strong>
            <div class="amount" style="color: <?php echo $selisih >= 0 ? '#28a745' : '#dc3545'; ?>">
                Rp <?php echo number_format($selisih, 0, ',', '.'); ?>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <table class="table table-striped table-hover table-bordered" id="tableData">
        <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Jurnal</th>
            <th>Teller</th>
            <th>Nama Anggota</th>
            <th>No Rekening</th>
            <th>Jenis Simpanan</th>
            <th>Jenis</th>
            <th>Nominal</th>
            <th>Saldo Sebelum</th>
            <th>Saldo Sesudah</th>
            <th>Keterangan</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sSQL = "
        SELECT
            ID,
            TellerID,
            TellerName,
            UserID,
            NamaAnggota,
            AccountNumber,
            NamaAkun,
            TanggalTransaksi,
            JenisTransaksi,
            Nominal,
            SaldoSebelum,
            SaldoSesudah,
            NoJurnal,
            Keterangan,
            Status,
            CreatedDate
        FROM transactionsimpanan
        ORDER BY TanggalTransaksi DESC, ID DESC
        ";

        $result = &$conn->Execute($sSQL);

        if ($result && !$result->EOF) {
            $no = 1;
            while (!$result->EOF) {
                $status = ($result->fields['Status'] == 1) ? "Berhasil" : "Batal";
                $status_class = ($result->fields['Status'] == 1) ? "badge bg-success" : "badge bg-danger";

                $jenis_class = ($result->fields['JenisTransaksi'] == 'SETOR') ? "badge bg-success" : "badge bg-warning text-dark";

                echo "<tr data-tanggal='" . date('Y-m-d', strtotime($result->fields['TanggalTransaksi'])) . "'
                          data-jenis='" . $result->fields['JenisTransaksi'] . "'
                          data-status='" . $result->fields['Status'] . "'>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . date('d/m/Y H:i', strtotime($result->fields['TanggalTransaksi'])) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['NoJurnal']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['TellerName']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['NamaAnggota']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['AccountNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['NamaAkun']) . "</td>";
                echo "<td><span class='" . $jenis_class . "'>" . $result->fields['JenisTransaksi'] . "</span></td>";
                echo "<td align='right'><strong>Rp " . number_format($result->fields['Nominal'], 0, ',', '.') . "</strong></td>";
                echo "<td align='right'>Rp " . number_format($result->fields['SaldoSebelum'], 0, ',', '.') . "</td>";
                echo "<td align='right'>Rp " . number_format($result->fields['SaldoSesudah'], 0, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['Keterangan']) . "</td>";
                echo "<td><span class='" . $status_class . "'>" . $status . "</span></td>";
                echo "</tr>";

                $no++;
                $result->MoveNext();
            }
        } else {
            echo "<tr><td colspan='13' class='text-center'>Tidak ada data transaksi</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        function applyFilter() {
            const tanggalDari = document.getElementById('filter_dari').value;
            const tanggalSampai = document.getElementById('filter_sampai').value;
            const jenis = document.getElementById('filter_jenis').value;
            const status = document.getElementById('filter_status').value;
            const search = document.getElementById('search').value.toLowerCase();
            const table = document.getElementById('tableData');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');

                if (cells.length > 0) {
                    const tanggalTrx = row.getAttribute('data-tanggal');
                    const jenisTrx = row.getAttribute('data-jenis');
                    const statusTrx = row.getAttribute('data-status');

                    const namaAnggota = cells[4].textContent.toLowerCase();
                    const noRekening = cells[5].textContent.toLowerCase();
                    const noJurnal = cells[2].textContent.toLowerCase();

                    let showRow = true;

                    // Filter tanggal
                    if (tanggalDari && tanggalTrx < tanggalDari) showRow = false;
                    if (tanggalSampai && tanggalTrx > tanggalSampai) showRow = false;

                    // Filter jenis transaksi
                    if (jenis !== 'ALL' && jenisTrx !== jenis) showRow = false;

                    // Filter status
                    if (status !== 'ALL' && statusTrx !== status) showRow = false;

                    // Filter search
                    if (search !== '' && !namaAnggota.includes(search) &&
                        !noRekening.includes(search) && !noJurnal.includes(search)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                }
            }

            updateRowNumbers();
        }

        function resetFilter() {
            document.getElementById('filter_dari').value = '<?php echo date('Y-m-01'); ?>';
            document.getElementById('filter_sampai').value = '<?php echo date('Y-m-d'); ?>';
            document.getElementById('filter_jenis').value = 'ALL';
            document.getElementById('filter_status').value = 'ALL';
            document.getElementById('search').value = '';

            const table = document.getElementById('tableData');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = '';
            }

            updateRowNumbers();
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
