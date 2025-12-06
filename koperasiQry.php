<?php
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	koperasiQry.php
 *          Date 		: 	06/12/2018
 *********************************************************************************/
function ctVerifyUser($login, $pwd)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere .= "loginID like binary " . tosql($login, "Text") . " and ";
	$sWhere .= "password = " . tosql($pwd, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM users";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : general.php
function ctGeneral($q, $cat)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = "category = " . tosql($cat, "Text");
	if ($q <> "") 	$sWhere .= " and name like " . tosql($q . "%", "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM general";
	$sSQL = $sSQL . $sWhere . ' ORDER BY code,ID';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctGeneralACC($q, $cat)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = "category = " . tosql($cat, "Text");
	if ($q <> "") 	$sWhere .= " and name like " . tosql($q . "%", "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM generalacc";
	$sSQL = $sSQL . $sWhere . ' ORDER BY code, ID';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctGeneralACC1($q, $cat)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere =  "name != BINARY UPPER(name) AND category = " . tosql($cat, "Text");
	if ($q <> "") 	$sWhere .= " and name like " . tosql($q . "%", "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM generalacc";
	$sSQL = $sSQL . $sWhere . ' order by CAST( code AS SIGNED INTEGER ) ASC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : trans.php
function ctTransaction($q, $yymm, $status)
{
	$conn->debug = 1;
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " docNo like " . tosql($q . "%", "Text") . " AND ";
	}
	$sWhere .= " yrmth = " . tosql($yymm, "Text");
	$sWhere .= " AND status = " . tosql($status, "Number");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM transaction";
	$sSQL = $sSQL . $sWhere . ' ORDER BY ID DESC, yrmth';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : trans.php
function ctTransactionID($id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($id <> "ALL") {
		$sWhere = " ID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 * FROM transaction";
	$sSQL = $sSQL . $sWhere . ' ORDER BY ID DESC, yrmth';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctTransactionCode($q, $yymm, $status, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.deductID = B.ID  ";
	if ($id <> "ALL") {
		$sWhere .= " AND A.deductID = " . tosql($id, "Number");
	}
	if ($q <> "") {
		$sWhere = " A.docNo like " . tosql($q . "%", "Text") . " AND ";
	}
	$sWhere .= " AND A.yrmth = " . tosql($yymm, "Text");
	$sWhere .= " AND A.status = " . tosql($status, "Number");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT A.*, B.*
			 FROM 	transaction A, general B";
	$sSQL = $sSQL . $sWhere . ' ORDER  BY A.ID DESC, A.yrmth';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctTransactionCodeWajib($q, $yymm, $status, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.deductID = B.ID  ";
	if ($id <> "ALL") {
		$sWhere .= " AND A.deductID = " . tosql($id, "Number");
	}
	if ($q <> "") {
		$sWhere = " A.docNo like " . tosql($q . "%", "Text") . " AND ";
	}
	$sWhere .= " AND A.yrmth = " . tosql($yymm, "Text");
	$sWhere .= " AND A.status = " . tosql($status, "Number");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT A.*, B.*
			 FROM 	yuran A, general B";
	$sSQL = $sSQL . $sWhere . ' ORDER  BY A.ID DESC, A.yrmth';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by	:	selMember.php  
function ctUserDept($userid, $deptid)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " a.userID = b.userID and a.status = '1' ";
	if ($userid == "ALL") {
		$sWhere .= " and a.departmentID = " . tosql($deptid, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	} else {
		$sWhere .= " and a.userID = " . tosql($userid, "Number");
		$sWhere .= " and a.departmentID = " . tosql($deptid, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT a.userID, a.memberID, b.name, a.newIC, a.oldIC, a.unitShare
			FROM userdetails a, users b ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY  name';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : member.php
function ctMember($q, $id)
{
	//$conn->debug =1;
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " name like " . tosql($q . "%", "Text");
		//		$sWhere .= " OR memberID like " . tosql($q."%","Text");
		//		$sWhere .= " OR newIC like " . tosql($q."%","Text") ;
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " userID = " . tosql($id, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "	SELECT	* FROM users ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberAll($q)
{
	global $conn;
	$sSQL = "";
	$sWhere = " a.userID = b.userID ";

	if ($q <> "") {
		$sWhere .= " AND b.userID = " . tosql($q, "Text");
	}

	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberStatusDept($q, $by, $status, $dept)
{
	global $conn;
	$sSQL = "";
	if (!strpos($status, ",")) $sWhere = " a.userID = b.userID AND b.status = " . tosql($status, "Number");
	else  $sWhere = " a.userID = b.userID AND b.status in (" . $status . ")";

	if ($dept <> "") {
		$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
	//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER )";
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberStatusDeptA($q, $by, $status, $dept, $a)
{
	//$conn->debug =1;
	global $conn;
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.status = " . tosql($status, "Number") . " AND a.isActive = " . tosql($a, "Number");
	if ($dept <> "") {
		$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberLoanDeptA($q, $by, $status, $dept, $a)
{
	//$conn->debug =1;
	global $conn;
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.status = " . tosql($status, "Number") . " AND a.isActive = " . tosql($a, "Number");
	if ($dept <> "") {
		$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : memberApply.php; memberEdit.php  - to check login exist or not
function ctLogin($id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " loginID = " . tosql($id, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM users";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : memberEdit.php
function ctMemberDetail($id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " a.userID = " . tosql($id, "Text");
	$sWhere .= " and a.userID = b.userID ";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	a.*, b.*
			FROM 	users a, userdetails b";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : loan.php; loanEdit.php
function ctLoan($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " loanID like " . tosql($q . "%", "Text");
		$sWhere .= " AND a.loanID = b.loanID ";
		$sWhere .= " OR loanNo like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " a.loanID = " . tosql($id, "Number");
		$sWhere .= " AND a.loanID = b.loanID ";
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 a.*,b.* FROM loans a, loandocs b";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctWelfare($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " ID like " . tosql($q . "%", "Text");
		$sWhere .= " OR welfareNo like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " ID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	* FROM welfares";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctLoanNew($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = " a.loanID = " . tosql($id, "Number");
	if ($q <> "") {
		$sWhere = " loanID like " . tosql($q . "%", "Text");
		$sWhere .= " AND a.loanID = b.loanID ";
		$sWhere .= " OR loanNo like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " a.loanID = " . tosql($id, "Number");
		$sWhere .= " AND a.loanID = b.loanID ";
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 * FROM loans a, loandocs b";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctLoanAll($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " loanID like " . tosql($q . "%", "Text");
		$sWhere .= " OR loanNo like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " a.loanID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 a.*, b.* FROM loans a, loandocs b";
	$sSQL = $sSQL . $sWhere . ' and a.loanID= b.loanID ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : loan.php, loanTable.php, loanList.php
function ctLoanStatusDept($q, $by, $status, $dept, $id = 0)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.status = " . tosql($status, "Number");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}

	if ($id) {
		$sWhere .= " AND A.loanType in (" . $id . ") ";
	}

	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loans A ";
		}
	}
	$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : share.php; shareEdit.php; shareStatus.php
function ctShare($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " shareID like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " shareID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 * FROM shares";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : share.php
function ctShareStatus($q, $by, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.status = " . tosql($id, "Number");
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND B.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
		$sWhere .= " AND A.userID = B.userID ";
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A, users B";
		}
	} else {
		$sSQL = "SELECT	A.* FROM 	shares A ";
	}
	$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctShareStatusDept($q, $by, $status, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.status = " . tosql($status, "Number");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	shares A ";
		}
	}
	$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : shareSell.php; shareSellEdit.php
function ctShareSell($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " shareID like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " shareID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 * FROM sharessell";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : sharesell.php
function ctShareSellStatus($q, $by, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.status = " . tosql($status, "Number");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A ";
		}
	}
	$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctShareSellStatusDept($q, $by, $status, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.status = " . tosql($status, "Number");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A, userdetails B, users C";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	sharessell A , userdetails B";
	}
	$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : selMember.php
function ctNumberShare($id)
{
	global $conn;
	$numShare = 0;

	$sSQL = "";
	$sWhere = "";
	$sWhere = " userID = " . tosql($id, "Text");
	$sWhere .= " and isApproved = '1' ";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	sum(unitShare) as addUnit FROM shares";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	$addUnit = 0;
	if ($rs->RowCount() == 1) $addUnit = $rs->fields(addUnit);

	$sSQL = "";
	$sWhere = "";
	$sWhere = " userID = " . tosql($id, "Text");
	$sWhere .= " and isApproved = '1' ";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	sum(unitShare) as minusUnit FROM sharessell";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	$minusUnit = 0;
	if ($rs->RowCount() == 1) $minusUnit = $rs->fields(minusUnit);

	$sSQL = "";
	$sWhere = "";
	$sWhere = " sellUserID = " . tosql($id, "Text");
	$sWhere .= " and isApproved = '1' ";
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	sum(unitShare) as gainUnit FROM sharessell";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	$gainUnit = 0;
	if ($rs->RowCount() == 1) $gainUnit = $rs->fields(gainUnit);

	$numShare = $addUnit - $minusUnit + $gainUnit;

	return $numShare;
}

// used by : angkasa.php
function ctAngkasa($q, $yymm)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " ic like " . tosql($q . "%", "Text") . " AND ";
	}
	$sWhere .= " yymm = " . tosql($yymm, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM angkasa";
	$sSQL = $sSQL . $sWhere . ' ORDER BY importedDate DESC, yymm ';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

//--- BEGIN : POSTING QUERY FOR ANGKASA & URUSNIAGA -----------------------------------------------------
// used by : postTrans.php
function ctAngkasaYYMM($yymm, $status)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " yymm = " . tosql($yymm, "Text");
	if ($status <> "All") {
		$sWhere .= " AND status = " . tosql($status, "Number");
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM angkasa";
	$sSQL = $sSQL . $sWhere . ' ORDER BY importedDate DESC, yymm ';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : postTrans.php
function ctTransYYMM($yymm, $status)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " yrmth = " . tosql($yymm, "Text");
	if ($status <> "All") {
		$sWhere .= " AND status = " . tosql($status, "Number");
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM transaction";
	$sSQL = $sSQL . $sWhere . ' ORDER BY createdDate DESC, yrmth ';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : POSTING QUERY FOR ANGKASA & URUSNIAGA -----------------------------------------------------

// used by : dividen.php
function ctDividenYear($yy)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere .= " year = " . tosql($yy, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM dividenyear";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctDividenDept($q, $by, $yy, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.startYear = " . tosql($yy, "Text");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	dividen A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	dividen A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	dividen A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	dividen A ";
		}
	}
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctDividenID($id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere .= " ID = " . tosql($id, "Number");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM dividen";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctDividenTransfer($yy, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere .= " startYear = " . tosql($yy, "Number");
	$sWhere .= " AND transfer = " . tosql($id, "Number");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM dividen";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

//--- BEGIN : USER SAVING -------------------------------------------------------------------------------
function ctUserSavingList($q, $by, $yymm, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.yrmth = " . tosql($yymm, "Text");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	usersaving A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	usersaving A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	usersaving A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	usersaving A ";
		}
	}
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctUserSaving($yrmth, $userID)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " yrmth = " . tosql($yrmth, "Text");
	$sWhere .= " AND userID = " . tosql($userID, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	usersaving ";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : USER SAVING -------------------------------------------------------------------------------

//--- BEGIN : TERPINATION OF MEMBER ---------------------------------------------------------------------
// used by : memberT.php
function ctMemberTerminate($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " userID = " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " userID = " . tosql($id, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "	SELECT	* FROM userterminate ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

// used by : memberT.php
function ctMemberTerminateStatuso($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($id <> "ALL") {
		$sWhere = " status = " . tosql($id, "Text");
	}
	if ($q <> "") {
		$sWhere = " ID like " . tosql($q . "%", "Text");
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	userterminate ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY CAST( b.userID AS SIGNED INTEGER ),applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberTerminateStatus($q, $by, $status, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND a.userID=c.userID and c.status = " . tosql($status, "Number");
	if ($dept <> "") {
		$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*,c.*
			 FROM 	users a, userdetails b, userterminate c";
	$sSQL = $sSQL . $sWhere . ' ORDER BY CAST( b.memberID AS SIGNED INTEGER ),c.applyDate DESC';

	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMemberTerminateStatusOk($q, $by, $status, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND a.userID=c.userID and c.status = " . tosql($status, "Number");
	if ($dept <> "") {
		//$sWhere .= " AND b.departmentID = " . tosql($dept,"Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			//$sWhere .= " AND b.memberID like " . tosql($q."%","Text");			
		} else if ($by == 2) {
			//$sWhere .= " AND a.name like " . tosql($q."%","Text");
		} else if ($by == 3) {
			//$sWhere .= " AND b.newIC like " . tosql($q."%","Text");		
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*,c.*
			 FROM 	users a, userdetails b, userterminate c";
	$sSQL = $sSQL . $sWhere . ' and c.type = 0 ORDER BY CAST( b.memberID AS SIGNED INTEGER ),c.applyDate DESC';

	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : TERPINATION OF MEMBER ---------------------------------------------------------------------

//--- BEGIN : LOAN PAYMENT -----------------------------------------------------------------------------
function ctLoanPymtList($q, $by, $yymm, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.yrmth = " . tosql($yymm, "Text");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loanpayment A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loanpayment A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loanpayment A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	loanpayment A ";
		}
	}
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : LOAN PAYMENT -----------------------------------------------------------------------------

//--- BEGIN : MONTH-END ---------------------------------------------------------------------------------
function ctMonthEnd($yrmth)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " yrmth = " . tosql($yrmth, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	monthend ";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMonthEndUser($yrmth, $userID)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " yrmth = " . tosql($yrmth, "Text");
	$sWhere .= " AND userID = " . tosql($userID, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	monthend ";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctMonthEndList($q, $by, $yymm, $dept)
{
	//$conn->debug =1;
	global $conn;
	$sSQL = "";
	$sWhere = " A.yrmth = " . tosql($yymm, "Text");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	monthend A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	monthend A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	monthend A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	monthend A ";
		}
	}
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : MONTH-END ---------------------------------------------------------------------------------

//--- BEGIN : YEAR-END ----------------------------------------------------------------------------------
function ctYearEnd($yy)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " year = " . tosql($yy, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	yearend ";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctYearEndUser($yy, $userID)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	$sWhere = " year = " . tosql($yy, "Text");
	$sWhere .= " AND userID = " . tosql($userID, "Text");
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	* FROM 	yearend ";
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function ctYearEndList($q, $by, $yy, $dept)
{
	global $conn;
	$sSQL = "";
	$sWhere = " A.year = " . tosql($yy, "Text");
	if ($dept <> "") {
		$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
		$sWhere .= " AND A.userID = B.userID ";
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND A.userID = C.userID ";
			$sWhere .= " AND C.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND A.userID = B.userID ";
			$sWhere .= " AND B.newIC like " . tosql($q . "%", "Text");
		}
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	if ($q <> "") {
		if ($by == 1 or $by == 3) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	yearend A, userdetails B";
		} else if ($by == 2) {
			$sSQL = "SELECT	DISTINCT A.* FROM 	yearend A, userdetails B, users C";
		}
	} else {
		if ($dept <> "") {
			$sSQL = "SELECT	DISTINCT A.* FROM 	yearend A, userdetails B";
		} else {
			$sSQL = "SELECT	DISTINCT A.* FROM 	yearend A ";
		}
	}
	$sSQL = $sSQL . $sWhere;
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
//--- END   : YEAR-END ----------------------------------------------------------------------------------


// used by : memberT.php
function ctMemberUrusniaga($q, $id)
{
	global $conn;
	//$conn->debug=true;
	//$sSQL = "";
	//$sWhere = "";		
	//$sWhere = " status = " . tosql($id,"Text");
	$sSQL = "SELECT a.* FROM transaction a ,
			userdetails b   WHERE a.userID = b.userID and b.memberID = '" . $id . "'";
	//$sWhere = " WHERE memberID = ".tosql($id,"Text").";
	//$sSQL = "SELECT	userID FROM userdetails ";
	//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);

	return $rs;
}


function ctMemberUrusniagaDetail($q, $id)
{
	global $conn;
	//$conn->debug=true;
	//$sSQL = "";
	//$sWhere = "";		
	//$sWhere = " status = " . tosql($id,"Text");
	$sSQL = "SELECT a. * , a.pymtAmt + a.cajAmt AS jumlah, CONCAT_WS( ',', c.code, c.name ) AS kod
				FROM transaction a, userdetails b, general c
				WHERE a.userID = b.userID
				AND a.deductid = c.id
				AND b.memberID = '" . $id . "'
				ORDER BY a.yrmth";
	//LIMIT 0 , 30
	//echo $sSQL;
	$rs = &$conn->Execute($sSQL);

	return $rs;
}

function ctMemberRUrusniaga($q, $id)
{
	global $conn;
	//$conn->debug=true;
	//$sSQL = "";
	//$sWhere = "";		
	//$sWhere = " status = " . tosql($id,"Text");

	$sSQL = "SELECT t.yrmth AS blnthn, count( t.id ) AS bil, sum( t.cajAmt ) AS jumCaj, sum( t.pymtAmt ) AS jumlah, 
		sum(CASE t.addminus
			WHEN 1 
			THEN t.pymtAmt
			ELSE 0 
			END ) AS jumlahkredit, 
		sum(CASE t.addminus
			WHEN 0 
			THEN t.pymtAmt
			ELSE 0 
			END ) AS jumlahdebit
			FROM transaction AS t
			GROUP BY yrmth";

	//echo $sSQL;
	$rs = &$conn->Execute($sSQL);

	return $rs;
}

function deductList($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM general WHERE category=\'J\' order by code ASC';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}

function strSelect($id, $code, $type = "arr")
{
	$strDeductCodeList = deductList(2);
	$strDeductNameList = deductList(3);

	if ($type == "arr") {
		$name = 'kod_akaun[' . $id . ']';
	} else {
		$name = 'kod_akaun2';
	}

	$strSelect = '<select name="' . $name . '" class="Data" onchange="document.MyForm.submit();">
				<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductCodeList); $i++) {
		$strSelect .= '	<option value="' . $strDeductCodeList[$i] . '" ';
		if ($code == $strDeductCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductCodeList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function strSelect2($id, $code, $type = "arr")
{
	$strDeductIDList = deductList(1);
	$strDeductCodeList = deductList(2);
	$strDeductNameList = deductList(3);

	if ($type == "arr") {
		$name = 'perkara[' . $id . ']';
	} else {
		$name = 'perkara2';
	}

	$strSelect = '<select class="form-select-sm" name="' . $name . '" onchange="document.MyForm.submit();">
				<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp; - &nbsp;' . $strDeductNameList[$i] . '';
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function selectAdmin($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `users` WHERE groupID in (1,2) and isActive = 1 AND loginID NOT IN ("superadmin")';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strAdminCodeList = array();
		$strAdminNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strAdminCodeList[$nCount] = $GetData->fields('userID');
			$strAdminNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select class="form-selectx" name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strAdminCodeList); $i++) {
		$strSelect .= '	<option value="' . $strAdminCodeList[$i] . '" ';
		if ($code == $strAdminCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strAdminNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function selectbank($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `general` WHERE category = "Z"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select class="form-select-sm" name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}



function selectMember($code, $name, $type)
{
	global $conn;

	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.status = " . tosql(1, "Number") . " AND a.isActive = " . tosql(1, "Number");
	if ($dept <> "") {
		$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
	}
	if ($q <> "") {
		if ($by == 1) {
			$sWhere .= " AND b.memberID like " . tosql($q . "%", "Text");
		} else if ($by == 2) {
			$sWhere .= " AND a.name like " . tosql($q . "%", "Text");
		} else if ($by == 3) {
			$sWhere .= " AND b.newIC like " . tosql($q . "%", "Text");
		}
	}

	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	DISTINCT a.*, b.*
		 FROM 	users a, userdetails b";
	$sSQL = $sSQL . $sWhere;
	$sSQL = $sSQL . "order by CAST( b.memberID AS SIGNED INTEGER )";
	$GetMember = &$conn->Execute($sSQL);
	if ($GetMember->RowCount() <> 0) {
		$strMemberIDList = array();
		$strMemberICList = array();
		$strMemberNameList = array();
		$nCount = 0;
		while (!$GetMember->EOF) {
			$strMemberIDList[$nCount] = $GetMember->fields('userID');
			$strMemberICList[$nCount] = $GetMember->fields('newIC');
			$strMemberNameList[$nCount] = $GetMember->fields('name');
			$GetMember->MoveNext();
			$nCount++;
		}
	}
	//end get list

	$strSelect = '<select class="form-select-sm" name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strMemberIDList); $i++) {
		$strSelect .= '	<option value="' . $strMemberIDList[$i] . '" ';
		if ($code == $strMemberIDList[$i]) $strSelect .= ' selected';
		if ($type == 1) $strSelect .=  '>' . $strMemberIDList[$i];
		if ($type == 2) $strSelect .=  '>' . $strMemberICList[$i];
		if ($type == 3) $strSelect .=  '>' . $strMemberNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function countPays($totalLoan, $totalInterest, $loanAmt, $loanCaj, $loanPeriod)
{
	if ($loanAmt <> "") {
		$monthlyPay		= number_format($loanAmt / $loanPeriod, 2, '.', '');
		$chkCent = substr($monthlyPay, -2, 2);
		$monthlyPay = (int)$monthlyPay;
		if ($chkCent > 0 && $chkCent <= 50) {
			$monthlyPay = number_format($monthlyPay + 0.5, 2, '.', '');
		} elseif ($chkCent > 50) {
			$monthlyPay = number_format($monthlyPay + 1, 2, '.', '');
		} else
			$monthlyPay = number_format($monthlyPay, 2, '.', '');
	}
	$lastmonthlyPay	= number_format($loanAmt - ($monthlyPay * ($loanPeriod - 1)), 2, '.', '');

	if ($loanCaj <> 0) { //zero interest for 0 caj
		if ($totalInterest and $loanPeriod <> "") {
			$interestPay	= number_format($totalInterest / $loanPeriod, 2, '.', '');
			$chkCent = substr($interestPay, -2, 2);
			$interestPay = (int)$interestPay;
			if ($chkCent > 0 && $chkCent <= 50) {
				$interestPay = number_format($interestPay + 0.5, 2, '.', '');
			} elseif ($chkCent > 50) {
				$interestPay = number_format($interestPay + 1, 2, '.', '');
			} else
				$interestPay = number_format($interestPay, 2, '.', '');
		}
	} else {
		$interestPay = '0.00';
	}
	$lastinterestPay = number_format($totalInterest - ($interestPay * ($loanPeriod - 1)), 2, '.', '');
	if ($lastinterestPay < 0) $lastinterestPay = '0.00';

	return array($monthlyPay, $lastmonthlyPay, $interestPay, $lastinterestPay);
}

//yuran kredit balance by member
/*
SELECT 
SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib

FROM transaction
WHERE deductID =1595
AND userID = '2203'
AND year( createdDate ) <= '2006'
AND month( createdDate ) <= '12' GROUP BY userID
*/

function getWajibDiv($id, $yr)
{
	global $conn;
	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction 
		WHERE 
		deductID = 1595 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		GROUP BY userID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $bakiAwal = $rsWajibOpen->fields(totalWajib); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
	else $bakiAwal = 0;

	return $bakiAwal;
}

function getFeesAwalthn($id, $yr)
{
	global $conn;

	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID in (1595,1780,1607) 
		AND userID = '" . $id . "' 
		AND year(createdDate) < " . $yr . "
		GROUP BY userID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $bakiAwalSaham = $rsWajibOpen->fields(totalWajib); //$rsWajibOpen->fields(yuranKt) - 
	else $bakiAwalSaham = 0;
	return $bakiAwalSaham;
}

function getFees($id)
{
	global $conn;
	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID IN (1595,1780,1607) 
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $totalFees = $rsWajibOpen->fields(totalWajib);
	else $totalFees = 0;
	return $totalFees;
}

function getShares($id, $yr)
{
	global $conn;
	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID IN (1596,1780) 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalWajib); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwal = 0;
	return $bakiAwal;
}

function getSharesterkini($id, $yr)
{
	global $conn;
	$getOpenTK = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID IN (1596,1780) 
		AND userID = '" . $id . "' 
		GROUP BY userID";
	$rsOpenTK = $conn->Execute($getOpenTK);
	if ($rsOpenTK->RowCount() == 1) $bakiAwalTK = $rsOpenTK->fields(totalWajib); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwalTK = 0;
	return $bakiAwalTK;
}

//Deposit Khas @ Simpanan Khas
function getDepoKhasAll($id, $yr)
{
	global $conn;
	// $yrmth = $yr.str_pad($mth,2,'0',STR_PAD_LEFT);

	$getDepo = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalDepo
		FROM transaction
		WHERE
		deductID = 1963 
		AND userID = '" . $id . "'
		GROUP BY userID";
	$rsDepo = $conn->Execute($getDepo);
	if ($rsDepo->RowCount() == 1) $totalDepo = $rsDepo->fields(totalDepo);
	else $totalDepo = 0;
	return $totalDepo;
}

function getFeesDiv($id, $yr)
{
	global $conn;

	$getWajibOpen = "
SELECT  SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID in (1595,1780)
		AND userID = " . $id . "
		AND yrmth = " . $yr . "
		GROUP BY userID";

	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) {
		$DB = $rsWajibOpen->fields(yuranDb); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
		$KT = $rsWajibOpen->fields(yuranKt);
		$bakiAwal = ($KT - $DB);
	} else $bakiAwal = 0;

	return $bakiAwal;
}



function getFeesDivTable($id, $yr)
{
	global $conn;

	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID in (1595,1780) 
		AND userID = '" . $id . "' 
		AND yrmth = " . $yr . "
		GROUP BY userID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $bakiAwal = $rsWajibOpen->fields(totalWajib); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
	else $bakiAwal = 0;

	return $bakiAwal;
}

function getBalanceHL($id)
{
	global $conn;
	$getOpen = "SELECT 
		SUM(BalanceHL) AS totalHL
		FROM accounthl
		WHERE
		userID = '" . $id . "' 
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $BakiHL = $rsOpen->fields(totalHL); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $BakiHL = 0;
	return $BakiHL;
}

function getSharesDiv($id, $yr)
{
	global $conn;
	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID = 1596 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalWajib); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwal = 0;
	return $bakiAwal;
}


function getTotFees($id, $yr)
{
	global $conn;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID in (1595,1607,1596,1780) 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);


	if ($rsOpen->RowCount() == 1)

		$bakiAwal = $rsOpen->fields(totalWajib); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwal = 0;

	return $bakiAwal;
}

function getFeeMonth($id, $yr, $mth)
{
	global $conn;
	$yrmth = $yr . $mth;

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID in (1595,1607,1780) 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		AND yrmth = '" . $yrmth . "'
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalWajib); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwal = 0;

	return $bakiAwal;
}


function ctInsuran($q, $id)
{
	global $conn;
	$sSQL = "";
	$sWhere = "";
	if ($q <> "") {
		$sWhere = " ID like " . tosql($q . "%", "Text");
		$sWhere .= " OR insuranNo like " . tosql($q . "%", "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	if ($id <> "ALL") {
		$sWhere = " ID = " . tosql($id, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	$sSQL = "SELECT	 * FROM insurankenderaan";
	$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiTunai($id, $yrmth, $bond)
{
	global $conn;

	$getTunaiOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID in (1539,1702,1709,1826,1719,1646)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";
	$rsTunaiOpen = $conn->Execute($getTunaiOpen);

	if ($rsTunaiOpen->RowCount() == 1) {
		$DB = $rsTunaiOpen->fields(yuranDb); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
		$KT = $rsTunaiOpen->fields(yuranKt);
		$bakiAwalTunai = ($DB - $KT);
	} else $bakiAwalTunai = 0;
	return $bakiAwalTunai;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiSBP($id, $yrmth, $bond)
{
	global $conn;

	$getTunaiOpen = "SELECT  SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1768,1827)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsTunaiOpen = $conn->Execute($getTunaiOpen);
	if ($rsTunaiOpen->RowCount() == 1) {
		$DB = $rsTunaiOpen->fields(yuranDb); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
		$KT = $rsTunaiOpen->fields(yuranKt);
		$bakiAwalSBP = ($DB - $KT);
	} else $bakiAwalSBP = 0;
	return $bakiAwalSBP;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiPNS($id, $yrmth, $bond)
{
	global $conn;

	$getPNSOpen = "SELECT  SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1614)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsPNSOpen = $conn->Execute($getPNSOpen);
	if ($rsPNSOpen->RowCount() == 1) {
		$DB = $rsPNSOpen->fields(yuranDb);
		$KT = $rsPNSOpen->fields(yuranKt);
		$bakiAwalPNS = ($DB - $KT);
	} else $bakiAwalPNS = 0;
	return $bakiAwalPNS;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiBRG($id, $yrmth, $bond)
{
	global $conn;

	$getBRGOpen = "SELECT  SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1613,1622,1623,1624)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsBRGOpen = $conn->Execute($getBRGOpen);
	if ($rsBRGOpen->RowCount() == 1) {
		$DB = $rsBRGOpen->fields(yuranDb);
		$KT = $rsBRGOpen->fields(yuranKt);
		$bakiAwalBRG = ($DB - $KT);
	} else $bakiAwalBRG = 0;
	return $bakiAwalBRG;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiSBR($id, $yrmth, $bond)
{
	global $conn;

	$getSBROpen = "SELECT  SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1736,1788,1791,1802,1804)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsSBROpen = $conn->Execute($getSBROpen);
	if ($rsSBROpen->RowCount() == 1) {
		$DB = $rsSBROpen->fields(yuranDb);
		$KT = $rsSBROpen->fields(yuranKt);
		$bakiAwalSBR = ($DB - $KT);
	} else $bakiAwalSBR = 0;
	return $bakiAwalSBR;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiASH($id, $yrmth, $bond)
{
	global $conn;

	$getASHOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1626,1704,1747)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsASHOpen = $conn->Execute($getASHOpen);
	if ($rsASHOpen->RowCount() == 1) {
		$DB = $rsASHOpen->fields(yuranDb);
		$KT = $rsASHOpen->fields(yuranKt);
		$bakiAwalASH = ($DB - $KT);
	} else $bakiAwalASH = 0;
	return $bakiAwalASH;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiKEN($id, $yrmth, $bond)
{
	global $conn;

	$getKENOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1541,1542,1545,1547,1711,1712,1758,1759,1674,1673,1847)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsKENOpen = $conn->Execute($getKENOpen);
	if ($rsKENOpen->RowCount() == 1) {
		$DB = $rsKENOpen->fields(yuranDb);
		$KT = $rsKENOpen->fields(yuranKt);
		$bakiAwalKEN = ($DB - $KT);
	} else $bakiAwalKEN = 0;
	return $bakiAwalKEN;
} ////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiMOT($id, $yrmth, $bond)
{
	global $conn;

	$getMOTOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1551,1549,1537,1762,1763,1713)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsMOTOpen = $conn->Execute($getMOTOpen);
	if ($rsMOTOpen->RowCount() == 1) {
		$DB = $rsMOTOpen->fields(yuranDb);
		$KT = $rsMOTOpen->fields(yuranKt);
		$bakiAwalMOT = ($DB - $KT);
	} else $bakiAwalMOT = 0;
	return $bakiAwalMOT;
} ////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiINS($id, $yrmth, $bond)
{
	global $conn;

	$getINSOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1644)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsINSOpen = $conn->Execute($getINSOpen);
	if ($rsINSOpen->RowCount() == 1) {
		$DB = $rsINSOpen->fields(yuranDb);
		$KT = $rsINSOpen->fields(yuranKt);
		$bakiAwalINS = ($DB - $KT);
	} else $bakiAwalINS = 0;
	return $bakiAwalINS;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiUMR($id, $yrmth, $bond)
{
	global $conn;

	$getUMROpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1631)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsUMROpen = $conn->Execute($getUMROpen);
	if ($rsUMROpen->RowCount() == 1) {
		$DB = $rsUMROpen->fields(yuranDb);
		$KT = $rsUMROpen->fields(yuranKt);
		$bakiAwalUMR = ($DB - $KT);
	} else $bakiAwalUMR = 0;
	return $bakiAwalUMR;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiRAYA($id, $yrmth, $bond)
{
	global $conn;

	$getRAYAOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1838)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsRAYAOpen = $conn->Execute($getRAYAOpen);
	if ($rsRAYAOpen->RowCount() == 1) {
		$DB = $rsRAYAOpen->fields(yuranDb);
		$KT = $rsRAYAOpen->fields(yuranKt);
		$bakiAwalRAYA = ($DB - $KT);
	} else $bakiAwalRAYA = 0;
	return $bakiAwalRAYA;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiDH($id, $yrmth, $bond)
{
	global $conn;

	$getDHOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1858)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsDHOpen = $conn->Execute($getDHOpen);
	if ($rsDHOpen->RowCount() == 1) {
		$DB = $rsDHOpen->fields(yuranDb);
		$KT = $rsDHOpen->fields(yuranKt);
		$bakiAwalDH = ($DB - $KT);
	} else $bakiAwalDH = 0;
	return $bakiAwalDH;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiKEC($id, $yrmth, $bond)
{
	global $conn;

	$getKECOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1850)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsKECOpen = $conn->Execute($getKECOpen);
	if ($rsKECOpen->RowCount() == 1) {
		$DB = $rsKECOpen->fields(yuranDb);
		$KT = $rsKECOpen->fields(yuranKt);
		$bakiAwalKEC = ($DB - $KT);
	} else $bakiAwalKEC = 0;
	return $bakiAwalKEC;
}
////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiAKAUN2($id, $yrmth, $bond)
{
	global $conn;

	$getAKAUN2Open = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN (1994)
		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsAKAUN2Open = $conn->Execute($getAKAUN2Open);
	if ($rsAKAUN2Open->RowCount() == 1) {
		$DB = $rsAKAUN2Open->fields(yuranDb);
		$KT = $rsAKAUN2Open->fields(yuranKt);
		$bakiAwalAKAUN2 = ($DB - $KT);
	} else $bakiAwalAKAUN2 = 0;
	return $bakiAwalAKAUN2;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiBulanY($id, $dtFrom, $dtTo)
{
	global $conn;

	$getOpen = "SELECT 
			
			SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS totalWajib, 
			SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranKt
			FROM transaction
			WHERE
			deductID IN (1595) 
			AND userID = '" . $id . "' 
			AND (createdDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')		
			GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalWajib) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;

	return $bakiAwal;
}

function getBakiBulanS($id, $dtFrom, $dtTo)
{
	global $conn;

	$getOpen = "SELECT 
			
			SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS totalWajib, 
			SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranKt
			FROM transaction
			WHERE
			deductID IN (1596) 
			AND userID = '" . $id . "' 
			AND (createdDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')		
			GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalWajib) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;

	return $bakiAwal;
}

////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
//-- fee awal bln (dividen)
function getFeesAwlDiv($id, $yy)
{
	global $conn;

	$getWajibOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN -pymtAmt ELSE pymtAmt END ) AS totalWajib
		FROM transaction
		WHERE
		deductID in (1595,1780,1607) 
		AND userID = '" . $id . "' 
		AND yrmth <= '" . $yy . "'
		GROUP BY userID";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $bakiAwalSaham = $rsWajibOpen->fields(totalWajib); //$rsWajibOpen->fields(yuranKt) - 
	else $bakiAwalSaham = 0;
	return $bakiAwalSaham;
}

function getListDividen($id, $yy)
{
	global $conn;

	$getWajibOpen = "SELECT * FROM DIVIDEN WHERE
 		userID = '" . $id . "' 
		AND yearDiv = '" . $yy . "' 
		Order by userID ";
	$getDividenOpn = $conn->Execute($getWajibOpen);
	return $getDividenOpn;
}


function getSumDividen($yy)
{
	global $conn;

	$getWajibOpen = "SELECT Sum(AmtDiv)as Amt FROM DIVIDEN WHERE yearDiv = '" . $yy . "'";
	$getSumDividen = $conn->Execute($getWajibOpen);
	return $getSumDividen;
}

function getJumlahPGBALL($id, $yrmthNow)
{
	global $conn;

	$getPotP = "SELECT SUM( jumBlnP ) AS Jumlah
FROM potbulan
WHERE status = 1
AND lastyrmthPymt >= '" . $yrmthNow . "' 
AND yrmth <= '" . $yrmthNow . "'
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumP = $getPot->fields(Jumlah); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumP = 0;
	return $totalJumP;
}

function getJumlahPGBALLSPSN($id, $yrmthNow)
{
	global $conn;

	$getPotP = "SELECT *
FROM importspsn
WHERE userID = '" . $id . "' 
AND yrmth = '" . $yrmthNow . "'";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumPSpsn = $getPot->fields(AmountSP); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumPSpsn = 0;
	return $totalJumPSpsn;
}


function getJumlahPGBALLPAT($id, $yrmthNow)
{
	global $conn;

	$getPotP = "SELECT SUM( jumBlnPAT ) AS Jumlah
FROM potbulan
WHERE status = 2
AND lastyrmthPymt >= '" . $yrmthNow . "' 
AND yrmth < '" . $yrmthNow . "'
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumP = $getPot->fields(Jumlah); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumP = 0;
	return $totalJumP;
}

function getJumlahHL($id, $yrmthNow)
{
	global $conn;

	$getPotP = "SELECT SUM( jumBlnP ) AS Jumlah
FROM potbulanHL
WHERE status IN (1)
AND (lastyrmthPymt >= '" . $yrmthNow . "' AND yrmth <= '" . $yrmthNow . "')
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumP = $getPot->fields(Jumlah); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumP = 0;
	return $totalJumP;
}


function getJumlah($id, $yrmthNow)
{
	global $conn;

	$getPotP = "SELECT SUM( jumBlnP ) AS Jumlah
FROM potbulan
WHERE status IN (1)
AND (lastyrmthPymt >= '" . $yrmthNow . "' AND yrmth <= '" . $yrmthNow . "')
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumP = $getPot->fields(Jumlah); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumP = 0;
	return $totalJumP;
}





function getJumlahPAT($id)
{
	global $conn;

	$getPotP = "SELECT SUM( jumBlnPAT ) AS Jumlah
FROM potbulan
WHERE status = 2
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotP);
	if ($getPot->RowCount() == 1) $totalJumP = $getPot->fields(Jumlah); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumP = 0;
	return $totalJumP;
}


function getJumlahY($id)
{
	global $conn;

	$getPotY = "SELECT WajibP
FROM potbulan
WHERE status = 1
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotY);
	if ($getPot->RowCount() == 1) $totalJumY = $getPot->fields(WajibP); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumY = 0;
	return $totalJumY;
}

function getJumlahYPAT($id)
{
	global $conn;

	$getPotY = "SELECT WajibP
FROM potbulan
WHERE status = 2
AND userID = '" . $id . "' 
GROUP BY userID";
	$getPot = $conn->Execute($getPotY);
	if ($getPot->RowCount() == 1) $totalJumY = $getPot->fields(WajibP); //$rsWajibOpen->fields(yuranKt) - 
	else $totalJumY = 0;
	return $totalJumY;
}

function getDataRecordHL($pk)
{
	global $conn;
	$GetLoansIDHL = "select loanID FROM loans where userID = '" . $pk . "' AND status = 3 ";
	$GetLoansID =  &$conn->Execute($GetLoansIDHL);
	return $GetLoansID;
}

/// loan PGB SPSN
function getloanType()
{
	global $conn;

	$getloantype = "SELECT DISTINCT c.loanType, b.code as deptCode, b.name as deptName 
			FROM  general b, potbulan c
			WHERE b.ID = c.loanType AND c.status = 1 AND c.loanType NOT IN (1616,1540,1620,1630)";

	$getloan = $conn->Execute($getloantype);
	return $getloan;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getTunai($id, $yymmTT, $yymm)
{
	global $conn;
	$gettunai = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth BETWEEN '" . $yymmTT . "' AND '" . $yymm . "' AND b.loanType IN (1896,1998) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPot = $conn->Execute($gettunai);
	return $getPot;
}

function getTunai1($id, $yymmTT, $yymm)
{
	global $conn;
	$gettunai = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth BETWEEN '" . $yymmTT . "' AND '" . $yymm . "' AND b.loanType IN (2005) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPot = $conn->Execute($gettunai);
	return $getPot;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBRG1($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG1 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1615) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotBRG1 = $conn->Execute($getBRG1);
	return $getPotBRG1;
}

function getBRG2($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG2 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1665) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotBRG2 = $conn->Execute($getBRG2);
	return $getPotBRG2;
}

function getBRG3($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG3 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1666) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotBRG3 = $conn->Execute($getBRG3);
	return $getPotBRG3;
}

function getBRGBR1($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRGBR1 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1615) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRGBR1 = $conn->Execute($getBRGBR1);
	return $getPotBRGBR1;
}

function getBRGBR2($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRGBR2 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1665) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRGBR2 = $conn->Execute($getBRGBR2);
	return $getPotBRGBR2;
}
function getBRGBR3($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRGBR3 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1666) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRGBR3 = $conn->Execute($getBRGBR3);
	return $getPotBRGBR3;
}
function getBRG1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG1AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1615) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRG1AS = $conn->Execute($getBRG1AS);
	return $getPotBRG1AS;
}
function getBRG2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG2AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1665) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRG2AS = $conn->Execute($getBRG2AS);
	return $getPotBRG2AS;
}
function getBRG3AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG3AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1666) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRG3AS = $conn->Execute($getBRG3AS);
	return $getPotBRG3AS;
}
function getBRG4AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getBRG4AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1667) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotBRG4AS = $conn->Execute($getBRG4AS);
	return $getPotBRG4AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getins($id, $yymmTT, $yymm)
{
	global $conn;
	$getins = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1620) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotins = $conn->Execute($getins);
	return $getPotins;
}
function getinsBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getinsBR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1620) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotinsBR = $conn->Execute($getinsBR);
	return $getPotinsBR;
}
function getinsAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getinsAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1620) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "' ";
	$getPotinsAS = $conn->Execute($getinsAS);
	return $getPotinsAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getPNS($id, $yymmTT, $yymm)
{
	global $conn;
	$getPNS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1616) AND a.userID = c.userID AND a.userID = '" . $id . "'";
	$getPotPNS = $conn->Execute($getPNS);
	return $getPotPNS;
}
function getPNSBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getPNSBR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1616) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotPNSBR = $conn->Execute($getPNSBR);
	return $getPotPNSBR;
}
function getPNSAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getPNSAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1616) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotPNSAS = $conn->Execute($getPNSAS);
	return $getPotPNSAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getUMRAH($id, $yymmTT, $yymm)
{
	global $conn;
	$getUMRAH = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID  AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1630) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotUMRAH = $conn->Execute($getUMRAH);
	return $getPotUMRAH;
}
function getUMRAHBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getUMRAHBR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1630) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotUMRAHBR = $conn->Execute($getUMRAHBR);
	return $getPotUMRAHBR;
}
function getUMRAHAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getUMRAHAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1630) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotUMRAHAS = $conn->Execute($getUMRAHAS);
	return $getPotUMRAHAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getKNDT1($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT1 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1546) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDT1 = $conn->Execute($getKNDT1);
	return $getPotKNDT1;
}
function getKNDT2($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT2 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1548) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDT2 = $conn->Execute($getKNDT2);
	return $getPotKNDT2;
}
function getKNDT3($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT3 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1760) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDT3 = $conn->Execute($getKNDT3);
	return $getPotKNDT3;
}
function getKNDT4($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT4 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1761) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDT4 = $conn->Execute($getKNDT4);
	return $getPotKNDT4;
}
function getKNDT1BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT1BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1546) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT1BR = $conn->Execute($getKNDT1BR);
	return $getPotKNDT1BR;
}
function getKNDT2BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT2BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1548) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT2BR = $conn->Execute($getKNDT2BR);
	return $getPotKNDT2BR;
}
function getKNDT3BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT3BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1760) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT3BR = $conn->Execute($getKNDT3BR);
	return $getPotKNDT3BR;
}
function getKNDT4BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT4BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1761) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT4BR = $conn->Execute($getKNDT4BR);
	return $getPotKNDT4BR;
}
function getKNDT1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT1AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1546) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT1AS = $conn->Execute($getKNDT1AS);
	return $getPotKNDT1AS;
}
function getKNDT2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT2AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1548) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT2AS = $conn->Execute($getKNDT2AS);
	return $getPotKNDT2AS;
}
function getKNDT3AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT3AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1760) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT3AS = $conn->Execute($getKNDT3AS);
	return $getPotKNDT3AS;
}
function getKNDT4AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDT4AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1761) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDT4AS = $conn->Execute($getKNDT4AS);
	return $getPotKNDT4AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getKNDB1($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB1 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1544) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDB1 = $conn->Execute($getKNDB1);
	return $getPotKNDB1;
}

