


<?php

/*********************************************************************************
 *          Project       :   iKOOP.com.my
 *          Filename      :   settingcoop.php
 *          Date          :   19/6/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$ssSQL = "SELECT * FROM setup WHERE setupID = 1";
$rss = &$conn->Execute($ssSQL);

$name = $rss->fields['name'];
$address1 = $rss->fields['address1'];
$address2 = $rss->fields['address2'];
$address3 = $rss->fields['address3'];
$address4 = $rss->fields['address4'];
$noPhone = $rss->fields['noPhone'];
$email = $rss->fields['email'];
$koperasiID = $rss->fields['koperasiID'];

if (get_session("Cookie_groupID") <> 2  or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

if ($action <> '') {
    $name = $_POST['name'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $address3 = $_POST['address3'];
    $address4 = $_POST['address4'];
    $noPhone = $_POST['noPhone'];
    $email = $_POST['email'];

    $sSQL = "UPDATE setup SET ";
    $sSQL .= "name= '" . $name . "', ";
    $sSQL .= "address1= '" . $address1 . "', ";
    $sSQL .= "address2= '" . $address2 . "', ";
    $sSQL .= "address3= '" . $address3 . "', ";
    $sSQL .= "address4= '" . $address4 . "', ";
    $sSQL .= "noPhone= '" . $noPhone . "', ";
    $sSQL .= "email= '" .  $email . "' ";
    $sSQL .= "WHERE setupID= 1";

    $rs = &$conn->Execute($sSQL);

    $updatedID  = get_session('Cookie_userID');
    $updatedBy  = get_session("Cookie_userName");
    $activity = "Kemaskini Maklumat Koperasi";
    activityLog($sSQL, $activity, $updatedID, $updatedBy, 9);

    alert('Maklumat Koperasi Telah Berjaya Dikemaskini.');
    gopage('?vw=settingcoop&mn=901', 1000);
}

if (!isset($pic)) $pic = dlookup("setup", "logo", "setupID=" . tosql(1, "Text"));
$Gambar = "upload_images/" . $pic;
?>
<!--- Begin: Display Profile ------------------------------------------------------------------------->
<h5 class="card-title"><i class="ti ti-settings"></i>&nbsp;KEMASKINI MAKLUMAT KOPERASI</h5>
<hr>
<form name="settingcoop" action="?vw=settingcoop&mn=<?php echo $mn; ?>" method="post">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
    <div class="table-responsive bg-light">
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Logo Koperasi</label>
            <div class="col-md-9">
                <div class="container" style="display: inline-block; position: relative;">
                    <div style="text-align: left; position: relative;">
                        <?php
                        if (isset($pic) && !empty($pic)) {
                            echo '<img id="elImage" src="' . $Gambar . '" style="height: 100px; width: 100px;" alt="Logo Koperasi">';
                        }
                        ?>
                        &nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-outline-primary" name="GetPicture" value="Muat Naik" onclick="window.location.href='?vw=uploadwinlogo&mn=901'" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Nama Koperasi</label>
            <div class="col-md-9">
                <input id="name" type="text" class="form-control" name="name" value="<?= $name ?>" maxlength="255">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Alamat 1</label>
            <div class="col-md-9">
                <input id="address1" type="text" class="form-control" name="address1" value="<?= $address1 ?>" maxlength="100">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Alamat 2</label>
            <div class="col-md-9">
                <input id="address2" type="text" class="form-control" name="address2" value="<?= $address2 ?>" maxlength="100">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Alamat 3</label>
            <div class="col-md-9">
                <input id="address3" type="text" class="form-control" name="address3" value="<?= $address3 ?>" maxlength="100">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Alamat 4</label>
            <div class="col-md-9">
                <input id="address4" type="text" class="form-control" name="address4" value="<?= $address4 ?>" maxlength="100">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Nombor Telefon Koperasi</label>
            <div class="col-md-9">
                <input id="noPhone" type="text" class="form-control" name="noPhone" value="<?= $noPhone ?>" maxlength="15">
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-3 col-form-label">Emel Koperasi</label>
            <div class="col-md-9">
                <input id="email" type="email" class="form-control" name="email" value="<?= $email ?>" maxlength="50">
            </div>
        </div>
        <div class="row m-2 mt-4 mb-4">
            <label class="col-md-3 col-form-label"></label>
            <div class="col-md-5">
                <input type="submit" name="action" class="btn btn-primary w-md waves-effect waves-light" value="Kemaskini">
            </div>
        </div>
    </div>
</form>

<?php
include("footer.php");
?>