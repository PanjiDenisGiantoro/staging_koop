<?php
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$id = isset($_GET['ID']) ? trim($_GET['ID']) : ''; // ID dari parameter URL
if ($id == '') {
    echo "<script>alert('ID rekening tidak ditemukan'); window.location.href='?vw=loansimpanan&mn=946';</script>";
    exit;
}

// 🔎 Ambil data lama
$sql = "SELECT * FROM depositoracc  C
            JOIN users B
              ON B.UserID COLLATE latin1_general_ci = C.UserID COLLATE latin1_general_ci
         WHERE ID=" . tosql($id, "Text");
$rs  = $conn->Execute($sql);

if ($rs->EOF) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='?vw=loansimpanan&mn=946';</script>";
    exit;
}

$UserID        = $rs->fields['UserID'];
$AccountNumber = $rs->fields['AccountNumber'];
$Code_simpanan = $rs->fields['Code_simpanan'];
$status        = $rs->fields['Status'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $UserID         = isset($_POST['UserID']) ? trim($_POST['UserID']) : '';
    $Code_simpanan  = isset($_POST['Code_simpanan']) ? trim($_POST['Code_simpanan']) : '';
    $status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

    if ($UserID != '' && $Code_simpanan != '') {
        $UpdateUserID     = $UserID;
        $DateUpdateUserID = date("Y-m-d H:i:s");

        // 🔎 Cek apakah kombinasi UserID + Code_simpanan sudah ada (kecuali rekening ini)
        $cekSql = "SELECT COUNT(*) as cnt 
                   FROM depositoracc 
                   WHERE UserID=" . tosql($UserID, "Text") . " 
                     AND Code_simpanan=" . tosql($Code_simpanan, "Text") . "
                     AND AccountNumber<>" . tosql($AccountNumber, "Text");
        $rsCek = $conn->Execute($cekSql);

        if ($rsCek && $rsCek->fields['cnt'] > 0) {
            echo "<script>alert('User ini sudah memiliki rekening dengan jenis simpanan tersebut!'); 
                  window.history.back();</script>";
            exit;
        }

        // ✅ Update data
        $sqlUpd = "UPDATE depositoracc SET 
                      UserID=" . tosql($UserID, "Text") . ",
                      Code_simpanan=" . tosql($Code_simpanan, "Text") . ",
                      Status=" . $status . ",
                      UpdateUserID=" . tosql($UpdateUserID, "Text") . ",
                      DateUpdateUserID=" . tosql($DateUpdateUserID, "Text") . "
                   WHERE AccountNumber=" . tosql($AccountNumber, "Text");

        $conn->Execute($sqlUpd);

        echo "<script>alert('Rekening Simpanan berhasil diperbarui'); 
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
    <h3>Edit Rekening Simpanan</h3>
    <form method="post" action="" onsubmit="return confirm('Apakah Anda yakin ingin mengubah data ini?')">

        <div class="form-group mb-3">
            <label>Account Number</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($AccountNumber) ?>" readonly>
        </div>

        <div class="form-group mb-3">
            <label>Nama</label>
            <input type="text" class="form-control"
                   value="<?= htmlspecialchars($rs->fields['name']) ?>" readonly>
            <input type="hidden" name="UserID" value="<?= htmlspecialchars($UserID) ?>">
        </div>


        <div class="form-group mb-3">
            <label>Jenis Simpanan</label>
            <select name="Code_simpanan" class="form-control" required>
                <option value="">-- Pilih Simpanan --</option>
                <?php
                $sqlGen = "SELECT * FROM general WHERE category = 'Y' ORDER BY nama_simpanan";
                $rsGen = $conn->Execute($sqlGen);
                while(!$rsGen->EOF){
                    $idSim = htmlspecialchars($rsGen->fields['ID']);
                    $namaSim = htmlspecialchars($rsGen->fields['nama_simpanan']);
                    $selected = ($idSim == $Code_simpanan) ? "selected" : "";
                    echo "<option value='$idSim' $selected>$namaSim</option>";
                    $rsGen->MoveNext();
                }
                ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" <?= $status == 1 ? "selected" : "" ?>>Aktif</option>
                <option value="0" <?= $status == 0 ? "selected" : "" ?>>Tidak Aktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="?vw=loansimpanan&mn=946" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include("footer.php"); ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih User",
            allowClear: true,
            width: '100%'
        });
    });
</script>
