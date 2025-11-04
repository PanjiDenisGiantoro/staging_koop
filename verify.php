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
$sActionFileName = "?vw=member&mn='" . $mn . "'";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sSQL     = "UPDATE userdetails SET " .
        "verified='" . 1 . "'";
    $sSQL     .= " WHERE userID=" . tosql($pk, "Number");

    $rs = &$conn->Execute($sSQL);

    $strActivity = $_POST['Submit'] . 'Verifikasi Anggota - ' . $pk;
    activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

    if ($rs) {
        echo '<script>alert("Anggota Telah Di Verifikasi."); window.opener.location.reload(); window.close();</script>';
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

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
            <table>
                <tr>
                    <td align="right">Nama Anggota:</td>
                    <td><span class="data-bold"><?php echo ucwords(strtolower(dlookup("users", "name", "userID=" . tosql($pk, "Number")))); ?></span></td>
                </tr>
                <tr>
                    <td align="right">Gambar Anggota:</td>
                    <td>
                        <div>
                            <?php
                            if (!isset($picuser)) $picuser = dlookup("userdetails", "picture", "userID=" . tosql($pk, "Number"));
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
                    <td><span class="data-bold"><?php echo dlookup("userdetails", "newIC", "userID=" . tosql($pk, "Number")); ?></span></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" class="btn btn-primary" value="Verifikasi">
                    </td>
                </tr>
            </table>
        </form>
    </div>

</body>

</html>