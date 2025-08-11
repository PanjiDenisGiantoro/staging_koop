<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	logout.php
*          Date 		: 	12/09/2003
*********************************************************************************/
require_once ("common.php");
	setcookie ("Cookie_userID","");
	set_session ("Cookie_userID",	"");
	set_session ("Cookie_userName",	"");
	set_session ("Cookie_fullName",	"");
	set_session ("Cookie_groupID",	"");
	set_session ("Cookie_groupName","");
	set_session ("Cookie_koperasiID","");
print	' 	<script>parent.location.href = "index.php";</script>';
?>