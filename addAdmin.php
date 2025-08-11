<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	addAdmin.php
 *          Date 		: 	04/12/2018
 *********************************************************************************/

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
include("forms.php");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>parent.location.href = "index.php";</script>';
}

$sFileName		= "?vw=addAdmin&mn=$mn";
$sActionFileName = "?vw=addAdmin&mn=$mn";
$title     		= "Tambah Kakitangan Pengurusan Sistem";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$a = 1;
$a++;
$FormLabel[$a]   	= "* Nama Penuh";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Id Log Masuk";
$FormElement[$a] 	= "loginID";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "* Kata Laluan";
$FormElement[$a] 	= "hidden";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";

$a++;
$FormLabel[$a]   	= "* Kenal pasti Kata Laluan";
$FormElement[$a] 	= "hidden";
$FormType[$a]	  	= "";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= "";
$FormSize[$a]    	= "";
$FormLength[$a]  	= "";

$a++;
$FormLabel[$a]   	= "Emel";
$FormElement[$a] 	= "email";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckEmailAddress);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* Jenis Kumpulan";
$FormElement[$a] 	= "groupID";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $groupAList;
$FormDataValue[$a]	= $groupAVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a = $a + 1;
$FormLabel[$a]   	= "* Nombor Anggota <br>(Staf Anggota)";
$FormElement[$a] 	= "sellMemberID";
$FormType[$a]	  	= "textx";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= ",jkh&nbsp;";
$FormElement[$a] 	= "Bukan anggota";
$FormType[$a]	  	= "checkbox";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {

	if (strlen($password) < 6) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Kata Laluan mesti sekurang-kurangnya 6 huruf.</strong> 
                                                </div>';
	}

	if ($password <> $password1) {
		array_push($strErrMsg, "password");
		array_push($strErrMsg, "password1");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Kata Laluan mesti sama dengan kenal pasti Kata Laluan.</strong> 
                                                </div>';
	}
	$GetLogin = ctLogin($loginID);
	if ($GetLogin->RowCount() == 1) {
		array_push($strErrMsg, "loginID");
		print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>ID Pengguna sudah wujud. Sila pilih yang ID Pengguna lain</strong> 
                                                </div>';
	}
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$$FormElement[$i],
				$FormCheck[$i][$j],
				$i
			);
		}
	}

	if (!$anggota) {
		if ($sellMemberID == "") {
			array_push($strErrMsg, "sellMemberID");
			print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Masukkan no. anggota</strong> 
                                                </div>';
		} else {
			if (dlookup("userdetails", "userID", "memberID=" . tosql($sellMemberID, "Text")) == "") {
				array_push($strErrMsg, 'sellMemberID');
				print '- <font class=redText>Nombor Anggota - ' . $sellMemberID . ' tidak sah...!</font><br>';
				$sellUserID = "";
				$sellUserName = "";
			} else {
				$sellUserID = dlookup("userdetails", "userID", "memberID=" . tosql($sellMemberID, "Text"));
				$sellUserName = dlookup("users", "name", "userID=" . tosql($sellUserID, "Text"));
			}
		}
	} else {
		$sellMemberID = '';
	}

	//--- End   : Call function FormValidation ---  
	$dateBirth = substr($dateBirth, 6, 4) . '-' . substr($dateBirth, 3, 2) . '-' . substr($dateBirth, 0, 2);
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		//$userID = strtoupper(uniqid(rand(),1)); 
		$sSQLi = "";
		$sSQLi	= "SELECT max( CAST(substring(  userID  , 2 ) AS SIGNED INTEGER )) AS new
					FROM `users`
					WHERE userID LIKE 'a%'";
		$rsi = &$conn->Execute($sSQLi);
		$new = $rsi->fields('new');
		$userID = 'a' . ++$new;
		$password = strtoupper(md5($password));
		$sSQL = "";
		$sSQL	= "INSERT INTO users (" .
			"userID, " .
			"loginID, " .
			"password, " .
			"email, " .
			"name, " .
			"groupID, " .
			"isActive, " .
			"memberID, " .
			"applyDate)" .
			" VALUES (" .
			tosql($userID, "Text") . ", " .
			tosql($loginID, "Text") . ", " .
			tosql($password, "Text") . ", " .
			tosql($email, "Text") . ", " .
			tosql($name, "Text") . ", " .
			tosql($groupID, "Text") . ", " .
			tosql(1, "Number") . ", " .
			tosql($sellMemberID, "Text") . ", " .
			tosql($applyDate, "Text") . ") ";


		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Tambah Kakitangan Baru';
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 9);

		print '<script>
					alert ("Maklumat berikut telah mendaftar sebagai admin sistem.");
					window.location.href = "' . $sActionFileName . '";			
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
?>
<div class="table-responsive">
	<h5 class="card-title"><?php echo strtoupper($title); ?><br><small>SILA MASUKKAN MAKLUMAT KAKITANGAN PENGURUSAN SISTEM</small></h5>

	<form name="MyForm" action="<? print $sFileName; ?>" method=post>

		<table border="0" cellpadding="3" cellspacing="6" width="100%" align="center">
			<style>
				input::-ms-reveal,
				input::-ms-clear {
					display: none;
				}
			</style>
			<?php
			//--- Begin : Looping to display label -------------------------------------------------------------
			for ($i = 1; $i <= count($FormLabel); $i++) {
				if ($cnt == 1) print '<tr valign="top">';
				print '<td class="Data" align="left">' . $FormLabel[$i];
				print '</td>';
				if (in_array($FormElement[$i], $strErrMsg))
					print '<td class="errdata">';
				else
					print '<td class="Data">';
				if ($i <> 1) print '</td><td class="Data"><td class="Data">';
				//--- Begin : Call function FormEntry ---------------------------------------------------------  
				$strFormValue = $$FormElement[$i];
				FormEntry(
					$FormLabel[$i],
					$FormElement[$i],
					$FormType[$i],
					$strFormValue,
					$FormData[$i],
					$FormDataValue[$i],
					$FormSize[$i],
					$FormLength[$i]
				);

				if ($i == 4) {
					print '
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
				</div>';
				}

				if ($i == 5) {
					print '
					<div class="col-md-10">
						<div class="input-group">
						<div style="position: relative; display: inline-block;">
							<input id="password2" type="password" class="form-controlx" placeholder="Minimum 6 Aksara" name="password1" size="20" maxlength="16">
							<div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
								<a id="eyeIcon2" href="#" onclick="togglePassword(2)" >
									<i id="eyeIconInner2" class="mdi mdi-eye-off-outline" aria-hidden="true"></i>
								</a>
		
							</div>
						</div>
						</div>
					</div>';
				}
				if ($i == 8) {
					print '
		<input type="button" class="btn btn-info btn-sm waves-effect waves-light" value="Pilih" onclick="window.open(\'selToMember.php?refer=d\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">
		<input type="text" name="sellUserName" class="form-controlx" value="' . $sellUserName . '" onfocus="this.blur()" size="50">';
				}

				//--- End   : Call function FormEntry ---------------------------------------------------------  
				print '</td>';
				if ($cnt == 0) print '</tr>';
			}
			?>
			<tr>
				<td class="Data" align="left">Bukan Anggota</td>
				<td class="Data"></td>
				<td class="Data">&nbsp;</td>
				<td class="Data"><input type="checkbox" class="form-check-input" name="anggota"></td>
			</tr>
			<tr>
				<td colspan="4" align=left class="Data">&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="4" align=center class="Data">
					<input type=Submit class="btn btn-md w-sm btn-primary" name=SubmitForm value="Simpan">
					<!-- <input type=Reset class="btn btn-sm btn-secondary" name=ResetForm value="Isi semula"> -->
				</td>
			</tr>
			<tr>
				<td colspan="4" align=center class="Data">&nbsp;
				</td>
			</tr>
		</table>

	</form>
</div>
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