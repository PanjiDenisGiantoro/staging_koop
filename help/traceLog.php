<?
function traceLog($sqlStmt, $reportLog)
{
global $conn;

$sqlTemp = explode("'",$sqlStmt);
$sqlTemp2 = explode($sqlStmt,"'");
$sql = implode(" ",$sqlTemp);
$sql = trim($sql);
$timeStamp = date("Y-m-j H:i:s");
$SQLtype = substr($sqlStmt,0,7);
$SQLeasy = implode($sqlTemp2," ");


//$userID = $_SESSION["user_id"] ;
//if ($userID == '') $userID = 0;
//$sqlInsert = "INSERT INTO ska_trace (sqlAyat, sqlJenis, masaLog, penggunaID, reportLog) VALUES ('".$sql."', '".$SQLtype."', '".$timeStamp."', ".$userID.", '".$reportLog."')";
//$rs = &$conn->Execute($sqlInsert);
}

//----contoh panggil :
//traceLog($insert, "Masukkan item id = ".$itemid); 
?>