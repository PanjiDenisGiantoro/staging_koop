<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	letter.php
 *
 **********************************************************************************/
include("common.php");
include("koperasiQry.php");
include("letter.inc.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

$letterID = $code;

$type			= strtoupper($type);
$code			= explode(":", $code);
$id				= explode(":", $id);
$boardDate		= date("d/m/Y");
$chequeDate		= date("d/m/Y");

$header =
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
	. '<html>'
	. '<head>'
	. '<title>' . $emaNetis . '></title>'
	. '<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">'
	. '<meta http-equiv="pragma" content="no-cache">'
	. '<meta http-equiv="expires" content="0">'
	. '<meta http-equiv="cache-control" content="no-cache">'
	. '<style type="text/css">
body {
	font: normal normal 13px/normal Verdana, sans-serif;
	margin: 0px;
	color: #000033;
}
table.lightgrey{
	font: bold normal 13px/normal Verdana, sans-serif;
	color: #000033;
	background-color: #f0f0f0;	
}
th {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #000033;
}
td {
	font: normal normal 13px/normal Verdana, sans-serif;
}
td.padding1 {
	font: normal normal 13px/normal Verdana, sans-serif;
	padding: 4.0pt 0pt 4.0pt 0pt;
	vertical-align: top;
}
td.padding2 {
	font: normal normal 13px/normal Verdana, sans-serif;
	padding: 4.0pt 6.0pt 4.0pt 6.0pt;
	vertical-align: top;
}
td.headerblue {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
	background-color: #0c479d;
	padding: 4px 8px 4px 8px;
}
td.headerorange {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
	background-image:url(shade-bkrm-04.gif);
	line-height:20px;
	padding: 2px 4px 2px 4px;
}
td.borderallblue {
	border-left: 1px solid #0c479d;
	border-right: 1px solid #0c479d;
	border-top: 1px solid #0c479d;
	border-bottom: 1px solid #0c479d;
}
td.borderall2 {
	border-left: 1px solid #df6403;
	border-right: 1px solid #df6403;
	border-top: 1px solid #df6403;
	border-bottom: 1px solid #df6403;
}
td.borderleftrightbottomblue {
	border-left: 1px solid #0c479d;
	border-right: 1px solid #0c479d;
	border-bottom: 1px solid #0c479d;
}
td.borderleftrightbottom2 {
	border-left: 1px solid #df6403;
	border-right: 1px solid #df6403;
	border-bottom: 1px solid #df6403;
}
td.bordertop1 {
	border-top: 1px solid #aaaaaa;
}
td.bordertop2 {
	border-top: 1px solid #aaaaaa;
}
ol.decimal {
	list-style-position: inside;
	list-style-type: decimal;
	padding-left: 0pt;
}
ol.none {
	list-style-position: inside;
	list-style-type: none;
	padding-left: 0pt;
	page
}
p.none{
	margin: 0px;
	padding: 0px;
}
p.spacing{
	line-height: 20px;
}form {
	font: normal normal 13px/normal Verdana, sans-serif;
	margin: 0px;
	padding: 0px;
}
input {
	font: normal normal 13px/normal Verdana, sans-serif;
	border-top: 1px solid #AAAAAA;
	border-bottom: 1px solid #AAAAAA;
	border-right: 1px solid #AAAAAA;
	border-left: 1px solid #AAAAAA;
}
input.none {
	font: normal normal 13px/normal Verdana, sans-serif;
	border: none;
}
select {
	font: normal normal 13px/normal Verdana, sans-serif;
	border-top: 1px solid #AAAAAA;
	border-bottom: 1px solid #AAAAAA;
	border-right: 1px solid #AAAAAA;
	border-left: 1px solid #AAAAAA;
}
textarea {
	font: normal normal 13px/normal Verdana, sans-serif;
	border-top: 1px solid #AAAAAA;
	border-bottom: 1px solid #AAAAAA;
	border-right: 1px solid #AAAAAA;
	border-left: 1px solid #AAAAAA;
}
a:link {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: none;
}
a:visited {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: none;
}
a:hover {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
	text-decoration: none;
}
a.blue:link {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: none;
}
a.blue:visited {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: none;
}
a.blue:hover {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: underline;
}
a.orange:link {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
	text-decoration: none;
}
a.orange:visited {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
	text-decoration: none;
}
a.orange:hover {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
	text-decoration: underline;
}
a.white:link {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #ffffff;
	text-decoration: none;
}
a.white:visited {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #ffffff;
	text-decoration: none;
}
a.white:hover {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
	text-decoration: none;
}
a.darkgrey:link {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #3d3d3d;
	text-decoration: none;
}
a.darkgrey:visited {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #3d3d3d;
	text-decoration: none;
}
a.darkgrey:hover {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	text-decoration: underline;
}
div {
	font: normal normal 13px/normal Verdana, sans-serif;
}
div.headerblue {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
	background-color: #0c479d;
	line-height:20px;
	padding: 2px 4px 2px 4px;
}
div.headerorange {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
	background-image:url(shade-bkrm-04.gif);
	line-height:20px;
	padding: 2px 4px 2px 4px;
}
div.none {
	margin: 0px;
	padding: 0px;
}
div.white {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
}

div.black {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #000000;
}
div.darkgrey {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #3d3d3d;
}
div.orange {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
}
div.blue {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
}
div.nav {
	font: normal normal 13px/normal Verdana, sans-serif;
	padding: 2px 4px 2px 4px;
}
div.navmain {
	font: normal normal 13px/normal Verdana, sans-serif;
	padding: 2px 8px 2px 8px;
}
div.blue3 {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
	background-color: #f0f0f0;
	padding: 4px
}
div.copyright {
	font: normal 9px/normal Verdana, sans-serif;
	color: #ffffff;
	padding: 2px 4px 2px 4px;
}
    .scrolls { 
        overflow-x: scroll;
        overflow-y: hidden;
        height: 80px;
    white-space:nowrap
    } 

font.blue {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #0c479d;
}
font.orange {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #df6403;
}
.lineBG {
	background-color: #111111
}
.tableBG {
	background-color: #f0f0f0
}
.rightBG {
	background-color: #f0f0f0;
}
.actionBG {
	background-color: #f0f0f0;
}
.menu {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000000;
}
.menuText {
	font-size: 11px;
	font-family: Poppins,Helvetica,Sans-serif;
	color: yellow
}
.menuBold {
	font-size: 11px;
	font-family: Poppins,Helvetica,Sans-serif;
	color: #000000;
	background-color: #FFFFFF;
	font-weight: bold;
}
.MenuDisable {
	font-size: 11px;
	font-family: Poppins,Helvetica,Sans-serif;
	color: #999999;
	background-color: #0c479d
}
.textFont {
	font-size: 13px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000033;
}
.dateFont {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #800000;
}
.but {
	font-size: 11px;
}
.menuButton {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #FFFFFF;
	background-color: #006699;
	width: 300px;
	margin-bottom : 5px;
}
.inputDisable {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000000; font-weight : bold;
	background-color: #f0f0f0
}
.entry {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000000;
	background-color: #EDEDED;
}

.Header {
	font: normal normal 13px/normal Verdana, sans-serif;
	color: #FFFFFF;
	background-color: #0c765d;
	padding: 4px 8px 4px 8px;
}
.Label {
	font: bold normal 13px/normal Verdana, sans-serif;
	color: #000033;
}
.Data {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000033;
	background-color: #f0f0f0
}
.DataB {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000033;
	background-color: #f0f0f0;
	font-weight: bold;
}
.contentH {
	font-size: 10pt;
	font-family: Verdana,Poppins,Sans-serif;
	color: #FFFFFF;
	font-weight: bold;
}
.contentD {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #666666;
}
.Section {
	font-size: 10pt;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000033;
	background-color: #CCCC99;
	font-weight: bold;
}

.ErrData {
	font-size: 11px;
	font-family: Poppins,Helvetica,Sans-serif;
	color: #000000;
	background-color: #FF0000
}
.redText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #FF0000
}
.blueText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #0000CC
}
.blackText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000033
}
.yellowText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #FFCC00
}
.whiteText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #FFFFFF
}
.greenText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #009900
}
.navyText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #000080
}
.maroonText {
	font-size: 11px;
	font-family: Verdana,Poppins,Sans-serif;
	color: #800000
}
.footer {
	font-size: 9px;
	font-family: Verdana,Poppins,sans-serif;
	color:#800000
}
#foldlist {
	list-style-image:url(tri.gif)
}
#node {
	list-style-image:url(node.gif)
}
#print {
	list-style-image:url(sym-printer-bkrm-01.gif)
}
</style>'
	. '</head>'
	. '<body>';