function getKNDB2($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB2 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1543) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDB2 = $conn->Execute($getKNDB2);
	return $getPotKNDB2;
}

function getKNDB3($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB3 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1756) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDB3 = $conn->Execute($getKNDB3);
	return $getPotKNDB3;
}

function getKNDB4($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB4 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1757) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotKNDB4 = $conn->Execute($getKNDB4);
	return $getPotKNDB4;
}
function getKNDB1BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB1BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1544) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB1BR = $conn->Execute($getKNDB1BR);
	return $getPotKNDB1BR;
}
function getKNDB2BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB2BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1543) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB2BR = $conn->Execute($getKNDB2BR);
	return $getPotKNDB2BR;
}
function getKNDB3BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB3BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1756) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB3BR = $conn->Execute($getKNDB3BR);
	return $getPotKNDB3BR;
}
function getKNDB4BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB4BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1757) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB4BR = $conn->Execute($getKNDB4BR);
	return $getPotKNDB4BR;
}
function getKNDB1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB1AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1544) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB1AS = $conn->Execute($getKNDB1AS);
	return $getPotKNDB1AS;
}
function getKNDB2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB2AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1543) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB2AS = $conn->Execute($getKNDB2AS);
	return $getPotKNDB2AS;
}
function getKNDB3AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB3AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1756) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB3AS = $conn->Execute($getKNDB3AS);
	return $getPotKNDB3AS;
}
function getKNDB4AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getKNDB4BAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1757) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotKNDB4AS = $conn->Execute($getKNDB4AS);
	return $getPotKNDB4AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getSHM1($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM1 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1737) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotSHM1 = $conn->Execute($getSHM1);
	return $getPotSHM1;
}

