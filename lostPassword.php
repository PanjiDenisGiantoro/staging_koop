<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	lostPassword.php
*          Date 		: 	05/10/2022
*********************************************************************************/
include ("header.php");
$title	= "Lupa Kata Laluan";
$sFileName = "?vw=lostPassword";
$sActionFileName= "index.php?page=login&error=";


print '<div align="center" class="card-body">
<p align="center"><h5 class="card-title"><i class="mdi mdi-onepassword"></i>&nbsp;<b>LUPA KATA LALUAN?</b></h5></p>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai email dan mobileNo dari input form
    $email = htmlspecialchars($_POST['email']);
    $mobileNo = htmlspecialchars($_POST['mobileNo']);

    $pk = dlookup("users", "userID", "email=" . tosql($email, "Text"));
    $pk2= dlookup("userdetails", "userID", "mobileNo=" . tosql($mobileNo, "Text"));

    if ($pk == $pk2) {
   
    //function random digit
    function generateRandomString($length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    //panggil function
    $randomString1 = generateRandomString();

    //update password baru menggunakan random word
    $sSQL = '';
	$sWhere = "";
	$sWhere = ' userID = ' . tosql($pk,"Text");
	$sSQL	= ' UPDATE users SET ' .
	          ' password=' . tosql(md5("$randomString1"), "Text") ;
	$sSQL .= ' WHERE ' . $sWhere;
	$rs = &$conn->Execute($sSQL);

    $to = $email;
    $subject = "Kata Laluan Sementara - iKOOP";
    $message = "Kata laluan sementara yang baru adalah ".$randomString1."";
    $headers = "Kata Laluan Sementara";

    // Mengirim email
    mail($to, $subject, $message, $headers);

    alert("Kata laluan telah ditukar kepada kata laluan baru. Sila semak emel anda untuk dapatkan kata laluan baru.");
    gopage("$sActionFileName",1000);

    } else {
        alert("Sila masukkan emel dan nombor telefon yang telah didaftarkan ke dalam sistem.");
    }
}

print '
<form name="MyForm" action="'.$sFileName.'" method=post>
<div class="card bg-light text-black">
    <div class="card-body">
        <div>Masukkan emel yang telah didaftarkan ke dalam sistem iKOOP.</div>
        <div><input type="text" class="form-controlx" placeholder="xxxxxxxxxxx@gmail.com" size="50" maxlength="50" value="'.$email.'" name="email" id="email"/></div>
        &nbsp;
        <div>Masukkan nombor telefon yang telah didaftarkan ke dalam sistem iKOOP.</div>
        <div><input type="text" class="form-controlx" placeholder="6013xxxxxxx" size="30" maxlength="50" value="'.$mobileNo.'" name="mobileNo" id="mobileNo"/></div>
        &nbsp;
        <div><input type="submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Hantar"></div>
    </div>
</div>';


include("footer.php");
