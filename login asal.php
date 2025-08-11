<?
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	login.php
*          Date 		: 	12/09/2003
*********************************************************************************/
include("common.php");	
include("koperasiQry.php");
$continue = get_param("continue");
//print '['.$action.'|'.$username.'|'.$password;
if ($action) {
	$err = 0;
	if ($username <> "" && $password <> "") {
		$encryptpwd = strtoupper(md5($password));
		$GetUser = ctVerifyUser($username, $encryptpwd);
		if ($GetUser->RowCount() == 1) {
			if ($GetUser->fields(isActive) == 1) {
				setcookie ("Cookie_userID",		$GetUser->fields(userID),time()+3);
/*				setcookie ("Cookie_userID",		$GetUser->fields(userID),	time()+36000);
				setcookie ("Cookie_userName",	$GetUser->fields(loginID),	time()+36000);
				setcookie ("Cookie_fullName",	$GetUser->fields(name),		time()+36000);
				setcookie ("Cookie_groupID",	$GetUser->fields(groupID),	time()+36000);
				setcookie ("Cookie_groupName",	$groupList[array_search($GetUser->fields(groupID),$groupVal)],	time()+36000);
*/				set_session ("Cookie_userID",		$GetUser->fields(userID));
				set_session ("Cookie_userName",	$GetUser->fields(loginID));
				set_session ("Cookie_fullName",	$GetUser->fields(name));
				set_session ("Cookie_groupID",	$GetUser->fields(groupID));
				set_session ("Cookie_groupName",	$groupList[array_search($GetUser->fields(groupID),$groupVal)]);
			} else {
				$err = 1;
			}
		} else {
			$err = 2;
		}
	} else {
		$err = 3;
	}
	print '<script>';
	if ($err <> 0) {
			print 'window.location = "page.php?action=login&error='.$err.'";';
	} else {
//		if ($continue <> "")
			print 'top.location = "index.php";';
//		else
//			print 'window.location = "memberApply.php?action=unknown";';
	}	
	print '</script>';
}
?>