function getSHM2($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM2 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1746) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotSHM2 = $conn->Execute($getSHM2);
	return $getPotSHM2;
}

function getSHM3($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM3 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1798) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotSHM3 = $conn->Execute($getSHM3);
	return $getPotSHM3;
}

function getSHM4($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM4 = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1799) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotSHM4 = $conn->Execute($getSHM4);
	return $getPotSHM4;
}
function getSHM1BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM1BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1737) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM1BR = $conn->Execute($getSHM1BR);
	return $getPotSHM1BR;
}
function getSHM2BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM2BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1746) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM2BR = $conn->Execute($getSHM2BR);
	return $getPotSHM2BR;
}
function getSHM3BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM3BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1798) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM3BR = $conn->Execute($getSHM3BR);
	return $getPotSHM3BR;
}
function getSHM4BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM4BR = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1799) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM4BR = $conn->Execute($getSHM4BR);
	return $getPotSHM4BR;
}
function getSHM1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM1AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1737) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM1AS = $conn->Execute($getSHM1AS);
	return $getPotSHM1AS;
}
function getSHM2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM2AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1746) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM2AS = $conn->Execute($getSHM2AS);
	return $getPotSHM2AS;
}
function getSHM3AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM3AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1798) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM3AS = $conn->Execute($getSHM3AS);
	return $getPotSHM3AS;
}
function getSHM4AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getSHM4AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1799) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSHM4AS = $conn->Execute($getSHM4AS);
	return $getPotSHM4AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getSBP($id, $yymmTT, $yymm)
{
	global $conn;
	$getSBP = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1769,1829) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotSBP = $conn->Execute($getSBP);
	return $getPotSBP;
}
function getSBPBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getSBPBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1769,1829) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSBPBR = $conn->Execute($getSBPBR);
	return $getPotSBPBR;
}
function getSBPAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getSBPAS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1769,1829) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotSBPAS = $conn->Execute($getSBPAS);
	return $getPotSBPAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getRAYA($id, $yymmTT, $yymm)
{
	global $conn;
	$getRAYA = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1840) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotRAYA = $conn->Execute($getRAYA);
	return $getPotRAYA;
}
function getRAYABR($id, $yymmTT, $yymm)
{
	global $conn;
	$getRAYABR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1840) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotRAYABR = $conn->Execute($getRAYABR);
	return $getPotRAYABR;
}
function getRAYAAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getRAYAAS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1840) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotRAYAAS = $conn->Execute($getRAYAAS);
	return $getPotRAYAAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getATSSHM($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHM = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1629) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotATSSHM = $conn->Execute($getATSSHM);
	return $getPotATSSHM;
}
function getATSSHMBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHMBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1629) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHMBR = $conn->Execute($getATSSHMBR);
	return $getPotATSSHMBR;
}
function getATSSHM1BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHM1BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1705) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHM1BR = $conn->Execute($getATSSHM1BR);
	return $getPotATSSHM1BR;
}
function getATSSHM2BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHM2BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1748) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHM2BR = $conn->Execute($getATSSHM2BR);
	return $getPotATSSHM2BR;
}
function getATSSHMAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHMAS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1629) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHMAS = $conn->Execute($getATSSHMAS);
	return $getPotATSSHMAS;
}
function getATSSHM1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHM1AS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1705) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHM1AS = $conn->Execute($getATSSHM1AS);
	return $getPotATSSHM1AS;
}
function getATSSHM2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getATSSHM2AS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1748) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotATSSHM2AS = $conn->Execute($getATSSHM2AS);
	return $getPotATSSHM2AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getWALIT($id, $yymmTT, $yymm)
{
	global $conn;
	$getWALIT = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID  AND a.status = 1 AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND b.loanType IN (1782) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotWALIT = $conn->Execute($getWALIT);
	return $getPotWALIT;
}
function getWALITBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getWALITBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1782) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotWALITBR = $conn->Execute($getWALITBR);
	return $getPotWALITBR;
}
function getWALITAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getWALITAS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1782) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotWALITAS = $conn->Execute($getWALITAS);
	return $getPotWALITAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getMOTO1($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO1 = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1550) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotMOTO1 = $conn->Execute($getMOTO1);
	return $getPotMOTO1;
}

