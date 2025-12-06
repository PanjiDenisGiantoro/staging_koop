<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	:   staffIncome.php
 *          Date 		: 	03/1/2025
 *********************************************************************************/
session_start();
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
$title     = "Maklumat Gaji Staf";

// $sFileName          = "?vw=staffIncome&mn=$mn";
// $sFileNameDel       = "?vw=Edit_memberStmtPotongan&mn=$mn";
// $sFileRef           = "?vw=Edit_memberStmtPotonganPokok&mn=$mn";
// $sActionFileName    = "?vw=Edit_memberStmtPotongan&mn=$mn&ID=$ID";

$IDName = get_session("Cookie_userName");
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
    !(get_session("Cookie_groupID") == 0 || get_session("Cookie_groupID") == 1 || get_session("Cookie_groupID") == 2 || get_session("Cookie_groupID") == 99)
    && get_session("Cookie_koperasiID") <> $koperasiID
) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0 || get_session("Cookie_groupID") == 99) {
    $pk = dlookup("users", "staffID", "loginID='" . $IDName . "'");
    $jabatan = dlookup("staff", "jabatanID", "staffID=" . $pk);
}

if (!isset($mth)) $mth    = date("n");
if (!isset($yr))  $yr    = date("Y");
if (!isset($mm))  $mm     = date("m");
if (!isset($yy))  $yy   = date("Y");

$yrmthNow = sprintf("%04d%02d", $yr, $mth);
$yymm = $yy . $mm;

