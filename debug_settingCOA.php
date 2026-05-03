<?php
/*
 * debug_settingCOA.php
 * Akses: http://yourdomain/debug_settingCOA.php
 * HAPUS file ini setelah selesai debug!
 */
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2');
if (!$isAdmin) { die("Akses ditolak."); }

$logFile = dirname(__FILE__) . '/logs/settingCOA_debug.log';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Debug Setting COA</title>
<style>
body { font-family: monospace; font-size: 12px; padding: 16px; background: #1e1e1e; color: #d4d4d4; }
h2 { color: #4ec9b0; }
h3 { color: #dcdcaa; margin-top: 24px; }
table { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
th { background: #264f78; color: #fff; padding: 4px 8px; text-align: left; }
td { padding: 4px 8px; border-bottom: 1px solid #333; }
td.empty { color: #f48771; font-style: italic; }
td.filled { color: #4ec9b0; }
pre { background: #111; padding: 12px; border-radius: 4px; white-space: pre-wrap; max-height: 400px; overflow-y: auto; color: #9cdcfe; }
.badge-ok { background: #4ec9b0; color: #000; padding: 1px 6px; border-radius: 3px; }
.badge-err { background: #f48771; color: #000; padding: 1px 6px; border-radius: 3px; }
a { color: #569cd6; }
</style>
</head>
<body>
<h2>Debug Setting COA</h2>
<p style="color:#f48771"><b>PENTING:</b> Hapus file ini setelah selesai debug!</p>

<!-- ==================== CEK TABEL setting_coa ==================== -->
<h3>1. Isi Tabel setting_coa</h3>
<?php
$rs = $conn->Execute("SELECT * FROM setting_coa ORDER BY modul, settingID");
if (!$rs) {
    echo '<p class="badge-err">ERROR: Tabel setting_coa tidak ditemukan atau query gagal.</p>';
} elseif ($rs->EOF) {
    echo '<p class="badge-err">Tabel ada tapi KOSONG. Jalankan INSERT dari setup_tables.sql.</p>';
} else {
    echo '<table>';
    echo '<tr><th>settingID</th><th>modul</th><th>kode_setting</th><th>label</th><th>ledger_code</th><th>ledger_name</th><th>updatedBy</th></tr>';
    while (!$rs->EOF) {
        $code = $rs->fields('ledger_code');
        $name = $rs->fields('ledger_name');
        $codeClass = $code ? 'filled' : 'empty';
        echo '<tr>';
        echo '<td>' . $rs->fields('settingID') . '</td>';
        echo '<td>' . $rs->fields('modul') . '</td>';
        echo '<td>' . $rs->fields('kode_setting') . '</td>';
        echo '<td>' . $rs->fields('label') . '</td>';
        echo '<td class="' . $codeClass . '">' . ($code ? htmlspecialchars($code) : '(kosong)') . '</td>';
        echo '<td class="' . $codeClass . '">' . ($name ? htmlspecialchars($name) : '(kosong)') . '</td>';
        echo '<td>' . $rs->fields('updatedBy') . '</td>';
        echo '</tr>';
        $rs->MoveNext();
    }
    echo '</table>';
}
?>

<!-- ==================== CEK listledger.php ==================== -->
<h3>2. Cek listledger.php</h3>
<?php
$lPath = dirname(__FILE__) . '/listledger.php';
if (!file_exists($lPath)) {
    echo '<p><span class="badge-err">TIDAK ADA</span> File listledger.php tidak ditemukan di server.</p>';
} else {
    $content = file_get_contents($lPath);
    if (strpos($content, 'targetCode') !== false) {
        echo '<p><span class="badge-ok">OK</span> listledger.php versi baru (support targetCode/targetName).</p>';
    } else {
        echo '<p><span class="badge-err">LAMA</span> listledger.php masih versi lama (tidak ada targetCode). Harus diupload ulang.</p>';
    }
}
?>
<p><a href="listledger.php?targetCode=lc_test&targetName=ln_test" target="_blank">Buka listledger.php (popup test)</a></p>

<!-- ==================== CEK LOG FILE ==================== -->
<h3>3. Log Simpan Terakhir</h3>
<?php
if (!file_exists($logFile)) {
    echo '<p>Belum ada log. Coba tekan tombol <b>Simpan</b> di halaman Setting COA, lalu refresh halaman ini.</p>';
} else {
    $logContent = file_get_contents($logFile);
    $lines = array_filter(explode("\n", trim($logContent)));
    $recent = array_slice($lines, -100);
    echo '<pre>' . htmlspecialchars(implode("\n", $recent)) . '</pre>';
    echo '<p><a href="?clearlog=1">Hapus log</a></p>';
}
if (isset($_GET['clearlog'])) {
    @unlink($logFile);
    echo '<script>window.location=window.location.href.split("?")[0];</script>';
}
?>

<!-- ==================== CEK PHP INFO ==================== -->
<h3>4. Info PHP</h3>
<?php
echo '<table>';
echo '<tr><th>Variabel</th><th>Nilai</th></tr>';
echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
echo '<tr><td>short_open_tag</td><td>' . ini_get('short_open_tag') . '</td></tr>';
echo '<tr><td>logs/ writable</td><td>';
$logsDir = dirname(__FILE__) . '/logs';
if (!is_dir($logsDir)) {
    echo '<span class="badge-err">Direktori /logs belum ada</span>';
} elseif (is_writable($logsDir)) {
    echo '<span class="badge-ok">Writable</span>';
} else {
    echo '<span class="badge-err">TIDAK writable — log tidak bisa ditulis</span>';
}
echo '</td></tr>';
echo '</table>';
?>

</body>
</html>