function getMOTO2($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO2 = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1552) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotMOTO2 = $conn->Execute($getMOTO2);
	return $getPotMOTO2;
}

function getMOTO3($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO3 = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1764) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotMOTO3 = $conn->Execute($getMOTO3);
	return $getPotMOTO3;
}

function getMOTO4($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO4 = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1765) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotMOTO4 = $conn->Execute($getMOTO4);
	return $getPotMOTO4;
}
function getMOTO1BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO1BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1550) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO1BR = $conn->Execute($getMOTO1BR);
	return $getPotMOTO1BR;
}
function getMOTO2BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO2BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1552) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO2BR = $conn->Execute($getMOTO2BR);
	return $getPotMOTO2BR;
}
function getMOTO3BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO3BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1764) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO3BR = $conn->Execute($getMOTO3BR);
	return $getPotMOTO3BR;
}
function getMOTO4BR($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO4BR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1765) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO4BR = $conn->Execute($getMOTO4BR);
	return $getPotMOTO4BR;
}
function getMOTO1AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO1AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1550) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO1AS = $conn->Execute($getMOTO1AS);
	return $getPotMOTO1AS;
}
function getMOTO2AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO2AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1552) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO2AS = $conn->Execute($getMOTO2AS);
	return $getPotMOTO2AS;
}
function getMOTO3AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO3AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1764) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO3AS = $conn->Execute($getMOTO3AS);
	return $getPotMOTO3AS;
}
function getMOTO4AS($id, $yymmTT, $yymm)
{
	global $conn;
	$getMOTO4AS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1765) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotMOTO4AS = $conn->Execute($getMOTO4AS);
	return $getPotMOTO4AS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getkecemasan($id, $yymmTT, $yymm)
{
	global $conn;
	$getkecemasan = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1852) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotkecemasan = $conn->Execute($getkecemasan);
	return $getPotkecemasan;
}
function getkecemasanBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getkecemasanBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1852) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotkecemasanBR = $conn->Execute($getkecemasanBR);
	return $getPotkecemasanBR;
}
function getkecemasanAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getkecemasanAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1852) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotkecemasanAS = $conn->Execute($getkecemasanAS);
	return $getPotkecemasanAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getsaham3B($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham3B = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID  AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1803) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotsaham3B = $conn->Execute($getsaham3B);
	return $getPotsaham3B;
}
function getsaham4B($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham4B = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.loanID = b.loanID AND a.status = 1 AND b.yrmth between '" . $yymmTT . "'  and '" . $yymm . "' AND b.loanType IN (1806) AND a.userID = c.userID AND a.userID = '" . $id . "' ";
	$getPotsaham4B = $conn->Execute($getsaham4B);
	return $getPotsaham4B;
}
function getsaham3BBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham3BBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1803) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotsaham3BBR = $conn->Execute($getsaham3BBR);
	return $getPotsaham3BBR;
}
function getsaham4BBR($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham4BBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1806) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotsaham4BBR = $conn->Execute($getsaham4BBR);
	return $getPotsaham4BBR;
}
function getsaham3BAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham3BAS = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1803) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotsaham3BAS = $conn->Execute($getsaham3BAS);
	return $getPotsaham3BAS;
}
function getsaham4BAS($id, $yymmTT, $yymm)
{
	global $conn;
	$getsaham4BAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1806) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPotsaham4BAS = $conn->Execute($getsaham4BAS);
	return $getPotsaham4BAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function gethartanah($id, $yymmTT, $yymm)
{
	global $conn;
	$gethartanah = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1860) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPothartanah = $conn->Execute($gethartanah);
	return $getPothartanah;
}
function gethartanahBR($id, $yymmTT, $yymm)
{
	global $conn;
	$gethartanahBR = " SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 2 AND a.loanID = b.loanID AND b.loanType IN (1860) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPothartanahBR = $conn->Execute($gethartanahBR);
	return $getPothartanahBR;
}
function gethartanahAS($id, $yymmTT, $yymm)
{
	global $conn;
	$gethartanahAS = "SELECT a.bondNo,b.pokok,b.untung FROM potbulan a, potbulanlook b, users c WHERE a.status = 1 AND a.loanID = b.loanID AND b.loanType IN (1860) AND a.userID = c.userID AND b.yrmth between '" . $yymmTT . "' and '" . $yymm . "' AND a.userID = '" . $id . "'";
	$getPothartanahAS = $conn->Execute($gethartanahAS);
	return $getPothartanahAS;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getsumkredit($id, $yrmth)
{
	global $conn;
	$getsumkredit = " SELECT SUM(CASE WHEN a.addminus = '1' THEN a.pymtAmt ELSE 0 END) AS kredit
		FROM transaction a,resit b WHERE a.docNo = b.no_resit AND a.yrmth = '" . $yrmth . "' AND b.kod_bank = '" . $id . "'";
	$getPotsumkredit = $conn->Execute($getsumkredit);
	return $getPotsumkredit;
}

function getsumdebit($id, $yrmth)
{
	global $conn;
	$getsumdebit = " SELECT SUM(CASE WHEN a.addminus = '0' THEN a.pymtAmt ELSE 0 END ) AS debit
		FROM transaction a,vauchers b WHERE a.docNo = b.no_baucer AND a.yrmth = '" . $yrmth . "' AND b.kod_bank = '" . $id . "'";
	$getPotsumdebit = $conn->Execute($getsumdebit);
	return $getPotsumdebit;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//GAJI SKALA 3000 Dan Ke Bawah
function getterima3000($dtFrom, $dtTo)
{
	global $conn;

	$getterima3000 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot <= '3000') 
AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.userID) AS terima1";

	$getPotterima3000 = $conn->Execute($getterima3000);
	return $getPotterima3000;
}
function getlulus3000($dtFrom, $dtTo)
{
	global $conn;
	$getlulus3000 = "SELECT SUM(mylulus) AS lulus FROM 
(SELECT COUNT(a.userID) AS mylulus FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot <= '3000') 
AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND a.status IN (3) GROUP BY a.userID) AS lulus1";
	$getPotlulus3000 = $conn->Execute($getlulus3000);
	return $getPotlulus3000;
}