//--- Prepare gaji type
$gajiList = array();
$gajiVal  = array();
$GetGaji = ctGeneral("", "R");
if ($GetGaji->RowCount() <> 0) {
    while (!$GetGaji->EOF) {
        array_push($gajiList, $GetGaji->fields(name));
        array_push($gajiVal, $GetGaji->fields(ID));
        $GetGaji->MoveNext();
    }
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$gaji_pokok = dlookup("staff_salary", "base_salary", "staffID=$pk ");

if ($action == 'Kira') {
    $insertBy    = get_session("Cookie_userName");
    $insertDate = date("Y-m-d H:i:s");
    $updatedBy    = get_session("Cookie_userName");
    $updatedDate = date("Y-m-d H:i:s");

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

    foreach ($sanitizedIDs as $staffID) {
        $base_salary = dlookup("staff_salary", "base_salary", "staffID=$staffID");

        if (empty($base_salary) || $base_salary <= 0) {
            $gaji_pokok = isset($_POST['amount']['2074']) ? floatval($_POST['amount']['2074']) : 0;

            if ($gaji_pokok > 0) {
                $sSQL = "UPDATE staff_salary 
                         SET base_salary = " . tosql($gaji_pokok, "Number") . ",
                             updatedBy = " . tosql($updatedBy, "Text") . ",
                             updatedDate = " . tosql($updatedDate, "Date") . "
                         WHERE staffID = " . tosql($staffID, "Number") . ";";

                $rs = $conn->Execute($sSQL);
                if (!$rs) {
                    die("SQL Error: " . $conn->ErrorMsg());
                }

                // Re-fetch the updated base_salary
                $base_salary = dlookup("staff_salary", "base_salary", "staffID=$staffID");
            } else {
                echo "<script>alert('Sila masukkan Gaji Pokok terlebih dahulu.');</script>";
                echo '<script>window.location.href = "?vw=staffIncome&pk=' . $pk . '";</script>';
                exit();
            }
        }
        // Assign the latest base_salary
        $gaji_pokok = $base_salary;


        foreach ($gajiVal as $index => $gajiID) {
            $type_gaji = dlookup("general", "type_gaji", "ID=$gajiID");
            if (isset($_POST['gajiID']) && in_array($gajiID, $_POST['gajiID'])) {
                $amount = isset($_POST['amount'][$gajiID]) ? $_POST['amount'][$gajiID] : 0;

                if ($type_gaji == "Statutori") {
                    if ($gajiID == "2071") {
                        $amount = ($amount / 100) * $gaji_pokok;
                    }

                    if ($amount <= 0) continue;

                    $existingEntry = dlookup("staff_salary", "ID", "staffID=$staffID AND gajiID=$gajiID");

                    if ($existingEntry) {
                        $sSQL = "UPDATE staff_salary 
                             SET amount = " . tosql($amount, "Text") . ",
                                 nett_salary = " . tosql($gajiBersih, "Text") . ",
                                 updatedBy = " . tosql($updatedBy, "Text") . ",
                                 updatedDate = " . tosql($updatedDate, "Date") . "
                             WHERE staffID = " . tosql($staffID, "Number") . "
                             AND gajiID = " . tosql($gajiID, "Number") . ";";
                    } else {
                        $sSQL = "INSERT INTO staff_salary (staffID, base_salary, gajiID, type_gaji, amount, nett_salary, insertBy, insertDate) 
                              VALUES (" . tosql($staffID, "Number") . ", "
                            . tosql($gaji_pokok, "Text") . ", "
                            . tosql($gajiID, "Number") . ", "
                            . tosql($type_gaji, "Text") . ", "
                            . tosql($amount, "Text") . ", "
                            . tosql($gajiBersih, "Text") . ", "
                            . tosql($insertBy, "Text") . ", "
                            . tosql($insertDate, "Date") . ");";
                    }
                } elseif ($type_gaji == "Pendapatan") {
                    if ($amount <= 0) continue;

                    // Gaji Pokok (base_salary)
                    if ($gajiID == "2074") {
                        // If gaji pokok is updated, update base_salary directly
                        if ($amount > 0) {
                            $sSQL = "UPDATE staff_salary 
                             SET base_salary = " . tosql($amount, "Text") . ",
                                 updatedBy = " . tosql($updatedBy, "Text") . ",
                                 updatedDate = " . tosql($updatedDate, "Date") . "
                                WHERE staffID = " . tosql($staffID, "Number") . ";";

                            $rs = $conn->Execute($sSQL);
                            if (!$rs) {
                                die("SQL Error: " . $conn->ErrorMsg());
                            }
                            $gaji_pokok = $amount;
                        }
                    }

                    $existingEntry = dlookup("staff_salary", "ID", "staffID=$staffID AND gajiID=$gajiID");

                    if ($existingEntry) {
                        $sSQL = "UPDATE staff_salary 
                             SET amount = " . tosql($amount, "Text") . ",
                                 nett_salary = " . tosql($gajiBersih, "Text") . ",
                                 updatedBy = " . tosql($updatedBy, "Text") . ",
                                 updatedDate = " . tosql($updatedDate, "Date") . "
                             WHERE staffID = " . tosql($staffID, "Number") . "
                             AND gajiID = " . tosql($gajiID, "Number") . ";";
                    } else {
                        $sSQL = "INSERT INTO staff_salary (staffID, base_salary, gajiID, type_gaji, amount, nett_salary, insertBy, insertDate) 
                              VALUES (" . tosql($staffID, "Number") . ", "
                            . tosql($gaji_pokok, "Text") . ", "
                            . tosql($gajiID, "Number") . ", "
                            . tosql($type_gaji, "Text") . ", "
                            . tosql($amount, "Text") . ", "
                            . tosql($gajiBersih, "Text") . ", "
                            . tosql($insertBy, "Text") . ", "
                            . tosql($insertDate, "Date") . ");";
                    }
                }

                $rs = $conn->Execute($sSQL);
                if (!$rs) {
                    die("SQL Error: " . $conn->ErrorMsg());
                }
            }
        }
    }
}

if (isset($_POST['delete'])) {
    $recordID = intval($_POST['delete_id']);

    if ($recordID > 0) {
        $sSQL = "DELETE FROM staff_salary WHERE ID = " . tosql($recordID, "Number") . ";";
        $rs = $conn->Execute($sSQL);

        if (!$rs) {
            die("SQL Error: " . $conn->ErrorMsg());
        } else {
            echo '<script>alert("Rekod berjaya dipadam.");</script>';
            echo '<script>window.location.href = "?vw=staffIncome&pk=' . $pk . '";</script>';
            exit();
        }
    } else {
        echo '<script>alert("ID rekod tidak sah.");</script>';
    }
}

if (isset($_POST['save_salary'])) {
    $nettSalary = floatval($_POST['nett_salary']);

    if ($nettSalary > 0) {
        // Assuming staffID is available in the session or a variable
        $staffID = $pk; // Update this based on how you get staffID

        // Insert or update nett salary in the database
        $sSQL = "UPDATE staff_salary 
                 SET nett_salary = " . tosql($nettSalary, "Number") . "
                 WHERE staffID = " . tosql($staffID, "Number") . ";";

        $rs = $conn->Execute($sSQL);

        if (!$rs) {
            die("SQL Error: " . $conn->ErrorMsg());
        } else {
            echo '<script>alert("Gaji bersih berjaya disimpan!");</script>';
            echo '<script>window.location.href = "?vw=staffIncome&pk=' . $pk . '";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Gaji bersih tidak sah!");</script>';
    }
}


$sSQL = "select * from staff_salary	 
		 WHERE  staffID = " . tosql($pk, "Text") . " ";
$rs = &$conn->Execute($sSQL);

$sSQL2 = "SELECT a.*, b.*
          FROM users a
          INNER JOIN staff b ON a.staffID = b.staffID
          WHERE a.staffID = " . tosql($pk, "Text");
$rs1 = $conn->Execute($sSQL2);

$jabatan =  dlookup("general", "name", "ID=" . tosql($rs1->fields("jabatanID"), "Number"));
$jenis_gaji = dlookup("general", "type_gaji", "ID=" . tosql($gajiVal[$index]));
?>

<body>
    <?php
    print '
<h5 class="card-title">' . strtoupper($title) . '</h5>
<div class="table-responsive">
<form id="MyForm" name="salary" method="post" action="">
    <input type="hidden" name="action" value="Hitung">
    <input type="hidden" name="StartRec" value="' . $StartRec . '">
    <input type="hidden" name="by" value="' . $by . '">

    <table width="100%">
        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>Nama Staf :</td><td><b>' . $rs1->fields["name"] . '</b></td></tr>
        <tr><td width="20%">Nombor Staf :</td><td><b>' . $pk . '</b></td></tr>
        <tr><td>Pekerjaan :</td><td><b>' . $rs1->fields["job"] . '</b></td></tr>
        <tr><td>Jabatan :</td><td><b>' . $jabatan . '</b></td></tr>
        <tr><td colspan="2"><hr class="1px"></td></tr>';

    $groupID = get_session("Cookie_groupID");
    if ($groupID == 1 || $groupID == 2) {
        print '
    <tr><td colspan="2">
        <table width="100%">
            <tr>
                <!-- Jenis Potongan Column -->
                <td width="30%" valign="top">
                    <div class="checkbox-group">
                        <strong>Jenis Potongan (Statutori)</strong><br>';
        foreach ($gajiList as $index => $gajiType) {
            $jenis_gaji = dlookup("general", "type_gaji", "ID=" . tosql($gajiVal[$index]));
            if ($jenis_gaji == "Statutori") { // Ensure only Statutori is shown here
                $checked = isset($gajiID) && in_array($gajiVal[$index], (array)$gajiID) ? ' checked' : '';
                print '<div style="display: flex; align-items: center; gap: 1px; margin-bottom: 5px; width: 100%;">
                                    <label class="form-check-label" style="width: 70%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <input type="checkbox" class="form-check-input" name="gajiID[]" value="' . $gajiVal[$index] . '"' . $checked . '>
                                    ' . $gajiType . '</label>
                                    <input type="number" class="form-control" name="amount[' . $gajiVal[$index] . ']" placeholder="0" style="width: 100px;" step="0.01">
                                </div>';
            }
        }
        print '
                    </div>
                </td>

                <!-- Jenis Elaun Column -->
                <td width="30%" valign="top">
                    <div class="checkbox-group">
                        <strong>Jenis Pendapatan</strong><br>';
        foreach ($gajiList as $index => $gajiType) {
            $jenis_gaji = dlookup("general", "type_gaji", "ID=" . tosql($gajiVal[$index]));
            if ($jenis_gaji == "Pendapatan") { // Ensure only Pendapatan is shown here
                $checked = isset($gajiID) && in_array($gajiVal[$index], (array)$gajiID) ? ' checked' : '';
                print '<div style="display: flex; align-items: center; gap: 1px; margin-bottom: 5px; width: 100%;">
                                    <label class="form-check-label" style="width: 70%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <input type="checkbox" class="form-check-input" name="gajiID[]" value="' . $gajiVal[$index] . '"' . $checked . '>
                                    ' . $gajiType . '</label>
                                    <input type="number" class="form-control" name="amount[' . $gajiVal[$index] . ']" placeholder="0" style="width: 100px;" step="0.01">
                                </div>';
            }
        }
        print '
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>';
    }

    if (($IDName == 'admin') || ($IDName == 'superadmin')) {
        print '<tr>
        <td colspan="2" align="center">
            <button type="submit" name="action" value="Hitung" class="btn btn-primary" style="margin-bottom: 10px;">Kira</button>
        </td>
    </tr>';
    }

    print '</table>
</form>
</div>';

    print '</table>
 </form>
</div>';

    print '<table class="table table-striped table-sm">
<tr class="table-primary">
    <td nowrap align="center"><b>Bil</b></td>
    <td nowrap align="left"><b>Jenis</b></td>
    <td nowrap align="left"><b>Nama</b></td>
    <td nowrap align="right"><b>Amaun</b></td>
    <td nowrap align="center"><b></b></td> 
</tr>';
    $count = 1;
    $totalPotongan = 0;
    $totalElaun = 0;
    $gajiPokok = 0;

    // Display Potongan
    $rs->MoveFirst();
    while (!$rs->EOF) {
        $typeGaji = $rs->fields['type_gaji'];
        if ($typeGaji == 'Statutori') {
            $gajiID = $rs->fields['gajiID'];
            $gajiAmount = !empty($rs->fields['amount']) ? $rs->fields['amount'] : 0;
            $recordID = $rs->fields['ID'];

            if (!empty($gajiID)) {
                $gajiName = dlookup("general", "name", "ID=$gajiID");

                print '<tr>
                <td align="center">' . $count . '</td>
                <td align="left">Potongan</td>
                <td align="left">' . $gajiName . '</td>
                <td align="right">RP ' . number_format($gajiAmount, 2) . '</td>
                <td align="center">';
                if ($groupID == 1 || $groupID == 2) {
                    print '<form method="POST" action="">
                        <input type="hidden" name="delete_id" value="' . $recordID . '">
                        <button type="submit" class="btn btn-danger btn-sm" name="delete" value="Padam" onClick="return confirm(\'Adakah anda pasti untuk padam?\');">
                            <i class="fa fa-trash"></i> 
                        </button>
                    </form>';
                }
                print '</td>
            </tr>';
                $totalPotongan += $gajiAmount;
                $count++;
            }
        }
        $rs->MoveNext();
    }

    print '<tr class="table-secondary">
    <td colspan="10" align="center"><b></b></td>
</tr>';

    // Display Elaun
    $rs->MoveFirst();
    while (!$rs->EOF) {
        $typeGaji = $rs->fields['type_gaji'];
        $gajiID = $rs->fields['gajiID'];
        $gajiAmount = !empty($rs->fields['amount']) ? $rs->fields['amount'] : 0;
        $recordID = $rs->fields['ID'];

        if (!empty($gajiID)) {
            $gajiName = dlookup("general", "name", "ID=$gajiID");

            if ($gajiID == 2074) {
                // Gaji Pokok - Store separately
                $gajiPokok = $gajiAmount;
            } else if ($typeGaji == 'Pendapatan') {
                // Normal Allowances
                print '<tr>
                <td align="center">' . $count . '</td>
                <td align="left">Elaun</td>
                <td align="left">' . $gajiName . '</td>
                <td align="right">RP ' . number_format($gajiAmount, 2) . '</td>
                <td align="center">';
                if ($groupID == 1 || $groupID == 2) {
                    print '<form method="POST" action="">
                        <input type="hidden" name="delete_id" value="' . $recordID . '">
                        <button type="submit" class="btn btn-danger btn-sm" name="delete" value="Padam" onClick="return confirm(\'Adakah anda pasti untuk padam?\');">
                            <i class="fa fa-trash"></i> 
                        </button>
                    </form>';
                }
                print '</td>
            </tr>';
                $totalElaun += $gajiAmount;
                $count++;
            }
        }
        $rs->MoveNext();
    }

    // // Display Gaji Pokok Separately
    // if ($gajiPokok > 0) {
    //     print '<tr class="table-warning">
    //         <td align="center"><b>-</b></td>
    //         <td align="center"><b>Gaji Pokok</b></td>
    //         <td align="center"><b>RP ' . number_format($gajiPokok, 2) . '</b></td>
    //         <td align="center"><b>-</b></td>
    //     </tr>';
    // }


    if ($count == 1) {
        print '<tr><td colspan="5" align="center"><b>- Tiada Rekod -</b></td></tr>';
    }

    print '</table>';

    $totalGross = $gaji_pokok + $totalElaun;
    $gajiBersih = $totalGross - $totalPotongan;
    $groupID = get_session("Cookie_groupID");

    print '<div><hr class="1px"></div>';
    print '<div>Gaji Pokok : <b>RP ' . number_format($gaji_pokok, 2) . '</b></div>';
    print '<div>Jumlah Elaun : <b>RP ' . number_format($totalElaun, 2) . '</b></div>';
    print '<div>Gaji Kasar : <b>RP ' . number_format($totalGross, 2) . '</b></div>';
    print '<div>Jumlah Potongan : <b>RP ' . number_format($totalPotongan, 2) . '</b></div>';
    print '<div><hr class="1px"></div>';
    print '<div style="display: flex; align-items: center; gap: 10px;">';
    print '<div>Gaji Bersih : <b>RP ' . number_format($gajiBersih, 2) . '</b></div>';

    if (($groupID != 0 && $groupID != 99) && $gajiBersih > 0) {
        print '<form method="POST" action="" style="margin: 0;">
    <input type="hidden" name="nett_salary" value="' . $gajiBersih . '">
    <button type="submit" class="btn btn-success" name="save_salary">Simpan</button>
</form>';
    }
    print '</div>'; // Close flexbox div

    print '<div><hr class="1px"></div>';
    ?>
    <?php

    if ($groupID != 0 && $groupID != 99) {
        print '
    <tr>
        <td colspan="2" align="center">
            <div style="text-align: center;">
                <input type="button" name="batal" value="Kembali" class="btn btn-md btn-primary" 
                       onclick="Javascript:(window.location.href=\'?vw=staff&mn=919\')">
            </div>
        </td>
    </tr>';
    }
    ?>
</body>

</html>