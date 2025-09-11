<?php
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $UserID         = isset($_POST['UserID']) ? trim($_POST['UserID']) : '';
    $AccountNumber  = isset($_POST['AccountNumber']) ? trim($_POST['AccountNumber']) : '';
    $Code_simpanan  = isset($_POST['Code_simpanan']) ? trim($_POST['Code_simpanan']) : '';
    $status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

    if ($UserID != '' && $AccountNumber == '' && $Code_simpanan != '') {

        // Generate AccountNumber otomatis
        $prefix = date("Ymd"); // tahun bulan tanggal, contoh 20250825

        // Cek nomor terakhir di hari ini
        $cekAcc = "SELECT MAX(AccountNumber) as max_acc 
               FROM depositoracc 
               WHERE AccountNumber LIKE '" . $prefix . "%'";
        $rsAcc = $conn->Execute($cekAcc);

        $lastNumber = 0;
        if ($rsAcc && $rsAcc->fields['max_acc'] != null) {
            $lastNumber = intval(substr($rsAcc->fields['max_acc'], 8)); // ambil 5 digit terakhir
        }

        $newNumber = str_pad($lastNumber + 1, 5, "0", STR_PAD_LEFT);
        $AccountNumber = $prefix . $newNumber;

        // 🔎 Cek apakah sudah ada user dengan kombinasi UserID + Code_simpanan
        $cekSql = "SELECT COUNT(*) as cnt 
               FROM depositoracc 
               WHERE UserID = " . tosql($UserID, "Text") . " 
                 AND Code_simpanan = " . tosql($Code_simpanan, "Text");
        $rsCek = $conn->Execute($cekSql);

        if ($rsCek && $rsCek->fields['cnt'] > 0) {
            echo "<script>alert('User ini sudah memiliki rekening dengan jenis simpanan tersebut!'); 
              window.history.back();</script>";
            exit;
        }

        // Lanjut insert
        $Balance        = 0;
        $DateApply      = date("Y-m-d H:i:s");
        $UpdateUserID   = $UserID;
        $DateUpdateUserID = $DateApply;

        $sql = "INSERT INTO depositoracc 
        (UserID, AccountNumber, Code_simpanan, Balance, Status, DateApply, UpdateUserID, DateUpdateUserID)
        VALUES (" . tosql($UserID, "Text") . ",
                " . tosql($AccountNumber, "Text") . ",
                " . tosql($Code_simpanan, "Text") . ",
                " . $Balance . ",
                " . $status . ",
                " . tosql($DateApply, "Text") . ",
                " . tosql($UpdateUserID, "Text") . ",
                " . tosql($DateUpdateUserID, "Text") . ")";

        $conn->Execute($sql);

        echo "<script>alert('Rekening Simpanan berhasil ditambahkan'); 
          window.location.href='?vw=loansimpanan&mn=946';</script>";
        exit;
    } else {
        echo "<script>alert('Semua field wajib diisi');</script>";
    }
}
?>

<!-- di dalam <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container mt-4">
    <h3>Tambah Rekening Simpanan</h3>
    <form method="post" action="" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan data ini?')">
        <div class="form-group mb-3">
            <label>Nama</label>
            <input type="hidden" name="UserID" id="UserID" value="<?php echo htmlspecialchars($UserID); ?>">

            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">Kode</span>
                    <input type="text" name="loanCode" id="loanCode" class="form-control"
                           value="<?php echo htmlspecialchars($loanCode); ?>" readonly>
                    <button type="button" class="btn btn-sm btn-info"
                            onclick="window.open('selMember.php','sel','top=10,left=10,width=900,height=600,scrollbars=yes,resizable=yes')">
                        Pilih
                    </button>
                </div>
                <div class="input-group mt-2">
                    <span class="input-group-text">Nama</span>
                    <input type="text" name="loanName" id="loanName" class="form-control"
                           value="<?php echo htmlspecialchars($loanName); ?>" readonly>
                </div>
            </div>

        </div>

        <div class="form-group mb-3">
            <label>Jenis Simpanan</label>
            <select name="Code_simpanan" class="form-control" required>
                <option value="">-- Pilih Simpanan --</option>
                <?php
                $sqlGen = "SELECT * FROM general  where category = 'Y' and status_active_simpanan = '1' ORDER BY nama_simpanan";
                $rsGen = $conn->Execute($sqlGen);
                while(!$rsGen->EOF){
                    echo "<option value='" . htmlspecialchars($rsGen->fields['ID']) . "'>"
                        . htmlspecialchars($rsGen->fields['name']) . "</option>";
                    $rsGen->MoveNext();
                }
                ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="?vw=loansimpanan&mn=946" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
// Expose function globally so selMember popup can call it
window.setSelectedMember = function(userId, userName) {
  var codeEl = document.getElementById('loanCode');
  var nameEl = document.getElementById('loanName');
  var idEl   = document.getElementById('UserID');
  if (codeEl) codeEl.value = userId;
  if (nameEl) nameEl.value = userName;
  if (idEl)   idEl.value   = userId;
};
</script>
<?php include("footer.php"); ?>
<script>
  $(function() {
    if (window.jQuery && $.fn.select2) {
      $('.select2').select2({
        placeholder: 'Pilih User',
        allowClear: true,
        width: '100%'
      });
    }
  });
</script>