function getamount3000($dtFrom, $dtTo)
{
	global $conn;
	$getamount3000 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot <= '3000') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
	$getPotamount3000 = $conn->Execute($getamount3000);
	return $getPotamount3000;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//GAJI SKALA 3001 - 5000
function getterima3001($dtFrom, $dtTo)
{
	global $conn;
	$getterima3001 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '3000' AND '5001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.userID) AS terima1";

	$getPotterima3001 = $conn->Execute($getterima3001);
	return $getPotterima3001;
}
function getlulus3001($dtFrom, $dtTo)
{
	global $conn;
	$getlulus3001 = "SELECT SUM(mylulus) AS lulus FROM 
(SELECT COUNT(a.userID) AS mylulus FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '3000' AND '5001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND a.status IN (3) GROUP BY a.userID) AS lulus1";
	$getPotlulus3001 = $conn->Execute($getlulus3001);
	return $getPotlulus3001;
}

function getamount3001($dtFrom, $dtTo)
{
	global $conn;
	$getamount3001 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '3000' AND '5001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
	$getPotamount3001 = $conn->Execute($getamount3001);
	return $getPotamount3001;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//GAJI SKALA 5001 - 10000
function getterima5001($dtFrom, $dtTo)
{
	global $conn;
	$getterima5001 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '5000' AND '10001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.userID) AS terima1";

	$getPotterima5001 = $conn->Execute($getterima5001);
	return $getPotterima5001;
}
function getlulus5001($dtFrom, $dtTo)
{
	global $conn;
	$getlulus5001 = "SELECT SUM(mylulus) AS lulus FROM 
(SELECT COUNT(a.userID) AS mylulus FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '5000' AND '10001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND a.status IN (3) GROUP BY a.userID) AS lulus1";
	$getPotlulus5001 = $conn->Execute($getlulus5001);
	return $getPotlulus5001;
}

function getamount5001($dtFrom, $dtTo)
{
	global $conn;
	$getamount5001 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot BETWEEN '5000' AND '10001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
	$getPotamount5001 = $conn->Execute($getamount5001);
	return $getPotamount5001;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//GAJI SKALA 10000 Dan Ke Atas
function getterima10000($dtFrom, $dtTo)
{
	global $conn;
	$getterima10000 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot >= '10001') 
AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.userID) AS terima1";

	$getPotterima10000 = $conn->Execute($getterima10000);
	return $getPotterima10000;
}
function getlulus10000($dtFrom, $dtTo)
{
	global $conn;
	$getlulus10000 = "SELECT SUM(mylulus) AS lulus FROM 
(SELECT COUNT(a.userID) AS mylulus FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot >= '10001') 
AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND a.status IN (3) GROUP BY a.userID) AS lulus1";
	$getPotlulus10000 = $conn->Execute($getlulus10000);
	return $getPotlulus10000;
}

function getamount10000($dtFrom, $dtTo)
{
	global $conn;
	$getamount10000 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b, loandocs c WHERE a.userID=b.userID AND a.loanID = c.loanID AND (c.gajiTot >= '10001') AND (c.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
	$getPotamount10000 = $conn->Execute($getamount10000);
	return $getPotamount10000;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//DSR 3000
function getDSR300040($dtFrom, $dtTo)
{
	global $conn;
	$getDSR300040 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay <= '3000') 
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40') GROUP BY a.userID) AS terima1";
	$getPotDSR300040 = $conn->Execute($getDSR300040);
	return $getPotDSR300040;
}

function getamountDSR300040($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR300040 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay <= '3000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40')";
	$getPotamountDSR300040 = $conn->Execute($getamountDSR300040);
	return $getPotamountDSR300040;
}

function getDSR300041($dtFrom, $dtTo)
{
	global $conn;
	$getDSR300041 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay <= '3000') 
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41') GROUP BY a.userID) AS terima1";
	$getPotDSR300041 = $conn->Execute($getDSR300041);
	return $getPotDSR300041;
}

