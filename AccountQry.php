<?php
///////////////////////////////////////////////BALANCE SHEET//////////////////////////////////////////////////////
///////////////////////////////////////////////BALANCE SHEET//////////////////////////////////////////////////////
/*function getAmaunD($ID,$dtFrom,$dtTo){
global $conn;
$getAmaun = "SELECT SUM(pymtAmt) AS amaun,a.*,b.* FROM generalacc a, transactionacc b WHERE 
		a.ID=b.MdeductID AND b.addminus IN (0) AND b.deductID = '".$ID."' AND (b.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.parentID";
$getPotAmaun= $conn->Execute($getAmaun);
return $getPotAmaun;
}*/

function getAmaunD($ID,$dtFrom,$dtTo){
global $conn;
$getAmaun = "SELECT SUM(pymtAmt) AS amaun,a.* FROM transactionacc a WHERE 
		a.addminus IN (0) AND a.deductID = '".$ID."' AND (a.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.deductID";
$getPotAmaun= $conn->Execute($getAmaun);
return $getPotAmaun;
}

function getAmaunK($ID,$dtFrom,$dtTo){
global $conn;
$getAmaun = "SELECT SUM(pymtAmt) AS amaun,a.* FROM transactionacc a WHERE 
		a.addminus IN (1) AND a.deductID = '".$ID."' AND (a.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.deductID";
$getPotAmaun= $conn->Execute($getAmaun);
return $getPotAmaun;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////TRIAL BALANCE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getAmaunTBD($ID,$dtFrom,$dtTo){
global $conn;
$getAmaun = "SELECT SUM(pymtAmt) AS amaun,a.*,b.* FROM generalacc a, transactionacc b WHERE 
		a.ID=b.deductID AND b.addminus IN (0) AND b.deductID = '".$ID."' AND (b.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.parentID";
$getPotAmaun= $conn->Execute($getAmaun);
return $getPotAmaun;
}

function getAmaunTBK($ID,$dtFrom,$dtTo){
global $conn;
$getAmaun = "SELECT SUM(pymtAmt) AS amaun,a.*,b.* FROM generalacc a, transactionacc b WHERE 
		a.ID=b.deductID AND b.addminus IN (1) AND b.deductID = '".$ID."' AND (b.tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') GROUP BY a.parentID";
$getPotAmaun= $conn->Execute($getAmaun);
return $getPotAmaun;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////TRIAL BALANCE//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
function getOpenAkaun($ID,$dtFrom,$dtTo){
global $conn;
$getOpenAkaun = "SELECT * FROM openakaun WHERE 
		generalID = '".$ID."' AND (tahun BETWEEN '".$dtFrom."' AND '".$dtTo."')";
$getPotOpenAkaun= $conn->Execute($getOpenAkaun);
return $getPotOpenAkaun;
}*/

/*function getamountDSR1000041($dtFrom,$dtTo){
global $conn;
$getamountDSR1000041 = " SELECT SUM(loanAmt) AS amount FROM loans a, userdetails b WHERE a.userID=b.userID AND (b.grossPay >= '10001') AND (a.startPymtDate BETWEEN '".$dtFrom."' AND '".$dtTo."') AND (a.Nisbahdsr >= '41')";
$getPotamountDSR1000041 = $conn->Execute($getamountDSR1000041);
return $getPotamountDSR1000041;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getbilangan($id,$yr){
global $conn;
$getbilangan = "SELECT b.name,
		SUM(CASE WHEN a.addminus = '0' THEN a.pymtAmt ELSE 0 END) AS BakiAwl,
		SUM(CASE WHEN a.addminus = '1' THEN a.pymtAmt ELSE 0 END) AS BakiAkhir, 
		count(a.deductID)as Bil, 
		a.deductID FROM transaction a, general b WHERE year(a.createdDate) < '".$yr1."' AND a.deductID=b.ID and a.deductID IN ('1539','1702','1709','1826','1768','1827','1644','1614','1838','1631','1613','1622','1623','1624','1736','1788','1802','1791','1804','1626','1704','1747','1850','1549','1551','1762','1763','1545','1547','1758','1759','1541','1542','1711','1712','1781','1595','1607','0') GROUP BY a.DeductID Order by 1 ASC";
$getPotbilangan = $conn->Execute($getbilangan);
return $getPotbilangan;
}*/
?>