$footer = '</body></html>';

for ($i = 0; $i < count($id); $i++) {
	$letterbody[$i] = '';
	if ($group == 0 or $group == 3 or $group == 4 or $group == 5 or $group == 6) {
		$letterObj = new CLetter();
	} else if ($group == 1 or $group == 2 or $group == 7) {
		$letterObj = new CLetterLoan();
	}
	$letterObj->SetGroup($group);
	for ($j = 0; $j < count($code); $j++) {
		$sql = "SELECT codeName,title, header,subject,content,footer FROM letters 
				where ID = " . tosql($code[$j], "Text");
		$Get =  &$conn->Execute($sql);

		$letterObj->SetCodeName($Get->fields(codeName));
		$letterObj->SetHeader($Get->fields(header));
		$letterObj->SetSubject($Get->fields(subject));
		$letterObj->SetContent($Get->fields(content));
		$letterObj->SetFooter($Get->fields(footer));

		$subject = $Get->fields(title);
		//print '<br>'.
		$letterObj->SetType($type);
		$letterObj->SetHeadLetter($head);
		$referID = $id[$i];

		if ($group == 2 || $group == 7) $userID = dlookup("loans", "userID", "loanID=" . tosql($id[$i], "Number"));
		else $userID = $id[$i];
		//if ($j == 0) {
		//if ($group == 0 OR $group == 4) {   

		$sSQL = "";
		$sSQL = "SELECT	DISTINCT a.*, b.* FROM users a, userdetails b";
		$sWhere = " a.userID = b.userID ";
		$sWhere .= " AND b.userID = '" . $userID . "'";
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL = $sSQL . $sWhere;
		$GetData = &$conn->Execute($sSQL);

		//$GetData		= ctMemberAll($userID);
		$memberStatus	= $GetData->fields('status');
		$memberStatusT	= $GetData->fields('statusT');
		$loginID		= $GetData->fields('loginID');
		$memberName		= $GetData->fields('name');

		$letterObj->SetReceivedDate(toDate('d/m/Y', $GetData->fields('applyDate')));
		$letterObj->SetApprovedDate(toDate('d/m/Y', $GetData->fields('approvedDate')));
		$letterObj->SetRejectedDate(toDate('d/m/Y', $GetData->fields('rejectedDate')));

		if ($memberStatus == 3) {
			$GetDataT		= ctMemberTerminate('', $id[$i]);
			$letterObj->SetReceivedDateT(toDate('d/m/Y', $GetDataT->fields('applyDate')));
			$letterObj->SetApprovedDateT(toDate('d/m/Y', $GetDataT->fields('approvedDate')));
			$letterObj->SetRejectedDateT(toDate('d/m/Y', $GetDataT->fields('rejectedDate')));
		}

		$monthFee		=  $GetData->fields('monthFee');
		$yr = date("Y");
		$totalFee = getFees($userID, $yr);
		$totalFeeLast = getFees($userID, $yr - 1);
		//$totalFee		=  $GetData->fields('totalFee');
		$address 		=  $GetData->fields('address');
		$postcode	   	=  $GetData->fields('postcode');
		$city			=  $GetData->fields('city');
		$stateID		=  $GetData->fields('stateID');
		$state			=  dlookup("general", "name", "ID=" . tosql($stateID, "Number"));
		$sex			=  $GetData->fields('sex');
		$maritalID		=  $GetData->fields('maritalID');
		$religionID		=  $GetData->fields('religionID');
		$deptID			=  $GetData->fields('departmentID');
		$department		=  dlookup("general", "name", "ID=" . tosql($deptID, "Number"));
		$departmentAdd	=  dlookup("general", "b_Address", "ID=" . tosql($deptID, "Number"));
		$departmentAdd	=  str_replace(",", ",<br>", $departmentAdd);
		$memberID		=  $GetData->fields('memberID');
		$staffID		=  $GetData->fields('staftNo');
		$memberNewIC	=  $GetData->fields('newIC');
		$memberOldIC	=  $GetData->fields('oldIC');
		$accTabungan	=  $GetData->fields('accTabungan');

		$letterObj->SetMonthFee($monthFee);
		$letterObj->SetTotalFee($totalFee);
		$letterObj->SetTotalFeeLast($totalFeeLast);

		//$letterObj->GetTotalFee();
		$letterObj->SetAddress($address);
		$letterObj->SetBankAddress($departmentAdd);
		$letterObj->SetPostcode($postcode);
		$letterObj->SetCity($city);
		$letterObj->SetState($state);
		$letterObj->SetInitial($sex, $maritalID);
		$letterObj->SetGreeting($religionID);
		$letterObj->SetDepartment($department);
		$letterObj->SetName($memberName);
		$letterObj->SetMemberID($memberID);
		$letterObj->SetStaffID($staffID);
		$letterObj->SetLoginID($loginID);
		$letterObj->SetNewIC(convertNewIC($memberNewIC));
		$letterObj->SetOldIC($memberNewIC);
		$letterObj->SetFundAccount($accTabungan);
		if ($type == 'EMAIL') {
			$emailto[$i] = $GetData->fields('email');
		}

		if ($group == 1 || $group == 2 || $group == 7) {
			$sSQL = "";
			$sSQL = "SELECT	a.*, b.* FROM loans a, loandocs b";
			$sWhere = "";
			$sWhere = " a.loanID = '" . $id[$i] . "'";
			$sWhere = " WHERE " . $sWhere;
			$sSQL = $sSQL . $sWhere . 'and a.loanID= b.loanID ORDER BY a.applyDate DESC';
			$GetDataL = &$conn->Execute($sSQL);
			$loanType				= $GetDataL->fields('loanType');
			$loanCode				= dlookup("general", "code", "ID=" . $loanType);

			$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);
			$letterRefer			= $codegroup;
			$loanStatus				= $GetDataL->fields('status');

			$letterObj->SetReceivedDateL(toDate('d/m/Y', $GetDataL->fields('applyDate')));
			$letterObj->SetApprovedDateL(toDate('d/m/Y', $GetDataL->fields('approvedDate')));
			$letterObj->SetRejectedDateL(toDate('d/m/Y', $GetDataL->fields('rejectedDate')));

			$strRefNum	= $GetDataL->fields('loanNo');
			$strkomoditi = $GetDataL->fields('no_sijil');
			$strkdruntung = $GetDataL->fields('kadar_u');
			$loanName	= dlookup("general", "name", "ID=" . $GetDataL->fields('loanType'));
			$strbond	= strtoupper($GetDataL->fields('rnoBond'));
			$letterObj->SetLoanID($id[$i]);
			$letterObj->SetRefNum($strRefNum);
			$letterObj->SetLoanName(ucwords(strtolower($loanName)));
			$letterObj->SetLoanBondNum($strbond);
			$letterObj->Setkomoditi($strkomoditi);
			$item	= dlookup("general", "name", "ID=" . $GetDataL->fields('itemType'));
			$letterObj->Setitem($item);
			$letterObj->Setkdruntung($strkdruntung);

			$yrDif = intval($GetDataL->fields('loanPeriod') / 12);
			$mthDif = bcmod($GetDataL->fields('loanPeriod'), '12');

			$dif = $mthDif;
			if ($dif > 12) {
				$mthend = $dif - 12;
				$yrend = $yrDif + 1;
			} else {
				$mthend = $dif;
				$yrend = $yrDif;
			}

			if ($GetDataL->fields('loanPeriod') < 12) $nLoanPeriod = $GetDataL->fields('loanPeriod') . ' BULAN';
			elseif ($mthend < 1) $nLoanPeriod = $yrend . ' TAHUN ';
			else $nLoanPeriod = $yrend . 'TAHUN' . $mthend . 'BULAN';

			$letterObj->SetLoanPeriod($nLoanPeriod);
			$letterObj->SetLoanPeriodMonth($GetDataL->fields('loanPeriod'));
			$letterObj->SetLoanPeriodMonthLessOne($GetDataL->fields('loanPeriod') - 1);

			$fLoanAmount		= floatval($GetDataL->fields('loanAmt'));
			$fLoanProfitRate 	= floatval($GetDataL->fields('kadar_u') * 1.77);
			$fLoanAmtProfit 	= floatval($GetDataL->fields('lpotBiayaN'));
			$clsRM->setValue($fLoanAmount);
			$strTotal = ucwords($clsRM->getValue());
			$letterObj->SetLoanAmount($fLoanAmount);
			$letterObj->SetLoanProfitRate($fLoanProfitRate);
			$letterObj->SetLoanAmtProfit($fLoanAmtProfit);
			$letterObj->SetLoanAmountStr($strTotal);
			$clsRM->setValue($fLoanAmtProfit);
			$strTotal2 = ucwords($clsRM->getValue());
			$letterObj->SetLoanJualanStr($strTotal2);

			$clsRM->setValue($GetDataL->fields('pokok'));
			$strTotal = ucwords($clsRM->getValue());
			$letterObj->SetmonthlyStr($strTotal);

			$clsRM->setValue($GetDataL->fields('pokokAkhir'));
			$strTotal = ucwords($clsRM->getValue());
			$letterObj->SetlastmonthlyStr($strTotal);

			$clsRM->setValue($GetDataL->fields('untung'));
			$strTotal = ucwords($clsRM->getValue());
			$letterObj->SetbenefitStr($strTotal);

			$clsRM->setValue($GetDataL->fields('untungAkhir'));
			$strTotal = ucwords($clsRM->getValue());
			$letterObj->SetlastbenefitStr($strTotal);

			$letterObj->SetmonthlyAmt($GetDataL->fields('pokok'));
			$letterObj->SetlastmonthlyAmt($GetDataL->fields('pokokAkhir'));
			$letterObj->SetbenefitAmt($GetDataL->fields('untung'));
			$letterObj->SetlastbenefitAmt($GetDataL->fields('untungAkhir'));

			$letterObj->SettotalPayed($GetDataL->fields('lpotBiayaN') - $GetDataL->fields('outstandingAmt'));

			//--------------------------------------------------------------------------------------------
			$yr = date("Y");
			$strloanbal = loanbalanceall($userID, $yr);
			//$strloanbalce1 = ($strloanbal->fields('a.loanAmt'));
			$strloanballast = loanbalanceall($userID, $yr - 1);
			$letterObj->SettotalBalance($strloanbal);
			$letterObj->SettotalBalanceLast($strloanballast);
			//$letterObj->GettotalBalance();
			//--------------------------------------------------------------------------------------------
			$letterObj->Setbayarankhidmat($GetDataL->fields('btindihCaj'));

			$pid1					= $GetDataL->fields('penjaminID1');
			$guarantorName1 		= dlookup("users", "name", "userID=" .	tosql($pid1, "Text"));
			$guarantorNum1			= dlookup("userdetails", "memberID", "userID=" . tosql($pid1, "Text"));
			$guarantorNewIC1		= convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($pid1, "Text")));
			$pid2					= $GetDataL->fields('penjaminID2');
			$guarantorName2 		= dlookup("users", "name", "userID=" . tosql($pid2, "Text"));
			$guarantorNum2			= dlookup("userdetails", "memberID", "userID=" . tosql($pid2, "Text"));
			$guarantorNewIC2		= convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($pid2, "Text")));
			$pid3					= $GetDataL->fields('penjaminID3');
			$guarantorName3 		= dlookup("users", "name", "userID=" . tosql($pid3, "Text"));
			$guarantorNum3			= dlookup("userdetails", "memberID", "userID=" . tosql($pid3, "Text"));
			$guarantorNewIC3		= convertNewIC(dlookup("userdetails", "newIC", "userID=" . tosql($pid3, "Text")));
			$letterObj->SetGuarantor1($guarantorName1, $guarantorNum1, $guarantorNewIC1);
			$letterObj->SetGuarantor2($guarantorName2, $guarantorNum2, $guarantorNewIC2);
			$letterObj->SetGuarantor3($guarantorName3, $guarantorNum3, $guarantorNewIC3);
		} else if ($group == 3) {

			$GetDataD			= ctDividenID($id[$i]);

			$startYear			= $GetDataD->fields('startYear');
			$divID				= tosql($GetDataD->fields('userID'), "Text");
			$memberName			= dlookup("users", "name", "userID=" . $divID);
			$loginID			= dlookup("users", "loginID", "userID=" . $divID);

			$yr = date("Y");
			$yrs = $yr - 1;
			$getOpen_div = "SELECT *
					FROM dividen
					WHERE yearDiv = '" . $yrs . "' AND status = '1'
					AND userID = '" . $userID . "'";

			$rsOpen_div = $conn->Execute($getOpen_div);
			$fDividenAmount		= $rsOpen_div->fields(AmtDiv);

			$getOpen_divtunai = "SELECT *
					FROM transaction
					WHERE deductID = '1701'
					AND userID = '" . $userID . "'";

			$rsOpen_divtunai = $conn->Execute($getOpen_divtunai);
			$fDividenBonus		= $rsOpen_divtunai->fields(pymtAmt);
			$fDividenPercentage	= 0.0;

			$letterObj->SetDividenAmount($fDividenAmount);
			$letterObj->SetDividenBonus($fDividenBonus);
			$letterObj->SetDividenPercentage($fDividenPercentage);
		} else if ($group == 6) {

			//$kenderaanID	=  $GetData->fields('ID');
			//$noplat		=  dlookup("insurankenderaan", "NoKenderaan", "ID=" . tosql($kenderaanID, "Number"));

			$sSQL = "SELECT * FROM insurankenderaan WHERE NoAnggota = '" . $userID . "' ORDER BY TarikhTamatInsuran DESC";

			$GetDataK = &$conn->Execute($sSQL);

			$letterObj->SetTarikhTamatInsuran(toDate('d/m/Y', $GetDataK->fields('TarikhTamatInsuran')));
			$letterObj->SetNoKenderaan($GetDataK->fields('NoKenderaan'));
		}
		$letterObj->InitSyntax();

		$letterbody[$i] .= $letterObj->PrintPage();
	}
} // END FOR