function getamountDSR300041($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR300041 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay <= '3000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41')";
	$getPotamountDSR300041 = $conn->Execute($getamountDSR300041);
	return $getPotamountDSR300041;
}
//DSR 3001
function getDSR300140($dtFrom, $dtTo)
{
	global $conn;
	$getDSR300140 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '3001' AND '5000')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40') GROUP BY a.userID) AS terima1";
	$getPotDSR300140 = $conn->Execute($getDSR300140);
	return $getPotDSR300140;
}

function getamountDSR300140($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR300140 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '3001' AND '5000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40')";
	$getPotamountDSR300140 = $conn->Execute($getamountDSR300140);
	return $getPotamountDSR300140;
}

function getDSR300141($dtFrom, $dtTo)
{
	global $conn;
	$getDSR300141 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '3001' AND '5000')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41') GROUP BY a.userID) AS terima1";
	$getPotDSR300141 = $conn->Execute($getDSR300141);
	return $getPotDSR300141;
}

function getamountDSR300141($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR300141 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '3001' AND '5000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41')";
	$getPotamountDSR300141 = $conn->Execute($getamountDSR300141);
	return $getPotamountDSR300141;
}
//DSR 5001
function getDSR500140($dtFrom, $dtTo)
{
	global $conn;
	$getDSR500140 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '5001' AND '10000')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40') GROUP BY a.userID) AS terima1";
	$getPotDSR500140 = $conn->Execute($getDSR500140);
	return $getPotDSR500140;
}

