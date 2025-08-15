<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	assignleave.php
 *		   Description	:   Assign Staff Leave
 *          Date 		: 	11/11/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("koperasiList.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session('Cookie_userID') == "" || get_session("Cookie_koperasiID") != 0) {
    print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) $member = 0;
else $member = 1;
if (!isset($strDate)) $strDate = date("d/m/Y");

$title = "PENETAPAN CUTI";

$cutiList = array();
$cutiVal  = array();
$GetCuti = ctGeneral("", "V");
if ($GetCuti->RowCount() != 0) {
    while (!$GetCuti->EOF) {
        array_push($cutiList, $GetCuti->fields('name'));
        array_push($cutiVal, $GetCuti->fields('ID'));
        $GetCuti->MoveNext();
    }
}

$userID = dlookup("users", "userID", "staffID=" . $pk);

if ($action == 'Kemaskini') {

    $staffIDs = explode(",", $pk);
    $sanitizedIDs = array();

    foreach ($staffIDs as $id) {
        $trimmedID = trim($id);
        if (!empty($trimmedID)) {
            $sanitizedIDs[] = tosql($trimmedID, "Number");
        }
    }

    if (empty($sanitizedIDs)) {
        echo '<script>alert("Tiada pengguna yang dipilih.");</script>';
        exit;
    }

    $strDate = substr($strDate, 6, 4) . '-' . substr($strDate, 3, 2) . '-' . substr($strDate, 0, 2);

    foreach ($sanitizedIDs as $staffID) {

        $userID = dlookup("users", "userID", "staffID=" . $staffID);

        if (!$userID) {
            echo "Error: UserID not found for StaffID $staffID.<br>";
            continue;
        }

        foreach ($cutiVal as $index => $leaveTypeID) {

            $leaveDays = isset($_POST['leaveDays'][$leaveTypeID]) ? $_POST['leaveDays'][$leaveTypeID] : null;

            if (!$leaveDays || $leaveDays <= 0) {
                continue;
            }

            $existingLeave = dlookup("sleave_details", "ID", "userID=" . $userID . " AND leaveTypeID=" . $leaveTypeID);

            if ($existingLeave) {
                $sSQL = "UPDATE sleave_details 
                        SET totalLeave = " . tosql($leaveDays, "Number") . ",
                            balanceLeave = " . tosql($leaveDays, "Number") . "
                        WHERE userID = " . $userID . " AND leaveTypeID = " . $leaveTypeID;
            } else {
                $sSQL = "INSERT INTO sleave_details (userID, leaveTypeID, totalLeave, balanceLeave)
                        VALUES (" . $userID . ", " . $leaveTypeID . ", " . tosql($leaveDays, "Number") . ", " . tosql($leaveDays, "Number") . ")";
            }

            $rs = $conn->Execute($sSQL);
        }
    }
    alert("Cuti berjaya ditetapkan.");
    gopage("?vw=staff&mn=933", 1000);
}

if ($member) {
    if (isset($pk)) $pkall = explode(":", $pk);
    unset($pk);
}

?>
<h5 class="card-title"><?= strtoupper($title) ?></h5>
<div style="width: 450px; text-align:left">
    <form name="MyForm" action="" method="post">
        <input type="hidden" name="action">
        <input type="hidden" name="pk" value="<?= implode(",", $pkall); ?>">
        <table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="left">
            <tr class="card-body bg-light">
                <td>
                    <table border="0" cellspacing="6" cellpadding="0" width="100%" align="center">
                        <?php
                        foreach ($pkall as $s => $pk) {

                            $sSQL = "SELECT * FROM users WHERE `staffID` = '" . $pk . "'";
                            $GetUser = $conn->Execute($sSQL);

                            if ($GetUser->EOF) {
                                echo "<tr><td colspan='3'>Debug: No user found in users table for Staff ID: $pk</td></tr>";
                                continue;
                            }

                            // Fetch user details
                            $userID = $GetUser->fields['userID'];
                            $staffID = $GetUser->fields['staffID'];
                            $name = dlookup("users", "name", "userID=" . tosql($userID, "Text"));
                            $gender = dlookup("staff", "sex", "staffID=" . tosql($staffID, "Text"));

                            // Convert gender value to readable text
                            $genderText = ($gender == 0) ? "Lelaki" : ($gender == 1 ? "Perempuan" : "Unknown");

                        ?>
                            <tr>
                                <td>Nombor Anggota</td>
                                <td>:</td>
                                <td><b><?= $userID; ?></b></td>
                            </tr>
                            <tr>
                                <td>Nama Staf</td>
                                <td>:</td>
                                <td><b><?= strtoupper($name); ?></b></td>
                            </tr>
                            <tr>
                                <td>Jantina</td>
                                <td>:</td>
                                <td><b><?= strtoupper($genderText); ?></b></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <hr class="mt-1">
                                </td>
                            </tr>
                        <?php
                        }

                        ?>
                        <?php
                        foreach ($cutiList as $index => $leaveType) {
                            $totalLeave = dlookup("sleave_details", "balanceLeave", "userID = " . tosql($userID, "Text") . " AND leavetypeID = " . tosql($cutiVal[$index], "Text"));

                            $maxLeave = dlookup("general", "jumlah_minimum", "ID = " . tosql($cutiVal[$index], "Text"));

                            $leaveDisplay = is_numeric($totalLeave) ? $totalLeave : "Tidak Ada";
                            $defaultLeave = is_numeric($maxLeave) ? $maxLeave : "";

                            if ($cutiVal[$index] == "2065" && $gender == 0) {
                        ?>
                                <tr>
                                    <td><?= $leaveType ?></td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control-sm" name="leaveDays[<?= $cutiVal[$index] ?>]" value="<?= $defaultLeave ?>" size="5" maxlength="3">
                                        <small>(Jumlah Baki: <?= $leaveDisplay ?>)</small>
                                    </td>
                                </tr>
                            <?php
                            } elseif ($cutiVal[$index] == "2064" && $gender == 1) {
                            ?>
                                <tr>
                                    <td><?= $leaveType ?></td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control-sm" name="leaveDays[<?= $cutiVal[$index] ?>]" value="<?= $defaultLeave ?>" size="5" maxlength="3">
                                        <small>(Jumlah Baki: <?= $leaveDisplay ?>)</small>
                                    </td>
                                </tr>
                            <?php
                            } elseif ($cutiVal[$index] != "2065" && $cutiVal[$index] != "2064") {
                            ?>
                                <tr>
                                    <td><?= $leaveType ?></td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control-sm" name="leaveDays[<?= $cutiVal[$index] ?>]" value="<?= $defaultLeave ?>" size="5" maxlength="3">
                                        <small>(Jumlah Baki: <?= $leaveDisplay ?>)</small>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="3" align="center">
                                <div class="mt-3">
                                    <input type="submit" name="action" value="Kemaskini" class="btn btn-primary" onclick="return validateForm();">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
</div>
</div>

<script>
    function validateForm() {
        const inputs = document.querySelectorAll('input[name^="leaveDays"]');
        for (let input of inputs) {
            const value = input.value;
            if (value !== '' && (!/^\d+$/.test(value) || value <= 0)) {
                alert('Sila masukkan nombor yang sah untuk hari cuti (positif).');
                return false;
            }
        }
        return true;
    }
</script>
<?php include("footer.php"); ?>