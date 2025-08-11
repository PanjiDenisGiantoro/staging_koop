<?php
include("header.php");

// Ambil senarai ahli dari DB
$sql = "
    SELECT u.userID, u.name
    FROM users u
    JOIN userdetails ud ON u.userID = ud.userID
    WHERE ud.jawkopID = 1
    ORDER BY u.userID";
$rs = $conn->Execute($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pilih ALK</title>
    
    <!-- ✅ Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    
    <style>
        body {
           background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="container p-4 bg-white rounded shadow" style="max-width: 600px;">
    <form name="selectALK">
        <h4 class="mb-4">Pilih ALK Yang Hadir</h4>

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <td style="width: 50px;">&nbsp;</td>
                    <td align="center">Nombor Anggota</td>
                    <td>Nama</td>
                </tr>
            </thead>
            <tbody>
                <?php 
                $bil = 1;
                while (!$rs->EOF) { ?>
                    <tr>                        
                        <td class="text-center">
                            <input class="form-check-input" type="checkbox" name="alk[]" 
                                   id="alk<?= $rs->fields('userID') ?>" 
                                   value="<?= $rs->fields('userID') ?>" 
                                   data-name="<?= $rs->fields('name') ?>">
                        </td>
                        <td align="center"><?= $rs->fields('userID') ?><td>
                            <label class="form-check-label" for="alk<?= $rs->fields('userID') ?>">
                                <?= $rs->fields('name') ?>
                            </label>
                        </td>
                    </tr>
                <?php 
                    $rs->MoveNext(); 
                    $bil++;
                } ?>
            </tbody>
        </table>

        <div class="mt-4 text-center">
            <button type="button" class="btn btn-primary" onclick="returnData();">Pilih</button>
            <button type="button" class="btn btn-secondary" onclick="window.close();">Tutup</button>
        </div>
    </form>
</div>


<!-- ✅ Custom Script -->
<script>
function returnData() {
    let checkboxes = document.querySelectorAll('input[name="alk[]"]:checked');
    let ids = [];
    let names = [];

    checkboxes.forEach(cb => {
        ids.push(cb.value);
        names.push(cb.getAttribute("data-name"));
    });

    if (window.opener && !window.opener.closed) {
        window.opener.document.MyForm.alk.value = ids.join(",");
        window.opener.document.MyForm.alkNames.value = names.join(", ");
        window.close();
    } else {
        alert("Parent window is not accessible.");
    }
}
</script>

</body>
</html>
