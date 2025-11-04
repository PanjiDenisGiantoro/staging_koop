<?php
session_start();
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$today = date('Y-m-d');

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$userCreated = get_session("Cookie_userID");

// Get PK (depositoracc id) from GET/POST
$pk = isset($_GET['pk']) ? $_GET['pk'] : (isset($_POST['pk']) ? $_POST['pk'] : '');

// Load basic user info from depositoracc
$userID = '';
if ($pk !== '') {
    $userID = dlookup("depositoracc", "UserID", "id=" . tosql($pk, "Number"));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pk !== '') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $newStatus = ($action === 'approve') ? 1 : (($action === 'reject') ? 2 : null);

    if ($newStatus !== null) {
        $sSQL = "UPDATE depositoracc SET status=" . tosql($newStatus, 'Number') . " WHERE id=" . tosql($pk, 'Number');
        $rs = $conn->Execute($sSQL);

        $label = ($newStatus == 1) ? 'Verifikasi' : 'Ditolak';
        $strActivity = $label . ' rekening anggota - ' . $pk;
        activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

        if ($rs) {
            $msg = ($newStatus == 1) ? 'Rekening telah diverifikasi.' : 'Rekening telah ditolak.';
            echo '<script>alert("' . $msg . '"); window.opener.location.reload(); window.close();</script>';
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
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <title>Form Styling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-spacing: 10px;
            margin-bottom: 20px;
        }

        td {
            padding: 10px;
            vertical-align: middle;
        }

        .form-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .image-container {
            display: flex;
            justify-content: center;
        }

        .data-bold {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-title" align="center">Maklumat Anggota</div>

        <form method="POST" action="">
            <input type="hidden" name="pk" value="<?php echo htmlspecialchars($pk); ?>">
            <table>
                <tr>
                    <td align="right">Nama Anggota:</td>
                    <td><span class="data-bold"><?php echo ($userID !== '' ? ucwords(strtolower(dlookup("users", "name", "userID=" . tosql($userID, "Text")))) : '-'); ?></span></td>
                </tr>
                <tr>
                    <td align="right">Gambar Anggota:</td>
                    <td>
                        <div>
                            <?php
                            if (!isset($picuser) && $userID !== '') $picuser = dlookup("userdetails", "picture", "userID=" . tosql($userID, "Text"));
                            if ($picuser) {
                                echo '<img src="upload_images/' . $picuser . '" alt="User Picture" height="150">';
                            } else {
                                echo '<img src="images/user.png" alt="User Picture" height="150">';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="right">Kartu Identitas Anggota:</td>
                    <td><span class="data-bold"><?php echo ($userID !== '' ? dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text")) : '-'); ?></span></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" class="d-flex gap-2" style="gap:10px;">
                        <button type="submit" name="action" value="approve" class="btn btn-primary">Verifikasi</button>
        
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Ditolak</button>
                    </td>
                </tr>
            </table>
        </form>

    </div>

</body>

</html>