function getamountDSR500140($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR500140 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '5001' AND '10000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40')";
	$getPotamountDSR500140 = $conn->Execute($getamountDSR500140);
	return $getPotamountDSR500140;
}

function getDSR500141($dtFrom, $dtTo)
{
	global $conn;
	$getDSR500141 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '5001' AND '10000')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41') GROUP BY a.userID) AS terima1";
	$getPotDSR500141 = $conn->Execute($getDSR500141);
	return $getPotDSR500141;
}

function getamountDSR500141($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR500141 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay BETWEEN '5001' AND '10000') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41')";
	$getPotamountDSR500141 = $conn->Execute($getamountDSR500141);
	return $getPotamountDSR500141;
}
//DSR 10000
function getDSR1000040($dtFrom, $dtTo)
{
	global $conn;
	$getDSR1000040 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay >= '10001')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40') GROUP BY a.userID) AS terima1";
	$getPotDSR1000040 = $conn->Execute($getDSR1000040);
	return $getPotDSR1000040;
}

function getamountDSR1000040($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR1000040 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay >= '10001') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr <= '40') ";
	$getPotamountDSR1000040 = $conn->Execute($getamountDSR1000040);
	return $getPotamountDSR1000040;
}

function getDSR1000041($dtFrom, $dtTo)
{
	global $conn;
	$getDSR1000041 = "SELECT SUM(myterima) AS terima FROM 
(SELECT COUNT(a.userID) AS myterima FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay >= '10001')
AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41') GROUP BY a.userID) AS terima1";
	$getPotDSR1000041 = $conn->Execute($getDSR1000041);
	return $getPotDSR1000041;
}

function getamountDSR1000041($dtFrom, $dtTo)
{
	global $conn;
	$getamountDSR1000041 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay >= '10001') AND (a.startPymtDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') AND (a.Nisbahdsr >= '41')";
	$getPotamountDSR1000041 = $conn->Execute($getamountDSR1000041);
	return $getPotamountDSR1000041;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getbilangan($id, $yr)
{
	global $conn;
	$getbilangan = "SELECT b.name,
		SUM(CASE WHEN a.addminus = '0' THEN a.pymtAmt ELSE 0 END) AS BakiAwl,
		SUM(CASE WHEN a.addminus = '1' THEN a.pymtAmt ELSE 0 END) AS BakiAkhir, 
		count(a.deductID)as Bil, 
		a.deductID FROM transaction a, general b WHERE year(a.createdDate) < '" . $yr1 . "' AND a.deductID=b.ID and a.deductID IN ('1539','1702','1709','1826','1768','1827','1644','1614','1838','1631','1613','1622','1623','1624','1736','1788','1802','1791','1804','1626','1704','1747','1850','1549','1551','1762','1763','1545','1547','1758','1759','1541','1542','1711','1712','1781','1595','1607','0') GROUP BY a.DeductID Order by 1 ASC";
	$getPotbilangan = $conn->Execute($getbilangan);
	return $getPotbilangan;
}

function getmonthfee($id)
{
	global $conn;

	$getmonthfee = "SELECT DISTINCT a.*, b.* FROM users a, userdetails b 
WHERE a.userID = b.userID AND a.userID = '" . $id . "' AND  b.status IN (1)'";
	$getPotmonthfee = $conn->Execute($getmonthfee);
	return $getPotmonthfee;
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function deductListb2($val, $sql='')
{
	global $conn;

	// Check if the 'coreID' column exists in the 'generalacc' table
	$checkColumnSQL = "SHOW COLUMNS FROM generalacc LIKE 'coreID'";
	$checkColumn = $conn->Execute($checkColumnSQL);
	$hasCoreID = ($checkColumn && $checkColumn->RowCount() > 0);

	// Default SQL query
	$sSQL = "SELECT * FROM generalacc 
             WHERE ID NOT IN (8,10,11,12,13) 
             AND category='AA' 
             AND name != BINARY UPPER(name)
             ORDER BY CAST(code AS SIGNED INTEGER) ASC";

	// Only modify $sSQL if 'coreID' exists in the table
	if ($hasCoreID) {
		if ($sql == "asetPerbelanjaan") { // PI
			$sSQL = "SELECT * FROM generalacc 
                     WHERE coreID IN (348,379,1172) 
                     AND category='AA' 
                     AND name != BINARY UPPER(name)
                     ORDER BY CAST(code AS SIGNED INTEGER) ASC";
		} elseif ($sql == "asetLiabilitiPerbelanjaan") { // PO
			$sSQL = "SELECT * FROM generalacc 
                     WHERE coreID IN (348,379,500,508,1172) 
                     AND category='AA' 
                     AND name != BINARY UPPER(name)
                     ORDER BY CAST(code AS SIGNED INTEGER) ASC";
		} elseif ($sql == "asetPendapatan") { // sebutharga, invois
			$sSQL = "SELECT * FROM generalacc 
                     WHERE coreID IN (348,379,13) 
                     AND category='AA' 
                     AND name != BINARY UPPER(name)
                     ORDER BY CAST(code AS SIGNED INTEGER) ASC";
		}
	}

	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}

	// Return based on $val
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}

function strSelect3($id, $code, $sql='', $type = "arr", $width = '')
{
	$strDeductIDList = deductListb2(1, $sql);
	$strDeductCodeList = deductListb2(2, $sql);
	$strDeductNameList = deductListb2(3, $sql);

	if ($type == "arr") {
		$name = 'perkara[' . $id . ']';
	} else {
		$name = 'perkara2';
	}

	if ($width != '') {
		$wd = "style='width:$width'";
	} else {
		$wd = '';
	}

	$strSelect = '<select name="' . $name . '" class="form-select-sm" onchange="document.MyForm.submit();" ' . $wd . '>
					<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		//$strSelect .=  '>'.$strDeductNameList[$i];
		$strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;-&nbsp;' . $strDeductNameList[$i] . '';
	}
	$strSelect .= '</select>';
	return $strSelect;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function deductListINV($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM generalacc WHERE parentID NOT IN (8,10,11,12,13) AND a_Kodkump IN (36) AND category=\'AA\'  order by CAST( code AS SIGNED INTEGER ) ASC';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}

	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}

