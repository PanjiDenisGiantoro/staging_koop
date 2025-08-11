<?
include "common.php";
unset($_SESSION);


session_start();

if ($_POST['txtUserID']) {
	$name = $_POST['txtUserID'];
	$katalaluan = $_POST['txtPassword'];
	$sql = "select menuID, userID, passwd, parentMenuID FROM `kandungan` WHERE userID = '".$name."' AND passwd = '".$katalaluan."'";
	//$conn->debug = true;
	$rs = $conn->Execute($sql);
	if ($rs->RecordCount()>0) {
		$user = $rs->fields("userID");
		$access = $rs->fields("kebenaran");
		$id=$rs->fields("menuID");
		$p=$rs->fields("parentMenuID");
		$_SESSION['user'] = $name;
		$_SESSION['iduser']=$id;
		$_SESSION['parent']=$p;

		//$_SESSION['access']=$access;
//		if ($access=="0") {
			print '<script>	';
			print '	window.location.href = "indexAdmin.php";';
			print '</script>';
		//	}
/*	elseif ($access=="1")
			print '<script>	';
			print '	window.location.href = "main_admin.php";';
			print '</script>';*/
	} else
	$mesej = "Invalid Password!";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Login Page</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="default.css">
</head>

<body>
<form action="admin.php" method="post">
  <table width="384" border="0" cellpadding="0" cellspacing="0" align="center">
    <!--DWLayoutTable-->
    <tr bgcolor="#FFFFFF" class="contentH"> 
      <td height="26" colspan="3" valign="top"><div align="center"><font color="#000099" size="2"><strong>DCL 
          Login</strong></font></div></td>
    </tr>
    <tr> 
      <td width="94" height="30">&nbsp;</td>
      <td width="197">&nbsp;</td>
      <td width="93">&nbsp;</td>
    </tr>
    <tr> 
      <td height="124"></td>
      <td valign="top"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#999999" class="contentD">
          <!--DWLayoutTable-->
          <tr> 
            <td width="194" height="33" valign="top" bgcolor="#CCFFFF"><div align="left"><strong>:: 
                UserID</strong></div></td>
          </tr>
          <tr> 
            <td height="31" valign="top" bgcolor="#CCFFFF"> <div align="left"> 
                <input name="txtUserID" type="text" id="txtUserID">
              </div></td>
          </tr>
          <tr> 
            <td height="26" valign="top" bgcolor="#CCFFFF"><div align="left"><strong>:: 
                Password</strong></div></td>
          </tr>
          <tr> 
            <td height="29" valign="top" bgcolor="#CCFFFF"> <div align="left"> 
                <input name="txtPassword" type="password" id="txtPassword">
              </div></td>
          </tr>
        </table></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td height="12"></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24"></td>
      <td valign="top"><div align="center"> 
          <input type="submit" name="Submit" value="Login" class="but">
        </div></td>
      <td></td>
    </tr>
    <tr>
      <td height="18"></td>
      <td></td>
      <td></td>
    </tr>
    <tr class="contentD"> 
      <td height="18" colspan="3" valign="top"><div align="center"><font color="red"><? echo $mesej; ?></font></div></td>
    </tr>
    <tr> 
      <td height="13"></td>
      <td></td>
      <td></td>
    </tr>
    <!--tr class="contentD"> 
      <td height="21" colspan="3" valign="top"><div align="center"><a href="forgot.php">Forgot 
          Password?</a></div></td>
    </tr-->
  </table>
</form>
</body>

</html>

