<?php

/*********************************************************************************
 *           Project        :    iKOOP.com.my
 *           Filename        :    loan.php
 *           Date            :    06/12/2015
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg)) $pg = 10;
if (!isset($q)) $q = "";
if (!isset($by)) $by = "0";
if (!isset($filter)) $filter = "ALL";
if (!isset($dept)) $dept = "";

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}
$IDName = get_session("Cookie_userName");

$sFileName = '?vw=loansimpanan&mn=902';
$sFileRef = '?vw=biayaDokumensimpanan&mn=902';
$title = "Rekening Simpanan";

if (isset($_GET['action']) && $_GET['action'] == "toggle") {
    $id = intval($_GET['ID']); // konversi ke integer biar aman

    $sql = "UPDATE depositoracc 
            SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END 
            WHERE ID = " . $id;
    $conn->Execute($sql);

    // balik ke halaman sebelumnya
    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: loansimpanan.php"); // fallback kalau referer kosong
    }
    exit();
} else if (isset($_GET['action'])) {
    die("Invalid action");
}


print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title">' . strtoupper($title) . '</h5>
    <input type="button" class="btn btn-md btn-primary" value="+ Rekening Baru " onClick="window.location.href=\'?vw=loanApplysimpanan&mn=902\'"/>
</div>';

?>

    <head>
        <title>Payment Summary</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            body {
                font-family: Poppins, sans-serif;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .container {
                display: flex;
                flex-direction: column;
                gap: 20px;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 12px;
                width: 100%;
                box-sizing: border-box;
                margin: 0 auto;
            }

            @media (min-width: 768px) {
                .container {
                    flex-direction: row;
                    align-items: stretch;
                }

                .chart-container {
                    flex: 1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .summary {
                    flex: 1;
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 20px;
                }
            }

            .chart-container {
                width: 100%;
                max-width: 400px;
                height: auto;
                margin: 0 auto;
            }

            .summary {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            .summary-box {
                padding: 10px;
                border-radius: 8px;
                background: #fafafa;
            }

            .summary-box strong {
                display: block;
                margin-bottom: 8px;
            }

            .amount {
                font-size: 1.5em;
                margin: 5px 0;
            }

            hr {
                border: none;
                border-top: 1px solid #ddd;
                margin: 10px 0;
            }

            .header {
                display: flex;
                justify-content: space-between;
                padding: 10px 20px;
                box-sizing: border-box;
                flex-wrap: wrap;
            }

            .header h2 {
                margin: 0;
            }

        </style>
    </head>

    <body>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>No IC</th>
            <th>Nomor Account</th>
            <th>Nama Simpanan</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sSQL = "
SELECT *,A.status as status_depositacc, A.id as id_unique, c.name as name_user
FROM depositoracc A
         JOIN USERdetails B
              ON A.UserID COLLATE latin1_general_ci = B.UserID COLLATE latin1_general_ci
         JOIN users C
              ON B.UserID COLLATE latin1_general_ci = C.UserID COLLATE latin1_general_ci
         JOIN general D
              ON CAST(A.Code_simpanan AS CHAR CHARACTER SET latin1) COLLATE latin1_general_ci
                  = CAST(D.ID AS CHAR CHARACTER SET latin1) COLLATE latin1_general_ci";
        $result = &$conn->Execute($sSQL);

        if ($result && !$result->EOF) {
            $label = ($result->fields['status_depositacc'] == 0) ? "Approve" : "Reject";
            $no = 1;
            while (!$result->EOF) {
                if ($result->fields['status_depositacc'] == 1) {
                    $status = "Aktif";
                } else {
                    $status = "Tidak Aktif";
                }
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['name_user']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['newIC']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['AccountNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($result->fields['nama_simpanan']) . "</td>";
                echo "<td>" .$status . "</td>";
                echo "<td>
    <a href='?vw=loansimpanan&mn=902&ID=" . $result->fields['id_unique'] . "&action=toggle' 
       onclick=\"return confirm('Apakah Anda yakin ingin mengubah status rekening ini?');\">
       $label
    </a>";

                if ($result->fields['status_depositacc'] != 1) {
                    // kalau status bukan Aktif, baru tampil Edit & Delete
                    echo " | 
    <a href='?vw=loaneditsimpanan&mn=902&ID=" . $result->fields['id_unique'] . "'>Edit</a> | 
    <a href='?vw=deleteloansimpanan&mn=902&ID=" . $result->fields['id_unique'] . "' 
       onclick=\"return confirm('Yakin mau hapus data ini?');\">
       Delete
    </a>";
                }

                echo "</td>";

                echo "</tr>";
                $no++;
                $result->MoveNext();
            }
        } else {
            echo "<tr><td colspan='7'>Tiada rekod</td></tr>";
        }
        ?>
        </tbody>
    </table>

    </body>
<?
include("footer.php");