function strSelectINV($id, $code, $type = "arr")
{
	$strDeductIDList = deductListINV(1);
	$strDeductCodeList = deductListINV(2);
	$strDeductNameList = deductListINV(3);

	if ($type == "arr") {
		$name = 'perkara[' . $id . ']';
	} else {
		$name = 'perkara2';
	}

	$strSelect = '<select name="' . $name . '" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		//$strSelect .=  '>'.$strDeductNameList[$i];
		$strSelect .=  '>' . $strDeductCodeList[$i] . '&nbsp;-&nbsp;' . $strDeductNameList[$i] . '';
	}
	$strSelect .= '</select>';
	return $strSelect;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selecttax($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AD"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

/////dasdasdsa
function strtax($id, $code, $type = "arr")
{
	$strDeductIDList = taxList(1);
	$strDeductNameList = taxList(3);

	if ($type == "arr") {
		$name = 'taxing[' . $id . ']';
	} else {
		$name = 'taxing2';
	}

	$strSelect = '<select name="' . $name . '" onchange="document.MyForm.submit();">
				<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

///////////////////////////////////////////////////


function taxList($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category=\'AD\'';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}

//////////////////////////////////////////////////////////

///sdasdas



/*function selectcodeacc($code,$name){
global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AA"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strAccCodeList = array();
		$strAccNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strAccCodeList[$nCount] = $GetData->fields('ID');
			$strAccNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
}*/


function selectcodeacc($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AA" ';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


/*function selectbatch($code,$name){
global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AG"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbatchCodeList = array();
		$strbatchNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbatchCodeList[$nCount] = $GetData->fields('ID');
			$strbatchNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
}*/


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectbatch($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AG" AND parentID NOT IN (0)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	$strSelect = '<select class="form-select-sm" name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function selectbatchINV($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE parentID IN (43)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function selectbatchPI($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE parentID IN (44)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function selectbatchBILL($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE parentID IN (46,47) ';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectbatchBAUCER($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AG" AND parentID NOT IN (48,44,43)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	$strSelect = '<select name="' . $name . '" class="form-selectx">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectaccount($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AF"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$straccountCodeList = array();
		$straccountNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$straccountCodeList[$nCount] = $GetData->fields('ID');
			$straccountNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list

	$strSelect = '<select name="' . $name . '">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($straccountCodeList); $i++) {
		$strSelect .= '	<option value="' . $straccountCodeList[$i] . '" ';
		if ($code == $straccountCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $straccountNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function selectcarabayar($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `general` WHERE category = "K" AND ID NOT IN (67,75,116,1597)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strSelectCBCodeList = array();
		$strSelectCBNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strSelectCBCodeList[$nCount] = $GetData->fields('ID');
			$strSelectCBNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list

	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strSelectCBCodeList); $i++) {
		$strSelect .= '	<option value="' . $strSelectCBCodeList[$i] . '" ';
		if ($code == $strcbCodeList[$i]) $strSelectCBSelect .= ' selected';
		$strSelect .=  '>' . $strSelectCBNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function strSelectCB($id, $code, $type = "arr")
{
	$strDeductIDList = cbList(1);
	$strDeductNameList = cbList(3);

	if ($type == "arr") {
		$name = 'carabayar[' . $id . ']';
	} else {
		$name = 'carabayar2';
	}

	$strSelect = '<select name="' . $name . '" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= '	<option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function cbList($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM `general` WHERE category = "K" AND ID NOT IN (67,75,116,1597)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}


function getFeess($id)
{
	global $conn;
	$getWajibOpen = "SELECT 
			SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE pymtAmt END ) AS totalWajib
			FROM transactionacc
			WHERE
			pymtRefer = '" . $id . "' 
			GROUP BY pymtRefer";
	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) $totalFees = $rsWajibOpen->fields(totalWajib);
	else $totalFees = 0;
	return $totalFees;
}

function getFe($id)
{
	global $conn;

	$getWajibOpen = "
	SELECT  SUM(CASE WHEN a.addminus = '0' THEN -a.pymtAmt ELSE 0 END) AS yuranDb, 
			SUM(CASE WHEN a.addminus = '1' THEN -a.pymtAmt ELSE 0 END) AS yuranKt
			FROM transactionacc a, singleentry b
			WHERE
			a.docNo=b.SENO AND a.batchNo = " . $id . "
			GROUP BY a.batchNo";

	$rsWajibOpen = $conn->Execute($getWajibOpen);
	if ($rsWajibOpen->RowCount() == 1) {
		$DB = $rsWajibOpen->fields(yuranDb); //$rsWajibOpen->fields(yuranKt) - $rsWajibOpen->fields(yuranDb);
		$KT = $rsWajibOpen->fields(yuranKt);
		$bakiAwal = ($KT - $DB);
	} else $bakiAwal = 0;

	return $bakiAwal;
}


function selectbanks($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE a_class IN (132)';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select class="form-selectx" name="' . $name . '">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function selectbanks1($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category = "AF"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select class="form-selectx" name="' . $name . '">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectbayar($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM general WHERE category = "K"';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list	
	$strSelect = '<select name="' . $name . '" class="form-select-sm">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function selectproject($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AI" ';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function selectjabatan($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM `generalacc` WHERE category = "AH" ';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-sm">
				<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function strproject($id, $code, $type = "arr", $width = '')
{
	$strDeductIDList = projectList(1);
	$strDeductNameList = projectList(3);

	if ($type == "arr") {
		$name = 'projecting[' . $id . ']';
	} else {
		$name = 'projecting2';
	}

	if ($width != '') {
		$wd = "style='width:$width'";
	} else {
		$wd = '';
	}

	$strSelect = '<select name="' . $name . '" class="form-select-sm" onchange="document.MyForm.submit();" ' . $wd . ' >
<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= ' <option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function projectList($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category=\'AI\'';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}




/////////////////////////////jabatan///////////////////////////////


function strjabatan($id, $code, $type = "arr")
{
	$strDeductIDList = jabatanList(1);
	$strDeductNameList = jabatanList(3);

	if ($type == "arr") {
		$name = 'jabatan1[' . $id . ']';
	} else {
		$name = 'jabatan2';
	}

	$strSelect = '<select name="' . $name . '" class="form-select-sm" onchange="document.MyForm.submit();">
	<option value="">- Kod -';
	for ($i = 0; $i < count($strDeductIDList); $i++) {
		$strSelect .= ' <option value="' . $strDeductIDList[$i] . '" ';
		if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strDeductNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}

function jabatanList($val)
{
	global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category=\'AH\'';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list
	if ($val == 1) return $strDeductIDList;
	if ($val == 2) return $strDeductCodeList;
	if ($val == 3) return $strDeductNameList;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getListOpenAccount($id, $yrmth)
{
	global $conn;

	$getOpenAccount = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '" . $id . "' AND yrmth = '" . $yrmth . "'";
	$getAkaun = $conn->Execute($getOpenAccount);
	return $getAkaun;
}


function getListOpenAccountB($id)
{
	global $conn;

	$getOpenAccount = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '" . $id . "'";
	$getAkaun = $conn->Execute($getOpenAccount);
	return $getAkaun;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectsyarikat($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AB","AC")';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-xs">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectsyarikatAC($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AC")';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-xs">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function selectsyarikatAB($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AB")';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-xs">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function selectsyarikatAK($code, $name)
{
	global $conn;
	//get list of admin value into array
	$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AK")';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strbankCodeList = array();
		$strbankNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strbankCodeList[$nCount] = $GetData->fields('ID');
			$strbankNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}
	//end get list



	$strSelect = '<select name="' . $name . '" class="form-select-xs">
					<option value="">- Pilih -';
	for ($i = 0; $i < count($strbankCodeList); $i++) {
		$strSelect .= '	<option value="' . $strbankCodeList[$i] . '" ';
		if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
		$strSelect .=  '>' . $strbankNameList[$i];
	}
	$strSelect .= '</select>';
	return $strSelect;
}


function selectinvestors($code,$name){
		global $conn;
			//get list of admin value into array
			$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AK") ORDER BY name';
			$GetData = $conn->Execute($sSQL);
			if ($GetData->RowCount() <> 0) {
				$strbankCodeList = array();
				$strbankNameList = array();
				$nCount = 0;
				while (!$GetData->EOF) {
					$strbankCodeList[$nCount] = $GetData->fields('ID');
					$strbankNameList[$nCount] = $GetData->fields('name');
					$GetData->MoveNext();
					$nCount++;
				}
			}
			//end get list
		
		
		
		$strSelect = '<select name="'.$name.'" class="form-select-xs">
						<option value="">- Pilih -';
					for ($i = 0; $i < count($strbankCodeList); $i++) {
						$strSelect .= '	<option value="'.$strbankCodeList[$i].'" ';
						if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
						$strSelect .=  '>'.$strbankNameList[$i];
					}
		$strSelect .= '</select>';
		return $strSelect;
		}



function deductListINV1($val){
global $conn;
	//get list of deduction value into array
	$sSQL = 'SELECT * FROM generalacc WHERE parentID NOT IN (8,10,11,12,13) AND a_Kodkump IN (36,35) AND category=\'AA\' ORDER BY CAST( code AS SIGNED INTEGER ) ASC';
	$GetData = $conn->Execute($sSQL);
	if ($GetData->RowCount() <> 0) {
		$strDeductIDList = array();
		$strDeductCodeList = array();
		$strDeductNameList = array();
		$nCount = 0;
		while (!$GetData->EOF) {
			$strDeductIDList[$nCount] = $GetData->fields('ID');
			$strDeductCodeList[$nCount] = $GetData->fields('code');
			$strDeductNameList[$nCount] = $GetData->fields('name');
			$GetData->MoveNext();
			$nCount++;
		}
	}

	//end get list
 if($val==1) return $strDeductIDList;
 if($val==2) return $strDeductCodeList;
 if($val==3) return $strDeductNameList;
}

function strSelectINV1($id,$code,$type="arr"){
$strDeductIDList = deductListINV1(1);
$strDeductCodeList = deductListINV1(2);
$strDeductNameList = deductListINV1(3);

if($type=="arr"){
	$name = 'perkara['.$id.']';
}else{
	$name = 'perkara2';
}

$strSelect = '<select name="'.$name.'" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Kod -';
			for ($i = 0; $i < count($strDeductIDList); $i++) {
				$strSelect .= '	<option value="'.$strDeductIDList[$i].'" ';
				if ($code == $strDeductIDList[$i]) $strSelect .= ' selected';
				//$strSelect .=  '>'.$strDeductNameList[$i];
				$strSelect .=  '>'.$strDeductCodeList[$i] .'&nbsp;-&nbsp;'.$strDeductNameList[$i].'';
			}
$strSelect .= '</select>';
return $strSelect;
}

function selectPelabur($code,$name){
	global $conn;
		//get list of admin value into array
		$sSQL = 'SELECT * FROM generalacc WHERE category IN ("AK") ORDER BY name';
		$GetData = $conn->Execute($sSQL);
		if ($GetData->RowCount() <> 0) {
			$strbankCodeList = array();
			$strbankNameList = array();
			$nCount = 0;
			while (!$GetData->EOF) {
				$strbankCodeList[$nCount] = $GetData->fields('ID');
				$strbankNameList[$nCount] = $GetData->fields('name');
				$GetData->MoveNext();
				$nCount++;
			}
		}
		//end get list
	
	
	
	$strSelect = '<select name="'.$name.'" class="form-select-xs">
					<option value="">- Pilih -';
				for ($i = 0; $i < count($strbankCodeList); $i++) {
					$strSelect .= '	<option value="'.$strbankCodeList[$i].'" ';
					if ($code == $strbankCodeList[$i]) $strSelect .= ' selected';
					$strSelect .=  '>'.$strbankNameList[$i];
				}
	$strSelect .= '</select>';
	return $strSelect;
	}


function selectTermPayment($code,$name){
	global $conn;
		//get list of term value into array
		$sSQL = 'SELECT * FROM generalacc WHERE category = "AL"';
		$GetData = $conn->Execute($sSQL);
		if ($GetData->RowCount() <> 0) {
			$strTermCodeList = array();
			$strTermNameList = array();
			$nCount = 0;
			while (!$GetData->EOF) {
				$strTermCodeList[$nCount] = $GetData->fields('ID');
				$strTermNameList[$nCount] = $GetData->fields('name');
				$GetData->MoveNext();
				$nCount++;
			}
		}
		//end get list



	$strSelect = '<select class="form-select-sm" name="'.$name.'">
					<option value="">- Pilih -';
				for ($i = 0; $i < count($strTermCodeList); $i++) {
					$strSelect .= '    <option value="'.$strTermCodeList[$i].'" ';
					if ($code == $strTermCodeList[$i]) $strSelect .= ' selected';
					$strSelect .=  '>'.$strTermNameList[$i];
				}
	$strSelect .= '</select>';
	return $strSelect;
	}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////