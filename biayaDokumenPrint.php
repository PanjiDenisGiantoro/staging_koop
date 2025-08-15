<?
include 'common.php';

date_default_timezone_set("Asia/Jakarta");
if(isset($pk)){ 
		$sSQL = "SELECT * FROM loandocs WHERE  loanID='".$pk."'";
		$rs = &$conn->Execute($sSQL);
		$arrloandoc = array('loanID', 'userID', 'a1', 'b1', 'c1', 'a2', 'b2', 'c2', 'a3', 'b3', 'c3', 'a4', 'b4', 'c4', 'totalA15', 'totalFee', 'total', 'totalFeePA1', 'totalPB1', 'balPA1', 'totalFeePA2', 'totalPB2', 'balPA2', 'totalFeePA3', 'totalPB3', 'balPA3', 'jamin80', 'jamin80yuran', 'jaminTot', 'biaya', 'biayayuran', 'biayaTot', 'gajiTot', 'gajiPot', 'gajiPotB', 'gajiBersih', 'potBenar', 'potBaru', 'btindih', 'btindihUntung', 'btindihCaj', 'btindihBal', 'yuranBul', 'yuranSedia', 'yuran', 'status', 'createDate', 'createdBy', 'prepare', 'prepareDate', 'prepareBy', 'review', 'reviewDate', 'reviewBy', 'lpotAsal', 'lpotUntung', 'lpotBiaya', 'lpotBulan', 'lpotAsalM', 'lpotUntungM', 'lpotBiayaM', 'lpotBulanM', 'lpotAsalN', 'lpotUntungN', 'lpotBiayaN', 'lpotBulanN', 'rnoBaucer', 'rnoBond', 'rcreatedDate', 'rpreparedby', 'approvedBy', 'updatedBy', 'updatedDate', 'remark', 'ajk1', 'ajkDate1', 'ajkStat1', 'ajk2', 'ajkDate2', 'ajkStat2', 'ajk3', 'ajkDate3', 'ajkStat3', 'result');
		foreach( $arrloandoc as $value){
			${$value}= $rs->fields($value); 
		}
		$lulusb = ($result=="lulus"?"checked":"");
		$tolakb = ($result=="tolak"?"checked":"");

		//----------
		if($rnoBaucer) {
		$idSedia = dlookup("vauchers", "disediakan", "no_baucer ='" .$rnoBaucer."'");
		$rpreparedby = dlookup("users", "name", "userID='" .$idSedia."'");
		$rdate = dlookup("vauchers", "tarikh_baucer", "no_baucer ='" .$rnoBaucer."'");
		$rcreatedDate = toDate("d/m/y",$rdate);

		$idSah = dlookup("vauchers", "disahkan", "no_baucer ='" .$rnoBaucer."'");    
		$approvedBy = dlookup("users", "name", "userID='" .$idSah."'");
		}
		//-------------
}
if($a1 =="on") $a1= "checked";
if($a2 =="on") $a2= "checked";
if($a3 =="on") $a3= "checked";
$a4 = ($a4=="on"?"checked":"");
$b1 = ($b1=="on"?"checked":"");
$b2 = ($b2=="on"?"checked":"");
$b3 = ($b3=="on"?"checked":"");
$b4 = ($b4=="on"?"checked":"");
$c1 = ($c1=="on"?"checked":"");
$c2 = ($c2=="on"?"checked":"");
$c3 = ($c3=="on"?"checked":"");
$c4 = ($c4=="on"?"checked":"");

if($yuran == "yurana"){
	$yurana = "checked"; $potyuran = "20.00";
}elseif($yuran == "yuranb"){
	$yuranb = "checked"; $potyuran = "30.00";
}elseif($yuran == "yuranc"){
	$yuranc = "checked"; $potyuran = "50.00";
}elseif($yuran == "yurand"){
	$yurand = "checked"; $potyuran = "80.00";
}elseif($yuran == "yurane"){
	$yurane = "checked"; $potyuran = "100.00";
}
	if(dlookup("loans", "houseLoan", "loanID='".$pk."'")==1){
	$psen = 75;
	}else{
	$psen = 50;
	}
		$memberID       = $userID;
		$memberNo		= $userID;
		$memberName		= dlookup("users", "name", "userID=" . $userID);
		$loanPeriod		= dlookup("loans", "loanPeriod", "loanID=" . tosql($pk, "Number"));
		$amtLoan		= dlookup("loans", "loanAmt", "loanID=" . tosql($pk, "Number"));
		$loanTypeID		= dlookup("loans", "loanType", "loanID=" . tosql($pk, "Number"));
		$loanTypeCode	= dlookup("general", "code", "ID=" . tosql($loanTypeID, "Number"));
		$loanTypeCodeID	= dlookup("general", "c_Deduct", "ID=" . tosql($loanTypeID, "Number"));
		$loanType		= dlookup("general", "name", "ID=" . tosql($loanTypeCodeID, "Text"));
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Koperasi</title>
<LINK rel="stylesheet" href="images/default.css" >
</head><body>';
include 'biayaDokumenMain.php';
print '<script>window.print();</script>';
print '
</body>
</html>
';
?>