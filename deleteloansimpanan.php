<?php
include("header.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

// Permission gate (optional, mirrors other pages)
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
if ((get_session("Cookie_groupID") <> 1 && get_session("Cookie_groupID") <> 2) || get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("Akses ditolak!");parent.location.href = "index.php";</script>';
    exit();
}

$ID = isset($_GET['ID']) ? trim($_GET['ID']) : '';
if ($ID === '' || !is_numeric($ID)) {
    print '<script>alert("Parameter ID tidak valid.");window.location.href="?vw=loansimpanan1&mn=946";</script>';
    exit();
}

// Optional safety: do not allow delete if record is active (status = 1)
$sCheck = "SELECT id, UserID, Status FROM depositoracc WHERE id = " . tosql($ID, 'Number');
$rsCheck = $conn->Execute($sCheck);
if (!$rsCheck || $rsCheck->EOF) {
    print '<script>alert("Data tidak ditemukan.");window.location.href="?vw=loansimpanan1&mn=946";</script>';
    exit();
}

if ((int)$rsCheck->fields['Status'] === 1) {
    print '<script>alert("Tidak boleh menghapus rekening yang masih AKTIF.");window.location.href="?vw=loansimpanan1&mn=946";</script>';
    exit();
}

// Perform delete
$sDel = "DELETE FROM depositoracc WHERE id = " . tosql($ID, 'Number');
$conn->Execute($sDel);

// Log activity if helper exists
if (function_exists('activityLog')) {
    $strActivity = 'Hapus Rekening Simpanan - ID: ' . $ID;
    activityLog($sDel, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);
}

print '<script>alert("Data rekening berhasil dihapus.");window.location.href="?vw=loansimpanan1&mn=946";</script>';
exit();
