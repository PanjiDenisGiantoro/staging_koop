<?php
include("header.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$Cookie_userID = get_session('Cookie_userID');
$title = "Muat Naik Gambar Selfie";
$sActionFileName = "?vw=cara_byr&dateBirth=$dateBirth&newIC=$newIC&email=$email&mobileNo=$mobileNo";

// Folder untuk menyimpan gambar
$uploadDir = "upload_images/";
$userID = dlookup("userdetails", "userID", "newIC=" . tosql($newIC, "Text"));
$name = dlookup("users", "name", "userID=" . tosql($userID, "Text"));
$updatedDate = date("Y-m-d H:i:s");

?>
<h5 class="card-title">
    <img src="images/number1.png" width="17" height="17">&nbsp;ISI PROFIL&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
    <font class="text-primary"><img src="images/number2-primary.png" width="17" height="17">&nbsp;MUAT NAIK DOKUMEN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i></font>&nbsp;
    <img src="images/number3.png" width="17" height="17">&nbsp;PEMBAYARAN&nbsp;<i class="mdi mdi-arrow-right-bold-outline"></i>&nbsp;
    <img src="images/number4.png" width="17" height="17">&nbsp;SELESAI
</h5>

<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
</div>
<?php

// Proses penyimpanan gambar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadData = array();

    // Proses gambar untuk setiap input
    // Senarai jenis gambar
    $types = array('selfie'); // Hanya gambar selfie yang diteruskan

    // Gantikan foreach dengan for loop
    for ($i = 0; $i < count($types); $i++) {
        $type = $types[$i];
        if (isset($_FILES["fileInput{$type}"]) && $_FILES["fileInput{$type}"]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES["fileInput{$type}"]['tmp_name'];
            $fileName = uniqid($type . "_") . "_" . basename($_FILES["fileInput{$type}"]['name']);
            $targetPath = $uploadDir . $fileName;

            // Simpan gambar ke folder
            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadData[$type] = $fileName;
            }
        }
    }

    // Pastikan gambar selfie dimuat naik
    if (count($uploadData) === 1) { // Memastikan hanya gambar selfie dimuat naik
        $selfieImage = isset($uploadData['selfie']) ? $uploadData['selfie'] : null;

        // Kemas kini SQL
        $sSQLUpd = "";
        $sWhere = "userID='" . $userID . "'";  // Pastikan userID betul
        $sSQLUpd = "UPDATE userdetails SET " .
            "picture=" . tosql($selfieImage, "Text") .
            ",updatedDate=" . tosql($updatedDate, "Text") .
            ",updatedBy=" . tosql($name, "Text");

        $sSQLUpd .= " WHERE " . $sWhere;
        $rsUpd = &$conn->Execute($sSQLUpd);

        if ($rsUpd) {
            alert("Gambar berjaya dimuat naik!");
            gopage("$sActionFileName", 1000);
        } else {
            echo '<script>alert("Terdapat masalah semasa mengemas kini data. Sila cuba lagi.");</script>';
        }
    } else {
        echo '<script>alert("Sila muat naik gambar selfie!");</script>';
    }
}

?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <style>
        .upload-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            /* Center the content horizontally */
        }

        .upload-box {
            width: 100%;
            /* Default to 100% width on small screens */
            height: 180px;
            border: 2px dashed #35a989;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .upload-box:hover {
            background-color: #f0f8ff;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #888;
            font-size: 16px;
            border-radius: 10px;
        }

        /* Bootstrap grid: On medium screens and up, each box will be 30% of the container width */
        @media (min-width: 768px) {
            .upload-box {
                width: 30%;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4"><?php echo $title; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="upload-container row">
                <!-- Hanya gambar selfie yang ada -->
                <div class="upload-box col-12 col-md-4" id="selfieBox">
                    <div class="image-preview" id="selfiePreview">Gambar Selfie</div>
                    <input type="file" name="fileInputselfie" id="fileInputselfie" style="display: none;" accept="image/*" capture="camera" onchange="previewImage(event, 'selfie')">
                </div>
            </div>
            <!-- Button to match the column width -->
            <button type="submit" class="btn btn-primary mt-4 w-100">Seterusnya</button>
        </form>
    </div>

    <script>
        function previewImage(event, type) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById(`${type}Preview`);
                preview.innerHTML = `<img src="${reader.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
            }
            reader.readAsDataURL(file);
        }
        document.getElementById('selfieBox').addEventListener('click', () => document.getElementById('fileInputselfie').click());
    </script>
</body>

</html>
<?php include("footer.php"); ?>