<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename	: 	pin.php
 *          Date 		: 	22/03/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> 0) {
    print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
    exit;
}

$email = dlookup("users", "email", "userID=" . tosql($userID, "Text"));

function generateRandomString($length = 4)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$randomString = generateRandomString(4);

$sSQL     = "SHOW COLUMNS FROM users";
$rss     = $conn->Execute($sSQL);


if ($rss) {
    echo "Fields in users:\n";
    while (!$rss->EOF) {
        echo $rss->fields['Field'] . "\n";
        $rss->MoveNext();
    }
    $rss->Close();
} else {
    echo "Error retrieving fields from users\n";
}
print '<br><br>';

if ($action <> '') {
    $msg = '';
    $err = 0;

    // Get the user's current PIN from the database
    $userID = get_session("Cookie_userID");
    $sSQL = 'SELECT * FROM users WHERE userID = ' . tosql($userID, "Text");
    $rs = &$conn->Execute($sSQL);

    if ($rs && $rs->RecordCount() == 1) {
        $userPIN = $rs->fields['pin'];
    } else {
        $userPIN = NULL;
    }

    // If user has no PIN, only validate the new PIN fields
    if ($userPIN === NULL) {
        if ($newpassword == '') {
            $msg = "Tiada PIN terkini dimasukkan...!";
            $err = 4;
        } elseif (strlen($newpassword) < 6) {
            $msg = "Pastikan panjang PIN 6 aksara.";
            $err = 1;
        } elseif ($newpassword <> $newpassword1) {
            $msg = "Pengesahan PIN tidak sama.";
            $err = 6;
        } else {
            // Proceed with the update since there is no current PIN
            $to = $email;
            $subject = "Kata Laluan Sementara - iKOOP";
            $message = "Kata laluan sementara yang baru adalah " . $randomString . "";
            $headers = "Kata Laluan Sementara";

            // Hantar emel
            if (mail($to, $subject, $message, $headers)) {
                echo "<script>alert('Kod pengesahan telah dihantar ke emel anda.');</script>";
            } else {
                echo "<script>alert('Ralat: Kod pengesahan gagal dihantar ke emel.');</script>";
            }
            echo "<script>alert('$message');</script>";
            $sWhere = ' userID = ' . tosql($userID, "Text");
            $ssSQL = ' UPDATE users SET pin=' . tosql($newpassword, "Text") . ' WHERE ' . $sWhere;
            $rss = &$conn->Execute($ssSQL);

            // $updatedID = get_session('Cookie_userID');
            // $updatedBy = get_session("Cookie_userName");
            // $activity = "Kemaskini PIN";
            // activityLog($sSQL, $activity, $updatedID, $updatedBy);
        }
    } else {
        // If user has a current PIN, validate current password and new PIN
        if ($rs->fields(pin) == $password) {
            if ($password == '') {
                $msg = "Tiada PIN terkini dimasukkan...!";
                $err = 4;
            } elseif (strlen($newpassword) < 6) {
                $msg = "Pastikan panjang PIN 6 aksara.";
                $err = 1;
            } elseif ($password == $newpassword) {
                $msg = "PIN terkini tidak boleh sama dengan PIN yang baru.";
                $err = 2;
            } elseif ($newpassword <> $newpassword1) {
                $msg = "Pengesahan PIN tidak sama.";
                $err = 6;
            } else {
                if ($password == $userPIN) {

                    $to = $email;
                    $subject = "Kata Laluan Sementara - iKOOP";
                    $message = "Kata laluan sementara yang baru adalah " . $randomString . "";
                    $headers = "Kata Laluan Sementara";

                    // Hantar emel
                    if (mail($to, $subject, $message, $headers)) {
                        echo "<script>alert('Kod pengesahan telah dihantar ke emel anda.');</script>";
                    } else {
                        echo "<script>alert('Ralat: Kod pengesahan gagal dihantar ke emel.');</script>";
                    }
                    echo "<script>alert('$message');</script>";
                    $sWhere = ' userID = ' . tosql($userID, "Text");
                    $ssSQL = ' UPDATE users SET pin=' . tosql($newpassword, "Text") . ' WHERE ' . $sWhere;
                    $rss = &$conn->Execute($ssSQL);

                    // $updatedBy = get_session("Cookie_userName");
                    // $activity = "Kemaskini PIN";
                    // activityLog($ssSQL, $activity, $userID, $updatedBy);
                } else {
                    $msg = "PIN terkini adalah salah...!";
                    $err = 3;
                }
            }
        } else {
            $msg = "PIN lama salah!";
            $err = 5;
        }
    }

    if ($msg <> '') {
        alert("$msg");
    } else {
        alert("Kemaskini PIN berjaya dikemaskinikan");
        gopage("?vw=pin&mn=4", 1000);
    }
}


$sSQL = "SELECT pin FROM users WHERE userID = '" . get_session("Cookie_userID") . "'";
$rs = &$conn->Execute($sSQL);
?>
<!--- Begin : Display pin ------------------------------------------------------------------------->
<h5 class="card-title"><i class="mdi mdi-lock"></i>&nbsp;KEMASKINI PIN KESELAMATAN</h5>
<hr>

<form name="pin" action="?vw=pin&mn=<?php echo $mn; ?>" method="post">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
    <div class="table-responsive bg-light">
        <?
        if ($rs->fields(pin) <> NULL) {
        ?>
            <div class="row m-2">
                <label class="col-md-2 col-form-label">PIN Lama</label>
                <div class="col-md-10">
                    <div class="input-group">
                        <div style="position: relative; display: inline-block;">
                            <input id="password1" type="password" class="form-controlx" placeholder="Maksimum 6 Aksara" name="password" size="20" maxlength="6">
                            <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                <a id="eyeIcon1" href="#" onclick="togglePassword(1)">
                                    <i id="eyeIconInner1" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?
        }
        ?>
        <div class="row m-2">
            <label class="col-md-2 col-form-label">PIN Baru<br></label>
            <div class="col-md-10">
                <div class="input-group">
                    <div style="position: relative; display: inline-block;">
                        <input id="password2" type="password" class="form-controlx" placeholder="Maksimum 6 Aksara" name="newpassword" size="20" maxlength="6">
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
            <label class="col-md-2 col-form-label">Pengesahan PIN Baru</label>
            <div class="col-md-10">
                <div class="input-group">
                    <div style="position: relative; display: inline-block;">
                        <input id="password3" type="password" class="form-controlx" placeholder="Maksimum 6 Aksara" name="newpassword1" size="20" maxlength="6">
                        <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                            <a id="eyeIcon3" href="#" onclick="togglePassword(3)">
                                <i id="eyeIconInner3" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?
        if ($Kemaskini <> "") {
        ?>
            <div class="row m-2">
                <label class="col-md-2 col-form-label">Nombor OTP</label>
                <div class="col-md-10">
                    <div class="input-group">
                        <div style="position: relative; display: inline-block;">
                            <form action="" method="post">
                                <label for="code">Masukkan Nombor OTP : </label>
                                <input type="text" class="form-controlx" id="code" name="code" maxlength="6" required>
                                <button type="submit" class="btn btn-primary">Hantar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?
        } else {
        ?>
            <div class="row m-2 mt-4 mb-4">
                <label class="col-md-2 col-form-label"></label>
                <div class="col-md-5">
                    <input type="submit" name="action" class="btn btn-primary w-md waves-effect waves-light" value="Kemaskini">
                </div>
            </div>
        <?
        }
        ?>
    </div>
</form>


<!--- End   : Display pin ------------------------------------------------------------------------->
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