if ($type == 'SURAT') {
	print $header;
	for ($i = 0; $i < count($id); $i++) {
		if ($letterbody[$i] <> '') {
			print $letterbody[$i];
		}

		$sSQL	= "INSERT INTO letterLog (" .
			"userID," .
			"letterGroup," .
			"letterID," .
			"letterRefer," .
			"referID," .
			"type," .
			"senderID," .
			"sendDate," .
			"sendBy)" .
			" VALUES (" .
			tosql($userID, "Text") . "," .
			tosql($group, "Number") . "," .
			tosql($letterID, "Text") . "," .
			tosql($letterRefer, "Text") . "," .
			tosql($referID, "Text") . "," .
			tosql($type, "Text") . "," .
			tosql(get_session("Cookie_userID"), "Text") . "," .
			tosql(date("Y-m-d H:i:s"), "Text") . "," .
			tosql(get_session("Cookie_userName"), "Text") . ")";
		$rs = &$conn->Execute($sSQL);

		if ($rs) {
			activityLog($sSQL, $activity[$i], get_session('Cookie_userID'), get_session("Cookie_userName"), 1);
		}
	}
	print '<script>window.print();</script>' . $footer;
} else if ($type == 'EMAIL') {
	$emailto[$i] = $GetData->fields('email');
	print $header;
	$ccto = "noreply@ikoop.com.my";
	for ($i = 0; $i < count($id); $i++) {
		// Email headers for support person
		$mailheader = "MIME-Version:1.0\n";
		$mailheader .= "Content-Type:text/html;charset=iso-8859-1\n";
		$mailheader .= "From: iKOOP\r\n";
		$mailheader .=	"Cc: " . $ccto . "\r\n";
		$mailheader .= "X-Mailer: PHP/" . phpversion() . "\n";

		if ($emailto[$i] <> '') {
			$result = mail($emailto[$i], $subject, $header . $letterbody[$i] . $footer, $mailheader);

			if (!$result) {
				$activity[$i] .= 'E-mel Gagal Dihantar- Masalah Rangkaian!';
				$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
					" VALUES ('" . $activity[$i] . "', ' ', ' ', '" . get_session('Cookie_userID') . "','" . date("Y-m-d H:i:s") . "', '" . get_session("Cookie_userName") . "', '1')";
				$rs = &$conn->Execute($sqlAct);
			} else {
				///				$activity[$i] .= 'E-mel berjaya dihantar - '. $userID .' - ' . dlookup("letters", "title", "ID=" .$letterID) . ' !';
				$activity[$i] .= 'E-mel berjaya dihantar - ' . $userID;
				$sSQL	= "INSERT INTO letterLog (" .
					"userID," .
					"letterGroup," .
					"letterID," .
					"letterRefer," .
					"referID," .
					"type," .
					"senderID," .
					"sendDate," .
					"sendBy)" .
					" VALUES (" .
					tosql($userID, "Text") . "," .
					tosql($group, "Number") . "," .
					tosql($letterID, "Text") . "," .
					tosql($letterRefer, "Text") . "," .
					tosql($referID, "Text") . "," .
					tosql($type, "Text") . "," .
					tosql(get_session("Cookie_userID"), "Text") . "," .
					tosql(date("Y-m-d H:i:s"), "Text") . "," .
					tosql(get_session("Cookie_userName"), "Text") . ")";
				$rs = &$conn->Execute($sSQL);
				if ($rs) {
					//activityLog($sSQL, $activity[$i], get_session('Cookie_userID'), get_session('Cookie_userName'));
					$sqlAct = "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
						" VALUES ('" . $activity[$i] . "', 'INSERT', '" . str_replace("'", "", $sSQL) . "', '" . get_session('Cookie_userID') . "','" . date("Y-m-d H:i:s") . "', '" . get_session("Cookie_userName") . "', '1')";
					$rs = &$conn->Execute($sqlAct);
				}
			}
			$temp = '<hr size="1" />' . $activity[$i] . '<hr size="1" />' . $letterbody[$i] . '<hr size="1" /><br />';

			print $temp;
		} else {
			$activity[$i] .= 'E-mel Gagal Dihantar! - Tiada Emel';
			$temp = '<hr size="1" />' . $activity[$i] . '<hr size="1" /><br />';
			activityLog('NONE no sql', $activity[$i], get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

			print $temp;
		}
	}
	print $footer;
}


function loanbalanceall($pk, $yr)
{

	global $conn;

	$sqlGet = "SELECT a.loanID, a.loanAmt, a.userID, b.ajkDate2 "
		. " FROM loans a, loandocs b "
		. " WHERE a.loanID = b.loanID and "
		. " (a.userID = '" . $pk . "') "
		. " AND b.rnoBaucer <> '' "
		. " AND a.status IN ('3','7') ORDER BY applyDate DESC";

	$GetLoan =  &$conn->Execute($sqlGet);


	if ($GetLoan->RowCount() <> 0) {

		$bil = 1;
		//print '';
		//$strloanval = '<table>';
		while (!$GetLoan->EOF) {
			//----
			//$loanBalance = $GetLoan->fields(outstandingAmt);

			//------------------- actively check balance from transaction ---------------------
			$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
						FROM loans where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sqlLoan);

			if ($Get->RowCount() > 0) {
				$loanAmt = $Get->fields(loanAmt);
				$totUntung = $Get->fields(totUntung);
				$loanType = $Get->fields(loanType);
			}

			$sql = "select name,c_Deduct FROM general where ID = '" . $loanType . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) {
				$c_Deduct = $Get->fields(c_Deduct);
				$c_name = ucwords(strtolower($Get->fields(name)));
			}

			$sql = "select rnoBond FROM loandocs where loanID = '" . $GetLoan->fields(loanID) . "'";
			$Get =  &$conn->Execute($sql);
			if ($Get->RowCount() > 0) $nobond = $Get->fields(rnoBond);

			//					AND month(createdDate) <= ". date("m") ."

			$getOpen = "SELECT 
					SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
					SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
					FROM transaction
					WHERE
					pymtRefer = '" . $nobond . "'
					AND deductID = '" . $c_Deduct . "'
					AND userID = '" . $pk . "' 
					AND year(createdDate) <= " . $yr . "
					GROUP BY userID";
			$rsOpen = $conn->Execute($getOpen);
			//print $rsOpen->fields(yuranDb).'-'.$rsOpen->fields(yuranKt).'<br>';
			if ($rsOpen->RowCount() == 1) {
				$loanbal =  $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
			} else {
				$loanbal =  $loanAmt;
			}
			//------------------- end -----------------------------------

			//print $bil . '-' . $c_name. '-' .$nobond. '-'. $loanAmt .'-'. $loanbal .'<br>';

			$strloanval .= '<tr>
			<td>&nbsp;</td>
			<td> ' . $bil . '. </td>
			<td>&nbsp;</td>
			<td>' . $c_name . '</td>
			<td>&nbsp;Bond Nombor ' . $nobond . '</td>
			<td>&nbsp;</td>
			<td>&nbsp;RM ' . number_format($loanbal, 2) . '</td>
			</tr>';

			$bil++;
			$GetLoan->MoveNext();
		}

		//$strloanval .=  '</table>';

	}
	return $strloanval;
}
