<?php
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");window.close();</script>';
    exit;
}

$pk = isset($_GET['pk']) ? $_GET['pk'] : (isset($_POST['pk']) ? $_POST['pk'] : '');

$userID = '';
$currentStatus = '';
$accountNumber = '';
$balance = 0;
$namaSimpanan = '';
$namaAnggota = '';
$newIC = '';
$picuser = '';

if ($pk !== '') {
    $sqlInfo = "SELECT d.*, u.name as NamaAnggota, b.newIC, b.picture, g.name as NamaSimpanan
                FROM depositoracc d
                LEFT JOIN users u ON CONVERT(d.UserID USING utf8) = CONVERT(u.UserID USING utf8)
                LEFT JOIN userdetails b ON CONVERT(u.userID USING utf8) = CONVERT(b.userID USING utf8)
                LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
                WHERE d.id = " . tosql($pk, "Number");
    $rsInfo = $conn->Execute($sqlInfo);
    if ($rsInfo && !$rsInfo->EOF) {
        $userID = $rsInfo->fields['UserID'];
        $currentStatus = $rsInfo->fields['Status'];
        $accountNumber = $rsInfo->fields['AccountNumber'];
        $balance = $rsInfo->fields['Balance'];
        $namaSimpanan = $rsInfo->fields['NamaSimpanan'] ? $rsInfo->fields['NamaSimpanan'] : '-';
        $namaAnggota = $rsInfo->fields['NamaAnggota'] ? $rsInfo->fields['NamaAnggota'] : '-';
        $newIC = $rsInfo->fields['newIC'] ? $rsInfo->fields['newIC'] : '-';
        $picuser = $rsInfo->fields['picture'];
    }
}

// Status mapping
$statusMap = array(
    1  => array('label' => 'Dalam Proses', 'class' => 'warning'),
    3  => array('label' => ($balance > 0 ? 'Aktif' : 'Diluluskan'), 'class' => 'success'),
    7  => array('label' => 'Ditolak', 'class' => 'danger'),
    9  => array('label' => 'Dibatalkan', 'class' => 'secondary'),
    11 => array('label' => 'Suspended', 'class' => 'info'),
);
$currentStatusLabel = isset($statusMap[$currentStatus]) ? $statusMap[$currentStatus]['label'] : 'Unknown';
$currentStatusClass = isset($statusMap[$currentStatus]) ? $statusMap[$currentStatus]['class'] : 'dark';

// Process status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pk !== '') {
    $newStatus = isset($_POST['new_status']) ? (int)$_POST['new_status'] : null;

    if ($newStatus !== null && in_array($newStatus, array(3, 7, 9, 11))) {
        $sSQL = "UPDATE depositoracc SET status=" . tosql($newStatus, 'Number') . " WHERE id=" . tosql($pk, 'Number');
        $rs = $conn->Execute($sSQL);

        $labelMap = array(3 => 'Diluluskan', 7 => 'Ditolak', 9 => 'Dibatalkan', 11 => 'Suspended');
        $label = $labelMap[$newStatus];
        $strActivity = $label . ' rekening simpanan ID-' . $pk . ' (' . $accountNumber . ')';
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

        if ($rs) {
            echo '<script>alert("Status berhasil diubah ke: ' . $label . '"); window.opener.location.reload(); window.close();</script>';
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <title>Verifikasi Simpanan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <h5 class="text-center mb-4">Verifikasi Rekening Simpanan</h5>

    <!-- Info Anggota -->
    <div class="text-center mb-3">
        <?php
        if ($picuser) {
            echo '<img src="upload_images/' . $picuser . '" alt="Foto" height="100" class="rounded-circle mb-2">';
        } else {
            echo '<img src="images/user.png" alt="Foto" height="100" class="rounded-circle mb-2">';
        }
        ?>
    </div>

    <table class="table table-sm table-borderless" style="font-size: 11pt;">
        <tr><td width="40%"><strong>Nama Anggota</strong></td><td>: <?php echo htmlspecialchars(ucwords(strtolower($namaAnggota))); ?></td></tr>
        <tr><td><strong>Kartu Identitas</strong></td><td>: <?php echo htmlspecialchars($newIC); ?></td></tr>
        <tr><td><strong>No Account</strong></td><td>: <?php echo htmlspecialchars($accountNumber); ?></td></tr>
        <tr><td><strong>Jenis Simpanan</strong></td><td>: <?php echo htmlspecialchars($namaSimpanan); ?></td></tr>
        <tr><td><strong>Saldo</strong></td><td>: <strong class="text-primary">Rp <?php echo number_format($balance, 2, ',', '.'); ?></strong></td></tr>
        <tr>
            <td><strong>Status Saat Ini</strong></td>
            <td>: <span class="badge bg-<?php echo $currentStatusClass; ?>"><?php echo $currentStatusLabel; ?></span></td>
        </tr>
    </table>

    <hr>

    <!-- Form Ubah Status -->
    <form method="POST" action="">
        <input type="hidden" name="pk" value="<?php echo htmlspecialchars($pk); ?>">

        <div class="mb-3">
            <label class="form-label"><strong>Ubah Status Ke:</strong></label>
            <select name="new_status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <?php if ($currentStatus == 1): ?>
                <option value="3">Diluluskan</option>
                <option value="7">Ditolak</option>
                <?php endif; ?>
                <?php if ($currentStatus == 3): ?>
                <option value="11">Suspended</option>
                <option value="9">Dibatalkan</option>
                <?php endif; ?>
                <?php if ($currentStatus == 7): ?>
                <option value="1">Kembali ke Dalam Proses</option>
                <?php endif; ?>
                <?php if ($currentStatus == 9): ?>
                <option value="1">Kembali ke Dalam Proses</option>
                <?php endif; ?>
                <?php if ($currentStatus == 11): ?>
                <option value="3">Aktifkan Kembali</option>
                <option value="9">Dibatalkan</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin mengubah status?')">Proses</button>
            <button type="button" class="btn btn-secondary" onclick="window.close()">Batal</button>
        </div>
    </form>
</div>

</body>
</html>