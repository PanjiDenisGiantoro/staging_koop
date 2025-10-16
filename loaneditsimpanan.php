<?php
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

// Validate ID
$ID = isset($_GET['ID']) ? trim($_GET['ID']) : '';
if ($ID === '' || !is_numeric($ID)) {
    echo "<script>alert('ID tidak valid'); window.location.href='?vw=loansimpanan&mn=946';</script>";
    exit;
}

// Fetch current record with user info
$sql = "
  SELECT 
    d.id,
    d.UserID,
    d.AccountNumber,
    d.Code_simpanan,
    d.Balance,
    d.Status,
    d.DateApply,
    u.name AS user_name
  FROM depositoracc d
  JOIN users u
    ON d.UserID COLLATE latin1_general_ci = u.UserID COLLATE latin1_general_ci
  WHERE d.id = " . tosql($ID, "Number") . "
";
$rs = $conn->Execute($sql);
if (!$rs || $rs->EOF) {
    echo "<script>alert('Rekening tidak ditemukan'); window.location.href='?vw=loansimpanan1&mn=946';</script>";
    exit;
}

$UserID        = $rs->fields['UserID'];
$AccountNumber = $rs->fields['AccountNumber'];
$CodeSimpanan  = $rs->fields['Code_simpanan'];
$Status        = (string)$rs->fields['Status'];
$UserName      = $rs->fields['user_name'];

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCodeSimpanan = isset($_POST['Code_simpanan']) ? trim($_POST['Code_simpanan']) : '';
    $newStatus       = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if ($newCodeSimpanan === '') {
        echo "<script>alert('Jenis simpanan wajib dipilih');</script>";
    } else {
        // Prevent duplicates: same user + same Code_simpanan, excluding current record
        $cekDup = "
          SELECT COUNT(*) AS cnt
          FROM depositoracc
          WHERE UserID = " . tosql($UserID, "Text") . "
            AND Code_simpanan = " . tosql($newCodeSimpanan, "Text") . "
            AND id <> " . tosql($ID, "Number");
        $rsDup = $conn->Execute($cekDup);
        if ($rsDup && (int)$rsDup->fields['cnt'] > 0) {
            echo "<script>alert('User ini sudah memiliki rekening untuk jenis simpanan tersebut!');</script>";
        } else {
            // Update record
            $upd = "
              UPDATE depositoracc
              SET Code_simpanan = " . tosql($newCodeSimpanan, "Text") . ",
                  Status = " . tosql($newStatus, "Number") . ",
                  UpdateUserID = " . tosql(get_session('Cookie_userID'), "Text") . ",
                  DateUpdateUserID = " . tosql(date('Y-m-d H:i:s'), "Text") . "
              WHERE id = " . tosql($ID, "Number");
            $conn->Execute($upd);

            echo "<script>alert('Rekening Simpanan berhasil diubah'); 
                  window.location.href='?vw=loansimpanan1&mn=946';</script>";
            exit;
        }
    }
}
?>

    <div class="container mt-4">
        <h3>Edit Rekening Simpanan</h3>
        <form method="post" action="" onsubmit="return confirm('Simpan perubahan?')">

            <!-- User (read-only) -->
            <div class="form-group mb-3">
                <label>Nama</label>
                <input type="hidden" name="UserID" id="UserID" value="<?php echo htmlspecialchars($UserID); ?>">
                <div class="mb-2">
                    <div class="input-group">
                        <span class="input-group-text">Kode</span>
                        <input type="text" name="loanCode" id="loanCode" class="form-control"
                               value="<?php echo htmlspecialchars($UserID); ?>" readonly>
                    </div>
                    <div class="input-group mt-2">
                        <span class="input-group-text">Nama</span>
                        <input type="text" name="loanName" id="loanName" class="form-control"
                               value="<?php echo htmlspecialchars($UserName); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Account Number (read-only) -->
            <div class="form-group mb-3">
                <label>No. Rekening</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($AccountNumber); ?>" readonly>
            </div>

            <!-- Jenis Simpanan -->
            <div class="form-group mb-3">
                <label>Jenis Simpanan</label>
                <select name="Code_simpanan" class="form-control" required>
                    <option value="">-- Pilih Simpanan --</option>
                    <?php
                    // Only active simpanan
                    $sqlGen = "SELECT ID, name FROM general WHERE category = 'Y' AND status_active_simpanan = '1' ORDER BY name";
                    $rsGen = $conn->Execute($sqlGen);
                    while ($rsGen && !$rsGen->EOF) {
                        $gid  = $rsGen->fields['ID'];
                        $gname= $rsGen->fields['name'];
                        $sel = ((string)$gid === (string)$CodeSimpanan) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($gid) . "' $sel>" . htmlspecialchars($gname) . "</option>";
                        $rsGen->MoveNext();
                    }
                    ?>
                </select>
            </div>

            <!-- Status -->
            <div class="form-group mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" <?php echo ($Status === '1' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="0" <?php echo ($Status === '0' ? 'selected' : ''); ?>>Tidak Aktif</option>
                    <option value="2" <?php echo ($Status === '2' ? 'selected' : ''); ?>>Ditolak</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="?vw=loansimpanan1&mn=946" class="btn btn-secondary">Batal</a>
        </form>
    </div>

<?php include("footer.php"); ?>