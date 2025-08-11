<?
if ($by==1) {
	$susun = " ORDER BY ".$fieldsort." ASC";
} elseif ($by==2) {
	$susun = " ORDER BY ".$fieldsort." DESC";
} else {
 	$susun = $defaultSusun;
}

if ($sql) {
	$part_sql = $sql;
	$sql = $part_sql . $susun;
} else {
	$sql = $defaultSQL;
	$part_sql = $sql;
	$sql = $part_sql . $susun;
	$sqlPaging = $defaultSQLPaging;
}
$rs = $conn->Execute($sqlPaging);
?>