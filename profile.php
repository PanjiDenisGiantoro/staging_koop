<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	profile.php
 *          Date 		: 	22/03/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
    exit;
}


if ($action <> '') {
    $msg = '';
    $err = 0;
    if ($password <> '') {
        if ($newpassword <> $newpassword1 || strlen($newpassword) < 6) {
            $msg = "Pastikan panjang Kata laluan melebihi 6 aksara";
            $err = 1;
        } else {
            if ($password == $newpassword) {
                $msg = "Kata laluan terkini tidak boleh sama dengan Kata laluan yang baru";
                $err = 2;
            } else {
                $encryptpwd = strtoupper(md5($password));
                $GetUser = ctVerifyUser(get_session("Cookie_userName"), $encryptpwd);
                if ($GetUser->RowCount() == 1) {
                    $sWhere = ' loginID = ' . tosql(get_session("Cookie_userName"), "Text");
                    $sSQL    = ' UPDATE users SET ' .
                        ' password=' . tosql(strtoupper(md5($newpassword)), "Text");
                    $sSQL .= ' WHERE ' . $sWhere;
                    $rs = &$conn->Execute($sSQL);
                    $updatedID     = get_session('Cookie_userID');
                    $updatedBy     = get_session("Cookie_userName");
                    $activity = "Tukar Kata Laluan - " . get_session('Cookie_userID');
                    activityLog($sSQL, $activity, $updatedID, $updatedBy, 9);
                } else {
                    $msg = "Kata laluan terkini adalah salah...!";
                    $err = 3;
                }
            }
        }
    } else {
        $msg = "Tiada Kata laluan terkini dimasukkan...!";
        $err = 4;
    }

    if ($msg <> '') {

        alert("$msg");

        /*
		print '<script>alert("' . $msg . '");
					</script>'; */
    } else {
        alert("Tukar kata laluan berjaya dikemaskinikan");
        gopage("index.php", 1000);
        /*
		print '<script>alert("Tukar kata laluan berjaya dikemaskinikan");
						//window.location.href = "index.php";
					</script>'; */
    }
}
?>
<!--- Begin : Display Profile ------------------------------------------------------------------------->
<h5 class="card-title"><i class="mdi mdi-key"></i>&nbsp;TUKAR KATA LALUAN</h5>
<hr>

<form name="profile" action="?vw=profile&mn=<?php echo $mn; ?>" method="post">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
    <div class="table-responsive bg-light">
        <div class="row m-2 mt-4">
            <label class="col-md-2 col-form-label">Id Pengguna</label>
            <label class="col-md-10">
                <b><? print get_session("Cookie_userName"); ?></b>
            </label>
        </div>
        <!-- <div class="row m-2">
                    <label class="col-md-2 col-form-label">Nama</label>
                    <label class="col-md-10">
                    <b><? print get_session('Cookie_fullName'); ?></b>
                    </label>
                </div> -->
        <div class="row m-2">
            <label class="col-md-2 col-form-label">Kata laluan Semasa</label>
            <div class="col-md-10">
                <div class="input-group">
                    <div style="position: relative; display: inline-block;">
                        <input id="password1" type="password" class="form-controlx" placeholder="Minimum 6 Aksara" name="password" size="20" maxlength="16">
                        <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                            <a id="eyeIcon1" href="#" onclick="togglePassword(1)">
                                <i id="eyeIconInner1" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-2 col-form-label">Kata laluan Baru<br></label>
            <div class="col-md-10">
                <div class="input-group">
                    <div style="position: relative; display: inline-block;">
                        <input id="password2" type="password" class="form-controlx" placeholder="Minimum 6 Aksara" name="newpassword" size="20" maxlength="16">
                        <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                            <a id="eyeIcon2" href="#" onclick="togglePassword(2)">
                                <i id="eyeIconInner2" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-2">
            <label class="col-md-2 col-form-label">Pengesahan Kata laluan Baru</label>
            <div class="col-md-10">
                <div class="input-group">
                    <div style="position: relative; display: inline-block;">
                        <input id="password3" type="password" class="form-controlx" placeholder="Minimum 6 Aksara" name="newpassword1" size="20" maxlength="16">
                        <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                            <a id="eyeIcon3" href="#" onclick="togglePassword(3)">
                                <i id="eyeIconInner3" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-2 mt-4 mb-4">

            <label class="col-md-2 col-form-label"></label>
            <div class="col-md-5">
                <input type="submit" name="action" class="btn btn-primary w-md waves-effect waves-light" value="Kemaskini">
                <!-- <input type="reset" name="action"  class="btn btn-secondary w-md waves-effect waves-light" value="Isi semula"> -->
            </div>

        </div>

        <!--<table class="table">
<tr class="table-success">
    <td width="20%">ID Pengguna</td>
    <td>:</td>
    <td><b><? print get_session("Cookie_userName"); ?></b></td>
    <td></td>
</tr>							
<tr>
    <td>Nama</td>
    <td>:</td>
    <td><b><? print get_session('Cookie_fullName'); ?></b></td>
    <td></td>
</tr>							
<tr>
    <td>Kata laluan Terkini</td>
    <td>:</td>
    <td><input type="password" class="form-controlx" name="password" size="20" maxlength="16"></td>
    <td></td>
</tr>		
<tr>
    <td>Kata laluan Baru<br><b>(MINIMUM 6 AKSARA)</b></td>
    <td>:</td>
    <td><input type="password" class="form-controlx" name="newpassword" size="20" maxlength="16"></td>
    <td></td>
</tr>		
<tr>
    <td>Pastikan Kata laluan Baru</td>
    <td>:</td>
    <td><input type="password" class="form-controlx" name="newpassword1" size="20" maxlength="16"></td>
    <td></td>
</tr>		
<tr>
    <td></td>
    <td></td>
    <td colspan="2" align="left">
    <input type="submit" name="action" class="btn btn-primary w-md waves-effect waves-light" value="Kemaskini">
    <input type="reset" name="action"  class="btn btn-primary w-md waves-effect waves-light" value="Isi semula">    
    </td>
</tr>							
</table-->
    </div>
</form>


<!--- End   : Display Profile ------------------------------------------------------------------------->
<?php
include("footer.php");

print '
<script language="JavaScript">
    function togglePassword(index) {
        var passwordInput = document.getElementById("password" + index);
        var eyeIconInner = document.getElementById("eyeIconInner" + index);

        // Toggle the password field visibility
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIconInner.classList.remove("mdi-eye-off-outline");
            eyeIconInner.classList.add("mdi-eye-outline");
        } else {
            passwordInput.type = "password";
            eyeIconInner.classList.remove("mdi-eye-outline");
            eyeIconInner.classList.add("mdi-eye-off-outline");
        }
    }
</script>';
?>